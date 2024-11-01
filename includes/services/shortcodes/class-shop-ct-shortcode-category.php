<?php


class Shop_CT_Shortcode_Category
{
    public static function init($atts = array())
    {
        if (isset($atts['id'])) {
            $id = absint($atts['id']);

            $category = new Shop_CT_Product_Category($id);

            $category_children = $category->get_children();

            /**
             * todo: pagination problems
             */
            $products = Shop_CT_Product::get(array(
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => Shop_CT_Product_Category::get_taxonomy(),
                        'terms' => $category->get_id(),
                        'include_children' => false,
                    ),
                ),
            ));

            $products = shop_ct_order_products($products);

            return \ShopCT\Core\TemplateLoader::get_template_buffer('frontend/product-category/show.view.php', compact('category', 'products', 'category_children'));
        }
    }
}
