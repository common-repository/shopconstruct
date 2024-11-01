<?php


namespace ShopCT\Model;


class PostType
{
    protected static $post_type;

    protected static $all_items_count;

    public static $last_query;

    /**
     * @return string
     */
    public static function get_post_type()
    {
        return static::$post_type;
    }

    /**
     * @return static[]
     */
    public static function all()
    {
        return static::get();
    }

    /**
     * @param array $args arguments for \WP_Query
     * @return static[]
     */
    public static function get( $args = array() ){
        $args = wp_parse_args($args, array(
            'post_type' => static::$post_type,
            'posts_per_page' => -1,
            'post_status' => 'any'
        ));

        $query = new \WP_Query( $args );
        $objects = array();
        if($query->have_posts()):
            $object_posts = $query->get_posts();
            foreach( $object_posts as $object_post ){
                $objects[] = new static( $object_post->ID );
            }
        endif;

        static::$last_query = $query;

        return $objects;
    }
}
