<?php

namespace Civi\Ecgcheck\Utils;

use Civi\API\Exception\UnauthorizedException;
use Civi\Api4\Job;
use Civi\Api4\OptionValue;
use Civi\Api4\Setting;
use CRM_Core_Exception;

class EcgcheckSettings {

  private static function getAvailableSettings(): array {
    return ['ecgcheck_api_key', 'ecgcheck_api_batch_size', 'ecgcheck_check_live_time'];
  }

  public static function getApiKey(): string {
    return EcgcheckSettings::getSettingValue('ecgcheck_api_key');
  }

  public static function getMainScheduledJobId(): int {
    $job = Job::get(TRUE)
      ->addWhere('name', '=', 'run_ecg_check_api_job')
      ->execute()
      ->first();

    return $job['id'];
  }

  public static function getApiTimeOut(): string {
    return 60 * 10;// sec
  }

  public static function setApiKey($apiKey) {
    if (!empty($apiKey)) {
      EcgcheckSettings::setSettingValue('ecgcheck_api_key', $apiKey);
    }
  }

  public static function getApiBatchSize(): int {
    return EcgcheckSettings::getSettingValue('ecgcheck_api_batch_size');
  }

  public static function setApiBatchSize($apiBatchSize) {
    if (!empty($apiBatchSize)) {
      EcgcheckSettings::setSettingValue('ecgcheck_api_batch_size', $apiBatchSize);
    }
  }

  public static function getCheckLiveTime(): int {
    return (int) EcgcheckSettings::getSettingValue('ecgcheck_check_live_time');
  }

  public static function setCheckLiveTime($hours) {
    EcgcheckSettings::setSettingValue('ecgcheck_check_live_time', $hours);
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

  public static function getListedStatusId(): int {
    $optionValue = OptionValue::get(TRUE)
      ->addWhere('option_group_id:name', '=', 'ecg_check_status')
      ->addWhere('name', '=', 'pending')
      ->execute()
      ->first();

    return (int) $optionValue['value'];
  }

  public static function getNotListedStatusId(): int {
    $optionValue = OptionValue::get(TRUE)
      ->addWhere('option_group_id:name', '=', 'ecg_check_status')
      ->addWhere('name', '=', 'error')
      ->execute()
      ->first();

    return (int) $optionValue['value'];
  }

}
