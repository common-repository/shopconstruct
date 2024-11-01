<?php
add_action('shop_ct_ajax_save_order', 'save_order');
function save_order() {

    $id = $_POST['id'];
    $order_data = $_POST['order_data'];
    $billing_details = isset($order_data['billing_details']) ? $order_data['billing_details'] : array();
    $shipping_details = isset($order_data['shipping_details']) ? $order_data['shipping_details'] : array();

    $settables = array_merge($billing_details, $shipping_details);

    $products = isset($order_data['products']) ? $order_data['products'] : array();

    $order = new Shop_CT_Order($id);

	foreach ($settables as $key => $value) {
		$function_name = 'set_' . $key;

		if (method_exists($order, $function_name)) {
			call_user_func([$order, $function_name], $value);
		}
    }

	foreach ($products as $product_id => $quantity) {
		$quantity = absint($quantity);

		if ($quantity) {
			$order->add_product(new Shop_CT_Product($product_id), $quantity);
		}
    }

    $order_data['date']['year'] = explode('-', $order_data['date']['date'])[0];
	$order_data['date']['month'] = explode('-', $order_data['date']['date'])[1];
	$order_data['date']['day'] = explode('-', $order_data['date']['date'])[2];

    $order
	    ->set_status($order_data['status'])
	    ->set_customer($order_data['customer'])
	    ->set_date(mktime($order_data['date']['hour'], $order_data['date']['minute'], $order_data['date']['second'], $order_data['date']['month'], $order_data['date']['day'], $order_data['year']));

	if (isset($order_data['comments']) && is_array($order_data['comments'])) {
		$order->set_notes($order_data['comments']);
	}

    $response = json_encode($order->save());

    echo $response;

    wp_die();
}

add_action( 'shop_ct_ajax_delete_order', 'shop_ct_ajax_delete_order_callback' );
function shop_ct_ajax_delete_order_callback() {
	$ids = $_POST['ids'];
	$statuses = array();

	foreach ( $ids as $id ) {
		$statuses[ $id ] = Shop_CT_Order::delete( $id );
	}

	echo json_encode( $statuses );
	wp_die();
}

add_action('shop_ct_ajax_change_order_status', 'shop_ct_ajax_change_order_status_callback');
function shop_ct_ajax_change_order_status_callback() {
	$status = $_POST['status'];

	$statuses = array();

	if (isset($_POST['ids']) && is_array($_POST['ids'])) {
		foreach ($_POST['ids'] as $id) {
			$statuses[$id] = Shop_CT_Order::update_status($id, $status);
		}
	} elseif (isset($_POST['id'])) {
		$id = $_POST['id'];

		$statuses[$id] = Shop_CT_Order::update_status($id, $status);
	}

	echo json_encode($statuses);
	wp_die();
}

add_action( 'shop_ct_ajax_delete_auto_draft', 'shop_ct_ajax_delete_auto_draft_callback' );
function shop_ct_ajax_delete_auto_draft_callback() {
	$id = absint($_POST['id']);

	if (!$id) {
		wp_die();
	}

	if (get_post_status($id) == 'auto-draft') {
		wp_delete_post($id, true);
	}

	wp_die();
}

add_action('shop_ct_order_new', 'shop_ct_order_new_callback', 10, 1);
function shop_ct_order_new_callback($args)
{
	if ( isset($args['order_data']['order']) && $args['order_data']['order'] instanceof Shop_CT_Order ) {
		do_action( 'shop_ct_new_order', array( 'order_data' => array('order' => $args['order_data']['order'])) );
	}
}

add_action( 'shop_ct_order_status_shop-ct-completed', 'shop_ct_order_status_shop_ct_completed_callback', 10, 1 );
function shop_ct_order_status_shop_ct_completed_callback( $args ) {

	if (isset($args['order_data']['order']) && $args['order_data']['order'] instanceof Shop_CT_Order) {
		do_action('shop_ct_completed_order', array('order_data' => array('order' => $args['order_data']['order'])));
		do_action('shop_ct_customer_invoice', array('order_data' => array('order' => $args['order_data']['order'])));
	} elseif (isset($args['id'])) {
		$id = intval($args['id']);

		$order = new Shop_CT_Order( $id );

		do_action( 'shop_ct_completed_order', array( 'order_data' => array('order' => $order) ) );
		do_action( 'shop_ct_customer_invoice', array( 'order_data' => array('order' => $order) ) );
	}
}

add_action( 'shop_ct_order_status_shop-ct-failed', 'shop_ct_order_status_shop_ct_failed_callback', 10, 1 );
function shop_ct_order_status_shop_ct_failed_callback( $args ) {
	if (isset($args['order_data']['order']) && $args['order_data']['order'] instanceof Shop_CT_Order) {
		do_action( 'shop_ct_failed_order', array('order_data' => array('order' => $args['order_data']['order'])) );
	} elseif (isset($args['id'])) {
		$id = intval($args['id']);

		$order = new Shop_CT_Order($id);

		do_action('shop_ct_failed_order', array('order_data' => array('order' => $order)));
	}
}

add_action( 'shop_ct_order_status_shop-ct-cancelled', 'shop_ct_order_status_shop_ct_cancelled_callback', 10, 1 );
function shop_ct_order_status_shop_ct_cancelled_callback( $args ) {
	if ( isset( $args['order_data']['order'] ) && $args['order_data']['order'] instanceof Shop_CT_Order ) {
		do_action('shop_ct_cancelled_order', array('order_data' => array('order' => $args['order_data']['order'])));
	} elseif ( isset( $args['id'] ) ) {
		$id = intval( $args['id'] );

		$order = new Shop_CT_Order($id);

		do_action('shop_ct_cancelled_order', array('order_data' => array('order' => $order)));
	}
}

add_action( 'shop_ct_order_status_shop-ct-processing', 'shop_ct_order_status_shop_ct_processing_callback', 10, 1 );
function shop_ct_order_status_shop_ct_processing_callback( $args ) {
	if ( isset( $args['order_data']['order'] ) && $args['order_data']['order'] instanceof Shop_CT_Order ) {
		do_action('shop_ct_processing_order', array('order_data' => array('order' => $args['order_data']['order'])));
	} elseif ( isset( $args['id'] ) ) {
		$id = intval( $args['id'] );

		$order = new Shop_CT_Order($id);

		do_action('shop_ct_processing_order', array('order_data' => array('order' => $order)));
	}
}

add_action('shop_ct_order_status_shop-ct-refunded', 'shop_ct_order_status_shop_ct_refunded_callback' );
function shop_ct_order_status_shop_ct_refunded_callback($args) {
	if ( isset( $args['order_data']['order'] ) && $args['order_data']['order'] instanceof Shop_CT_Order ) {
		do_action('shop_ct_refunded_order', array('order_data' => array('order' => $args['order_data']['order'])));
	} elseif ( isset( $args['id'] ) ) {
		$id = intval( $args['id'] );

		$order = new Shop_CT_Order($id);

		do_action('shop_ct_refunded_order', array('order_data' => array('order' => $order)));
	}
}

add_action('shop_ct_ajax_get_order_shipping_cost', 'shop_ct_ajax_get_order_shipping_cost_callback');
function shop_ct_ajax_get_order_shipping_cost_callback() {
	$code = $_GET['code'];
	$products = $_GET['products'];

	$cost = Shop_CT_Order::get_shipping_cost_by_country_and_products($code, $products);

	$response['success'] = is_numeric($cost);

	if ($response['success']) {
		$response['cost'] = $cost;
	} elseif (false === $cost) {
		$response['info'] = ['text' => __('Your shipping country is not in our shipping zone', 'shop_ct')];
	} elseif (null === $cost) {
		$response['info'] = ['text' => __('Your order does not require delivery', 'shop_ct')];
	}

	echo json_encode($response);
	wp_die();
}