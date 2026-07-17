<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

get_header();

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
			// Use Reign's default template for regular pages.
			get_template_part( 'template-parts/content', 'page' );
		}

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			reign_maybe_wrap_builder_comments( true );
			comments_template();
			reign_maybe_wrap_builder_comments( false );
		endif;

	endwhile; // End of the loop.
	?>
</div>

<?php do_action( 'reign_after_content_section' ); ?>

<?php
get_footer();