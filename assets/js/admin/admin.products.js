shopCT.products = {

    openPopup: function (id) {

        var data = {
            action: 'shop_ct_ajax',
            task: 'product_popup',
            nonce: shopCTL10n.shop_ct_nonce,
            id: id
        };

        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            type: 'get',
            data: data,
            dataType: 'json',
            beforeSend: function () {
                shopCT.pageLoading();
            }
        }).done(function (result) {
            if (result.success) {
                shop_ct_popup.show("shop_ct_popup_block", {
                    'title': result.title,
                    'row_content': result.return_html,
                    'popup_type': 'product'
                });
                shopCT.pageLoaded();
            }
        }).fail(function () {
            shopCT.pageLoaded();
        });

    },
    delete_product: function (id, force_delete, reload_page) {

        force_delete = typeof force_delete != 'undefined' ? force_delete : false;
        reload_page = typeof reload_page != 'undefined' ? reload_page : false;

        var data = {
            action: 'shop_ct_ajax',
            task: 'delete_product',
            nonce: shopCTL10n.shop_ct_nonce,
            id: id,
            force_delete: force_delete
        };

        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            type: 'get',
            data: data,
            dataType: 'json',
            beforeSend: function (xhr) {
            },
            success: function (result, status, xhr) {
                if (reload_page) {
                    var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
                    shopCT.changePage('', page_slug);
                }
            },
            error: function (xhr, status, error) {
            }
        });
    },
    trash_product: function (id) {

        var data = {
            action: 'shop_ct_ajax',
            task: 'trash_product',
            nonce: shopCTL10n.shop_ct_nonce,
            id: id
        };
        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            type: 'get',
            data: data,
            async: false,
            dataType: 'json',
            beforeSend: function (xhr) {
            },
            success: function (result, status, xhr) {
                if (result.success) {
                    var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
                    shopCT.changePage('', page_slug);
                }
            },
            error: function (xhr, status, error) {
            }
        });
    },
    untrash_product: function (id) {
        var data = {
            action: 'shop_ct_ajax',
            task: 'untrash_product',
            nonce: shopCTL10n.shop_ct_nonce,
            id: id
        };
        jQuery.ajax({
            url: shopCTL10n.ajax_url,
            type: 'get',
            data: data,
            async: false,
            dataType: 'json',
            beforeSend: function (xhr) {
            },
            success: function (result, status, xhr) {
                if (result.success) {
                    var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
                    shopCT.changePage('', page_slug);
                }
            },
            error: function (xhr, status, error) {
            }
        });
    },
    apply_bulk_action: function (el, action, form_id) {
        var checked_ids = [],
            dialog_wrapper = jQuery("#shop_ct_error_dialog_wrapper");

        jQuery.each(jQuery("#" + form_id + " table input[type='checkbox']:checked"), function () {
            checked_ids.push(jQuery(this).val());
        });

        switch (action) {
            case "trash":
                shopCT.products.trash_product(checked_ids);
                break;
            case "restore":
            case "untrash":
                shopCT.products.untrash_product(checked_ids);
                break;
            case "delete":
                dialog_wrapper.html("<p>" + shopCTL10n.ask_delete_products + "</p>");
                dialog_wrapper.dialog({ // ALERTING ARE YOU SURE DIALOG
                    dialogClass: 'shop_ct_error_dialog',
                    draggable: false,
                    resizable: false,
                    closeOnEscape: true,
                    hide: 100,
                    height: 300,
                    modal: true,
                    closeText: '<span class="dashicons dashicons-no"></span>',
                    position: {my: "center", at: "center", of: "#shop_ct_wrapper"},
                    title: 'Delete Products',
                    buttons: {
                        "YES": function () {
                            jQuery(this).dialog("close");
                            shopCT.products.delete_product(checked_ids, true, true);
                            return false;
                        },
                        "NO": function () {
                            jQuery(this).dialog("close"); //simply closes the dialog box
                        }
                    }
                });
                break;
            default:
                return false;
                break;
        }
    }
};

function admin_product_handling() {

    jQuery("body").on("click", "#shop_ct_add_product_btn", function () {
        shopCT.products.openPopup();
        return false;
    });


    jQuery("body").on("click", "#shop_ct_products_list_table table tbody .row-title", function () {
        var id = jQuery(this).parent().parent().parent().find('.shop-ct-check-column input[type="checkbox"]').val();
        shopCT.products.openPopup(id);
        return false;
    });

    jQuery("body").on("click", "#shop_ct_products_list_table table tbody .edit a", function () {
        var id = jQuery(this).parent().parent().parent().parent().find('.shop-ct-check-column input[type="checkbox"]').val();
        shopCT.products.openPopup(id);
        return false;
    });

    jQuery("body").on("click", "#shop_ct_products_list_table table tbody .trash a", function () {
        var id = jQuery(this).parent().parent().parent().parent().find('.shop-ct-check-column input[type="checkbox"]').val();
        shopCT.products.trash_product(id);
        return false;
    });

    jQuery("body").on("click", "#shop_ct_products_list_table table tbody .untrash a", function () {
        var id = jQuery(this).parent().parent().parent().parent().find('.shop-ct-check-column input[type="checkbox"]').val();
        shopCT.products.untrash_product(id);
        return false;
    });

    jQuery("body").on("click", "#shop_ct_products_list_table #doaction-top,#shop_ct_products_list_table #doaction-bottom", function () {
        var el = jQuery(this),
            action = jQuery(this).parent().find('select').val(),
            form_id = "shop_ct_products_list_table";

        if (action != "bulk_actions")
            shopCT.products.apply_bulk_action(el, action, form_id);
        return false;
    });

    jQuery("body").on("click", "#shop_ct_products_list_table #post-query-submit", function () {
        var new_params = new Array(),
            old_url = window.location.href,
            new_url = shopCT.deleteQueryArgs(['m', 'cat', 'paged'], old_url);
        if (jQuery("#filter-by-date").val() != '0') new_params.m = jQuery("#filter-by-date").val();
        if (jQuery("#cat").val() != '0') new_params.cat = jQuery("#cat").val();

        var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
        if (!shopctIsEmpty(new_params)) {
            new_url = shopCT.addQueryArgs(new_params, new_url);
            shopCT.changePage(new_url, page_slug);
        } else {
            shopCT.changePage(new_url, page_slug);
        }
        return false;
    });
}

jQuery(window).ready(function () {

    admin_product_handling();





});
