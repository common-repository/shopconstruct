shopCT.reviews = {
    orderby_arr: ['author', 'in_response_to', 'submitted_on'],
    ordering_arr: ['ASC', 'DESC'],

    openPopup : function(id, parent_id) {

        var title;

        var data = {
            action: 'shop_ct_ajax',
            task : 'review_popup',
            nonce:shopCTL10n.shop_ct_nonce,
            id : id,
        };

        if (parent_id == undefined) {
            title = 'Edit Review';
        } else {
            title = 'Reply';
            data.parent_id = parent_id;
        }

        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            type : 'get',
            data : data,
            dataType : 'json',
            beforeSend: function() {
                shopCT.pageLoading();
            },
            success: function(result,status,xhr){
                if (result.success) {
                    shop_ct_popup.show("shop_ct_popup_block",{
                        'title': title,
                        'row_content': result.return_html
                    });
                    shopCT.pageLoaded();
                    if(data.parent_id !== undefined) {
                        jQuery('body').find('#popup-control-author_name, #popup-control-author_email, #popup-control-author_url').prop('readonly', true);
                    }
                } else {
                    console.log("no success");
                }
            },
            error: function(xhr, status, error) {
                console.log(error);
                shopCT.pageLoaded();
            }
        });
    },
};

jQuery(document).ready(function() {

    function handle_borders() {
        var rows = jQuery('body').find('#shop_ct_review_form .shop_ct_review_table tbody tr');
        jQuery(rows).each(function (key, value) {
            var x = jQuery(value).children('.column-table_actions').children('i')[0];
            if(jQuery(x).attr('data-action') == 'approve' && !jQuery(x).hasClass('spam')) {
                jQuery(value).css('background-color', '#fef7f1');
                jQuery(value).children('th').css('border-left', '4px solid #d54e21');
            } else if( jQuery(x).hasClass('spam') ) {
                jQuery(value).children('th').css('border-left', '4px solid rgb(187, 47, 0)');
                jQuery(value).css('background-color', 'rgb(243, 225, 209)');
            }
        });
    }

    handle_borders();

    jQuery(window).on('shopCT:load', function() {
        handle_borders();
    });

    jQuery('body').on('click', '.shop_ct_review_table th a', function () {

        var link = jQuery(this).attr("href");
        var slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");

        shopCT.changePage(link,slug);
        return false;
    });

    var current_review_id;
    var parent_id;

    jQuery('body').on('click', '.shop_ct_review_content', function() {
        var id = jQuery(this).parent().siblings('th').children('input').val();
        current_review_id = id;

        shopCT.reviews.openPopup(id);
        return false;
    });

    jQuery('body').on('click', '#popup-control-update_button', function() {

        var form_data = {
            author_name: jQuery('#popup-control-author_name').val(),
            author_email: jQuery('#popup-control-author_email').val(),
            author_url: jQuery('#popup-control-author_url').val(),
            status: jQuery('#control-container-status_radio input:radio:checked').val(),
            content: jQuery('#popup-control-content').val(),
            rating: jQuery('#shop-ct-control-rating_dropdown').val() ? jQuery('#shop-ct-control-rating_dropdown').val() : 0
        };
        var data = {
            comment_id: current_review_id,
            action: 'shop_ct_ajax',
            task : 'update_review',
            nonce:shopCTL10n.shop_ct_nonce,
            form_data : form_data,
        };

        if (parent_id !== undefined) {
            data.parent_id = parent_id;
        }

        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            type: 'POST',
            data: data,
            dataType: 'JSON'
        }).done(function (data) {
            var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
            shop_ct_popup.remove("shop_ct_popup_block");
            shopCT.changePage('', page_slug);
            current_review_id = undefined;
            parent_id = undefined;
        }).fail(function (data, error) {
            console.log(error);
        });

        return false;
    });

    /**
     * Table actions.
     */
    jQuery('body').on('click', '.reviews_table_action', function() {
        var action = jQuery(this).attr('data-action');
        var id = jQuery(this).parent().siblings('th').children('input:checkbox').val();
        var ajaxData = {
            action: 'shop_ct_ajax',
            nonce:shopCTL10n.shop_ct_nonce,
        };

        ajaxData.id = id;
        ajaxData.task = 'change_review_status';
        ajaxData.status = action;

        switch (action) {
            case 'edit_view' :
                current_review_id = id;
                shopCT.reviews.openPopup(id);
                break;
            case 'reply' :
                parent_id = id;
                shopCT.reviews.openPopup('reply', id);
                break;
            default :
                if(action === 'trash') {
                    jQuery( "#shop_ct_error_dialog_wrapper" ).html("<p>Are you sure you want to delete this review ?</p>");
                    jQuery( "#shop_ct_error_dialog_wrapper" ).dialog({
                        dialogClass:'shop_ct_error_dialog',
                        draggable: false,
                        resizable: false,
                        closeOnEscape:true,
                        hide : 100,
                        height: 300,
                        modal: true,
                        closeText: '<span class="dashicons dashicons-no"></span>',
                        position : { my: "center", at: "center", of: "#shop_ct_wrapper" },
                        title : 'Delete Reviews',
                        buttons: {
                            "YES": function() {
                                var _this = jQuery(this);
                                _this.dialog("close");
                                jQuery(window).trigger("shop_ct_reviews:row_actions", ajaxData);
                            },
                            "NO": function(){
                                jQuery(this).dialog("close");
                                return false;
                            }
                        }
                    });
                } else {
                    jQuery(window).trigger("shop_ct_reviews:row_actions", ajaxData);
                }
        }
    });

    jQuery(window).on('shop_ct_reviews:row_actions', function(event, ajaxData) {

        if (ajaxData.status !== undefined && ajaxData.task !== undefined) {
            jQuery.ajax({
                url: shopCTL10n.ajax_url,
                type: 'POST',
                data: ajaxData,
                dataType: 'JSON',
            }).done(function () {
                var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
                shopCT.changePage('', page_slug);
            }).fail(function (data, error) {
                console.log(error);
            })
        }

    });

    jQuery('body').on('click', '#shop_ct_review_form #doaction-top , #shop_ct_review_form ~ div #doaction-bottom', function () {
        var action = jQuery(this).siblings('select').val();
        var task;
        var ids = [];
        var rts = [];
        var checked_checkboxes = jQuery('#shop_ct_review_form table tbody th input:checked');
        var status;
        var ajaxData = {};

        for(var i = 0; i < checked_checkboxes.length; i++) {
            ids.push(jQuery(checked_checkboxes[i]).val());

            var x = jQuery(checked_checkboxes[i]).parent().siblings('td.column-rating').text();

            jQuery.isNumeric(x) ? rts.push(x) : rts.push('');
        }

        switch ( action ) {
            case 'bulk_actions' :
                return false;
                break;

            case 'trash' :
                task = 'change_review_status';

                break;

            case 'approve' :
                task = 'change_review_status';
                status = 'approve';

                break;

            case 'hold' :
                task = 'change_review_status';
                status = 'hold';

                break;

            case 'spam' :
                task = 'change_review_status';
                status = 'spam';

                break;
        }

        if( task !== undefined && status === undefined ) {
            ajaxData = {
                action: 'shop_ct_ajax',
                task: task,
                nonce: shopCTL10n.shop_ct_nonce,
                ids: ids,
                rts: rts,
            }
        } else if( task !== undefined && status !== undefined) {
            ajaxData = {
                action: 'shop_ct_ajax',
                task: task,
                nonce: shopCTL10n.shop_ct_nonce,
                ids: ids,
                status: status,
                rts: rts,
            }
        }

        if( !isEmpty(ajaxData) ) {

            if(ajaxData.status === undefined) {
                jQuery( "#shop_ct_error_dialog_wrapper" ).html("<p>Are you sure you want to delete these reviews ?</p>");
                jQuery( "#shop_ct_error_dialog_wrapper" ).dialog({
                    dialogClass:'shop_ct_error_dialog',
                    draggable: false,
                    resizable: false,
                    closeOnEscape:true,
                    hide : 100,
                    height: 300,
                    modal: true,
                    closeText: '<span class="dashicons dashicons-no"></span>',
                    position : { my: "center", at: "center", of: "#shop_ct_wrapper" },
                    title : 'Delete Reviews',
                    buttons: {
                        "YES": function() {
                            var _this = jQuery(this);
                            _this.dialog("close");
                            jQuery(window).trigger("shop_ct_reviews:bulk_actions", ajaxData);
                        },
                        "NO": function(){
                            jQuery(this).dialog("close");
                            return false;
                        }
                    }
                })
            } else {
                jQuery(window).trigger("shop_ct_reviews:bulk_actions", ajaxData);
            }
        }
    });

    jQuery(window).on('shop_ct_reviews:bulk_actions', function (event, ajaxData) {
        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: ajaxData,
        }).done(function() {
            var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
            shopCT.changePage('', page_slug);
        }).fail(function (data, error) {
            console.log(data, error);
        });
    });
});