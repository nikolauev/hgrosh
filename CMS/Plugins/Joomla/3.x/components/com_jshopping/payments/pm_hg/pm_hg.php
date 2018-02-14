<?php
/*
* @info Платёжный модуль Hutki grosh для JoomShopping
* @package JoomShopping for Joomla!
* @subpackage payment
* @author Esas.by
*/

use Esas\HutkiGrosh\AlfaclickRq;
use Esas\HutkiGrosh\BillNewRq;
use Esas\HutkiGrosh\HutkiGrosh;
use Esas\HutkiGrosh\WebPayRq;

defined('_JEXEC') or die('Restricted access');
require_once('hutkigrosh_api.php');

class pm_hg extends PaymentRoot
{
    const MODULE_MACHINE_NAME = 'pm_hg';
    const CONFIG_HG_SHOP_NAME = 'hutkigrosh_shop_name';
    const CONFIG_HG_LOGIN = 'hutkigrosh_login';
    const CONFIG_HG_PASSWORD = 'hutkigrosh_password';
    const CONFIG_HG_ERIP_ID = 'hutkigrosh_eripid';
    const CONFIG_HG_SANDBOX = 'hutkigrosh_sandbox'; // это стандартное поле для всех платежных шлюзов в Drupal 8
    const CONFIG_HG_EMAIL_NOTIFICATION = 'hutkigrosh_email_notification';
    const CONFIG_HG_SMS_NOTIFICATION = 'hutkigrosh_sms_notification';
    const CONFIG_HG_COMPLETE_TEXT = 'hutkigrosh_compete_message';
    const CONFIG_HG_PAYMENT_METHOD_DESCRIPTION = 'hutkigrosh_payment_method_description';
    const CONFIG_HG_BILL_STATUS_PENDING = 'hutkigrosh_bill_status_pending';
    const CONFIG_HG_BILL_STATUS_PAYED = 'hutkigrosh_bill_status_payed';
    const CONFIG_HG_BILL_STATUS_FAILED = 'hutkigrosh_bill_status_failed';
    const CONFIG_HG_BILL_STATUS_CANCELED = 'hutkigrosh_bill_status_canceled';

    static function createName($setting_name)
    {
        return 'pm_params[' . $setting_name . ']';
    }

    static function loadLanguageFile()
    {
        $lang = JFactory::getLanguage();
        $langtag = $lang->getTag();

        if (file_exists(JPATH_ROOT . '/components/com_jshopping/payments/pm_hg/lang/' . $langtag . '.php')) {
            require_once(JPATH_ROOT . '/components/com_jshopping/payments/pm_hg/lang/' . $langtag . '.php');
        } else {
            require_once(JPATH_ROOT . '/components/com_jshopping/payments/pm_hg/lang/ru-RU.php'); //если языковый файл не найден, то подключаем ru-RU.php
        }
    }

    /**
     * Отображение формы с настройками платежного шлюза (админка)
     * @param $params
     */
    function showAdminFormParams($params)
    {
        $array_params = array(
            self::CONFIG_HG_SANDBOX,
            self::CONFIG_HG_SHOP_NAME,
            self::CONFIG_HG_ERIP_ID,
            self::CONFIG_HG_LOGIN,
            self::CONFIG_HG_PASSWORD,
            self::CONFIG_HG_SMS_NOTIFICATION,
            self::CONFIG_HG_EMAIL_NOTIFICATION,
            self::CONFIG_HG_BILL_STATUS_PENDING,
            self::CONFIG_HG_BILL_STATUS_FAILED,
            self::CONFIG_HG_BILL_STATUS_PAYED,
            self::CONFIG_HG_BILL_STATUS_CANCELED,
        );

        foreach ($array_params as $key) {
            if (!isset($params[$key])) {
                $params[$key] = '';
            }
        }

        $orders = JModelLegacy::getInstance('orders', 'JshoppingModel');

        self::loadLanguageFile();
        include(dirname(__FILE__) . '/adminparamsform.php');
    }

    const HG_RESP_CODE_OK = '0';
    const HG_RESP_CODE_ERROR = '2018';


    function checkTransaction($pmconfigs, $order, $act)
    {
        $request_params = JFactory::getApplication()->input->request->getArray();
        // все переменные передаются в запросе, можно передевать через сессию
        $hgStatusCode = $request_params['hg_status'];
        $billId = $request_params['bill_id'];
        if ($hgStatusCode != '0') {
            // в hutkigrosh_api большое кол-во кодов неуспешного выставления счета, поэтому для упрощения сводим их все к одному
            $respCode = self::HG_RESP_CODE_ERROR;
            $message = Hutkigrosh::getStatusError($hgStatusCode);
        } else {
            $respCode = self::HG_RESP_CODE_OK;
            $message = 'Order[' . $order->order_id . '] was successfully added to Hutkigrosh with billid[' . $billId . ']';
        }
        //пока счет не будет оплачен в ЕРИП у заказа будет статус Pending
        return array($respCode, $message, $billId);
    }

    /**
     * На основе кода ответа от платежного шлюза задаем статус заказу
     * @param int $rescode
     * @param array $pmconfigs
     * @return mixed
     */
    function getStatusFromResCode($rescode, $pmconfigs){
        if ($rescode != '0') {
            $status = $pmconfigs[self::CONFIG_HG_BILL_STATUS_FAILED];
        } else {
            $status = $pmconfigs[self::CONFIG_HG_BILL_STATUS_PENDING];
        }
        return $status;
    }

    /**
     * При каких кодах ответов от платежного шлюза считать оплату неуспешной.
     * @return array
     */
    function getNoBuyResCode(){
        // в hutkigrosh_api большое кол-во кодов неуспешного выставления счета, поэтому для упрощения сводим их все к одному
        return array(self::HG_RESP_CODE_ERROR);
    }

    /**
     * Форма отображаемая клиенту на step7. В теории должна содердать поля, которые надо задать клиенту перед отправкой
     * на плетежный шлюз. В случае с ХуткиГрош никаких полей клиенту показывать не надо и тут сразу выполняется запрос на
     * выставления счета к шлюзу и редирект на следующий step
     * @param $pmconfigs
     * @param $order
     * @throws Exception
     */
    function showEndForm($pmconfigs, $order)
    {
        $hg = new Hutkigrosh($pmconfigs[self::CONFIG_HG_SANDBOX]);
        $res = $hg->apiLogIn($pmconfigs[self::CONFIG_HG_LOGIN], $pmconfigs[self::CONFIG_HG_PASSWORD]);

        // Ошибка авторизации
        if (!$res) {
            $error = $hg->getError(); //TODO редирект
            $hg->apiLogOut(); // Завершаем сеанс
            throw new Exception($error);
        }
        $billNewRq = new BillNewRq();
        $billNewRq->eripId = $pmconfigs[self::CONFIG_HG_ERIP_ID];
        $billNewRq->invId = $order_id = $order->order_id;
        $billNewRq->fullName = $order->f_name . ' ' . $order->l_name;
        $billNewRq->mobilePhone = $order->phone;
        $billNewRq->email = $order->email;
        $billNewRq->fullAddress = $order->city . ' ' . $order->state . ' ' . $order->street;
        $billNewRq->amount = $order->order_total;
        $billNewRq->currency = $order->currency_code;
        $billNewRq->notifyByEMail = $pmconfigs[self::CONFIG_HG_EMAIL_NOTIFICATION];
        $billNewRq->notifyByMobilePhone = $pmconfigs[self::CONFIG_HG_SMS_NOTIFICATION];
        foreach ($order->getAllItems() as $line_item) {
            $arItem['invItemId'] = $line_item->product_name;
            $arItem['desc'] = $line_item->product_name;
            $arItem['count'] = round($line_item->product_quantity);
            $arItem['amt'] = $line_item->product_item_price;
            $arItems[] = $arItem;
            unset($arItem);
        }
        $billNewRq->products = $arItems;

        $billID = $hg->apiBillNew($billNewRq);
        $hgStatusCode = $hg->getStatus();
        $hg->apiLogOut();
        /**
         * На этом этапе мы только выполняем запрос к HG для добавления счета. Мы не показываем итоговый экран
         * (с кнопками webpay и alfaclick), а выполняем автоматический редирект на step7
         **/
        $redirectUrl = "index.php?option=com_jshopping&controller=checkout&task=step7" .
            "&js_paymentclass=" . self::MODULE_MACHINE_NAME .
            "&hg_status=" . $hgStatusCode .
            "&order_id=" . $order->order_id;
        if ($billID)
            $redirectUrl .= "&bill_id=" . $billID;
        JFactory::getApplication()->redirect($redirectUrl);
    }


    function getUrlParams($pmconfigs)
    {
        $reqest_params = JFactory::getApplication()->input->request->getArray();
        $params = array();
        $params['order_id'] = $reqest_params['order_id'];
        $params['hash'] = '';
        $params['checkHash'] = false;
        $params['checkReturnParams'] = false;
        return $params;
    }

    /**
     * Получаем из БД текст успешного выставления счета
     * В отличие от других CMS Joomls не может хранить его прямо в параметрах модуля.
     * Для больших текстов (с html) используется отдельная таблица
     * @return string
     */
    public static function getCompletionText(){
        $statictext = JSFactory::getTable("statictext","jshop");
        $rowstatictext = $statictext->loadData("order_hg_completion_text");
        $text = $rowstatictext->text;
        if (trim(strip_tags($text))==""){
            $text = '';
        }
        return $text;
    }

    /**
     * В теории, тут должно отправлятся уведомление на шлюз об успешном оформлении заказа.
     * В случае с ХуткиГрош мы тут отображаем итоговый экран с доп. кнопками.
     * @param $pmconfigs
     * @param $order
     * @param $payment
     */
    function complete($pmconfigs, $order, $payment) {
        self::loadLanguageFile();
        $completion_text = strtr(pm_hg::getCompletionText(), array("@order_number" => $order->order_id));
        $alfaclick_billID = $order->transaction;
        $alfaclick_phone = $order->phone;
        $alfaclick_url = self::generateControllerPath("alfaclick");
        $hg = new Hutkigrosh($pmconfigs[pm_hg::CONFIG_HG_SANDBOX]);
        $res = $hg->apiLogIn($pmconfigs[pm_hg::CONFIG_HG_LOGIN], $pmconfigs[pm_hg::CONFIG_HG_PASSWORD]);
        // Ошибка авторизации
        if (!$res) {
            saveToLog("payment.log", 'HG login failed: ' . $hg->getError());
            $hg->apiLogOut(); // Завершаем сеанс
        } else {
            $webPayRq = new WebPayRq();
            $webPayRq->billId = $order->transaction;
            $mainReturnUrl = JURI::root(). self::generateControllerPath("complete") .
                "&order_id=" . $order->order_id .
                "&bill_id=" . $order->transaction;
            $webPayRq->returnUrl = $mainReturnUrl . "&webpay_status=payed";
            $webPayRq->cancelReturnUrl = $mainReturnUrl . "&webpay_status=failed";

            $webpay_form = $hg->apiWebPay($webPayRq);
            $webpay_status = $_REQUEST['webpay_status']; // ???
            $hg->apiLogOut();
        }
        include(JPATH_SITE.'/components/com_jshopping/payments/pm_hg/completion.php');
    }

    /**
     * Получаем из БД заказ не по order_id, а по индентификатору транзакции внешней системы
     * Для ХуткиГрош это billID
     * @param $transaction
     * @return null
     */
    static function getOrderByTrxId($transaction) {
        $db = JFactory::getDBO();
        $query = "SELECT order_id FROM `#__jshopping_orders` WHERE transaction = '".$db->escape($transaction)."' order by order_id desc";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        if (count($rows) != 1 ) {
            saveToLog("payment.log", 'Can not load order by transaction[' . $transaction . "]");
            return null;
        }
        $order = JSFactory::getTable('order', 'jshop');
        $order->load($rows[0]->order_id);
        return $order;
    }

    /**
     * Генерация редиректа на контроллер.
     * @param $task
     * @return string
     */
    public static function generateControllerPath($task)
    {
        return "index.php?option=com_jshopping&controller=hutkigrosh&task=" . $task;
    }
}

?>