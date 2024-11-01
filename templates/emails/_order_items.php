<table cellspacing="0" cellpadding="6" style="width:100%;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;color:#737373;border:1px solid #e4e4e4">
	<thead>
        <tr>
            <th style="border: 1px solid #e4e4e4;"><?php _e('Product', 'shop_ct'); ?></th>
            <th style="border: 1px solid #e4e4e4;"><?php _e('Quantity', 'shop_ct'); ?></th>
            <th style="border: 1px solid #e4e4e4;"><?php _e('Price', 'shop_ct'); ?></th>
        </tr>
	</thead>

	<tfoot>
        <tr>
            <th style="border: 1px solid #e4e4e4;" colspan="2"><?php _e('Subtotal', 'shop_ct'); ?>:</th>
            <td style="border: 1px solid #e4e4e4;"><?php echo Shop_CT_Formatting::format_price($data['order_data']['order']->get_subtotal()); ?></td>
        </tr>
        <?php if ($data['order_data']['order']->requires_delivery()) : ?>
        <tr>
            <th style="border: 1px solid #e4e4e4;" colspan="2"><?php _e('Shipping', 'shop_ct'); ?>:</th>
            <td style="border: 1px solid #e4e4e4;"><?php echo Shop_CT_Formatting::format_price($data['order_data']['order']->get_shipping_cost()); ?></td>
        </tr>
        <?php endif;
        $total = $data['order_data']['order']->get_total();
        ?>
        <tr>
            <th style="border: 1px solid #e4e4e4;" colspan="2"><?php _e('Total', 'shop_ct'); ?>:</th>
            <td style="border: 1px solid #e4e4e4;"><?php echo Shop_CT_Formatting::format_price($total); ?></td>
        </tr>
	</tfoot>

	<tbody>
	<?php foreach ($data['order_data']['order']->get_products() as $product) :
		$price = isset($product['cost']) ? $product['cost'] : $product['object']->get_price();
		$quantity = $product['quantity'];
		?>
        <tr>
            <td style="border: 1px solid #e4e4e4;"><?php echo $product['object']->get_post_data()->post_title; ?></td>
            <td style="border: 1px solid #e4e4e4;"><?php echo $quantity; ?></td>
            <td style="border: 1px solid #e4e4e4;"><?php echo Shop_CT_Formatting::format_price($price); ?></td>
        </tr>
    <?php endforeach; ?>
	</tbody>
</table>