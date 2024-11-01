<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Template Loader
 *
 * @class \ShopCT\Core\TemplateLoader
 */
class Shop_CT_Template_Manager
{
    public function __construct()
    {
        add_filter('template_include', array($this, 'template_loader'));
        add_action("comment_form_top", array($this, 'comment_top_loader'));
    }

    public static function order_details_page()
    {
        if (!isset($_GET['oid'])) {
            global $wp_query;

            $wp_query->set_404();
            status_header(404);
        } else {
            $order = new Shop_CT_Order($_GET['oid']);

            do_action('shop_ct_order_success_page', $order);

            \ShopCT\Core\TemplateLoader::get_template('frontend/orders/order-details.view.php', compact('order'));
        }


        exit;
    }

    public static function handle_product_file_download()
    {
        if (!isset($_GET['pid']) || !isset($_GET['pid']) || !isset($_GET['email']) || !isset($_GET['token'])) {
            global $wp_query;

            $wp_query->set_404();
            status_header(404);
        } else {
            global $wp_query, $wpdb;

            $order = new Shop_CT_Order($_GET['oid']);
            $product = new Shop_CT_Product($_GET['pid']);
            $email = sanitize_email($_GET['email']);

            $permission = $product->get_download_permission($order->get_id(), $email);

            if (false !== $permission && $product->is_valid_download_token($order->get_id(), $permission['token'], $email)):

                if ($product->is_download_permission_expired($order->get_id(), $email) || $product->is_download_limit_expired($order->get_id(), $email)):
                    \ShopCT\Core\TemplateLoader::get_template('frontend/orders/download-expired.view.php',compact('product','order'));
                else:

                    self::handle_download($product,$order,$permission);

                    exit;
                endif;
            else:
                $wp_query->set_404();
                status_header(404);
            endif;


        }
    }

    private static function handle_download(Shop_CT_Product $product, Shop_CT_Order $order,$permission)
    {
        global $wpdb;

        if (null !== $permission['limit']) {
            $wpdb->update(
                Shop_CT_Product::get_download_permissions_table_name(),
                array('limit' => --$permission['limit']),
                array('order_id' => $order->get_id(), 'product_id' => $product->get_id(), 'email' => $order->get_billing_email())
            );
        }

        if(!file_exists('./shop-ct-temp')){
            mkdir('./shop-ct-temp');
        }

        $files = $product->get_downloadable_files();
        $temp_files = array_map(function($file){
            $tmpfname = tempnam("./shop-ct-temp", "FOO");
            copy($file['url'],$tmpfname);
            return $tmpfname;
        },$files);

        $zipname = 'order#'.$order->get_id().'.zip';
        $zippath = './shop-ct-temp/'.$zipname;

        $zip = new ZipArchive();

        $zip->open($zippath, ZipArchive::CREATE);

        foreach ($files as $i=>$file) {
            $zip->addFile($temp_files[$i],basename($file['url']));
        }
        $zip->close();
        header('Content-Type: application/zip');
        header('Content-disposition: attachment; filename='.$zipname);
        header('Content-Length: ' . filesize($zippath));
        readfile($zippath);

        foreach ($files as $i=>$file) {
            unlink($temp_files[$i]);
        }

        unlink($zippath);


    }

    public function comment_top_loader()
    {
        if (is_singular('shop_ct_product') || (isset($GLOBALS["shop_ct_single_product"]) && $GLOBALS["shop_ct_single_product"] != "")) {
            //todo: use product methods
            if (get_option("shop_ct_product_settings_enable_review_rating", "yes") != "yes") return;
            ?>
            <input type="hidden" name="shop_ct_product_review_required" value="true"/>
            <div class="review_stars_wrapper">
                <div class="review_stars">
                    <input class="review_star review_star-5" id="review_star-5" type="radio" name="review_star"
                           value="5"/>
                    <label class="review_star review_star-5 fa fa-star" for="review_star-5"></label>
                    <input class="review_star review_star-4" id="review_star-4" type="radio" name="review_star"
                           value="4"/>
                    <label class="review_star review_star-4 fa fa-star" for="review_star-4"></label>
                    <input class="review_star review_star-3" id="review_star-3" type="radio" name="review_star"
                           value="3"/>
                    <label class="review_star review_star-3 fa fa-star" for="review_star-3"></label>
                    <input class="review_star review_star-2" id="review_star-2" type="radio" name="review_star"
                           value="2"/>
                    <label class="review_star review_star-2 fa fa-star" for="review_star-2"></label>
                    <input class="review_star review_star-1" id="review_star-1" type="radio" name="review_star"
                           value="1"/>
                    <label class="review_star review_star-1 fa fa-star" for="review_star-1"></label>
                </div>
                <p id="single_product_rating_word"></p>
            </div>
            <?php
        }
    }

    public function product_content($content)
    {
        if (is_singular(Shop_CT_Product::get_post_type())):
            $product = new Shop_CT_Product(get_the_ID());

            $content = \ShopCT\Core\TemplateLoader::get_template_buffer('frontend/product/show.php', array('product' => $product));


        endif;

        return $content;
    }

    public function template_loader($template)
    {
        $new_template = '';
        /**
         * Product Category
         */
        if (is_tax(Shop_CT_Product_Category::get_taxonomy())) {

            $new_template = \ShopCT\Core\TemplateLoader::locate_template('frontend/product-category/show.template.view.php');
        }

        /**
         * Product Tag
         */
        if (is_tax(Shop_CT_Product_Tag::get_taxonomy())) {

            $new_template = \ShopCT\Core\TemplateLoader::locate_template('frontend/product-tag/show.php');

        }

        /**
         * single product page
         */
        if (is_singular(Shop_CT_Product::get_post_type())) {
            $new_template = \ShopCT\Core\TemplateLoader::locate_template('frontend/product/show/index.template.view.php');
        }


        if (is_singular()) {
            $id = get_the_ID();
            /**
             * Checkout page
             */
            if ($id == SHOP_CT()->checkout->settings->checkout_page_id) {
                $new_template = \ShopCT\Core\TemplateLoader::locate_template('frontend/checkout/show.template.view.php');
            }
        }

        if ('' != $new_template) {
            return $new_template;
        }

        return $template;
    }
}

