<?php
/**
 * @var $categories Shop_CT_Product_Category[]
 */
?>

<div class="--container shop-ct">

    <?php \ShopCT\Core\TemplateLoader::get_template('frontend/product-category/list.view.php', compact('categories')) ?>

</div>
