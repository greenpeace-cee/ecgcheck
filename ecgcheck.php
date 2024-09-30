<?php

require_once 'ecgcheck.civix.php';

use CRM_Ecgcheck_ExtensionUtil as E;

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function ecgcheck_civicrm_config(&$config): void {
  _ecgcheck_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function ecgcheck_civicrm_install(): void {
  _ecgcheck_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function ecgcheck_civicrm_enable(): void {
  _ecgcheck_civix_civicrm_enable();
}
