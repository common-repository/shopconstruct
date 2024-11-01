<?php
/**
 * @var $product Shop_CT_Product
 */
?>
<div class="shop-ct-grid-item mat-card hide_if_virtual">
    <span class="mat-card-title"><?php _e('Shipping','shop_ct'); ?></span>
    <div class="shop-ct-field mat-input-text full-width">
        <input type="number" min="0" name="post_meta[weight]" id="post_meta[weight]" value="<?= $product->get_weight(); ?>"/>
        <label for="post_meta[weight]"><?php printf('%s(%s)', __('Weight', 'shop_ct'), SHOP_CT()->product_settings->weight_unit); ?></label>
        <span></span>
    </div>
    <div class="shop-ct-flex shop-ct-justify-between">
        <div class="shop-ct-flex-2 mat-input-outer-label"><?php printf('%s(%s)', __('Dimensions', 'shop_ct'), SHOP_CT()->product_settings->dimension_unit); ?></div>
        <div class="shop-ct-field shop-ct-flex-1 mat-input-text">
            <input type="number" min="0" name="post_meta[length]" id="post_meta[length]" value="<?= $product->get_length(); ?>"/>
            <label for="post_meta[length]">Length</label>
            <span></span>
        </div>
        <div class="shop-ct-field shop-ct-flex-1 mat-input-text">
            <input type="number" min="0" name="post_meta[width]" id="post_meta[width]" value="<?= $product->get_width(); ?>"/>
            <label for="post_meta[width]">Width</label>
            <span></span>
        </div>
        <div class="shop-ct-field shop-ct-flex-1 mat-input-text">
            <input type="number" min="0" name="post_meta[height]" id="post_meta[height]" value="<?= $product->get_height(); ?>"/>
            <label for="post_meta[height]">Height</label>
            <span></span>
        </div>
    </div>
</div>
