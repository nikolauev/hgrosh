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
				<td class="key" width="300"><?php echo _JSHOP_CFG_HGROSH_TEST; ?></td>
				<td>
					<label for="hgrosh_test_yes"><?php echo _JSHOP_CFG_HGROSH_TEST_YES; ?></label>
					<input id="hgrosh_test_yes" type="radio" name="pm_params[hgrosh_test]" class="radiobox" value="1" <?php if(($params['hgrosh_test']=="")||($params['hgrosh_test']==1)):?> checked<?php endif;?> />
					<label for="hgrosh_test_no"><?php echo _JSHOP_CFG_HGROSH_TEST_NO; ?></label>
					<input id="hgrosh_test_no" type="radio" name="pm_params[hgrosh_test]" class="radiobox" value="0" <?php if(($params['hgrosh_test']!="")&&($params['hgrosh_test']==0)):?> checked<?php endif;?> />
					<?php echo JHTML::tooltip(_JSHOP_CFG_HGROSH_TEST_DESCRIPTION); ?>
				</td>
			</tr>
			<tr>
				<td class="key" width="300"><?php echo _JSHOP_CFG_HGROSH_STOREID; ?></td>
				<td>
					<input type="text" name="pm_params[hgrosh_store_id]" class="inputbox" value="<?php echo $params['hgrosh_store_id']; ?>" />
				</td>
			</tr>
			<tr>
				<td class="key" width="300"><?php echo _JSHOP_CFG_HGROSH_LOGIN; ?></td>
				<td>
					<input type="text" name="pm_params[hgrosh_login]" class="inputbox" value="<?php echo $params['hgrosh_login']; ?>" />
				</td>
			</tr>
			<tr>
				<td class="key" width="300"><?php echo _JSHOP_CFG_HGROSH_PSWD; ?></td>
				<td>
					<input type="text" name="pm_params[hgrosh_pswd]" class="inputbox" value="<?php echo $params['hgrosh_pswd']; ?>" />
				</td>
			</tr>
            <tr>
                <td class="key" width="300"><?php echo _JSHOP_CFG_HGROSH_RETURN_URL; ?></td>
                <td>
                    <input type="text" name="pm_params[hgrosh_return_url]" class="inputbox" value="<?php echo ($params['hgrosh_return_url'] ? $params['hgrosh_return_url'] : "index.php?option=com_jshopping&controller=checkout&task=step7&act=return&js_paymentclass=pm_hgrosh"); ?>" />
                </td>
            </tr>
            <tr>
                <td class="key" width="300"><?php echo _JSHOP_CFG_HGROSH_CANCEL_RETURN_URL; ?></td>
                <td>
                    <input type="text" name="pm_params[hgrosh_cancel_return_url]" class="inputbox" value="<?php echo ($params['hgrosh_cancel_return_url'] ? $params['hgrosh_cancel_return_url'] : "index.php?option=com_jshopping&controller=checkout&task=step7&act=cancel&js_paymentclass=pm_hgrosh"); ?>" />
                </td>
            </tr>
            <tr>
                <td class="key" width="300"><?php echo _JSHOP_CFG_HGROSH_SYSTEM_URL; ?></td>
                <td>
                    <input type="text" name="pm_params[hgrosh_system_url]" class="inputbox" value="<?php echo ($params['hgrosh_system_url'] ? $params['hgrosh_system_url'] : "index.php?option=com_jshopping&controller=checkout&task=step7&act=notify&js_paymentclass=pm_hgrosh"); ?>" />
                </td>
            </tr>
			<tr>
				<td class="key" width="300"><?php echo _JSHOP_CFG_HGROSH_SSL; ?></td>
				<td>
					<input type="text" name="pm_params[hgrosh_ssl]" class="inputbox" value="<?php echo ($params['hgrosh_ssl'] ? $params['hgrosh_ssl'] : "/hgrosh/ssl/cacert.pem"); ?>" />
				</td>
			</tr>


			<tr>
				<td class="key">
					<?php echo _JSHOP_TRANSACTION_END; ?>
				</td>
				<td>
				<?php
					echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_end_status]', 'class="inputbox" size="1"', 'status_id', 'name', $params['transaction_end_status']);
				?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo _JSHOP_TRANSACTION_PENDING; ?>
				</td>
				<td>
				<?php
					echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_pending_status]', 'class="inputbox" size="1"', 'status_id', 'name', $params['transaction_pending_status']);
				?>
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo _JSHOP_TRANSACTION_FAILED; ?>
				</td>
				<td>
				<?php
					echo JHTML::_('select.genericlist', $orders->getAllOrderStatus(), 'pm_params[transaction_failed_status]', 'class="inputbox" size="1"', 'status_id', 'name', $params['transaction_failed_status']);
				?>
				</td>
			</tr>
		</table>
	</fieldset>
</div>
<div class="clr"></div>