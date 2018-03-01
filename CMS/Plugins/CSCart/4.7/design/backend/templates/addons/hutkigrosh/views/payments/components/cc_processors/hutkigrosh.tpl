{function config_field_status_list}
    <div class="control-group">
        <label class="control-label" for="{$config_field_id}">{$config_field_label}:</label>
        <div class="controls">
            <select name="payment_data[processor_params][{$config_field_id}]" id="{$config_field_id}">
                {foreach from=$order_statuses key=key item=status}
                    <option value="{$key}"
                            {if $processor_params.$config_field_id == $key}selected="selected"{/if}>{$status.description}</option>
                {/foreach}
            </select>
        </div>
    </div>
{/function}

{function config_field_checkbox}
    <div class="control-group">
        <label class="control-label" for="{$config_field_id}">{$config_field_label}:</label>
        <div class="controls">
            <input type="checkbox" name="payment_data[processor_params][{$config_field_id}]" id="{$config_field_id}"
                   value="Y" {if $processor_params.$config_field_id == "Y"} checked="checked"{/if} />
        </div>
    </div>
{/function}

{function config_field_text}
    <div class="control-group">
        <label class="control-label" for="{$config_field_id}">{$config_field_label}:</label>
        <div class="controls">
            <input type="text" name="payment_data[processor_params][{$config_field_id}]" id="{$config_field_id}"
                   value="{$processor_params.$config_field_id}" class="input-text"/>
        </div>
    </div>
{/function}

{config_field_checkbox config_field_id="hutkigrosh_sandbox" config_field_label="{__("payments.hutkigrosh_sandbox")}"}
{config_field_text config_field_id="hutkigrosh_shop_name" config_field_label="{__("payments.hutkigrosh_shop_name")}"}
{config_field_text config_field_id="hutkigrosh_erip_id" config_field_label="{__("payments.hutkigrosh_erip_id")}"}
{config_field_text config_field_id="hutkigrosh_hg_login" config_field_label="{__("payments.hutkigrosh_hg_login")}"}
{config_field_text config_field_id="hutkigrosh_hg_password" config_field_label="{__("payments.hutkigrosh_hg_password")}"}
{config_field_checkbox config_field_id="hutkigrosh_notification_sms" config_field_label="{__("payments.hutkigrosh_notification_sms")}"}
{config_field_checkbox config_field_id="hutkigrosh_notification_email" config_field_label="{__("payments.hutkigrosh_notification_email")}"}

{config_field_status_list config_field_id="hutkigrosh_bill_status_failed" config_field_label="{__("payments.hutkigrosh_bill_status_failed")}"}
{config_field_status_list config_field_id="hutkigrosh_bill_status_pending" config_field_label="{__("payments.hutkigrosh_bill_status_pending")}"}
{config_field_status_list config_field_id="hutkigrosh_bill_status_payed" config_field_label="{__("payments.hutkigrosh_bill_status_payed")}"}
{config_field_status_list config_field_id="hutkigrosh_bill_status_canceled" config_field_label="{__("payments.hutkigrosh_bill_status_canceled")}"}