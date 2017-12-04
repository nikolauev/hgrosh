<?php if ($testmode) { ?>
<div class="alert alert-info"><?php echo $text_testmode; ?></div>
<?php } ?>
<div class="buttons">
    <div class="pull-right">
        <a href="<?php echo $action; ?>" class="btn btn-primary" id="button-confirm"
           data-loading-text="<?php echo $text_loading; ?>"><?php echo $button_confirm; ?></a>
    </div>
</div>
<script type="text/javascript"><!--
    $('#button-confirm').on('click', function () {
        $('#button-confirm').button('loading');
    });
    //--></script>