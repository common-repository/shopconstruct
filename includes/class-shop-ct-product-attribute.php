<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Shop_CT_Product_Attribute
{

    use Shop_CT_Setter;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $type='text';

    /**
     * @var string
     */
    private $order_by='term_id';

    /**
     * @var int
     */
    private $public = 1;

    /**
     * @var Shop_CT_Product_Attribute_Term[]|int|WP_Error
     */
    private $terms = array();

    /**
     * Shop_CT_Product_Attribute constructor.
     *
     * @param null $id
     * @param array $args
     */
    public function __construct($id = NULL, $args = array())
    {
        $attribute = null;
        global $wpdb;
        if (null !== $id && is_numeric($id)) {


            $id = absint($id);
            $attribute = $wpdb->get_row('SELECT * FROM ' . SHOP_CT()->get_product_attribute_table_name() . ' WHERE id = ' . $id, ARRAY_A);


        }elseif(!empty($args)){
            $where = '';
            if(isset($args['slug'])){
                $where .= (empty($where) ? ' where ' : ' '). 'slug="'.$args['slug'].'"';
            }

            $attribute = $wpdb->get_row('SELECT * FROM ' . SHOP_CT()->get_product_attribute_table_name() . ' ' . $where, ARRAY_A);
        }


        if (null !== $attribute) {
            $this->id = null!== $id ? $id : $attribute['id'];
            $this->set($attribute);

            $term_ids = get_terms(array('taxonomy' => $this->slug, 'hide_empty' => false, 'fields' => 'ids'));

            foreach ($term_ids as $term_id) {
                $this->terms[] = new Shop_CT_Product_Attribute_Term($term_id);
            }
        }
    }

    /**
     * @return int
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Shop_CT_Product_Attribute
     */
    public function set_name($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function get_slug()
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     *
     * @return Shop_CT_Product_Attribute
     */
    public function set_slug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function get_type()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return Shop_CT_Product_Attribute
     */
    public function set_type($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function get_order_by()
    {
        return $this->order_by;
    }

    /**
     * @param string $order_by
     *
     * @return Shop_CT_Product_Attribute
     */
    public function set_order_by($order_by)
    {
        $this->order_by = $order_by;

        return $this;
    }

    /**
     * @return int
     */
    public function get_public()
    {
        return $this->public;
    }

    /**
     * @param int $public
     *
     * @return Shop_CT_Product_Attribute
     */
    public function set_public($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return int|Shop_CT_Product_Attribute_Term[]|WP_Error
     */
    public function get_terms()
    {
        return $this->terms;
    }

    public function add_term(Shop_CT_Product_Attribute_Term $term)
    {
        $this->terms[] = $term;
    }

    private function can_be_saved()
    {
        return isset($this->name);
    }

    public function save()
    {
        if (!$this->can_be_saved()) {
            return false;
        }

        global $wpdb;

        $data = array(
            'name' => $this->name,
            'slug' => $this->slug,
            'type' => $this->type,
            'order_by' => $this->order_by,
            'public' => $this->public,
        );

        $result = NULL === $this->id
            ? $wpdb->insert(SHOP_CT()->get_product_attribute_table_name(), $data)
            : $wpdb->update(SHOP_CT()->get_product_attribute_table_name(), $data, array('id' => $this->id));

        $term_result = array();
        if (false !== $result) {
            if(null === $this->id){
                $this->id = $wpdb->insert_id;
            }
            foreach ($this->terms as $term) {
                $term_result[] = $term->save();
            }
        }

        return array(
            'attribute' => $result,
            'terms' => $term_result,
        );
    }

    /**
     * @return array|null|object
     */
    private static function get_attributes_without_terms()
    {
        global $wpdb;

        return $wpdb->get_results('SELECT * FROM ' . SHOP_CT()->get_product_attribute_table_name());
    }


    /**
     * @return self[]
     */
    public static function get_all()
    {
        global $wpdb;

        $ids = $wpdb->get_results('SELECT id FROM ' . SHOP_CT()->get_product_attribute_table_name(), ARRAY_A);
        $attributes = array();

        foreach ($ids as $data) {
            $attributes[] = new self($data['id']);
        }

        return $attributes;
    }

    /**
     * @param $taxonomy
     * @return bool|Shop_CT_Product_Attribute
     */
    public static function get_by_taxonomy($taxonomy)
    {
        global $wpdb;
        $id = $wpdb->get_row($wpdb->prepare('SELECT id FROM ' . SHOP_CT()->get_product_attribute_table_name() . ' WHERE slug=%s LIMIT 1', $taxonomy));

        if (null !== $id) {
            return new Shop_CT_Product_Attribute($id);
        }
        return false;
    }

    public static function register()
    {
        $attributes = self::get_attributes_without_terms();
        $result = array();

        foreach ($attributes as $attribute) {
            if (taxonomy_exists($attribute->slug)) {
                continue;
            }

            $args = array(
                'public' => $attribute->public,
                'hierarchical' => false,
                // todo: add update_count_callback function
                'labels' => array(
                    'name' => __('Product Attributes', 'shop_ct'),
                    'singular_name' => __('Product Attribute', 'shop_ct'),
                    'menu_name' => _x('Attributes', 'Admin menu name', 'shop_ct'),
                    'search_items' => __('Search Product Attributes', 'shop_ct'),
                    'all_items' => __('All Product Attributes', 'shop_ct'),
                    'parent_item' => __('Parent Product Attribute', 'shop_ct'),
                    'parent_item_colon' => __('Parent Product Attribute:', 'shop_ct'),
                    'edit_item' => __('Edit Product Attribute', 'shop_ct'),
                    'update_item' => __('Update Product Attribute', 'shop_ct'),
                    'add_new_item' => __('Add New Product Attribute', 'shop_ct'),
                    'new_item_name' => __('New Product Attribute Name', 'shop_ct')
                ),
                'rewrite' => array(
                    'slug' => $attribute->slug,
                    'with_front' => false,
                    'hierarchical' => true,
                )
            );

            $result[] = register_taxonomy(
                $attribute->slug,
                apply_filters('shop_ct_attribute_associated_object_types', array('shop_ct_product')),
                apply_filters('shop_ct_attribute_registration_args', $args)
            );
        }

        return $result;
    }

    public static function delete($id)
    {
        $id = absint($id);
        $taxonomy = $GLOBALS['wpdb']->get_var('SELECT slug FROM ' . SHOP_CT()->get_product_attribute_table_name() . ' WHERE id = ' . $id);

        if (NULL !== $taxonomy) {
            $term_ids = get_terms(array(
                'taxonomy' => $taxonomy,
                'fields' => 'ids',
                'hide_empty' => false,
            ));

            foreach ($term_ids as $term_id) {
                /*$result[$term_id] = */
                wp_delete_term($term_id, $taxonomy);
            }

            $result = $GLOBALS['wpdb']->query('DELETE FROM ' . SHOP_CT()->get_product_attribute_table_name() . ' WHERE id = ' . $id);
        }

        return isset($result) ? $result : false;
    }
}
