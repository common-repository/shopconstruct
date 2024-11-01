<?php
/**
 * Class Shop_CT_Checkout
 */
class Shop_CT_Checkout {
    /**
     * @var Shop_CT_Checkout_Settings
     */
    public $settings;

    public function __construct(){
        $this->settings = Shop_CT_Checkout_Settings::instance();
    }

}