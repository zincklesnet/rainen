<?php
/**
 * Forums Loop card/cover
 *
 * @package bbPress
 * @subpackage Theme
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$layout      = get_theme_mod( 'forum_archive_layout', 'default' );
$grid_option = get_theme_mod( 'forum_archive_layout_per_row', 'three' );

// Map grid options to their corresponding classes.
$grid_classes = array(
	'two'   => 'lg-wb-grid-1-2',
	'three' => 'lg-wb-grid-1-3',
	'four'  => 'lg-wb-grid-1-4',
);

$grid_class   = isset( $grid_classes[ $grid_option ] ) ? $grid_classes[ $grid_option ] : 'lg-wb-grid-1-3';
$layout_class = in_array( $layout, array( 'cover', 'card' ) ) ? 'rg-forum-details' : '';

?>

<li class="wb-grid-cell sm-wb-grid-1-2 md-wb-grid-1-2 <?php echo esc_attr( $grid_class ); ?>">
	<div class="rg-cover-list-item">
		<?php if ( function_exists( 'bbp_get_forum_thumbnail_image' ) ) { ?>
			<a href="<?php bbp_forum_permalink(); ?>" class="rg-cover-wrap" title="<?php bbp_forum_title(); ?>">
				<?php echo bbp_get_forum_thumbnail_image( bbp_get_forum_id(), 'large', 'full' ); ?>
			</a>
		<?php } else { ?>
			<a href="<?php bbp_forum_permalink(); ?>" class="rg-cover-wrap" title="<?php bbp_forum_title(); ?>">
				<?php // echo bbp_get_forum_thumbnail_image( bbp_get_forum_id(), 'large', 'full' ); ?>
			</a>
		<?php } ?>

		<div class="rg-card-forum-details <?php echo esc_attr( $layout_class ); ?>">
			<div class="rg-sec-header">
				<?php do_action( 'bbp_theme_before_forum_title' ); ?>
				<h3><a class="bbp-forum-title" href="<?php bbp_forum_permalink(); ?>"><?php bbp_forum_title(); ?></a></h3>
				<?php do_action( 'bbp_theme_after_forum_title' ); ?>
			</div>

			<div class="rg-forum-content-wrap">
				<?php do_action( 'bbp_theme_before_forum_description' ); ?>
				<div class="rg-forum-content"><?php echo esc_html( wp_trim_words( bbp_get_forum_content( bbp_get_forum_id() ), 18, '&hellip;' ) ); ?></div>
				<?php do_action( 'bbp_theme_after_forum_description' ); ?>
			</div>

			<div class="forums-meta rg-forums-meta">
			<?php
				do_action( 'bbp_theme_before_forum_sub_forums' );

				$r = array(
					'before'           => '',
					'after'            => '',
					'link_before'      => '<span>',
					'link_after'       => '</span>',
					'count_before'     => ' (',
					'count_after'      => ')',
					'count_sep'        => ', ',
					'separator'        => ' ',
					'forum_id'         => '',
					'show_topic_count' => false,
					'show_reply_count' => false,
				);

				bbp_list_forums( $r );

				do_action( 'bbp_theme_after_forum_sub_forums' );
				?>
				</div>

			<?php if ( $layout != 'cover' ) { ?>
				<div class="rg-timestamp">
					<?php do_action( 'bbp_theme_before_forum_freshness_link' ); ?>
					<?php bbp_forum_freshness_link(); ?>
					<?php do_action( 'bbp_theme_after_forum_freshness_link' ); ?>
				</div>
			<?php } ?>
		</div>
	</div>
</li>
