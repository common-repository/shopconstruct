<?php
/**
 * @var $category Shop_CT_Product_Category
 * @var $category_children Shop_CT_Product_Category[]
 * @var $products Shop_CT_Product[]
 * @var $paged int
 * @var $totalPages int
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

?>
<div class="--container shop-ct">
    <?php

    do_action('shop_ct_before_show_product_category');


    if (empty($category_children) && empty($products)) {
        ?>
        <h2 class="--text-center"><?php _e('Nothing found here', 'shop_ct'); ?></h2>
        <?php
    }

    if (!empty($category_children) && !SHOP_CT()->isAdvanced() || isset($GLOBALS['shop_ct_style_settings']) && $GLOBALS['shop_ct_style_settings']->category_grid_show_subcategories === 'yes') {

        if (SHOP_CT()->isAdvanced() && $GLOBALS['shop_ct_style_settings']->category_show_title === 'yes') {
            echo '<h2 class="shop-ct-title">' . __("Sub Categories of ", "shop_ct") . '"' . $category->get_name() . '"' . '</h2>';
        }

        \ShopCT\Core\TemplateLoader::get_template('frontend/product-category/list.view.php', array('categories' => $category_children));
    }

    if (SHOP_CT()->isAdvanced() && $GLOBALS['shop_ct_style_settings']->category_show_title === 'yes') {
        echo '<h2 class="shop-ct-title">' . __("Products in ", "shop_ct") . '"' . $category->get_name() . '"' . '</h2>';
    }
    ?>
    <div class="shop-ct-side-wrap shop-ct-product-grid-wrap" data-category="<?php echo $category->get_id(); ?>">
        <div class="shop-ct-side">
            <?php
            echo do_shortcode('[ShopConstruct_sorting]');
            echo do_shortcode('[ShopConstruct_filtering current_category_id="' . $category->get_id() . '"]');
            ?>
        </div>
        <div class="shop-ct-side-main">
            <?php \ShopCT\Core\TemplateLoader::get_template('frontend/product/list.view.php', compact('products', 'paged', 'totalPages')); ?>
        </div>
    </div>
    <?php


    ?>

</div><!-- .--container -->
