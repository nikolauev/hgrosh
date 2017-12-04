<?php echo $header; ?>
<div class="container">
    <ul class="breadcrumb">
        <?php foreach ($breadcrumbs as $breadcrumb) { ?>
        <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
        <?php } ?>
    </ul>
    <div class="row"><?php echo $column_left; ?>
        <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } elseif ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-9'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-12'; ?>
        <?php } ?>
        <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
            <h1><?php echo $heading_title; ?></h1>
            <?php echo $text_message; ?>
            <?php if ($message) { ?>
                <div class="alert alert-danger" id="message"><?php echo $message; ?></div>
            <?php } ?>
            <div class="buttons" >
                <div class="pull-right">
                    <div class="webpayform">
                        <?php echo $webpayform?>
                    </div>
                    <br>
                    <div class = "alfaclick">
                        <input type = "hidden" value = "<?php echo $alfaclickbillID?>" id = "billID"/>
                        <input type = "tel" maxlength = "20" value = "<?php echo $alfaclickTelephone?>" id = "phone"/>
                        <a class="btn btn-primary" id="alfaclick_button">Выставить счет в AlfaClick</a>
                    </div>
                    <br>
                    <div><a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a></div>
                </div>
            </div>
        <script type = "text/javascript" src = "http://ajax.microsoft.com/ajax/jQuery/jquery-1.11.0.min.js"></script>
        <script>
            var submitButton = $('.webpayform input[type="submit"]');
            submitButton.addClass('btn btn-primary');
            $('.buttons').find('.pull-right').children().css( "margin", "5px" ).addClass("pull-right");
            $(document).ready(function(){
                $('#alfaclick_button').click(function(){
                    $.post('<?= $alfaclickUrl ?>',
                        {
                            phone : $('#phone').val(),
                            billid : $('#billID').val()}
                ).done(function(data){
                        if (data == '0'){
                            $('#message').remove();
                            $('.buttons').before('<div class="alert alert-danger" id="message">Не удалось выставить счет в системе AlfaClick</div>');
                        } else {
                            $('#message').remove();
                            $('.buttons').before('<div class="alert alert-info" id="message">Выставлен счет в системе AlfaClick</div>');
                        }
                    })
                })
            });
        </script>
        <?php echo $content_bottom; ?></div>
        <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?>