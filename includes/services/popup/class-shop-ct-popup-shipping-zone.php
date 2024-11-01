<?php

class Shop_CT_Popup_Shipping_Zone extends Shop_CT_popup {
	public function control_multi_select($id, $control) { ?>
        <label><span class="control_title"><?php echo $control['label']; ?></span>
            <select name="<?php echo $id; ?>" id="popup-control-<?php echo $id; ?>" class="popup-control-value select2" multiple="multiple" data-allow-group-click="true">
            <?php foreach ($control['choices'] as $continent) : ?>
                <optgroup label="<?php echo $continent['name']; ?>">
                <?php foreach ($continent['countries'] as $country_code) : ?>
                    <option <?php if (in_array($country_code, $control['default'])) echo ' selected="selected" '; ?> value="<?php echo $country_code; ?>"><?php echo Shop_CT()->locations->get_country_name_by_code($country_code); ?></option>
                <?php endforeach; ?>
                </optgroup>
            <?php endforeach; ?>
            </select>
        </label>
	<?php }
}