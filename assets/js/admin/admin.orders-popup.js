jQuery(window).on('shop_ct_popup:ready', function(event, elementId, args) {
	if (args.popup_type === 'order') {
		jQuery('.order-datepicker').datepicker({
			defaultDate: '',
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			showButtonPanel: true
		});

		jQuery('.order-items-list').on('click', '.delete-order-item', function() {
			jQuery(this).closest('.order-items-list-item').remove();
			if (!jQuery('.order-items-list-item').length) {
				jQuery('.order-items-list').append('<p class="order-items-list-empty">'+jQuery('.order-items-list').data('empty')+'</p>');
			}

		});

		jQuery('.order-add-new-item').on('click', function() {
			var product = jQuery('.order-new-item-product').val();
			var quantity = parseInt(jQuery('.order-new-item-quantity').val());

			if (!product) {
				return false;
			}

			var existingListItem = jQuery('.order-items-list-item[data-product-id="'+product+'"]');

			if (existingListItem.length) {
				var input = existingListItem.find('input[type="number"]');
				input.val( parseInt(input.val()) + quantity );

				return false;
			}

			jQuery.ajax({
				url: shopCTL10n.ajax_url,
				type: 'post',
				data: {
					action: 'shop_ct_get_order_item',
					product_id: product,
					quantity: quantity,
					nonce: shopCTL10n.shop_ct_nonce
				}
			}).done(function (result) {
				jQuery('.order-items-list').append(result);
				if (jQuery('.order-items-list-empty').length) {
					jQuery('.order-items-list-empty').remove();
				}
				window.shopCtPopupMasonry.masonry();
			});
			return false;
		});

		/**
		 * Order saving .
		 */
		jQuery('.order-save').on('click', function(){
			var formData = jQuery('#order_popup_form').serialize(),
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
				if(response.success) {
					shop_ct_popup.showToastr(shopCTL10n.order_published);
					shop_ct_popup.remove('shop_ct_popup_block');
					var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
					shopCT.changePage('', page_slug);
					jQuery('#order_popup_form').data('submited','true');
				}
			}).fail(function (error) {
				shop_ct_popup.showToastr(shopCTL10n.serverSideError,'error');
			});

			return false;
		});

	}
});

jQuery(window).on("shop_ct_popup:beforeClose",function(event,element_id,args) {

	if (args.popup_type === 'order') {
		var container = jQuery('#shop_ct_order_popup_main_form');

		if (container.data("changed") == 'true' && container.data("submited") != 'true') {
			var exit = confirm(shopCTL10n.popupAlert);

			if (!exit) {
				window.shop_ct_popup_close = false;
			} else {
				if (jQuery('#popup-control-auto_draft').val() == '1') {
					deleteAutoDraft();
				}
			}
		} else if (container.data("submited") != 'true' && jQuery(jQuery('#popup-control-auto_draft')[0]).val() == 1) {
			deleteOrderAutoDraft();
		}
	}
});

function deleteOrderAutoDraft() {
	var id = jQuery('#popup-control-order_is_new_and_id').val(),
		ajaxData = {
			action: 'shop_ct_ajax',
			task: 'delete_auto_draft',
			nonce: shopCTL10n.shop_ct_nonce,
			id: id,
		};

	jQuery.ajax({
		url: shopCTL10n.ajax_url,
		type: 'POST',
		dataType: 'JSON',
		data: ajaxData,
	});
}