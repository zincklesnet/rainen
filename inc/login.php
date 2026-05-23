<?php
/**
 * Login
 *
 * @package reign
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Kirki' ) ) {
	return;
}

function reign_is_login_page() {
	return in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) );
}

$rg_custom_login = get_theme_mod( 'custom_login_register_toggle' );
if ( $rg_custom_login ) {
	add_action( 'login_enqueue_scripts', 'reign_login_enqueue_scripts' );
}

function reign_login_enqueue_scripts() {
	wp_enqueue_style( 'reign_login', get_template_directory_uri() . '/assets/css/login.min.css', '', REIGN_THEME_VERSION );
}

/**
 * Change login logo url
 *
 * @return void
 */
function reign_login_logo_url() {
	return home_url();
}
add_filter( 'login_headerurl', 'reign_login_logo_url' );

/**
 * Change the login header text to the site name.
 *
 * @return string
 */
function reign_login_title() {
	return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'reign_login_title' );

/**
 * Login page - custom classes
 */
if ( ! function_exists( 'custom_login_classes' ) ) {

	add_filter( 'login_body_class', 'custom_login_classes' );

	/**
	 * Adds custom classes to the login page body based on theme settings.
	 *
	 * @param array $classes Existing body classes.
	 * @return array Modified body classes with custom classes added.
	 */
	function custom_login_classes( $classes ) {
		$rg_custom_login           = get_theme_mod( 'custom_login_register_toggle' );
		$rg_admin_background       = get_theme_mod( 'toggle_custom_background' );
		$custom_login_theme_toggle = get_theme_mod( 'custom_login_theme_toggle', false );
		$custom_login_choose_theme = get_theme_mod( 'custom_login_choose_theme', 'simple' );

		// Define the is_login_page logic within the function.
		$login_url        = wp_login_url();
		$current_url_path = wp_parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
		$login_url_path   = wp_parse_url( $login_url, PHP_URL_PATH );

		$is_login_page = $current_url_path === $login_url_path;

		if ( $rg_custom_login && $is_login_page ) {
			if ( $rg_admin_background && ! $custom_login_theme_toggle ) {
				$classes[] = 'login-split-page rg-login';
			} elseif ( $custom_login_theme_toggle ) {
				$classes[] = 'rg-login login-' . $custom_login_choose_theme;
			} else {
				$classes[] = 'rg-login';
			}
		}

		return $classes;
	}
}

/**
 * Login page - login scripts
 */
function reign_login_scripts() {
	$rg_logoimg = get_theme_mod( 'admin_logo_media' );
	$rg_title   = get_bloginfo();
	?>
	<script>
		jQuery( document ).ready( function () {

			var loginLogoImage = function() {
				jQuery('.login.rg-login #login > h1 > a').each(function() {
					var $this = jQuery(this);
					var bg = $this.css('background-image');
					bgLogo = bg.replace('url(','').replace(')','').replace(/\"/gi, "");
					$this.append( '<img class="bs-cs-login-logo" src="' + bgLogo + '" alt="" aria-hidden="true" />' );
				});
			};

			var loginLogoTitle = function() {
				jQuery('.login.rg-login #login > h1 > a').each(function() {
					var $this = jQuery(this);
					$this.addClass('rg-login-title').html( '<span class="bs-cs-login-title"><?php echo esc_js( $rg_title ); ?></span>' );
				});
			};
			<?php if ( ! empty( $rg_logoimg ) ) { ?>
				loginLogoImage();
			<?php } else { ?>
				loginLogoTitle();
			<?php } ?>

			var loginHeight = function() {

				jQuery( 'body.login.login-split-page #login' ).each(function() {
					var $loginH = jQuery( 'body.login.login-split-page #login' ).height();
					var $winH = jQuery( window ).height();

					if ( $loginH > $winH ) {
						jQuery( 'body.login.login-split-page' ).addClass('login-exh');
					} else {
						jQuery( 'body.login.login-split-page' ).removeClass('login-exh');
					}
				});
			};
			loginHeight();
			jQuery( window ).on( 'resize', function () {
				loginHeight();
			} );

			if (jQuery('.login-container').length==0) {
				jQuery('.language-switcher').appendTo(jQuery('#login'));
			} else{
				jQuery('.language-switcher').appendTo(jQuery('.login-container'));
			}
		} )
	</script>
	<?php
}

/**
 * Login page - custom styling
 */
if ( ! function_exists( 'login_custom_head' ) ) {

	function login_custom_head() {
		$custom_login_theme_toggle = get_theme_mod( 'custom_login_theme_toggle', false );

		$rg_admin_login_background_switch   = get_theme_mod( 'toggle_custom_background' );
		$rg_admin_login_heading_position    = get_theme_mod( 'admin_login_heading_position' );
		$rg_admin_login_background_text     = get_theme_mod( 'login_custom_heading', esc_attr__( 'Welcome back!', 'reign' ) );
		$rg_admin_login_background_textarea = get_theme_mod( 'login_custom_text', esc_html__( 'We\'re thrilled to see you again! Your presence in our community brightens our day. Thank you for returning and being an integral part of our community.', 'reign' ) );
		$rg_admin_login_heading_color       = get_theme_mod( 'admin_login_heading_color' );
		$rg_admin_login_overlay_opacity     = get_theme_mod( 'admin_login_overlay_opacity', 30 );

		if ( $rg_admin_login_background_switch && false === $custom_login_theme_toggle ) {
			if ( $rg_admin_login_heading_position ) {
				$heading_postion_style = 'padding-top: ' . $rg_admin_login_heading_position . '%;';
			} else {
				$heading_postion_style = 'padding-top: 0%;';
			}
			echo '<div class="login-split"><div style="' . $heading_postion_style . '">';
			if ( $rg_admin_login_background_text ) {
				echo wp_kses_post( sprintf( esc_html__( '%s', 'reign' ), $rg_admin_login_background_text ) );
			}
			if ( $rg_admin_login_background_textarea ) {
				echo '<span>';
				echo stripslashes( $rg_admin_login_background_textarea );
				echo '</span>';
			}
			echo '</div><div class="split-overlay"></div></div>';
		}

		// Logo Variables.
		$rg_logoimg                = get_theme_mod( 'admin_logo_media' );
		$rg_logowidth              = get_theme_mod( 'admin_logo_width' );
		$admin_logo_spacing        = get_theme_mod( 'admin_logo_spacing' );
		$rg_login_background_media = get_theme_mod( 'login_background_media' );
		$rg_login_logo             = '';
		$login_title_font_size     = get_theme_mod( 'login_title_font_size' );
		$login_title_color         = get_theme_mod( 'login_title_color' );

		// New Options.
		$custom_login_theme_toggle       = get_theme_mod( 'custom_login_theme_toggle', false );
		$custom_login_choose_theme       = get_theme_mod( 'custom_login_choose_theme', 'simple' );
		$login_custom_background_color   = get_theme_mod( 'login_custom_background_color' );
		$login_custom_background_gallery = get_theme_mod( 'login_custom_background_gallery', REIGN_THEME_URI . '/lib/images/gallery/img-1.jpg' );
		$login_custom_background_image   = get_theme_mod( 'login_custom_background_image' );
		$login_background_video_toggle   = get_theme_mod( 'login_background_video_toggle', false );
		$login_background_video          = get_theme_mod( 'login_background_video' );

		$admin_login_form_transparency         = get_theme_mod( 'admin_login_form_transparency' );
		$login_form_background_image           = get_theme_mod( 'login_form_background_image' );
		$login_form_background_color           = get_theme_mod( 'login_form_background_color' );
		$login_form_width                      = get_theme_mod( 'login_form_width' );
		$login_form_min_height                 = get_theme_mod( 'login_form_min_height' );
		$login_form_radius                     = get_theme_mod( 'login_form_radius' );
		$login_form_shadow                     = get_theme_mod( 'login_form_shadow' );
		$login_form_shadow_opacity             = get_theme_mod( 'login_form_shadow_opacity' );
		$login_form_padding                    = get_theme_mod( 'login_form_padding' );
		$login_form_link_color                 = get_theme_mod( 'login_form_link_color' );
		$login_form_link_hover_color           = get_theme_mod( 'login_form_link_hover_color' );
		$login_form_input_bg_color             = get_theme_mod( 'login_form_input_bg_color' );
		$login_form_input_text_color           = get_theme_mod( 'login_form_input_text_color' );
		$login_form_input_border_color         = get_theme_mod( 'login_form_input_border_color' );
		$login_form_input_width                = get_theme_mod( 'login_form_input_width' );
		$login_form_input_border_radius        = get_theme_mod( 'login_form_input_border_radius' );
		$login_form_input_label_color          = get_theme_mod( 'login_form_input_label_color' );
		$login_form_input_remember_label_color = get_theme_mod( 'login_form_input_remember_label_color' );
		$login_form_input_label_size           = get_theme_mod( 'login_form_input_label_size' );
		$login_form_remember_label_size        = get_theme_mod( 'login_form_remember_label_size' );

		// Button Styling.
		$login_button_bg_color           = get_theme_mod( 'login_button_bg_color' );
		$login_button_border_color       = get_theme_mod( 'login_button_border_color' );
		$login_button_bg_hover_color     = get_theme_mod( 'login_button_bg_hover_color' );
		$login_button_border_hover_color = get_theme_mod( 'login_button_border_hover_color' );
		$login_button_text_color         = get_theme_mod( 'login_button_text_color' );
		$login_button_text_hover_color   = get_theme_mod( 'login_button_text_hover_color' );
		$login_button_width              = get_theme_mod( 'login_button_width' );
		$login_button_top_padding        = get_theme_mod( 'login_button_top_padding' );
		$login_button_bottom_padding     = get_theme_mod( 'login_button_bottom_padding' );
		$login_button_radius             = get_theme_mod( 'login_button_radius' );
		$login_button_shadow             = get_theme_mod( 'login_button_shadow' );
		$login_button_shadow_opacity     = get_theme_mod( 'login_button_shadow_opacity' );
		$login_button_text_size          = get_theme_mod( 'login_button_text_size' );

		// Theme Four.
		$login_modern_heading          = get_theme_mod( 'login_modern_heading', esc_attr__( 'Welcome back!', 'reign' ) );
		$login_modern_text             = get_theme_mod( 'login_modern_text', esc_html__( 'We\'re thrilled to see you again! Your presence in our community brightens our day. Thank you for returning and being an integral part of our community.', 'reign' ) );
		$login_modern_heading_position = get_theme_mod( 'login_modern_heading_position' );
		$login_modern_overlay_opacity  = get_theme_mod( 'login_modern_overlay_opacity', 30 );
		$login_modern_heading_color    = get_theme_mod( 'login_modern_heading_color' );

		if ( true === $custom_login_theme_toggle && 'modern' === $custom_login_choose_theme ) {
			if ( $login_modern_heading_position ) {
				$heading_postion_style = 'padding-top: ' . $login_modern_heading_position . '%;';
			} else {
				$heading_postion_style = 'padding-top: 30%;';
			}
			echo '<div class="login-split"><div style="' . $heading_postion_style . '">';
			if ( $login_modern_heading ) {
				echo wp_kses_post( sprintf( esc_html__( '%s', 'reign' ), $login_modern_heading ) );
			}
			if ( $login_modern_text ) {
				echo '<span>';
				echo stripslashes( $login_modern_text );
				echo '</span>';
			}
			echo '</div><div class="split-overlay"></div></div>';
		}

		// Add video background wrapper.
		if ( $login_background_video_toggle && $login_background_video ) {
			echo '<div class="reign-login-video-background-wrapper">';
			echo '<video id="background-video" autoplay loop muted>';
			echo '<source src="' . esc_url( $login_background_video ) . '" type="video/mp4">';
			echo '</video>';
			echo '</div>';
		}

		echo '<style>';
		if ( ! empty( $rg_logoimg ) ) {
			$rg_login_logo = $rg_logoimg;
		}

		?>
		.login h1 a,
		.login .wp-login-logo a {
			background-image: url(<?php echo esc_url( $rg_login_logo ); ?>);
			background-size: contain;
			background-repeat: no-repeat;
			display: block;
			height: auto;
			margin-bottom: 15px;
			<?php
			if ( ! empty( $rg_logowidth ) ) {
				echo 'width:' . esc_attr( $rg_logowidth ) . 'px ! important;';
			}
			if ( ! empty( $admin_logo_spacing ) ) {
				echo 'margin-bottom:' . esc_attr( $admin_logo_spacing ) . 'px;';
			}
			?>
		}

		.login #login h1 img.bs-cs-login-logo.private-on {
			<?php
			if ( $rg_logowidth ) {
				echo 'width:' . esc_attr( $rg_logowidth ) . 'px ! important;';
			}
			?>
		}	
		<?php

		if ( ! empty( $login_title_font_size ) ) {
			?>
			body.login #login .bs-cs-login-title {
				font-size: <?php echo esc_attr( $login_title_font_size ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_title_color ) ) {
			?>
			body.login #login h1 a.rg-login-title {
				color: <?php echo esc_attr( $login_title_color ); ?> !important;
			}
			<?php
		}

		if ( $rg_admin_login_background_switch && $rg_login_background_media ) {
			?>
			.login-split {
				background-image: url(<?php echo esc_url( $rg_login_background_media ); ?>);
				background-size: cover;
				background-position: 50% 50%;
			}	
			<?php
		}

		if ( $rg_admin_login_overlay_opacity ) {
			?>
			@media( min-width: 992px ) {
				body.login.login-split-page .login-split .split-overlay {
					opacity: <?php echo esc_attr( $rg_admin_login_overlay_opacity / 100 ); ?>;
				}
			}
			<?php
		}
		if ( $rg_admin_login_heading_color ) {
			?>
			@media( min-width: 992px ) {
				body.login.login-split-page .login-split div {
					color: <?php echo esc_attr( $rg_admin_login_heading_color ); ?>;
				}
			}	
			<?php
		}

		// New Themes Styles.
		if ( ! empty( $login_custom_background_color ) ) {
			?>
			body.login {
				background-color: <?php echo esc_attr( $login_custom_background_color ); ?> !important;
				background-image: none !important;
			}
			<?php
		}

		if ( $custom_login_theme_toggle && $custom_login_choose_theme && $login_custom_background_gallery ) {
			?>
			body.login {
				background-image: url(<?php echo esc_url( $login_custom_background_gallery ); ?>);
				background-size: cover;
				background-position: 50% 50%;
				background-repeat: no-repeat;
			}	
			<?php
		}

		if ( $custom_login_theme_toggle && $custom_login_choose_theme && $login_custom_background_image ) {
			?>
			body.login {
				background-image: url(<?php echo esc_url( $login_custom_background_image ); ?>);
				background-size: cover;
				background-position: 50% 50%;
				background-repeat: no-repeat;
			}	
			<?php
		}

		if ( $login_modern_overlay_opacity ) {
			?>
			@media( min-width: 992px ) {
				.login.rg-login.login-modern .login-split .split-overlay {
					opacity: <?php echo esc_attr( $login_modern_overlay_opacity / 100 ); ?>;
				}
			}
			<?php
		}
		if ( $login_modern_heading_color ) {
			?>
			@media( min-width: 992px ) {
				.login.rg-login.login-modern .login-split div {
					color: <?php echo esc_attr( $login_modern_heading_color ); ?>;
				}
			}	
			<?php
		}

		// Form.
		if ( false === $admin_login_form_transparency ) {
			?>
			body.login.login-simple #login {
				background-color: #fff;
			}
			<?php
		}

		if ( ! empty( $login_form_background_color && false === $admin_login_form_transparency ) ) {
			?>
			body.login #login {
				background-color: <?php echo esc_attr( $login_form_background_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_background_image && false === $admin_login_form_transparency ) ) {
			?>
			body.login #login {
				background-image: url(<?php echo esc_url( $login_form_background_image ); ?>);
				background-size: cover;
				background-position: 50% 50%;
				background-repeat: no-repeat;
			}
			<?php
		}

		if ( ! empty( $login_form_width ) ) {
			?>
			@media( min-width: 992px ) {
				body.login #login {
					width: 100%;
					max-width: <?php echo esc_attr( $login_form_width ); ?> !important;
				}
			}
			<?php
		}

		if ( ! empty( $login_form_min_height ) ) {
			?>
			@media( min-width: 992px ) {
				body.login #login {
					min-height: <?php echo esc_attr( $login_form_min_height ); ?> !important;
				}
			}
			<?php
		}

		if ( ! empty( $login_form_radius ) ) {
			?>
			body.login #login {
				border-radius: <?php echo esc_attr( $login_form_radius ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_shadow && $login_form_shadow_opacity ) ) {
			?>
			body.login #login {
				box-shadow: 0 0 <?php echo esc_attr( $login_form_shadow ); ?> rgb(0, 0, 0, <?php echo esc_attr( $login_form_shadow_opacity ); ?> ) !important;
			}
			<?php
		}

		if ( ! empty( $login_form_padding ) ) {
			?>
			body.login #login {
				padding: <?php echo esc_attr( $login_form_padding ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_link_color ) ) {
			?>
			body.login #login #nav a,
			body.login #login #backtoblog a,
			body.login #login a.privacy-policy-link {
				color: <?php echo esc_attr( $login_form_link_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_link_hover_color ) ) {
			?>
			body.login #login #nav a:hover,
			body.login #login #backtoblog a:hover,
			body.login #login a.privacy-policy-link:hover {
				color: <?php echo esc_attr( $login_form_link_hover_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_input_bg_color ) ) {
			?>
			body.login #login form .input,
			body.login #login input[type="text"] {
				background: <?php echo esc_attr( $login_form_input_bg_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_input_text_color ) ) {
			?>
			body.login #login form .input,
			body.login #login input[type="text"] {
				color: <?php echo esc_attr( $login_form_input_text_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_input_border_color ) ) {
			?>
			body.login #login form .input,
			body.login #login input[type="text"] {
				border-color: <?php echo esc_attr( $login_form_input_border_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_input_width ) ) {
			?>
			body.login #login form .input,
			body.login #login input[type="text"] {
				width: <?php echo esc_attr( $login_form_input_width ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_input_border_radius ) ) {
			?>
			body.login #login form .input,
			body.login #login input[type="text"] {
				border-radius: <?php echo esc_attr( $login_form_input_border_radius ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_input_label_color ) ) {
			?>
			body.login #login form label,
			body.login .language-switcher .dashicons {
				color: <?php echo esc_attr( $login_form_input_label_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_input_remember_label_color ) ) {
			?>
			body.login #login form .forgetmenot label {
				color: <?php echo esc_attr( $login_form_input_remember_label_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_input_label_size ) ) {
			?>
			body.login #login form label {
				font-size: <?php echo esc_attr( $login_form_input_label_size ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_form_remember_label_size ) ) {
			?>
			body.login #login form .forgetmenot label {
				font-size: <?php echo esc_attr( $login_form_remember_label_size ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_button_bg_color ) ) {
			?>
			body.login #login .button-primary {
				background: <?php echo esc_attr( $login_button_bg_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_button_border_color ) ) {
			?>
			body.login #login .button-primary {
				border-color: <?php echo esc_attr( $login_button_border_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_button_bg_hover_color ) ) {
			?>
			body.login #login .button-primary:hover {
				background: <?php echo esc_attr( $login_button_bg_hover_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_button_border_hover_color ) ) {
			?>
			body.login #login .button-primary:hover {
				border-color: <?php echo esc_attr( $login_button_border_hover_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_button_text_color ) ) {
			?>
			body.login #login .button-primary {
				color: <?php echo esc_attr( $login_button_text_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_button_text_hover_color ) ) {
			?>
			body.login #login .button-primary:hover {
				color: <?php echo esc_attr( $login_button_text_hover_color ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_button_width ) ) {
			?>
			body.login #login .button-primary {
				width: <?php echo esc_attr( $login_button_width ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_button_top_padding ) ) {
			?>
			body.login #login .button-primary {
				padding-top: <?php echo esc_attr( $login_button_top_padding ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_button_bottom_padding ) ) {
			?>
			body.login #login .button-primary {
				padding-bottom: <?php echo esc_attr( $login_button_bottom_padding ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_button_radius ) ) {
			?>
			body.login #login .button-primary {
				border-radius: <?php echo esc_attr( $login_button_radius ); ?> !important;
			}
			<?php
		}

		if ( ! empty( $login_button_shadow ) && $login_button_shadow_opacity ) {
			?>
			body.login #login .button-primary {
				box-shadow: 0 0 <?php echo esc_attr( $login_button_shadow ); ?> rgb(0, 0, 0, <?php echo esc_attr( $login_button_shadow_opacity ); ?> ) !important;
			}
			<?php
		}

		if ( ! empty( $login_button_text_size ) ) {
			?>
			body.login #login .button-primary {
				font-size: <?php echo esc_attr( $login_button_text_size ); ?> !important;
			}
			<?php
		}

		echo '</style>';
	}
}

function reign_theme_login_load() {
	$rg_custom_login = get_theme_mod( 'custom_login_register_toggle' );

	if ( $rg_custom_login ) {
		add_action( 'login_head', 'reign_login_scripts', 150 );
		add_action( 'login_head', 'login_custom_head', 150 );
	}

	add_action(
		'login_enqueue_scripts',
		function () {
			if ( ! wp_script_is( 'jquery', 'done' ) ) {
				wp_enqueue_script( 'jquery' );
			}
		},
		1
	);
}
add_action( 'init', 'reign_theme_login_load' );

add_action(
	'customize_controls_print_styles',
	function () {
		?>
		<style>
			.customize-control-kirki-sortable ul.ui-sortable li.invisible {
				visibility: visible;
			}
		</style>
		<?php
	}
);
