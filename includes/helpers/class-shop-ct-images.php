<?php

class Shop_CT_Images
{

    /**
     * @return string
     */
    public static function get_placeholder_img_src(){
        return apply_filters( 'shop_ct_placeholder_img_src', SHOP_CT()->plugin_url() . '/assets/images/placeholder.png' );
    }

    /**
     * @param $size
     * @return string
     */
    public static function get_placeholder_img( $size ){
        $dimensions = self::get_image_sizes( $size );

        return apply_filters('shop_ct_placeholder_img',
            '<img 
                src="' . self::get_placeholder_img_src() . '" 
                alt="' . esc_attr__( 'Placeholder', 'shop_ct' ) . '" 
                width="' . esc_attr( $dimensions['width'] ) . '" 
                class="shop_ct-placeholder wp-post-image" 
                height="' . esc_attr( $dimensions['height'] ) . '" />',
            $size,
            $dimensions );
    }

    /**
     * @param string|array $image_size
     * @return array
     * [ 'width' => int, 'height' => int, 'crop' => int ]
     */
    public static function get_image_sizes( $image_size ){
        if ( is_array( $image_size ) ) {
            $width  = isset( $image_size[0] ) ? $image_size[0] : 500;
            $height = isset( $image_size[1] ) ? $image_size[1] : 500;
            $crop   = isset( $image_size[2] ) ? $image_size[2] : 1;

            $size = array(
                'width'  => (int) $width,
                'height' => (int) $height,
                'crop'   => (int) $crop
            );

            $image_size = $width . '_' . $height;

        } elseif ( $image_size === 'thumbnail' ) {
            $size           = get_option( $image_size . '_image_size', array() );
            $size['width']  = isset( $size['width'] ) ? (int) $size['width'] : 500;
            $size['height'] = isset( $size['height'] ) ? (int) $size['height'] : 500;
            $size['crop']   = isset( $size['crop'] ) ? (int) $size['crop'] : 0;

        } else {
            $size = array(
                'width'  => 500,
                'height' => 500,
                'crop'   => 1
            );
        }

        return apply_filters( 'shop_ct_get_image_size_' . $image_size, $size );
    }


}