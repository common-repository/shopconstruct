<?php
/**
 * @var $categories Shop_CT_Product_Category[]
 */
?>
<div class="shop_ct_categories_container">
    <?php if (!empty($categories)):

        foreach ($categories as $category) :

            $thumbnail_url = $category->get_thumbnail_url('medium'); ?>

            <a class="shop_ct_category_item" href="<?php echo $category->get_permalink(); ?>">
                <div class="shop_ct_category_thumbnail_wrap">
                    <div class="blured_background" style="background-image:url(<?php echo $thumbnail_url;?>);"></div>
                    <div class="shop_ct_category_thumbnail">
                        <img src="<?php echo $thumbnail_url ?>" alt="<?php echo $category->get_name(); ?>" />
                    </div>

                </div>
                <div class="shop_ct_category_title"><h3><?php echo $category->get_name(); ?></h3></div>
            </a>
        <?php endforeach;
    endif;
    ?>
</div><!-- .shop_ct_categories_container -->