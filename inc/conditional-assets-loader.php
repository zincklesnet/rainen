<?php
/**
 * Conditional Assets Loader
 *
 * Helper functions to determine when third-party plugin assets should load
 * This improves performance by only loading CSS/JS where actually needed
 *
 * IMPORTANT: All functions include efficient widget detection by checking
 * widget options directly in the database rather than scanning sidebars.
 * This ensures sidebar widgets work properly while maintaining performance.
 * Results are cached using WordPress transients for optimal speed.
 *
 * @package Reign
 * @since 7.8.3
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check if conditional asset loading should be bypassed
 *
 * @return bool True to bypass all conditional checks and load all assets
 */
function reign_bypass_conditional_assets() {
	// Always load all assets in Customizer preview
	// Users need to see all possible styling options
	if ( is_customize_preview() ) {
		return true;
	}

	// Always load all assets in Elementor editor
	// Designers need access to all styling while editing
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			return true;
		}

		// Also load all assets in Elementor preview mode
		// Users need to see accurate preview of their designs
		if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
			return true;
		}

		// Check for Elementor preview via URL parameter
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only page-builder preview detection (presence check only).
		if ( isset( $_GET['elementor-preview'] ) ) {
			return true;
		}

		// REMOVED: front-end bypass on `_elementor_edit_mode` post-meta.
		// That meta is set permanently on every post ever opened in the
		// Elementor editor — it does NOT mean the visitor is currently
		// previewing. The two checks above (is_edit_mode + is_preview_mode)
		// + the elementor-preview URL parameter already cover every actual
		// "editing or previewing" context. The removed branch was causing
		// the homepage (and every Elementor-built page) to load 10-20
		// unnecessary plugin asset bundles on every public page view.
	}

	// Always load all assets in other page builders
	if ( function_exists( 'et_core_is_fb_enabled' ) && et_core_is_fb_enabled() ) {
		// Divi Builder
		return true;
	}

	if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {
		// Beaver Builder
		return true;
	}

	if ( function_exists( 'is_gutenberg_page' ) && is_gutenberg_page() ) {
		// Gutenberg editor (older check)
		return true;
	}

	// Check if in block editor (newer WordPress versions)
	if ( is_admin() && function_exists( 'get_current_screen' ) ) {
		$screen = get_current_screen();
		if ( $screen ) {
			// Load all assets in block editor
			if ( $screen->is_block_editor() ) {
				return true;
			}

			// Load all assets when creating/editing any post type
			// This includes pages, posts, and custom post types
			if ( 'post' === $screen->base && in_array( $screen->post_type, get_post_types() ) ) {
				return true;
			}
		}
	}

	// Also check for classic editor (non-Gutenberg)
	if ( is_admin() ) {
		global $pagenow;
		// Load all assets when creating or editing posts/pages
		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
			return true;
		}
	}

	// Allow developers to bypass all conditional checks
	$bypass = apply_filters( 'reign_bypass_conditional_assets', false );

	// Check for query parameter bypass (for debugging)
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin debug flag (presence check only, capability-gated).
	if ( isset( $_GET['load_all_assets'] ) && current_user_can( 'manage_options' ) ) {
		$bypass = true;
	}

	// Check for constant bypass (for development)
	if ( defined( 'REIGN_LOAD_ALL_ASSETS' ) && REIGN_LOAD_ALL_ASSETS ) {
		$bypass = true;
	}

	return $bypass;
}

/**
 * Safe wrapper to check if we're on a singular page with valid post content
 *
 * @return bool|WP_Post Returns the post object if valid, false otherwise
 */
function reign_get_singular_post() {
	if ( ! function_exists( 'is_singular' ) || ! is_singular() ) {
		return false;
	}

	global $post;
	if ( ! $post || ! is_a( $post, 'WP_Post' ) || empty( $post->post_content ) ) {
		return false;
	}

	return $post;
}

/**
 * Safe wrapper for checking active widgets
 *
 * @param string $widget_id The widget ID to check
 * @return bool True if widget is active, false otherwise
 */
function reign_is_widget_active( $widget_id ) {
	if ( ! function_exists( 'is_active_widget' ) ) {
		return false;
	}

	return is_active_widget( false, false, $widget_id );
}

/**
 * More efficient widget detection by checking widget options directly
 * Uses transient caching to avoid repeated database queries
 *
 * @param array $widget_bases Array of widget base IDs to check for
 * @return bool True if any of the widgets are active, false otherwise
 */
function reign_are_widgets_active( $widget_bases ) {
	if ( empty( $widget_bases ) || ! is_array( $widget_bases ) ) {
		return false;
	}

	// Create cache key based on widget bases
	$cache_key     = 'reign_widgets_' . md5( serialize( $widget_bases ) );
	$cached_result = get_transient( $cache_key );

	// Return cached result if available
	if ( false !== $cached_result ) {
		return (bool) $cached_result;
	}

	$widgets_active = false;

	foreach ( $widget_bases as $widget_base ) {
		// Check if widget option exists and has active instances
		$widget_options = get_option( "widget_{$widget_base}" );
		if ( is_array( $widget_options ) ) {
			// Remove _multiwidget key and check if any instances remain
			unset( $widget_options['_multiwidget'] );
			if ( ! empty( $widget_options ) ) {
				$widgets_active = true;
				break; // Found at least one active widget
			}
		}
	}

	// Cache result for 1 hour (widgets don't change frequently)
	set_transient( $cache_key, $widgets_active ? 1 : 0, HOUR_IN_SECONDS );

	return $widgets_active;
}

/**
 * Clear widget cache when widgets are updated
 * Hooked to widget update actions to ensure cache accuracy
 */
function reign_clear_widget_cache() {
	// Runs at priority 1 on wp_ajax_save-widget / wp_ajax_widgets-order —
	// BEFORE core's own capability checks fire. Gate it ourselves so a
	// Subscriber-level request can't trigger cache invalidation.
	if ( wp_doing_ajax() && ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	// Delete all widget-related transients.
	global $wpdb;
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", '_transient_reign_widgets_%' ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", '_transient_timeout_reign_widgets_%' ) );
}

// Hook into widget save/delete actions to clear cache
add_action( 'sidebar_admin_setup', 'reign_clear_widget_cache' );
add_action( 'wp_ajax_save-widget', 'reign_clear_widget_cache', 1 );
add_action( 'wp_ajax_widgets-order', 'reign_clear_widget_cache', 1 );

/**
 * Get active widgets on the current page
 * Checks all active sidebars including Gutenberg block widgets
 *
 * @return array Array of widget base IDs that are active on current page
 */
function reign_get_current_page_widgets() {
	static $cache = null;
	if ( null !== $cache ) {
		return $cache;
	}
	$active_widgets = array();

	// Get all sidebars and their widgets
	$sidebars_widgets = wp_get_sidebars_widgets();

	if ( ! is_array( $sidebars_widgets ) ) {
		$cache = $active_widgets;
		return $cache;
	}

	// Sidebars that NEVER render on the current request — widgets inside
	// them must not trigger conditional asset loading. The map is built
	// once per request based on the current template context. Without this
	// gate, a LearnDash widget left in `ld-single-course-sidebar` (which
	// only renders on /courses/<slug>/) would force LearnDash CSS onto
	// EVERY page on the site. Same logic for WooCommerce, WC Vendors, EDD,
	// FluentCart, and BuddyPress group/member-specific sidebars.
	$inactive_sidebar_ids = reign_get_inactive_sidebar_ids_for_current_request();

	// Check each sidebar that has widgets
	foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
		// Skip empty sidebars and wp_inactive_widgets.
		if ( empty( $widgets ) || 'wp_inactive_widgets' === $sidebar_id || ! is_array( $widgets ) ) {
			continue;
		}

		// Skip sidebars that the current template will not render.
		if ( in_array( $sidebar_id, $inactive_sidebar_ids, true ) ) {
			continue;
		}

		// Include only widgets from sidebars relevant to the current page.
		foreach ( $widgets as $widget ) {
			// Extract widget base from widget ID (e.g., 'bp_core_members_widget-2' -> 'bp_core_members_widget')
			$widget_base = preg_replace( '/-[0-9]+$/', '', $widget );

			// Check if this is a block widget
			if ( 'block' === $widget_base ) {
				// For block widgets, check the content for BP/plugin blocks
				$block_content = reign_get_block_widget_content( $widget );
				if ( $block_content ) {
					// Check for BuddyPress/BuddyBoss blocks
					if ( strpos( $block_content, 'wp:bp/' ) !== false ||
						strpos( $block_content, 'wp:buddypress/' ) !== false ||
						strpos( $block_content, 'wp:buddyboss/' ) !== false ||
						strpos( $block_content, 'buddypress' ) !== false ) {
						$active_widgets[] = 'bp_block_widget'; // Virtual widget name for BP blocks
					}
					// Check for WooCommerce blocks
					if ( strpos( $block_content, 'wp:woocommerce/' ) !== false ||
						strpos( $block_content, 'wp:wc/' ) !== false ||
						strpos( $block_content, 'woocommerce' ) !== false ) {
						$active_widgets[] = 'woocommerce_block_widget';
					}
					// Check for LearnDash blocks
					if ( strpos( $block_content, 'wp:learndash/' ) !== false ||
						strpos( $block_content, 'wp:ld/' ) !== false ||
						strpos( $block_content, 'wp:sfwd/' ) !== false ||
						strpos( $block_content, 'learndash' ) !== false ) {
						$active_widgets[] = 'learndash_block_widget';
					}
					// Check for other plugin blocks as needed
				}
			} elseif ( ! in_array( $widget_base, $active_widgets ) ) {
				$active_widgets[] = $widget_base;
			}
		}
	}

	$cache = $active_widgets;
	return $cache;
}

/**
 * Decide whether the general (theme) left / right sidebar regions actually
 * render on the current request.
 *
 * The general `sidebar-left` / `sidebar-right` regions only render when the
 * resolved layout for the current view is `left_sidebar`, `right_sidebar` or
 * `both_sidebar`. Full-width / stretched layouts (and any per-page metabox set
 * to those) render NO general sidebar — see Reign_Theme_Structure::
 * render_left_sidebar_area() / render_right_sidebar_area() and
 * reign_get_sidebar_id_to_show().
 *
 * Without this check the asset loader treated a widget living in `sidebar-left`
 * as "present on current page" even on a full-width landing page (e.g. the
 * homepage), which forced BuddyPress nouveau-main.css (~465KB) onto the
 * homepage just because a BP login widget sat in the left sidebar region.
 *
 * This mirrors the render decision rather than duplicating the full template
 * tree: it reads the per-page metabox `site_layout` first (authoritative
 * override), then falls back to the per-post-type customizer layout default
 * for singular views. Non-singular / unknown contexts return true (stay broad)
 * so directory and archive sidebars keep working.
 *
 * @param string $side 'left' or 'right'.
 * @return bool True if the general sidebar for that side renders on this request.
 */
if ( ! function_exists( 'reign_general_sidebar_renders' ) ) :
	function reign_general_sidebar_renders( $side = 'right' ) {
		static $cache = array();
		if ( isset( $cache[ $side ] ) ) {
			return $cache[ $side ];
		}

		$render_layouts = ( 'left' === $side )
			? array( 'left_sidebar', 'both_sidebar' )
			: array( 'right_sidebar', 'both_sidebar' );

		// Only singular views read a per-page metabox / per-CPT layout. For any
		// other context (directories, archives, search, 404) we cannot cheaply
		// reproduce the template decision here, so keep widget detection broad
		// to avoid suppressing a sidebar that does render.
		if ( ! is_singular() ) {
			$cache[ $side ] = true;
			return $cache[ $side ];
		}

		$post_id = get_queried_object_id();
		if ( ! $post_id ) {
			$cache[ $side ] = true;
			return $cache[ $side ];
		}

		$theme_slug  = apply_filters( 'wbcom_essential_theme_slug', 'reign' );
		$meta        = get_post_meta( $post_id, $theme_slug . '_wbcom_metabox_data', true );
		$site_layout = isset( $meta['layout']['site_layout'] ) ? $meta['layout']['site_layout'] : '';

		// Per-page metabox override is authoritative when set (not the "Default"
		// sentinel '0' / empty).
		if ( '' !== $site_layout && '0' !== $site_layout ) {
			$cache[ $side ] = in_array( $site_layout, $render_layouts, true );
			return $cache[ $side ];
		}

		// No per-page override: fall back to the per-post-type customizer layout
		// default, exactly like the sidebar render methods do.
		$post_type = get_post_type( $post_id );
		$layout    = get_theme_mod( "reign_{$post_type}_single_layout", 'right_sidebar' );

		$cache[ $side ] = in_array( $layout, $render_layouts, true );
		return $cache[ $side ];
	}
endif;

/**
 * Build the list of sidebar IDs that will NOT render on this request.
 *
 * Reign theme registers ~17 sidebars (see inc/init.php::reign_widgets_init).
 * Most plugin-specific sidebars only render in their plugin's templates
 * (e.g., `woocommerce-sidebar-right` renders only on WC pages). Before
 * this helper, the asset loader treated a widget in any sidebar as
 * "present on current page" — which forced LearnDash CSS to load on
 * every page if anyone had ever dropped an LD widget into the
 * LD-specific sidebar.
 *
 * Returns the LIST OF SIDEBAR IDS to EXCLUDE from widget detection
 * because their owning context does not apply to the current request.
 *
 * Statically memoized per request.
 *
 * @return array<string>
 */
if ( ! function_exists( 'reign_get_inactive_sidebar_ids_for_current_request' ) ) :
	function reign_get_inactive_sidebar_ids_for_current_request() {
		static $cache = null;
		if ( null !== $cache ) {
			return $cache;
		}

		$inactive = array();

		// General (theme) left / right sidebars only render when the resolved
		// layout for THIS view includes that side. On a full-width / stretched
		// page (e.g. the homepage) neither renders, so a widget left in the
		// region must not trigger integration CSS. Mirrors the sidebar render
		// methods in Reign_Theme_Structure.
		if ( ! reign_general_sidebar_renders( 'right' ) ) {
			$inactive[] = 'sidebar-right';
		}
		if ( ! reign_general_sidebar_renders( 'left' ) ) {
			$inactive[] = 'sidebar-left';
		}

		// WooCommerce sidebars only render on WC pages.
		$is_wc_context = ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_shop() || is_cart() || is_checkout() || is_account_page() ) );
		if ( ! $is_wc_context ) {
			$inactive[] = 'woocommerce-sidebar-right';
			$inactive[] = 'woocommerce-sidebar-left';
			$inactive[] = 'reign_off_canvas_sidebar';
		}

		// LearnDash sidebars only render on LD single course / group pages.
		$is_ld_course_context = is_singular( 'sfwd-courses' );
		$is_ld_group_context  = is_singular( array( 'sfwd-groups', 'groups' ) );
		if ( ! $is_ld_course_context ) {
			$inactive[] = 'ld-single-course-sidebar';
		}
		if ( ! $is_ld_group_context ) {
			$inactive[] = 'ld-single-group-sidebar';
		}

		// WC Vendors store sidebar only on vendor store pages.
		if ( ! ( class_exists( 'WCV_Vendors' ) && method_exists( 'WCV_Vendors', 'is_vendor_page' ) && WCV_Vendors::is_vendor_page() ) ) {
			$inactive[] = 'wcvendors-store-sidebar';
		}

		// BuddyPress component-specific sidebars only render on their components.
		if ( ! function_exists( 'bp_is_active' ) ) {
			$inactive[] = 'group-index';
			$inactive[] = 'member-index';
			$inactive[] = 'activity-index';
			$inactive[] = 'group-single';
			$inactive[] = 'member-profile';
		} else {
			if ( ! ( function_exists( 'bp_is_groups_directory' ) && bp_is_groups_directory() ) ) {
				$inactive[] = 'group-index';
			}
			if ( ! ( function_exists( 'bp_is_members_directory' ) && bp_is_members_directory() ) ) {
				$inactive[] = 'member-index';
			}
			if ( ! ( function_exists( 'bp_is_activity_directory' ) && bp_is_activity_directory() ) ) {
				$inactive[] = 'activity-index';
			}
			if ( ! ( function_exists( 'bp_is_group' ) && bp_is_group() ) ) {
				$inactive[] = 'group-single';
			}
			if ( ! ( function_exists( 'bp_is_user' ) && bp_is_user() ) ) {
				$inactive[] = 'member-profile';
			}
		}

		// EDD sidebars only on download archive / single pages.
		if ( ! ( is_post_type_archive( 'download' ) || is_tax( array( 'download_category', 'download_tag' ) ) ) ) {
			$inactive[] = 'edd-download-archive-sidebar';
		}
		if ( ! is_singular( 'download' ) ) {
			$inactive[] = 'edd-single-download-sidebar';
		}

		// FluentCart sidebars only on FluentCart pages.
		if ( ! ( function_exists( 'is_fluent_cart_page' ) && is_fluent_cart_page() ) ) {
			$inactive[] = 'fluentcart-sidebar-left';
			$inactive[] = 'fluentcart-sidebar-right';
		}

		// Allow themes / child themes / plugins to add to the inactive list.
		$cache = apply_filters( 'reign_inactive_sidebar_ids_for_current_request', $inactive );
		return $cache;
	}
endif;

/**
 * Get content of a block widget
 *
 * @param string $widget_id The widget ID (e.g., 'block-7')
 * @return string|false The block content or false if not found
 */
function reign_get_block_widget_content( $widget_id ) {
	// Extract the block number from widget ID
	preg_match( '/block-([0-9]+)/', $widget_id, $matches );
	if ( ! isset( $matches[1] ) ) {
		return false;
	}

	$block_id      = intval( $matches[1] );
	$widget_blocks = get_option( 'widget_block' );

	if ( isset( $widget_blocks[ $block_id ]['content'] ) ) {
		return $widget_blocks[ $block_id ]['content'];
	}

	return false;
}

/**
 * Check if Elementor is being used on current page and scan for widgets
 *
 * @return array Array of plugin identifiers that need assets loaded
 */
function reign_get_elementor_widget_plugins() {
	$plugins_needed = array();

	// Check if Elementor is active
	if ( ! defined( 'ELEMENTOR_VERSION' ) || ! class_exists( '\Elementor\Plugin' ) ) {
		return $plugins_needed;
	}

	// Get current post ID
	$post_id = get_the_ID();
	if ( ! $post_id ) {
		return $plugins_needed;
	}

	// Check if page is built with Elementor
	if ( ! \Elementor\Plugin::$instance->db->is_built_with_elementor( $post_id ) ) {
		return $plugins_needed;
	}

	// Get Elementor data
	$elementor_data = get_post_meta( $post_id, '_elementor_data', true );
	if ( empty( $elementor_data ) ) {
		return $plugins_needed;
	}

	// Decode if it's JSON string
	if ( is_string( $elementor_data ) ) {
		$elementor_data = json_decode( $elementor_data, true );
	}

	if ( ! is_array( $elementor_data ) ) {
		return $plugins_needed;
	}

	// Scan Elementor data for widgets
	$widget_types = reign_scan_elementor_widgets( $elementor_data );

	// Map widget types to plugins
	foreach ( $widget_types as $widget_type ) {
		$plugin = reign_detect_elementor_widget_plugin( $widget_type );
		if ( $plugin && ! in_array( $plugin, $plugins_needed ) ) {
			$plugins_needed[] = $plugin;
		}
	}

	return $plugins_needed;
}

/**
 * Recursively scan Elementor data for widget types
 *
 * @param array $elements Elementor elements array
 * @return array Array of widget types found
 */
function reign_scan_elementor_widgets( $elements ) {
	$widget_types = array();

	if ( ! is_array( $elements ) ) {
		return $widget_types;
	}

	foreach ( $elements as $element ) {
		// Check if this is a widget
		if ( isset( $element['elType'] ) && 'widget' === $element['elType'] ) {
			if ( isset( $element['widgetType'] ) ) {
				$widget_type = $element['widgetType'];

				// Special handling for WordPress widget
				if ( 'wp-widget' === $widget_type || 'WordPress' === $widget_type ) {
					// Check for the actual widget class in settings
					if ( isset( $element['settings']['widget_type'] ) ) {
						// Append wp-widget prefix for classic widgets
						$widget_type = 'wp-widget-' . $element['settings']['widget_type'];
					} elseif ( isset( $element['settings']['id_base'] ) ) {
						// Alternative location for widget type
						$widget_type = 'wp-widget-' . $element['settings']['id_base'];
					}
				}

				$widget_types[] = $widget_type;
			}
		}

		// Check for nested elements (sections, columns)
		if ( isset( $element['elements'] ) && is_array( $element['elements'] ) ) {
			$nested_widgets = reign_scan_elementor_widgets( $element['elements'] );
			$widget_types   = array_merge( $widget_types, $nested_widgets );
		}
	}

	return array_unique( $widget_types );
}

/**
 * Detect which plugin an Elementor widget belongs to
 *
 * @param string $widget_type The Elementor widget type
 * @return string|false Plugin identifier or false if no plugin assets needed
 */
function reign_detect_elementor_widget_plugin( $widget_type ) {
	// Special handling for WordPress widget in Elementor
	if ( 'wp-widget' === $widget_type || 'WordPress' === $widget_type ) {
		// This is a classic WordPress widget in Elementor
		// We'll need to check the actual widget type from settings
		// For now, return false as we can't determine without more context
		return false;
	}

	// Elementor widget type to plugin mapping
	$widget_mapping = array(
		// BuddyPress/BuddyBoss widgets - both native and classic WordPress widgets
		'bp-members'                                     => 'buddypress',
		'bp-groups'                                      => 'buddypress',
		'bp-activity'                                    => 'buddypress',
		'bp-friends'                                     => 'buddypress',
		'bp-profile-completion'                          => 'buddypress',
		'buddypress-members'                             => 'buddypress',
		'buddypress-groups'                              => 'buddypress',
		'buddypress-activity'                            => 'buddypress',
		'buddyboss-members'                              => 'buddypress',
		'buddyboss-groups'                               => 'buddypress',
		'buddyboss-activity'                             => 'buddypress',
		// Classic BP widget names when used via WordPress widget
		'wp-widget-bp_core_members_widget'               => 'buddypress',
		'wp-widget-bp_core_whos_online_widget'           => 'buddypress',
		'wp-widget-bp_core_recently_active_widget'       => 'buddypress',
		'wp-widget-bp_groups_widget'                     => 'buddypress',
		'wp-widget-bp_messages_sitewide_notices_widget'  => 'buddypress',
		'wp-widget-bp_core_login_widget'                 => 'buddypress',
		'wp-widget-bp_activity_widget'                   => 'buddypress',

		// WooCommerce widgets - both native and classic WordPress widgets
		'woocommerce-products'                           => 'woocommerce',
		'woocommerce-product-categories'                 => 'woocommerce',
		'woocommerce-product-add-to-cart'                => 'woocommerce',
		'woocommerce-product-price'                      => 'woocommerce',
		'woocommerce-product-images'                     => 'woocommerce',
		'woocommerce-product-rating'                     => 'woocommerce',
		'woocommerce-product-meta'                       => 'woocommerce',
		'woocommerce-product-short-description'          => 'woocommerce',
		'woocommerce-product-content'                    => 'woocommerce',
		'woocommerce-product-data-tabs'                  => 'woocommerce',
		'woocommerce-product-additional-information'     => 'woocommerce',
		'woocommerce-product-related'                    => 'woocommerce',
		'woocommerce-product-upsell'                     => 'woocommerce',
		'woocommerce-products-archive'                   => 'woocommerce',
		'woocommerce-breadcrumb'                         => 'woocommerce',
		'woocommerce-product-title'                      => 'woocommerce',
		'woocommerce-product-stock'                      => 'woocommerce',
		'woocommerce-cart'                               => 'woocommerce',
		'woocommerce-checkout'                           => 'woocommerce',
		'woocommerce-my-account'                         => 'woocommerce',
		'woocommerce-order-tracking'                     => 'woocommerce',
		'woocommerce-menu-cart'                          => 'woocommerce',
		// Classic WooCommerce widget names when used via WordPress widget
		'wp-widget-woocommerce_widget_cart'              => 'woocommerce',
		'wp-widget-woocommerce_layered_nav_filters'      => 'woocommerce',
		'wp-widget-woocommerce_layered_nav'              => 'woocommerce',
		'wp-widget-woocommerce_price_filter'             => 'woocommerce',
		'wp-widget-woocommerce_product_categories'       => 'woocommerce',
		'wp-widget-woocommerce_product_search'           => 'woocommerce',
		'wp-widget-woocommerce_product_tag_cloud'        => 'woocommerce',
		'wp-widget-woocommerce_products'                 => 'woocommerce',
		'wp-widget-woocommerce_recently_viewed_products' => 'woocommerce',
		'wp-widget-woocommerce_top_rated_products'       => 'woocommerce',
		'wp-widget-woocommerce_recent_reviews'           => 'woocommerce',
		'wp-widget-woocommerce_rating_filter'            => 'woocommerce',

		// LearnDash widgets - both native and classic WordPress widgets
		'learndash-course-list'                          => 'learndash',
		'learndash-lesson-list'                          => 'learndash',
		'learndash-topic-list'                           => 'learndash',
		'learndash-quiz-list'                            => 'learndash',
		'learndash-course-content'                       => 'learndash',
		'learndash-course-infobar'                       => 'learndash',
		'learndash-course-certificate'                   => 'learndash',
		'learndash-course-progress'                      => 'learndash',
		'learndash-user-status'                          => 'learndash',
		'ld-course-list'                                 => 'learndash',
		'ld-lesson-list'                                 => 'learndash',
		'ld-profile'                                     => 'learndash',
		// Classic LearnDash widget names when used via WordPress widget
		'wp-widget-sfwd-certificates-widget'             => 'learndash',
		'wp-widget-sfwd-courses-widget'                  => 'learndash',
		'wp-widget-sfwd-lessons-widget'                  => 'learndash',
		'wp-widget-sfwd-quiz-widget'                     => 'learndash',
		'wp-widget-learndash_course_progress'            => 'learndash',
		'wp-widget-learndash_course_navigation'          => 'learndash',
		'wp-widget-learndash_course_info'                => 'learndash',
		'wp-widget-learndash_user_status'                => 'learndash',

		// bbPress widgets - both native and classic WordPress widgets
		'bbpress-forums'                                 => 'bbpress',
		'bbpress-topics'                                 => 'bbpress',
		'bbpress-replies'                                => 'bbpress',
		'bbpress-forum-index'                            => 'bbpress',
		'bbpress-single-forum'                           => 'bbpress',
		'bbpress-topic-index'                            => 'bbpress',
		'bbpress-single-topic'                           => 'bbpress',
		'bbpress-stats'                                  => 'bbpress',
		'bbp-forums'                                     => 'bbpress',
		'bbp-topics'                                     => 'bbpress',
		// Classic bbPress widget names when used via WordPress widget
		'wp-widget-bbp_forums_widget'                    => 'bbpress',
		'wp-widget-bbp_topics_widget'                    => 'bbpress',
		'wp-widget-bbp_replies_widget'                   => 'bbpress',
		'wp-widget-bbp_login_widget'                     => 'bbpress',
		'wp-widget-bbp_stats_widget'                     => 'bbpress',
		'wp-widget-bbp_views_widget'                     => 'bbpress',

		// Events Calendar widgets
		'tribe-events'                                   => 'eventscalendar',
		'tribe-events-list'                              => 'eventscalendar',
		'tribe-events-calendar'                          => 'eventscalendar',
		'events-calendar'                                => 'eventscalendar',

		// Other plugin widgets - both native and classic WordPress widgets
		'edd-downloads'                                  => 'edd',
		'edd-cart'                                       => 'edd',
		'edd-checkout'                                   => 'edd',
		'edd-login'                                      => 'edd',
		'edd-register'                                   => 'edd',
		// Classic EDD widget names when used via WordPress widget
		'wp-widget-edd_cart_widget'                      => 'edd',
		'wp-widget-edd_categories_tags_widget'           => 'edd',
		'wp-widget-edd_product_details'                  => 'edd',
		'peepso-activity'                                => 'peepso',
		'peepso-members'                                 => 'peepso',
		'peepso-groups'                                  => 'peepso',
		'rtmedia-gallery'                                => 'rtmedia',
		'rtmedia-uploader'                               => 'rtmedia',
		'lifterlms-courses'                              => 'lifterlms',
		'lifterlms-memberships'                          => 'lifterlms',
		'tutor-courses'                                  => 'tutorlms',
		'tutor-course-list'                              => 'tutorlms',
		'geodir-listings'                                => 'geodirectory',
		'geodir-search'                                  => 'geodirectory',
		'directorist-listings'                           => 'directorist',
		'directorist-search'                             => 'directorist',
		'dokan-stores'                                   => 'dokan',
		'dokan-vendor'                                   => 'dokan',
		'wcfm-stores'                                    => 'wcfm',
		'wcfm-store'                                     => 'wcfm',
		'wcv-vendors'                                    => 'wc_vendors',
		'wcv-vendor'                                     => 'wc_vendors',
		'mvx-vendors'                                    => 'multivendorx',
		'mvx-vendor'                                     => 'multivendorx',
		'sensei-courses'                                 => 'sensei',
		'sensei-lessons'                                 => 'sensei',
		'pmpro-levels'                                   => 'pmpro',
		'pmpro-checkout'                                 => 'pmpro',
		'wpforo-forums'                                  => 'wpforo',
		'wpforo-topics'                                  => 'wpforo',
		'surecart-products'                              => 'surecart',
		'surecart-checkout'                              => 'surecart',
		'youzify-members'                                => 'youzify',
		'youzify-groups'                                 => 'youzify',
		'wp-job-manager-jobs'                            => 'wp_job_manager',
		'job-listings'                                   => 'wp_job_manager',
	);

	// Check for exact match
	if ( isset( $widget_mapping[ $widget_type ] ) ) {
		return $widget_mapping[ $widget_type ];
	}

	// Check for pattern-based matching
	if ( strpos( $widget_type, 'bp-' ) === 0 || strpos( $widget_type, 'buddypress' ) !== false || strpos( $widget_type, 'buddyboss' ) !== false ) {
		return 'buddypress';
	}

	if ( strpos( $widget_type, 'woocommerce' ) !== false || strpos( $widget_type, 'wc-' ) === 0 ) {
		return 'woocommerce';
	}

	if ( strpos( $widget_type, 'learndash' ) !== false || strpos( $widget_type, 'ld-' ) === 0 || strpos( $widget_type, 'sfwd' ) !== false ) {
		return 'learndash';
	}

	if ( strpos( $widget_type, 'bbpress' ) !== false || strpos( $widget_type, 'bbp-' ) === 0 ) {
		return 'bbpress';
	}

	if ( strpos( $widget_type, 'edd' ) !== false ) {
		return 'edd';
	}

	if ( strpos( $widget_type, 'lifterlms' ) !== false || strpos( $widget_type, 'llms' ) !== false ) {
		return 'lifterlms';
	}

	if ( strpos( $widget_type, 'tutor' ) !== false ) {
		return 'tutorlms';
	}

	if ( strpos( $widget_type, 'tribe' ) !== false || strpos( $widget_type, 'events' ) !== false ) {
		return 'eventscalendar';
	}

	if ( strpos( $widget_type, 'peepso' ) !== false ) {
		return 'peepso';
	}

	return false;
}

/**
 * Check if specific plugin widgets are active on current page
 *
 * @param array $widget_bases Array of widget bases to check
 * @return bool True if any of the widgets are active on current page
 */
function reign_current_page_has_widgets( $widget_bases ) {
	$current_widgets = reign_get_current_page_widgets();

	foreach ( $widget_bases as $widget_base ) {
		if ( in_array( $widget_base, $current_widgets ) ) {
			return true;
		}
	}

	return false;
}

/**
 * Get widget to plugin mapping ONLY for plugins with actual theme CSS/JS assets
 *
 * @return array Widget patterns and their corresponding plugin assets
 */
function reign_get_widget_plugin_mapping() {
	return array(
		// Pattern-based detection - ONLY for plugins with actual theme assets
		'patterns' => array(
			'/^bp_.*/'               => 'buddypress',               // BuddyPress: bp_* → nouveau-main.css + BP JS
			'/^buddypress.*/'        => 'buddypress',        // BuddyPress: buddypress* → nouveau-main.css + BP JS
			'/^buddyboss.*/'         => 'buddypress',         // BuddyBoss: buddyboss* → nouveau-main.css + BP JS
			'/^reign_bp.*/'          => 'buddypress',          // Reign BP: reign_bp* → nouveau-main.css + BP JS
			'/^bbp_.*/'              => 'bbpress',                 // bbPress: bbp_* → bbpress-main.css + bbpress.js
			'/^bbpress.*/'           => 'bbpress',              // bbPress: bbpress* → bbpress-main.css + bbpress.js
			'/^woocommerce.*/'       => 'woocommerce',      // WooCommerce: woocommerce* → woocommerce-main.css + woocommerce.js
			'/^wc_.*/'               => 'woocommerce',              // WooCommerce: wc_* → woocommerce-main.css + woocommerce.js
			'/^edd.*/'               => 'edd',                      // EDD: edd* → edd-main.css + edd.js
			'/^learndash.*/'         => 'learndash',          // LearnDash: learndash* → learndash-main.css
			'/^ld_.*/'               => 'learndash',                // LearnDash: ld_* → learndash-main.css
			'/^sfwd.*/'              => 'learndash',               // LearnDash: sfwd* → learndash-main.css
			'/^lifterlms.*/'         => 'lifterlms',          // LifterLMS: lifterlms* → lifterlms-main.css + lifterlms.js
			'/^llms.*/'              => 'lifterlms',               // LifterLMS: llms* → lifterlms-main.css + lifterlms.js
			'/^tutor.*/'             => 'tutorlms',               // TutorLMS: tutor* → tutorlms-main.css
			'/^events.*/'            => 'eventscalendar',        // Events Calendar: events* → eventscalendar-main.css
			'/^tribe.*/'             => 'eventscalendar',         // Events Calendar: tribe* → eventscalendar-main.css
			'/^peepso.*/'            => 'peepso',                // PeepSo: peepso* → peepso-main.css + peepso.js
			'/^rtmedia.*/'           => 'rtmedia',              // rtMedia: rtmedia* → rtmedia-main.css
			'/^dokan.*/'             => 'dokan',                  // Dokan: dokan* → dokan-main.css
			'/^wcfm.*/'              => 'wcfm',                    // WCFM: wcfm* → wcfm-main.css
			'/^wc_vendors.*/'        => 'wc_vendors',        // WC Vendors: wc_vendors* → wc-vendors-main.css
			'/^multivendorx.*/'      => 'multivendorx',    // MultiVendorX: multivendorx* → multivendorx-main.css
			'/^directorist.*/'       => 'directorist',      // Directorist: directorist* → directorist-main.css
			'/^geodir.*/'            => 'geodirectory',          // GeoDirectory: geodir* → geodirectory-main.css
			'/^wpforo.*/'            => 'wpforo',                // wpForo: wpforo* → wpforo-main.css
			'/^wp_job_manager.*/'    => 'wp_job_manager', // WP Job Manager: wp_job_manager* → wp-job-manager-main.css
			'/^wp_resume_manager.*/' => 'wp_job_manager', // Resume Manager: wp_resume_manager* → wp-job-manager-main.css
			'/^sensei.*/'            => 'sensei',                // Sensei: sensei* → sensei-main.css
			'/^surecart.*/'          => 'surecart',            // SureCart: surecart* → surecart-main.css
			'/^youzify.*/'           => 'youzify',              // Youzify: youzify* → youzify-main.css
			'/^pmpro.*/'             => 'pmpro',                  // Paid Memberships Pro: pmpro* → pmpro-main.css
		),

		// Explicit mapping for widgets that don't follow naming patterns
		'explicit' => array(
			// WordPress core widgets - no plugin assets needed
			'custom_html'     => false,
			'text'            => false,
			'search'          => false,
			'recent_posts'    => false,
			'recent_comments' => false,
			'archives'        => false,
			'categories'      => false,
			'meta'            => false,
			'calendar'        => false,
			'tag_cloud'       => false,
			'nav_menu'        => false,
			'rss'             => false,
		),
	);
}

/**
 * Detect which plugin a widget belongs to based on naming patterns
 *
 * @param string $widget_base The widget base ID
 * @return string|false Plugin identifier or false if no plugin assets needed
 */
function reign_detect_widget_plugin( $widget_base ) {
	$mapping = reign_get_widget_plugin_mapping();

	// First check explicit mapping
	if ( isset( $mapping['explicit'][ $widget_base ] ) ) {
		return $mapping['explicit'][ $widget_base ];
	}

	// Then check pattern-based detection
	foreach ( $mapping['patterns'] as $pattern => $plugin ) {
		if ( preg_match( $pattern, $widget_base ) ) {
			return $plugin;
		}
	}

	// No plugin detected - probably a custom widget that doesn't need plugin assets
	return false;
}

/**
 * Dynamically enqueue assets based on widgets active on current page
 * Uses intelligent pattern detection to identify required plugin assets
 */
function reign_enqueue_widget_specific_assets() {
	$current_widgets = reign_get_current_page_widgets();

	if ( empty( $current_widgets ) ) {
		return;
	}

	$assets_to_load = array();

	// Detect plugins for each active widget
	foreach ( $current_widgets as $widget_base ) {
		$plugin = reign_detect_widget_plugin( $widget_base );

		if ( $plugin && ! in_array( $plugin, $assets_to_load ) ) {
			$assets_to_load[] = $plugin;
		}
	}

	// Also check for Elementor widgets if Elementor is active
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$elementor_plugins = reign_get_elementor_widget_plugins();
		foreach ( $elementor_plugins as $plugin ) {
			if ( ! in_array( $plugin, $assets_to_load ) ) {
				$assets_to_load[] = $plugin;
			}
		}
	}

	// Load the assets
	reign_load_widget_assets( $assets_to_load );
}

/**
 * Load specific plugin assets - ONLY for plugins with actual theme files
 *
 * @param array $assets Array of asset types to load
 */
function reign_load_widget_assets( $assets ) {
	if ( empty( $assets ) ) {
		return;
	}

	$rtl_css = is_rtl() ? '-rtl' : '';
	// BP template-package theme_mod is only meaningful when BP is active.
	// Defer the read into the BP-active branch so non-BP installs do not
	// touch the BP-specific setting at all.
	$theme_package_id = '';

	foreach ( $assets as $asset ) {
		switch ( $asset ) {
			case 'buddypress':
				if ( class_exists( 'BuddyPress' ) ) {
					if ( '' === $theme_package_id ) {
						$theme_package_id = get_theme_mod( 'buddypress_theme_package_id', 'nouveau' );
					}
					if ( 'nouveau' === $theme_package_id ) {
						wp_enqueue_style( 'reign_nouveau_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/nouveau-main.min.css', '', REIGN_THEME_VERSION );
					}
					wp_enqueue_script( 'reign-bp-header-icons', get_template_directory_uri() . '/assets/js/reign-bp-header-icons.min.js', array( 'jquery', 'password-strength-meter' ), REIGN_THEME_VERSION, true );
					wp_enqueue_script( 'reign-buddypress', get_template_directory_uri() . '/assets/js/reign-buddypress.min.js', array( 'jquery', 'password-strength-meter' ), REIGN_THEME_VERSION, true );
				}
				break;

			case 'bbpress':
				if ( class_exists( 'bbPress' ) ) {
					wp_enqueue_style( 'reign_bbpress_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/bbpress-main.min.css', '', REIGN_THEME_VERSION );
					wp_enqueue_script( 'reign-bbpress', get_template_directory_uri() . '/assets/js/reign-bbpress.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
				}
				break;

			case 'woocommerce':
				if ( class_exists( 'WooCommerce' ) ) {
					wp_enqueue_style( 'reign-woocommerce', get_template_directory_uri() . '/assets/css' . $rtl_css . '/woocommerce-main.min.css', '', REIGN_THEME_VERSION );
					wp_enqueue_script( 'reign-woocommerce', get_template_directory_uri() . '/assets/js/reign-woocommerce.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
				}
				break;

			case 'edd':
				if ( class_exists( 'Easy_Digital_Downloads' ) ) {
					wp_enqueue_style( 'reign_edd_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/edd-main.min.css', '', REIGN_THEME_VERSION );
					wp_enqueue_script( 'reign-edd', get_template_directory_uri() . '/assets/js/reign-edd.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
				}
				break;

			case 'learndash':
				if ( defined( 'LEARNDASH_VERSION' ) ) {
					wp_enqueue_style( 'reign_learndash_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/learndash-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'lifterlms':
				if ( class_exists( 'LifterLMS' ) ) {
					wp_enqueue_style( 'reign_lifterlms_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/lifterlms-main.min.css', '', REIGN_THEME_VERSION );
					wp_enqueue_script( 'reign-lifterlms', get_template_directory_uri() . '/assets/js/reign-lifterlms.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
				}
				break;

			case 'tutorlms':
				if ( defined( 'TUTOR_VERSION' ) ) {
					wp_enqueue_style( 'reign_tutorlms_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/tutorlms-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'eventscalendar':
				if ( class_exists( 'Tribe__Events__Main' ) ) {
					wp_enqueue_style( 'reign_eventscalendar_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/eventscalendar-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'peepso':
				if ( class_exists( 'PeepSo' ) ) {
					wp_enqueue_style( 'reign_peepso_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/peepso-main.min.css', '', REIGN_THEME_VERSION );
					wp_enqueue_script( 'reign-peepso', get_template_directory_uri() . '/assets/js/reign-peepso.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
				}
				break;

			case 'rtmedia':
				if ( defined( 'RTMEDIA_VERSION' ) ) {
					wp_enqueue_style( 'reign_rtmedia_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/rtmedia-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'dokan':
				if ( function_exists( 'dokan' ) ) {
					wp_enqueue_style( 'reign_dokan_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/dokan-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'wcfm':
				if ( class_exists( 'WCFMmp' ) ) {
					wp_enqueue_style( 'reign_wcfm_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/wcfm-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'wc_vendors':
				if ( class_exists( 'WC_Vendors' ) ) {
					wp_enqueue_style( 'reign_wc_vendors_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/wc-vendors-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'multivendorx':
				if ( class_exists( 'MultiVendorX' ) ) {
					wp_enqueue_style( 'reign_multivendorx_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/multivendorx-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'directorist':
				if ( class_exists( 'Directorist_Base' ) ) {
					wp_enqueue_style( 'reign_directorist_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/directorist-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'geodirectory':
				if ( defined( 'GEODIRECTORY_VERSION' ) ) {
					wp_enqueue_style( 'reign_geodirectory_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/geodirectory-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'wpforo':
				if ( defined( 'WPFORO_VERSION' ) ) {
					wp_enqueue_style( 'reign_wpforo_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/wpforo-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'wp_job_manager':
				if ( class_exists( 'WP_Job_Manager' ) ) {
					wp_enqueue_style( 'reign_wp_job_manager_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/wp-job-manager-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'shoplentor':
				if ( ( class_exists( 'WooLentor' ) || defined( 'WOOLENTOR_VERSION' ) ) && class_exists( 'WooCommerce' ) ) {
					wp_enqueue_style( 'reign_shoplentor_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/shoplentor.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'sensei':
				if ( class_exists( 'Sensei_Main' ) ) {
					wp_enqueue_style( 'reign_sensei_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/sensei-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'surecart':
				if ( defined( 'SURECART_PLUGIN_FILE' ) ) {
					wp_enqueue_style( 'reign_surecart_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/surecart-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'stachethemes_event_calendar':
				if ( defined( 'STEC_PLUGIN_VERSION' ) ) {
					wp_enqueue_style( 'reign-stachethemes-event-calendar', get_template_directory_uri() . '/assets/css' . $rtl_css . '/stachethemes-event-calendar.min.css', array(), REIGN_THEME_VERSION );
				}
				break;

			case 'youzify':
				if ( class_exists( 'Youzify' ) ) {
					wp_enqueue_style( 'reign_youzify_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/youzify-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;

			case 'pmpro':
				if ( defined( 'PMPRO_VERSION' ) ) {
					wp_enqueue_style( 'reign_pmpro_style', get_template_directory_uri() . '/assets/css' . $rtl_css . '/pmpro-main.min.css', '', REIGN_THEME_VERSION );
				}
				break;
		}
	}
}

// Hook dynamic widget asset loading into WordPress
// Removed - now integrated into main condition checks
// add_action( 'wp_enqueue_scripts', 'reign_enqueue_widget_specific_assets', 15 );

/**
 * Debug function to show active widgets and detected plugins on current page
 * Add ?debug_widgets=1 to any URL to see active widgets and their plugin mappings
 * Only for admins and only when debugging is explicitly requested
 */
function reign_debug_current_page_widgets() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin debug flag (presence check only, capability-gated).
	if ( ! isset( $_GET['debug_widgets'] ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$current_widgets = reign_get_current_page_widgets();

	echo '<div style="position: fixed; top: 50px; right: 20px; background: white; border: 2px solid #ccc; padding: 15px; z-index: 9999; max-width: 400px; max-height: 500px; overflow-y: auto; font-family: monospace; font-size: 12px;">';
	echo '<h4 style="margin-top: 0;">🔍 Widget Detection Debug</h4>';

	if ( empty( $current_widgets ) ) {
		echo '<p><strong>No widgets active on this page.</strong></p>';
	} else {
		echo '<h5>📋 Active Widgets (' . count( $current_widgets ) . '):</h5>';
		echo '<ul style="margin: 5px 0; padding-left: 20px;">';

		$assets_loaded = array();
		foreach ( $current_widgets as $widget ) {
			$plugin      = reign_detect_widget_plugin( $widget );
			$color       = $plugin ? '#2196F3' : '#666';
			$plugin_text = $plugin
				? ' &rarr; <strong style="color: ' . esc_attr( $color ) . '">' . esc_html( $plugin ) . '</strong>'
				: ' &rarr; <em>no assets</em>';
			echo '<li style="margin: 3px 0;">' . esc_html( $widget ) . wp_kses_post( $plugin_text ) . '</li>';

			if ( $plugin && ! in_array( $plugin, $assets_loaded ) ) {
				$assets_loaded[] = $plugin;
			}
		}
		echo '</ul>';

		if ( ! empty( $assets_loaded ) ) {
			echo '<h5>⚡ Assets Loaded (' . count( $assets_loaded ) . '):</h5>';
			echo '<ul style="margin: 5px 0; padding-left: 20px;">';
			foreach ( $assets_loaded as $asset ) {
				echo '<li style="color: #4CAF50; margin: 2px 0;"><strong>' . esc_html( $asset ) . '</strong></li>';
			}
			echo '</ul>';
		}
	}

	echo '<button onclick="this.parentNode.style.display=\'none\'" style="background: #f44336; color: white; border: none; padding: 5px 10px; cursor: pointer; border-radius: 3px; margin-top: 10px;">Close</button>';
	echo '</div>';
}
add_action( 'wp_footer', 'reign_debug_current_page_widgets' );

/**
 * Check if we should load EDD assets
 */
function reign_should_load_edd_assets() {
	if ( ! class_exists( 'Easy_Digital_Downloads' ) ) {
		return false;
	}

	// Check for bypass
	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// EDD specific pages
	if ( function_exists( 'edd_is_checkout' ) && edd_is_checkout() ) {
		return true;
	}

	if ( function_exists( 'edd_is_success_page' ) && edd_is_success_page() ) {
		return true;
	}

	if ( function_exists( 'edd_is_failed_transaction_page' ) && edd_is_failed_transaction_page() ) {
		return true;
	}

	if ( function_exists( 'edd_is_purchase_history_page' ) && edd_is_purchase_history_page() ) {
		return true;
	}

	// Downloads archive and single
	if ( is_post_type_archive( 'download' ) || is_singular( 'download' ) ) {
		return true;
	}

	// Check for EDD shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'downloads' ) ||
		has_shortcode( $post->post_content, 'purchase_link' ) ||
		has_shortcode( $post->post_content, 'edd_checkout' ) ||
		has_shortcode( $post->post_content, 'edd_cart' ) ||
		has_shortcode( $post->post_content, 'edd_profile_editor' ) ||
		has_shortcode( $post->post_content, 'edd_login' ) ||
		has_shortcode( $post->post_content, 'edd_register' ) ||
		has_shortcode( $post->post_content, 'download_checkout' ) ||
		has_shortcode( $post->post_content, 'edd_receipt' ) ||
		has_shortcode( $post->post_content, 'download_history' ) ||
		has_shortcode( $post->post_content, 'purchase_history' ) ||
		has_shortcode( $post->post_content, 'download_cart' ) ||
		has_shortcode( $post->post_content, 'download_discounts' ) ||
		has_shortcode( $post->post_content, 'purchase_collection' ) ||
		has_shortcode( $post->post_content, 'edd_downloads' ) ||
		has_shortcode( $post->post_content, 'edd_price' )
	) ) {
		return true;
	}

	// Check edd sell services shortcode
	if ( class_exists( 'Edd_Sell_Services' ) ) {
		if ( $post && (
			has_shortcode( $post->post_content, 'edd_sell_services_manage_order' )
		) ) {
			return true;
		}
	}

	// Check for EDD widgets
	if ( is_active_widget( false, false, 'edd_cart_widget' ) ||
		is_active_widget( false, false, 'edd_categories_tags_widget' ) ||
		is_active_widget( false, false, 'edd_product_details' ) ) {
		return true;
	}

	// Check for Elementor widgets
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$elementor_plugins = reign_get_elementor_widget_plugins();
		if ( in_array( 'edd', $elementor_plugins ) ) {
			return true;
		}
	}

	return apply_filters( 'reign_load_edd_assets', false );
}

/**
 * Check if we should load bbPress assets
 * Now checks for current page widgets dynamically
 */
function reign_should_load_bbpress_assets() {
	if ( ! class_exists( 'bbPress' ) ) {
		return false;
	}

	// Check for bypass
	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// bbPress pages
	if ( function_exists( 'is_bbpress' ) && is_bbpress() ) {
		return true;
	}

	// BuddyPress group forums
	if ( function_exists( 'bp_is_group' ) && function_exists( 'bp_is_current_action' ) && bp_is_group() && bp_is_current_action( 'forum' ) ) {
		return true;
	}

	// Check for bbPress shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'bbp-forum-index' ) ||
		has_shortcode( $post->post_content, 'bbp-topic-index' ) ||
		has_shortcode( $post->post_content, 'bbp-single-forum' ) ||
		has_shortcode( $post->post_content, 'bbp-single-topic' ) ||
		has_shortcode( $post->post_content, 'bbp-login' ) ||
		has_shortcode( $post->post_content, 'bbp-register' )
	) ) {
		return true;
	}

	// Check for bbPress widgets on current page (dynamic detection)
	$bbpress_widgets = array(
		'bbp_forums_widget',
		'bbp_topics_widget',
		'bbp_replies_widget',
		'bbp_login_widget',
		'bbp_stats_widget',
		'bbp_views_widget',
	);

	if ( reign_current_page_has_widgets( $bbpress_widgets ) ) {
		return true;
	}

	// Check for Elementor widgets
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$elementor_plugins = reign_get_elementor_widget_plugins();
		if ( in_array( 'bbpress', $elementor_plugins ) ) {
			return true;
		}
	}

	return apply_filters( 'reign_load_bbpress_assets', false );
}

/**
 * Check if we should load LearnDash assets
 * Now checks for current page widgets dynamically
 */
function reign_should_load_learndash_assets() {
	if ( ! class_exists( 'SFWD_LMS' ) ) {
		return false;
	}

	// Check for bypass
	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// LearnDash specific pages
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only asset-loading routing on public search results.
	if ( ( is_search() && isset( $_GET['post_type'] ) && 'sfwd-courses' === sanitize_key( wp_unslash( $_GET['post_type'] ) ) ) ||
		( is_archive() && get_post_type() === 'sfwd-courses' ) ||
		( is_archive() && is_post_type_archive( 'sfwd-courses' ) ) ||
		( is_archive() && get_post_type() === 'sfwd-lessons' ) ||
		( is_archive() && get_post_type() === 'sfwd-topic' ) ||
		( is_archive() && get_post_type() === 'sfwd-quiz' ) ||
		is_singular( array( 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-groups', 'groups' ) )
	) {
		return true;
	}

	// Check for LearnDash shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'ld_course_list' ) ||
		has_shortcode( $post->post_content, 'ld_lesson_list' ) ||
		has_shortcode( $post->post_content, 'ld_quiz_list' ) ||
		has_shortcode( $post->post_content, 'learndash_course_progress' ) ||
		has_shortcode( $post->post_content, 'ld_profile' ) ||
		has_shortcode( $post->post_content, 'ld_course_info' ) ||
		has_shortcode( $post->post_content, 'ld_user_course_points' ) ||
		has_shortcode( $post->post_content, 'learndash_login' ) ||
		has_shortcode( $post->post_content, 'ld_course_resume' )
	) ) {
		return true;
	}

	// ld_dashboard is intentionally excluded — the dashboard page manages
	// its own asset loading and does not need learndash-main.min.css.

	// Check for LearnDash widgets on current page (dynamic detection)
	$learndash_widgets = array(
		'sfwd-certificates-widget',
		'sfwd-courses-widget',
		'sfwd-lessons-widget',
		'sfwd-quiz-widget',
		'learndash_course_progress',
		'learndash_course_navigation',
		'learndash_course_info',
		'learndash_user_status',
		'learndash_block_widget', // Virtual widget for block detection
	);

	if ( reign_current_page_has_widgets( $learndash_widgets ) ) {
		return true;
	}

	// Check for Elementor widgets
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$elementor_plugins = reign_get_elementor_widget_plugins();
		if ( in_array( 'learndash', $elementor_plugins ) ) {
			return true;
		}
	}

	return apply_filters( 'reign_load_learndash_assets', false );
}

/**
 * Check if we should load LearnDash Dashboard plugin assets.
 * Gated by the Ld_Dashboard plugin class, not by the core LearnDash predicate,
 * so ld-dashboard-main.min.css loads only when this plugin is active and its
 * pages are being rendered — not on every LearnDash course/lesson/quiz page.
 */
function reign_should_load_ld_dashboard_assets() {
	if ( ! class_exists( 'Ld_Dashboard' ) ) {
		return false;
	}

	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// Resolve the dashboard page ID stored in plugin settings.
	$dashboard_page_id = 0;
	if ( class_exists( 'Ld_Dashboard_Functions' ) ) {
		$dashboard_page_id = (int) Ld_Dashboard_Functions::instance()->ld_dashboard_get_page_id( 'dashboard' );
	}

	$post = reign_get_singular_post();
	if ( $post ) {
		// Current page is the configured dashboard page.
		if ( $dashboard_page_id && $post->ID === $dashboard_page_id ) {
			return true;
		}

		// Current page contains any LD Dashboard shortcode.
		if (
			has_shortcode( $post->post_content, 'ld_dashboard' ) ||
			has_shortcode( $post->post_content, 'ld_instructor_registration' ) ||
			has_shortcode( $post->post_content, 'ld_dashboard_instructors_list' ) ||
			has_shortcode( $post->post_content, 'ld_student_details' ) ||
			has_shortcode( $post->post_content, 'ld_email' ) ||
			has_shortcode( $post->post_content, 'ld_dashboard_todo_list' )
		) {
			return true;
		}
	}

	return apply_filters( 'reign_load_ld_dashboard_assets', false );
}

/**
 * Check if we should load LifterLMS assets
 * Now checks for current page widgets dynamically
 */
function reign_should_load_lifterlms_assets() {
	if ( ! class_exists( 'LifterLMS' ) ) {
		return false;
	}

	// Check for bypass
	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// LifterLMS specific pages
	if ( is_lifterlms() ||
		is_llms_account_page() ||
		is_llms_checkout() ||
		is_tax( 'membership_cat' ) ||
		is_tax( 'course_cat' ) ||
		is_tax( 'course_tag' )
	) {
		return true;
	}

	// Check for LifterLMS shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'lifterlms_courses' ) ||
		has_shortcode( $post->post_content, 'lifterlms_memberships' ) ||
		has_shortcode( $post->post_content, 'lifterlms_achievements' ) ||
		has_shortcode( $post->post_content, 'lifterlms_certificates' ) ||
		has_shortcode( $post->post_content, 'lifterlms_my_account' ) ||
		has_shortcode( $post->post_content, 'lifterlms_checkout' ) ||
		has_shortcode( $post->post_content, 'lifterlms_pricing_table' )
	) ) {
		return true;
	}

	// Check for LifterLMS widgets on current page (dynamic detection)
	$lifterlms_widgets = array(
		'lifterlms_widget_course_progress',
		'lifterlms_widget_course_syllabus',
		'lifterlms_widget_certificates',
		'lifterlms_widget_my_achievements',
		'llms_course_continue_button',
		'llms_lesson_navigation',
		'lifterlms_block_widget', // Virtual widget for block detection
	);

	if ( reign_current_page_has_widgets( $lifterlms_widgets ) ) {
		return true;
	}

	// Check for Elementor widgets
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$elementor_plugins = reign_get_elementor_widget_plugins();
		if ( in_array( 'lifterlms', $elementor_plugins ) ) {
			return true;
		}
	}

	return apply_filters( 'reign_load_lifterlms_assets', false );
}

/**
 * Check if we should load PeepSo assets
 */
function reign_should_load_peepso_assets() {
	if ( ! class_exists( 'PeepSo' ) ) {
		return false;
	}

	// Check for bypass
	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// PeepSo pages
	if ( class_exists( 'PeepSo' ) && method_exists( 'PeepSo', 'get_page' ) ) {
		$peepso_url = PeepSo::get_page( 'activity' );
		if ( $peepso_url && function_exists( 'get_page_by_path' ) && is_page( get_page_by_path( basename( $peepso_url ) ) ) ) {
			return true;
		}
	}

	// Check if on any PeepSo page
	if ( class_exists( 'PeepSoUrlSegments' ) && method_exists( 'PeepSoUrlSegments', 'get_instance' ) ) {
		$segments = PeepSoUrlSegments::get_instance();
		if ( $segments && isset( $segments->_shortcode_found ) && $segments->_shortcode_found ) {
			return true;
		}
	}

	// Check for PeepSo shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'peepso_activity' ) ||
		has_shortcode( $post->post_content, 'peepso_members' ) ||
		has_shortcode( $post->post_content, 'peepso_groups' ) ||
		has_shortcode( $post->post_content, 'peepso_profile' ) ||
		has_shortcode( $post->post_content, 'peepso_photos' ) ||
		has_shortcode( $post->post_content, 'peepso_register' ) ||
		has_shortcode( $post->post_content, 'peepso_recover' ) ||
		has_shortcode( $post->post_content, 'peepso_notifications' ) ||
		has_shortcode( $post->post_content, 'peepso_search' ) ||
		has_shortcode( $post->post_content, 'peepso_messages' ) ||
		has_shortcode( $post->post_content, 'peepso_external_link_warning' )
	) ) {
		return true;
	}

	// Check for Elementor widgets
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$elementor_plugins = reign_get_elementor_widget_plugins();
		if ( in_array( 'peepso', $elementor_plugins ) ) {
			return true;
		}
	}

	return apply_filters( 'reign_load_peepso_assets', false );
}

/**
 * Check if we should load Dokan assets
 */
function reign_should_load_dokan_assets() {
	if ( ! class_exists( 'WeDevs_Dokan' ) ) {
		return false;
	}

	// Dokan pages
	if ( function_exists( 'dokan_is_store_page' ) && dokan_is_store_page() ) {
		return true;
	}

	if ( function_exists( 'dokan_is_seller_dashboard' ) && dokan_is_seller_dashboard() ) {
		return true;
	}

	// Store listing page
	if ( function_exists( 'dokan_is_store_listing' ) && dokan_is_store_listing() ) {
		return true;
	}

	// WooCommerce pages (Dokan extends WooCommerce)
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		return true;
	}

	// Product page (may have vendor info)
	if ( is_singular( 'product' ) ) {
		return true;
	}

	// Check for Dokan shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'dokan-stores' ) ||
		has_shortcode( $post->post_content, 'dokan-best-selling-product' ) ||
		has_shortcode( $post->post_content, 'dokan-vendor-registration' ) ||
		has_shortcode( $post->post_content, 'dokan-dashboard' ) ||
		has_shortcode( $post->post_content, 'dokan-customer-migration' ) ||
		has_shortcode( $post->post_content, 'dokan-top-rated-product' ) ||
		has_shortcode( $post->post_content, 'dokan-my-orders' ) ||
		has_shortcode( $post->post_content, 'dokan_product_advertisement' ) ||
		has_shortcode( $post->post_content, 'rda_dokan_store_listing' )
	) ) {
		return true;
	}

	return apply_filters( 'reign_load_dokan_assets', false );
}

/**
 * Check if we should load Events Calendar assets
 */
function reign_should_load_events_calendar_assets() {
	if ( ! class_exists( 'Tribe__Events__Main' ) ) {
		return false;
	}

	// Check for bypass
	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// Events Calendar pages
	if ( function_exists( 'tribe_is_event' ) && tribe_is_event() ) {
		return true;
	}

	if ( function_exists( 'tribe_is_event_query' ) && tribe_is_event_query() ) {
		return true;
	}

	if ( function_exists( 'tribe_is_venue' ) && tribe_is_venue() ) {
		return true;
	}

	if ( function_exists( 'tribe_is_organizer' ) && tribe_is_organizer() ) {
		return true;
	}

	if ( function_exists( 'tribe_is_upcoming' ) && tribe_is_upcoming() ) {
		return true;
	}

	if ( function_exists( 'tribe_is_past' ) && tribe_is_past() ) {
		return true;
	}

	if ( function_exists( 'tribe_is_month' ) && tribe_is_month() ) {
		return true;
	}

	if ( function_exists( 'tribe_is_day' ) && tribe_is_day() ) {
		return true;
	}

	// Check for Events Calendar shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'tribe_events' ) ||
		has_shortcode( $post->post_content, 'tribe_mini_calendar' ) ||
		has_shortcode( $post->post_content, 'tribe_event_countdown' )
	) ) {
		return true;
	}

	// Check for Elementor widgets
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$elementor_plugins = reign_get_elementor_widget_plugins();
		if ( in_array( 'eventscalendar', $elementor_plugins ) ) {
			return true;
		}
	}

	return apply_filters( 'reign_load_events_calendar_assets', false );
}

/**
 * Check if we should load BuddyPress assets
 * Now checks for current page widgets dynamically
 */
function reign_should_load_buddypress_assets() {
	if ( ! class_exists( 'BuddyPress' ) ) {
		return false;
	}

	// Check for bypass
	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// Always load on BuddyPress pages
	if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {
		return true;
	}

	// Load on user pages (author archives)
	if ( is_author() ) {
		return true;
	}

	// Check for BuddyPress shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'bp_core_widget_members' ) ||
		has_shortcode( $post->post_content, 'bp_groups' ) ||
		has_shortcode( $post->post_content, 'bp_group' ) ||
		has_shortcode( $post->post_content, 'activity-stream' ) ||
		has_shortcode( $post->post_content, 'bp_member_carousel' ) ||
		has_shortcode( $post->post_content, 'bp_group_carousel' ) ||
		has_shortcode( $post->post_content, 'members-listing' ) ||
		has_shortcode( $post->post_content, 'groups-listing' ) ||
		has_shortcode( $post->post_content, 'activity-listing' )
	) ) {
		return true;
	}

	// Check for BuddyPress widgets on current page (dynamic detection)
	$bp_widgets = array(
		// Classic BP widgets (if any exist)
		'bp_core_members_widget',
		'bp_core_whos_online_widget',
		'bp_core_recently_active_widget',
		'bp_groups_widget',
		'bp_messages_sitewide_notices_widget',
		'bp_core_login_widget',
		'bp_activity_widget',
		'bp_core_friends_widget',
		'bp_messages_inbox_count_widget',
		// Reign BP widgets (actually found in system)
		'bp_reign_members_widget',
		'bp_reign_activity_widget',
		'bp_reign_groups_widget',
		'bp_reign_groups_carousel_widget',
		'bp_reign_bp_login_widget',
		'bp_reign_profile_completion_widget',
		// Virtual widget for Gutenberg blocks
		'bp_block_widget',
	);

	if ( reign_current_page_has_widgets( $bp_widgets ) ) {
		return true;
	}

	// Check for Elementor widgets
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$elementor_plugins = reign_get_elementor_widget_plugins();
		if ( in_array( 'buddypress', $elementor_plugins ) ) {
			return true;
		}
	}

	// Load on specific pages that might use BP features
	if ( is_page() && function_exists( 'bp_core_get_directory_page_ids' ) ) {
		$page_id  = get_the_ID();
		$bp_pages = bp_core_get_directory_page_ids();
		if ( is_array( $bp_pages ) && in_array( $page_id, $bp_pages ) ) {
			return true;
		}
	}

	return apply_filters( 'reign_load_buddypress_assets', false );
}

/**
 * Check if we should load TutorLMS assets
 */
function reign_should_load_tutorlms_assets() {
	if ( ! function_exists( 'tutor' ) ) {
		return false;
	}

	// Check for bypass
	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// TutorLMS pages
	if ( function_exists( 'is_tutor' ) && is_tutor() ) {
		return true;
	}

	// Course archive and single
	if ( is_post_type_archive( 'courses' ) || is_singular( 'courses' ) ) {
		return true;
	}

	// Lesson and quiz pages
	if ( is_singular( array( 'lesson', 'tutor_quiz', 'tutor_assignments' ) ) ) {
		return true;
	}

	// Course category archive
	if ( is_tax( 'course-category' ) ) {
		return true;
	}

	// Load assets on instructor/student public profile
	$tutor_view = isset( $_GET['view'] ) ? sanitize_text_field( wp_unslash( $_GET['view'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only asset-loading routing on a public profile view.
	if ( 'instructor' === $tutor_view || 'student' === $tutor_view ) {
		$username = get_query_var( 'tutor_profile_username' );
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only asset-loading routing on a public profile view.
		if ( empty( $username ) && isset( $_GET['tutor_profile_username'] ) ) {
			$username = sanitize_text_field( wp_unslash( $_GET['tutor_profile_username'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only asset-loading routing on a public profile view.
		}
		if ( ! empty( $username ) ) {
			return true;
		}
	}

	// Dashboard page
	if ( function_exists( 'tutor_utils' ) && function_exists( 'tutor' ) ) {
		$tutor_utils = tutor_utils();
		if ( $tutor_utils && method_exists( $tutor_utils, 'get_option' ) ) {
			$dashboard_page = $tutor_utils->get_option( 'tutor_dashboard_page_id' );
			if ( $dashboard_page && is_page( $dashboard_page ) ) {
				return true;
			}
		}
	}

	// Check for TutorLMS shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'tutor_course' ) ||
		has_shortcode( $post->post_content, 'tutor_student_registration_form' ) ||
		has_shortcode( $post->post_content, 'tutor_dashboard' ) ||
		has_shortcode( $post->post_content, 'tutor_instructor_registration_form' ) ||
		has_shortcode( $post->post_content, 'reign_tutor_course' ) ||
		has_shortcode( $post->post_content, 'reign_course_categories' )
	) ) {
		return true;
	}

	// Check for Elementor widgets
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$elementor_plugins = reign_get_elementor_widget_plugins();
		if ( in_array( 'tutorlms', $elementor_plugins ) ) {
			return true;
		}
	}

	// Check for Reign TutorLMS Addon + BuddyPress "My Courses" tab
	if ( class_exists( 'Reign_Tutorlms_Addon' ) && function_exists( 'bp_is_current_component' ) ) {
		$settings = get_option( 'reign_options', array() );
		if (
			isset( $settings['tutorlms']['enable_profile_courses_tab'] ) &&
			$settings['tutorlms']['enable_profile_courses_tab'] &&
			bp_is_user() &&
			bp_is_current_component( 'courses' )
		) {
			return true;
		}
	}

	return apply_filters( 'reign_load_tutorlms_assets', false );
}

/**
 * Check if we should load GeoDirectory assets
 */
function reign_should_load_geodirectory_assets() {
	if ( ! class_exists( 'GeoDirectory' ) ) {
		return false;
	}

	// GeoDirectory pages
	if ( function_exists( 'geodir_is_geodir_page' ) && geodir_is_geodir_page() ) {
		return true;
	}

	// GeoDirectory CPT pages
	if ( function_exists( 'geodir_get_posttypes' ) ) {
		$gd_post_types = geodir_get_posttypes();
		if ( ! empty( $gd_post_types ) && ( is_singular( $gd_post_types ) || is_post_type_archive( $gd_post_types ) ) ) {
			return true;
		}
	}

	// Check for GeoDirectory shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'gd_listings' ) ||
		has_shortcode( $post->post_content, 'gd_map' ) ||
		has_shortcode( $post->post_content, 'gd_search' ) ||
		has_shortcode( $post->post_content, 'gd_categories' ) ||
		has_shortcode( $post->post_content, 'gd_tags' ) ||
		has_shortcode( $post->post_content, 'gd_add_listing' )
	) ) {
		return true;
	}

	// Check for geodirectory widgets on current page (dynamic detection)
	$geodirectory_widgets = array(
		'gd_map',
		'gd_search',
		'gd_location_switcher',
		'gd_add_listing',
	);

	if ( reign_current_page_has_widgets( $geodirectory_widgets ) ) {
		return true;
	}

	return apply_filters( 'reign_load_geodirectory_assets', false );
}

/**
 * Check if we should load WP Job Manager assets
 */
function reign_should_load_wp_job_manager_assets() {
	if ( ! class_exists( 'WP_Job_Manager' ) ) {
		return false;
	}

	// Job/Resume listing pages
	if (
		is_singular( array( 'job_listing', 'resume' ) ) ||
		is_post_type_archive( array( 'job_listing', 'resume' ) ) ||
		is_tax( get_object_taxonomies( array( 'job_listing', 'resume' ) ) )
	) {
		return true;
	}

	// Check for WP Job Manager shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'jobs' ) ||
		has_shortcode( $post->post_content, 'job' ) ||
		has_shortcode( $post->post_content, 'job_dashboard' ) ||
		has_shortcode( $post->post_content, 'submit_job_form' ) ||
		has_shortcode( $post->post_content, 'job_summary' ) ||
		has_shortcode( $post->post_content, 'submit_resume_form' ) ||
		has_shortcode( $post->post_content, 'candidate_dashboard' ) ||
		has_shortcode( $post->post_content, 'past_applications' ) ||
		has_shortcode( $post->post_content, 'resumes' ) ||
		has_shortcode( $post->post_content, 'embeddable_job_widget_generator' ) ||
		has_shortcode( $post->post_content, 'my_bookmarks' ) ||
		has_shortcode( $post->post_content, 'job_alerts' ) ||
		has_shortcode( $post->post_content, 'astoundify-favorites-dashboard' ) ||
		has_shortcode( $post->post_content, 'jobmate_job_listing' ) ||
		has_shortcode( $post->post_content, 'jobmate_resume_listing' ) ||
		has_shortcode( $post->post_content, 'job_tag_cloud' ) ||
		has_shortcode( $post->post_content, 'jobs_by_tag' ) ||
		has_shortcode( $post->post_content, 'job_apply' ) ||
		has_shortcode( $post->post_content, 'job_manager_companies' )
	) ) {
		return true;
	}

	// Check for WP Job Manager widgets on current page (dynamic detection)
	$current_page_widgets = reign_get_current_page_widgets();

	// Check if any widget contains job manager related strings
	foreach ( $current_page_widgets as $widget_id ) {
		if ( false !== strpos( $widget_id, 'job_manager' ) ||
			false !== strpos( $widget_id, 'wp_job' ) ||
			false !== strpos( $widget_id, 'resume_manager' ) ||
			( false !== strpos( $widget_id, 'job' ) && false !== strpos( $widget_id, 'manager' ) ) ||
			( false !== strpos( $widget_id, 'job' ) && false !== strpos( $widget_id, 'listing' ) ) ||
			false !== strpos( $widget_id, 'resume' ) ) {
			return true;
		}
	}

	// Also check specific known widget IDs
	$wp_job_manager_widgets = array(
		'wp_job_manager_widget_recent_jobs',
		'wp_job_manager_widget_featured_jobs',
		'wp_job_manager_widget_job_categories',
		'wp_job_manager_widget_job_summary',
		'wp_job_manager_widget_job_search',
		'wp_resume_manager_widget_recent_resumes',
		'wp_resume_manager_widget_featured_resumes',
		'job_manager_recent_jobs',
		'job_manager_featured_jobs',
	);

	if ( reign_current_page_has_widgets( $wp_job_manager_widgets ) ) {
		return true;
	}

	return apply_filters( 'reign_load_wp_job_manager_assets', false );
}

/**
 * Check if we should load rtMedia assets
 */
function reign_should_load_rtmedia_assets() {
	if ( ! class_exists( 'RTMedia' ) ) {
		return false;
	}

	// Check for bypass
	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// Always load on BuddyPress pages
	if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {
		return true;
	}

	// rtMedia pages
	if ( function_exists( 'is_rtmedia_page' ) && is_rtmedia_page() ) {
		return true;
	}

	// Check for rtMedia shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'rtmedia_gallery' ) ||
		has_shortcode( $post->post_content, 'rtmedia_uploader' )
	) ) {
		return true;
	}

	// Check for rtMedia widgets
	if ( is_active_widget( false, false, 'rtmedia_gallery_widget' ) ||
		is_active_widget( false, false, 'rtmedia_upload_widget' ) ) {
		return true;
	}

	// Check for Elementor widgets
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$elementor_plugins = reign_get_elementor_widget_plugins();
		if ( in_array( 'rtmedia', $elementor_plugins ) ) {
			return true;
		}
	}

	return apply_filters( 'reign_load_rtmedia_assets', false );
}

/**
 * Check if we should load BP Better Messages assets
 */
function reign_should_load_bp_better_messages_assets() {
	if ( ! class_exists( 'Better_Messages' ) ) {
		return false;
	}

	// Always load on BuddyPress pages
	if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {
		return true;
	}

	// Load on messages component (covers all message-related pages)
	if ( class_exists( 'Better_Messages_Mini_List' ) ) {
		return true;
	}

	// Always load on messages component
	if ( function_exists( 'bp_is_messages_component' ) && bp_is_messages_component() ) {
		return true;
	}

	// Check for Better Messages shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'bp_better_messages' ) ||
		has_shortcode( $post->post_content, 'bp_better_messages_pm_button' ) ||
		has_shortcode( $post->post_content, 'bp_better_messages_unread_counter' )
	) ) {
		return true;
	}

	// Always load if mini widgets are enabled (they can appear site-wide)
	if ( function_exists( 'Better_Messages' ) ) {
		$bm_instance = Better_Messages();
		if ( $bm_instance && isset( $bm_instance->settings ) && is_array( $bm_instance->settings ) ) {
			$settings = $bm_instance->settings;
			if ( isset( $settings['miniWidgetsEnable'] ) && '1' == $settings['miniWidgetsEnable'] ) {
				return true;
			}
		}
	}

	return apply_filters( 'reign_load_bp_better_messages_assets', false );
}

/**
 * Check if we should load WC Vendors assets
 */
function reign_should_load_wc_vendors_assets() {
	if ( ! class_exists( 'WC_Vendors' ) ) {
		return false;
	}

	// Vendor dashboard pages
	if ( function_exists( 'is_wcv_vendor_dashboard' ) && is_wcv_vendor_dashboard() ) {
		return true;
	}

	// Vendor store pages
	if ( function_exists( 'wcv_is_vendor_page' ) && wcv_is_vendor_page() ) {
		return true;
	}

	// WooCommerce pages (vendors extend WooCommerce)
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		return true;
	}

	// Check for WC Vendors shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'wcv_vendorslist' ) ||
		has_shortcode( $post->post_content, 'wcv_shop_settings' ) ||
		has_shortcode( $post->post_content, 'wcv_orders' ) ||
		has_shortcode( $post->post_content, 'wcv_vendor_dashboard' ) ||
		has_shortcode( $post->post_content, 'wcv_sold_by' ) ||
		has_shortcode( $post->post_content, 'wcv_recent_products' ) ||
		has_shortcode( $post->post_content, 'wcv_products' ) ||
		has_shortcode( $post->post_content, 'wcv_featured_products' ) ||
		has_shortcode( $post->post_content, 'wcv_sale_products' ) ||
		has_shortcode( $post->post_content, 'wcv_top_rated_products' ) ||
		has_shortcode( $post->post_content, 'wcv_best_selling_products' ) ||
		has_shortcode( $post->post_content, 'wcv_product_category' ) ||
		has_shortcode( $post->post_content, 'wcv_pro_dashboard' ) ||
		has_shortcode( $post->post_content, 'wcv_feedback_form' ) ||
		has_shortcode( $post->post_content, 'wcv_pro_dashboard_nav' ) ||
		has_shortcode( $post->post_content, 'wcv_feedback' ) ||
		has_shortcode( $post->post_content, 'wcv_pro_vendorslist' ) ||
		has_shortcode( $post->post_content, 'wcv_vendor' ) ||
		has_shortcode( $post->post_content, 'wcv_membership_plans' ) ||
		has_shortcode( $post->post_content, 'wcvm_membership_plans' )
	) ) {
		return true;
	}

	return apply_filters( 'reign_load_wc_vendors_assets', false );
}

/**
 * Check if we should load WCFM assets
 */
function reign_should_load_wcfm_assets() {
	if ( ! class_exists( 'WCFM' ) ) {
		return false;
	}

	// WCFM pages
	if ( function_exists( 'wcfm_is_marketplace' ) && wcfm_is_marketplace() ) {
		return true;
	}

	if ( function_exists( 'wcfm_is_store_page' ) && wcfm_is_store_page() ) {
		return true;
	}

	// WooCommerce pages (WCFM extends WooCommerce)
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		return true;
	}

	// Check for WCFM shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'wcfm' ) ||
		has_shortcode( $post->post_content, 'wcfm_stores' ) ||
		has_shortcode( $post->post_content, 'wcfm_store_info' ) ||
		has_shortcode( $post->post_content, 'wc_frontend_manager' ) ||
		has_shortcode( $post->post_content, 'wcfm_vendor_membership' ) ||
		has_shortcode( $post->post_content, 'wcfm_vendor_registration' ) ||
		has_shortcode( $post->post_content, 'wcfm_chat_now' ) ||
		has_shortcode( $post->post_content, 'wcfm_enquiry' ) ||
		has_shortcode( $post->post_content, 'wcfm_follow' ) ||
		has_shortcode( $post->post_content, 'wcfm_more_offers' ) ||
		has_shortcode( $post->post_content, 'wcfm_notifications' ) ||
		has_shortcode( $post->post_content, 'wcfm_policy' ) ||
		has_shortcode( $post->post_content, 'wcfm_product_custom_field_show' ) ||
		has_shortcode( $post->post_content, 'wcfm_shipping_time' ) ||
		has_shortcode( $post->post_content, 'wcfm_store_fb_feed' ) ||
		has_shortcode( $post->post_content, 'wcfm_store_twitter_feed' ) ||
		has_shortcode( $post->post_content, 'wcfm_store_sold_by' ) ||
		has_shortcode( $post->post_content, 'wcfm_store_hours' ) ||
		has_shortcode( $post->post_content, 'wcfm_stores_carousel' ) ||
		has_shortcode( $post->post_content, 'wcfmvm_subscribe' )
	) ) {
		return true;
	}

	return apply_filters( 'reign_load_wcfm_assets', false );
}

/**
 * Check if we should load MultivendorX assets
 */
function reign_should_load_multivendorx_assets() {
	if ( ! class_exists( 'MVX' ) ) {
		return false;
	}

	// MultivendorX vendor pages
	if ( function_exists( 'is_vendor_page' ) && is_vendor_page() ) {
		return true;
	}

	if ( function_exists( 'is_vendor_dashboard' ) && is_vendor_dashboard() ) {
		return true;
	}

	// WooCommerce pages (MultivendorX extends WooCommerce)
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		return true;
	}

	// Check for MultivendorX shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'mvx_vendorslist' ) ||
		has_shortcode( $post->post_content, 'mvx_vendor' ) ||
		has_shortcode( $post->post_content, 'vendor_dashboard' ) ||
		has_shortcode( $post->post_content, 'vendor_registration' ) ||
		has_shortcode( $post->post_content, 'vendor_coupons' ) ||
		has_shortcode( $post->post_content, 'mvx_recent_products' ) ||
		has_shortcode( $post->post_content, 'mvx_products' ) ||
		has_shortcode( $post->post_content, 'mvx_featured_products' ) ||
		has_shortcode( $post->post_content, 'mvx_sale_products' ) ||
		has_shortcode( $post->post_content, 'mvx_top_rated_products' ) ||
		has_shortcode( $post->post_content, 'mvx_best_selling_products' ) ||
		has_shortcode( $post->post_content, 'mvx_product_category' )
	) ) {
		return true;
	}

	return apply_filters( 'reign_load_multivendorx_assets', false );
}

/**
 * Check if we should load Sensei LMS assets
 */
function reign_should_load_sensei_assets() {
	if ( ! class_exists( 'Sensei_Main' ) ) {
		return false;
	}

	// Sensei pages
	if ( function_exists( 'is_sensei' ) && is_sensei() ) {
		return true;
	}

	// Course and lesson pages
	if ( is_singular( array( 'course', 'lesson', 'quiz' ) ) ) {
		return true;
	}

	if ( is_post_type_archive( array( 'course', 'lesson' ) ) ) {
		return true;
	}

	// Check for reign sensei addon
	if (
		class_exists( 'Reign_Sensei_Addon' ) &&
		function_exists( 'bp_is_group_single' ) &&
		function_exists( 'bp_is_current_action' ) &&
		bp_is_group_single() &&
		bp_is_current_action( 'courses' )
	) {
		return true;
	}

	// Check for Sensei shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'sensei_courses' ) ||
		has_shortcode( $post->post_content, 'sensei_user_courses' ) ||
		has_shortcode( $post->post_content, 'sensei_featured_courses' )
	) ) {
		return true;
	}

	// Check for Sensei widgets using our unified system
	$sensei_widgets = array(
		'sensei_category_courses',
		'sensei_course_categories',
		'sensei_course_component',
		'sensei_lesson_component',
	);

	if ( reign_current_page_has_widgets( $sensei_widgets ) ) {
		return true;
	}

	return apply_filters( 'reign_load_sensei_assets', false );
}

/**
 * Check if we should load Youzify assets
 */
function reign_should_load_youzify_assets() {
	if ( ! class_exists( 'Youzify' ) ) {
		return false;
	}

	// Always load on BuddyPress pages as Youzify extends BuddyPress
	if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {
		return true;
	}

	// User pages (author archives) - Youzify profiles
	if ( is_author() ) {
		return true;
	}

	// Check for Youzify shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'youzify_members' ) ||
		has_shortcode( $post->post_content, 'youzify_groups' ) ||
		has_shortcode( $post->post_content, 'youzify_wall' )
	) ) {
		return true;
	}

	return apply_filters( 'reign_load_youzify_assets', false );
}

/**
 * Check if we should load WP Event Manager assets
 */
function reign_should_load_wp_event_manager_assets() {
	if ( ! class_exists( 'WP_Event_Manager' ) ) {
		return false;
	}

	// Event pages
	if ( is_post_type_archive( 'event_listing' ) || is_singular( 'event_listing' ) ) {
		return true;
	}

	// Event categories and tags
	if ( is_tax( array( 'event_listing_category', 'event_listing_tag' ) ) ) {
		return true;
	}

	// Check for WP Event Manager shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'events' ) ||
		has_shortcode( $post->post_content, 'event' ) ||
		has_shortcode( $post->post_content, 'submit_event_form' ) ||
		has_shortcode( $post->post_content, 'event_dashboard' )
	) ) {
		return true;
	}

	return apply_filters( 'reign_load_wp_event_manager_assets', false );
}

/**
 * Check if we should load Directorist assets
 */
function reign_should_load_directorist_assets() {
	if ( ! class_exists( 'Directorist_Base' ) ) {
		return false;
	}

	// Directorist listing pages
	if ( function_exists( 'is_directorist' ) && is_directorist() ) {
		return true;
	}

	// Custom post types for listings
	if ( is_singular( 'at_biz_dir' ) || is_post_type_archive( 'at_biz_dir' ) ) {
		return true;
	}

	// Check for Directorist shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'directorist_all_listing' ) ||
		has_shortcode( $post->post_content, 'directorist_search_listing' ) ||
		has_shortcode( $post->post_content, 'directorist_category' ) ||
		has_shortcode( $post->post_content, 'directorist_user_dashboard' ) ||
		has_shortcode( $post->post_content, 'directorist_payment_receipt' ) ||
		has_shortcode( $post->post_content, 'directorist_author_profile' ) ||
		has_shortcode( $post->post_content, 'directorist_signin_signup' ) ||
		has_shortcode( $post->post_content, 'directorist_all_categories' ) ||
		has_shortcode( $post->post_content, 'directorist_all_locations' ) ||
		has_shortcode( $post->post_content, 'directorist_location' ) ||
		has_shortcode( $post->post_content, 'directorist_tag' ) ||
		has_shortcode( $post->post_content, 'directorist_search_result' ) ||
		has_shortcode( $post->post_content, 'directorist_transaction_failure' ) ||
		has_shortcode( $post->post_content, 'directorist_add_listing' )
	) ) {
		return true;
	}

	return apply_filters( 'reign_load_directorist_assets', false );
}

/**
 * Check if we should load WooCommerce assets
 *
 * @param string $asset_type Optional. Specify 'css' or 'js' for asset-specific filtering. Default empty.
 * @return bool True if WooCommerce assets should load, false otherwise.
 */
function reign_should_load_woocommerce_assets( $asset_type = '' ) {
	if ( ! class_exists( 'woocommerce' ) && ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	// Check for bypass
	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// Check if we're on a WooCommerce page
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		return true;
	}

	// Check for WooCommerce specific pages
	if ( function_exists( 'is_cart' ) && is_cart() ) {
		return true;
	}

	if ( function_exists( 'is_checkout' ) && is_checkout() ) {
		return true;
	}

	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		return true;
	}

	// Check if it's a product page
	if ( is_singular( 'product' ) ) {
		return true;
	}

	// Check for shop page
	if ( function_exists( 'is_shop' ) && is_shop() ) {
		return true;
	}

	// Check for dokan
	if ( function_exists( 'dokan_is_store_page' ) && dokan_is_store_page() ) {
		return true;
	}

	// Check for reign dokan addon
	if ( class_exists( 'Reign_Dokan_Addon' ) && function_exists( 'bp_current_component' ) ) {
		$current_component = bp_current_component();
		$current_action    = bp_current_action();

		// Store tab
		if ( 'store' === $current_component ) {
			return true;
		}

		// Wishlists tab
		if ( 'whislists' === $current_action ) {
			return true;
		}

		// Favorite tab
		if ( 'favorite' === $current_action ) {
			return true;
		}
	}

	// Check for reign wcfm addon
	if ( class_exists( 'Reign_Wcfm_Addon' ) && function_exists( 'bp_current_component' ) ) {
		$current_tab = bp_current_component();

		if ( in_array( $current_tab, array( 'favourite', 'store' ), true ) ) {
			return true;
		}
	}

	// Check for reign wc vendor addon
	if ( class_exists( 'Reign_Wcvendors_Addon' ) && function_exists( 'bp_current_component' ) ) {
		$current_component = bp_current_component();
		$current_action    = bp_current_action();

		if ( 'favorite' === $current_component || 'store' === $current_component ) {
			return true;
		}
	}

	// Check for WooCommerce widgets using our unified system
	$woo_widgets = array(
		// Classic WooCommerce widgets
		'woocommerce_widget_cart',
		'woocommerce_layered_nav_filters',
		'woocommerce_layered_nav',
		'woocommerce_price_filter',
		'woocommerce_product_categories',
		'woocommerce_product_search',
		'woocommerce_product_tag_cloud',
		'woocommerce_products',
		'woocommerce_recently_viewed_products',
		'woocommerce_top_rated_products',
		'woocommerce_recent_reviews',
		'woocommerce_rating_filter',
		// WooCommerce Brands widgets
		'wc_brands_brand_description',
		'woocommerce_brand_nav',
		'wc_brands_brand_thumbnails',
		// Virtual widget for Gutenberg blocks
		'woocommerce_block_widget',
	);

	if ( reign_current_page_has_widgets( $woo_widgets ) ) {
		return true;
	}

	// Check for Elementor widgets
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		$elementor_plugins = reign_get_elementor_widget_plugins();
		if ( in_array( 'woocommerce', $elementor_plugins ) ) {
			return true;
		}
	}

	// Check if content contains WooCommerce shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'products' ) ||
		has_shortcode( $post->post_content, 'product' ) ||
		has_shortcode( $post->post_content, 'product_page' ) ||
		has_shortcode( $post->post_content, 'product_category' ) ||
		has_shortcode( $post->post_content, 'add_to_cart' ) ||
		has_shortcode( $post->post_content, 'woocommerce_cart' ) ||
		has_shortcode( $post->post_content, 'woocommerce_checkout' ) ||
		has_shortcode( $post->post_content, 'woocommerce_my_account' ) ||
		has_shortcode( $post->post_content, 'reign_woo_product_layout' ) ||
		has_shortcode( $post->post_content, 'rg_woo_product_categories' ) ||
		has_shortcode( $post->post_content, 'rg_woo_product_category_with_subcategory' )
	) ) {
		return true;
	}

	// Apply appropriate filter based on asset type
	$should_load = false;
	if ( 'css' === $asset_type ) {
		$should_load = apply_filters( 'reign_load_woocommerce_css', $should_load );
	} elseif ( 'js' === $asset_type ) {
		$should_load = apply_filters( 'reign_load_woocommerce_js', $should_load );
	} else {
		$should_load = apply_filters( 'reign_load_woocommerce_assets', $should_load );
	}

	return $should_load;
}

/**
 * Check if we should load PMPRO assets
 */
function reign_should_load_pmpro_assets() {
	if ( ! defined( 'PMPRO_VERSION' ) ) {
		return false;
	}

	// Paid Memberships Pro pages
	if ( function_exists( 'pmpro_is_login_page' ) && pmpro_is_login_page() ) {
		return true;
	}

	if ( function_exists( 'pmpro_is_register_page' ) && pmpro_is_register_page() ) {
		return true;
	}

	if ( function_exists( 'pmpro_is_checkout' ) && pmpro_is_checkout() ) {
		return true;
	}

	// Check for PMPRO shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'pmpro_membership' ) ||
		has_shortcode( $post->post_content, 'membership' ) ||
		has_shortcode( $post->post_content, 'pmpro_login' ) ||
		has_shortcode( $post->post_content, 'pmpro_account' ) ||
		has_shortcode( $post->post_content, 'pmpro_levels' ) ||
		has_shortcode( $post->post_content, 'pmpro_member_profile_edit' ) ||
		has_shortcode( $post->post_content, 'pmpro_invoice' ) ||
		has_shortcode( $post->post_content, 'pmpro_confirmation' ) ||
		has_shortcode( $post->post_content, 'pmpro_checkout' ) ||
		has_shortcode( $post->post_content, 'pmpro_billing' ) ||
		has_shortcode( $post->post_content, 'pmpro_cancel' )
	) ) {
		return true;
	}

	return apply_filters( 'reign_load_pmpro_assets', false );
}

/**
 * Check if we should load wpForo assets
 */
function reign_should_load_wpforo_assets() {
	if ( ! function_exists( 'is_plugin_active' ) || ! is_plugin_active( 'wpforo/wpforo.php' ) ) {
		return false;
	}

	// wpForo pages
	if ( function_exists( 'WPF' ) && function_exists( 'wpforo_is_wpforo_page' ) && wpforo_is_wpforo_page() ) {
		return true;
	}

	// Check for wpForo shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'wpforo' ) ||
		has_shortcode( $post->post_content, 'wpforo-login' ) ||
		has_shortcode( $post->post_content, 'wpforo-register' )
	) ) {
		return true;
	}

	return apply_filters( 'reign_load_wpforo_assets', false );
}

/**
 * Check if we should load BB Platform assets
 */
function reign_should_load_bb_platform_assets() {
	if ( ! defined( 'BP_PLATFORM_VERSION' ) ) {
		return false;
	}

	// BuddyBoss Platform extends BuddyPress - use BP logic
	return reign_should_load_buddypress_assets();
}

/**
 * Check if we should load SureCart assets
 */
function reign_should_load_surecart_assets() {
	if ( ! defined( 'SURECART_PLUGIN_FILE' ) ) {
		return false;
	}

	// SureCart pages
	if ( function_exists( 'surecart_is_checkout_page' ) && surecart_is_checkout_page() ) {
		return true;
	}

	if ( function_exists( 'surecart_is_account_page' ) && surecart_is_account_page() ) {
		return true;
	}

	// Product pages
	if ( is_singular( 'sc_product' ) || is_post_type_archive( 'sc_product' ) ) {
		return true;
	}

	// Check for SureCart shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'surecart' ) ||
		has_shortcode( $post->post_content, 'sc_product' ) ||
		has_shortcode( $post->post_content, 'sc_checkout' )
	) ) {
		return true;
	}

	return apply_filters( 'reign_load_surecart_assets', false );
}

/**
 * Check if we should load FluentCart assets
 */
function reign_should_load_fluentcart_assets() {
	// Check if FluentCart plugin is active by its constant.
	if ( ! defined( 'FLUENTCART_PLUGIN_FILE_PATH' ) ) {
		return false;
	}

	// FluentCart store pages (checkout, receipt, shop, cart, customer
	// profile) are stored as page IDs in the store settings option —
	// same source Reign_FluentCart_Support uses for body classes.
	$store_settings = get_option( 'fluent_cart_store_settings', array() );
	$page_keys      = array( 'checkout_page_id', 'receipt_page_id', 'shop_page_id', 'cart_page_id', 'customer_profile_page_id' );
	foreach ( $page_keys as $page_key ) {
		if ( ! empty( $store_settings[ $page_key ] ) && is_page( (int) $store_settings[ $page_key ] ) ) {
			return true;
		}
	}

	// Product pages.
	if ( is_singular( 'fluent-products' ) || is_post_type_archive( 'fluent-products' ) || is_tax( 'product-categories' ) || is_tax( 'product-brands' ) ) {
		return true;
	}

	return apply_filters( 'reign_load_fluentcart_assets', false );
}

/**
 * Check if we should load PrductRoadmap assets
 */
function reign_should_load_product_roadmap_assets() {
	// Check if PrductRoadmap plugin is active by its constant.
	if ( ! class_exists( 'RoadmapPlugin' ) ) {
		return false;
	}

	// Roadmap post types.
	if ( is_singular( array( 'roadmap_item', 'roadmap_changelog' ) ) || is_post_type_archive( array( 'roadmap_item', 'roadmap_changelog' ) ) ) {
		return true;
	}

	// The designated roadmap page (set by the plugin's setup wizard).
	$roadmap_page_id = (int) get_option( 'roadmap_page_id' );
	if ( $roadmap_page_id && is_page( $roadmap_page_id ) ) {
		return true;
	}

	// Check for roadmap shortcodes.
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'roadmap' ) ||
		has_shortcode( $post->post_content, 'roadmap_changelog' )
	) ) {
		return true;
	}

	return apply_filters( 'reign_load_product_roadmap_assets', false );
}

/**
 * Check if we should load GamiPress assets
 */
function reign_should_load_gamipress_assets() {
	if ( ! class_exists( 'GamiPress' ) ) {
		return false;
	}

	// Always load on BuddyPress pages (GamiPress integrates with BP)
	if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {
		return true;
	}

	// GamiPress achievement pages
	if ( is_singular( 'achievement' ) || is_post_type_archive( 'achievement' ) ) {
		return true;
	}

	// GamiPress rank pages
	if ( is_singular( 'rank' ) || is_post_type_archive( 'rank' ) ) {
		return true;
	}

	// GamiPress point type pages
	if ( is_singular( 'point' ) || is_post_type_archive( 'point' ) ) {
		return true;
	}

	// Check for GamiPress shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'gamipress_achievement' ) ||
		has_shortcode( $post->post_content, 'gamipress_achievements' ) ||
		has_shortcode( $post->post_content, 'gamipress_points' ) ||
		has_shortcode( $post->post_content, 'gamipress_ranks' ) ||
		has_shortcode( $post->post_content, 'gamipress_leaderboard' )
	) ) {
		return true;
	}

	// Check for GamiPress widgets
	if ( reign_is_widget_active( 'gamipress_achievement_widget' ) ||
		reign_is_widget_active( 'gamipress_points_widget' ) ||
		reign_is_widget_active( 'gamipress_rank_widget' ) ) {
		return true;
	}

	return apply_filters( 'reign_load_gamipress_assets', false );
}

/**
 * Check if we should load Shoplentor assets
 */
function reign_should_load_shoplentor_assets() {
	if ( ! ( class_exists( 'WooLentor' ) || defined( 'WOOLENTOR_VERSION' ) ) ) {
		return false;
	}

	if ( ! class_exists( 'WooCommerce' ) ) {
		return false;
	}

	// Check for bypass
	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// Check for WooLentor shortcodes
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'woolentor' ) ||
		has_shortcode( $post->post_content, 'woolentor_products' ) ||
		has_shortcode( $post->post_content, 'woolentor_product_tab' ) ||
		has_shortcode( $post->post_content, 'woolentor_product_grid' ) ||
		has_shortcode( $post->post_content, 'woolentor_product_slider' ) ||
		has_shortcode( $post->post_content, 'wl_product_archive' )
	) ) {
		return true;
	}

	// Check for WooLentor widgets
	$shoplentor_widgets = array(
		'woolentor_product_grid',
		'woolentor_product_slider',
		'woolentor_product_tab',
	);

	if ( reign_current_page_has_widgets( $shoplentor_widgets ) ) {
		return true;
	}

	// Load on WooCommerce pages (WooLentor extends WooCommerce)
	if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
		return true;
	}

	// Load on product pages
	if ( is_singular( 'product' ) ) {
		return true;
	}

	return apply_filters( 'reign_load_shoplentor_assets', false );
}

/**
 * Check if we should load Stachethemes Event Calendar assets.
 *
 * Runs at wp_enqueue_scripts priority 5001, after the plugin has already
 * decided (priority 11) whether to enqueue stec-css-dependencies. The
 * wp_style_is() check below mirrors the plugin's own has_calendar_entries()
 * logic so we always stay in sync with it.
 */
function reign_should_load_stachethemes_assets() {

	if ( ! defined( 'STEC_PLUGIN_VERSION' ) ) {
		return false;
	}

	// Check for bypass (Customizer, Elementor editor, etc.)
	if ( reign_bypass_conditional_assets() ) {
		return true;
	}

	// Primary check: mirror the plugin's own decision.
	// The plugin enqueues stec-css-dependencies when it detects calendar
	// content (shortcode, REST block, BuddyPress tab, etc.). If it decided
	// to load, we load too — no need to duplicate all detection logic here.
	if ( wp_style_is( 'stec-css-dependencies', 'enqueued' ) ||
		wp_style_is( 'stec-css-dependencies', 'to_do' ) ) {
		return true;
	}

	// Fallback shortcode check (covers pages with calendar shortcodes).
	$post = reign_get_singular_post();
	if ( $post && (
		has_shortcode( $post->post_content, 'stec' ) ||
		has_shortcode( $post->post_content, 'stachethemes_ec' ) ||
		has_shortcode( $post->post_content, 'stec_dashboard' ) ||
		has_shortcode( $post->post_content, 'stec_events_list' ) ||
		has_shortcode( $post->post_content, 'stec_events_slider' ) ||
		has_shortcode( $post->post_content, 'stec_single' ) ||
		has_shortcode( $post->post_content, 'stec_submit_form' ) ||
		has_shortcode( $post->post_content, 'stec_event_tickets' )
	) ) {
		return true;
	}

	// Fallback: single event page or calendar archive.
	if ( is_singular( 'stec_event' ) || is_post_type_archive( 'stec_event' ) ) {
		return true;
	}

	// Fallback: any BuddyPress member profile or group page when the plugin
	// has BP integration active. Load on the whole member/group context so
	// the calendar tab navigation renders correctly on first visit.
	if ( function_exists( 'bp_is_user' ) && bp_is_user() ) {
		return true;
	}

	if ( function_exists( 'bp_is_group' ) && bp_is_group() ) {
		return true;
	}

	return apply_filters( 'reign_load_stachethemes_assets', false );
}
