<?php

class Shop_CT_Ajax_Display_Settings
{

    public function __construct()
    {
        add_action( "shop_ct_ajax_send_display_post_type", array($this, 'display_post_type') );
        add_action( "shop_ct_ajax_save_display_settings", array($this, 'save') );
    }

    public function display_post_type()
    {
        if ( isset( $_GET['checked_post_type_ids'] ) ) {
            $checked_post_type_ids = $_GET['checked_post_type_ids'];
        } else {
            $checked_post_type_ids = array();
        }

        ob_start();

        foreach ( $checked_post_type_ids as $checked_post_type_id ) {
            $post = get_post( $checked_post_type_id );
            \ShopCT\Core\TemplateLoader::get_template('admin/settings/display/single-item.php', compact('post'));
        }

        $output = ob_get_clean();
        echo json_encode( array( "success" => 1, "output" => $output ) );
        die;
    }

    public function save()
    {
        if ( !isset( $_GET['display_settings'] ) ) {
            echo json_encode(array("success" => 0));
            die;
        }

        $display_settings = $_GET['display_settings'];



        update_option( "shop_ct_display_shop_pages", $display_settings );

        echo json_encode( array( "success" => 1 ) );
        die;
    }
}
