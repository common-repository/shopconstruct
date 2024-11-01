<?php

$product = new Shop_CT_Product(get_the_ID());

get_header();
?>
    <div class="--container shop-ct">
        <?php \ShopCT\Core\TemplateLoader::get_template('frontend/product/show/index.view.php', compact('product')); ?>
    </div>
<?php
get_footer();
