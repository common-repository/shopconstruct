<?php

class Shop_CT_Service_Product_Categories extends Shop_CT_Settings {

	/**
	 * Shop_CT_Service_Product_Categories constructor.
	 */
	public function __construct() {
		$this->init();
	}

	protected function init() {

	}

	/**
	 * @return string
	 */
	public function display() {
		$table = new Shop_CT_list_table();

		/* set labels for texts */

		$table->labels = array(
			'page_title'   => 'Categories',
			'add_new_item' => '<span class="fa fa-plus"></span>',
			'search'       => __( 'Search Categories', 'shop_ct' ),
		);

		$table->ids = array(
			'add_new_item' => 'shop_ct_add_cat_btn',
		);

		$table->classes = array();

		$table->form_id = "shop_ct_categories_form";

		$table->table_classes = array( 'shop_ct_cat_table', 'shop_ct_list_table' );

		$table->statuses = array();

		$table->show_statuses = false;

		$table->show_search_box = true;

		$table->bulk_actions = array(
			'bulk_actions' => __( 'Bulk Actions', 'shop_ct' ),
			'delete'       => __( 'Delete', 'shop_ct' ),
		);

		$table->show_bulk_actions = true;

		$table->show_filters = false;

		$table->filters = array();

		$table->columns = array();

		$table->columns['cb'] = array(
			'name'     => '',
			'sortable' => false,
		);
		$table->columns['term_id'] = array(
			'name'     => __( 'ID', 'shop_ct' ),
			'sortable' => true,
		);
		$table->columns['featured_img'] = array(
			'name'     => '<i class="fa fa-picture-o"></i>',
			'sortable' => false,
		);

		$table->columns['name'] = array(
			'name'     => __( 'Name', 'shop_ct' ),
			'sortable' => true,
			'primary'  => true,
		);

		$table->columns['count'] = array(
			'name'     => __( 'Count', 'shop_ct' ),
			'sortable' => true,
		);

		$table->columns['shortcode'] = array(
            'name'     => __( 'Shortcode', 'shop_ct' ),
            'sortable' => false,
		);

		$table->row_actions = array(
			'edit'   => __( 'Edit', 'shop_ct' ),
			'delete' => __( 'Delete', 'shop_ct' ),
			'view'   => __( 'View', 'shop_ct' ),
		);

		$table->items_object_type = 'shop_ct_product_category';

		$table->items_resource_type = 'taxonomy';


		$table->items_object_args = array();

		$table->pagination = true;

		$table->per_page = 10;

		$table->total_pages = 1;

		$table->hierarchial = false;

		ob_start();

		$table->display();

		$return = ob_get_clean();


		return $return;
	}
}