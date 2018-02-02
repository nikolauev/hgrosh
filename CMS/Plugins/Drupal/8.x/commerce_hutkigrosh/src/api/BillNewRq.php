<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 25.01.2018
 * Time: 14:52
 */

namespace Drupal\commerce_hutkigrosh\api;


class BillNewRq
{
    public $eripId;
    public $invId;
    public $fullName;
    public $mobilePhone;
    public $email;
    public $fullAddress;
    public $amount;
    public $currency;
    public $products;
    public $notifyByEMail = false;
    public $notifyByMobilePhone = false;
}