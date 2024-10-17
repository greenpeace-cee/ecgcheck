<?php

namespace Civi\Api4\Action\Email;

use Civi\Api4\Generic\AbstractAction;
use Civi\Api4\Generic\Result;
use Civi\Ecgcheck\Utils\EcgcheckSettings;

/**
 * Email.runEcgCheckApi action
 * Run ECG Check Api
 *
 * @method $this setApiButchSize(int $cid) Set Api Butch Size
 * @method int getApiButchSize() Get Api Butch Size
 */
class RunEcgCheckApi extends AbstractAction {

  /**
   * @var int|null
   */
  protected ?int $apiButchSize = null;

  public function _run(Result $result) {
    if (!empty($this->apiButchSize)) {
      $apiButchSize = $this->apiButchSize;
    } else {
      $apiButchSize = EcgcheckSettings::getDefaultApiBatchSize();
    }

    $result[] = ['api_call_message' => 'todo, apiButchSize:' . $apiButchSize];
    // TODO: implement api call
  }

}
