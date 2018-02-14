<?php
/*
* @info Платёжный модуль HutkiGrosh для JoomShopping
* @package JoomShopping for Joomla!
* @subpackage payment
* @author HutkiGrosh.by
*/

//защита от прямого доступа
defined('_JEXEC') or die();

//определяем константы для русского языка
define('_JSHOP_CFG_HUTKIGROSH_SANDBOX', 'Режим песочницы');
define('_JSHOP_CFG_HUTKIGROSH_SANDBOX_DESCRIPTION', 'Если выбран режим песочницы, оплата будет проходить в тестовом режиме');
define('_JSHOP_CFG_HUTKIGROSH_STOREID', 'Уникальный идентификатор услуги ЕРИП');
define('_JSHOP_CFG_HUTKIGROSH_STORE', 'Название магазина');
define('_JSHOP_CFG_HUTKIGROSH_LOGIN', 'Логин интернет-магазина');
define('_JSHOP_CFG_HUTKIGROSH_PSWD', 'Пароль интернет-магазина');
define('_JSHOP_CFG_HUTKIGROSH_SMS_NOTIFICATION', 'Sms оповещение');
define('_JSHOP_CFG_HUTKIGROSH_EMAIL_NOTIFICATION', 'Email оповещение');
define('_JSHOP_CFG_HUTKIGROSH_COMPLETE_TEXT', 'Текст успешного выставления счета');
define('_JSHOP_CFG_HUTKIGROSH_SSL', 'Путь к SSL');
define('_JSHOP_CFG_HUTKIGROSH_BILL_STATUS_FAILED', 'Статус заказа, если произошла ошибка выставления счета');
define('_JSHOP_CFG_HUTKIGROSH_BILL_STATUS_PENDING', 'Статус заказа, если счет выставлен в ЕРИП');
define('_JSHOP_CFG_HUTKIGROSH_BILL_STATUS_PAYED', 'Статус заказа, если счет оплачен в ЕРИП');
define('_JSHOP_CFG_HUTKIGROSH_BILL_STATUS_CANCELED', 'Статус заказа, если счет отменен в ЕРИП');
define('_JSHOP_HUTKIGROSH_ALFACLICK_LABEL', 'Выставить счет в Alfaclick');
define('_JSHOP_HUTKIGROSH_WEBPAY_MSG_PAYED', 'Счет успешно оплачен в WebPay');
define('_JSHOP_HUTKIGROSH_WEBPAY_MSG_FAILED', 'Ошибка оплаты счета через WebPay');
