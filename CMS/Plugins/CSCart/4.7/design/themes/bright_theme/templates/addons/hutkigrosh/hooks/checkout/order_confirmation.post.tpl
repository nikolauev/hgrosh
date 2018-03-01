<div id="hutkigrosh_buttons" class="ty-checkout-complete__buttons buttons-container">
    {if $webpay_status and $webpay_status == 'payed'}
        <div class=" cm-notification-content notification-content
     alert-success" id="hutkigrosh_message">{__("hutkigrosh.webpay.msg.success")}</div>
    {elseif $webpay_status and $webpay_status == 'failed'}
        <div class="cm-notification-content notification-content alert-error"
             id="hutkigrosh_message">{__("hutkigrosh.webpay.msg.failed")}</div>
    {/if}
        <div id="webpay" class="ty-checkout-complete__buttons-left">
            {$webpay_form nofilter}
        </div>
        <div id="alfaclick" class="ty-checkout-complete__buttons-right">
            <input type="hidden" value="{$alfaclick_bill_id}" id="bill_id"/>
            <input type="hidden" value="{$order_info.order_id}" id="order_id"/>
            <input type="tel" maxlength="20" value="{$alfaclick_phone}" id="phone"/>
            <a class="ty-btn ty-btn__secondary " id="alfaclick_button">{__("hutkigrosh.alfaclick.label")}</a>
        </div>
</div>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jQuery/jquery-1.11.0.min.js"></script>
<script>
    var webpay_form_button = $('#webpay input[type="submit"]');
    webpay_form_button.addClass('ty-btn ty-btn__secondary');
    jQuery(document).ready(function ($) {
        $('#alfaclick_button').click(function () {
            $.post('{$alfaclick_url}',
                {
                    phone: $('#phone').val(),
                    bill_id: $('#bill_id').val(),
                    order_id: $('#order_id').val()
                }
            ).done(function (result) {
                if (result.trim() == 'ok') {
                    $('#hutkigrosh_message').remove();
                    $('#webpay').before('<div class="cm-notification-content notification-content alert-success" id="hutkigrosh_message">{__("hutkigrosh.alfaclick.msg.success")}</div>');
                } else {
                    $('#hutkigrosh_message').remove();
                    $('#webpay').before('<div class="cm-notification-content notification-content alert-error" id="hutkigrosh_message">{__("hutkigrosh.alfaclick.msg.failed")}</div>');
                }
            })
        })
    });
</script>
