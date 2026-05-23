<?php

/**
 * Template for the FAQs support section in BuddyPress Profanity plugin.
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
?>
<div class="wbcom-tab-content">
	<div class="wbcom-faq-admin-setting">
		<div class="wbcom-admin-title-section">
			<h3><?php esc_html_e('Frequently Asked Questions', 'buddypress-profanity'); ?></h3>
		</div>
		<div class="wbcom-faq-admin-settings-block">
			<div id="wbcom-faq-settings-section" class="wbcom-faq-table">

				<div class="wbcom-faq-section-row">
					<div class="wbcom-faq-admin-row">
						<button class="wbcom-faq-accordion">
							<?php esc_html_e('Does this plugin require BuddyPress?', 'buddypress-profanity'); ?>
						</button>
						<div class="wbcom-faq-panel">
							<p><?php esc_html_e('Yes, it requires you to have BuddyPress installed and activated.', 'buddypress-profanity'); ?></p>
						</div>
					</div>
				</div>

				<div class="wbcom-faq-section-row">
					<div class="wbcom-faq-admin-row">
						<button class="wbcom-faq-accordion">
							<?php esc_html_e('Does this plugin filter multiple keywords?', 'buddypress-profanity'); ?>
						</button>
						<div class="wbcom-faq-panel">
							<p><?php esc_html_e('Yes, multiple keywords can be set to filter using the "Keywords to remove" setting under the General tab.', 'buddypress-profanity'); ?></p>
						</div>
					</div>
				</div>

				<div class="wbcom-faq-section-row">
					<div class="wbcom-faq-admin-row">
						<button class="wbcom-faq-accordion">
							<?php esc_html_e('How do I specify a custom character to replace filtered keywords?', 'buddypress-profanity'); ?>
						</button>
						<div class="wbcom-faq-panel">
							<p><?php esc_html_e('This can be achieved by using the filters provided in the plugin. To replace keywords with a custom character, e.g., @, add the following code in your theme\'s functions.php file or wherever appropriate:', 'buddypress-profanity'); ?></p>
							<pre>
add_filter( 'wbbprof_word_rendering_symbols', 'custom_wbbprof_word_rendering_symbols', 10, 1 );
function custom_wbbprof_word_rendering_symbols( $rendering_symbols ) {
	$rendering_symbols['at_the_rate'] = '[ @] At the rate';
	return $rendering_symbols;
}

add_filter( 'wbbprof_custom_character', 'custom_wbbprof_custom_character', 10, 1 );
function custom_wbbprof_custom_character( $symbol ) {
	$symbol = '@';
	return $symbol;
}
							</pre>
							<p><?php esc_html_e('After adding this code, a new option will appear under "Filter Character". Select the newly added option and save the settings.', 'buddypress-profanity'); ?></p>
						</div>
					</div>
				</div>

				<div class="wbcom-faq-section-row">
					<div class="wbcom-faq-admin-row">
						<button class="wbcom-faq-accordion">
							<?php esc_html_e('Does this change the content in the BuddyPress database?', 'buddypress-profanity'); ?>
						</button>
						<div class="wbcom-faq-panel">
							<p><?php esc_html_e('No, the plugin only filters content displayed on the screen. The BuddyPress database remains unaffected by the plugin changes.', 'buddypress-profanity'); ?></p>
						</div>
					</div>
				</div>

				<div class="wbcom-faq-section-row">
					<div class="wbcom-faq-admin-row">
						<button class="wbcom-faq-accordion">
							<?php esc_html_e('How is the Case Matching setting useful?', 'buddypress-profanity'); ?>
						</button>
						<div class="wbcom-faq-panel">
							<p><?php esc_html_e('The "Case Matching" setting offers two options: Case Sensitive and Case Insensitive. Case Sensitive filters keywords with strict case matching, which is not recommended. Case Insensitive captures more words while filtering.', 'buddypress-profanity'); ?></p>
							<p><?php esc_html_e('We recommend using Case Insensitive matching.', 'buddypress-profanity'); ?></p>
						</div>
					</div>
				</div>

				<div class="wbcom-faq-section-row">
					<div class="wbcom-faq-admin-row">
						<button class="wbcom-faq-accordion">
							<?php esc_html_e('How is the Strict Filtering setting useful?', 'buddypress-profanity'); ?>
						</button>
						<div class="wbcom-faq-panel">
							<p><?php esc_html_e('The "Strict Filtering" setting, when enabled, filters embedded keywords.', 'buddypress-profanity'); ?></p>
							<p><?php esc_html_e('We recommend keeping Strict Filtering enabled.', 'buddypress-profanity'); ?></p>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>