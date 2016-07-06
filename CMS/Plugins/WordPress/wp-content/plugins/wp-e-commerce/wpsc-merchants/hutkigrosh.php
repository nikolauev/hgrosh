<?php
header('Content-Type: text/html; charset=utf-8');
$nzshpcrt_gateways[$num]['name'] = 'HutkiGrosh';
$nzshpcrt_gateways[$num]['internalname'] = 'hutkigrosh';
$nzshpcrt_gateways[$num]['function'] = 'gateway_hutkigrosh';
$nzshpcrt_gateways[$num]['form'] = "form_hutkigrosh";
$nzshpcrt_gateways[$num]['submit_function'] = "submit_hutkigrosh";
$nzshpcrt_gateways[$num]['payment_type'] = "ERIP";
$nzshpcrt_gateways[$num]['display_name'] = 'ERIP';

include_once  'class_hutkigrosh.php';


function form_hutkigrosh()
{	
	$hutkigrosh_return_url = ( get_option('hutkigrosh_return_url')=='' ? 'http://'.$_SERVER['SERVER_NAME'].'/?hutkigrosh_callback=1&hutkigrosh_action=return' : get_option('hutkigrosh_return_url') );
	$hutkigrosh_cancel_return_url = ( get_option('hutkigrosh_cancel_return_url')=='' ? 'http://'.$_SERVER['SERVER_NAME'].'/?hutkigrosh_callback=1&hutkigrosh_action=cancel' : get_option('hutkigrosh_cancel_return_url') );
	$hutkigrosh_system_url = ( get_option('hutkigrosh_system_url')=='' ? 'http://'.$_SERVER['SERVER_NAME'].'/?hutkigrosh_callback=1&hutkigrosh_action=system' : get_option('hutkigrosh_system_url') );
	$hutkigrosh_ssl = ( get_option('hutkigrosh_ssl')=='' ? 'http://'.$_SERVER['SERVER_NAME'].'/hutkigrosh/ssl/cacert.pem' : get_option('hutkigrosh_ssl') );
	
	$hutkigrosh_debug = get_option('hutkigrosh_debug');
	$hutkigrosh_debug1 = "";
	$hutkigrosh_debug2 = "";
	switch($hutkigrosh_debug)
	{
		case 0:
			$hutkigrosh_debug2 = "checked ='checked'";
			break;
		case 1:
			$hutkigrosh_debug1 = "checked ='checked'";
			break;
	}
	
	$output = "
		<tr>
			<td>Уникальный идентификатор услуги в системе УРИП</td>
			<td><input type='text' size='40' value='".get_option('hutkigrosh_storeid')."' name='hutkigrosh_storeid' /></td>
		</tr>
		<tr>
			<td>Название магазина</td>
			<td><input type='text' size='40' value='".get_option('hutkigrosh_store')."' name='hutkigrosh_store' /></td>
		</tr>
		<tr>
			<td>Логин интернет-магазина</td>
			<td><input type='text' size='40' value='".get_option('hutkigrosh_login')."' name='hutkigrosh_login' /></td>
		</tr>
		<tr>
			<td>Пароль Интернет магазина</td>
			<td><input type='text' size='40' value='".get_option('hutkigrosh_pswd')."' name='hutkigrosh_pswd' /></td>
		</tr>
		<tr>
			<td>Режим песочницы</td>
			<td>
				<input type='radio' value='1' name='hutkigrosh_debug' id='hutkigrosh_debug1' ".$hutkigrosh_debug1." /> <label for='hutkigrosh_debug1'>".__('Да', 'wpsc')."</label> &nbsp;
				<input type='radio' value='0' name='hutkigrosh_debug' id='hutkigrosh_debug2' ".$hutkigrosh_debug2." /> <label for='hutkigrosh_debug2'>".__('Нет', 'wpsc')."</label>
			</td>
		</tr>	
		<tr>
			<td>Url магазина для успешного возврата</td>
			<td><input type='text' size='40' value='".$hutkigrosh_return_url."' name='hutkigrosh_return_url' /></td>
		</tr>
		<tr>
			<td>Url магазина для неуспешного возврата</td>
			<td><input type='text' size='40' value='".$hutkigrosh_cancel_return_url."' name='hutkigrosh_cancel_return_url' /></td>
		</tr>
		<tr>
			<td>Url магазина для  возврата с сообщением</td>
			<td><input type='text' size='40' value='".$hutkigrosh_system_url."' name='hutkigrosh_system_url' /></td>
		</tr>
		<tr>
			<td>Путь к SSL</td>
			<td><input type='text' size='40' value='".$hutkigrosh_ssl."' name='hutkigrosh_ssl' /></td>
		</tr>
	";
	return $output;
}
function submit_hutkigrosh()
{  
	if(isset($_POST['hutkigrosh_storeid']))
    {
    	update_option('hutkigrosh_storeid', $_POST['hutkigrosh_storeid']);
    }
    
  	if(isset($_POST['hutkigrosh_store']))
    {
    	update_option('hutkigrosh_store', $_POST['hutkigrosh_store']);
    }
    
  	if(isset($_POST['hutkigrosh_login']))
    {
    	update_option('hutkigrosh_login', $_POST['hutkigrosh_login']);
    }
    
  	if(isset($_POST['hutkigrosh_pswd']))
    {
    	update_option('hutkigrosh_pswd', $_POST['hutkigrosh_pswd']);
    }
    
  	if(isset($_POST['hutkigrosh_debug']))
    {
    	update_option('hutkigrosh_debug', $_POST['hutkigrosh_debug']);
    }

 	if(isset($_POST['hutkigrosh_return_url']))
    {
    	update_option('hutkigrosh_return_url', $_POST['hutkigrosh_return_url']);
    }

  	if(isset($_POST['hutkigrosh_cancel_return_url']))
    {
    	update_option('hutkigrosh_cancel_return_url', $_POST['hutkigrosh_cancel_return_url']);
    }

 	if(isset($_POST['hutkigrosh_system_url']))
    {
    	update_option('hutkigrosh_system_url', $_POST['hutkigrosh_system_url']);
    }

  	if(isset($_POST['hutkigrosh_ssl']))
    {
    	update_option('hutkigrosh_ssl', $_POST['hutkigrosh_ssl']);
    }
	
	return true;
}
//функция инициализации оплаты.
function gateway_hutkigrosh($separator, $sessionid)
{
	global $wpdb, $wpsc_cart, $wpsc_gateways;
	
	$purchase_log_sql = "SELECT * FROM `".WPSC_TABLE_PURCHASE_LOGS."` WHERE `sessionid`= ".$sessionid." LIMIT 1";
	$purchase_log = $wpdb->get_results($purchase_log_sql,ARRAY_A) ;
	
	$order_id = $purchase_log[0]["id"];
	$amount_ceil = round($purchase_log[0]["totalprice"]);

	$cart_sql = "SELECT * FROM `".WPSC_TABLE_CART_CONTENTS."` WHERE `purchaseid`='".$order_id."'";
	$cart = $wpdb->get_results($cart_sql,ARRAY_A);

    $submited_form_data = "SELECT * FROM `wp_wpsc_submited_form_data` WHERE `log_id`=".$order_id;
    $submited_form_data_value = $wpdb->get_results($submited_form_data,ARRAY_A);

    $checkout_forms = "SELECT * FROM `wp_wpsc_checkout_forms`";
    $checkout_forms_value = $wpdb->get_results($checkout_forms,ARRAY_A);

    foreach($submited_form_data_value as $check_form){
        $submit_form[$check_form['form_id']] = $check_form;
    }

    foreach($checkout_forms_value as $form_val){
        $arForm[$form_val['unique_name']] = $submit_form[$form_val['id']]['value'];
    }

	$pathSSL = (strlen(get_option("uc_hutkigrosh_ssl"))>0 ? get_option("uc_hutkigrosh_ssl") : 'http://'.$_SERVER['SERVER_NAME'].'/hutkigrosh/ssl/cacert.pem');


	//инициализация класса
	$test_mode = get_option('hutkigrosh_debug');

    $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($test_mode);

	$arParams['ap_storeid'] = get_option('hutkigrosh_storeid');
	$arParams['ap_order_num'] = $order_id;
	$arParams['ap_currency_id'] = "974";
	$arParams['ap_return_url'] = htmlspecialchars(get_option('hutkigrosh_return_url'));
	$arParams['ap_cancel_return_url'] = htmlspecialchars(get_option('hutkigrosh_cancel_return_url')."&hutkigrosh_session=".$sessionid);
	$arParams['ap_system_url'] = htmlspecialchars(get_option('hutkigrosh_system_url'));
	$arParams['ap_test'] = get_option('hutkigrosh_debug');
	$arParams['ap_total'] = $amount_ceil;
	$arParams['login'] = get_option('hutkigrosh_login');
	$arParams['pswd'] = get_option('hutkigrosh_pswd');
	//логинимся
    //------------------------------------------------------------------------------------------------------------------
//    $checkout_forms2 = "show tables";
//    $checkout_forms_value2 = $wpdb->get_results($checkout_forms2,ARRAY_A);
//    $checkout_forms23 = "SELECT * FROM `ga1f6_jshopping_payment_method`";
//    $checkout_forms_value23 = $wpdb->get_results($checkout_forms23,ARRAY_A);
//    echo '<pre>';
//    print_r(get_option('hutkigrosh_login'));
//    echo '<pre>';
//    print_r($checkout_forms_value2);
//    exit();
    //------------------------------------------------------------------------------------------------------------------
    $res = $hg->apiLogIn($arParams['login'], $arParams['pswd']);
	// Ошибка авторизации
	if (!$res) {
		echo $hg->getError();
		$hg->apiLogOut(); // Завершаем сеанс
		exit;
	}
    $arItems = array();
        /// создаем заказ
	if(is_array($cart)) {
		foreach ($cart as $line_item) {
			$arItem['invItemId'] = $line_item['prodid'];
			$arItem['desc'] = $line_item['name'];
			$arItem['count'] = round($line_item['quantity']);
			$arItem['amt'] = $line_item['price'];
			$arItems[] = $arItem;
			unset($arItem);
		}
	}
//
        $total = $purchase_log[0]["totalprice"];
        $data = array(
            'eripId' => $arParams['ap_storeid'],
            'invId' => $order_id,
            'fullName' => $arForm['billingfirstname'].' '.$arForm['billinglastname'],
            'mobilePhone' => $arForm['billingphone'],
            'email' => $arForm['billingemail'],
            'fullAddress' => $arForm['billingstate'].' '.$arForm['billingcity'].' '.$arForm['billingaddress'],
            'amt' => $total,
            'curr'=> 'BYN',
            'products' => $arItems
        );


        $billID = $hg->apiBillNew($data);
        if (!$billID) {
            echo $hg->getError();
            $hg->apiLogOut(); // Завершаем сеанс
            exit;
        }
        // выставляем счет в другие системы ------------------------------------------------------------------------------------------

//        $dataBgpb = array(
//            'billId' => $billID,
//            'paymentId' => 1234567890,
//            'spClaimId' => $order_id,
//            'amount' => $total,
//            'currency' => 974,
//            'clientFio' => $arForm['billingfirstname'].' '.$arForm['billinglastname'],
//            'clientAddress' => $arForm['billingstate'].' '.$arForm['billingcity'].' '.$arForm['billingaddress'],
//            'returnUrl' => $arParams['ap_return_url'],
//            'cancelReturnUrl' => $arParams['ap_cancel_return_url'],
//        );

        echo '<h1>Спасибо за заказ!</h2>';
        echo '<h1>Счет для оплаты в системе ЕРИП: ' . $order_id . '</h2>';
        echo '<hr>';
//        print_r($hg->apiBgpbPay($dataBgpb));
        ?>

    <hr>
    <a href="/">Вернуться на сайт.</a>
    <hr>
        <?
        $hg->apiLogOut();
        exit();

}
function nzshpcrt_hutkigrosh_callback()
{
    date_default_timezone_set('Europe/Minsk');
    global $wpdb;
    if(isset($_GET['hutkigrosh_callback']) && ($_GET['hutkigrosh_callback'] == 1))
    {
        if(isset($_GET['purchaseid']))
        {
            $login = get_option('hutkigrosh_login');
            $pwd = get_option('hutkigrosh_pswd');
            $mode = get_option('hutkigrosh_debug');
            $billID = $_GET['purchaseid'];
            $arError = array();
            $hg = new \Alexantr\HootkiGrosh\HootkiGrosh($mode);

            $res = $hg->apiLogIn($login, $pwd);

            // Ошибка авторизации
            if (!$res) {
                $arError[] = $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
            }
            // получаем информацию о счете

            $info = $hg->apiBillInfo($billID);
            if (!$info) {
                $arError[] = $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
//                exit;
            }
            $text = '';
            foreach($arError as $k){
                $text = ' '.$k;
            }
            $ORDER_ID = $info['invId'];

            $notes = "Оплачено через hutkigrosh. Номер платежа: " . $ORDER_ID. "; Дата платежа ".date("Y-m-d H:i:s");
            $wpdb->query("UPDATE " . WPSC_TABLE_PURCHASE_LOGS . " SET processed = '3', date = '" . time() . "', notes = '" . $notes . "' WHERE id = " . $ORDER_ID . " LIMIT 1");
        }
    }
}

add_action('init', 'nzshpcrt_hutkigrosh_callback');

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////


