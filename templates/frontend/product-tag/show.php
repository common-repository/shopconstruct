<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

get_header('shop_ct');

$queried_object = get_queried_object();

$product_tag = new Shop_CT_Product_Tag($queried_object->term_id);

$category_children = $product_tag;

$products = Shop_CT_Product::get(array(
    'post_status' => 'any',
    'tax_query' => array(
        array(
            'taxonomy' => Shop_CT_Product_Tag::get_taxonomy(),
            'terms' => $product_tag->get_id(),
        ),
    ),
));
?>
    <div class="--container shop-ct">

        <?php

        do_action('shop_ct_before_show_product_tag');

        if (!empty($products)): ?>

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

            </div><!-- .shop_ct_category_products_container -->

        <?php else: ?>

            <h2 class="--text-center"><?php _e('Nothing found here', 'shop_ct'); ?></h2>

        <?php endif; ?>
    </div>
<?php


get_footer('shop_ct');