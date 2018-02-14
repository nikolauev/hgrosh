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
define('_JSHOP_CFG_HUTKIGROSH_SANDBOX', 'Sanbox');
define('_JSHOP_CFG_HUTKIGROSH_SANDBOX_DESCRIPTION', 'If set to yes, payment transaction will be execute in debug mode');
define('_JSHOP_CFG_HUTKIGROSH_STOREID', 'Unique identifier of the store');
define('_JSHOP_CFG_HUTKIGROSH_STORE', 'Store Name');
define('_JSHOP_CFG_HUTKIGROSH_LOGIN', 'Login e-shop');
define('_JSHOP_CFG_HUTKIGROSH_PSWD', 'Password e-shop');
define('_JSHOP_CFG_HUTKIGROSH_SMS_NOTIFICATION', 'Sms notification');
define('_JSHOP_CFG_HUTKIGROSH_EMAIL_NOTIFICATION', 'Email notification');
define('_JSHOP_CFG_HUTKIGROSH_SSL', 'Path to SSL');
define('_JSHOP_CFG_HUTKIGROSH_BILL_STATUS_FAILED', 'Order status, if bill was not added to ERIP');
define('_JSHOP_CFG_HUTKIGROSH_BILL_STATUS_PENDING', 'Order status, if bill was added to ERIP');
define('_JSHOP_CFG_HUTKIGROSH_BILL_STATUS_PAYED', 'Order status, if bill was payed in ERIP');
define('_JSHOP_CFG_HUTKIGROSH_BILL_STATUS_CANCELED', 'Order status, if bill was canceled in ERIP');
define('_JSHOP_HUTKIGROSH_ALFACLICK_LABEL', 'Add bill to Alfaclick');
define('_JSHOP_HUTKIGROSH_WEBPAY_MSG_PAYED', 'WebPay payment failed!');
define('_JSHOP_HUTKIGROSH_WEBPAY_MSG_FAILED', 'WebPay payment completed');

