<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Shop_CT_Meta {
	/**
	 * @var string
	 */
	protected $table_name;

	/**
	 * @var array
	 */
	protected $cache = array();

	/**
	 * @var array
	 */
	protected $multiples = array();

	/**
	 * @var static
	 */
	protected static $instance;

	/**
	 * Shop_CT_Meta constructor.
	 *
	 * @param string $tablename
	 */
	public function __construct($tablename) {
		$this->table_name = $tablename;
	}

	public static function get_instance($table_name) {
		$class_name = get_called_class();

		if (!static::$instance instanceof $class_name) {
			static::$instance = new static($table_name);
		}

		return static::$instance;
	}

	/**
	 * Returns meta value from cache if exists, otherwise from database.
	 *
	 * @param int $item_id
	 * @param string $key
	 * @param bool $multiple
	 *
	 * @return mixed|null|string
	 */
	public function get($item_id, $key, $multiple = false) {
		$item_id = absint($item_id);
		$key = sanitize_key($key);
		$multiple = (bool)$multiple;

		$cache_value = $this->get_from_cache($item_id, $key, $multiple);

		if (NULL !== $cache_value) {
			return $cache_value;
		}

		if ($multiple) {
			$this->multiples[$item_id][] = $key;
		}

		$this->cache[$item_id][$key] = $this->get_from_db($item_id, $key, $multiple);

		return $this->cache[$item_id][$key];
	}

	/**
	 * Returns meta value from cache if exists.
	 *
	 * @param int $item_id
	 * @param string $key
	 * @param $multiple
	 *
	 * @return null
	 */
	private function get_from_cache($item_id, $key, $multiple) {
		if ($multiple) {
			if (isset($this->multiples[$item_id]) && in_array($key, $this->multiples[$item_id])) {
				return isset($this->cache[$item_id][$key]) ? $this->cache[$item_id][$key] : NULL;
			}

			return NULL;
		} else {
			if (isset($this->multiples[$item_id]) && in_array($key, $this->multiples[$item_id])) {
				return isset($this->cache[$item_id][$key][0]) ? $this->cache[$item_id][$key][0] : NULL;
			} elseif (isset($this->cache[$item_id][$key])) {
				return $this->cache[$item_id][$key];
			}

			return NULL;
		}
	}

	/**
	 * @param int $item_id
	 * @param string $key
	 * @param bool $multiple
	 *
	 * @return array|null|object|string
	 */
	private function get_from_db($item_id, $key, $multiple) {
		global $wpdb;

		if ($multiple) {
			$value = $wpdb->get_results('SELECT `value` FROM ' . $this->table_name . ' WHERE `item_id` = ' . $item_id . ' AND `key` = "' . $key . '" ORDER BY `date` DESC;');

			foreach ($value as &$item) {
				$unserialized_value = @unserialize($item);

				if (false !== $unserialized_value || 'b:0;' === $item) {
					$item = $unserialized_value;
				}
			}

			unset($item);
		} else {
			$value = $wpdb->get_var('SELECT `value` FROM ' . $this->table_name . ' WHERE `item_id` = ' . $item_id . ' AND `key` = "' . $key . '" ORDER BY `date` DESC LIMIT 1;');

			$unserialized_value = @unserialize($value);

			if (false !== $unserialized_value || 'b:0;' === $value) {
				$value = $unserialized_value;
			}
		}

		return $value;
	}

	public function set($item_id, $key, $value) {
		$key = sanitize_text_field($key);
		// todo: sanitize value

		if (is_array($value) || is_object($value) || is_bool($value)) {
			$serialized_value = serialize($value);
		}

		global $wpdb;

		$result = $wpdb->insert($this->table_name, ['item_id' => $item_id, 'key' => $key, 'value' => isset($serialized_value) ? $serialized_value : $value]);

		if (false !== $result) {
			if (isset($this->multiples[$item_id]) && in_array($key, $this->multiples[$item_id])) {
				$this->set_to_cache($item_id, $key, $this->get_from_db($item_id, $key, true));
			} else {
				$this->set_to_cache($item_id, $key, $value);
			}

			return true;
		}

		return false;
	}

	/**
	 * Adds meta to cache.
	 *
	 * @param int $item_id
	 * @param string $key
	 * @param mixed $value
	 */
	private function set_to_cache($item_id, $key, $value) {
		$this->cache[$item_id][$key] = $value;
	}

	/**
	 * Remove value from cache.
	 *
	 * @param int $item_id
	 * @param string $key
	 */
	private function delete_from_cache($item_id, $key) {
		if (isset($this->cache[$item_id], $this->cache[$item_id][$key])) {
			unset($this->cache[$item_id][$key]);
		}
	}

	/**
	 * Delete meta from database and cache.
	 *
	 * @param int $item_id
	 * @param string $key
	 *
	 * @return bool
	 */
	public function delete($item_id, $key) {
		global $wpdb;

		$item_id = absint($item_id);
		$key = sanitize_key($key);

		$result = $wpdb->delete($this->table_name, ['item_id' => $item_id, 'key' => $key]);

		if ($result === false) {
			return false;
		}

		$this->delete_from_cache($item_id, $key);

		if (isset($this->multiples[$item_id]) && in_array($key, $this->multiples[$item_id])) {
			unset($this->multiples[$item_id][array_search($key, $this->multiples[$item_id])]);
		}

		return true;
	}

	public function set_if_changed($value, $id, $key) {
		return $value === $this->get($id, $key) ? false : $this->set($id, $key, $value);
	}
}