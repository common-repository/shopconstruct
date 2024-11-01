<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

include_once( "shop-ct-product-functions.php" );

/**
 * Filters on data used in admin and frontend.
 */
add_filter( 'shop_ct_stock_amount', 'intval' ); 						// Stock amounts are integers by default

/**
 * Short Description (excerpt).
 */
add_filter( 'shop_ct_short_description', 'wptexturize' );
add_filter( 'shop_ct_short_description', 'convert_smilies' );
add_filter( 'shop_ct_short_description', 'convert_chars' );
add_filter( 'shop_ct_short_description', 'wpautop' );
add_filter( 'shop_ct_short_description', 'shortcode_unautop' );
add_filter( 'shop_ct_short_description', 'prepend_attachment' );
add_filter( 'shop_ct_short_description', 'do_shortcode', 11 );