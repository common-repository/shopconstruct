jQuery(window).on("shop_ct_popup:ready", function (event, elementId, args) {
    if (args.popup_type === 'product') {

        shopCTmageGallerySortable();
        shopCTproductTags.init();

        jQuery('.product-save').on('click', function () {
            jQuery('#product_content').val(wp.editor.getContent('product_content'));
            var formData = jQuery('#product_popup_form').serialize(),
                _btn = jQuery(this);
            jQuery.ajax({
                url: shopCTL10n.ajax_url,
                method: 'post',
                data: formData,
                dataType: 'json',
                beforeSend: function () {
                    _btn.attr("disabled", 'disabled');
                    _btn.parent().find(".spinner").css("visibility", "visible");
                }
            }).always(function () {
                _btn.removeAttr("disabled");
                _btn.parent().find(".spinner").css("visibility", "hidden");
            }).done(function (response) {
                shop_ct_popup.showToastr(shopCTL10n.product_published);
                shop_ct_popup.remove('shop_ct_popup_block');
                var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
                shopCT.changePage('', page_slug);
                jQuery('#product_popup_form').data('submited','true');
            }).fail(function (error) {
                shop_ct_popup.showToastr(shopCTL10n.serverSideError,'error');
            });
        });

        jQuery('.product-add-attribute').on('click', function () {
            var attr = jQuery('#product-new-attribute-taxonomy').val();

            jQuery.ajax({
                url: shopCTL10n.ajax_url,
                type: 'get',
                data: {
                    action: 'shop_ct_get_product_attribute_item',
                    product_id: jQuery('#product_id').val(),
                    attribute_taxonomy: attr,
                    nonce: shopCTL10n.shop_ct_nonce
                }
            }).done(function (result) {
                jQuery('.product-attributes-list').append(result);
                window.shopCtPopupMasonry.masonry();
            });
            return false;
        });

        jQuery('.product-attributes-list').on('click', '.product-attributes-delete', function () {
            jQuery(this).closest('.product-attributes-item').remove();
            window.shopCtPopupMasonry.masonry();
            return false;
        });


        var dates = jQuery('.product-datepicker').datepicker({
            defaultDate: '',
            dateFormat: 'yy-mm-dd',
            numberOfMonths: 1,
            showButtonPanel: true,
            onSelect: function (selectedDate) {
                var option = jQuery(this).is('#product_sale_price_dates_from') ? 'minDate' : 'maxDate';
                var instance = jQuery(this).data('datepicker');
                var date = jQuery.datepicker.parseDate(instance.settings.dateFormat || jQuery.datepicker._defaults.dateFormat, selectedDate, instance.settings);
                dates.not(this).datepicker('option', option, date);
                this.setAttribute('value', this.value);
            }
        });

        jQuery('.product-schedule-sale').on('click', function () {
            var datesWrap = jQuery('.product-sale-dates'),
                salePrice = parseFloat(jQuery('#product_sale_price').val()),
                regularPrice = parseFloat(jQuery('#product_regular_price').val());




            if (datesWrap.hasClass('-hide')) {
                if(salePrice.length =='' || isNaN(salePrice) || salePrice >= regularPrice){
                    shop_ct_popup.showToastr(shopCTL10n.invalidSalePrice,'error');
                    return false;
                }

                jQuery(this).text(jQuery(this).data('remove'));
                datesWrap.removeClass('-hide');
            } else {
                jQuery('#product_sale_price_dates_from,#product_sale_price_dates_to').val('');
                jQuery('#product_sale_price_dates_from_hours,#product_sale_price_dates_from_minutes, #product_sale_price_dates_to_hours, #product_sale_price_dates_to_minutes').val('00');
                jQuery(this).text(jQuery(this).data('original'));
                datesWrap.addClass('-hide');
            }
            window.shopCtPopupMasonry.masonry();
            return false;
        });
        wp.editor.initialize('product_content', {
            tinymce: {
                wpautop: true,
                plugins : 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
                toolbar1: 'formatselect bold italic | bullist numlist | blockquote | alignleft aligncenter alignright | link unlink | wp_more | spellchecker'
            },
            quicktags: true
        });


        jQuery('.product-image-gallery-add').on('click', function () {
            var _this = jQuery(this);
            var custom_uploader = wp.media({
                title: 'Add Images to Product Gallery',
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Add to gallery'
                },
                multiple: true  // Set this to true to allow multiple files to be selected
            })
                .on('select', function () {
                    var attachments = custom_uploader.state().get('selection').toJSON();
                    jQuery.each(attachments, function (i, attachment) {
                        var list = _this.closest('.product-image-gallery');
                        list.append('<li>\n' +
                            '<div class="product-image-gallery-inner">\n' +
                            '                        <img src="' + attachment.url + '"/>\n' +
                            '                        <button class="product-image-gallery-delete"><i class="fa fa-times"></i></button>\n' +
                            '                        <input type="hidden" name="post_meta[product_image_gallery][]" id="post_meta[product_image_gallery][]" value="' + attachment.id + '" />\n' +
                            '</div>\n' +
                            '                    </li>');

                    });
                    window.shopCtPopupMasonry.masonry();
                    shopCTmageGallerySortable();
                }).open();
            return false;
        });


        jQuery('.product-image-gallery').on('click', '.product-image-gallery-delete', function () {
            jQuery(this).closest('li').remove();
            return false;
        });

        jQuery( '.product-permalink-block' ).on( 'click', '.edit-slug', function() {
            editPermalink();
        });


        function editPermalink() {
            var $=jQuery,
                postId = jQuery('#product_id').val(),
                i, slug_value,
                $el, revert_e,
                c = 0,
                real_slug = jQuery('#product_name'),
                revert_slug = real_slug.val(),
                permalink = jQuery( '#sample-permalink' ),
                permalinkOrig = permalink.html(),
                permalinkInner = jQuery( '#sample-permalink a' ).html(),
                buttons = jQuery('#edit-slug-buttons'),
                buttonsOrig = buttons.html(),
                full = jQuery('#editable-post-name-full');

            // Deal with Twemoji in the post-name
            full.find( 'img' ).replaceWith( function() { return this.alt; } );
            full = full.html();

            permalink.html( permalinkInner );
            $el = jQuery( '#editable-post-name' );
            revert_e = $el.html();

            buttons.html( '<button type="button" class="save button button-small">' + shopCTL10n.ok + '</button> <button type="button" class="cancel button-link">' + shopCTL10n.cancel + '</button>' );
            buttons.children( '.save' ).click( function() {
                var new_slug = $el.children( 'input' ).val();

                if ( new_slug == jQuery('#editable-post-name-full').text() ) {
                    buttons.children('.cancel').click();
                    return;
                }
                jQuery.post(shopCTL10n.ajax_url, {
                    action: 'sample-permalink',
                    post_id: postId,
                    new_slug: new_slug,
                    new_title: jQuery('#product_title').val(),
                    samplepermalinknonce: jQuery('#samplepermalinknonce').val()
                }, function(data) {
                    var box = jQuery('#edit-slug-box');
                    box.html(data);
                    if (box.hasClass('hidden')) {
                        box.fadeIn('fast', function () {
                            box.removeClass('hidden');
                        });
                    }

                    buttons.html(buttonsOrig);
                    permalink.html(permalinkOrig);
                    real_slug.val(new_slug);
                    jQuery( '.edit-slug' ).focus();
                });
            });

            buttons.children( '.cancel' ).click( function() {
                jQuery('#view-post-btn').show();
                $el.html(revert_e);
                buttons.html(buttonsOrig);
                permalink.html(permalinkOrig);
                real_slug.val(revert_slug);
                jQuery( '.edit-slug' ).focus();
            });

            for ( i = 0; i < full.length; ++i ) {
                if ( '%' == full.charAt(i) )
                    c++;
            }

            slug_value = ( c > full.length / 4 ) ? '' : full;
            $el.html( '<input type="text" id="new-post-slug" value="' + slug_value + '" autocomplete="off" />' ).children( 'input' ).keydown( function( e ) {
                var key = e.which;
                // On enter, just save the new slug, don't save the post.
                if ( 13 === key ) {
                    e.preventDefault();
                    buttons.children( '.save' ).click();
                }
                if ( 27 === key ) {
                    buttons.children( '.cancel' ).click();
                }
            } ).keyup( function() {
                real_slug.val( this.value );
            }).focus();
        }


        jQuery('.product-add-downloadable-file').on('click',function(){
            var _this = jQuery(this),
                row = _this.data('row'),
                tbody = _this.closest('table').find('tbody');
            if(tbody.find('tr.no-items').length){
                tbody.find('tr.no-items').remove();

            }
            tbody.append(row);
            downloadablesSortable();
            window.shopCtPopupMasonry.masonry();
            return false;
        });

        function downloadablesSortable(){
            jQuery('.product-downloadable-files-block .ui-sortable').sortable({
                handle : '.sort',
                placeholder: "ui-sortable-placeholder-shop-ct",
            });
        }

        if(jQuery('.product-downloadable-files-block .ui-sortable').find('tr:not(.no-items)').length){
            downloadablesSortable();
        }

        jQuery('.product-downloadable-files-block').on('click','.upload_button',function(){
            var _this = jQuery(this);

            var custom_uploader = wp.media({
                title: shopCTL10n.addDownloadableFile,
                button: {
                    text: shopCTL10n.insertFile
                },
                multiple: false  // Set this to true to allow multiple files to be selected
            })
                .on('select', function() {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    _this.parent().find("input[type='url']").val(attachment.url);
                    if(!_this.parent().parent().find("input[type='text']").val().length){
                        _this.parent().parent().find("input[type='text']").val(attachment.name);
                    }

                })
                .open();

            return false;
        });

        jQuery('.product-downloadable-files-block').on('click','.remove i',function(){
            if(jQuery(this).closest('tbody').find('tr').length == 1)
                jQuery(this).closest('tbody').append('<tr class="no-items"><td colspan="7">'+shopCTL10n.noFiles+'</td></tr>');
            jQuery(this).closest('tr').remove();
        });

        function showHideProductOptions(){
            var is_virtual      = jQuery( '#product_virtual:checked' ).size();
            var is_downloadable = jQuery( '#product_downloadable:checked' ).size();
            var managing_stock = jQuery('#product_manage_stock:checked').size();

            var hide_classes = '.hide_if_downloadable, .hide_if_virtual';
            var show_classes = '.show_if_downloadable, .show_if_virtual, .show_if_managing_stock';

            jQuery( hide_classes ).css('display','block');
            jQuery( show_classes ).css('display','none');

            if ( is_downloadable ) {
                jQuery( '.show_if_downloadable' ).css('display','block');
                jQuery( '.hide_if_downloadable' ).css('display','none');
            }
            if ( is_virtual ) {
                jQuery( '.show_if_virtual' ).css('display','block');
                jQuery( '.hide_if_virtual' ).css('display','none');
            }

            if(managing_stock){
                jQuery('.show_if_managing_stock').css('display','block');
            }

            setTimeout(function(){
                window.shopCtPopupMasonry.masonry();
            },0);
        }

        showHideProductOptions();

        jQuery('#product_downloadable,#product_virtual,#product_manage_stock').on('change',function(){
            showHideProductOptions();
        });


    }

});

jQuery(window).on('shop_ct_popup:beforeClose', function (event, elementId, args) {

    if (args.popup_type === 'product') {
        var container = jQuery('#product_popup_form');
        console.log(container.data("changed"));

        var is_auto_draft = jQuery("#product_autodraft").val(),
            post_id;
        if(container.data("changed") == 'true' && container.data("submited") != 'true'){
            var exit = confirm(shopCTL10n.popupAlert);
            if(exit == false){
                window.shop_ct_popup_close = false;
            }else{
                if(is_auto_draft == 1){
                    post_id = jQuery('#product_id').val();
                    shopCT.products.delete_product(post_id,true);
                }
            }
        }else{
            if(container.data("submited") != 'true' && is_auto_draft == 1){
                post_id = jQuery('#product_id').val();
                shopCT.products.delete_product(post_id,true);
            }
        }

    }
});

window.addEventListener("beforeunload", function (event) {
    if(jQuery('#product_popup_form').length){

        var container = jQuery('#product_popup_form');

        if(container.data("changed") == 'true' && container.data("submited") != 'true'){
            var confirmationMessage = shopCTL10n.popupAlert;

            event.returnValue = confirmationMessage;

            return confirmationMessage;

        }
    }
});

jQuery(window).unload(function(event) {
    if(jQuery("#product_popup_form").length){
        var is_auto_draft = jQuery("#product_autodraft").val();
        if(is_auto_draft == 1){
            var post_id = jQuery('#product_id').val();
            shopCT.products.delete_product(post_id,true);
        }
    }
});


jQuery(window).on('shop_ct_popup:afterClose', function (event,elementId,args) {

    if(args.popup_type === 'product'){
        wp.editor.remove('product_content');
    }

    if (!jQuery('.shop-ct-product-cat-checklist').length) {
        return false;
    }

    jQuery.ajax({
        url: shopCTL10n.ajax_url,
        type: 'get',
        data: {action: 'shop_ct_get_product_cat_checklist', product_id: jQuery('#product_id').val()}
    }).done(function (result) {
        jQuery('.shop-ct-product-cat-checklist-items').html(result);
        window.shopCtPopupMasonry.masonry();
    });

});


function shopCTmageGallerySortable() {
    var _list = jQuery('.product-image-gallery');
    if (_list.length) {
        _list.sortable({
            opacity: 0.5,
            placeholder: "ui-shop-ct-image-placeholder",
            items: "li:not(.product-image-gallery-add)"
        });
    }
}

var shopCTarrayUniqueNoempty = function (array) {
    var out = [];

    jQuery.each(array, function (key, val) {
        val = jQuery.trim(val);

        if (val && jQuery.inArray(val, out) === -1) {
            out.push(val);
        }
    });

    return out;
};

var shopCTproductTags = {

    getTags: function () {
        var t = jQuery('.product-tags-list input').map(function () {
            return this.value;
        }).get();
        return t;
    },

    addTags: function () {
        var text = jQuery('#product_tags_input').val();

        if ('undefined' == typeof( text ) || '' === text) {
            return false;
        }

        var newTags = shopCTproductTags.clean(text).split(','),
            tags = shopCTproductTags.getTags();

        if (!newTags.length) {
            return false;
        }

        tags = tags ? jQuery.merge(tags, newTags) : newTags;
        tags = shopCTarrayUniqueNoempty(tags);
        shopCTproductTags.build(tags);
        jQuery('#product_tags_input').val('');
        window.shopCtPopupMasonry.masonry();
    },

    clean: function (tags) {
        return tags.replace(/\s*,\s*/g, ',').replace(/,+/g, ',').replace(/[,\s]+$/, '').replace(/^[,\s]+/, '');
    },

    build: function (tags) {
        jQuery('.product-tags-list').html('');

        jQuery.each(tags, function (i, tag) {
            jQuery('.product-tags-list').append('<span class="product-tag-item">\n' +
                '                            <input type="hidden" name="product_tags[]" value="' + tag + '" />\n' +
                '                            <span class="product-tag-item-name">' + tag + '</span>\n' +
                '                            <span class="product-tag-item-delete"><i class="fa fa-times"></i></span>\n' +
                '                        </span>');
        });


    },

    init: function () {
        jQuery('#product_tags_input').keypress(function (e) {
            if (13 == e.which) {
                e.preventDefault();
                shopCTproductTags.addTags();
            }
        });


        jQuery('.product-tags-add').on('click', function (e) {
            e.preventDefault();
            shopCTproductTags.addTags();
            return false;
        });

        jQuery('.product-tags-list').on('click', '.product-tag-item-delete', function (e) {
            e.preventDefault();
            jQuery(this).parent().remove();
            return false;
        });

    }

}
