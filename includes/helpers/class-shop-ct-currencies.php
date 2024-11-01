<?php

class Shop_CT_Currencies
{
    /**
     * @param string $currency
     * @return string
     */
    public static function get_currency_symbol($currency = null)
    {
        if (empty($currency)) {
            $currency = SHOP_CT()->settings->currency;
        }

        switch ($currency) {
            case 'AMD' :
                $currency_symbol = '&#1423;';
                break;
            case 'AED' :
                $currency_symbol = 'د.إ';
                break;
            case 'AUD' :
            case 'ARS' :
            case 'CAD' :
            case 'CLP' :
            case 'COP' :
            case 'HKD' :
            case 'MXN' :
            case 'NZD' :
            case 'SGD' :
            case 'USD' :
                $currency_symbol = '&#36;';
                break;
            case 'BDT':
                $currency_symbol = '&#2547;&nbsp;';
                break;
            case 'BGN' :
                $currency_symbol = '&#1083;&#1074;.';
                break;
            case 'BRL' :
                $currency_symbol = '&#82;&#36;';
                break;
            case 'CHF' :
                $currency_symbol = '&#67;&#72;&#70;';
                break;
            case 'CNY' :
            case 'JPY' :
            case 'RMB' :
                $currency_symbol = '&yen;';
                break;
            case 'CZK' :
                $currency_symbol = '&#75;&#269;';
                break;
            case 'DKK' :
                $currency_symbol = 'DKK';
                break;
            case 'DOP' :
                $currency_symbol = 'RD&#36;';
                break;
            case 'EGP' :
                $currency_symbol = 'EGP';
                break;
            case 'EUR' :
                $currency_symbol = '&euro;';
                break;
            case 'GBP' :
                $currency_symbol = '&pound;';
                break;
            case 'HRK' :
                $currency_symbol = 'Kn';
                break;
            case 'HUF' :
                $currency_symbol = '&#70;&#116;';
                break;
            case 'IDR' :
                $currency_symbol = 'Rp';
                break;
            case 'ILS' :
                $currency_symbol = '&#8362;';
                break;
            case 'INR' :
                $currency_symbol = 'Rs.';
                break;
            case 'ISK' :
                $currency_symbol = 'Kr.';
                break;
            case 'KIP' :
                $currency_symbol = '&#8365;';
                break;
            case 'KRW' :
                $currency_symbol = '&#8361;';
                break;
            case 'MYR' :
                $currency_symbol = '&#82;&#77;';
                break;
            case 'NGN' :
                $currency_symbol = '&#8358;';
                break;
            case 'NOK' :
                $currency_symbol = '&#107;&#114;';
                break;
            case 'NPR' :
                $currency_symbol = 'Rs.';
                break;
            case 'PHP' :
                $currency_symbol = '&#8369;';
                break;
            case 'PLN' :
                $currency_symbol = '&#122;&#322;';
                break;
            case 'PYG' :
                $currency_symbol = '&#8370;';
                break;
            case 'RON' :
                $currency_symbol = 'lei';
                break;
            case 'RUB' :
                $currency_symbol = '&#1088;&#1091;&#1073;.';
                break;
            case 'SEK' :
                $currency_symbol = '&#107;&#114;';
                break;
            case 'THB' :
                $currency_symbol = '&#3647;';
                break;
            case 'TRY' :
                $currency_symbol = '&#8378;';
                break;
            case 'TWD' :
                $currency_symbol = '&#78;&#84;&#36;';
                break;
            case 'UAH' :
                $currency_symbol = '&#8372;';
                break;
            case 'VND' :
                $currency_symbol = '&#8363;';
                break;
            case 'ZAR' :
                $currency_symbol = '&#82;';
                break;
            default :
                $currency_symbol = '';
                break;
        }

        return apply_filters('shop_ct_currency_symbol', $currency_symbol, $currency);
    }

    public static function get_currencies()
    {
        return apply_filters('shop_ct_currencies',
            array(
                'AMD' => __('Armenian Dram', 'shop_ct'),
                'AED' => __('United Arab Emirates Dirham', 'shop_ct'),
                'ARS' => __('Argentine Peso', 'shop_ct'),
                'AUD' => __('Australian Dollars', 'shop_ct'),
                'BDT' => __('Bangladeshi Taka', 'shop_ct'),
                'BRL' => __('Brazilian Real', 'shop_ct'),
                'BGN' => __('Bulgarian Lev', 'shop_ct'),
                'CAD' => __('Canadian Dollars', 'shop_ct'),
                'CLP' => __('Chilean Peso', 'shop_ct'),
                'CNY' => __('Chinese Yuan', 'shop_ct'),
                'COP' => __('Colombian Peso', 'shop_ct'),
                'CZK' => __('Czech Koruna', 'shop_ct'),
                'DKK' => __('Danish Krone', 'shop_ct'),
                'DOP' => __('Dominican Peso', 'shop_ct'),
                'EUR' => __('Euros', 'shop_ct'),
                'HKD' => __('Hong Kong Dollar', 'shop_ct'),
                'HRK' => __('Croatia kuna', 'shop_ct'),
                'HUF' => __('Hungarian Forint', 'shop_ct'),
                'ISK' => __('Icelandic krona', 'shop_ct'),
                'IDR' => __('Indonesia Rupiah', 'shop_ct'),
                'INR' => __('Indian Rupee', 'shop_ct'),
                'NPR' => __('Nepali Rupee', 'shop_ct'),
                'ILS' => __('Israeli Shekel', 'shop_ct'),
                'JPY' => __('Japanese Yen', 'shop_ct'),
                'KIP' => __('Lao Kip', 'shop_ct'),
                'KRW' => __('South Korean Won', 'shop_ct'),
                'MYR' => __('Malaysian Ringgits', 'shop_ct'),
                'MXN' => __('Mexican Peso', 'shop_ct'),
                'NGN' => __('Nigerian Naira', 'shop_ct'),
                'NOK' => __('Norwegian Krone', 'shop_ct'),
                'NZD' => __('New Zealand Dollar', 'shop_ct'),
                'PYG' => __('Paraguayan Guaraní', 'shop_ct'),
                'PHP' => __('Philippine Pesos', 'shop_ct'),
                'PLN' => __('Polish Zloty', 'shop_ct'),
                'GBP' => __('Pounds Sterling', 'shop_ct'),
                'RON' => __('Romanian Leu', 'shop_ct'),
                'RUB' => __('Russian Ruble', 'shop_ct'),
                'SGD' => __('Singapore Dollar', 'shop_ct'),
                'ZAR' => __('South African rand', 'shop_ct'),
                'SEK' => __('Swedish Krona', 'shop_ct'),
                'CHF' => __('Swiss Franc', 'shop_ct'),
                'TWD' => __('Taiwan New Dollars', 'shop_ct'),
                'THB' => __('Thai Baht', 'shop_ct'),
                'TRY' => __('Turkish Lira', 'shop_ct'),
                'UAH' => __('Ukrainian Hryvnia', 'shop_ct'),
                'USD' => __('US Dollars', 'shop_ct'),
                'VND' => __('Vietse Dong', 'shop_ct'),
                'EGP' => __('Egyptian Pound', 'shop_ct')
            )
        );
    }

}