<?php
/**
 * @var $cart Shop_CT_Cart
 */
?>
<!-- Cart header -->
<div class="shop-ct-cart-head">
    <span class="shop-ct-cart-title"><?php _e('Shopping bag'); ?></span>
    <span class="shop-ct-cart-count-n"><?php

            printf(
                _n(
                    '%s item',
                    '%s items',
                    $cart->get_count(),
                    'shop_ct'
                ),
                number_format_i18n($cart->get_count())
            );


        ?></span>
</div>

<!-- Cart Items -->
<div class="shop-ct-cart-items-wrap">

        <?php
        $products = $cart->get_products();
        if(empty($products)):
            \ShopCT\Core\TemplateLoader::get_template('frontend/cart/no-items.view.php');
            else:
            \ShopCT\Core\TemplateLoader::get_template('frontend/cart/cart-items.view.php',compact('cart', 'products'));
        endif;
        ?>

</div>

<!-- Cart footer -->
<div class="shop-ct-cart-footer">
    <!-- Cart Totals -->
    <?php if(!empty($products)): ?>
    <div class="shop-ct-cart-totals">
        <span class="shop-ct-cart-total">Total: <span class="shop-ct-cart-total-value"><?php echo Shop_CT_Formatting::format_price($cart->get_total()); ?></span></span>
    </div>
    <?php endif; ?>
    <div class="shop-ct-cart-footer-btns">
        <span class="shop-ct-cart-close"><?php _e('Continue Shopping', 'shop_ct'); ?></span>
        <?php
        $checkout_id = SHOP_CT()->checkout->settings->checkout_page_id;

        if(!empty($checkout_id)){
            $checkout_url = get_the_permalink((int)$checkout_id);
            $products  =$cart->get_products();
            echo '<a class="shop-ct-button shop-ct-cart-checkout '.(empty($products) ? 'shop-ct-disabled-link' : '').'" href="'.$checkout_url.'">'.__('Checkout','shop_ct').'</a>';
        }

        ?>
    </div>
</div>

