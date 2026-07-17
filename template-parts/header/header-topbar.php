<?php
/**
 * Top Bar
 *
 * This is the template that displays topbar
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>

<div class="reign-header-top">
	<div class="container">

		<div class="header-top-aside header-top-left">
			<?php
			$info_links = get_theme_mod( 'reign_header_topbar_info_links', reign_header_topbar_default_info_links() );
			// Repeater values are stored as JSON strings by the Customizer_Framework (Field::sanitize_json_array). Decode before iterating.
			if ( is_string( $info_links ) ) {
				$decoded    = json_decode( $info_links, true );
				$info_links = is_array( $decoded ) ? $decoded : array();
			}
			if ( ! empty( $info_links ) && is_array( $info_links ) ) {
				foreach ( $info_links as $info_link ) {
					if ( isset( $info_link['link_text'] ) ) {
						$text = $info_link['link_text'];
						if ( ! empty( $info_link['link_url'] ) ) {
							$text = '<a href="' . esc_url( $info_link['link_url'] ) . '">' . esc_html( $text ) . '</a>';
						}
						echo '<span>' . wp_kses_post( $info_link['link_icon'] ) . '' . wp_kses_post( $text ) . '</span>';
					}
				}
			}
			?>
		</div>

		<div class="header-top-aside header-top-right">
			<?php
			$social_links = get_theme_mod( 'reign_header_topbar_social_links', reign_header_topbar_default_social_links() );
			// Repeater values are stored as JSON strings by the Customizer_Framework (Field::sanitize_json_array). Decode before iterating.
			if ( is_string( $social_links ) ) {
				$decoded      = json_decode( $social_links, true );
				$social_links = is_array( $decoded ) ? $decoded : array();
			}
			if ( ! empty( $social_links ) && is_array( $social_links ) ) {
				foreach ( $social_links as $social_link ) {
					if ( isset( $social_link['link_url'] ) && isset( $social_link['link_text'] ) ) {
						echo '<a href="' . esc_url( $social_link['link_url'] ) . '" title="' . esc_attr( $social_link['link_text'] ) . '">' . wp_kses_post( $social_link['link_icon'] ) . '</a>';
					}
				}
			}
			?>
		</div>

	</div>
</div>
