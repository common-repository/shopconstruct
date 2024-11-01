<?php
/**
 * $product Shop_CT_Product
 */
?>
<div class="shop-ct-grid-item mat-card">
    <span class="mat-card-title"><?php _e('Taxonomies', 'shop_ct'); ?></span>
    <div class="shop-ct-field">
        <span class="shop-ct-field-label"><?php _e('Categories', 'shop_ct'); ?></span>
        <div class="shop-ct-product-cat-checklist">
            <ul class="shop-ct-product-cat-checklist-items">
                <?php
                $terms = get_the_terms($product->get_id(), Shop_CT_Product_Category::get_taxonomy());
                $term_ids = array();
                if (is_array($terms)) {
                    foreach ($terms as $term) {
                        $term_ids[] = $term->term_id;
                    }
                }
                wp_terms_checklist($product->get_id(), array(
                    'taxonomy' => Shop_CT_Product_Category::get_taxonomy(),
                    'descendants_and_self' => false,
                    'popular_cats' => true,
                    'walker' => false,
                    'checked_ontop' => 1,
                    'selected_cats' => $term_ids,
                ));
                ?>
            </ul>
        </div>
        <p>
            <button class="shop-ct-add-cat-btn mat-button">Add New Category</button>
        </p>
    </div>
    <div class="shop-ct-field product-tags-field">
        <div class="shop-ct-flex shop-ct-justify-between shop-ct-align-end shop-ct-flex-wrap">
            <div class="mat-input-text">
                <input type="text" id="product_tags_input" value=""/>
                <label for="product_tags_input"><?php _e('Tags (Separate tags with commas)', 'shop_ct'); ?></label>
                <span></span>
            </div>
            <div>
                <button class="product-tags-add mat-button"><?php _e('Add', 'shop_ct'); ?></button>
            </div>

        </div>

        <div class="product-tags-list">
            <?php
            $tags = wp_get_post_terms($product->get_id(), Shop_CT_Product_Tag::get_taxonomy());
            if (!empty($tags)):
                foreach ($tags as $tag): ?>
                    <span class="product-tag-item">
                            <input type="hidden" name="product_tags[]" value="<?= $tag->name; ?>"/>
                            <span class="product-tag-item-name"><?= $tag->name; ?></span>
                            <span class="product-tag-item-delete"><i class="fa fa-times"></i></span>
                        </span>
                <?php endforeach;
            endif;
            ?>
        </div>
    </div>
</div>
