<div>
  <div style="display: flex">
    <div style="width: 340px">
      <div>
        <h3>Email statistic:</h3>
        <table style="max-width: 340px">
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
    <div style="width: 340px">
      <div>
        <h3>Links:</h3>
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

  <div>
    <h3>Settings:</h3>
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
