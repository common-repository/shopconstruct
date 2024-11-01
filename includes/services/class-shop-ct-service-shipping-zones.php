<?php

class Shop_CT_Service_Shipping_Zones extends Shop_CT_Settings {

	/**
	 * Shop_CT_Service_Shipping_Zones constructor.
	 */
	public function __construct() {}

	public function display() {
		$table = new Shop_CT_List_Table_Shipping_Zones();

		$table->labels = array(
			'page_title'   => __('Shipping Zones', 'shop_ct'),
			'add_new_item' => '<span class="fa fa-plus"></span>',
			'search'       => __( 'Search Shipping Zone', 'shop_ct' )
		);

		$table->ids = array(
			'add_new_item' => 'shop_ct_add_shipping_zone_btn',
		);

		$table->classes = array();

		$table->table_classes = array( 'shop_ct_shipping_zone_table', 'shop_ct_list_table' );

		$table->statuses = array(
			'shop_ct_enabled' => __('Enabled', 'shop_ct'),
			'shop_ct_disabled' => __('Disabled', 'shop_ct'),
		);

		$table->show_statuses = true;

		$table->form_id = 'shop_ct_shipping_zone_form';

		$table->show_search_box = false;

		$table->show_bulk_actions = false;

		$table->show_filters = false;

		$table->filters = array( 'months', 'category' );

		$table->items_object_type = 'shop_ct_shipping_zone';

		$table->items_resource_type = 'custom';

		$table->pagination = true;

		$table->per_page = 15;

		$table->row_actions = [
			'edit' => __('Edit', 'shop_ct'),
			'delete' => __('Delete', 'shop_ct'),
		];

		$table->hierarchial = false;

		unset($table->columns['date']);

		$table->columns['cb'] = array(
			'name'     => '',
			'sortable' => false
		);

		$table->columns['status'] = array(
			'name'     => __( 'Status', 'shop_ct' ),
			'sortable' => false,
		);

		$table->columns['cost'] = array(
			'name' => __('Cost', 'shop_ct'),
			'sortable' => false,
		);

		$table->columns['countries'] = array(
			'name'     => __( 'Countries', 'shop_ct' ),
			'sortable' => true,
		);

		ob_start();

		$table->display();

		$return = ob_get_clean();

		return $return;
	}
}