<?php echo $header; ?>
<?php echo $column_left; ?>
<div id="content">


    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-webpay" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><img src="view/image/hg.png" alt="" /> <?php echo $heading_title; ?></h1>
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



            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-webpay" class="form-horizontal">

        <div class="form-group">
            <label class="col-sm-2 control-label" for="input-hutkigrosh_storeid">
                <span data-toggle="tooltip" title="" data-original-title="">
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
                    <label class="col-sm-2 control-label" for="input-hutkigrosh_storeid">
                <span data-toggle="tooltip" title="" data-original-title="">
                    <?php echo $text_store; ?>
                </span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" name="hutkigrosh_store" value="<?php echo $hutkigrosh_store; ?>"
                               placeholder="<?php echo $text_store; ?>"
                               id="input-hutkigrosh_storeid" class="form-control">
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-hutkigrosh_storeid">
                <span data-toggle="tooltip" title="" data-original-title="">
                    <?php echo $text_login; ?>
                </span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" name="hutkigrosh_login" value="<?php echo $hutkigrosh_login; ?>"
                               placeholder="<?php echo $text_login; ?>"
                               id="input-hutkigrosh_storeid" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-hutkigrosh_storeid">
                <span data-toggle="tooltip" title="" data-original-title="">
                    <?php echo $text_pswd; ?>
                </span>
                    </label>
                    <div class="col-sm-10">
                        <input type="text" name="hutkigrosh_pswd" value="<?php echo $hutkigrosh_pswd; ?>"
                               placeholder="<?php echo $text_pswd; ?>"
                               id="input-hutkigrosh_storeid" class="form-control">
                    </div>
                </div>




                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-hutkigrosh_storeid">
                <span data-toggle="tooltip" title="" data-original-title="">
                    <?php echo $text_test; ?>
                </span>
                    </label>
                    <div class="col-sm-10">
                        <?php if ($hutkigrosh_test) { ?>
                            <input type="checkbox" name="hutkigrosh_test" value="1" checked="checked" class="form-control" />
                        <?php } else { ?>
                            <input type="checkbox" name="hutkigrosh_test" value="1" class="form-control" />
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
                        <input type="text" name="hutkigrosh_sort_order" value="<?php echo $hutkigrosh_sort_order; ?>"
                               placeholder="<?php echo $text_sort_order; ?>"
                               id="input-hutkigrosh_sort_order" class="form-control">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="hutkigrosh_status"><?php echo $hutkigrosh_status; ?></label>
                    <div class="col-sm-10" id="hutkigrosh_status">
                        <select class="form-control" name="hutkigrosh_status">
                            <?php if (hutkigrosh_status) { ?>
                            <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                            <option value="0"><?php echo $text_disabled; ?></option>
                            <?php } else { ?>
                            <option value="1"><?php echo $text_enabled; ?></option>
                            <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                            <?php } ?>
                        </select></div>
                </div>

            </form>




            </div>
        </div>
    </div>
</div>

<?php echo $footer; ?>