<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 16.02.2018
 * Time: 14:31
 */
//namespace Esas\Hutkigrosh\CSCart;

class OrderProductWrapperCSCart extends esas\hutkigrosh\wrappers\OrderProductWrapper
{

    private $product;

    public function __construct($product) {
        $this->product = $product;
    }


    /**
     * Артикул товара
     * @return string
     */
    public function getInvId()
    {
        return $this->product["product_code"]; // может все-таки product_id или item_id?
    }

    /**
     * Название или краткое описание товара
     * @return mixed
     */
    public function getName()
    {
        return $this->product["product"];
    }

    /**
     * Количество товароа в корзине
     * @return mixed
     */
    public function getCount()
    {
        return $this->product["amount"];
    }

    /**
     * Цена за единицу товара
     * @return mixed
     */
    public function getUnitPrice()
    {
        return $this->product["price"];
    }
}