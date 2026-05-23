<?php
/**
 * The template for displaying reign custom styles
 *
 * @package Reign
 */

/**
 * Reign custom styles
 */
function reign_get_custom_styles() {

	if ( class_exists( 'PeepSo' ) ) {
		global $wbtm_reign_settings;

		$profile_cover_height = isset( $wbtm_reign_settings['reign_peepsoextender']['profile_cover_height'] ) ? $wbtm_reign_settings['reign_peepsoextender']['profile_cover_height'] : '24';
		$group_cover_height   = isset( $wbtm_reign_settings['reign_peepsoextender']['group_cover_height'] ) ? $wbtm_reign_settings['reign_peepsoextender']['group_cover_height'] : '24';

		// PeepSo Cover Image Height.
		return <<<CSS
        @media (min-width: 61.25em) {
            .ps-focus__cover:before,
            .ps-focus--small .ps-focus__cover:before {
                padding-top: {$profile_cover_height}%;
            }
            .ps-group__profile-focus .ps-focus__cover:before {
                padding-top: {$group_cover_height}%;
            }
        }
CSS;
	}

	if ( class_exists( 'BuddyPress' ) ) {

		$register_split_view = get_theme_mod( 'register_split_view' );

		if ( $register_split_view ) {

			$register_heading_color    = get_theme_mod( 'register_heading_color' );
			$register_background_media = get_theme_mod( 'register_background_media' );

			if ( ! empty( $rg_logoimg ) ) {
				$rg_login_logo = $rg_logoimg;
			}

			if ( $register_background_media ) {
				// Register page custom css.
				return <<<CSS

            body.buddypress.activate.login-split-page .login-split,
            body.buddypress.register.login-split-page .login-split {
                background-image: url($register_background_media);
                background-size: cover;
                background-position: 50% 50%;
            }

            body.buddypress.activate.login-split-page .login-split div,
            body.buddypress.register.login-split-page .login-split div {
                color: $register_heading_color;
            }
            CSS;
			}
		}
	}
}
