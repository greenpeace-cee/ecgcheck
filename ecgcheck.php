<?php

require_once 'ecgcheck.civix.php';

use CRM_Ecgcheck_ExtensionUtil as E;

/**
 * All hook documentation:
 * @link https://docs.civicrm.org/dev/en/latest/hooks/
 */

function ecgcheck_civicrm_config(&$config): void {
  _ecgcheck_civix_civicrm_config($config);

  // prevent add listeners twice
  if (isset(Civi::$statics[__FUNCTION__])) {
    return;
  }
  Civi::$statics[__FUNCTION__] = 1;

  Civi::dispatcher()->addListener(
      'hook_civicrm_post',
      'Civi\Ecgcheck\HookListeners\PostSaveEntity\HandleEmailEcgStatus::run',
      PHP_INT_MAX - 1
  );
}

function ecgcheck_civicrm_install(): void {
  _ecgcheck_civix_civicrm_install();
}

function ecgcheck_civicrm_enable(): void {
  _ecgcheck_civix_civicrm_enable();
}

function ecgcheck_civicrm_navigationMenu(&$menu) {
  _ecgcheck_civix_insert_navigation_menu($menu, 'Administer/System Settings', [
    'label' => E::ts('ECG email check dashboard'),
    'name' => 'civicrm_ecgcheck_dashboard',
    'permission' => 'administer CiviCRM',
    'url' => 'civicrm/ecgcheck/dashboard',
    'icon' => 'crm-i fa-envelope',
  ]);

  _ecgcheck_civix_navigationMenu($menu);
}
