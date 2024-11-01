<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Allows log files to be written to for debugging purposes
 *
 * @class 		Shop_CT_Logger
 */
class Shop_CT_Logger {

    /**
     * Stores open file _handles.
     *
     * @var array
     * @access private
     */
    private $_handles;

    /**
     * Constructor for the logger.
     */
    public function __construct() {
        $this->_handles = array();
    }


    /**
     * Destructor.
     */
    public function __destruct() {
        foreach ( $this->_handles as $handle ) {
            @fclose( $handle );
        }
    }


    /**
     * Open log file for writing.
     *
     * @access private
     * @param mixed $handle
     * @return bool success
     */
    private function open( $handle ) {
        if ( isset( $this->_handles[ $handle ] ) ) {
            return true;
        }

        if ( $this->_handles[ $handle ] = @fopen( self::get_log_file_path( $handle ), 'a' ) ) {
            return true;
        }

        return false;
    }


    /**
     * Add a log entry to chosen file.
     *
     * @param string $handle
     * @param string $message
     */
    public function add( $handle, $message ) {
        if ( $this->open( $handle ) && is_resource( $this->_handles[ $handle ] ) ) {
            $time = date_i18n( 'm-d-Y @ H:i:s -' ); // Grab Time
            @fwrite( $this->_handles[ $handle ], $time . " " . $message . "\n" );
        }

        do_action( 'shop_ct_log_add', $handle, $message );
    }


    /**
     * Clear entries from chosen file.
     *
     * @param mixed $handle
     */
    public function clear( $handle ) {
        if ( $this->open( $handle ) && is_resource( $this->_handles[ $handle ] ) ) {
            @ftruncate( $this->_handles[ $handle ], 0 );
        }

        do_action( 'shop_ct_log_clear', $handle );
    }

    /**
     * @param string $handle
     * @return string
     */
    public static function get_log_file_path( $handle ){
        return trailingslashit( SHOP_CT_LOG_DIR ) . $handle . '-' . sanitize_file_name( wp_hash( $handle ) ) . '.log';
    }

}
