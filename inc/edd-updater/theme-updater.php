<?php
/**
 * Easy Digital Downloads Theme Updater
 *
 * @package EDD Sample Theme
 */
// Includes the files needed for the theme updater
if ( !class_exists( 'EDD_Reign_Theme_Updater_Admin' ) ) {
	include( dirname( __FILE__ ) . '/theme-updater-admin.php' );
}
// Loads the updater classes
$updater = new EDD_Reign_Theme_Updater_Admin(
	// Config settings
	$config = array(
		'remote_api_url' => 'https://wbcomdesigns.com/', // Site where EDD is hosted
		'item_name'      => 'Reign Theme', // Name of theme
		'theme_slug'     => 'reign-theme', // Theme slug
		'version'        => REIGN_THEME_VERSION, // The current version of this theme
		'author'         => 'Wbcom Designs', // The author of this theme
		'download_id'    => '', // Optional, used for generating a license renewal link
		'renew_url'      => '', // Optional, allows for a custom license renewal link
		'beta'           => false, // Optional, set to true to opt into beta versions
	),
	// Strings
	$strings = array(
		'theme-license'             => __( 'Theme License', 'reign' ),
		'enter-key'                 => __( 'Enter your theme license key.', 'reign' ),
		'license-key'               => __( 'License Key', 'reign' ),
		'license-action'            => __( 'License Action', 'reign' ),
		'deactivate-license'        => __( 'Deactivate License', 'reign' ),
		'activate-license'          => __( 'Activate License', 'reign' ),
		'status-unknown'            => __( 'License status is unknown.', 'reign' ),
		'renew'                     => __( 'Renew?', 'reign' ),
		'unlimited'                 => __( 'unlimited', 'reign' ),
		'license-key-is-active'     => __( 'License key is active.', 'reign' ),
		'expires%s'                 => __( 'Expires %s.', 'reign' ),
		'expires-never'             => __( 'Lifetime License.', 'reign' ),
		'%1$s/%2$-sites'            => __( 'You have %1$s / %2$s sites activated.', 'reign' ),
		'license-key-expired-%s'    => __( 'License key expired %s.', 'reign' ),
		'license-key-expired'       => __( 'License key has expired.', 'reign' ),
		'license-keys-do-not-match' => __( 'License keys do not match.', 'reign' ),
		'license-is-inactive'       => __( 'License is inactive.', 'reign' ),
		'license-key-is-disabled'   => __( 'License key is disabled.', 'reign' ),
		'site-is-inactive'          => __( 'Site is inactive.', 'reign' ),
		'license-status-unknown'    => __( 'License status is unknown.', 'reign' ),
		'update-notice'             => __( "Updating this theme will cause you to lose any customizations you have made. Click 'Cancel' to stop or 'OK' to update.", 'reign' ),
		'update-available'          => __('<strong>%1$s %2$s</strong> is available. <a href="%3$s" class="thickbox" title="%4s">Check out what\'s new</a> or <a href="%5$s"%6$s>update now</a>.', 'reign' ),
	)
);
