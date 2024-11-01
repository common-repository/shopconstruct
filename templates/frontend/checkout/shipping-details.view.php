<?php
/**
 * @var $cart Shop_CT_Cart
 * @var $countries array
 */
$current_location = \ShopCT\Core\Geolocation::geolocate_ip()['country'];
?>
<div class="shop_ct_checkout_sections shipping_section">
    <div class="shop_ct_checkout_shipping_section_title">
        <h2 class="shop_ct_checkout_sections_title">2. <?php _e('Shipping Details','shop_ct'); ?></h2>
    </div>
    <div class="shop_ct_checkout_information_section">
        <div class="shop_ct_checkout_left_form">
            <label for="shop_ct_checkout_shipping_first_name">
                <?php _e('First Name','shop_ct'); ?>
                <input id="shop_ct_checkout_shipping_first_name" type="text" name="shipping_first_name"/>
            </label>
            <label for="shop_ct_checkout_shipping_last_name">
                <?php _e('Last Name','shop_ct'); ?>
                <input id="shop_ct_checkout_shipping_last_name" type="text" name="shipping_last_name"/>
            </label>
            <label for="shop_ct_checkout_shipping_company">
                <?php _e('Company','shop_ct'); ?>
                <input id="shop_ct_checkout_shipping_company" type="text" name="shipping_company"/>
            </label>
            <label for="shop_ct_checkout_shipping_country">
                <?php _e('Country','shop_ct'); ?>
                <select name="shipping_country" class="js-states form-control" id="shop_ct_checkout_shipping_country">
                    <?php foreach ($countries as $key => $val):
                        ?>
                        <option value="<?php echo $key; ?>" <?php selected($current_location,$key); ?> ><?php echo $val; ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label for="shop_ct_checkout_shipping_state_country">
                <?php _e('State/Country','shop_ct'); ?>
                <input id="shop_ct_checkout_shipping_state_country" type="text" name="shipping_state" />
            </label>

        </div>
        <div class="shop_ct_checkout_right_form">
            <label for="shop_ct_checkout_shipping_city">
                <?php _e('City','shop_ct'); ?>
                <input id="shop_ct_checkout_shipping_city" type="text" name="shipping_city"/>
            </label>
            <label for="shop_ct_checkout_shipping_address_first">
                <?php _e('Address 1','shop_ct'); ?>
                <input id="shop_ct_checkout_shipping_address_first" type="text" name="shipping_address_1"/>
            </label>
            <label for="shop_ct_checkout_shipping_address_second">
                <?php _e('Address 2','shop_ct'); ?>
                <input id="shop_ct_checkout_shipping_address_second" type="text" name="shipping_address_2"/>
            </label>
            <label for="shop_ct_checkout_shipping_postcode">
                <?php _e('Postcode','shop_ct'); ?>
                <input id="shop_ct_checkout_shipping_postcode" type="text" name="shipping_postcode"/>
            </label>
            <label for="shop_ct_checkout_shipping_cpn">
                <?php _e('Customer Provided Note','shop_ct'); ?>:
                <textarea id="shop_ct_checkout_shipping_cpn" rows="7" cols="20" name="shipping_customer_note"></textarea>
            </label>
        </div>
    </div>
</div>
