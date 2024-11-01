jQuery(window).ready(function() {
	var _body = jQuery('body');

    _body.on("click", ".accordion-section-title", function() {
		if (jQuery(this).parent().hasClass("open")) {
			jQuery(this).parent().removeClass("open");
			jQuery(this).parent().find(".accordion-section-content ").css("display", "none");
		} else {
			jQuery(this).parent().addClass("open");
			jQuery(this).parent().find(".accordion-section-content ").css("display", "block");
		}

	});

    _body.on("click", "#submit_display_page", function() {

		var checked_post_type_ids = [];

        _body.find("#add-post-type-page input[type='checkbox']").each(function(i) {

			var _this = jQuery(this);
			var id = _this.val();

			if (_this.is(":checked") && !jQuery("#display_settings_list li.shop-post-" + id).length) {
				checked_post_type_ids.push(id);
			}

		});

		var data = {
			action: 'shop_ct_ajax',
			task: 'send_display_post_type',
			nonce: shopCTL10n.shop_ct_nonce,
			checked_post_type_ids: checked_post_type_ids,
		};


		jQuery.ajax({
			url: shopCTL10n.ajax_url,
			type: 'get',
			data: data,
			dataType: 'json',
			beforeSend: function(xhr) {
			}
		}).done(function(result) {
			if (result.success) {
				if (jQuery("#display_settings_list"))
					jQuery("#display_settings_list").append(result.output);
			}
		});

		return false;
	});
    _body.on("click", "#submit_display_post", function() {

		var checked_post_type_ids = [];

		jQuery("body").find("#add-post-type-post input[type='checkbox']").each(function(i) {

			var _this = jQuery(this);
			var id = _this.val();

			if (_this.is(":checked") && !jQuery("#display_settings_list li.shop-post-" + id).length) {
				checked_post_type_ids.push(id);
			}

		});


		var data = {
			action: 'shop_ct_ajax',
			task: 'send_display_post_type',
			nonce: shopCTL10n.shop_ct_nonce,
			checked_post_type_ids: checked_post_type_ids,
		};


		jQuery.ajax({
			url: shopCTL10n.ajax_url,
			type: 'get',
			data: data,
			dataType: 'json',
		}).done(function(result) {
			if (result.success) {
				if (jQuery("#display_settings_list")) {
					jQuery("#display_settings_list").append(result.output);
				}
			}
		});

		return false;
	});

    _body.on("click", ".open_display_settings", function() {
		if (jQuery(this).find("i").hasClass("fa fa-caret-down")) {
			jQuery(this).find("i").removeClass("fa fa-caret-down").addClass("fa fa-caret-up");
			jQuery(this).parent().parent().find(".post-item-settings").css("display", "block")
		} else {
			jQuery(this).find("i").removeClass("fa fa-caret-up").addClass("fa fa-caret-down");
			jQuery(this).parent().parent().find(".post-item-settings").css("display", "none")
		}

	});

	_body.on("change", ".display_settings_show", function() {
		jQuery(this).closest('.post_settings_li').find(".shop_page_type").html(" (" + jQuery(this).find("option:selected").html() + ")");

		var value = jQuery(this).val();

		switch (value) {

			case 'single_product' :
				jQuery(this).closest('ul').find('.shop_ct_all__list').find('div').hide();
				jQuery(this).closest('ul').find('.shop_ct_all__list').find(".shop_all_products").css("display", "block");
				break;

			case 'single_category' :
				console.log('a');
				jQuery(this).closest('ul').find('.shop_ct_all__list').find('div').hide();
				jQuery(this).closest('ul').find('.shop_ct_all__list').find(".shop_all_categories").css("display", "block");
				break;
			default:
                jQuery(this).closest('ul').find('.shop_ct_all__list div').hide();
		}
	});

	_body.on("click", ".display_settings_save", function() {

		var display_settings_array = [],
		    $object;


		jQuery(".post_settings_li").each(function() {

			var $resource = jQuery(this).find(".display_settings_show").val();
			var $post_id = jQuery(this).find(".post-item-settings").attr("id").substr(19);

			switch ($resource) {
				case "single_product" :
					$object = jQuery(this).find(".display_settings_single_product").val();

					break;

				case "single_category" :
					$object = jQuery(this).find(".display_settings_single_category").val();

					break;
				default:
					$object = null;

					break;
			}

			display_settings_array.push({
				post_id: $post_id,
				resource: $resource,
				object: $object
			});
		});


		var data = {
			action: 'shop_ct_ajax',
			task: 'save_display_settings',
			nonce: shopCTL10n.shop_ct_nonce,
			display_settings: display_settings_array
		};
		var successText = "Settings Saved Successfully";


		jQuery.ajax({
			url: shopCTL10n.ajax_url,
			type: 'get',
			data: data,
			dataType: 'json',
			beforeSend: function(xhr) {
			},
			success: function(result) {
				if (result.success) {
					shopCT.pageLoaded();
					shop_ct_popup.showToastr(successText);
				}
			},
			error: function(xhr, status, error) {

			}
		});
		return false;
	});

    _body.on("click", ".delete_shop_page", function() {
		jQuery(this).closest('li.post_settings_li').remove();
	});


});