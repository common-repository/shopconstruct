<?php

/**
 * Class Shop_CT_Payment_Gateway
 */
abstract class Shop_CT_Payment_Gateway extends Shop_CT_Settings {
	public $id;
    /**
     * @var string
     * @values ['yes','no']
     */
    public $enabled;
	/**
	 * Set if the place order button should be renamed on selection.
	 * @var string
	 */
    public $order_button_text;
	/**
	 * Payment method title.
	 * @var string
	 */
    public $title;
	/**
	 * Icon for the gateway.
	 * @var string
	 */
    public $icon;
	/**
	 * Description for the gateway.
	 * @var string
	 */
    public $description;
    /**
     * @var string
     */
    public $instructions;
	/**
	 * Check if the gateway is available for use.
	 *
	 * @return bool
	 */
	public function is_available() {
		return ( 'yes' === $this->enabled );
	}
	/**
	 * Return the gateway's title.
	 *
	 * @return string
	 */
	public function get_title() {
		return apply_filters( 'shop_ct_gateway_title', $this->title, $this->id );
	}
	/**
	 * Return the gateway's description.
	 *
	 * @return string
	 */
	public function get_description() {
		return apply_filters( 'shop_ct_gateway_description', $this->description, $this->id );
	}
    /**
     * Return the gateway's description.
     *
     * @return string
     */
    public function get_instructions() {
        return apply_filters( 'shop_ct_gateway_instructions', $this->instructions, $this->id );
    }
	/**
	 * Return the gateway's icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return apply_filters( 'shop_ct_gateway_icon', $this->icon, $this->id );
	}
	public function get_icon_img(){
        $icon = $this->icon ? '<img src="' . $this->icon . '" alt="' . esc_attr( $this->get_title() ) . '" />' : '';
        return apply_filters( 'shop_ct_gateway_icon_img', $icon, $this->id );
    }
	/**
	 * Core credit card form which gateways can use if needed.
	 *
	 * @param  array $args
	 * @param  array $fields
	 */
	public function credit_card_form( $args = array(), $fields = array() ) {
		// todo : register this
		wp_enqueue_script( 'shop_ct-credit-card-form' );

		$default_args = array(
			'fields_have_names' => true, // Some gateways like stripe don't need names as the form is tokenized.
		);

		$args = wp_parse_args( $args, apply_filters( 'shop_ct_credit_card_form_args', $default_args, $this->id ) );

		$default_fields = array(
			'card-number-field' => '<p class="form-row form-row-wide">
				<label for="' . esc_attr( $this->id ) . '-card-number">' . __( 'Card Number', 'shop_ct' ) . ' <span class="required">*</span></label>
				<input id="' . esc_attr( $this->id ) . '-card-number" class="input-text shop_ct-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="•••• •••• •••• ••••" name="' . ( $args['fields_have_names'] ? $this->id . '-card-number' : '' ) . '" />
			</p>',
			'card-expiry-field' => '<p class="form-row form-row-first">
				<label for="' . esc_attr( $this->id ) . '-card-expiry">' . __( 'Expiry (MM/YY)', 'shop_ct' ) . ' <span class="required">*</span></label>
				<input id="' . esc_attr( $this->id ) . '-card-expiry" class="input-text shop_ct-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="' . esc_attr__( 'MM / YY', 'shop_ct' ) . '" name="' . ( $args['fields_have_names'] ? $this->id . '-card-expiry' : '' ) . '" />
			</p>',
			'card-cvc-field' => '<p class="form-row form-row-last">
				<label for="' . esc_attr( $this->id ) . '-card-cvc">' . __( 'Card Code', 'shop_ct' ) . ' <span class="required">*</span></label>
				<input id="' . esc_attr( $this->id ) . '-card-cvc" class="input-text shop_ct-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="' . esc_attr__( 'CVC', 'shop_ct' ) . '" name="' . ( $args['fields_have_names'] ? $this->id . '-card-cvc' : '' ) . '" />
			</p>'
		);

		$fields = wp_parse_args( $fields, apply_filters( 'shop_ct_credit_card_form_fields', $default_fields, $this->id ) );
		?>
		<fieldset id="<?php echo $this->id; ?>-cc-form">
			<?php do_action( 'shop_ct_credit_card_form_start', $this->id ); ?>
			<?php
			foreach ( $fields as $field ) {
				echo $field;
			}
			?>
			<?php do_action( 'shop_ct_credit_card_form_end', $this->id ); ?>
			<div class="clear"></div>
		</fieldset>
		<?php
	}
}