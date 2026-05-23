<?php
/**
 * Buddypress activity bump general tab content.
 *
 * @package bp-activity-bump
 * @subpackage bp-activity-bump\admin\tab-templates
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/* admin setting on dashboard */
$bp_bump_genral_setting = get_option( 'bp_bump_admin_general_options' );
?>
<div class="wbcom-tab-content">
	<div class="wbcom-admin-title-section">
		<h3><?php esc_html_e( 'General Settings', 'bp-activity-bump' ); ?></h3>
	</div>
	<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'bp_bump_admin_general_options' );
			do_settings_sections( 'bp_bump_admin_general_options' );
			?>
			<div class="form-table">
				<div class="wbcom-settings-section-wrap">
					<div class="wbcom-settings-section-options-heading">
						<label for="bpwoo-shop-tab">
							<?php esc_html_e( 'Bump On', 'bp-activity-bump' ); ?>
						</label>
						<p class="description"><?php esc_html_e( 'Enable which action will bump activity.', 'bp-activity-bump' ); ?></p>
					</div>
					<div class="wbcom-settings-section-options">
						<input name="bp_bump_admin_general_options[bp_bump_activity_option]" class="regular-text" type="radio" id="bp-bump-display-liked-activity"  value="favorite-activity"<?php ( isset( $bp_bump_genral_setting['bp_bump_activity_option'] ) ) ? checked( $bp_bump_genral_setting['bp_bump_activity_option'], 'favorite-activity' ) : ''; ?>>
						<label for="bp-bump-display-liked-activity"><?php esc_html_e( 'Like/Favorite', 'bp-activity-bump' ); ?></label>
						<input name="bp_bump_admin_general_options[bp_bump_activity_option]" type="radio" id="bp-bump-display-commented-activity"  value="commented-activity"<?php ( isset( $bp_bump_genral_setting['bp_bump_activity_option'] ) ) ? checked( $bp_bump_genral_setting['bp_bump_activity_option'], 'commented-activity' ) : ''; ?>>
						<label for="bp-bump-display-commented-activity"><?php esc_html_e( 'New Comment', 'bp-activity-bump' ); ?></label>
						<input name="bp_bump_admin_general_options[bp_bump_activity_option]" type="radio" id="bp-bump-display-both-activities"  value="both-activity"<?php ( isset( $bp_bump_genral_setting['bp_bump_activity_option'] ) ) ? checked( $bp_bump_genral_setting['bp_bump_activity_option'], 'both-activity' ) : ''; ?>>				
						<label for="bp-bump-display-both-activities"><?php esc_html_e( 'Both', 'bp-activity-bump' ); ?></label>		
					</div>
				</div>
			</div>
			<?php submit_button(); ?>
		</form>
	</div>
</div>
