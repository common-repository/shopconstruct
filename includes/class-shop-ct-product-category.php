<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Shop_CT_Product_Category extends Shop_CT_Term
{

    protected static $taxonomy = 'shop_ct_product_category';

    private static $thumbnail_id_key = 'thumbnail_id';

    private $thumbnail_id;

    /**
     * @var WP_Term
     */
    public $term;

    public function __construct($id = NULL)
    {
        if (isset($id) && is_numeric($id)) {
            $term = get_term($id, self::$taxonomy);

            if ($term instanceof WP_Term) {
                $this->term = $term;
                $this->id = $term->term_id;
                $this->name = $term->name;
                $this->slug = $term->slug;
                $this->description = $term->description;
                $this->parent = $term->parent;
                $this->count = $term->count;

                $this->thumbnail_id = get_term_meta($this->id, self::$thumbnail_id_key, true);
            }
        }
    }

    /**
     * @return mixed
     */
    public function get_thumbnail_id()
    {
        return $this->thumbnail_id;
    }

    /**
     * @param mixed $thumbnail_id
     *
     * @return Shop_CT_Product_Category
     */
    public function set_thumbnail_id($thumbnail_id)
    {
        $this->thumbnail_id = absint($thumbnail_id);

        return $this;
    }

    public function get_thumbnail_url($size='thumbnail')
    {
        if (empty($this->thumbnail_id)) {
        	return SHOP_CT()->plugin_url().'/assets/images/placeholder.png';
        }

        return wp_get_attachment_image_src($this->thumbnail_id, $size)[0];

    }

    /**
     * @return Shop_CT_Product_Category[]
     */
    public function get_children()
    {
        $children = get_terms(array(
            'taxonomy' => self::$taxonomy,
            'parent' => $this->id,
            'number' => '',

        ));
        if (empty($children)) {
            return null;
        }

        $children_obj = array();
        foreach ($children as $child) {
            $children_obj[] = new Shop_CT_Product_Category($child->term_id);
        }
        return $children_obj;
    }

    /**
     * Checkes if all required fields are correctly set.
     *
     * @return bool
     */
    private function can_be_saved()
    {
        return !empty($this->name) && taxonomy_exists(self::$taxonomy);
    }

    /**
     * @return string|WP_Error
     */
    public function get_permalink()
    {
        return get_term_link($this->id, self::$taxonomy);
    }

    /**
     * Return the shortcode for category
     *
     * @return string category shortcode
     */
    public function get_shortcode()
    {
        return '[ShopConstruct_category id="' . $this->id . '"]';
    }

    /**
     * Save category.
     *
     * @return array|bool|WP_Error
     */
    public function save()
    {
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
        $return_array = array('term' => $result);

        $meta_result = NULL;

        if (!is_wp_error($result)) {
            $this->id = $result['term_id'];
            $return_array['term_meta'] = update_term_meta($this->id, self::$thumbnail_id_key, $this->thumbnail_id);
        } else {
            $return_array['term_meta'] = false;
        }

        return $return_array;
    }
}
