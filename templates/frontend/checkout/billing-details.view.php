<?php
/**
 * @var $cart Shop_CT_Cart
 * @var $countries array
 */
$current_location = \ShopCT\Core\Geolocation::geolocate_ip()['country'];
?>
<div class="shop_ct_checkout_sections billing_section">
    <div class="shop_ct_checkout_billing_title">
        <h2 class="shop_ct_checkout_sections_title">1. <?php _e('Billing Details', 'shop_ct'); ?></h2>
    </div>
    <div class="shop_ct_checkout_information_section">
        <div class="shop_ct_checkout_left_form">
            <label for="shop_ct_checkout_first_name" class="shop_ct_checkout_first_name shop-ct-required">
                <?php _e('First Name', 'shop_ct'); ?>
                <input id="shop_ct_checkout_first_name" type="text" name="billing_first_name" required/>
            </label>
            <label for="shop_ct_checkout_last_name" class="shop_ct_checkout_last_name shop-ct-required">
                <?php _e('Last Name', 'shop_ct'); ?>
                <input id="shop_ct_checkout_last_name" type="text" name="billing_last_name" required/>
            </label>
            <label for="shop_ct_checkout_email" class="shop_ct_checkout_email shop-ct-required">
                <?php _e('Email', 'shop_ct'); ?>
                <input id="shop_ct_checkout_email" type="text" name="billing_email" required/>
            </label>
            <label for="shop_ct_checkout_phone">
                <?php _e('Phone', 'shop_ct'); ?>
                <input id="shop_ct_checkout_phone" type="text" name="billing_phone"/>
            </label>

            <label for="shop_ct_checkout_country" class="shop_ct_checkout_country shop-ct-required">
                Country
                <select class="js-states form-control" id="shop_ct_checkout_country" name="billing_country" required>
                    <?php foreach ($countries as $key => $val):
                        ?>
                        <option value="<?php echo $key; ?>" <?php selected($current_location,$key); ?>><?php echo $val; ?></option>
                    <?php endforeach; ?>
                </select>
            </label>


        </div>
        <div class="shop_ct_checkout_right_form">

            <label for="shop_ct_checkout_state_country">
                <?php _e('State/Country', 'shop_ct'); ?>
                <input id="shop_ct_checkout_state_country" type="text" name="billing_country"/>
            </label>
            <label for="shop_ct_checkout_city">
                <?php _e('City', 'shop_ct'); ?>
                <input id="shop_ct_checkout_city" type="text" name="billing_city"/>
            </label>
            <label for="shop_ct_checkout_address_first" class="shop_ct_checkout_address_first">
                <?php _e('Address 1', 'shop_ct'); ?>
                <input id="shop_ct_checkout_address_first" type="text" name="billing_address_1"/>
            </label>
            <label for="shop_ct_checkout_address_second">
                <?php _e('Address 2', 'shop_ct'); ?>
                <input id="shop_ct_checkout_address_second" type="text" name="billing_address_2"/>
            </label>
            <label for="shop_ct_checkout_postcode">
                <?php _e('Postcode', 'shop_ct'); ?>
                <input id="shop_ct_checkout_postcode" type="text" name="billing_postcode"/>
            </label>



        </div>
    </div>

</div>
