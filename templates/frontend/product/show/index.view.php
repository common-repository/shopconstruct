<?php
/**
 * @var $product Shop_CT_Product
 */
$productCategories = $product->get_categories();
if (!empty($productCategories)) {
    $first_category = new Shop_CT_Product_Category($product->get_categories()[0]->term_id);
    $first_category_name = $first_category->get_name();
    $first_category_permalink = $first_category->get_permalink();
}

$current_location = \ShopCT\Core\Geolocation::geolocate_ip()['country'];
$current_location_name = SHOP_CT()->locations->get_country_name_by_code($current_location);
if (!empty($current_location)) {
    $shipping_zone = Shop_CT_Shipping_Zone::get_zone_by_location($current_location);
} else {
    $shipping_zone = new Shop_CT_Shipping_Zone(1);
}

$attributes = $product->get_attributes();
$content = $product->get_post_data()->post_content;

do_action('shop_ct_before_show_product');
?>
    <div class="shop_ct_product_container shop-ct" data-product_id="<?php echo $product->get_id(); ?>">

        <?php if (!empty($first_category) && (!SHOP_CT()->isAdvanced() || $GLOBALS['shop_ct_style_settings']->product_page_breadcrumbs === 'yes')): ?>
            <!-- Breadcrumbs -->
            <div id="shop_ct_product_breadcrumbs">
                <a href="<?php echo $first_category_permalink; ?>" class="shop-ct-prev-cat-link"><i
                            class="fa fa-angle-left"></i> Back to <span
                            class="shop-ct-prev-cat-name"><?php echo $first_category_name; ?></span></a>
            </div>
        <?php endif; ?>

        <div id="shop_ct_product_entry_info">
            <!-- Product Images -->
            <?php \ShopCT\Core\TemplateLoader::get_template('frontend/product/show/layouts/images.view.php', compact('product')); ?>
            <div id="shop_ct_product_meta_details">
                <!-- Product Heading -->
                <?php \ShopCT\Core\TemplateLoader::get_template('frontend/product/show/layouts/heading.view.php', compact('product')); ?>
                <!-- Meta Info -->
                <?php \ShopCT\Core\TemplateLoader::get_template('frontend/product/show/layouts/metainfo.view.php', compact('product', 'current_location', 'current_location_name', 'shipping_zone')); ?>

                <!-- Product excerpt -->
                <div class="shop_ct_product_excerpt">
                    <?php echo apply_filters('shop_ct_short_description', $product->get_post_data()->post_excerpt); ?>
                </div>
                <!-- Product purchase -->
                <?php \ShopCT\Core\TemplateLoader::get_template('frontend/product/show/layouts/purchase.view.php', compact('product', 'shipping_zone')); ?>
            </div>
        </div>


        <!-- Share buttons -->
        <!--<div class="shop_ct_product_share">
            <hr class="grey_line">
            <h3><?php /*_e("SHARE THIS:", "shop_ct"); */?></h3>
            <hr>
        </div>-->


    </div>

    <div class="shop_ct_product_info_nav">
        <ul>
            <?php
            $active_tab = false;
            if (!empty($attributes) || !empty($content)):
                $active_tab = true;
                ?>
                <li class="active" rel="shop_ct_product_details">
                    <?php _e('DETAILS', 'shop_ct') ?>
                </li>

            <?php endif; ?>
            <?php if (comments_open($product->get_id())): ?>
                <li <?php if (!$active_tab) {
                    echo 'class="active"';
                    $active_tab = true;
                } ?> rel="shop_ct_product_reviews">
                    <?php printf(__('REVIEWS (%s)', 'shop_ct'), wp_count_comments($product->get_id())->approved); ?>
                </li>
            <?php endif; ?>
        </ul>
    </div>

    <div class="shop_ct_product_info">
        <?php
        $active_section =false;
        if (!empty($attributes) || !empty($content)):
            $active_section = true;
            ?>
            <section id="shop_ct_product_details" class="active">

                <?php if (!empty($attributes)): ?>

                    <div class="shop_ct_product_attr">
                        <div class="shop_ct_product_attr_title"><?php _e('Attributes', 'shop_ct'); ?></div>
                        <div class="shop_ct_product_attr_value">
                            <ul class="single_product_text_attrs">


                                <?php foreach ($product->get_attributes() as $attr_slug => $attr_terms):
                                    if (empty($attr_terms)) {
                                        continue;
                                    }
                                    $attribute = new Shop_CT_Product_Attribute(null, array('slug' => $attr_slug));
                                    $values = implode(',&nbsp;', array_map(
                                        function (Shop_CT_Product_Attribute_Term $el) {
                                            return $el->get_name();
                                        }, $attr_terms));
                                    ?>
                                    <li title="<?php echo $values; ?>>">
                                        <span class="attr_name"><?php echo $attribute->get_name(); ?>: </span><span
                                                class="attr_value"><?php echo $values; ?></span>
                                    </li>

                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>

                <?php endif; ?>

                <?php if (!empty($content)): ?>

                    <div class="shop_ct_product_desc">
                        <div class="shop_ct_product_attr_title">
                            <?php _e('Description', 'shop_ct'); ?>
                        </div>
                        <div class="shop_ct_product_attr_value shop_ct_product_description">
                            <?php echo wp_kses_post($content); ?>
                        </div>
                    </div>

                <?php endif; ?>

            </section>
        <?php endif; ?>



        <?php if (comments_open($product->get_id())): ?>
            <section <?php if (!$active_section) {
                echo 'class="active"';
                $active_section = true;
            } ?> id="shop_ct_product_reviews">
                <?php \ShopCT\Core\TemplateLoader::get_template('frontend/product/show/layouts/comments.view.php', compact('product')); ?>
            </section>
        <?php endif; ?>
    </div>
<?php

do_action('shop_ct_after_show_product');
