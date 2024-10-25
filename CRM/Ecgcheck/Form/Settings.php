<?php

use Civi\Ecgcheck\Utils\EcgcheckSettings;
use CRM_Ecgcheck_ExtensionUtil as E;

class CRM_Ecgcheck_Form_Settings extends CRM_Core_Form {

  public function buildQuickForm() {
    $this->add('number', 'api_batch_size', 'Api batch size', ['min' => 1], true);
    $this->add('number', 'job_batch_size', 'job batch size', ['min' => 1], true);
    $this->add('number', 'check_live_time', 'check status again after time(hours)', [], true);
    $this->add('text', 'api_key', 'Api key', [], true);

    $this->addButtons([
      [
        'type' => 'submit',
        'name' => E::ts('Update'),
        'isDefault' => TRUE
      ]
    ]);

    parent::buildQuickForm();
  }

  public function postProcess() {
    $values = $this->exportValues();

    if (!empty($values['api_batch_size']) && !empty((int) $values['api_batch_size'])) {
      EcgcheckSettings::setApiBatchSize((int) $values['api_batch_size']);
    }

    if (!empty($values['api_key'])) {
      EcgcheckSettings::setApiKey($values['api_key']);
    }

    if (!empty($values['check_live_time'])) {
      EcgcheckSettings::setCheckLiveTime((int) $values['check_live_time']);
    }

    if (!empty($values['job_batch_size'])) {
      EcgcheckSettings::setJobButchSize((int) $values['job_batch_size']);
    }

    CRM_Core_Session::setStatus('', ts('Settings are updated!'), 'success');

    parent::postProcess();
  }

  public function setDefaultValues(): array {
    return [
      'api_batch_size' => EcgcheckSettings::getApiBatchSize(),
      'api_key' => EcgcheckSettings::getApiKey(),
      'check_live_time' => EcgcheckSettings::getCheckLiveTime(),
      'job_batch_size' => EcgcheckSettings::getJobButchSize(),
    ];
  }

}
