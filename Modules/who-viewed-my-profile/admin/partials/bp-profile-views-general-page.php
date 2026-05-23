<?php
/**
 * This file is used for rendering and saving plugin general settings.
 *
 * @package bp_stats
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}
/* admin setting on dashboard */
$bp_profile_views_general_options = get_option( 'bp_profile_views_general_options' );
?>
<div class="wbcom-tab-content">
	<div class="wbcom-admin-title-section wbcom-flex">
		<h3 class="wbcom-welcome-title"><?php esc_html_e( 'General Settings', 'bp-profile-views' ); ?></h3>		
		<a href="https://docs.wbcomdesigns.com/doc_category/who-viewed-my-profile-buddypress/" target="_blank" class="wbcom-docslink">Documentation</a>	
	</div><!-- .wbcom-welcome-head -->
	<form method="post" action="options.php">
		<?php
		settings_fields( 'bp_profile_views_general_options' );
		do_settings_sections( 'bp_profile_views_general_options' );
		?>
		<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">		
			<div class="form-table">
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
					<p><label><?php esc_html_e( 'View Count Methods', 'bp-profile-views' ); ?></label></p>					
					<p class="description"><strong><?php esc_html_e( 'By Session:', 'bp-profile-views' ); ?></strong> <?php esc_html_e( 'The count increases by one when a user logs in and visits your profile. No increase for repeat visits in the same session.', 'bp-profile-views' ); ?></p><br/>
					<p class="description"><strong><?php esc_html_e( 'By Referrer: ', 'bp-profile-views' ); ?></strong><?php esc_html_e( 'The count increases each time a user visits your profile from a different page but not when refreshing the profile page.', 'bp-profile-views' ); ?></p>					
					</div>
					<div class="wbcom-settings-section-options">
						<select name="bp_profile_views_general_options[save_count_by]">
							<option value="session" <?php ( isset( $bp_profile_views_general_options['save_count_by'] ) ) ? selected( $bp_profile_views_general_options['save_count_by'], 'session' ) : ''; ?>><?php echo esc_html__( 'By session', 'bp-profile-views' ); ?></option>
							<option value="referer" <?php ( isset( $bp_profile_views_general_options['save_count_by'] ) ) ? selected( $bp_profile_views_general_options['save_count_by'], 'referer' ) : ''; ?>><?php echo esc_html__( 'By referer', 'bp-profile-views' ); ?></option>
						</select>
					</div>
				</div>
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label><?php esc_html_e( 'Choose Chart Style', 'bp-profile-views' ); ?></label>
						<p class="description"><?php esc_html_e( 'Select the chart style for presenting profile visitors data on the Views tab within the BuddyPress Member Profile.', 'bp-profile-views' )?></p>
					</div>
					<div class="wbcom-settings-section-options">
						<select name="bp_profile_views_general_options[chart_style]">
							<option value="line" <?php ( isset( $bp_profile_views_general_options['chart_style'] ) ) ? selected( $bp_profile_views_general_options['chart_style'], 'line' ) : ''; ?>><?php echo esc_html__( 'Line', 'bp-profile-views' ); ?></option>
							<option value="bar" <?php ( isset( $bp_profile_views_general_options['chart_style'] ) ) ? selected( $bp_profile_views_general_options['chart_style'], 'bar' ) : ''; ?>><?php echo esc_html__( 'Bar', 'bp-profile-views' ); ?></option>									
						</select>
					</div>	
				</div>
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label><?php esc_html_e( 'Recent Visitor Per Page', 'bp-profile-views' ); ?></label>
						<p class="description"><?php esc_html_e( 'Displaying the number of visitors in the profile views tab.', 'bp-profile-views' )?></p>
					</div>
					<div class="wbcom-settings-section-options">
						<input type="number" class="bp-profile-member-count" name="bp_profile_views_general_options[view_member_count]" value="<?php echo isset( $bp_profile_views_general_options['view_member_count'] ) ? esc_attr( $bp_profile_views_general_options['view_member_count'] ) : ''; ?>"><br>
					</div>			
				</div>
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label><?php esc_html_e( 'Show Recent Visitors in Member Header?', 'bp-profile-views' ); ?></label>
						<p class="description"><?php esc_html_e( 'Users will see the recent visitors on their profile.', 'bp-profile-views' )?></p>
					</div>
					<div class="wbcom-settings-section-options">
						<input class="bp-profile-show-recent-member" name="bp_profile_views_general_options[show_recent_members]" type="checkbox" value="yes" <?php ( isset( $bp_profile_views_general_options['show_recent_members'] ) ) ? checked( $bp_profile_views_general_options['show_recent_members'], 'yes' ) : ''; ?>>
					</div>			
				</div>
				<?php if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss  ) ){ ?>
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label><?php esc_html_e( 'Show Recent Visitors in Member Profile Cover Area?', 'bp-profile-views' ); ?></label>
						<p class="description"><?php esc_html_e( 'Users will see the recent visitors on their profile cover area. (Note: This setting will work only if "Show recent visitors in the member header" option is enabled.)', 'bp-profile-views' )?></p>
					</div>
					<div class="wbcom-settings-section-options">
						<input class="bp-profile-show-inside-header-meta" name="bp_profile_views_general_options[show_inside_header_meta]" type="checkbox" value="yes" <?php ( isset( $bp_profile_views_general_options['show_inside_header_meta'] ) ) ? checked( $bp_profile_views_general_options['show_inside_header_meta'], 'yes' ) : ''; ?>>
					</div>			
				</div>
				<?php } ?>
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label><?php esc_html_e( 'Number of Visitors to Display in Header', 'bp-profile-views' ); ?></label>
						<p class="description"><?php esc_html_e( 'Set the number of visitors to display in the member profile header (default: 5).', 'bp-profile-views' )?></p>
					</div>
					<div class="wbcom-settings-section-options">
						<input type="number" class="bp-profile-max-display-in-header" name="bp_profile_views_general_options[max_display_in_header]" value="<?php echo isset( $bp_profile_views_general_options['max_display_in_header'] ) && !empty( $bp_profile_views_general_options['max_display_in_header'] ) ? esc_attr( $bp_profile_views_general_options['max_display_in_header'] ) : 5; ?>"><br>
					</div>			
				</div>
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label><?php esc_html_e( 'Visitor Avatar Size in Header', 'bp-profile-views' ); ?></label>
						<p class="description"><?php esc_html_e( 'Set the size of visitor avatars in the header (default: 32px).', 'bp-profile-views' )?></p>
					</div>
					<div class="wbcom-settings-section-options">
						<input type="number" class="bp-profile-default-avatar-size" name="bp_profile_views_general_options[default_avatar_size]" value="<?php echo isset( $bp_profile_views_general_options['default_avatar_size'] ) ? esc_attr( $bp_profile_views_general_options['default_avatar_size'] ) : 32; ?>"><br>
					</div>			
				</div>
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label><?php esc_html_e( 'Enable Logout Visitor Count', 'bp-profile-views' ); ?></label>
						<p class="description"><?php esc_html_e( 'Include logged-out user views from profile statistics. By default, guest views are saved under user ID zero.', 'bp-profile-views' )?></p>
					</div>
					<div class="wbcom-settings-section-options">
						<input class="bp-profile-allow-user-settings" name="bp_profile_views_general_options[exclude_logout_user_count]" type="checkbox" value="yes" <?php ( isset( $bp_profile_views_general_options['exclude_logout_user_count'] ) ) ? checked( $bp_profile_views_general_options['exclude_logout_user_count'], 'yes' ) : ''; ?>>
					</div>			
				</div>
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label><?php esc_html_e( 'Visitor Notification', 'bp-profile-views' ); ?></label>
						<p class="description"><?php esc_html_e( 'Send notifications to users when someone views their profile, providing real-time engagement.', 'bp-profile-views' )?></p>
					</div>
					<div class="wbcom-settings-section-options">
						<input class="bp-profile-allow-user-settings" name="bp_profile_views_general_options[allow_user_notification]" type="checkbox" value="yes" <?php ( isset( $bp_profile_views_general_options['allow_user_notification'] ) ) ? checked( $bp_profile_views_general_options['allow_user_notification'], 'yes' ) : ''; ?>>
					</div>			
				</div>
			</div>
		<?php submit_button(); ?>
	</form>
</div>
