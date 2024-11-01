<?php

class Shop_CT_Shipping_Zone {

	private $id;

	private $name;

	private $cost = 0;

	private $status;

	private $countries;

	private $removed_countries;

	/**
	 * Shop_CT_Shipping_Zone constructor.
	 *
	 * @param $id
	 */
	public function __construct($id = NULL) {
		if (null !== $id && is_numeric($id)) {
			global $wpdb;

			$id = absint($id);
			$zone = $wpdb->get_row('SELECT * FROM ' . self::get_table_name() . ' WHERE id = ' . $id);

			if ($zone) {
				$this->id = $id;
				$this->name = $zone->name;
				$this->cost = (float)$zone->cost;
				$this->status = absint($zone->status);
			}

			$countries = $wpdb->get_results('SELECT country_iso_code FROM ' . self::get_countries_table_name() . ' WHERE zone_id = ' . $id, ARRAY_A);
			$countries = array_column($countries, 'country_iso_code');

			if ($countries) {
				$this->countries = $countries;
			}
			else {
				$this->countries = array();
			}

			$this->removed_countries = array();
		}
	}

	/**
	 * @return mixed
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return mixed
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @param mixed $name
	 *
	 * @return Shop_CT_Shipping_Zone
	 */
	public function set_name($name) {
		$this->name = $name;

		return $this;
	}

	/**
	 * @return int|float
	 */
	public function get_cost() {
		return $this->cost;
	}

	/**
	 * @param int|float $cost
	 *
	 * @return Shop_CT_Shipping_Zone
	 */
	public function set_cost($cost) {
		$this->cost = floatval(abs($cost));

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * @param mixed $status
	 *
	 * @return Shop_CT_Shipping_Zone
	 */
	public function set_status($status) {
		$this->status = $status;

		return $this;
	}

	/**
	 * @return array
	 */
	public function get_countries() {
		return $this->countries;
	}

	public function add_country($country) {
		if (!in_array($country, $this->countries) && 2 === strlen($country)) {
			$this->countries[] = $country;
		}
	}

	public function set_countries($countries) {
		$removed_countries = array_diff($this->countries, $countries);

		foreach ($removed_countries as $removed_country) {
			$this->remove_country($removed_country);
		}

		foreach ($countries as $country) {
			$country = trim($country);

			$this->add_country($country);
		}
	}

	public function remove_country($code) {
		$key = array_search($code, $this->countries);

		if ($key) {
			$this->removed_countries[] = $code;

			unset($this->countries[$key]);
		}
	}

	public function save() {
		global $wpdb;

		$zone_data = [
			'name' => $this->name,
			'cost' => $this->cost,
			'status' => $this->status,
		];

		$deleted = $inserted = array();

		$result = null === $this->id ? $wpdb->insert(self::get_table_name(), $zone_data) : $wpdb->update(self::get_table_name(), $zone_data, ['id' => $this->id]);

		if (false !== $result) {
			if (null === $this->id) {
				$this->id = $wpdb->insert_id;
			}

			foreach ($this->removed_countries as $removed_country) {
				$deleted[$removed_country] = $wpdb->delete(self::get_countries_table_name(), ['zone_id' => $this->id, 'country_iso_code' => $removed_country]);
			}

			foreach ($this->countries as $country) {
				$query = 'INSERT INTO ' . self::get_countries_table_name() . ' (`zone_id`, `country_iso_code`) VALUES (' . $this->id . ', "' . $country . '") ON DUPLICATE KEY UPDATE `zone_id` = ' . $this->id;

				$inserted[$country] = $wpdb->query($query);
			}
		}

		return ['deleted' => $deleted, 'inserted' => $inserted, 'zone' => $result];
	}

	public static function delete($id) {
		if (is_numeric($id) && $id == absint($id) && 1 !== absint($id)) {
			global $wpdb;

			$zone = $wpdb->delete(self::get_table_name(), ['id' => $id]);
			$countries = $wpdb->delete(self::get_countries_table_name(), ['zone_id' => $id]);

			return ['zone' => $zone, 'countries' => $countries];
		}

		return false;
	}

	public static function get_table_name() {
		return $GLOBALS['wpdb']->prefix . 'shop_ct_shipping_zones';
	}

	public static function get_countries_table_name() {
		return $GLOBALS['wpdb']->prefix . 'shop_ct_shipping_zone_countries';
	}

	/**
	 * @return self[]
	 */
	public static function get_all() {
		global $wpdb;

		$zones = array();
		$ids = $wpdb->get_results('SELECT id FROM ' . self::get_table_name() . ' ORDER BY id DESC', ARRAY_A);

		foreach ($ids as $id) {
			$zones[$id['id']] = new self($id['id']);
		}

		return $zones;
	}

	public static function get_statuses() {
		return [
			0 => __('Disabled', 'shop_ct'),
			1 => __('Enabled', 'shop_ct'),
		];
	}

	/**
	 * @param $code
	 *
	 * @return bool|Shop_CT_Shipping_Zone
	 */
	public static function get_zone_by_location($code) {
		if (2 !== strlen($code)) {
            return (self::rest_of_the_world_enabled() ? new Shop_CT_Shipping_Zone(1) : false);
		}

		global $wpdb;

		$zones = self::get_table_name();
		$counties = self::get_countries_table_name();

		$pairs = $wpdb->get_results("SELECT `id`, `country_iso_code`, `name` , `status` FROM $zones INNER JOIN $counties ON $zones.id = $counties.zone_id");

		foreach ($pairs as $pair) {
			if ($pair->country_iso_code === $code && absint($pair->status) === 1 ) {
				return new Shop_CT_Shipping_Zone($pair->id);
			}
		}

		return (self::rest_of_the_world_enabled() ? new Shop_CT_Shipping_Zone(1) : false);
	}

	public static function rest_of_the_world_enabled() {
		return (bool)$GLOBALS['wpdb']->get_var('SELECT status FROM ' . self::get_table_name() . ' WHERE id = 1');
	}
}