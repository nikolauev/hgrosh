<?php
header('Content-Type: text/html; charset=utf-8');
include_once  'class_hutkigrosh.php';
class ControllerPaymentHutkiGrosh extends Controller {
	// Транслитерация строк.

    public function index() {
        $this->language->load('payment/hutkigrosh');
        $data['text_testmode'] = $this->language->get('text_testmode');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['testmode'] = $this->config->get('hutkigrosh_test');
        $data['action'] = $this->url->link('payment/hutkigrosh/send');

//        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/hutkigrosh.tpl')) {
//            $this->template = $this->config->get('config_template') . '/template/payment/hutkigrosh.tpl';
//        } else {
//            $this->template = 'default/template/payment/hutkigrosh.tpl';
//        }
        return $this->load->view('payment/hutkigrosh', $data);
//        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/hutkigrosh.tpl')) {
//            return $this->load->view($this->config->get('config_template') . '/template/payment/hutkigrosh.tpl', $data);
//        } else {
//            return $this->load->view('default/template/payment/hutkigrosh.tpl', $data);
//        }
//        $this->render();
    }


    public function send() {
        //инициализируем URL для HG (тестовы/рабочий)

        $this->language->load('payment/hutkigrosh');

        if(!isset($this->session->data['order_id'])) {
            $this->redirect($this->url->link('checkout/checkout'));
            return false;
        }
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
//		$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('pending_status_id'));

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
                $arItem['invItemId'] = $line_item['product_id'];
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

        $dataBgpb = array(
            'billId' => $this->_billID,
            'paymentId' => 1234567890,
            'spClaimId' => $order_id,
            'amount' => $total,
            'currency' => 974,
            'clientFio' => $order_info['firstname'].' '.$order_info['lastname'],
            'clientAddress' => $order_info['payment_address_1'].' '.$order_info['payment_address_2'].' '.$order_info['payment_zone'],
            'returnUrl' => $this->url->link('payment/hutkigrosh/notify'),
            'cancelReturnUrl' => $this->url->link('payment/hutkigrosh/fail'),
        );
//        $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('cod_order_status_id'), '', false);
//        $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('cod_order_status_id'));


        echo '<h1>Спасибо за заказ!</h2>';
        echo '<h1>Счет для оплаты в системе ЕРИП: ' . $order_id . '</h2>';
        echo '<hr>';
        $hg->apiLogOut();
?>
        <div class="buttons">
            <div class="pull-right"><a href= "<?=HTTP_SERVER?>index.php?route=checkout/success" class="btn btn-primary">Продолжить</a></div>
        </div>
        <?

        //------------------------------------------------------------------------------------------------------------------------------------------------------------------
    }
    
	# нажатие кнопки "<< Назад в магазин" 
	public function fail() {
        $this->response->redirect($this->url->link('checkout/checkout'));
		return TRUE;
	}

	# перенаправление клиента после оплаты
	public function success() {
        $this->response->redirect($this->url->link('checkout/success'));
		return TRUE;
        }

    public function confirm() {
        if ($this->session->data['payment_method']['code'] == 'hutkigrosh') {
            $this->load->model('checkout/order');

//            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('cod_order_status_id'));
        }
    }

        #уведомление об оплате
        public function notify() {
            $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($this->config->get('hutkigrosh_test'));
            $this->log->write($this->request->get['purchaseid']);
            if(isset($this->request->get["purchaseid"])) {
                $this->_login = $this->config->get('hutkigrosh_login'); // имя пользователя
                $this->_pwd = $this->config->get('hutkigrosh_pswd'); // пароль
                $name = $this->_login;
                $pwd = $this->_pwd;
                $res = $hg->apiLogIn($name, $pwd);

            // Ошибка авторизации
            if (!$res) {
                echo $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                exit;
            }

            $info = $hg->apiBillInfo($this->request->get["purchaseid"]);
            if (!$info) {
                echo $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                exit;
            }
			$order_mer_code = IntVal($info['invId']);
			$this->load->model('checkout/order');

            $this->model_checkout_order->addOrderHistory($order_mer_code, 2);


		}else{
                echo 'hello';
            }
	}


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    private $base_url; // url api
    private $test;


}
