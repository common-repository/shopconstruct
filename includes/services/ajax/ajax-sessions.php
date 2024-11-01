<?php
/** Add Actions */

add_action( 'init', 'shop_ct_session_start' );
add_action( "shop_ct_ajax_set_session", "shop_ct_ajax_set_session_callback" );
add_action( "shop_ct_ajax_get_sessions", "shop_ct_ajax_get_sessions_callback" );

function shop_ct_session_start() {
	if ( ! session_id() ) {
		session_start();
	}
}

function shop_ct_ajax_set_session_callback() {
	if ( session_id() == '' ) {
		die( 'no session' );
	} else {
		if ( isset( $_GET['name'] ) && ! empty( $_GET['name'] ) && isset( $_GET['value'] ) ) {
			$name              = $_GET['name'];
			$value             = $_GET['value'];
			$_SESSION[ $name ] = $value;
			echo json_encode( array( "success" => 1, "session" => $_SESSION[ $name ] ) );
			die();
		}
	}
}


function shop_ct_ajax_get_sessions_callback() {
	if ( session_id() == '' ) {
		die( 'no session' );
	} else {
		echo json_encode( array( "success" => 1, "sessions" => $_SESSION ) );
		die();
	}
}