<?php
/**
 * BuddyPress - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @since 3.0.0
 * @version 10.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

bp_nouveau_activity_hook( 'before', 'entry' );
$activity_id      = bp_get_activity_id();
$bp_edit_activity = bp_activity_get_meta( $activity_id, '_bp_edit_activity', true );
?>

<li class="<?php bp_activity_css_class(); ?>" id="activity-<?php bp_activity_id(); ?>" <?php bp_nouveau_activity_data_attribute_id(); ?> data-bp-timestamp="<?php bp_nouveau_activity_timestamp(); ?>">

	<?php bp_nouveau_activity_entry_dropdown_toggle_buttons(); ?>

	<div class="activity-avatar item-avatar">

		<a href="<?php bp_activity_user_link(); ?>">

			<?php bp_activity_avatar( array( 'type' => 'full' ) ); ?>

		</a>

	</div>

	<div class="activity-content">

		<div class="activity-header">

			<?php bp_activity_action(); ?>

			<p class="activity-date">
				<a href="<?php echo esc_url( bp_activity_get_permalink( bp_get_activity_id() ) ); ?>">
					<?php echo esc_html( bp_core_time_since( bp_get_activity_date_recorded() ) ); ?>
				</a>
			</p>

			<?php if ( $bp_edit_activity ) { ?>
				<span class="reign-activity-edited-text">
					<?php esc_html_e( '(Edited)', 'reign' ); ?>				
				</span>
			<?php } ?>

		</div>

		<?php if ( bp_nouveau_activity_has_content() ) : ?>

			<div class="activity-inner">

				<?php bp_get_template_part( 'activity/type-parts/content', bp_activity_type_part() ); ?>

			</div>

		<?php endif; ?>

		<div class="bp-activity-post-footer bp-activity-content-actions"><?php do_action( 'bp_activity_before_post_footer_content' ); ?></div>

		<?php bp_nouveau_activity_entry_buttons(); ?>

	</div>

	<?php bp_nouveau_activity_hook( 'before', 'entry_comments' ); ?>

	<?php if ( bp_activity_get_comment_count() || ( is_user_logged_in() && ( bp_activity_can_comment() || bp_is_single_activity() ) ) ) : ?>

		<div class="activity-comments">

			<?php bp_activity_comments(); ?>

			<?php bp_nouveau_activity_comment_form(); ?>

		</div>

	<?php endif; ?>

	<?php bp_nouveau_activity_hook( 'after', 'entry_comments' ); ?>

</li>

<?php
bp_nouveau_activity_hook( 'after', 'entry' );
