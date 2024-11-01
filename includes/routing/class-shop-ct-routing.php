<?php

/**
 * Class Shop_CT_Routing
 */
class Shop_CT_Routing {

    public function __construct()
    {
        $this->routes = apply_filters( 'shop_ct_routes', [
            'shop-ct-paypal-ipn' => [
                'Shop_CT_Paypal_IPN_Listener' => 'init',
            ],
            'order-details' => [
                'Shop_CT_Template_Manager' => 'order_details_page'
            ],
            'shop-ct-download-file' => [
                'Shop_CT_Template_Manager' => 'handle_product_file_download'
            ],
        ]);

        foreach($this->routes as $route_endpoint => $handlers){
            add_rewrite_endpoint( $route_endpoint, EP_ROOT );
        }

        add_action('template_redirect', [$this, 'handle_routing']);
    }

	public function handle_routing(){
        global $wp_query;

        foreach($this->routes as $route_endpoint => $handlers){

            foreach($handlers as $handler_class=>$method_name){

                if (  isset( $wp_query->query_vars[$route_endpoint] )){

                    call_user_func([$handler_class,$method_name]);

                }

            }

        }
	}
}