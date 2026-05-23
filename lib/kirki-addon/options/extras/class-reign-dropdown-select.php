<?php
/**
 * Reign_Dropdown_Select
 *
 * @package Reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

use Kirki\Field\Select;

if ( ! class_exists( 'Reign_Dropdown_Select' ) ) :

	/**
	 * @class Reign_Dropdown_Select
	 */
	class Reign_Dropdown_Select extends Select {

		/**
		 * Gets the unique identifier for the theme component.
		 *
		 * @return string Component slug.
		 */
		public function get_slug() : string {
			return 'reign_dropdown_select';
		}

		/**
		 * Filter arguments before creating the control.
		 *
		 * @access public
		 * @since 0.1
		 * @param array                $args         The field arguments.
		 * @param WP_Customize_Manager $wp_customize The customizer instance.
		 * @return array
		 */
		public function filter_control_args( $args, $wp_customize ) {

			if ( 'reign_login_page' === $args['settings'] || 'reign_registration_page' === $args['settings'] || 'reign_404_page' === $args['settings'] ) {

				$args = parent::filter_control_args( $args, $wp_customize );

				$all_pages          = get_pages();
				$args['choices'][0] = __( '-- Select a Page --', 'reign' );

				foreach ( $all_pages as $page ) {
					$args['choices'][ $page->ID ] = $page->post_title;
				}
			}

			return $args;
		}

	}

endif;

if ( defined( 'KIRKI_VERSION' ) && version_compare( KIRKI_VERSION, '4.0', '>=' ) ) {
	$args = array();
	return new Reign_Dropdown_Select( $args );
}
