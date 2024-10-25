<?php

use Civi\Ecgcheck\Utils\EcgcheckSettings;
use Civi\Ecgcheck\Utils\EmailEcgCheckCustomFields;
use CRM_Ecgcheck_ExtensionUtil as E;

class CRM_Ecgcheck_Page_Dashboard extends CRM_Core_Page {

  public function run() {
    CRM_Utils_System::setTitle(E::ts('ECG email check dashboard'));

    $this->assign('statistic', EmailEcgCheckCustomFields::getStatistic());
    $this->assign('apiTimeOut', EcgcheckSettings::getApiTimeOut());
    $this->assign('apiBatchSize', EcgcheckSettings::getApiBatchSize());
    $this->assign('scheduledJobId', EcgcheckSettings::getMainScheduledJobId());
    $this->assign('api4SearchEmailLink', $this->generateApi4SearchEmailLink());
    $this->assign('checkLiveTime', EcgcheckSettings::getCheckLiveTime());
    $this->assign('jobBatchSize', EcgcheckSettings::getJobBatchSize());

    parent::run();
  }

  private function generateApi4SearchEmailLink(): string {
    $api4Params = 'where=' . urlencode('[["ecg_check.status:name", "IN", ["' . implode('","', EmailEcgCheckCustomFields::getAvailableStatuses()) . '"]]]');
    $api4Params .= '&select=' . urlencode('["ecg_check.*", "email", "contact_id", "id"]');
    $api4Params .= '&limit=100';

    return CRM_Utils_System::url(
      'civicrm/api4#/explorer/Email/get' . '?' . $api4Params,
      '',
      NULL,
      NULL,
      FALSE,
      TRUE,
      FALSE
    );
  }

}
