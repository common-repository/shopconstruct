<?php
/**
 * @var $attribute Shop_CT_Product_Attribute| null
 * @var $product Shop_CT_Product|null
 */

if (null === $attribute): ?>
    <div class="product-attributes-item">
        <input type="text" name="product-attribute-taxonomies[]" value="" placeholder="<?php _e('Name', 'shop_ct'); ?>">
        <textarea name="product-attribute-terms[]"
                  placeholder="<?php _e('Values separated with commas', 'shop_ct'); ?>"></textarea>
        <span class="product-attributes-delete"><i class="fa fa-trash-o"></i></span>
    </div>
<?php else: ?>
    <div class="product-attributes-item">
        <span class="product-attribute-taxonomy-placeholder"><?php echo $attribute->get_name(); ?></span>
        <input type="hidden" name="product-attribute-taxonomies[]" value="<?php echo $attribute->get_id(); ?>">
        <textarea name="product-attribute-terms[]"
                  placeholder="<?php _e('Values separated with commas', 'shop_ct'); ?>"><?php
            if (null !== $product):
                $terms = $product->get_attribute_terms($attribute);
                if (!empty($terms)):
                    echo implode(',', array_map(
                        function (Shop_CT_Product_Attribute_Term $el) {
                            return $el->get_name();
                        }, $terms));
                endif;
            endif;
            ?></textarea>
        <span class="product-attributes-delete"><i class="fa fa-trash-o"></i></span>
    </div>
<?php endif;

