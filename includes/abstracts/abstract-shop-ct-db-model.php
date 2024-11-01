<?php

abstract class Shop_CT_DB_Model
{

    /**
     * @var string
     */
    protected static $table_name;

    /**
     * count all items in database
     * @var int
     */
    protected static $all_items_count;

    /**
     * @return string
     */
    public static function get_table_name(){
        return static::$table_name;
    }

    /**
     * Get all items
     *
     * @return static[]
     */
    public static function all()
    {
        return static::get();
    }


    /**
     * @param array $args
     * @return static[]
     */
    public static function get($args = array())
    {
        global $wpdb;
        $args = wp_parse_args($args, [
            'orderby' => 'id',
            'order' => 'ASC',
            'per_page' => false,
            'paged' => 1,
            'search' => false,
            'search_target' => 'name'
        ]);
        $table_name = static::get_table_name();
        /** Count all items */
        if (null !== static::$all_items_count) {
            $count = static::$all_items_count;
        } else {
            static::$table_name = $count = $wpdb->get_var("SELECT COUNT(*) FROM " . static::get_table_name());
        }
        if ($count == 0) return array();
        $paginate = '';
        $search_query = '';

        /* Pagination */
        if (false !== $args['per_page']) {
            /* number of items to query */
            $num = $args['per_page'] == '' ? $count : $args['per_page'];
            /* current page */
            $page = $args['paged'];
            /* Total Pages */
            $total = intval(($count - 1) / $num) + 1;
            /* Check if paged correctly */
            $page = intval($page);
            if (empty($page) or $page <= 0) $page = 1;
            if ($page > $total) $page = $total;
            /* Offset for mysql */
            $start = $page * $num - $num;
            $paginate = " LIMIT $start, $num";
        }

        /* Search */
        if ($args['search'] != "") {
            // First, escape the search string for use in a LIKE statement.
            $search = $wpdb->esc_like($args['search']);
            // Add wildcards, since we are searching within text.
            $search = '%' . $search . '%';
            $search_query = $wpdb->prepare(" WHERE %s LIKE %s", $args['search_target'], $search, $search);
        }

        /* Ordering */
        $ordering = $wpdb->prepare(" ORDER BY %s %s", $args['orderby'], $args['order']);

        /* And the main query to retrieve The Items */
        $items = $wpdb->get_results("SELECT * FROM {$table_name}{$ordering}{$paginate}{$search_query}", ARRAY_A);

        /* Return actual objects */
        $item_objs = array();
        if (null !== $items) {
            foreach ($items as $item) {
                $item_objs[$item['id']] = new static($item['id']);
            }
        }
        return $item_objs;
    }

    /**
     * Check if an item with specified id exists
     *
     * @param $id
     * @return bool
     */
    public static function exists($id)
    {
        global $wpdb;
        $item = $wpdb->get_row($wpdb->prepare('SELECT id FROM ' . static::get_table_name() . " WHERE id=%d", absint($id)));
        return null!==$item;
    }
}