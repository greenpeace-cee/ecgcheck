<?php

namespace Civi\Ecgcheck\Utils;

use Civi\Api4\Setting;
use CRM_Core_Exception;

class EcgcheckSettings {

  private static function getAvailableSettings(): array {
    return ['ecgcheck_api_key', 'ecgcheck_default_api_batch_size'];
  }

  public static function getApiKey(): string {
    return EcgcheckSettings::getSettingValue('ecgcheck_api_key');
  }

  public static function setApiKey($apiKey) {
    if (!empty($apiKey)) {
      EcgcheckSettings::setSettingValue('ecgcheck_api_key', $apiKey);
    }
  }

  public static function getDefaultApiBatchSize(): int {
    return EcgcheckSettings::getSettingValue('ecgcheck_default_api_batch_size');
  }

  public static function setDefaultApiBatchSize($apiBatchSize) {
    if (!empty($apiBatchSize)) {
      EcgcheckSettings::setSettingValue('ecgcheck_default_api_batch_size', $apiBatchSize);
    }
  }

  /**
   * @param $settingName
   * @return mixed|null
   */
  private static function getSettingValue($settingName) {
    if (!in_array($settingName, EcgcheckSettings::getAvailableSettings())) {
      return NULL;
    }

    try {
      $settings = Setting::get(TRUE)
        ->addSelect($settingName)
        ->execute();
    } catch (CRM_Core_Exception $e) {
      return NULL;
    }
    foreach ($settings as $setting) {
      if ($setting['name'] == $settingName) {
        return $setting['value'];
      }
    }

    return NULL;
  }

  /**
   * @param $settingName
   * @param $settingValue
   * @return void
   */
  private static function setSettingValue($settingName, $settingValue) {
    if (empty($settingValue) || !in_array($settingName, EcgcheckSettings::getAvailableSettings())) {
      return;
    }

    try {
      Setting::set(TRUE)
        ->addValue($settingName, $settingValue)
        ->execute();
    } catch (CRM_Core_Exception $e) {}
  }

}
