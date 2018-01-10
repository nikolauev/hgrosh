<?php
header('Content-Type: text/html; charset=utf-8');
include_once 'hutkigrosh_api.php';

class ControllerPaymentHutkiGrosh extends Controller
{
    // Транслитерация строк.
    const HUTKIGROSH_STOREID = 'hutkigrosh_storeid';
    const HUTKIGROSH_STORE_NAME = 'payment_hutkigrosh_store';
    const HUTKIGROSH_LOGIN = 'hutkigrosh_login';
    const HUTKIGROSH_PASSWORD = 'hutkigrosh_pswd';
    const HUTKIGROSH_SANDBOX = 'hutkigrosh_test';
    const HUTKIGROSH_MODULE_STATUS = 'payment_hutkigrosh_status';
    const HUTKIGROSH_MODULE_SORT_ORDER = 'payment_hutkigrosh_sort_order';
    const HUTKIGROSH_ORDER_STATUS_PENDING = 'hutkigrosh_order_status_pending';
    const HUTKIGROSH_ORDER_STATUS_PAYED = 'hutkigrosh_order_status_payed';
    const HUTKIGROSH_ORDER_STATUS_ERROR = 'hutkigrosh_order_status_error';
    const HUTKIGROSH_ERIP_TREE_PATH = 'hutkigrosh_erip_tree_path';
    const HUTKIGROSH_EMAIL_NOTIFICATION = 'hutkigrosh_email_notification';
    const HUTKIGROSH_SMS_NOTIFICATION = 'hutkigrosh_sms_notification';

    protected function index()
    {
        $this->language->load('payment/hutkigrosh');
        $this->data['text_testmode'] = $this->language->get('text_testmode');
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['testmode'] = $this->config->get(self::HUTKIGROSH_SANDBOX);
        $this->data['action'] = $this->url->link('payment/hutkigrosh/pay');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/hutkigrosh.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/hutkigrosh.tpl';
        } else {
            $this->template = 'default/template/payment/hutkigrosh.tpl';
        }
        $this->render();
    }


    public function pay()
    {
        //инициализируем URL для HG (тестовы/рабочий)
        try {
            $this->language->load('payment/hutkigrosh');

            if (!isset($this->session->data['order_id'])) {
                $this->redirect($this->url->link('checkout/checkout'));
                return false;
            }
            $this->load->model('checkout/order');
            $localOrderInfo = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get(self::HUTKIGROSH_ORDER_STATUS_PENDING));

            $this->test = $this->config->get(self::HUTKIGROSH_SANDBOX);
            $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($this->config->get(self::HUTKIGROSH_SANDBOX));
            $res = $hg->apiLogIn($this->config->get(self::HUTKIGROSH_LOGIN), $this->config->get(self::HUTKIGROSH_PASSWORD));

            // Ошибка авторизации
            if (!$res) {
                $error = $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                return $this->failure($error);
            }

            /// создаем заказ
            $line_items = $this->cart->getProducts();
            if (is_array($line_items)) {
                foreach ($line_items as $line_item) {
                    $arItem['invItemId'] = $line_item['key'];
                    $arItem['desc'] = $line_item['name'] . ' ' . $line_item['model'];
                    $arItem['count'] = round($line_item['quantity']);
                    $arItem['amt'] = $line_item['total'];
                    $arItems[] = $arItem;
                    unset($arItem);
                }
            }
//
            $billNewRq = new \Alexantr\HootkiGrosh\BillNewRq();
            $billNewRq->eripId = $this->config->get(self::HUTKIGROSH_STOREID);
            $billNewRq->invId = $localOrderInfo["order_id"];
            $billNewRq->fullName = $localOrderInfo['firstname'] . ' ' . $localOrderInfo['lastname'];
            $billNewRq->mobilePhone = $localOrderInfo['telephone'];
            $billNewRq->email = $localOrderInfo['email'];
            $billNewRq->fullAddress = $localOrderInfo['payment_address_1'] . ' ' . $localOrderInfo['payment_address_2'] . ' ' . $localOrderInfo['payment_zone'];
            $billNewRq->amount = $localOrderInfo['total'];
            $billNewRq->currency = $localOrderInfo['currency_code'];
            $billNewRq->notifyByEMail = $this->config->get(self::HUTKIGROSH_EMAIL_NOTIFICATION);
            $billNewRq->notifyByMobilePhone = $this->config->get(self::HUTKIGROSH_SMS_NOTIFICATION);
            $billNewRq->products = $arItems;


            $this->_billID = $hg->apiBillNew($billNewRq);
            if (!$this->_billID) {
                $error = $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                return $this->failure($error);
            }

            $webPayRq = new \Alexantr\HootkiGrosh\WebPayRq();
            $webPayRq->billId = $this->_billID;
            $webPayRq->returnUrl = $this->url->link('payment/hutkigrosh/callback') . "&" . "purchaseid=" . $this->_billID . "&status=complete";
            $webPayRq->cancelReturnUrl = $this->url->link('payment/hutkigrosh/callback') . "&" . "purchaseid=" . $this->_billID . "&status=error";

            $webpayform = $hg->apiWebPay($webPayRq);
            $hg->apiLogOut();

            $this->createPage($this->_billID, $localOrderInfo, $webpayform, null);
        } catch (Exception $e) {
            return $this->failure($e->getMessage());
        }
    }


    public function alfaclick()
    {
        $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($this->config->get(self::HUTKIGROSH_SANDBOX));
        $res = $hg->apiLogIn($this->config->get(self::HUTKIGROSH_LOGIN), $this->config->get(self::HUTKIGROSH_PASSWORD));
        if (!$res) {
            echo $hg->getError();
            $hg->apiLogOut();
            exit;
        }
        $alfaclickRq = new \Alexantr\HootkiGrosh\AlfaclickRq();
        $alfaclickRq->billId = $this->request->post['billid'];
        $alfaclickRq->phone = $this->request->post['phone'];

        $responceXML = $hg->apiAlfaClick($alfaclickRq);
        $hg->apiLogOut();
        echo intval($responceXML->__toString()) == '0' ? "error" : "ok";
    }


    protected function failure($error)
    {
        $this->session->data['error'] = $error;
        $this->response->redirect($this->url->link('checkout/cart', '', true));
    }

    public function callback()
    {
        try {
            $biilId = $this->request->get['purchaseid'];
            $this->checkOrderStatus($biilId);
            $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($this->config->get(self::HUTKIGROSH_SANDBOX));
            $res = $hg->apiLogIn($this->config->get(self::HUTKIGROSH_LOGIN), $this->config->get(self::HUTKIGROSH_PASSWORD));
            if (!$res) {
                $error = $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                throw new Exception($error);
            }
            $localOrderInfo = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $webPayRq = new \Alexantr\HootkiGrosh\WebPayRq();
            $webPayRq->billId = $biilId;
            $webPayRq->returnUrl = $this->url->link('payment/hutkigrosh/callback') . "&" . "purchaseid=" . $biilId . "&status=complete";
            $webPayRq->cancelReturnUrl = $this->url->link('payment/hutkigrosh/callback') . "&" . "purchaseid=" . $biilId . "&status=error";
            $webpayform = $hg->apiWebPay($webPayRq);
            $hg->apiLogOut();
            $this->createPage($biilId, $localOrderInfo, $webpayform, $this->language->get('text_webpay_error'));
        } catch (Exception $e) {
            return $this->failure($e->getMessage());
        }
    }

    public function notify()
    {
        try {
            $this->checkOrderStatus($this->request->get['purchaseid']);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }


    #уведомление об оплате
    protected function checkOrderStatus($purchaseid)
    {
        $this->language->load('payment/hutkigrosh');
        $pendingStatusId = $this->config->get(self::HUTKIGROSH_ORDER_STATUS_PENDING);
        $payedStatusId = $this->config->get(self::HUTKIGROSH_ORDER_STATUS_PAYED);
        $errorStatusId = $this->config->get(self::HUTKIGROSH_ORDER_STATUS_ERROR);

        if (!is_numeric($pendingStatusId) || !is_numeric($payedStatusId) || !is_numeric($errorStatusId)) {
            throw new Exception('Incorrect module configuration');
        }
        if (!isset($this->request->get['purchaseid'])) {
            throw new Exception('Wrong purchaseid');
        }

        $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($this->config->get(self::HUTKIGROSH_SANDBOX));
        $res = $hg->apiLogIn($this->config->get(self::HUTKIGROSH_LOGIN), $this->config->get(self::HUTKIGROSH_PASSWORD));
        if (!$res) {
            $error = $hg->getError();
            $hg->apiLogOut(); // Завершаем сеанс
            throw new Exception($error);
        }
        #дополнительно проверим статус счета в hg
        $hgBillInfo = $hg->apiBillInfo($purchaseid);
        if (empty($hgBillInfo)) {
            $error = $hg->getError();
            $hg->apiLogOut(); // Завершаем сеанс
            throw new Exception($error);
        } else {
            $this->load->model('checkout/order');
            $localOrderInfo = $this->model_checkout_order->getOrder($hgBillInfo['invId']);
            if ($localOrderInfo['firstname'] . ' ' . $localOrderInfo['lastname'] != $hgBillInfo['fullName']
                && $localOrderInfo['total'] != $hgBillInfo['amt']) {
                throw new Exception("Unmapped purchaseid");
            }
            if ($hgBillInfo['statusEnum'] == 'Payed') {
                if (is_numeric($payedStatusId))
                    $this->model_checkout_order->update(IntVal($hgBillInfo['invId']), $payedStatusId);
            } elseif (in_array($hgBillInfo['statusEnum'], array('Outstending', 'DeletedByUser', 'PaymentCancelled'))) {
                if (is_numeric($errorStatusId))
                    $this->model_checkout_order->update(IntVal($hgBillInfo['invId']), $errorStatusId);
            } elseif (in_array($hgBillInfo['statusEnum'], array('PaymentPending', 'NotSet'))) {
                if (is_numeric($pendingStatusId))
                    $this->model_checkout_order->update(IntVal($hgBillInfo['invId']), $pendingStatusId);
            }
        }
    }

    protected function createPage($billId, $localOrderInfo, $webpayform, $message)
    {
        $this->document->setTitle($this->language->get('heading_title'));
        $this->data['webpayform'] = $webpayform;
        $this->data['message'] = $message;

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('common/home'),
            'text' => $this->language->get('text_home'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/cart'),
            'text' => $this->language->get('text_basket'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/checkout', '', 'SSL'),
            'text' => $this->language->get('text_checkout'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['breadcrumbs'][] = array(
            'href' => $this->url->link('checkout/success'),
            'text' => $this->language->get('text_success'),
            'separator' => $this->language->get('text_separator')
        );

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_message'] = sprintf($this->language->get('text_erip_instruction'), $this->session->data['order_id'], $this->config->get('hutkigrosh_erip_tree_path'), $this->session->data['order_id']);
        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['continue'] = $this->url->link('checkout/success');

        $this->data['alfaclickbillID'] = $billId;
        $this->data['alfaclickTelephone'] = preg_replace("/[^0-9]/", '', $localOrderInfo['telephone']);
        $this->data['alfaclickUrl'] = $this->url->link('payment/hutkigrosh/alfaclick');

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );
        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . 'template/payment/hutkigrosh_checkout_success.tpl')) {
            $templateView = $this->config->get('config_template') . 'template/payment/hutkigrosh_checkout_success.tpl';
        } else {
            $templateView = 'default/template/payment/hutkigrosh_checkout_success.tpl';
        }

        $this->template = $templateView;
        $this->response->setOutput($this->render());
    }
}
