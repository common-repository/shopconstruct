<?php
class Shop_CT_Gateway_Paypal extends Shop_CT_Payment_Gateway {

	/** @var Shop_CT_Logger Logger instance */
	public static $log = false;


	public $email;
	public $testmode;
	public $debug;
	public $receiver_email;
	public $invoice_prefix;
	public $send_shipping;
	public $page_style;
	public $api_username;
	public $api_password;
	public $api_signature;

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                 = 'paypal';

		$this->init();
		$this->init_sections();
		$this->init_controls();

	}

	/**
	 * Initialize user defined variables
	 */
	public function init(){
		$this->enabled = $this->get_option('enabled','no');
		$this->title = $this->get_option('title',__('PayPal','shop_ct'));
		$this->description = $this->get_option('description',__("Pay via PayPal, you can pay with your credit card if you don't have a PayPal account.",'shop_ct') );
		$this->email = $this->get_option('email',get_option('admin_email'));
		$this->testmode = 'yes' === $this->get_option('testmode','no');
		$this->debug = 'yes' === $this->get_option('debug','no');
		$this->receiver_email = $this->get_option( 'receiver_email', $this->email );
		$this->invoice_prefix = $this->get_option('invoice_prefix','SHOP-CT-');
		$this->send_shipping = $this->get_option('send_shipping','no');
		$this->api_username = $this->get_option('api_username','');
		$this->api_password = $this->get_option('api_password','');
		$this->api_signature = $this->get_option('api_signature','');
	}

	public function init_sections(){
		$this->sections = array(
			'paypal'=>array(
				'title'=>__('PayPal','shop_ct'),
				'description'=> __( 'PayPal standard sends customers to PayPal to enter their payment information. PayPal IPN requires fsockopen/cURL support to update order statuses after payment', 'shop_ct' ),
			),
			'advanced'=>array(
				'title'=>__('Advanced options','shop_ct'),
			),
			/*'api'=>array(
				'title'=>__('API Credentials','shop_ct'),
				'description'=>sprintf(__('Enter your PayPal API credentials to process refunds via PayPal. Learn how to access your PayPal API Credentials %shere%s.'),'<a target="_blank" href="https://developer.paypal.com/webapps/developer/docs/classic/api/apiCredentials/#creating-classic-api-credentials">','</a>'),
			)*/
		);
	}

	public function init_controls(){
		$this->controls = array(
			'shop_ct_'. $this->id .'_enabled' =>array(
				'label'=>__('Enable PayPal standard','shop_ct'),
				'section'=>'paypal',
				'type'=>'checkbox',
				'default'=>$this->enabled,
			),
			'shop_ct_'.$this->id.'_title' =>array(
				'label'=>__('Title','shop_ct'),
				'section'=>'paypal',
				'type'=>'text',
				'default'=>$this->title,
			),
			'shop_ct_'.$this->id.'_description'=>array(
				'label'=>__('Description','shop_ct'),
				'section'=>'paypal',
				'type'=>'textarea',
				'default'=>$this->description,
			),
			'shop_ct_'.$this->id.'_email'=>array(
				'label'=>__('PayPal Email','shop_ct'),
				'section'=>'paypal',
				'type'=>'email',
				'default'=>$this->email,
			),
			'shop_ct_'.$this->id.'_testmode'=>array(
				'label'=>__('Enable PayPal sandbox','shop_ct'),
				'description'=>sprintf(__('PayPal sandbox can be used to test payments. Sign up for a developer account %shere%s.'),'<a href="https://developer.paypal.com/" target="_blank">','</a>'),
				'section'=>'paypal',
				'type'=>'checkbox',
				'default'=>$this->testmode ? 'yes' : 'no',
			),
			'shop_ct_'.$this->id.'_debug'=>array(
				'label'=>__('Enable logging','shop_ct'),
				'description'=>sprintf( __( 'Log PayPal events, such as IPN requests, inside <code>%s</code>', 'shop_ct' ), Shop_CT_Logger::get_log_file_path( 'paypal' ) ),
				'section'=>'paypal',
				'type'=>'checkbox',
				'default'=>$this->debug ? 'yes' : 'no',
			),
			'shop_ct_'.$this->id.'_receiver_email'=>array(
				'label'=>__('Receiver Email','shop_ct'),
				'section'=>'advanced',
				'type'=>'email',
				'default'=>$this->receiver_email,
			),
			/*'shop_ct_'.$this->id.'_invoice_prefix'=>array(
				'label'=>__('Invoice Prefix','shop_ct'),
				'section'=>'advanced',
				'type'=>'text',
				'default'=>$this->invoice_prefix,
			),*/
			'shop_ct_'.$this->id.'_send_shipping'=>array(
				'label'=>__('Send shipping details to PayPal instead of billing.','shop_ct'),
				'description'=>__('PayPal allows us to send one address. If you are using PayPal for shipping labels you may prefer to send the shipping address rather than billing.','shop_ct'),
				'section'=>'advanced',
				'type'=>'checkbox',
				'default'=>$this->send_shipping,
			),
			/*'shop_ct_'.$this->id.'_api_username'=>array(
				'label'=>__('API Username','shop_ct'),
				'placeholder'=>__('Optional','shop_ct'),
				'section'=>'api',
				'type'=>'text',
				'default'=>$this->api_username,
			),
			'shop_ct_'.$this->id.'_api_password'=>array(
				'label'=>__('API Password','shop_ct'),
				'placeholder'=>__('Optional','shop_ct'),
				'section'=>'api',
				'type'=>'text',
				'default'=>$this->api_password,
			),
			'shop_ct_'.$this->id.'_api_signature'=>array(
				'label'=>__('API Signature','shop_ct'),
				'placeholder'=>__('Optional','shop_ct'),
				'section'=>'api',
				'type'=>'text',
				'default'=>$this->api_signature,
			)*/
		);
	}

	/**
	 * Get gateway icon.
	 * @return string
	 */
	public function get_icon() {
		$icon_html = '';
		$icon      = (array) $this->get_icon_image( SHOP_CT()->locations->get_base_country() );

		foreach ( $icon as $i ) {
			$icon_html .= '<img src="' . esc_attr( $i ) . '" alt="' . esc_attr__( 'PayPal Acceptance Mark', 'shop_ct' ) . '" />';
		}

		$icon_html .= sprintf( '<a href="%1$s" class="about_paypal" onclick="javascript:window.open(\'%1$s\',\'WIPaypal\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=yes, width=1060, height=700\'); return false;" title="' . esc_attr__( 'What is PayPal?', 'shop_ct' ) . '">' . esc_attr__( 'What is PayPal?', 'shop_ct' ) . '</a>', esc_url( $this->get_icon_url( SHOP_CT()->locations->get_base_country() ) ) );

		return apply_filters( 'shop_ct_gateway_icon', $icon_html, $this->id );
	}

	/**
	 * Get the link for an icon based on country.
	 * @param  string $country
	 * @return string
	 */
	protected function get_icon_url( $country ) {
		$countries = array( 'DZ', 'AU', 'BH', 'BE', 'BQ', 'BW', 'CA', 'CN', 'CW', 'CZ', 'DK', 'FI', 'FR', 'DE', 'GR', 'HK', 'HU', 'IN', 'ID', 'IT', 'JO', 'KE', 'KW', 'LU', 'MY', 'MA', 'NL', 'NO', 'OM', 'PH', 'PL', 'PT', 'QA', 'IE', 'RU', 'BL', 'SX', 'MF', 'SA', 'SG', 'SK', 'KR', 'SS', 'ES', 'SE', 'TW', 'TH', 'TR', 'AE', 'GB', 'US', 'VN' );

		if ( in_array( $country, $countries ) ) {
			return 'https://www.paypal.com/' . strtolower( $country ) . '/webapps/mpp/paypal-popup';
		}

		return 'https://www.paypal.com/' . strtolower( $country ) . '/cgi-bin/webscr?cmd=xpt/Marketing/general/WIPaypal-outside';
	}

	/**
	 * Get PayPal images for a country.
	 * @param  string $country
	 * @return array of image URLs
	 */
	protected function get_icon_image( $country ) {
		switch ( $country ) {
			case 'US' :
			case 'NZ' :
			case 'CZ' :
			case 'HU' :
			case 'MY' :
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo/AM_mc_vs_dc_ae.jpg';
				break;
			case 'TR' :
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_odeme_secenekleri.jpg';
				break;
			case 'GB' :
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/Logo/AM_mc_vs_ms_ae_UK.png';
				break;
			case 'MX' :
				$icon = array(
					'https://www.paypal.com/es_XC/Marketing/i/banner/paypal_visa_mastercard_amex.png',
					'https://www.paypal.com/es_XC/Marketing/i/banner/paypal_debit_card_275x60.gif'
				);
				break;
			case 'FR' :
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_moyens_paiement_fr.jpg';
				break;
			case 'AU' :
				$icon = 'https://www.paypalobjects.com/webstatic/en_AU/mktg/logo/Solutions-graphics-1-184x80.jpg';
				break;
			case 'DK' :
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_PayPal_betalingsmuligheder_dk.jpg';
				break;
			case 'RU' :
				$icon = 'https://www.paypalobjects.com/webstatic/ru_RU/mktg/business/pages/logo-center/AM_mc_vs_dc_ae.jpg';
				break;
			case 'NO' :
				$icon = 'https://www.paypalobjects.com/webstatic/mktg/logo-center/banner_pl_just_pp_319x110.jpg';
				break;
			case 'CA' :
				$icon = 'https://www.paypalobjects.com/webstatic/en_CA/mktg/logo-image/AM_mc_vs_dc_ae.jpg';
				break;
			case 'HK' :
				$icon = 'https://www.paypalobjects.com/webstatic/en_HK/mktg/logo/AM_mc_vs_dc_ae.jpg';
				break;
			case 'SG' :
				$icon = 'https://www.paypalobjects.com/webstatic/en_SG/mktg/Logos/AM_mc_vs_dc_ae.jpg';
				break;
			case 'TW' :
				$icon = 'https://www.paypalobjects.com/webstatic/en_TW/mktg/logos/AM_mc_vs_dc_ae.jpg';
				break;
			case 'TH' :
				$icon = 'https://www.paypalobjects.com/webstatic/en_TH/mktg/Logos/AM_mc_vs_dc_ae.jpg';
				break;
			case 'JP' :
				$icon = 'https://www.paypal.com/ja_JP/JP/i/bnr/horizontal_solution_4_jcb.gif';
				break;
			default :
				$icon = SHOP_CT()->plugin_url() . '/includes/gateways/paypal/assets/images/paypal.png' ;
				break;
		}
		return apply_filters( 'shop_ct_paypal_icon', $icon );
	}


    public function get_request_url(Shop_CT_Order $order)
    {
        $args = apply_filters( 'shop_ct_paypal_args', array_merge(
            array(
                'cmd'           => '_cart',
                'business'      => $this->email,
                'no_note'       => 1,
                'currency_code' => SHOP_CT()->settings->currency,
                'charset'       => 'utf-8',
                'rm'            => is_ssl() ? 2 : 1,
                'upload'        => 1,
                'return'        => add_query_arg(array('oid'=>$order->get_id()),site_url('order-details')),
                'cancel_return' => site_url('shop-ct-payment-canceled'),
                'invoice'       => $order->get_id(),
                'custom'        => $order->get_id(),
                'notify_url'    => site_url('shop-ct-paypal-ipn'),
                'first_name'    => $order->get_billing_first_name(),
                'last_name'     => $order->get_billing_last_name(),
                'address1'      =>  $order->get_billing_address_1(),
                'address2'      => $order->get_billing_address_2(),
                'city'          => $order->get_billing_city(),
                'state'         => $this->get_paypal_state( $order->get_billing_country(), $order->get_billing_state() ),
                'zip'           => $order->get_billing_postcode(),
                'country'       => $order->get_billing_country() ,
                'email'         => $order->get_billing_email(),
            ),
            $this->get_phone_number_args( $order ),
            $this->get_shipping_args( $order ),
            $this->get_line_item_args( $order )
        ), $order );

        $paypal_args = http_build_query( $args, '', '&' );

        if ( $this->testmode ) {
            return 'https://www.sandbox.paypal.com/cgi-bin/webscr?test_ipn=1&' . $paypal_args;
        } else {
            return 'https://www.paypal.com/cgi-bin/webscr?' . $paypal_args;
        }

    }

    protected function get_line_item_args(Shop_CT_Order $order){
        $line_item_args = array();
        $shipping_cost = $order->get_shipping_cost();
        $total = $order->get_total();
        $line_items_index = 1;

        if($shipping_cost > 0 && $shipping_cost !== $total){
            $line_item_args['shipping_1'] = $shipping_cost;
        }elseif( $shipping_cost > 0){
            $line_item_args['item_name_1'] = __('Shipping','shop_ct');
            $line_item_args['quantity_1'] = 1;
            $line_item_args['amount_1'] = $shipping_cost;
            $line_items_index++;
        }
        foreach($order->get_products() as $product){
            /** @var Shop_CT_Product $p */
            $p = $product['object'];

            $line_item_args['item_name_'.$line_items_index] = $p->get_title();
            $line_item_args['quantity_'.$line_items_index] = $product['quantity'];
            $line_item_args['amount_'.$line_items_index] = isset($product['cost']) ? $product['cost'] : $p->get_price();
            $line_items_index++;
        }

        return $line_item_args;

    }

    /**
     * Get phone number args for paypal request.
     * @param  Shop_CT_Order $order
     * @return array
     */
    protected function get_phone_number_args( $order ) {
        if ( in_array( $order->get_billing_country(), array( 'US', 'CA' ) ) ) {
            $phone_number = str_replace( array( '(', '-', ' ', ')', '.' ), '', $order->get_billing_phone() );
            $phone_number = ltrim( $phone_number, '+1' );
            $phone_args   = array(
                'night_phone_a' => substr( $phone_number, 0, 3 ),
                'night_phone_b' => substr( $phone_number, 3, 3 ),
                'night_phone_c' => substr( $phone_number, 6, 4 ),
            );
        } else {
            $phone_args = array(
                'night_phone_b' => $order->get_billing_phone(),
            );
        }
        return $phone_args;
    }

    /**
     * Get shipping args for paypal request.
     * @param  Shop_CT_Order $order
     * @return array
     */
    protected function get_shipping_args( $order ) {
        $shipping_args = array();

        if ( 'yes' == $this->send_shipping) {
            $shipping_args['no_shipping']      = 0;

            // If we are sending shipping, send shipping address instead of billing
            $shipping_args['first_name'] = $order->get_shipping_first_name();
            $shipping_args['last_name']  = $order->get_shipping_last_name();
            $shipping_args['address1']   = $order->get_shipping_address_1();
            $shipping_args['address2']   = $order->get_shipping_address_2();
            $shipping_args['city']       = $order->get_shipping_city();
            $shipping_args['state']      = $order->get_shipping_state();
            $shipping_args['country']    =  $order->get_shipping_country();
            $shipping_args['zip']        = $order->get_shipping_postcode();
        } else {
            $shipping_args['no_shipping']      = 1;
        }

        return $shipping_args;
    }

    /**
     * Get the state to send to paypal.
     * @param  string $cc
     * @param  string $state
     * @return string
     */
    protected function get_paypal_state( $cc, $state ) {
        if ( 'US' === $cc ) {
            return $state;
        }

        $states = SHOP_CT()->locations->get_states( $cc );

        if ( isset( $states[ $state ] ) ) {
            return $states[ $state ];
        }

        return $state;
    }

}