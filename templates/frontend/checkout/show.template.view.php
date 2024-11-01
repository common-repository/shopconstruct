<?php

get_header();
$cart = SHOP_CT()->cart_manager->get_cart();
$countries = SHOP_CT()->locations->get_countries();
?>
    <div class="--container shop-ct">
        <?php \ShopCT\Core\TemplateLoader::get_template('frontend/checkout/show.view.php',compact('cart','countries')); ?>
    </div>
<?php
get_footer();
