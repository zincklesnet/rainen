<?php
/**
 * Support For TutorLMS
 *
 * @package reign
 */

// Hook into the template_redirect action.
add_filter( 'body_class', 'reign_tutorlms_body_class_custom_color' );

/**
 * Adds a custom body class based on the Tutor LMS color preset type.
 *
 * @param array $classes Array of existing body classes.
 * @return array Modified array of body classes.
 */
function reign_tutorlms_body_class_custom_color( $classes ) {

	if ( function_exists( 'tutor' ) ) {

		if ( function_exists( 'get_tutor_option' ) && 'default' == get_tutor_option( 'color_preset_type' ) ) {
			$classes[] = 'reign-tutor-custom-colors';
		}

		return $classes;
	}
}
