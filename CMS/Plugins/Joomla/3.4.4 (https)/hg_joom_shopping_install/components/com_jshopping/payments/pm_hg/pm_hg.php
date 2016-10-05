<?php
/*
* @info Платёжный модуль Hutki grosh для JoomShopping
* @package JoomShopping for Joomla!
* @subpackage payment
* @author Hutki grosh
*/

defined('_JEXEC') or die('Restricted access');

class pm_hg extends PaymentRoot
{
    function loadLanguageFile()
    {
        $lang = JFactory::getLanguage();
        $langtag = $lang->getTag();

        if (file_exists(JPATH_ROOT.'/components/com_jshopping/payments/pm_hg/lang/'.$langtag.'.php')) {
            require_once(JPATH_ROOT.'/components/com_jshopping/payments/pm_hg/lang/'.$langtag.'.php');
        } else {
            require_once(JPATH_ROOT.'/components/com_jshopping/payments/pm_hg/lang/ru-RU.php'); //если языковый файл не найден, то подключаем ru-RU.php
        }
    }

    function showAdminFormParams($params)
    {		$array_params = array(
        'hgrosh_test', 'hgrosh_store_id', 'hgrosh_store', 'hgrosh_login', 'hgrosh_pswd',
        'hgrosh_return_url', 'hgrosh_cancel_return_url', 'hgrosh_system_url', 'hgrosh_ssl',
        'transaction_end_status', 'transaction_pending_status', 'transaction_failed_status'
    );

        foreach ($array_params as $key) {
            if (!isset($params[$key])) {
                $params[$key] = '';
            }
        }

        $orders = JModelLegacy::getInstance('orders', 'JshoppingModel');

        $this->loadLanguageFile();

        include(dirname(__FILE__).'/adminparamsform.php');
    }

    function nofityFinish($pmconfigs, $order, $rescode)
    {
        include(dirname(__FILE__)."/hg_notify.php");
    }

    function checkTransaction($pmconfigs, $order, $act){
        return array(1, '');
    }

    function showEndForm($pmconfigs, $order)
    {
        require 'HootkiGrosh.php';
        $is_test = ($pmconfigs['hgrosh_test'] == 1) ? true : false; // тестовый api

        $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($is_test);
//        ?>
<!--        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>-->
<!--        --><?//
        $order_id = $order->order_id;
        $this->_login = $pmconfigs['hgrosh_login']; // имя пользователя
        $this->_pwd = $pmconfigs['hgrosh_pswd']; // пароль
        $name = $this->_login;
        $pwd = $this->_pwd;
        $res = $hg->apiLogIn($name, $pwd);
        $_SESSION['hg_login'] = $this->_login;
        $_SESSION['hg_pwd'] = $this->_pwd;
        $_SESSION['hg_test'] = $is_test;
        // Ошибка авторизации
        if (!$res) {
            echo $hg->getError();
            $hg->apiLogOut(); // Завершаем сеанс
            exit;
        }

        /// создаем заказ
        $line_items = $order->getAllItems();
        if(is_array($line_items)) {
            foreach ($line_items as $line_item) {
                $arItem['invItemId'] = $line_item->product_name;
                $arItem['desc'] = $line_item->product_name;
                $arItem['count'] = round($line_item->product_quantity);
                $arItem['amt'] = $line_item->product_item_price;
                $arItems[] = $arItem;
                unset($arItem);
            }
        }


        $data = array(
            'eripId' => $pmconfigs['hgrosh_store_id'],
            'invId' => $order_id,
            'fullName' => $order->f_name.' '.$order->l_name,
            'mobilePhone' => $order->phone,
            'email' => $order->email,
            'fullAddress' => $order->city.' '.$order->state.' '.$order->street,
            'amt' => $order->order_total,
            'curr'=> $order->currency_code,
            'products' => $arItems
        );

        $this->_billID = $hg->apiBillNew($data);
        if (!$this->_billID) {
            echo $hg->getError();
            $hg->apiLogOut(); // Завершаем сеанс
            exit;
        }
        $dataBgpb = array(
            'billId' => $this->_billID,
            'eripId' => $pmconfigs['hgrosh_store_id'],
            'spClaimId' => $order_id,
            'amount' => $order->order_total,
            'currency' => 933,
            'clientFio' => $order->f_name.' '.$order->l_name,
            'clientAddress' => $order->city.' '.$order->state.' '.$order->street,
            'returnUrl' => $pmconfigs['hgrosh_return_url'],
            'cancelReturnUrl' => $pmconfigs['hgrosh_cancel_return_url'],
        );

        echo '<h1>Спасибо за заказ!</h2>';
        echo '<h1>Счет для оплаты в системе ЕРИП: ' . $order_id . '</h2>';
        echo $hg->apiBgpbPay($dataBgpb);
        ?>
        <br>
        <hr>
        <div class="alfaclick">
            <input type="hidden" value="<?=$this->_billID?>" id="billID">
            <input type="hidden" value="<?=$this->base_url?>" id="cookie">
            <input type="text" maxlength="20" value="<?=$order->phone?>" id="phone">
            <button>Выставить счет в AlfaClick</button>
        </div>
        <script>
            jQuery(document).on('click','button',function(){
                jQuery.post('/hgrosh/alfaclick.php',
                    {
                        phone : jQuery('#phone').val(),
                        billid : jQuery('#billID').val()
                    }
                ).done(function(data){
                        if(data == '0')
                            alert('Не удалось выставить счет в системе AlfaClick');
                        else
                            alert('Выставлен счет в системе AlfaClick');
                    });
            });
        </script><?
        $hg->apiLogOut();
    }


    function getUrlParams($pmconfigs)
    {
        $params = array();
        require 'HootkiGrosh.php';
        $is_test = ($pmconfigs['hgrosh_test'] == 1) ? true : false; // тестовый api

        $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($is_test);
        $hg_data = JRequest::get('get');
        if(isset($hg_data["purchaseid"]))
        {
            $res = $hg->apiLogIn($pmconfigs['hgrosh_login'], $pmconfigs['hgrosh_pswd']);

            // Ошибка авторизации
            if (!$res) {
                echo $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                exit;
            }

            $info = $hg->apiBillInfo($hg_data["purchaseid"]);
            if (!$info) {
                echo $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                exit;
            }
            if (!$info) {
                echo $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                exit;
            }


            $params['order_id'] = IntVal($info['invId']);
            $params['hash'] = '';
            $params['checkHash'] = false;
            $params['checkReturnParams'] = ($params['order_id']>0 ? true : false);
        }
        return $params;
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////


}