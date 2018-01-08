<?

use Bitrix\Main\Config\Option;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?><?
$psId = (int)Option::get( 'sale.hutkigrosh', "PAY_SYSTEM_ID");
$APPLICATION->IncludeComponent(
    "bitrix:sale.order.payment.receive",
    "",
    Array(
        "PAY_SYSTEM_ID_NEW" => $psId
    )
);?><br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>