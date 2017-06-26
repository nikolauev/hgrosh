<?php
/*
* @info Платёжный модуль HGROSH для JoomShopping
* @package JoomShopping for Joomla!
* @subpackage payment
* @author HGROSH.by
*/

//защита от прямого доступа
defined('_JEXEC') or die();

//определяем константы для английского языка
define('_JSHOP_CFG_HGROSH_TEST', 'Debug');
define('_JSHOP_CFG_HGROSH_TEST_DESCRIPTION', 'If set to yes, payment transaction will be execute in debug mode');
define('_JSHOP_CFG_HGROSH_TEST_YES', 'Yes');
define('_JSHOP_CFG_HGROSH_TEST_NO', 'No');
define('_JSHOP_CFG_HGROSH_STOREID', 'Unique identifier of the store');
define('_JSHOP_CFG_HGROSH_STORE', 'Store Name');
define('_JSHOP_CFG_HGROSH_LOGIN', 'Login e-shop');
define('_JSHOP_CFG_HGROSH_PSWD', 'Password e-shop');
define('_JSHOP_CFG_HGROSH_RETURN_URL', 'Success URL');
define('_JSHOP_CFG_HGROSH_CANCEL_RETURN_URL', 'Fail URL');
define('_JSHOP_CFG_HGROSH_SYSTEM_URL', 'System URL');
define('_JSHOP_CFG_HGROSH_SSL', 'Path to SSL');
