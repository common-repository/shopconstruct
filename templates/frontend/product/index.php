<?php
/**
 * @var $products Shop_CT_Product[]
 */

?>

<div class="shop_ct_category_products_container">
        <?php foreach ($products as $product) : ?>
            <div class="shop_ct_category_products_item">
                <a href="<?php echo $product->get_permalink() ?>">
                    <div class="shop_ct_category_product_img">
                        <img src="<?php echo $product->get_image_url('medium'); ?>">
                    </div>
                    <div class="shop_ct_category_product_info">
                        <h3><?php echo $product->get_title(); ?></h3>
                        <span>
				                    <?php if ($product->is_on_sale()) : ?>
                                        <del class="shop_ct_category_product_regular_price"><?php echo Shop_CT_Formatting::format_price($product->get_regular_price()); ?></del>
                                        <ins class="shop_ct_category_product_sale_price"><?php echo Shop_CT_Formatting::format_price($product->get_sale_price()); ?></ins>
                                    <?php else : ?>
                                        <span class="shop_ct_category_product_regular_price"><?php echo Shop_CT_Formatting::format_price($product->get_regular_price()); ?></span>
                                    <?php endif; ?>
			                    </span>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    <?php the_posts_pagination( array() ); ?>
</div><!-- .shop_ct_category_products_container -->
