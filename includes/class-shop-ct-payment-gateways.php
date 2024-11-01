<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * ShopConstruct Payment Gateways class
 *
 * Loads payment gateways via hooks for use in the store.
 *
 * @class 		Shop_CT_Payment_Gateways
 */
class Shop_CT_Payment_Gateways {

	/** @var Shop_CT_Payment_Gateway[] Array of payment gateway classes. */
	public $payment_gateways;

	/**
	 * @var Shop_CT_Payment_Gateways The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main Shop_CT_Payment_Gateways Instance.
	 *
	 * Ensures only one instance of Shop_CT_Payment_Gateways is loaded or can be loaded.
	 *
	 * @return Shop_CT_Payment_Gateways Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'shop_ct' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'shop_ct' ), '1.0' );
	}

	/**
	 * Initialize payment gateways.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Load gateways and hook in functions.
	 */
	public function init() {
        $load_gateways = array(
	        'Shop_CT_Gateway_COD',
	        'Shop_CT_Gateway_Paypal',
            'Shop_CT_Gateway_BACS',
            'Shop_CT_Gateway_Cheque',
        );
		/** Filter */
		$load_gateways = apply_filters( 'shop_ct_payment_gateways', $load_gateways );
		/** Load gateways */
		foreach ( $load_gateways as $key=>$gateway ) {
			    $load_gateway = is_string( $gateway ) ? new $gateway() : $gateway;
				$this->payment_gateways[ $load_gateway->id ] = $load_gateway;
		}
    }

	/**
	 * Get gateways.
	 *
	 * @access public
	 * @return Shop_CT_Payment_Gateway[]
	 */
	public function payment_gateways() {
		return $this->payment_gateways;
	}
	/**
	 * Get available gateways.
	 *
	 * @return Shop_CT_Payment_Gateway[]
	 */
	public function get_available_payment_gateways() {
		$_available_gateways = array();

		foreach ( $this->payment_gateways as $gateway ) {
			if ( $gateway->is_available() ) {
					$_available_gateways[ $gateway->id ] = $gateway;
			}
		}
		return apply_filters( 'shop_ct_available_payment_gateways', $_available_gateways );
	}

	/**
	 * Set the current, active gateway.
	 *
	 * @param array $gateways Available payment gateways.
	 */
	public function set_current_gateway( $gateways ) {
		// Be on the defensive
		if ( ! is_array( $gateways ) || empty( $gateways ) ) {
			return;
		}

        $current_gateway = current( $gateways );

		// Ensure we can make a call to set_current() without triggering an error
		if ( $current_gateway && is_callable( array( $current_gateway, 'set_current' ) ) ) {
			$current_gateway->set_current();
		}
	}

	public function output_admin_page( $page ){
		$gateways = $this->payment_gateways();
		if(key_exists($page,$gateways)) $gateways[$page]->display();
	}
}