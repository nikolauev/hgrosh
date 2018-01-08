<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>

<? use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$psTitle = GetMessage("SPCP_DTITLE");
$psDescription = GetMessage("SPCP_DDESCR");


$arPSCorrespondence = array(
    "ERIP" => array(
        "NAME" =>GetMessage("HG_ERIP"),
        "DESCR" => GetMessage("HG_ERIP_DESC"),
        "VALUE" => "",
        "TYPE" => ""
    ),
    "LOGIN" => array(
        "NAME" => GetMessage("HG_LOGIN"),
        "DESCR" => GetMessage("HG_LOGIN_DESC"),
        "VALUE" => "",
        "TYPE" => ""
    ),
    "PWD" => array(
        "NAME" => GetMessage("HG_PWD"),
        "DESCR" => GetMessage("HG_PWD_DESC"),
        "VALUE" => "",
        "TYPE" => ""
    ),
    "MODE" => array(
        "NAME" => GetMessage("HG_MODE"),
        "DESCR" => GetMessage("HG_MODE_DESC"),
        "VALUE" => "1",
        "TYPE" => ""
    ),
    "HG_RETURN_URL" => array(
        "NAME" => GetMessage("hg_return_url_N"),
        "DESCR" => GetMessage("hg_return_url_D"),
        "VALUE" => "/payinfo/ok.php",
        "TYPE" => ""
    ),
    "HG_CANCEL_RETURN_URL" => array(
        "NAME" => GetMessage("hg_cancel_return_url_N"),
        "DESCR" => GetMessage("hg_cancel_return_url_D"),
        "VALUE" => "/payinfo/error.php",
        "TYPE" => ""
    ),
    "HG_NOTIFY_URL" => array(
        "NAME" => GetMessage("hg_notify_url_N"),
        "DESCR" => GetMessage("hg_notify_url_D"),
        "VALUE" => "/payinfo/ok.php",
        "TYPE" => ""
    ),
    "ORDER_ID" => array(
        "NAME" => GetMessage("ORDER_ID"),
        "DESCR" => GetMessage("ORDER_ID_DESCR"),
        "VALUE" => "ID",
        "TYPE" => "ORDER"
    ),
);