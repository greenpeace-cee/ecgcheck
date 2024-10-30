<?php

namespace Civi\Ecgcheck\Utils;

use Civi\Api4\Email;
use DateTime;
use Exception;

class EmailEcgCheckCustomFields {

  const CUSTOM_GROUP = 'ecg_check';
  const CUSTOM_FIELD_LAST_CHECK = 'last_check';
  const CUSTOM_FIELD_STATUS = 'status';
  const TABLE_NAME = 'civicrm_ecg_check';
  private $emailEntity;

  public function __construct($emailIds) {
    $this->emailEntity = Email::update(FALSE)->addWhere('id', 'IN', $emailIds);
  }

  public static function getAvailableStatuses(): array {
    return ['pending', 'listed', 'not_listed', 'error'];
  }

  public function setPendingStatus() {
    $this->setStatus('pending');
  }

  public function setListedStatus() {
    $this->setStatus('listed');
  }

  public function setNotListedStatus() {
    $this->setStatus('not_listed');
  }

  public function setErrorStatus() {
    $this->setStatus('error');
  }

  public function updateLastCheckDate() {
    $this->emailEntity->addValue(self::CUSTOM_GROUP . '.' . self::CUSTOM_FIELD_LAST_CHECK, (new DateTime())->format('Y-m-d H:i:s'));
  }

  private function setStatus($statusName) {
    $this->emailEntity->addValue(self::CUSTOM_GROUP . '.' . self::CUSTOM_FIELD_STATUS . ':name', $statusName);
  }

  /**
   * @throws Exception
   */
  public function execute() {
    $this->emailEntity->execute();
  }

  public static function getStatistic(): array {
    $emails = Email::get(FALSE)
      ->addSelect('COUNT(id) AS count', 'ecg_check.status:name AS status')
      ->addGroupBy('ecg_check.status')
      ->execute();

    $emptyPseudoStatusName = 'is_null';
    $allEmailsCount = 0;
    $statisticData = [];
    $existenceStatuses = [];

    foreach ($emails as $email) {
      $allEmailsCount += $email['count'];
      $statusName = ($email['status'] === null) ? $emptyPseudoStatusName : $email['status'];
      $statisticData[] = ['status' => $statusName, 'count' => $email['count']];
      $existenceStatuses[] = $statusName;
    }

    foreach (array_merge(self::getAvailableStatuses(), [$emptyPseudoStatusName]) as $statusName) {
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
