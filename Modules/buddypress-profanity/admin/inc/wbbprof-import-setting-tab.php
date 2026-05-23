<?php

/**
 * Handles the rendering and saving of general plugin settings for BuddyPress Profanity.
 *
 * @link       https://www.wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Profanity
 * @subpackage Buddypress_Profanity/inc
 */

// Exit if accessed directly.
if (! defined('ABSPATH')) {
	exit;
}

// Retrieve plugin settings.
$wbbprof_settings  = bp_get_option('wbbprof_settings');

// Display a success message if the settings were saved successfully.
if (isset($_GET['msg']) && 'success' === $_GET['msg']) { // phpcs:ignore
?>
	<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">
		<p><strong><?php esc_html_e('Changes saved successfully', 'buddypress-profanity'); ?></strong></p>
	</div>
<?php
}
?>
<div class="wbcom-tab-content">
	<div class="wbcom-wrapper-admin">
		<div class="wbcom-admin-title-section">
			<h3><?php esc_html_e('Add Keywords', 'buddypress-profanity'); ?></h3>
		</div>
		<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
			<form method="post" action="admin.php?action=update_network_options" enctype="multipart/form-data">
				<?php
				settings_fields('buddypress_profanity_general');
				do_settings_sections('buddypress_profanity_general');
				?>
				<div class="form-table buddypress-profanity-admin-table">
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="wbbprof_import_keywords"><?php esc_html_e('Add Keywords', 'buddypress-profanity'); ?></label>
							<p class="description" id="keywords-description">
								<?php esc_html_e('Please add a list of words separated by commas. The plugin will check for these words and mark content containing them as profane.', 'buddypress-profanity'); ?>
							</p>
						</div>
						<div class="wbcom-selectize-control-wrap wbcom-settings-section-options">
							<textarea name="wbbprof_import[keywords]" id="wbbprof_import_keywords" cols="120" rows="10"><?php echo isset($wbbprof_settings['keywords']) ? esc_textarea($wbbprof_settings['keywords']) : ''; ?></textarea>
							<input type="hidden" name="wbbprof_import[import]" value="import" />
						</div>
					</div>
				</div>
				<?php submit_button(); ?>
			</form>
		</div>
	</div>
</div>