<?php

class Shop_CT_Cart_Manager {

	/**
	 * @var Shop_CT_Cart
	 */
	private $cart;

	/**
	 * Shop_CT_Cart_Manager constructor.
	 */
	public function __construct() {
		add_action('shop_ct_ajax_add_to_cart', array($this, 'add_to_cart'));
		add_action('shop_ct_ajax_remove_from_cart', array($this, 'remove_from_cart'));
		add_action('shop_ct_ajax_change_cart_product_quantity', array($this, 'change_quantity'));
		add_action('shop_ct_remove_old_carts', array($this, 'remove_old_carts'));

		if(!is_admin()){
            $this->get_cart();
        }


		if(!is_user_logged_in()){
            add_action('wp_head',array($this,'print_meta_cart_cookie'),0);
        }

		register_deactivation_hook(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'shop-construct.php', [$this, 'unset_cart_clean_cron_job']);
	}

	public function unset_cart_clean_cron_job() {
		wp_clear_scheduled_hook('shop_ct_remove_old_carts');
	}

	public function remove_old_carts() {
		global $wpdb;

		$wpdb->query('DELETE FROM ' . Shop_CT_Cart::get_table_name() . ' WHERE `user_id` IS NULL AND DATE(updated_at) < ' . date('Y-m-d', time() - MONTH_IN_SECONDS));
	}

	/**
	 * @return Shop_CT_Cart
	 */
	public function get_cart() {
		if (!($this->cart instanceof Shop_CT_Cart)) {
		    if(is_user_logged_in()){
                $this->cart =  new Shop_CT_Cart(null, get_current_user_id());
            }elseif(isset($_COOKIE['shop_ct_cart_hash'])){
                $this->cart = new Shop_CT_Cart($_COOKIE['shop_ct_cart_hash']);
            }elseif(isset($_REQUEST['shop_ct_current_hash'])){
                $this->cart = new Shop_CT_Cart($_REQUEST['shop_ct_current_hash']);
            }else{
                $this->cart = new Shop_CT_Cart();
                $this->update_cookie();
            }
		}

		return $this->cart;
	}

	public function add_to_cart() {
		if (isset($_POST['product_id']) && Shop_CT_Validator::is_valid_id($_POST['product_id'])) {
			$product_id = absint($_POST['product_id']);
			$quantity = isset($_POST['quantity']) && is_numeric($_POST['quantity']) && 1 <= $_POST['quantity'] ? absint($_POST['quantity']) : 1;

			$added = $this->get_cart()->add_product($product_id, $quantity);

			if(!$added) {
			    return false;
            }

			$saved = $this->get_cart()->save();

			if(!$saved) {
			    return false;
            }

			$this->update_cookie();

			return true;
		}

		return false;
	}

	public function remove_from_cart() {
		if (isset($_POST['product_id']) && Shop_CT_Validator::is_valid_id($_POST['product_id'])) {
			$this->get_cart()->remove_product(absint($_POST['product_id']));

			$this->get_cart()->save();
			$this->update_cookie();
		}else{
		    throw new Exception('Wrong value for "product_id" field');
        }
	}

	public function change_quantity() {
		if (isset($_POST['product_id']) && Shop_CT_Validator::is_valid_id($_POST['product_id']) && isset($_POST['quantity']) && is_numeric($_POST['quantity'])) {
			$this->cart->change_quantity($_POST['product_id'], $_POST['quantity']);

			$this->get_cart()->save();
			$this->update_cookie();
		}
	}

	private function update_cookie() {
		setcookie('shop_ct_cart_hash', $this->get_cart()->get_hash(), time() + (86400 * 30),'/');
	}

    public function print_meta_cart_cookie()
    {
        echo '<script>var shopCTCartCookie = \''.$this->get_cart()->get_hash().'\';</script>';
	}
}
