<?php

class Shop_CT_List_Table_Shipping_Zones extends Shop_CT_list_table {
    /**
     * @param Shop_CT_Shipping_Zone $zone
     */
	public function column_title($zone) {
		echo '<a class="row-title edit" href="#">' . (1 === $zone->get_id() ? __($zone->get_name(), 'shop_ct') : $zone->get_name()) . '</a>';
	}

    /**
     * @param Shop_CT_Shipping_Zone $zone
     */
	public function column_status($zone) {
		$statuses = Shop_CT_Shipping_Zone::get_statuses();

		echo $statuses[$zone->get_status()];
	}

    /**
     * @param Shop_CT_Shipping_Zone $zone
     */
	public function column_cost($zone) {
	    $formatted_price = Shop_CT_Formatting::format_price($zone->get_cost() );

		echo $formatted_price;

	}

    /**
     * @param Shop_CT_Shipping_Zone $zone
     */
	public function column_countries($zone) {
		echo 1 !== $zone->get_id() ? implode(', ', $zone->get_countries()) : '&#8212;';
	}

	public function get_items() {
		return Shop_CT_Shipping_Zone::get_all();
	}

    /**
     * @param Shop_CT_Shipping_Zone $zone
     */
	public function column_cb($zone) {
		echo '<input class="shop-ct-col-checkbox" type="checkbox" id="cb-select-' . $zone->get_id() . '" name="comment[]" value="' . $zone->get_id() . '" /><div class="locked-indicator"></div>';
	}

	protected function handle_row_actions($zone, $column_name, $primary){
		if (is_array($this->row_actions) && !empty($this->row_actions)) {
			if (1 === $zone->get_id()) {
				unset($this->row_actions['delete']);
			}
			$actions = array();
			foreach ($this->row_actions as $action => $name) {
				$actions[$action] = "<a class='" . $action . "' title='" . $name . "' href='#' >" . $name . "</a>";
			}

			return $this->row_actions($actions);
		}
	}
}