<?php
/**
 * Topics Loop - Single
 *
 * @package bbPress
 * @subpackage Theme
 */
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;
?>
<li>
	<div id="bbp-topic-<?php bbp_topic_id(); ?>" <?php bbp_topic_class(); ?>>

		<div class="rg-topic-avatar">

			<?php
			echo bbp_get_topic_author_link( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'type' => 'avatar',
					'size' => '60',
				)
			);
			?>

		</div>

		<div class="rg-topic-center">

			<div class="bbp-topic-title">

				<?php if ( bbp_is_user_home() ) : ?>

					<?php if ( bbp_is_favorites() ) : ?>

						<span class="bbp-row-actions">

							<?php do_action( 'bbp_theme_before_topic_favorites_action' ); ?>

							<?php
							bbp_topic_favorite_link(
								array(
									'before'    => '',
									'favorite'  => '+',
									'favorited' => '&times;',
								)
							);
							?>

							<?php do_action( 'bbp_theme_after_topic_favorites_action' ); ?>

						</span>

					<?php elseif ( bbp_is_subscriptions() ) : ?>

						<span class="bbp-row-actions">

							<?php do_action( 'bbp_theme_before_topic_subscription_action' ); ?>

							<?php
							bbp_topic_subscription_link(
								array(
									'before'      => '',
									'subscribe'   => '+',
									'unsubscribe' => '&times;',
								)
							);
							?>

							<?php do_action( 'bbp_theme_after_topic_subscription_action' ); ?>

						</span>

					<?php endif; ?>

				<?php endif; ?>

				<?php do_action( 'bbp_theme_before_topic_title' ); ?>

				<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink(); ?>"><?php bbp_topic_title(); ?></a>

				<?php do_action( 'bbp_theme_after_topic_title' ); ?>

				<?php // bbp_topic_pagination(); ?>

				<?php do_action( 'bbp_theme_before_topic_meta' ); ?>

				<div class="bbp-topic-meta">

					<?php do_action( 'bbp_theme_before_topic_started_by' ); ?>

					<span class="bbp-topic-started-by"><?php printf( esc_html__( 'by %1$s', 'reign' ), bbp_get_topic_author_link( array( 'type' => 'name' ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span> <span class="bbp-topic-freshness"><?php do_action( 'bbp_theme_before_topic_freshness_link' ); ?><?php esc_html_e( 'last updated', 'reign' ); ?> <?php bbp_topic_freshness_link(); ?><?php do_action( 'bbp_theme_after_topic_freshness_link' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>

					<?php do_action( 'bbp_theme_after_topic_started_by' ); ?>

					<?php if ( ! bbp_is_single_forum() || ( bbp_get_topic_forum_id() !== bbp_get_forum_id() ) ) : ?>

						<?php do_action( 'bbp_theme_before_topic_started_in' ); ?>

						<span class="bbp-topic-started-in"><?php printf( esc_html__( 'in: %1$s', 'reign' ), '<a href="' . bbp_get_forum_permalink( bbp_get_topic_forum_id() ) . '">' . bbp_get_forum_title( bbp_get_topic_forum_id() ) . '</a>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<?php do_action( 'bbp_theme_after_topic_started_in' ); ?>

					<?php endif; ?>

				</div>

				<?php do_action( 'bbp_theme_after_topic_meta' ); ?>

				<?php bbp_topic_row_actions(); ?>

			</div>
		</div>

		<div class="rg-topic-comments">
			<div class="rg-topic-comments-avatars">
			<?php	reign_bbp_get_reply_avtar(); ?>
			</div>

			<div class="bbp-topic-reply-count"><a class="bbp-topic-permalink" href="<?php bbp_topic_permalink(); ?>"><?php bbp_topic_reply_count(); ?> <?php esc_html_e( 'Replies', 'reign' ); ?></a></div>
		</div>


	</div><!-- #bbp-topic-<?php bbp_topic_id(); ?> -->
</li>
