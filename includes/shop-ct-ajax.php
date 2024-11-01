<?php

add_action("shop_ct_ajax_order_popup", 'shop_ct_ajax_order_popup');
add_action('wp_ajax_shop_ct_get_order_item', 'shop_ct_ajax_get_order_item');
add_action('wp_ajax_shop_ct_update_order', 'shop_ct_ajax_update_order');

add_action('wp_ajax_shop_ct_load_products_html', 'shop_ct_ajax_load_products_html');
add_action('wp_ajax_nopriv_shop_ct_load_products_html', 'shop_ct_ajax_load_products_html');

function shop_ct_ajax_load_products_html() {

    if(isset($_GET['prod_category']) && absint($_GET['prod_category']) == $_GET['prod_category']){
        $category = new Shop_CT_Product_Category(absint($_GET['prod_category']));
        $products = shop_ct_get_cat_products($category);
        $paged = Shop_CT_Product::$last_query->query['paged'];
        $totalPages = Shop_CT_Product::$last_query->max_num_pages;

        echo json_encode(array(
            'success' => 1,
            'result' => \ShopCT\Core\TemplateLoader::get_template_buffer('frontend/product/list-items.view.php', compact('products')),
            'paged' => $paged,
            'totalPages' => $totalPages
        ));
        die;
    } else {
        echo json_encode(array('success' => 0));
        die;
    }



}

function shop_ct_ajax_update_order()
{
    if (isset($_REQUEST['order_id'])) {
        $id = $_REQUEST['order_id'];
    } else {
        return;
    }

    if (absint($id) != $id) {
        die('Invalid value passed for "ID" field');
    }

    $id = absint($id);


    $order = new Shop_CT_Order($id);

    $order->set_status($_POST['order_status']);
    $order->set_customer($_POST['order_customer']);
    $order->set_date($_POST['order_date']);

    $date = $_POST['order_date']
        . ' ' . (!empty($_POST['order_date_hours']) && strlen($_POST['order_date_hours']) == 2 ? $_POST['order_date_hours'] : '00')
        . ':' . (!empty($_POST['order_date_minutes']) && strlen($_POST['order_date_minutes']) == 2 ? $_POST['order_date_minutes'] : '00');

    $order->set_date($date);

    $order->set_shipping_first_name($_POST['shipping_first_name'])
        ->set_shipping_last_name($_POST['shipping_last_name'])
        ->set_shipping_company($_POST['shipping_company'])
        ->set_shipping_address_1($_POST['shipping_address_1'])
        ->set_shipping_address_2($_POST['shipping_address_2'])
        ->set_shipping_city($_POST['shipping_city'])
        ->set_shipping_postcode($_POST['shipping_postcode'])
        ->set_shipping_country($_POST['shipping_country'])
        ->set_shipping_state($_POST['shipping_state'])
        ->set_shipping_customer_note($_POST['customer_note'])
        ->set_billing_first_name($_POST['billing_first_name'])
        ->set_billing_last_name($_POST['billing_last_name'])
        ->set_billing_company($_POST['billing_company'])
        ->set_billing_address_1($_POST['billing_address_1'])
        ->set_billing_address_2($_POST['billing_address_2'])
        ->set_billing_city($_POST['billing_city'])
        ->set_billing_postcode($_POST['billing_postcode'])
        ->set_billing_country($_POST['billing_country'])
        ->set_billing_state($_POST['billing_state'])
        ->set_billing_phone($_POST['billing_phone'])
        ->set_payment_method($_POST['payment_method'])
        ->set_transaction_id($_POST['transaction_id']);

    $products = @$_POST['order_products'];

    $order->remove_products();

    if (!empty($products) && is_array($products)) {

        foreach ($products as $product_info) {
            $product = new Shop_CT_Product($product_info['product']);
            $order->add_product($product, $product_info['quantity'], $product->get_price());
        }
    }

    $order->save();

    echo json_encode(array('success' => 1));
    die;
}

function shop_ct_ajax_get_order_item()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'shop_ct_nonce')) {
        wp_die('are_you sure you want to do this?');
    }

    $product = new Shop_CT_Product($_POST['product_id']);
    $cost = $product->get_price();
    $quantity = intval($_POST['quantity']);

    \ShopCT\Core\TemplateLoader::get_template('admin/orders/popup-item.view.php', compact('product', 'quantity', 'cost'));
    die;
}

function shop_ct_ajax_order_popup()
{
    /** Check if we are editing an order or adding new order */
    if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && $_REQUEST['id'] > 0) {
        /** if editing get the post object from database */

        $id = absint($_REQUEST['id']);

        $autoDraft = false;

        $order = new Shop_CT_Order($id);

    } else {
        $autoDraft = true;
        /** if adding new order create an auto-draft to work with */

        $order = new Shop_CT_Order();
    }

    $countries = SHOP_CT()->locations->get_countries();
    $customers = Shop_CT_Customer::get_all_for_selectbox();
    $allProducts = Shop_CT_Product::all();

    echo json_encode(array(
        'success' => 1,
        'return_html' => \ShopCT\Core\TemplateLoader::get_template_buffer('admin/orders/popup.view.php', compact('order', 'autoDraft', 'countries', 'customers', 'allProducts')),
        'title' => $autoDraft ? __('Add Order', 'shop_ct') : sprintf('%s #%s', __('Order', 'shop_ct'), $order->get_id()),
    ));
    die();
}
