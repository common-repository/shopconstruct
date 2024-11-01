<?php

class Shop_CT_Email_Order_Customer_Invoice extends Shop_CT_Email {


	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public static $action = 'shop_ct_customer_invoice';

	/**
	 * @var boolean
	 */
	public $enabled;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var
	 */
	public $customer_heading;

	/**
	 * @var string
	 */
	public $customer_message;

	/**
	 * @var string
	 */
	public $customer_subject;

	/**
	 * Shop_CT_Email_Order_New constructor.
	 *
	 * @internal param $receiver
	 * @internal param $order_data
	 *
	 */
	public function __construct() {
		$this->id = 'email_customer_invoice';
		$this->init();

		add_action( 'shop_ct_customer_invoice', array( $this, 'send_email' ), 10, 1 );
		add_action( 'shop_ct_email_customer_invoice_popup', array( $this, 'init_popup' ) );
	}

	/**
	 *
	 */
	public function init() {
		$this->enabled = $this->get_option( 'status', 'yes' ) == 'yes' ? 'yes' : 'no';
		$this->description = $this->get_option('description', 'An email with the invoice is sent to chosen recipients when the order and payment processes are successfully completed');
		$this->customer_subject = $this->get_option('customer_subject', __('Order Invoice','shop_ct'));
		$this->customer_heading = $this->get_option('customer_heading', __('Order Invoice','shop_ct'));
		$this->customer_message = $this->get_option('customer_message', __('Order Invoice','shop_ct'));
	}

	/**
	 * @param $template_path
	 * @param $template_file_name
	 * @param $data
	 *
	 * @return string
	 */
	protected function buildHtml( $template_path, $template_file_name, $data ) {
		extract( $data );

		ob_start();

		require $template_path . $template_file_name . '.php';

		return ob_get_clean();
	}

	/**
	 * @param $data
	 *
	 * @return array|bool
	 *
	 */
	public function send_email( $data ) {
		if ( $this->enabled === 'no' ) {
			return false;
		}

		$subject = $this->customer_subject;

		$message = $this->customer_message;

		$heading = $this->customer_heading;

		$template_path = SHOP_CT_CUSTOMER_EMAIL_TEMPLATES_PATH;

		$template_file_name = 'common';

		$html = $this->buildHtml( $template_path, $template_file_name, [
			'message' => $message,
			'heading' => $heading,
			'data'    => $data,
		]);

		$headers[] = 'Content-Type: text/html; charset=UTF-8';
		$receiver = $data['order_data']['order']->get_billing_email();

		$result   = [
			'email' => wp_mail( $receiver, $subject, $html, $headers ),
			'html'  => $html,
		];

		return $result;
	}

	public function init_popup() {
		$id = $_REQUEST['id'];

		$obj = new Shop_CT_Popup_Email_Settings();

		$obj->two_column = false;

		$obj->form_id = 'shop_ct_email_settings_form';

		$obj->sections['main'] = array(
			'title'    => __( Shop_CT_Email::get_emails()[$id]['label'], 'shop_ct' ),
			'column'   => 'left',
			'priority' => 1,
			'class'    => 'two-column',
		);

		$obj->controls['description'] = array(
			'label' => '',
			'type' => 'description',
			'section' => 'main',
			'default' => $this->description,
		);

		$obj->controls['status'] = array(
			'label'   => __( 'Enable', 'shop_ct' ) . '/' . __('Disable', 'shop_ct'),
			'type'    => 'checkbox',
			'section' => 'main',
			'default' => $this->enabled,
		);

		$obj->controls['customer_subject'] = array(
			'label' => __('Subject For Customer', 'shop_ct'),
			'type' => 'text',
			'section' => 'main',
			'default' => $this->customer_subject,
		);

		$obj->controls['customer_heading'] = array(
			'label' => __('Email Heading For Customer', 'shop_ct'),
			'type' => 'text',
			'section' => 'main',
			'default' => $this->customer_heading,
		);

		$obj->controls['customer_message'] = array(
			'label' => __('Email Message For Customer', 'shop_ct'),
			'type' => 'text',
			'section' => 'main',
			'default' => $this->customer_message,
		);

//		todo Add.
//			$obj->controls['type'] = array(
//				'label' => __('Recipient(s)', 'shop_ct'),
//				'type' => 'select',
//				'section' => 'main',
//				'choices' => Shop_CT_Email::get_available_email_types(),
//				'default' => get_option('shop_ct_email_' . $id . '_type'),
//			);

		$obj->controls['submit'] = array(
			'type' => 'submit',
			'section' => 'main',
			'label' => __('Save changes', 'shop_ct'),
		);

		ob_start();

		$obj->display();

		$html = ob_get_clean();

		echo json_encode( array(
			'return_html' => $html,
			'success' => 1,
		) );

		wp_die();
	}
}
