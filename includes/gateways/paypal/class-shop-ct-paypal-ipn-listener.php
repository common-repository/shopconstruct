<?php


class Shop_CT_Paypal_IPN_Listener {
    /**
     * Initialize the Paypal IPN Listener
     */
	public static function init(){
		// Set this to true to use the sandbox endpoint during testing:
		$enable_sandbox = Shop_CT()->payment_gateways->payment_gateways()['paypal']->testmode;

	    // Use this to specify all of the email addresses that you have attached to paypal:
		$my_email_addresses = array( SHOP_CT()->payment_gateways->payment_gateways()['paypal']->email, SHOP_CT()->payment_gateways->payment_gateways()['paypal']->receiver_email );

		$save_log_file = true;
		$log_file_dir = __DIR__ . "/logs";

		$ipn = new Shop_CT_Paypal_IPN();
		if ($enable_sandbox) {
			$ipn->use_sandbox();
		}
        $ipn->use_php_certs();
		$verified = $ipn->verify_ipn();


		$data_text = "";
		foreach ($_POST as $key => $value) {
			$data_text .= $key . " = " . $value . "\r\n";
		}

		$test_text = "";
		if ($_POST["test_ipn"] == 1) {
			$test_text = "Test ";
		}

        // Check the receiver email to see if it matches your list of paypal email addresses
		$receiver_email_found = false;
		foreach ($my_email_addresses as $a) {
			if (strtolower($_POST["receiver_email"]) == strtolower($a)) {
				$receiver_email_found = true;
				break;
			}
		}

		list($year, $month, $day, $hour, $minute, $second, $timezone) = explode(":", date("Y:m:d:H:i:s:T"));
		$date = $year . "-" . $month . "-" . $day;
		$timestamp = $date . " " . $hour . ":" . $minute . ":" . $second . " " . $timezone;
		$dated_log_file_dir = $log_file_dir . "/" . $year . "/" . $month;

		$paypal_ipn_status = "VERIFICATION FAILED";
		if ($verified) {
			$paypal_ipn_status = "RECEIVER EMAIL MISMATCH";
			if ($receiver_email_found) {
				$paypal_ipn_status = "Completed Successfully";


				$order_id =$_POST['custom'];



				$order = new Shop_CT_Order( $order_id );

				switch( $_POST['payment_status'] ){
					case "Completed":
					    $status = 'shop-ct-processing';
					    $note = sprintf( __('%s - The payment has been completed, and the funds have been added successfully to your account balance', 'shop_ct' ), $timestamp );
                        //Shop_CT_Order::update_status( $order, 'shop-ct-processing' );
                        do_action('shop_ct_order_completed',$order);
						break;
					case "Denied":
                        $status = 'shop-ct-failed';
                        $note = sprintf( __( '%s - The payment was denied.' ), $timestamp );
						break;
					case "Pending":
					    $status = 'shop-ct-on-hold';
                        $note = sprintf( __( '%s -  The payment is pending.', 'shop_ct' ), $timestamp );
                        if( isset( $_POST['pending_reason'] ) ):
                            switch( $_POST['pending_reason'] ){
                                case 'address':
                                    $note .= __( 'The payment is pending because your customer did not include a confirmed shipping address and your Payment Receiving Preferences is set yo allow you to manually accept or deny each of these payments. To change your preference, go to the Preferences section of your Profile', 'shop_ct');
                                    break;
                                case 'authorization':
                                    $note .= __( 'You set the payment action to Authorization and have not yet captured funds.', 'shop_ct' );
                                    break;
                                case 'delayed_disbursement':
                                    $note .= __( 'The transaction has been approved and is currently awaiting funding from the bank. This typically takes less than 48 hrs. ', 'shop_ct' );
                                    break;
                                case 'echeck':
                                    $note .= __('The payment is pending because it was made by an eCheck that has not yet cleared.','shop_ct');
                                    break;
                                case 'intl':
                                    $note .= __('The payment is pending because you hold a non-U.S. account and do not have a withdrawal mechanism. You must manually accept or deny this payment from your Account Overview.','shop_ct');
                                    break;
                                case 'multi_currency':
                                    $note .= __(' You do not have a balance in the currency sent, and you do not have your profiles\'s Payment Receiving Preferences option set to automatically convert and accept this payment. As a result, you must manually accept or deny this payment.','shop_ct');
                                    break;
                                case 'order':
                                    $note .= __('You set the payment action to Order and have not yet captured funds.','shop_ct');
                                    break;
                                case 'paymentreview':
                                    $note .= __('The payment is pending while it is reviewed by PayPal for risk.','shop_ct');
                                    break;
                                case 'regulatory_review':
                                    $note .= __('The payment is pending because PayPal is reviewing it for compliance with government regulations. PayPal will complete this review within 72 hours.','shop_ct');
                                    break;
                                case 'unilateral':
                                    $note .= __('The payment is pending because it was made to an email address that is not yet registered or confirmed.','shop_ct');
                                    break;
                                case 'upgrade':
                                    $note .= __('The payment is pending because it was made via credit card and you must upgrade your account to Business or Premier status before you can receive the funds. This can also mean that you have reached the monthly limit for transactions on your account.','shop_ct');
                                    break;
                                case 'verify':
                                    $note .= __('The payment is pending because you are not yet verified. You must verify your account before you can accept this payment.','shop_ct');
                                    break;
                                case 'other':
                                    $note .= __('The reason your payment is set to a pending list is due to its undefined status. For more information, contact PayPal Customer Service.','shop_ct');
                                    break;
                            }
                        endif;
						break;
					case "Refunded":
					    $status = 'shop-ct-refunded';
                        $note = sprintf( __( '%s - You refunded the payment', 'shop_ct' ), $timestamp );
						break;
					case "Reversed":
					    $status = 'shop-ct-cancelled';
                        $note = sprintf( __( '%s - A payment was reversed due to a chargeback or other type of reversal. The funds have been removed from your account balance and returned to the buyer', 'shop_ct' ), $timestamp );
						break;
				}
				$order->set_status($status);
				$order->add_note( array( 'text' => $note ) );
				$order->save();
			}
		} elseif ($enable_sandbox) {
			if ($_POST["test_ipn"] != 1) {
				$paypal_ipn_status = "RECEIVED FROM LIVE WHILE SANDBOXED";
			}
		} elseif ($_POST["test_ipn"] == 1) {
			$paypal_ipn_status = "RECEIVED FROM SANDBOX WHILE LIVE";
		}

		if ($save_log_file) {
			// Create log file directory
			if (!is_dir($dated_log_file_dir)) {
				if (!file_exists($dated_log_file_dir)) {
					mkdir($dated_log_file_dir, 0777, true);
					if (!is_dir($dated_log_file_dir)) {
						$save_log_file = false;
					}
				} else {
					$save_log_file = false;
				}
			}
			// Restrict web access to files in the log file directory
			$htaccess_body = "RewriteEngine On" . "\r\n" . "RewriteRule .* - [L,R=404]";
			if ($save_log_file && (!is_file($log_file_dir . "/.htaccess") || file_get_contents($log_file_dir . "/.htaccess") !== $htaccess_body)) {
				if (!is_dir($log_file_dir . "/.htaccess")) {
					file_put_contents($log_file_dir . "/.htaccess", $htaccess_body);
					if (!is_file($log_file_dir . "/.htaccess") || file_get_contents($log_file_dir . "/.htaccess") !== $htaccess_body) {
						$save_log_file = false;
					}
				} else {
					$save_log_file = false;
				}
			}
			if ($save_log_file) {
				// Save data to text file
				file_put_contents($dated_log_file_dir . "/" . $test_text . "paypal_ipn_" . $date . ".txt", "paypal_ipn_status = " . $paypal_ipn_status . "\r\n" . "paypal_ipn_date = " . $timestamp . "\r\n" . $data_text . "\r\n", FILE_APPEND);
			}
		}

		// Reply with an empty 200 response to indicate to paypal the IPN was received correctly
		header("HTTP/1.1 200 OK");
		exit;
	}

}