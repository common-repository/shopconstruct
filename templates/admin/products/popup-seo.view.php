<?php
/**
 * @var $product Shop_CT_Product
 */
?>
<div class="shop-ct-grid-item mat-card">
    <span class="mat-card-title"><?php _e('SEO', 'shop_ct'); ?></span>
    <div class="shop-ct-field mat-input-text full-width">
        <input name="post_meta[meta_title]" id="post_meta[meta_title]" value="<?= $product->get_meta_title(); ?>"/>
        <label for="post_meta[meta_title]"><?php _e('Meta Title', 'shop_ct'); ?></label>
        <span></span>
    </div>
    <div class="shop-ct-field mat-input-text full-width">
        <input name="post_meta[meta_description]" id="post_meta[meta_description]"
               value="<?= $product->get_meta_description(); ?>"/>
        <label for="post_meta[meta_description]"><?php _e('Meta Description', 'shop_ct'); ?></label>
        <span></span>
    </div>
    <div class="shop-ct-field mat-input-checkbox">
        <input type="hidden" name="post_meta[meta_noindex]" value="0"/>
        <label class="mat-input-checkbox-slider full-width">
            <input type="checkbox" id="post_meta[meta_noindex]" name="post_meta[meta_noindex]"
                   value="1" <?php checked($product->get_meta_noindex()); ?> />
            <span></span>
        </label>
        <label for="post_meta[meta_noindex]"><?php _e('Enable Meta Noindex?', 'shop_ct'); ?></label>
    </div>
</div>