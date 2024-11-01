shopCT.attributes = {

	openPopup: function(id, title, attribute, submit_btn_value ){
		var data = {
			action: 'shop_ct_ajax',
			task : 'attributes_popup',
			id : id,
			submit_btn_value : submit_btn_value,
			nonce:shopCTL10n.shop_ct_nonce,
		};


		if(attribute) data.attribute = attribute;

		jQuery.ajax({
			url: shopCTL10n.ajax_url,
			type : 'get',
			data : data,
			dataType : 'json',
			beforeSend: function(xhr){
				shopCT.pageLoading();
			},
			success: function(result,status,xhr){
				if (result.success) {
					var height = false;
					shop_ct_popup.show("shop_ct_popup_block",{
						'title': title,
						'row_content': result.return_html,
						'width': 800,
						'height': height
					});
					shopCT.pageLoaded();
				}else{
					console.log("no success");
				}
			},
			error : function(xhr,status,error){
				shopCT.pageLoaded();
			}
		});
	},

	init : function(){
		this.removeEventListeres();
		this.addEventListeners();
	},

	removeEventListeres:function(){
		jQuery("#shop_ct_add_attr_btn").off("click");
	},

	addEventListeners: function(){
		jQuery("#shop_ct_add_attr_btn").on("click",function(){
			shopCT.attributes.openPopup("new","Add Attrbiute",false,"Add Attribute");
			return false;
		});
	}
};

function handle_no_products() {
	var checked_rows = jQuery('.shop_ct_attr_terms_table>tbody').find('tr').not('.no-items');
	(checked_rows.length == 0 ? jQuery('.no-items').show() : jQuery('.no-items').hide());
}

jQuery(window).ready(function(){
	shopCT.attributes.init();
	jQuery(window).on("shopCT:load",function(){
		shopCT.attributes.init();
	});
	
	jQuery('body').on('click', '#add_attribute_term_button', function () {

		var ajaxData = {
			action: 'shop_ct_ajax',
			task: 'add_new_attribute_term',
			nonce: shopCTL10n.shop_ct_nonce,
		};

		jQuery.ajax({
			url: shopCTL10n.ajax_url,
			type: 'GET',
			dataType: 'JSON',
			data: ajaxData,
		}).done(function (response) {
			var table = jQuery('.shop_ct_attr_terms_table');
			table.find('tbody').append(response.html);
			handle_no_products();
		});

		return false;
	});

	jQuery('body').on('click', '.shop_ct_attr_terms_table .remove-term', function () {

		var button = jQuery(this);

		jQuery("#shop_ct_error_dialog_wrapper").html("<p>Are you sure you want to delete this term ?</p>");
		jQuery("#shop_ct_error_dialog_wrapper").dialog({
			dialogClass: 'shop_ct_error_dialog',
			draggable: false,
			resizable: false,
			closeOnEscape: true,
			hide: 100,
			height: 300,
			modal: true,
			closeText: '<span class="dashicons dashicons-no"></span>',
			position: {my: "center", at: "center", of: "#shop_ct_wrapper"},
			title: 'Delete Term',
			buttons: {
				"YES": function () {
					var first, second;
					first = jQuery(button).closest('tr');
					second = jQuery(button).closest('tr').next('tr');

					first.remove();
					second.remove();

					jQuery(this).dialog("close");
					handle_no_products();
					return false;
				},
				"NO": function () {
					jQuery(this).dialog("close");
					return false;
				}
			}
		});

		return false;
	});
	
	jQuery('body').on('click', '#popup-control-submit_attribute', function () {
		var form = jQuery('#shop_ct_popup_attributes_form'),
			table = form.find('.shop_ct_list_table');

		var attribute_name = jQuery('#popup-control-name').val(),
			attribute_slug = jQuery('#popup-control-slug').val(),
			attribute_type = jQuery('#shop-ct-control-type').val(),
			order_by = jQuery('#shop-ct-control-ordering').val(),
			is_new = jQuery('#popup-control-id').val();



		var inputs = [
			jQuery('#popup-control-name')
		];
		var error_message = jQuery('#shop-ct-popup-section-error_section #errors');
		error_message.empty();

		var is_clear = true;
		var b = false;

		jQuery.each(inputs, function (key, input) {

			var v = jQuery(input).val();

			jQuery(input).css('border', '');

			if (v === undefined || v.trim() == '') {
				jQuery(input).css('border', '1px solid red');
				if (!b) {


					error_message.append('<p>Please fill all required fields.</p>');
					b = true;
					is_clear = false;
				}
			}
		});

		if (!is_clear) {
			return false;
		}

		var terms = table.find('.shop_ct_attr_term_item');

		var data = {
			attribute: {
				name: attribute_name,
				slug: attribute_slug,
				type: attribute_type,
				order_by: order_by,
			},
			terms: []
		};
		
		jQuery.each(terms, function (key, term) {
			var term_id = jQuery(term).find('input[name="term_name"]').data('id'),
				term_name = jQuery(term).find('input[name="term_name"]').val(),
				term_slug = term_name.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'');
			
			data.terms[key] = {
				id: term_id,
				name: term_name,
				slug: term_slug,
			};
		});



		var ajaxData = {
			action: 'shop_ct_ajax',
			task: 'save_attribute',
			data: data,
			nonce: shopCTL10n.shop_ct_nonce,
			id: is_new
		};

		jQuery.ajax({
			url: shopCTL10n.ajax_url,
			type: 'POST',
			dataType: 'JSON',
			data: ajaxData
		}).always(function (response) {
			shop_ct_popup.remove('shop_ct_popup_block');
			var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
			shopCT.changePage('',page_slug);
		});

		return false;
	});

	jQuery('body').on('click', '.shop_ct_attr_table tbody a.edit, .shop_ct_attr_table tbody a.row-title', function () {

		var attr_id = jQuery(this).closest('tr').find('th input:checkbox').val();

		shopCT.attributes.openPopup(attr_id, 'Edit Attribute');

		return false;
	});

	jQuery('body').on('click', '.shop_ct_attr_table tbody a.delete', function () {

		var attr_id = jQuery(this).closest('tr').find('th input:checkbox').val(),
			ajaxData = {
				action: 'shop_ct_ajax',
				task: 'delete_attribute',
				id: attr_id,
				nonce: shopCTL10n.shop_ct_nonce
			};

		jQuery("#shop_ct_error_dialog_wrapper").html("<p>Are you sure you want to delete this attribute ?</p>");
		jQuery("#shop_ct_error_dialog_wrapper").dialog({
			dialogClass: 'shop_ct_error_dialog',
			draggable: false,
			resizable: false,
			closeOnEscape: true,
			hide: 100,
			height: 300,
			modal: true,
			closeText: '<span class="dashicons dashicons-no"></span>',
			position: {my: "center", at: "center", of: "#shop_ct_wrapper"},
			title: 'Delete Attribute',
			buttons: {
				"YES": function () {
					jQuery.ajax({
						url: shopCTL10n.ajax_url,
						dataType: 'JSON',
						type: 'POST',
						data: ajaxData
					}).always(function (response) {
						var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
						shopCT.changePage('',page_slug);
					});
					jQuery(this).dialog("close");
				},
				"NO": function () {
					jQuery(this).dialog("close");
					return false;
				}
			}
		});

	});

	jQuery('body').on('click', '#shop_ct_attribute_form #doaction-top, #shop_ct_attribute_form #doaction-bottom', function () {

		var checkboxes = jQuery('.shop_ct_attr_table tbody th input:checkbox:checked');

		if (jQuery(this).siblings('select').val() == 'delete' && checkboxes.length > 0) {

			var ids = [];

			jQuery.each(checkboxes, function (index, checkbox) {
				ids.push(jQuery(checkbox).val());
			});

			var ajaxData = {
				action: 'shop_ct_ajax',
				task: 'delete_attribute',
				ids: ids,
				nonce: shopCTL10n.shop_ct_nonce
			};

			jQuery("#shop_ct_error_dialog_wrapper").html("<p>Are you sure you want to delete these attributes ?</p>");
			jQuery("#shop_ct_error_dialog_wrapper").dialog({
				dialogClass: 'shop_ct_error_dialog',
				draggable: false,
				resizable: false,
				closeOnEscape: true,
				hide: 100,
				height: 300,
				modal: true,
				closeText: '<span class="dashicons dashicons-no"></span>',
				position: {my: "center", at: "center", of: "#shop_ct_wrapper"},
				title: 'Delete Attributes',
				buttons: {
					"YES": function () {
						jQuery.ajax({
							url: shopCTL10n.ajax_url,
							dataType: 'JSON',
							type: 'POST',
							data: ajaxData
						}).always(function (response) {
							var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
							shopCT.changePage('', page_slug);
						});
						jQuery(this).dialog("close");
					},
					"NO": function () {
						jQuery(this).dialog("close");
						return false;
					}
				}
			});
		}

		return false;
	});
});