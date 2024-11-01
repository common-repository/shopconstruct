<?php

use ShopCT\Model\PostType;

/**
 * Abstract Product Class
 *
 * The Shop_CT product class handles individual product data.
 *
 * @class       Shop_CT_Product
 * @category    Abstract Class
 *
 */
class Shop_CT_Product extends PostType
{

    /**
     * @var string
     */
    protected static $post_type = 'shop_ct_product';
    /**
     * The product (post) ID.
     *
     * @var int
     */
    protected $ID = 0;
    /**
     * The product (post) ID, the same as $ID but this is done to avoid errors.
     *
     * @var int
     */
    protected $id = 0;
    /**
     * $post Stores post data.
     *
     * @var WP_Post
     */
    protected $post = null;
    /**
     * Current product's type: simple|external
     *
     * @var string
     */
    protected $product_type;
    /**
     * @var string
     */
    protected $visibility;
    /**
     * Stock Keeping Unit
     *
     * @var string
     */
    protected $sku;
    /**
     * @var int
     * @values [0,1]
     */
    protected $manage_stock;
    /**
     * Product stock amount
     *
     * @var int
     */
    protected $stock;
    /**
     * Stock status
     *
     * @var string
     * @values [instock,outofstock]
     */
    protected $stock_status;
    /**
     * @var string
     * @values ['no','notify','yes']
     */
    protected $backorders;
    /**
     * The product's total stock, including that of its children.
     *
     * @var int
     */
    protected $total_stock;
    /**
     * @var int
     */
    protected $width;
    /**
     * @var int
     */
    protected $length;
    /**
     * @var int
     */
    protected $height;
    /**
     * @var int
     */
    protected $weight;
    /**
     * Product price, calculated based on regular price and sale price
     *
     * @var int
     */
    protected $price;
    /**
     * @var float
     */
    protected $regular_price;
    /**
     * @var float
     */
    protected $sale_price;
    /**
     * @var float
     */
    public $final_price;
    /**
     *
     * @var string
     */
    protected $sale_price_dates_from;
    /**
     *
     * @var int
     */
    protected $sale_price_dates_to;
    /**
     * Allow one item to be bought in a single order
     *
     * @var int
     * @values [0,1]
     */
    protected $sold_individually;
    /**
     * @var int
     * @values [0,1]
     */
    protected $virtual;
    /**
     * @var int
     * @values [0,1]
     */
    protected $downloadable;
    /**
     * Arrays of files
     * structure
     * [
     *      [name:'filename',url:'file_url']
     * ]
     * @var array[]
     */
    protected $downloadable_files;
    /**
     * @var int[]
     */
    protected $product_image_gallery;
    /**
     * @var int
     */
    protected $image_id;
    /**
     * @var int
     */
    protected $download_limit;
    /**
     * the number of days before a download link expires, or can be left blank
     *
     * @var int
     */
    protected $download_expiry;
    /**
     * @var string
     * @values application|music
     */
    protected $download_type;
    /**
     * External/affiliate products have product url parameter
     *
     * @var string
     */
    protected $product_url;
    /**
     * External/affiliate product button text
     *
     * @var string
     */
    protected $product_button_text;
    /**
     * @var string
     */
    protected $meta_title;
    /**
     * @var string
     */
    protected $meta_description;
    /**
     * @var int
     * @values [0,1]
     */
    protected $meta_noindex;
    /**
     * product note
     *
     * @var string
     */
    protected $note;

    /**
     * @var array()
     */
    protected $attributes;

    /**
     * @var Shop_CT_Product_Attribute_Term[]
     */
    protected $attribute_terms;

    protected $rating;

    /**
     * @var Shop_CT_Product_Attribute_Term[]
     */
    protected $added_attribute_terms;

    protected $orders_count;

    protected $meta_keys;

    protected $download_permissions;

    protected $added_download_permissions;

    /**
     * Constructor gets the post object and sets the ID for the loaded product.
     *
     * @param int|Shop_CT_Product|object $product Product ID, post object, or product object
     */
    public function __construct($product)
    {
        $this->ID = &$this->id;
        if (is_numeric($product)) {
            $this->id = absint($product);
            $this->post = get_post($this->id);
        } elseif ($product instanceof Shop_CT_Product) {
            $this->id = absint($product->id);
            $this->post = $product->post;
        } elseif (isset($product->ID)) {
            $this->id = absint($product->ID);
            $this->post = $product;
        }
        if (null !== $this->id):
            $this->meta_keys = apply_filters('shop_ct_product_meta', array(
                'product_type',
                'visibility',
                'sku',
                'manage_stock',
                'stock',
                'stock_status',
                'backorders',
                'width',
                'length',
                'height',
                'weight',
                'regular_price',
                'sale_price',
                'sale_price_dates_from',
                'sale_price_dates_to',
                'sold_individually',
                'virtual',
                'downloadable',
                'downloadable_files',
                'product_image_gallery',
                'download_limit',
                'download_expiry',
                'download_type',
                'product_url',
                'product_button_text',
                'meta_title',
                'meta_description',
                'meta_noindex',
                'note',
                'rating'
            ));
            foreach ($this->meta_keys as $key) {
                if(!SHOP_CT()->product_meta){
                    var_dump(SHOP_CT()->product_meta);die;
                }
                $this->$key = SHOP_CT()->product_meta->get($this->id, $key);
            }

            if($this->id) {
                //calculate final price for product
                $this->get_price();
            }

            if (!empty($this->product_image_gallery)) {
                $this->image_id = $this->product_image_gallery[0];
            }

            $attrs = Shop_CT_Product_Attribute::get_all();
            foreach ($attrs as $attr) {
                $terms = wp_get_post_terms($this->id, $attr->get_slug());
                if (!is_wp_error($terms) && !empty($terms)) {
                    $this->attributes[$attr->get_slug()] = array();
                    foreach ($terms as $term) {
                        $this->attributes[$attr->get_slug()][] = new Shop_CT_Product_Attribute_Term($term->term_id);
                        $this->attribute_terms[] = new Shop_CT_Product_Attribute_Term($term->term_id);
                    }
                }

            }

        endif;
    }

    public static function get_download_permissions_table_name()
    {
        return $GLOBALS['wpdb']->prefix.'shop_ct_download_permissions';
    }

    /**
     * @return int
     */
    public function get_id()
    {
        return $this->id;
    }

    /**
     * Return the product type.
     *
     * @return string
     */
    public function get_product_type()
    {
        return is_null($this->product_type) ? 'simple' : $this->product_type;
    }

    /**
     * @param $product_type
     * @return $this
     */
    public function set_product_type($product_type)
    {
        $this->product_type = sanitize_text_field($product_type);
        return $this;
    }

    /**
     * @return mixed
     */
    public function get_visibility()
    {
        return apply_filters('shop_ct_product_visibility', $this->visibility);
    }

    /**
     * @param $visibility
     * @return $this
     */
    public function set_visibility($visibility)
    {
        $this->visibility = sanitize_text_field($visibility);
        return $this;
    }

    /**
     * @return mixed
     */
    public function get_sku()
    {
        return apply_filters('shop_ct_product_sku', $this->sku);
    }

    /**
     * @param $sku
     * @return $this
     */
    public function set_sku($sku)
    {
        $this->sku = sanitize_text_field($sku);
        return $this;
    }

    /**
     * @return int
     */
    public function get_manage_stock()
    {
        return apply_filters('shop_ct_product_manage_stock', (bool)$this->manage_stock, $this);
    }

    /**
     * @param $manage_stock
     * @return $this
     * @throws Exception
     */
    public function set_manage_stock($manage_stock)
    {
        if (!in_array($manage_stock, array(0, 1))) {
            throw new Exception('Invalid value for "manage_stock" field, expected 0|1, got "' . $manage_stock . '"');
        }

        $this->manage_stock = (int)$manage_stock;

        return $this;
    }

    /**
     * @return int
     */
    public function get_stock()
    {
        return $this->stock;
    }

    /**
     * Set stock level of the product.
     *
     * @param int $amount (default: null) Amount by which the stock will be changed
     * @param string $mode can be set, add, or subtract
     * @return Shop_CT_Product
     * @throws Exception
     */
    public function set_stock($amount = null, $mode = 'set')
    {
        if (!is_null($amount) && $this->managing_stock()) {
            $prev_value = (int)SHOP_CT()->product_meta->get($this->id, 'stock');
            switch ($mode) {
                case 'add' :
                    $new_value = $prev_value + $amount;
                    break;
                case 'subtract' :
                    $new_value = $prev_value - $amount;
                    break;
                default :
                    $new_value = $amount;
                    break;
            }
            if (!SHOP_CT()->product_meta->set($this->id, 'stock', $new_value)) {
                throw new Exception('Could not update stock for ' . $this->get_post_data()->post_title . ', product id:' . $this->id . ', previous stock value:' . $prev_value . ', new value:' . $new_value . '');
            }
            $this->stock = $new_value;
            $this->check_stock_status();
            do_action('shop_ct_product_set_stock', $this);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function get_stock_status()
    {
        return $this->stock_status;
    }

    /**
     * Set stock status of the product.
     *
     * @param string $status
     * @throws Exception
     */
    public function set_stock_status($status)
    {
        if (!in_array($status, array('outofstock', 'instock'))) {
            throw new Exception('Invalid value for field "stock_status", accepts only "outofstock","instock". "' . $status . '" is used instead');
        }
        if ($this->managing_stock()) {
            if (!$this->backorders_allowed() && $this->get_stock_quantity() <= SHOP_CT()->product_settings->notify_no_stock_amount) {
                $status = 'outofstock';
            }
        }
        if (SHOP_CT()->product_meta->set($this->id, 'stock_status', $status)) {
            $this->stock_status = $status;
            do_action('shop_ct_product_set_stock_status', $this->id, $status);
        } else {
            throw new Exception('Failed to update stock status for "' . $this->get_post_data()->post_title . '", product id:' . $this->id . ', new value:' . $status);
        }
    }

    /**
     * @return string
     */
    public function get_backorders()
    {
        return $this->backorders;
    }

    /**
     * @param string $backorders
     * @return Shop_CT_Product
     */
    public function set_backorders($backorders)
    {
        $this->backorders = $backorders;
        return $this;
    }

    public function backorders_require_notification()
    {
        return $this->backorders === 'notify';
    }

    /**
     * Returns the product width.
     * @return string
     */
    public function get_width()
    {
        return apply_filters('shop_ct_product_width', $this->width ? $this->width : '', $this);
    }

    public function set_width($width)
    {
        $this->width = absint($width);
        return $this;
    }

    /**
     * Returns the product height.
     * @return string
     */
    public function get_height()
    {
        return apply_filters('shop_ct_product_height', $this->height ? $this->height : '', $this);
    }

    /**
     * @param $height
     * @return Shop_CT_Product
     */
    public function set_height($height)
    {
        $this->height = absint($height);

        return $this;
    }

    /**
     * Returns the product's weight.
     * @return string
     */
    public function get_weight()
    {
        return apply_filters('shop_ct_product_weight', $this->weight, $this);
    }

    /**
     * @param $weight
     * @return Shop_CT_Product
     */
    public function set_weight($weight)
    {
        $this->weight = absint($weight);
        return $this;
    }

    /**
     * Returns the product length.
     * @return string
     */
    public function get_length()
    {
        return apply_filters('shop_ct_product_length', $this->length ? $this->length : '', $this);
    }

    public function set_length($length)
    {
        $this->length = absint($length);
        return $this;
    }

    /**
     * Returns the product's regular price.
     *
     * @return string price
     */
    public function get_regular_price()
    {
        return apply_filters('shop_ct_get_regular_price', $this->regular_price, $this);
    }

    public function set_regular_price($regular_price)
    {
        $this->regular_price = $regular_price;
        return $this;
    }

    /**
     * Returns the product's sale price.
     *
     * @return string price
     */
    public function get_sale_price()
    {
        return apply_filters('shop_ct_get_sale_price', $this->sale_price, $this);
    }

    /**
     * @param $sale_price
     * @return Shop_CT_Product
     */
    public function set_sale_price($sale_price)
    {
        $this->sale_price = $sale_price;
        return $this;
    }

    /**
     * @return string
     */
    public function get_sale_price_dates_from()
    {
        return $this->sale_price_dates_from;
    }

    /**
     * @param string $sale_price_dates_from
     * @return Shop_CT_Product
     */
    public function set_sale_price_dates_from($sale_price_dates_from)
    {
        $this->sale_price_dates_from = $sale_price_dates_from;
        return $this;
    }

    /**
     * @return string
     */
    public function get_sale_price_dates_to()
    {
        return $this->sale_price_dates_to;
    }

    /**
     * @param string $sale_price_dates_to
     * @return Shop_CT_Product
     */
    public function set_sale_price_dates_to($sale_price_dates_to)
    {
        $this->sale_price_dates_to = $sale_price_dates_to;
        return $this;
    }

    /**
     * Check if a product is sold individually (no quantities).
     *
     * @return bool
     */
    public function is_sold_individually()
    {
        return apply_filters('shop_ct_is_sold_individually', (bool)$this->sold_individually, $this);
    }

    /**
     * @return bool
     */
    public function get_sold_individually()
    {
        return apply_filters('shop_ct_get_sold_individually', (bool)$this->sold_individually, $this);
    }

    /**
     * @param $sold_individually int
     *
     * @return Shop_CT_Product
     */
    public function set_sold_individually($sold_individually)
    {
        $this->sold_individually = (int)$sold_individually;
        return $this;
    }

    /**
     * Checks if a product is virtual (has no shipping).
     *
     * @return bool
     */
    public function is_virtual()
    {
        return apply_filters('shop_ct_is_virtual', (bool)$this->virtual, $this);
    }

    /**
     * @return bool
     */
    public function get_virtual()
    {
        return apply_filters('shop_ct_get_virtual', (bool)$this->virtual, $this);
    }

    /**
     * @param $virtual int
     * @return $this
     * @throws Exception
     */
    public function set_virtual($virtual)
    {
        if (!in_array($virtual, array(0, 1))) {
            throw new Exception('Invalid value for "virtual" field, expected 0|1, got "' . $virtual . '"');
        }
        $this->virtual = $virtual;
        return $this;
    }

    /**
     * Checks if a product is downloadable.
     *
     * @return bool
     */
    public function is_downloadable()
    {
        return apply_filters('shop_ct_product_is_downloadable', (bool)$this->downloadable);
    }

    /**
     * @return bool
     */
    public function get_downloadable()
    {
        return apply_filters('shop_ct_product_get_downloadable', (bool)$this->downloadable);
    }

    /**
     * @param $downloadable
     * @return Shop_CT_Product
     */
    public function set_downloadable($downloadable)
    {
        $this->downloadable = (int)$downloadable;
        return $this;
    }

    /**
     * @return array[]
     */
    public function get_downloadable_files()
    {
        $downloadable_files = array_filter(isset($this->downloadable_files) ? (array)maybe_unserialize($this->downloadable_files) : array());
        if (!empty($downloadable_files)) {
            foreach ($downloadable_files as $key => $file) {
                if (!is_array($file)) {
                    $downloadable_files[$key] = array(
                        'url' => $file,
                        'name' => ''
                    );
                }
                if (empty($file['name'])) {
                    $downloadable_files[$key]['name'] = Shop_CT_Formatting::get_filename_from_url($downloadable_files[$key]['url']);
                }
                $downloadable_files[$key]['url'] = apply_filters('shop_ct_file_download_path', $downloadable_files[$key]['url'], $this, $key);
            }
        }
        return apply_filters('shop_ct_product_files', $downloadable_files, $this);
    }

    /**
     * @param array[] $downloadable_files
     * @return Shop_CT_Product
     */
    public function set_downloadable_files($downloadable_files)
    {
        $this->downloadable_files = $downloadable_files;
        return $this;
    }

    /**
     * @return int[]
     */
    public function get_product_image_gallery()
    {
        return apply_filters('shop_ct_product_image_gallery', array_filter(array_filter((array)$this->product_image_gallery), 'wp_attachment_is_image'), $this);
    }

    /**
     * @param int[] $product_image_gallery
     * @return Shop_CT_Product
     */
    public function set_product_image_gallery($product_image_gallery)
    {
        $this->product_image_gallery = array_filter(array_filter((array)$product_image_gallery), 'wp_attachment_is_image');
        return $this;
    }

    /**
     * Gets the main product image ID.
     *
     * @return int
     */
    public function get_image_id()
    {
        if(is_null($this->image_id) && !empty($this->product_image_gallery)) {
            $this->image_id = $this->product_image_gallery[0];
        }

        return $this->image_id;
    }

    /**
     * @param $image_id
     * @return $this
     */
    public function set_image_id($image_id)
    {
        $this->image_id = (int)$image_id;
        return $this;
    }

    /**
     * @return int
     */
    public function get_download_limit()
    {
        return (!empty($this->download_limit) ? $this->download_limit : null);
    }

    /**
     * @param int $download_limit
     * @return Shop_CT_Product
     */
    public function set_download_limit($download_limit)
    {
        $this->download_limit = (int)$download_limit;
        return $this;
    }

    /**
     * the number of days before a download link expires, or can be left blank
     *
     * @return int
     */
    public function get_download_expiry()
    {
        return (!empty($this->download_expiry) ? $this->download_expiry : null);
    }

    /**
     * @param int $download_expiry
     * @return Shop_CT_Product
     */
    public function set_download_expiry($download_expiry)
    {
        $this->download_expiry = (int)$download_expiry;
        return $this;
    }

    /**
     * @return string
     */
    public function get_download_type()
    {
        return $this->download_type;
    }

    /**
     * @param string $download_type
     * @return Shop_CT_Product
     */
    public function set_download_type($download_type)
    {
        $this->download_type = sanitize_text_field($download_type);
        return $this;
    }

    /**
     * Get external url for the product
     *
     * @return mixed
     */
    public function get_product_url()
    {
        return apply_filters('shop_ct_product_url', $this->product_url);
    }

    /**
     * @param $product_url
     *
     * @return Shop_CT_Product
     */
    public function set_product_url($product_url)
    {
        $this->product_url = esc_url($product_url);
        return $this;
    }

    /**
     * Get external product's button text
     *
     * @return mixed
     */
    public function get_product_button_text()
    {
        return apply_filters('shop_ct_product_button_text', $this->product_button_text);
    }

    /**
     * @param $product_button_text string
     *
     * @return Shop_CT_Product
     */
    public function set_product_button_text($product_button_text)
    {
        $this->product_button_text = sanitize_text_field($product_button_text);
        return $this;
    }

    /**
     * @return string
     */
    public function get_meta_title()
    {
        return (string)$this->meta_title;
    }

    /**
     * @param string $meta_title
     *
     * @return Shop_CT_Product
     */
    public function set_meta_title($meta_title)
    {
        $this->meta_title = sanitize_text_field($meta_title);
        return $this;
    }

    /**
     * @return string
     */
    public function get_meta_description()
    {
        return (string)$this->meta_description;
    }

    /**
     * @param string $meta_description
     *
     * @return Shop_CT_Product
     */
    public function set_meta_description($meta_description)
    {
        $this->meta_description = sanitize_text_field($meta_description);
        return $this;
    }

    /**
     * @return bool
     */
    public function get_meta_noindex()
    {
        return (bool)$this->meta_noindex;
    }

    /**
     * @param int $meta_noindex
     *
     * @return Shop_CT_Product
     * @throws Exception
     */
    public function set_meta_noindex($meta_noindex)
    {
        if (!in_array((int)$meta_noindex, array(0, 1))) {
            throw new Exception('invalid value of "meta_noindex" field!');
        }
        $this->meta_noindex = (int)$meta_noindex;
        return $this;
    }

    public function get_note()
    {
        return apply_filters('shop_ct_product_note', $this->note);
    }

    /**
     * @param $note
     *
     * @return $this
     */
    public function set_note($note)
    {
        $this->note = $note;
        return $this;
    }

    /**
     * Check if downloadable product has a file attached.
     *
     * @param string $download_id file identifier
     * @return bool Whether downloadable product has a file attached.
     */
    public function has_file($download_id = '')
    {
        return ($this->is_downloadable() && $this->get_file($download_id)) ? true : false;
    }

    /**
     * Returns the product's active price.
     *
     * @return string price
     */
    public function get_price()
    {
        if(is_null($this->regular_price)){
            $price = 0;
        } elseif (!$this->is_on_sale()) {
            $price = $this->regular_price;
        } elseif (is_numeric($this->sale_price) && $this->sale_price < $this->regular_price) {
            $price = $this->sale_price;
        } else {
            $price = $this->regular_price;
        }

        if($price !== $this->final_price) {
            $this->final_price = $price;

        }

        $db_price = get_post_meta($this->id, 'shop_ct_final_price', true);

        if($db_price != $price) {
            update_post_meta($this->id, 'shop_ct_final_price', $price);
        }

        return apply_filters('shop_ct_get_price', $this->final_price, $this);
    }

    /**
     * Get total stock.
     *
     * @return int
     */
    public function get_total_stock()
    {
        if (empty($this->total_stock)) {
            $this->total_stock = max(0, $this->get_stock_quantity());
        }
        return apply_filters('shop_ct_stock_amount', $this->total_stock);
    }

    /**
     * Return the shortcode for product
     *
     * @return string product shortcode
     */
    public function get_shortcode()
    {
        return '[ShopConstruct_product id="' . $this->id . '"]';
    }

    /**
     * Wrapper for get_permalink.
     *
     * @return string
     */
    public function get_permalink()
    {
        return get_permalink($this->id);
    }

    /**
     * @return WP_Post
     */
    public function get_post_data()
    {
        return $this->post;
    }

    /**
     * @param $key
     * @param $value
     * @return Shop_CT_Product
     */
    public function set_post_data($key, $value)
    {
        $this->post->$key = $value;
        return $this;
    }

    /**
     * Returns number of items available for sale.
     *
     * @return int
     */
    public function get_stock_quantity()
    {
        return apply_filters('shop_ct_get_stock_quantity', $this->managing_stock() ? apply_filters('shop_ct_stock_amount', $this->stock) : null, $this);
    }

    /**
     * Check if the stock status needs changing.
     */
    public function check_stock_status()
    {
        if (!$this->backorders_allowed() && $this->get_total_stock() <= get_option('shop_ct_product_settings_notify_low_stock_amount')) {
            if ($this->stock_status !== 'outofstock') {
                $this->set_stock_status('outofstock');
            }
        } elseif ($this->backorders_allowed() || $this->get_total_stock() > get_option('shop_ct_product_settings_notify_low_stock_amount')) {
            if ($this->stock_status !== 'instock') {
                $this->set_stock_status('instock');
            }
        }
    }

    /**
     * Reduce stock level of the product.
     *
     * @param int $amount Amount to reduce by. Default: 1
     * @return int new stock level
     */
    public function reduce_stock($amount = 1)
    {
        $this->set_stock($amount, 'subtract');
        return $this->get_stock();
    }

    /**
     * Increase stock level of the product.
     *
     * @param int $amount Amount to increase by. Default 1.
     * @return int new stock level
     */
    public function increase_stock($amount = 1)
    {
        $this->set_stock($amount, 'add');
        return $this->get_stock();
    }

    /**
     * Returns whether or not the product is stock managed.
     *
     * @return bool
     */
    public function managing_stock()
    {
        return (!isset($this->manage_stock) || !$this->manage_stock || SHOP_CT()->product_settings->manage_stock !== 'yes') ? false : true;
    }

    /**
     * Returns whether or not the product is in stock.
     *
     * @return bool
     */
    public function is_in_stock()
    {
        return (($this->managing_stock() && $this->get_total_stock() > SHOP_CT()->product_settings->notify_no_stock_amount) || $this->stock_status === 'instock' || $this->backorders_allowed());
    }

    /**
     * Returns whether or not the product can be backordered.
     *
     * @return bool
     */
    public function backorders_allowed()
    {
        return apply_filters('shop_ct_product_backorders_allowed', $this->backorders === 'yes' || $this->backorders === 'notify' ? true : false, $this->ID);
    }

    /**
     * Check if a product is on backorder.
     *
     * @param int $qty_in_cart (default: 0)
     * @return bool
     */
    public function is_on_backorder($qty_in_cart = 0)
    {
        return $this->managing_stock() && $this->backorders_allowed() && ($this->get_total_stock() - $qty_in_cart) < 0 ? true : false;
    }

    /**
     * Returns whether or not the product has enough stock for the order.
     *
     * @param mixed $quantity
     * @return bool
     */
    public function has_enough_stock($quantity)
    {
        return !$this->managing_stock() || $this->backorders_allowed() || $this->get_stock_quantity() >= $quantity ? true : false;
    }

    /**
     * Returns the availability of the product.
     *
     * @return array()]
     */
    public function get_availability()
    {
        $availability = $class = '';
        if ($this->managing_stock()) {
            if ($this->is_in_stock() && $this->get_total_stock() > SHOP_CT()->product_settings->notify_no_stock_amount) {
                switch (Shop_CT()->product_settings->stock_format) {
                    case 'no_amount' :
                        $availability = __('In stock', 'shop_ct');
                        break;
                    case 'low_amount' :
                        if ($this->get_total_stock() <= Shop_CT()->product_settings->notify_low_stock_amount) {
                            $availability = sprintf(__('Only %s left in stock', 'shop_ct'), $this->get_total_stock());
                            if ($this->backorders_allowed() && $this->backorders_require_notification()) {
                                $availability .= ' ' . __('(can be backordered)', 'shop_ct');
                            }
                        } else {
                            $availability = __('In stock', 'shop_ct');
                        }
                        break;
                    default :
                        $availability = sprintf(__('%s in stock', 'shop_ct'), $this->get_total_stock());
                        if ($this->backorders_allowed() && $this->backorders_require_notification()) {
                            $availability .= ' ' . __('(can be backordered)', 'shop_ct');
                        }
                        break;
                }
                $class = 'in-stock';
            } elseif ($this->backorders_allowed() && $this->backorders_require_notification()) {
                $availability = __('Available on backorder', 'shop_ct');
                $class = 'available-on-backorder';
            } elseif ($this->backorders_allowed()) {
                $availability = __('In stock', 'shop_ct');
                $class = 'in-stock';
            } else {
                $availability = __('Out of stock', 'shop_ct');
                $class = 'out-of-stock';
            }
        } elseif (!$this->is_in_stock()) {
            $availability = __('Out of stock', 'shop_ct');
            $class = 'out-of-stock';
        }else{
            $availability = __('In stock', 'shop_ct');
            $class = 'in-stock';
        }
        return apply_filters('shop_ct_get_availability', array('availability' => $availability, 'class' => $class), $this);
    }

    /**
     * Check if the product can be purchased,
     * also checks for shipping zone
     *
     * @param bool $shipping_zone
     * @return bool
     */
    public function can_be_purchased($shipping_zone = false)
    {
        $stock_not_low = ($this->is_in_stock() && (!$this->managing_stock() || $this->get_total_stock() > SHOP_CT()->product_settings->notify_no_stock_amount));
        return ( ($stock_not_low || $this->backorders_allowed()) && ($this->is_virtual() || $shipping_zone instanceof Shop_CT_Shipping_Zone) );
    }

    /**
     * Checks the product type.
     *
     * @param string $type Array or string of types
     * @return bool
     */
    public function is_product_type($type)
    {
        return ($this->product_type == $type || (is_array($type) && in_array($this->product_type, $type))) ? true : false;
    }

    /**
     * Get a file by $download_id.
     *
     * @param string $download_id file identifier
     * @return array|false if not found
     */
    public function get_file($download_id = '')
    {

        $files = $this->get_downloadable_files();

        if ('' === $download_id) {
            $file = sizeof($files) ? current($files) : false;
        } elseif (isset($files[$download_id])) {
            $file = $files[$download_id];
        } else {
            $file = false;
        }

        // allow overriding based on the particular file being requested
        return apply_filters('shop_ct_product_file', $file, $this, $download_id);
    }

    /**
     * Get file download path identified by $download_id.
     *
     * @param string $download_id file identifier
     * @return string
     */
    public function get_file_download_path($download_id)
    {
        $files = $this->get_downloadable_files();

        if (isset($files[$download_id])) {
            $file_path = $files[$download_id]['url'];
        } else {
            $file_path = '';
        }

        return apply_filters('shop_ct_product_file_download_path', $file_path, $this, $download_id);
    }

    /**
     * Checks if a product needs shipping.
     *
     * @return bool
     */
    public function needs_shipping()
    {
        return apply_filters('shop_ct_product_needs_shipping', !$this->is_virtual(), $this);
    }

    /**
     * Returns whether or not the product post exists.
     *
     * @return bool
     */
    public function exists()
    {
        return empty($this->post) || null === $this->id ? false : true;
    }

    /**
     * Get the title of the post.
     *
     * @return string
     */
    public function get_title()
    {
        return apply_filters('shop_ct_product_title', $this->post ? $this->post->post_title : null, $this);
    }

    /**
     * Get the add to url used mainly in loops.
     *
     * @return string
     */
    public function add_to_cart_url()
    {
        return apply_filters('shop_ct_product_add_to_cart_url', $this->get_permalink(), $this);
    }

    /**
     * Get the add to cart button text for the single page.
     *
     * @return string
     */
    public function single_add_to_cart_text()
    {
        return apply_filters('shop_ct_product_single_add_to_cart_text', __('Add to cart', 'shop_ct'), $this);
    }

    /**
     * Get the add to cart button text.
     *
     * @return string
     */
    public function add_to_cart_text()
    {
        return apply_filters('shop_ct_product_add_to_cart_text', __('Read more', 'shop_ct'), $this);
    }

    /**
     * Returns whether or not the product is visible in the catalog.
     *
     * @return bool
     */
    public function is_visible()
    {
        if (!$this->post) {
            $visible = false;

            // Published/private
        } elseif ($this->post->post_status !== 'publish' && !current_user_can('edit_posts')) {
            $visible = false;

            // Out of stock visibility
        } elseif ('yes' === Shop_CT()->product_settings->hide_out_of_stock_items && !$this->is_in_stock()) {
            $visible = false;

            // visibility setting
        } elseif ('hidden' === $this->visibility) {
            $visible = false;
        } elseif ('visible' === $this->visibility) {
            $visible = true;

            // Visibility in loop
        } elseif (is_search()) {
            $visible = 'search' === $this->visibility;
        } else {
            $visible = 'catalog' === $this->visibility;
        }
        return apply_filters('shop_ct_product_is_visible', $visible, $this->id);
    }

    /**
     * Returns whether or not the product is on sale.
     *
     * @return bool
     */
    public function is_on_sale()
    {
        return ($this->has_sale_price() && $this->is_in_sale_schedule());
    }

    public function is_in_sale_schedule(){
        if(empty($this->sale_price_dates_from) && empty($this->sale_price_dates_to)){
            return true;
        }

        $current_date = Shop_CT_Dates::get_wp_datetime();

        $sale_started = (!empty($this->sale_price_dates_from)
            ? ($current_date > new DateTime($this->sale_price_dates_from,Shop_CT_Dates::get_timezone()))
            : true);

        $sale_not_ended = (!empty($this->sale_price_dates_to)
            ? ($current_date < new DateTime($this->sale_price_dates_to,Shop_CT_Dates::get_timezone()) && $sale_started)
            : $sale_started);

        return $sale_not_ended;
    }

    public function has_sale_countdown(){

        if(!empty($this->sale_price_dates_from) || !empty($this->sale_price_dates_to)){
	        return $this->is_in_sale_schedule();

        }

	    return false;
    }

    public function has_sale_price()
    {
        return (bool)(!empty($this->sale_price) && $this->sale_price < $this->regular_price);
    }

    /**
     * Returns false if the product cannot be bought.
     *
     * @return bool
     */
    public function is_purchasable()
    {
        $purchasable = true;
        // Products must exist of course
        if (!$this->exists()) {
            $purchasable = false;
            // Other products types need a price to be set
        } elseif ($this->get_price() === '') {
            $purchasable = false;
            // Check the product is published
        } elseif ($this->post->post_status !== 'publish' && !current_user_can('edit_posts')) {
            $purchasable = false;
        }
        return apply_filters('shop_ct_is_purchasable', $purchasable, $this);
    }

    /**
     * Returns the price in html format.
     *
     * @param string $price (default: '')
     * @return string
     */
    public function get_price_html($price = '')
    {
        $display_price = Shop_CT_Formatting::format_price($this->get_price());
        $display_regular_price = Shop_CT_Formatting::format_price($this->get_regular_price());
        if ($this->get_price() > 0) {
            if ($this->is_on_sale() && $this->get_regular_price()) {
                $price .= $this->get_price_html_from_to($display_regular_price, $display_price);
                $price = apply_filters('shop_ct_sale_price_html', $price, $this);
            } else {
                $price .= Shop_CT_Formatting::format_price($display_price);
                $price = apply_filters('shop_ct_price_html', $price, $this);
            }
        } elseif ($this->get_price() === '') {
            $price = apply_filters('shop_ct_empty_price_html', '', $this);
        } elseif ($this->get_price() == 0) {
            if ($this->is_on_sale() && $this->get_regular_price()) {
                $price .= $this->get_price_html_from_to($display_regular_price, __('Free!', 'shop_ct'));
                $price = apply_filters('shop_ct_free_sale_price_html', $price, $this);
            } else {
                $price = '<span class="amount">' . __('Free!', 'shop_ct') . '</span>';
                $price = apply_filters('shop_ct_free_price_html', $price, $this);
            }
        }
        return apply_filters('shop_ct_get_price_html', $price, $this);
    }

    /**
     * Functions for getting parts of a price, in html, used by get_price_html.
     *
     * @return string
     */
    public function get_price_html_from_text()
    {
        $from = '<span class="from">' . _x('From:', 'min_price', 'shop_ct') . ' </span>';
        return apply_filters('shop_ct_get_price_html_from_text', $from, $this);
    }

    /**
     * Functions for getting parts of a price, in html, used by get_price_html.
     *
     * @param  string $from String or float to wrap with 'from' text
     * @param  mixed $to String or float to wrap with 'to' text
     * @return string
     */
    public function get_price_html_from_to($from, $to)
    {
        $price = '<del>' . $from . '</del> <ins>' . $to . '</ins>';
        return apply_filters('shop_ct_get_price_html_from_to', $price, $from, $to, $this);
    }

    /**
     * Get the average rating of product. This is calculated once and stored in postmeta.
     * @return string
     */
    public function get_rating()
    {
        if($this->rating !== null) {
            return $this->rating;
        } else {
            $this->calculate_rating();
        }


        return $this->rating;
    }

    /**
     * @return float|int
     */
    public function calculate_rating()
    {
        $reviews = get_comments(array('post_id' => $this->id, 'status' => 'approve', 'type' => Shop_CT_Product_Review::COMMENT_TYPE));
        if (!empty($reviews)) {
            $ratings = array();

            foreach ($reviews as $review) {
                $reviewObj = new Shop_CT_Product_Review($review->comment_ID);


                if ($reviewObj->get_rating() > 0) {
                    array_push($ratings, $reviewObj->get_rating());
                }
            }

            $length = count($ratings);

            $rating = $length != 0 ? array_sum($ratings) / $length : 0;
        } else {
            $rating = 0;
        }

        SHOP_CT()->product_meta->set($this->id, 'rating',$rating);
        update_post_meta($this->id, 'shop_ct_rating', $rating);

        $this->rating = $rating;

        return $rating;
    }

    /**
     * Get the total amount (COUNT) of ratings.
     * @param  int $value Optional. Rating value to get the count for. By default returns the count of all rating values.
     * @return int
     */
    public function get_rating_count($value = null)
    {
        $reviews = get_comments(array('post_id' => $this->id, 'status' => 'approve', 'type' => Shop_CT_Product_Review::COMMENT_TYPE));
        $votes = 0;
        if (!empty($reviews)) {
            foreach ($reviews as $review) {
                $rating = (int) get_comment_meta($review->comment_ID, 'rating', true);
                if ($rating) {
                    $votes++;
                }
            }
        }
        return (int)$votes;
    }

    /**
     * Returns the product rating in html format.
     *
     * @param string $rating (default: '')
     *
     * @return string
     */
    public function get_rating_html($rating = null)
    {
        $rating_html = '';
        if (!is_numeric($rating)) {
            $rating = $this->get_average_rating();
        }
        if ($rating > 0) {
            $rating_html = '<div class="star-rating" title="' . sprintf(__('Rated %s out of 5', 'shop_ct'), $rating) . '">';
            $rating_html .= '<span style="width:' . (($rating / 5) * 100) . '%"><strong class="rating">' . $rating . '</strong> ' . __('out of 5', 'shop_ct') . '</span>';
            $rating_html .= '</div>';
        }
        return apply_filters('shop_ct_product_get_rating_html', $rating_html, $rating);
    }

    /**
     * Get the total amount (COUNT) of reviews.
     *
     * @return int The total number of product reviews
     */
    public function get_review_count()
    {
        $reviews = get_comments(array('post_id' => $this->ID, 'status' => 'approve'));
        if (empty($reviews)) $count = 0;
        else $count = count($reviews);
        return $count;
    }

    /**
     * Returns the product categories.
     *
     * @param string $sep (default: ', ')
     * @param string $before (default: '')
     * @param string $after (default: '')
     * @return WP_Term[]|WP_Error
     */
    public function get_categories($sep = ', ', $before = '', $after = '')
    {
        return wp_get_post_terms($this->id, Shop_CT_Product_Category::get_taxonomy());
    }

    /**
     * Returns the product tags.
     *
     * @param string $sep (default: ', ')
     * @param string $before (default: '')
     * @param string $after (default: '')
     * @return WP_Term[]|WP_Error
     */
    public function get_tags($sep = ', ', $before = '', $after = '')
    {
        return wp_get_post_terms($this->id, Shop_CT_Product_Tag::get_taxonomy());
    }

    /**
     * Returns product attributes.
     * todo: check
     * @param null|Shop_CT_Product_Attribute $attribute
     * @return array
     */
    public function get_attribute_terms($attribute = null)
    {
        if(null === $attribute){
            return $this->attribute_terms;
        }else{
            return (isset($this->attributes[$attribute->get_slug()]) && !empty($this->attributes[$attribute->get_slug()]) ?  $this->attributes[$attribute->get_slug()] : null);
        }

    }

    /**
     * @param $attribute_terms
     * @return Shop_CT_Product
     */
    public function set_attribute_terms($attribute_terms)
    {
        $this->attribute_terms = $attribute_terms;
        return $this;
    }

    /**
     * @param Shop_CT_Product_Attribute_Term $term
     * @return Shop_CT_Product
     */
    public function add_attribute_term(Shop_CT_Product_Attribute_Term $term)
    {
        $exists = false;

        foreach ($this->attribute_terms as $existing_term) {
            if ($existing_term->get_id() === $term->get_id()) {
                $exists = true;
            }
        }
        if (false === $exists)  {
            $this->attribute_terms[] = $term;
            $this->attributes[$term->get_attribute()->get_slug()][] = $term;
            $this->added_attribute_terms[] = $term;
        }



        return $this;
    }

    /**
     * @return Shop_CT_Product_Attribute_Term[][]
     */
    public function get_attributes()
    {
        return $this->attributes;
    }

    /**
     * Returns whether or not the product has dimensions set.
     *
     * @return bool
     */
    public function has_dimensions()
    {
        return $this->get_dimensions() ? true : false;
    }

    /**
     * Returns whether or not the product has weight set.
     *
     * @return bool
     */
    public function has_weight()
    {
        return $this->get_weight() ? true : false;
    }

    /**
     * Returns formatted dimensions.
     * @return string
     */
    public function get_dimensions()
    {
        $dimensions = implode(' x ', array_filter(array(
            $this->get_length(),
            $this->get_width(),
            $this->get_height(),
        )));
        if (!empty($dimensions)) {
            $dimensions .= ' ' . Shop_CT()->product_settings->dimension_unit;
        }
        return apply_filters('shop_ct_product_dimensions', $dimensions, $this);
    }

    /**
     * Returns the main product image URL.
     * @param string $size
     * @return string
     */
    public function get_image_url($size = 'thumbnail')
    {
        $imageId = $this->get_image_id();
        if (is_null($imageId)) {
            return trailingslashit( SHOP_CT()->plugin_url() ) . "assets/images/placeholder.png";
        }

        return wp_get_attachment_image_src($imageId, $size)[0];
    }

    /**
     * Get product name with SKU or ID. Used within admin.
     *
     * @return string Formatted product name
     */
    public function get_formatted_name()
    {
        if ($this->get_sku()) {
            $identifier = $this->get_sku();
        } else {
            $identifier = '#' . $this->id;
        }
        return sprintf(__('%s &ndash; %s', 'shop_ct'), $identifier, $this->get_title());
    }

    /**
     * @todo Rename the function.
     *
     * @return int
     */
    public function get_orders_count()
    {
        global $wpdb;
        if(null !== $this->orders_count){
            return $this->orders_count;
        }
        $count = 0;
        $product_id = (int)$this->ID;
        $orders = $wpdb->get_results("SELECT ID FROM " . $wpdb->posts . " WHERE post_type = 'shop_ct_order' AND post_status IN ('shop_ct-processing', 'shop-ct-completed')");
        foreach ($orders as $order) {
            $products = get_post_meta($order->ID, 'products', true);
            if (is_array($products) && key_exists($product_id, $products)) {
                $count += 1;
            }
        }
        return $this->orders_count = $count;
    }

    /**
     * @param $order_id
     * @param $email
     * @param null $user_id
     * @return bool todo: optimize the search
     * todo: optimize the search
     */
    public function has_download_permission($order_id,$email, $user_id = null)
    {
        $r = false;

        if (!empty($this->download_permissions)) {
            foreach ($this->download_permissions as $permission) {
                if ($order_id = $permission['order_id'] && !empty($user_id) && $user_id === $permission['user_id']) {
                    $r = true;
                }
                if ($order_id = $permission['order_id'] && $permission['email'] === $email) {
                    $r = true;
                }
            }
        }

        if ($r) {
            return $r;
        }

        global $wpdb;

        $query = "SELECT `email`,`user_id`,`token`,`expires_at`, `limit` FROM `" . self::get_download_permissions_table_name() . "` WHERE `product_id`='" . $this->id . "' AND `order_id`='".$order_id."'";
        if (!empty($user_id)) {
            $query .= " AND (`email`='" . $email . "' OR `user_id`='" . $user_id . "')";
        }else{
            $query .= " AND `email`='".$email."'";
        }

        $download_permission = $wpdb->get_row($query, ARRAY_A);

        if (!empty($download_permission)) {
            $r = true;
            $this->download_permissions[] = [
                'email' => $download_permission['email'],
                'user_id' => $download_permission['user_id'],
                'token' => $download_permission['token'],
                'expires_at' => $download_permission['expires_at'],
                'limit' => $download_permission['limit'],
                'order_id' => $order_id
            ];
        }


        return $r;
    }

    /**
     * @param $email
     * @param null $user_id
     * @return Shop_CT_Product
     */
    public function add_download_permission($order_id,$email, $user_id = null)
    {
        $new_token = bin2hex(random_bytes(16));

        if(!empty($this->download_expiry)){
            $expiry = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' +'.$this->download_expiry.' day'));
        }else{
            $expiry = null;
        }

        if(!empty($this->download_limit)){
            $limit = $this->download_limit;
        }else{
            $limit = null;
        }

        $d = [
            'email' => $email,
            'user_id' => $user_id,
            'token' => $new_token,
            'order_id' => $order_id,
            'expires_at' => $expiry,
            'limit' => $limit,
        ];
        $this->download_permissions[] = $d;
        $this->added_download_permissions[] = $d;

        return $this;

    }

    /**
     * @param $order_id
     * @param $email
     * @param null $user_id
     * @return null|string
     */
    public function get_download_link_for_user($order_id, $email, $user_id = null)
    {
        if (!$this->has_download_permission($order_id,$email, $user_id)) {
            return null;
        }

        $permission = $this->get_download_permission($order_id,$email, $user_id);

        return esc_url(add_query_arg(array('pid'=>$this->id,'oid'=>$order_id,'email'=>$email,'token'=>$permission['token']),site_url('shop-ct-download-file')));

    }

    /**
     * @param string $token
     * @param string $email
     * @param int|null $user_id
     * @return bool
     */
    public function is_valid_download_token($order_id,$token, $email, $user_id = null)
    {
        $permission = $this->get_download_permission($order_id,$email, $user_id);
        if (!$permission) {
            return false;
        }

        return ($permission['token'] === $token);
    }

    public function is_download_permission_expired($order_id,$email,$user_id=null)
    {
        $permission = $this->get_download_permission($order_id,$email, $user_id);

        if(false === $permission || null === $permission['expires_at']){
            return false;
        }

        return (strtotime(date('Y-m-d H:i:s')) < strtotime($permission['expires_at']));

    }

    public function is_download_limit_expired($order_id,$email,$user_id=null)
    {
        $permission = $this->get_download_permission($order_id,$email, $user_id);

        if(false === $permission || null === $permission['limit']){
            return false;
        }

        return ($permission['limit'] <= 0);
    }

    /**
     * @param $order_id
     * @param $email
     * @param null $user_id
     * @return array|bool todo: optimize the search
     * todo: optimize the search
     */
    public function get_download_permission($order_id,$email, $user_id = null)
    {
        if (!$this->has_download_permission($order_id,$email, $user_id)) {
            return false;
        }

        $r = array();
        foreach ($this->download_permissions as $permission) {
            if ($order_id = $permission['order_id'] && !empty($user_id) && $user_id === $permission['user_id']) {
                $r = $permission;
            }
            if ($order_id = $permission['order_id'] && $permission['email'] === $email) {
                $r = $permission;
            }
        }

        return $r;
    }
    

    public function can_be_saved()
    {
    	$title = $this->get_title();
    	if(empty($title)) {
		    $this->post->post_title = __('(no title)', 'shop_ct');
	    }
        return post_type_exists(self::$post_type);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function save()
    {
        if (!$this->can_be_saved()) {
            return false;
        }

        global $wpdb;

        /**
         * as WordPress updates posts in awfull way and all parameters in WP_Post are public, here we have to watch for all public parameters
         */
        $post_data = array(
            'ID' => $this->id,
            'post_author' => $this->post->post_author,
            'post_date' => $this->post->post_date,
            'post_date_gmt' => $this->post->post_date_gmt,
            'post_content' => $this->post->post_content,
            'post_title' => $this->post->post_title,
            'post_excerpt' => $this->post->post_excerpt,
            'post_status' => $this->post->post_status,
            'comment_status' => $this->post->comment_status,
            'ping_status' => $this->post->ping_status,
            'post_password' => $this->post->post_password,
            'post_name' => $this->post->post_name,
            'to_ping' => $this->post->to_ping,
            'pinged' => $this->post->pinged,
            'post_modified' => $this->post->post_modified,
            'post_modified_gmt' => $this->post->post_modified_gmt,
            'post_content_filtered' => $this->post->post_content_filtered,
            'post_parent' => $this->post->post_parent,
            'guid' => $this->post->guid,
            'menu_order' => $this->post->menu_order,
            'post_type' => $this->post->post_type,
            'post_mime_type' => $this->post->post_mime_type,
            '_thumbnail_id' => 0
        );
        $result = is_null($this->id)
            ? wp_insert_post($post_data)
            : wp_update_post($post_data);
        if (!$result) {
            throw new Exception('failed to save product');
        } elseif ($result && is_null($this->id)) {
            $this->id = $result;
        }

        if (!empty($this->added_attribute_terms)) {
            foreach ($this->added_attribute_terms as $term) {
                wp_add_object_terms($this->id, $term->get_id(), $term->get_attribute()->get_slug());
            }
        }


        foreach ($this->meta_keys as $meta_key) {
            if ($this->$meta_key !== SHOP_CT()->product_meta->get($this->id, $meta_key)) {
                SHOP_CT()->product_meta->set($this->id, $meta_key, $this->$meta_key);
            }
        }

        if (!empty($this->added_download_permissions)) {

            foreach ($this->added_download_permissions as $added_download_permission) {
                $d = [
                    'product_id' => $this->id,
                    'order_id' => $added_download_permission['order_id'],
                    'email' => $added_download_permission['email'],
                    'token' => $added_download_permission['token'],
                    'expires_at' => $added_download_permission['expires_at'],
                    'limit' => $added_download_permission['limit']
                ];

                if (!empty($added_download_permission['user_id'])) {
                    $d['user_id'] = $added_download_permission['user_id'];
                }

                $wpdb->insert(self::get_download_permissions_table_name(), $d);
            }
        }

        return true;
    }
}
