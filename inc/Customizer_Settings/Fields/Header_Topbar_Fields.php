<?php
/**
 * Reign Customizer Header Topbar
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_Header_Topbar_Fields' ) ) :

	/**
	 * @class Reign_Customizer_Header_Topbar_Fields
	 */
	class Reign_Customizer_Header_Topbar_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_Header_Topbar_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_Header_Topbar_Fields Instance.
		 *
		 * Ensures only one instance of Reign_Customizer_Header_Topbar_Fields is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_Header_Topbar_Fields - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Customizer_Header_Topbar_Fields Constructor.
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
				'reign_header_topbar',
				array(
					'title'       => esc_html__( 'Topbar', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_header_panel',
					'description' => '',
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			$default_value_set = reign_get_customizer_default_value_set();

			\Reign\Customizer_Framework\Field::add( 'switch',
				array(
					'settings'    => 'reign_header_topbar_enable',
					'label'       => esc_html__( 'Enable Topbar', 'reign' ),
					'description' => esc_html__( 'Enable or Disable topbar.', 'reign' ),
					'section'     => 'reign_header_topbar',
					'default'     => 1,
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add( 'custom',
				array(
					'settings'        => 'reign_header_topbar_enable_divider',
					'section'         => 'reign_header_topbar',
					'choices'         => array(
						'color' => '#dcdcde',
					),
					'active_callback' => array(
						array(
							'setting'  => 'reign_header_topbar_enable',
							'operator' => '===',
							'value'    => true,
						),
					),
				)
			);

			$fields_on_hold   = array();
			$fields_on_hold[] = array(
				\Reign\Customizer_Framework\Field::add( 'switch',
					array(
						'settings'        => 'reign_header_topbar_sticky',
						'label'           => esc_html__( 'Sticky Topbar', 'reign' ),
						'description'     => esc_html__( 'Enable or Disable sticky topbar.', 'reign' ),
						'section'         => 'reign_header_topbar',
						'default'         => 0,
						'priority'        => 10,
						'choices'         => array(
							'on'  => esc_html__( 'Enable', 'reign' ),
							'off' => esc_html__( 'Disable', 'reign' ),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_header_topbar_enable',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				),

				\Reign\Customizer_Framework\Field::add( 'custom',
					array(
						'settings'        => 'reign_header_topbar_sticky_divider',
						'section'         => 'reign_header_topbar',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_header_topbar_enable',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				),
			);

			$fields_on_hold[] = array(
				\Reign\Customizer_Framework\Field::add( 'switch',
					array(
						'settings'        => 'reign_header_topbar_mobile_view_disable',
						'label'           => esc_html__( 'Disable Topbar On Mobile', 'reign' ),
						'description'     => esc_html__( 'Disable topbar on mobile view.', 'reign' ),
						'section'         => 'reign_header_topbar',
						'default'         => 0,
						'priority'        => 10,
						'choices'         => array(
							'on'  => esc_html__( 'Enable', 'reign' ),
							'off' => esc_html__( 'Disable', 'reign' ),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_header_topbar_enable',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				),

				\Reign\Customizer_Framework\Field::add( 'custom',
					array(
						'settings'        => 'reign_header_topbar_mobile_view_disable_divider',
						'section'         => 'reign_header_topbar',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_header_topbar_enable',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				),
			);

			$fields_on_hold[] = array(
				\Reign\Customizer_Framework\Field::add( 'select',
					array(
						'settings'        => 'reign_header_topbar_mobile_content',
						'label'           => esc_html__( 'Mobile Topbar Content', 'reign' ),
						'description'     => esc_html__( 'On phones the topbar shows a single row. Choose what appears so it does not split into two rows. Pick one side for a clean, fixed-height bar.', 'reign' ),
						'section'         => 'reign_header_topbar',
						'default'         => 'info',
						'priority'        => 10,
						'choices'         => array(
							'info'   => esc_html__( 'Info links only', 'reign' ),
							'social' => esc_html__( 'Social links only', 'reign' ),
							'both'   => esc_html__( 'Both (may wrap on small screens)', 'reign' ),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_header_topbar_enable',
								'operator' => '===',
								'value'    => true,
							),
							array(
								'setting'  => 'reign_header_topbar_mobile_view_disable',
								'operator' => '!=',
								'value'    => true,
							),
						),
					)
				),

				\Reign\Customizer_Framework\Field::add( 'custom',
					array(
						'settings'        => 'reign_header_topbar_mobile_content_divider',
						'section'         => 'reign_header_topbar',
						'choices'         => array(
							'color' => '#dcdcde',
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_header_topbar_enable',
								'operator' => '===',
								'value'    => true,
							),
							array(
								'setting'  => 'reign_header_topbar_mobile_view_disable',
								'operator' => '!=',
								'value'    => true,
							),
						),
					)
				),
			);

			$fields_on_hold[] = array(
				\Reign\Customizer_Framework\Field::add( 'repeater',
					array(
						'settings'        => 'reign_header_topbar_info_links',
						'label'           => esc_html__( 'Info Links', 'reign' ),
						'description'     => sprintf(
							__( 'Fontawesome classes are used to set icons. Visit %s for available icons.', 'reign' ),
							'<a href="' . esc_url( 'https://fontawesome.com/v5/search' ) . '" target="_blank">' . esc_html( 'https://fontawesome.com/' ) . '</a>'
						),
						'section'         => 'reign_header_topbar',
						'priority'        => 13,
						'row_label'       => array(
							'type'  => 'field',
							'value' => esc_html__( 'Info Link', 'reign' ),
							'field' => 'link_text',
						),
						'button_label'    => esc_html__( 'Add', 'reign' ),
						'transport'       => 'postMessage',
						'default'         => $default_value_set['reign_header_topbar_info_links'],
						'fields'          => array(
							'link_text' => array(
								'type'        => 'text',
								'label'       => esc_html__( 'Title', 'reign' ),
								'description' => '',
								'default'     => '',
							),
							'link_icon' => array(
								'type'        => 'textarea',
								'label'       => esc_html__( 'Icon', 'reign' ),
								'description' => esc_html__( 'for eg. <i class="fa fa-phone-alt"></i>', 'reign' ),
								'default'     => '',
							),
							'link_url'  => array(
								'type'        => 'text',
								'label'       => esc_html__( 'Link', 'reign' ),
								'description' => '',
								'default'     => '',
							),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_header_topbar_enable',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				),
			);

			$fields_on_hold[] = array(

				\Reign\Customizer_Framework\Field::add( 'repeater',
					array(
						'settings'        => 'reign_header_topbar_social_links',
						'label'           => esc_html__( 'Social Links', 'reign' ),
						'description'     => sprintf(
							__( 'Fontawesome classes are used to set icons. Visit %s for available icons.', 'reign' ),
							'<a href="' . esc_url( 'https://fontawesome.com/v5/search' ) . '" target="_blank">' . esc_html( 'https://fontawesome.com/' ) . '</a>'
						),
						'section'         => 'reign_header_topbar',
						'priority'        => 14,
						'row_label'       => array(
							'type'  => 'field',
							'value' => esc_html__( 'Social Link', 'reign' ),
							'field' => 'link_text',
						),
						'button_label'    => esc_html__( 'Add', 'reign' ),
						'transport'       => 'postMessage',
						'default'         => $default_value_set['reign_header_topbar_social_links'],
						'fields'          => array(
							'link_text' => array(
								'type'        => 'text',
								'label'       => esc_html__( 'Title', 'reign' ),
								'description' => '',
								'default'     => '',
							),
							'link_icon' => array(
								'type'        => 'textarea',
								'label'       => esc_html__( 'Icon', 'reign' ),
								'description' => esc_html__( 'for eg. <i class="fab fa-facebook"></i>', 'reign' ),
								'default'     => '',
							),
							'link_url'  => array(
								'type'        => 'url',
								'label'       => esc_html__( 'Link', 'reign' ),
								'description' => '',
								'default'     => '',
							),
						),
						'active_callback' => array(
							array(
								'setting'  => 'reign_header_topbar_enable',
								'operator' => '===',
								'value'    => true,
							),
						),
					)
				),
			);

			$fields_on_hold = apply_filters( 'reign_header_topbar_fields_on_hold', $fields_on_hold );

			foreach ( $fields_on_hold as $key => $value ) {
				$fields[] = $value;
			}
		}
	}

endif;

/**
 * Main instance of Reign_Customizer_Header_Topbar_Fields.
 *
 * @return Reign_Customizer_Header_Topbar_Fields
 */
Reign_Customizer_Header_Topbar_Fields::instance();
