<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
    <div class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
        <?php } ?>
    </div>
    <h1><?php echo $heading_title; ?></h1>
    <?php echo $text_message; ?>
    <div class="buttons">
        <div class="right">
            <div class="webpayform">
                <?php echo $webpayform?>
            </div>
            <br>
            <div class = "alfaclick">
                <input type = "hidden" value = "<?php echo $alfaclickbillID?>" id = "billID"/>
                <input type = "tel" maxlength = "20" value = "<?php echo $alfaclickTelephone?>" id = "phone"/>
                <a class="button" id="alfaclick_button">Выставить счет в AlfaClick</a>
            </div>
            <br>
            <a href="<?php echo $continue; ?>" class="button"><?php echo $button_continue; ?></a></div>
    </div>
    
    <script type = "text/javascript" src = "http://ajax.microsoft.com/ajax/jQuery/jquery-1.11.0.min.js"></script>
    <script>
        var submitButton = $('.webpayform input[type="submit"]');
        submitButton.addClass('button');
        $(document).ready(function(){
            $('#alfaclick_button').click(function(){
                $.post('<?= $this->url->link('payment/hutkigrosh/alfaclick') ?>',
                {
                        phone : $('#phone').val(),
                        billid : $('#billID').val()}
                        ).done(function(data){
                            if (data == '0'){
                                alert('Не удалось выставить счет в системе AlfaClick');
                            } else {
                                alert('Выставлен счет в системе AlfaClick');
                            }
                        })
                    })
                });
    </script> 

    <?php echo $content_bottom; ?></div>
<?php echo $footer; ?>
