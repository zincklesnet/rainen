<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RTM_WooCommerce_Customization' ) ) :

	/**
	 * @class RTM_WooCommerce_Customization
	 */
	class RTM_WooCommerce_Customization {

		/**
		 * The single instance of the class.
		 *
		 * @var RTM_WooCommerce_Customization
		 */
		protected static $_instance = null;

		/**
		 * Main RTM_WooCommerce_Customization Instance.
		 *
		 * Ensures only one instance of RTM_WooCommerce_Customization is loaded or can be loaded.
		 *
		 * @return RTM_WooCommerce_Customization - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * RTM_WooCommerce_Customization Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {

			/**
			* Removing woocommerce breadcrumb.
			*/
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );

			/**
			* Removes the "shop" title on the main shop page.
			*/
			add_filter( 'woocommerce_show_page_title', array( $this, 'remove_shop_page_title' ), 10 );

			/*
			 * Reign WooCommerce Shortcode to render product categories.
			 */
			add_shortcode( 'rg_woo_product_categories', array( $this, 'render_rg_woo_product_categories' ) );

			/*
			 * Reign WooCommerce Shortcode to render product categories & subcategory together.
			 */
			add_shortcode( 'rg_woo_product_category_with_subcategory', array( $this, 'render_rg_woo_product_category_with_subcategory' ) );

			/**
			* Modify page title for product-category and product-tag.
			*/
			add_filter( 'reign_page_header_section_title', array( $this, 'manage_page_header_section_title' ), 10, 1 );
		}

		/**
		 * Modify the page header section title for taxonomy archives.
		 *
		 * This function changes the page header section title to the name of the current taxonomy term
		 * if the current page is a taxonomy archive for either 'product_cat' or 'product_tag'.
		 *
		 * @param string $title The current title of the page header section.
		 *
		 * @return string The modified title, if on a taxonomy archive page, or the original title otherwise.
		 */
		public function manage_page_header_section_title( $title ) {
			if ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {
				$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
				if ( isset( $term->name ) ) {
					$title = $term->name;
				}
			}
			return $title;
		}

		/**
		 * Renders the WooCommerce product categories with various customizable options.
		 *
		 * This function generates and returns the HTML for displaying WooCommerce product categories based on
		 * the attributes provided. The attributes control aspects such as the number of categories to display,
		 * the layout of the categories, and whether to show parent categories only or include category counts.
		 *
		 * The function performs the following steps:
		 * 1. Merges default attribute values with any provided values using `wp_parse_args()`.
		 * 2. Starts output buffering with `ob_start()`.
		 * 3. Includes the template file that generates the category list.
		 * 4. Retrieves the buffered output and returns it as a string.
		 *
		 * @param array $atts An associative array of attributes used to customize the category display.
		 *                    The following keys are recognized:
		 *                    - 'title' (string): The title to display above the categories. Default is 'Product Categories'.
		 *                    - 'per_row' (int): Number of categories to display per row. Default is 3.
		 *                    - 'count' (int): Total number of categories to display. Default is 6.
		 *                    - 'show_parent_categories_only' (bool): Whether to display only parent categories. Default is false.
		 *                    - 'show_count' (bool): Whether to show the number of products in each category. Default is true.
		 *                    - 'enable_slider' (bool): Whether to enable a slider for category display. Default is false.
		 *                    - 'layout' (string): The layout type to use for displaying categories. Default is 'layout-type-1'.
		 *                    - 'selected_categories' (array): Array of specific categories to display. Default is an empty array.
		 *
		 * @return string The HTML content for displaying the product categories.
		 */
		public function render_rg_woo_product_categories( $atts = array() ) {
			$atts = wp_parse_args(
				(array) $atts,
				array(
					'title'                       => esc_html__( 'Product Categories', 'reign' ),
					'per_row'                     => 3,
					'count'                       => 6,
					'show_parent_categories_only' => false,
					'show_count'                  => true,
					'enable_slider'               => false,
					'layout'                      => 'layout-type-1',
					'selected_categories'         => array(),
				)
			);

			ob_start();

			// Sanitize each attribute as needed before use in the template.
			include REIGN_THEME_DIR . '/template-parts/widgets/rg-woo-product-category.php';

			return ob_get_clean();
		}

		/**
		 * Renders WooCommerce product categories together with their subcategories.
		 *
		 * This function generates and returns the HTML for displaying WooCommerce product categories and their
		 * subcategories based on the attributes provided. The attributes control aspects such as the number of
		 * categories and subcategories to display, the layout, and whether to enable a slider.
		 *
		 * The function performs the following steps:
		 * 1. Merges default attribute values with any provided values using `wp_parse_args()`.
		 * 2. Starts output buffering with `ob_start()`.
		 * 3. Includes the template file that generates the category and subcategory list.
		 * 4. Retrieves the buffered output and returns it as a string.
		 *
		 * @param array $atts An associative array of attributes used to customize the category and subcategory display.
		 *                    The following keys are recognized:
		 *                    - 'title' (string): The title to display above the categories. Default is 'Product Categories'.
		 *                    - 'per_row' (int): Number of categories to display per row. Default is 3.
		 *                    - 'count' (int): Total number of categories to display. Default is 6.
		 *                    - 'subcat_count' (int): Number of subcategories to display per category. Default is 4.
		 *                    - 'enable_slider' (bool): Whether to enable a slider for category display. Default is false.
		 *                    - 'layout' (string): The layout type to use for displaying categories and subcategories. Default is 'layout-type-1'.
		 *                    - 'selected_categories' (array): Array of specific categories to display. Default is an empty array.
		 *
		 * @return string The HTML content for displaying the product categories and their subcategories.
		 */
		public function render_rg_woo_product_category_with_subcategory( $atts = array() ) {
			$atts = wp_parse_args(
				(array) $atts,
				array(
					'title'               => esc_html__( 'Product Categories', 'reign' ),
					'per_row'             => 3,
					'count'               => 6,
					'subcat_count'        => 4,
					'enable_slider'       => false,
					'layout'              => 'layout-type-1',
					'selected_categories' => array(),
				)
			);

			ob_start();

			// Sanitize each attribute as needed before use in the template.
			include REIGN_THEME_DIR . '/template-parts/widgets/rg-woo-product-category-with-subcategory.php';

			return ob_get_clean();
		}

		public function remove_shop_page_title( $show_title ) {
			$show_title = false;
			return $show_title;
		}
	}

endif;

/**
 * Main instance of RTM_WooCommerce_Customization.
 *
 * @return RTM_WooCommerce_Customization
 */
RTM_WooCommerce_Customization::instance();
