<?php
/**
 * This file is used for rendering and saving plugin welcome settings.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly
}
?>
<div class="wbcom-tab-content">
	<div class="wbcom-welcome-main-wrapper">
		<div class="wbcom-welcome-head">
		<h2 class="wbcom-welcome-title"><?php esc_html_e( 'BuddyPress Member Reviews', 'bp-member-reviews' ); ?></h2>
			<p class="wbcom-welcome-description"><?php esc_html_e( 'This plugin allows only site members to add reviews to the BuddyPress members on the site. If the visitor is not logged in, the visitor can only see the listing of the reviews but cannot review.', 'bp-member-reviews' ); ?></p>
			<p class="wbcom-welcome-description"><?php esc_html_e( 'The review form allows the members to rate the member’s profile out of 5 points with multiple review criteria. You can add multiple criteria for review. You can change the positions of those Criteria. The review form shows on the member’s profile, but you can show the review form on another page by using a shortcode.', 'bp-member-reviews' ); ?></p>
		</div><!-- .wbcom-welcome-head -->

		<div class="wbcom-welcome-content">
			<div class="wbcom-welcome-support-info">
				<h3><?php esc_html_e( 'Help & Support Resources', 'bp-member-reviews' ); ?></h3>
				<p><?php esc_html_e( 'If you need assistance, here are some helpful resources. Our documentation is a great place to start, and our support team is available if you require further help.', 'bp-member-reviews' ); ?></p>

				<div class="wbcom-support-info-wrap">
					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-book"></span><?php esc_html_e( 'Documentation', 'bp-member-reviews' ); ?></h3>
						<p><?php esc_html_e( 'Explore our detailed guide on BuddyPress Member Review to understand all the features and how to make the most of them.', 'bp-member-reviews' ); ?></p>
						<a href="<?php echo esc_url( 'https://docs.wbcomdesigns.com/doc_category/buddypress-member-review/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Read Documentation', 'bp-member-reviews' ); ?></a>
						</div>
					</div>

					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-sos"></span><?php esc_html_e( 'Support Center', 'bp-member-reviews' ); ?></h3>
						<p><?php esc_html_e( 'Our support team is here to assist you with any questions or issues. Feel free to contact us anytime through our support center.', 'bp-member-reviews' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Get Support', 'bp-member-reviews' ); ?></a>
					</div>
					</div>
					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e( 'Share Your Feedback', 'bp-member-reviews' ); ?></h3>
						<p><?php esc_html_e( 'We\'d love to hear about your experience with the plugin. Your feedback and suggestions help us improve future updates.', 'bp-member-reviews' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/submit-review/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Send Feedback', 'bp-member-reviews' ); ?></a>
					</div>
					</div>
				</div>
			</div>
		</div>

	</div><!-- .wbcom-welcome-content -->
</div><!-- .wbcom-welcome-main-wrapper -->
