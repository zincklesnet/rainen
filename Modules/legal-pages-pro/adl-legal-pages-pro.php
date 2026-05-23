<?php
/*
Plugin Name: Legal Pages Pro
Plugin URI: https://aazztech.com/product/legal-pages-pro
Description: A very useful plugin to generate legal pages for your websites/ business. Simple, easy and elegant to use.
Version: 1.1.0
Author: ADL plugins
Author URI: https://aazztech.com
License: GPLv2 or later
Text Domain: adl-legal-pages
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2016 ADL plugins.
*/

// Make sure we don't expose any info if called directly
if (!defined('ADL_LP_ALERT_MSG')) define( 'ADL_LP_ALERT_MSG', __( 'You should not access this file directly.!', 'adl-legal-pages' ) );
if ( !defined('ABSPATH') ) die( ADL_LP_ALERT_MSG );
if ( !defined('ADL_LP_BASE') ) { define('ADL_LP_BASE', plugin_basename( __FILE__ )); }

// Load plugin config
require_once 'config.php';
// main plugin class
require_once 'main.php';


if ( class_exists( 'Adl_Legal_Pages' ) ) { // Instantiate the plugin class
    global $ADL_LP;
    $ADL_LP = new Adl_Legal_Pages();
    $ADL_LP->check_req_php_version();
    $ADL_LP->warn_if_unsupported_wp();
    register_activation_hook(__FILE__, array($ADL_LP, 'prepare_plugin'));
    register_uninstall_hook(__FILE__, array('Adl_Legal_Pages', 'remove_plugin_data'));
    $ADL_LP->init();
}
