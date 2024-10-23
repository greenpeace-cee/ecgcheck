<?php

namespace Civi\Ecgcheck\Utils;

use Civi\API\Exception\UnauthorizedException;
use Civi\Api4\Email;
use CRM_Core_DAO;
use CRM_Core_Exception;
use CRM_Core_Session;
use DateTime;
use Exception;

class EmailEcgCheckCustomFields {

  const CUSTOM_GROUP = 'ecg_check';
  const CUSTOM_FIELD_ERROR_MESSAGE = 'error_message';
  const CUSTOM_FIELD_LAST_CHECK = 'last_check';
  const CUSTOM_FIELD_STATUS = 'status';
  const TABLE_NAME = 'civicrm_ecg_check';

  public static function getAvailableStatuses() {
    return ['pending', 'listed', 'not_listed', 'error'];
  }

  private ?int $emailId = null;

  /**
   * @throws Exception
   */
  public function __construct($emailId) {
    $this->setEmailId($emailId);
  }

  /**
   * @throws Exception
   */
  public function setPendingStatus() {
    $this->validateEmail();
    $this->setStatus('pending');
  }

  /**
   * @throws Exception
   */
  public function setListedStatus() {
    $this->validateEmail();
    $this->setStatus('listed');
  }

  /**
   * @throws Exception
   */
  public function setNotListedStatus() {
    $this->validateEmail();
    $this->setStatus('not_listed');
  }

  /**
   * @throws Exception
   */
  public function setErrorStatus() {
    $this->validateEmail();
    $this->setStatus('error');
  }

  public function getAllData(): array {
    $email = Email::get(TRUE)
      ->addSelect(
        EmailEcgCheckCustomFields::CUSTOM_GROUP . '.error_message',
        EmailEcgCheckCustomFields::CUSTOM_GROUP . '.status:name',
        EmailEcgCheckCustomFields::CUSTOM_GROUP . '.status:description',
        EmailEcgCheckCustomFields::CUSTOM_GROUP . '.status',
        EmailEcgCheckCustomFields::CUSTOM_GROUP . '.last_check',
        'id'
      )
      ->addWhere('id', '=', $this->emailId)
      ->setLimit(1)
      ->execute()
      ->first();

    return [
      'id' => $email['id'],
      'status_name' => $email[EmailEcgCheckCustomFields::CUSTOM_GROUP . '.status:name'],
      'last_check' => $email[EmailEcgCheckCustomFields::CUSTOM_GROUP . '.last_check'],
      'error_message' => $email[EmailEcgCheckCustomFields::CUSTOM_GROUP . '.error_message'],
    ];
  }

  /**
   * @throws Exception
   */
  public function updateLastCheckDate() {
    $this->validateEmail();

    Email::update(TRUE)
      ->addValue(EmailEcgCheckCustomFields::CUSTOM_GROUP . '.last_check', (new DateTime())->format('Y-m-d H:i:s'))
      ->addWhere('id', '=', $this->emailId)
      ->execute();
  }

  /**
   * @throws Exception
   */
  public function cleanErrorMessage() {
    $this->validateEmail();

    Email::update(TRUE)
      ->addValue(EmailEcgCheckCustomFields::CUSTOM_GROUP . '.error_message', '')
      ->addWhere('id', '=', $this->emailId)
      ->execute();
  }

  /**
   * @throws Exception
   */
  public function setErrorMessage($errorMessage) {
    $this->validateEmail();

    Email::update(TRUE)
      ->addValue(EmailEcgCheckCustomFields::CUSTOM_GROUP . '.error_message', $errorMessage)
      ->addWhere('id', '=', $this->emailId)
      ->execute();
  }

  /**
   * @param $emailId
   * @return void
   * @throws Exception
   */
  private function setEmailId($emailId) {
    $emailId = (int) $emailId;

    if (empty($emailId)) {
      throw new Exception('Cannot find email. Email: ' . $emailId);
    }

    try {
      $email = Email::get(TRUE)
        ->addSelect('id')
        ->addWhere('id', '=', $emailId)
        ->setLimit(1)
        ->execute()
        ->first();
    } catch (CRM_Core_Exception $e) {
      throw new Exception('Cannot find email. Email: ' . $emailId . 'Error: ' . $e->getMessage());
    }

    if (empty($email)) {
      throw new Exception('Cannot find email. Email: ' . $emailId);
    }

    $this->emailId = $emailId;
  }

  /**
   * @throws CRM_Core_Exception
   */
  private function setStatus($statusName) {
    Email::update(TRUE)
      ->addValue(EmailEcgCheckCustomFields::CUSTOM_GROUP . '.status:name', $statusName)
      ->addWhere('id', '=', $this->emailId)
      ->execute();
  }

  /**
   * @throws Exception
   */
  private function validateEmail() {
    if (empty($this->emailId)) {
      throw new Exception('Email is now found.');
    }
  }

  public static function getStatistic(): array {
    $emails = Email::get(TRUE)
      ->addSelect('COUNT(id) AS count', 'ecg_check.status:name AS status')
      ->addGroupBy('ecg_check.status')
      ->execute();

    $allEmailsCount = 0;
    $statisticData = [];
    $existenceStatuses = [];

    foreach ($emails as $email) {
      $allEmailsCount += $email['count'];
      $statusName = ($email['status'] === null) ? 'is_null' : $email['status'];
      $statisticData[] = ['status' => $statusName, 'count' => $email['count']];
      $existenceStatuses[] = $statusName;
    }

    foreach (EmailEcgCheckCustomFields::getAvailableStatuses() as $statusName) {
      if (!in_array($statusName, $existenceStatuses)) {
        $statisticData[] = [
          'status' => $statusName,
          'count' => 0,
        ];
      }
    }

    foreach ($statisticData as $key => $statisticItem) {
      $statisticData[$key]['percent'] = round($statisticItem['count'] * 100 / $allEmailsCount, 3);
      $statisticData[$key]['allEmailsCount'] = $allEmailsCount;
    }

    return $statisticData;
  }

}
