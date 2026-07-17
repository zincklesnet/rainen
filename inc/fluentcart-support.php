<?php
/**
 * FluentCart Support for Reign Theme
 *
 * @package Reign
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if FluentCart is active.
if ( ! defined( 'FLUENTCART_PLUGIN_FILE_PATH' ) ) {
	return;
}

/**
 * Reign FluentCart Support Class
 */
class Reign_FluentCart_Support {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Hide duplicate elements on FluentCart single products.
		add_action( 'wp', array( $this, 'reign_fluentcart_single_page_setup' ) );

		// Ensure FluentCart post types have proper names in customizer.
		add_filter( 'reign_customizer_supported_post_types', array( $this, 'reign_fluentcart_rename_post_types' ) );

		// Add FluentCart cart icon support.
		add_action( 'after_setup_theme', array( $this, 'reign_fluentcart_cart_icon_support' ) );

		// Set defaults for post types on theme activation only.
		add_action( 'after_switch_theme', array( $this, 'reign_fluentcart_set_post_type_defaults' ) );

		// Inject FluentCart cart choice into header icon-picker fields when
		// FluentCart owns the cart. Generic framework filter, applies to
		// every Field::add() call so we self-gate on the setting id.
		add_filter( 'reign_customizer_field_args', array( $this, 'reign_fluentcart_add_to_icon_choices' ), 10, 2 );

		// Add body classes for FluentCart pages.
		add_filter( 'body_class', array( $this, 'reign_fluentcart_body_classes' ) );

		// Disable floating cart button on checkout/receipt pages.
		add_filter( 'fluent_cart/buttons/enable_floating_cart_button', array( $this, 'reign_fluentcart_disable_floating_button' ), 10, 2 );

		// Force full-width layout for FluentCart pages (via meta filter).
		add_filter( 'get_post_metadata', array( $this, 'reign_fluentcart_force_layout' ), 10, 4 );
	}

	/**
	 * Setup FluentCart single product pages
	 */
	public function reign_fluentcart_single_page_setup() {
		if ( ! is_singular( 'fluent-products' ) ) {
			return;
		}

		// Hide the entire page header section for FluentCart products.
		add_filter( 'reign_page_header_enable', '__return_false' );

		// Hide featured image as FluentCart includes product media.
		add_filter( 'reign_single_post_featured_image', '__return_false' );
	}

	/**
	 * Rename FluentCart post types in customizer
	 */
	public function reign_fluentcart_rename_post_types( $post_types ) {
		foreach ( $post_types as &$post_type ) {
			if ( $post_type['slug'] === 'fluent-products' ) {
				$post_type['name'] = __( 'FluentCart Products', 'reign' );
			}
		}
		return $post_types;
	}

	/**
	 * Add FluentCart cart icon support
	 */
	public function reign_fluentcart_cart_icon_support() {
		// Only if WooCommerce and SureCart are not active.
		if ( class_exists( 'WooCommerce' ) || defined( 'SURECART_PLUGIN_FILE' ) ) {
			return;
		}

		// Create a custom function for FluentCart cart count.
		if ( ! function_exists( 'reign_fc_cart_count' ) ) {
			/**
			 * Display FluentCart cart count
			 */
			function reign_fc_cart_count() {
				$item_count = 0;

				if ( class_exists( '\\FluentCart\\Api\\Resource\\FrontendResource\\CartResource' ) ) {
					$cart_status = \FluentCart\Api\Resource\FrontendResource\CartResource::getStatus();
					if ( ! empty( $cart_status['cart_data'] ) ) {
						$item_count = count( $cart_status['cart_data'] );
					}
				}
				?>
				<div class="rg-fluentcart-cart-icon-wrap rg-icon-wrap">
					<a href="#" class="cart-icon-wrap fcart-cart-toogle-button" data-fluent-cart-cart-expand-button aria-label="<?php esc_attr_e( 'View Shopping Cart', 'reign' ); ?>">
						<span class="fa fa-shopping-cart"></span>
						<sup class="rg-count fc-cart-count fct-cart-badge-count" data-cart-badge-count><?php echo esc_html( $item_count ); ?></sup>
					</a>
				</div>
				<?php
			}
		}

		// Only create template file on theme activation or admin area.
		if ( is_admin() || did_action( 'after_switch_theme' ) ) {
			$this->reign_fluentcart_create_cart_template();
		}

		// Hook to add FluentCart cart to header icons set.
		add_filter( 'customize_register', array( $this, 'reign_fluentcart_add_cart_to_customizer' ), 20 );
		add_filter( 'reign_header_default_icons', array( $this, 'reign_fluentcart_add_cart_to_default_icons' ) );
		add_filter( 'reign_mobile_header_default_icons', array( $this, 'reign_fluentcart_add_cart_to_default_icons' ) );
	}

	/**
	 * Add FluentCart cart icon to default header icons
	 */
	public function reign_fluentcart_add_cart_to_default_icons( $icons ) {
		// Don't add if already present.
		if ( ! in_array( 'fluentcart-cart', $icons, true ) ) {
			// Add FluentCart cart after regular cart or after search.
			$cart_key = array_search( 'cart', $icons, true );
			if ( false !== $cart_key ) {
				array_splice( $icons, $cart_key + 1, 0, 'fluentcart-cart' );
			} else {
				$search_key = array_search( 'search', $icons, true );
				if ( false !== $search_key ) {
					array_splice( $icons, $search_key + 1, 0, 'fluentcart-cart' );
				} else {
					array_unshift( $icons, 'fluentcart-cart' );
				}
			}
		}
		return $icons;
	}

	/**
	 * Add FluentCart cart option to customizer header icons
	 */
	public function reign_fluentcart_add_cart_to_customizer( $wp_customize ) {
		// Desktop header icons.
		$desktop_control = $wp_customize->get_control( 'reign_header_icons_set' );
		if ( $desktop_control && isset( $desktop_control->choices ) ) {
			if ( ! isset( $desktop_control->choices['fluentcart-cart'] ) ) {
				$desktop_control->choices['fluentcart-cart'] = __( 'FluentCart Cart', 'reign' );
			}
		}

		// Mobile header icons.
		$mobile_control = $wp_customize->get_control( 'reign_mobile_header_icons_set' );
		if ( $mobile_control && isset( $mobile_control->choices ) ) {
			if ( ! isset( $mobile_control->choices['fluentcart-cart'] ) ) {
				$mobile_control->choices['fluentcart-cart'] = __( 'FluentCart Cart', 'reign' );
			}
		}
	}

	/**
	 * Inject 'fluentcart-cart' into the header icon-picker choices when
	 * FluentCart owns the cart (i.e. neither WooCommerce nor SureCart is
	 * active). Hooks `reign_customizer_field_args` so the choice becomes
	 * available in the customizer sortable + the saved-value path.
	 *
	 * @param array                $args         Field args.
	 * @param \WP_Customize_Manager $wp_customize Customizer manager.
	 * @return array
	 */
	public function reign_fluentcart_add_to_icon_choices( $args, $wp_customize ) {
		if ( ! isset( $args['settings'] ) ) {
			return $args;
		}
		$target_settings = array( 'reign_header_icons_set', 'reign_mobile_header_icons_set' );
		if ( ! in_array( $args['settings'], $target_settings, true ) ) {
			return $args;
		}
		// Defer to WooCommerce / SureCart if either is active.
		if ( class_exists( 'WooCommerce' ) || defined( 'SURECART_PLUGIN_FILE' ) ) {
			return $args;
		}
		if ( isset( $args['choices'] ) && is_array( $args['choices'] ) ) {
			$args['choices']['fluentcart-cart'] = __( 'FluentCart Cart', 'reign' );
		}
		return $args;
	}

	/**
	 * Set default layouts for FluentCart post types on theme activation
	 */
	public function reign_fluentcart_set_post_type_defaults() {
		// Check if we've already set defaults.
		if ( get_option( 'reign_fluentcart_post_type_defaults_set' ) ) {
			return;
		}

		// Set full width layout for FluentCart post types.
		set_theme_mod( 'reign_fluent-products_archive_layout', 'full_width' );
		set_theme_mod( 'reign_fluent-products_single_layout', 'full_width' );

		// Mark that we've set the defaults.
		update_option( 'reign_fluentcart_post_type_defaults_set', true );
	}

	/**
	 * Create cart template file
	 */
	private function reign_fluentcart_create_cart_template() {
		$header_icons_dir = get_template_directory() . '/template-parts/header-icons/';
		$fluentcart_cart_file = $header_icons_dir . 'fluentcart-cart.php';

		if ( ! file_exists( $fluentcart_cart_file ) ) {
			// Create the file content.
			$file_content = "<?php\n/**\n * FluentCart Cart Icon\n *\n * Template part for displaying the FluentCart cart count\n *\n * @package Reign\n */\n\nif ( function_exists( 'reign_fc_cart_count' ) ) {\n\treign_fc_cart_count();\n}\n";

			// Try to create the file.
			if ( is_writable( $header_icons_dir ) ) {
				file_put_contents( $fluentcart_cart_file, $file_content );
			}
		}
	}

	/**
	 * Add body classes for FluentCart pages
	 */
	public function reign_fluentcart_body_classes( $classes ) {
		// Get FluentCart store settings (all page IDs are stored here).
		$store_settings = get_option( 'fluent_cart_store_settings', array() );

		// Map of page keys to body class names.
		$page_classes = array(
			'checkout_page_id'           => 'fluent-cart-checkout',
			'receipt_page_id'            => 'fluent-cart-receipt',
			'shop_page_id'               => 'fluent-cart-shop',
			'cart_page_id'               => 'fluent-cart-cart',
			'customer_profile_page_id'   => 'fluent-cart-profile',
		);

		// Check each FluentCart page and add appropriate body class.
		foreach ( $page_classes as $page_key => $body_class ) {
			$page_id = ! empty( $store_settings[ $page_key ] ) ? $store_settings[ $page_key ] : 0;
			if ( $page_id && is_page( $page_id ) ) {
				$classes[] = $body_class;
			}
		}

		// Add class for single product pages.
		if ( is_singular( 'fluent-products' ) ) {
			$classes[] = 'fluent-cart-product';
		}

		// Add class for product archive pages (categories, brands, main archive).
		if ( is_post_type_archive( 'fluent-products' ) || is_tax( 'product-categories' ) || is_tax( 'product-brands' ) ) {
			$classes[] = 'fluent-cart-archive';
		}

		return $classes;
	}

	/**
	 * Disable FluentCart floating button on checkout/receipt pages
	 */
	public function reign_fluentcart_disable_floating_button( $enabled, $args ) {
		// Get current page ID.
		$current_page_id = get_the_ID();
		if ( ! $current_page_id ) {
			return $enabled;
		}

		// Get FluentCart store settings.
		$store_settings = get_option( 'fluent_cart_store_settings', array() );

		// Disable on checkout and receipt pages.
		if ( ( ! empty( $store_settings['checkout_page_id'] ) && (int) $store_settings['checkout_page_id'] === (int) $current_page_id ) ||
		     ( ! empty( $store_settings['receipt_page_id'] ) && (int) $store_settings['receipt_page_id'] === (int) $current_page_id ) ) {
			return false;
		}

		return $enabled;
	}

	/**
	 * Force full-width layout for FluentCart pages via meta filter
	 *
	 * This filter intercepts post meta reads and sets full-width layout
	 * for FluentCart pages that don't have a layout set.
	 * If a layout is already configured, it respects that setting.
	 */
	public function reign_fluentcart_force_layout( $metadata, $object_id, $meta_key, $single ) {
		// Only filter the specific meta key we need.
		if ( 'reign_wbcom_metabox_data' !== $meta_key ) {
			return $metadata;
		}

		// Only apply on frontend, not in admin.
		if ( is_admin() ) {
			return $metadata;
		}

		// Check if this page is mapped in FluentCart.
		if ( ! $this->is_fluentcart_page( $object_id ) ) {
			return $metadata;
		}

		// Remove this filter temporarily to get the actual meta.
		remove_filter( 'get_post_metadata', array( $this, 'reign_fluentcart_force_layout' ), 10 );
		$actual_meta = get_post_meta( $object_id, 'reign_wbcom_metabox_data', true );
		add_filter( 'get_post_metadata', array( $this, 'reign_fluentcart_force_layout' ), 10, 4 );

		// Ensure we have an array structure.
		if ( ! is_array( $actual_meta ) ) {
			$actual_meta = array();
		}

		// Only set full-width if no layout is configured or if set to default ('0').
		if ( ! isset( $actual_meta['layout']['site_layout'] ) || $actual_meta['layout']['site_layout'] === '0' || empty( $actual_meta['layout']['site_layout'] ) ) {
			$actual_meta['layout']['site_layout'] = 'full_width';
		}

		// Return as single value if requested, array otherwise.
		return $single ? array( $actual_meta ) : array( array( $actual_meta ) );
	}

	/**
	 * Check if a page ID is mapped in FluentCart settings
	 *
	 * @param int $page_id Page ID to check.
	 * @return bool True if page is a FluentCart page.
	 */
	private function is_fluentcart_page( $page_id ) {
		if ( ! $page_id ) {
			return false;
		}

		// Get FluentCart store settings (cached statically for performance).
		static $store_settings = null;
		if ( null === $store_settings ) {
			$store_settings = get_option( 'fluent_cart_store_settings', array() );
		}

		// Page keys to check in FluentCart settings.
		$page_keys = array(
			'checkout_page_id',
			'receipt_page_id',
			'shop_page_id',
			'cart_page_id',
			'customer_profile_page_id',
		);

		// Check if this page ID matches any FluentCart mapped page.
		foreach ( $page_keys as $key ) {
			if ( ! empty( $store_settings[ $key ] ) && (int) $store_settings[ $key ] === (int) $page_id ) {
				return true;
			}
		}

		return false;
	}

}

// Initialize FluentCart support.
new Reign_FluentCart_Support();
