<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shop_CT_Install Class.
 */

class Shop_CT_Install {

	/**
	 * Check ShopConstruct version and run the updater is required.
	 *
	 * This check is done on all requests and runs if the versions do not match.
	 */

	public static function check_version() {
		if( get_option( 'shop_ct_version' ) !== SHOP_CT()->version ){
			self::install();
			update_option( 'shop_ct_version',SHOP_CT()->version );
			do_action( 'shop_ct_updated' );
		}
	}

	/**
	 * Install ECWP.
	 */
	public static function install() {

		if ( ! defined( 'SHOP_CT_INSTALLING' ) ) {
			define( 'SHOP_CT_INSTALLING', true );
		}

		self::create_tables();
		self::schedule_cron_jobs();
		self::install_default_settings();

		// Trigger action
		do_action( 'shop_ct_installed' );
	}

	private static function install_default_settings()
	{
		$checkout_page = get_option('shop_ct_checkout_page_id');

		if (!empty($checkout_page)) {
			return;
		}

		$post_id = wp_insert_post(array(
			'post_title' => __('Checkout', 'shop_ct'),
			'post_name' => 'shop-ct-checkout',
			'post_type' => 'page',
			'post_status' => 'publish'
		));

		update_option('shop_ct_checkout_page_id', $post_id);
	}

	private static function create_tables(){
		global $wpdb;

		$collate = '';

		if ( $wpdb->has_cap( 'collation' ) ) {
			if ( ! empty( $wpdb->charset ) ) {
				$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			}
			if ( ! empty( $wpdb->collate ) ) {
				$collate .= " COLLATE $wpdb->collate";
			}
		}

		$wpdb->query( "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "shop_ct_sessions` (
		  session_id bigint(20) NOT NULL AUTO_INCREMENT,
		  session_key char(32) NOT NULL,
		  session_value longtext NOT NULL,
		  session_expiry bigint(20) NOT NULL,
		  UNIQUE KEY session_id (session_id),
		  PRIMARY KEY  (session_key)
		)" );

		self::create_product_meta_table($collate);
		self::create_order_meta_table($collate);
		self::create_attributes_table($collate);
		self::create_shipping_zones_table($collate);
		self::create_shipping_zone_countries_table($collate);
		self::create_cart_table($collate);
		self::create_order_products_table($collate);
		self::create_download_permissions_table($collate);
	}

	private static function create_attributes_table( $collate ){
	    global $wpdb;

	    $wpdb->query("CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "shop_ct_attributes` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `name` varchar(32) NOT NULL,
          `slug` varchar(32) NOT NULL,
          `type` enum('select','text') NOT NULL,
          `order_by` enum('name','name_numeric','term_id') NOT NULL DEFAULT 'name',
          `public` int(1) NOT NULL DEFAULT '1',
          PRIMARY KEY (`id`)
        ) $collate");
    }

	private static function create_product_meta_table($collate){
        global $wpdb;

        $wpdb->query("CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."shop_ct_product_meta`(  
          `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `item_id` BIGINT(20) UNSIGNED NOT NULL,
          `key` VARCHAR(100) NOT NULL,
          `value` LONGTEXT,
          `date` TIMESTAMP,
          `user_id` BIGINT(20) UNSIGNED NOT NULL,
          PRIMARY KEY (`id`)
        ) $collate;");
    }

    private static function create_order_meta_table($collate){
        global $wpdb;

        $wpdb->query("CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."shop_ct_order_meta`(  
          `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
          `item_id` BIGINT(20) UNSIGNED NOT NULL,
          `key` VARCHAR(100) NOT NULL,
          `value` LONGTEXT,
          `date` TIMESTAMP,
          `user_id` BIGINT(20) UNSIGNED NOT NULL,
          PRIMARY KEY  (`id`)
        ) $collate;");
    }

	private static function create_shipping_zones_table($collate) {
		global $wpdb;

		$wpdb->query("CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "shop_ct_shipping_zones` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(64) NOT NULL,
			`status` int(1) unsigned NOT NULL DEFAULT 1,
            `cost` int(11) unsigned NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`)
		) $collate");

		if (count($wpdb->get_results("select id from`" . $wpdb->prefix . "shop_ct_shipping_zones` where `id` is not null")) === 0) {
			$wpdb->insert(
				$wpdb->prefix . 'shop_ct_shipping_zones',
				[
					'id' => 1,
					'name' => 'Rest of the world',
					'status' => 1,
					'cost' => 0,
				]
			);
		}
	}

	private static function create_shipping_zone_countries_table($collate) {
		global $wpdb;

		$wpdb->query("CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "shop_ct_shipping_zone_countries` (
			`zone_id` int(11) unsigned NOT NULL,
            `country_iso_code` varchar(2) NOT NULL,
            PRIMARY KEY (`zone_id`,`country_iso_code`)
        ) $collate");
	}

	private static function create_cart_table($collate) {
		global $wpdb;

		$wpdb->query("CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "shop_ct_cart` (
            `hash` varchar(32) NOT NULL,
            `product_id` bigint(20) unsigned NOT NULL,
            `user_id` bigint(20) unsigned DEFAULT NULL,
            `quantity` int(11) unsigned NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL,
            PRIMARY KEY (`hash`,`product_id`)
            ) $collate");
	}

	private static function create_order_products_table($collate) {
		global $wpdb;

		$wpdb->query("CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "shop_ct_order_products` (
            `order_id` bigint(20) unsigned NOT NULL,
            `product_id` bigint(20) unsigned NOT NULL,
            `quantity` int(11) unsigned NOT NULL,
            `cost` float(11,2) NOT NULL,
            PRIMARY KEY (`order_id`,`product_id`)
        ) $collate");
	}


	private static function create_download_permissions_table($collate) {
		global $wpdb;

		$wpdb->query("CREATE TABLE IF NOT EXISTS `" . Shop_CT_Product::get_download_permissions_table_name() . "` (
            `product_id` BIGINT(20) UNSIGNED NOT NULL,
            `order_id` BIGINT(20) UNSIGNED NOT NULL,
            `email` VARCHAR(60) NOT NULL,
            `user_id` BIGINT(20) UNSIGNED,
            `token` TEXT,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `expires_at` TIMESTAMP NULL DEFAULT NULL,
            `limit` int(100),
            PRIMARY KEY (`order_id`,`product_id`,`email`)
        ) $collate");
	}

	private static function schedule_cron_jobs() {
		if (! wp_next_scheduled ( 'my_hourly_event' )) {
			wp_schedule_event(time(), MONTH_IN_SECONDS, 'shop_ct_remove_old_carts');
		}
	}

}

