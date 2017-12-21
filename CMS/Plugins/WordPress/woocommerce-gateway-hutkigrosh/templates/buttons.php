<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<?php if ($_REQUEST['webpay_status'] == 'payed') { ?>
    <div class="woocommerce-message" id="hutkigroshmessage"><?php echo __('webpay_success_text', 'woocommerce-hutkigrosh-payments') ?></div>
<?php } elseif ($_REQUEST['webpay_status'] == 'failed') { ?>
    <div class="woocommerce-error" id="hutkigroshmessage"><?php echo __('webpay_failed_text', 'woocommerce-hutkigrosh-payments') ?></div>
<?php } ?>
<div class="hutkigrosh">
    <div class="alfaclick">
        <input type="hidden" value="<?php echo $alfaclickbillID ?>" id="billID"/>
        <input type="tel" maxlength="20" value="<?php echo $alfaclickTelephone ?>" id="phone"/>
        <a class="button"
           id="alfaclick_button"><?php echo __('alfaclick_button_label', 'woocommerce-hutkigrosh-payments') ?></a>
    </div>
    <script>
        jQuery(document).ready(function ($) {
            $('#alfaclick_button').click(function () {
                jQuery.post('<?= $alfaclickUrl ?>',
                    {
                        action: 'alfaclick',
                        phone: $('#phone').val(),
                        billid: $('#billID').val()
                    }
                ).done(function (data) {
                    if (Number(data) == '0') {
                        $('#hutkigroshmessage').remove();
                        $('.hutkigrosh').before('<div class="woocommerce-error" id="hutkigroshmessage">Не удалось выставить счет в системе AlfaClick</div>');
                    } else {
                        $('#hutkigroshmessage').remove();
                        $('.hutkigrosh').before('<div class="woocommerce-message" id="hutkigroshmessage">Выставлен счет в системе AlfaClick</div>');
                    }
                })
            })
        });
    </script>
    <?php echo $webpayform ?>
</div>
