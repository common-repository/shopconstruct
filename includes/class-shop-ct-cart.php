<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

class Shop_CT_Cart {
    /**
     * @var string
     */
    private $hash;

    /**
     * @var int
     */
    private $user_id;

    /**
     * @var array
     * [
     *      int => [
     *          'object' => Shop_CT_Product,
     *          'quantity' => int
     *      ]
     * ]
     */
    private $products = array();

    private $removed_products = array();

    /**
     * Shop_CT_Cart constructor.
     *
     * @param null|string $hash
     * @param null|int $user_id
     */
    public function __construct($hash = null, $user_id = null) {
        $this->hash = $hash;
        if (null !== $user_id && is_numeric($user_id)) {
            $this->user_id = $user_id;
            $this->hash = $this->get_hash_for_user($user_id);

            if (!$this->hash) {
                $this->hash = bin2hex(random_bytes(16));
            }
            $this->set_products_from_db();
        } elseif (null !== $hash) {
            $this->set_products_from_db();

            if (is_user_logged_in()) {
                $this->user_id = get_current_user_id();
            }
        } else {
            $this->hash = bin2hex(random_bytes(16));
        }
    }

    /**
     * @return string
     */
    public function get_hash() {
        return $this->hash;
    }

    /**
     * @return int
     */
    public function get_user_id() {
        return $this->user_id;
    }

    /**
     * @return array
     */
    public function get_products() {
        return $this->products;
    }

    /**
     * @param int $id
     * @param int $quantity
     *
     * @return bool
     *
     */
    public function add_product($id, $quantity = 1) {
        if (!is_numeric($id) || $id < 1) {
            return false;
        }

        $id = absint($id);
        $quantity = absint($quantity);
        $index = $this->has_product($id);

        if (false === $index) {
            $index = $id;
            $product = new Shop_CT_Product($id);

            if ($product instanceof Shop_CT_Product) {
                $this->products[$id]['object'] = $product;

            }
        } else {
            $quantity = $this->products[$index]['quantity'] + ($quantity > 1 ? $quantity: 1);

            /** @var Shop_CT_Product $product */
            $product = $this->products[$index]['object'];
        }

        if($quantity>1 && $product->is_sold_individually()){
            $quantity = 1;
        }

        if(!$product->has_enough_stock($quantity)) {
            return false;
        }


        $this->products[$index]['quantity'] = $quantity;

        return true;
    }

    /**
     * @param $id int
     * @param int $quantity
     * @return bool
     */
    public function change_quantity($id, $quantity=1)
    {
        if (!is_numeric($id) || $id < 1) {
            return false;
        }
        $id = absint($id);
        $quantity = (int)$quantity;
        $index = $this->has_product($id);

        if(false === $index){
            return false;
        }

        /** @var Shop_CT_Product $product */
        $product = $this->products[$index]['object'];

        if($quantity>1 && $product->is_sold_individually()){
            $quantity = 1;
        }

        if(1 > $quantity){
            return $this->remove_product($id);
        }

        if(!$product->has_enough_stock($quantity)) {
            return false;
        }

        $this->products[$index]['quantity'] = $quantity;

        return true;
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function remove_product($id) {
        if (!is_numeric($id) || $id < 1) {
            return false;
        }

        $id = absint($id);

        foreach ($this->products as $index => $product) {
            if ($product['object']->get_id() === $id) {
                unset($this->products[$index]);
                $this->removed_products[] = $id;
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function save() {
        global $wpdb;

        $results = array();

        if(!empty($this->removed_products)){
            foreach($this->removed_products as $removed_product){
                $wpdb->query($wpdb->prepare("DELETE FROM ".self::get_table_name()." WHERE product_id=%d AND hash=%s",$removed_product,$this->hash));
            }
        }

        if (null === $this->user_id) {
            foreach ($this->products as $product) {
                $results[$product['object']->get_id()] = $wpdb->query($wpdb->prepare("INSERT INTO " . self::get_table_name() . " (`hash`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES (%s, %d, %d, NOW(), NOW()) ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), updated_at = NOW()",$this->hash,$product['object']->get_id(), $product['quantity']));
            }
        } else {
            foreach ($this->products as $product) {
                $results[$product['object']->get_id()] = $wpdb->query($wpdb->prepare("INSERT INTO " . self::get_table_name() . " (`hash`, `product_id`, `user_id`, `quantity`, `created_at`, `updated_at`) VALUES (%s, %d, %d, %d, NOW(), NOW()) ON DUPLICATE KEY UPDATE quantity = VALUES(quantity), updated_at = NOW()",$this->hash,$product['object']->get_id(),$this->user_id,$product['quantity']));
            }
        }

        return true;
    }

    /**
     * @param int $user_id
     *
     * @return null|string
     */
    private function get_hash_for_user($user_id) {
        global $wpdb;

        return $wpdb->get_var("SELECT `hash` FROM " . self::get_table_name() . " WHERE `user_id` = " . $user_id);
    }

    /**
     * @return bool
     */
    private function set_products_from_db() {
        if (null === $this->hash) {
            return false;
        }

        global $wpdb;

        $data = $wpdb->get_results($wpdb->prepare("SELECT `product_id`, `quantity` FROM " . self::get_table_name() . " WHERE `hash` = %s",$this->hash) );

        foreach ($data as $pair) {
            $this->products[] = [
                'object' => new Shop_CT_Product($pair->product_id),
                'quantity' => $pair->quantity,
            ];
        }

        if (empty($data)) {
            return false;
        }

        return true;
    }

    /**
     * @param int $id
     *
     * @return bool|int
     */
    private function has_product($id) {
        foreach ($this->products as $key => $product) {
            if ($product['object']->get_id() == $id) {
                return $key;
            }
        }

        return false;
    }

    /**
     * @param bool $without_tax
     *
     * @return int
     */
    public function get_total($without_tax = true) {
        $sum = 0;

        foreach ($this->products as $product) {
            $sum += $product['quantity'] * $product['object']->get_price();
        }

        return $without_tax ? $sum : $sum * 1; // todo: replace 1 with tax rate.
    }

    /**
     * @return string
     */
    public static function get_table_name() {
        return $GLOBALS['wpdb']->prefix . 'shop_ct_cart';
    }

    /**
     * @return int
     */
    public function get_count()
    {
        if(empty($this->products)){
            return 0;
        }

        $count = 0 ;
        foreach($this->products as $product){
            $count += $product['quantity'];
        }

        return $count;
    }

    /**
     * @param $hash
     *
     * @return int|false The number of rows updated, or false on error.
     */
    public static function delete($hash) {
        $hash = sanitize_text_field($hash);

        setcookie('shop_ct_cart_hash', '', time() -3600,'/');
        unset($_COOKIE['shop_ct_cart_hash']);
        return $GLOBALS['wpdb']->delete(self::get_table_name(), ['hash' => $hash]);
    }

    /**
     * If order requires delivery. True if at least 1 product in order needs delivery.
     *
     * @return bool
     */
    public function requires_delivery() {
        foreach ($this->products as $product) {
            /** @var Shop_CT_Product $productObject */
            $productObject = $product['object'];

            if ($productObject->needs_shipping()) {
                return true;
            }
        }

        return false;
    }
}