<?php

/**
 * Forums Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<?php
$layout = get_theme_mod( 'forum_archive_layout', 'default' );
$class  = 'rg-card-list';

if ( $layout == 'cover' ) {
	$class = 'rg-cover-list';
}
?>

<!-- Forums List -->
<?php do_action( 'bbp_template_before_forums_loop' ); ?>

	<?php if ( $layout == 'card' || $layout == 'cover' ) { ?>
		<ul class="grid-view wb-grid rg-forums-list <?php echo esc_attr( $class ); ?>">
			<?php
			while ( bbp_forums() ) :
				bbp_the_forum();
				?>
				<?php bbp_get_template_part( 'loop-forum-card' ); ?>
			<?php endwhile; ?>
		</ul>
	<?php } else { ?>
		<ul id="forums-list-<?php bbp_forum_id(); ?>" class="bbp-forums">

			<li class="bbp-header">

				<ul class="forum-titles">
					<li class="bbp-forum-info"><?php esc_html_e( 'Forum', 'reign' ); ?></li>
					<li class="bbp-forum-topic-count"><?php esc_html_e( 'Topics', 'reign' ); ?></li>
					<li class="bbp-forum-reply-count">
					<?php
					bbp_show_lead_topic()
						? esc_html_e( 'Replies', 'reign' )
						: esc_html_e( 'Posts', 'reign' );
					?>
					</li>
					<li class="bbp-forum-freshness"><?php esc_html_e( 'Last Post', 'reign' ); ?></li>
				</ul>

			</li><!-- .bbp-header -->

			<li class="bbp-body">

				<?php
				while ( bbp_forums() ) :
					bbp_the_forum();
					?>

					<?php bbp_get_template_part( 'loop', 'single-forum' ); ?>

				<?php endwhile; ?>

			</li><!-- .bbp-body -->

			<li class="bbp-footer">

				<div class="tr">
					<p class="td colspan4">&nbsp;</p>
				</div><!-- .tr -->

			</li><!-- .bbp-footer -->

		</ul><!-- .forums-directory -->
	<?php } ?>

<?php do_action( 'bbp_template_after_forums_loop' ); ?>
