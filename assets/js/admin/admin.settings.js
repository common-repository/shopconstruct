'use strict';
jQuery(window).ready(function () {
    var $body = jQuery( 'body' );

    jQuery(window).on('shopCT:load',function(){
        jQuery("#shop_ct_wrapper").find(".jscolor").each(function () {
            new jscolor( this );
        })
    });

    $body.on( 'click', ".js-ripple", function(e){
        var $this = jQuery(this);
        var $offset = $this.parent().offset();
        var $circle = $this.find('.c-ripple__circle');

        var x = e.pageX - $offset.left;
        var y = e.pageY - $offset.top;

        $circle.css({
            top: y + 'px',
            left: x + 'px'
        });

        $this
            .addClass('is-active')
            .off('animationend webkitAnimationEnd oanimationend MSAnimationEnd')
            .on('animationend webkitAnimationEnd oanimationend MSAnimationEnd', function() {
                jQuery(this).removeClass('is-active');
            });
    });

    $body.on("click", ".shop_ct_save_settings", function () {
        var successText = "Settings Saved Successfully";
        var formData = jQuery('.shop_ct_settings_form').serialize();
        shopCT.settings_save_data = {
            action: 'shop_ct_ajax',
            task: 'save_settings',
            nonce: shopCTL10n.shop_ct_nonce,
            formData: formData
        };
        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            type: 'post',
            data: shopCT.settings_save_data,
            dataType: 'json'
        }).done(function (result) {
	        if (result.success) {
		        shop_ct_popup.showToastr(successText);
	        }
        }).fail(function (xhr, status, error) {

        });

        return false;
    });

    /**
     Settings paypal country select
     */
    $body.on("change", "#shop_ct_shop_ct_specific_allowed_countries", function () {
        var state = jQuery(this).val();

        if (state == 'specific') {
            jQuery('#specific_countries_div').css("display", "inline-flex");
        } else if (state == 'all') {
            jQuery('#specific_countries_div').css("display", "none");
        }
    });

    if (jQuery("#shop_ct_shop_ct_specific_allowed_countries").val() == 'specific') {
        jQuery('#specific_countries_div').css("display", "inline-flex");
    }

    /**
     product-settings
     */
    $body.on("click", "#shop_ct_shop_ct_enable_review_rating", function () {
        if (jQuery(this).is(":checked")) {
            jQuery("body").find("#product_settings_enable_ratings_div").css("display", "block");
        } else {
            jQuery("body").find("#product_settings_enable_ratings_div").css("display", "none");
        }
    });

    $body.on("click", ".shop_ct_section_navigation li", function () {

        jQuery("body").find(".shop_ct_section_navigation li").removeClass("active");
        jQuery(this).addClass("active");
        var sectionToShow = jQuery(this).attr("rel");

        //shopCT.sessions.setSession("product_settings_active_tab",sectionToShow);

        jQuery("body").find(".shop_ct_hidden_section").removeClass("active");
        jQuery("body").find("#" + sectionToShow).addClass("active");


    });

    $body.on('change', '#shop_ct_shop_ct_allowed_countries', function () {
        var val = jQuery(this).val();
        if (val == 'all') {
            jQuery("#control-container-shop_ct_specific_ship_to_countries,#control-container-shop_ct_button_select_shipping_countries").addClass('hidden');
        } else {
            jQuery("#control-container-shop_ct_specific_ship_to_countries,#control-container-shop_ct_button_select_shipping_countries").removeClass('hidden');
        }
    });

    $body.on('click', '#select_all_countries_btn', function () {
        var options = jQuery('#shop_ct_shop_ct_specific_ship_to_countries').find('option');
        options.attr('selected', 'selected');
        options.closest('select').trigger('change')
    });

    $body.on('click', '#select_no_countries_btn', function () {
        var options = jQuery('#shop_ct_shop_ct_specific_ship_to_countries').find('option');
        options.removeAttr('selected');
        options.closest('select').trigger('change')
    });

    $body.on('change', '#shop_ct_shop_ct_currency', function() {
        var symbol = jQuery(this).find('option[value="' + jQuery(this).val() + '"]').text().match(/\((.+)\)/)[1],
            selected = jQuery('#shop_ct_shop_ct_currency_pos').val();
        
        jQuery('#shop_ct_shop_ct_currency_pos').empty().append(
            '<option value="left" ' + ('left' === selected ? 'selected="selected"' : '') + '>' + currencyPositionTexts.left + ' (' + symbol + '99.99)' + '</option>' +
            '<option value="right" ' + ('right' === selected ? 'selected="selected"' : '') + '>' + currencyPositionTexts.right + ' (99.99' + symbol + ')' + '</option>' +
            '<option value="left-space" ' + ('left-space' === selected ? 'selected="selected"' : '') + '>' + currencyPositionTexts.left_with_space + ' (' + symbol + ' 99.99)' + '</option>' +
            '<option value="right-space" ' + ('right-space' === selected ? 'selected="selected"' : '') + '>' + currencyPositionTexts.right_with_space + ' (99.99 ' + symbol + ')' + '</option>'
        );
    });
});
