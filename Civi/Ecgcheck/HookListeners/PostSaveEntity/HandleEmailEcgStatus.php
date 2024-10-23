<?php

namespace Civi\Ecgcheck\HookListeners\PostSaveEntity;

use Civi\Core\Event\GenericHookEvent;
use Civi\Ecgcheck\Utils\EmailEcgCheckCustomFields;
use Exception;

class HandleEmailEcgStatus {

  private static array $lockedEmailIds = [];

  /**
   * @param GenericHookEvent $event
   */
  public static function run(GenericHookEvent $event) {
    $eventValues = $event->getHookValues();
    $operation = $eventValues[0];
    $entity = $eventValues[1];
    $entityId = (int) $eventValues[2];

    if ($entity !== 'Email') {
      return;
    }

    if (!in_array($operation, ['edit', 'update', 'merge', 'create'])) {
      return;
    }

    // prevent twice running hook
    if (HandleEmailEcgStatus::isEmailLocked($entityId)) {
      return;
    }

    HandleEmailEcgStatus::addLockedEmailId($entityId);

    try {
      $emailEcgCheck = new EmailEcgCheckCustomFields($entityId);
      $emailEcgCheck->setPendingStatus();
      $emailEcgCheck->updateLastCheckDate();
      $emailEcgCheck->cleanErrorMessage();
    } catch (Exception $e) {
     // TODO: log it
    }

    HandleEmailEcgStatus::removeLockedEmailId($entityId);
  }

  public static function addLockedEmailId($emailId) {
    if (empty($emailId)) {
      return;
    }

    HandleEmailEcgStatus::$lockedEmailIds[] = $emailId;
  }

  public static function removeLockedEmailId($emailId) {
    if (empty($emailId)) {
      return;
    }

    HandleEmailEcgStatus::$lockedEmailIds = array_diff(HandleEmailEcgStatus::$lockedEmailIds, [$emailId]);
    HandleEmailEcgStatus::$lockedEmailIds = array_values(HandleEmailEcgStatus::$lockedEmailIds);
  }

  private static function isEmailLocked($emailId): bool {
    if (empty($emailId)) {
      return false;
    }

    return in_array($emailId, HandleEmailEcgStatus::$lockedEmailIds);
  }

}
