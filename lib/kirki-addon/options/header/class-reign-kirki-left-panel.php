<?php
/**
 * Reign Kirki Left Panel
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_Left_Panel' ) ) :

	/**
	 * @class Reign_Kirki_Left_Panel
	 */
	class Reign_Kirki_Left_Panel {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_Left_Panel
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_Left_Panel Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_Left_Panel is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_Left_Panel - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_Left_Panel Constructor.
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
				'reign_left_section',
				array(
					'priority'    => 80,
					'title'       => esc_html__( 'Left Panel', 'reign' ),
					'panel'       => 'reign_header_panel',
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
					'settings'    => 'reign_left_panel_gloabl_setting',
					'label'       => esc_html__( 'Left Panel - Global Setting', 'reign' ),
					'description' => esc_html__( 'This setting will work globally for all the pages. Left panel can be shown on a specific page by editing that particular page.', 'reign' ),
					'section'     => 'reign_left_section',
					'default'     => 'on',
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_left_panel_gloabl_setting_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Radio_Buttonset(
				array(
					'settings' => 'reign_left_panel_position',
					'label'    => esc_html__( 'Left Panel Position', 'reign' ),
					'section'  => 'reign_left_section',
					'default'  => 'left',
					'priority' => 10,
					'choices'  => array(
						'left'  => esc_html__( 'Left', 'reign' ),
						'right' => esc_html__( 'Right', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_left_panel_position_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings' => 'reign_left_panel_toggle',
					'label'    => esc_html__( 'Left Panel - Toggle', 'reign' ),
					'section'  => 'reign_left_section',
					'default'  => 'on',
					'priority' => 10,
					'choices'  => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_left_panel_toggle_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Radio_Buttonset(
				array(
					'settings' => 'reign_left_panel_state',
					'label'    => esc_html__( 'Left Panel - Default State', 'reign' ),
					'section'  => 'reign_left_section',
					'default'  => 'closed',
					'priority' => 10,
					'choices'  => array(
						'open'   => esc_html__( 'Open', 'reign' ),
						'closed' => esc_html__( 'Closed', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_left_panel_state_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_left_panel_shift_body',
					'label'       => esc_html__( 'Left Panel - Shift Body', 'reign' ),
					'description' => esc_html__( 'When enabled, the main content will shift when the panel opens or closes.', 'reign' ),
					'section'     => 'reign_left_section',
					'default'     => '',
					'tooltip'     => esc_html__( 'The box layout isn\'t working with this setting.', 'reign' ),
					'priority'    => 10,
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_left_panel_shift_body_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'    => 'reign_left_panel_icon_typography',
					'label'       => esc_html__( 'Left Panel - Icon Font Size', 'reign' ),
					'description' => esc_html__( 'Set left panel icon font size (Default value is 18px).', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => array(
						'font-size' => '18px',
					),
					'output'      => array(
						array(
							'media_query' => '@media (min-width: 960px)',
							'element'     => 'ul.navbar-reign-panel li.menu-item a i, ul.navbar-reign-panel li.menu-item a img._mi',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_left_panel_icon_typography_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Slider(
				array(
					'settings'    => 'reign_left_panel_icon_height_width',
					'label'       => esc_html__( 'Left Panel - Icon Height/Width', 'reign' ),
					'description' => esc_html__( 'Set left panel icon height width (Default value is 47px).', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => 47,
					'transport'   => 'refresh',
					'choices'     => array(
						'min'  => 30,
						'max'  => 50,
						'step' => 1,
					),
					'output'      => array(
						array(
							'media_query' => '@media (min-width: 960px)',
							'element'     => 'ul.navbar-reign-panel li.menu-item a i, ul.navbar-reign-panel li.menu-item a img._mi',
							'property'    => 'width',
							'suffix'      => 'px',
						),
						array(
							'media_query' => '@media (min-width: 960px)',
							'element'     => 'ul.navbar-reign-panel li.menu-item a i, ul.navbar-reign-panel li.menu-item a img._mi',
							'property'    => 'min-width',
							'suffix'      => 'px',
						),
						array(
							'media_query' => '@media (min-width: 960px)',
							'element'     => 'ul.navbar-reign-panel li.menu-item a i, ul.navbar-reign-panel li.menu-item a img._mi',
							'property'    => 'height',
							'suffix'      => 'px',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_left_panel_icon_height_width_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Dimension(
				array(
					'settings'    => 'reign_left_panel_icon_border_radius',
					'label'       => esc_html__( 'Left Panel - Icon Border Radius', 'reign' ),
					'description' => esc_html__( 'Set left panel icon border radius (Default value is 6px).', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => '6px',
					'transport'   => 'refresh',
					'output'      => array(
						array(
							'media_query' => '@media (min-width: 960px)',
							'element'     => 'ul.navbar-reign-panel li.menu-item a',
							'property'    => 'border-radius',
						),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_left_panel_icon_border_radius_divider',
					'section'  => 'reign_left_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Typography(
				array(
					'settings'    => 'reign_left_panel_menu_typography',
					'label'       => esc_html__( 'Left Panel - Menu Typography', 'reign' ),
					'description' => esc_html__( 'Set left panel menu typography.', 'reign' ),
					'section'     => 'reign_left_section',
					'priority'    => 10,
					'default'     => array(
						'font-family'    => '',
						'variant'        => '',
						'font-style'     => '',
						'font-size'      => '16px',
						'letter-spacing' => '0px',
						'text-transform' => '',
					),
					'output'      => array(
						array(
							'media_query' => '@media (min-width: 960px)',
							'element'     => 'ul.navbar-reign-panel li.menu-item a',
						),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_Left_Panel.
 *
 * @return Reign_Kirki_Left_Panel
 */
Reign_Kirki_Left_Panel::instance();
