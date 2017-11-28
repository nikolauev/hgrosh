<?php
header('Content-Type: text/html; charset=utf-8');
include_once 'hutkigrosh_api.php';
class ControllerPaymentHutkiGrosh extends Controller {
	// Транслитерация строк.

    protected function index() {
        $this->language->load('payment/hutkigrosh');
        $this->data['text_testmode'] = $this->language->get('text_testmode');
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['testmode'] = $this->config->get('hutkigrosh_test');
        $this->data['action'] = $this->url->link('payment/hutkigrosh/pay');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/hutkigrosh.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/hutkigrosh.tpl';
        } else {
            $this->template = 'default/template/payment/hutkigrosh.tpl';
        }
        $this->render();
    }


    public function pay() {
        //инициализируем URL для HG (тестовы/рабочий)

        $this->language->load('payment/hutkigrosh');

        if(!isset($this->session->data['order_id'])) {
            $this->redirect($this->url->link('checkout/checkout'));
            return false;
        }
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('pending_status_id'));

//-----------------------------------------------------------------------------------------------------------------------------------------------
        $order_id = $order_info["order_id"];
        $this->_login = $this->config->get('hutkigrosh_login'); // имя пользователя
        $this->_pwd = $this->config->get('hutkigrosh_pswd'); // пароль
        $name = $this->_login;
        $pwd = $this->_pwd;

        $this->test = $this->config->get('hutkigrosh_test');
        $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($this->config->get('hutkigrosh_test'));
        $res = $hg->apiLogIn($name, $pwd);

        // Ошибка авторизации
        if (!$res) {
            echo $hg->getError();
            $hg->apiLogOut(); // Завершаем сеанс
            exit;
        }

        /// создаем заказ
        $line_items = $this->cart->getProducts();
        if(is_array($line_items)) {
            foreach ($line_items as $line_item) {
                $arItem['invItemId'] = $line_item['key'];
                $arItem['desc'] = $line_item['name']. ' '.$line_item['model'];
                $arItem['count'] = round($line_item['quantity']);
                $arItem['amt'] = $line_item['total'];
                $arItems[] = $arItem;
                unset($arItem);
            }
        }
//
        $total = $order_info['total'];
        $data = array(
            'eripId' => $this->config->get('hutkigrosh_storeid'),
            'invId' => $order_id,
            'fullName' => $order_info['firstname'].' '.$order_info['lastname'],
            'mobilePhone' => $order_info['telephone'],
            'email' => $order_info['email'],
            'fullAddress' => $order_info['payment_address_1'].' '.$order_info['payment_address_2'].' '.$order_info['payment_zone'],
            'amt' => $total,
            'curr'=> $order_info['currency_code'],
            'products' => $arItems
        );


        $this->_billID = $hg->apiBillNew($data);
        if (!$this->_billID) {
            echo $hg->getError();
            $hg->apiLogOut(); // Завершаем сеанс
            exit;
        }
        // выставляем счет в другие системы ------------------------------------------------------------------------------------------

        $orderData = array(
            'billId' => $this->_billID,
            'eripId' => $this->config->get('hutkigrosh_storeid'),
            'spClaimId' => $order_id,
            'amount' => $total,
            'currency' => 933,
            'clientFio' => $order_info['firstname'].' '.$order_info['lastname'],
            'clientAddress' => $order_info['payment_address_1'].' '.$order_info['payment_address_2'].' '.$order_info['payment_zone'],
            'returnUrl' => $this->url->link('payment/hutkigrosh/notify') . "&" . "purchaseid=" . $this->_billID,
            'cancelReturnUrl' => $this->url->link('payment/hutkigrosh/notify') . "&" . "purchaseid=" . $this->_billID,
        );

        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('hutkigrosh_order_status_pending'));

        $this->data['webpayform'] = $hg->apiWebPay($orderData);
        $hg->apiLogOut();
        $this->checkoutSuccess($order_info);

        //------------------------------------------------------------------------------------------------------------------------------------------------------------------
    }
    
    public function checkoutSuccess($order_info) {
        $this->document->setTitle($this->language->get('heading_title'));

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
        $this->data['text_message'] = sprintf($this->language->get('text_erip_instruction'), $this->session->data['order_id'], $this->session->data['order_id']);
        $this->data['button_continue'] = $this->language->get('button_continue');
        $this->data['continue'] = $this->url->link('checkout/success');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/hutkigrosh_checkout_success.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/hutkigrosh_checkout_success.tpl';
        } else {
            $this->template = 'default/template/payment/hutkigrosh_checkout_success.tpl';
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );
        
        $this->data['alfaclickbillID'] = $this->_billID;
//        $this->data['alfaclickBaseUrl'] = $this->base_url;
        $this->data['alfaclickTelephone'] = preg_replace("/[^0-9]/", '', $order_info['telephone']);

        $this->response->setOutput($this->render());
    }

    public function alfaclick(){
        $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($this->config->get('hutkigrosh_test'));
        $res = $hg->apiLogIn($this->config->get('hutkigrosh_login'), $this->config->get('hutkigrosh_pswd'));
        if (!$res) {
            echo $hg->getError();
            $hg->apiLogOut();
            exit;
        }
        $responceXML = $hg->apiAlfaClick($this->request->post['billid'], $this->request->post['phone']);
        $hg->apiLogOut();
        echo $responceXML->__toString();
    }

	# нажатие кнопки "<< Назад в магазин" 
	public function fail() {
		$this->redirect($this->url->link('checkout/checkout'));
		return TRUE;
	}

	# перенаправление клиента после оплаты
	public function success() {
		$this->redirect($this->url->link('checkout/success'));
		return TRUE;		
	}

	#уведомление об оплате
	public function notify() {
        $pendingStatusId = $this->config->get('hutkigrosh_order_status_pending');
        $payedStatusId = $this->config->get('hutkigrosh_order_status_payed');
        $errorStatusId = $this->config->get('hutkigrosh_order_status_error');

        if(is_numeric($pendingStatusId) || is_numeric($payedStatusId) || is_numeric($errorStatusId)){
            if (isset($this->request->get['purchaseid'])) {
                $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($this->config->get('hutkigrosh_test'));
                $res = $hg->apiLogIn($this->config->get('hutkigrosh_login'), $this->config->get('hutkigrosh_pswd'));
                if (!$res) {
                    echo $hg->getError();
                    $hg->apiLogOut();
                    exit;
                }
                #дополнительно проверим статус счета в hg
                $info = $hg->apiBillInfo($this->request->get['purchaseid']);
                if(empty($info)){
                    echo $hg->getError();
                }else{
                    $this->load->model('checkout/order');
                    if($info['statusEnum']=='Payed'){
                        if(is_numeric($payedStatusId))
                            $this->model_checkout_order->update(IntVal($info['invId']), $payedStatusId);
                    }elseif(in_array($info['statusEnum'],array('Outstending','DeletedByUser','PaymentCancelled'))){
                        if(is_numeric($errorStatusId))
                            $this->model_checkout_order->update(IntVal($info['invId']), $errorStatusId);
                    }elseif(in_array($info['statusEnum'],array('PaymentPending','NotSet'))){
                        if(is_numeric($pendingStatusId))
                            $this->model_checkout_order->update(IntVal($info['invId']), $pendingStatusId);
                    }
                }
                $hg->apiLogOut();
            }
        }
	}
}
