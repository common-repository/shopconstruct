var shopCT = {

	init: function() {
		shopCT.sessions.getSessions();

		if (jQuery('#shop_ct_wrapper').find('.select2').length) {
			jQuery('#shop_ct_wrapper').find('.select2').select2();
		}

		if (jQuery('#shop_ct_wrapper').find('.shop_ct_datepicker').length) {
			jQuery('#shop_ct_wrapper').find('.shop_ct_datepicker').datepicker()
		}
	},

	getSearchParams: function() {
		var query_string = {};

		var query = window.location.search.substring(1);

		var vars = query.split("&");

		for (var i = 0; i < vars.length; i++) {

			var pair = vars[i].split("=");

			// If first entry with this name
			if (typeof query_string[pair[0]] === "undefined") {

				query_string[pair[0]] = decodeURIComponent(pair[1]);
				// If second entry with this name

			} else if (typeof query_string[pair[0]] === "string") {

				var arr = [query_string[pair[0]], decodeURIComponent(pair[1])];

				query_string[pair[0]] = arr;
				// If third or later entry with this name

			} else {
				query_string[pair[0]].push(decodeURIComponent(pair[1]));
			}
		}
		return query_string;
	},

	getLinkParams: function(link) {
		var query_string = {};

		var regexp = /\?([\s\S]*)/g;

		var search_query = link.match(regexp);

		if (typeof search_query === "object") {
			var query = search_query[0];

			var vars = query.split("&");

			for (var i = 0; i < vars.length; i++) {

				var pair = vars[i].split("=");

				// If first entry with this name
				if (typeof query_string[pair[0]] === "undefined") {

					query_string[pair[0]] = decodeURIComponent(pair[1]);
					// If second entry with this name

				} else if (typeof query_string[pair[0]] === "string") {

					var arr = [query_string[pair[0]], decodeURIComponent(pair[1])];

					query_string[pair[0]] = arr;
					// If third or later entry with this name

				} else {
					query_string[pair[0]].push(decodeURIComponent(pair[1]));
				}
			}
		}

		return query_string;
	},

	getLinkUrl: function(link) {
		var regexp = /([\s\S]*?)\?/g,
			search_query = regexp.exec(link);

		return typeof search_query === "object" && search_query !== null ? search_query[1] : false;
	},

	pageLoadingCrushed: function(message) {
		jQuery("#shop_ct_error_dialog_wrapper").html("<p>While Loading The Page an error occurred `</br>" + message + "");
		jQuery("#shop_ct_error_dialog_wrapper").dialog({ // ALERTING ARE YOU SURE DIALOG
			dialogClass: 'shop_ct_error_dialog',
			draggable: false,
			resizable: false,
			closeOnEscape: true,
			hide: 100,
			height: 300,
			modal: true,
			closeText: 'X',
			position: {my: "center", at: "center", of: "#shop_ct_wrapper"},
			title: 'Oops',
			buttons: {
				"Reload Page": function() { // ID USER CLICKED YES I SURE
					location.reload();
				},
			}
		});

	},

	addQueryArgs: function(args, url) {
		if (shopctIsEmpty(args)) {
			return url;
		}

		var params = shopCT.getLinkParams(url),
			new_url = shopCT.getLinkUrl(url),
			new_params = [];

		for (var k in args) {
			params[k] = args[k];
		}

		for (var i in params) {
			new_params.push(i + "=" + params[i]);
		}

		return new_url + new_params.join("&");
	},

	deleteQueryArgs: function(args, url) {
		if (shopctIsEmpty(args)) {
			return url;
		}

		var params = shopCT.getLinkParams(url),
			new_url = shopCT.getLinkUrl(url),
			new_params = [];

		for (var k in args) {
			if (params.hasOwnProperty(args[k])) {
				delete params[args[k]];
			}
		}
		for (var i in params) {
			new_params.push(i + "=" + params[i]);
		}

		return new_url + new_params.join("&");
	},

	pageLoaded: function() {
		jQuery("#shop_ct_wrapper").removeClass("preloading");
        jQuery(window).triggerHandler("shopCTPage:load");
	},

	pageLoading: function() {
		jQuery("#shop_ct_wrapper").addClass("preloading");
	},

	changePage: function(link, slug) {
		var _this = this,
			params,
			key;
		_this.page_data = {
			action: 'shop_ct_ajax',
			task: 'get_page_contents',
			slug: slug,
			nonce: shopCTL10n.shop_ct_nonce
		};
		if (link === "") {
			params = shopCT.getLinkParams(window.location.href);
		} else {
			params = shopCT.getLinkParams(link);
			history.pushState({}, 'shopCT Page ' + slug, link);
		}

		for (var k in params) {
			key = k.replace("?", "");
			window['shopCT']['page_data'][key] = params[k];
		}

		return jQuery.ajax({
			url: shopCTL10n.ajax_url,
			type: 'get',
			data: _this.page_data,
			dataType: 'json',
			beforeSend: function() {
				jQuery(".shop_ct_ajax_bind_html").html("");
				shopCT.pageLoading();
			}
		}).done(function(result) {
			/* if ajax return page content print it or open dialog with error message */
			if (result.success) {
				jQuery(".shop_ct_ajax_bind_html").html(result.page_contents);
				document.title = result.title;

				if (typeof screenMeta != "undefined") {
					screenMeta.init();
				}

				shopCT.init();
				shopCT.pageLoaded();
				setTimeout(function(){
                    jQuery(window).triggerHandler("shopCT:load");
                    jQuery(window).triggerHandler("shopCT:load_" + slug, [slug]);
				},0);

			} else if (result.error) {
				shopCT.pageLoaded();
				shopCT.pageLoadingCrushed(result.error);
			} else {
				shopCT.pageLoaded();
				shopCT.pageLoadingCrushed(result);
			}
			jQuery('html, body').animate({scrollTop: 0}, 0);
		}).fail(function(xhr, status) {
			shopCT.pageLoaded();
			shopCT.pageLoadingCrushed(status);
		});
	},


	reloadPage: function() {
		var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
		shopCT.changePage('', page_slug);
	},

	adminpanel: {

		activate_links: function(slug) {
			var active_item;
			jQuery(".shop_ct_adminbar").find(".shop-ct-link").each(function(){
				if(jQuery(this).data("slug") == slug){
					active_item = jQuery(this);
				}
			});

			jQuery(".shop_ct_adminbar").find(".current").removeClass("current");
			active_item.parent().addClass("current");

			if (active_item.parent().hasClass("sub_item")) {
				active_item.parentsUntil("li.sub_page").parent().parent().addClass("current");
				active_item.parentsUntil("ul.shop_ct_submenu").parent().addClass("current");
			}

			if (active_item.parent().hasClass("first_sub_item")) {
				active_item.parent().parent().parent().addClass("current");
			}

			if (active_item.parent().hasClass('sub_page')) {
				active_item.parent().find(".first_sub_item").addClass("current");
			}
		}

	},

	sessions: {

		sessions: {},

		setSession: function(name, value) {
			eval('shopCT.sessions.sessions.' + name + '=' + JSON.stringify(value));
			var data = {
				action: 'shop_ct_ajax',
				task : 'set_session',
				nonce: shopCTL10n.shop_ct_nonce,
				name : name,
				value : value
			};

			jQuery.ajax({
			  url: shopCTL10n.ajax_url,
			  type : 'get',
			  data : data,
			  dataType : 'json',
			}).done(function() {
				shopCT.sessions.getSessions();
			}).fail(function(xhr, status, error) {
				console.log(error)
			});
		},

		getSessions: function() {
			var data = {
			  action: 'shop_ct_ajax',
			  task : 'get_sessions',
			  nonce:shopCTL10n.shop_ct_nonce,
			};

			jQuery.ajax({
			  url: shopCTL10n.ajax_url,
			  type : 'get',
			  data : data,
			  dataType : 'json',
			}).done(function(result) {
				if (result.success) {
					shopCT.sessions.sessions = result.sessions;
				}
			});
		},

		getSession: function(name) {
			return window['shopCT']['sessions']['sessions'][name] || '';
		}
	},

	settings_save_data : {}
};

function image_gallery_sorting(container) {
	setTimeout(function() {
		var _input = container.parent().find('.popup-control-value'),
			ids = [];
		jQuery(container).find('.image').each(function() {
			ids.push(jQuery(this).data('attachment_id'));
		});
		_input.val(JSON.stringify(ids));
	},200);

}

function image_gallery_sortable() {
	var _list = jQuery('.popup_image_gallery_list');
	if (_list.length) {
		_list.sortable({
			opacity: 0.5,
			placeholder: "ui-shop-ct-image-placeholder",
			stop: function(event, ui) {
				image_gallery_sorting(ui.item.parent());
			}
		});
	}
}

function shopCTAddMainEventListeners() {

	var slug = jQuery("#shop_ct_wrapper").data("current-page");
	shopCT.adminpanel.activate_links(slug);

	jQuery("#toplevel_page_shopCT_customize").find("a").on("click", function(e) {
		if (e.button == 0) {
			jQuery(this).parent().parent().find(".current").removeClass("current");
			jQuery(this).parent().addClass("current");

			var _this_link = jQuery(this).attr("href");
			var myRegexp = /([^\?]*)\?page=([^\?]*)/g;
			var match = myRegexp.exec(_this_link);
			var slug = match[2];
			shopCT.adminpanel.activate_links(slug);
			jQuery("#shop_ct_wrapper").data("current-page", slug);
			shopCT.changePage(_this_link, slug);
			return false;
		}
	});

	jQuery("body").on("click", ".control-container-image .remove_control_image", function() {
		var _this = jQuery(this);
		_this.parent().parent().find(".popup-control-value").val("");
		_this.parent().parent().find("img").parent().parent().remove();
		_this.html(_this.data("add_new_text"));
		_this.removeClass("remove_control_image").addClass("add_image_control");
		return false;
	});

	/**
	 * When a popup is opened handle the tinymce,datepicker,tagbox and select2
     */
	jQuery(window).on("shop_ct_popup:ready", function(event, element_id) {
		var container = jQuery("#shop_ct_popup_window-" + element_id);

		if (container.find('.select2').length) {
			container.find('.select2').select2();
		}

		if (jQuery('#shop_ct_wrapper').find('.shop_ct_datepicker').length) {
			jQuery('#shop_ct_wrapper').find('.shop_ct_datepicker').datepicker({
				dateFormat: "yy-mm-dd"
			});
		}

		if (typeof tagBox != "undefined")
			tagBox.init();


	});
}

jQuery(window).ready(function() {
	shopCT.init();
	shopCTAddMainEventListeners();

});

jQuery(document).ready(function() {
	var getParams = shopCT.getLinkParams(window.location.href);

	if (getParams.hasOwnProperty('popup_id')) {
		setTimeout(function() {
			var popup_id = getParams['popup_id'],
				id = getParams['id'] || null;

			shopCT[popup_id].openPopup(id);
		}, 0);
	}

	jQuery('body').on('input keyup blur change','.mat-input-text input',function(){
        this.setAttribute('value', this.value);
        if(jQuery(this).val().length){
			jQuery(this).addClass('not-empty');
		}else{
            jQuery(this).removeClass('not-empty');
		}
	});

    jQuery('body').on('input keyup blur change','.mat-input-text textarea',function(){
    	jQuery(this).text(this.value);
    });

    jQuery(window).on("shop_ct_popup:rendered",function(event,elementId,args) {
        if(jQuery('.shop_ct_popup_window .shop-ct-grid').length){
                window.shopCtPopupMasonry = jQuery('.shop_ct_popup_window .shop-ct-grid').masonry({
                    itemSelector: '.shop-ct-grid-item',
                });
            jQuery('.shop_ct_popup_window').on('mouseup',function(){
                window.shopCtPopupMasonry.masonry();
			});
        }
    });


	jQuery('body').on('click','.shop-ct-link',function(e){
        if(e.button == 0){
            var _this_link = jQuery(this).attr("href"),
                page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
            if(typeof jQuery(this).data("slug") != 'undefined'){
                page_slug = jQuery(this).data("slug");
                jQuery("#shop_ct_wrapper").data("current-page",page_slug);
            }
            shopCT.adminpanel.activate_links(page_slug);
            shopCT.changePage(_this_link,page_slug);
            return false;
        }
	});

	jQuery('body').on('click','.shop-ct-toggle-button',function(){
        jQuery(this).parent().toggleClass("active");
	});

	jQuery('body').on('click','.shop-ct-tabs-menu-link',function(){
        var id = jQuery(this).attr('href').replace("#","");
        var activeClass = "active";
        if (jQuery(this).parent().parent().hasClass("category-tabs")) {
            activeClass = "tabs";
        }
        jQuery(this).parent().parent().find("." + activeClass).removeClass(activeClass);
        jQuery(this).parent().addClass(activeClass);
        var _tab = jQuery("#"+id);
        _tab.siblings().css('display', 'none');
        _tab.css('display', 'block');


        jQuery(window).triggerHandler('shop_ct_tabs:change');

        return false;
	});

	jQuery('body').on('click','.shop-ct-search-submit',function(){
        var old_url = window.location.href,
            new_url = shopCT.deleteQueryArgs(['s', 'paged'], old_url),
            new_params = [],
            page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page"),
            search_input = jQuery("#table-search-input");
        if (search_input.val() != '') new_params.s = encodeURIComponent(search_input.val());
        if (!shopctIsEmpty(new_params)) {
            new_url = shopCT.addQueryArgs(new_params, new_url);
            shopCT.changePage(new_url, page_slug);
        } else {
            shopCT.changePage(new_url, page_slug);
        }
        return false;
	});

	jQuery('body').on('keydown','.shop-ct-search',function(e){
        if(e.keyCode == 13){
            jQuery("#search-submit").click();
        }
	});

	jQuery('body').on('click','.shop-ct-post-status a',function(){
        var _this_link = jQuery(this).attr("href"),
            page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
        shopCT.changePage(_this_link, page_slug);
        return false;
	});

    /**
	 * todo: move this to proper place
     */
	jQuery('body').on('click','.shop-ct-col-checkbox',function(e){
        if(jQuery(this).parents("tbody").length){
            if ( 'undefined' == e.shiftKey ) { return true; }
            if ( e.shiftKey ) {
                if ( !lastClicked ) { return true; }
                checks = jQuery( lastClicked ).closest( 'form' ).find( ':checkbox' ).filter( ':visible:enabled' );
                first = checks.index( lastClicked );
                last = checks.index( this );
                checked = jQuery(this).prop('checked');
                if ( 0 < first && 0 < last && first != last ) {
                    sliced = ( last > first ) ? checks.slice( first, last ) : checks.slice( last, first );
                    sliced.prop( 'checked', function() {
                        if ( jQuery(this).closest('tr').is(':visible') )
                            return checked;

                        return false;
                    });
                }
            }
            lastClicked = this;

            // toggle "check all" checkboxes
            var unchecked = jQuery(this).closest('tbody').find(':checkbox').filter(':visible:enabled').not(':checked');
            jQuery(this).closest('table').children('thead, tfoot').find(':checkbox').prop('checked', function() {
                return ( 0 === unchecked.length );
            });

            return true;
        }else if( jQuery(this).parents("thead").length || jQuery(this).parents("tfoot").length ){
            var $=jQuery,$this = $(this),
                $table = $this.closest( 'table' ),
                controlChecked = $this.prop('checked'),
                toggle = e.shiftKey || $this.data('wp-toggle');

            $table.children( 'tbody' ).filter(':visible')
                .children().children('.shop-ct-check-column').find(':checkbox')
                .prop('checked', function() {
                    if ( $(this).is(':hidden,:disabled') ) {
                        return false;
                    }

                    if ( toggle ) {
                        return ! $(this).prop( 'checked' );
                    } else if ( controlChecked ) {
                        return true;
                    }

                    return false;
                });

            $table.children('thead,  tfoot').filter(':visible')
                .children().children('.shop-ct-check-column').find(':checkbox')
                .prop('checked', function() {
                    if ( toggle ) {
                        return false;
                    } else if ( controlChecked ) {
                        return true;
                    }

                    return false;
                });
        }
	});

});

function isEmpty(object) {
	for (var key in object) {
		if (object.hasOwnProperty(key)) {
			return false;
		}
	}
	return true;
}
