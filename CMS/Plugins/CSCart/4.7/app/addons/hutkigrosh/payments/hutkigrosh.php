<?php

use \esas\hutkigrosh\protocol\HutkigroshProtocol;
use \esas\hutkigrosh\protocol\BillNewRq;
use \esas\hutkigrosh\protocol\BillProduct;
use \esas\hutkigrosh\protocol\LoginRq;
//use \Esas\Hutkigrosh\CSCart\ConfigurationWrapperCSCart;
//use \Esas\Hutkigrosh\CSCart\OrderWrapperCSCart;
//use \Esas\Hutkigrosh\CSCart\OrderProductWrapperCSCart;

if (!defined('BOOTSTRAP')) { die('Access denied'); }
if ($mode == 'place_order') {
    try {
        $orderWrapper = new OrderWrapperCSCart($order_info);
        $configurationWrapper = new ConfigurationWrapperCSCart($processor_data['processor_params']);
        $hg = new HutkigroshProtocol($configurationWrapper->isSandbox());
        $resp = $hg->apiLogIn(new LoginRq($configurationWrapper->getHutkigroshLogin(), $configurationWrapper->getHutkigroshPassword()));
        if ($resp->hasError()) {
            $hg->apiLogOut();
            throw new Exception($resp->getResponseMessage());
        }
        $billNewRq = new BillNewRq();
        $billNewRq->setEripId($configurationWrapper->getEripId());
        $billNewRq->setInvId($orderWrapper->getOrderId());
        $billNewRq->setFullName($orderWrapper->getFullName());
        $billNewRq->setMobilePhone($orderWrapper->getMobilePhone());
        $billNewRq->setEmail($orderWrapper->getEmail());
        $billNewRq->setFullAddress($orderWrapper->getAddress());
        $billNewRq->setAmount($orderWrapper->getAmount());
        $billNewRq->setCurrency($orderWrapper->getCurrency());
        $billNewRq->setNotifyByEMail($configurationWrapper->isEmailNotification());
        $billNewRq->setNotifyByMobilePhone($configurationWrapper->isSmsNotification());
        foreach ($orderWrapper->getProducts() as $lineItem) {
            $cartProduct = new OrderProductWrapperCSCart($lineItem);
            $product = new BillProduct();
            $product->setName($cartProduct->getName());
            $product->setInvId($cartProduct->getInvId());
            $product->setCount($cartProduct->getCount());
            $product->setUnitPrice($cartProduct->getUnitPrice());
            $billNewRq->addProduct($product);
            unset($product); //??
        }

        $resp = $hg->apiBillNew($billNewRq);
        $hg->apiLogOut();
        // в массив $pp_response помещаются данные для дальнейшей обработки ядром
        if ($resp->hasError()) {
            $pp_response['order_status'] = 'F';
            $pp_response["reason_text"] = $resp->getResponseMessage();
        } else {
            $pp_response['order_status'] = $configurationWrapper->getBillStatusPending();
            $pp_response['transaction_id'] = $resp->getBillId();
        }
    } catch (Throwable $e) {
        $pp_response['order_status'] = 'F';
        $pp_response["reason_text"] = "Server exception";
    }
}

