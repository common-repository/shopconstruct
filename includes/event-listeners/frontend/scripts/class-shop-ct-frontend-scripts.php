<?php


class Shop_CT_Frontend_Scripts
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'run'));
        add_action('shop_ct_order_success_page',array($this,'enqueue_order_success'));
        add_action('shop_ct_product_shortcode',array($this,'single_product'));
    }

    public function run()
    {
        $js_vars = array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'shop_ct_nonce' => wp_create_nonce('shop_ct_nonce'),
            'display_settings_click_right' => __('Click the arrow on the right of the item to reveal additional configuration options.', 'shop_ct'),
            'gmt_offset' => Shop_CT_Dates::get_wp_gmt_offset(),
            'star1' => __('HATED IT !', 'shop_ct'),
            'star2' => __('NOT BAD !', 'shop_ct'),
            'star3' => __('GOOD !', 'shop_ct'),
            'star4' => __('LOVED IT !', 'shop_ct'),
            'star5' => __('EXCELLENT !', 'shop_ct'),
            'cartUpdated' => __('Cart updated successfully', 'shop_ct'),
            'emptyCart' => __('Your shopping cart is empty','shop_ct')
        );


        wp_enqueue_style( "shop_ct_font_awesome", SHOP_CT()->plugin_url() . "/lib/fontawesome/css/fontawesome-all.min.css", false );

        wp_enqueue_style('animate-css', SHOP_CT()->plugin_url() . '/assets/css/animate.css', false, SHOP_CT()->version);
        wp_enqueue_style('shop-ct-common', SHOP_CT()->plugin_url() . '/assets/css/frontend/common.css', false, SHOP_CT()->version);
        wp_enqueue_style('shop-ct-popup', SHOP_CT()->plugin_url() . '/assets/css/popup.style.css', false, SHOP_CT()->version);

        wp_enqueue_style('shop-ct-checkout', SHOP_CT()->plugin_url() . '/assets/css/frontend/checkout.css', false, SHOP_CT()->version);


        wp_enqueue_script('shop-ct-popup', SHOP_CT()->plugin_url() . '/assets/js/shop-ct-popup.js', array('jquery'), SHOP_CT()->version);
        wp_enqueue_script('shop-ct-common', SHOP_CT()->plugin_url() . '/assets/js/frontend/common.js', array('shop-ct-popup'), SHOP_CT()->version, true);
        wp_enqueue_script('shop-ct-checkout', SHOP_CT()->plugin_url() . '/assets/js/frontend/checkout.js', array('jquery'), SHOP_CT()->version, true);

        wp_localize_script( 'shop-ct-checkout', 'shopCTcheckout', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'shop_ct_nonce' => wp_create_nonce('shop_ct_nonce'),
            'checkoutSuccess' => __('Order Created Successfully','shop_ct'),
            'btnLoading' => __('Loading...','shop_ct'),
            'btnRedirecting' => __('Redirecting...','shop_ct'),
            'formRequiredFieldsEmpty' => __('Please fill in all required fields', 'shop_ct'),
            ) );
        wp_localize_script('shop-ct-common', 'shopCTL10n',$js_vars);


        if(is_single() && is_singular(Shop_CT_Product::get_post_type())){
            $this->single_product();
        }


        /*if ( in_array( $id, $shop_ids ) || is_tax( Shop_CT_Product_Category::get_taxonomy() ) ) {
            if ( ! wp_script_is( 'jquery' ) ) {
                wp_enqueue_script( 'jquery' );
            }
            wp_enqueue_style( "shop_ct_front_css", plugins_url( "../assets/css/front_end_style.css", __FILE__ ), false );

            wp_enqueue_style( "shop_ct_font_awesome", 'http://fontawesome.io/assets/font-awesome/css/font-awesome.css', false );
            wp_enqueue_script( "shop_ct_front_end_js", plugins_url( "../assets/js/front-end.js", __FILE__ ), false );
            wp_enqueue_script( "shop_ct_js_elevatezoom", SHOP_CT()->plugin_url()."lib/elevatezoom-master/jquery.elevatezoom.js", false );
            wp_enqueue_script( "shop_ct_js_elevatezoom_min", SHOP_CT()->plugin_url()."lib/elevatezoom-master/jquery.elevateZoom-3.0.8.min.js", false );
            wp_localize_script( 'shop_ct_front_end_js', 'shopCTL10n', $js_vars );
        }*/
    }

    public function single_product()
    {
        wp_enqueue_style( "google-fonts-lato", "https://fonts.googleapis.com/css?family=Lato:300,400,600,700", false );
        wp_enqueue_script( "elevate-zoom", SHOP_CT()->plugin_url()."/lib/elevatezoom-master/jquery.elevateZoom-3.0.8.min.js", array('jquery') );
        wp_enqueue_script( "countdown-js", SHOP_CT()->plugin_url()."/lib/jquery.countdown-2.1.0/jquery.countdown.js", array('jquery') );
        wp_enqueue_script( "moment-js", SHOP_CT()->plugin_url()."/lib/moment-js/moment.js" );
        wp_enqueue_script( "moment-timezone", SHOP_CT()->plugin_url()."/lib/moment-js/moment-timezone-with-data.js" );
        wp_enqueue_script('shop-ct-product', SHOP_CT()->plugin_url() . '/assets/js/frontend/product.js', array('elevate-zoom','countdown-js', 'moment-js','moment-timezone','jquery'), SHOP_CT()->version);
        wp_enqueue_style('shop-ct-product', SHOP_CT()->plugin_url() . '/assets/css/frontend/product.css', false, SHOP_CT()->version);
    }

    public function enqueue_order_success()
    {
        wp_enqueue_style('shop-ct-order-success', SHOP_CT()->plugin_url() . '/assets/css/frontend/order-success.css', false, SHOP_CT()->version);
    }

}
