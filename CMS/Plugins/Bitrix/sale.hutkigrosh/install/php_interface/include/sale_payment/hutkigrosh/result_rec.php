<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?><?

use Esas\HootkiGrosh\HGConfig;
use Esas\HootkiGrosh\HootkiGrosh;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

include_once 'hutkigrosh_api.php';
$purchaseid = $_REQUEST['purchaseid'];
if (!isset($purchaseid)) {
    throw new Exception('Wrong purchaseid');
}

$config = new HGConfig();
$config->password = CSalePaySystemAction::GetParamValue("PWD");
$config->login = CSalePaySystemAction::GetParamValue("LOGIN");
$config->sandbox = CSalePaySystemAction::GetParamValue("SANDBOX");


#дополнительно проверим статус счета в hg
$hg = new HootkiGrosh($config);
$hgBillInfo = $hg->apiBillInfo($purchaseid);
if (empty($hgBillInfo)) {
    $error = $hg->getError();
    $hg->apiLogOut(); // Завершаем сеанс
    throw new Exception($error);
} else {
    $hg->apiLogOut();
    $localOrderInfo = CSaleOrder::GetByID($hgBillInfo['invId']);
    if ($localOrderInfo['USER_NAME'] . ' ' . $localOrderInfo['USER_LAST_NAME'] != $hgBillInfo['fullName']
        && $localOrderInfo['PRICE'] != $hgBillInfo['amt']) {
        throw new Exception("Unmapped purchaseid");
    }
    if ($localOrderInfo["ID"] > 0 && $localOrderInfo["PAYED"] != "Y") {

        CSaleOrder::PayOrder($localOrderInfo["ID"], "Y");
        $fields = array("STATUS_ID" => "P",
            "PAYED" => "Y"
        );
        CSaleOrder::Update($localOrderInfo["ID"], $fields);
        echo "OK";
    }
}

die();
?>
