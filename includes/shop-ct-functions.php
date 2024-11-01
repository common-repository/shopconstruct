<?php

/**
 * use only in prod category page
 *
 *
 * @return Shop_CT_Product_Category
 */
function shop_ct_get_current_cat() {
    $queried_object = get_queried_object();
    return new Shop_CT_Product_Category($queried_object->term_id);
}

/**
 * use only in prod category page
 *
 *
 * @return Shop_CT_Product_Category[]
 */
function shop_ct_get_cat_children(){
    $queried_object = get_queried_object();
    $category = new Shop_CT_Product_Category($queried_object->term_id);

    return $category->get_children();
}

/**
 * get products array for category page
 *
 * @param $category Shop_CT_Product_Category
 * @return Shop_CT_Product[]
 */
function shop_ct_get_cat_products($category) {
    global $wp_query;

    $query = array(
        'post_status' => 'publish',
        'posts_per_page' => 10,
        'paged' => isset($_GET['prod_paged']) && absint($_GET['prod_paged']) == $_GET['prod_paged'] ?  absint($_GET['prod_paged']): 1,
        'tax_query' => array(
            array(
                'taxonomy' => Shop_CT_Product_Category::get_taxonomy(),
                'terms' => array($category->get_id()),
                'include_children' => true,
            ),
        ),
    );

    if(isset($_GET['prod_min_rating']) && absint($_GET['prod_min_rating']) == $_GET['prod_min_rating']) {
        $query['meta_query'] = array(
            array(
                'key' => 'shop_ct_rating',
                'value' => $_GET['prod_min_rating'],
                'compare' => '>=',
                'type' => 'NUMERIC'
            )
        );
    }

    if(isset($_GET['prod_min_price']) && !empty($_GET['prod_min_price'])) {
        $minPriceQuery = array(
            'key' => 'shop_ct_final_price',
            'value' => floatval($_GET['prod_min_price']),
            'compare' => '>=',
            'type' => 'NUMERIC'
        );


        if(isset($query['meta_query'])) {
            $query['meta_query'][] = $minPriceQuery;
        } else {
            $query['meta_query'] = array(
                $minPriceQuery
            );
        }
    }

    if(isset($_GET['prod_max_price']) && !empty($_GET['prod_max_price'])) {
        $maxPriceQuery = array(
            'key' => 'shop_ct_final_price',
            'value' => floatval($_GET['prod_max_price']),
            'compare' => '<=',
            'type' => 'NUMERIC'
        );


        if(isset($query['meta_query'])) {
            $query['meta_query'][] = $maxPriceQuery;
        } else {
            $query['meta_query'] = array(
                $maxPriceQuery
            );
        }
    }

    $products = Shop_CT_Product::get($query);


    if(empty($products))
        return array();

    return shop_ct_order_products($products);
}
