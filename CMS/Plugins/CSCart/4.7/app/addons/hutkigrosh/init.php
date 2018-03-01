<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.03.2018
 * Time: 12:53
 */

if (!defined('BOOTSTRAP')) { die('Access denied'); }

fn_register_hooks(
    'prepare_checkout_payment_methods'
);