<?php

add_action( 'shop_ct_ajax_email_popup', 'shop_ct_ajax_email_popup_callback' );
function shop_ct_ajax_email_popup_callback() {
	$id = $_REQUEST['id'];

	do_action('shop_ct_email_' . $id . '_popup');
}

add_action('shop_ct_ajax_save_email_settings', 'shop_ct_ajax_save_email_settings_callback' );
function shop_ct_ajax_save_email_settings_callback()
{
	$data = $_POST['data'];

	$data['receiver'] = explode(',', $data['receiver']);

	foreach ( $data['receiver'] as &$recipient ) {
		$recipient = trim($recipient);
		$recipient = stripslashes($recipient);
		$recipient = htmlspecialchars($recipient);
	}

	unset($recipient);

	foreach ( $data as $key => $value ) {
		if ($key != 'id') {
			$result[$key] = update_option( 'shop_ct_email_' . $data['id'] . '_' . $key, $value );
		}
	}

	echo json_encode($result);

	wp_die();
}