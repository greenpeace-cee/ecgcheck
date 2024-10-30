<div class="crm-block crm-form-block">
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
    <a class="crm-button" href="{crmURL p='civicrm/ecgcheck/dashboard' q="reset=1"}">Go to dashboard</a>
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
      {include file="CRM/Ecgcheck/Chanks/CheckLiveTimeHelpInfo.tpl"}
    </div>
  </div>

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
