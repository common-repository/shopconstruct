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
					<?php $this->heading; ?>
				</h1>
			</td>
		</tr>

		<tr>
			<td style="padding: 20px 40px 0 40px;">
				<div style="color:#737373;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:14px;line-height:150%;text-align:left">
					<p style="margin:0 0 16px;"><?php $this->message; ?></p>


					<h2 style="color:#557da1;display:block;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif;font-size:18px;font-weight:bold;line-height:130%;margin:16px 0 8px;text-align:left">
						<?php _e('Customer details', 'shop_ct'); ?>
					</h2>
					<ul>
						<li>
							<strong><?php echo __('Email', 'shop_ct') . ': '; ?></strong>
										<span style="color:#505050;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
											<a href="mailto:<?php echo $this->receiver; ?></a>
										</span>
						</li>
						<li>
							<strong><?php echo __('Tel', 'shop_ct') . ': '; ?></strong>
										<span style="color:#505050;font-family:'Helvetica Neue',Helvetica,Roboto,Arial,sans-serif">
											<?php // Customer Phone number ?>
										</span>
						</li>
					</ul>
				</div>
			</td>
		</tr>
		<tr>
			<td align="center" valign="top" style="padding: 48px;border:0;color:#99b1c7;font-family:Arial;font-size:12px;line-height:125%;text-align:center">
				<?php
				echo get_bloginfo('name') . ' - ';
				_e('Powered by ECommerce', 'shop_ct');
				?>
			</td>
		</tr>
	</table>
</div>
</body>
</html>

