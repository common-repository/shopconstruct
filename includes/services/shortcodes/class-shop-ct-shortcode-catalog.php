<?php

class Shop_CT_Shortcode_Catalog
{
    public static function init($atts = array())
    {
        do_action('shop_ct_catalog_shortcode');

        $products = Shop_CT_Product::get();

        $products = shop_ct_order_products($products);

        $categories = Shop_CT_Product_Category::get(array('parent' => 0));

        return \ShopCT\Core\TemplateLoader::get_template_buffer('frontend/catalog/index.view.php', compact('products', 'categories'));
    }
}
