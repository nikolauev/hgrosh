<?php
/*
* @info Платёжный модуль hgrosh для JoomShopping
* @package JoomShopping for Joomla!
* @subpackage payment
* @author hgrosh.by
*/

defined('_JEXEC') or die();

?>
<div class="col100">
    <fieldset class="adminform">
        <table class="admintable" width="100%">
            <tr>
                <td class="key" width="300"><?php echo _JSHOP_CFG_HUTKIGROSH_SANDBOX; ?></td>
                <td>
                    <input type="checkbox" class="inputbox" value="1"
                           name="<?= pm_hg::createName(pm_hg::CONFIG_HG_SANDBOX) ?>"
                           <?php if ($params[pm_hg::CONFIG_HG_SANDBOX]) { ?>checked="checked"<?php } ?>>
                    <?php echo JHTML::tooltip(_JSHOP_CFG_HUTKIGROSH_SANDBOX_DESCRIPTION); ?>
                </td>
            </tr>
            <tr>
                <td class="key" width="300"><?php echo _JSHOP_CFG_HUTKIGROSH_STORE; ?></td>
                <td>
                    <input type="text" name="<?= pm_hg::createName(pm_hg::CONFIG_HG_SHOP_NAME) ?>" class="inputbox"
                           value="<?php echo $params[pm_hg::CONFIG_HG_SHOP_NAME]; ?>"/>
                </td>
            </tr>
            <tr>
                <td class="key" width="300"><?php echo _JSHOP_CFG_HUTKIGROSH_STOREID; ?></td>
                <td>
                    <input type="text" name="<?= pm_hg::createName(pm_hg::CONFIG_HG_ERIP_ID) ?>" class="inputbox"
                           value="<?php echo $params[pm_hg::CONFIG_HG_ERIP_ID]; ?>"/>
                </td>
            </tr>
            <tr>
                <td class="key" width="300"><?php echo _JSHOP_CFG_HUTKIGROSH_LOGIN; ?></td>
                <td>
                    <input type="text" name="<?= pm_hg::createName(pm_hg::CONFIG_HG_LOGIN) ?>" class="inputbox"
                           value="<?php echo $params[pm_hg::CONFIG_HG_LOGIN]; ?>"/>
                </td>
            </tr>
            <tr>
                <td class="key" width="300"><?php echo _JSHOP_CFG_HUTKIGROSH_PSWD; ?></td>
                <td>
                    <input type="text" name="<?= pm_hg::createName(pm_hg::CONFIG_HG_PASSWORD) ?>" class="inputbox"
                           value="<?php echo $params[pm_hg::CONFIG_HG_PASSWORD]; ?>"/>
                </td>
            </tr>
            <tr>
                <td class="key" width="300"><?php echo _JSHOP_CFG_HUTKIGROSH_SMS_NOTIFICATION; ?></td>
                <td>
                    <input type="checkbox" class="inputbox" value="1"
                           name="<?= pm_hg::createName(pm_hg::CONFIG_HG_SMS_NOTIFICATION) ?>"
                           <?php if ($params[pm_hg::CONFIG_HG_SMS_NOTIFICATION]) { ?>checked="checked"<?php } ?>>
                </td>
            </tr>
            <tr>
                <td class="key" width="300"><?php echo _JSHOP_CFG_HUTKIGROSH_EMAIL_NOTIFICATION; ?></td>
                <td>
                    <input type="checkbox" class="inputbox" value="1"
                           name="<?= pm_hg::createName(pm_hg::CONFIG_HG_EMAIL_NOTIFICATION) ?>"
                           <?php if ($params[pm_hg::CONFIG_HG_EMAIL_NOTIFICATION]) { ?>checked="checked"<?php } ?>>
                </td>
            </tr>

            <tr>
                <td class="key">
                    <?php echo _JSHOP_CFG_HUTKIGROSH_BILL_STATUS_FAILED; ?>
                </td>
                <td>
                    <?php
                    echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), pm_hg::createName(pm_hg::CONFIG_HG_BILL_STATUS_FAILED), 'class="inputbox" size="1"', 'status_id', 'name', $params[pm_hg::CONFIG_HG_BILL_STATUS_FAILED]);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _JSHOP_CFG_HUTKIGROSH_BILL_STATUS_PENDING; ?>
                </td>
                <td>
                    <?php
                    echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), pm_hg::createName(pm_hg::CONFIG_HG_BILL_STATUS_PENDING), 'class="inputbox" size="1"', 'status_id', 'name', $params[pm_hg::CONFIG_HG_BILL_STATUS_PENDING]);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _JSHOP_CFG_HUTKIGROSH_BILL_STATUS_PAYED; ?>
                </td>
                <td>
                    <?php
                    echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), pm_hg::createName(pm_hg::CONFIG_HG_BILL_STATUS_PAYED), 'class="inputbox" size="1"', 'status_id', 'name', $params[pm_hg::CONFIG_HG_BILL_STATUS_PAYED]);
                    ?>
                </td>
            </tr>
            <tr>
                <td class="key">
                    <?php echo _JSHOP_CFG_HUTKIGROSH_BILL_STATUS_CANCELED; ?>
                </td>
                <td>
                    <?php
                    echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), pm_hg::createName(pm_hg::CONFIG_HG_BILL_STATUS_CANCELED), 'class="inputbox" size="1"', 'status_id', 'name', $params[pm_hg::CONFIG_HG_BILL_STATUS_CANCELED]);
                    ?>
                </td>
            </tr>
        </table>
    </fieldset>
</div>
<div class="clr"></div>