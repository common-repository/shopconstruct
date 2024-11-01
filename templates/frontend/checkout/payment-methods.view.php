<?php
/**
 * @var $cart Shop_CT_Cart
 * @var $zone Shop_CT_Shipping_Zone | bool
 * @var $shipping_cost float
 */
?>
<?php if(false != $zone): ?>
<div class="shop_ct_checkout_sections payment_section">
    <div class="shop_ct_checkout_payment_title">
        <h2 class="shop_ct_checkout_sections_title">4. <?php _e('Payment Method','shop_ct'); ?></h2>
    </div>
    <div class="shop_ct_checkout_payment_section">
        <main>
            <?php
            $check = false;

            if(SHOP_CT()->payment_gateways->payment_gateways()['paypal']->is_available()): ?>
                <input id="tab1" type="radio" name="payment_method" <?php if(!$check){ echo 'checked'; $check=true; } ?> value="paypal">
                <label for="tab1" class="paypal_label"><?php echo SHOP_CT()->payment_gateways->payment_gateways()['paypal']->get_title(); ?></label>
            <?php endif; ?>

            <?php if(SHOP_CT()->payment_gateways->payment_gateways()['bacs']->is_available()): ?>
                <input id="tab2" type="radio" name="payment_method" <?php if(!$check){ echo 'checked'; $check=true; } ?> value="bacs">
                <label for="tab2" class="bacs_label"><?php echo SHOP_CT()->payment_gateways->payment_gateways()['bacs']->get_title(); ?></label>
            <?php endif; ?>

            <?php if(SHOP_CT()->payment_gateways->payment_gateways()['cheque']->is_available()): ?>
                <input id="tab3" type="radio" name="payment_method" <?php if(!$check){ echo 'checked'; $check=true; } ?> value="cheque">
                <label for="tab3" class="cheque_label"><?php echo SHOP_CT()->payment_gateways->payment_gateways()['cheque']->get_title() ?></label>
            <?php endif; ?>

            <?php if(SHOP_CT()->payment_gateways->payment_gateways()['cod']->is_available()): ?>
                <input id="tab4" type="radio" name="payment_method" <?php if(!$check){ echo 'checked'; $check=true; } ?> value="cod">
                <label for="tab4" class="cash_label"><?php echo SHOP_CT()->payment_gateways->payment_gateways()['cod']->get_title(); ?></label>
            <?php endif; ?>

            <?php if(SHOP_CT()->payment_gateways->payment_gateways()['paypal']->is_available()): ?>
                <section id="content1" class="paypal_section">
                    <?php echo wp_unslash(SHOP_CT()->payment_gateways->payment_gateways()['paypal']->get_description()); ?>
                </section>
            <?php endif; ?>

            <?php if(SHOP_CT()->payment_gateways->payment_gateways()['bacs']->is_available()): ?>
                <section id="content2" class="bacs_section">
                    <?php echo wp_unslash(SHOP_CT()->payment_gateways->payment_gateways()['bacs']->get_description()); ?>
                </section>
            <?php endif; ?>

            <?php if(SHOP_CT()->payment_gateways->payment_gateways()['cheque']->is_available()): ?>
                <section id="content3" class="cod_section">
                    <?php echo wp_unslash(SHOP_CT()->payment_gateways->payment_gateways()['cheque']->get_description()); ?>
                </section>
            <?php endif; ?>

            <?php
            if(SHOP_CT()->payment_gateways->payment_gateways()['cod']->is_available()): ?>
                <section id="content4" class="cash_section">
                    <?php echo wp_unslash(SHOP_CT()->payment_gateways->payment_gateways()['cod']->get_description()); ?>
                </section>
            <?php endif; ?>

        </main>
    </div>
</div>
<div class="shop_ct_checkout_submit_section">
    <button class="shop-ct-button"><?php _e('Confirm Order','shop_ct'); ?></button>
</div>
<?php else: ?>
<p style="text-align:center">
    <b><?php _e('Unavailable for your zone', 'shop_ct'); ?></b>
</p>

<?php endif; ?>
