<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 26.02.2018
 * Time: 14:25
 */

use esas\hutkigrosh\protocol\HutkigroshProtocol;
use esas\hutkigrosh\protocol\WebPayRq;
use esas\hutkigrosh\protocol\LoginRq;

if ($mode == 'complete') {
    if (!empty($_REQUEST['order_id'])) {
        $order_info = fn_get_order_info($_REQUEST['order_id']);
        if (strtolower($order_info["payment_method"]["processor"]) == "hutkigrosh") {
            $orderWrapper = new OrderWrapperCSCart($order_info);
            $configurationWrapper = new ConfigurationWrapperCSCart($order_info["payment_method"]["processor_params"]);
            $hg = new HutkigroshProtocol($configurationWrapper->isSandbox());
            $resp = $hg->apiLogIn(new LoginRq($configurationWrapper->getHutkigroshLogin(), $configurationWrapper->getHutkigroshPassword()));
            if ($resp->hasError()) {
                $hg->apiLogOut();
                throw new Exception($resp->getResponseMessage());
            }
            $webPayRq = new WebPayRq();
            $webPayRq->setBillId($orderWrapper->getBillid());
            $webPayRq->setReturnUrl(REAL_URL . '&webpay_status=payed');
            $webPayRq->setCancelReturnUrl(REAL_URL . '&webpay_status=failed');
            $webPayRs = $hg->apiWebPay($webPayRq);
            $hg->apiLogOut();
            Tygh::$app['view']->assign('webpay_form', $webPayRs->getHtmlForm());
            // в случае возврата на эту страницу со станицы webpay. стутус нужен для отображения информационного соощения клиенту
            Tygh::$app['view']->assign('webpay_status', $_REQUEST['webpay_status']);
            Tygh::$app['view']->assign('alfaclick_bill_id', $orderWrapper->getBillId());
            Tygh::$app['view']->assign('alfaclick_phone', $orderWrapper->getMobilePhone());
            Tygh::$app['view']->assign('alfaclick_url', fn_url("hutkigrosh.alfaclick"));
        }
    }

}