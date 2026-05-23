<?php
/**
 * This file is used for rendering and saving plugin welcome settings.
 *
 *  @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Newsfeed
 * @subpackage Buddypress_Newsfeed/inc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
	// Exit if accessed directly.
}
?>


<div class="wbcom-tab-content">
	<div class="wbcom-welcome-main-wrapper">
		<div class="wbcom-welcome-head">
		<p class="wbcom-welcome-description"><?php esc_html_e( "As a member, you'll see a unified Newsfeed that merges all your BuddyPress activity including posts from your connections, followed members, groups, forums, mentions, favorites, and personal updates, all in one place.", 'buddypress-newsfeed' ); ?></p>

		<div class="wbcom-welcome-content">
			<div class="wbcom-welcome-support-info">
				<h3><?php esc_html_e( 'Help &amp; Support Resources', 'buddypress-newsfeed' ); ?></h3>
				<p><?php esc_html_e( 'Here are all the resources you may need to get help from us. Documentation is usually the best place to start. Should you require help anytime, our customer care team is available to assist you at the support center.', 'buddypress-newsfeed' ); ?></p>

				<div class="wbcom-support-info-wrap">
					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-book"></span><?php esc_html_e( 'Documentation', 'buddypress-newsfeed' ); ?></h3>
						<p><?php esc_html_e( 'Explore our detailed guide on BuddyPress Newsfeed to understand all the features and how to make the most of them.', 'buddypress-newsfeed' ); ?></p>
						<a href="<?php echo esc_url( 'https://docs.wbcomdesigns.com/doc_category/buddypress-newsfeed/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Read Documentation', 'buddypress-newsfeed' ); ?></a>
						</div>
					</div>

					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-sos"></span><?php esc_html_e( 'Support Center', 'buddypress-newsfeed' ); ?></h3>
						<p><?php esc_html_e( 'Our support team is here to assist you with any questions or issues. Feel free to contact us anytime through our support center.', 'buddypress-newsfeed' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Get Support', 'buddypress-newsfeed' ); ?></a>
					</div>
					</div>
					<div class="wbcom-support-info-widgets">
						<div class="wbcom-support-inner">
						<h3><span class="dashicons dashicons-admin-comments"></span><?php esc_html_e( 'Share Your Feedback', 'buddypress-newsfeed' ); ?></h3>
						<p><?php esc_html_e( 'We’d love to hear about your experience with the plugin. Your feedback and suggestions help us improve future updates.', 'buddypress-newsfeed' ); ?></p>
						<a href="<?php echo esc_url( 'https://wbcomdesigns.com/submit-review/' ); ?>" class="button button-primary button-welcome-support" target="_blank"><?php esc_html_e( 'Send Feedback', 'buddypress-newsfeed' ); ?></a>
					</div>
					</div>
				</div>
			</div>
		</div>

	</div><!-- .wbcom-welcome-content -->
</div><!-- .wbcom-welcome-main-wrapper -->















