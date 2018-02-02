<?php

namespace Drupal\commerce_hutkigrosh\Controller;

use Drupal;
use Drupal\commerce_hutkigrosh\api\AlfaclickRq;
use Drupal\commerce_hutkigrosh\api\HutkigroshAPI;
use Drupal\commerce_hutkigrosh\Plugin\Commerce\PaymentGateway\Hutkigrosh;
use Drupal\commerce_order\Entity\Order;
use Drupal\state_machine\Plugin\Workflow\WorkflowTransition;
use Exception;
use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 31.01.2018
 * Time: 11:31
 */
class HutkigroshController
{
    public function alfaclick()
    {
        $order = Order::load($_POST['order_id']);
        if (empty($order)) {
            throw new Exception('Can not detect order');
        }
        if ($order->get('payment_gateway')->isEmpty()) {
            return;
        }
        $settings = $order->get('payment_gateway')->entity->getPluginConfiguration();

        $hg = new HutkigroshAPI($settings[Hutkigrosh::CONFIG_HG_SANDBOX]);
        $res = $hg->apiLogIn($settings[Hutkigrosh::CONFIG_HG_LOGIN], $settings[Hutkigrosh::CONFIG_HG_PASSWORD]);

        // Ошибка авторизации
        if (!$res) {
            $error = $hg->getError();
            $hg->apiLogOut(); // Завершаем сеанс
            throw new Exception($error);
        }
        $alfaclickRq = new AlfaclickRq();
        $alfaclickRq->billId = $_REQUEST['bill_id'];
        $alfaclickRq->phone = $_POST['phone'];

        $responceXML = $hg->apiAlfaClick($alfaclickRq);
        $hg->apiLogOut();
        return new Response(intval($responceXML->__toString()) == '0' ? "error" : "ok");
    }

    public function notify()
    {
        $billId = $_REQUEST['purchaseid'];
        $payment = $this->loadPaymentByRemoteId($billId);
        if (empty($payment)) {
            throw new Exception('Can not detect payment');
        }
        $settings = $payment->getPaymentGateway()->getPluginConfiguration();
        $hg = new HutkigroshAPI($settings[Hutkigrosh::CONFIG_HG_SANDBOX]);
        $res = $hg->apiLogIn($settings[Hutkigrosh::CONFIG_HG_LOGIN], $settings[Hutkigrosh::CONFIG_HG_PASSWORD]);
        // Ошибка авторизации
        if (!$res) {
            $error = $hg->getError();
            $hg->apiLogOut(); // Завершаем сеанс
            throw new Exception($error);
        }
        #дополнительно проверим статус счета в hg
        $hgBillInfo = $hg->apiBillInfo($billId);
        if (empty($hgBillInfo)) {
            $error = $hg->getError();
            $hg->apiLogOut(); // Завершаем сеанс
            throw new Exception($error);
        } else {
            $localOrderInfo = Order::load($hgBillInfo['invId']);
            $address = $localOrderInfo->getBillingProfile()->get('address')->first();
            if (trim($address->getGivenName() . " " . $address->getFamilyName()) != trim($hgBillInfo['fullName'])
                || $payment->getAmount()->getNumber() != $hgBillInfo['amt']) {
                throw new Exception("Unmapped purchaseid");
            }
            // пока изменить статус заказа не получится, т.к. в commerce2 используется механизм workflow,
            // а согласно https://docs.drupalcommerce.org/commerce2/developer-guide/orders/workflows/choosing-workflow
            // workflow привязывается к типу заказа, а нам надо привязка к платежному шлюзу
            $order_state = $localOrderInfo->getState();
            $order_state_transitions = $order_state->getTransitions();
            if ($hgBillInfo['statusEnum'] == 'Payed') {
                $payment->state = Hutkigrosh::HG_PAYMENT_STATUS_COMPLETE;
//                $order_state->applyTransition($order_state_transitions[Hutkigrosh::HG_ORDER_STATUS_COMPLETED]);
            } elseif (in_array($hgBillInfo['statusEnum'], array('Outstending', 'DeletedByUser', 'PaymentCancelled'))) {
                $payment->state = Hutkigrosh::HG_PAYMENT_STATUS_CANCELED;
//                $order_state->applyTransition($order_state_transitions[Hutkigrosh::HG_ORDER_STATUS_CANCLED]);
            } elseif (in_array($hgBillInfo['statusEnum'], array('PaymentPending', 'NotSet'))) {
                $payment->state = Hutkigrosh::HG_PAYMENT_STATUS_PENDING;
//                $order_state->applyTransition($order_state_transitions[Hutkigrosh::HG_ORDER_STATUS_PENDING]);
            }
            $payment->save();
            $localOrderInfo->save();
        }
    }

    public function loadPaymentByRemoteId($remote_id)
    {
        /** @var \Drupal\commerce_payment\PaymentStorage $storage */
        $storage = Drupal::entityTypeManager()->getStorage('commerce_payment');
        $payment_by_remote_id = $storage->loadByProperties(['remote_id' => $remote_id]); //TODO добавить проверку на статус
        return reset($payment_by_remote_id);
    }
}