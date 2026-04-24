<?php
/**
 * Search Loop - Single Topic
 *
 * @package bbPress
 * @subpackage Theme
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;
?>

<div id="post-<?php bbp_topic_id(); ?>" <?php bbp_topic_class(); ?>>
	<div class="bbp-topic-top">
		<?php do_action( 'bbp_theme_before_topic_title' ); ?>
		<span><?php esc_html_e( 'Topic:', 'reign' ); ?>
			<a href="<?php bbp_topic_permalink(); ?>"><?php bbp_topic_title(); ?></a>
		</span>

		<span class="bbp-topic-title-meta">
			<?php if ( function_exists( 'bbp_is_forum_group_forum' ) && bbp_is_forum_group_forum( bbp_get_topic_forum_id() ) ) : ?>
				<?php esc_html_e( 'in group forum ', 'reign' ); ?>

			<?php else : ?>
				<?php esc_html_e( 'in forum ', 'reign' ); ?>

			<?php endif; ?>
			<a href="<?php bbp_forum_permalink( bbp_get_topic_forum_id() ); ?>"><?php bbp_forum_title( bbp_get_topic_forum_id() ); ?></a>
		</span><!-- .bbp-topic-title-meta -->

		<?php do_action( 'bbp_theme_after_topic_title' ); ?>

		<a href="<?php bbp_topic_permalink(); ?>" class="bbp-topic-permalink">#<?php bbp_topic_id(); ?></a>

	</div><!-- .bbp-topic-header -->
	<div class="bbp-topic-header">
		<div class="bbp-topic-authoravtar">
			<?php bbp_topic_author_link( array( 'type' => 'avatar' ) ); ?>
		</div>
		<div class="bbp-topic-authorname">
			<?php bbp_topic_author_link( array( 'type' => 'name' ) ); ?>
			<?php if ( bbp_is_user_keymaster() ) : ?>

				<?php do_action( 'bbp_theme_before_topic_author_admin_details' ); ?>

				<div class="bbp-reply-ip"><?php bbp_author_ip( bbp_get_topic_id() ); ?></div>

				<?php do_action( 'bbp_theme_after_topic_author_admin_details' ); ?>

			<?php endif; ?>
		</div>
		<div class="bbp-topic-date">
			<span class="bbp-topic-post-date"><?php bbp_topic_post_date( bbp_get_topic_id() ); ?></span>
		</div>

		<?php do_action( 'bbp_theme_after_topic_author_details' ); ?>

	</div><!-- .bbp-topic-author -->

	<div class="bbp-topic-content">

		<?php do_action( 'bbp_theme_before_topic_content' ); ?>

		<?php bbp_topic_content(); ?>

		<?php do_action( 'bbp_theme_after_topic_content' ); ?>

	</div><!-- .bbp-topic-content -->
</div>
