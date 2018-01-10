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
    "SANDBOX" => array(
        "NAME" => GetMessage("HG_SANDBOX"),
        "DESCR" => GetMessage("HG_SANDBOX_DESC"),
        "VALUE" => "1",
        "TYPE" => ""
    ),
    "ERIP_TREE_PATH" => array(
        "NAME" => GetMessage("ERIP_TREE_PATH"),
        "DESCR" => GetMessage("ERIP_TREE_PATH_DESC"),
        "VALUE" => "",
        "TYPE" => ""
    ),
    "NOTIFY_BY_PHONE" => array(
        "NAME" => GetMessage("HG_NOTIFY_BY_PHONE"),
        "DESCR" => GetMessage("HG_NOTIFY_BY_PHONE_DESC"),
        "VALUE" => "",
        "TYPE" => ""
    ),
    "NOTIFY_BY_EMAIL" => array(
        "NAME" => GetMessage("HG_NOTIFY_BY_EMAIL"),
        "DESCR" => GetMessage("HG_NOTIFY_BY_EMAIL_DESC"),
        "VALUE" => "",
        "TYPE" => ""
    )
);