<?php
/**
 * @var $product Shop_CT_Product
 */
?>
<div class="shop-ct-grid-item mat-card">
    <span class="mat-card-title"><?php _e('General','shop_ct'); ?></span>
    <div class="product-title-block">
        <div class="shop-ct-field mat-input-text full-width">
            <input name="post_data[post_title]" id="product_title" value="<?= $product->get_title(); ?>"/>
            <label for="product_title"><?php _e('Title', 'shop_ct'); ?></label>
            <span></span>
        </div>
        <div class="product-permalink-block">
            <?php
            if ( $product->get_post_data()->post_status !== "auto-draft" ): ?>
                <div id="edit-slug-box" class="hide-if-no-js">
                    <?php echo get_sample_permalink_html( $product->get_id() ); ?>
                </div>
            <?php
                wp_nonce_field( 'samplepermalink', 'samplepermalinknonce', false );
            endif; ?>
        </div>
    </div>

    <div class="shop-ct-field mat-input-text full-width">
        <input name="post_meta[sku]" id="post_meta[sku]" value="<?= $product->get_sku(); ?>"/>
        <label for="post_meta[sku]"><?php _e('SKU', 'shop_ct'); ?></label>
        <span></span>
    </div>
    <div class="shop-ct-field mat-input-checkbox">
        <input type="hidden" name="post_data[comment_status]" value="closed"/>
        <label class="mat-input-checkbox-slider full-width">
            <input type="checkbox" id="post_data[comment_status]" name="post_data[comment_status]"
                   value="open" <?php checked('open', $product->get_post_data()->comment_status); ?> />
            <span></span>
        </label>
        <label for="post_data[comment_status]"><?php _e('Enable Review', 'shop_ct'); ?></label>
    </div>
    <div class="shop-ct-field mat-input-checkbox">
        <input type="hidden" name="post_meta[virtual]" value="0"/>
        <label class="mat-input-checkbox-slider full-width">
            <input type="checkbox" id="product_virtual" name="post_meta[virtual]"
                   value="1" <?php checked($product->get_virtual()); ?> />
            <span></span>
        </label>
        <label for="product_virtual"><?php _e('Virtual Product', 'shop_ct'); ?></label>
    </div>
    <div class="shop-ct-field mat-input-checkbox full-width">
        <input type="hidden" name="post_meta[downloadable]" value="0"/>
        <label class="mat-input-checkbox-slider">
            <input type="checkbox" id="product_downloadable" name="post_meta[downloadable]"
                   value="1" <?php checked($product->get_downloadable()); ?> />
            <span></span>
        </label>
        <label for="product_downloadable"><?php _e('Downloadable Product', 'shop_ct'); ?></label>
    </div>
    <div class="shop-ct-field mat-input-text full-width">
        <input name="post_meta[regular_price]" id="product_regular_price"
               value="<?= $product->get_regular_price(); ?>"/>
        <label for="product_regular_price"><?php _e('Regular Price', 'shop_ct'); ?>
            (<?= Shop_CT_Currencies::get_currency_symbol(Shop_CT()->settings->currency); ?>)</label>
        <span></span>
    </div>
    <div class="shop-ct-field mat-input-text full-width">
        <input name="post_meta[sale_price]" id="product_sale_price" value="<?= $product->get_sale_price(); ?>"/>
        <label for="product_sale_price"><?php _e('Discounted Price', 'shop_ct'); ?>
            (<?= Shop_CT_Currencies::get_currency_symbol(Shop_CT()->settings->currency); ?>)</label>
        <span></span>
    </div>
    <div class="shop-ct-field">
        <button data-original="<?php _e('Schedule Sale', 'shop_ct'); ?>"
                data-remove="<?php _e('Remove Schedule', 'shop_ct'); ?>"
                class="product-schedule-sale mat-button"><?php
	        $sale_dates_to = $product->get_sale_price_dates_to();
	        $sale_dates_from = $product->get_sale_price_dates_from();
            if(!empty($sale_dates_to) || !empty($sale_dates_from)){
                _e('Remove Schedule', 'shop_ct');
            }else{
                _e('Schedule Sale', 'shop_ct');
            }
            ?></button>
    </div>
    <div class="shop-ct-field-group product-sale-dates <?php echo(!empty($sale_dates_to) || !empty($sale_dates_from) ? '' : '-hide'); ?>">
        <div class="shop-ct-field shop-ct-flex shop-ct-justify-between shop-ct-align-end">
            <div class="mat-input-text shop-ct-flex-4">
                <input placeholder="From... yy-mm-dd" class="product-datepicker"
                       name="product_sale_price_dates_from" id="product_sale_price_dates_from"
                       value="<?= !empty($sale_dates_from) ? date_i18n('Y-m-d', strtotime($sale_dates_from)) : ''; ?>"/>
                <span></span>
            </div>
            <span class="at shop-ct-flex-1 text-center">@</span>
            <div class="mat-input-text shop-ct-flex-3">
                <input type="number" placeholder="00" name="product_sale_price_dates_from_hours"
                       id="product_sale_price_dates_from_hours" size="2"
                       value="<?= !empty($sale_dates_from) ? date_i18n('H', strtotime($sale_dates_from)) : '00'; ?>"/>
                <span></span>
            </div>
            <div class="mat-input-text shop-ct-flex-3">
                <input type="number" placeholder="00" name="product_sale_price_dates_from_minutes"
                       id="product_sale_price_dates_from_minutes"
                       value="<?= !empty($sale_dates_from) ? date_i18n('i', strtotime($sale_dates_from)) : '00'; ?>"/>
                <span></span>
            </div>
        </div>
        <div class="shop-ct-field shop-ct-flex shop-ct-justify-between shop-ct-align-end">
            <div class="mat-input-text shop-ct-flex-4">
                <input placeholder="To... yy-mm-dd" class="product-datepicker"
                       name="product_sale_price_dates_to"
                       id="product_sale_price_dates_to"
                       value="<?= !empty($sale_dates_to) ? date_i18n('Y-m-d', strtotime($sale_dates_to)) : ''; ?>"/>
                <span></span>
            </div>
            <span class="at shop-ct-flex-1 text-center">@</span>
            <div class="mat-input-text shop-ct-flex-3">
                <input type="number" size="2" placeholder="00" name="product_sale_price_dates_to_hours"
                       id="product_sale_price_dates_to_hours"
                       value="<?= !empty($sale_dates_to) ? date_i18n('H', strtotime($sale_dates_to)) : '00'; ?>"/>
                <span></span>
            </div>
            <div class="mat-input-text shop-ct-flex-3">
                <input type="number" size="2" placeholder="00" name="product_sale_price_dates_to_minutes"
                       id="product_sale_price_dates_to_minutes"
                       value="<?= !empty($sale_dates_to) ? date_i18n('i', strtotime($sale_dates_to)) : '00'; ?>"/>
                <span></span>
            </div>
        </div>
    </div>
</div>
