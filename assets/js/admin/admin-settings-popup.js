shopCT.emails = {
	id: '',

	openPopup: function(id) {

		this.id = id;

		var title = 'Edit Email Settings';

		var data = {
			action: 'shop_ct_ajax',
			task: 'email_popup',
			nonce: shopCTL10n.shop_ct_nonce,
			id: id,
		};

		jQuery.ajax({
			url: shopCTL10n.ajax_url,
			type: 'get',
			data: data,
			dataType: 'json',
			beforeSend: function() {
				shopCT.pageLoading();
			}
		}).done(function(result) {
			if (result.success) {
				shop_ct_popup.show("shop_ct_popup_block",{
					'title': title,
					'row_content': result.return_html
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
	jQuery('body').on('click', '.shop_ct_email_config_icon', function() {
		var id = jQuery(this).closest('tr').data('id');

		shopCT.emails.openPopup(id);
	});

	jQuery('body').on('click', '#shop_ct_email_settings_form #popup-control-submit', function() {

		var data = jQuery(this).closest('form').serializeArray(),
			dataObject = {};

		for (var i = 0, length = data.length; i < length; ++i) {
			dataObject[data[i]['name']] = data[i]['value'];
		}

		if (dataObject.status == undefined) {
			dataObject.status = 'no';
		} else {
			switch (dataObject.status) {
				case 'true' :
					dataObject.status = 'yes';
					break;
				case 'false' :
					dataObject.status = 'no';
					break;
			}
		}

		dataObject.id = shopCT.emails.id;
		
		var ajaxData = {
			action: 'shop_ct_ajax',
			task: 'save_email_settings',
			nonce: shopCTL10n.shop_ct_nonce,
			data: dataObject,
		};

		jQuery.ajax({
			url: shopCTL10n.ajax_url,
			type: 'POST',
			data: ajaxData,
			dataType: 'JSON',
		}).done(function() {
			var slug = jQuery('body').find("#shop_ct_wrapper").data("current-page");

			shop_ct_popup.remove('shop_ct_popup_block');
			shopCT.changePage('', slug);
		}).fail(function() {
			console.log('Oops');
		});

		return false;
	});

	jQuery('body').on('keyup change', '#popup-control-receiver:not(".shop_ct_single_receiver")', function() {
		var heading = jQuery('#control-container-customer_heading'),
			subject = jQuery('#control-container-customer_subject'),
			message = jQuery('#control-container-customer_message');

		if (jQuery(this).val().indexOf('{customer}') > -1) {
			heading.removeClass('invisible');
			subject.removeClass('invisible');
			message.removeClass('invisible');
		} else {
			heading.addClass('invisible');
			subject.addClass('invisible');
			message.addClass('invisible');
		}
	});
});
