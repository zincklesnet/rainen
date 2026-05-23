<?php

/**
 * Fires before the activity directory listing.
 *
 * @since 1.5.0
 */
do_action( 'bp_before_directory_activity' );

?>
<div id="buddypress" class="<?php echo esc_attr( bp_nouveau_get_container_classes() ); ?>">
	<?php bp_nouveau_before_activity_directory_content(); ?>

		<?php if ( $allow_posting ) : ?>
			<?php if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) : ?>
				<?php
				wp_enqueue_script( 'bp-nouveau-activity-post-form' );
				if ( wp_script_is( 'bp-nouveau-activity-reacted', 'registered' ) ) {
					wp_enqueue_script( 'bp-nouveau-activity-reacted' );
				}

				// If reaction is enabled for activity post or comment then load the template.
				if ( function_exists( 'bb_load_reaction_popup_modal_js_template' ) ) {
					bb_load_reaction_popup_modal_js_template();
				}
				?>
			<?php endif; ?>	
			<?php bp_get_template_part( 'activity/post-form' ); ?>
		<?php endif; ?>

		<div class="screen-content">

			<?php // bp_get_template_part( 'common/search-and-filters-bar' ); ?>

			<?php bp_nouveau_activity_hook( 'before_directory', 'list' ); ?>

			<div id="activity-stream" class="activity" data-bp-list="activity" data-ajax="<?php echo function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ? 'false' : ''; ?>">

				<?php	bp_nouveau_before_loop(); ?>

				<?php if ( bp_has_activities( $query ) ) : ?>
					
					<ul class="activity-list item-list bp-list">

					<?php
					while ( bp_activities() ) :
						bp_the_activity();
						?>

						<?php bp_get_template_part( 'activity/entry' ); ?>

					<?php endwhile; ?>
					
					<?php if ( bp_activity_has_more_items() ) : ?>

						<li class="load-more" data-limit="<?php echo esc_attr( $limit ); ?>">
							<a href="<?php bp_activity_load_more_link(); ?>"><?php echo esc_html_x( 'Load More', 'button', 'buddypress-newsfeed' ); ?></a>
						</li>

					<?php endif; ?>

						</ul>

				<?php else : ?>

						<?php bp_nouveau_user_feedback( 'activity-loop-none' ); ?>

				<?php endif; ?>

				<?php bp_nouveau_after_loop(); ?>

			</div><!-- .activity -->

			<?php bp_nouveau_after_activity_directory_content(); ?>

		</div><!-- // .screen-content -->
	
	<?php if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss ) ) : ?>
		<?php bp_nouveau_after_directory_page(); ?>
	<?php endif; ?>
</div>
