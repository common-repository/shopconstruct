<?php

$category = shop_ct_get_current_cat();
$category_children = shop_ct_get_cat_children();

$products = shop_ct_get_cat_products($category);
$paged = Shop_CT_Product::$last_query->query['paged'];
$totalPages = Shop_CT_Product::$last_query->max_num_pages;

get_header('blog');

\ShopCT\Core\TemplateLoader::get_template(
    'frontend/product-category/show.view.php',
    compact('category', 'category_children','products', 'paged', 'totalPages' )
);

get_footer('blog');
