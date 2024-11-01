<?php
/**
 * @var $cart Shop_CT_Cart
 * @var $zone Shop_CT_Shipping_Zone | bool
 * @var $shipping_cost float
 */
?>
<div class="shop_ct_checkout_sections order_section">
    <div class="shop_ct_checkout_order_title">
        <h2 class="shop_ct_checkout_sections_title">3. <?php _e('Order Items', 'shop_ct'); ?></h2>
    </div>
    <div class="shop_ct_checkout_order_items_section">
        <table class="shop_ct_checkout_order_cart_items">
            <thead>
            <tr>
                <th><?php _e('Products', 'shop_ct'); ?></th>
                <th><?php _e('Title', 'shop_ct'); ?></th>
                <th><?php _e('Price', 'shop_ct'); ?></th>
                <th><?php _e('Quantity', 'shop_ct'); ?></th>
                <th><?php _e('Subtotal', 'shop_ct'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($cart->get_products() as $item):
                /** @var Shop_CT_Product $product */
                $product = $item['object'];
                ?>
                <tr>
                    <input type="hidden" name="shop_ct_order_product_ids[]" value="<?php echo $product->get_id(); ?>"/>
                    <input type="hidden" name="shop_ct_order_product_qty[]" value="<?php echo $item['quantity']; ?>"/>
                    <td class="shop_ct_checkout_product_img_section">
                        <img src="<?php echo $product->get_image_url('small'); ?>" />
                    </td>
                    <td>
                            <span class="shop_ct_checkout_product_title">
                                <a href="<?php echo $product->get_permalink(); ?>" target="_blank"><?php echo $product->get_title(); ?></a>
                            </span>
                    </td>
                    <td>
                        <span class="shop_ct_checkout_product_price"><?php echo Shop_CT_Formatting::format_price($product->get_price()); ?></span>
                    </td>
                    <td>
                        <span class="shop_ct_checkout_product_quantity"><?php echo $item['quantity']; ?></span>
                    </td>
                    <td>
                        <span class="shop_ct_checkout_product_subtotal"><?php echo Shop_CT_Formatting::format_price($cart->get_total()); ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4"><?php _e('Subtotal', 'shop_ct'); ?>:</th>
                <td><?php echo Shop_CT_Formatting::format_price($cart->get_total()); ?></td>
            </tr>
            <?php if ($cart->requires_delivery()): ?>
                <tr>
                    <th class="shipping_cost_label" colspan="4"><?php printf('%s(%s)',__('Shipping', 'shop_ct'),false !== $zone ? $zone->get_name() : 'unavailable'); ?>:</th>
                    <td class="shipping_cost"><?php
                        if (false !== $zone) { ?>
                            <span><?php echo Shop_CT_Formatting::format_price($shipping_cost) ?></span>
                            <input type="hidden" id="shipping_cost_real" name="shipping_cost" value="<?php echo $shipping_cost ?>" />
                            <?php
                        } else {
                            _e('Not Available', 'shop_ct');
                            }
                        ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <th colspan="4"><?php _e('Total', 'shop_ct'); ?>:</th>
                <td class="checkout_total"><?php echo Shop_CT_Formatting::format_price($cart->get_total() + $shipping_cost); ?></td>
            </tr>
            </tfoot>
        </table>

    </div>
</div>
