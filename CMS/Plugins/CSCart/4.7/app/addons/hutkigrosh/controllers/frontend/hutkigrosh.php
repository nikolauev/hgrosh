<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 26.02.2018
 * Time: 14:02
 */

use esas\hutkigrosh\protocol\AlfaclickRq;
use esas\hutkigrosh\protocol\BillInfoRq;
use esas\hutkigrosh\protocol\LoginRq;
use esas\hutkigrosh\protocol\HutkigroshProtocol;

if ($mode == 'alfaclick') {
    $order = fn_get_order_info($_REQUEST['order_id']);
    if (empty($order)) {
        throw new Exception('Can not detect order');
    }
    $configurationWrapper = new ConfigurationWrapperCSCart($order["payment_method"]["processor_params"]);
    $hg = new HutkigroshProtocol($configurationWrapper->isSandbox());
    $resp = $hg->apiLogIn(new LoginRq($configurationWrapper->getHutkigroshLogin(), $configurationWrapper->getHutkigroshPassword()));
    if ($resp->hasError()) {
        $hg->apiLogOut();
        throw new Exception($resp->getResponseMessage());
    }

    $alfaclickRq = new AlfaclickRq();
    $alfaclickRq->setBillId($_REQUEST['bill_id']);
    $alfaclickRq->setPhone($_REQUEST['phone']);

    $resp = $hg->apiAlfaClick($alfaclickRq);
    $hg->apiLogOut();
    echo $resp->hasError() ? "error" : "ok";
    exit;
} elseif ($mode == 'notify') {
    try {
        $billId = $_REQUEST['purchaseid'];
        $processor_data = fn_get_processor_data_by_processor_name('hutkigrosh');
        if (empty($processor_data)) {
            exit;
        }
        $configurationWrapper = new ConfigurationWrapperCSCart($processor_data["processor_params"]);
        $hg = new HutkigroshProtocol($configurationWrapper->isSandbox());
        $resp = $hg->apiLogIn(new LoginRq($configurationWrapper->getHutkigroshLogin(), $configurationWrapper->getHutkigroshPassword()));
        if ($resp->hasError()) {
            $hg->apiLogOut();
            throw new Exception($resp->getResponseMessage(), $resp->getResponseCode());
        }
        $billInfoRs = $hg->apiBillInfo(new BillInfoRq($billId));
        $hg->apiLogOut();
        if ($billInfoRs->hasError())
            throw new Exception($resp->getResponseMessage(), $resp->getResponseCode());
        $order_info = fn_get_order_info($billInfoRs->getInvId());
        if (empty($order_info))
            throw new Exception('Can not load order info for id[' . $billInfoRs->getInvId() . "]");
        $localOrderWrapper = new OrderWrapperCSCart($order_info);
        if ($billInfoRs->getFullName() != $localOrderWrapper->getFullName() || $billInfoRs->getAmount() != $localOrderWrapper->getAmount()) {
            throw new Exception("Unmapped purchaseid: localFullname[" . $localOrderWrapper->getFullName()
                . "], remoteFullname[" . $billInfoRs->getFullName()
                . "], localAmount[" . $localOrderWrapper->getAmount()
                . "], remoteAmount[" . $billInfoRs->getAmount() . "]");
        }
        if ($billInfoRs->isStatusPayed()) {
            $status = $configurationWrapper->getBillStatusPayed();
        } elseif ($billInfoRs->isStatusCanceled()) {
            $status = $configurationWrapper->getBillStatusCanceled();
        } elseif ($billInfoRs->isStatusPending()) {
            $status = $configurationWrapper->getBillStatusPending();
        }
        if (isset($status) && $localOrderWrapper->getStatus() != $status) {
            fn_change_order_status($localOrderWrapper->getOrderId(), $status, '', false);
        }
    } catch (Exception $e) {
        //todo залогировать
    }
    exit;
}

/**
 * Получаем из БД настройки процессора по имени.
 *
 * @param $payment_id
 * @return array|bool
 */
function fn_get_processor_data_by_processor_name($processor_name)
{
    $processor_data = db_get_row("SELECT * FROM ?:payment_processors WHERE processor = ?s OR processor_script = ?s", $processor_name, strtolower($processor_name) . ".tpl");
    if (empty($processor_data)) {
        return false;
    }
    $pdata = db_get_row("SELECT processor_params FROM ?:payments WHERE processor_id = ?i", $processor_data['processor_id']);
    if (empty($pdata)) {
        return false;
    }
    $processor_data['processor_params'] = unserialize($pdata['processor_params']);
    return $processor_data;
}