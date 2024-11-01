<?php

/**
 * ShopConstruct Admin
 *
 * @class       Shop_CT_Admin
 * @category    Admin
 * @package     ShopConstruct/Admin
 * @version     2.3
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Shop_CT_Admin class.
 */
class Shop_CT_Admin
{

    /**
     * Current page of admin panel
     */
    public $current_page = "";

    /**
     * Admin menus instance
     * @var Shop_CT_Admin_Menus
     */
    public $menus = null;

    /**
     * Assets for shop_ct pages
     * @var Shop_CT_Admin_Assets
     */
    public $assets = null;

    /**
     * Constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, 'includes'));
        add_action('current_screen', array($this, 'screen_specific_hooks'));
        add_action('wp_ajax_shop_ct_deactivation_feedback', array($this, 'send_deactivation_feedback'));
    }

    /**
     * Include any classes we need within admin.
     */
    public function includes()
    {

        include_once('list-table/class-shop-ct-list-table.php');
        include_once('list-table/class-shop-ct-list-table-attribute-taxonomies.php');
        include_once('list-table/class-shop-ct-list-table-order.php');
        include_once('list-table/class-shop-ct-list-table-reviews.php');
        include_once('list-table/class-shop-ct-list-table-products.php');

        $this->assets = apply_filters('shop_ct_admin_assets', $this->assets);
        $this->assets = apply_filters('shop_ct_admin_scripts', $this->assets);
        $this->assets = apply_filters('shop_ct_admin_styles', $this->assets);

        $this->init();


    }

    public function init()
    {
        $this->assets = new Shop_CT_Admin_Assets();

        $this->menus = new Shop_CT_Admin_Menus();
    }

    public function screen_specific_hooks()
    {
        $screen = get_current_screen();
        if('plugins' === $screen->id){
            add_action('admin_footer',array($this,'deactivation_modal'));
        }
    }

    public function deactivation_modal()
    {
        \ShopCT\Core\TemplateLoader::get_template('admin/deactivation-modal.view.php');
    }

    public static function gutenberg_block()
    {
        if (!function_exists('register_block_type')) {
            return;
        }

        wp_register_script(
            'shop_ct_gutenberg_block',
            SHOP_CT()->plugin_url() . '/assets/js/admin/admin.blocks.js',
            array('wp-blocks', 'wp-element', 'wp-components')
        );
        wp_register_style(
            'shop_ct_gutenberg_block',
            SHOP_CT()->plugin_url() . '/assets/css/admin.blocks.css',
            array('wp-edit-blocks')
        );

        $products = Shop_CT_Product::all();
        $categories = Shop_CT_Product_Category::all();

        $productOptions = array();
        $productMetas = array();
        $categoryOptions = array();
        $categoryMetas = array();

        if (!empty($products)) {
            foreach ($products as $product) {
                $productOptions[] = [
                    'value' => $product->get_id(),
                    'label' => $product->get_title(),
                ];
                $productMetas[$product->get_id()] = array(
                    'title' => $product->get_title(),
                );
            }
        }

        if (!empty($categories)) {
            foreach ($categories as $category) {
                $categoryOptions[] = [
                    'value' => $category->get_id(),
                    'label' => $category->get_name(),
                ];
                $categoryMetas[$category->get_id()] = array(
                    'title' => $category->get_name(),
                );
            }
        }


        wp_localize_script('shop_ct_gutenberg_block', 'shopCTBlockI10n', array(
            'products' => $productOptions,
            'productMetas' => $productMetas,
            'categories' => $categoryOptions,
            'categoryMetas' => $categoryMetas
        ));

        register_block_type('shop-ct/product', array(
            'editor_script' => 'shop_ct_gutenberg_block',
            'editor_style' => 'shop_ct_gutenberg_block',
        ));
    }

    public static function gutenberg_block_categories($categories, $post)
    {
        if (!in_array($post->post_type,array('post','page'))) {
            return $categories;
        }
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'shop-ct',
                    'title' => __('ShopConstruct', 'photo-portfolio'),
                ),
            )
        );
    }

    public function send_deactivation_feedback()
    {
        if(!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'],'shop_ct_deactivation_feedback')){
            die(0);
        }

        $data = array(
            'version' => SHOP_CT()->version,
            'license' => SHOP_CT()->license,
            'reason' => sanitize_text_field($_POST['value']),
            'comment' => sanitize_text_field($_POST['comment']),
        );

        if(!isset($_POST['anon']) || $_POST['anon'] === 'no') {
            $data['site_url']= home_url();
            $data['email'] = get_option('admin_email');
        }

        wp_remote_post('https://shopconstruct.com/api/v1/deactivation-feedback', array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'blocking' => true,
            'headers' => array(),
            'body' => $data,
        ));

        echo 'ok';
        die;
    }

}
