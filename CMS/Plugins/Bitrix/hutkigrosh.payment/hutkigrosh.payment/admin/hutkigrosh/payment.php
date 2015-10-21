<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?
include 'HootkiGrosh.php';
$arPropPS['ERIP'] = trim(htmlspecialchars(CSalePaySystemAction::GetParamValue("ERIP")));
$arPropPS['LOGIN'] = trim(htmlspecialchars(CSalePaySystemAction::GetParamValue("LOGIN")));
$arPropPS['PWD'] = trim(htmlspecialchars(CSalePaySystemAction::GetParamValue("PWD")));
$arPropPS['MODE'] = trim(htmlspecialchars(CSalePaySystemAction::GetParamValue("MODE")));
$arPropPS['HG_RETURN_URL'] = CSalePaySystemAction::GetParamValue("HG_RETURN_URL");
$arPropPS['HG_CANCEL_RETURN_URL'] = CSalePaySystemAction::GetParamValue("HG_CANCEL_RETURN_URL");
$arPropPS['HG_NOTIFY_URL'] = CSalePaySystemAction::GetParamValue("HG_NOTIFY_URL");
$ORDER_ID = CSalePaySystemAction::GetParamValue("ORDER_ID");

//выберем все товары из корзины
$arBasketItems = array();

$dbBasketItems = CSaleBasket::GetList(
    array(
        "NAME" => "ASC",
        "ID" => "ASC"
    ),
    array(
        "FUSER_ID" => CSaleBasket::GetBasketUserID(),
        "LID" => SITE_ID,
        "ORDER_ID" => $ORDER_ID
    ),
    false,
    false,
    array("ID",
        "NAME",
        "CALLBACK_FUNC",
        "MODULE",
        "PRODUCT_ID",
        "QUANTITY",
        "DELAY",
        "CAN_BUY",
        "PRICE",
        "WEIGHT")
);

while ($arItems = $dbBasketItems->Fetch())
{
    if (strlen($arItems["CALLBACK_FUNC"]) > 0)
    {
        CSaleBasket::UpdatePrice($arItems["ID"],
            $arItems["CALLBACK_FUNC"],
            $arItems["MODULE"],
            $arItems["PRODUCT_ID"],
            $arItems["QUANTITY"]);
        $arItems = CSaleBasket::GetByID($arItems["ID"]);
    }
    $arBasketItems[] = $arItems;
}




$hg = new \Alexantr\HootkiGrosh\HootkiGrosh($arPropPS['MODE']);
if(!$_GET['alfaclick']){
    $res = $hg->apiLogIn($arPropPS['LOGIN'], $arPropPS['PWD']);
}else{
    $res = $hg->apiLogIn($_SESSION['HG_LOGIN'], $_SESSION['HG_PWD']);
}


// Ошибка авторизации
if (!$res) {
    echo $hg->getError();
    $hg->apiLogOut(); // Завершаем сеанс
    exit;
}

/// создаем заказ
if(is_array($arBasketItems)) {
    $totalSummOrder = 0;
    foreach ($arBasketItems as $line_item) {
        $arItem['invItemId'] = $line_item['ID'];
        $arItem['desc'] = $line_item['NAME'];
        $arItem['count'] = round($line_item['QUANTITY']);
        $arItem['amt'] = round($line_item['QUANTITY']*$line_item['PRICE']);
        $totalSummOrder +=$arItem['amt'];
        $arItems[] = $arItem;
        unset($arItem);
    }
}
$data = array(
    'eripId' => $arPropPS['ERIP'],
    'invId' => $ORDER_ID,
    'fullName' => $GLOBALS["SALE_INPUT_PARAMS"]['USER']['NAME'].' '.$GLOBALS["SALE_INPUT_PARAMS"]['USER']['LAST_NAME'],
    'mobilePhone' => $GLOBALS["SALE_INPUT_PARAMS"]['PROPERTY']['PHONE'],
    'email' => $GLOBALS["SALE_INPUT_PARAMS"]['PROPERTY']['EMAIL'],
    'fullAddress' => $GLOBALS["SALE_INPUT_PARAMS"]['PROPERTY']['CITY'].' '.$GLOBALS["SALE_INPUT_PARAMS"]['PROPERTY']['ADDRESS'],
    'amt' => $totalSummOrder,
    'curr'=> 'BYR',
    'products' => $arItems
);


$billID = $hg->apiBillNew($data);
if (!$billID) {
//    echo $hg->getError();
//    $hg->apiLogOut(); // Завершаем сеанс
//    exit;
}else{
    $_SESSION['HG_LOGIN'] = $arPropPS['LOGIN'];
    $_SESSION['HG_PWD'] = $arPropPS['PWD'];
    $_SESSION['BILL_ID'] = $billID;
}

if($_POST['alfaclick']  ==  'true'){
    $data = array(
        'billid'=>$_SESSION['BILL_ID'],
        'phone'=>$_POST['phone'],
    );
    $responce = $hg->apiAlfaClick($data);
    if($responce == '0'){
        echo "<h1 style='color: #ff0000'>Ошибка при выставлении счета в системе AlfaClick!</h1>";
    }else{
        echo "<h1 style='color: green'>Счет успешно выставлен</h1>";
    }
}


//// выставляем счет в другие системы ------------------------------------------------------------------------------------------

$dataBgpb = array(
    'billId' => $billID,
    'paymentId' => 1234567890, // внести в опции
    'spClaimId' => $ORDER_ID,
    'amount' => $totalSummOrder,
    'currency' => 974,
    'clientFio' => $GLOBALS["SALE_INPUT_PARAMS"]['USER']['NAME'].' '.$GLOBALS["SALE_INPUT_PARAMS"]['USER']['LAST_NAME'],
    'clientAddress' => $GLOBALS["SALE_INPUT_PARAMS"]['PROPERTY']['CITY'].' '.$GLOBALS["SALE_INPUT_PARAMS"]['PROPERTY']['ADDRESS'],
    'returnUrl' => $arPropPS['HG_RETURN_URL'],
    'cancelReturnUrl' => $arPropPS['HG_CANCEL_RETURN_URL'],
);
//
echo '<h2>Спасибо за заказ!</h2>';
echo '<h2>Счет для оплаты в системе ЕРИП: ' . $ORDER_ID . '</h2>';
echo '<hr>';
echo '<h2>Для оплаты через карту, в системе БелГазПромБанка</h2>';
echo $hg->apiBgpbPay($dataBgpb);
?>
<br>
<hr>
<h2>Форма для выставления счета в системе AlfaClick</h2>
<div class="alfaclick">
    <form method="post">
    <input type="hidden" value="true" name="alfaclick">
    <input type="text" maxlength="20"name="phone" value="<?=$GLOBALS["SALE_INPUT_PARAMS"]['PROPERTY']['PHONE']?>" id="phone">
    <input type="submit" value="Выставить счет в AlfaClick">
    </form>

</div>
<?
$hg->apiLogOut();



?>
<!--<pre>--><?// print_r($data);?><!--</pre>-->

