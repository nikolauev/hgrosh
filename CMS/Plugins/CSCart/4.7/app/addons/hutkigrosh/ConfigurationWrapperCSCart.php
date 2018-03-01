<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 16.02.2018
 * Time: 14:02
 */
//namespace Esas\Hutkigrosh\CSCart;

class ConfigurationWrapperCSCart extends esas\hutkigrosh\wrappers\ConfigurationWrapper
{
    private $configuration;

    public function __construct($configuration) {
        $this->configuration = $configuration;
    }

    /**
     * Произольно название интернет-мазагина
     * @return string
     */
    public function getShopName()
    {
        return $this->configuration[self::CONFIG_HG_SHOP_NAME];
    }

    /**
     * Имя пользователя для доступа к системе ХуткиГрош
     * @return string
     */
    public function getHutkigroshLogin()
    {
        return $this->configuration[self::CONFIG_HG_LOGIN];
    }

    /**
     * Пароль для доступа к системе ХуткиГрош
     * @return string
     */
    public function getHutkigroshPassword()
    {
        return $this->configuration[self::CONFIG_HG_PASSWORD];
    }

    /**
     * Включен ли режим песчоницы
     * @return boolean
     */
    public function isSandbox()
    {
        return $this->configuration[self::CONFIG_HG_SANDBOX] == "Y";
    }

    /**
     * Уникальный идентификатор услуги в ЕРИП
     * @return string
     */
    public function getEripId()
    {
        return $this->configuration[self::CONFIG_HG_ERIP_ID];
    }

    /**
     * Включена ля оповещение клиента по Email
     * @return boolean
     */
    public function isEmailNotification()
    {
        return $this->configuration[self::CONFIG_HG_EMAIL_NOTIFICATION] == "Y";
    }

    /**
     * Включена ля оповещение клиента по Sms
     * @return boolean
     */
    public function isSmsNotification()
    {
        return $this->configuration[self::CONFIG_HG_SMS_NOTIFICATION] == "Y";
    }

    /**
     * Итоговый текст, отображаемый клменту после успешного выставления счета
     * Чаще всего содержит подробную инструкцию по оплате счета в ЕРИП
     * @return string
     */
    public function getCompletionText()
    {
        // TODO: Implement getCompletionText() method.
    }

    /**
     * Какой статус присвоить заказу после успешно выставления счета в ЕРИП (на шлюз Хуткигрош_
     * @return string
     */
    public function getBillStatusPending()
    {
        return $this->configuration[self::CONFIG_HG_BILL_STATUS_PENDING];
    }

    /**
     * Какой статус присвоить заказу после успешно оплаты счета в ЕРИП (после вызова callback-а шлюзом ХуткиГрош)
     * @return string
     */
    public function getBillStatusPayed()
    {
        return $this->configuration[self::CONFIG_HG_BILL_STATUS_PAYED];
    }

    /**
     * Какой статус присвоить заказу в случаче ошибки выставления счета в ЕРИП
     * @return string
     */
    public function getBillStatusFailed()
    {
        return $this->configuration[self::CONFIG_HG_BILL_STATUS_FAILED];
    }

    /**
     * Какой статус присвоить заказу после успешно оплаты счета в ЕРИП (после вызова callback-а шлюзом ХуткиГрош)
     * @return string
     */
    public function getBillStatusCanceled()
    {
        return $this->configuration[self::CONFIG_HG_BILL_STATUS_CANCELED];
    }
}