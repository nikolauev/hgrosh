<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 09.02.2018
 * Time: 14:51
 */
defined('_JEXEC') or die();

use Esas\HutkiGrosh\AlfaclickRq;
use Esas\HutkiGrosh\HutkiGrosh;

require_once(JPATH_SITE . '/components/com_jshopping/payments/pm_hg/hutkigrosh_api.php');
require_once(JPATH_SITE . '/components/com_jshopping/payments/pm_hg/pm_hg.php');

class JshoppingControllerHutkigrosh extends JshoppingControllerBase
{
    /**
     * Выставляет счет в альфаклик
     */
    function alfaclick()
    {
        $pm_method = JSFactory::getTable('paymentMethod', 'jshop');
        $pm_method->loadFromClass(pm_hg::MODULE_MACHINE_NAME);
        $pmconfigs = $pm_method->getConfigs();
        $hg = new HutkiGrosh($pmconfigs[pm_hg::CONFIG_HG_SANDBOX]);
        $res = $hg->apiLogIn($pmconfigs[pm_hg::CONFIG_HG_LOGIN], $pmconfigs[pm_hg::CONFIG_HG_PASSWORD]);
        // Ошибка авторизации
        if (!$res) {
            saveToLog("payment.log", 'Hutkigrosh: login failed: ' . $hg->getError());
            $hg->apiLogOut(); // Завершаем сеанс
        } else {
            $alfaclickRq = new AlfaclickRq();
            $alfaclickRq->billId = $_POST['billid'];
            $alfaclickRq->phone = $_POST['phone'];
            $responceXML = $hg->apiAlfaClick($alfaclickRq);
            $hg->apiLogOut();
        }
        if (isset($responceXML) && intval($responceXML->__toString()) > 0)
            echo "ok";
        else
            echo "error";
        die();
    }

    /**
     * В Joomla после оформления заказа и перехода на стадию "finish". Происходит очистка
     * сессии. И если необходимо повторно отобразить итоговую страницу с инструкцией по оплате счета
     * приходится или подпихивать в сессию переменную jshop_end_order_id или делать через этот метож контроллера
     */
    function complete()
    {
        $order_id = $_REQUEST['order_id'];
        $bill_id = $_REQUEST['bill_id'];
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($order_id);
        $pm_method = $order->getPayment();
        $paymentsysdata = $pm_method->getPaymentSystemData();
        $payment_system = $paymentsysdata->paymentSystem;
        // проверяем что для указанного заказа оплата производилась через ХуткиГрош
        if ($payment_system
            && $pm_method->payment_class == pm_hg::MODULE_MACHINE_NAME
            && $order->transaction == $bill_id) {
            $pmconfigs = $pm_method->getConfigs();
            $payment_system->complete($pmconfigs, $order, $pm_method);
        }
    }

    /**
     * Callback, который вызывает сам ХуткиГрош для оповещение об оплате счета в ЕРИП
     * Тут выполняется дополнительная проверка статуса счета на шлюза и при необходимости изменение его статус заказа
     * в локальной БД
     */
    function notify()
    {
        try {
            $billId = $_REQUEST['purchaseid'];
            $order = pm_hg::getOrderByTrxId($billId);
            if (!isset($order) || !isset($order->order_id)) {
                throw new Exception('Hutkigrosh: Can not detect order by billid[' . $billId . "]");
            }
            $pm_method = $order->getPayment();
            $pmconfigs = $pm_method->getConfigs();
            $hg = new HutkiGrosh($pmconfigs[pm_hg::CONFIG_HG_SANDBOX]);
            $res = $hg->apiLogIn($pmconfigs[pm_hg::CONFIG_HG_LOGIN], $pmconfigs[pm_hg::CONFIG_HG_PASSWORD]);
            // Ошибка авторизации
            if (!$res) {
                $hg->apiLogOut(); // Завершаем сеанс
                throw new Exception('Hutkigrosh: login failed: ' . $hg->getError());
            }
            #дополнительно проверим статус счета в hg
            $hgBillInfo = $hg->apiBillInfo($billId);
            if (empty($hgBillInfo)) {
                $hg->apiLogOut(); // Завершаем сеанс
                throw new Exception('Hutkigrosh: login failed: ' . $hg->getError());
            } else {
                $localFullName = $order->f_name . ' ' . $order->l_name;
                if (trim($localFullName) != trim($hgBillInfo['fullName'])
                    || $order->order_total != $hgBillInfo['amt']) {
                    throw new Exception("Unmapped purchaseid: localFullname[" . $localFullName
                        . "], remoteFullname[" . $hgBillInfo['fullName']
                        . "], localAmount[" . $order->order_total
                        . "], remoteAmount[" . $hgBillInfo['amt']);
                }
                if ($hgBillInfo['statusEnum'] == 'Payed') {
                    $status = $pmconfigs[pm_hg::CONFIG_HG_BILL_STATUS_PAYED];
                } elseif (in_array($hgBillInfo['statusEnum'], array('Outstending', 'DeletedByUser', 'PaymentCancelled'))) {
                    $status = $pmconfigs[pm_hg::CONFIG_HG_BILL_STATUS_CANCELED];
                } elseif (in_array($hgBillInfo['statusEnum'], array('PaymentPending', 'NotSet'))) {
                    $status = $pmconfigs[pm_hg::CONFIG_HG_BILL_STATUS_PENDING];
                }
                if (isset($status) && $order->order_status != $status) {
                    $model = JSFactory::getModel('orderChangeStatus', 'jshop');
                    $model->setData($order->order_id, $status, 0); //тут можно включить sendmail
                    $model->store();
                }
            }

        } catch (Exception $e) {
            saveToLog("payment.log", $e->getMessage());
        }
    }
}