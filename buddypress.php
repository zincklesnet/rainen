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

$register_split_view       = get_theme_mod( 'register_split_view', false );
$register_heading_position = get_theme_mod( 'register_heading_position', '' );
$register_custom_heading   = get_theme_mod( 'register_custom_heading', esc_html__( 'Join our community!', 'reign' ) );
$register_custom_text      = get_theme_mod( 'register_custom_text', esc_html__( 'Become a member today to connect with others, join groups, and share experiences!', 'reign' ) );

if ( ( function_exists( 'bp_is_register_page' ) && bp_is_register_page() ) || ( function_exists( 'bp_is_activation_page' ) && bp_is_activation_page() ) ) {
	$class_bp_register = 'rg-bp-container-reg';

	if ( reign_is_truthy( $register_split_view ) ) {
		if ( $register_heading_position ) {
			$heading_postion_style = 'padding-top: ' . $register_heading_position . '%;';
		} else {
			$heading_postion_style = 'padding-top: 0;';
		}
		echo '<div class="login-split"><div style="' . esc_attr( $heading_postion_style ) . '">';
		if ( $register_custom_heading ) {
			echo wp_kses_post( sprintf( esc_html__( '%s', 'reign' ), $register_custom_heading ) );
		}
		if ( $register_custom_text ) {
			echo '<span>';
			echo wp_kses_post( stripslashes( $register_custom_text ) );
			echo '</span>';
		}
		echo '</div><div class="split-overlay"></div></div>';
	}
} else {
	$class_bp_register = 'rg-bp-container';
}

do_action( 'reign_before_content_section' ); ?>

<div class="content-wrapper <?php echo esc_attr( $class_bp_register ); ?>">
	<?php
	// Temporarily reset $wp_smiliessearch to avoid errors with count().
	global $wp_smiliessearch;
	$temp_wp_smiliessearch = isset( $wp_smiliessearch ) ? $wp_smiliessearch : array();
	$wp_smiliessearch      = array();

	while ( have_posts() ) :
		the_post();

		get_template_part( 'template-parts/content', 'buddypress' );

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;

	endwhile; // End of the loop.

	// Restore the original $wp_smiliessearch.
	$wp_smiliessearch = $temp_wp_smiliessearch;
	?>
</div>

<?php get_sidebar( 'buddypress' ); ?>

<?php
get_footer();
