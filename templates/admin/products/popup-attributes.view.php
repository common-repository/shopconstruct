<?php
/**
 * @var $product Shop_CT_Product
 */
?>
<div class="shop-ct-grid-item mat-card">
    <span class="mat-card-title"><?php _e('Attributes', 'shop_ct'); ?></span>
    <div class="shop-ct-field">
        <select id="product-new-attribute-taxonomy">
            <option value="custom"><?php _e('New Attribute', 'shop_ct'); ?></option>
            <?php foreach (Shop_CT_Product_Attribute::get_all() as $attr): ?>
                <option value="<?= $attr->get_id(); ?>"><?= $attr->get_name(); ?></option>
            <?php endforeach; ?>
        </select>
        <button class="product-add-attribute mat-button"><?php _e('Add Attribute', 'shop_ct'); ?></button>
    </div>
    <div class="shop-ct-field">
        <div class="product-attributes-list">
            <?php
            $attributes = $product->get_attributes();
            if (!empty($attributes)):
                foreach ($product->get_attributes() as $attr_slug => $attr_terms):
                    if (empty($attr_terms)) {
                        continue;
                    }
                    $attribute = new Shop_CT_Product_Attribute(null,array('slug' => $attr_slug));
                    \ShopCT\Core\TemplateLoader::get_template('admin/products/popup-attribute-row.view.php', compact('product', 'attribute'));
                endforeach;
            endif;
            ?>
        </div>
    </div>
</div>
