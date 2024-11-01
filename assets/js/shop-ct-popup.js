/**
 * Popup opbject for shopConstruct
 */

'use strict';
var shop_ct_popup = {

    elems: {},
    toastElements: [],
    toastInterval: false,

    html: function (id, title, content, args) {
        var title_html = '',
            content_html = '';

        if (typeof args.closeAnimation !== 'undefined') {
            shop_ct_popup['elems'][id]['closeAnimation'] = args.closeAnimation;
        } else {
            shop_ct_popup['elems'][id]['closeAnimation'] = 'zoomOut';
        }

        if(title !== ''){
            title_html ='<div class="shop_ct_window_title_block"><div class="shop_ct_window_title">' + title + '</div><div class="shop_ct_window_close_btn"><span class="close_icon dashicons dashicons-no"></span></div></div>';
            content_html = '<div class="shop_ct_window_content">' + content + '</div>';
        }else{
            content_html = content;
        }

        return '<div class="shop-ct-popup-outer-window" id="shop_ct_popup_window-' + id + '">' +
            '<div id="shop_ct_window_overlay-' + id + '" class="shop_ct_window_overlay" data-id="' + id + '" ></div>' +
            '<div class="shop_ct_popup_window shop_ct_window ajax_window animated"><div class="shop_ct_popup_window_inner">' +
            title_html+
            content_html+
            '</div></div></div>';
    },

    prepareDOM: function (elementId) {
        var dfd = jQuery.Deferred(),
            containerDom = jQuery("#" + elementId);

        if (!containerDom.length) {
            jQuery('body').append('<div id="' + elementId + '"></div>');
        }
        setTimeout(function () {
            dfd.resolve();
        }, 0);

        return dfd.promise();
    },

    show: function (element_id, args) {
        var content,
            title;

        shop_ct_popup.prepareDOM(element_id).then(function () {
            shop_ct_popup['elems'][element_id] = {};
            shop_ct_popup['elems'][element_id]['container'] = jQuery("#" + element_id);
            shop_ct_popup['elems'][element_id]['container_html'] = jQuery("#" + element_id).prop('outerHTML');

            /**
             * specify custom content, or place it inside the container that will be replaced with popup
             */
            if (typeof args.row_content !== 'undefined') {
                content = args.row_content
            } else {
                content = shop_ct_popup['elems'][element_id]['container'].html();
            }

            if (typeof args.title !== 'undefined') {
                title = args.title
            } else {
                title = '';
            }

            content = shop_ct_popup.html(element_id, title, content, args);

            shop_ct_popup['elems'][element_id]['container'].replaceWith(content);
            /** give height/width to popup */
            var css_obj = {};
            if (typeof args.width !== 'undefined') {
                css_obj.width = args.width;
            }

            if (typeof args.height !== 'undefined') {
                css_obj.height = args.height;
            }

	        if (typeof args.css !== 'undefined') {
		        css_obj = args.css;
	        }

            shop_ct_popup['elems'][element_id]['args'] = args;
            var popup_elem = jQuery("body").find("#shop_ct_popup_window-" + element_id);

            popup_elem.find('.shop_ct_popup_window').css(css_obj);
            /** give data attributes to the form to know whether it is changed/submited when closing the popup */
            if (popup_elem.find('form').length) {
                popup_elem.find('form').data("changed", 'false');
                popup_elem.find('form').data("submited", 'false');
                /** when changing inputs inside the form change the data attribute 'changed' to true */
                popup_elem.find('form').find(':input').change(function () {
                    popup_elem.find('form').data("changed", 'true');

                });
                /** when submit button is clicked change the data attribute 'submited' to true */
                popup_elem.find('form').find('input[type=submit]').on('click', function () {
                    popup_elem.find('form').data("submited", 'true');
                    popup_elem.find('form').data("changed", 'false');
                });
            }


            var animation = args.animation || 'zoomIn';
            shop_ct_popup['elems'][element_id]['animation'] = animation;


            jQuery(window).trigger("shop_ct_popup:rendered", [element_id,args]);

            /** Animate */
            popup_elem.find('.shop_ct_popup_window').addClass(animation).one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
                /** Call event handlers for popup ready event */
                jQuery(window).trigger("shop_ct_popup:ready", [element_id,args]);

                /** on close icon, overlay click close the popup */
                jQuery("#shop_ct_popup_window-" + element_id + " .close_icon, #shop_ct_window_overlay-" + element_id).on("click", function () {
                    shop_ct_popup.remove(element_id);
                });
            });



        });


    },

    remove: function (element_id) {
        window.shop_ct_popup_close = true;
        setTimeout(function () {
            var args = shop_ct_popup['elems'][element_id]['args'];
            jQuery.when(jQuery(window).triggerHandler('shop_ct_popup:beforeClose',[element_id,args])).done(function () {
                if (window.shop_ct_popup_close) {
                    var _body = jQuery("body");
                    _body.find("#shop_ct_window_overlay-" + element_id).fadeOut(500);
                    var animation = shop_ct_popup['elems'][element_id]['animation'];
                    var closeAnimation = shop_ct_popup['elems'][element_id]['closeAnimation'];
                    _body.find("#shop_ct_popup_window-" + element_id).removeClass(animation).addClass(closeAnimation).one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
                        _body.find("#shop_ct_window_overlay-" + element_id).remove();
                        _body.find("#shop_ct_popup_window-" + element_id).replaceWith(shop_ct_popup['elems'][element_id]['container_html']);
                        delete window.shop_ct_popup_close;
                        delete shop_ct_popup['elems'][element_id];
                        jQuery(window).trigger("shop_ct_popup:afterClose", [element_id,args]);
                    });
                }
            });
        }, 200);
    },

    showToastr: function (text, type) {
        type= type||'success';
        text = text||'Success!';

        if(jQuery('.shop-ct-toastr').length >= 4){
            clearInterval(shop_ct_popup.toastInterval);
            shop_ct_popup.toastInterval = false;
            shop_ct_popup.removeToastr();
        }

        if(!jQuery('.shop-ct-toastr-wrap').length){
            jQuery('body').append('<div class="shop-ct-toastr-wrap"></div>');
        }

        jQuery(".shop-ct-toastr-wrap").append("<div class='shop-ct-toastr "+type+"'>" + text + "</div>");

        if(!shop_ct_popup.toastInterval){
            shop_ct_popup.toastInterval = setInterval(function () {
                shop_ct_popup.removeToastr();
            }, 5000);
        }

    },

    removeToastr: function(){
        if(jQuery('.shop-ct-toastr').length === 1){
            clearInterval(shop_ct_popup.toastInterval);
            shop_ct_popup.toastInterval = false;
        }
        jQuery('.shop-ct-toastr').first().addClass('shop-ct-close-toastr').on("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend",function(){
            jQuery(this).remove();
        });
    }
};

jQuery(document).ready(function () {
    jQuery('body').on('click', '.shop-ct-img-control', function () {
        var _this = jQuery(this);
        if (_this.hasClass("add_image_control")) {
            var custom_uploader = wp.media({
                title: 'Image Gallery',
                library: {
                    type: 'image'
                },
                button: {
                    text: 'Add Image Gallery Item'
                },
                multiple: false  // Set this to true to allow multiple files to be selected
            })
                .on('select', function () {
                    var attachment = custom_uploader.state().get('selection').first().toJSON();
                    _this.parent().parent().find(".popup-control-value").val(attachment.id);

                    if (_this.find("img").length) {
                        _this.find("img").attr("src", attachment.url);
                    } else {
                        _this.removeClass("add_image_control").addClass("remove_control_image");
                        _this.html(_this.data("remove_text"));
                        _this.parent().parent().prepend('<p><a href="#" class="add_image_control shop-ct-img-control"><img src="' + attachment.url + '" alt="" /></a></p>')
                    }
                })
                .open();
        } else if (_this.hasClass("remove_control_image")) {
            _this.parent().parent().find(".popup-control-value").val("");
            _this.parent().parent().find("img").parent().parent().remove();
            _this.html(_this.data("add_new_text"));
            _this.removeClass("remove_control_image").addClass("add_image_control");
            return false;
        }

        return false;
    });
});