shopCT.shippingZones = {
	continents: [],

	openPopup: function(id) {
		var title;

		if (id) {
			title = "Edit Shipping Zone";
		} else {
			id = "new";
			title = "Add Shipping Zone";
		}

		var data = {
			action: 'shop_ct_ajax',
			task: 'shipping_zone_popup',
			nonce: shopCTL10n.shop_ct_nonce,
			id: id,
		};

		return jQuery.ajax({
			url: shopCTL10n.ajax_url,
			type: 'get',
			data: data,
			dataType: 'json',
			beforeSend: function() {
				shopCT.pageLoading();
			}
		}).done(function(result) {
			if (result.success) {
				if (result.hasOwnProperty('continents')) {
					shopCT.shippingZones.continents = result.continents
				}

				shop_ct_popup.show("shop_ct_popup_block",{
					'title': title,
					'row_content':result.return_html
				});
				shopCT.pageLoaded();
			} else {
				console.log("no success");
			}
		}).fail(function(xhr, status, error) {
			console.log(error);
			shopCT.pageLoaded();
		});
	},
};

jQuery(document).ready(function() {
	jQuery('body').on('click', '#shop_ct_add_shipping_zone_btn', function(e) {
		shopCT.shippingZones.openPopup('new');

		return false;
	});

	jQuery('body').on('submit', '#shop_ct_shipping_zone_popup_form', function(e) {
		e.preventDefault();

		var id = jQuery(this).find('#popup-control-id').val(),
			name = jQuery(this).find('#popup-control-name').val(),
			cost = jQuery(this).find('#popup-control-cost').val(),
			status = jQuery(this).find('#shop-ct-control-status').val(),
			countries = jQuery(this).find('#popup-control-countries').val();

		jQuery.ajax({
			url: shopCTL10n.ajax_url,
			dataType: 'JSON',
			type: 'POST',
			data: {
				action: 'shop_ct_ajax',
				task: 'save_shipping_zone',
				nonce: shopCTL10n.shop_ct_nonce,
				id: id,
				name: name,
				cost: cost,
				status: status,
				countries: countries,
			},
		}).done(function() {
			var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");

			shop_ct_popup.remove('shop_ct_popup_block');
			shopCT.changePage('', page_slug);
		});
	});

	jQuery('body').on('click', '#shop_ct_shipping_zone_form a.edit', function(e) {
		e.preventDefault();

		var id = jQuery(this).closest('tr').find('th input.shop-ct-col-checkbox').val();

		shopCT.shippingZones.openPopup(id || 'new');
	});

	jQuery('body').on('click', '#shop_ct_shipping_zone_form a.delete', function(e) {
		e.preventDefault();

		var id = jQuery(this).closest('tr').find('th input.shop-ct-col-checkbox').val(),
			r = confirm('Are you sure yoy want to delete this zone ?');

		if (!r) {
			return false;
		}

		jQuery.ajax({
			url: shopCTL10n.ajax_url,
			dataType: 'JSON',
			type: 'POST',
			data: {
				action: 'shop_ct_ajax',
				task: 'delete_shipping_zone',
				nonce: shopCTL10n.shop_ct_nonce,
				id: id,
			},
		}).done(function() {
			var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");

			shopCT.changePage('', page_slug);
		});
	});
});