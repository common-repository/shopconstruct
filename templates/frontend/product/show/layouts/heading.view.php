<?php
/**
 * @var $product Shop_CT_Product
 */

?>
<div class="shop_ct_product_heading">

    <h1 class="shop_ct_product_title"><?php echo $product->get_title(); ?></h1>
    <?php
    $rating = $product->get_rating();
    $ordersCount = $product->get_orders_count();
    if ((SHOP_CT()->product_settings->enable_review_rating === "yes" && !empty($rating)) || !empty($ordersCount)) : ?>
        <div class="shop_ct_product_rating_orders">

            <div class="shop_ct_product_rating_stars">
                <?php
                if (SHOP_CT()->product_settings->enable_review_rating === "yes" && !empty($rating)) : ?>

                    <div class="rating_stars">
                        <?php

                        $product_rating = $rating;

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
                    </div>
                    <div class="shop_ct_product_rating">
                        <span class="product_approx_rating"><?php printf(__('%s out of 5 (%s votes)', 'shop_ct'), $product_rating, $product->get_rating_count()); ?></span>
                        <span class="line_slash">|</span>
                    </div>

                <?php endif;
                $orders_count = $product->get_orders_count();
                if (SHOP_CT()->isAdvanced() && $GLOBALS['shop_ct_style_settings']->product_page_orders_count === 'yes'): ?>
                    <div class="shop_ct_product_orders_count">
                        <span class="product_order_count"><?php _e(sprintf("%d orders", $product->get_orders_count()), "shop_ct") ?></span>
                    </div>
                <?php endif; ?>

            </div>

        </div>
    <?php endif; ?>
    <div class="shop_ct_product_prices" data-onsale="<?php echo intval($product->is_on_sale()); ?>"
         data-regular_price="<?php echo Shop_CT_Formatting::format_price($product->get_regular_price()); ?>"
         data-sale_date_to="<?php echo $product->get_sale_price_dates_to(); ?>"
         data-sale_date_from="<?php echo $product->get_sale_price_dates_from(); ?>">
        <?php
        if ($product->is_on_sale()) :
            if (!$product->has_sale_countdown()) : ?>
                <del><?php echo Shop_CT_Formatting::format_price($product->get_regular_price()); ?></del>
                <ins class="shop_ct_product_sale_price"><?php echo Shop_CT_Formatting::format_price($product->get_sale_price()); ?></ins>
            <?php else : ?>
                <del><?php echo Shop_CT_Formatting::format_price($product->get_regular_price()); ?></del>
                <ins class="shop_ct_product_sale_price"><?php echo Shop_CT_Formatting::format_price($product->get_sale_price()); ?></ins>
	            <div class="countdown">
		            <span class="shop_ct_product_sale_date"></span>
	            </div>
            <?php endif;
        else : ?>
            <span class="shop_ct_product_regular_price"><?php echo Shop_CT_Formatting::format_price($product->get_regular_price()); ?></span>
        <?php endif; ?>
    </div>
</div>
