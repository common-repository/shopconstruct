shopCT.terms = {
	orderby_arr : ['ID','name','description','slug','count'],
	ordering_arr : ['ASC','DESC'],
	page : "",
	per_page : "",
	total_pages : "",
	total_terms_count : "",

	openPopup : function(id, title, taxonomy, submit_btn_value){

		var data = {
			action: 'shop_ct_ajax',
			task : 'terms_popup',

			taxonomy : taxonomy,
			id : id,
			submit_btn_value : submit_btn_value,

			nonce:shopCTL10n.shop_ct_nonce,
		};

		jQuery.ajax({
			url: shopCTL10n.ajax_url,
			type : 'get',
			data : data,
			dataType : 'json',
			beforeSend: function(xhr){
				shopCT.pageLoading();
			},
		}).done(function(result) {
			if (result.success) {
				var height = false;
				if(!jQuery('#shop_ct_cat_popup_block').length){
                    jQuery('body').append('<div id="shop_ct_cat_popup_block"></div>');
				}

				shop_ct_popup.show("shop_ct_cat_popup_block",{
					'title': title,
					'row_content': result.return_html,
					'width': 800,
					'height': height
				});
				if(result.count){
					shopCT.terms.shop_ct_category_count(result.count);
				}
				shopCT.pageLoaded();
			}else{
				console.log("no success");
			}
		}).fail(function(xhr, status, error) {
			console.log(xhr);
			shopCT.pageLoaded();
		});
	},

	init : function(){
		var _this = this;
		_this.wrap = jQuery("body").find("#shop_ct_categories_wrapper");

		shopCT.terms.page=_this.wrap.data("page");
		shopCT.terms.per_page=_this.wrap.data("per_page");
		shopCT.terms.total_pages=_this.wrap.data("total");
		shopCT.terms.total_terms_count=_this.wrap.data("total_terms_count");
	},

	orderCategories : function(orderby,ordering){
		if(shopCT.terms.orderby_arr.in_array(orderby) && shopCT.terms.ordering_arr.in_array(ordering)){
			var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");

			var order = {
				ordering : ordering,
				orderby : orderby
			}

			shopCT.reloadPage();
		}
	},

	cat_bulk_action_apply : function(el,taxonomy,successText,form_id){
		if (el.parent().find("select option:selected").val() == 'delete'){

			var checked_term_ids = new Array();
			jQuery.each(jQuery("#"+form_id+" table input[type='checkbox']:checked"), function() {
			  checked_term_ids.push(jQuery(this).val());
			});
			if (checked_term_ids.length){

				var data = {
					action: 'shop_ct_ajax',
					task : 'delete_term',
					id : checked_term_ids,
					taxonomy : taxonomy,
					nonce:shopCTL10n.shop_ct_nonce,
				};

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
							page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
							shopCT.changePage('',page_slug);
							jQuery("#shop_ct_categories_form input:checked").prop("checked",false);
							jQuery("#shop_ct_categories_form #bulk-action-selector-top option:first-child").prop("selected",true);
							shopCT.pageLoaded();
							shop_ct_popup.showToastr(successText);
						}
					}
				});
			}
		}
	},


	delete_term_item : function(id,name,taxonomy,successText){
		jQuery( "#shop_ct_error_dialog_wrapper" ).html("<p>Are you sure you want to delete '"+name+"'?</p>");
		jQuery( "#shop_ct_error_dialog_wrapper" ).dialog({ // ALERTING ARE YOU SURE DIALOG
			dialogClass:'shop_ct_error_dialog',
			draggable: false,
			resizable: false,
			closeOnEscape:true,
			hide : 100,
			height: 300,
			modal: true,
			closeText: '<span class="dashicons dashicons-no"></span>',
			position : { my: "center", at: "center", of: "#shop_ct_wrapper" },
			title : 'Delete Category',
			buttons: {
				"YES": function() {      // IF USER CLICKED YES I AM SURE and the whole category gets deleted
							var _this = jQuery(this);
							_this.dialog("close");
							var data = {
								action: 'shop_ct_ajax',
								task : 'delete_term',
								id: id,
								taxonomy : taxonomy,
								nonce:shopCTL10n.shop_ct_nonce,
							};

							jQuery.ajax({
								url: shopCTL10n.ajax_url,
								type : 'get',
								data : data,
								dataType : 'json',
								beforeSend: function(xhr){
									shopCT.pageLoading();
								},
							}).done(function(result) {
								if (result.success) {
									page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");
									shopCT.changePage('',page_slug);
									shopCT.pageLoaded();
									shop_ct_popup.showToastr(successText);
								}
							}).fail(function() {

							});

							return false;

				},
				"NO": function(){
					jQuery(this).dialog("close"); //simply closes the dialog box
				}
			}
		});

	},

	popup_submit_term: function(term_args,taxonomy,successText){
		var data = {
			action: 'shop_ct_ajax',
			task : 'popup_save',

			term_args : term_args,
			taxonomy : taxonomy,

			nonce:shopCTL10n.shop_ct_nonce,
		};

		jQuery.ajax({
			url: shopCTL10n.ajax_url,
			type : 'post',
			data : data,
			dataType : 'json',
			beforeSend: function(){
				shopCT.pageLoading();
			},
		}).done(function(result){
			if (result.success) {
				var page_slug = jQuery("body").find("#shop_ct_wrapper").data("current-page");

				shop_ct_popup.remove("shop_ct_cat_popup_block");
				shopCT.changePage('',page_slug);
				shopCT.pageLoaded();
				shop_ct_popup.showToastr(successText);
			}
		});
	},

	shop_ct_category_count:function (count){
		var c_el = jQuery("body").find(".shop_ct_cat_count_div .count");
		if(c_el.length && !isNaN(parseInt(count))){
			c_el.html(count);
		}
	}
	
	
};


jQuery( window ).ready(function() {

	shopCT.terms.init();

	jQuery('body').on('click', '#shop_ct_tags_form .delete-tag', function() {
		var id = jQuery(this).parent().parent().parent().parent().find("input[type='checkbox']").val();
		var name =  jQuery(this).parent().parent().parent().find("strong .row-title").html();
		var taxonomy = "shop_ct_product_tag";
		var successText = "Tag Successfully Deleted";

		shopCT.terms.delete_term_item(id,name,taxonomy,successText);
		/* UNFOCUS!!! */
		return false;
	});

	jQuery("body").on("click","#shop_ct_categories_form #doaction-top,#shop_ct_categories_form  #doaction-bottom",function(){
		var el = jQuery(this);
		var taxonomy = "shop_ct_product_category";
		var successText = "Categories Successfully Deleted";
		var form_id = "shop_ct_categories_form";

		shopCT.terms.cat_bulk_action_apply(el,taxonomy,successText,form_id);
		return false;
	});

	jQuery("body").on("click","#shop_ct_categories_form .delete-tag",function(){
		var id = jQuery(this).parent().parent().parent().parent().find("input[type='checkbox']").val();
		var name =  jQuery(this).parent().parent().parent().find("strong .row-title").html();
		var taxonomy = "shop_ct_product_category";
		var successText = "Category Successfully Deleted";

		shopCT.terms.delete_term_item(id,name,taxonomy,successText);
		/* UNFOCUS!!! */
		return false;
	});

	jQuery("body").on("click","#shop_ct_add_cat_btn, .shop-ct-add-cat-btn",function(){
		shopCT.terms.openPopup("new","Add Category","shop_ct_product_category","Add Category");
		return false;
	});

	jQuery('body').on('click', '#shop_ct_add_tag_btn', function() {
		shopCT.terms.openPopup('new', 'Add Tag', 'shop_ct_product_tag', 'Add Tag');
	});

	jQuery("body").on("click","#shop_ct_categories_form .row-title",function(){
		id = jQuery(this).parent().parent().parent().find("input[type='checkbox']").val();
		shopCT.terms.openPopup(id,"Edit Category","shop_ct_product_category","Edit Category");
		return false;
	});

	jQuery("body").on("click","#shop_ct_categories_form .edit",function(){
		id = jQuery(this).parent().parent().parent().find("input[type='checkbox']").val();
		shopCT.terms.openPopup(id,"Edit Category","shop_ct_product_category","Edit Category");
		return false;
	});

	jQuery("body").on("click","#shop_ct_tags_form .row-title",function(){
		id = jQuery(this).parent().parent().parent().find("input[type='checkbox']").val();
		shopCT.terms.openPopup(id,"Edit Tag","shop_ct_product_tag","Edit Tag");
		return false;
	});

	jQuery("body").on("click","#shop_ct_tags_form .edit",function(){
		id = jQuery(this).parent().parent().parent().find("input[type='checkbox']").val();
		shopCT.terms.openPopup(id,"Edit Tag","shop_ct_product_tag","Edit Tag");
		return false;
	});


	jQuery("body").on("click","#popup-control-submit_category",function(){

			var term_args = {};

			jQuery("body").find(".popup-control-value").each(function(i){

				var _this = jQuery(this);
				var value = "";
				var key = _this.attr("id").replace("popup-control-","");
				if (_this.is(":checkbox")){
					if(_this.is(":checked")){
						value = "true";
					}else{
						value = "false";
					}
				}else{
					value = _this.val();
				}
				term_args[key] = value;
			});

			var taxonomy = "shop_ct_product_category";

			if(typeof term_args.id !="undefined" && term_args.id==""){
				term_args.id="new";
				var successText = "Category Successfully Added";
			}else{
				var successText = "Category Successfully Edited";
			}

			shopCT.terms.popup_submit_term(term_args,taxonomy,successText);
			return false;
	});

	jQuery("body").on("click","#popup-control-submit_tag",function(){

			var term_args = {};

			jQuery("body").find(".popup-control-value").each(function(i){

				var _this = jQuery(this);
				var value = "";
				var key = _this.attr("id").replace("popup-control-","");
				if (_this.is(":checkbox")){
					if(_this.is(":checked")){
						value = "true";
					}else{
						value = "false";
					}
				}else{
					value = _this.val();
				}
				term_args[key] = value;
			});

			var taxonomy = "shop_ct_product_tag";

			if(typeof term_args.id !="undefined" && term_args.id==""){
				term_args.id="new";
				var successText = "Tag Successfully Added";
			}else{
				var successText = "Tag Successfully Edited";
			}

			shopCT.terms.popup_submit_term(term_args,taxonomy,successText);
			return false;
	});

	jQuery("body").on("click","#apply_page_settings",function(){
		var per_page = jQuery("body").find("#cat_per_page").val();
		shopCT.terms.perPageChange(per_page);
		return false;
	});

});
