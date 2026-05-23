<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://wbcomdesigns.com
 * @since      1.0.0
 *
 * @package    Buddypress_Edit_Activities
 * @subpackage Buddypress_Edit_Activities/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wbcom-tab-content">      
<div class="wbcom-faq-adming-setting">
	<div class="wbcom-admin-title-section">
		<h3><?php esc_html_e( 'Have some questions?', 'buddypress-edit-activity' ); ?></h3>
	</div>
	<div class="wbcom-faq-admin-settings-block">
		<div id="wbcom-faq-settings-section" class="wbcom-faq-table">
			<div class="wbcom-faq-section-row">
				<div class="wbcom-faq-admin-row">
					<button class="wbcom-faq-accordion">
						<?php esc_html_e( 'What is BuddyPress Edit Activity?', 'buddypress-edit-activity' ); ?>
					</button>
					<div class="wbcom-faq-panel">
						<p> 
							<?php esc_html_e( 'BuddyPress Edit Activity is a plugin that allows users to edit their activity posts and comments directly from the frontend of your BuddyPress-powered site. It helps maintain content accuracy and improves user engagement.', 'buddypress-edit-activity' ); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="wbcom-faq-section-row">
				<div class="wbcom-faq-admin-row">
					<button class="wbcom-faq-accordion">
						<?php esc_html_e( 'Can users edit their activity posts sitewide?', 'buddypress-edit-activity' ); ?>
					</button>
					<div class="wbcom-faq-panel">
						<p> 
							<?php esc_html_e( 'Yes, users can edit their posts in the Sitewide Activity Stream, Single Group Pages, and User Activity Pages, ensuring their shared content remains relevant and up-to-date.', 'buddypress-edit-activity' ); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="wbcom-faq-section-row">
				<div class="wbcom-faq-admin-row">
					<button class="wbcom-faq-accordion">
						<?php esc_html_e( 'How long can users edit their activity posts?', 'buddypress-edit-activity' ); ?>
					</button>
					<div class="wbcom-faq-panel">
						<p> 
							<?php esc_html_e( 'Administrators can configure the editing duration with options such as Forever, 30 Days, 7 Days, 1 Day, 1 Hour, or 10 minutes, allowing flexible control over post modifications.', 'buddypress-edit-activity' ); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="wbcom-faq-section-row">
				<div class="wbcom-faq-admin-row">
					<button class="wbcom-faq-accordion">
						<?php esc_html_e( 'Can users edit activity comments?', 'buddypress-edit-activity' ); ?>
					</button>
					<div class="wbcom-faq-panel">
						<p> 
							<?php esc_html_e( 'Yes, users can edit their own comments on activity posts, just like they can edit the posts themselves, ensuring accuracy in conversations and community discussions.', 'buddypress-edit-activity' ); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="wbcom-faq-section-row">
				<div class="wbcom-faq-admin-row">
					<button class="wbcom-faq-accordion">
						<?php esc_html_e( 'Does the plugin work with BuddyPress Groups?', 'buddypress-edit-activity' ); ?>
					</button>
					<div class="wbcom-faq-panel">
						<p> 
							<?php esc_html_e( 'Yes, BuddyPress Edit Activity allows users to edit their posts within BuddyPress Groups, enabling better collaboration and accurate discussions within group activity feeds.', 'buddypress-edit-activity' ); ?>
						</p>
					</div>
				</div>
			</div>


			<div class="wbcom-faq-section-row">
				<div class="wbcom-faq-admin-row">
					<button class="wbcom-faq-accordion">
						<?php esc_html_e( 'Does this plugin support other BuddyPress add-ons?', 'buddypress-edit-activity' ); ?>
					</button>
					<div class="wbcom-faq-panel">
						<p> 
							<?php esc_html_e( 'Yes, BuddyPress Edit Activity seamlessly integrates with BuddyPress Check-ins, BuddyPress Quotes, BuddyPress Status, BuddyPress Giphy, and BuddyPress Polls, allowing users to modify check-ins, quotes, status updates, GIFs, and poll details after posting.', 'buddypress-edit-activity' ); ?>
						</p>
					</div>
				</div>
			</div>

			<div class="wbcom-faq-section-row">
				<div class="wbcom-faq-admin-row">
					<button class="wbcom-faq-accordion">
						<?php esc_html_e( 'How do users edit an activity post?', 'buddypress-edit-activity' ); ?>
					</button>
					<div class="wbcom-faq-panel">
						<p> 
							<?php esc_html_e( 'Users can locate their post in the activity stream or user profile, click the Edit button next to the post, make the necessary changes, and click Update Activity to save the edits.', 'buddypress-edit-activity' ); ?>
						</p>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
</div>