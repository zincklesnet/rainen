<?php
/**
 * Replies Loop - Single Reply
 *
 * @package bbPress
 * @subpackage Theme
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;
?>
<div <?php bbp_reply_class(); ?>>
	<div class="bbp-reply-header">

		<?php do_action( 'bbp_theme_before_reply_author_details' ); ?>
		<div class="bbp-reply-authoravatar">
			<?php bbp_reply_author_link( array( 'type' => 'avatar' ) ); ?>
		</div>
		<div class="bbp-reply--authorname">
			<?php bbp_reply_author_link( array( 'type' => 'name' ) ); ?>

			<?php if ( current_user_can( 'moderate', bbp_get_reply_id() ) ) : ?>
				<?php do_action( 'bbp_theme_before_reply_author_admin_details' ); ?>
				<div class="bbp-reply-ip"><?php bbp_author_ip( bbp_get_reply_id() ); ?></div>
				<?php do_action( 'bbp_theme_after_reply_author_admin_details' ); ?>
			<?php endif; ?>
		</div>

		<div class="bbp-reply__date">
			<span class="bbp-reply-post-date"><?php bbp_reply_post_date(); ?></span>
		</div>

		<?php do_action( 'bbp_theme_after_reply_author_details' ); ?>
	</div><!-- #post-<?php bbp_reply_id(); ?> -->

	<div class="bbp-reply-content">
		<?php do_action( 'bbp_theme_before_reply_content' ); ?>
		<?php bbp_reply_content(); ?>
		<?php do_action( 'bbp_theme_after_reply_content' ); ?>
	</div><!-- .bbp-reply-content -->

	<div class="bbp-reply-footer">
		<div class="bbp-meta">
			<?php if ( bbp_is_single_user_replies() ) : ?>
				<span class="bbp-header">
					<?php esc_html_e( 'In reply to: ', 'reign' ); ?>
					<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink( bbp_get_reply_topic_id() ); ?>"><?php bbp_topic_title( bbp_get_reply_topic_id() ); ?></a>
				</span>
			<?php endif; ?>

			<div id="post-<?php bbp_reply_id(); ?>" class="bbp-reply-permalink-wrapper">
				<a href="<?php bbp_reply_url(); ?>" class="bbp-reply-permalink">#<?php bbp_reply_id(); ?></a>
			</div><!-- #post-<?php bbp_reply_id(); ?> -->

			<?php do_action( 'bbp_theme_before_reply_admin_links' ); ?>
			<?php bbp_reply_admin_links(); ?>
			<?php do_action( 'bbp_theme_after_reply_admin_links' ); ?>
		</div><!-- .bbp-meta -->
	</div>
</div><!-- .reply -->
