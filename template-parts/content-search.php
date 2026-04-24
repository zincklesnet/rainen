<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Reign
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'reign_rg_post_content_before' ); ?>

	<div class="rg-post-content">
		<header class="entry-header">
			<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
			<div class="entry-meta"><?php reign_entry_list_footer(); ?></div>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<?php
			if ( is_singular() ) {
				/* translators: %s: Name of current post */
				the_content(
					sprintf(
						wp_kses( __( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'reign' ), array( 'span' => array( 'class' => array() ) ) ),
						the_title( '<span class="screen-reader-text">"', '"</span>', false )
					)
				);
			} else {
				the_excerpt();
			}
			?>

			<?php
			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'reign' ),
					'after'  => '</div>',
				)
			);
			?>

			<?php if ( ! is_singular() ) { ?>
				<p class="no-margin"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'View %s', 'reign' ), the_title_attribute( 'echo=0' ) ) ); ?>" class="read-more button"><?php esc_html_e( 'Read More', 'reign' ); ?></a></p>
				<?php } ?>

		</div><!-- .entry-content -->
	</div>

	<?php do_action( 'reign_rg_post_content_after' ); ?>

</article><!-- #post-## -->
