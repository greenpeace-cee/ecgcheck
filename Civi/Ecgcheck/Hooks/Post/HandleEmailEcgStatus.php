<?php

namespace Civi\Ecgcheck\Hooks\Post;

use Civi\Core\Event\GenericHookEvent;
use Civi\Ecgcheck\Utils\EmailEcgCheckCustomFields;
use Civi\Core\Service\AutoSubscriber;

class HandleEmailEcgStatus extends AutoSubscriber {

  public static function getSubscribedEvents(): array {
    return ['hook_civicrm_post' => ['run', -20]];
  }

  public static function run(GenericHookEvent $event) {
    if ($event->entity !== 'Email') {
      return;
    }

    if (!in_array($event->action, ['edit', 'update', 'merge', 'create'])) {
      return;
    }

    EmailEcgCheckCustomFields::markAsPendingEmails([$event->id]);
  }

}
