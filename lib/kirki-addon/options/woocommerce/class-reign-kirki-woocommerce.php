<?php
/**
 * Reign Kirki WooCommerce
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Kirki_WooCommerce' ) ) :

	/**
	 * @class Reign_Kirki_WooCommerce
	 */
	class Reign_Kirki_WooCommerce {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Kirki_WooCommerce
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Kirki_WooCommerce Instance.
		 *
		 * Ensures only one instance of Reign_Kirki_WooCommerce is loaded or can be loaded.
		 *
		 * @return Reign_Kirki_WooCommerce - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Kirki_WooCommerce Constructor.
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
				'reign_woocommerce_shop_section',
				array(
					'title'       => esc_html__( 'Shop Page', 'reign' ),
					'priority'    => 9,
					'description' => '',
					'panel'       => 'woocommerce',
				)
			);

			new \Kirki\Section(
				'reign_woocommerce_single_product_section',
				array(
					'title'       => esc_html__( 'Single Product Page', 'reign' ),
					'priority'    => 9,
					'description' => '',
					'panel'       => 'woocommerce',
				)
			);

			new \Kirki\Section(
				'reign_woocommerce_cart_section',
				array(
					'title'       => esc_html__( 'Cart Page', 'reign' ),
					'priority'    => 9,
					'description' => '',
					'panel'       => 'woocommerce',
				)
			);

			new \Kirki\Section(
				'reign_woocommerce_my_account_section',
				array(
					'title'       => esc_html__( 'My Account Page', 'reign' ),
					'priority'    => 9,
					'description' => '',
					'panel'       => 'woocommerce',
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			// Shop Page.
			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_woo_layout_view_buttons',
					'label'       => esc_html__( 'Show Grid Buttons', 'reign' ),
					'description' => esc_html__( 'Enable/Disable product listing view button on product archive page.', 'reign' ),
					'section'     => 'reign_woocommerce_shop_section',
					'priority'    => 10,
					'default'     => 'on',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_woo_layout_view_buttons_divider',
					'section'  => 'reign_woocommerce_shop_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Select(
				array(
					'type'     => 'select',
					'settings' => 'reign_woo_product_layout',
					'label'    => esc_html__( 'Product Layouts', 'reign' ),
					'section'  => 'reign_woocommerce_shop_section',
					'default'  => 'woo_product_default',
					'choices'  => array(
						'woo_product_default' => esc_html__( 'Default', 'reign' ),
						'woo_product_layout1' => esc_html__( 'Layout 1', 'reign' ),
						'woo_product_layout2' => esc_html__( 'Layout 2', 'reign' ),
						'woo_product_layout3' => esc_html__( 'Layout 3', 'reign' ),
						'woo_product_layout4' => esc_html__( 'Layout 4', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_woo_product_layout_divider',
					'section'  => 'reign_woocommerce_shop_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_woo_shop_sort',
					'label'       => esc_html__( 'Product Sort', 'reign' ),
					'description' => esc_html__( 'Enable/Disable product sorting on product archive page.', 'reign' ),
					'section'     => 'reign_woocommerce_shop_section',
					'priority'    => 10,
					'default'     => 'on',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_woo_shop_sort_divider',
					'section'  => 'reign_woocommerce_shop_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'          => 'reign_woo_shop_result_count',
					'label'             => esc_html__( 'Product Result Count', 'reign' ),
					'description'       => esc_html__( 'Enable/Disable product result count on product archive page.', 'reign' ),
					'section'           => 'reign_woocommerce_shop_section',
					'priority'          => 10,
					'default'           => 'on',
					'sanitize_callback' => 'reign_sanitize_checkbox',
					'choices'           => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_woo_shop_result_count_divider',
					'section'  => 'reign_woocommerce_shop_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'          => 'reign_woo_off_canvas_filter',
					'label'             => esc_html__( 'Display Filter Button', 'reign' ),
					'description'       => esc_html__( 'Set filters for archive products. (Go to the Appearance > Widgets > Off Canvas Sidebar)', 'reign' ),
					'section'           => 'reign_woocommerce_shop_section',
					'priority'          => 10,
					'default'           => '',
					'sanitize_callback' => 'reign_sanitize_checkbox',
					'choices'           => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Field\Text(
				array(
					'settings'        => 'reign_woo_off_canvas_filter_text',
					'label'           => esc_html__( 'Filter Text', 'reign' ),
					'description'     => esc_html__( 'Allow you to change filter panel heading text.', 'reign' ),
					'section'         => 'reign_woocommerce_shop_section',
					'default'         => esc_html__( 'Filter', 'reign' ),
					'active_callback' => 'reign_has_woo_filter_button',
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_woo_off_canvas_filter_divider',
					'section'  => 'reign_woocommerce_shop_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_woo_product_thumbnail_hover_effect',
					'label'       => esc_html__( 'Product Thumbnail Hover Effect', 'reign' ),
					'description' => esc_html__( 'Show product thumbnail hover effect in archive page products list (Note: Need to set secondary image in product gallery).', 'reign' ),
					'section'     => 'reign_woocommerce_shop_section',
					'priority'    => 10,
					'default'     => 'on',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			// Single Product Page.
			new \Kirki\Field\Select(
				array(
					'settings' => 'reign_woo_single_product_layout',
					'label'    => esc_html__( 'Product Layouts', 'reign' ),
					'section'  => 'reign_woocommerce_single_product_section',
					'default'  => 'woo_single_product_default',
					'choices'  => array(
						'woo_single_product_default' => esc_html__( 'Default', 'reign' ),
						'woo_single_product_layout1' => esc_html__( 'Layout 1', 'reign' ),
						'woo_single_product_layout2' => esc_html__( 'Layout 2', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_woo_single_product_layout_divider',
					'section'  => 'reign_woocommerce_single_product_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Radio_Image(
				array(
					'settings' => 'reign_woo_single_product_image',
					'label'    => esc_html__( 'Product Image Layout', 'reign' ),
					'section'  => 'reign_woocommerce_single_product_section',
					'default'  => 'product_image_layout1',
					'choices'  => array(
						'product_image_layout1' => REIGN_THEME_URI . '/lib/images/image-layout1.svg',
						'product_image_layout2' => REIGN_THEME_URI . '/lib/images/image-layout2.svg',
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_woo_single_product_image_divider',
					'section'  => 'reign_woocommerce_single_product_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_woo_summary_bar',
					'label'       => esc_html__( 'Review Summary Bar', 'reign' ),
					'description' => esc_html__( 'Enable/Disable product review summary bar on product single page.', 'reign' ),
					'section'     => 'reign_woocommerce_single_product_section',
					'priority'    => 10,
					'default'     => 'on',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_woo_summary_bar_divider',
					'section'  => 'reign_woocommerce_single_product_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Radio(
				array(
					'settings'    => 'reign_woo_review_position',
					'label'       => esc_html__( 'Review Position', 'reign' ),
					'description' => esc_html__( 'Allow you to change reviews postion on product single page.', 'reign' ),
					'section'     => 'reign_woocommerce_single_product_section',
					'priority'    => 10,
					'default'     => 'inside',
					'choices'     => array(
						'inside'  => esc_html__( 'Inside Tab', 'reign' ),
						'outside' => esc_html__( 'Outside Tab', 'reign' ),
					),
				)
			);

			// Cart Page.
			new \Kirki\Field\Select(
				array(
					'settings' => 'reign_woo_cart_layout',
					'label'    => esc_html__( 'Cart Page Layouts', 'reign' ),
					'section'  => 'reign_woocommerce_cart_section',
					'default'  => 'woo_cart_default',
					'choices'  => array(
						'woo_cart_default' => esc_html__( 'Default', 'reign' ),
						'woo_cart_layout1' => esc_html__( 'Layout 1', 'reign' ),
						'woo_cart_layout2' => esc_html__( 'Layout 2', 'reign' ),
					),
				)
			);

			// Checkout Page.
			new \Kirki\Field\Select(
				array(
					'settings' => 'reign_woo_checkout_layout',
					'label'    => esc_html__( 'Checkout Page Layouts', 'reign' ),
					'section'  => 'woocommerce_checkout',
					'default'  => 'woo_checkout_default',
					'priority' => 2,
					'choices'  => array(
						'woo_checkout_default' => esc_html__( 'Default', 'reign' ),
						'woo_checkout_layout1' => esc_html__( 'Layout 1', 'reign' ),
					),
				)
			);

			// My Account Page.
			new \Kirki\Field\Select(
				array(
					'settings' => 'reign_woo_myaccount_layout',
					'label'    => esc_html__( 'My Account Layouts', 'reign' ),
					'section'  => 'reign_woocommerce_my_account_section',
					'default'  => 'woo_myaccount_default',
					'choices'  => array(
						'woo_myaccount_default' => esc_html__( 'Default', 'reign' ),
						'woo_myaccount_layout1' => esc_html__( 'Layout 1', 'reign' ),
						'woo_myaccount_layout2' => esc_html__( 'Layout 2', 'reign' ),
						'woo_myaccount_layout3' => esc_html__( 'Layout 3', 'reign' ),
					),
				)
			);

			new \Kirki\Pro\Field\Divider(
				array(
					'settings' => 'reign_woo_myaccount_layout_divider',
					'section'  => 'reign_woocommerce_shop_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			new \Kirki\Field\Checkbox_Switch(
				array(
					'settings'    => 'reign_woo_myaccount_menu_toggle',
					'label'       => esc_html__( 'My Account Toggle Menu', 'reign' ),
					'description' => esc_html__( 'Note: This setting work only for small screen.', 'reign' ),
					'section'     => 'reign_woocommerce_my_account_section',
					'priority'    => 10,
					'default'     => '',
					'choices'     => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);
		}
	}

endif;

/**
 * Main instance of Reign_Kirki_WooCommerce.
 *
 * @return Reign_Kirki_WooCommerce
 */
Reign_Kirki_WooCommerce::instance();
