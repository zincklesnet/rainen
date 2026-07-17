<?php
/**
 * Reign Customizer WP Login Screen Fields
 *
 * Ported from `lib/kirki-addon/options/wp-login-screen/class-reign-kirki-wp-login-screen.php`.
 * Kirki removal — Phase 1 atomic sweep. Args arrays preserved verbatim.
 *
 * NOTE: Several settings here use `'transport' => 'postMessage'` without an
 * `'output'` map (live-preview audit issue per onboarding lesson #21). Per
 * Partition B sweep instructions, transport values are preserved as-is; the
 * audit/fix is deferred to a later phase.
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_WP_Login_Screen_Fields' ) ) :

	/**
	 * @class Reign_Customizer_WP_Login_Screen_Fields
	 */
	class Reign_Customizer_WP_Login_Screen_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_WP_Login_Screen_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_WP_Login_Screen_Fields Instance.
		 *
		 * Ensures only one instance is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_WP_Login_Screen_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'init', array( $this, 'add_panels_and_sections' ) );
			add_action( 'init', array( $this, 'add_fields' ) );
		}

		/**
		 * Add panels and sections
		 */
		public function add_panels_and_sections() {

			\Reign\Customizer_Framework\Panel::add(
				'reign_login_register_screen_panel',
				array(
					'priority'    => 150,
					'title'       => esc_html__( 'Branded Login & Register', 'reign' ),
					'description' => esc_html__( 'Replace the default WordPress login screen with your own branding - logo, background, form and button styling.', 'reign' ),
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_login_logo_section',
				array(
					'priority'    => 10,
					'title'       => esc_html__( 'WP Login / Register', 'reign' ),
					'description' => esc_html__( 'Turn the custom login screen on and set its logo.', 'reign' ),
					'panel'       => 'reign_login_register_screen_panel',
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_login_theme_section',
				array(
					'priority'    => 20,
					'title'       => esc_html__( 'Themes', 'reign' ),
					'description' => esc_html__( 'Pick a ready-made login screen design as a starting point.', 'reign' ),
					'panel'       => 'reign_login_register_screen_panel',
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_login_background_section',
				array(
					'priority'    => 30,
					'title'       => esc_html__( 'Background', 'reign' ),
					'description' => esc_html__( 'Background image, color, video or overlay for the login screen.', 'reign' ),
					'panel'       => 'reign_login_register_screen_panel',
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_login_customize_form_section',
				array(
					'priority'    => 40,
					'title'       => esc_html__( 'Customize Login Form', 'reign' ),
					'description' => esc_html__( 'Colors, spacing and field styling for the login form box.', 'reign' ),
					'panel'       => 'reign_login_register_screen_panel',
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_login_button_section',
				array(
					'priority'    => 50,
					'title'       => esc_html__( 'Customize Button', 'reign' ),
					'description' => esc_html__( 'Colors and styling for the login form submit button.', 'reign' ),
					'panel'       => 'reign_login_register_screen_panel',
				)
			);

			// Register Page. Gated on BuddyPress to match the field registrations
			// below (was `if ( 'BuddyPress' )`, a constant-true string literal,
			// which left an empty section on non-BuddyPress sites).
			if ( class_exists( 'BuddyPress' ) ) {
				\Reign\Customizer_Framework\Section::add(
					'reign_community_register_section',
					array(
						'priority'    => 60,
						'title'       => esc_html__( 'Community Register', 'reign' ),
						'description' => esc_html__( 'Style the BuddyPress registration page.', 'reign' ),
						'panel'       => 'reign_login_register_screen_panel',
					)
				);
			}
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			$default_value_set = reign_get_customizer_default_value_set();

			// Logo.
			\Reign\Customizer_Framework\Field::add(
				'switch',
				array(
					'settings'    => 'custom_login_register_toggle',
					'label'       => esc_html__( 'Custom Login Screen', 'reign' ),
					'description' => esc_html__( 'Toggle the custom login page', 'reign' ),
					'section'     => 'reign_login_logo_section',
					'default'     => 'off',
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'custom_login_register_toggle_divider',
					'section'         => 'reign_login_logo_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_title_font_size',
					'label'           => esc_html__( 'Login Title Font Size', 'reign' ),
					'description'     => esc_html__( 'Default value is 26px.', 'reign' ),
					'section'         => 'reign_login_logo_section',
					'priority'        => 10,
					'default'         => '26px',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_title_font_size_divider',
					'section'         => 'reign_login_logo_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_title_color',
					'label'           => esc_html__( 'Login Title Color', 'reign' ),
					'section'         => 'reign_login_logo_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_title_color_divider',
					'section'         => 'reign_login_logo_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'image',
				array(
					'settings'        => 'admin_logo_media',
					'label'           => esc_html__( 'Custom Logo', 'reign' ),
					'description'     => esc_html__( 'Display a custom logo in place of the default WordPress logo.', 'reign' ),
					'section'         => 'reign_login_logo_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'admin_logo_media_divider',
					'section'         => 'reign_login_logo_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'slider',
				array(
					'settings'        => 'admin_logo_width',
					'label'           => esc_html__( 'Custom Logo Width', 'reign' ),
					'description'     => esc_html__( 'Set custom logo width (px).', 'reign' ),
					'section'         => 'reign_login_logo_section',
					'priority'        => 10,
					'default'         => '145px',
					'transport'       => 'postMessage',
					'choices'         => array(
						'min'  => 50,
						'max'  => 300,
						'step' => 1,
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'admin_logo_width_divider',
					'section'         => 'reign_login_logo_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'slider',
				array(
					'settings'        => 'admin_logo_spacing',
					'label'           => esc_html__( 'Custom Logo Space Bottom', 'reign' ),
					'description'     => esc_html__( 'Set custom logo bottom spacing (px).', 'reign' ),
					'section'         => 'reign_login_logo_section',
					'priority'        => 10,
					'default'         => 0,
					'transport'       => 'postMessage',
					'choices'         => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'admin_logo_spacing_divider',
					'section'         => 'reign_login_logo_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'switch',
				array(
					'settings'        => 'toggle_custom_background',
					'label'           => esc_html__( 'Toggle Custom Background', 'reign' ),
					'description'     => esc_html__( 'Set custom background design for login page.', 'reign' ),
					'section'         => 'reign_login_logo_section',
					'default'         => 'off',
					'priority'        => 10,
					'choices'         => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'toggle_custom_background_divider',
					'section'         => 'reign_login_logo_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'toggle_custom_background',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'image',
				array(
					'settings'        => 'login_background_media',
					'label'           => esc_html__( 'Background Image', 'reign' ),
					'description'     => esc_html__( 'Set custom background image.', 'reign' ),
					'section'         => 'reign_login_logo_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'toggle_custom_background',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_background_media_divider',
					'section'         => 'reign_login_logo_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'toggle_custom_background',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'text',
				array(
					'settings'        => 'login_custom_heading',
					'label'           => esc_html__( 'Custom Heading', 'reign' ),
					'description'     => esc_html__( 'Display a custom title.', 'reign' ),
					'section'         => 'reign_login_logo_section',
					'priority'        => 10,
					'default'         => esc_html__( 'Welcome back!', 'reign' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'toggle_custom_background',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_custom_heading_divider',
					'section'         => 'reign_login_logo_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'toggle_custom_background',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'textarea',
				array(
					'settings'        => 'login_custom_text',
					'label'           => esc_html__( 'Custom Text', 'reign' ),
					'description'     => esc_html__( 'Display a custom text.', 'reign' ),
					'section'         => 'reign_login_logo_section',
					'priority'        => 10,
					'default'         => esc_html__( 'We\'re thrilled to see you again! Your presence brightens our day. Thank you for returning and being an integral part of our community.', 'reign' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'toggle_custom_background',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_custom_text_divider',
					'section'         => 'reign_login_logo_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'toggle_custom_background',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'slider',
				array(
					'settings'        => 'admin_login_heading_position',
					'label'           => esc_html__( 'Custom Heading Position', 'reign' ),
					'description'     => esc_html__( 'Set custom text position.', 'reign' ),
					'section'         => 'reign_login_logo_section',
					'priority'        => 10,
					'default'         => 0,
					'transport'       => 'postMessage',
					'choices'         => array(
						'min'  => 0,
						'max'  => 90,
						'step' => 1,
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'toggle_custom_background',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'admin_login_heading_position_divider',
					'section'         => 'reign_login_logo_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'toggle_custom_background',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'slider',
				array(
					'settings'        => 'admin_login_overlay_opacity',
					'label'           => esc_html__( 'Overlay Opacity', 'reign' ),
					'description'     => esc_html__( 'Set overlay opacity value between 0 and 100%', 'reign' ),
					'section'         => 'reign_login_logo_section',
					'priority'        => 10,
					'default'         => 30,
					'transport'       => 'postMessage',
					'choices'         => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'toggle_custom_background',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'admin_login_overlay_opacity_divider',
					'section'         => 'reign_login_logo_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'toggle_custom_background',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'admin_login_heading_color',
					'label'           => esc_html__( 'Custom Heading Color', 'reign' ),
					'description'     => esc_html__( 'Set custom text color.', 'reign' ),
					'section'         => 'reign_login_logo_section',
					'default'         => '#ffffff',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'toggle_custom_background',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			// Login Theme.
			\Reign\Customizer_Framework\Field::add(
				'switch',
				array(
					'settings'        => 'custom_login_theme_toggle',
					'label'           => esc_html__( 'Set New Theme', 'reign' ),
					'description'     => esc_html__( 'Set new custom login page theme.', 'reign' ),
					'section'         => 'reign_login_theme_section',
					'default'         => 'off',
					'priority'        => 10,
					'choices'         => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'custom_login_theme_toggle_divider',
					'section'         => 'reign_login_theme_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'radio_image',
				array(
					'settings'        => 'custom_login_choose_theme',
					'label'           => esc_html__( 'Choose Theme', 'reign' ),
					'section'         => 'reign_login_theme_section',
					'default'         => 'simple',
					'priority'        => 10,
					'choices'         => array(
						'simple'   => REIGN_THEME_URI . '/lib/images/thumbnail/default-1.jpg',
						'minimal'  => REIGN_THEME_URI . '/lib/images/thumbnail/default-2.jpg',
						'creative' => REIGN_THEME_URI . '/lib/images/thumbnail/default-3.jpg',
						'modern'   => REIGN_THEME_URI . '/lib/images/thumbnail/default-4.jpg',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'custom-heading-divider',
					'label'           => esc_html__( 'Left Side Content:', 'reign' ),
					'section'         => 'reign_login_theme_section',
					'default'         => '<hr>',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_choose_theme',
							'operator' => '==',
							'value'    => 'modern',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'text',
				array(
					'settings'        => 'login_modern_heading',
					'label'           => esc_html__( 'Custom Heading', 'reign' ),
					'description'     => esc_html__( 'Display a custom title.', 'reign' ),
					'section'         => 'reign_login_theme_section',
					'priority'        => 10,
					'default'         => 'Welcome back!',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_choose_theme',
							'operator' => '==',
							'value'    => 'modern',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_modern_heading_divider',
					'section'         => 'reign_login_theme_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_choose_theme',
							'operator' => '==',
							'value'    => 'modern',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'textarea',
				array(
					'settings'        => 'login_modern_text',
					'label'           => esc_html__( 'Custom Text', 'reign' ),
					'description'     => esc_html__( 'Display a custom text.', 'reign' ),
					'section'         => 'reign_login_theme_section',
					'priority'        => 10,
					'default'         => esc_html__( 'We\'re thrilled to see you again! Your presence brightens our day. Thank you for returning and being an integral part of our community.', 'reign' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_choose_theme',
							'operator' => '==',
							'value'    => 'modern',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_modern_text_divider',
					'section'         => 'reign_login_theme_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_choose_theme',
							'operator' => '==',
							'value'    => 'modern',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'slider',
				array(
					'settings'        => 'login_modern_heading_position',
					'label'           => esc_html__( 'Custom Heading Position', 'reign' ),
					'description'     => esc_html__( 'Set custom text position.', 'reign' ),
					'section'         => 'reign_login_theme_section',
					'priority'        => 10,
					'default'         => 30,
					'transport'       => 'postMessage',
					'choices'         => array(
						'min'  => 5,
						'max'  => 90,
						'step' => 1,
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_choose_theme',
							'operator' => '==',
							'value'    => 'modern',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_modern_heading_position_divider',
					'section'         => 'reign_login_theme_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_choose_theme',
							'operator' => '==',
							'value'    => 'modern',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'slider',
				array(
					'settings'        => 'login_modern_overlay_opacity',
					'label'           => esc_html__( 'Left Side Overlay Opacity', 'reign' ),
					'description'     => esc_html__( 'Set overlay opacity value between 0 and 100%', 'reign' ),
					'section'         => 'reign_login_theme_section',
					'priority'        => 10,
					'default'         => 30,
					'transport'       => 'postMessage',
					'choices'         => array(
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_choose_theme',
							'operator' => '==',
							'value'    => 'modern',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_modern_overlay_opacity_divider',
					'section'         => 'reign_login_theme_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_choose_theme',
							'operator' => '==',
							'value'    => 'modern',
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_modern_heading_color',
					'label'           => esc_html__( 'Custom Content Color', 'reign' ),
					'description'     => esc_html__( 'Set custom text color.', 'reign' ),
					'section'         => 'reign_login_theme_section',
					'default'         => '#ffffff',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_choose_theme',
							'operator' => '==',
							'value'    => 'modern',
						),
					),
				)
			);

			// Login Background.
			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_custom_background_color',
					'label'           => esc_html__( 'Custom Background Color', 'reign' ),
					'section'         => 'reign_login_background_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_custom_background_color_divider',
					'section'         => 'reign_login_background_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'radio_image',
				array(
					'settings'        => 'login_custom_background_gallery',
					'label'           => esc_html__( 'Background Gallery', 'reign' ),
					'section'         => 'reign_login_background_section',
					'default'         => REIGN_THEME_URI . '/lib/images/gallery/img-1.jpg',
					'priority'        => 10,
					'choices'         => array(
						REIGN_THEME_URI . '/lib/images/gallery/img-1.jpg' => REIGN_THEME_URI . '/lib/images/gallery/img-1.jpg',
						REIGN_THEME_URI . '/lib/images/gallery/img-2.jpg' => REIGN_THEME_URI . '/lib/images/gallery/img-2.jpg',
						REIGN_THEME_URI . '/lib/images/gallery/img-3.jpg' => REIGN_THEME_URI . '/lib/images/gallery/img-3.jpg',
						REIGN_THEME_URI . '/lib/images/gallery/img-4.jpg' => REIGN_THEME_URI . '/lib/images/gallery/img-4.jpg',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_custom_background_gallery_divider',
					'section'         => 'reign_login_background_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'image',
				array(
					'settings'        => 'login_custom_background_image',
					'label'           => esc_html__( 'Set Custom Background Image', 'reign' ),
					'description'     => esc_html__( 'Set custom background image.', 'reign' ),
					'section'         => 'reign_login_background_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_custom_background_image_divider',
					'section'         => 'reign_login_background_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'switch',
				array(
					'settings'        => 'login_background_video_toggle',
					'label'           => esc_html__( 'Enable Background Video?', 'reign' ),
					'description'     => esc_html__( 'Set custom video background design.', 'reign' ),
					'section'         => 'reign_login_background_section',
					'default'         => 'off',
					'priority'        => 10,
					'choices'         => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'login_background_video_toggle_divider',
					'section'         => 'reign_login_background_section',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'login_background_video_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'upload',
				array(
					'settings'        => 'login_background_video',
					'label'           => esc_html__( 'Background Video', 'reign' ),
					'description'     => esc_html__( 'Set custom video background design.', 'reign' ),
					'section'         => 'reign_login_background_section',
					'default'         => '',
					'priority'        => 10,
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_theme_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'login_background_video_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			// Login Form.
			\Reign\Customizer_Framework\Field::add(
				'switch',
				array(
					'settings'        => 'admin_login_form_transparency',
					'label'           => esc_html__( 'Enable Form Transparency', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'default'         => 'off',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'image',
				array(
					'settings'        => 'login_form_background_image',
					'label'           => esc_html__( 'Form Background Image', 'reign' ),
					'description'     => esc_html__( 'Set form background image.', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'admin_login_form_transparency',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_form_background_color',
					'label'           => esc_html__( 'Form Background Color', 'reign' ),
					'description'     => esc_html__( 'Set form background color.', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'admin_login_form_transparency',
							'operator' => '==',
							'value'    => false,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings' => 'login_form_background_color_divider',
					'section'  => 'reign_login_customize_form_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_form_width',
					'label'           => esc_html__( 'Form Width', 'reign' ),
					'description'     => esc_html__( 'Default value is 350px.', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_form_min_height',
					'label'           => esc_html__( 'Form Minimum Height', 'reign' ),
					'description'     => esc_html__( 'Default value is 200px.', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_form_radius',
					'label'           => esc_html__( 'Form Radius', 'reign' ),
					'description'     => esc_html__( 'Default value is 5px.', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_form_shadow',
					'label'           => esc_html__( 'Form Shadow', 'reign' ),
					'description'     => esc_html__( 'Default value is 15px.', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_form_shadow_opacity',
					'label'           => esc_html__( 'Form Shadow Opacity', 'reign' ),
					'description'     => esc_html__( 'Default shadow opacity value is 80%.', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_form_padding',
					'section'         => 'reign_login_customize_form_section',
					'label'           => esc_html__( 'Form Padding', 'reign' ),
					'description'     => esc_html__( 'Default value is 26px.', 'reign' ),
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'custom-input-field-divider',
					'label'           => esc_html__( 'Input Fields:', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'default'         => '<hr>',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_form_link_color',
					'label'           => esc_html__( 'Login Form Link Color', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_form_link_hover_color',
					'label'           => esc_html__( 'Login Form Link Hover Color', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_form_input_bg_color',
					'label'           => esc_html__( 'Input Field Background Color', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_form_input_text_color',
					'label'           => esc_html__( 'Input Field Text Color', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_form_input_border_color',
					'label'           => esc_html__( 'Input Field Border Color', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_form_input_width',
					'label'           => esc_html__( 'Input Text Field Width', 'reign' ),
					'description'     => esc_html__( 'Default value is 100%.', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_form_input_border_radius',
					'label'           => esc_html__( 'Input Text Field Border Radius', 'reign' ),
					'description'     => esc_html__( 'Default value is 3px.', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings'        => 'custom-input-field-label_divider',
					'label'           => esc_html__( 'Input Fields Labels:', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'default'         => '<hr>',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_form_input_label_color',
					'label'           => esc_html__( 'Input Field Label Color', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_form_input_remember_label_color',
					'label'           => esc_html__( 'Remember me Label Color', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_form_input_label_size',
					'label'           => esc_html__( 'Input Field Label Font Size', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'description'     => esc_html__( 'Default value is 14px.', 'reign' ),
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_form_remember_label_size',
					'label'           => esc_html__( 'Remember Me Font Size', 'reign' ),
					'section'         => 'reign_login_customize_form_section',
					'description'     => esc_html__( 'Default value is 14px.', 'reign' ),
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			// Login Buttons.
			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_button_bg_color',
					'label'           => esc_html__( 'Button Background Color', 'reign' ),
					'section'         => 'reign_login_button_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_button_border_color',
					'label'           => esc_html__( 'Button Border Color', 'reign' ),
					'section'         => 'reign_login_button_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_button_bg_hover_color',
					'label'           => esc_html__( 'Button Background Color (Hover)', 'reign' ),
					'section'         => 'reign_login_button_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_button_border_hover_color',
					'label'           => esc_html__( 'Button Border Color (Hover)', 'reign' ),
					'section'         => 'reign_login_button_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_button_text_color',
					'label'           => esc_html__( 'Button Text Color', 'reign' ),
					'section'         => 'reign_login_button_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'color',
				array(
					'settings'        => 'login_button_text_hover_color',
					'label'           => esc_html__( 'Button Text Color (Hover)', 'reign' ),
					'section'         => 'reign_login_button_section',
					'default'         => '',
					'priority'        => 10,
					'choices'         => array( 'alpha' => true ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings' => 'login_button_text_hover_color_divider',
					'section'  => 'reign_login_button_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_button_width',
					'label'           => esc_html__( 'Button Width', 'reign' ),
					'description'     => esc_html__( 'Default value is 100%.', 'reign' ),
					'section'         => 'reign_login_button_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_button_top_padding',
					'label'           => esc_html__( 'Button Top Padding', 'reign' ),
					'description'     => esc_html__( 'Default value is 13px.', 'reign' ),
					'section'         => 'reign_login_button_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_button_bottom_padding',
					'label'           => esc_html__( 'Button Bottom Padding', 'reign' ),
					'description'     => esc_html__( 'Default value is 13px.', 'reign' ),
					'section'         => 'reign_login_button_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_button_radius',
					'label'           => esc_html__( 'Button Radius', 'reign' ),
					'description'     => esc_html__( 'Default value is 3px.', 'reign' ),
					'section'         => 'reign_login_button_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_button_shadow',
					'label'           => esc_html__( 'Button Shadow', 'reign' ),
					'description'     => esc_html__( 'Default value is 0px.', 'reign' ),
					'section'         => 'reign_login_button_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_button_shadow_opacity',
					'label'           => esc_html__( 'Button Shadow Opacity', 'reign' ),
					'description'     => esc_html__( 'Default shadow opacity value is 80%.', 'reign' ),
					'section'         => 'reign_login_button_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'dimension',
				array(
					'settings'        => 'login_button_text_size',
					'label'           => esc_html__( 'Button Text Size', 'reign' ),
					'description'     => esc_html__( 'Default value is 13px.', 'reign' ),
					'section'         => 'reign_login_button_section',
					'priority'        => 10,
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'custom_login_register_toggle',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			// Register Page.
			if ( class_exists( 'BuddyPress' ) ) {
				\Reign\Customizer_Framework\Field::add(
					'switch',
					array(
						'settings'    => 'register_split_view',
						'label'       => esc_html__( 'Toggle Split View', 'reign' ),
						'description' => esc_html__( 'Set split view for register page.', 'reign' ),
						'section'     => 'reign_community_register_section',
						'default'     => 'off',
						'priority'    => 10,
						'choices'     => array(
							'on'  => esc_html__( 'Enable', 'reign' ),
							'off' => esc_html__( 'Disable', 'reign' ),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings'        => 'register_split_view_divider',
						'section'         => 'reign_community_register_section',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'register_split_view',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'image',
					array(
						'settings'        => 'register_background_media',
						'label'           => esc_html__( 'Background Image', 'reign' ),
						'description'     => esc_html__( 'Set custom background image.', 'reign' ),
						'section'         => 'reign_community_register_section',
						'priority'        => 10,
						'default'         => '',
						'transport'       => 'postMessage',
						'active_callback' => array(
							array(
								'setting'  => 'register_split_view',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings'        => 'register_background_media_divider',
						'section'         => 'reign_community_register_section',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'register_split_view',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'text',
					array(
						'settings'        => 'register_custom_heading',
						'label'           => esc_html__( 'Custom Heading', 'reign' ),
						'description'     => esc_html__( 'Display a custom title.', 'reign' ),
						'section'         => 'reign_community_register_section',
						'priority'        => 10,
						'default'         => esc_html__( 'Let\'s stay connected!', 'reign' ),
						'transport'       => 'postMessage',
						'active_callback' => array(
							array(
								'setting'  => 'register_split_view',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings'        => 'register_custom_heading_divider',
						'section'         => 'reign_community_register_section',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'register_split_view',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'textarea',
					array(
						'settings'        => 'register_custom_text',
						'label'           => esc_html__( 'Custom Text', 'reign' ),
						'description'     => esc_html__( 'Display a custom text.', 'reign' ),
						'section'         => 'reign_community_register_section',
						'priority'        => 10,
						'default'         => esc_html__( 'Join our community today to broaden your network and connect with new people!', 'reign' ),
						'transport'       => 'postMessage',
						'active_callback' => array(
							array(
								'setting'  => 'register_split_view',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings'        => 'register_custom_text_divider',
						'section'         => 'reign_community_register_section',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'register_split_view',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'slider',
					array(
						'settings'        => 'register_heading_position',
						'label'           => esc_html__( 'Custom Heading Position', 'reign' ),
						'description'     => esc_html__( 'Set custom text position.', 'reign' ),
						'section'         => 'reign_community_register_section',
						'priority'        => 10,
						'default'         => 0,
						'transport'       => 'postMessage',
						'choices'         => array(
							'min'  => 0,
							'max'  => 90,
							'step' => 1,
						),
						'active_callback' => array(
							array(
								'setting'  => 'register_split_view',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings'        => 'register_heading_position_divider',
						'section'         => 'reign_community_register_section',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'register_split_view',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'slider',
					array(
						'settings'        => 'register_overlay_opacity',
						'label'           => esc_html__( 'Overlay Opacity', 'reign' ),
						'description'     => esc_html__( 'Set overlay opacity value between 0 and 100%', 'reign' ),
						'section'         => 'reign_community_register_section',
						'priority'        => 10,
						'default'         => 70,
						'transport'       => 'postMessage',
						'output'          => array(
							array(
								'element'  => 'body.buddypress.activate.login-split-page .login-split .split-overlay, body.buddypress.register.login-split-page .login-split .split-overlay',
								'property' => 'opacity',
								'units'    => '%', // use to append px, %, etc.
							),
						),
						'choices'         => array(
							'min'  => 0,
							'max'  => 100,
							'step' => 1,
						),
						'active_callback' => array(
							array(
								'setting'  => 'register_split_view',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'custom',
					array(
						'settings'        => 'register_overlay_opacity_divider',
						'section'         => 'reign_community_register_section',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'register_split_view',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

				\Reign\Customizer_Framework\Field::add(
					'color',
					array(
						'settings'        => 'register_heading_color',
						'label'           => esc_html__( 'Custom Heading Color', 'reign' ),
						'description'     => esc_html__( 'Set custom text color.', 'reign' ),
						'section'         => 'reign_community_register_section',
						'default'         => '#ffffff',
						'priority'        => 10,
						'choices'         => array( 'alpha' => true ),
						'transport'       => 'postMessage',
						'output'          => array(
							array(
								'element' => 'body.buddypress.activate.login-split-page .login-split div, body.buddypress.register.login-split-page .login-split div',
							),
						),
						'active_callback' => array(
							array(
								'setting'  => 'register_split_view',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);
			}

		}
	}

endif;

/**
 * Main instance of Reign_Customizer_WP_Login_Screen_Fields.
 *
 * @return Reign_Customizer_WP_Login_Screen_Fields
 */
Reign_Customizer_WP_Login_Screen_Fields::instance();
