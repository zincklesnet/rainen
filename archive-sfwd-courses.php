<?php
/**
 * The template for displaying archive course list
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

get_header();

global $wbtm_reign_settings;
$archive_course_layout = ( isset( $wbtm_reign_settings['learndash']['archive_course_layout'] ) ) ? $wbtm_reign_settings['learndash']['archive_course_layout'] : 'layout_one';

?>
<?php do_action( 'reign_before_content_section' ); ?>
<div class="content-wrapper">

	<?php
	if ( class_exists( 'LearnMate_LearnDash_Addon' ) ) {
		learnmate_get_template( 'ld-template-parts/course-topbar.php' );
	}
	?>

	<?php if ( have_posts() ) : ?>
		<?php
		if ( class_exists( 'LearnMate_LearnDash_Addon' ) ) {
			$view_to_render = isset( $_COOKIE['learnmate_course_view'] ) ? sanitize_key( wp_unslash( $_COOKIE['learnmate_course_view'] ) ) : 'lm-grid-view';
		} else {
			$view_to_render = 'lm-grid-view';
		}
		echo '<div id="lm-course-archive-data" class="' . esc_attr( $view_to_render ) . '">';
		while ( have_posts() ) :
			the_post();
			if ( class_exists( 'LearnMate_LearnDash_Addon' ) ) {
				if ( 'layout_two' === $archive_course_layout ) {
					learnmate_get_template( 'ld-template-parts/course-list-view2.php' );
				} elseif ( 'layout_three' === $archive_course_layout ) {
					learnmate_get_template( 'ld-template-parts/course-list-view3.php' );
				} else {
					learnmate_get_template( 'ld-template-parts/course-list-view.php' );
				}
			} else {
				get_template_part( 'learndash/ld30/course-list' );
			}
			endwhile;
		echo '</div>';

		// Previous/next page navigation.
		echo '<div class="lm-course-pagination-section">';
			the_posts_pagination(
				array(
					'prev_text'          => '<i class="far fa-angle-double-left" aria-hidden="true"></i><span class="screen-reader-text">' . __( 'Previous page', 'reign' ) . '</span>',
					'next_text'          => '<i class="far fa-angle-double-right" aria-hidden="true"></i><span class="screen-reader-text">' . __( 'Next page', 'reign' ) . '</span>',
					'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'reign' ) . ' </span>',
				)
			);
		echo '</div>';

	else :

		get_template_part( 'template-parts/content', 'none' );

	endif;
	?>

</div>

<?php do_action( 'reign_after_content_section' ); ?>

<?php
get_footer();
