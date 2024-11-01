<?php
/**
 * @var $order Shop_CT_Order
 */

get_header();
?>
    <div class="--container shop-ct">
        <div class="shop_ct_order_success_main_section">
            <h2 class="shop_ct_order_success_main_section_desc"><?php _e('THANK YOU FOR YOUR ORDER!', 'shop_ct'); ?></h2>
            <div class="shop_ct_order_success_main_section_order_table">
                <table>
                    <thead>
                    <tr>
                        <th class="item_box"><?php _e('item', 'shop_ct'); ?></th>
                        <th><?php _e('price', 'shop_ct'); ?></th>
                        <th><?php _e('quantity', 'shop_ct'); ?></th>
                        <th><?php _e('subtotal', 'shop_ct'); ?></th>
                        <?php if ($order->requires_delivery()): ?>
                            <th><?php _e('actions', 'shop_ct'); ?></th>
                        <?php endif; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($order->get_products() as $p):
                        /** @var Shop_CT_Product $product */
                        $product = $p['object'];
                        ?>
                        <tr>
                            <td class="table-item-block">
                                <div class="table_image_block"><img src="<?php echo $product->get_image_url(); ?>"
                                                                    alt="<?php echo $product->get_title(); ?>"/></div>
                                <div class="table_text_block">
                                    <span class="product_desc"><?php echo $product->get_title(); ?></span>
                                </div>
                            </td>
                            <td class="table_price_box"><?php echo Shop_CT_Formatting::format_price($p['cost']); ?></td>
                            <td>
                                <span class="shop_ct_order_success_main_section_order_table_number"><?php echo $p['quantity']; ?></span>
                            </td>
                            <td><?php echo Shop_CT_Formatting::format_price($p['cost'] * $p['quantity']) ?></td>
                            <?php if ($order->requires_delivery()):
                                ?>
                                <td><?php
	                                $downloadable_files = $product->get_downloadable_files();
                                    if (!empty($downloadable_files)):

                                        $permission = $product->get_download_permission($order->get_id(), $order->get_billing_email());

                                        if (false !== $permission && $product->is_valid_download_token($order->get_id(), $permission['token'], $order->get_billing_email())):

                                            if ($product->is_download_permission_expired($order->get_id(), $order->get_billing_email()) || $product->is_download_limit_expired($order->get_id(), $order->get_billing_email())):

                                                _e('Download limits for this order have been expired', 'shop_ct');

                                            else:
                                                 ?>
                                                    <a href="<?php echo $product->get_download_link_for_user($order->get_id(), $order->get_billing_email()); ?>"
                                                       target="_blank"><?php echo __('Download Files', 'shop_ct'); ?></a>
                                                    <br/>
                                                    <?php


                                            endif;

                                        elseif('shop-ct-pending' === $order->get_status()):

                                            _e('You will be able to download the files after your payment is confirmed', 'shop_ct');

                                        endif;

                                    endif;
                                    ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php
get_footer();