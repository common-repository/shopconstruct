<?php
/**
 * todo: move codes to appropriate places
 */


/**
 * @param $id
 * @param null $public
 * @param null $type
 * @param null $terms
 * @return string
 * @todo move to templates
 */
function shop_ct_add_product_attribute($id, $public = NULL, $type = NULL, $terms = NULL)
{
    global $wpdb;
    $value_placeholder = __( "Enter some text, or some attributes by '" . SHOP_CT_DELIMITER . "' separating values.", 'shop_ct');

    ob_start();

    if ($id == 'custom') {
        ?>

        <div class="shop_ct_custom_attribute shop_ct_attribute no-label" data-attribute-id="<?php echo $id; ?>">
            <div class="shop_ct_attribute_name_section">
                <span><?php _e('Name: ', 'shop_ct'); ?></span><br/>
                <input type="text" class="shop_ct_attribute_name"/>
            </div>
            <div class="shop_ct_attribute_values_section">
                <span><?php _e('Value(s)', 'shop_ct'); ?>: </span><br/>
                <textarea placeholder="<?php echo $value_placeholder; ?>" class="shop_ct_attribute_values"></textarea>
            </div>
        </div>

        <?php
    } elseif (is_numeric($id)) {
        $attr = new Shop_CT_Product_Attribute($id);
        $name = $attr->get_name();

        ?>
        <div class="shop_ct_attribute no-label" data-attribute-id="<?php echo $id; ?>">
            <div class="shop_ct_attribute_name_section">
                <span><?php _e('Name', 'shop_ct'); ?>: </span><br />
                <input type="text" class="shop_ct_attribute_name" disabled="disabled" value="<?php echo $name; ?>" title="attribute name">
            </div>

            <div class="shop_ct_attribute_values_section">
                <span><?php _e('Value(s)', 'shop_ct'); ?>: </span><br />

                <?php if ($type == 'text') : ?>

                <textarea class="shop_ct_attribute_values" placeholder="<?php echo $value_placeholder; ?>"></textarea>

                <?php elseif ($type == 'select') : ?>

                <select class="select2 shop_ct_attribute_values" multiple="multiple">
                    <?php
                    $terms = $attr->get_terms();

                    foreach ($terms as $term) {
                        echo '<option value="' . $term->get_id() . '">' . $term->get_id() . '</option>';
                    }
                    ?>
                </select>

                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    return ob_get_clean();
}

/**
 * @param $id
 * @param array $attrs
 * @return string
 */
function shop_ct_load_product_attributes($id, $attrs = NULL)
{
    $value_placeholder = __( "Enter some text, or some attributes by '" . SHOP_CT_DELIMITER . "' separating values.", 'shop_ct');

    if (!is_null($attrs)) {
        $all_attributes = Shop_CT_Product_Attribute::get_all();

        ob_start();

        foreach ($attrs as $attr_id=>$attr_term_ids) :
            $attribute = new Shop_CT_Product_Attribute($attr_id);

            $term_names = array();

            foreach ($attr_term_ids as $t_id) {
                $t = new Shop_CT_Product_Attribute_Term($t_id);
                $term_names[$t_id] = $t->get_name();
            }
            ?>

            <div class="shop_ct_attribute no-label" data-attribute-id="<?php echo $attr_id; ?>">

                <div class="shop_ct_attribute_name_section">
                    <span><?php _e('Name', 'shop_ct'); ?>: </span>
                    <input type="text" class="shop_ct_attribute_name" disabled="disabled" value="<?php echo $attribute->get_name(); ?>">
                </div>

                <div class="shop_ct_attribute_values_section">
                    <span class="shop_ct_block"><?php _e('Value(s)', 'shop_ct'); ?>: </span>

                    <?php if ($attribute->get_type() == 'text') : ?>

                        <textarea class="shop_ct_attribute_values" placeholder="<?php echo $value_placeholder; ?>"><?php echo implode(', ', $term_names) ?></textarea>

                    <?php elseif ($attribute->get_type() == 'select') : ?>

                        <select class="select2 shop_ct_attribute_values" multiple="multiple" data-placeholder="<?php _e('Insert terms'); ?>">
                            <?php
                            $terms = $attribute->get_terms();

                            foreach ($terms as $term) {
                                $selected = in_array($term->get_id(), $attr_term_ids) ? 'selected' : '';
                                echo '<option value="' . $term->get_id() . '" ' . $selected . '>' . $term->get_name() . '</option>';
                            }
                            ?>
                        </select>

                    <?php endif; ?>
                </div>

                <i class="fa fa-times shop_ct_remove_product_attribute" aria-hidden="true"></i>

            </div>

            <?php
        endforeach;

        return ob_get_clean();
    }
}

/**
 * @param $id
 * @return mixed|null
 */
function shop_ct_get_product_attributes($id)
{
    if ($id != 'new') {
        return get_post_meta($id, 'shop_ct_product_attributes', true);
    }
    
    return NULL;
}


function shop_ct_sort_by_price_asc($a, $b)
{
    if ($a->final_price === $b->final_price) {
        return 0;
    }
    return $a->final_price > $b->final_price ? 1 : -1;
}

function shop_ct_sort_by_price_desc($a, $b)
{
    if ($a->final_price === $b->final_price) {
        return 0;
    }
    return $a->final_price < $b->final_price ? 1 : -1;
}

function shop_ct_sort_by_review_asc($a, $b)
{
    if ($a->get_rating() === $b->get_rating()) {
        return 0;
    }
    return $a->get_rating() > $b->get_rating() ? 1 : -1;
}

function shop_ct_sort_by_review_desc($a, $b)
{
    if ($a->get_rating() === $b->get_rating()) {
        return 0;
    }
    return $a->get_rating() < $b->get_rating() ? 1 : -1;
}


function shop_ct_sort_by_date_asc($a, $b)
{
    if ($a->get_post_data()->post_date === $b->get_post_data()->post_date) {
        return 0;
    }
    return $a->get_post_data()->post_date > $b->get_post_data()->post_date ? 1 : -1;
}

function shop_ct_sort_by_date_desc($a, $b)
{
    if ($a->get_post_data()->post_date === $b->get_post_data()->post_date) {
        return 0;
    }
    return $a->get_post_data()->post_date < $b->get_post_data()->post_date ? 1 : -1;
}

function shop_ct_order_products($products)
{

    $sorting_function = '';

    if (isset($_GET['products_orderby']) && in_array($_GET['products_orderby'], array('date', 'price', 'review'))) {
        $sorting_function = 'shop_ct_sort_by_' . $_GET['products_orderby'];
    } else {
        $sorting_function = 'shop_ct_sort_by_date';
    }

    if (isset($_GET['products_ordering']) && in_array($_GET['products_ordering'], array('asc', 'desc'))) {
        $sorting_function .= '_' . $_GET['products_ordering'];
    } else {
        $sorting_function .= '_desc';
    }

    usort($products, $sorting_function);

    return $products;
}