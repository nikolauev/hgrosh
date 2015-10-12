<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Оплата заказа");
include 'HootkiGrosh.php';
if (CModule::IncludeModule("sale"))
{
    if ( $_REQUEST['purchaseid'] ){
        $billID = $_REQUEST['purchaseid'];
        $arRes = CSalePaySystemAction::GetList(array(),array('ACTION_FILE'=>'/bitrix/php_interface/include/sale_payment/hutkigrosh'),false, false,array('PARAMS'));
        $obRes = $arRes->Fetch();

        $arParam = unserialize($obRes['PARAMS']);

        $arPropPS['LOGIN'] = $arParam['LOGIN']['VALUE'];
        $arPropPS['PWD'] = $arParam['PWD']['VALUE'];
        $arPropPS['MODE'] = $arParam['MODE']['VALUE'];
        $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($arPropPS['MODE']);

        $res = $hg->apiLogIn($arPropPS['LOGIN'], $arPropPS['PWD']);

        // Ошибка авторизации
        if (!$res) {
            echo $hg->getError();
            $hg->apiLogOut(); // Завершаем сеанс
        }
        // получаем информацию о счете

        $info = $hg->apiBillInfo($billID);
        if (!$info) {
            echo $hg->getError();
            $hg->apiLogOut(); // Завершаем сеанс
            exit;
        }
        $ORDER_ID = $info['invId'];
        $resOrder = CSaleOrder::GetByID($ORDER_ID);
// проверяем статус счета
        $status = $hg->apiBillStatus($billID);
        if ($status == 'Payed') {
        ?>
            Счет <?=$ORDER_ID?> оплачен. <br/> Код транзакции <?=$billID?>
            <br/> <br/>
            Информация об оплате в скором времени будет подтвеждена администратором.
            <br/> <br/>
            <a href="<?=SITE_DIR?>/personal">Перейти в линый кабинет</a>
            <?
            if (!CSaleOrder::PayOrder($ORDER_ID, "Y", True, True, 0, array("PAY_VOUCHER_NUM" => $billID)))
            {
                echo "Ошибка обновления информации о заказе.";
            }
        }else{
            echo 'some problems';
        }

} else
    echo 'Не переданы параметры.';
}
?>
<pre><?print_r($resOrder);?></pre>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>