<?php
/**
 * @var $cart Shop_CT_Cart
 */
?>
<table class="shop-ct-cart-items">
    <tbody>
    <?php foreach ($cart->get_products() as $item):
        /** @var Shop_CT_Product $product */
        $product = $item['object'];
        ?>
        <tr data-product-id="<?php echo $product->get_id(); ?>">
            <td class="shop-ct-cart-product-img-wrap">
                <div class="shop-ct-cart-product-img"><img src="<?php echo $product->get_image_url('small'); ?>" /></div>
            </td>
            <td>
                <div class="shop-ct-cart-product-meta">
                    <span class="shop-ct-cart-product-title"><a
                                href="<?php echo $product->get_permalink(); ?>"><?php echo $product->get_title(); ?></a></span>
                    <span class="shop-ct-cart-price"><?php echo Shop_CT_Formatting::format_price($product->get_price()); ?></span>
                </div>
            </td>
            <td class="shop-ct-cart-product-qty-wrap">
                <input class="shop-ct-cart-product-qty" type="number" name="shop-ct-cart-product-qty" size="3"
                       value="<?php echo $item['quantity']; ?>"
                       title="<?php _e('quantity', 'shop_ct'); ?>" <?php if ($product->get_stock() > '0') {
                    echo "max='" . $product->get_stock() . "'";
                } ?> <?php echo $product->get_sold_individually() || ($product->managing_stock() && ($product->get_stock() <= 0 && !$product->backorders_allowed())) ? "readonly" : ""; ?> />
            </td>
            <td><span class="shop-ct-cart-delete-product">X</span></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
