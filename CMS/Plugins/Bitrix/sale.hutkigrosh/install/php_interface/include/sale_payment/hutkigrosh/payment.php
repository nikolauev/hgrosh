<? use Bitrix\Main\Localization\Loc;
use Bitrix\Sale\Order;
use Esas\HootkiGrosh\HGConfig;
use Esas\HootkiGrosh\HootkiGrosh;
use Esas\HootkiGrosh\WebPayRq;
Loc::loadMessages(__FILE__);

\Bitrix\Main\Page\Asset::getInstance()->addCss("/bitrix/themes/.default/sale.css");

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
include 'hutkigrosh_api.php';
$config = new HGConfig();
$config->login = trim(htmlspecialchars(CSalePaySystemAction::GetParamValue("LOGIN")));
$config->password = trim(htmlspecialchars(CSalePaySystemAction::GetParamValue("PWD")));
$config->sandbox = trim(htmlspecialchars(CSalePaySystemAction::GetParamValue("SANDBOX")));

$hg = new HootkiGrosh($config);
$order = Order::load($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);
$billID = $order->getField("COMMENTS");
if (empty($billID))
    $billID = addBill($hg, $order);
$webPayRq = new WebPayRq();
$webPayRq->billId = $billID;
// путь формируется некорректно, если оплату выполнять через личный кабинет
$url = (CMain::IsHTTPS() ? "https" : "http")."://".((defined("SITE_SERVER_NAME") && strlen(SITE_SERVER_NAME) > 0) ? SITE_SERVER_NAME : COption::GetOptionString("main", "server_name", "")) . $APPLICATION->GetCurUri();
$webPayRq->returnUrl = $url."&webpay_status=payed";
$webPayRq->cancelReturnUrl = $url."&webpay_status=failed";

$webpayform = $hg->apiWebPay($webPayRq);
$hg->apiLogOut();

?>
<div class="sale-paysystem-wrapper">
	<span class="tablebodytext">
		<?= Loc::getMessage('hutkigrosh_success_text', array("#ORDER_ID#" => $order->getId(), "#ERIP_TREE_PATH#" => trim(htmlspecialchars(CSalePaySystemAction::GetParamValue("ERIP_TREE_PATH"))))); ?>
	</span>
    <?php if ($_REQUEST['webpay_status'] == 'payed') { ?>
        <div class="alert alert-info"
             id="hutkigroshmessage"><?= Loc::getMessage('hutkigrosh_webpay_success_text') ?></div>
    <?php } elseif ($_REQUEST['webpay_status'] == 'failed') { ?>
        <div class="alert alert-danger"
             id="hutkigroshmessage"><?= Loc::getMessage('hutkigrosh_webpay_failed_text') ?></div>
    <?php } ?>
    <div class="webpayform">
        <?= $webpayform; ?>
    </div>
    <div class="alfaclick">
        <input type="text" maxlength="20" name="phone" value="<?= $GLOBALS["SALE_INPUT_PARAMS"]['PROPERTY']['PHONE'] ?>"
               id="phone">
        <button class="sale-paysystem-yandex-button-item">Выставить счет в AlfaClick</button>
    </div>
</div>
<script type="text/javascript" src="http://ajax.microsoft.com/ajax/jQuery/jquery-1.11.0.min.js"></script>
<script>
    var submitButton = $('.webpayform input[type="submit"]');
    submitButton.addClass('sale-paysystem-yandex-button-item');
    $(document).ready(function () {
        $(document).on('click', 'button', function () {
            console.log('click');
            var phone = $('#phone').val();
            $.post('/hutkigrosh/alfaclick.php',
                {
                    phone: phone,
                    billid: '<?=$billID;?>',
                }
            ).done(function (result) {
                console.log(result);
                if (result.trim() == 'ok') {
                    $('#hutkigroshmessage').remove();
                    $('.webpayform').before('<div class="alert alert-info" id="hutkigroshmessage">Выставлен счет в системе AlfaClick</div>');
                } else {
                    $('#hutkigroshmessage').remove();
                    $('.webpayform').before('<div class="alert alert-danger" id="hutkigroshmessage">Не удалось выставить счет в системе AlfaClick</div>');
                }

            });
        });

    });

</script>
<?

function addBill(HootkiGrosh $hg, Order $order)
{
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
            "ORDER_ID" => $order->getId()
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
            "CURRENCY",
            "WEIGHT")
    );

    while ($arItems = $dbBasketItems->Fetch()) {
        if (strlen($arItems["CALLBACK_FUNC"]) > 0) {
            CSaleBasket::UpdatePrice($arItems["ID"],
                $arItems["CALLBACK_FUNC"],
                $arItems["MODULE"],
                $arItems["PRODUCT_ID"],
                $arItems["QUANTITY"]);
            $arItems = CSaleBasket::GetByID($arItems["ID"]);
        }
        $arBasketItems[] = $arItems;
    }


    // cоздаем заказ
    if (is_array($arBasketItems)) {
        $totalSummOrder = 0;
        foreach ($arBasketItems as $line_item) {
            $arItem['invItemId'] = $line_item['ID'];
            $arItem['desc'] = $line_item['NAME'];
            $arItem['count'] = round($line_item['QUANTITY']);
            $arItem['amt'] = $line_item['QUANTITY'] * $line_item['PRICE'];
            $orderCurrency = isset($orderCurrency) ? $orderCurrency : $arItem['CURRENCY'];
            if ($orderCurrency != $arItem['CURRENCY'])
                throw new Exception('Multicurrency orders are not allowd'); //TODO со временем можно сделать выставление разных счетов
            $totalSummOrder += $arItem['amt'];
            $arItems[] = $arItem;
            unset($arItem);
        }
    }

    $billNewRq = new \ESAS\HootkiGrosh\BillNewRq();
    $billNewRq->eripId = trim(htmlspecialchars(CSalePaySystemAction::GetParamValue("ERIP")));
    $billNewRq->invId = $order->getId();
    $billNewRq->fullName = $GLOBALS["SALE_INPUT_PARAMS"]['USER']['NAME'] . ' ' . $GLOBALS["SALE_INPUT_PARAMS"]['USER']['LAST_NAME'];
    $billNewRq->mobilePhone = $GLOBALS["SALE_INPUT_PARAMS"]['PROPERTY']['PHONE'];
    $billNewRq->email = $GLOBALS["SALE_INPUT_PARAMS"]['PROPERTY']['EMAIL'];
    $billNewRq->fullAddress = $GLOBALS["SALE_INPUT_PARAMS"]['PROPERTY']['CITY'] . ' ' . $GLOBALS["SALE_INPUT_PARAMS"]['PROPERTY']['ADDRESS'];
    $billNewRq->amount = $totalSummOrder;
    $billNewRq->notifyByEMail = (trim(htmlspecialchars(CSalePaySystemAction::GetParamValue("NOTIFY_BY_EMAIL"))) == 1 ? true : false);
    $billNewRq->notifyByMobilePhone = (trim(htmlspecialchars(CSalePaySystemAction::GetParamValue("NOTIFY_BY_PHONE"))) == 1 ? true : false);
    $billNewRq->currency = $orderCurrency;
    $billNewRq->products = $arItems;


    $billID = $hg->apiBillNew($billNewRq);
    if (!$billID) {
        $error = $hg->getError();
        $hg->apiLogOut(); // Завершаем сеанс
        throw new Exception($error);
    }
    //сохраним billid для данного заказа, может быть есть более подходящее место чем поле COMMENTS?
    CSaleOrder::Update($order->getId(), array("COMMENTS" => $billID));
    return $billID;
}

?>

