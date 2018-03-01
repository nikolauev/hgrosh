<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 16.02.2018
 * Time: 12:07
 */
//namespace Esas\Hutkigrosh\CSCart;

class OrderWrapperCSCart extends esas\hutkigrosh\wrappers\OrderWrapper
{
    private $orderInfo;

    public function __construct($orderInfo) {
        $this->orderInfo = $orderInfo;
    }

    /**
     * Уникальный номер заказ в рамках CMS
     * @return string
     */
    public function getOrderId()
    {
        return $this->orderInfo['order_id'];
    }

    /**
     * Полное имя покупателя
     * @return string
     */
    public function getFullName()
    {
        return $this->orderInfo['b_firstname'] . " " . $this->orderInfo['b_lastname'];
    }

    /**
     * Мобильный номер покупателя для sms-оповещения
     * (если включено администратором)
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->orderInfo['b_phone'];
    }

    /**
     * Email покупателя для email-оповещения
     * (если включено администратором)
     * @return string
     */
    public function getEmail()
    {
        return $this->orderInfo['email'];
    }

    /**
     * Физический адрес покупателя
     * @return string
     */
    public function getAddress()
    {
        return $this->orderInfo['b_address'] . " " . $this->orderInfo['b_city'] . " " . $this->orderInfo['b_country'];
    }

    /**
     * Общая сумма товаров в заказе
     * @return string
     */
    public function getAmount()
    {
        return $this->orderInfo['total'];
    }

    /**
     * Валюта заказа (буквенный код)
     * @return string
     */
    public function getCurrency()
    {
        return $this->orderInfo['secondary_currency']; // ???
    }

    /**
     * Массив товаров в заказе
     * @return OrderProductWrapper[]
     */
    public function getProducts()
    {
        return $this->orderInfo['products'];
    }

    /**
     * BillId (идентификатор хуткигрош) успешно выставленного счета
     * @return mixed
     */
    public function getBillId()
    {
        return $this->orderInfo["payment_info"]["transaction_id"];
    }

    /**
     * Текущий статус заказа в CMS
     * @return mixed
     */
    public function getStatus()
    {
        return $this->orderInfo["status"];
    }
}