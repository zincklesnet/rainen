<?php
/**
 * Reign Customizer WooCommerce Fields
 *
 * Ported from `lib/kirki-addon/options/woocommerce/class-reign-kirki-woocommerce.php`.
 * Kirki removal — Phase 1 atomic sweep. Args arrays preserved verbatim.
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Customizer_WooCommerce_Fields' ) ) :

	/**
	 * @class Reign_Customizer_WooCommerce_Fields
	 */
	class Reign_Customizer_WooCommerce_Fields {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Customizer_WooCommerce_Fields
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Customizer_WooCommerce_Fields Instance.
		 *
		 * Ensures only one instance is loaded or can be loaded.
		 *
		 * @return Reign_Customizer_WooCommerce_Fields - Main instance.
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

			\Reign\Customizer_Framework\Section::add(
				'reign_woocommerce_shop_section',
				array(
					'title'       => esc_html__( 'Shop Page', 'reign' ),
					'priority'    => 5,
					'description' => esc_html__( 'Layout and controls for the product listing (shop / archive) page.', 'reign' ),
					'panel'       => 'woocommerce',
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_woocommerce_single_product_section',
				array(
					'title'       => esc_html__( 'Single Product Page', 'reign' ),
					'priority'    => 6,
					'description' => esc_html__( 'Layout and options for an individual product page.', 'reign' ),
					'panel'       => 'woocommerce',
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_woocommerce_cart_section',
				array(
					'title'       => esc_html__( 'Cart Page', 'reign' ),
					'priority'    => 7,
					'description' => esc_html__( 'Layout for the cart page.', 'reign' ),
					'panel'       => 'woocommerce',
				)
			);

			\Reign\Customizer_Framework\Section::add(
				'reign_woocommerce_my_account_section',
				array(
					'title'       => esc_html__( 'My Account Page', 'reign' ),
					'priority'    => 8,
					'description' => esc_html__( 'Layout for the customer My Account page.', 'reign' ),
					'panel'       => 'woocommerce',
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			// Shop Page.
			\Reign\Customizer_Framework\Field::add(
				'switch',
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

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings' => 'reign_woo_layout_view_buttons_divider',
					'section'  => 'reign_woocommerce_shop_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'select',
				array(
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

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings' => 'reign_woo_product_layout_divider',
					'section'  => 'reign_woocommerce_shop_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'switch',
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

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings' => 'reign_woo_shop_sort_divider',
					'section'  => 'reign_woocommerce_shop_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'switch',
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

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings' => 'reign_woo_shop_result_count_divider',
					'section'  => 'reign_woocommerce_shop_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'switch',
				array(
					'settings'          => 'reign_woo_off_canvas_filter',
					'label'             => esc_html__( 'Display Filter Button', 'reign' ),
					'description'       => esc_html__( 'Show a Filter button on the shop page that opens an off-canvas filter sidebar. Add filter widgets under Appearance > Widgets > Off Canvas Sidebar.', 'reign' ),
					'section'           => 'reign_woocommerce_shop_section',
					'priority'          => 10,
					'default'           => 'off',
					'sanitize_callback' => 'reign_sanitize_checkbox',
					'choices'           => array(
						'on'  => esc_html__( 'Enable', 'reign' ),
						'off' => esc_html__( 'Disable', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'text',
				array(
					'settings'        => 'reign_woo_off_canvas_filter_text',
					'label'           => esc_html__( 'Filter Text', 'reign' ),
					'description'     => esc_html__( 'Heading shown on the filter button and panel.', 'reign' ),
					'section'         => 'reign_woocommerce_shop_section',
					'default'         => esc_html__( 'Filter', 'reign' ),
					'active_callback' => 'reign_has_woo_filter_button',
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings' => 'reign_woo_off_canvas_filter_divider',
					'section'  => 'reign_woocommerce_shop_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
					'active_callback' => 'reign_has_woo_filter_button',
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'switch',
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
			\Reign\Customizer_Framework\Field::add(
				'select',
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

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings' => 'reign_woo_single_product_layout_divider',
					'section'  => 'reign_woocommerce_single_product_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'radio_image',
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

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings' => 'reign_woo_single_product_image_divider',
					'section'  => 'reign_woocommerce_single_product_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'switch',
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

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings' => 'reign_woo_summary_bar_divider',
					'section'  => 'reign_woocommerce_single_product_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'radio',
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
			\Reign\Customizer_Framework\Field::add(
				'select',
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
			\Reign\Customizer_Framework\Field::add(
				'select',
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
			\Reign\Customizer_Framework\Field::add(
				'select',
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

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings' => 'reign_woo_myaccount_layout_divider',
					'section'  => 'reign_woocommerce_my_account_section',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'switch',
				array(
					'settings'    => 'reign_woo_myaccount_menu_toggle',
					'label'       => esc_html__( 'My Account Toggle Menu', 'reign' ),
					'description' => esc_html__( 'Note: This setting work only for small screen.', 'reign' ),
					'section'     => 'reign_woocommerce_my_account_section',
					'priority'    => 10,
					'default'     => 'off',
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
 * Main instance of Reign_Customizer_WooCommerce_Fields.
 *
 * @return Reign_Customizer_WooCommerce_Fields
 */
Reign_Customizer_WooCommerce_Fields::instance();
