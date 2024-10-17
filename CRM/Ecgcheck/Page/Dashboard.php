<?php

use Civi\Ecgcheck\Utils\EmailEcgCheckCustomFields;
use CRM_Ecgcheck_ExtensionUtil as E;

class CRM_Ecgcheck_Page_Dashboard extends CRM_Core_Page {

  public function run() {
    $this->assign('statistic', EmailEcgCheckCustomFields::getStatistic());

    parent::run();
  }

}
