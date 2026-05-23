<?php
/**
 * Zombify Plugin Uninstall
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !function_exists("zombify_uninstall_func") ) {

    /**
     * Zombify activation function
     */
    function zombify_uninstall_func()
    {

    }

    add_action("zombify_uninstall", "zombify_uninstall_func", 10, 0);

}