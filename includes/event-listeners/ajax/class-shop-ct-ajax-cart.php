<?php

class Shop_CT_Ajax_Cart
{

    public function __construct()
    {
        add_action('wp_ajax_shop_ct_add_to_cart', array($this, 'add_to_cart'));
        add_action('wp_ajax_nopriv_shop_ct_add_to_cart', array($this, 'add_to_cart'));

        add_action('wp_ajax_shop_ct_show_cart',array($this,'show_cart'));
        add_action('wp_ajax_nopriv_shop_ct_show_cart',array($this,'show_cart'));

        add_action('wp_ajax_shop_ct_remove_from_cart',array($this,'remove_from_cart'));
        add_action('wp_ajax_nopriv_shop_ct_remove_from_cart',array($this,'remove_from_cart'));

        add_action('wp_ajax_shop_ct_change_cart_qty',array($this,'change_cart_qty'));
        add_action('wp_ajax_nopriv_shop_ct_change_cart_qty',array($this,'change_cart_qty'));

    }

    public function add_to_cart()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'shop_ct_nonce')) {
            die(0);
        }

        $result = SHOP_CT()->cart_manager->add_to_cart();

        if ($result) {
            die(json_encode(array(
                'count' => SHOP_CT()->cart_manager->get_cart()->get_count(),
            )));
        } else {
            die(json_encode(array(
                'errorMessage' => __('Could not add item to cart', 'shop_ct'),
            )));
        }
    }

    public function remove_from_cart()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'shop_ct_nonce')) {
            die(0);
        }

        SHOP_CT()->cart_manager->remove_from_cart();

        echo json_encode(array(
            'count' => SHOP_CT()->cart_manager->get_cart()->get_count(),
            'count_n' => sprintf(_n('%s item', '%s items',SHOP_CT()->cart_manager->get_cart()->get_count() ), SHOP_CT()->cart_manager->get_cart()->get_count()),
            'total' => Shop_CT_Formatting::format_price(SHOP_CT()->cart_manager->get_cart()->get_total()),
        ));
        die;
    }

    public function change_cart_qty()
    {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'shop_ct_nonce')) {
            die(0);
        }

        SHOP_CT()->cart_manager->change_quantity();
        echo json_encode(array(
            'count' => SHOP_CT()->cart_manager->get_cart()->get_count(),
            'count_n' => sprintf(_n('%s item', '%s items',SHOP_CT()->cart_manager->get_cart()->get_count() ), SHOP_CT()->cart_manager->get_cart()->get_count()),
            'total' => Shop_CT_Formatting::format_price(SHOP_CT()->cart_manager->get_cart()->get_total()),
        ));
        die;
    }

    public function show_cart()
    {
        $cart = SHOP_CT()->cart_manager->get_cart();
		$products = $cart->get_products();


        \ShopCT\Core\TemplateLoader::get_template('frontend/cart/show.view.php',compact('cart'));
        die;
    }



}
