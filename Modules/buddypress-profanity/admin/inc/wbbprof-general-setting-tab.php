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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Get plugin settings.
$wbbprof_settings = bp_get_option( 'wbbprof_settings' );

// Content to be filtered and word rendering symbols.
$content_to_filter = content_to_filter_array();
$rendering_symbols = word_rendering_symbols();
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
			<h3><?php esc_html_e( 'General Profanity Settings', 'buddypress-profanity' ); ?></h3>
		</div>
		<div class="wbcom-admin-option-wrap wbcom-admin-option-wrap-view">
			<form method="post" action="admin.php?action=update_network_options">
				<?php
				settings_fields( 'buddypress_profanity_general' );
				do_settings_sections( 'buddypress_profanity_general' );
				?>
				<div class="form-table buddypress-profanity-admin-table">
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="keywords"><?php esc_html_e( 'Blocked Keywords', 'buddypress-profanity' ); ?></label>
							<p class="description" id="keywords-description">
								<?php esc_html_e( 'Enter specific words or phrases you want to filter out from your community content.', 'buddypress-profanity' ); ?>
							</p>
							<p>
								<a href="javascript:void(0)" class="button" id="wbbprof_to_reset"><?php esc_html_e( 'Reset to Default', 'buddypress-profanity' ); ?></a>
							</p>
						</div>
						<div class="wbcom-selectize-control-wrap wbcom-settings-section-options">
							<input name="wbbprof_settings[keywords]" type="text" class="regular-text wbbprof-keywords-text" value="<?php echo isset( $wbbprof_settings['keywords'] ) ? esc_attr( $wbbprof_settings['keywords'] ) : ''; ?>" placeholder="<?php esc_html_e( 'Keywords to remove', 'buddypress-profanity' ); ?>" />
						</div>
					</div>
				</div>
				<div class="form-table">
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label><?php esc_html_e( 'Filter Scope', 'buddypress-profanity' ); ?></label>
							<p class="description" id="keywords-description">
								<?php esc_html_e( 'Select which types of content (posts, comments, messages, etc.) will be subject to filtering.', 'buddypress-profanity' ); ?>
							</p>
						</div>
						<div class="wbcom-settings-section-options">
							<ul class="wbcom-settings-member-retraction wbcom-settings-section-options-flex">
								<?php
								foreach ( $content_to_filter as $key => $value ) {
									$checked = isset( $wbbprof_settings['filter_contents'] ) && in_array( $key, $wbbprof_settings['filter_contents'], true ) ? 'checked' : '';
									?>
									<li>
										<label class="wb-switch">
											<input name="wbbprof_settings[filter_contents][]" value="<?php echo esc_attr( $key ); ?>" type="checkbox" <?php echo esc_attr( $checked ); ?>>
											<div class="wb-slider wb-round"></div>
										</label>
										<label class="wbbprof-span-text wbbprof-chkbox-txt" for="bp_create_post_<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></label>
									</li>
									<?php
								}
								?>
							</ul>
						</div>
					</div>
				</div>
				<div class="form-table">
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label><?php esc_html_e( 'Word Rendering', 'buddypress-profanity' ); ?></label>
							<p class="description" id="keywords-description">
								<?php esc_html_e( 'Choose how blocked words appear to users.', 'buddypress-profanity' ); ?>
							</p>
						</div>
						<div class="wbcom-settings-section-options word-rendering-wrapper">
							<fieldset>
								<legend class="screen-reader-text"><span><?php esc_html_e( 'Word Rendering', 'buddypress-profanity' ); ?></span></legend>
								<label>
									<input name="wbbprof_settings[word_render]" value="first" type="radio" <?php isset( $wbbprof_settings['word_render'] ) ? checked( $wbbprof_settings['word_render'], 'first' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'First letter retained', 'buddypress-profanity' ); ?></span>
									<code>[blog => b***]</code>
								</label>
								<br>
								<label>
									<input name="wbbprof_settings[word_render]" value="last" type="radio" <?php isset( $wbbprof_settings['word_render'] ) ? checked( $wbbprof_settings['word_render'], 'last' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'Last letter retained', 'buddypress-profanity' ); ?></span>
									<code>[blog => ***g]</code>
								</label>
								<br>
								<label>
									<input name="wbbprof_settings[word_render]" value="first_last" type="radio" <?php isset( $wbbprof_settings['word_render'] ) ? checked( $wbbprof_settings['word_render'], 'first_last' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'First and Last letter retained', 'buddypress-profanity' ); ?></span>
									<code>[blog => b**g]</code>
								</label>
								<br>
								<label>
									<input name="wbbprof_settings[word_render]" value="all" type="radio" <?php isset( $wbbprof_settings['word_render'] ) ? checked( $wbbprof_settings['word_render'], 'all' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'All letters removed', 'buddypress-profanity' ); ?></span>
									<code>[blog => ****]</code>
								</label>
								<br>
							</fieldset>
						</div>
					</div>
				</div>
				<div class="form-table">
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label for="character"><?php esc_html_e( 'Filter Character', 'buddypress-profanity' ); ?></label>
							<p class="description" id="keywords-description">
								<?php esc_html_e( 'Select the symbol that will replace filtered content.', 'buddypress-profanity' ); ?>
							</p>
						</div>
						<div class="wbcom-settings-section-options">
							<select name="wbbprof_settings[character]">
								<?php
								foreach ( $rendering_symbols as $key => $value ) {
									$selected = isset( $wbbprof_settings['character'] ) && $wbbprof_settings['character'] === $key ? 'selected' : '';
									echo "<option value='" . esc_attr( $key ) . "' " . esc_attr( $selected ) . '>' . esc_html( $value ) . '</option>';
								}
								?>
							</select>
						</div>
					</div>
				</div>
				<div class="form-table">
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label><?php esc_html_e( 'Case Matching', 'buddypress-profanity' ); ?></label>
							<p class="description"><?php esc_html_e( 'The "Case Matching" setting offers two options: Case Sensitive and Case Insensitive. Case Sensitive filters keywords with strict case matching. Case Insensitive captures more words while filtering, which is recommended.', 'buddypress-profanity' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<fieldset>
								<legend class="screen-reader-text"><span><?php esc_html_e( 'Case Matching', 'buddypress-profanity' ); ?></span></legend>
								<label>
									<input name="wbbprof_settings[case]" value="case" type="radio" <?php isset( $wbbprof_settings['case'] ) ? checked( $wbbprof_settings['case'], 'case' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'Case Sensitive', 'buddypress-profanity' ); ?></span>
								</label>
								<br>
								<label>
									<input name="wbbprof_settings[case]" value="incase" type="radio" <?php isset( $wbbprof_settings['case'] ) ? checked( $wbbprof_settings['case'], 'incase' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'Case Insensitive (recommended)', 'buddypress-profanity' ); ?></span>
								</label>
								<br>
							</fieldset>
						</div>
					</div>
				</div>
				<div class="form-table">
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label><?php esc_html_e( 'Strict Filtering', 'buddypress-profanity' ); ?></label>
							<p class="description"><?php esc_html_e( 'When Strict Filtering is ON, embedded keywords are filtered.', 'buddypress-profanity' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options word-rendering-wrapper">
							<fieldset>
								<legend class="screen-reader-text"><span><?php esc_html_e( 'Strict Filtering', 'buddypress-profanity' ); ?></span></legend>
								<label>
									<input name="wbbprof_settings[strict_filter]" value="off" type="radio" <?php isset( $wbbprof_settings['strict_filter'] ) ? checked( $wbbprof_settings['strict_filter'], 'off' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'Strict Filtering ON (recommended)', 'buddypress-profanity' ); ?></span>
									<code>[e.g., "ass" becomes "p***able"]</code>
								</label>
								<br>
								<label>
									<input name="wbbprof_settings[strict_filter]" value="on" type="radio" <?php isset( $wbbprof_settings['strict_filter'] ) ? checked( $wbbprof_settings['strict_filter'], 'on' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'Strict Filtering OFF', 'buddypress-profanity' ); ?></span>
									<code>[e.g., "ass" becomes "passable"] </code>
								</label>
								<br>
							</fieldset>
						</div>
					</div>
				</div>
				<div class="form-table">
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label><?php esc_html_e( 'Mask Email Addresses', 'buddypress-profanity' ); ?></label>
							<p class="description"><?php esc_html_e( 'Automatically detect and mask email addresses to protect user privacy and prevent data harvesting.', 'buddypress-profanity' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<fieldset>
								<legend class="screen-reader-text"><span><?php esc_html_e( 'Mask Email Addresses', 'buddypress-profanity' ); ?></span></legend>
								<label>
									<input name="wbbprof_settings[mask_emails]" value="on" type="radio" <?php isset( $wbbprof_settings['mask_emails'] ) ? checked( $wbbprof_settings['mask_emails'], 'on' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'Yes', 'buddypress-profanity' ); ?></span>
								</label>
								<br>
								<label>
									<input name="wbbprof_settings[mask_emails]" value="off" type="radio" <?php isset( $wbbprof_settings['mask_emails'] ) ? checked( $wbbprof_settings['mask_emails'], 'off' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'No', 'buddypress-profanity' ); ?></span>
								</label>
								<br>
							</fieldset>
						</div>
					</div>
				</div>

				<div class="form-table">
					<div class="wbcom-settings-section-wrap">
						<div class="wbcom-settings-section-options-heading">
							<label><?php esc_html_e( 'Mask Phone Numbers', 'buddypress-profanity' ); ?></label>
							<p class="description"><?php esc_html_e( 'Automatically detect and mask phone numbers to protect user privacy and prevent data harvesting.', 'buddypress-profanity' ); ?></p>
						</div>
						<div class="wbcom-settings-section-options">
							<fieldset>
								<legend class="screen-reader-text"><span><?php esc_html_e( 'Mask Phone Numbers', 'buddypress-profanity' ); ?></span></legend>
								<label>
									<input name="wbbprof_settings[mask_phones]" value="on" type="radio" <?php isset( $wbbprof_settings['mask_phones'] ) ? checked( $wbbprof_settings['mask_phones'], 'on' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'Yes', 'buddypress-profanity' ); ?></span>
								</label>
								<br>
								<label>
									<input name="wbbprof_settings[mask_phones]" value="off" type="radio" <?php isset( $wbbprof_settings['mask_phones'] ) ? checked( $wbbprof_settings['mask_phones'], 'off' ) : ''; ?>>
									<span class="wbbprof-span-text"><?php esc_html_e( 'No', 'buddypress-profanity' ); ?></span>
								</label>
								<br>
							</fieldset>
						</div>
					</div>
				</div>
				<?php submit_button(); ?>
			</form>
		</div>
	</div>
</div>
