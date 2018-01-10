<?php echo $header; ?>
<div id="content">
<div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
</div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
<div class="heading">
    <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
    <div class="buttons"><a onclick="$('#form').submit();" class="button"><?php echo $button_save; ?></a><a href="<?php echo $cancel; ?>" class="button"><?php echo $button_cancel; ?></a></div>
</div>
<div class="content">
<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
<table class="form">
    <tr>
        <td><?php echo $text_storeid; ?></td>
        <td><input type="text" name="hutkigrosh_storeid" value="<?php echo $hutkigrosh_storeid; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $text_store; ?></td>
        <td><input type="text" name="hutkigrosh_store" value="<?php echo $hutkigrosh_store; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $text_login; ?></td>
        <td><input type="text" name="hutkigrosh_login" value="<?php echo $hutkigrosh_login; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $text_pswd; ?></td>
        <td><input type="text" name="hutkigrosh_pswd" value="<?php echo $hutkigrosh_pswd; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $text_erip_tree_path; ?></td>
        <td><input type="text" name="hutkigrosh_erip_tree_path" value="<?php echo $hutkigrosh_erip_tree_path; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $text_order_status_pending; ?></td>
        <td><select name="hutkigrosh_order_status_pending">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $hutkigrosh_order_status_pending) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?php echo $text_order_status_payed; ?></td>
        <td><select name="hutkigrosh_order_status_payed">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $hutkigrosh_order_status_payed) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?php echo $text_order_status_error; ?></td>
        <td><select name="hutkigrosh_order_status_error">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $hutkigrosh_order_status_error) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><?php echo $text_email_notification; ?></td>
        <td>
            <input type="checkbox" name="hutkigrosh_email_notification" value="1"
            <?php if ($hutkigrosh_email_notification) { ?>checked="checked"<?php } ?> />
        </td>
    </tr>
    <tr>
        <td><?php echo $text_sms_notification; ?></td>
        <td>
            <input type="checkbox" name="hutkigrosh_sms_notification" value="1"
            <?php if ($hutkigrosh_sms_notification) { ?>checked="checked"<?php } ?> />
        </td>
    </tr>
    <tr>
        <td><?php echo $text_test; ?></td>
        <td><?php if ($hutkigrosh_test) { ?>
            <input type="checkbox" name="hutkigrosh_test" value="1" checked="checked" />
            <?php } else { ?>
            <input type="checkbox" name="hutkigrosh_test" value="1" />
            <?php } ?>
        </td>
    </tr>
    <tr>
        <td><?php echo $text_sort_order; ?></td>
        <td><input type="text" name="hutkigrosh_sort_order" value="<?php echo $hutkigrosh_sort_order; ?>" /></td>
    </tr>
    <tr>
        <td><?php echo $text_status; ?></td>
        <td>
            <select name="hutkigrosh_status">
                <?php if ($hutkigrosh_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
            </select>
        </td>
    </tr>
</table>
</form>
</div>
</div>
</div>
<?php echo $footer; ?>