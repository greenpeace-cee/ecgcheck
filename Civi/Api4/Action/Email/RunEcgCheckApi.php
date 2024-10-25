<?php

namespace Civi\Api4\Action\Email;

use Civi\Api4\Email;
use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Ecgcheck\HookListeners\PostSaveEntity\HandleEmailEcgStatus;
use Civi\Ecgcheck\Utils\EcgcheckSettings;
use Civi\Ecgcheck\Utils\EmailEcgCheckCustomFields;
use CRM_Core_DAO;

/**
 * Email.runEcgCheckApi action
 * Run ECG Check Api
 *
 * @method $this setApiButchSize(int $cid) Set Api Butch Size
 * @method int getApiButchSize() Get Api Butch Size
 */
class RunEcgCheckApi extends AbstractAction {

  /**
   * @var int|null
   */
  protected ?int $apiButchSize = null;
  private $logs = [];

  public function _run(Result $result) {
    $this->apiButchSize = (!empty($this->apiButchSize) ? $this->apiButchSize : EcgcheckSettings::getApiBatchSize());
    $emails = $this->findEmails();
    $emailsBatches = $this->prepareBatches($emails);

    foreach ($emailsBatches as $emailsBatch) {
      $this->callApi($emailsBatch);
    }

    if (empty($emails)) {
      $this->logs[] = ['message' => 'No emails found in todo.',];
    }

    $result[] = [
      'apiButchSize' => $this->apiButchSize,
      'checkLiveTime' => EcgcheckSettings::getCheckLiveTime(),
      'logs' => $this->logs
    ];
  }

  private function findEmails(): array {
    $checkLiveTime = EcgcheckSettings::getCheckLiveTime();
    $preparedEmails = [];

    $query = '
      SELECT email.id AS id, email.email AS email
      FROM civicrm_email AS email
      LEFT JOIN civicrm_ecg_check AS ecg ON email.id = ecg.entity_id
      WHERE
        ecg.status IN (%1, %2)
        OR ecg.status IS NULL
    ';

    if ($checkLiveTime !== 0) {
      $query .= ' OR ecg.last_check + INTERVAL %3 HOUR <= NOW() ';
    }

    $dao = CRM_Core_DAO::executeQuery($query, [
      1 => [EcgcheckSettings::getListedStatusId(), 'String'],
      2 => [EcgcheckSettings::getNotListedStatusId(), 'String'],
      3 => [$checkLiveTime, 'Integer'],
    ]);

    while ($dao->fetch()) {
      $preparedEmails[] = [
        'id' => $dao->id,
        'email' => $dao->email,
        'hashedEmail' => hash("sha512", $dao->email),
      ];
    }

    return $preparedEmails;
  }

  private function prepareBatches(array $emails): array {
    return array_chunk($emails, $this->apiButchSize);
  }

  private function callApi($emails)
  {
    $apiCallBody = $this->prepareApiCallBody($emails);
    $curl = curl_init();

    curl_setopt_array($curl, [
      CURLOPT_URL => 'https://ecg.rtr.at/dev/api/v1/emails/check/batch?X-API-KEY',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => EcgcheckSettings::getApiTimeOut(),
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $apiCallBody,
      CURLOPT_HTTPHEADER => [
        'X-API-KEY: ' . EcgcheckSettings::getApiKey(),
        'Content-Type: application/json'
      ],
    ]);

    $response = curl_exec($curl);
    $apiResult = json_decode($response, true);
    curl_close($curl);

    if (json_last_error() !== JSON_ERROR_NONE) {
      $this->logs[] = ['errorMessage' => 'Failed to fetch emails from API. Wrong JSON structure. Response:' . $response,];
      return;
    }

    if (!isset($apiResult['emails'])) {
      $this->logs[] = ['errorMessage' => 'Failed to fetch emails from API. Response:' . $response,];
      return;
    }

    $this->handleApiResult($apiResult, $emails);
  }

  private function prepareApiCallBody($emails) {
    $hashedEmails = [];

    foreach ($emails as $email) {
      $hashedEmails[] = $email['hashedEmail'];
    }

    return json_encode([
      'emails' => $hashedEmails,
      "contained" => "boolean",
      "hashed" => "boolean"
    ]);
  }

  private function handleApiResult($apiResult, $emails) {
    $listedEmails = $apiResult['emails'];

    foreach ($emails as $email) {
      HandleEmailEcgStatus::addLockedEmailId($email['id']);

      try {
        $emailEcgCheck = new EmailEcgCheckCustomFields($email['id']);
        if (in_array($email['hashedEmail'], $listedEmails)) {
          $emailEcgCheck->setListedStatus();
          $this->logs[] = ['message' => 'Mark as listed email id=' . $email['id']];
        } else {
          $emailEcgCheck->setNotListedStatus();
          $this->logs[] = ['message' => 'Mark as not listed email id=' . $email['id']];
        }

        $emailEcgCheck->updateLastCheckDate();
        $emailEcgCheck->cleanErrorMessage();
      } catch (\Exception $e) {
        $this->logs[] = ['message' => 'Cannot update email id=' . $email['id']];
      }

      HandleEmailEcgStatus::removeLockedEmailId($email['id']);
    }
  }

}
