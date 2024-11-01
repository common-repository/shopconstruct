<?php
/**
 * Plugin Name: ShopConstruct
 * Plugin URI: https://shopconstruct.com
 * Description: Independent e-comemrce plugin, working with any theme. Cart and Checkout functionality integrated!
 * Version: 1.1.2
 * Author: ShopConstruct
 * Author URI: https://shopconstruct.com
 * Requires at least: 4.0.0
 * Tested up to: 5.2.1
 * Text Domain: shop_ct
 */

use ShopCT\Core\Geolocation;
use ShopCT\Core\Locations;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require 'config.php';
require_once(__DIR__ . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php");

if (!class_exists('Shop_CT')) :
    /**
     * Main ShopConstruct Class.
     *
     * @class Shop_CT
     */
    final class Shop_CT
    {

        /**
         * Shop_CT version.
         *
         * @var string
         */
        public $version = "1.1.2";

        public $license = 'free';

        /**
         * @var string[]
         */
        private $event_listeners;

        /**
         * Admin instance
         *
         * @var Shop_CT_Admin
         */
        public $admin = null;

        /**
         * Countries instance.
         *
         * @var Locations
         */
        public $locations = null;

        /**
         * Emails instance.
         *
         * @var null
         */
        public $emails = null;

        /**
         * The single instance of the class.
         *
         * @var Shop_CT
         */
        private static $_instance = null;

        /**
         * Session instance.
         *
         * @var Shop_CT_Session
         */
        public $session = null;

        /**
         * Customer instance.
         *
         * @var Shop_CT_Customer
         */
        public $customer = null;

        /**
         * Shop CT product attribute table name.
         *
         * @var string
         */
        private $product_attribute_table_name;

        /**
         * @var string
         */
        private $order_item_table_name;

        /**
         * @var string
         */
        private $order_item_meta_table_name;

        /**
         * @var Shop_CT_Meta
         */
        public $product_meta;

        /**
         * @var Shop_CT_Product_Settings
         */
        public $product_settings;

        /**
         * @var Shop_CT_Service_Settings
         */
        public $settings;

        /**
         * @var Shop_CT_Meta
         */
        public $order_meta;
        /**
         * @var Shop_CT_Checkout
         */
        public $checkout;

        /**
         * @var Shop_CT_Payment_Gateways
         */
        public $payment_gateways;

        /**
         * @var Shop_CT_Email_Settings
         */
        public $email_settings;

        /**
         * @var Shop_CT_Cart_Manager
         */
        public $cart_manager;

        /**
         * @var Shop_CT_Routing
         */
        public $routing;

        /**
         * Main Shop_CT Instance.
         *
         * Ensures only one instance of Shop_CT is loaded or can be loaded.
         *
         * @static
         * @see SHOP_CT()
         * @return Shop_CT - Main instance.
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        private function __clone()
        {
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'shop_ct'), '2.1');
        }

        /**
         * Unserializing instances of this class is forbidden.
         */
        private function __wakeup()
        {
            _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?', 'shop_ct'), '2.1');
        }

        /**
         * Shop_CT Constructor.
         */
        private function __construct()
        {
            if(!is_null(self::$_instance)){
                throw new Exception('Trying to create duplicate object from Shop_CT class which is meant to be singleton');
            }

            $this->product_attribute_table_name = $GLOBALS['wpdb']->prefix . 'shop_ct_attributes';
            $this->order_item_table_name = $GLOBALS['wpdb']->prefix . 'shop_ct_order_items';
            $this->order_item_meta_table_name = $GLOBALS['wpdb']->prefix . 'shop_ct_order_item_meta';

            $this->event_listeners = apply_filters('shop_ct_register_event_listeners', array(
                'common' => array(
                    'Shop_CT_Comment_Filter',
                    'Shop_CT_Order_Completed',
                ),
                'frontend' => array(
                    'Shop_CT_Frontend_Scripts',
                    'Shop_CT_Cart_Actions',
                    'Shop_CT_Template_Manager'
                ),
                'ajax' => array(
                    'Shop_CT_Ajax_Display_Settings',
                    'Shop_CT_Ajax_Settings',
                    'Shop_CT_Ajax_Cart',
                    'Shop_CT_Ajax_Admin_Products',
                ),
                'admin' => array(

                ),
            ));

            $this->define_constants();
            $this->includes();
            $this->init_hooks();

            global $shop_ct_plugin_url, $shop_ct_plugin_path;
            $shop_ct_plugin_path = untrailingslashit(plugin_dir_path(__FILE__));
            $shop_ct_plugin_url = plugins_url('', __FILE__);

            do_action('shop_ct_loaded');
        }

        /**
         * Hook into actions and filters.
         * @since  1.0
         */
        private function init_hooks()
        {
            register_activation_hook(__FILE__, array('Shop_CT_Install', 'install'));
            add_action('after_setup_theme', array($this, 'setup_environment'));
            add_action('init', array('Shop_CT_Install', 'check_version'), 0);
            add_action('init', array($this, 'init'), 1);
            add_action('init', array($this, 'apply_event_listeners'), 2);
            add_action('init', array('Shop_CT_Admin', 'gutenberg_block'));
            add_action('init', array('Shop_CT_Admin', 'gutenberg_block'));
            add_filter( 'block_categories', array('Shop_CT_Admin', 'gutenberg_block_categories'), 10, 2 );

            add_action( 'plugins_loaded', array($this, 'textdomain') );
        }

        /**
         * Define ECWP Constants.
         */
        private function define_constants()
        {
            $upload_dir = wp_upload_dir();

            define('SHOP_CT_PLUGIN_FILE', __FILE__);
            define('SHOP_CT_PLUGIN_BASENAME', plugin_basename(__FILE__));
            define('SHOP_CT_VERSION', $this->version);
            define('SHOP_CT_ROUNDING_PRECISION', 4);
            define('SHOP_CT_DISCOUNT_ROUNDING_MODE', 2);
            define('SHOP_CT_TAX_ROUNDING_MODE', 'yes' === get_option('shop_ct_prices_include_tax', 'no') ? 2 : 1);
            define('SHOP_CT_DELIMITER', ',');
            define('SHOP_CT_LOG_DIR', $upload_dir['basedir'] . '/shop-ct-logs/');
            define('SHOP_CT_SESSION_CACHE_GROUP', 'shop_ct_session_id');
            define('SHOP_CT_IMAGES_PATH', $this->plugin_path() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR);
            define('SHOP_CT_IMAGES_URL', $this->plugin_url() . '/assets/images/');
            define('SHOP_CT_TEMPLATES_PATH', $this->plugin_path() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR);
            define('SHOP_CT_EMAIL_TEMPLATES_PATH', SHOP_CT_TEMPLATES_PATH . 'emails' . DIRECTORY_SEPARATOR);
            define('SHOP_CT_ADMIN_EMAIL_TEMPLATES_PATH', SHOP_CT_EMAIL_TEMPLATES_PATH . 'admin' . DIRECTORY_SEPARATOR);
            define('SHOP_CT_CUSTOMER_EMAIL_TEMPLATES_PATH', SHOP_CT_EMAIL_TEMPLATES_PATH . 'customer' . DIRECTORY_SEPARATOR);
        }

        /**
         * What type of request is this?
         * string $type ajax, frontend or admin.
         *
         * @param $type
         * @return bool
         */
        private function is_request($type)
        {
            switch ($type) {
                case 'admin' :
                    return is_admin();
                case 'ajax' :
                    return defined('DOING_AJAX');
                case 'cron' :
                    return defined('DOING_CRON');
                case 'frontend' :
                    return !is_admin() && !defined('DOING_CRON');
            }
        }

        /**
         * Include required core files used in admin and on the frontend.
         */
        public function includes()
        {
            require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'shop-ct-functions.php';


            if(defined('DOING_AJAX') && DOING_AJAX) {
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'shop-ct-ajax.php';
            }

            include_once('includes/helpers/shop-ct-core-functions.php');

            include_once('includes/services/ajax/ajax.php');
            include_once('includes/services/ajax/ajax-attributes.php');
            include_once('includes/services/ajax/ajax-email.php');
            include_once('includes/services/ajax/ajax-orders.php');
            include_once('includes/services/ajax/ajax-products.php');
            include_once('includes/services/ajax/ajax-reviews.php');
            include_once('includes/services/ajax/ajax-sessions.php');
            include_once('includes/services/ajax/ajax-sessions.php');
            include_once('includes/services/ajax/ajax-terms.php');
            include_once('includes/services/ajax/ajax.shipping-zones.php');

        }

        /**
         * Init ShopConstruct when WordPress initialises.
         */
        public function init()
        {
            do_action('before_shop_ct_init');

            Shop_CT_Post_types::init();
            Geolocation::init();
            Shop_CT_Shortcodes::init();

            if($this->isAdvanced()) {
                require_once __DIR__ . DIRECTORY_SEPARATOR . 'advanced' . DIRECTORY_SEPARATOR . 'shop-ct-advanced.php';
            }


            $this->routing = new Shop_CT_Routing();
            $this->order_meta = new Shop_CT_Meta($GLOBALS['wpdb']->prefix . 'shop_ct_order_meta');
            $this->product_meta = new Shop_CT_Meta($GLOBALS['wpdb']->prefix . 'shop_ct_product_meta');

            $this->checkout = new Shop_CT_Checkout();


            $this->product_settings = Shop_CT_Product_Settings::instance();
            $this->settings = Shop_CT_Service_Settings::instance();

            $this->locations = new Locations();

            $this->emails = new Shop_CT_Email();

            $this->payment_gateways = Shop_CT_Payment_Gateways::instance();

            $this->cart_manager = new Shop_CT_Cart_Manager();

            if ($this->is_request('admin')) {
                $this->admin = new Shop_CT_Admin();
            }

            //if ($this->is_request('frontend')) {
                //$this->cart     = new Shop_CT_Cart();
                //$this->customer = new Shop_CT_Customer();
            //}

            do_action('shop_ct_init');
        }

        public function apply_event_listeners()
        {
            if (empty($this->event_listeners)) {
                throw new Exception('Something is wrong with ShopConstruct: Event Listeners are missing, please try to update the plugin or contact customer support');
            }

            foreach ($this->event_listeners as $request_type => $listeners) {
                if ($request_type != 'common' && !$this->is_request($request_type)) {
                    continue;
                }


                foreach ($listeners as $listener) {
                    new $listener;
                }
            }

        }

        /**
         * Ensure theme and server variable compatibility and setup image sizes.
         */
        public function setup_environment()
        {
            define('SHOP_CT_TEMPLATE_PATH', $this->template_path());
            $this->add_thumbnail_support();
        }

        /**
         * Ensure post thumbnail support is turned on.
         */
        private function add_thumbnail_support()
        {
            if (!current_theme_supports('post-thumbnails')) {
                add_theme_support('post-thumbnails');
            }
            add_post_type_support('shop_ct_product', 'thumbnail');
        }

        /**
         * Get the template path.
         * @return string
         */
        public function template_path()
        {
            return apply_filters('shop_ct_template_path', 'shop-ct/');
        }

        /**
         * Get Ajax URL.
         * @return string
         */
        public function ajax_url()
        {
            return admin_url('admin-ajax.php', 'relative');
        }

        /**
         * @return string
         */
        public function get_product_attribute_table_name()
        {
            return $this->product_attribute_table_name;
        }

        /**
         * @return string
         */
        public function get_order_item_table_name()
        {
            return $this->order_item_table_name;
        }

        public function get_order_item_meta_table_name()
        {
            return $this->order_item_meta_table_name;
        }

        /**
         * ShopConstruct Plugin Path.
         *
         * @var string
         * @return string
         */
        public function plugin_path()
        {
            return untrailingslashit(plugin_dir_path(__FILE__));
        }

        /**
         * ShopConstruct Plugin Url.
         * @return string
         */
        public function plugin_url()
        {
            return plugins_url('', __FILE__);
        }

        public function isAdvanced()
        {
            return file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'advanced' . DIRECTORY_SEPARATOR . 'shop-ct-advanced.php');
        }

        public function textdomain()
        {
            load_plugin_textdomain( 'shop_ct', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
        }
    }

endif;

/**
 * Main instance of Shop_CT.
 *
 * Returns the main instance of ECWP to prevent the need to use globals.
 *
 * @since  2.1
 * @return Shop_CT
 */

function SHOP_CT()
{
    return Shop_CT::instance();
}

$GLOBALS['shop_ct'] = SHOP_CT();
