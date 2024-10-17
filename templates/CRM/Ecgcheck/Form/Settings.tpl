<div>
  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="top"}
  </div>

  <div>
    <div class="crm-section">
      <div class="label">{$form.default_api_batch_size.label}</div>
      <div class="content">{$form.default_api_batch_size.html}</div>
      <div class="clear"></div>
    </div>
    <div class="crm-section">
      <div class="label">{$form.api_key.label}</div>
      <div class="content">{$form.api_key.html}</div>
      <div class="clear"></div>
    </div>
  </div>

  <div class="crm-submit-buttons">
    {include file="CRM/common/formButtons.tpl" location="bottom"}
  </div>
</div>
