<!--класс region для добавление margin 10px-->
<div class="hutkigrosh-buttons region">
    <?= $hutkigrosh_success_text ?>
    <?php if (isset($_REQUEST['webpay_status']) && $_REQUEST['webpay_status'] == 'payed') { ?>
        <div class="alert alert-info"
             id="hutkigrosh-message"><?= t('Webpay: payment complete') ?></div>
    <?php } elseif (isset($_REQUEST['webpay_status']) && $_REQUEST['webpay_status'] == 'failed') { ?>
        <div class="alert alert-danger"
             id="hutkigrosh-message"><?= t('Webpay: payment failed') ?></div>
    <?php } ?>
    <div class="webpay_form">
        <?php echo $webpay_form ?>
    </div>
    <br>
    <div class="alfaclick">
        <input type="hidden" value="<?php echo $alfaclick_billID ?>" id="billID"/>
        <input type="tel" maxlength="20" value="<?php echo $alfaclick_phone ?>" id="phone"/>
        <a class="btn btn-primary" id="alfaclick_button"><?php echo $alfaclick_label ?></a>
    </div>
    <br>
</div>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jQuery/jquery-1.11.0.min.js"></script>
<script>
    var submitButton = $('.webpayform input[type="submit"]');
    submitButton.addClass('btn btn-primary');
    //    $('.buttons').find('.pull-right').children().css( "margin", "5px" ).addClass("pull-right");
    $(document).ready(function () {
        $('#alfaclick_button').click(function () {
            $.post('<?= $alfaclick_url ?>', {
                phone : $('#phone').val(),
                billid : $('#billID').val()})
                .done(function (result) {
                    if (result.trim() == 'ok') {
                        $('#hutkigrosh-message').remove();
                        $('.webpay_form').before('<div class="alert alert-info" id="hutkigrosh-message">Выставлен счет в системе AlfaClick</div>');
                    } else {
                        $('#hutkigrosh-message').remove();
                        $('.webpay_form').before('<div class="alert alert-danger" id="hutkigrosh-message">Не удалось выставить счет в системе AlfaClick</div>');
                    }
                })
        })
    });
    $(document).ready(function () {
        $('#webpay_button').click(function () {
            $.post('https://securesandbox.webpay.by/', $(this).serialize())
        })
    });
</script>