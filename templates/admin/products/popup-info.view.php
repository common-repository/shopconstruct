<?php
/**
 * @var $product Shop_CT_Product
 */
?>
<div class="shop-ct-grid-item mat-card">
    <span class="mat-card-title"><?php _e('Info','shop_ct'); ?></span>
    <div class="shop-ct-field">
            <textarea name="post_data[post_content]"
                      id="product_content"><?=wp_kses_post($product->get_post_data()->post_content); ?></textarea>
    </div>
    <div class="shop-ct-field mat-input-text full-width">
            <textarea id="post_data[post_excerpt]"
                      name="post_data[post_excerpt]"><?= $product->get_post_data()->post_excerpt; ?></textarea>
        <label for="product_excerpt"><?php _e('Short Description', 'shop_ct'); ?></label>
        <span></span>
    </div>
    <div class="shop-ct-field shop-ct-product-gallery">
        <span class="shop-ct-field-label"><?php _e('Image Gallery', 'shop_ct'); ?></span>
        <ul class="product-image-gallery">
            <li class="product-image-gallery-add">
                <div>
                    <i class="fa fa-plus"></i>
                    <span><?php _e('Add Image(s)', 'shop_ct'); ?></span>
                </div>
            </li>
            <?php foreach ($product->get_product_image_gallery() as $gallery_item): ?>
                <li>
                    <div class="product-image-gallery-inner">
                        <img src="<?= wp_get_attachment_image_src($gallery_item)[0]; ?>"/>
                        <button class="product-image-gallery-delete"><i class="fa fa-times"></i></button>
                        <input type="hidden" name="post_meta[product_image_gallery][]" id="post_meta[product_image_gallery][]"
                               value="<?= $gallery_item; ?>"/>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
