<?php
/**
 * Plugin configurations
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

if( version_compare(phpversion(), '5.4', '<') ){
    throw new Exception('Your current version of PHP '.phpversion().' is outdated. Please, update it to 5.4 and higher in order to use ShopConstruct.');
}


$GLOBALS['shop_ct_aliases'] = array(

    // HELPERS
    'Shop_CT_Geo_IP' => 'includes/helpers/class-shop-ct-geo-ip',
    'Shop_CT_Formatting' => 'includes/helpers/class-shop-ct-formatting',
    'Shop_CT_Currencies' => 'includes/helpers/class-shop-ct-currencies',
    'Shop_CT_Logger' => 'includes/helpers/class-shop-ct-logger',
    'Shop_CT_Dates' => 'includes/helpers/class-shop-ct-dates',
    'Shop_CT_Images' => 'includes/helpers/class-shop-ct-images',
    'Shop_CT_Validator' => 'includes/helpers/class-shop-ct-validator',

    // INSTALLATION
    'Shop_CT_Install' => 'includes/install/class-shop-ct-install',

    // DB MODELS
    'Shop_CT_Cart' => 'includes/class-shop-ct-cart',
    'Shop_CT_Cart_Manager' => 'includes/class-shop-ct-cart-manager',
    'Shop_CT_Meta' => 'includes/class-shop-ct-meta',
    'Shop_CT_Payment_Gateway' => 'includes/abstracts/abstract-shop-ct-payment-gateway',
    'Shop_CT_Payment_Gateways' => 'includes/class-shop-ct-payment-gateways',
    'Shop_CT_Product' => 'includes/class-shop-ct-product',
    'Shop_CT_Product_Attribute' => 'includes/class-shop-ct-product-attribute',
    'Shop_CT_Product_Attribute_Term' => 'includes/class-shop-ct-product-attribute-term',
    'Shop_CT_Product_Tag' => 'includes/class-shop-ct-product-tag',
    'Shop_CT_Term' => 'includes/class-shop-ct-term',
    'Shop_CT_Product_Category' => 'includes/class-shop-ct-product-category',
    'Shop_CT_Product_Review' => 'includes/class-shop-ct-product-review',
    'Shop_CT_Order' => 'includes/class-shop-ct-order',
    'Shop_CT_Customer' => 'includes/class-shop-ct-customer',
    'Shop_CT_Shipping_Zone' => 'includes/class-shop-ct-shipping-zone',


    'Shop_CT_Post_types' => 'includes/class-shop-ct-post-types',

    'Shop_CT_Settings' => 'includes/abstracts/abstract-shop-ct-settings',

    // Payment Gateways
    'Shop_CT_Gateway_Paypal' => 'includes/gateways/paypal/class-shop-ct-gateway-paypal',
	'Shop_CT_Paypal_IPN' => 'includes/gateways/paypal/class-shop-ct-paypal-ipn',
	'Shop_CT_Paypal_IPN_Listener' => 'includes/gateways/paypal/class-shop-ct-paypal-ipn-listener',
    'Shop_CT_Gateway_BACS' => 'includes/gateways/bacs/class-shop-ct-gateway-bacs',
    'Shop_CT_Gateway_Cheque' => 'includes/gateways/cheque/class-shop-ct-gateway-cheque',
    'Shop_CT_Gateway_COD' => 'includes/gateways/cod/class-shop-ct-gateway-cod',

    // SERVICES
    'Shop_CT_Services' => 'includes/services/class-shop-ct-services',
    'Shop_CT_Email_Settings' => 'includes/services/class-shop-ct-email-settings',
    'Shop_CT_Service_Attributes' => 'includes/services/class-shop-ct-service-attributes',
    'Shop_CT_Service_Categories' => 'includes/services/class-shop-ct-service-categories',
    'Shop_CT_Service_Tags' => 'includes/services/class-shop-ct-service-tags',
    'Shop_CT_Service_Product_Categories' => 'includes/services/class-shop-ct-service-product-categories',
    'Shop_CT_Checkout_Settings' => 'includes/services/checkout/class-shop-ct-checkout-settings',
    'Shop_CT_Checkout' => 'includes/services/checkout/class-shop-ct-checkout',
    'Shop_CT_Dashboard' => 'includes/services/class-shop-ct-service-dashboard',
    'Shop_CT_Service_Orders' => 'includes/services/class-shop-ct-service-orders',
    'Shop_CT_Product_Settings' => 'includes/services/class-shop-ct-service-product-settings',
    'Shop_CT_Service_Products' => 'includes/services/class-shop-ct-service-products',
    'Shop_CT_Service_Reviews' => 'includes/services/class-shop-ct-service-reviews',
    'Shop_CT_Service_Settings' => 'includes/services/class-shop-ct-service-settings',
    'Shop_CT_Email' => 'includes/services/emails/class-shop-ct-email',
    'Shop_CT_Email_New_Customer' => 'includes/services/emails/class-shop-ct-email-new-customer',
    'Shop_CT_Email_Order_Cancelled' => 'includes/services/emails/class-shop-ct-email-order-cancelled',
    'Shop_CT_Email_Order_Completed' => 'includes/services/emails/class-shop-ct-email-order-completed',
    'Shop_CT_Email_Order_Customer_Invoice' => 'includes/services/emails/class-shop-ct-email-order-customer-invoice',
    'Shop_CT_Email_Order_Failed' => 'includes/services/emails/class-shop-ct-email-order-failed',
    'Shop_CT_Email_Order_New' => 'includes/services/emails/class-shop-ct-email-order-new',
    'Shop_CT_Email_Downloadable_Files' => 'includes/services/emails/calss-shop-ct-email-downloadable-files',
    'Shop_CT_Email_Order_Processing' => 'includes/services/emails/class-shop-ct-email-order-processing',
    'Shop_CT_Email_Order_Refunded' => 'includes/services/emails/class-shop-ct-email-order-refunded',
    'Shop_CT_popup' => 'includes/services/popup/class-shop-ct-popup',
    'Shop_CT_popup_attribute_taxonomies' => 'includes/services/popup/class-shop-ct-popup-attribute-taxonomies',
    'Shop_CT_Popup_Email_Settings' => 'includes/services/popup/class-shop-ct-popup-email-settings',
    'Shop_CT_popup_order' => 'includes/services/popup/class-shop-ct-popup-order',
    'Shop_CT_popup_product' => 'includes/services/popup/class-shop-ct-popup-product',
    'Shop_CT_popup_review' => 'includes/services/popup/class-shop-ct-popup-review',
	'Shop_CT_Popup_Shipping_Zone' => 'includes/services/popup/class-shop-ct-popup-shipping-zone',
    'Shop_CT_Service_Shipping_Zones' => 'includes/services/class-shop-ct-service-shipping-zones',
    'Shop_CT_Shortcodes' => 'includes/services/shortcodes/class-shop-ct-shortcodes',
    'Shop_CT_Shortcode_Product' => 'includes/services/shortcodes/class-shop-ct-shortcode-product',
    'Shop_CT_Shortcode_Catalog' => 'includes/services/shortcodes/class-shop-ct-shortcode-catalog',
    'Shop_CT_Shortcode_Cart_Button' => 'includes/services/shortcodes/class-shop-ct-shortcode-cart-button',
    'Shop_CT_Shortcode_Category' => 'includes/services/shortcodes/class-shop-ct-shortcode-category',

    //ADMIN
    'Shop_CT_Admin' => 'includes/admin/class-shop-ct-admin',
    'Shop_CT_Admin_Menus' => 'includes/admin/class-shop-ct-admin-menus',
    'Shop_CT_Admin_Assets' => 'includes/admin/class-shop-ct-admin-assets',
    'Shop_CT_list_table' => 'includes/admin/list-table/class-shop-ct-list-table',
    'Shop_CT_list_table_attribute_taxonomies' => 'includes/admin/list-table/class-shop-ct-list-table-attribute-taxonomies',
    'Shop_CT_list_table_order' => 'includes/admin/list-table/class-shop-ct-list-table-order',
    'Shop_CT_List_Table_Products' => 'includes/admin/list-table/class-shop-ct-list-table-products',
    'Shop_CT_list_table_reviews' => 'includes/admin/list-table/class-shop-ct-list-table-reviews',
    'Shop_CT_List_Table_Shipping_Zones' => 'includes/admin/list-table/class-shop-ct-list-table-shipping-zones',

    // ABSTRACTS
	'Shop_CT_Setter' => 'includes/abstracts/trait-shop-ct-setter',

    // ROUTING
	'Shop_CT_Routing' => 'includes/routing/class-shop-ct-routing',
	'Shop_CT_Rewrite_Rules' => 'includes/routing/class-shop-ct-rewrite-rules',

    // Event Listeners
    'Shop_CT_Comment_Filter' => 'includes/event-listeners/comments/class-shop-ct-comment-filter',
    'Shop_CT_Frontend_Scripts' => 'includes/event-listeners/frontend/scripts/class-shop-ct-frontend-scripts',
    'Shop_CT_Template_Manager' => 'includes/event-listeners/frontend/class-shop-ct-template-manager',
    'Shop_CT_Ajax_Display_Settings' => 'includes/event-listeners/ajax/admin/class-shop-ct-ajax-display-settings',
    'Shop_CT_Ajax_Cart' => 'includes/event-listeners/ajax/class-shop-ct-ajax-cart',
    'Shop_CT_Ajax_Settings' => 'includes/event-listeners/ajax/admin/class-shop-ct-ajax-settings',
    'Shop_CT_Ajax_Admin_Products' => 'includes/event-listeners/ajax/admin/class-shop-ct-ajax-admin-products',
    'Shop_CT_Cart_Actions' => 'includes/event-listeners/frontend/class-shop-ct-cart-actions',
    'Shop_CT_Order_Completed' => 'includes/event-listeners/order/class-shop-ct-order-completed',

);
/**
 * @param $classname
 * @throws Exception
 */
function shop_ct_autoload( $classname ){
    global $shop_ct_aliases;
    /**
     * We do not touch classes that are not related to us
     */
    if( !strstr( $classname, 'Shop_CT_' ) ){
        return;
    }
    if( ! key_exists( $classname, $shop_ct_aliases ) ){
        throw new Exception( 'trying to load "'.$classname.'" class that is not registered in plugin configurations.' );
    }
    $path = SHOP_CT()->plugin_path().'/'.$shop_ct_aliases[$classname].'.php';
    if( !file_exists( $path ) ){
        throw new Exception( 'the given path for class "'.$classname.'" is wrong, trying to load from '.$path );
    }
    require_once $path;
    if( !interface_exists( $classname ) && !class_exists( $classname ) && !trait_exists( $classname ) ){
        throw new Exception( 'The class "'.$classname.'" is not declared in "'.$path.'" file.' );
    }
}
spl_autoload_register( 'shop_ct_autoload' );
