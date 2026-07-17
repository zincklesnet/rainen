<?php
/**
 * Reign Customizer Forms
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Forms_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Forms_Fields
	 */
	class Reign_Customizer_Forms_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Forms_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Forms_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Forms_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Forms_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Forms_Fields Constructor.
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
				'reign_forms_panel',
				array(
					'priority'    => 80,
					'title'       => esc_html__( 'Forms', 'reign' ),
					'description' => esc_html__( 'Colors for input fields, textareas and selects. These follow your active Color Palette (Color Options); fine-tune them per palette here.', 'reign' ),
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_forms_style',
				array(
					'title'       => esc_html__( 'Form Field Colors', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_forms_panel',
					'description' => esc_html__( 'Resting colors for form fields - text, background, border and placeholder.', 'reign' ),
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_forms_focus_style',
				array(
					'title'       => esc_html__( 'Focus State Colors', 'reign' ),
					'priority'    => 20,
					'panel'       => 'reign_forms_panel',
					'description' => esc_html__( 'Colors shown while a visitor is typing in a field (on focus).', 'reign' ),
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			$default_value_set = reign_forms_color_scheme_default_set();

			foreach ( $default_value_set as $color_scheme_key => $default_set ) {

				\Reign\Customizer_Framework\Field::add( 'color',
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

				\Reign\Customizer_Framework\Field::add( 'color',
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

				\Reign\Customizer_Framework\Field::add( 'color',
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

				\Reign\Customizer_Framework\Field::add( 'color',
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

				\Reign\Customizer_Framework\Field::add( 'color',
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

				\Reign\Customizer_Framework\Field::add( 'color',
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

				\Reign\Customizer_Framework\Field::add( 'color',
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

				\Reign\Customizer_Framework\Field::add( 'color',
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
 * Main instance of Reign_Customizer_Forms_Fields.
 *
 * @return Reign_Customizer_Forms_Fields
 */
Reign_Customizer_Forms_Fields::instance();
