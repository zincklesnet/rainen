<?php
/**
 * The template for members notifications loop
 *
 * This template can be overridden by copying it to yourtheme/buddypress/members/single/notifications/notifications-loop.php.
 *
 * @since   BuddyPress 3.0.0
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( bp_has_notifications( bp_ajax_querystring( 'notifications' ) ) ) :

	bp_nouveau_pagination( 'top' ); ?>

	<form action="" method="post" id="notifications-bulk-management" class="standard-form">
		<ul class="notification-list rg-nouveau-list rg-item-list list-view">
			<li class="rg-item-wrap rg-header-item align-items-center no-hover-effect">
				<div class="bulk-select-all">
					<input id="select-all-notifications" class="rg-styled-checkbox" type="checkbox" aria-label="<?php esc_attr_e( 'Select all', 'reign' ); ?>" />
					<label for="select-all-notifications"></label>
				</div>

				<div class="notifications-options-nav">
					<?php bp_nouveau_notifications_bulk_management_dropdown(); ?>
				</div><!-- .notifications-options-nav -->

				<?php wp_nonce_field( 'notifications_bulk_nonce', 'notifications_bulk_nonce' ); ?>

				<div class="date rg-sort-by-date push-right">
					<?php esc_html_e( 'Date Received', 'reign' ); ?>
					<?php bp_nouveau_notifications_sort_order_links(); ?>
				</div>
			</li>

			<?php
			while ( bp_the_notifications() ) :
				bp_the_notification();
				$bp                 = buddypress();
				$bp_notification_id = bp_get_the_notification_id();
				$readonly           = isset( $bp->notifications->query_loop->notification->readonly ) ? $bp->notifications->query_loop->notification->readonly : false;
				?>

				<li class="rg-item-wrap">
					<div class="bulk-select-check">
						<input id="<?php echo esc_attr( $bp_notification_id ); ?>" type="checkbox" name="notifications[]" value="<?php echo esc_attr( $bp_notification_id ); ?>" class="notification-check bs-styled-checkbox" data-readonly="<?php echo esc_attr( $readonly ); ?>"/>
						<label for="<?php echo esc_attr( $bp_notification_id ); ?>"><span class="bp-screen-reader-text"><?php esc_html_e( 'Select this notification', 'reign' ); ?></span></label>
					</div>

					<div class="notification-content">
						<div class="notification-description"><?php bp_the_notification_description(); ?></div>
						<div class="notification-since"><?php bp_the_notification_time_since(); ?></div>
					</div>

					<div class="notification-actions actions">
					<?php bp_the_notification_action_links(); ?>
					</div>
				</li>

			<?php endwhile; ?>
		</ul>
	</form>

	<?php bp_nouveau_pagination( 'bottom' ); ?>

<?php else : ?>

	<?php bp_nouveau_user_feedback( 'member-notifications-none' ); ?>

	<?php
endif;
