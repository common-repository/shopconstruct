<?php
/**
 * @var $order Shop_CT_Order
 * @var $autoDraft bool
 * @var $countries
 * @var $customers
 * @var $allProducts
 */

$saveText = $autoDraft ? __('Publish', 'shop_ct') : __('Save Changes', 'shop_ct');
?>
<div class="shop-ct-popup-content">
	<form id="order_popup_form" action="#" method="post">
		<div class="shop-ct-grid shop-ct-popup-grid">
			<input type="hidden" id="order_id" name="order_id" value="<?php echo $order->get_id(); ?>"/>
			<input type="hidden" id="order_autodraft" name="order_autodraft" value="<?php echo intval($autoDraft); ?>"/>
			<input type="hidden" name="post_data[post_status]" value="publish"/>
			<input type="hidden" name="action" value="shop_ct_update_order"/>
			<?php \ShopCT\Core\TemplateLoader::get_template('admin/orders/popup-general.view.php', compact('order', 'customers')); ?>
			<?php \ShopCT\Core\TemplateLoader::get_template('admin/orders/popup-items.view.php', compact('order', 'allProducts')); ?>
			<?php \ShopCT\Core\TemplateLoader::get_template('admin/orders/popup-shipping.view.php', compact('order', 'countries')); ?>
			<?php \ShopCT\Core\TemplateLoader::get_template('admin/orders/popup-billing.view.php', compact('order', 'countries')); ?>
		</div>
	</form>
</div>
<div class="shop-ct-popup-actions">
	<div>
		<span class="spinner"></span>
		<input type="submit" class="mat-button mat-button--primary order-save" value="<?= $saveText; ?>" />
	</div>
</div>
