<?php

add_action('shop_ct_ajax_shipping_zone_popup', 'shop_ct_ajax_shipping_zone_popup_callback');
function shop_ct_ajax_shipping_zone_popup_callback() {
	$popup = new Shop_CT_Popup_Shipping_Zone();

	$popup->two_column = false;

	$popup->form_id = 'shop_ct_shipping_zone_popup_form';

	$zone_id = isset($_REQUEST['id']) ? $_REQUEST['id'] : 'new';

	$defaults['name'] = '';
	$defaults['cost'] = 0;
	$defaults['status'] = 1;
	$defaults['countries'] = array();

	if ('new' !== $zone_id) {
		$zone = new Shop_CT_Shipping_Zone($zone_id);

		$defaults['name'] = $zone->get_name();
		$defaults['cost'] = $zone->get_cost();
		$defaults['status'] = $zone->get_status();
		$defaults['countries'] =  $zone->get_countries();
	}

	$popup->sections['main'] = [
		'priority' => 1,
		'type' => 'default'
	];

	$popup->controls['name'] = [
		'type' => 'text',
		'label' => __('Name', 'shop_ct'),
		'default' => $defaults['name'],
		'section' => 'main',
	];

	$popup->controls['cost'] = [
		'type' => 'number',
		'label' => __('Cost', 'shop_ct') . ' (' . Shop_CT_Currencies::get_currency_symbol() . ')',
		'default' => $defaults['cost'],
		'section' => 'main',
	];

	$popup->controls['status'] = [
		'type' => 'select',
		'choices' => Shop_CT_Shipping_Zone::get_statuses(),
		'section' => 'main',
		'label' => __('Status', 'shop_ct'),
		'default' => $defaults['status'],
	];

	if (!isset($zone) || !($zone instanceof Shop_CT_Shipping_Zone) || 1 !== $zone->get_id()) {
		$popup->controls['countries'] = [
			'type' => 'multi_select',
			'search' => true,
			'choices' => SHOP_CT()->locations->get_continents(),
			'section' => 'main',
			'attrs' => ['multiple' => 'multiple'],
			'label' => __('Countries', 'shop_ct'),
			'default' => $defaults['countries'],
		];
	}

	$popup->controls['id'] = [
		'type' => 'hidden',
		'section' => 'main',
		'default' => $zone_id,
	];

	$popup->controls['save'] = [
		'type' => 'submit',
		'value' => __('Save', 'shop_ct'),
		'section' => 'main',
	];

	ob_start();

	$popup->display();

	$html = ob_get_clean();

	echo json_encode(array(
		'return_html' => $html,
		'success' => true,
	));

	wp_die();
}

add_action('shop_ct_ajax_save_shipping_zone', 'shop_ct_ajax_save_shipping_zone_callback');
function shop_ct_ajax_save_shipping_zone_callback() {
	$id = is_numeric($_POST['id']) ? absint($_POST['id']) : null;
	$name = $_POST['name'];
	$cost = $_POST['cost'];
	$status = $_POST['status'];
	$countries = $_POST['countries'];

	$zone = new Shop_CT_Shipping_Zone($id);
	$zone
		->set_name($name)
		->set_cost($cost)
		->set_status($status)
		->set_countries($countries);

	echo json_encode($zone->save());
	wp_die();
}

add_action('shop_ct_ajax_delete_shipping_zone', 'shop_ct_ajax_delete_shipping_zone_callback');
function shop_ct_ajax_delete_shipping_zone_callback() {
	$id = $_POST['id'];
	$result = Shop_CT_Shipping_Zone::delete($id);

	echo json_encode(['success' => (bool)$result]);
	wp_die();
}
