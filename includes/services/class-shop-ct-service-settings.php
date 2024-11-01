<?php

class Shop_CT_Service_Settings extends Shop_CT_Settings {

    public $show_cart_button_in_menu;

    /** @var  string Base Location of store */
    public $base_country = '';

    /** @var string/array Selling Location(s) */
    public $allowed_countries = '';

    /** @var string/array  */
    public $specific_ship_to_countries = '';

    /** @var string Default Customer Address */
    public $default_customer_address = '';

    /** @var string Currency used in store */
    public $currency = '';

    /** @var string  Currency position */
    public $currency_pos = '';

    /** @var string Thousand Separator */
    public $price_thousand_sep = '';

    /** @var string Decimal Separator */
    public $price_decimal_sep = '';

    /** @var string Number of Decimals */
    public $price_num_decimals = '';

    /**
     * ECWP_General_Settings constructor.
     */
    public function __construct() {
        $this->init();
    }

    public function init(){
        $this->form_id = 'shop_ct_general_options_form';
        /**
         * Initialize user defined options
         */
        $this->base_country = $this->get_option( 'base_country', '');
        $this->show_cart_button_in_menu = $this->get_option( 'show_cart_button_in_menu', 'yes');
        $this->allowed_countries = $this->get_option( 'allowed_countries', 'all' );
        $this->specific_ship_to_countries = $this->get_option( 'specific_ship_to_countries', '' );
        $this->default_customer_address = $this->get_option( ' ', 'geolocation' );
        $this->currency = $this->get_option('currency', 'USD');
        $this->currency_pos = $this->get_option('currency_pos','left');
        $this->price_thousand_sep = $this->get_option('price_thousand_sep',',');
        $this->price_decimal_sep = $this->get_option('price_decimal_sep','.');
        $this->price_num_decimals = $this->get_option('price_num_decimals','2');

        $this->custom_css = $this->get_option( 'custom_css', '' );

        $this->init_sections();
        $this->init_controls();
    }

    /**
     * Initialize Sections
     */
    public function init_sections(){
        $this->sections = array(
            'general' => array(
                'title'=>__('General Options','shop_ct'),
            ),
            'currency' => array(
                'title'=>__('Currency Options','shop_ct'),
                'description'=>__('The following options affect how prices are displayed on the frontend.','shop_ct'),
            ),
            'design' => array(
                'title' => __( 'Design Options', 'shop_ct' ),
            )
        );
    }

    /**
     * Initialize controls
     */
    public function init_controls(){
        $this->controls = array(
            'shop_ct_show_cart_button_in_menu' => array(
                'label' => __('Show cart button in menu?','shop_ct'),
                'section'=>'general',
                'type'=>'checkbox',
                'default'=>$this->show_cart_button_in_menu,
            ),
            'shop_ct_base_country' => array(
                'label' => __('Base Location','shop_ct'),
                'section'=>'general',
                'type'=>'select',
                'countries'=>'yes',
                'default'=>$this->base_country,
            ),
            'shop_ct_allowed_countries'	=> array(
                'label'=>__('Selling Location(s)','shop_ct'),
                'section'=>'general',
                'type'=>'select',
                'default'=>$this->allowed_countries,
                'choices'=>array(
                    'all'=>__("Sell to all countries","shop_ct"),
                    'specific'=>__('Sell to specific countries only','shop_ct'),
                )
            ),
            'shop_ct_specific_ship_to_countries'=>array(
                'label'=>__('Specific Countries','shop_ct'),
                'section'=>'general',
                'type'=>'select',
                'multiple' => true,
                'countries'=>'yes',
                'html_class' => ( $this->allowed_countries == 'all' ? array('hidden') : array() ),
                'default'=>$this->specific_ship_to_countries,
            ),
            'shop_ct_button_select_shipping_countries'=>array(
                'section'=>'general',
                'type'=>'button_select_countries',
                'html_class' => ( $this->allowed_countries == 'all' ? array('hidden') : array() ),
            ),
            'shop_ct_default_customer_address'=>array(
                'label'=>__('Customer Default Address','shop_ct'),
                'section'=>'general',
                'type'=>'select',
                'default'=>$this->default_customer_address,
                'choices'=>array(
                    ''=>__('No address','shop_ct'),
                    'base'=>__('Shop base address','shop_ct'),
                    'geolocation'=>__('Geolocate','shop_ct'),
                    'geolocation_ajax'=>__('Geolocate (with page caching support)','shop_ct'),
                )
            ),
            'shop_ct_currency'=>array(
                'label'=>__('Currency','shop_ct'),
                'section'=>'currency',
                'type'=>'select',
                'search'=>'yes',
                'default'=>$this->currency,
                'choices'=>$this->get_currencies(),
            ),
            'shop_ct_currency_pos'=>array(
                'label'=>__('Currency Symbol Position','shop_ct'),
                'section'=>'currency',
                'type'=>'select',
                'default'=>$this->currency_pos,
                'choices'=>array(
                    'left'=>__( 'Left (' . Shop_CT_Currencies::get_currency_symbol( $this->currency ) . '99.99)', 'shop_ct' ),
                    'right' => __( 'Right (99.99' . Shop_CT_Currencies::get_currency_symbol( $this->currency) . ')', 'shop_ct' ),
                    'left-space' => __( 'Left with space (' . Shop_CT_Currencies::get_currency_symbol( $this->currency ) . '&nbsp;99.99)', 'shop_ct' ),
                    'right-space'=> __( 'Right with space (99.99&nbsp;' . Shop_CT_Currencies::get_currency_symbol( $this->currency) . ')', 'shop_ct' )
                )
            ),
            'shop_ct_price_thousand_sep'=>array(
                'label'=>__('Thousand Separator','shop_ct'),
                'section'=>'currency',
                'type'=>'text',
                'default'=>$this->price_thousand_sep,
            ),
            'shop_ct_price_decimal_sep'=>array(
                'label'=>__('Decimal Separator','shop_ct'),
                'section'=>'currency',
                'type'=>'text',
                'default'=>$this->price_decimal_sep,
            ),
            'shop_ct_price_num_decimals'=>array(
                'label'=>__('Number of Decimals','shop_ct'),
                'section'=>'currency',
                'type'=>'text',
                'default'=>$this->price_num_decimals,
            ),
            'shop_ct_custom_css'=>array(
                'label'=>__('Custom CSS','shop_ct'),
                'section'=>'design',
                'type'=>'textarea',
                'default'=>$this->custom_css,
            )

        );;
    }

    /**
     * Select All/None
     * @param $id
     * @param $control
     */
    public function control_button_select_countries($id,$control){
        ?>
        <div style="text-align: right">
            <button type="button" id="select_all_countries_btn" class="shop_ct_mui_ripple_button ecpw_mui_ripple"><?php _e('Select all','shop_ct'); ?></button>
            <button type="button" id="select_no_countries_btn" class="shop_ct_mui_ripple_button ecpw_mui_ripple"><?php _e('Select None','shop_ct'); ?></button>
        </div>
        <?php
    }

    /**
     * Returns array of currencies for dropdown
     * @return array
     */
    public function get_currencies(){
        $base = Shop_CT_Currencies::get_currencies();

        foreach($base as $code=>$name){
            $base[$code] = esc_html( $name . ' (' . Shop_CT_Currencies::get_currency_symbol( $code ) . ')' );
        }

        return $base;
    }


}
