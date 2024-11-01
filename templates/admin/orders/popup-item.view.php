<?php
/**
 * @var $product Shop_CT_Product
 * @var $quantity int
 * @var $cost float
 */
?>
<li class="mat-list-item order-items-list-item" data-product-id="<?php echo $product->get_id(); ?>">
	<img class="mat-list-item--graphic" src="<?php echo $product->get_image_url() ?>" alt="" />
	<?php echo $product->get_title(); ?>
	<span class="mat-list-item--meta"><input type="number" min="1" name="order_products[<?php echo $product->get_id() ?>][quantity]" value="<?php echo $quantity; ?>" /><span class="order-product-cost"><?php
		echo Shop_CT_Formatting::format_price($product->get_price());
	?></span><span class="delete-order-item shop-ct-on-click--scale fa fa-times"></span></span>
	<input type="hidden" name="order_products[<?php echo $product->get_id() ?>][product]" value="<?php echo $product->get_id(); ?>" />
</li>