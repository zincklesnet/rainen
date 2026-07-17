<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

get_header();

$reign_single_post_navigation = get_theme_mod( 'reign_single_post_navigation', 'on' );

// Check if any page builder is used.
$is_page_builder_used = reign_is_page_builder_used();
$active_builder       = reign_get_active_page_builder();

?>

<?php do_action( 'reign_before_content_section' ); ?>

	<div class="<?php echo esc_attr( reign_get_page_builder_content_classes() ); ?>">
		<?php
		while ( have_posts() ) :
			the_post();

			// If page builder is active, output content directly without Reign's template.
			if ( $is_page_builder_used ) {
				reign_output_page_builder_content();
			} else {
				// Use Reign's default template for regular posts.
				get_template_part( 'template-parts/content', get_post_format() );
			}

			do_action( 'reign_post_content_after' );

			$args = array(
				'prev_text' => '<span class="rg-next-prev">' . __( 'Previous', 'reign' ) . '	</span><span class="nav-title">%title</span>',
				'next_text' => '<span class="rg-next-prev">' . __( 'Next', 'reign' ) . '</span><span class="nav-title">%title</span>',
			);

			if ( is_singular( 'post' ) ) {
				if ( reign_is_truthy( $reign_single_post_navigation ) ) {
					reign_post_navigation();
				}
			}

			do_action( 'reign_post_comment_before' );

			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found -- intentionally disabled action hook.
			if ( ( 'sfwd-courses' !== get_post_type() ) || ( ! is_plugin_active( 'reign-learndash-addon/reign-learndash-addon.php' ) ) ) {
				// If comments are open or we have at least one comment, load up the comment template.
				if ( comments_open() || get_comments_number() ) :
					reign_maybe_wrap_builder_comments( true );
					comments_template();
					reign_maybe_wrap_builder_comments( false );
				endif;
			}

		endwhile; // End of the loop.
		?>
	</div>

<?php do_action( 'reign_after_content_section' ); ?>

<?php
get_footer();
