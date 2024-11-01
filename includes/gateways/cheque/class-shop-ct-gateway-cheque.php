<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Cheque Payment Gateway.
 *
 * Provides a Cheque Payment Gateway, mainly for testing purposes.
 *
 * @class 		Shop_CT_Gateway_Cheque
 * @extends		Shop_CT_Payment_Gateway
 */
class Shop_CT_Gateway_Cheque extends Shop_CT_Payment_Gateway {
    /**
     * Constructor for the gateway.
     */
    public function __construct() {
        $this->id                 = 'cheque';
        $this->icon               = apply_filters('shop_ct_cheque_icon', '');
        // Load the settings.
        $this->init();
        $this->init_sections();
        $this->init_controls();
        // Customer Emails
        add_action( 'shop_ct_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
    }
    /**
     * Define user set variables
     */
    public function init(){
        $this->enabled = $this->get_option('enabled','yes');
        $this->title        = $this->get_option( 'title', __( 'Cheque', 'shop_ct' ) );
        $this->description  = $this->get_option( 'description', __('Please send your cheque to Store Name, Store Street, Store Town, Store State / County, Store Postcode.','shop_ct') );
        $this->instructions = $this->get_option( 'instructions', __('Please send your cheque to Store Name, Store Street, Store Town, Store State / County, Store Postcode.','shop_ct') );
    }
    /**
     * Initialize Sections
     */
    public function init_sections(){
        $this->sections = array(
            'cheque'=>array(
                'title'=>__('Cheque','shop_ct'),
                'description'=>$this->method_description,
            )
        );
    }
    public function init_controls(){
        $this->controls = array(
            'shop_ct_'.$this->id.'_enabled'=>array(
                'section'=>'cheque',
                'type'=>'checkbox',
                'default'=>$this->enabled,
                'label'=>__('Enable Cheque Payment','shop_ct')
            ),
            'shop_ct_'.$this->id.'_title'=>array(
                'section'=>'cheque',
                'type'=>'text',
                'default'=>$this->title,
                'label'=>__('Title','shop_ct'),
            ),
            'shop_ct_'.$this->id.'_description'=>array(
                'section'=>'cheque',
                'type'=>'textarea',
                'default'=>$this->description,
                'label'=>__('Description','shop_ct'),
            ),
            'shop_ct_'.$this->id.'_instructions'=>array(
                'section'=>'cheque',
                'type'=>'textarea',
                'default'=>$this->instructions,
                'label'=>__('Instructions','shop_ct'),
            )
        );
    }
    /**
     * Add content to the ECWP emails.
     *
     * @access public
     * @param Shop_CT_order $order
     * @param bool $sent_to_admin
     * @param bool $plain_text
     */
    public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
        if ( $this->instructions && ! $sent_to_admin && 'cheque' === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
            echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
        }
    }
}
