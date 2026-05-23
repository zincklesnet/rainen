<?php
/**
 * Reign Kirki Forms
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Forms' ) ) :

	/**
	 * @class Reign_Kirki_Forms
	 */
	class Reign_Kirki_Forms {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Forms
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Forms Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Forms is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Forms - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Forms Constructor.
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

			new \Kirki\Panel(
				'reign_forms_panel',
				array(
					'priority'    => 80,
					'title'       => esc_html__( 'Forms', 'reign' ),
					'description' => '',
				)
			);

			new \Kirki\Section(
				'reign_forms_style',
				array(
					'title'       => esc_html__( 'Active Color Setting', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_forms_panel',
					'description' => '',
				)
			);

			new \Kirki\Section(
				'reign_forms_focus_style',
				array(
					'title'       => esc_html__( 'Focus Color Setting', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_forms_panel',
					'description' => '',
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			$default_value_set = reign_forms_color_scheme_default_set();

			foreach ( $default_value_set as $color_scheme_key => $default_set ) {

				new \Kirki\Field\Color(
					array(
						'settings'        => $color_scheme_key . '-' . 'reign_form_text_color',
						'label'           => esc_html__( 'Form Text Color', 'reign' ),
						'description'     => esc_html__( 'Sets the text color for form elements.', 'reign' ),
						'section'         => 'reign_forms_style',
						'default'         => $default_value_set[ $color_scheme_key ]['reign_form_text_color'],
						'priority'        => 10,
						'choices'         => array( 'alpha' => true ),
						'active_callback' => array(
							array(
								'setting'  => 'reign_color_scheme',
								'operator' => '===',
								'value'    => $color_scheme_key,
							),
						),
					)
				);

				new \Kirki\Field\Color(
					array(
						'settings'        => $color_scheme_key . '-' . 'reign_form_background_color',
						'label'           => esc_html__( 'Form Background Color', 'reign' ),
						'description'     => esc_html__( 'Sets the background color for form elements.', 'reign' ),
						'section'         => 'reign_forms_style',
						'default'         => $default_value_set[ $color_scheme_key ]['reign_form_background_color'],
						'priority'        => 10,
						'choices'         => array( 'alpha' => true ),
						'active_callback' => array(
							array(
								'setting'  => 'reign_color_scheme',
								'operator' => '===',
								'value'    => $color_scheme_key,
							),
						),
					)
				);

				new \Kirki\Field\Color(
					array(
						'settings'        => $color_scheme_key . '-' . 'reign_form_border_color',
						'label'           => esc_html__( 'Form Border Color', 'reign' ),
						'description'     => esc_html__( 'Sets the border color for form elements.', 'reign' ),
						'section'         => 'reign_forms_style',
						'default'         => $default_value_set[ $color_scheme_key ]['reign_form_border_color'],
						'priority'        => 10,
						'choices'         => array( 'alpha' => true ),
						'active_callback' => array(
							array(
								'setting'  => 'reign_color_scheme',
								'operator' => '===',
								'value'    => $color_scheme_key,
							),
						),
					)
				);

				new \Kirki\Field\Color(
					array(
						'settings'        => $color_scheme_key . '-' . 'reign_form_placeholder_color',
						'label'           => esc_html__( 'Form Placeholder Color', 'reign' ),
						'description'     => esc_html__( 'Sets the placeholder color for form elements.', 'reign' ),
						'section'         => 'reign_forms_style',
						'default'         => $default_value_set[ $color_scheme_key ]['reign_form_placeholder_color'],
						'priority'        => 10,
						'choices'         => array( 'alpha' => true ),
						'output'          => array(
							array(
								'element'  => 'input::-webkit-input-placeholder, textarea::-webkit-input-placeholder',
								'property' => 'color',
							),
							array(
								'element'  => 'input:-moz-placeholder, input::-moz-placeholder, textarea:-moz-placeholder, textarea::-moz-placeholder',
								'property' => 'color',
							),
							array(
								'element'  => 'input:-ms-input-placeholder, textarea:-ms-input-placeholder',
								'property' => 'color',
							),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_color_scheme',
								'operator' => '===',
								'value'    => $color_scheme_key,
							),
						),
					)
				);

				new \Kirki\Field\Color(
					array(
						'settings'        => $color_scheme_key . '-' . 'reign_form_focus_text_color',
						'label'           => esc_html__( 'Form Focus Text Color', 'reign' ),
						'description'     => esc_html__( 'Sets the focus color for form elements.', 'reign' ),
						'section'         => 'reign_forms_focus_style',
						'default'         => $default_value_set[ $color_scheme_key ]['reign_form_focus_text_color'],
						'priority'        => 10,
						'choices'         => array( 'alpha' => true ),
						'active_callback' => array(
							array(
								'setting'  => 'reign_color_scheme',
								'operator' => '===',
								'value'    => $color_scheme_key,
							),
						),
					)
				);

				new \Kirki\Field\Color(
					array(
						'settings'        => $color_scheme_key . '-' . 'reign_form_focus_background_color',
						'label'           => esc_html__( 'Form Focus Background Color', 'reign' ),
						'description'     => esc_html__( 'Sets the focus background color for form elements.', 'reign' ),
						'section'         => 'reign_forms_focus_style',
						'default'         => $default_value_set[ $color_scheme_key ]['reign_form_focus_background_color'],
						'priority'        => 10,
						'choices'         => array( 'alpha' => true ),
						'active_callback' => array(
							array(
								'setting'  => 'reign_color_scheme',
								'operator' => '===',
								'value'    => $color_scheme_key,
							),
						),
					)
				);

				new \Kirki\Field\Color(
					array(
						'settings'        => $color_scheme_key . '-' . 'reign_form_focus_border_color',
						'label'           => esc_html__( 'Form Focus Border Color', 'reign' ),
						'description'     => esc_html__( 'Sets the focus border color for form elements.', 'reign' ),
						'section'         => 'reign_forms_focus_style',
						'default'         => $default_value_set[ $color_scheme_key ]['reign_form_focus_border_color'],
						'priority'        => 10,
						'choices'         => array( 'alpha' => true ),
						'active_callback' => array(
							array(
								'setting'  => 'reign_color_scheme',
								'operator' => '===',
								'value'    => $color_scheme_key,
							),
						),
					)
				);

				new \Kirki\Field\Color(
					array(
						'settings'        => $color_scheme_key . '-' . 'reign_form_focus_placeholder_color',
						'label'           => esc_html__( 'Form Focus Placeholder Color', 'reign' ),
						'description'     => esc_html__( 'Sets the focus placeholder color for form elements.', 'reign' ),
						'section'         => 'reign_forms_focus_style',
						'default'         => $default_value_set[ $color_scheme_key ]['reign_form_focus_placeholder_color'],
						'priority'        => 10,
						'choices'         => array( 'alpha' => true ),
						'output'          => array(
							array(
								'element'  => 'input:focus::-webkit-input-placeholder, textarea:focus::-webkit-input-placeholder',
								'property' => 'color',
							),
							array(
								'element'  => 'input:focus:-moz-placeholder, input:focus::-moz-placeholder, textarea:focus:-moz-placeholder, textarea:focus::-moz-placeholder',
								'property' => 'color',
							),
							array(
								'element'  => 'input:focus:-ms-input-placeholder, textarea:focus:-ms-input-placeholder',
								'property' => 'color',
							),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_color_scheme',
								'operator' => '===',
								'value'    => $color_scheme_key,
							),
						),
					)
				);
			}
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Forms.
 *
 * @return Reign_Kirki_Forms
 */
Reign_Kirki_Forms::instance();
