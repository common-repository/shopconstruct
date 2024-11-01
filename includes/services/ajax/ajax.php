<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/** Add Actions for ajax hooks */
add_action( "wp_ajax_shop_ct_ajax", "shop_ct_ajax_callback" );
add_action( "shop_ct_ajax_wp_editor_ajax", "shop_ct_ajax_wp_editor_ajax_callback" );
add_action( "shop_ct_ajax_get_page_contents", "shop_ct_ajax_get_page_contents_callback" );
add_action("wp_ajax_shop_ct_checkout","shop_ct_ajax_checkout");
add_action("wp_ajax_shop_ct_checkout_shipping_select","shop_ct_checkout_shipping_select");

/**
 * Main ShopConstruct ajax callback which calls for a specific hook to make ajax callbacks inside ECWP
 */
function shop_ct_ajax_callback() {
    if ( isset( $_REQUEST['nonce'] ) && ! empty( $_REQUEST['nonce'] ) ) {
        $nonce = $_REQUEST['nonce'];
        if ( ! wp_verify_nonce( $nonce, 'shop_ct_nonce' ) ) {
            echo json_encode( array( "error" => __( 'Wrong wp_nonce parameter', 'shop_ct' ) ) );
            die();
        }
    } else {
        echo json_encode( array( "error" => __( 'Wrong wp_nonce parameter', 'shop_ct' ) ) );
        die();
    }
    if ( isset( $_REQUEST['task'] ) && ! empty( $_REQUEST['task'] ) ) {
        $task = $_REQUEST['task'];
        if ( isset( $_GET['action'] ) ) {
            unset( $_GET['action'] );
        }
        if ( isset( $_GET['task'] ) ) {
            unset( $_GET['task'] );
        }
        if ( isset( $_GET['nonce'] ) ) {
            unset( $_GET['nonce'] );
        }
        do_action( "shop_ct_ajax_" . $task );

    } else {
        die( '0' );
    }
    /* Default status */
    die( '0' );
}

/**
 * Ajax callback for TinyMCE
 */
function shop_ct_ajax_wp_editor_ajax_callback() {
    ob_start();

    if ( isset( $_GET['id'] ) ) {
        $id = $_GET['id'];
    } else {
        $id = "";
    }
    if ( isset( $_GET['default'] ) ) {
        $default = $_GET['default'];
    } else {
        $default = "";
    }

    $default = stripslashes($default);
    $default = wp_kses_post($default);

    wp_editor( $default, $id, array() );

    $return = ob_get_clean();

    echo json_encode( array( "success" => 1, "return" => $return ) );
    die;
}

/**
 * Change the page of ShopConstruct admin panel
 */
function shop_ct_ajax_get_page_contents_callback() {
    $slug = $_GET['slug'];

    SHOP_CT()->admin->current_page = $slug;

    $item = SHOP_CT()->admin->menus->get_item( $slug );

    ob_start();

    SHOP_CT()->admin->menus->load_admin_page();

    $html = ob_get_clean();

    echo json_encode( array(
        "success"       => 1,
        "page_contents" => $html,
        "title"         => $item->page_title,
        "slug"          => $slug
    ) );

    die();
}

function shop_ct_checkout_shipping_select(){
    $country = $_GET['country'];
    $cart = SHOP_CT()->cart_manager->get_cart();


    $zone = Shop_CT_Shipping_Zone::get_zone_by_location($country);

    if(false !== $zone){
        echo json_encode(array(
            'success'=>1,
            'shipping_cost_format' => Shop_CT_Formatting::format_price($zone->get_cost()),
            'shipping_cost' => $zone->get_cost(),
            'total' => Shop_CT_Formatting::format_price($cart->get_total() + $zone->get_cost()),
            'label' => sprintf('%s(%s):', __("Shipping","shop_ct"), $zone->get_name())
        ));
        die;
    }

    echo json_encode(array(
        'success'=>0,
    ));
    die;


}

function shop_ct_ajax_checkout(){
//    register_shutdown_function(function (){
//        var_dump(error_get_last());
//    });

    $order = new Shop_CT_Order();

    if(empty($_POST['shop_ct_order_product_ids'])){
        echo json_encode(array(
            'error' => __('Please add products to your cart before confirming order','shop_ct'),
            'success' => 0,
        ));
        die;
    }

    foreach($_POST['shop_ct_order_product_ids'] as $key=>$product_id){
        $product =new Shop_CT_Product($product_id);
        $order->add_product($product, $_POST['shop_ct_order_product_qty'][$key]);
    }

    try{
        $order
            ->set_billing_first_name($_POST['billing_first_name'])
            ->set_billing_last_name($_POST['billing_last_name'])
            ->set_billing_address_1($_POST['billing_address_1'])
            ->set_billing_country($_POST['billing_country'])
            ->set_billing_email($_POST['billing_email'])
            ->set_status('shop-ct-pending')
            ->set_payment_method($_POST['payment_method']);
    } catch (Exception $e){
        echo json_encode(array(
            'error' => $e->getMessage(),
            'success' => 0,
        ));
        die;
    }

    if($order->requires_delivery()){
        try{
            $order
                ->set_shipping_first_name($_POST['shipping_first_name'])
                ->set_shipping_last_name($_POST['shipping_last_name'])
                ->set_shipping_address_1($_POST['shipping_address_1'])
                ->set_shipping_city($_POST['shipping_city'])
                ->set_shipping_postcode($_POST['shipping_postcode'])
                ->set_shipping_country($_POST['shipping_country'])
                ->set_shipping_state($_POST['shipping_state'])
                ->set_shipping_cost($_POST['shipping_cost']);
        } catch (Exception $e){
            echo json_encode(array(
                'error' => $e->getMessage(),
                'success' => 0,
            ));
            die;
        }
    }


    if(isset($_POST['shipping_company'])){
        $order->set_shipping_company($_POST['shipping_company']);
    }
    if(isset($_POST['shipping_company'])){
        $order->set_shipping_address_2($_POST['shipping_address_2']);
    }
    if(isset($_POST['shipping_customer_note'])){
        $order->set_shipping_customer_note($_POST['shipping_customer_note']);
    }
    if(isset($_POST['billing_state'])){
        $order->set_billing_state($_POST['billing_state']);
    }
    if(isset($_POST['billing_postcode'])){
        $order->set_billing_postcode($_POST['billing_postcode']);
    }
    if(isset($_POST['billing_company'])){
        $order->set_billing_company($_POST['billing_company']);
    }
    if(isset($_POST['billing_address_2'])){
        $order->set_billing_address_2($_POST['billing_address_2']);
    }
    if(isset($_POST['billing_city'])){
        $order->set_billing_city($_POST['billing_city']);
    }
    if(isset($_POST['billing_phone'])){
        $order->set_billing_phone($_POST['billing_phone']);
    }

    if(isset($_POST['transaction_id'])){
        $order->set_transaction_id($_POST['transaction_id']);
    }

    $order->save();


    $data =array();

    switch($order->get_payment_method()){
        case 'paypal':
            $data = array(
                'redirect' => SHOP_CT()->payment_gateways->payment_gateways()['paypal']->get_request_url($order),
            );
            break;
        case 'cheque':
            $data = array(
                'instructions' => SHOP_CT()->payment_gateways->payment_gateways()['cheque']->get_instructions(),
            );
            break;
        case 'cod':
            $data = array(
                'instructions' => SHOP_CT()->payment_gateways->payment_gateways()['cod']->get_instructions()
            );
            break;
        case 'bacs':
            $data = array(
                'instructions' => SHOP_CT()->payment_gateways->payment_gateways()['bacs']->get_instructions()
            );
            break;
    }

    // empty the shopping bag
    Shop_CT_Cart::delete(SHOP_CT()->cart_manager->get_cart()->get_hash());

    echo json_encode(array_merge($data,array('success' => 1)));
    die;
}
