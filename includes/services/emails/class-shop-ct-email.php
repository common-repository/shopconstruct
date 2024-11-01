<?php

/**
 * Class Shop_CT_Email
 */
class Shop_CT_Email extends Shop_CT_Settings {

	/**
	 * @var string ID of email notification
	 */
	public $id;

	/**
	 *
	 */
	public $emails;

	/**
	 * @var array Array of email notification classes
	 */
	public $available_emails;

	/**
	 * @var string Email template file name WITHOUT extension (from "ecommerce/templates/email" folder).
	 */
	protected $templateFileName;

	/**
	 * @var string HTML of email
	 */
	protected $html;

	/**
	 * @var string Reciever
	 */
	public $toEmail = "";

	/**
	 * @var string Subject of email
	 */
	public $subject = "";

	/**
	 * @var string Message of email
	 */
	public $message;

	/**
	 * @var string
	 */
	public $from_name;

	/**
	 * @var string
	 */
	public $from_address;

	/**
	 * @var string
	 */
	public $header_image;

	/**
	 * @var string
	 */
	public $footer_text;

	/**
	 * @var string
	 */
	public $base_color;

	/**
	 * @var string
	 */
	public $background_color;

	/**
	 * @var string
	 */
	public $body_background_color;

	/**
	 * @var string
	 */
	public $text_color;
	

	/**
	 * Shop_CT_Email constructor.
	 */
	public function __construct() {
		$this->id = 'email';
		$this->init();
		$this->available_emails = self::get_emails();
		foreach ( $this->available_emails as $key => $email ) {
			if ( class_exists( $email['class_name'] ) ) {
				$this->emails[$key] = new $email['class_name'];
				//forward_static_call( array( $email['class_name'], 'make_action' ) );
			}
		}
	}

	/**
	 * @param string $key
	 * @param bool $default
	 * @param bool $concat
	 *
	 * @return mixed
	 */
	public function get_option( $key, $default = false, $concat = true ) {
		
		if ( ! $concat ) {
			return get_option( $key, $default );
		} else {
			return get_option( $this->plugin_id . "_" . $this->id . "_" . $key, $default );
		}
	}

	/**
	 * Initialize user defined variables
	 */
	public function init() {
		$this->from_name             = $this->get_option( 'from_name', 'Site title' );
		$this->from_address          = $this->get_option( 'from_address', get_option( 'admin_email' ) );
		$this->header_image          = $this->get_option( 'header_image', '' );
		$this->footer_text           = $this->get_option( 'footer_text', __( 'Site title - Powered by ShopConstruct', 'shop_ct' ) );
		$this->base_color            = $this->get_option( 'base_color', '#cc251d' );
		$this->background_color      = $this->get_option( 'background_color', '#f5f5f5' );
		$this->body_background_color = $this->get_option( 'body_background_color', '#fdfdfd' );
		$this->text_color            = $this->get_option( 'text_color', '#737373' );

		$this->init_sections();
		$this->init_controls();
	}

	/**
	 * Initialise settings
	 */
	public function init_sections() {
		$this->sections = array(
			'notifications' => array(
				'title'       => __( 'Email Notifications', 'shop_ct' ),
				'description' => __( 'Email notifications sent from ShopConstruct are listed below. Click on an email to configure it.', 'shop_ct' ),
			),
			'options'       => array(
				'title' => __( 'Email Sender Options', 'shop_ct' ),
			),
			'template'      => array(
				'title'       => __( 'Email Template', 'shop_ct' ),
				'description' => __( 'This section lets you customize the ShopConstruct emails.', 'shop_ct' ),
			),
		);
	}

	/**
	 * Initialise controls
	 */
	public function init_controls() {
		$this->controls = array(
			'shop_ct_' . $this->id . '_notifications'         => array(
				'section' => 'notifications',
				'type'    => 'email_notifications',
			),
			'shop_ct_' . $this->id . '_from_name'             => array(
				'section' => 'options',
				'label'   => __( '"From" Name', 'shop_ct' ),
				'default' => $this->from_name,
				'type'    => 'text',
			),
			'shop_ct_' . $this->id . '_from_address'          => array(
				'section' => 'options',
				'label'   => __( '"From" Address', 'shop_ct' ),
				'default' => $this->from_address,
				'type'    => 'text',
			),
			'shop_ct_' . $this->id . '_header_image'          => array(
				'section'     => 'template',
				'label'       => __( 'Header Image', 'shop_ct' ),
				'default'     => $this->header_image,
				'type'        => 'text',
				'placeholder' => __( 'N/A', 'shop_ct' ),
				'help'        => __( 'URL to an image you want to show in the email header. Upload images using the media uploader (Admin &gt; Media).', 'shop_ct' ),
			),
			'shop_ct_' . $this->id . '_footer_text'           => array(
				'section' => 'template',
				'label'   => __( 'Footer Text', 'shop_ct' ),
				'default' => $this->footer_text,
				'type'    => 'text',
			),
			'shop_ct_' . $this->id . '_base_color'            => array(
				'section' => 'template',
				'label'   => __( 'Base Colour', 'shop_ct' ),
				'default' => $this->base_color,
				'type'    => 'color',
			),
			'shop_ct_' . $this->id . '_background_color'      => array(
				'section' => 'template',
				'label'   => __( 'Background Colour', 'shop_ct' ),
				'default' => $this->background_color,
				'type'    => 'color',
			),
			'shop_ct_' . $this->id . '_body_background_color' => array(
				'section' => 'template',
				'label'   => __( 'Body Background Colour', 'shop_ct' ),
				'default' => $this->body_background_color,
				'type'    => 'color',
			),
			'shop_ct_' . $this->id . '_text_color'            => array(
				'section' => 'template',
				'label'   => __( 'Body Text Colour', 'shop_ct' ),
				'default' => $this->text_color,
				'type'    => 'textarea',
			),
		);
	}

	/**
	 * Email notifications table
	 *
	 * @param $id
	 * @param $control
	 */
	public function control_email_notifications( $id, $control ) {

		?>

		<table id="shop_ct_settings_email_table">
			<thead>
			<tr>
				<th></th>
				<th><?php _e( 'Email', 'shop_ct' ); ?></th>
				<th><?php _e( 'Content Type', 'shop_ct' ); ?></th>
				<th></th>
			</tr>
			</thead>
			<tfoot>
			</tfoot>
			<tbody>
			<?php
			foreach ( $this->available_emails as $id => $item ) :
				switch ( $item['status'] ) {
					case 'true' :
					case 'yes' :
						$icon = '<i class="fa fa-check-circle shop_ct_email_status_icon" aria-hidden="true"></i>';
						break;
					case 'false' :
					case 'no' :
						$icon = '<i class="fa fa-times-circle shop_ct_email_status_icon disabled" aria-hidden="true"></i>';
						break;
					default :
						$icon = $item['status'];
				};
				?>
				<tr data-id="<?php echo $id; ?>">
					<td><?php echo $icon; ?></td>
					<td><?php echo $item['label']; ?></td>
					<td><?php echo $item['type']; ?></td>
					<td><i class="fa fa-cog shop_ct_email_config_icon" aria-hidden="true"></i></td>
				</tr>
				<?php
			endforeach;
			?>
			</tbody>
		</table>

		<?php
	}

	public static function get_available_email_types() {
		return array(
			'html'  => __( 'HTML', 'shop_ct' ),
			'text'  => __( 'Plain text', 'shop_ct' ),
			'multi' => __( 'Multipart', 'shop_ct' ),
		);
	}

	/**
	 * Get available email types
	 * 
	 * @return mixed
	 */
	public static function get_emails() {
		return apply_filters( 'shop_ct_emails_list', array(
			'new_order'        => array(
				'status'     => get_option( 'shop_ct_email_new_order_status', 'true' ),
				'label'      => 'New order',
				'type'       => 'text/html',
				'recipient'  => str_replace('{customer}', __('Customer', 'shop_ct'), implode( ', ', get_option( 'shop_ct_email_new_order_receiver', array() ) )),
				'class_name' => 'Shop_CT_Email_Order_New',
			),
            'downloadable_files'        => array(
				'status'     => get_option( 'shop_ct_email_downloadable_files_status', 'true' ),
				'label'      => 'Downloadable files',
				'type'       => 'text/html',
				'class_name' => 'Shop_CT_Email_Downloadable_Files',
			),
			'cancelled_order'  => array(
				'status'     => get_option( 'shop_ct_email_cancelled_order_status', 'true' ),
				'label'      => 'Cancelled order',
				'type'       => 'text/html',
				'recipient'  => str_replace('{customer}', __('Customer', 'shop_ct'), implode( ', ', get_option( 'shop_ct_email_cancelled_order_receiver', array() ) )),
				'class_name' => 'Shop_CT_Email_Order_Cancelled',
			),
			'failed_order'     => array(
				'status'     => get_option( 'shop_ct_email_failed_order_status', 'true' ),
				'label'      => 'Failed order',
				'type'       => 'text/html',
				'recipient'  => str_replace('{customer}', __('Customer', 'shop_ct'), implode( ', ', get_option( 'shop_ct_email_failed_order_receiver', array() ) )),
				'class_name' => 'Shop_CT_Email_Order_Failed',
			),
//			'on_hold_order'    => array(
//				'status'     => get_option( 'shop_ct_email_on_hold_order_status', 'true' ),
//				'label'      => 'Order on-hold',
//				'type'       => 'text/html',
//				'recipient'  => implode( ', ', get_option( 'shop_ct_email_on_hold_order_receiver', array() ) ),
//				'class_name' => 'Shop_CT_Email_Order_Failed',
//			),
			'completed_order'  => array(
				'status'     => get_option( 'shop_ct_email_completed_order_status', 'true' ),
				'label'      => 'Completed order',
				'type'       => 'text/html',
				'recipient'  => str_replace('{customer}', __('Customer', 'shop_ct'), implode( ', ', get_option( 'shop_ct_email_completed_order_receiver', array() ) )),
				'class_name' => 'Shop_CT_Email_Order_Completed',
			),
			'refunded_order'   => array(
				'status'     => get_option( 'shop_ct_email_refunded_order_status', 'true' ),
				'label'      => 'Refunded order',
				'type'       => 'text/html',
				'recipient'  => str_replace('{customer}', __('Customer', 'shop_ct'), implode( ', ', get_option( 'shop_ct_email_refunded_order_receiver', array() ) )),
				'class_name' => 'Shop_CT_Email_Order_Refunded',
			),
			'customer_invoice' => array(
				'status'     => get_option( 'shop_ct_email_customer_invoice_status', 'true' ),
				'label'      => 'Customer invoice',
				'type'       => 'text/html',
				'recipient'  => __('Customer', 'shop_ct'),
				'class_name' => 'Shop_CT_Email_Order_Customer_Invoice',
			),
		) );
	}
}