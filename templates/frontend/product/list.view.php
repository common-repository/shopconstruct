<?php
/**
 * @var $products Shop_CT_Product[]
 * @var $paged int
 * @var $totalPages int
 */

?>
<div class="shop_ct_category_products_container shop-ct-product-grid shop-ct-prod-grid-infinte-scroll" data-paged="<?php echo $paged; ?>" data-total="<?php echo $totalPages; ?>">
    <?php \ShopCT\Core\TemplateLoader::get_template('frontend/product/list-items.view.php', compact('products')); ?>
</div><!-- .shop_ct_category_products_container -->
<?php //the_posts_pagination( array(
//        'total' => Shop_CT_Product::$last_query->max_num_pages
//) ); ?>
