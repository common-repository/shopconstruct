<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Shop_CT_Product_Tag extends Shop_CT_Term {

	protected static $taxonomy = 'shop_ct_product_tag';

	/**
	 * @var WP_Term
	 */
	public $term;

	public function __construct($id = NULL) {
		if (isset($id) && is_numeric($id)) {
			$term = get_term($id);

			if ($term instanceof WP_Term) {
				$this->term = $term;
				$this->id = $term->term_id;
				$this->name = $term->name;
				$this->slug = $term->slug;
				$this->description = $term->description;
				$this->parent = $term->parent;
			}
		}
	}

	/**
	 * Checkes if all required fields are correctly set.
	 *
	 * @return bool
	 */
	private function can_be_saved() {
		return !empty($this->name) && taxonomy_exists(self::$taxonomy);
	}

	/**
	 * Save Tag.
	 *
	 * @return array|bool|WP_Error
	 */
	public function save() {
		if (!$this->can_be_saved()) {
			return false;
		}

		$args = array(
			'slug' => $this->slug,
			'description' => $this->description,
			'parent' => $this->parent,
		);

		if (NULL !== $this->id) {
			$args['name'] = $this->name;
		}

		$result = is_null($this->id) ? wp_insert_term($this->name, self::$taxonomy, $args) : wp_update_term($this->id, self::$taxonomy, $args);

		return is_wp_error($result) ? false : $result;
	}
}
