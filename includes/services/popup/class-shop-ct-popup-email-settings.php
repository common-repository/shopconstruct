<?php

class Shop_CT_Popup_Email_Settings extends Shop_CT_popup {
	public function control_description( $id, $control ) {
		$description = $control['default']
		?>
		<div>
			<p>
				<?php _e($description, 'shop_ct'); ?>
			</p>
		</div>
		<?php
	}
}