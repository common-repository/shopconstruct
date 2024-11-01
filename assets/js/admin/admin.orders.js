shopCT.orders = {
    openPopup : function(id) {
        var title;

        if(id) {
            title = "Edit Order";
        } else {
            id = "new";
            title = " Add Order ";
        }

        var data = {
            action: 'shop_ct_ajax',
            task : 'order_popup',
            nonce:shopCTL10n.shop_ct_nonce,
            id : id,
        };

        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            type : 'get',
            data : data,
            dataType : 'json',
            beforeSend: function() {
                shopCT.pageLoading();
            },
            success: function(result){
                if (result.success) {
                    shop_ct_popup.show("shop_ct_popup_block",{
                        'title': title,
                        'row_content': result.return_html,
	                    'popup_type': 'order'
                    });
                    shopCT.pageLoaded();
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

    showOrderPopupAndReturnAjax: function (id) {

        if(!id) {
            id = "new";
        }

        var data = {
            action: 'shop_ct_ajax',
            task : 'order_popup',
            nonce:shopCTL10n.shop_ct_nonce,
            id : id,
        };

        var ajax = jQuery.ajax({
            url: shopCTL10n.ajax_url,
            type : 'get',
            data : data,
            dataType : 'json',
            beforeSend: function() {
                shopCT.pageLoading();
            }
        }).fail(function (xhr, status, error) {
            console.log(error);
            shopCT.pageLoaded();
        });

        return ajax;
    }
};

jQuery(document).ready(function() {
    var Body = jQuery('body');

    /**
     * Make table ordering asynchronous
     */
    Body.on('click', '.shop_ct_order_table th a', function () {

        var link = jQuery(this).attr("href");
        var slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");

        shopCT.changePage(link, slug);
        return false;
    });

    /**
     * Order popup
     */
    Body.on('click', '#shop_ct_add_order_btn', function() {
        var ajax = shopCT.orders.showOrderPopupAndReturnAjax();

        ajax.done(function(result) {
            if (result.success) {
                var title = 'Add order';
                shop_ct_popup.show("shop_ct_popup_block",{
                    'title': title,
                    'row_content': result.return_html,
	                'popup_type': 'order'
                });
                shopCT.pageLoaded();
            } else {
                console.log("no success");
            }
        });

        return false;
    });

    /**
     * Edit/View order.
     */
    Body.on('click', '#shop_ct_order_form .edit, #shop_ct_order_form .row-title, #shop_ct_order_form .edit-view-order', function() {

        var order_id = jQuery(this).parent().parent().siblings('th').children('input').attr('value');

        if(order_id === undefined) {
            order_id = jQuery(this).parent().siblings('th').children('input').attr('value');
        }

        var ajax = shopCT.orders.showOrderPopupAndReturnAjax(order_id);

        ajax.done(function (result) {
            if (result.success) {
                shop_ct_popup.show("shop_ct_popup_block",{
                    'title': result.title,
                    'row_content': result.return_html,
	                'popup_type': 'order'
                });
                shopCT.pageLoaded();
            } else {
                console.log("no success");
            }
        });

        return false;
    });

    /**
     * Bulk actions.
     */
    Body.on('click', '#shop_ct_order_form #doaction-top, #shop_ct_order_form #doaction-bottom', function () {
        var action = jQuery(this).siblings('select').val();
        var task;
        var ids = [];
        var checked_checkboxes = jQuery('#shop_ct_order_form').find('table tbody th input:checked');
        var status;
        var ajaxData = {};

        for(var i = 0; i < checked_checkboxes.length; i++) {
            ids.push(jQuery(checked_checkboxes[i]).val());
        }

        if (ids.length == 0) {
            return false;
        }


        switch ( action ) {
            case 'bulk_actions' :
                break;

            case 'delete' :
                task = 'delete_order';

                break;

            case 'complete' :
                task = 'change_order_status';
                status = 'shop-ct-completed';

                break;

            case 'processing' :
                task = 'change_order_status';
                status = 'shop-ct-processing';

                break;

            case 'on_hold' :
                task = 'change_order_status';
                status = 'shop-ct-on-hold';

                break;
        }

        if( task == 'delete_order' && status === undefined ) {
            ajaxData = {
                action: 'shop_ct_ajax',
                task: task,
                nonce: shopCTL10n.shop_ct_nonce,
                ids: ids,
            }
        } else if( task !== undefined && status !== undefined) {
            ajaxData = {
                action: 'shop_ct_ajax',
                task: task,
                nonce: shopCTL10n.shop_ct_nonce,
                ids: ids,
                status: status,
            }
        }

        if( !isEmpty(ajaxData) ) {

            if(ajaxData.task === 'delete_order') {

                jQuery( "#shop_ct_error_dialog_wrapper" ).html("<p>Are you sure you want to delete these orders ?</p>");
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
                    title : 'Delete ' + (ajaxData.ids.length == 1 ? 'Order' : 'Orders'),
                    buttons: {
                        "YES": function() {
                            var _this = jQuery(this);
                            _this.dialog("close");
                            task = 'change_gift_status';
                            status = 'delete';
                            jQuery(window).trigger("shop_ct_orders:row_actions", ajaxData);
                        },
                        "NO": function(){
                            jQuery(this).dialog("close"); //simply closes the dialog box
                            return false;
                        }
                    }
                });

            } else {
                jQuery(window).trigger("shop_ct_orders:row_actions", ajaxData);
            }

            return false;
        }
    });

    jQuery(window).on('shop_ct_orders:row_actions', function (event, ajaxData) {
        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: ajaxData,
        }).done(function() {
            var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
            shopCT.changePage('',page_slug);
        });
    });

    /**
     * Change single order status.
     */
    Body.on('click', 'i.shop-ct-order-actions', function() {
        var id = jQuery(this).parent().siblings('th').children('input').val();
        var status = jQuery(this).attr('data-status');

        var ajaxData = {
            action: 'shop_ct_ajax',
            task: 'change_order_status',
            nonce: shopCTL10n.shop_ct_nonce,
            id: id,
            status: status,
        };

        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            type: 'POST',
            dataType: 'JSON',
            data: ajaxData,
        }).done(function() {
            var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
            shopCT.changePage('',page_slug);
        });

    });




});