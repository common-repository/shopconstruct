<?php

class Shop_CT_Ajax_Settings
{

    public function __construct()
    {
        add_action('shop_ct_ajax_save_settings', array($this,'save'));
    }

    public function save()
    {
        if ( isset( $_POST['formData'] ) && ! empty( $_POST['formData'] ) ) {
            parse_str( $_POST['formData'], $formData );
        } else {
            die( 0 );
        }
        if ( isset( $formData ) && ! empty( $formData ) ) {
            foreach ( $formData as $key => $value ) {
                if ( get_option( $key ) !== false ) {
                    // The option already exists, so update it.
                    update_option( $key, $value );
                } else {
                    add_option( $key, $value );
                }
            }
        }

        echo json_encode( array( 'data' => $formData, "success" => "Settings saved successfully" ) );
        die;
    }

}