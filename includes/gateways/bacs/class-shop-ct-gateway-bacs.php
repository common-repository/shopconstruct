<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
/**
 * Bank Transfer Payment Gateway.
 *
 * Provides a Bank Transfer Payment Gateway. Based on code by Mike Pepper.
 *
 * @class       Shop_CT_Gateway_BACS
 * @extends     Shop_CT_Payment_Gateway
 */
class Shop_CT_Gateway_BACS extends Shop_CT_Payment_Gateway {
    /** @var array Array of locales */
    public $locale;
    /**
     * Constructor for the gateway.
     */
    public function __construct() {
        $this->id                 = 'bacs';
        $this->icon               = apply_filters('shop_ct_bacs_icon', '');
        // Load the settings.
        $this->init();
        $this->init_sections();
        $this->init_controls();
        // Customer Emails
        add_action( 'shop_ct_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );

    }
    /**
     * Define user set variables
     */
    public function init(){
        $this->enabled      = $this->get_option( 'enabled', 'no' );
        $this->title        = $this->get_option( 'title', __('Direct Bank Transfer','shop_ct') );
        $this->description  = $this->get_option( 'description', __('Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won\'t be shipped until the funds have cleared in our account.','shop_ct') );
        $this->instructions = $this->get_option( 'instructions', __('Make your payment directly into our bank account. Please use your Order ID as the payment reference. Your order won\'t be shipped until the funds have cleared in our account.','shop_ct') );
        $this->account_details = $this->get_option( 'accounts',
            array(
                array(
                    'account_name'   => '',
                    'account_number' => '',
                    'sort_code'      => '',
                    'bank_name'      => '',
                    'iban'           => '',
                    'bic'            => ''
                )
            )
        );
    }
    /**
     * Initialize Sections
     */
    public function init_sections(){


        $this->sections = array(
            'bacs'=>array(
                'title'=>__( 'BACS', 'shop_ct' ),
                'description'=>__( 'Allows payments by BACS, more commonly known as direct bank/wire transfer.', 'shop_ct' ),
            ),
        );
    }
    /**
     * Initialize Controls
     */
    public function init_controls(){
        $this->controls = array(
            'shop_ct_'.$this->id.'_enabled'=>array(
                'label'=>__('Enable Bank Transfer','shop_ct'),
                'type'=>'checkbox',
                'section'=>'bacs',
                'default'=>$this->enabled,
            ),
            'shop_ct_'.$this->id.'_name'=>array(
                'label'=>__('Title','shop_ct'),
                'type'=>'text',
                'section'=>'bacs',
                'default'=>$this->name,
            ),
            'shop_ct_'.$this->id.'_description'=>array(
                'label'=>__('Description','shop_ct'),
                'type'=>'textarea',
                'section'=>'bacs',
                'default'=>$this->description,
            ),
            'shop_ct_'.$this->id.'_instructions'=>array(
                'label'=>__('Instructions','shop_ct'),
                'type'=>'textarea',
                'section'=>'bacs',
                'default'=>$this->instructions,
            ),
            'shop_ct_'.$this->id.'_accounts'=>array(
                'label'=>__('','shop_ct'),
                'type'=>'account_details',
                'section'=>'bacs',
                'deafault'=>$this->account_details,
            )
        );
    }
    /**
     * Generate account details html.
     *
     * @return string
     */
    public function control_account_details($id,$control) {
        $country 	= SHOP_CT()->locations->get_base_country();
        $locale		= $this->get_country_locale();

        // Get sortcode label in the $locale array and use appropriate one
        $sortcode = isset( $locale[ $country ]['sortcode']['label'] ) ? $locale[ $country ]['sortcode']['label'] : __( 'Sort Code', 'shop_ct' );

        ?>
        <span class="control_title"><?php _e('Account Details','shop_ct'); ?></span>
        <table id="shop_ct_bacs_accounts" class="widefat shop_ct_input_table" cellspacing="0">
            <thead>
            <tr>
                <th id="cb" class="column-cb shop_ct-check-column"><input type="checkbox" id="cb-select-all" /></th>
                <th><?php _e( 'Account Name', 'shop_ct' ); ?></th>
                <th><?php _e( 'Account Number', 'shop_ct' ); ?></th>
                <th><?php _e( 'Bank Name', 'shop_ct' ); ?></th>
                <th><?php echo $sortcode; ?></th>
                <th><?php _e( 'IBAN', 'shop_ct' ); ?></th>
                <th><?php _e( 'BIC / Swift', 'shop_ct' ); ?></th>
            </tr>
            </thead>
            <tbody class="accounts">
            <?php
            $i = -1;
            if ( $this->account_details ) {
                foreach ( $this->account_details as $account ) {
                    $i++;

                    echo '<tr class="account">
                            <td class="shop-ct-check-column"><input id="cb-select-'.$i.'" type="checkbox" value="'.$i.'"</td>
                            <td><input class="shop_ct_setting" type="text" value="' . esc_attr( wp_unslash( $account['account_name'] ) ) . '" name="shop_ct_bacs_accounts['.$i.'][account_name]" /></td>
                            <td><input class="shop_ct_setting" type="text" value="' . esc_attr( $account['account_number'] ) . '" name="shop_ct_bacs_accounts['.$i.'][account_number]" /></td>
                            <td><input class="shop_ct_setting" type="text" value="' . esc_attr( wp_unslash( $account['bank_name'] ) ) . '" name="shop_ct_bacs_accounts['.$i.'][bank_name]" /></td>
                            <td><input class="shop_ct_setting" type="text" value="' . esc_attr( $account['sort_code'] ) . '" name="shop_ct_bacs_accounts['.$i.'][sort_code]" /></td>
                            <td><input class="shop_ct_setting" type="text" value="' . esc_attr( $account['iban'] ) . '" name="shop_ct_bacs_accounts['.$i.'][iban]" /></td>
                            <td><input class="shop_ct_setting" type="text" value="' . esc_attr( $account['bic'] ) . '" name="shop_ct_bacs_accounts['.$i.'][bic]" /></td>
                        </tr>';
                }
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="7"><a href="#" class="add button"><?php _e( '+ Add Account', 'shop_ct' ); ?></a> <a href="#" class="remove_rows button"><?php _e( 'Remove selected account(s)', 'shop_ct' ); ?></a></th>
            </tr>
            </tfoot>
        </table>
        <?php
    }
    /**
     * Add content to the ECWP emails.
     *
     * @param Shop_CT_order $order
     * @param bool $sent_to_admin
     * @param bool $plain_text
     */
    public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {

        if ( ! $sent_to_admin && 'bacs' === $order->get_payment_method() && $order->has_status( 'on-hold' ) ) {
            if ( $this->instructions ) {
                echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
            }
            $this->bank_details( $order->get_id() );
        }

    }
    /**
     * Get bank details and place into a list format.
     *
     * @param int $order_id
     */
    private function bank_details( $order_id ) {

        if ( empty( $this->account_details ) ) {
            return;
        }

        // Get order and store in $order
        $order 		= new Shop_CT_Order( $order_id );

        // Get the order country and country $locale
        $country 	= $order->get_billing_country();
        $locale		= $this->get_country_locale();

        // Get sortcode label in the $locale array and use appropriate one
        $sortcode = isset( $locale[ $country ]['sortcode']['label'] ) ? $locale[ $country ]['sortcode']['label'] : __( 'Sort Code', 'shop_ct' );

        $bacs_accounts = apply_filters( 'shop_ct_bacs_accounts', $this->account_details );

        if ( ! empty( $bacs_accounts ) ) {
            echo '<h2>' . __( 'Our Bank Details', 'shop_ct' ) . '</h2>' . PHP_EOL;

            foreach ( $bacs_accounts as $bacs_account ) {

                $bacs_account = (object) $bacs_account;

                if ( $bacs_account->account_name || $bacs_account->bank_name ) {
                    echo '<h3>' . wp_unslash( implode( ' - ', array_filter( array( $bacs_account->account_name, $bacs_account->bank_name ) ) ) ) . '</h3>' . PHP_EOL;
                }

                echo '<ul class="order_details bacs_details">' . PHP_EOL;

                // BACS account fields shown on the thanks page and in emails
                $account_fields = apply_filters( 'shop_ct_bacs_account_fields', array(
                    'account_number'=> array(
                        'label' => __( 'Account Number', 'shop_ct' ),
                        'value' => $bacs_account->account_number
                    ),
                    'sort_code'     => array(
                        'label' => $sortcode,
                        'value' => $bacs_account->sort_code
                    ),
                    'iban'          => array(
                        'label' => __( 'IBAN', 'shop_ct' ),
                        'value' => $bacs_account->iban
                    ),
                    'bic'           => array(
                        'label' => __( 'BIC', 'shop_ct' ),
                        'value' => $bacs_account->bic
                    )
                ), $order_id );

                foreach ( $account_fields as $field_key => $field ) {
                    if ( ! empty( $field['value'] ) ) {
                        echo '<li class="' . esc_attr( $field_key ) . '">' . esc_attr( $field['label'] ) . ': <strong>' . wptexturize( $field['value'] ) . '</strong></li>' . PHP_EOL;
                    }
                }

                echo '</ul>';
            }
        }
    }
    /**
     * Get country locale if localized.
     *
     * @return array
     */
    public function get_country_locale() {

        if ( ! $this->locale ) {

            // Locale information to be used - only those that are not 'Sort Code'
            $this->locale = apply_filters( 'shop_ct_get_bacs_locale', array(
                'AU' => array(
                    'sortcode'	=> array(
                        'label'		=> __( 'BSB', 'shop_ct' ),
                    ),
                ),
                'CA' => array(
                    'sortcode'	=> array(
                        'label'		=> __( 'Bank Transit Number', 'shop_ct' ),
                    ),
                ),
                'IN' => array(
                    'sortcode'	=> array(
                        'label'		=> __( 'IFSC', 'shop_ct' ),
                    ),
                ),
                'IT' => array(
                    'sortcode'	=> array(
                        'label'		=> __( 'Branch Sort', 'shop_ct' ),
                    ),
                ),
                'NZ' => array(
                    'sortcode'	=> array(
                        'label'		=> __( 'Bank Code', 'shop_ct' ),
                    ),
                ),
                'SE' => array(
                    'sortcode'	=> array(
                        'label'		=> __( 'Bank Code', 'shop_ct' ),
                    ),
                ),
                'US' => array(
                    'sortcode'	=> array(
                        'label'		=> __( 'Routing Number', 'shop_ct' ),
                    ),
                ),
                'ZA' => array(
                    'sortcode'	=> array(
                        'label'		=> __( 'Branch Code', 'shop_ct' ),
                    ),
                ),
            ) );

        }

        return $this->locale;
    }
}
