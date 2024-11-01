<?php
/**
 * Class Shop_CT_Email_Order_New
 */
final class Shop_CT_Email_Order_Completed extends Shop_CT_Email {

	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public static $action = 'shop_ct_completed_order';

	/**
	 * @var mixed|void
	 */
	public $receivers;

	/**
	 * @var boolean
	 */
	public $enabled;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var string
	 */
	public $subject;

	/**
	 * @var string
	 */
	public $heading;

	/**
	 * Shop_CT_Email_Order_New constructor.
	 *
	 * @internal param $receiver
	 * @internal param $order_data
	 *
	 */
	public function __construct() {
		$this->id = 'email_completed_order';
		$this->init();

		add_action( 'shop_ct_completed_order', array( $this, 'send_emails' ), 10, 1 );
		add_action( 'shop_ct_email_completed_order_popup', array( $this, 'init_popup' ) );
	}

	/**
	 *
	 */
	public function init() {
		$this->enabled = $this->get_option( 'status', 'yes' ) == 'yes' ? 'yes' : 'no';
		$this->receivers = implode(', ', $this->get_option('receiver', array(get_option('admin_email'))));
		$this->description = $this->get_option('description', 'Completed order emails are sent to chosen recipient(s) when an order is marked as completed.');
		$this->subject = $this->get_option('subject', __('Order Completed','shop_ct'));
		$this->heading = $this->get_option('heading', __('Order Completed','shop_ct'));
		$this->message = $this->get_option('message', __('Your Order has been completed. Here are the order details:','shop_ct'));
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
	 */
	public function send_emails( $data ) {

		$receivers = empty($this->receivers) ? array() : explode(',', $this->receivers);

		foreach ( $receivers as $receiver ) {
			$receiver = trim($receiver);
			$this->send_email( $data, $receiver );
		}
	}

	/**
	 * @param $data
	 * @param $receiver
	 *
	 * @return array|bool
	 */
	public function send_email( $data, $receiver ) {
		if ( $this->enabled === 'no' ) {
			return false;
		}

		$subject = $this->subject;

		$message = $this->message;

		$heading = $this->heading;

		$template_path = SHOP_CT_ADMIN_EMAIL_TEMPLATES_PATH;

		$template_file_name = 'common';

		$html = $this->buildHtml( $template_path, $template_file_name, array(
			'message' => $message,
			'heading' => $heading,
			'data'    => $data,
		) );

		$headers[] = 'Content-Type: text/html; charset=UTF-8';

		$result = array(
			'email' => wp_mail( $receiver, $subject, $html, $headers ),
			'html'  => $html,
		);

		return $result;
	}

	/**
	 *
	 */
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

		$obj->controls['receiver'] = array(
			'label' => __('Recipient(s)', 'shop_ct'),
			'type' => 'text',
			'section' => 'main',
			'default' => $this->receivers,
			'attrs' => array('class' => 'shop_ct_single_receiver')
		);

		$obj->controls['subject'] = array(
			'label' => __('Subject', 'shop_ct'),
			'type' => 'text',
			'section' => 'main',
			'default' => $this->subject,
		);

		$obj->controls['heading'] = array(
			'label' => __('Email Heading', 'shop_ct'),
			'type' => 'text',
			'section' => 'main',
			'default' => $this->heading,
		);

		$obj->controls['message'] = array(
			'label' => __('Email Message', 'shop_ct'),
			'type' => 'text',
			'section' => 'main',
			'default' => $this->message,
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