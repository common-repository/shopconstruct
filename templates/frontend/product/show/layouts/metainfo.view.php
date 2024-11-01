<?php
/**
 * @var $product Shop_CT_Product
 * @var $shipping_zone Shop_CT_Shipping_Zone|bool
 * @var $current_location_name string
 * @var v
 */
?>

<div class="shop_ct_product_meta_info">
    <ul class="shop_ct_product_meta_info">
        <?php
        if(!$product->is_virtual()): ?>
            <li>
                <?php _e("Shipping","shop_ct"); ?>:&nbsp;&nbsp;<span><?php
                    if(!$shipping_zone instanceof Shop_CT_Shipping_Zone || !$shipping_zone->get_status()){
                        echo 'Not available in '. $current_location_name;
                    }else{
                        echo Shop_CT_Formatting::format_price($shipping_zone->get_cost()).' to '.$current_location_name.'('.$shipping_zone->get_name().')';
                    } ?></span>
            </li>
        <?php endif; ?>

        <li><?php _e("Availability","shop_ct"); ?>: <span><?php echo $product->get_availability()['availability']; ?></span></li>

        <?php
        $tags = $product->get_tags();
        if(!empty($tags)): ?>
            <li>
                <?php _e("Tags","shop_ct"); ?>:
                <div class="product_tag_cat_list">
                    <?php
                    $tagnames = array();

                    foreach($tags as $tag){
                        $tagnames[] = "<a href='".get_term_link($tag->term_id)."'><span>".$tag->name."</span></a>";
                    };
                    echo implode(" ",$tagnames);
                    ?>
                </div>
            </li>
        <?php endif; ?>

        <?php
        $categories = $product->get_categories();

        if(!empty($categories)): ?>
            <li>
                <?php _e("Categories","shop_ct"); ?>:
                <div class="product_tag_cat_list">
                    <?php
                    $catnames = array();
                    foreach($categories as $product_category){
                        $catnames[] = "<a href='".get_term_link($product_category->term_id)."'><span>".$product_category->name."</span></a>";
                    };
                    echo implode(" ",$catnames);
                    ?>
                </div>
            </li>
        <?php endif;

        ?>

    </ul>
</div>
