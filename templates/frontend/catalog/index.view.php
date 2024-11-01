<?php
/**
 * @var $categories Shop_CT_Product_Category
 * @var $products Shop_CT_Product
 */

?>

<div class="--container shop-ct">
    <?php



    do_action('shop_ct_before_show_catalog');

    if (!empty($categories)) {
       echo '<h2>' . __("Product Categories", "shop_ct") . '</h2>';

        \ShopCT\Core\TemplateLoader::get_template('frontend/product-category/list.view.php', compact('categories'));
    }

    echo do_shortcode('[ShopConstruct_sorting]');

    if (!empty($products)) {

       echo '<h2>' . __("Products", "shop_ct") . '</h2>';

        \ShopCT\Core\TemplateLoader::get_template('frontend/product/list.view.php', compact('products'));
    }

    if (empty($categories) && empty($products)) {
        ?>
        <h2 class="--text-center"><?php _e('Nothing found here', 'shop_ct'); ?></h2>
        <?php
    }

    ?>
</div>
