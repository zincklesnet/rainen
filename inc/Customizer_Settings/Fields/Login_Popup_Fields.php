<?php
/**
 * Reign Customizer Login Popup
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Login_Popup_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Login_Popup_Fields
	 */
	class Reign_Customizer_Login_Popup_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Login_Popup_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Login_Popup_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Login_Popup_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Login_Popup_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Login_Popup_Fields Constructor.
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

			\Reign\Customizer_Framework\Section::add(
				'reign_signin_popup_options',
				array(
					'title'       => esc_html__( 'Sign-in & Register Popup', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_general_panel',
					'description' => esc_html__( 'Open login and registration in a popup instead of the default WordPress login page. Form colors follow your palette under Color Options.', 'reign' ),
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_signin_popup',
					'label'       => esc_html__( 'Sign-in Popup', 'reign' ),
					'description' => esc_html__( 'Open the login / register form in a popup instead of the WordPress login page.', 'reign' ),
					'section'     => 'reign_signin_popup_options',
					'priority'    => 10,
					'default'     => 'off',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_signin_popup_divider',
					'section'         => 'reign_signin_popup_options',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_signin_popup',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'radio',
				array(
					'settings'        => 'reign_sign_form_popup',
					'label'           => esc_html__( 'Form Popup', 'reign' ),
					'description'     => esc_html__( 'Use the built-in Reign form, or supply your own shortcode.', 'reign' ),
					'section'         => 'reign_signin_popup_options',
					'priority'        => 10,
					'default'         => 'default',
					'choices'         => array(
						'default' => esc_html__( 'Reign Login Form', 'reign' ),
						'custom'  => esc_html__( 'Custom shortcode', 'reign' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_signin_popup',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'select',
				array(
					'settings'        => 'reign_sign_form_display',
					'label'           => esc_html__( 'Form Display', 'reign' ),
					'description'     => esc_html__( 'Which form the popup opens with - login, register, or both.', 'reign' ),
					'section'         => 'reign_signin_popup_options',
					'priority'        => 10,
					'default'         => 'login',
					'choices'         => array(
						'both'     => esc_html__( 'Both', 'reign' ),
						'login'    => esc_html__( 'Login', 'reign' ),
						'register' => esc_html__( 'Register', 'reign' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_sign_form_popup',
							'operator' => '!=',
							'value'    => 'custom',
						),
						array(
							'setting'  => 'reign_signin_popup',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			if ( class_exists( 'BuddyPress' ) ) {

				\Reign\Customizer_Framework\Field::add( 'select',
					array(
						'settings'        => 'reign_login_redirect',
						'label'           => esc_html__( 'Login Redirect Destination', 'reign' ),
						'description'     => esc_html__( 'Where visitors go after they log in.', 'reign' ),
						'section'         => 'reign_signin_popup_options',
						'priority'        => 10,
						'default'         => 'current',
						'choices'         => array(
							'current'  => esc_html__( 'Current page', 'reign' ),
							'profile'  => esc_html__( 'Profile page', 'reign' ),
							'activity' => esc_html__( 'Activity page', 'reign' ),
							'custom'   => esc_html__( 'Custom page', 'reign' ),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_sign_form_popup',
								'operator' => '!=',
								'value'    => 'custom',
							),
							array(
								'setting'  => 'reign_signin_popup',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

			} else {

				\Reign\Customizer_Framework\Field::add( 'select',
					array(
						'settings'        => 'reign_login_redirect',
						'label'           => esc_html__( 'Login Redirect Destination', 'reign' ),
						'description'     => esc_html__( 'Where visitors go after they log in.', 'reign' ),
						'section'         => 'reign_signin_popup_options',
						'priority'        => 10,
						'default'         => 'current',
						'choices'         => array(
							'current' => esc_html__( 'Current page', 'reign' ),
							'custom'  => esc_html__( 'Custom page', 'reign' ),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_sign_form_popup',
								'operator' => '!=',
								'value'    => 'custom',
							),
							array(
								'setting'  => 'reign_signin_popup',
								'operator' => '==',
								'value'    => true,
							),
						),
					)
				);

			}

			\Reign\Customizer_Framework\Field::add( 'url',
				array(
					'settings'        => 'reign_login_redirect_url',
					'label'           => esc_html__( 'Login Custom URL', 'reign' ),
					'description'     => esc_html__( 'The page to send users to after login.', 'reign' ),
					'section'         => 'reign_signin_popup_options',
					'priority'        => 10,
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'reign_signin_popup',
							'operator' => '==',
							'value'    => true,
						),
						array(
							'setting'  => 'reign_sign_form_popup',
							'operator' => '!=',
							'value'    => 'custom',
						),
						array(
							'setting'  => 'reign_login_redirect',
							'operator' => '==',
							'value'    => 'custom',
						),
					),
				)
			);

			if ( ! class_exists( 'PeepSo' ) ) {

				if ( ! class_exists( 'BuddyPress' ) ) {

					\Reign\Customizer_Framework\Field::add( 'select',
						array(
							'settings'        => 'reign_register_redirect',
							'label'           => esc_html__( 'Register Redirect', 'reign' ),
							'description'     => esc_html__( 'Where visitors go after they register.', 'reign' ),
							'section'         => 'reign_signin_popup_options',
							'priority'        => 10,
							'default'         => 'current',
							'choices'         => array(
								'current' => esc_html__( 'Current page', 'reign' ),
								'custom'  => esc_html__( 'Custom page', 'reign' ),
							),
							'active_callback' => array(
								array(
									'setting'  => 'reign_sign_form_popup',
									'operator' => '!=',
									'value'    => 'custom',
								),
								array(
									'setting'  => 'reign_signin_popup',
									'operator' => '==',
									'value'    => true,
								),
							),
						)
					);

					\Reign\Customizer_Framework\Field::add( 'url',
						array(
							'settings'        => 'reign_register_redirect_url',
							'label'           => esc_html__( 'Register Custom URL', 'reign' ),
							'description'     => esc_html__( 'The page to send users to after registering.', 'reign' ),
							'section'         => 'reign_signin_popup_options',
							'priority'        => 10,
							'default'         => '',
							'active_callback' => array(
								array(
									'setting'  => 'reign_signin_popup',
									'operator' => '==',
									'value'    => true,
								),
								array(
									'setting'  => 'reign_sign_form_popup',
									'operator' => '!=',
									'value'    => 'custom',
								),
								array(
									'setting'  => 'reign_register_redirect',
									'operator' => '==',
									'value'    => 'custom',
								),
							),
						)
					);
				}
			}

			\Reign\Customizer_Framework\Field::add( 'textarea',
				array(
					'settings'          => 'reign_login_form_title',
					'label'             => esc_html__( 'Login Form Label', 'reign' ),
					'description'       => esc_html__( 'Defaults to "Login to your Account" if left empty. Supports plain text, HTML, and links.', 'reign' ),
					'section'           => 'reign_signin_popup_options',
					'default'           => '',
					'priority'          => 10,
					'sanitize_callback' => 'wp_kses_post',
					'active_callback'   => array(
						array(
							'setting'  => 'reign_sign_form_popup',
							'operator' => '==',
							'value'    => 'default',
						),
						array(
							'setting'  => 'reign_signin_popup',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'textarea',
				array(
					'settings'          => 'reign_register_form_title',
					'label'             => esc_html__( 'Register Form Label', 'reign' ),
					'description'       => esc_html__( 'Defaults to "Register in {site name}" if left empty. Supports plain text, HTML, and links.', 'reign' ),
					'section'           => 'reign_signin_popup_options',
					'default'           => '',
					'priority'          => 10,
					'sanitize_callback' => 'wp_kses_post',
					'active_callback'   => array(
						array(
							'setting'  => 'reign_sign_form_popup',
							'operator' => '==',
							'value'    => 'default',
						),
						array(
							'setting'  => 'reign_signin_popup',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'textarea',
				array(
					'settings'        => 'reign_login_description',
					'label'           => esc_html__( 'Login Form Description', 'reign' ),
					'description'     => esc_html__( 'Note: Use only for Reign Login Form.', 'reign' ),
					'section'         => 'reign_signin_popup_options',
					'default'         => '',
					'priority'        => 10,
					'active_callback' => array(
						array(
							'setting'  => 'reign_sign_form_popup',
							'operator' => '==',
							'value'    => 'default',
						),
						array(
							'setting'  => 'reign_signin_popup',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'textarea',
				array(
					'settings'        => 'reign_sign_form_shortcode',
					'label'           => esc_html__( 'Popup Content', 'reign' ),
					'description'     => esc_html__( 'You can use own custom HTML or shortcodes that will appear in popup box', 'reign' ),
					'section'         => 'reign_signin_popup_options',
					'priority'        => 10,
					'active_callback' => array(
						array(
							'setting'  => 'reign_sign_form_popup',
							'operator' => '==',
							'value'    => 'custom',
						),
						array(
							'setting'  => 'reign_signin_popup',
							'operator' => '==',
							'value'    => true,
						),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Customizer_Login_Popup_Fields.
 *
 * @return Reign_Customizer_Login_Popup_Fields
 */
Reign_Customizer_Login_Popup_Fields::instance();
