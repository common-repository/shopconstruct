<?php
/**
 * @var $product Shop_CT_Product
 * @var $shipping_zone Shop_CT_Shipping_Zone|bool
 */
?>
<div>
    <?php
    if ($product->get_product_type() !== 'external') :
        if($product->can_be_purchased($shipping_zone)): ?>
            <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <div class="shop_ct_product_quantity">
                    <label for="input-number-mod"><span><?php _e("QTY","shop_ct"); ?></span></label>

                    <div id="product_order_qty_container">
                        <input type="number" id="product_order_qty" name="shop-ct-add-to-cart-count" title="qty" class="mod" value="1" min="1" <?php if($product->get_stock() > '0'){ echo "max='".$product->get_stock()."'"; } ?> <?php echo $product->get_sold_individually() || ($product->managing_stock() && ($product->get_stock() <=0 && !$product->backorders_allowed())) ? "readonly" : ""; ?> />
                        <?php if($product->get_stock() > '0') : ?>
                            <span id="product_order_qty_span">
                                <span> / </span>
                                <span id="product_stock_amount"><?php echo $product->get_stock(); ?></span>
                                <span> (Available)</span>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="shop_ct_product_meta_buttons">
                    <button class="shop-ct-add-to-cart" data-product-id="<?php echo $product->get_id(); ?>"><?php _e("ADD TO CART","shop_ct"); ?></button>
                </div>
            </form>
        <?php elseif(!$product->is_virtual() && ! $shipping_zone instanceof Shop_CT_Shipping_Zone): ?>
            <div class="shop-ct-danger-notice-text"><?php _e('Shipping is not available in your country', 'shop_ct'); ?></div>
        <?php else: ?>
            <div class="shop-ct-danger-notice-text"><?php _e('SOLD OUT', 'shop_ct'); ?></div>
        <?php endif;
    else :
        ?>
        <div class="shop_ct_product_meta_buttons">
            <a href="<?php echo $product->get_product_url(); ?>" target="_blank">
                <button><?php _e($product->get_product_button_text(), "shop_ct"); ?></button>
            </a>
        </div>
        <?php
    endif;
    ?>
</div>