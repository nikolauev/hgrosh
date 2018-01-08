<?php
//подключаем только служебную часть пролога (для работы с CModule и CSalePaySystemAction), без визуальной части, чтобы не было вывода ненужного html
use Esas\HootkiGrosh\HGConfig;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/include/sale_payment/hutkigrosh/hutkigrosh_api.php');

if (!CModule::IncludeModule("sale")) return;

//получаем параметры платежной системы
//может быть есть возможность сделать это как-то более красиво?
$orderID =  $_POST['orderid'];
$order = \Bitrix\Sale\Order::load($orderID);
$params = CSalePaySystemAction::getParamsByConsumer('PAYSYSTEM_'.$order->getPaymentSystemId()[0]);
$config = new HGConfig();
$config->sandbox = $params['MODE']['VALUE'];
$config->login = $params['LOGIN']['VALUE'];
$config->password= $params['PWD']['VALUE'];
//$arRes = CSalePaySystemAction::GetList(array(),array('ACTION_FILE'=>'/bitrix/php_interface/include/sale_payment/hutkigrosh'),false, false,array('PARAMS'));

$hg = new \Esas\HootkiGrosh\HootkiGrosh($config);
$alfaclickRq = new \Esas\HootkiGrosh\AlfaclickRq();
$alfaclickRq->billId = $_POST['billid'];
$alfaclickRq->phone = $_POST['phone'];
$responceXML = $hg->apiAlfaClick($alfaclickRq);
$hg->apiLogOut();
echo $responceXML->__toString();
