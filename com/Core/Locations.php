<?php


namespace ShopCT\Core;


class Locations
{
    /**
     * Auto-load in-accessible properties on demand.
     *
     * @param  mixed $key
     *
     * @return mixed
     */
    public function __get( $key ) {
        if ( 'countries' == $key ) {
            return $this->get_countries();
        } elseif ( 'states' == $key ) {
            return $this->get_states();
        }
    }

    /**
     * Get all countries.
     * @return array
     */
    public function get_countries() {
        if ( !isset( $this->countries ) || empty( $this->countries ) || null === $this->countries ) {
            $this->countries = apply_filters( 'shop_ct_countries', include( SHOP_CT()->plugin_path() . '/i18n/countries.php' ) );
            if ( apply_filters( 'shop_ct_sort_countries', true ) ) {
                asort( $this->countries );
            }
        }
        return $this->countries;
    }

    /**
     * Get all continents.
     * @return array
     */
    public function get_continents() {
        if ( empty( $this->continents ) ) {
            $this->continents = apply_filters( 'shop_ct_continents', include( SHOP_CT()->plugin_path() . '/i18n/continents.php' ) );
        }
        return $this->continents;
    }
    /**
     * Get continent code for a country code.
     * @param string $cc string
     * @return string
     */
    public function get_continent_code_for_country( $cc ) {
        $cc                 = trim( strtoupper( $cc ) );
        $continents         = $this->get_continents();
        $continents_and_ccs = wp_list_pluck( $continents, 'countries' );
        foreach ( $continents_and_ccs as $continent_code => $countries ) {
            if ( false !== array_search( $cc, $countries ) ) {
                return $continent_code;
            }
        }
        return '';
    }

    /**
     * Load the states.
     */
    public function load_country_states() {
        global $states;

        // States set to array() are blank i.e. the country has no use for the state field.
        $states = array(
            'AF' => array(),
            'AT' => array(),
            'AX' => array(),
            'BE' => array(),
            'BI' => array(),
            'CZ' => array(),
            'DE' => array(),
            'DK' => array(),
            'EE' => array(),
            'FI' => array(),
            'FR' => array(),
            'IS' => array(),
            'IL' => array(),
            'KR' => array(),
            'NL' => array(),
            'NO' => array(),
            'PL' => array(),
            'PT' => array(),
            'SG' => array(),
            'SK' => array(),
            'SI' => array(),
            'LK' => array(),
            'SE' => array(),
            'VN' => array(),
        );

        // Load only the state files the shop owner wants/needs.
        $allowed = array_merge( $this->get_allowed_countries(), $this->get_shipping_countries() );

        if ( $allowed ) {
            foreach ( $allowed as $code => $country ) {
                if ( ! isset( $states[ $code ] ) && file_exists( SHOP_CT()->plugin_path() . '/i18n/states/' . $code . '.php' ) ) {
                    include( SHOP_CT()->plugin_path() . '/i18n/states/' . $code . '.php' );
                }
            }
        }

        $this->states = apply_filters( 'shop_ct_states', $states );
    }

    /**
     * Get the states for a country.
     *
     * @param  string $cc country code
     *
     * @return array of states
     */
    public function get_states( $cc = null ) {
        if ( empty( $this->states ) ) {
            $this->load_country_states();
        }

        if ( ! is_null( $cc ) ) {
            return isset( $this->states[ $cc ] ) ? $this->states[ $cc ] : false;
        } else {
            return $this->states;
        }
    }

    /**
     * Get the base country for the store.
     * @return string
     */
    public function get_base_country() {
        $default = Shop_CT()->locations->get_base_location();

        return apply_filters( 'shop_ct_countries_base_country', $default['country'] );
    }

    /**
     * Get the base state for the store.
     * @return string
     */
    public function get_base_state() {
        $default = SHOP_CT()->locations->get_base_location();

        return apply_filters( 'shop_ct_countries_base_state', $default['state'] );
    }

    /**
     * Get the base city for the store.
     * @return string
     */
    public function get_base_city() {
        return apply_filters( 'shop_ct_countries_base_city', '' );
    }

    /**
     * Get the base postcode for the store.
     * @return string
     */
    public function get_base_postcode() {
        return apply_filters( 'shop_ct_countries_base_postcode', '' );
    }

    /**
     * Get the allowed countries for the store.
     * @return array
     */
    public function get_allowed_countries() {
        if ( get_option( 'shop_ct_allowed_countries' ) !== 'specific' ) {
            return $this->countries;
        }

        $countries = array();

        $raw_countries = get_option( 'shop_ct_specific_allowed_countries', array() );

        if ( $raw_countries ) {
            foreach ( $raw_countries as $country ) {
                $countries[ $country ] = $this->countries[ $country ];
            }
        }

        return apply_filters( 'shop_ct_countries_allowed_countries', $countries );
    }

    /**
     * Get the countries you ship to.
     * @return array
     */
    public function get_shipping_countries() {
        if ( get_option( 'shop_ct_ship_to_countries' ) == '' ) {
            return $this->get_allowed_countries();
        }

        if ( get_option( 'shop_ct_ship_to_countries' ) !== 'specific' ) {
            return $this->countries;
        }

        $countries = array();

        $raw_countries = get_option( 'shop_ct_specific_ship_to_countries' );

        foreach ( $raw_countries as $country ) {
            $countries[ $country ] = $this->countries[ $country ];
        }

        return apply_filters( 'shop_ct_countries_shipping_countries', $countries );
    }

    /**
     * Get allowed country states.
     * @return array
     */
    public function get_allowed_country_states() {
        if ( get_option( 'shop_ct_allowed_countries' ) !== 'specific' ) {
            return $this->states;
        }

        $states = array();

        $raw_countries = get_option( 'shop_ct_specific_allowed_countries' );

        foreach ( $raw_countries as $country ) {
            if ( isset( $this->states[ $country ] ) ) {
                $states[ $country ] = $this->states[ $country ];
            }
        }

        return apply_filters( 'shop_ct_countries_allowed_country_states', $states );
    }

    /**
     * Get shipping country states.
     * @return array
     */
    public function get_shipping_country_states() {
        if ( get_option( 'shop_ct_ship_to_countries' ) == '' ) {
            return $this->get_allowed_country_states();
        }

        if ( get_option( 'shop_ct_ship_to_countries' ) !== 'specific' ) {
            return $this->states;
        }

        $states = array();

        $raw_countries = get_option( 'shop_ct_specific_ship_to_countries' );

        foreach ( $raw_countries as $country ) {
            if ( ! empty( $this->states[ $country ] ) ) {
                $states[ $country ] = $this->states[ $country ];
            }
        }

        return apply_filters( 'shop_ct_countries_shipping_country_states', $states );
    }

    /**
     * Gets an array of countries in the EU.
     *
     * MC (monaco) and IM (isle of man, part of UK) also use VAT.
     *
     * @param string $type
     *
     * @return array
     */
    public function get_european_union_countries( $type = '' ) {
        $countries = array(
            'AT',
            'BE',
            'BG',
            'CY',
            'CZ',
            'DE',
            'DK',
            'EE',
            'ES',
            'FI',
            'FR',
            'GB',
            'GR',
            'HU',
            'HR',
            'IE',
            'IT',
            'LT',
            'LU',
            'LV',
            'MT',
            'NL',
            'PL',
            'PT',
            'RO',
            'SE',
            'SI',
            'SK'
        );

        if ( 'eu_vat' === $type ) {
            $countries[] = 'MC';
            $countries[] = 'IM';
        }

        return $countries;
    }

    /**
     * Gets the correct string for shipping - either 'to the' or 'to'
     * @return string
     */
    public function shipping_to_prefix( $country_code = '' ) {
        $country_code = $country_code ? $country_code : SHOP_CT()->customer->get_shipping_country();
        $countries    = array( 'GB', 'US', 'AE', 'CZ', 'DO', 'NL', 'PH', 'USAF' );
        $return       = in_array( $country_code, $countries ) ? __( 'to the', 'shop_ct' ) : __( 'to', 'shop_ct' );

        return apply_filters( 'shop_ct_countries_shipping_to_prefix', $return, $country_code );
    }

    /**
     * Prefix certain countries with 'the'
     * @return string
     */
    public function estimated_for_prefix( $country_code = '' ) {
        $country_code = $country_code ? $country_code : $this->get_base_country();
        $countries    = array( 'GB', 'US', 'AE', 'CZ', 'DO', 'NL', 'PH', 'USAF' );
        $return       = in_array( $country_code, $countries ) ? __( 'the', 'shop_ct' ) . ' ' : '';

        return apply_filters( 'shop_ct_countries_estimated_for_prefix', $return, $country_code );
    }

    /**
     * Outputs the list of countries and states for use in dropdown boxes.
     *
     * @param string $selected_country (default: '')
     * @param string $selected_state (default: '')
     * @param bool $escape (default: false)
     * @param bool $escape (default: false)
     */
    public function country_dropdown_options( $selected_country = '', $selected_state = '', $escape = false ) {
        if ( $this->countries ) {
            foreach ( $this->countries as $key => $value ) :
                if ( $states = $this->get_states( $key ) ) :
                    echo '<optgroup label="' . esc_attr( $value ) . '">';
                    foreach ( $states as $state_key => $state_value ) :
                        echo '<option value="' . esc_attr( $key ) . ':' . $state_key . '"';

                        if ( $selected_country == $key && $selected_state == $state_key ) {
                            echo ' selected="selected"';
                        }

                        echo '>' . $value . ' &mdash; ' . ( $escape ? esc_js( $state_value ) : $state_value ) . '</option>';
                    endforeach;
                    echo '</optgroup>';
                else :
                    echo '<option';
                    if ( $selected_country == $key && $selected_state == '*' ) {
                        echo ' selected="selected"';
                    }
                    echo ' value="' . esc_attr( $key ) . '">' . ( $escape ? esc_js( $value ) : $value ) . '</option>';
                endif;
            endforeach;
        }
    }

    /**
     * @param array $attr
     *
     * @return string
     */
    public function get_all_countries_dropdown( $attr ) {
        $placeholder = isset( $attr['placeholder'] ) ? $attr['placeholder'] : null;
        $multiple = isset( $attr['multiple'] ) ? $attr['multiple'] : false;
        $id = isset( $attr['id'] ) ? $attr['id'] : null;
        $name = isset( $attr['name'] ) ? $attr['name'] : null;
        $name .= $multiple === 'yes' ? '[]' : '';
        $selected_country_code = isset( $attr['selected'] ) ? $attr['selected'] : array();
        $class = array( 'select2' );
        if ( isset( $attr['class'] ) )
            if ( is_array( $attr['class'] ) ) $class = array_merge ( $class, $attr['class'] );
            elseif ( is_string( $attr['class'] ) ) $class = array_merge ( $class, explode( ' ', $attr['class']) );
            else
                $class = null;

        ob_start();
        echo '<select  ' . ( $name ? 'name="' . $name . '"' : '' ) . ( $id ? 'id="' . $id . '"' : '' ) . ( $class ? 'class="' . implode( ' ', $class ) . '" ' : '' ) . ( $multiple === 'yes' ? 'multiple ' : '' ) . ( $placeholder ? 'data-placeholder="' . $placeholder . '"' : '' ) . '>';
        if($multiple !== 'yes'){
            echo '<option>&#8212;Select&#8212;</option>';
        }
        if ( is_array( $selected_country_code ) && ! empty( $selected_country_code ) ) {
            foreach ( $this->countries as $code => $country ) {
                if ( $states = $this->get_states( $code ) ) :
                    echo '<optgroup label="' . esc_attr( $country ) . '">';
                    foreach ( $states as $state_key => $state_value ):
                        $state_label = esc_attr( $code ) . ':' . $state_key;
                        echo '<option value="' . $state_label . '"';

                        if ( in_array( $state_label, $selected_country_code ) ) {
                            echo ' selected="selected"';
                        }
                        echo '>' . $country . ' &mdash; ' . ( $state_value ) . '</option>';
                    endforeach;
                else:
                    echo '<option value="' . $code . '"';

                    if ( in_array( $code, $selected_country_code ) ) {
                        echo ' selected="selected"';
                    }
                    echo '>' . $country . '</option>';
                endif;
            }
        } elseif ( is_string( $selected_country_code ) ) {
            foreach ( $this->countries as $code => $country ) {
                if ( $states = $this->get_states( $code ) ) :
                    echo '<optgroup label="' . esc_attr( $country ) . '">';
                    foreach ( $states as $state_key => $state_value ):
                        $state_label = esc_attr( $code ) . ':' . $state_key;
                        echo '<option value="' . $state_label . '"';

                        if ( $state_label == $selected_country_code ) {
                            echo ' selected="selected"';
                        }
                        echo '>' . $country . ' &mdash; ' . ( $state_value ) . '</option>';
                    endforeach;
                else:
                    echo '<option value="' . $code . '"';

                    if ( $code == $selected_country_code ) {
                        echo ' selected="selected"';
                    }
                    echo '>' . $country . '</option>';
                endif;
            }
        }else{
            foreach ( $this->countries as $code => $country ) {
                if ( $states = $this->get_states( $code ) ) :
                    echo '<optgroup label="' . esc_attr( $country ) . '">';
                    foreach ( $states as $state_key => $state_value ):
                        $state_label = esc_attr( $code ) . ':' . $state_key;
                        echo '<option value="' . $state_label . '"';
                        echo '>' . $country . ' &mdash; ' . ( $state_value ) . '</option>';
                    endforeach;
                else:
                    echo '<option value="' . $code . '"';

                    echo '>' . $country . '</option>';
                endif;
            }
        }


        echo '</select>';

        return ob_get_clean();
    }

    /**
     * Return Country name by it's code
     * @return string country name/base location
     */
    public function get_country_name_by_code( $code ) {
        if ( isset( $this->countries[ $code ] ) ) {
            return $this->countries[ $code ];
        }

        $base = get_option('shop_ct_base_country');
        return isset($this->countries[ $base ]) ? $this->countries[ $base ] : null;
    }

    /**
     * For country name $output='name', for code/name pair $output='pair', for code void.
     *
     * @param string $output .
     *
     * @return array
     */
    public function get_base_location( $output = 'code' ) {
        $code = get_option( 'shop_ct_settings_base_location' );

        if ( $output == 'name' ) {
            return apply_filters( 'shop_ct_settings_base_location', $this->countries[ $code ] );
        } elseif ( $output == 'pair' ) {
            return apply_filters( 'shop_ct_settings_base_location', array( $code => $this->countries[ $code ] ) );
        }

        return apply_filters( 'shop_ct_settings_base_location', $code );
    }

    public function is_valid_code($code) {
        return key_exists($code, $this->get_countries());
    }
}
