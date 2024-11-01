<?php
/**
 * @var $product Shop_CT_Product
 */
$large_image_url = $product->get_image_url('large');

?>

<div class="shop_ct_product_images" data-has-zoom="<?php echo (!SHOP_CT()->isAdvanced() || $GLOBALS['shop_ct_style_settings']->product_page_zoom === 'yes') ? 'yes': 'no' ?>">
    <div class="shop_ct_product_main_image">
        <span>
            <img src="<?php echo $large_image_url; ?>" data-zoom-image="<?php echo $large_image_url; ?>" />
        </span>
    </div>
    <div id="shop_ct_product_secondary_images" class="shop_ct_product_secondary_images">
        <?php
        $product_images = $product->get_product_image_gallery();

        foreach ( $product_images as $product_image ) :
            $attachment_url = wp_get_attachment_image_src( $product_image )[0];
            if ( $attachment_url !== $product->get_image_url()[0] ) :
                $large_image_url = wp_get_attachment_image_src( $product_image, "large" )[0];
                $full_image_url = wp_get_attachment_image_src( $product_image, 'full' )[0];
                $thumb_image_url = wp_get_attachment_image_src( $product_image, 'thumbnail' )[0];
                ?>
                <div class="shop_ct_product_secondary_image <?php echo ($product_image === reset($product_images) ? 'active' : ''); ?>">
                    <div>
                        <span><img src="<?php echo $thumb_image_url; ?>" data-img="<?php echo $large_image_url; ?>" data-zoom="<?php echo $full_image_url; ?>"/></span>
                    </div>
                </div>
                <?php
            endif;
        endforeach; ?>
    </div>
</div>
