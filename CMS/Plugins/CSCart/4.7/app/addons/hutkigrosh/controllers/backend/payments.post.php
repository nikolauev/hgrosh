<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 28.02.2018
 * Time: 17:51
 */

if (!defined('BOOTSTRAP')) { die('Access denied'); }

if ($mode == 'processor') {

    $order_statuses = fn_get_statuses(STATUSES_ORDER);
    Tygh::$app['view']->assign('order_statuses', $order_statuses);
}