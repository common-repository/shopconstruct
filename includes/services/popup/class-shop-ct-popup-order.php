<?php

class Shop_CT_popup_order extends Shop_CT_popup
{
    private $table_items = array();

    private $table_fees = array();

    private $table_shippings = array();

    private function compare_by_name($a, $b)
    {
        return strcmp($a["title"], $b["title"]);
    }

    private function set_table_items($id)
    {
        array_push($this->table_items, $id);
    }

    private function set_table_fees($fee)
    {
        array_push($this->table_fees, $fee);
    }

    private function get_name_by_id($id)
    {
        $product = get_post($id);
        return $product->post_title;
    }

    public function get_currency()
    {

        if (get_option("shop_ct_currency")) {
            $currency = get_option("shop_ct_currency");
        } else {
            $currency = 'EUR';
        }

        return $currency;
    }

    public function show_total($price, $quantity)
    {
        return number_format(floatval($price) * intval($quantity), 2, '.', ' ');
    }

    private function get_products()
    {

        $choices = array();
        $result = get_posts(['post_type' => Shop_CT_Product::get_post_type()]);

        foreach ($result as $key => $post) {
            array_push($choices, array(
                'id' => $post->ID,
                'title' => $post->post_title,
            ));
        }

        usort($choices, array($this, 'compare_by_name'));

        return $choices;
    }

    private function get_fees()
    {
        return $this->table_shippings;
    }

    public function control_status_dropdown($id, $post_type)
    {

        $all_statuses = apply_filters("shop_ct_post_statuses", array());
        $return = array();

        foreach ($all_statuses[$post_type] as $key => $value) {
            $return[$key] = $value;
        }

        return $return;
    }

    public function control_customers()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'users';
        $sql = "SELECT id, user_nicename, user_email FROM " . $table_name;
        $name_email = array('Guest' => 'Guest');
        $result = $wpdb->get_results($sql);

        foreach ($result as $key => $object) {
            $name_email[$object->user_nicename] = $object->user_nicename . ' (' . $object->user_email . ')';
        }

        return $name_email;
    }

    public function control_order_items_table($id, $control, $items = NULL)
    {

        $currency = $this->get_currency();

        $products = isset($control['items']) ? $control['items'] : array();

        /** @var Shop_CT_Order $order */
        $order = $control['order'];

        $currency_position = get_option("shop_ct_currency_pos", "");
        $currency_symbol = Shop_CT_Currencies::get_currency_symbol($currency);

        ?>
        <div><p class="button button-primary" id="order_popup_products_table_actions">Remove Selected Products</p></div>
        <table id="order_product_table" class="widefat striped">
            <thead>
            <tr>
                <th><input id="order_items_table_header_checkbox" class="order_items_table_checkbox" type="checkbox"
                           value="all"></th>
                <th><?php _e('Item', 'shop_ct'); ?></th>
                <th><?php _e('Price', 'shop_ct'); ?></th>
                <th><?php _e('Quantity', 'shop_ct'); ?></th>
                <th><?php _e('Total', 'shop_ct'); ?></th>
                <th></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="4"></td>
                <td colspan="2" id="total_cost"><?php _e('Subtotal', 'shop_ct'); ?>: <?php echo Shop_CT_Formatting::format_price($order->get_total()); ?></td>
            </tr>
            <?php if ($order->requires_delivery()): ?>
                <tr>
                    <td colspan="4"></td>
                    <td colspan="2" id="total_cost"><?php _e('Shipping', 'shop_ct'); ?>
                        : <?php echo Shop_CT_Formatting::format_price($order->get_shipping_cost()); ?></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="4"></td>
                <td colspan="2" id="total_cost"><?php _e('Total', 'shop_ct'); ?>
                    : <?php echo Shop_CT_Formatting::format_price($order->get_total()); ?></td>
            </tr>
            </tfoot>
            <tbody>
            <tr id="no_item_row">
                <td colspan="6"><?php _e('No Items', 'shop_ct'); ?></td>
            </tr>
            <?php foreach ($products as $id => $array) {
                /** @var Shop_CT_Product $product */
                $product = $array['object'];
                $quantity = $array['quantity'];
                $cost = isset($array['cost']) ? $array['cost'] : $product->get_price();

                $this->set_table_items($product->get_id());
                $name = $product->get_title();
                echo $this->add_table_row($id, $name, $quantity,$cost)['string'];
            } ?>

            </tbody>
        </table>
        <input type="hidden" id="totalCost" value="0"/>

        <?php
    }

    public function control_get_all_available_products($id, $control)
    { ?>
        <div id="order_tabs">
            <ul id="shop_ct_order_tabs">
                <li class="active"><a href="#shop_ct_order_products"
                                      class="shop_ct_tabs_menu_link"><?php _e('Products', 'shop_ct') ?></a></li>
            </ul>

            <div style="display: block" class="order_tab">
                <div id="shop_ct_order_products" class="order_popup_all_products_list">
                    <form>
                        <ul>
                            <?php
                            $choices = $this->get_products();
                            foreach ($choices as $key => $value) :

                                $thumbnail_url = wp_get_attachment_url(get_post_thumbnail_id($value['id']));

                                if (!is_string($thumbnail_url)) {
                                    $thumbnail_url = plugins_url("../../images/placeholder.png", __FILE__);
                                }
                                $img = '<img src="' . $thumbnail_url . '" width="35" alt="" />';
                                if (in_array($value['id'], $this->table_items)) : ?>
                                    <li>
                                        <input class="all_products_list" id="post_<?php echo $value['id'] ?>"
                                               type="checkbox" name="<?php echo $value['id'] ?>"
                                               value="<?php echo $value['title'] ?>" checked>

                                        <label class="order_popup_product_list_label"
                                               id="label_post_<?php echo $value['id'] ?>"
                                               for="post_<?php echo $value['id'] ?>"><?php echo $img . $value['title'] ?></label><br/>
                                    </li>
                                <?php endif;
                            endforeach;

                            foreach ($choices as $key => $value) :
                                $thumbnail_url = wp_get_attachment_url(get_post_thumbnail_id($value['id']));

                                if (!is_string($thumbnail_url)) {
                                    $thumbnail_url = plugins_url("../../images/placeholder.png", __FILE__);
                                }
                                $img = '<img src="' . $thumbnail_url . '" width="35" alt="" />';
                                if (!in_array($value['id'], $this->table_items)) : ?>
                                    <li>
                                        <input class="all_products_list" id="post_<?php echo $value['id'] ?>"
                                               type="checkbox" name="<?php echo $value['id'] ?>"
                                               value="<?php echo $value['title'] ?>">
                                        <label class="order_popup_product_list_label"
                                               id="label_post_<?php echo $value['id'] ?>"
                                               for="post_<?php echo $value['id'] ?>"><?php echo $img . $value['title'] ?></label><br/>
                                    </li>
                                <?php endif;
                            endforeach
                            ?>
                        </ul>
                    </form>
                </div>
            </div>

            <div class="orders_popup_table_overlay"><i class="fa fa-spinner fa-pulse"></i></div>
        </div>
        <?php
    }

    public function control_notes($id, $control)
    {
        if (isset($control['notes'])) {
            $notes = $control['notes'];

            foreach ($notes as $id => $note) {
                ?>
                <div class="order_note"
                     data-comment_id="<?php echo $note->comment_ID; ?>"><?php echo $note->comment_content; ?>
                    <span class="dashicons dashicons-no remove_comment_button"></span>
                </div>
                <?php
            }
        }
    }

    public function add_table_row($id, $name, $quantity = 1,$cost)
    {

        ob_start();

        $current_row_total = Shop_CT_Formatting::format_price($cost*$quantity);

        $current_row_single = Shop_CT_Formatting::format_price($cost);
        ?>
        <tr id="<?php echo 'row_' . $id ?>">
            <td><input id="<?php echo 'checkbox_for_' . $id ?>"
                       class="order_items_table_body_checkbox order_items_table_checkbox" type="checkbox"
                       value="<?php echo $name ?>" data-id="<?php echo $id ?>" data-quantity="1"></td>
            <td><?php echo $name ?></td>
            <td class="single_cost"><?php echo $current_row_single ?></td>
            <td class="qty"><input class="order_product_quantity" type="number" min="0" value="<?php echo $quantity ?>"
                                   name="quantity"></td>
            <td class="item_total"><?php echo $current_row_total ?></td>
            <td><span class="dashicons dashicons-no remove_product product"></span></td>
        </tr>
        <?php

        return array(
            'string' => ob_get_clean(),
        );
    }
}