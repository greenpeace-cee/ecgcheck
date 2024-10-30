
<div class="ecgcheck__dashboard-items">
  <div class="ecgcheck__dashboard-item">
    <div class="crm-block crm-form-block">
      <div class="crm-accordion-wrapper">
        <div class="crm-accordion-header crm-master-accordion-header">Settings:</div>
        <div class="crm-accordion-body">
          <div class="ecgcheck__dashboard-item-content">
            <ul>
              <li>
                <span>Api time out: <b>{$apiTimeOut}</b> sec</span>
              </li>
              <li>
                <span>Api batch size: <b>{$apiBatchSize}</b> items</span>
                <a href="{crmURL p='civicrm/ecgcheck/settings' q="reset=1"}">edit</a>
              </li>
              <li>
                <span>Job batch size: <b>{$jobBatchSize}</b> items</span>
                <a href="{crmURL p='civicrm/ecgcheck/settings' q="reset=1"}">edit</a>
              </li>
              <li>
              <span>Api key: <b>
                {if empty($hiddenApiKey)}
                  <span>Is empty need to be set!</span>
                {else}
                  <span>{$hiddenApiKey}</span>
                {/if}
                </b>
              </span>
                <a href="{crmURL p='civicrm/ecgcheck/settings' q="reset=1"}">edit api key</a>
              </li>
              <li >
                <p>
                  <span>Check status again after time(check live time): <b>{$checkLiveTime}</b> hours </span>
                  <a href="{crmURL p='civicrm/ecgcheck/settings' q="reset=1"}">edit</a>
                </p>
                {include file="CRM/Ecgcheck/Chanks/CheckLiveTimeHelpInfo.tpl"}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="ecgcheck__dashboard-item">
    <div class="crm-block crm-form-block">
      <div class="crm-accordion-wrapper">
        <div class="crm-accordion-header crm-master-accordion-header">Email statistic:</div>
        <div class="crm-accordion-body">
          <div class="ecgcheck__dashboard-item-content">
            <h3></h3>
            <table>
              <tr>
                <th>Status</th>
                <th>count</th>
                <th>percent</th>
              </tr>
              {foreach from=$statistic item=statisticItem}
                <tr>
                  <td>{$statisticItem.status}</td>
                  <td><b>{$statisticItem.count}</b> of {$statisticItem.allEmailsCount}</td>
                  <td>{$statisticItem.percent}%</td>
                </tr>
              {/foreach}
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="ecgcheck__dashboard-item">
    <div class="crm-block crm-form-block">
      <div class="crm-accordion-wrapper">
        <div class="crm-accordion-header crm-master-accordion-header">Links:</div>
        <div class="crm-accordion-body">
          <div class="ecgcheck__dashboard-item-content">
            <ul>
              <li>
                <a target="_blank" href="{crmURL p='civicrm/admin/joblog' q="id={$scheduledJobId}&reset=1"}">Show Schedule job logs</a>
              </li>
              <li>
                <a target="_blank" href="{crmURL p='civicrm/admin/job/edit' q="action=update&id={$scheduledJobId}&reset=1"}">Edit Schedule job</a>
              </li>
              <li>
                <a target="_blank" href="{crmURL p='civicrm/admin/job/edit' q="action=view&id={$scheduledJobId}&reset=1"}">Execute Schedule job</a>
              </li>
              <li>
                <a target="_blank" href="{$api4SearchEmailLink}">Search emails by API4</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{literal}
<style>
  .ecgcheck__dashboard-item {
    width: 320px;
  }

  .ecgcheck__dashboard-items {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
  }

  .ecgcheck__dashboard-item-content {
    padding: 10px 20px 20px 20px;
  }
</style>
{/literal}
