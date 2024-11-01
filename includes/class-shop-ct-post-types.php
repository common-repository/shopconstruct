<?php
/**
 * Post Types
 *
 * Registers post types and taxonomies.
 *
 * @class     Shop_CT_Post_types
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shop_CT_Post_types Class.
 */
class Shop_CT_Post_types {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'register_taxonomies' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_types' ), 5 );
		add_action( 'init', array( __CLASS__, 'register_post_status' ), 9 );
		add_action( 'init', array( __CLASS__, 'support_jetpack_omnisearch' ) );
		add_filter( 'shop_ct_post_statuses', array( __CLASS__, 'order_statuses_callback' ) );
	}

	/**
	 * Register core taxonomies.
	 */

	public static function register_taxonomies() {
		register_taxonomy( 'shop_ct_product_category',
			apply_filters( 'shop_ct_taxonomy_objects_product_cat', array( 'shop_ct_product' ) ),
			apply_filters( 'shop_ct_taxonomy_args_product_cat', array(
				'hierarchical'          => true,
				//'update_count_callback' => 'shop_ct_term_recount',
				'label'                 => __( 'Product Categories', 'shop_ct' ),
				'labels' => array(
						'name'              => __( 'Product Categories', 'shop_ct' ),
						'singular_name'     => __( 'Product Category', 'shop_ct' ),
						'menu_name'         => _x( 'Categories', 'Admin menu name', 'shop_ct' ),
						'search_items'      => __( 'Search Product Categories', 'shop_ct' ),
						'all_items'         => __( 'All Product Categories', 'shop_ct' ),
						'parent_item'       => __( 'Parent Product Category', 'shop_ct' ),
						'parent_item_colon' => __( 'Parent Product Category:', 'shop_ct' ),
						'edit_item'         => __( 'Edit Product Category', 'shop_ct' ),
						'update_item'       => __( 'Update Product Category', 'shop_ct' ),
						'add_new_item'      => __( 'Add New Product Category', 'shop_ct' ),
						'new_item_name'     => __( 'New Product Category Name', 'shop_ct' )
					),
				'show_ui'               => true,
				'query_var'             => true,
				'rewrite'               => array(
					'slug'         => _x( 'product-category', 'slug', 'shop_ct' ),
					'with_front'   => true,
					'hierarchical' => true,
				),
			) )
		);

		register_taxonomy( 'shop_ct_product_tag',
			apply_filters( 'shop_ct_taxonomy_objects_product_tag', array( 'shop_ct_product' ) ),
			apply_filters( 'shop_ct_taxonomy_args_product_tag', array(
				'hierarchical'          => false,
				//'update_count_callback' => 'shop_ct_term_recount',
				'label'                 => __( 'Product Tags', 'shop_ct' ),
				'labels'                => array(
						'name'                       => __( 'Product Tags', 'shop_ct' ),
						'singular_name'              => __( 'Product Tag', 'shop_ct' ),
						'menu_name'                  => _x( 'Tags', 'Admin menu name', 'shop_ct' ),
						'search_items'               => __( 'Search Product Tags', 'shop_ct' ),
						'all_items'                  => __( 'All Product Tags', 'shop_ct' ),
						'edit_item'                  => __( 'Edit Product Tag', 'shop_ct' ),
						'update_item'                => __( 'Update Product Tag', 'shop_ct' ),
						'add_new_item'               => __( 'Add New Product Tag', 'shop_ct' ),
						'new_item_name'              => __( 'New Product Tag Name', 'shop_ct' ),
						'popular_items'              => __( 'Popular Product Tags', 'shop_ct' ),
						'separate_items_with_commas' => __( 'Separate Product Tags with commas', 'shop_ct'  ),
						'add_or_remove_items'        => __( 'Add or remove Product Tags', 'shop_ct' ),
						'choose_from_most_used'      => __( 'Choose from the most used Product tags', 'shop_ct' ),
						'not_found'                  => __( 'No Product Tags found', 'shop_ct' ),
					),
				'show_ui'               => true,
				'query_var'             => true,
				'rewrite'               => array(
					'slug'       => _x( 'product-tag', 'slug', 'shop_ct' ),
					'with_front' => false
				), 
			) )
		);

		Shop_CT_Product_Attribute::register();

		do_action( 'shop_ct_after_register_taxonomy' );
	}

	/**
	 * Register core post types.
	 */
	public static function register_post_types() {

		$product_permalink = _x( 'product', 'slug', 'shop_ct' );

		$registered = false;

		if ( ! post_type_exists( 'shop_ct_product' ) ) {

			$params = array(
				'labels'              => array(
					'name'                  => __( 'Products', 'shop_ct' ),
					'singular_name'         => __( 'Product', 'shop_ct' ),
					'menu_name'             => _x( 'Products', 'Admin menu name', 'shop_ct' ),
					'add_new'               => __( 'Add Product', 'shop_ct' ),
					'add_new_item'          => __( 'Add New Product', 'shop_ct' ),
					'edit'                  => __( 'Edit', 'shop_ct' ),
					'edit_item'             => __( 'Edit Product', 'shop_ct' ),
					'new_item'              => __( 'New Product', 'shop_ct' ),
					'view'                  => __( 'View Product', 'shop_ct' ),
					'view_item'             => __( 'View Product', 'shop_ct' ),
					'search_items'          => __( 'Search Products', 'shop_ct' ),
					'not_found'             => __( 'No Products found', 'shop_ct' ),
					'not_found_in_trash'    => __( 'No Products found in trash', 'shop_ct' ),
					'parent'                => __( 'Parent Product', 'shop_ct' ),
					'featured_image'        => __( 'Product Image', 'shop_ct' ),
					'set_featured_image'    => __( 'Set product image', 'shop_ct' ),
					'remove_featured_image' => __( 'Remove product image', 'shop_ct' ),
					'use_featured_image'    => __( 'Use as product image', 'shop_ct' ),
				),
				'description'         => __( 'This is where you can add new products to your store.', 'shop_ct' ),
				'public'              => true,
				'show_ui'             => false,
				'capability_type'     => 'shop_ct_product',
				'map_meta_cap'        => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'hierarchical'        => false,
				'rewrite'             => array(
					'slug'       => untrailingslashit( $product_permalink ),
					'with_front' => false,
					'feeds'      => true,
				),
				'query_var'           => true,
				'supports'            => array(
					'title',
					'editor',
					'excerpt',
					'thumbnail',
					'comments',
					'custom-fields',
					'page-attributes',
					'publicize',
					'wpcom-markdown',
				),
				'has_archive'         => 'shop',
				'show_in_nav_menus'   => false,
			);

			register_post_type( 'shop_ct_product', apply_filters( 'shop_ct_register_post_type_product', $params ) );
			$registered = true;
		}

		if ( ! post_type_exists( 'shop_ct_order' ) ) {

			$params = array(
				'labels'              => array(
					'name'                  => __( 'Orders', 'shop_ct' ),
					'singular_name'         => __( 'Order', 'shop_ct' ),
					'menu_name'             => _x( 'Orders', 'Admin menu name', 'shop_ct' ),
					'add_new'               => __( 'Add Order', 'shop_ct' ),
					'add_new_item'          => __( 'Add New Order', 'shop_ct' ),
					'edit'                  => __( 'Edit', 'shop_ct' ),
					'edit_item'             => __( 'Edit Order', 'shop_ct' ),
					'new_item'              => __( 'New Order', 'shop_ct' ),
					'view'                  => __( 'View Order', 'shop_ct' ),
					'view_item'             => __( 'View Order', 'shop_ct' ),
					'search_items'          => __( 'Search Orders', 'shop_ct' ),
					'not_found'             => __( 'No Orders found', 'shop_ct' ),
					'not_found_in_trash'    => __( 'No Orders found in trash', 'shop_ct' ),
					'parent'                => __( 'Parent Order', 'shop_ct' ),
					'featured_image'        => __( 'Order Image', 'shop_ct' ),
					'set_featured_image'    => __( 'Set order image', 'shop_ct' ),
					'remove_featured_image' => __( 'Remove order image', 'shop_ct' ),
					'use_featured_image'    => __( 'Use as order image', 'shop_ct' ),
				),
				'description'         => __( 'This is where you can add new order.', 'shop_ct' ),
				'public'              => true,
				'show_ui'             => false,
				'capability_type'     => 'shop_ct_order',
				'map_meta_cap'        => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'hierarchical'        => false,
				'query_var'           => true,
				'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', 'page-attributes', 'publicize', 'wpcom-markdown' ),
				'show_in_nav_menus'   => true
			);

            register_post_type( 'shop_ct_order',	apply_filters( 'shop_ct_register_post_type_order', $params ) );
            $registered = true;
		};

		if($registered){
            flush_rewrite_rules();
        }
	}

	/**
	 * Register our custom post statuses, used for order status.
	 */
	public static function register_post_status() {
		/* Orders */
		$ord_statuses = array(
			'shop-ct-pending' => __('Pending Payment', 'shop_ct'),
			'shop-ct-processing' => __('Processing' , 'shop_ct'),
			'shop-ct-on-hold' => __('On Hold' , 'shop_ct'),
			'shop-ct-completed' => __('Completed' , 'shop_ct'),
			'shop-ct-cancelled' => __('Cancelled' , 'shop_ct'),
			'shop-ct-refunded' => __('Refunded' , 'shop_ct'),
			'shop-ct-failed' => __('Failed' , 'shop_ct'),
		);

		foreach($ord_statuses as $key => $status) {

			register_post_status( $key, array(
					'label'                     => __( $status, 'shop_ct' ),
					'public'                    => true,
					'exclude_from_search'       => false,
					'show_in_admin_all_list'    => true,
					'show_in_admin_status_list' => true,
					'label_count'               => _n_noop( $status . ' <span class="count">(%s)</span>', $status . ' <span class="count">(%s)</span>', 'shop_ct' )
				)
			);

		}
		$statuses = array(
			'shop_ct_enabled' => array(
				'label' => 'Enabled',
			),
			'shop_ct_disabled' => array(
				'label' => 'Disabled',
			),
		);

		foreach( $statuses as $status => $arg ) {
			register_post_status( $status, $arg );
		}
	}

	public static function order_statuses_callback($default){
		$default['shop_ct_order'] = array(
			'shop-ct-pending' => 		__('Pending Payment', 'exwp'),
			'shop-ct-processing' => 	__('Processing' , 'shop_ct'),
			'shop-ct-on-hold' => 		__('On Hold' , 'shop_ct'),
			'shop-ct-completed' =>		__('Completed' , 'shop_ct'),
			'shop-ct-cancelled' => 	__('Cancelled' , 'shop_ct'),
			'shop-ct-refunded' => 		__('Refunded' , 'shop_ct'),
			'shop-ct-failed' => 		__('Failed' , 'shop_ct'),
		);

		$default['shop_ct_product'] = array();

		$default_statuses = get_post_statuses();

		foreach ($default_statuses as $key => $value) {

			$new_value = __($value, 'shop_ct');

			$default['shop_ct_product'][$key] = $new_value;
		}

		return $default;
	}

	/**
	 * Add Product Support to Jetpack Omnisearch.
	 */
	public static function support_jetpack_omnisearch() {
		if ( class_exists( 'Jetpack_Omnisearch_Posts' ) ) {
			new Jetpack_Omnisearch_Posts( 'product' );
		}
	}

}