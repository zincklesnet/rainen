<?php
/**
 * BuddyPress Member Review support tab.
 *
 * @package BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="bupr-adming-setting">
	<div class="bupr-tab-header">
		<h3><?php esc_html_e( 'FAQ(s) ', 'bp-member-reviews' ); ?></h3>
		<input type="hidden" class="bupr-tab-active" value="support"/>
	</div>

	<div class="bupr-admin-settings-block">
		<div id="bupr-settings-tbl" class="bupr-table">
			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-12">
				   <button class="bupr-accordion">
					<?php esc_html_e( 'How can we submit a review to a member profile using this plugin?', 'bp-member-reviews' ); ?>
					</button>
					<div class="panel">
						<p>
							<?php esc_html_e( 'When visiting the "/members" section on the site, go to the single profile view page. There you can see a menu named "Reviews" which will allow you to add a profile review only if you are a member.', 'bp-member-reviews' ); ?>
						</p>
					</div>
				</div>
			</div>
			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-12">
					<button class="bupr-accordion">
						<?php esc_html_e( 'How can we add more rating criteria for the review form?', 'bp-member-reviews' ); ?>
					</button>
					<div class="panel">
						<p>
							<?php esc_html_e( 'Just go to "Dashboard->Review->BP member review settings page" and click the "Add Criteria" button to add more fields. Then click the "Save Settings" button to update the review settings.', 'bp-member-reviews' ); ?>
						</p>
					</div>
				</div>
			</div>
			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-12">
					<button class="bupr-accordion">
						<?php esc_html_e( 'What is the Top Members widget and how do I use it?', 'bp-member-reviews' ); ?>
					</button>
					<div class="panel">
						<p>
							<?php esc_html_e( 'The Members Review widget displays a list of members on the site front-end. When you successfully activate the BP Member Profile Review plugin, you can see the Members Review widget in the widget section.', 'bp-member-reviews' ); ?>
						</p>
					</div>
				</div>
			</div>
			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-12">
					<button class="bupr-accordion">
						<?php esc_html_e( 'Can I use the review form on any other page?', 'bp-member-reviews' ); ?>
					</button>
					<div class="panel">
						<p><?php esc_html_e( 'Yes, you can use the review form on another page. Just copy the shortcode from the review settings page and paste it on the other page.', 'bp-member-reviews' ); ?></p>
					</div>
				</div>
			</div>
			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-12">
					<button class="bupr-accordion">
						<?php esc_html_e( 'What should I do if I am unable to publish the shortcode page after updating to WordPress 5.0?', 'bp-member-reviews' ); ?>
					</button>
					<div class="panel">
						<p><?php esc_html_e( 'Install the Classic Editor plugin. Then, in the right sidebar, you’ll see a link for “use classic editor”.', 'bp-member-reviews' ); ?></p>
					</div>
				</div>
			</div>
			<div class="bupr-admin-row border">
				<div class="bupr-admin-col-12">
					<button class="bupr-accordion">
						<?php esc_html_e( 'Where do I ask for support?', 'bp-member-reviews' ); ?>
					</button>
					<div class="panel">
						<p><?php
						 /* translators: %s: */
						echo sprintf( esc_html__( 'Please visit %1$s for any query related to the plugin and BuddyPress.', 'bp-member-reviews' ), '<a href="http://wbcomdesigns.com/contact" rel="nofollow" target="_blank"> Wbcom Designs </a>' ); ?>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
