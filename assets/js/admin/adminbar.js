/*var shop_ct_adminbar_model = {

	adminbar_resize : function(){
        if( jQuery(window).width() > 782 ){
            var left_menu_width = jQuery("#adminmenuwrap").width();
            jQuery(".shop_ct_adminbar").css({width:"calc(100% - "+left_menu_width+"px)",left:left_menu_width+"px",top:"32px"});
            jQuery(".shop_ct_loading_cover").css({width:"calc(100% - "+left_menu_width+"px)",left:left_menu_width+"px"});
            jQuery(".shop_ct_error_dialog").css({left:"calc(50% - "+left_menu_width+"px)"});
        }else{
            jQuery(".shop_ct_adminbar").css({width:"calc(100% - 20px)",left:"0px",top:"50px"});
            jQuery(".shop_ct_loading_cover").css({width:"100%",left:"0px"});
            jQuery(".shop_ct_error_dialog").css({left:"50%"});
        }

		window.requestAnimationFrame(shop_ct_adminbar_model.adminbar_resize);
	}
}

jQuery(window).ready(function(){
	shop_ct_adminbar_model.adminbar_resize();
});*/
jQuery(window).ready(function(){
    jQuery('.adminbar_change_block .btn1').click(function(){
        jQuery('.shop_ct_adminbar').removeClass('vertical');
        jQuery('.shop_ct_adminbar').addClass('horizontal');
        shopCT.sessions.setSession('shop_ct_adminbar','horizontal');
    });
    jQuery('.adminbar_change_block .btn2').click(function(){
        jQuery('.shop_ct_adminbar').removeClass('horizontal');
        jQuery('.shop_ct_adminbar').addClass('vertical');
        shopCT.sessions.setSession('shop_ct_adminbar','vertical');
    });
});