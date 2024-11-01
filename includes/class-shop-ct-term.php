<?php

abstract class Shop_CT_Term {
    /**
	 * @var int
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $slug;

	/**
	 * @var string
	 */
	protected $description;

	/**
	 * @var int
	 */
	protected $parent = 0;

    /**
     * @var int
     */
	protected $count = 0;

	/**
	 * @return int
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

    /**
     * @return int
     */
    public function get_count()
    {
        return $this->count;
	}

	/**
	 * @return string
	 */
	public static function get_taxonomy() {
		return static::$taxonomy;
	}

	/**
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * @return int
	 */
	public function get_parent() {
		return $this->parent;
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function set_name( $name ) {
		$this->name = sanitize_text_field($name);

		return $this;
	}

	/**
	 * @param string $slug
	 *
	 * @return $this
	 */
	public function set_slug( $slug ) {
		$this->slug = sanitize_text_field($slug);

		return $this;
	}

	/**
	 * @param string $description
	 *
	 * @return $this
	 */
	public function set_description( $description ) {
		$this->description = esc_html($description);

		return $this;
	}

	/**
	 * @param int $parent
	 *
	 * @return $this
	 */
	public function set_parent( $parent ) {
		$this->parent = $parent;

		return $this;
	}

	public static function get_by_name($name, $taxonomy){
	    $term = get_term_by('name', $name, $taxonomy);

	    if(false !== $term){
	        return new Shop_CT_Product_Attribute_Term($term->term_id);
        }

        return false;

    }

    /**
     * @return static[]|null
     */
    public static function all()
    {
        return static::get();
    }

    /**
     * @param array $args
     * @return static[]|null
     */
    public static function get( $args = array() ){
        $args = wp_parse_args($args, array(
            'taxonomy'=> static::$taxonomy,
            'hide_empty' => true, //don't show empty terms
            'number' => 0, //get all
        ));

        $terms = get_terms($args);

        if(empty($terms)){
            return null;
        }

        $result = array();
        foreach($terms as $term){
            $result[] = new static($term->term_id);
        }

        return $result;

    }
}