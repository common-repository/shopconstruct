jQuery(document).ready(function(){
    jQuery("body").on('submit','#shop_ct_checkout_form', function(e){
        e.preventDefault();
        var _btn = jQuery(this),
            btnOriginalText = _btn.text(),
            requiredFields = jQuery('.shop-ct-required input, .shop-ct-required select'),
            formValid = true,
            formErrors = [];

        for(var i = 0;i<requiredFields.length;i++){
            if(!jQuery(requiredFields[i]).val().length) {
                formValid = false;
                formErrors.push(shopCTcheckout.formRequiredFieldsEmpty);
                break;
            }
        }

        if(false === formValid){
            shop_ct_popup.showToastr(formErrors.join('<br />'),'error');
            return false;
        }


        jQuery.ajax({
            url: shopCTcheckout.ajax_url,
            dataType:"JSON",
            type:'POST',
            data: jQuery('#shop_ct_checkout_form').serialize(),
            beforeSend: function(){
                _btn.attr('disabled','disabled');
                _btn.text(shopCTcheckout.btnLoading);
            }
        }).done(function(result){
            if(!result.success){
                shop_ct_popup.showToastr(result.error,'error');
            }else{
                if(typeof result.instructions !== 'undefined' ){
                    _btn.replaceWith('<p class="shop-ct-checkout-instructions">'+result.instructions+'</p>');
                }else if(typeof result.redirect !== 'undefined'){
                    _btn.text(shopCTcheckout.btnRedirecting);
                    window.location.replace(result.redirect);
                }else{
                    shop_ct_popup.showToastr(shopCTcheckout.checkoutSuccess,'error');
                    _btn.text(btnOriginalText);
                }
            }
        })
    });

    jQuery("#shop_ct_checkout_shipping_country").on('change',function(){
        var c = jQuery(this).val();

        jQuery.ajax({
            url: shopCTcheckout.ajax_url,
            dataType:"JSON",
            type:'GET',
            data: {action:'shop_ct_checkout_shipping_select',country:c}
        }).done(function(result){
            if(result.success){
                jQuery('.shipping_cost span').html(result.shipping_cost_format);
                jQuery('#shipping_cost_real').val(result.shipping_cost);
                jQuery('.checkout_total').html(result.total);
                jQuery('.shipping_cost_label').html(result.label);
            }
        })
    })
});
