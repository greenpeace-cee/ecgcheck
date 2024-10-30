<?php

namespace Civi\Ecgcheck\HookListeners\PostSaveEntity;

use Civi\Core\Event\GenericHookEvent;
use Civi\Ecgcheck\Utils\EmailEcgCheckCustomFields;

class HandleEmailEcgStatus {

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

    EmailEcgCheckCustomFields::markAsPendingEmails([$entityId]);
  }

}
