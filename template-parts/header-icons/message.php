<?php
/**
 * Messages
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( class_exists( 'BuddyPress' ) && is_user_logged_in() && bp_is_active( 'messages' ) ) {
	?>
	<div class="rg-msg header-notifications-dropdown-toggle">
		<a class="rg-icon-wrap dropdown-toggle" href="<?php echo esc_url( bp_loggedin_user_domain() . bp_get_messages_slug() ); ?>" title="<?php esc_attr_e( 'Messages', 'reign' ); ?>">
			<span class="far fa-envelope"></span>
			<?php
			if ( class_exists( 'BP_Better_Messages' ) ) {
				echo do_shortcode( '[bp_better_messages_unread_counter hide_when_no_messages="1"]' );
			} elseif ( function_exists( 'bp_get_total_unread_messages_count' ) && bp_get_total_unread_messages_count( bp_loggedin_user_id() ) > 0 ) {
				?>
					<span class="count rg-count"><?php echo esc_html( bp_get_total_unread_messages_count( bp_loggedin_user_id() ) > 9 ? '9+' : bp_get_total_unread_messages_count( bp_loggedin_user_id() ) ); ?></span>
					<?php

			}
			?>
		</a>
		<div class="header-notifications-dropdown-menu" aria-labelledby="nav_private_messages" aria-live="polite">
			<div class="dropdown-title">
				<?php esc_html_e( 'Messages', 'reign' ); ?>
				<?php if ( ! class_exists( 'BP_Better_Messages' ) && function_exists( 'bp_get_total_unread_messages_count' ) && 0 < bp_get_total_unread_messages_count( bp_loggedin_user_id() ) ) : ?>
				<a class="mark-read-all mark-messages-read" href="#"><?php esc_html_e( 'Mark all as read', 'reign' ); ?></a>
				<?php endif; ?>
			</div>
			<?php get_template_part( 'template-parts/header-icons/messages-dropdown-inner' ); ?>
		</div><!-- .header-notifications-dropdown-menu -->
	</div>
	<?php
} elseif ( function_exists( 'buddynext_header_messages_bell' ) ) {
	// BuddyNext active (mutually exclusive with BuddyPress) — render the BN
	// messages icon linking to the member's inbox (no badge yet, zero JS).
	buddynext_header_messages_bell();
}
