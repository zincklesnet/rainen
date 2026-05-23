<?php
/**
 * Support For LifterLMS
 *
 * @package reign
 */

// Hook into the template_redirect action.
add_action( 'template_redirect', 'reign_remove_single_course_page_header' );

function reign_remove_single_course_page_header() {
	// Check if LifterLMS plugin is active.
	if ( class_exists( 'LLMS_Post_Types' ) ) {
		// Check if the current page is a single course page.
		if ( is_singular( 'course' ) ) {
			// Remove all actions hooked to 'reign_before_content' for course pages.
			remove_all_actions( 'reign_before_content' );
		}
	}
}

if ( ! function_exists( 'reign_lifterlms_get_single_template' ) ) {
	/**
	 * Filters the single template for LifterLMS courses.
	 *
	 * @param string $single_template The path to the single template.
	 * @return string The modified path to the single template.
	 */
	function reign_lifterlms_get_single_template( $single_template ) {

		// Check if the post type is 'course' (LifterLMS courses).
		if ( get_post_type() === 'course' ) {
			$template = 'lifterlms/single-course.php'; // Your custom path inside the theme.

			if ( file_exists( get_stylesheet_directory() . '/' . $template ) ) {
				$single_template = get_stylesheet_directory() . '/' . $template;
			} elseif ( file_exists( get_template_directory() . '/' . $template ) ) {
				$single_template = get_template_directory() . '/' . $template;
			}
		}

		return $single_template;
	}

	add_filter( 'single_template', 'reign_lifterlms_get_single_template', 10 );
}
