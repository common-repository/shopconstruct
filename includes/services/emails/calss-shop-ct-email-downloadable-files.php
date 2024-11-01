<?php

class Shop_CT_Email_Downloadable_Files extends Shop_CT_Email
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public static $action = 'shop_ct_new_order';

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
    public function __construct()
    {
        $this->id = 'email_downloadable_files';
        $this->init();

        add_action('shop_ct_added_download_permissions', array($this, 'send_email'), 10, 1);
        add_action('shop_ct_email_downloadable_files_popup', array($this, 'init_popup'));
    }

    /**
     *
     */
    public function init()
    {
        $this->enabled = $this->get_option('status', 'yes') == 'yes' ? 'yes' : 'no';
        $this->receivers = implode(', ', $this->get_option('receiver', array('{customer}')));
        $this->description = $this->get_option('description', 'These emails are sent when new doanload permissions are added to a order.');
        $this->subject = $this->get_option('subject', __('Download Permissions added for an order', 'shop_ct'));
        $this->heading = $this->get_option('heading', __('Download Permissions added for an order', 'shop_ct'));
        $this->message = $this->get_option('message', __('There are downloadable files that you can download from {order_details_link}', 'shop_ct'));
    }

    /**
     * @param $template_path
     * @param $template_file_name
     * @param $data
     *
     * @return string
     */
    protected function buildHtml($template_path, $template_file_name, $data)
    {
        extract($data);

        ob_start();

        require $template_path . $template_file_name . '.php';

        return ob_get_clean();
    }

    /**
     * @param $for_admin
     * @param $data
     * @param $receiver
     *
     * @return array|bool
     */
    public function send_email($data)
    {
        if ('no' === $this->enabled) {
            return false;
        }

        /** @var Shop_CT_Order $order */
        $order = $data['order_data']['order'];

        $subject = $this->subject;

        $message = str_replace(
            '{order_details_link}',
            '<a href="' . add_query_arg(array('oid' => $order->get_id()), site_url('order-details')) . '">Order Details Page</a>',
            $this->message
        );

        $heading = $this->heading;

        $template_path = SHOP_CT_CUSTOMER_EMAIL_TEMPLATES_PATH;

        $template_file_name = 'basic';

        $html = $this->buildHtml($template_path, $template_file_name, array(
            'message' => $message,
            'heading' => $heading,
            'data' => $data,
        ));

        $headers[] = 'Content-Type: text/html; charset=UTF-8';

        $receiver = $order->get_billing_email();

        $result = array(
            'email' => wp_mail($receiver, $subject, $html, $headers),
            'html' => $html,
        );

        return $result;
    }

    /**
     *
     */
    public function init_popup()
    {
        $id = $_REQUEST['id'];

        $obj = new Shop_CT_Popup_Email_Settings();

        $obj->two_column = false;

        $obj->form_id = 'shop_ct_email_settings_form';

        $obj->sections['main'] = array(
            'title' => __(Shop_CT_Email::get_emails()[$id]['label'], 'shop_ct'),
            'priority' => 1,
        );

        $obj->controls['description'] = array(
            'label' => '',
            'type' => 'description',
            'section' => 'main',
            'default' => $this->description,
        );

        $obj->controls['status'] = array(
            'label' => __('Enable', 'shop_ct') . '/' . __('Disable', 'shop_ct'),
            'type' => 'checkbox',
            'section' => 'main',
            'default' => $this->enabled,
        );

        $obj->controls['receiver'] = array(
            'label' => __('Recipient(s)', 'shop_ct'),
            'type' => 'text',
            'section' => 'main',
            'default' => $this->receivers,
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

        $obj->controls['submit'] = array(
            'type' => 'submit',
            'section' => 'main',
            'label' => __('Save changes', 'shop_ct'),
        );

        ob_start();

        $obj->display();

        $html = ob_get_clean();

        echo json_encode(array(
            'return_html' => $html,
            'success' => 1,
        ));

        wp_die();
    }
}