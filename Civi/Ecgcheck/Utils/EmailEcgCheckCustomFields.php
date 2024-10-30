<?php

namespace Civi\Ecgcheck\Utils;

use Civi\Api4\Email;
use CRM_Core_DAO;

class EmailEcgCheckCustomFields {

  const CUSTOM_GROUP = 'ecg_check';
  const CUSTOM_FIELD_LAST_CHECK = 'last_check';
  const CUSTOM_FIELD_STATUS = 'status';
  const TABLE_NAME = 'civicrm_ecg_check';

  public static function getAvailableStatuses(): array {
    return ['pending', 'listed', 'not_listed', 'error'];
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

  public static function updateLastCheckDateToEmails($emailIds) {
    if (empty($emailIds)) {
      return;
    }

    $valuesSql = ' (' . implode(', NOW()), (', $emailIds) . ', NOW()) ';
    $query = '
        INSERT INTO civicrm_ecg_check (entity_id, last_check)
        VALUES ' . $valuesSql . '
        ON DUPLICATE KEY UPDATE last_check = VALUES(last_check);
    ';

    CRM_Core_DAO::executeQuery($query, []);
  }

  public static function markAsListedEmails($emailIds) {
    self::setEmailStatusToEmails($emailIds, EcgcheckSettings::getListedStatusId());
  }

  public static function markAsNotListedEmails($emailIds) {
    self::setEmailStatusToEmails($emailIds, EcgcheckSettings::getNotListedStatusId());
  }

  public static function markAsPendingEmails($emailIds) {
    self::setEmailStatusToEmails($emailIds, EcgcheckSettings::getPendingStatusId());
  }

  public static function setEmailStatusToEmails($emailIds, $statusId) {
    if (empty($emailIds) || empty($statusId)) {
      return;
    }

    $valuesSql = ' (' . implode(', %1), (', $emailIds) . ', %1) ';
    $query = '
        INSERT INTO civicrm_ecg_check (entity_id, status)
        VALUES ' . $valuesSql . '
        ON DUPLICATE KEY UPDATE status = VALUES(status);
    ';

    CRM_Core_DAO::executeQuery($query, [
      1 => [$statusId, 'String'],
    ]);
  }

}
