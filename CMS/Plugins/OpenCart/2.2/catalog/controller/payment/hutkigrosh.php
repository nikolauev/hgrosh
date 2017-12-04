<?php
header('Content-Type: text/html; charset=utf-8');
include_once 'hutkigrosh_api.php';

class ControllerPaymentHutkiGrosh extends Controller
{
    // Транслитерация строк.

    public function index()
    {
        $this->language->load('payment/hutkigrosh');
        $data['text_testmode'] = $this->language->get('text_testmode');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['text_loading'] = $this->language->get('text_loading');
        $data['testmode'] = $this->config->get('hutkigrosh_test');
        $data['action'] = $this->url->link('payment/hutkigrosh/pay');
        $data['continue'] = $this->url->link('checkout/success');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . 'payment/hutkigrosh.tpl')) {
            return $this->load->view($this->config->get('config_template') . 'payment/hutkigrosh.tpl', $data);
        } else {
            return $this->load->view('payment/hutkigrosh.tpl', $data);
        }
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
            // confirm ??
            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('pending_status_id'));
            $order_id = $localOrderInfo["order_id"];
            $this->_login = $this->config->get('hutkigrosh_login'); // имя пользователя
            $this->_pwd = $this->config->get('hutkigrosh_pswd'); // пароль
            $name = $this->_login;
            $pwd = $this->_pwd;

            $this->test = $this->config->get('hutkigrosh_test');
            $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($this->config->get('hutkigrosh_test'));
            $res = $hg->apiLogIn($name, $pwd);

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
                    $arItem['invItemId'] = $line_item['product_id'];
                    $arItem['desc'] = $line_item['name'] . ' ' . $line_item['model'];
                    $arItem['count'] = round($line_item['quantity']);
                    $arItem['amt'] = $line_item['total'];
                    $arItems[] = $arItem;
                    unset($arItem);
                }
            }
//
            $billNewRq = new \Alexantr\HootkiGrosh\BillNewRq();
            $billNewRq->eripId = $this->config->get('hutkigrosh_storeid');
            $billNewRq->invId = $order_id;
            $billNewRq->fullName = $localOrderInfo['firstname'] . ' ' . $localOrderInfo['lastname'];
            $billNewRq->mobilePhone = $localOrderInfo['telephone'];
            $billNewRq->email = $localOrderInfo['email'];
            $billNewRq->fullAddress = $localOrderInfo['payment_address_1'] . ' ' . $localOrderInfo['payment_address_2'] . ' ' . $localOrderInfo['payment_zone'];
            $billNewRq->amount = $localOrderInfo['total'];
            $billNewRq->currency = $localOrderInfo['currency_code'];
            $billNewRq->products = $arItems;


            $this->_billID = $hg->apiBillNew($billNewRq);
            if (!$this->_billID) {
                $error = $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                return $this->failure($error);
            }

            $webPayRq = new \Alexantr\HootkiGrosh\WebPayRq();
            $webPayRq->billId = $this->_billID;
            $webPayRq->returnUrl = $this->url->link('payment/hutkigrosh/notify') . "&" . "purchaseid=" . $this->_billID . "&status=complete";
            $webPayRq->cancelReturnUrl = $this->url->link('payment/hutkigrosh/notify') . "&" . "purchaseid=" . $this->_billID . "&status=error";

            $webpayform = $hg->apiWebPay($webPayRq);
            $hg->apiLogOut();

            $this->createPage($this->_billID, $localOrderInfo, $webpayform, null);
        } catch (Exception $e) {
            return $this->failure($e->getMessage());
        }
    }


    public function alfaclick()
    {
        $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($this->config->get('hutkigrosh_test'));
        $res = $hg->apiLogIn($this->config->get('hutkigrosh_login'), $this->config->get('hutkigrosh_pswd'));
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
        echo $responceXML->__toString();
    }

    # нажатие кнопки "<< Назад в магазин"
    public function fail()
    {
        $this->response->redirect($this->url->link('checkout/checkout'));
        return TRUE;
    }

    protected function failure($error)
    {
        $this->session->data['error'] = $error;
        $this->response->redirect($this->url->link('checkout/cart', '', true));
    }

    # перенаправление клиента после оплаты
    public function success()
    {
        $this->response->redirect($this->url->link('checkout/success'));
        return TRUE;
    }

    #уведомление об оплате
    public function notify()
    {
        try {
            $this->language->load('payment/hutkigrosh');
            $pendingStatusId = $this->config->get('hutkigrosh_order_status_pending');
            $payedStatusId = $this->config->get('hutkigrosh_order_status_payed');
            $errorStatusId = $this->config->get('hutkigrosh_order_status_error');
            $paystatus = $this->request->get['status'];

            if (!is_numeric($pendingStatusId) || !is_numeric($payedStatusId) || !is_numeric($errorStatusId)) {
                throw new Exception('Incorrect module configuration');
            }
            if (!isset($this->request->get['purchaseid'])) {
                throw new Exception('Wrong purchaseid');
            }

            $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($this->config->get('hutkigrosh_test'));
            $res = $hg->apiLogIn($this->config->get('hutkigrosh_login'), $this->config->get('hutkigrosh_pswd'));

            if (!$res) {
                $error = $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                return $this->failure($error);
            }
            #дополнительно проверим статус счета в hg
            $hgBillInfo = $hg->apiBillInfo($this->request->get['purchaseid']);
            if (empty($hgBillInfo)) {
                $error = $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                return $this->failure($error);
            } else {
                $this->load->model('checkout/order');
                if ($hgBillInfo['statusEnum'] == 'Payed') {
                    if (is_numeric($payedStatusId))
                        $this->model_checkout_order->addOrderHistory(IntVal($hgBillInfo['invId']), $payedStatusId);
                } elseif (in_array($hgBillInfo['statusEnum'], array('Outstending', 'DeletedByUser', 'PaymentCancelled')) or $paystatus == "error") {
                    if (is_numeric($errorStatusId))
                        $this->model_checkout_order->addOrderHistory(IntVal($hgBillInfo['invId']), $errorStatusId);
                } elseif (in_array($hgBillInfo['statusEnum'], array('PaymentPending', 'NotSet'))) {
                    if (is_numeric($pendingStatusId))
                        $this->model_checkout_order->addOrderHistory(IntVal($hgBillInfo['invId']), $pendingStatusId);
                }
            }
//                $hg->apiLogOut();

            $localOrderInfo = $this->model_checkout_order->getOrder($this->session->data['order_id']);
            $webPayRq = new \Alexantr\HootkiGrosh\WebPayRq();
            $webPayRq->billId = $hgBillInfo['billID'];
            $webPayRq->returnUrl = $this->url->link('payment/hutkigrosh/notify') . "&" . "purchaseid=" . $hgBillInfo['billID'] . "&status=complete";
            $webPayRq->cancelReturnUrl = $this->url->link('payment/hutkigrosh/notify') . "&" . "purchaseid=" . $hgBillInfo['billID'] . "&status=error";
            $webpayform = $hg->apiWebPay($webPayRq);
            $hg->apiLogOut();
            $this->createPage($hgBillInfo['billID'], $localOrderInfo, $webpayform, $this->language->get('text_webpay_error'));
        } catch (Exception $e) {
            return $this->failure($e->getMessage());
        }
    }

    protected function createPage($billId, $localOrderInfo, $webpayform, $message)
    {
        $this->document->setTitle($this->language->get('heading_title'));
        $data['webpayform'] = $webpayform;
        $data['message'] = $message;

        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_basket'),
            'href' => $this->url->link('checkout/cart')
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_checkout'),
            'href' => $this->url->link('checkout/checkout', '', true)
        );
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_success'),
            'href' => $this->url->link('checkout/success')
        );

        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_message'] = sprintf($this->language->get('text_erip_instruction'), $this->session->data['order_id'], $this->config->get('hutkigrosh_erip_tree_path'), $this->session->data['order_id']);
        $data['button_continue'] = $this->language->get('button_continue');
        $data['continue'] = $this->url->link('checkout/success');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        $data['alfaclickbillID'] = $billId;
        $data['alfaclickTelephone'] = preg_replace("/[^0-9]/", '', $localOrderInfo['telephone']);
        $data['alfaclickUrl'] = $this->url->link('payment/hutkigrosh/alfaclick');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . 'payment/hutkigrosh_checkout_success.tpl')) {
            $templateView = $this->config->get('config_template') . 'payment/hutkigrosh_checkout_success.tpl';
        } else {
            $templateView = 'payment/hutkigrosh_checkout_success.tpl';
        }
        $this->response->setOutput($this->load->view($templateView, $data));
    }
}
