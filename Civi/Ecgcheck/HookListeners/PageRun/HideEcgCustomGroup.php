<?php

namespace Civi\Ecgcheck\HookListeners\PageRun;

use Civi\Api4\CustomGroup;
use Civi\Core\Event\GenericHookEvent;
use CRM_Contact_Page_View_Summary;
use CRM_Core_Region;
use CRM_Core_Resources;
use CRM_Ecgcheck_ExtensionUtil;

class HideEcgCustomGroup {

    public static function run(GenericHookEvent $event) {
        $eventValues = $event->getHookValues();
        if (empty($eventValues[0]) || !is_object($eventValues[0])) {
            return;
        }

        if (get_class($eventValues[0]) !== CRM_Contact_Page_View_Summary::class) {
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
