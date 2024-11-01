<?php
/**
 * @var $order Shop_CT_Order
 */
?>
<div class="shop-ct-grid-item mat-card">
	<span class="mat-card-title"><?php _e('Billing Details','shop_ct'); ?></span>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="billing_first_name" id="shop_ct_billing_first_name" value="<?= $order->get_shipping_first_name(); ?>"/>
		<label for="shop_ct_billing_first_name"><?php _e('First Name', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="billing_last_name" id="shop_ct_billing_last_name" value="<?= $order->get_billing_last_name(); ?>"/>
		<label for="shop_ct_billing_last_name"><?php _e('Last Name', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="billing_company" id="shop_ct_billing_company" value="<?= $order->get_billing_company(); ?>"/>
		<label for="shop_ct_billing_company"><?php _e('Company', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="billing_address_1" id="shop_ct_billing_address_1" value="<?= $order->get_billing_address_1(); ?>"/>
		<label for="shop_ct_billing_address_1"><?php _e('Address 1', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="billing_address_2" id="shop_ct_billing_address_2" value="<?= $order->get_billing_address_2(); ?>"/>
		<label for="shop_ct_billing_address_2"><?php _e('Address 2', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="billing_city" id="shop_ct_billing_city" value="<?= $order->get_billing_city(); ?>"/>
		<label for="shop_ct_billing_city"><?php _e('City', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="billing_postcode" id="shop_ct_billing_postcode" value="<?= $order->get_billing_postcode(); ?>"/>
		<label for="shop_ct_billing_postcode"><?php _e('Postcode', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-select full-width">
		<label for="billing_country"><?php _e('Country','shop_ct'); ?></label>
		<select name="billing_country" id="shop_ct_billing_country">
			<option>&#8212; <?php _e('Select', 'shop_ct'); ?> &#8212;</option>
			<?php foreach ( $countries as $countryCode => $countryName ): ?>
				<option value="<?php echo $countryCode; ?>" <?php
				if ($countryCode === $order->get_billing_country()) {
					echo 'selected="selected"';
				}
				?>><?php echo $countryName; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="billing_state" id="shop_ct_billing_state" value="<?= $order->get_billing_state(); ?>"/>
		<label for="shop_ct_billing_state"><?php _e('State', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input type="email" name="billing_phone" id="shop_ct_billing_phone" value="<?= $order->get_billing_phone(); ?>"/>
		<label for="shop_ct_billing_phone"><?php _e('Email', 'shop_ct'); ?></label>
		<span></span>
	</div>
	<div class="shop-ct-field mat-input-select full-width">
		<label for="shop_ct_payment_method"><?php _e('Payment Method','shop_ct'); ?></label>
		<select name="payment_method" id="shop_ct_payment_method">
			<option>&#8212; <?php _e('Select', 'shop_ct'); ?> &#8212;</option>
			<?php foreach ( SHOP_CT()->payment_gateways->get_available_payment_gateways() as $gateway ): ?>
				<option value="<?php echo $gateway->id ?>" <?php
				if ($gateway->id === $order->get_payment_method()) {
					echo 'selected="selected"';
				}
				?>><?php echo $gateway->title; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="shop-ct-field mat-input-text full-width">
		<input name="transaction_id" id="shop_ct_transaction_id" value="<?= $order->get_transaction_id(); ?>"/>
		<label for="shop_ct_transaction_id"><?php _e('State', 'shop_ct'); ?></label>
		<span></span>
	</div>
</div>

