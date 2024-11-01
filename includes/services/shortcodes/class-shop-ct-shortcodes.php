<?php

/**
 * Class Shop_CT_Shortcodes
 */
class Shop_CT_Shortcodes
{
    /**
     * @throws Exception
     */
    public static function init()
    {
        $shortcodes = apply_filters('shop_ct_shortcodes',array(
            'ShopConstruct_cart_button' => array( 'Shop_CT_Shortcode_Cart_Button', 'init' ),
            'ShopConstruct_product'=> array('Shop_CT_Shortcode_Product','init'),
            'ShopConstruct_catalog' => array('Shop_CT_Shortcode_Catalog', 'init'),
            'ShopConstruct_category' => array('Shop_CT_Shortcode_Category','init'),
            'ShopConstruct_sorting' => array('Shop_CT_Shortcode_Sorting','init'),
            'ShopConstruct_filtering' => array('Shop_CT_Shortcode_Filtering','init'),
        ));

        foreach ($shortcodes as $tag=>$callback){
            if((is_array($callback) && method_exists($callback[0],$callback[1])) || function_exists($callback)){
                add_shortcode($tag,$callback);
            }else{
                throw new Exception('A callback for shortcode must be valid function: the callback for '.$tag.' does not exist');
            }
        }
    }

}

class Shop_CT_Shortcode_Sorting
{
    public static function init($atts = array())
    {

        return \ShopCT\Core\TemplateLoader::get_template_buffer('frontend/sorting.view.php');
    }
}

class Shop_CT_Shortcode_Filtering
{
    public static function init($atts = array())
    {
        $args = array();
        if(isset($atts['current_category_id'])) {

            $args['currentCategoryId'] = absint($atts['current_category_id']);
        }
        return \ShopCT\Core\TemplateLoader::get_template_buffer('frontend/filtering.view.php', $args);
    }
}
