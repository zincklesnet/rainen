<?php
/**
 * Support For Dokan
 *
 * @package reign
 */

if ( ! function_exists( 'render_store_header_on_top' ) ) {
	/**
	 * Renders the store header at the top of the page.
	 *
	 * @return void
	 */
	function render_store_header_on_top() {
		if ( dokan_is_store_page() ) {
			if ( class_exists( 'Reign_Theme_Structure' ) ) {
				$reign_theme_structure_obj = Reign_Theme_Structure::instance();
				remove_action( 'reign_before_content', array( $reign_theme_structure_obj, 'render_page_header' ) );
			}
		}
	}

	add_action( 'reign_before_content', 'render_store_header_on_top', 9 );
}
