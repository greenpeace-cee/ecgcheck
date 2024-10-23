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
        <a target="_blank" href="{crmURL p='civicrm/ecgcheck/settings' q="reset=1"}">edit</a>
      </li>
      <li >
        <p>
          <span>check status again after time(check live time): <b>{$checkLiveTime}</b> hours </span>
          <a target="_blank" href="{crmURL p='civicrm/ecgcheck/settings' q="reset=1"}">edit</a>
        </p>
        <div class="status">
          <p>
            <b>Examples:</b><br>
          </p>
          <ul>
            <li>
              Value = 6 hours: <br>
              If emails(listed or not listed) and 'last check date' + 6 hours <= now, <br>
              then those emails will be checked again at next scheduled job. <br> <br>
            </li>
            <li>
              Example: Value = 0 hours:<br>
              emails will be not checked again at next scheduled job.
            </li>
          </ul>
        </div>
      </li>
    </ul>
  </div>
</div>
