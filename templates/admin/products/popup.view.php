<?php
/**
 * @var $product Shop_CT_Product
 * @var $autoDraft bool
 */

$saveText = $autoDraft ? __('Publish', 'shop_ct') : __('Save Changes', 'shop_ct');
?>
<div class="shop-ct-popup-content">
    <form id="product_popup_form" action="#" method="post">
        <div class="shop-ct-grid shop-ct-popup-grid">
            <input type="hidden" id="product_id" name="product_id" value="<?php echo $product->get_id(); ?>"/>
            <input type="hidden" id="product_autodraft" name="product_autodraft" value="<?php echo intval($autoDraft); ?>"/>
            <input type="hidden" name="post_data[post_status]" value="publish"/>
            <input type="hidden" name="action" value="shop_ct_update_product"/>
            <input type="hidden" id="product_name" name="post_data[post_name]" value="<?php echo $product->get_post_data()->post_name; ?>"/>
            <?php \ShopCT\Core\TemplateLoader::get_template('admin/products/popup-general.view.php', compact('product')); ?>
            <?php \ShopCT\Core\TemplateLoader::get_template('admin/products/popup-info.view.php', compact('product')); ?>
            <?php \ShopCT\Core\TemplateLoader::get_template('admin/products/popup-downloadable.view.php', compact('product')); ?>
            <?php \ShopCT\Core\TemplateLoader::get_template('admin/products/popup-taxonomy.view.php', compact('product')); ?>
            <?php \ShopCT\Core\TemplateLoader::get_template('admin/products/popup-attributes.view.php', compact('product')); ?>
            <?php \ShopCT\Core\TemplateLoader::get_template('admin/products/popup-inventory.view.php', compact('product')); ?>
            <?php \ShopCT\Core\TemplateLoader::get_template('admin/products/popup-shipping.view.php', compact('product')); ?>
            <?php \ShopCT\Core\TemplateLoader::get_template('admin/products/popup-seo.view.php', compact('product')); ?>
        </div>
    </form>
</div>
<div class="shop-ct-popup-actions">
    <div>
        <span class="spinner"></span>
        <input type="submit" class="mat-button mat-button--primary product-save" value="<?= $saveText; ?>" />
    </div>
</div>
