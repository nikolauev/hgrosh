<?php
//подключаем только служебную часть пролога (для работы с CModule и CSalePaySystemAction), без визуальной части, чтобы не было вывода ненужного html
use Esas\HootkiGrosh\HGConfig;
use Bitrix\Main\Config\Option;

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
require_once($_SERVER["DOCUMENT_ROOT"].'/bitrix/php_interface/include/sale_payment/hutkigrosh/hutkigrosh_api.php');

if (!CModule::IncludeModule("sale")) return;

//получаем параметры платежной системы
//может быть есть возможность сделать это как-то более красиво?
$psId = (int)Option::get( 'sale.hutkigrosh', "PAY_SYSTEM_ID");
$params = CSalePaySystemAction::getParamsByConsumer('PAYSYSTEM_'.$psId);
$config = new HGConfig();
$config->sandbox = $params['SANDBOX']['VALUE'];
$config->login = $params['LOGIN']['VALUE'];
$config->password= $params['PWD']['VALUE'];
//$arRes = CSalePaySystemAction::GetList(array(),array('ACTION_FILE'=>'/bitrix/php_interface/include/sale_payment/hutkigrosh'),false, false,array('PARAMS'));

$hg = new \Esas\HootkiGrosh\HootkiGrosh($config);
$alfaclickRq = new \Esas\HootkiGrosh\AlfaclickRq();
$alfaclickRq->billId = $_POST['billid'];
$alfaclickRq->phone = $_POST['phone'];
$responceXML = $hg->apiAlfaClick($alfaclickRq);
$hg->apiLogOut();
echo intval($responceXML->__toString()) == '0' ? "error" : "ok";
