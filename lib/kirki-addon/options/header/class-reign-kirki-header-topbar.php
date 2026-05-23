<?php
/**
 * Reign Kirki Header Topbar
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Header_Topbar' ) ) :

	/**
	 * @class Reign_Kirki_Header_Topbar
	 */
	class Reign_Kirki_Header_Topbar {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Header_Topbar
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Header_Topbar Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Header_Topbar is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Header_Topbar - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Header_Topbar Constructor.
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

			new \Kirki\Field\Checkbox_Switch(
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

			new \Kirki\Pro\Field\Divider(
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
				new \Kirki\Field\Checkbox_Switch(
					array(
						'settings'        => 'reign_header_topbar_sticky',
						'label'           => esc_html__( 'Sticky Topbar', 'reign' ),
						'description'     => esc_html__( 'Enable or Disable sticky topbar (desktop view).', 'reign' ),
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

				new \Kirki\Pro\Field\Divider(
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
				new \Kirki\Field\Checkbox_Switch(
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

				new \Kirki\Pro\Field\Divider(
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
				new \Kirki\Field\Repeater(
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

				new \Kirki\Field\Repeater(
					array(
						'settings'        => 'reign_header_topbar_social_links',
						'label'           => esc_html__( 'Social Links', 'reign' ),
						'description'     => sprintf(
							__( 'Fontawesome classes are used to set icons. Visit %s for available icons.', 'reign' ),
							'<a href="' . esc_url( 'https://fontawesome.com/v5/search' ) . '" target="_blank">' . esc_html( 'https://fontawesome.com/' ) . '</a>'
						),
						'section'         => 'reign_header_topbar',
						'priority'        => 13,
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
								'default'     => '<i class="fab fa-facebook"></i>',
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
 * Main instance of Reign_Kirki_Header_Topbar.
 *
 * @return Reign_Kirki_Header_Topbar
 */
Reign_Kirki_Header_Topbar::instance();
