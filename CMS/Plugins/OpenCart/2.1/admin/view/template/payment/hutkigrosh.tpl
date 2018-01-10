<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">


    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-webpay" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><img src="view/image/hgrosh.png" alt=""/> <?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $heading_title; ?></h3>
            </div>
            <div class="panel-body">
                <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-webpay"
                      class="form-horizontal">

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-hutkigrosh_storeid">
                        <span data-toggle="tooltip" title="<?php echo $text_storeid_help; ?>" data-original-title="">
                            <?php echo $text_storeid; ?>
                        </span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" name="hutkigrosh_storeid" value="<?php echo $hutkigrosh_storeid; ?>"
                                   placeholder="<?php echo $text_storeid; ?>"
                                   id="input-hutkigrosh_storeid" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-hutkigrosh_store">
                        <span data-toggle="tooltip" title="<?php echo $text_store_help; ?>" data-original-title="">
                            <?php echo $text_store; ?>
                        </span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" name="hutkigrosh_store" value="<?php echo $hutkigrosh_store; ?>"
                                   placeholder="<?php echo $text_store; ?>"
                                   id="input-hutkigrosh_store" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-hutkigrosh_login">
                        <span data-toggle="tooltip" title="<?php echo $text_login_help; ?>" data-original-title="">
                            <?php echo $text_login; ?>
                        </span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" name="hutkigrosh_login" value="<?php echo $hutkigrosh_login; ?>"
                                   placeholder="<?php echo $text_login; ?>"
                                   id="input-hutkigrosh_login" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-hutkigrosh_pswd">
                        <span data-toggle="tooltip" title="<?php echo $text_pswd_help; ?>" data-original-title="">
                            <?php echo $text_pswd; ?>
                        </span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" name="hutkigrosh_pswd" value="<?php echo $hutkigrosh_pswd; ?>"
                                   placeholder="<?php echo $text_pswd; ?>"
                                   id="input-hutkigrosh_pswd" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-hutkigrosh_order_status_pending">
                        <span data-toggle="tooltip" title="" data-original-title="">
                            <?php echo $text_order_status_pending; ?>
                        </span>
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control" id="input-hutkigrosh_order_status_pending"
                                    name="hutkigrosh_order_status_pending">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $hutkigrosh_order_status_pending) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"
                                        selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-hutkigrosh_order_status_payed">
                        <span data-toggle="tooltip" title="" data-original-title="">
                            <?php echo $text_order_status_payed; ?>
                        </span>
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control" id="input-hutkigrosh_order_status_payed"
                                    name="hutkigrosh_order_status_payed">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $hutkigrosh_order_status_payed) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"
                                        selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-hutkigrosh_order_status_error">
                        <span data-toggle="tooltip" title="" data-original-title="">
                            <?php echo $text_order_status_error; ?>
                        </span>
                        </label>
                        <div class="col-sm-10">
                            <select class="form-control" id="input-hutkigrosh_order_status_error"
                                    name="hutkigrosh_order_status_error">
                                <?php foreach ($order_statuses as $order_status) { ?>
                                <?php if ($order_status['order_status_id'] == $hutkigrosh_order_status_error) { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"
                                        selected="selected"><?php echo $order_status['name']; ?></option>
                                <?php } else { ?>
                                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                                <?php } ?>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-hutkigrosh_erip_tree_path">
                        <span data-toggle="tooltip" title="<?php echo $text_erip_tree_path_help; ?>"
                              data-original-title="">
                            <?php echo $text_erip_tree_path; ?>
                        </span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" name="hutkigrosh_erip_tree_path"
                                   value="<?php echo $hutkigrosh_erip_tree_path; ?>"
                                   placeholder="<?php echo $text_erip_tree_path; ?>"
                                   id="input-hutkigrosh_erip_tree_path" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-hutkigrosh_email_notification">
                        <span data-toggle="tooltip" title="<?php echo $text_email_notification_help; ?>"
                              data-original-title="">
                            <?php echo $text_email_notification; ?>
                        </span>
                        </label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="input-hutkigrosh_email_notification"
                                   name="hutkigrosh_email_notification" value="1"
                            <?php if ($hutkigrosh_email_notification) { ?>checked="checked"<?php } ?>
                            class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-hutkigrosh_sms_notification">
                        <span data-toggle="tooltip" title="<?php echo $text_sms_notification_help; ?>"
                              data-original-title="">
                            <?php echo $text_sms_notification; ?>
                        </span>
                        </label>
                        <div class="col-sm-10">
                            <input type="checkbox" id="input-hutkigrosh_sms_notification"
                                   name="hutkigrosh_sms_notification" value="1"
                            <?php if ($hutkigrosh_sms_notification) { ?>checked="checked"<?php } ?>
                            class="form-control"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-hutkigrosh_test">
                        <span data-toggle="tooltip" title="" data-original-title="">
                            <?php echo $text_test; ?>
                        </span>
                        </label>
                        <div class="col-sm-10">
                            <?php if ($hutkigrosh_test) { ?>
                            <input type="checkbox" id="input-hutkigrosh_test" name="hutkigrosh_test" value="1"
                                   checked="checked" class="form-control"/>
                            <?php } else { ?>
                            <input type="checkbox" id="input-hutkigrosh_test" name="hutkigrosh_test" value="1"
                                   class="form-control"/>
                            <?php } ?>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-hutkigrosh_sort_order">
                        <span data-toggle="tooltip" title="" data-original-title="">
                            <?php echo $text_sort_order; ?>
                        </span>
                        </label>
                        <div class="col-sm-10">
                            <input type="text" name="hutkigrosh_sort_order"
                                   value="<?php echo $hutkigrosh_sort_order; ?>"
                                   placeholder="<?php echo $text_sort_order; ?>"
                                   id="input-hutkigrosh_sort_order" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="input-status"><?php echo $text_status; ?></label>
                        <div class="col-sm-10">
                            <select class="form-control" id="input-status" name="hutkigrosh_status">
                                <?php if ($hutkigrosh_status) { ?>
                                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                                <option value="0"><?php echo $text_disabled; ?></option>
                                <?php } else { ?>
                                <option value="1"><?php echo $text_enabled; ?></option>
                                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>