<div>
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
    <a class="crm-button" target="_blank" href="{crmURL p='civicrm/ecgcheck/dashboard' q="reset=1"}">Go to dashboard</a>
  </div>

  <div>
    <div class="crm-section">
      <div class="label">{$form.api_batch_size.label}</div>
      <div class="content">{$form.api_batch_size.html}</div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.api_key.label}</div>
      <div class="content">{$form.api_key.html}</div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.job_batch_size.label}</div>
      <div class="content">{$form.job_batch_size.html}</div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.check_live_time.label}</div>
      <div class="content">{$form.check_live_time.html}</div>
      <div class="clear"></div>
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
    </div>
  </div>

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
