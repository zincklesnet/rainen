<?php
/**
 * The template for displaying FluentCart single products
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

get_header();

// Check if any page builder is used.
$is_page_builder_used = reign_is_page_builder_used();
?>

<?php do_action( 'reign_before_content_section' ); ?>

	<div class="<?php echo esc_attr( reign_get_page_builder_content_classes() ); ?>">
		<?php
		while ( have_posts() ) :
			the_post();

			// If page builder is active, output content directly.
			if ( $is_page_builder_used ) {
				reign_output_page_builder_content();
			} else {
				// Output FluentCart product content.
				?>
				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
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
				</article><!-- #post-<?php the_ID(); ?> -->
				<?php
			}

		endwhile; // End of the loop.
		?>
	</div>

<?php do_action( 'reign_after_content_section' ); ?>

<?php
get_footer();
