<?php

class Shop_CT_Shortcode_Cart_Button
{

    public static function init($args)
    {
        if(isset($args['is_button'])){
            $isButton = $args['is_button'];
        } else {
            $isButton = 'yes';
        }
        $cart = SHOP_CT()->cart_manager->get_cart();
        return \ShopCT\Core\TemplateLoader::get_template_buffer('frontend/cart/cart-button.view.php',compact('cart', 'isButton'));
    }

}
