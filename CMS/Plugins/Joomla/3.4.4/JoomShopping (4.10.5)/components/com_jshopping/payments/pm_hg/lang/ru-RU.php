<?php
/*
* @info Платёжный модуль ArtPay для JoomShopping
* @package JoomShopping for Joomla!
* @subpackage payment
* @author ArtPay.by
*/

//защита от прямого доступа
defined('_JEXEC') or die();

//определяем константы для русского языка
define('_JSHOP_CFG_HGROSH_TEST', 'Режим песочницы');
define('_JSHOP_CFG_HGROSH_TEST_DESCRIPTION', 'Если выбран режим песочницы, оплата будет проходить в тестовом режиме');
define('_JSHOP_CFG_HGROSH_TEST_YES', 'Да');
define('_JSHOP_CFG_HGROSH_TEST_NO', 'Нет');
define('_JSHOP_CFG_HGROSH_STOREID', 'Уникальный идентификатор услуги ЕРИП');
define('_JSHOP_CFG_HGROSH_STORE', 'Название магазина');
define('_JSHOP_CFG_HGROSH_LOGIN', 'Логин интернет-магазина');
define('_JSHOP_CFG_HGROSH_PSWD', 'Пароль интернет-магазина');
define('_JSHOP_CFG_HGROSH_RETURN_URL', 'Url магазина для успешного возврата');
define('_JSHOP_CFG_HGROSH_CANCEL_RETURN_URL', 'Url магазина для неуспешного возврата');
define('_JSHOP_CFG_HGROSH_SYSTEM_URL', 'Url магазина для  возврата с сообщением');
define('_JSHOP_CFG_HGROSH_SSL', 'Путь к SSL');
