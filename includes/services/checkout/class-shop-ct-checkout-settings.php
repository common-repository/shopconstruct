<?php
class Shop_CT_Checkout_Settings extends Shop_CT_Settings {
    /** @var string Allows customers to checkout without creating an account. */
    public $enable_guest_checkout = 'yes';
    /** @var string Force SSL (HTTPS) on the checkout pages (an SSL Certificate is required). */
    public $force_ssl_checkout = 'yes';
    /** @var string Checkout Page ID */
    public $checkout_page_id = null;
    /** @var string Terms and Conditions ID */
    public $terms_page_id = '';
    /** @var string Pay page endpoint */
    public $checkout_pay_endpoint = 'order-pay';
    /** @var string Order Received page endpoint */
    public $checkout_order_received_endpoint = 'order-received';
    /**
     * Shop_CT_Checkout_Settings constructor.
     */
    public function __construct(){
        $this->form_id = 'shop_ct_checkout_settings_form';
        $this->init();
        $this->init_sections();
        $this->init_controls();
    }
    /**
     * todo: review last 2 options
     */
    public function init(){
        $this->enable_guest_checkout = $this->get_option('enable_guest_checkout','yes');
        $this->force_ssl_checkout = $this->get_option('force_ssl_checkout','yes');
        $this->checkout_page_id = $this->get_option('checkout_page_id','');
        $this->terms_page_id = $this->get_option('terms_page_id','');
        $this->checkout_pay_endpoint = $this->get_option('checkout_pay_endpoint','order-pay');
        $this->checkout_order_received_endpoint = $this->get_option('checkout_order_received_endpoint','order-received');
    }
    public function init_sections(){
        $this->sections = array(
            'process'=>array(
                'title'=>__('Checkout Process','shop_ct'),
            ),
            'pages'=>array(
                'title'=>__('Checkout Pages','shop_ct'),
                'description'=>__('These pages need to be set so that ShopConstruct knows where to send users to checkout.','shop_ct'),
            ),
            /*'endpoints'=>array(
                'title'=>__('Checkout Endpoints','shop_ct'),
                'description'=>__('Endpoints are appended to your page URLs to handle specific actions during the checkout process. They should be unique.','shop_ct'),
            ),*/
        );
    }
    public function init_controls() {
        $this->controls = array(
            'shop_ct_checkout'=>array(
                'label'=>__('Checkout','shop_ct'),
                'section'=>'process',
                'type'=>'checkbox',
                'grouped'=>'yes',
                'choices'=>array(
                    'shop_ct_enable_guest_checkout'=>array(
                        'label'=>__('Enable guest checkout','shop_ct'),
                        'description'=>__('Allows customers to checkout without creating an account.','shop_ct'),
                        'default'=>$this->enable_guest_checkout,
                    ),
                    /*'shop_ct_force_ssl_checkout'=>array(
                        'label'=>__('Force secure checkout','shop_ct'),
                        'description'=>__('Force SSL (HTTPS) on the checkout pages (an SSL Certificate is required).','shop_ct'),
                        'default'=>$this->force_ssl_checkout,
                    )*/
                )
            ),
            'shop_ct_checkout_page_id'=>array(
                'label'=>__('Checkout Page','shop_ct'),
                'section'=>'pages',
                'type'=>'page_select',
                'default'=>$this->checkout_page_id
            ),
            'shop_ct_terms_page_id'=>array(
                'label'=>__('Terms and Conditions','shop_ct'),
                'section'=>'pages',
                'type'=>'page_select',
                'default'=>$this->terms_page_id,
            ),
            /*'shop_ct_checkout_pay_endpoint'=>array(
                'label'=>__('Pay','shop_ct'),
                'section'=>'endpoints',
                'type'=>'text',
                'default'=>$this->checkout_pay_endpoint,
            ),
            'shop_ct_checkout_order_received_endpoint'=>array(
                'label'=>__('Order Received','shop_ct'),
                'section'=>'endpoints',
                'type'=>'text',
                'default'=>$this->checkout_order_received_endpoint,
            )*/
        );
    }
}