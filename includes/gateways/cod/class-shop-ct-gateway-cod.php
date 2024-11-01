<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Cash on Delivery Gateway.
 *
 * Provides a Cash on Delivery Payment Gateway.
 *
 * @class 		Shop_CT_Gateway_COD
 * @extends		Shop_CT_Payment_Gateway
 */
class Shop_CT_Gateway_COD extends Shop_CT_Payment_Gateway {
    /**
     * Constructor for the gateway.
     */
    public function __construct() {
        $this->id                 = 'cod';
        $this->icon               = apply_filters( 'shop_ct_cod_icon', '' );

        // Load the settings
        $this->init();
        $this->init_sections();
        $this->init_controls();

        // Get settings
        add_action( 'shop_ct_thankyou_cod', array( $this, 'thankyou_page' ) );

        // Customer Emails
        add_action( 'shop_ct_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
    }
    /**
     * Define user set variables
     */
    public function init(){
        $this->enabled = $this->get_option('enabled','yes');
        $this->title              = $this->get_option( 'title', __( 'Cash on Delivery', 'shop_ct' ) );
        $this->description        = $this->get_option( 'description', __('Pay with cash upon delivery.','shop_ct') );
        $this->instructions       = $this->get_option( 'instructions', __( 'Thank you, your order is being processed.', 'shop_ct' ) );
    }
    /**
     * Initialize Sections
     */
    public function init_sections(){
        $this->sections = array(
            'cod'=>array(
                'title'=> __( 'Cash on Delivery', 'shop_ct' ),
                'description'=> __( 'Have your customers pay with cash (or by other means) upon delivery.', 'shop_ct' ),
            )
        );
    }
    /**
     * Initialize Controls
     */
    public function init_controls(){
        $this->controls = array(
            'shop_ct_'.$this->id.'_enabled'=>array(
                'section'=>'cod',
                'type'=>'checkbox',
                'default'=>$this->enabled,
                'label'=>__('Enable Cash on Delivery','shop_ct'),
            ),
            'shop_ct_'.$this->id.'_title'=>array(
                'section'=>'cod',
                'type'=>'text',
                'default'=>$this->title,
                'label'=>__('Title','shop_ct'),
            ),
            'shop_ct_'.$this->id.'_description'=>array(
                'section'=>'cod',
                'type'=>'textarea',
                'default'=>$this->description,
                'label'=>__('Description','shop_ct'),
            ),
            'shop_ct_'.$this->id.'_instructions'=>array(
                'section'=>'cod',
                'type'=>'textarea',
                'default'=>$this->instructions,
                'label'=>__('Instructions','shop_ct'),
            ),
        );
    }
    /**
     * Check If The Gateway Is Available For Use.
     *
     * @return bool
     */
    public function is_available() {
        $order          = null;
        $needs_shipping = false;

        // Test if shipping is needed first
        if ( SHOP_CT()->cart_manager->get_cart() && SHOP_CT()->cart_manager->get_cart() ) {
            $needs_shipping = true;
        }
        
        $needs_shipping = apply_filters( 'shop_ct_cart_needs_shipping', $needs_shipping );
        // Virtual order, with virtual disabled
        if ( ! $needs_shipping ) {
            return false;
        }

        return parent::is_available();
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
        if ( $this->instructions && ! $sent_to_admin && 'cod' === $order->get_payment_method() ) {
            echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
        }
    }
}
