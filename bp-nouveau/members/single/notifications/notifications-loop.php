<?php
/**
 * BuddyPress - Members Notifications Loop
 *
 * @since 3.0.0
 * @version 3.1.0
 */

if ( bp_has_notifications( bp_ajax_querystring( 'notifications' ) ) ) :

	bp_nouveau_pagination( 'top' ); ?>

	<form action="" method="post" id="notifications-bulk-management" class="standard-form">
		<ul class="notification-list rg-nouveau-list rg-item-list list-view">
			<li class="rg-item-wrap rg-header-item align-items-center no-hover-effect">
				<div class="bulk-select-all">
					<input id="select-all-notifications" type="checkbox" class="rg-styled-checkbox">
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
				?>

				<li class="rg-item-wrap">
					<div class="bulk-select-check">
						<input id="<?php bp_the_notification_id(); ?>" type="checkbox" name="notifications[]" value="<?php bp_the_notification_id(); ?>" class="notification-check rg-styled-checkbox">
						<label for="<?php bp_the_notification_id(); ?>"></label>
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
