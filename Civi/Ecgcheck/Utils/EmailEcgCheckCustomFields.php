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
      ->addSelect('ecg_check.error_message',
        'ecg_check.status:name',
        'ecg_check.status:description',
        'ecg_check.status',
        'ecg_check.last_check',
        'id'
      )
      ->addWhere('id', '=', $this->emailId)
      ->setLimit(1)
      ->execute()
      ->first();

    return [
      'id' => $email['id'],
      'status_name' => $email['ecg_check.status:name'],
      'last_check' => $email['ecg_check.last_check'],
      'error_message' => $email['ecg_check.error_message'],
    ];
  }

  /**
   * @throws Exception
   */
  public function updateLastCheckDate() {
    $this->validateEmail();

    Email::update(TRUE)
      ->addValue('ecg_check.last_check', (new DateTime())->format('Y-m-d H:i:s'))
      ->addWhere('id', '=', $this->emailId)
      ->execute();
  }

  /**
   * @throws Exception
   */
  public function cleanErrorMessage() {
    $this->validateEmail();

    Email::update(TRUE)
      ->addValue('ecg_check.error_message', '')
      ->addWhere('id', '=', $this->emailId)
      ->execute();
  }

  /**
   * @throws Exception
   */
  public function setErrorMessage($errorMessage) {
    $this->validateEmail();

    Email::update(TRUE)
      ->addValue('ecg_check.error_message', $errorMessage)
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
      ->addValue('ecg_check.status:name', $statusName)
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
    $allEmailsCount = Email::get(TRUE)
      ->addSelect('COUNT(id) AS count')
      ->execute()
      ->first()['count'];
    $pendingEmailsCount = Email::get(TRUE)
      ->addSelect('COUNT(id) AS count')
      ->addWhere('ecg_check.status:name', '=', 'pending')
      ->execute()
      ->first()['count'];
    $listedEmailsCount = Email::get(TRUE)
      ->addSelect('COUNT(id) AS count')
      ->addWhere('ecg_check.status:name', '=', 'listed')
      ->execute()
      ->first()['count'];
    $notListedEmailsCount = Email::get(TRUE)
      ->addSelect('COUNT(id) AS count')
      ->addWhere('ecg_check.status:name', '=', 'not_listed')
      ->execute()
      ->first()['count'];
    $errorEmailsCount = Email::get(TRUE)
      ->addSelect('COUNT(id) AS count')
      ->addWhere('ecg_check.status:name', '=', 'error')
      ->execute()
      ->first()['count'];
    $withoutStatusEmailsCount = Email::get(TRUE)
      ->addSelect('COUNT(id) AS count')
      ->addWhere('ecg_check.status:name', 'IS NULL')
      ->execute()
      ->first()['count'];

    return [
      'withoutStatusEmails' => $withoutStatusEmailsCount,
      'pendingEmails' => $pendingEmailsCount,
      'listedEmails' => $listedEmailsCount,
      'notListedEmails' => $notListedEmailsCount,
      'errorEmails' => $errorEmailsCount,
      'withoutStatusEmailsPercent' => round($withoutStatusEmailsCount * 100 / $allEmailsCount, 3),
      'pendingEmailsPercent' => round($pendingEmailsCount * 100 / $allEmailsCount, 3),
      'listedEmailsPercent' => round($listedEmailsCount * 100 / $allEmailsCount, 3),
      'notListedEmailsPercent' => round($notListedEmailsCount * 100 / $allEmailsCount, 3),
      'errorEmailsPercent' => round($errorEmailsCount * 100 / $allEmailsCount, 3),
      'allEmails' => $allEmailsCount,
    ];
  }

}
