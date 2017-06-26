<?php
/*
* @info Платёжный модуль hutkigrosh для JoomShopping
* @package JoomShopping for Joomla!
* @subpackage payment
* @author hutkigrosh.by
*/

error_reporting (0);
$hg_data = JRequest::get('get');
require 'HootkiGrosh.php';
$is_test = ($pmconfigs['hgrosh_test'] == 1) ? true : false; // тестовый api
$hg = new \Alexantr\HootkiGrosh\HootkiGrosh($is_test);
if(isset($hg_data["purchaseid"]))
{
    $res = $hg->apiLogIn($pmconfigs['hgrosh_login'], $pmconfigs['hgrosh_pswd']);

    // Ошибка авторизации
    if (!$res) {
        echo $hg->getError();
        $hg->apiLogOut(); // Завершаем сеанс
        exit;
    }

    $info = $hg->apiBillInfo($hg_data["purchaseid"]);
    if (!$info) {
        echo $hg->getError();
        $hg->apiLogOut(); // Завершаем сеанс
        exit;
    }
    if (!$info) {
        echo $hg->getError();
        $hg->apiLogOut(); // Завершаем сеанс
        exit;
    }

    define('_JEXEC', 1);
    define('DS', DIRECTORY_SEPARATOR);
    $option='com_jshopping';
    $my_path = dirname(__FILE__);
    $my_path = explode(DS.'components',$my_path);
    $my_path = $my_path[0];
    if (file_exists($my_path . '/defines.php'))
        include_once $my_path . '/defines.php';

    if (!defined('_JDEFINES'))
    {
        define('JPATH_BASE', $my_path);
        require_once JPATH_BASE.'/includes/defines.php';
    }

    define('JPATH_COMPONENT',				JPATH_BASE . '/components/' . $option);
    define('JPATH_COMPONENT_SITE',			JPATH_SITE . '/components/' . $option);
    define('JPATH_COMPONENT_ADMINISTRATOR',	JPATH_ADMINISTRATOR . '/components/' . $option);

    require_once JPATH_BASE.'/includes/framework.php';
    $app = JFactory::getApplication('site');
    $app->initialise();

    JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');
    jimport('joomla.application.component.model');

    require_once (JPATH_COMPONENT_SITE."/lib/factory.php");
    require_once (JPATH_COMPONENT_SITE.'/lib/functions.php');
    include_once(JPATH_COMPONENT_SITE."/controllers/checkout.php");

    $error = '';

    $order_id = IntVal($info['invId']);
    if ($order_id)
    {
        $order = &JTable::getInstance('order', 'jshop');
        $order->load($order_id);

        if ($order->order_id)
        {
            $pm_method = &JTable::getInstance('paymentMethod', 'jshop');
            $pm_method->load($order->payment_method_id);
            $pmconfigs = $pm_method->getConfigs();

            $status = $pmconfigs['transaction_end_status'];
            if ($status && !$order->order_created)
            {
                $order->order_created = 1;
                $order->order_status = $status;
                $order->store();
                $pay_class = new JshoppingControllerCheckout();
                $pay_class->sendOrderEmail($order->order_id);
                $order->changeProductQTYinStock("-");
                $pay_class->_changeStatusOrder($order->order_id, $status, 0);
            }

            if ($status && $order->order_status != $status)
            {
                $this->_changeStatusOrder($order_id, $status, 1);
            }
        }
        else
            $error = 'error Unknown order_id';
    }
    else
        $error = 'error Incorrect order_id';

    if ($error == '')
        $ret = "ok";
    else
        $ret = $error;
    die($ret);
}
?>