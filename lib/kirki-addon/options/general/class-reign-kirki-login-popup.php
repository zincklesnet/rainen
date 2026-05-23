<?php
/**
 * Reign Kirki Login Popup
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Login_Popup' ) ) :

	/**
	 * @class Reign_Kirki_Login_Popup
	 */
	class Reign_Kirki_Login_Popup {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Login_Popup
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Login_Popup Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Login_Popup is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Login_Popup - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Login_Popup Constructor.
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

			new \Kirki\Section(
				'reign_signin_popup_options',
				array(
					'title'       => esc_html__( 'Sign-in Popup | Register Form Fields', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_general_panel',
					'description' => '',
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_signin_popup',
					'label'       => esc_html__( 'Sign-in Popup', 'reign' ),
					'description' => '',
					'section'     => 'reign_signin_popup_options',
					'priority'    => 10,
					'default'     => '',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
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

			new \Kirki\Field\Radio(
				array(
					'settings'        => 'reign_sign_form_popup',
					'label'           => esc_html__( 'Form Popup', 'reign' ),
					'description'     => '',
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

			new \Kirki\Field\Select(
				array(
					'settings'        => 'reign_sign_form_display',
					'label'           => esc_html__( 'Form Display', 'reign' ),
					'description'     => '',
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

				new \Kirki\Field\Select(
					array(
						'settings'        => 'reign_login_redirect',
						'label'           => esc_html__( 'Login Redirect Destination', 'reign' ),
						'description'     => '',
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

				new \Kirki\Field\Select(
					array(
						'settings'        => 'reign_login_redirect',
						'label'           => esc_html__( 'Login Redirect Destination', 'reign' ),
						'description'     => '',
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

			new \Kirki\Field\URL(
				array(
					'settings'        => 'reign_login_redirect_url',
					'label'           => esc_html__( 'Login Custom URL', 'reign' ),
					'description'     => '',
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

					new \Kirki\Field\Select(
						array(
							'settings'        => 'reign_register_redirect',
							'label'           => esc_html__( 'Register Redirect', 'reign' ),
							'description'     => '',
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

					new \Kirki\Field\URL(
						array(
							'settings'        => 'reign_register_redirect_url',
							'label'           => esc_html__( 'Register Custom URL', 'reign' ),
							'description'     => '',
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

			new \Kirki\Field\Textarea(
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

			new \Kirki\Field\Textarea(
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
 * Main instance of Reign_Kirki_Login_Popup.
 *
 * @return Reign_Kirki_Login_Popup
 */
Reign_Kirki_Login_Popup::instance();
