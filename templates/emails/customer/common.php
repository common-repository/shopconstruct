<?php
/**
 * Common template for
 *
 * New order
 * Processing order
 * Completed order
 * Failed order
 * Processing order
 * Refunded order
 */
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body style="width: 600px;margin: 0 auto;">
<div class="wrapper" style="border: 1px solid #dcdcdc;border-radius: 5px 5px 0 0">
	<table style="margin-left: calc(50% - 300px);height: 100%; width: 100%;" border="0" cellpadding="0" cellspacing="0">
		<tr style="background-color: #557DA1;border-radius: 5px 5px 0 0;display: block;">
			<td align="center" valign="top" style="padding: 36px 48px;display: block;">
				<br />
				<h1 style="color:#ffffff;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:30px;font-weight:300;line-height:150%;margin:0;text-align:left;">
					<?php echo $heading; ?>
				</h1>
			</td>
		</tr>

		<tr>
			<td style="padding: 20px 40px 0 40px;">
				<div style="color:#737373;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:14px;line-height:150%;text-align:left">
					<p style="margin:0 0 16px;"><?php _e($message, 'shop_ct'); ?></p>
					<h2 style="color:#557da1;display:block;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left"><?php echo __('Order', 'shop_ct') . ' #' . $data['order_data']['order']->get_id(); ?></h2>

					<?php require SHOP_CT_EMAIL_TEMPLATES_PATH . '_order_items.php'; ?>

					<h2 style="color:#557da1;display:block;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left">
						<?php _e('Customer details', 'shop_ct'); ?>
					</h2>
					<ul>
						<li>
							<strong><?php echo __('Email', 'shop_ct') . ': '; ?></strong>
							<span style="color:#505050;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
                                <a href="mailto:<?php echo $data['order_data']['order']->get_billing_email(); ?>"><?php echo $data['order_data']['order']->get_billing_email(); ?></a>
                            </span>
						</li>
						<li>
							<strong><?php echo __('Tel', 'shop_ct') . ': '; ?></strong>
							<span style="color:#505050;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif"><?php echo $data['order_data']['order']->get_billing_phone(); ?></span>
						</li>
					</ul>
					<table cellspacing="0" cellpadding="0" style="width:100%;vertical-align:top" border="0">
						<tbody>
						<tr>
							<td valign="top" width="50%">
								<h3 style="color:#557da1;display:block;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:16px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left">
									<?php _e('Billing address', 'shop_ct'); ?>
								</h3>
								<p style="color:#505050;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;margin:0 0 16px">
									<?php
									$billing_first_name = $data['order_data']['order']->get_billing_first_name();
									$billing_last_name = $data['order_data']['order']->get_billing_last_name();
									$billing_company = $data['order_data']['order']->get_billing_company();
									$billing_address_1 = $data['order_data']['order']->get_billing_address_1();
									$billing_address_2 = $data['order_data']['order']->get_billing_address_2();
									$billing_city = $data['order_data']['order']->get_billing_city();
									$billing_postcode = $data['order_data']['order']->get_billing_postcode();
									$billing_country = $data['order_data']['order']->get_billing_country();
									echo !empty($billing_first_name) || !empty($billing_last_name)
										? $billing_first_name . ' ' . $billing_last_name . '<br />'
										: '';
									echo !empty($billing_company) ? $billing_company . '<br />' : '';
									echo !empty($billing_address_1) ? $billing_address_1 . '<br />' : '';
									echo !empty($billing_address_2) ? $billing_address_2 . '<br />' : '';
									echo !empty($billing_city) ? $billing_city . '<br />' : '';
									echo !empty($billing_postcode) ? $billing_postcode . '<br />' : '';
									echo !empty($billing_country) ? SHOP_CT()->locations->get_country_name_by_code($billing_country) : '';
									?>
								</p>
							</td>
						</tr>
						</tbody>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top" style="padding: 48px;border:0;color:#99b1c7;font-family:Arial;font-size:12px;line-height:125%;text-align:center">
				<?php
				echo get_bloginfo('name') . ' - ';
				_e('Powered by ShopConstruct', 'shop_ct');
				?>
			</td>
		</tr>
	</table>
</div>
</body>
</html>
