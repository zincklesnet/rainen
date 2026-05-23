<?php
/**
 * Fires before the activity directory listing.
 *
 * @since 1.0.0
 */

/**
 * Fires before the activity directory listing.
 *
 * @since 1.5.0
 */

do_action( 'bp_before_directory_activity' ); ?>

<div id="buddypress">

	<?php

	/**
	 * Fires before the activity directory display content.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_before_directory_activity_content' );
	?>

	<?php if ( $allow_posting && is_user_logged_in() ) : ?>

		<?php bp_get_template_part( 'activity/post-form' ); ?>

	<?php endif; ?>

	<div id="template-notices" role="alert" aria-atomic="false">
		<?php

		/**
		 * Fires towards the top of template pages for notice display.
		 *
		 * @since 1.0.0
		 */
		do_action( 'template_notices' );
		?>

	</div>

	<?php

	/**
	 * Fires before the display of the activity list.
	 *
	 * @since 1.5.0
	 */
	do_action( 'bp_before_directory_activity_list' );
	?>

	<div class="activity" aria-live="polite" aria-atomic="false" aria-relevant="all">

		<?php do_action( 'bp_before_activity_loop' ); ?>

		<?php if ( bp_has_activities( $query ) ) : ?>

			<ul id="activity-stream" class="activity-list item-list">

				<?php
				while ( bp_activities() ) :
					bp_the_activity();
					?>

					<?php bp_get_template_part( 'activity/entry' ); ?>

				<?php endwhile; ?>

			</ul>

		<?php else : ?>

		<div id="message" class="info">
			<p><?php esc_html_e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress-newsfeed' ); ?></p>
		</div>

	<?php endif; ?>

	<?php

	/**
	 * Fires after the finish of the activity loop.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_after_activity_loop' );
	?>

</div>
