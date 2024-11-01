<?php

class Shop_CT_Dates
{

    /**
     * returns timezone gmt offset for wordpress ( set from Settings->timezone)
     *
     * @return string
     */
    public static function get_wp_gmt_offset(){
        $gmt_offset = get_option("gmt_offset","");
        $timezone_string = get_option("timezone_string","");
        if(!empty($timezone_string)){
            $tt = new DateTimeZone($timezone_string);
            $time = new DateTime('now', $tt);
            $gmt_offset = $time->format('Z')/3600;
        }

        return $gmt_offset;
    }

    public static function get_timezone()
    {
        $gmt_offset = get_option("gmt_offset","");
        $timezone_string = get_option("timezone_string","");
        if(!empty($timezone_string)){
            $tt = new DateTimeZone($timezone_string);
            $time = new DateTime('now', $tt);
            $gmt_offset = $time->format('Z')/3600;
        }

        $gmt_offset = floatval($gmt_offset) >= 0 ? '+'.$gmt_offset : $gmt_offset;

        return new DateTimeZone($gmt_offset);
    }

    public static function get_wp_datetime()
    {
        $gmt_offset = get_option("gmt_offset","");
        $timezone_string = get_option("timezone_string","");

        if(!empty($timezone_string)){
            $tt = new DateTimeZone($timezone_string);
        }else{
            $tt = new DateTimeZone((floatval($gmt_offset) >= 0 ? '+'.$gmt_offset : $gmt_offset));
        }

        return new DateTime('now', $tt);
    }

    /**
     * @param $date string
     * @param string $format
     * @return bool
     */
    public static function validate($date,$format = 'Y-m-d H:i:s')
    {
        return (false !== DateTime::createFromFormat('Y-m-d H:i', $date));
    }

}