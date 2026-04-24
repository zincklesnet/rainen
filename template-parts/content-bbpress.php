<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Reign
 */

$wbcom_metabox_data = get_post_meta( get_the_ID(), 'reign_wbcom_metabox_data', true );
$page_option        = 'on';

$hide = true;
if ( $page_option == 'on' ) {
	$hide = false;
} elseif ( $page_option == '' ) {
	$hide = true;
}
$reign_subheader_settings = get_post_meta( $post->ID, '_subheader_overwrite', true );
if ( $reign_subheader_settings == 'yes' ) {
	$hide = true;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php if ( bbp_is_single_forum() && ! $hide ) : ?>
		<header class="entry-header bb-single-forum">
			<h1 class="entry-title"><?php bbp_forum_title(); ?></h1>
		</header> <!--.entry-header -->
	<?php endif; ?>

	<?php do_action( 'reign_bbpress_before_content' ); ?>

	<div class="entry-content">
		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'reign' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php do_action( 'reign_bbpress_after_content' ); ?>

	<?php if ( false && get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				sprintf(
				/* translators: %s: Name of current post */
					esc_html__( 'Edit %s', 'reign' ),
					the_title( '<span class="screen-reader-text">"', '"</span>', false )
				),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-## -->
