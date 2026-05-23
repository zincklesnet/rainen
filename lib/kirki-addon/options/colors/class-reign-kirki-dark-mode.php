<?php
/**
 * Reign Kirki Dark Mode
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Dark_Mode' ) ) :

	/**
	 * @class Reign_Kirki_Dark_Mode
	 */
	class Reign_Kirki_Dark_Mode {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Dark_Mode
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Dark_Mode Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Dark_Mode is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Dark_Mode - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Dark_Mode Constructor.
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
				'reign_dark_mode_options',
				array(
					'title'       => esc_html__( 'Dark Mode Option', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_color_panel',
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
					'settings'    => 'reign_dark_mode_option',
					'label'       => esc_html__( 'Dark Mode Toggle', 'reign' ),
					'description' => esc_html__( 'Note: Option not working when activate color scheme \'Dark\' from colors settings.', 'reign' ),
					'section'     => 'reign_dark_mode_options',
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
					'settings'        => 'reign_dark_mode_option_divider',
					'section'         => 'reign_dark_mode_options',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_dark_mode_option',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Select(
				array(
					'settings'        => 'reign_dark_mode_style',
					'label'           => esc_html__( 'Dark Mode Toggle Style', 'reign' ),
					'description'     => esc_html__( 'Set the dark mode toggle style.', 'reign' ),
					'section'         => 'reign_dark_mode_options',
					'priority'        => 10,
					'default'         => 'style2',
					'tooltip'         => esc_html__( 'If you choose layout 4, you will find the dark mode option in the user menu section.', 'reign' ),
					'choices'         => array(
						'style1' => esc_html__( 'Style 1', 'reign' ),
						'style2' => esc_html__( 'Style 2', 'reign' ),
						'style3' => esc_html__( 'Style 3', 'reign' ),
						'style4' => esc_html__( 'Style 4', 'reign' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_dark_mode_option',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_dark_mode_style_divider',
					'section'         => 'reign_dark_mode_options',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_dark_mode_option',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Radio_Buttonset(
				array(
					'settings'        => 'reign_default_mode',
					'label'           => esc_html__( 'Select Default Mode', 'reign' ),
					'section'         => 'reign_dark_mode_options',
					'default'         => 'light',
					'tooltip'         => esc_html__( 'Please choose the default mode for your website. (Note: This option will not work with the dark mode scheme activated in the color scheme settings.)', 'reign' ),
					'choices'         => array(
						'light' => esc_html__( 'Light', 'reign' ),
						'dark'  => esc_html__( 'Dark', 'reign' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_dark_mode_option',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_dark_mode_default_state_divider',
					'section'         => 'reign_dark_mode_options',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_dark_mode_option',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'        => 'reign_custom_dark_mode_option',
					'label'           => esc_html__( 'Custom Dark Mode Colors', 'reign' ),
					'description'     => esc_html__( 'If enable this option, you can set custom colors from Customizer Setting > Colors > Color Scheme > Dark.', 'reign' ),
					'section'         => 'reign_dark_mode_options',
					'priority'        => 10,
					'default'         => '',
					'choices'         => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_dark_mode_option',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings'        => 'reign_custom_dark_mode_option_divider',
					'section'         => 'reign_dark_mode_options',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_dark_mode_option',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			new \Kirki\Field\Image(
				array(
					'settings'        => 'reign_dark_mode_logo',
					'label'           => esc_html__( 'Dark Mode Logo', 'reign' ),
					'description'     => esc_html__( 'Set dark mode logo (Note: First set theme logo from Site Identity > Logo)', 'reign' ),
					'section'         => 'reign_dark_mode_options',
					'priority'        => 10,
					'default'         => '',
					'active_callback' => array(
						array(
							'setting'  => 'reign_dark_mode_option',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Dark_Mode.
 *
 * @return Reign_Kirki_Dark_Mode
 */
Reign_Kirki_Dark_Mode::instance();
