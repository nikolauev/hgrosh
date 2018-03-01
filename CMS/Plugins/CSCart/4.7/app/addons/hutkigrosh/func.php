<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.03.2018
 * Time: 12:55
 */
if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_hutkigrosh_prepare_checkout_payment_methods(&$cart, &$auth, &$payment_groups)
{
    if (isset($cart['payment_id'])) {
        foreach ($payment_groups as $tab => $payments) {
            foreach ($payments as $payment_id => $payment_data) {
                    if ($payment_data['payment'] == 'Hutkigrosh') {
                        unset($payment_groups[$tab][$payment_id]['instructions']);
                    }
            }
        }
    }
}