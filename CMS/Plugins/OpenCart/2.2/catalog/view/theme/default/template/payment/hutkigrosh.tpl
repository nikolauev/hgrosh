<?php if ($testmode) { ?>
<div class="warning"><?php echo $text_testmode; ?></div>
<?php } ?>
<div class="content">
<form action="<?php echo $action; ?>" method="post">
</form>
    </div>
    <div class="buttons">
        <div class="pull-right">
            <input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
        </div>
    </div>
<script type="text/javascript">
    $('#button-confirm').bind('click', function() {
        location.href = "index.php?route=payment/hutkigrosh/send";
//        $.ajax({
//            url: 'index.php?route=payment/hutkigrosh/send',
//            type: 'post',
//            data: $('#payment :input'),
//            dataType: 'json',
//            cache: false,
//            beforeSend: function() {
//                $('#button-confirm').button('loading');
//            },
//            complete: function() {
//                $('#button-confirm').button('reset');
//            },
//            success: function(json) {
//                if (json['error']) {
//                    alert(json['error']);
//                }
//
//                if (json['redirect']) {
//                    location = json['redirect'];
//                }
//            }
//        });
    });
</script>