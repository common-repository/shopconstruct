<?php
/**
 * @var $cart Shop_CT_Cart
 * @var $countries array
 */
?>
<div id="shop_ct_checkout_main">
        <div class="shop_ct_checkout_information_section">
            <form method="post" action="" id="shop_ct_checkout_form">
                <div class="shop_ct_checkout_header">
                    <p class="shop_ct_checkout_title"><?php _e('secure checkout','shop_ct'); ?></p>
                </div>
                <?php
                wp_nonce_field('shop_ct_checkout'); ?>
                <input type="hidden" name="action" value="shop_ct_checkout" />
                <?php
                \ShopCT\Core\TemplateLoader::get_template('frontend/checkout/billing-details.view.php',compact('cart','countries'));
                if($cart->requires_delivery()):
                    \ShopCT\Core\TemplateLoader::get_template('frontend/checkout/shipping-details.view.php',compact('cart','countries'));
                endif;
                $current_location = \ShopCT\Core\Geolocation::geolocate_ip()['country'];
                $zone = Shop_CT_Shipping_Zone::get_zone_by_location($current_location);
                $shipping_cost = false !== $zone ? $zone->get_cost() : 0;
                \ShopCT\Core\TemplateLoader::get_template('frontend/checkout/order-items.view.php',compact('cart', 'zone', 'shipping_cost'));
                \ShopCT\Core\TemplateLoader::get_template('frontend/checkout/payment-methods.view.php',compact('cart', 'zone', 'shipping_cost'));
                ?>
            </form>
        </div>
</div>
