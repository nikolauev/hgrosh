<div id="hutkigrosh">
    <div id="completion_text">
        <?php echo $completion_text ?>
    </div>
    <?php if ($webpay_status and $webpay_status == 'payed') { ?>
        <div class="alert alert-info" id="hutkigrosh_message"><?= _JSHOP_HUTKIGROSH_WEBPAY_MSG_PAYED ?></div>
    <?php } elseif ($webpay_status and $webpay_status == 'failed') { ?>
        <div class="alert alert-error" id="hutkigrosh_message"><?= _JSHOP_HUTKIGROSH_WEBPAY_MSG_FAILED ?></div>
    <?php } ?>
    <div id="webpay">
        <?php echo $webpay_form ?>
    </div>
    <div id="alfaclick">
        <input type="hidden" value="<?= $alfaclick_billID ?>" id="billID"/>
        <input type="tel" maxlength="20" value="<?= $alfaclick_phone ?>" id="phone"/>
        <a class="btn btn-success"
           id="alfaclick_button"><?= _JSHOP_HUTKIGROSH_ALFACLICK_LABEL ?></a>
    </div>
    <script type = "text/javascript" src = "http://ajax.microsoft.com/ajax/jQuery/jquery-1.11.0.min.js"></script>
    <script>
        var webpay_form_button = $('#webpay input[type="submit"]');
        webpay_form_button.addClass('btn btn-success');
        jQuery(document).ready(function ($) {
            $('#alfaclick_button').click(function () {
                jQuery.post('<?= $alfaclick_url ?>',
                    {
                        phone: $('#phone').val(),
                        billid: $('#billID').val()
                    }
                ).done(function (result) {
                    if (result.trim() == 'ok') {
                        $('#hutkigrosh_message').remove();
                        $('#webpay').before('<div class="alert alert-info" id="hutkigrosh_message">Выставлен счет в системе AlfaClick</div>');
                    } else {
                        $('#hutkigrosh_message').remove();
                        $('#webpay').before('<div class="alert alert-error" id="hutkigrosh_message">Не удалось выставить счет в системе AlfaClick</div>');
                    }
                })
            })
        });
    </script>
</div>
