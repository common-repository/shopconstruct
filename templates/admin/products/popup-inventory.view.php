<?php
/**
 * @var$product Shop_CT_Product
 */
?>
<div class="shop-ct-grid-item mat-card">
    <span class="mat-card-title"><?php _e('Inventory','shop_ct'); ?></span>
    <?php if(SHOP_CT()->product_settings->manage_stock === 'yes'): ?>
    <div class="shop-ct-field mat-input-checkbox">
        <input type="hidden" name="product_manage_stock" value="0"/>
        <label class="mat-input-checkbox-slider full-width">
            <input type="checkbox" id="product_manage_stock" name="post_meta[manage_stock]"
                   value="1" <?php checked($product->get_manage_stock()); ?> />
            <span></span>
        </label>
        <label for="product_manage_stock"><?php _e('Manage Stock?', 'shop_ct'); ?></label>
    </div>
    <?php endif; ?>
    <div class="show_if_managing_stock" <?php if(!$product->managing_stock()){ echo 'style="display:none"'; } ?> >
        <div class="shop-ct-field mat-input-text full-width">
            <input type="number" name="post_meta[stock]" id="post_meta[stock]"
                   value="<?= $product->get_stock(); ?>"/>
            <label for="post_meta[stock]"><?php _e('Stock Qty', 'shop_ct'); ?></label>
            <span></span>
        </div>
        <div class="shop-ct-field mat-input-select full-width">
            <label for="post_meta[backorders]"><?php _e('Allow backorders?','shop_ct'); ?></label>
            <select name="post_meta[backorders]" id="post_meta[backorders]">
                <option value="no" <?php echo selected($product->get_backorders(),'no') ?>>Do not allow</option>
                <option value="notify" <?php echo selected($product->get_backorders(),'notify') ?>>Allow, but notify customer</option>
                <option value="yes" <?php echo selected($product->get_backorders(),'yes') ?>>Allow</option>
            </select>
        </div>
    </div>
    <div class="shop-ct-field mat-input-select full-width">
        <label for="post_meta[stock_status]"><?php _e('Stock Status','shop_ct'); ?></label>
        <select name="post_meta[stock_status]" id="post_meta[stock_status]">
            <option value="instock" <?php echo selected($product->get_stock_status(),'instock') ?>>In stock</option>
            <option value="outofstock" <?php echo selected($product->get_stock_status(),'outofstock') ?>>Out of stock</option>
        </select>
    </div>
    <div class="shop-ct-field mat-input-checkbox">
        <input type="hidden" name="post_meta[sold_individually]" value="0"/>
        <label class="mat-input-checkbox-slider full-width">
            <input type="checkbox" id="post_meta[sold_individually]" name="post_meta[sold_individually]"
                   value="1" <?php checked($product->is_sold_individually()); ?> />
            <span></span>
        </label>
        <label for="post_meta[sold_individually]"><?php _e('Sold individually', 'shop_ct'); ?></label>
    </div>
</div>
