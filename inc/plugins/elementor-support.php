<?php
/**
 * Elementor compatibility functions.
 *
 * @package Reign
 * @since 7.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'reign_register_elementor_locations' ) ) {
	function reign_register_elementor_locations( $elementor_theme_manager ) {
			$elementor_theme_manager->register_location( 'header' );
			$elementor_theme_manager->register_location( 'footer' );
	}
	add_action( 'elementor/theme/register_locations', 'reign_register_elementor_locations' );
}
