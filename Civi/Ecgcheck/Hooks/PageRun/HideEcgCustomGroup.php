<?php

namespace Civi\Ecgcheck\Hooks\PageRun;

use Civi\Api4\CustomGroup;
use Civi\Core\Event\GenericHookEvent;
use CRM_Contact_Page_View_Summary;
use CRM_Core_Region;
use CRM_Core_Resources;
use CRM_Ecgcheck_ExtensionUtil;
use Civi\Core\Service\AutoSubscriber;

class HideEcgCustomGroup extends AutoSubscriber {

  public static function getSubscribedEvents(): array {
    return ['hook_civicrm_pageRun' => ['run', -20]];
  }

  public static function run(GenericHookEvent $event) {
    if (empty($event->page) || !is_object($event->page)) {
      return;
    }

    if (get_class($event->page) !== CRM_Contact_Page_View_Summary::class) {
      return;
    }

    $customGroupId = HideEcgCustomGroup::getEcgCustomGroupId();
    if (empty($customGroupId)) {
      return;
    }

    CRM_Core_Resources::singleton()->addVars('ecgcheck', [
      'ecgCustomGroupSelector'  => '#crm-email-content details[id^="email_custom_' . $customGroupId . '_"]',
    ]);
    CRM_Core_Region::instance('page-header')->add([
      'scriptUrl' => CRM_Ecgcheck_ExtensionUtil::url('js/hide_ecg_custom_group.js'),
    ]);
  }

  private static function getEcgCustomGroupId() {
    $customGroup = CustomGroup::get(FALSE)
      ->addWhere('extends', '=', 'Email')
      ->addWhere('name', '=', 'ecg_check')
      ->setLimit(1)
      ->execute()
      ->first();

    return empty($customGroup['id']) ? null : $customGroup['id'];
  }

}
