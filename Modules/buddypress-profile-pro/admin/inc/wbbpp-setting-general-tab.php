<?php
/**
 *
 * This file is called for general settings section at admin settings.
 *
 * @package Buddypress_Profile_Pro
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
	$wbbpp_general_settings = get_site_option( 'wbbpp_general_settings' );
} else {
	$wbbpp_general_settings = get_option( 'wbbpp_general_settings' );
}

$api_status = get_option( 'wbbpp_googlemapapi_status' );
$verify_api = '';
if( 'verified' == $api_status ){
	$verify_api = "verify-api";
}
$map_api_key 		= isset( $wbbpp_general_settings['place_api'] ) ? esc_attr( $wbbpp_general_settings['place_api'] ) : '';
$default_language 	= isset( $wbbpp_general_settings['default_language'] ) ? esc_attr( $wbbpp_general_settings['default_language'] ) : 'en';

$verify_btn_style = 'display: none;';
if ( $map_api_key != '' ) {
	$verify_btn_style = '';
}
?>
<div class="wbcom-tab-content">
<div class="wbcom-wrapper-admin">
<div class="wbcom-admin-title-section wbcom-flex">
	<h3 class="wbcom-welcome-title"><?php esc_html_e( 'General Setting', 'buddypress-profile-pro' ); ?></h3>
	<a href="<?php echo esc_url( 'https://docs.wbcomdesigns.com/doc_category/buddypress-profile-pro/' ); ?>" class="wbcom-docslink" target="_blank"><?php esc_html_e( 'Documentation', 'buddypress-profile-pro' ); ?></a>
</div>

<div class="wbcom-wrapper-section">
<div class="wbcom-admin-option-wrap">	
<form method="post" action="options.php">
	<?php
	settings_fields( 'wbbpp_general_settings_section' );
	do_settings_sections( 'wbbpp_general_settings_section' );
	?>
	<div class="container">
		<div class="wbcom-settings-section-wrap">
		<p><i class="fa fa-question-circle"></i>
			<span class="bprm-tab-description">
				<?php esc_html_e( 'To use the Google Autocomplete Field Type in the Profile field, you must register your app project on the Google API Console and get a Google API key which you can add here.', 'buddypress-profile-pro' ); ?>
			</span>
		</p>
		<p>
			<strong><?php esc_html_e( 'Step 1: ', 'buddypress-profile-pro' ); ?></strong>
			<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="blank"><?php esc_html_e( 'Get an API Key from the Google API Console.', 'buddypress-profile-pro' ); ?></a>
			<span><?php esc_html_e( '( Click the GET A KEY in the link provided, which guides you through the process of registering a project in the Google API Console. )', 'buddypress-profile-pro' ); ?></span>
		</p>
		<p>
			<strong><?php esc_html_e( 'Step 2:', 'buddypress-profile-pro' ); ?></strong>
			<span><?php esc_html_e( 'Add the API key in the below field.', 'buddypress-profile-pro' ); ?></span>
		</p>
		</div>
		<div class="form-table">
			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label><?php esc_html_e( 'Default Language', 'buddypress-profile-pro' ); ?></label>
					<p class="description"><?php esc_html_e( 'Set the default language to be used with the API providers.', 'buddypress-profile-pro' ); ?></p>
				</div>
				<div class="wbcom-settings-section-options">
					<input class="regular-text" type="text" name="wbbpp_general_settings[default_language]" value="<?php echo esc_attr( $default_language ); ?>" />	
				</div>
			</div>
			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="blogname"><?php esc_html_e( 'Google API Key', 'buddypress-profile-pro' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'This API Key will help fetch the google places while setting place for work and education.', 'buddypress-profile-pro' ); ?>
					<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="blank"><?php esc_html_e( 'Get google API Key.', 'buddypress-profile-pro' ); ?></a>
					</p>
				</div>
				<div class="wbcom-settings-section-options google-api-key-settings">	
					<input name='wbbpp_general_settings[place_api]' type='text' class="regular-text" value='<?php echo isset( $wbbpp_general_settings['place_api'] ) ? esc_attr( $wbbpp_general_settings['place_api'] ) : ''; ?>' placeholder="<?php esc_html_e( 'API Key', 'buddypress-profile-pro' ); ?>" id="wbbpp_map_api_key"/>
					
					<button type="button" class="button button-secondary <?php echo esc_attr( $verify_api ); ?>" style="<?php echo esc_attr( $verify_btn_style ); ?>" id="wbbpp-verify-apikey">
						<?php esc_html_e( 'Verify', 'buddypress-profile-pro' ); ?>
						<?php if( 'verified' == $api_status ){ ?>
						<i class="fa fa-check"></i>
						<?php } ?>
					</button>
				</div>
			</div>
			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="blogname"><?php esc_html_e( 'Enable profile fields visibility settings', 'buddypress-profile-pro' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'Enable this option if you want users to change their profile fields visibility setting.', 'buddypress-profile-pro' ); ?>
					</p>
				</div>
					<div class="wbcom-settings-section-options">
						<input type="checkbox" name="wbbpp_general_settings[fld_visib_stngs]" value="yes"
				<?php
				if ( isset( $wbbpp_general_settings['fld_visib_stngs'] ) ) {
					checked( $wbbpp_general_settings['fld_visib_stngs'], 'yes' );}
				?>
				>					
				</div>
			</div>
			<div class="wbcom-settings-section-wrap">
				<div class="wbcom-settings-section-options-heading">
					<label for="blogname"><?php esc_html_e( 'Change tab name', 'buddypress-profile-pro' ); ?></label>
					<p class="description" id="tagline-description"><?php esc_html_e( 'You can change tab name of pro fields. This will change tab name as well as tab slug.', 'buddypress-profile-pro' ); ?>
					</p>
				</div>
					<div class="wbcom-settings-section-options">
						<input type="text" class="regular-text" name="wbbpp_general_settings[fld_tab_name]" value="<?php echo isset( $wbbpp_general_settings['fld_tab_name'] ) ? esc_attr( $wbbpp_general_settings['fld_tab_name'] ) : 'Extended Fields'; ?>">					
				</div>
			</div>
		</div>
	</div>
	<?php submit_button(); ?>
</form>
</div>
</div>
</div> <!-- closing of div class wbcom-tab-content -->
