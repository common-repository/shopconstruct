<?php

$args = array(
    'parent' => 0
);

if (isset($currentCategoryId)) {
    $args['parent'] = $currentCategoryId;
    $currentCategory = new Shop_CT_Product_Category($currentCategoryId);
    $parentId = $currentCategory->get_parent();
    if($parentId) {
        $parentCategory = new Shop_CT_Product_Category($currentCategory->get_parent());
    }

}

$categories = Shop_CT_Product_Category::get($args);

global $wp;
$currentUrl = home_url($wp->request);


?>

<div class="shop-ct-filtering">
    <form action="#" method="get">
        <?php if (!empty($categories) || isset($currentCategory)): ?>
            <div class="shop-ct-filter-section">
                <h3><?php _e('Category','shop_ct'); ?></h3>
                <ul class="shop-ct-filter-category-list">
                    <?php

                    if(isset($parentCategory)): ?>
                        <li class="shop-ct-cat-parent">
                            <a href="<?php echo $parentCategory->get_permalink(); ?>">
                                <svg fill="currentColor" preserveAspectRatio="xMidYMid meet" height="1em" width="1em" viewBox="0 0 8 12" class="lb8Bs" style="vertical-align:middle"><title>Chevron Left</title><g fill="none" stroke="none" stroke-width="1" fill-rule="evenodd"><g transform="translate(1.000000, 1.000000)" stroke="currentColor" stroke-width="2"><polyline transform="translate(3.500000, 5.507797) rotate(90.000000) translate(-3.500000, -5.507797) " points="-1.5 3 3.5155939 8.0155939 8.5 3.0311879"></polyline></g></g></svg>
                                <?php echo $parentCategory->get_name(); ?>
                            </a>
                        </li>

                    <?php endif;

                    if (isset($currentCategory)): ?>

                        <li class="shop-ct-current-cat">
                            <a href="#"><?php echo $currentCategory->get_name() ?></a><span class="shop-ct-cat-count"><?php echo $currentCategory->get_count(); ?></span></li>
                    <?php

                    endif;

                    if(!empty($categories)):
                    foreach ($categories as $category): ?>
                        <li>
                            <a href="<?php echo $category->get_permalink(); ?>"><?php echo $category->get_name(); ?></a><span class="shop-ct-cat-count"><?php echo $category->get_count(); ?></span>
                        </li>
                    <?php endforeach;
                    endif;

                    ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="shop-ct-filter-section">
            <h3>Rating</h3>
            <a class="shop-ct-filter-rating-link <?php if(!isset($_GET['prod_min_rating']) || !$_GET['prod_min_rating']) { echo 'shop-ct-filter-rating-active'; } ?>"
               data-rating="0"
               href="<?php echo remove_query_arg('prod_min_rating', $_SERVER['REQUEST_URI']); ?>">
                    <?php _e('Show All','shop_ct'); ?>
            </a>

            <a class="shop-ct-filter-rating-link <?php if(isset($_GET['prod_min_rating']) && $_GET['prod_min_rating'] == '1') { echo 'shop-ct-filter-rating-active'; } ?>"
               data-rating="1"
               href="<?php echo add_query_arg(array('prod_min_rating' => '1'), $_SERVER['REQUEST_URI']); ?>">
                    <?php _e('1 star and higher','shop_ct'); ?>
            </a>

            <a class="shop-ct-filter-rating-link <?php if(isset($_GET['prod_min_rating']) && $_GET['prod_min_rating'] == '2') { echo 'shop-ct-filter-rating-active'; } ?>"
               data-rating="2"
            href="<?php echo add_query_arg(array('prod_min_rating' => '2'), $_SERVER['REQUEST_URI']); ?>">
                    <?php _e('2 stars and higher','shop_ct'); ?>
            </a>


            <a class="shop-ct-filter-rating-link <?php if(isset($_GET['prod_min_rating']) && $_GET['prod_min_rating'] == '3') { echo 'shop-ct-filter-rating-active'; } ?>"
               data-rating="3"
               href="<?php echo add_query_arg(array('prod_min_rating' => '3'), $_SERVER['REQUEST_URI']); ?>">
                    <?php _e('3 stars and higher','shop_ct'); ?>
            </a>

            <a class="shop-ct-filter-rating-link <?php if(isset($_GET['prod_min_rating']) && $_GET['prod_min_rating'] == '4') { echo 'shop-ct-filter-rating-active'; } ?>"
               data-rating="4"
               href="<?php echo add_query_arg(array('prod_min_rating' => '4'), $_SERVER['REQUEST_URI']); ?>">
                    <?php _e('4 stars and higher','shop_ct'); ?>
            </a>

        </div>

        <div class="shop-ct-filter-section">
            <h3>Price</h3>
            <div class="shop-ct-filter-price-wrap">
                <div class="shop-ct-filter-price-min">
                    <span><?php echo Shop_CT_Currencies::get_currency_symbol(Shop_CT()->settings->currency); ?></span>
                    <input type="number" name="prod_min_price" title="min. price" value="<?php if(isset($_GET['prod_min_price']) && !empty($_GET['prod_min_price'])) { echo floatval($_GET['prod_min_price']); } ?>"/>
                </div>
                <span class="shop-ct-filter-price-dash">-</span>
                <div class="shop-ct-filter-price-max">
                    <span><?php echo Shop_CT_Currencies::get_currency_symbol(Shop_CT()->settings->currency); ?></span>
                    <input type="number" name="prod_max_price" title="max. price" value="<?php if(isset($_GET['prod_max_price']) && !empty($_GET['prod_max_price'])) { echo floatval($_GET['prod_max_price']); } ?>" />
                </div>
                <button class="shop-ct-filter-price-button">
                    <svg fill="currentColor" preserveAspectRatio="xMidYMid meet" height="12" width="8" viewBox="0 0 8 13" style="vertical-align:middle"><g fill="none" stroke="none" stroke-width="1" fill-rule="evenodd"><g transform="translate(-2.000000, 4.000000)" stroke="currentColor" stroke-width="2"><polyline transform="translate(5.500000, 2.500000) rotate(90.000000) translate(-5.500000, -2.500000)" points="10.5 5 5.5 6.66133815e-16 0.5 5"></polyline></g></g></svg>
                </button>
            </div>
        </div>

    </form>
</div>