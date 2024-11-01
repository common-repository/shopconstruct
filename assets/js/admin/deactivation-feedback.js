"use strict";
jQuery(document).ready(function () {
    var form = jQuery('.shop-ct-deactivation-form'),
        confirmLink = jQuery(".shop-ct-deactivation-submit"),
        skipLink = jQuery(".shop-ct-deactivation-skip"),
        cancelLink = jQuery(".shop-ct-deactivation-cancel"),
        deactivationURL;


    jQuery('body').on('click', '#the-list tr[data-slug=shopconstruct] .deactivate a', function (e) {
        e.preventDefault();

        shop_ct_popup.show('shop_ct_deactivation_popup', {width: 600, height: 400});

        deactivationURL = jQuery(this).attr('href');

        return false;
    });

    jQuery('body').on('click', '.shop-ct-deactivation-body input[name=selected-reason]', function (e) {
        jQuery(this).closest('form').find('label.shop-ct-active').removeClass('shop-ct-active').addClass('shop-ct-hide');
        jQuery(this).closest('div').find('label.shop-ct-hide').removeClass('shop-ct-hide').addClass('shop-ct-active');
        if ( jQuery(".shop-ct-deactivation-submit").hasClass('shop-ct-hide')) {
            jQuery(".shop-ct-deactivation-submit").removeClass('shop-ct-hide');
            jQuery(".shop-ct-deactivation-skip").addClass('shop-ct-hide');
            jQuery(".shop-ct-deactive-feedback-anon").removeClass('shop-ct-hide');
        }
    });

    jQuery('body').on('click','.shop-ct-deactivation-submit', function (e) {
        e.preventDefault();

        var checkedOption = jQuery('.shop-ct-deactivation-body input[name=selected-reason]:checked'),
            comment = checkedOption.closest('div').find('textarea').val(),
            anon = jQuery('.shop-ct-deactive-feedback-anon input[type=checkbox]').is(':checked') ? 'yes': 'no',
            nonce = jQuery('input[name=shop_ct_deactivation_feedback_nonce]').val();
        if(checkedOption.length || comment.length){
            shop_ct_popup.remove('shop_ct_deactivation_popup');
            sendDeactivationFeedback(checkedOption.val(),comment, anon,nonce);
            setTimeout(function(){
                window.location.replace(deactivationURL);
            },0);
        }else{
            shop_ct_popup.remove('shop_ct_deactivation_popup');
            window.location.replace(deactivationURL);
        }

        return false;
    });

    jQuery('body').on('click','.shop-ct-deactivation-skip', function (e) {
        e.preventDefault();

        window.location.replace(deactivationURL);

        return false;
    });

    jQuery('body').on('click','.shop-ct-deactivation-cancel', function (e) {
        e.preventDefault();

        shop_ct_popup.remove('shop_ct_deactivation_popup');

        return false;
    });

    function sendDeactivationFeedback(v, c, a, n) {
        jQuery.ajax({
            url: ajaxurl,
            method: 'post',
            data: {
                action: 'shop_ct_deactivation_feedback',
                value: v,
                comment: c,
                anon: a,
                nonce: n
            }
        });
    }
});