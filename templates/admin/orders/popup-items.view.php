<?php
/**
 * @var $order Shop_CT_Order
 * @var $allProducts Shop_CT_Product[]
 */
$products = $order->get_products();
?>
<div class="shop-ct-grid-item mat-card">
	<span class="mat-card-title"><?php _e('Items','shop_ct'); ?></span>
	<div class="shop-ct-field order-new-item-field">
		<select class="order-new-item-product">
			<option value=""><?php _e('&#8212;Select Product&#8212;', 'shop_ct'); ?></option>
			<?php
			if (!empty($allProducts)):
				foreach ($allProducts as $product):
				echo '<option value="'.$product->get_id().'" data-price="'.$product->get_price().'">'.$product->get_title().'</option>';
				endforeach;
			endif;
			?>
		</select>
		<input type="number" value="1" min="1" class="order-new-item-quantity" title="<?php _e('Quantity', 'shop_ct'); ?>" />
		<button class="order-add-new-item mat-button"><?php _e('Add Product', 'shop_ct'); ?></button>
	</div>
	<ul class="order-items-list mat-list shop-ct-margin-top-20" data-empty="<?php _e('No items','shop_ct'); ?>">
		<?php
		if (!empty($products)) {
			foreach ( $order->get_products() as $product ) {
				\ShopCT\Core\TemplateLoader::get_template('admin/orders/popup-item.view.php', array(
					'product'=>$product['object'],
					'cost' => $product['cost'],
					'quantity' => $product['quantity']
				));
			}
		}
		else {
			echo '<p class="order-items-list-empty">'.__('No items','shop_ct').'</p>';
		}
		?>
	</ul>
</div>
