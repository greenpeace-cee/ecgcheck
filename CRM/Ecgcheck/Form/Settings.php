<?php

use Civi\Ecgcheck\Utils\EcgcheckSettings;
use CRM_Ecgcheck_ExtensionUtil as E;

class CRM_Ecgcheck_Form_Settings extends CRM_Core_Form {

  public function buildQuickForm() {
    $this->add('number', 'default_api_batch_size', 'Api batch size', ['min' => 1], true);
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

    if (!empty($values['default_api_batch_size']) && !empty((int) $values['default_api_batch_size'])) {
      EcgcheckSettings::setDefaultApiBatchSize((int) $values['default_api_batch_size']);
    }

    if (!empty($values['api_key'])) {
      EcgcheckSettings::setApiKey($values['api_key']);
    }

    CRM_Core_Session::setStatus('', ts('Settings are updated!'), 'success');

    parent::postProcess();
  }

  public function setDefaultValues(): array {
    return [
      'default_api_batch_size' => EcgcheckSettings::getDefaultApiBatchSize(),
      'api_key' => EcgcheckSettings::getApiKey(),
    ];
  }

}
