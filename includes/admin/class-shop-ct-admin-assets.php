<?php

/**
 * Shop_CT_Admin_Assets Class.
 */
class Shop_CT_Admin_Assets
{

    /**
     * Hook in tabs.
     */
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    }

    /**
     * @param $hook
     */
    public function admin_styles($hook)
    {
        if (in_array($hook, SHOP_CT()->admin->menus->toplevel_pages) || in_array($hook, SHOP_CT()->admin->menus->pages)) {


            wp_enqueue_style("shop_ct_admin_styles", SHOP_CT()->plugin_url() . "/assets/css/admin.style.css", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_admin_orders", SHOP_CT()->plugin_url() . "/assets/css/admin.orders.css", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_admin_settings", SHOP_CT()->plugin_url() . "/assets/css/admin.settings.css", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_checkouts_styles", SHOP_CT()->plugin_url() . "/assets/css/admin.checkouts.css", FALSE, SHOP_CT()->version);

            wp_enqueue_style('shop_ct_reviews_style', SHOP_CT()->plugin_url() . '/assets/css/admin.reviews.css', FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_popup_styles", SHOP_CT()->plugin_url() . "/assets/css/popup.style.css", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_product_settings_styles", SHOP_CT()->plugin_url() . "/assets/css/admin.display-settings.css", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_animate_css", SHOP_CT()->plugin_url() . "/assets/css/animate.css", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_font_awesome", SHOP_CT()->plugin_url() . "/lib/fontawesome/css/fontawesome-all.min.css", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_jquery_ui_css", "http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_select2_css", SHOP_CT()->plugin_url() . "/lib/select2/css/select2.css", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_google_fonts_Raleway", "https://fonts.googleapis.com/css?family=Raleway:400,500,400italic", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_google_fonts_Open_sans", "https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,300,300italic,400italic)", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_media_css", SHOP_CT()->plugin_url() . "/assets/css/media.css", false, SHOP_CT()->version);

        }

        if ($hook === 'plugins.php') {
            wp_enqueue_style("shop_ct_animate_css", SHOP_CT()->plugin_url() . "/assets/css/animate.css", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_admin_styles", SHOP_CT()->plugin_url() . "/assets/css/admin.style.css", FALSE, SHOP_CT()->version);

            wp_enqueue_style("shop_ct_popup_styles", SHOP_CT()->plugin_url() . "/assets/css/popup.style.css", FALSE, SHOP_CT()->version);
        }
    }

    public function admin_scripts($hook)
    {

        if (in_array($hook, SHOP_CT()->admin->menus->toplevel_pages) || in_array($hook, SHOP_CT()->admin->menus->pages)) {

            wp_enqueue_media();
            wp_enqueue_editor();
            wp_enqueue_script("jquery-ui-dialog");
            wp_enqueue_script("jquery-ui-datepicker");
            wp_enqueue_script("tags-box");
            wp_enqueue_script("jquery-masonry");
            wp_enqueue_script('quicktags');

            wp_enqueue_script('shop_ct_admin', SHOP_CT()->plugin_url() . "/assets/js/admin/admin.js");

            $admin_url = admin_url();
            $wp_scripts = wp_scripts();


            wp_localize_script('shop_ct_admin', 'shopCTL10n', array(
                'shop_ct_nonce' => wp_create_nonce('shop_ct_nonce'),
                'ajax_url' => admin_url('admin-ajax.php'),
                'admin_url' => $admin_url,
                'plugin_url' => SHOP_CT()->plugin_url(),
                'hook' => $hook,
                'scripts_base_url' => $wp_scripts->base_url,
                'scripts_editor_src' => $wp_scripts->registered['editor']->src,
                'display_settings_click_right' => __('Click the arrow on the right of the item to reveal additional configuration options.', 'shop_ct'),
                'product_published' => __('Product Saved Successfully', 'shop_ct'),
                'serverSideError' => __('Something went wrong while processing your request, please try again later', 'shop_ct'),
                'ask_delete_product' => __('Are you sure you want to delete this product?', 'shop_ct'),
                'ask_delete_products' => __('Are you sure you want to delete ALL selected products?', 'shop_ct'),
                'popupAlert' => __('All inserted data will be lost if you close the popup without saving.', 'shop_ct'),
                'ok' => __('OK', 'shop_ct'),
                'cancel' => __('Cancel', 'shop_ct'),
                'publishOn' => __('Publish on:', 'shop_ct'),
                'publishOnFuture' => __('Schedule for:', 'shop_ct'),
                'publishOnPast' => __('Published on:', 'shop_ct'),
                'dateFormat' => __('%1$s %2$s, %3$s @ %4$s:%5$s', 'shop_ct'),
                'showcomm' => __('Show more comments', 'shop_ct'),
                'endcomm' => __('No more comments found.', 'shop_ct'),
                'publish' => __('Publish', 'shop_ct'),
                'schedule' => __('Schedule', 'shop_ct'),
                'update' => __('Update', 'shop_ct'),
                'savePending' => __('Save as Pending', 'shop_ct'),
                'saveDraft' => __('Save Draft', 'shop_ct'),
                'private' => __('Private', 'shop_ct'),
                'public' => __('Public', 'shop_ct'),
                'publicSticky' => __('Public, Sticky', 'shop_ct'),
                'password' => __('Password Protected', 'shop_ct'),
                'privatelyPublished' => __('Privately Published', 'shop_ct'),
                'published' => __('Published', 'shop_ct'),
                'saveAlert' => __('The changes you made will be lost if you navigate away from this page.', 'shop_ct'),
                'savingText' => __('Saving Draft&#8230;', 'shop_ct'),
                'permalinkSaved' => __('Permalink saved', 'shop_ct'),
                'closePopup' => __('Press \'OK\' to close anyways', 'shop_ct'),
                'name' => __('Name', 'shop_ct'),
                'comma_separated_values' => __('Values separated with commas', 'shop_ct'),
                'noFiles' => __('No Files', 'shop_ct'),
                'addDownloadableFile' => __('Add Downloadable File', 'shop_ct'),
                'insertFile' => __('Insert file', 'shop_ct'),
                'invalidSalePrice' => __('Invalid Discounted Price', 'shop_ct'),
                'order_published' => __('Order Saved Successfully', 'shop_ct'),
            ));

        }

        if ($hook === 'plugins.php') {
            wp_enqueue_script('shop_ct_popup', SHOP_CT()->plugin_url() . "/assets/js/shop-ct-popup.js");
            wp_enqueue_script('shop_ct_deactivation', SHOP_CT()->plugin_url() . "/assets/js/admin/deactivation-feedback.js");
        }
    }
}
