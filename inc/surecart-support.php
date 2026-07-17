<?php
/**
 * SureCart Support for Reign Theme
 *
 * @package Reign
 */

// File name follows the theme's `inc/*-support.php` plugin-integration convention.
// phpcs:disable WordPress.Files.FileName

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if SureCart is active.
if ( ! defined( 'SURECART_PLUGIN_FILE' ) ) {
	return;
}

/**
 * Reign SureCart Support Class
 */
class Reign_SureCart_Support {

	/**
	 * Constructor
	 */
	public function __construct() {
		// Hide duplicate elements on SureCart single products.
		add_action( 'wp', array( $this, 'reign_surecart_single_page_setup' ) );

		// Ensure SureCart post types have proper names in customizer.
		add_filter( 'reign_customizer_supported_post_types', array( $this, 'reign_surecart_rename_post_types' ) );

		// Add SureCart cart icon support.
		add_action( 'after_setup_theme', array( $this, 'reign_surecart_cart_icon_support' ) );

		// Hook into when SureCart sets its page IDs (most performant approach).
		add_action( 'update_option_surecart_shop_page_id', array( $this, 'reign_surecart_page_created' ), 10, 2 );
		add_action( 'update_option_surecart_checkout_page_id', array( $this, 'reign_surecart_page_created' ), 10, 2 );
		add_action( 'update_option_surecart_cart_page_id', array( $this, 'reign_surecart_page_created' ), 10, 2 );
		add_action( 'update_option_surecart_dashboard_page_id', array( $this, 'reign_surecart_page_created' ), 10, 2 );

		// Set defaults for post types on theme activation only.
		add_action( 'after_switch_theme', array( $this, 'reign_surecart_set_post_type_defaults' ) );

		// Inject SureCart cart choice into header icon-picker fields when
		// SureCart owns the cart. Generic framework filter, applies to
		// every Field::add() call so we self-gate on the setting id.
		add_filter( 'reign_customizer_field_args', array( $this, 'reign_surecart_add_to_icon_choices' ), 10, 2 );
	}

	/**
	 * Setup SureCart single product pages
	 */
	public function reign_surecart_single_page_setup() {
		if ( ! is_singular( array( 'sc_product', 'sc_collection', 'sc_upsell' ) ) ) {
			return;
		}

		// Hide the entire page header section for SureCart products.
		add_filter( 'reign_page_header_enable', '__return_false' );

		// Hide featured image as SureCart includes product media.
		add_filter( 'reign_single_post_featured_image', '__return_false' );

		// Add CSS to hide duplicate elements.
		add_action( 'wp_head', array( $this, 'reign_surecart_hide_duplicate_elements' ) );
	}

	/**
	 * Hide duplicate elements with CSS for SureCart products
	 */
	public function reign_surecart_hide_duplicate_elements() {
		?>
		<style type="text/css">
			/* Hide duplicate title in post meta */
			.single-sc_product .rg-post-meta-info-wrapper .entry-header,
			.single-sc_collection .rg-post-meta-info-wrapper .entry-header,
			.single-sc_upsell .rg-post-meta-info-wrapper .entry-header {
				display: none !important;
			}
			
			/* Hide featured image */
			.single-sc_product .entry-media.rg-post-thumbnail,
			.single-sc_collection .entry-media.rg-post-thumbnail,
			.single-sc_upsell .entry-media.rg-post-thumbnail {
				display: none !important;
			}
			
			/* Hide any page header that might show */
			.single-sc_collection .lm-site-header-section,
			.single-sc_upsell .lm-site-header-section {
				display: none !important;
			}
		</style>
		<?php
	}

	/**
	 * Rename SureCart post types in customizer
	 */
	public function reign_surecart_rename_post_types( $post_types ) {
		foreach ( $post_types as &$post_type ) {
			switch ( $post_type['slug'] ) {
				case 'sc_product':
					$post_type['name'] = __( 'SureCart Products', 'reign' );
					break;
				case 'sc_collection':
					$post_type['name'] = __( 'SureCart Collections', 'reign' );
					break;
				case 'sc_upsell':
					$post_type['name'] = __( 'SureCart Upsells', 'reign' );
					break;
			}
		}
		return $post_types;
	}

	/**
	 * Add SureCart cart icon support
	 */
	public function reign_surecart_cart_icon_support() {
		// Create a custom function for SureCart cart count similar to WooCommerce.
		if ( ! function_exists( 'reign_sc_cart_count' ) ) {
			function reign_sc_cart_count() {
				?>
				<div class="rg-surecart-cart-icon-wrap rg-icon-wrap">
					<?php
					// Use SureCart's cart menu icon block.
					echo do_blocks( '<!-- wp:surecart/cart-menu-icon {"cart_icon":"shopping-bag","cart_menu_always_shown":true} /-->' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- do_blocks() returns rendered, safe block HTML from a static markup string.
					?>
				</div>
				<?php
			}
		}

		// Only create template file on theme activation or admin area.
		if ( is_admin() || did_action( 'after_switch_theme' ) ) {
			$this->reign_surecart_create_cart_template();
		}

		// Hook to add SureCart cart to header icons set.
		add_filter( 'customize_register', array( $this, 'reign_surecart_add_cart_to_customizer' ), 20 );
		add_filter( 'reign_header_default_icons', array( $this, 'reign_surecart_add_cart_to_default_icons' ) );
		add_filter( 'reign_mobile_header_default_icons', array( $this, 'reign_surecart_add_cart_to_default_icons' ) );
	}

	/**
	 * Add SureCart cart icon to default header icons
	 */
	public function reign_surecart_add_cart_to_default_icons( $icons ) {
		// Don't add if already present.
		if ( ! in_array( 'surecart-cart', $icons, true ) ) {
			// Add SureCart cart after regular cart or after search.
			$cart_key = array_search( 'cart', $icons, true );
			if ( false !== $cart_key ) {
				array_splice( $icons, $cart_key + 1, 0, 'surecart-cart' );
			} else {
				$search_key = array_search( 'search', $icons, true );
				if ( false !== $search_key ) {
					array_splice( $icons, $search_key + 1, 0, 'surecart-cart' );
				} else {
					array_unshift( $icons, 'surecart-cart' );
				}
			}
		}
		return $icons;
	}

	/**
	 * Add SureCart cart option to customizer header icons
	 */
	public function reign_surecart_add_cart_to_customizer( $wp_customize ) {
		// Desktop header icons.
		$desktop_control = $wp_customize->get_control( 'reign_header_icons_set' );
		if ( $desktop_control && isset( $desktop_control->choices ) ) {
			if ( ! isset( $desktop_control->choices['surecart-cart'] ) ) {
				$desktop_control->choices['surecart-cart'] = __( 'SureCart Cart', 'reign' );
			}
		}

		// Mobile header icons.
		$mobile_control = $wp_customize->get_control( 'reign_mobile_header_icons_set' );
		if ( $mobile_control && isset( $mobile_control->choices ) ) {
			if ( ! isset( $mobile_control->choices['surecart-cart'] ) ) {
				$mobile_control->choices['surecart-cart'] = __( 'SureCart Cart', 'reign' );
			}
		}
	}

	/**
	 * Inject 'surecart-cart' into the header icon-picker choices when
	 * SureCart owns the cart (defers to WooCommerce if both are active).
	 * Hooks `reign_customizer_field_args` so the choice becomes available
	 * in the customizer sortable + the saved-value path.
	 *
	 * @param array                $args         Field args.
	 * @param \WP_Customize_Manager $wp_customize Customizer manager.
	 * @return array
	 */
	public function reign_surecart_add_to_icon_choices( $args, $wp_customize ) {
		if ( ! isset( $args['settings'] ) ) {
			return $args;
		}
		$target_settings = array( 'reign_header_icons_set', 'reign_mobile_header_icons_set' );
		if ( ! in_array( $args['settings'], $target_settings, true ) ) {
			return $args;
		}
		// WooCommerce takes precedence if both are active.
		if ( class_exists( 'WooCommerce' ) ) {
			return $args;
		}
		if ( isset( $args['choices'] ) && is_array( $args['choices'] ) ) {
			$args['choices']['surecart-cart'] = __( 'SureCart Cart', 'reign' );
		}
		return $args;
	}

	/**
	 * Handle when SureCart creates or updates a page
	 */
	public function reign_surecart_page_created( $old_value, $new_value ) {
		// Only process if a new page ID is set.
		if ( ! $new_value || $new_value === $old_value ) {
			return;
		}

		// Set full width layout for the page.
		$this->reign_surecart_set_page_layout( $new_value );
	}

	/**
	 * Set layout for a specific page
	 */
	private function reign_surecart_set_page_layout( $page_id ) {
		if ( ! $page_id ) {
			return;
		}

		// Get existing meta data.
		$meta_data = get_post_meta( $page_id, 'reign_wbcom_metabox_data', true );

		// Initialize if not exists.
		if ( ! is_array( $meta_data ) ) {
			$meta_data = array();
		}

		// Only set if not already set.
		if ( ! isset( $meta_data['layout']['site_layout'] ) || '0' === $meta_data['layout']['site_layout'] ) {
			$meta_data['layout']['site_layout'] = 'full_width';
			update_post_meta( $page_id, 'reign_wbcom_metabox_data', $meta_data );
		}
	}

	/**
	 * Set default layouts for SureCart post types on theme activation
	 */
	public function reign_surecart_set_post_type_defaults() {
		// Check if we've already set defaults.
		if ( get_option( 'reign_surecart_post_type_defaults_set' ) ) {
			return;
		}

		// Set full width layout for SureCart post types.
		$surecart_post_types = array( 'sc_product', 'sc_collection', 'sc_upsell' );

		foreach ( $surecart_post_types as $post_type ) {
			// Archive layout.
			set_theme_mod( 'reign_' . $post_type . '_archive_layout', 'full_width' );

			// Single layout.
			set_theme_mod( 'reign_' . $post_type . '_single_layout', 'full_width' );
		}

		// Also set layouts for existing pages.
		$pages = array(
			get_option( 'surecart_shop_page_id' ),
			get_option( 'surecart_checkout_page_id' ),
			get_option( 'surecart_cart_page_id' ),
			get_option( 'surecart_dashboard_page_id' ),
		);

		foreach ( array_filter( $pages ) as $page_id ) {
			$this->reign_surecart_set_page_layout( $page_id );
		}

		// Mark that we've set the defaults.
		update_option( 'reign_surecart_post_type_defaults_set', true );
	}

	/**
	 * Create cart template file
	 */
	private function reign_surecart_create_cart_template() {
		$header_icons_dir   = get_template_directory() . '/template-parts/header-icons/';
		$surecart_cart_file = $header_icons_dir . 'surecart-cart.php';

		if ( ! file_exists( $surecart_cart_file ) ) {
			// Create the file content.
			$file_content = "<?php\n/**\n * SureCart Cart Icon\n *\n * Template part for displaying the SureCart cart count\n *\n * @package Reign\n */\n\nif ( function_exists( 'reign_sc_cart_count' ) ) {\n\treign_sc_cart_count();\n}\n";

			// Try to create the file.
			if ( is_writable( $header_icons_dir ) ) {
				file_put_contents( $surecart_cart_file, $file_content );
			}
		}
	}
}

// Initialize SureCart support.
new Reign_SureCart_Support();