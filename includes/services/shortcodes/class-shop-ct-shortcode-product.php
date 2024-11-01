<?php

class Shop_CT_Shortcode_Product
{

    public static function init($atts = array())
    {
        if(!isset($atts['id'])){
            throw new Exception('"id" parameter is required for product shortcode');
        }

        do_action('shop_ct_product_shortcode');

        $product = new Shop_CT_Product($atts['id']);

        return \ShopCT\Core\TemplateLoader::get_template_buffer('frontend/product/show/index.view.php',compact('product'));
    }

}
