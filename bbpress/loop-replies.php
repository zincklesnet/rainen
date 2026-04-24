<?php
/**
 * Replies Loop
 *
 * @package bbPress
 * @subpackage Theme
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

do_action( 'bbp_template_before_replies_loop' );
?>

<ul id="topic-<?php bbp_topic_id(); ?>-replies" class="forums bbp-replies rg-replies">

	<?php if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) : ?>

		<li>
			<?php if ( ! bbp_show_lead_topic() ) : ?>

				<?php bbp_topic_subscription_link(); ?>

				<?php bbp_user_favorites_link(); ?>

			<?php endif; ?>
		</li>

	<?php endif; ?>

	<li class="bbp-body">

		<?php if ( bbp_thread_replies() ) : ?>

			<?php bbp_list_replies(); ?>

		<?php else : ?>

			<?php
			while ( bbp_replies() ) :
				bbp_the_reply();
				?>

				<?php bbp_get_template_part( 'loop', 'single-reply' ); ?>

			<?php endwhile; ?>

		<?php endif; ?>

	</li><!-- .bbp-body -->

</ul><!-- #topic-<?php bbp_topic_id(); ?>-replies -->

<?php
do_action( 'bbp_template_after_replies_loop' );
