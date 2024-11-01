<?php

class Shop_CT_Product_Attribute_Term extends Shop_CT_Term {

	/**
	 * @var string
	 */
	private $taxonomy;

	/**
	 * Shop_CT_Product_Attribute_Term constructor.
	 *
	 * @param int $id
	 * @param null $taxonomy
	 */
	public function __construct($id = NULL, $taxonomy = NULL) {
		if (NULL !== $id && is_numeric($id)) {
			$id = absint($id);

			$term = get_term($id, $taxonomy);

			if ($term instanceof WP_Term) {
				$this->id = $id;
				$this->taxonomy = $term->taxonomy;
				$this->set_name($term->name);
				$this->set_slug($term->slug);
				$this->set_description($term->description);
			}
		} elseif (NULL !== $taxonomy && taxonomy_exists($taxonomy)) {
			$this->taxonomy = $taxonomy;
		}
	}

    /**
     * @return bool|Shop_CT_Product_Attribute
     */
	public function get_attribute(){
	    return Shop_CT_Product_Attribute::get_by_taxonomy($this->taxonomy);
    }

	private function can_be_saved() {
		return isset($this->name);
	}

    /**
     * @return bool
     * @throws Exception
     */
	public function save() {
		$args = array(
			'description' => $this->description,
			'slug' => $this->slug,
		);

		if (NULL !== $this->id) {
			$args['name'] = $this->name;
		}

        $result = NULL === $this->id
            ? wp_insert_term($this->name, $this->taxonomy, $args)
            : wp_update_term($this->id, $this->taxonomy, $args);

		if(!$result){
		    throw new Exception('failed to save attribute term');
        }elseif(null===$this->id){
		    $this->id = $result['term_id'];
        }

		return true;
	}
}