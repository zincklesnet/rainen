<?php
/**
 * This file is called for general settings section at admin settings.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Newsfeed
 * @subpackage Buddypress_Newsfeed/inc
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( is_multisite() && is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
	$bnews_general_settings = get_site_option( 'bnews_general_settings' );
} else {
	$bnews_general_settings = get_option( 'bnews_general_settings' );
}
$act_list = bnews_get_act_list();
?>
<div class="wbcom-tab-content">
	<div class="wbcom-admin-title-section">
		<h3 class="wbcom-welcome-title"><?php esc_html_e( 'BuddyPress Newsfeed', 'buddypress-newsfeed' ); ?></h3>			
	</div><!-- .wbcom-welcome-head -->
	<form method="post" action="options.php" class="bnews-gen-form">
		<?php
		settings_fields( 'bnews_general_settings_section' );
		do_settings_sections( 'bnews_general_settings_section' );
		?>
		<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">		
			<div class="form-table">
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label for="blogname"><?php esc_html_e( 'Disable for Activity-Specific Tabs', 'buddypress-newsfeed' ); ?></label>
						<p class="description"><?php esc_html_e( 'Turn off this option to organize and display each activity in its respective tab for a more categorized layout.', 'buddypress-newsfeed' ); ?></p>
					</div>
						<div class="wbcom-settings-section-options">
						<label class="wb-switch">
							<input name='bnews_general_settings[disable_all]' type='checkbox' class="regular-text blpro-disp-resp-tr" value="yes" <?php ( isset( $bnews_general_settings['disable_all'] ) ) ? checked( $bnews_general_settings['disable_all'], 'yes' ) : ''; ?>/>
							<div class="wb-slider wb-round"></div>
						</label>
						</div>	
					</div>	
				</div>	
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label for="blogname"><?php esc_html_e( 'Default Tab Selection', 'buddypress-newsfeed' ); ?></label>
						<p class="description"><?php esc_html_e( 'Choose which tab should be displayed first by default.', 'buddypress-newsfeed' ); ?></p>
					</div>
					<div class="wbcom-settings-section-options bnews-general-settings-wrap">
						<label class="blpro-label-padding">
							<input name="bnews_general_settings[first_tab]" value="personal" type="radio" <?php ( isset( $bnews_general_settings['first_tab'] ) ) ? checked( $bnews_general_settings['first_tab'], 'personal' ) : ''; ?>>
							<span class="blpro-span-text"><?php esc_html_e( 'Personal', 'buddypress-newsfeed' ); ?></span>
						</label>
						<label class="blpro-label-padding">
							<input name="bnews_general_settings[first_tab]" value="newsfeed" type="radio" <?php ( isset( $bnews_general_settings['first_tab'] ) ) ? checked( $bnews_general_settings['first_tab'], 'newsfeed' ) : ''; ?>>
							<span class="blpro-span-text"><?php esc_html_e( 'Newsfeed', 'buddypress-newsfeed' ); ?></span>
						</label>
					</div>	
				</div>
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label for="blogname"><?php esc_html_e( 'Enable Activity Post Form on Newsfeed', 'buddypress-newsfeed' ); ?></label>
						<p class="description"><?php esc_html_e( 'Display the activity post form directly on the Newsfeed tab.', 'buddypress-newsfeed' ); ?></p>
					</div>
					<div class="wbcom-settings-section-options">
						<label class="wb-switch">
							<input name='bnews_general_settings[post_form_enable]' type='checkbox' class="regular-text blpro-disp-resp-tr" value="yes" <?php ( isset( $bnews_general_settings['post_form_enable'] ) ) ? checked( $bnews_general_settings['post_form_enable'], 'yes' ) : ''; ?>/>
							<div class="wb-slider wb-round"></div>
						</label>
					</div>			
				</div>
				<?php if ( ! isset( buddypress()->buddyboss ) ) { ?>
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="blogname"><?php esc_html_e( 'Enable Relevant Activity', 'buddypress-newsfeed' ); ?></label>
							<p class="description"><?php esc_html_e( 'Show activity updates that are most relevant to the user based on their connections and interactions.', 'buddypress-newsfeed' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<label class="wb-switch">
								<input name='bnews_general_settings[_bp_enable_relevant_feed]' type='checkbox' class="regular-text blpro-disp-resp-tr" value="yes" <?php ( isset( $bnews_general_settings['_bp_enable_relevant_feed'] ) ) ? checked( $bnews_general_settings['_bp_enable_relevant_feed'], 'yes' ) : ''; ?>/>
								<div class="wb-slider wb-round"></div>
							</label>
						</div>			
					</div>
				<?php } ?>
			</div>
		<?php submit_button(); ?>
	</form>
</div>

