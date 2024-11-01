<?php


class Shop_CT_Cart_Actions
{

    public function __construct()
    {
        if(SHOP_CT()->isAdvanced() && $GLOBALS['shop_ct_style_settings']->category_grid_show_cart_button === 'yes') {
            add_action('shop_ct_before_show_product_category',array($this,'view_cart_btn'));
        }

        if(SHOP_CT()->isAdvanced() && $GLOBALS['shop_ct_style_settings']->product_page_show_cart_button === 'yes') {
            add_action('shop_ct_before_show_product',array($this,'view_cart_btn'));
        }

        add_action('shop_ct_before_show_product_tag',array($this,'view_cart_btn'));
        add_action('shop_ct_before_show_catalog',array($this,'view_cart_btn'));

        if(SHOP_CT()->settings->show_cart_button_in_menu === 'yes'){
            add_filter( 'wp_nav_menu_items', array($this, 'add_cart_button_to_menu'), 10, 2 );
        }

    }

    public function view_cart_btn()
    {
        echo do_shortcode('[ShopConstruct_cart_button]');
    }

    public function add_cart_button_to_menu( $items, $args ) {
        $items .= '<li>'.do_shortcode('[ShopConstruct_cart_button is_button="no"]').'</li>';
        return $items;
    }

}