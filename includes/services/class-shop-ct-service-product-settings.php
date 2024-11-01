<?php
class Shop_CT_Product_Settings extends Shop_CT_Settings {

	/** @var string Weight Unit */
	public $weight_unit = 'kg';

	/** @var string Dimension Unit */
	public $dimension_unit = 'm';

	/** @var string Enable Ratings On Reviews */
	public $enable_review_rating = 'yes';

	/** @var string Ratings are required to leave a review */
	public $review_rating_required = 'yes';

	/** @var string Show "verified owner" label for customer reviews */
	public $review_rating_verification_label = 'yes';

	/** @var string  Show "verified owner" label for customer reviews */
	public $review_rating_verification_required = 'no';

	/** @var string Enable stock management */
	public $manage_stock = 'yes';

	/** @var string Notification Recipient(s) */
	public $stock_email_recipient = '';

	/** @var string Low Stock Threshold */
	public $notify_low_stock_amount = '2';

	/** @var string Out Of Stock Threshold */
	public $notify_no_stock_amount = '0';

	/** @var string  Hide out of stock items from the catalog */
	public $hide_out_of_stock_items = 'no';

	/** @var string Stock Display Format */
	public $stock_format = '';

	/** @var string File Download Method */
	public $file_download_method = 'force';

	/** @var string Downloads require login */
	public $downloads_require_login = 'no';

	/** @var string Enable this option to grant access to downloads when orders are "processing", rather than "completed". */
	public $downloads_grant_access_after_payment = 'yes';


	/**
	 * Shop_CT_Product_Settings constructor.
	 */
	public function __construct() {
		$this->form_id = 'shop_ct_product_settings_form';
		/* enable navigation for this page */
		$this->navigation = 'yes';
		$this->init();
		$this->init_panels();
		$this->init_sections();
		$this->init_controls();
	}

	/**
	 * Initialize user defined options
	 */
	public function init() {


		$this->weight_unit                          = $this->get_option( 'weight_unit', 'kg' );
		$this->dimension_unit                       = $this->get_option( 'dimension_unit', 'm' );
		$this->enable_review_rating                 = $this->get_option( 'enable_review_rating', 'yes' );
		$this->review_rating_required               = $this->get_option( 'review_rating_required', 'yes' );
		$this->review_rating_verification_label     = $this->get_option( 'review_rating_verification_label', 'yes' );
		$this->review_rating_verification_required  = $this->get_option( 'review_rating_verification_required', 'no' );
		$this->manage_stock                         = $this->get_option( 'manage_stock', 'yes' );
		$this->stock_email_recipient                = $this->get_option( 'stock_email_recipient', get_option( 'admin_email' ) );
		$this->notify_low_stock_amount              = $this->get_option( 'notify_low_stock_amount', '2' );
		$this->notify_no_stock_amount               = $this->get_option( 'notify_no_stock_amount', '0' );
		$this->hide_out_of_stock_items              = $this->get_option( 'hide_out_of_stock_items', 'no' );
		$this->stock_format                         = $this->get_option( 'stock_format', '' );
		$this->file_download_method                 = $this->get_option( 'file_download_method', 'force' );
		$this->downloads_require_login              = $this->get_option( 'downloads_require_login', 'no' );
		$this->downloads_grant_access_after_payment = $this->get_option( 'downloads_grant_access_after_payment', 'yes' );

	}

	public function init_panels(){
		$this->panels = array(
			'general' => array( 'title'=>__('General','shop_ct') ),
			'inventory' => array( 'title'=>__('Inventory','shop_ct') ),
			'downloadable_products' => array( 'title'=>__('Downloadable Product','shop_ct') ),
		);
	}

	/**
	 * Initialize Sections
	 */
	public function init_sections() {
		$this->sections = array(
			'measurements' => array(
				'title' => __('Measurements','shop_ct'),
				'panel' => 'general',
			),
			'reviews' => array(
				'title' => __('Reviews','shop_ct'),
				'panel'=>'general',
			),
			'inventory'=>array(
				'title'=>__('Inventory','shop_ct'),
				'panel'=>'inventory',
			),
			'downloadable_products'=>array(
				'title'=>__('Downloadable Products','shop_ct'),
				'panel'=>'downloadable_products'
			)
		);
	}

	/**
	 * Initialize Controls
	 */
	public function init_controls() {
		$this->controls=array(
			'shop_ct_weight_unit'=> array(
				'label'=>__('Weight Unit','shop_ct'),
				'section'=>'measurements',
				'type'=>'select',
				'default'=>$this->weight_unit,
				'choices'=>array(
					'kg'=>__('kg','shop_ct'),
					'g'=>__('g','shop_ct'),
					'lbs'=>__('lbs','shop_ct'),
					'oz'=>__('oz','shop_ct'),
				),
			),
			'shop_ct_dimension_unit' => array(
				'label'=>__('Dimensions Unit','shop_ct'),
				'section'=>'measurements',
				'type'=>'select',
				'default'=>$this->dimension_unit,
				'choices'=>array(
					'm'=>__('m','shop_ct'),
					'cm'=>__('cm','shop_ct'),
					'mm'=>__('mm','shop_ct'),
					'in'=>__('in','shop_ct'),
					'yd'=>__('yd','shop_ct'),
				)
			),
			'shop_ct_review_options'=>array(
				'label'=>__('Product Ratings','shop_ct'),
				'grouped'=>'yes',
				'section'=>'reviews',
				'type'=>'checkbox',
				'choices'=>array(
					'shop_ct_enable_review_rating'=>array(
						'label'=>__('Enable ratings on reviews','shop_ct'),
						'default'=>$this->enable_review_rating,
					),
					'shop_ct_review_rating_required'=>array(
						'label'=>__('Ratings are required to leave a review','shop_ct'),
						'default'=>$this->review_rating_required,
					),
					'shop_ct_review_rating_verification_label'=>array(
						'label'=>__('Show \'verified owner\' label for customer reviews','shop_ct'),
						'default'=>$this->review_rating_verification_label,
					),
					'shop_ct_review_rating_verification_required'=>array(
						'label'=>__('Only allow reviews from \'verified owners\'','shop_ct'),
						'default'=>$this->review_rating_verification_required
					)
				)
			),
			'shop_ct_manage_stock'=>array(
				'label'=>__('Manage Stock','shop_ct'),
				'description'=>__('Enable stock management','shop_ct'),
				'section'=>'inventory',
				'type'=>'checkbox',
				'default'=>$this->manage_stock,
			),
			'shop_ct_stock_email_recipient'=>array(
				'label'=>__('Notification Recipient(s)','shop_ct'),
				'section'=>'inventory',
				'type'=>'email',
				'default'=>$this->stock_email_recipient,
			),
			'shop_ct_notify_low_stock_amount'=>array(
				'label'=>__('Low Stock Threshold','shop_ct'),
				'section'=>'inventory',
				'type'=>'number',
				'default'=>$this->notify_low_stock_amount,
			),
			'shop_ct_notify_no_stock_amount'=>array(
				'label'=>__('Out Of Stock Threshold','shop_ct'),
				'section'=>'inventory',
				'type'=>'number',
				'default'=>$this->notify_no_stock_amount,
			),
			'shop_ct_hide_out_of_stock_items'=>array(
				'label'=>__('Out Of Stock Visibility','shop_ct'),
				'description'=>__('Hide out of stock items from the catalog','shop_ct'),
				'type'=>'checkbox',
				'section'=>'inventory',
				'default'=>$this->hide_out_of_stock_items,
			),
			'shop_ct_stock_format'=>array(
				'label'=>__('Stock Display Format','shop_ct'),
				'section'=>'inventory',
				'type'=>'select',
				'default'=>$this->stock_format,
				'choices'=>array(
					''=>__('Always show stock e.g. "12 in stock"','shop_ct'),
					'low_amount'=>__('Only show stock when low e.g. "Only 2 left in stock" vs. "In Stock"','shop_ct'),
					'no_amount'=>__('Never show stock amount','shop_ct')
				)
			),
			'shop_ct_file_download_method'=>array(
				'label'=>__('File Download Method','shop_ct'),
				'section'=>'downloadable_products',
				'type'=>'select',
				'default'=>$this->file_download_method,
				'choices'=>array(
					'force'=>__('Force Downloads','shop_ct'),
					'xsendfile'=>__('X-Accel-Redirect/X-Sendfile','shop_ct'),
					'redirect'=>__('Redirect only','shop_ct'),
				)
			),
			'shop_ct_download_access_restriction'=>array(
				'label'=>__('Access Restriction','shop_ct'),
				'section'=>'downloadable_products',
				'type'=>'checkbox',
				'grouped'=>'yes',
				'choices'=>array(
					'shop_ct_downloads_require_login'=>array(
						'label'=>__('Downloads require login','shop_ct'),
						'description'=>__('This setting does not apply to guest purchases.','shop_ct'),
						'default'=>$this->downloads_require_login,
					),
					'shop_ct_downloads_grant_access_after_payment'=>array(
						'label'=>__('Grant access to downloadable products after payment','shop_ct'),
						'description'=>__('Enable this option to grant access to downloads when orders are "processing", rather than "completed".','shop_ct'),
						'default'=>$this->downloads_grant_access_after_payment,
					)
				)
			)
		);
	}
}