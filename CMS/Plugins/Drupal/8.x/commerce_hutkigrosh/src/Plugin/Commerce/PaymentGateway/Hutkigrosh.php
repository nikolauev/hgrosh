<?php

namespace Drupal\commerce_hutkigrosh\Plugin\Commerce\PaymentGateway;

use Drupal\commerce_hutkigrosh\api\BillNewRq;
use Drupal\commerce_hutkigrosh\api\HutkigroshAPI;
use Drupal\commerce_payment\Entity\PaymentInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\ManualPaymentGatewayInterface;
use Drupal\commerce_payment\Plugin\Commerce\PaymentGateway\PaymentGatewayBase;
use Drupal\commerce_price\Price;
use Drupal\Core\Form\FormStateInterface;
use Exception;

/**
 * Provides the Hutkigrosh payment gateway.
 *
 * @CommercePaymentGateway(
 *   id = "hutkigrosh",
 *   label = "Hutkigrosh (ERIP gate)",
 *   display_label = "Hutkigrosh",
 *   forms = {
 *     "add-payment-method" = "Drupal\commerce_hutkigrosh\PluginForm\HutkigroshForm",
 *   },
 * )
 */
class Hutkigrosh extends PaymentGatewayBase implements ManualPaymentGatewayInterface
{
    const HUTKIGROSH_MODULE_ID = 'commerce_hutkigrosh';
    const CONFIG_HG_SHOP_NAME = 'hutkigrosh_shop_name';
    const CONFIG_HG_LOGIN = 'hutkigrosh_login';
    const CONFIG_HG_PASSWORD = 'hutkigrosh_password';
    const CONFIG_HG_ERIP_ID = 'hutkigrosh_eripid';
    const CONFIG_HG_SANDBOX = 'hutkigrosh_sandbox'; // это стандартное поле для всех платежных шлюзов в Drupal 8
    const CONFIG_HG_EMAIL_NOTIFICATION = 'hutkigrosh_email_notification';
    const CONFIG_HG_SMS_NOTIFICATION = 'hutkigrosh_sms_notification';
    const CONFIG_HG_COMPLETE_TEXT = 'hutkigrosh_compete_message';
    const CONFIG_HG_PAYMENT_METHOD_DESCRIPTION = 'hutkigrosh_payment_method_description';

    const HG_ORDER_STATUS_PENDING = 'hg_pay_pending';
    const HG_ORDER_STATUS_COMPLETED = 'hg_pay_complete';
    const HG_ORDER_STATUS_CANCLED = 'hg_pay_failed';

    const HG_PAYMENT_STATUS_PENDING = 'hg_pay_pending';
    const HG_PAYMENT_STATUS_COMPLETE = 'hg_pay_complete';
    const HG_PAYMENT_STATUS_CANCELED = 'hg_pay_complete';
    const HG_PAYMENT_STATUS_FAILED = 'hg_pay_failed';

    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration()
    {
        $defaults = array(
            self::CONFIG_HG_PAYMENT_METHOD_DESCRIPTION => t('Adding bills to ERIP via Hutkigrosh gateway'),
            self::CONFIG_HG_COMPLETE_TEXT => array(
                'value' => t('Your bill was successfully added to ERIP.'))
        );

        return $defaults + parent::defaultConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function buildConfigurationForm(array $form, FormStateInterface $form_state)
    {
        $form = parent::buildConfigurationForm($form, $form_state);

        $form[self::CONFIG_HG_SHOP_NAME] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Shop name'),
            '#required' => TRUE,
            '#default_value' => $this->configuration[self::CONFIG_HG_SHOP_NAME]
        );
        $form[self::CONFIG_HG_ERIP_ID] = array(
            '#type' => 'textfield',
            '#title' => $this->t('ERIP ID'),
            '#required' => TRUE,
            '#default_value' => $this->configuration[self::CONFIG_HG_ERIP_ID]
        );
        $form[self::CONFIG_HG_LOGIN] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Hutkigrosh login'),
            '#default_value' => $this->configuration[self::CONFIG_HG_LOGIN],
            '#required' => TRUE,
        );
        $form[self::CONFIG_HG_PASSWORD] = array(
            '#type' => 'textfield',
            '#title' => $this->t('Hutkigrosh password'),
            '#default_value' => $this->configuration[self::CONFIG_HG_PASSWORD],
            '#required' => TRUE,

        );
        $form[self::CONFIG_HG_EMAIL_NOTIFICATION] = array(
            '#type' => 'checkbox',
            '#title' => $this->t('Email notification'),
            '#default_value' => $this->configuration[self::CONFIG_HG_EMAIL_NOTIFICATION],
        );
        $form[self::CONFIG_HG_SMS_NOTIFICATION] = array(
            '#type' => 'checkbox',
            '#title' => $this->t('Sms notification'),
            '#default_value' => $this->configuration[self::CONFIG_HG_SMS_NOTIFICATION],
        );
        // в текущей версии commerce 2 нет возможности отображения описания клиенту
//        $form[self::CONFIG_HG_PAYMENT_METHOD_DESCRIPTION] = array(
//            '#type' => 'textfield',
//            '#title' => $this->t('Display payment method description'),
//            '#required' => TRUE,
//            '#default_value' => $this->configuration[self::CONFIG_HG_PAYMENT_METHOD_DESCRIPTION]
//        );
        $form[self::CONFIG_HG_COMPLETE_TEXT] = array(
            '#type' => 'text_format',
            '#title' => $this->t('Complete text'),
            '#description' => $this->t('Shown the end of checkout, after the customer has placed their order.'),
            '#required' => TRUE,
            '#default_value' => $this->configuration[self::CONFIG_HG_COMPLETE_TEXT]['value'],
            '#format' => $this->configuration[self::CONFIG_HG_COMPLETE_TEXT]['format']
        );
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfigurationForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateConfigurationForm($form, $form_state);
        // можно добавить проверку логина и пароля (с помощью api.login)
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function submitConfigurationForm(array &$form, FormStateInterface $form_state)
    {
        parent::submitConfigurationForm($form, $form_state);

        if (!$form_state->getErrors()) {
            $values = $form_state->getValue($form['#parents']);
            $this->configuration[self::CONFIG_HG_SHOP_NAME] = $values[self::CONFIG_HG_SHOP_NAME];
            $this->configuration[self::CONFIG_HG_ERIP_ID] = $values[self::CONFIG_HG_ERIP_ID];
            $this->configuration[self::CONFIG_HG_LOGIN] = $values[self::CONFIG_HG_LOGIN];
            $this->configuration[self::CONFIG_HG_PASSWORD] = $values[self::CONFIG_HG_PASSWORD];
            $this->configuration[self::CONFIG_HG_SANDBOX] = $values['mode'] == 'test';  // это стандартное поле для всех платежных шлюзов в Drupal 8
            $this->configuration[self::CONFIG_HG_SMS_NOTIFICATION] = $values[self::CONFIG_HG_SMS_NOTIFICATION];
            $this->configuration[self::CONFIG_HG_EMAIL_NOTIFICATION] = $values[self::CONFIG_HG_EMAIL_NOTIFICATION];
            $this->configuration[self::CONFIG_HG_PAYMENT_METHOD_DESCRIPTION] = $values[self::CONFIG_HG_PAYMENT_METHOD_DESCRIPTION];
            $this->configuration[self::CONFIG_HG_COMPLETE_TEXT] = $values[self::CONFIG_HG_COMPLETE_TEXT];
        }
    }

    public function buildPaymentInstructions(PaymentInterface $payment)
    {
        $instructions = [];
        if (!empty($this->configuration[self::CONFIG_HG_COMPLETE_TEXT]['value'])) {
            $instructions = [
                '#type' => 'processed_text',
                '#text' => format_string($this->configuration[self::CONFIG_HG_COMPLETE_TEXT]['value'], array("@order_number" => $payment->getOrderId())),
                '#format' => $this->configuration[self::CONFIG_HG_COMPLETE_TEXT]['format'],
            ];
        }

        return $instructions;
    }

    public function createWebpayForm()
    {

    }

    /**
     * Creates a payment.
     *
     * @param \Drupal\commerce_payment\Entity\PaymentInterface $payment
     *   The payment.
     * @param bool $capture
     *   Whether the created payment should be captured (VS authorized only).
     *   Allowed to be FALSE only if the plugin supports authorizations.
     *
     * @throws \InvalidArgumentException
     *   If $capture is FALSE but the plugin does not support authorizations.
     * @throws \Drupal\commerce_payment\Exception\PaymentGatewayException
     *   Thrown when the transaction fails f
     * or any reason.
     */
    public function createPayment(PaymentInterface $payment, $capture = TRUE)
    {
        try {
            $this->assertPaymentState($payment, ['new']);
            $hg = new HutkigroshAPI($this->configuration[self::CONFIG_HG_SANDBOX]);
            $res = $hg->apiLogIn($this->configuration[self::CONFIG_HG_LOGIN], $this->configuration[self::CONFIG_HG_PASSWORD]);

            // Ошибка авторизации
            if (!$res) {
                $error = $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                throw new Exception($error);
            }

            $order = $payment->getOrder();
            $address = $order->getBillingProfile()->get('address')->first();
            $billNewRq = new BillNewRq();
            $billNewRq->eripId = $this->configuration[self::CONFIG_HG_ERIP_ID];
            $billNewRq->invId = $order->id();
            $billNewRq->fullName = $address->getGivenName() . " " . $address->getFamilyName();
            $billNewRq->mobilePhone = $order->getBillingProfile()->field_phone->first()->value; //необходимо добавить поле в профиль с машинным именем field_phone
            $billNewRq->email = $order->getCustomer()->getEmail();
            $billNewRq->fullAddress = $address->getAddressLine1() . ", " . $address->getLocality() . ", " . $address->getCountryCode();
            $billNewRq->amount = $payment->getAmount()->getNumber();
            $billNewRq->currency = $payment->getAmount()->getCurrencyCode();
            $billNewRq->notifyByEMail = $this->configuration[self::CONFIG_HG_EMAIL_NOTIFICATION];
            $billNewRq->notifyByMobilePhone = $this->configuration[self::CONFIG_HG_SMS_NOTIFICATION];
            foreach ($order->getItems() as $orderItem) {
                $arItem['invItemId'] = $orderItem->getPurchasedEntity()->getSKU();
                $arItem['desc'] = $orderItem->getTitle();
                $arItem['count'] = round($orderItem->getQuantity());
                $arItem['amt'] = $orderItem->getUnitPrice()->getNumber();
                $arItems[] = $arItem;
                unset($orderItem);
            }
            $billNewRq->products = $arItems;

            $billID = $hg->apiBillNew($billNewRq);
            if (!$billID) {
                $error = $hg->getError();
                $hg->apiLogOut(); // Завершаем сеанс
                throw new Exception($error);
            }
            $hg->apiLogOut();
            $payment->setRemoteId($billID);
            $payment->state = self::HG_PAYMENT_STATUS_PENDING;
            $payment->save();
            // статус самого заказа автоматически переводится в complete вот тут CheckoutFlowBase::redirectToStep
            // хотя на нужен какой-то промежуточный статус (например pending) пока клиент не оплатит счет в ЕРИП
            // и не вызовается HutkigroshController::notify
        } catch (Exception $e) {
            drupal_set_message($e->getMessage(), 'error', TRUE);
            $payment->state = self::HG_PAYMENT_STATUS_FAILED;
            $payment->save();
        }
    }


    /**
     * Receives the given payment.
     *
     * @param \Drupal\commerce_payment\Entity\PaymentInterface $payment
     *   The payment.
     * @param \Drupal\commerce_price\Price $amount
     *   The received amount. If NULL, defaults to the entire payment amount.
     */
    public function receivePayment(PaymentInterface $payment, Price $amount = NULL)
    {
        // TODO: Implement receivePayment() method.
    }

    /**
     * Refunds the given payment.
     *
     * @param \Drupal\commerce_payment\Entity\PaymentInterface $payment
     *   The payment to refund.
     * @param \Drupal\commerce_price\Price $amount
     *   The amount to refund. If NULL, defaults to the entire payment amount.
     *
     * @throws \Drupal\commerce_payment\Exception\PaymentGatewayException
     *   Thrown when the transaction fails for any reason.
     */
    public function refundPayment(PaymentInterface $payment, Price $amount = NULL)
    {
        // TODO: Implement refundPayment() method.
    }

    /**
     * Voids the given payment.
     *
     * @param \Drupal\commerce_payment\Entity\PaymentInterface $payment
     *   The payment to void.
     *
     * @throws \Drupal\commerce_payment\Exception\PaymentGatewayException
     *   Thrown when the transaction fails for any reason.
     */
    public function voidPayment(PaymentInterface $payment)
    {
        // TODO: Implement voidPayment() method.
    }
}
