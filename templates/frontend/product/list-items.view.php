<?php
/**
 * @var $products Shop_CT_Product[]
 */
?>
<?php if(!empty($products)):
    foreach ($products as $product) : ?>
        <div class="shop_ct_category_products_item" data-prod-id="<?php echo $product->get_id(); ?>">
            <a href="<?php echo $product->get_permalink() ?>">
                <div class="shop_ct_category_product_img">
                    <img src="<?php echo $product->get_image_url('medium'); ?>">
                </div>
                <div class="shop_ct_category_product_info">
                    <h3><?php echo $product->get_title(); ?></h3>
                    <?php if (SHOP_CT()->product_settings->enable_review_rating === "yes") : ?>
                        <span class="rating_stars">
                                <?php
                                $product_rating = $product->get_rating();
                                for ($n = 5; $n > 0; $n--) {
                                    $class = '';

                                    if ($product_rating >= $n && $product_rating < $n + 1) {
                                        $class = 'active';
                                    } elseif (round($product_rating) == $n) {
                                        $class = 'half';
                                    }

                                    echo "<span class='rating_star " . $class . " fa fa-star'></span>";
                                };
                                ?>
                            </span>
                    <?php endif; ?>
                    <span class="price_block">
			                    <?php if ($product->is_on_sale()) : ?>
                                    <del class="shop_ct_category_product_regular_price"><?php echo Shop_CT_Formatting::format_price($product->get_regular_price()); ?></del>
                                    <ins class="shop_ct_category_product_sale_price"><?php echo Shop_CT_Formatting::format_price($product->get_sale_price()); ?></ins>
                                <?php else : ?>
                                    <span class="shop_ct_category_product_regular_price"><?php echo Shop_CT_Formatting::format_price($product->get_regular_price()); ?></span>
                                <?php endif; ?>
		                    </span>
                </div>
            </a>
            <div class="shop-ct-product-cart-actions">
                <form class="shop-ct-product-cart-update" method="get" action="<?php $_SERVER['PHP_SELF']; ?>" >
                    <button class="shop-ct-button shop-ct-add-to-cart" data-product-id="<?php echo $product->get_id(); ?>">
                        <i class="fa fa-cart-plus" aria-hidden="true"></i>
                        <span class="shop-ct-spinner">
                                        <span class="shop-ct-bounce1"></span><span class="shop-ct-bounce2"></span><span class="shop-ct-bounce3"></span>
                                    </span>
                    </button><input type="number" name="shop-ct-add-to-cart-count" value="1" min="1" title="<?php _e('Product Count','shop_ct'); ?>"  />
                </form>
            </div>
        </div>
    <?php endforeach;
else:
    ?>
    <p>Nothing found</p>
<?php endif; ?>
