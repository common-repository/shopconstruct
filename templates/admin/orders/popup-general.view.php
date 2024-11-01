<?php
/**
 * @var $order Shop_CT_Order
 * @var $customers array
 */

$date = $order->get_date();
?>
<div class="shop-ct-grid-item mat-card">
	<span class="mat-card-title"><?php _e('General','shop_ct'); ?></span>
	<div class="shop-ct-field mat-input-select full-width">
		<label for="shop_ct_order_status"><?php _e('Status','shop_ct'); ?></label>
		<select name="order_status" id="shop_ct_order_status">
			<?php foreach ( Shop_CT_Order::get_order_status_labels() as $statusCode => $statusLabel ): ?>
				<option value="<?php echo $statusCode; ?>" <?php
				if ($statusCode === $order->get_status()) {
					echo 'selected="selected"';
				}
				?>><?php echo $statusLabel; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="shop-ct-field mat-input-select full-width">
		<label for="shop_ct_order_customer"><?php _e('Customer','shop_ct'); ?></label>
		<select name="order_customer" id="shop_ct_order_customer">
			<?php foreach ( $customers as $key => $value ): ?>
				<option value="<?php echo $key; ?>" <?php
				if ($key === $order->get_customer()) {
					echo 'selected="selected"';
				}
				?>><?php echo $value; ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<span class="shop-ct-margin-top-20 mat-input-label"><?php _e('Date','shop_ct'); ?></span>
	<div class="shop-ct-field shop-ct-no-margin shop-ct-flex shop-ct-justify-between shop-ct-align-end">
		<div class="mat-input-text shop-ct-no-margin shop-ct-flex-4">
			<input placeholder="yy-mm-dd" class="order-datepicker"
			       name="order_date" id="shop_ct_order_date"
			       value="<?php echo !empty($date) ? date_i18n('Y-m-d', strtotime($date)) : ''; ?>"/>
			<span></span>
		</div>
		<span class="at shop-ct-flex-1 text-center">@</span>
		<div class="mat-input-text shop-ct-no-margin shop-ct-flex-3">
			<input type="number" placeholder="00" name="order_date_hours"
			       id="order_date_hours" size="2"
			       value="<?php echo !empty($date) ? date_i18n('H', strtotime($date)) : '00'; ?>"/>
			<span></span>
		</div>
		<div class="mat-input-text shop-ct-no-margin shop-ct-flex-3">
			<input type="number" placeholder="00" name="order_date_minutes"
			       id="order_date_minutes"
			       value="<?php echo !empty($date) ? date_i18n('i', strtotime($date)) : '00'; ?>"/>
			<span></span>
		</div>
	</div>
</div>
