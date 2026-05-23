<?php
/**
 * Page Builder Helper Functions
 *
 * @package Reign
 * @subpackage Page Builder Compatibility
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if any page builder is active
 *
 * @return bool
 */
function reign_is_any_page_builder_active() {
	$manager = Reign_Page_Builder_Manager::get_instance();
	$active_builders = $manager->get_active_builders();
	return ! empty( $active_builders );
}

/**
 * Check if any page builder is used on current post
 *
 * @param int $post_id Optional post ID
 * @return bool
 */
function reign_is_page_builder_used( $post_id = null ) {
	$manager = Reign_Page_Builder_Manager::get_instance();
	return $manager->is_any_builder_used( $post_id );
}

/**
 * Get active page builder for current post
 *
 * @param int $post_id Optional post ID
 * @return Reign_Page_Builder_Base|false
 */
function reign_get_active_page_builder( $post_id = null ) {
	$manager = Reign_Page_Builder_Manager::get_instance();
	return $manager->get_active_builder( $post_id );
}

/**
 * Get content wrapper classes for page builders
 *
 * @return string
 */
function reign_get_page_builder_content_classes() {
	$builder = reign_get_active_page_builder();
	if ( $builder ) {
		return $builder->get_content_wrapper_classes();
	}
	return 'content-wrapper';
}

/**
 * Output page builder content
 *
 * @param WP_Post $post Optional post object
 */
function reign_output_page_builder_content( $post = null ) {
	$builder = reign_get_active_page_builder();
	if ( $builder ) {
		$builder->output_builder_content( $post );
	}
}

/**
 * Maybe wrap comments for page builder pages
 *
 * @param bool $wrap True to open wrapper, false to close
 */
function reign_maybe_wrap_builder_comments( $wrap = true ) {
	$builder = reign_get_active_page_builder();
	if ( $builder ) {
		$builder->maybe_wrap_comments( $wrap );
	}
}

/**
 * Check if a specific page builder is active
 *
 * @param string $builder_slug Builder slug (e.g., 'divi-builder', 'elementor')
 * @return bool
 */
function reign_is_builder_active( $builder_slug ) {
	$manager = Reign_Page_Builder_Manager::get_instance();
	return $manager->is_builder_active( $builder_slug );
}

/**
 * Get specific page builder instance
 *
 * @param string $builder_slug Builder slug
 * @return Reign_Page_Builder_Base|null
 */
function reign_get_page_builder( $builder_slug ) {
	$manager = Reign_Page_Builder_Manager::get_instance();
	return $manager->get_builder( $builder_slug );
}

/**
 * Check if using any page builder template
 *
 * @return bool
 */
function reign_is_page_builder_template() {
	$builder = reign_get_active_page_builder();
	if ( $builder ) {
		return $builder->is_builder_template();
	}
	return false;
}

/**
 * Check if in page builder preview mode
 *
 * @return bool
 */
function reign_is_page_builder_preview() {
	$builder = reign_get_active_page_builder();
	if ( $builder ) {
		return $builder->is_builder_preview();
	}
	return false;
}

/* Divi-specific backward compatibility functions */

/**
 * Check if Divi Builder is active
 *
 * @return bool
 */
function reign_is_divi_active() {
	return reign_is_builder_active( 'divi-builder' );
}

/**
 * Check if Divi Builder is used on current post
 *
 * @param int $post_id Optional post ID
 * @return bool
 */
function reign_is_divi_builder_used( $post_id = null ) {
	$divi = reign_get_page_builder( 'divi-builder' );
	if ( $divi ) {
		return $divi->is_builder_used( $post_id );
	}
	return false;
}

/**
 * Check if using Divi template
 *
 * @return bool
 */
function reign_is_divi_template() {
	$divi = reign_get_page_builder( 'divi-builder' );
	if ( $divi ) {
		return $divi->is_builder_template();
	}
	return false;
}

/**
 * Get Divi content wrapper classes
 *
 * @return string
 */
function reign_get_divi_content_wrapper_classes() {
	$divi = reign_get_page_builder( 'divi-builder' );
	if ( $divi && $divi->is_builder_used() ) {
		return $divi->get_content_wrapper_classes();
	}
	return 'content-wrapper';
}

/**
 * Output Divi content
 *
 * @param WP_Post $post Optional post object
 */
function reign_output_divi_content( $post = null ) {
	$divi = reign_get_page_builder( 'divi-builder' );
	if ( $divi ) {
		$divi->output_builder_content( $post );
	}
}

/**
 * Maybe wrap Divi comments
 *
 * @param bool $wrap True to open wrapper, false to close
 */
function reign_maybe_wrap_divi_comments( $wrap = true ) {
	$divi = reign_get_page_builder( 'divi-builder' );
	if ( $divi ) {
		$divi->maybe_wrap_comments( $wrap );
	}
}