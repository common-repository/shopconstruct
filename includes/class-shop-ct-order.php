<?php

use ShopCT\Model\PostType;

if (!defined('ABSPATH')) {
    exit;
}

class Shop_CT_Order extends PostType
{
	/**
	 * @var string
	 */
	protected static $post_type = 'shop_ct_product';

    /**
     * @var int|string
     */
    private $id;

    /**
     * @var string
     */
    private $shipping_first_name = '';

    /**
     * @var string
     */
    private $shipping_last_name = '';

    /**
     * @var string
     */
    private $shipping_company = '';

    /**
     * @var string
     */
    private $shipping_address_1 = '';

    /**
     * @var string
     */
    private $shipping_address_2 = '';

    /**
     * @var string
     */
    private $shipping_country = '';

    /**
     * @var string
     */
    private $shipping_city = '';

    /**
     * @var string
     */
    private $shipping_postcode = '';

    /**
     * @var string
     */
    private $shipping_state = '';

    /**
     * @var string
     */
    private $shipping_customer_note = '';

    /**
     * @var string
     */
    private $billing_first_name = '';

    /**
     * @var string
     */
    private $billing_last_name = '';

    /**
     * @var string
     */
    private $billing_company = '';

    /**
     * @var string
     */
    private $billing_address_1 = '';

    /**
     * @var string
     */
    private $billing_address_2 = '';

    /**
     * @var string
     */
    private $billing_country = '';

    /**
     * @var string
     */
    private $billing_city = '';

    /**
     * @var string
     */
    private $billing_postcode = '';

    /**
     * @var string
     */
    private $billing_state = '';

    /**
     * @var string
     */
    private $billing_email = '';

    /**
     * @var string
     */
    private $billing_phone = '';

    /**
     * @var string
     */
    private $payment_method = '';

    /**
     * @var string
     */
    private $transaction_id = '';

    /**
     * @var string
     */
    private $status = '';

    /**
     * @var mixed|string
     */
    private $customer = '';

    /**
     * @var mixed|string
     */
    private $date = '';

    /**
     * @var array|int
     */
    private $notes = array();

    /**
     * @var array
     */
    private $deleted_comments = array();

    /**
     * @var array|mixed
     */
    private $products = array();

	/**
	 * @var array
	 */
    private $deleted_products = array();

    /**
     * @var string
     */
    private $initial_status;

    /**
     * @var float|int
     */
    private $shipping_cost;

    /**
     * @var float
     */
    private $subtotal;

    /**
     * Shop_CT_Order constructor.
     *
     * @param int|string $id
     */
    public function __construct($id = NULL)
    {
        if (null !== $id && is_numeric($id)) {
            $id = absint($id);
            $order_post = get_post($id);


            if ($order_post instanceof WP_Post) {
                $this->id = $order_post->ID;
                $this->status = $this->initial_status = $order_post->post_status;

                $this->shipping_first_name = Shop_CT()->order_meta->get($this->id, 'shipping_first_name');
                $this->shipping_last_name = Shop_CT()->order_meta->get($this->id, 'shipping_last_name');
                $this->shipping_company = Shop_CT()->order_meta->get($this->id, 'shipping_company');
                $this->shipping_address_1 = Shop_CT()->order_meta->get($this->id, 'shipping_address_1');
                $this->shipping_address_2 = Shop_CT()->order_meta->get($this->id, 'shipping_address_2');
                $this->shipping_country = Shop_CT()->order_meta->get($this->id, 'shipping_country');
                $this->shipping_city = Shop_CT()->order_meta->get($this->id, 'shipping_city');
                $this->shipping_postcode = Shop_CT()->order_meta->get($this->id, 'shipping_post_code');
                $this->shipping_state = Shop_CT()->order_meta->get($this->id, 'shipping_state');
                $this->shipping_customer_note = Shop_CT()->order_meta->get($this->id, 'shipping_customer_note');

                $this->billing_first_name = Shop_CT()->order_meta->get($this->id, 'billing_first_name');
                $this->billing_last_name = Shop_CT()->order_meta->get($this->id, 'billing_last_name');
                $this->billing_company = Shop_CT()->order_meta->get($this->id, 'billing_company');
                $this->billing_address_1 = Shop_CT()->order_meta->get($this->id, 'billing_address_1');
                $this->billing_address_2 = Shop_CT()->order_meta->get($this->id, 'billing_address_2');
                $this->billing_country = Shop_CT()->order_meta->get($this->id, 'billing_country');
                $this->billing_city = Shop_CT()->order_meta->get($this->id, 'billing_city');
                $this->billing_postcode = Shop_CT()->order_meta->get($this->id, 'billing_post_code');
                $this->billing_state = Shop_CT()->order_meta->get($this->id, 'billing_state');
                $this->billing_email = Shop_CT()->order_meta->get($this->id, 'billing_email');
                $this->billing_phone = Shop_CT()->order_meta->get($this->id, 'billing_phone');

                $this->payment_method = Shop_CT()->order_meta->get($this->id, 'payment_method');
                $this->transaction_id = Shop_CT()->order_meta->get($this->id, 'transaction_id');
                $this->customer = SHOP_CT()->order_meta->get($this->id, 'customer');
                $this->date = SHOP_CT()->order_meta->get($this->id, 'date');
                $this->notes = get_comments(['post_id' => $this->id, 'comment_type' => 'shop_ct_order_note', 'orderby' => 'comment_date', 'order' => 'asc']);

                $this->shipping_cost = Shop_CT()->order_meta->get($this->id, 'shipping_cost');

                $this->products = $this->get_products_from_db();
            }
        } else {
            $order = get_default_post_to_edit('shop_ct_order', true);
            $this->id = $order->ID;
            $this->date = $order->post_date;
            $this->status = 'auto-draft';
        }
    }

    /**
     * @return int|string
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function get_shipping_first_name()
    {
        return $this->shipping_first_name;
    }

    /**
     * @param string $shipping_first_name
     *
     * @return Shop_CT_Order
     */
    public function set_shipping_first_name($shipping_first_name)
    {
        $this->shipping_first_name = $shipping_first_name;

        return $this;
    }

    /**
     * @return string
     */
    public function get_shipping_last_name()
    {
        return $this->shipping_last_name;
    }

    /**
     * @param string $shipping_last_name
     *
     * @return Shop_CT_Order
     */
    public function set_shipping_last_name($shipping_last_name)
    {
        $this->shipping_last_name = $shipping_last_name;

        return $this;
    }

    /**
     * @return string
     */
    public function get_shipping_company()
    {
        return $this->shipping_company;
    }

    /**
     * @param string $shipping_company
     *
     * @return Shop_CT_Order
     */
    public function set_shipping_company($shipping_company)
    {
        $this->shipping_company = $shipping_company;

        return $this;
    }

    /**
     * @return string
     */
    public function get_shipping_address_1()
    {
        return $this->shipping_address_1;
    }

    /**
     * @param string $shipping_address_1
     *
     * @return Shop_CT_Order
     */
    public function set_shipping_address_1($shipping_address_1)
    {
        $this->shipping_address_1 = $shipping_address_1;

        return $this;
    }

    /**
     * @return string
     */
    public function get_shipping_address_2()
    {
        return $this->shipping_address_2;
    }

    /**
     * @param string $shipping_address_2
     *
     * @return Shop_CT_Order
     */
    public function set_shipping_address_2($shipping_address_2)
    {
        $this->shipping_address_2 = $shipping_address_2;

        return $this;
    }

    /**
     * @return string
     */
    public function get_shipping_country()
    {
        return $this->shipping_country;
    }

    /**
     * @param string $shipping_country
     *
     * @return Shop_CT_Order
     */
    public function set_shipping_country($shipping_country)
    {
        $this->shipping_country = $shipping_country;

        return $this;
    }

    /**
     * @return string
     */
    public function get_shipping_city()
    {
        return $this->shipping_city;
    }

    /**
     * @param string $shipping_city
     *
     * @return Shop_CT_Order
     */
    public function set_shipping_city($shipping_city)
    {
        $this->shipping_city = $shipping_city;

        return $this;
    }

    /**
     * @return string
     */
    public function get_shipping_postcode()
    {
        return $this->shipping_postcode;
    }

    /**
     * @param string $shipping_postcode
     *
     * @return Shop_CT_Order
     */
    public function set_shipping_postcode($shipping_postcode)
    {
        $this->shipping_postcode = $shipping_postcode;

        return $this;
    }

    /**
     * @return string
     */
    public function get_shipping_state()
    {
        return $this->shipping_state;
    }

    /**
     * @param string $shipping_state
     *
     * @return Shop_CT_Order
     */
    public function set_shipping_state($shipping_state)
    {
        $this->shipping_state = $shipping_state;

        return $this;
    }

    /**
     * @return string
     */
    public function get_shipping_customer_note()
    {
        return $this->shipping_customer_note;
    }

    /**
     * @param string $shipping_customer_note
     *
     * @return Shop_CT_Order
     */
    public function set_shipping_customer_note($shipping_customer_note)
    {
        $this->shipping_customer_note = $shipping_customer_note;

        return $this;
    }

    /**
     * @return string
     */
    public function get_billing_first_name()
    {
        return $this->billing_first_name;
    }

    /**
     * @param string $billing_first_name
     *
     * @return Shop_CT_Order
     */
    public function set_billing_first_name($billing_first_name)
    {
        $this->billing_first_name = $billing_first_name;

        return $this;
    }

    /**
     * @return string
     */
    public function get_billing_last_name()
    {
        return $this->billing_last_name;
    }

    /**
     * @param string $billing_last_name
     *
     * @return Shop_CT_Order
     */
    public function set_billing_last_name($billing_last_name)
    {
        $this->billing_last_name = $billing_last_name;

        return $this;
    }

    /**
     * @return string
     */
    public function get_billing_company()
    {
        return $this->billing_company;
    }

    /**
     * @param string $billing_company
     *
     * @return Shop_CT_Order
     */
    public function set_billing_company($billing_company)
    {
        $this->billing_company = $billing_company;

        return $this;
    }

    /**
     * @return string
     */
    public function get_billing_address_1()
    {
        return $this->billing_address_1;
    }

    /**
     * @param string $billing_address_1
     *
     * @return Shop_CT_Order
     */
    public function set_billing_address_1($billing_address_1)
    {
        $this->billing_address_1 = $billing_address_1;

        return $this;
    }

    /**
     * @return string
     */
    public function get_billing_address_2()
    {
        return $this->billing_address_2;
    }

    /**
     * @param string $billing_address_2
     *
     * @return Shop_CT_Order
     */
    public function set_billing_address_2($billing_address_2)
    {
        $this->billing_address_2 = $billing_address_2;

        return $this;
    }

    /**
     * @return string
     */
    public function get_billing_country()
    {
        return $this->billing_country;
    }

    /**
     * @param string $billing_country
     *
     * @return Shop_CT_Order
     */
    public function set_billing_country($billing_country)
    {
        $this->billing_country = $billing_country;

        return $this;
    }

    /**
     * @return string
     */
    public function get_billing_city()
    {
        return $this->billing_city;
    }

    /**
     * @param string $billing_city
     *
     * @return Shop_CT_Order
     */
    public function set_billing_city($billing_city)
    {
        $this->billing_city = $billing_city;

        return $this;
    }

    /**
     * @return string
     */
    public function get_billing_postcode()
    {
        return $this->billing_postcode;
    }

    /**
     * @param string $billing_postcode
     *
     * @return Shop_CT_Order
     */
    public function set_billing_postcode($billing_postcode)
    {
        $this->billing_postcode = $billing_postcode;

        return $this;
    }

    /**
     * @return string
     */
    public function get_billing_state()
    {
        return $this->billing_state;
    }

    /**
     * @param string $billing_state
     *
     * @return Shop_CT_Order
     */
    public function set_billing_state($billing_state)
    {
        $this->billing_state = $billing_state;

        return $this;
    }

    /**
     * @return string
     */
    public function get_billing_email()
    {
        return $this->billing_email;
    }

    /**
     * @param string $billing_email
     *
     * @return Shop_CT_Order
     */
    public function set_billing_email($billing_email)
    {
        $this->billing_email = $billing_email;

        return $this;
    }

    /**
     * @return string
     */
    public function get_billing_phone()
    {
        return $this->billing_phone;
    }

    /**
     * @param string $billing_phone
     *
     * @return Shop_CT_Order
     */
    public function set_billing_phone($billing_phone)
    {
        $this->billing_phone = $billing_phone;

        return $this;
    }

    /**
     * @return string
     */
    public function get_payment_method()
    {
        return $this->payment_method;
    }

    /**
     * @param string $payment_method
     *
     * @return Shop_CT_Order
     */
    public function set_payment_method($payment_method)
    {
        $this->payment_method = $payment_method;

        return $this;
    }

    /**
     * @return string
     */
    public function get_transaction_id()
    {
        return $this->transaction_id;
    }

    /**
     * @param string $transaction_id
     *
     * @return Shop_CT_Order
     */
    public function set_transaction_id($transaction_id)
    {
        $this->transaction_id = $transaction_id;

        return $this;
    }

    /**
     * @return string
     */
    public function get_status()
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return bool
     */
    public function has_status($status)
    {
        return $this->status === (string)$status;
    }

    /**
     * @param string $status
     *
     * @return Shop_CT_Order
     */
    public function set_status($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return mixed|string
     */
    public function get_customer()
    {
        return $this->customer;
    }

    /**
     * @param mixed|string $customer
     *
     * @return Shop_CT_Order
     */
    public function set_customer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * @return mixed|string
     */
    public function get_date()
    {
        return $this->date;
    }

    /**
     * @param mixed|string $date
     *
     * @return Shop_CT_Order
     */
    public function set_date($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return array|int
     */
    public function get_notes()
    {
        return $this->notes;
    }

    /**
     * Order notes are WordPress comments.
     *
     * @param array|int $notes
     *
     * @return Shop_CT_Order
     */
    public function set_notes($notes)
    {
        $ids = array_column($notes, 'id');

        foreach ($this->notes as $note) {
            if ($note instanceof WP_Comment && !in_array($note->comment_ID, $ids)) {
                $this->deleted_comments[] = $note->comment_ID;
            }
        }

        $this->notes = $notes;

        return $this;
    }

    /**
     * @param array $comment Array that must have 'text' key.
     *
     * @return $this
     */
    public function add_note($comment)
    {
        if (isset($comment['text'])) {
            $comment['text'] = sanitize_text_field($comment['text']);
            $comment['id'] = 'new';

            $this->notes[] = $comment;
        }

        return $this;
    }

    /**
     * @param int $comment_id Comment ID from comments table.
     *
     * @return $this
     */
    public function remove_note($comment_id)
    {
        $comment_id = absint($comment_id);

        foreach ($this->notes as $index => $note) {
            if (($note instanceof WP_Comment && $note->comment_ID === $comment_id) || (isset($note['id']) && $note['id'] == $comment_id)) {
                unset($this->notes[$index]);

                $this->deleted_comments[] = $comment_id;
            }
        }

        return $this;
    }

    /**
     * @param bool $force
     *
     * @return array
     */
    public function get_products($force = false)
    {
        if (empty($this->products) || $force) {
            $this->products = $this->get_products_from_db();
        }

        return $this->products;
    }

	/**
	 * @param Shop_CT_Product $product
	 * @param int $quantity
	 * @param null $cost
	 */
    public function add_product(Shop_CT_Product $product, $quantity = 1, $cost = null)
    {
    	if ($cost === null) {
    		$cost = $product->get_price();
	    }

        $this->products[$product->get_id()] = array(
        	'object' => $product,
	        'quantity' => $quantity,
	        'cost' => $cost
        );
    }

	public function remove_products()
	{
		if (empty($this->products)) {
			return false;
		}

		foreach ($this->products as $id => $product) {
			$this->deleted_products[] = $id;
		}

		$this->products = array();
    }

    /**
     * If order requires delivery. True if at least 1 product in order needs delivery.
     *
     * @return bool
     */
    public function requires_delivery()
    {
        foreach ($this->products as $product) {
            /** @var Shop_CT_Product $productObject */
            $productObject = $product['object'];

            if ($productObject->needs_shipping()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $product
     *
     * @return bool|false|int
     */
    private function save_product($product)
    {
        if (!$product['object'] instanceof Shop_CT_Product) {
            return false;
        }

        if (!isset($product['quantity']) || !is_numeric($product['quantity'])) {
            $product['quantity'] = 1;
        }

        if(!isset($product['cost'])){
        	$cost = $product['object']->get_price();
        }
        else {
        	$cost = $product['cost'];
        }

        global $wpdb;

        $sql = 'INSERT INTO ' . self::get_order_products_table_name() . '(`order_id`, `product_id`, `quantity`, `cost`) 
			VALUES (' . $this->id . ', ' . $product["object"]->get_id() . ', ' . $product['quantity'] . ', ' . $cost . ')
			ON DUPLICATE KEY UPDATE `quantity` = VALUES(quantity), `cost` = VALUES(cost)';

        $result = $wpdb->query($sql);

        return $result;
    }

    /**
     * @return array
     */
    public function save()
    {
        $shipping_zone = Shop_CT_Shipping_Zone::get_zone_by_location($this->shipping_country);

        if ($this->requires_delivery() && false === $shipping_zone) {
            echo json_encode( array( 'error' => array( 'text' => __('Your order requires shipping but your shipping country is not in our shipping zone.', 'shop_ct') ) ) );
            wp_die();
        }

        global $wpdb;

        $is_auto_draft = 'auto-draft' === get_post_status($this->id);

        $post_result = wp_update_post([
            'ID' => $this->id,
            'post_title' => '#' . $this->id . ' (by ' . $this->customer . ')',
            'post_excerpt' => ' ',
            'post_status' => $this->status,
            'post_name' => 'Shop_CT_Order',
            'post_type' => 'shop_ct_order',
        ]);

        $shipping_first_name_result = Shop_CT()->order_meta->set_if_changed($this->shipping_first_name, $this->id, 'shipping_first_name');
        $shipping_last_name_result = Shop_CT()->order_meta->set_if_changed($this->shipping_last_name, $this->id, 'shipping_last_name');
        $shipping_company_result = Shop_CT()->order_meta->set_if_changed($this->shipping_company, $this->id, 'shipping_company');
        $shipping_address_1_result = Shop_CT()->order_meta->set_if_changed($this->shipping_address_1, $this->id, 'shipping_address_1');
        $shipping_address_2_result = Shop_CT()->order_meta->set_if_changed($this->shipping_address_2, $this->id, 'shipping_address_2');
        $shipping_country_result = Shop_CT()->order_meta->set_if_changed($this->shipping_country, $this->id, 'shipping_country');
        $shipping_city_result = Shop_CT()->order_meta->set_if_changed($this->shipping_city, $this->id, 'shipping_city');
        $shipping_post_code_result = Shop_CT()->order_meta->set_if_changed($this->shipping_postcode, $this->id, 'shipping_postcode');
        $shipping_state_result = Shop_CT()->order_meta->set_if_changed($this->shipping_state, $this->id, 'shipping_state');
        $shipping_customer_note_result = Shop_CT()->order_meta->set_if_changed($this->shipping_customer_note, $this->id, 'shipping_customer_note');
        $billing_first_name_result = Shop_CT()->order_meta->set_if_changed($this->billing_first_name, $this->id, 'billing_first_name');
        $billing_last_name_result = Shop_CT()->order_meta->set_if_changed($this->billing_last_name, $this->id, 'billing_last_name');
        $billing_company_result = Shop_CT()->order_meta->set_if_changed($this->billing_company, $this->id, 'billing_company');
        $billing_address_1_result = Shop_CT()->order_meta->set_if_changed($this->billing_address_1, $this->id, 'billing_address_1');
        $billing_address_2_result = Shop_CT()->order_meta->set_if_changed($this->billing_address_2, $this->id, 'billing_address_2');
        $billing_country_result = Shop_CT()->order_meta->set_if_changed($this->billing_country, $this->id, 'billing_country');
        $billing_city_result = Shop_CT()->order_meta->set_if_changed($this->billing_city, $this->id, 'billing_city');
        $billing_post_code_result = Shop_CT()->order_meta->set_if_changed($this->billing_postcode, $this->id, 'billing_postcode');
        $billing_state_result = Shop_CT()->order_meta->set_if_changed($this->billing_state, $this->id, 'billing_state');
        $billing_email_result = Shop_CT()->order_meta->set_if_changed($this->billing_email, $this->id, 'billing_email');
        $billing_phone_result = Shop_CT()->order_meta->set_if_changed($this->billing_phone, $this->id, 'billing_phone');
        $payment_method_result = Shop_CT()->order_meta->set_if_changed($this->payment_method, $this->id, 'payment_method');
        $transaction_id_result = Shop_CT()->order_meta->set_if_changed($this->transaction_id, $this->id, 'transaction_id');
        $status_result = Shop_CT()->order_meta->set_if_changed($this->status, $this->id, 'status');
        $customer_result = Shop_CT()->order_meta->set_if_changed($this->customer, $this->id, 'customer');
        $date_result = Shop_CT()->order_meta->set_if_changed($this->date, $this->id, 'date');
        $shipping_cost_result = Shop_CT()->order_meta->set_if_changed($this->shipping_cost, $this->id, 'shipping_cost');

        $note_result = $this->save_notes();

        $notes_result = $note_result['notes_result'];
        $delete_note_result = $note_result['deleted_notes_result'];
        $product_result = array();

        if (!empty($this->deleted_products)) {
        	foreach($this->deleted_products as $deleted_product_id) {
				$wpdb->delete(self::get_order_products_table_name(), array('order_id' => $this->id, 'product_id' => $deleted_product_id));
	        }
        }

        foreach ($this->products as $product) {
            $product_result[] = $this->save_product($product);
        }

        if ($status_result) {
            do_action('shop_ct_order_status_' . $this->status, array(
                'id' => $this->id,
                'status' => $this->status,
            ) );
        }

        if ($is_auto_draft) {
            do_action('shop_ct_order_new', array( 'order_data' => array( 'order' => $this ) ) );
        }

        return [
            'post_result' => $post_result,
            'product_result' => $product_result,
            'shipping_first_name_result' => $shipping_first_name_result,
            'shipping_last_name_result' => $shipping_last_name_result,
            'shipping_company_result' => $shipping_company_result,
            'shipping_address_1_result' => $shipping_address_1_result,
            'shipping_address_2_result' => $shipping_address_2_result,
            'shipping_country_result' => $shipping_country_result,
            'shipping_city_result' => $shipping_city_result,
            'shipping_post_code_result' => $shipping_post_code_result,
            'shipping_state_result' => $shipping_state_result,
            'shipping_customer_note_result' => $shipping_customer_note_result,
            'billing_first_name_result' => $billing_first_name_result,
            'billing_last_name_result' => $billing_last_name_result,
            'billing_company_result' => $billing_company_result,
            'billing_address_1_result' => $billing_address_1_result,
            'billing_address_2_result' => $billing_address_2_result,
            'billing_country_result' => $billing_country_result,
            'billing_city_result' => $billing_city_result,
            'billing_post_code_result' => $billing_post_code_result,
            'billing_state_result' => $billing_state_result,
            'billing_email_result' => $billing_email_result,
            'billing_phone_result' => $billing_phone_result,
            'payment_method_result' => $payment_method_result,
            'transaction_id_result' => $transaction_id_result,
            'status_result' => $status_result,
            'customer_result' => $customer_result,
            'date_result' => $date_result,
            'notes_result' => $notes_result,
            'delete_note_result' => $delete_note_result,
            'shipping_cost_result' => $shipping_cost_result,
        ];
    }

    private function save_notes()
    {
        $notes_result = $delete_note_result = array();

        foreach ($this->deleted_comments as $deleted_comment_id) {
            $delete_note_result[] = wp_delete_comment($deleted_comment_id);
        }

        foreach ($this->notes as $note) {
            if ($note instanceof WP_Comment) {
                continue;
            }

            if (isset($note['id'], $note['text']) && 'new' === $note['id']) {
                $notes_result[] = wp_insert_comment( array(
                    'comment_post_ID' => $this->id,
                    'comment_author' => $this->customer,
                    'comment_content' => $note['text'],
                    'comment_type' => 'shop_ct_order_note',
                ) );
            }
        }

        return ['deleted_notes_result' => $delete_note_result, 'notes_result' => $notes_result];
    }

    /**
     * @param int $shipping_cost
     */
    public function set_shipping_cost($shipping_cost = 0)
    {
        $this->shipping_cost = $shipping_cost;
    }

    /**
     * @return float|int
     */
    public function get_shipping_cost()
    {
        if (null !== $this->shipping_cost) {
            return $this->shipping_cost;
        }

        if ($this->requires_delivery()) {
            $zone = Shop_CT_Shipping_Zone::get_zone_by_location($this->shipping_country);

            $is_in_zone = $zone instanceof Shop_CT_Shipping_Zone;

            if ($is_in_zone) {
                return $this->shipping_cost = $zone->get_cost();
            } elseif (Shop_CT_Shipping_Zone::rest_of_the_world_enabled()) {
                $zone = new Shop_CT_Shipping_Zone(1);

                return $this->shipping_cost = $zone->get_cost();
            } else {
                return $this->shipping_cost = 0;
            }
        }

        return $this->shipping_cost = 0;
    }

    private function get_products_from_db()
    {
        global $wpdb;

        $rows = $wpdb->get_results('SELECT product_id, quantity, cost FROM ' . self::get_order_products_table_name() . ' WHERE order_id = ' . $this->id);
        $products = array();

        foreach ($rows as $row) {
            $product = new Shop_CT_Product($row->product_id);

            $products[$product->get_id()]['cost'] = $row->cost;


            $products[$product->get_id()]['object'] = $product;
            $products[$product->get_id()]['quantity'] = $row->quantity;
        }

        return $products;
    }

    public static function get_order_products_table_name()
    {
        return $GLOBALS['wpdb']->prefix . 'shop_ct_order_products';
    }

    /**
     * @param $id
     *
     * @return null|string
     */
    public static function get_products_count($id)
    {
        global $wpdb;

        return $wpdb->get_var('SELECT SUM(quantity) FROM ' . self::get_order_products_table_name() . ' WHERE order_id = ' . absint($id));
    }

    /**
     * @return array
     */
    public static function get_order_statuses()
    {
        return array(
            'shop-ct-pending',
            'shop-ct-processing',
            'shop-ct-on-hold',
            'shop-ct-completed',
            'shop-ct-cancelled',
            'shop-ct-refunded',
            'shop-ct-failed',
        );
    }

	/**
	 * @return array
	 */
	public static function get_order_status_labels()
	{
		return array(
			'shop-ct-pending'    => __( 'Pending Payment', 'exwp' ),
			'shop-ct-processing' => __( 'Processing', 'shop_ct' ),
			'shop-ct-on-hold'    => __( 'On Hold', 'shop_ct' ),
			'shop-ct-completed'  => __( 'Completed', 'shop_ct' ),
			'shop-ct-cancelled'  => __( 'Cancelled', 'shop_ct' ),
			'shop-ct-refunded'   => __( 'Refunded', 'shop_ct' ),
			'shop-ct-failed'     => __( 'Failed', 'shop_ct' ),
		);
	}

    public function get_subtotal()
    {
        if(!$this->subtotal) {
            $this->subtotal = (float)$GLOBALS['wpdb']->get_var('SELECT SUM(cost*quantity) FROM ' . self::get_order_products_table_name() . ' WHERE order_id = ' . absint($this->id));
        }
        return $this->subtotal;
	}

    /**
     *
     * @return float|int
     */
    public function get_total()
    {
        $products_total = $this->get_subtotal();
        $shipping_total = (float)$this->get_shipping_cost();

        return ($products_total + $shipping_total);
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public static function delete($id)
    {
        if (!is_numeric($id) || $id < 1 || $id != (int)$id) {
            return false;
        }

        $id = absint($id);

        $result['post'] = wp_delete_post($id, true);

        if (false !== $result['post']) {
            $GLOBALS['wpdb']->delete(self::get_order_products_table_name(), ['order_id' => $id]);
        }

        return $result;
    }

    /**
     * @param $id
     * @param $status
     *
     * @return bool
     */
    public static function update_status($id, $status)
    {
        if (!in_array($status, self::get_order_statuses())) {
            return false;
        }

        $id = absint($id);

        if (!$id) {
            return false;
        }

        $order = new self($id);

        $old_status = $order->status;
        $order->status = $status;
        $result = $order->save()['status_result'];

        if ('shop-ct-completed' === $status && 'shop-ct-completed' !== $old_status) {
            Shop_CT()->order_meta->set($id, 'complete_date', time());
        }

        return $result;
    }

    /**
     * @param $country_code
     * @param $product_ids
     *
     * @return bool|float|int|null false if country is not in shipping zone, null if order does not require delivery and float|int if requires and country is in shipping zone.
     */
    public static function get_shipping_cost_by_country_and_products($country_code, $product_ids)
    {
        if (!Shop_CT()->locations->is_valid_code($country_code)) {
            return false;
        }

        $requires_delivery = false;

        foreach ($product_ids as $product_id) {
            $product_object = new Shop_CT_Product($product_id);

            if ($product_object->needs_shipping()) {
                $requires_delivery = true;

                break;
            }
        }

        if (!$requires_delivery) {
            return null;
        }

        $zone = Shop_CT_Shipping_Zone::get_zone_by_location($country_code);

        if ($zone instanceof Shop_CT_Shipping_Zone) {
            return $zone->get_cost();
        } elseif (Shop_CT_Shipping_Zone::rest_of_the_world_enabled()) {
            $zone = new Shop_CT_Shipping_Zone(1);

            return $zone->get_cost();
        }

        return false;
    }

    /**
     * Get all completed orders
     * @return array
     */
    public static function all_completed()
    {
        global $wpdb;

        $res = $wpdb->get_results('select ID from '.$wpdb->posts.' where post_status="shop-ct-completed"');

        if(empty($res)){
            return array();
        }

        $o = array();

        foreach($res as $row){
            $o[] = new Shop_CT_Order($row->ID);
        }

        return $o;


    }

}
