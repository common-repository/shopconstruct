<?php
/**
 * @var $order Shop_CT_Order
 */
?>
<div class="shop-ct-grid-item mat-card">
	<span class="mat-card-title"><?php _e('Shipping Details','shop_ct'); ?></span>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="shipping_first_name" id="shop_ct_shipping_first_name" value="<?= $order->get_shipping_first_name(); ?>"/>
		<label for="shop_ct_shipping_first_name"><?php _e('First Name', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="shipping_last_name" id="shop_ct_shipping_last_name" value="<?= $order->get_shipping_last_name(); ?>"/>
		<label for="shop_ct_shipping_last_name"><?php _e('Last Name', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="shipping_company" id="shop_ct_shipping_company" value="<?= $order->get_shipping_company(); ?>"/>
		<label for="shop_ct_shipping_company"><?php _e('Company', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="shipping_address_1" id="shop_ct_shipping_address_1" value="<?= $order->get_shipping_address_1(); ?>"/>
		<label for="shop_ct_shipping_address_1"><?php _e('Address 1', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="shipping_address_2" id="shop_ct_shipping_address_2" value="<?= $order->get_shipping_address_2(); ?>"/>
		<label for="shop_ct_shipping_address_2"><?php _e('Address 2', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="shipping_city" id="shop_ct_shipping_city" value="<?= $order->get_shipping_city(); ?>"/>
		<label for="shop_ct_shipping_city"><?php _e('City', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="shipping_postcode" id="shop_ct_shipping_postcode" value="<?= $order->get_shipping_postcode(); ?>"/>
		<label for="shop_ct_shipping_postcode"><?php _e('Postcode', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-select full-width">
		<label for="shop_ct_shipping_country"><?php _e('Country','shop_ct'); ?></label>
		<select name="shipping_country" id="shop_ct_shipping_country">
			<option>&#8212; <?php _e('Select', 'shop_ct'); ?> &#8212;</option>
			<?php foreach ( $countries as $countryCode => $countryName ): ?>
				<option value="<?php echo $countryCode; ?>" <?php
					if ($countryCode === $order->get_shipping_country()) {
						echo 'selected="selected"';
					}
				?>><?php echo $countryName; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="shipping_state" id="shop_ct_shipping_state" value="<?= $order->get_shipping_state(); ?>"/>
		<label for="shop_ct_shipping_state"><?php _e('State', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
            <textarea id="shop_ct_shipping_customer_note"
                      name="customer_note"><?php echo $order->get_shipping_customer_note(); ?></textarea>
		<label for="shop_ct_shipping_customer_note"><?php _e('Additional Details/Notes', 'shop_ct'); ?></label>
		<span></span>
	</div>
</div>
