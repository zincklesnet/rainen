<?php
/*
 * Plugin Name: rtMedia Shortcode Generator
 * Plugin URI: https://rtmedia.io/products/rtmedia-shortcode-generator/
 * Description: This plugin quickly generate shortcodes for rtMedia gallery & uploader.
 * Version: 1.1.1
 * Text Domain: rtmedia
 * Author: rtCamp
 * Author URI: http://rtcamp.com/?utm_source=dashboard&utm_medium=plugin&utm_campaign=buddypress-media
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if( !defined( 'RTMEDIA_SHORTCODE_GENERATOR_PATH' ) ) {
	/**
	 *  The server file system path to the plugin directory
	 */
	define( 'RTMEDIA_SHORTCODE_GENERATOR_PATH', plugin_dir_path( __FILE__ ) );
}

if( !defined( 'RTMEDIA_SHORTCODE_GENERATOR_URL' ) ) {
	/**
	 * The url to the plugin directory
	 */
	define( 'RTMEDIA_SHORTCODE_GENERATOR_URL', plugin_dir_url( __FILE__ ) );
}

if( !defined( 'RTMEDIA_SHORTCODE_GENERATOR_BASE_NAME' ) ) {
	/**
	 * The base name of the plugin directory
	 */
	define( 'RTMEDIA_SHORTCODE_GENERATOR_BASE_NAME', plugin_basename( __FILE__ ) );
}

if( !defined( 'RTMEDIA_SHORTCODE_GENERATOR_VERSION' ) ) {
    /**
	 * The version of the plugin
	 */
	define( 'RTMEDIA_SHORTCODE_GENERATOR_VERSION', '1.1.1' );
}

if( !defined( 'EDD_RTMEDIA_SHORTCODE_GENERATOR_STORE_URL' ) ) {
	// this is the URL our updater / license checker pings. This should be the URL of the site with EDD installed
	define( 'EDD_RTMEDIA_SHORTCODE_GENERATOR_STORE_URL', 'https://rtmedia.io/' );
}

if( !defined( 'EDD_RTMEDIA_SHORTCODE_GENERATOR_ITEM_NAME' ) ) {
	// the name of your product. This should match the download name in EDD exactly
	define( 'EDD_RTMEDIA_SHORTCODE_GENERATOR_ITEM_NAME', 'rtMedia Shortcode Generator' );
}

// define RTMEDIA_DEBUG to true in wp-config.php to debug updates
if( defined( 'RTMEDIA_DEBUG' ) && RTMEDIA_DEBUG === true ){
    set_site_transient( 'update_plugins', null );
}

/**
 * Auto Loader Function
 *
 * Autoloads classes on instantiation. Used by spl_autoload_register.
 *
 * @param string $class_name The name of the class to autoload
 */
function rtmedia_shortcode_generator_autoloader( $class_name ) {
	$rtlibpath = array(
        'app/' . $class_name . '.php',
        'app/admin/' . $class_name . '.php',
        'app/main/controllers/shortcodes/' . $class_name . '.php'
    );
    
	foreach( $rtlibpath as $path ) {
		$path = RTMEDIA_SHORTCODE_GENERATOR_PATH . $path;
        
		if( file_exists( $path ) ){
			include $path;
            
			break;
		}
	}
}

function rtmedia_shortcode_generator_loader( $class_construct ) {
        /*
	 * do not construct classes or load files if rtMedia Pro is activated
	 * as it might break some functionality
	 */
	if( defined( 'RTMEDIA_PRO_PATH' ) ){
        add_action( 'admin_notices', 'rtmedia_shortcode_generator_pro_active_notice' );
		return $class_construct;
	}
    
    $class_construct[ 'ShortcodeGenerator' ] = false;
    
    return $class_construct;
}

function rtmedia_shortcode_generator_pro_active_notice(){
	?>
		<div class="error">
			<p>
				<strong>rtMedia Shortcode Generator</strong> plugin cannot be activated with rtMedia Pro. Please <strong><a href="https://rtmedia.io/blog/rtmedia-pro-splitting-major-change" target="_blank">read this</a></strong> for more details. You may <strong><a href="https://rtmedia.io/premium-support/" target="_blank">contact support for help</a></strong>.
			</p>
		</div>
	<?php
	// automatic deactivate plugin if rtMedia Pro is active and current user can deactivate plugin.
	if( current_user_can( 'activate_plugins' ) ){
		deactivate_plugins( RTMEDIA_SHORTCODE_GENERATOR_BASE_NAME );
	}
}

/**
 * Register the autoloader function into spl_autoload
 */
spl_autoload_register( 'rtmedia_shortcode_generator_autoloader' );
add_filter( 'rtmedia_class_construct', 'rtmedia_shortcode_generator_loader' );

/*
 * EDD License
 */
include_once RTMEDIA_SHORTCODE_GENERATOR_PATH . 'lib/rt-edd-license/RTEDDLicense.php';

$rtmedia_shortcode_generator_details = array(
	'rt_product_id'                  => 'rtmedia_shortcode_generator',
	'rt_product_name'                => 'rtMedia Shortcode Generator',
	'rt_product_href'                => 'rtmedia-shortcode-generator',
	'rt_license_key'                 => 'edd_rtmedia_shortcode_generator_license_key',
	'rt_license_status'              => 'edd_rtmedia_shortcode_generator_license_status',
	'rt_nonce_field_name'            => 'edd_rtmedia_shortcode_generator_nonce',
	'rt_license_activate_btn_name'   => 'edd_rtmedia_shortcode_generator_license_activate',
	'rt_license_deactivate_btn_name' => 'edd_rtmedia_shortcode_generator_license_deactivate',
	'rt_product_path'                => RTMEDIA_SHORTCODE_GENERATOR_PATH,
	'rt_product_store_url'           => EDD_RTMEDIA_SHORTCODE_GENERATOR_STORE_URL,
	'rt_product_base_name'           => RTMEDIA_SHORTCODE_GENERATOR_BASE_NAME,
	'rt_product_version'             => RTMEDIA_SHORTCODE_GENERATOR_VERSION,
	'rt_item_name'                   => EDD_RTMEDIA_SHORTCODE_GENERATOR_ITEM_NAME,
	'rt_license_hook'                => 'rtmedia_license_tabs',
	'rt_product_text_domain'         => 'rtmedia'
);

new RTEDDLicense( $rtmedia_shortcode_generator_details );

/*
 * One click install/activate rtMedia.
 */
include_once( RTMEDIA_SHORTCODE_GENERATOR_PATH . 'lib/plugin-installer/RTMPluginInstaller.php' );

global $rtm_plugin_installer;

if( empty( $rtm_plugin_installer ) ) {
	$rtm_plugin_installer = new RTMPluginInstaller();
}
