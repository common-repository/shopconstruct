<?php
/**
 * Helper class for formatting
 * Class Shop_CT_Formatting
 */
class Shop_CT_Formatting {

    /**
     * @param float|integer|string $price
     * @param string $symbol
     * @return string
     */
    public static function format_price( $price, $symbol = null ){
        if (empty($price)){
            return __( 'Free!', 'shop_ct' );
        }

        $currency = Shop_CT()->settings->currency;
        $currency_pos = SHOP_CT()->settings->currency_pos;
        $num_decimals = SHOP_CT()->settings->price_num_decimals;
        $decimal_sep = SHOP_CT()->settings->price_decimal_sep;
        $thousand_sep = SHOP_CT()->settings->price_thousand_sep;

        if (empty($symbol)){
            $symbol = Shop_CT_Currencies::get_currency_symbol($currency);
        }

        if($currency_pos == "left"){
            $formatted_price = !empty($price)
                ? $symbol.number_format((float)$price, $num_decimals, $decimal_sep, $thousand_sep)
                : "";
        }elseif($currency_pos == "right"){
            $formatted_price = !empty($price)
                ? number_format((float)$price, $num_decimals, $decimal_sep, $thousand_sep). $symbol
                : "";
        }elseif($currency_pos == "left-space"){
            $formatted_price = !empty($price)
                ? $symbol."&nbsp;".number_format((float)$price, $num_decimals, $decimal_sep, $thousand_sep)
                : "";
        }else{
            $formatted_price = !empty($price)
                ? number_format((float)$price, $num_decimals, $decimal_sep, $thousand_sep)."&nbsp;".$symbol
                : "";
        };

        return $formatted_price;
    }

    /**
     * lean variables using sanitize_text_field.
     *
     * @param $var
     * @return array|string
     */
    public static function clean($var){
        return is_array( $var ) ? array_map( array(__CLASS__, 'clean'), $var ) : sanitize_text_field( $var );
    }

    /**
     * Sanitize taxonomy names. Slug format (no spaces, lowercase).
     *
     * urldecode is used to reverse munging of UTF8 characters.
     *
     * @param string $taxonomy
     * @return string
     */
    public static function sanitize_taxonomy_name($taxonomy){
        return apply_filters( 'shop_ct_sanitize_taxonomy_name', urldecode( sanitize_title( $taxonomy ) ), $taxonomy );
    }

    /**
     * @param $url
     * @return string
     */
    public static function get_filename_from_url( $url ){
        if (!is_array($url)) return false;
        $parts = explode('/', $url);

        $file = explode(".",end($parts));

        return array_shift($file);
    }

    /**
     * @param $url
     * @return string
     */
    public function get_file_extension_from_url($url){
        $exploded = explode(".", $url);

        $extension = end($exploded);

        return $extension;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public function sort_array_by_value_length($a, $b){
        if (strlen($a) > strlen($b)) {
            $r = 1;
        } elseif (strlen($a) == strlen($b)) {
            $r = 0;
        } else {
            $r = -1;
        }

        return $r;
    }

}