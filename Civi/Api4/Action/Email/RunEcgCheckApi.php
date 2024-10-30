<?php

namespace Civi\Api4\Action\Email;

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
 * @method $this setApiBatchSize(int $cid) Set Api Batch Size
 * @method int getApiBatchSize() Get Api Batch Size
 */
class RunEcgCheckApi extends AbstractAction {

  /**
   * @var int|null
   */
  protected ?int $apiBatchSize = null;
  private $logs = [];

  public function _run(Result $result) {
    $this->apiBatchSize = (!empty($this->apiBatchSize) ? $this->apiBatchSize : EcgcheckSettings::getApiBatchSize());
    $emails = $this->findEmails();
    $emailsBatches = $this->prepareBatches($emails);

    foreach ($emailsBatches as $emailsBatch) {
      $this->callApi($emailsBatch);
    }

    if (empty($emails)) {
      $this->logs[] = ['No emails found in todo.',];
    }

    $result['values'] = [
      'apiBatchSize' => $this->apiBatchSize,
      'jobBatchSize' => EcgcheckSettings::getJobBatchSize(),
      'checkLiveTime' => EcgcheckSettings::getCheckLiveTime(),
      'logs' => $this->logs
    ];
  }

  private function findEmails(): array {
    $checkLiveTime = EcgcheckSettings::getCheckLiveTime();
    $preparedEmails = [];

    $query = '
      SELECT email.id AS id, email.email AS email, ecg.status AS status_id
      FROM civicrm_email AS email
      LEFT JOIN civicrm_ecg_check AS ecg ON email.id = ecg.entity_id
      WHERE
        ecg.status IN (%1, %2)
        OR ecg.status IS NULL
    ';

    if ($checkLiveTime !== 0) {
      $query .= ' OR ecg.last_check + INTERVAL %3 HOUR <= NOW() ';
    }

    $query .= ' ORDER BY email.id ASC, ecg.last_check ASC ';
    $query .= ' LIMIT %4 ';

    $dao = CRM_Core_DAO::executeQuery($query, [
      1 => [EcgcheckSettings::getPendingStatusId(), 'String'],
      2 => [EcgcheckSettings::getErrorStatusId(), 'String'],
      3 => [$checkLiveTime, 'Integer'],
      4 => [EcgcheckSettings::getJobBatchSize(), 'Integer'],
    ]);

    while ($dao->fetch()) {
      $preparedEmails[] = [
        'id' => $dao->id,
        'email' => $dao->email,
        'statusId' => $dao->status_id,
        'hashedEmail' => hash("sha512", $dao->email),
      ];
    }

    return $preparedEmails;
  }

  private function prepareBatches(array $emails): array {
    return array_chunk($emails, $this->apiBatchSize);
  }

  private function callApi($emails) {
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
      $this->logs[] = ['Failed to fetch emails from API. Wrong JSON structure. Response:' . $response,];
      return;
    }

    if (!isset($apiResult['emails'])) {
      $this->logs[] = ['Failed to fetch emails from API. Response:' . $response,];
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
    $listedEmailIds = [];
    $notListedEmailIds = [];
    $allEmailIds = array_column($emails, 'id');
    $listedStatusId = EcgcheckSettings::getListedStatusId();
    $notListedStatusId = EcgcheckSettings::getNotListedStatusId();

    foreach ($emails as $email) {
      if (in_array($email['hashedEmail'], $listedEmails)) {
        if ((int) $email['statusId'] !== $listedStatusId) {
          $listedEmailIds[] = $email['id'];
        }
      } else {
        if ((int) $email['statusId'] !== $notListedStatusId) {
          $notListedEmailIds[] = $email['id'];
        }
      }
    }

    EmailEcgCheckCustomFields::markAsListedEmails($listedEmailIds);
    $this->logs[] = ['Mark as listed email ids:' . implode(',', $listedEmailIds)];

    EmailEcgCheckCustomFields::markAsNotListedEmails($notListedEmailIds);
    $this->logs[] = ['Mark as not listed email ids:' . implode(',', $notListedEmailIds)];

    EmailEcgCheckCustomFields::updateLastCheckDateToEmails($allEmailIds);
    $this->logs[] = ['Update last check date for email ids:' . implode(',', $allEmailIds)];
  }

}
