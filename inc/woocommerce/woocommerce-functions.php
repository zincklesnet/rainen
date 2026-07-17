<?php
/**
 * Support For WooCommerce
 *
 * @package reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

add_filter( 'reign_alter_display_right_sidebar', 'reign_alter_display_right_sidebar_for_woo', 10, 1 );

/**
 *
 * Function to hide right sideabr at woocommerce cart and shop page.
 *
 * @since 2.6.0
 */
function reign_alter_display_right_sidebar_for_woo( $display ) {
	if ( class_exists( 'WooCommerce' ) ) {
		if ( is_cart() || is_checkout() ) {
			$display = false;
		}
	}
	return $display;
}

// add_action( 'woocommerce_before_cart', 'reign_display_breadcrumb_at_checkout' );
// add_action( 'woocommerce_before_checkout_form', 'reign_display_breadcrumb_at_checkout' );
if ( ! class_exists( 'WooCommerce_Germanized' ) ) {
	add_action( 'rtm_post_begins', 'reign_display_breadcrumb_at_checkout' );
}

/**
 *
 * Function to display breadcrumb at woocommerce cart and checkout page.
 *
 * @since 2.6.0
 */
function reign_display_breadcrumb_at_checkout() {
	if ( class_exists( 'WooCommerce' ) ) {
		if ( is_cart() || is_checkout() || is_wc_endpoint_url( 'order-received' ) ) {
			?>
			<div class="rg-woo-breadcrumbs-wrapper page-title">
				<nav class="rg-woo-breadcrumbs breadcrumbs heading-font checkout-breadcrumbs h3">
					<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="<?php echo esc_attr( reign_woo_checkout_breadcrumb_class( 'cart' ) ); ?>"><?php esc_html_e( 'Shopping Cart', 'reign' ); ?></a>
					<span class="divider hide-for-small"><i class="far fa-arrow-right"></i></span>
					<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="<?php echo esc_attr( reign_woo_checkout_breadcrumb_class( 'checkout' ) ); ?>"><?php esc_html_e( 'Checkout Details', 'reign' ); ?></a>
					<span class="divider hide-for-small"><i class="far fa-arrow-right"></i></span>
					<a href="#" class="no-click <?php echo esc_attr( reign_woo_checkout_breadcrumb_class( 'order-received' ) ); ?>"><?php esc_html_e( 'Order Complete', 'reign' ); ?></a>
				</nav>
			</div><!-- .page-title -->
			<?php
		}
	}
}

function reign_woo_checkout_breadcrumb_class( $endpoint ) {
	$classes = array();
	if ( $endpoint == 'cart' && is_cart() || $endpoint == 'checkout' && is_checkout() && ! is_wc_endpoint_url( 'order-received' ) ||
	$endpoint == 'order-received' && is_wc_endpoint_url( 'order-received' ) ) {
		$classes[] = 'current';
	} else {
		$classes[] = 'hide-for-small';
	}
	return implode( ' ', $classes );
}

add_action( 'woocommerce_before_account_navigation', 'reign_woo_my_account_avatar' );

function reign_woo_my_account_avatar() {
	$logout_url = ( function_exists( 'wc_logout_url' ) ) ? wc_logout_url() : wc_get_endpoint_url( 'customer-logout', '' );
	?>
	<div class="rg-woo-account-user circle">
		<div class="rg-woo-account-content-wrapper">
			<div class="rg-woo-user-avatar">
				<?php
				$current_user = wp_get_current_user();
				$user_id      = $current_user->ID;
				echo get_avatar( $user_id, 150 );
				?>
			</div>
			<div class="rg-woo-user-info">
				<div class="user-name">
					<?php
					echo esc_html( $current_user->display_name );
					?>
				</div>
				<div class="user-email">
					<?php
					$current_user = wp_get_current_user();
					echo esc_html( $current_user->user_email );
					?>
				</div>
				<div class="user-logout"><a href="<?php echo esc_url( $logout_url ); ?>"><?php esc_html_e( 'Log Out', 'reign' ); ?></a></div>
			</div>
		</div>
	</div>
	<?php
}

if ( ! function_exists( 'reign_sanitize_checkbox' ) ) {
	/**
	 * Checkbox sanitization callback
	 *
	 * @since 7.1.2
	 */
	if ( class_exists( 'WooCommerce' ) ) {
		function reign_sanitize_checkbox( $checked ) {
			// Boolean check.
			return ( ( isset( $checked ) && true == $checked ) ? true : false );
		}
	}
}

// Remove orderby if disabled.
if ( ! reign_is_truthy( get_theme_mod( 'reign_woo_shop_sort', true ) ) ) {
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
}

// Remove result count if disabled.
if ( ! reign_is_truthy( get_theme_mod( 'reign_woo_shop_result_count', true ) ) ) {
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
}

/**
 * Checks if on the WooCommerce shop page.
 *
 * @since 7.1.2
 */
if ( ! function_exists( 'reign_is_woo_shop' ) ) {
	if ( class_exists( 'WooCommerce' ) ) {
		function reign_is_woo_shop() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return false;
			} elseif ( function_exists( 'is_shop' ) && is_shop() ) {
				return true;
			}
		}
	}
}

/**
 * Checks if on a WooCommerce tax.
 *
 * @since 7.1.2
 */
if ( ! function_exists( 'reign_is_woo_tax' ) ) {
	if ( class_exists( 'WooCommerce' ) ) {
		function reign_is_woo_tax() {
			if ( ! class_exists( 'WooCommerce' ) ) {
				return false;
			} elseif ( ! is_tax() ) {
				return false;
			} elseif ( function_exists( 'is_product_taxonomy' ) ) {
				if ( is_product_taxonomy() ) {
					return true;
				}
			}
		}
	}
}

/**
 * Add off canvas filter button.
 *
 * @since 7.1.2
 */

// if ( class_exists( 'WooCommerce' ) && true === get_theme_mod( 'reign_woo_off_canvas_filter', false ) ) {
// add_action( 'woocommerce_before_shop_loop', 'off_canvas_filter_button', 29 );
// }

if ( ! function_exists( 'off_canvas_filter_button' ) ) {
	if ( class_exists( 'WooCommerce' ) ) {
		function off_canvas_filter_button() {

			// Return if is not in shop page.
			if ( ! reign_is_woo_shop()
				&& ! reign_is_woo_tax() ) {
				return;
			}

			// Get filter text.
			$text = get_theme_mod( 'reign_woo_off_canvas_filter_text' );
			$text = $text ? $text : esc_html__( 'Filter', 'reign' );

			$output = '<a href="#" class="reign-woo-canvas-filter"><i class="far fa-filter" aria-hidden="true"></i><span class="off-canvas-filter-text">' . esc_html( $text ) . '</span></a>';

			echo apply_filters( 'reign_off_canvas_filter_button_output', $output ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Need to output HTML.
		}
	}
}

if ( ! function_exists( 'reign_has_woo_filter_button' ) ) {
	if ( class_exists( 'WooCommerce' ) ) {
		function reign_has_woo_filter_button() {
			if ( reign_is_truthy( get_theme_mod( 'reign_woo_off_canvas_filter', false ) ) ) {
				return true;
			} else {
				return false;
			}
		}
	}
}

/**
 * Show WooCommerce Filter In Shop Page
 *
 * @since 7.1.2
 */
if ( ! function_exists( 'reign_filters_widget_side' ) ) {

	function reign_filters_widget_side() {
		$text = get_theme_mod( 'reign_woo_off_canvas_filter_text' );
		$text = $text ? $text : esc_html__( 'Filter', 'reign' );
		?>
		<div class="reign-filter-widget-side">
			<div class="widget-heading">
				<h3 class="widget-title"><?php echo esc_html( $text ); ?></h3>
				<a href="#" class="widget-close"><?php esc_html_e( 'Close', 'reign' ); ?></a>
			</div>
			<div class="reign-module-filter">
				<div class="woocommerce">
					<div class="reign-woo-filter">
						<aside id="secondary" class="woo-off-canvas-sidebar">
							<?php dynamic_sidebar( 'reign_off_canvas_sidebar' ); ?>
						</aside>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	// Hook into 'wp' so the query is fully ready.
	add_action( 'wp', function() {
		if (
			class_exists( 'WooCommerce' ) &&
			( is_shop() || is_product_taxonomy() ) &&
			reign_is_truthy( get_theme_mod( 'reign_woo_off_canvas_filter', false ) )
		) {
			add_action( 'reign_before_page', 'reign_filters_widget_side', 9 );
		}
	});
}

/**
 * WooCommerce Filter Close
 *
 * @since 7.1.2
 */
if ( ! function_exists( 'reign_filters_widget_close_side' ) ) {
	function reign_filters_widget_close_side() {

		?>
		<div class="reign-woo-filter-close"></div>
		<?php
	}

	if ( class_exists( 'WooCommerce' ) && reign_is_truthy( get_theme_mod( 'reign_woo_off_canvas_filter', false ) ) ) {
		add_action( 'reign_footer', 'reign_filters_widget_close_side' );
	}
}

/**
 * Product Thumbnail Hover Effect
 *
 * @since 7.1.2
 */
if ( ! function_exists( 'rg_woocommerce_before_shop_loop_item_title' ) ) {
	if ( class_exists( 'WooCommerce' ) && reign_is_truthy( get_theme_mod( 'reign_woo_product_thumbnail_hover_effect', true ) ) ) {

		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );

		function rg_woocommerce_before_shop_loop_item_title() {
			echo rg_woocommerce_get_product_thumbnail(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		add_action( 'woocommerce_before_shop_loop_item_title', 'rg_woocommerce_before_shop_loop_item_title', 10 );
	}
}

if ( ! function_exists( 'rg_woocommerce_get_product_thumbnail' ) ) {

	if ( class_exists( 'WooCommerce' ) && reign_is_truthy( get_theme_mod( 'reign_woo_product_thumbnail_hover_effect', true ) ) ) {
		function rg_woocommerce_get_product_thumbnail( $size = 'woocommerce_thumbnail', $placeholder_width = 0, $placeholder_height = 0 ) {

			global $post, $product, $woocommerce;
			$image_ids = $product->get_gallery_image_ids();

			$output = '';

			if ( has_post_thumbnail() ) {

				$output .= '<div class="rg-product-images">';
				$output .= '<div class="primary-img">' . get_the_post_thumbnail( $post->ID, $size ) . '</div>';

				if ( ! empty( $image_ids ) ) {
					$secondary_image_id = $image_ids['0'];
					$output            .= '<div class="secondary-img">' . wp_get_attachment_image( $secondary_image_id, $size ) . '</div>';
				}

				$output .= '</div>';
			} elseif ( wc_placeholder_img_src() ) {

				$output .= '<div class="rg-product-images">';
				$output .= wc_placeholder_img( $size );
				$output .= '</div>';
			}

			return $output;
		}
	}
}

/**
 * Register WooCommerce Layout Shortcode and Layout Modifiers
 * @since 7.7.6
 */

// Define reusable layout mapping.
function reign_get_woo_layout_map() {
	return [
		'default' => 'woo_product_default',
		'one'     => 'woo_product_layout1',
		'two'     => 'woo_product_layout2',
		'three'   => 'woo_product_layout3',
		'four'    => 'woo_product_layout4',
	];
}

/**
 * Shortcode: [reign_woo_product_layout layout="three" limit="8" columns="4" category=""]
 */
add_shortcode( 'reign_woo_product_layout', 'reign_woo_product_layout_shortcode' );

function reign_woo_product_layout_shortcode( $atts ) {
	global $reign_shortcode_layout_page_ids;

	$atts = shortcode_atts(
		[
			'layout'   => 'default',
			'limit'    => 8,
			'columns'  => 4,
			'category' => '',
		],
		$atts,
		'reign_woo_product_layout'
	);

	$layout_map = reign_get_woo_layout_map();

	$layout_key      = strtolower( sanitize_key( $atts['layout'] ) );
	$selected_layout = $layout_map[ $layout_key ] ?? 'woo_product_default';

	// Track this page for layout override
	if ( is_singular() ) {
		if ( ! isset( $reign_shortcode_layout_page_ids ) ) {
			$reign_shortcode_layout_page_ids = [];
		}
		$reign_shortcode_layout_page_ids[ get_the_ID() ] = $layout_key;
	}

	// Override layout for shortcode rendering
	if ( 'woo_product_default' !== $selected_layout ) {
		add_filter( 'theme_mod_reign_woo_product_layout', fn() => $selected_layout );
	}

	// Set custom columns via WooCommerce filter
	add_filter( 'loop_shop_columns', function () use ( $atts ) {
		return absint( $atts['columns'] );
	}, 99 );

	ob_start();

	$args = [
		'post_type'           => 'product',
		'post_status'         => 'publish',
		'posts_per_page'      => absint( $atts['limit'] ),
		'ignore_sticky_posts' => true,
		'meta_query'          => WC()->query->get_meta_query(),
		'tax_query'           => WC()->query->get_tax_query(),
	];

	if ( ! empty( $atts['category'] ) ) {
		$args['product_cat'] = sanitize_text_field( $atts['category'] );
	}

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		woocommerce_product_loop_start();

		while ( $query->have_posts() ) {
			$query->the_post();
			wc_get_template_part( 'content', 'product' );
		}

		woocommerce_product_loop_end();
	} else {
		do_action( 'woocommerce_no_products_found' );
	}

	wp_reset_postdata();

	// Cleanup filters after render
	remove_all_filters( 'theme_mod_reign_woo_product_layout' );
	remove_all_filters( 'loop_shop_columns' );

	return ob_get_clean();
}

/**
 * Parse layout from shortcode on singular page load
 */
add_action( 'wp', function () {
	global $reign_shortcode_layout_page_ids;

	if ( is_singular() ) {
		$post = get_post();
		if ( $post instanceof WP_Post && has_shortcode( $post->post_content, 'reign_woo_product_layout' ) ) {
			preg_match( '/\[reign_woo_product_layout[^\]]*layout="([^"]+)"/', $post->post_content, $match );
			$layout = $match[1] ?? 'default';

			if ( ! isset( $reign_shortcode_layout_page_ids ) ) {
				$reign_shortcode_layout_page_ids = [];
			}
			$reign_shortcode_layout_page_ids[ $post->ID ] = sanitize_key( $layout );
		}
	}
});

/**
 * Append Woo layout class to body
 */
add_filter( 'body_class', 'reign_add_woo_layout_to_body_class_with_shortcode_override', 20 );
function reign_add_woo_layout_to_body_class_with_shortcode_override( $classes ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return $classes;
	}

	global $post, $reign_shortcode_layout_page_ids;

	$layout_map         = reign_get_woo_layout_map();
	$all_layout_classes = array_map( fn( $v ) => str_replace( '_', '-', $v ), array_values( $layout_map ) );

	$has_shortcode = false;

	if ( is_singular() && isset( $post->ID ) ) {
		$post_content = get_post_field( 'post_content', $post->ID );

		if ( has_shortcode( $post_content, 'reign_woo_product_layout' ) ) {
			$has_shortcode = true;
		}
	}

	// Check if shortcode layout is set via global variable
	if ( is_singular() && isset( $reign_shortcode_layout_page_ids[ get_the_ID() ] ) ) {
		$layout_key   = $reign_shortcode_layout_page_ids[ get_the_ID() ];
		$layout_class = str_replace( '_', '-', $layout_map[ $layout_key ] ?? 'woo_product_default' );
		$classes      = array_diff( $classes, $all_layout_classes );
		$classes[]    = $layout_class;

	} elseif ( $has_shortcode ) {
		// Fallback if shortcode is present but no specific layout set
		$layout = get_theme_mod( 'reign_woo_product_layout', 'woo_product_default' );
		$layout = str_replace( '_', '-', $layout );
		$classes = array_diff( $classes, $all_layout_classes );
		$classes[] = $layout;
	}

	if ( $has_shortcode && ! in_array( 'woocommerce', $classes, true ) ) {
		$classes[] = 'woocommerce';
	}

	return $classes;
}

/**
 * Inject layout-specific WooCommerce markup hooks
 */
add_action( 'wp', 'reign_apply_dynamic_woo_layout' );
function reign_apply_dynamic_woo_layout() {
	if ( ! class_exists( 'WooCommerce' ) ) return;

	global $reign_shortcode_layout_page_ids;
	$layout_map = reign_get_woo_layout_map();

	$layout = get_theme_mod( 'reign_woo_product_layout', 'woo_product_default' );
	if ( is_singular() && isset( $reign_shortcode_layout_page_ids[ get_the_ID() ] ) ) {
		$layout_key = $reign_shortcode_layout_page_ids[ get_the_ID() ];
		$layout     = $layout_map[ $layout_key ] ?? 'woo_product_default';
	}

	if ( in_array( $layout, [ 'woo_product_layout2', 'woo_product_layout3', 'woo_product_layout4' ], true ) ) {
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 24 );
		add_action( 'woocommerce_shop_loop_item_title', fn() => print '<div class="reign-woo-summary-wrap">', 9 );
		add_action( 'woocommerce_after_shop_loop_item', fn() => print '</div>', 25 );
		add_action( 'woocommerce_after_shop_loop_item_title', 'reign_wrapper_before_add_to_cart', 15 );
		add_action( 'woocommerce_after_shop_loop_item', 'reign_wrapper_after_add_to_cart', 15 );
	}

	if ( $layout === 'woo_product_layout2' ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'reign_wrapper_before_add_to_cart', 15 );
		remove_action( 'woocommerce_after_shop_loop_item', 'reign_wrapper_after_add_to_cart', 15 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );
		add_action( 'woocommerce_before_shop_loop_item', fn() => print '<div class="loop-image-wrap">', 9 );
		add_action( 'woocommerce_before_shop_loop_item_title', fn() => print '</div>', 11 );
		add_action( 'woocommerce_before_shop_loop_item_title', function () {
			reign_wrap_loop_button_start();
			woocommerce_template_loop_add_to_cart();
			print '</div>';
		});
	}

	if ( $layout === 'woo_product_layout4' ) {
		add_action( 'woocommerce_shop_loop_item_title', 'reign_wrapper_before_title', 9 );
		add_action( 'woocommerce_after_shop_loop_item_title', 'reign_wrapper_after_price', 10 );
		suppress_layout4_wrappers();
	}
}

function reign_wrapper_before_add_to_cart() {
	echo '<div class="reign-woo-button-wrap">';
}

function reign_wrapper_after_add_to_cart() {
	echo '</div>';
}

function reign_wrap_loop_button_start() {
	$classes = apply_filters( 'reign_loop_button_wrap_classes', [ 'reign-woo-button-wrap' ] );
	echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
}

function reign_wrapper_before_title() {
	echo '<div class="reign-woo-summary-inner-wrap">';
}

function reign_wrapper_after_price() {
	echo '</div>';
}

function suppress_layout4_wrappers() {
	remove_action( 'woocommerce_after_shop_loop_item_title', 'reign_wrapper_after_price', 10 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'reign_wrapper_before_add_to_cart', 15 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
	add_action( 'woocommerce_after_shop_loop_item', 'reign_wrapper_before_add_to_cart_layout_4', 15 );
}

function reign_wrapper_before_add_to_cart_layout_4() {
	echo '<div class="reign-woo-button-wrap">';
	woocommerce_template_loop_add_to_cart();
	echo '</div>';
}

/**
 * Product archive layouts switcher
 *
 * @since 7.5.2
 */

/**
 * Get default view for product catalog
 *
 * @return string
 */
function reign_get_default_catalog_view_mod() {

	$default = 'grid-four';

	$use_cookies = true;
	if ( is_customize_preview() ) {
		$use_cookies = false;
	}

	$reign_woo_layout_view_buttons = get_theme_mod( 'reign_woo_layout_view_buttons', true );

	if ( reign_is_truthy( $reign_woo_layout_view_buttons ) ) {
		$use_cookies = false;
	}

	if ( $use_cookies ) { // Do not use cookie in customize.
		$cookie_mod = ! empty( $_COOKIE['reign_wc_pl_view_mod'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['reign_wc_pl_view_mod'] ) ) : false;
		if ( $cookie_mod ) {
			if ( 'grid-four' == $cookie_mod ) {
				$default = $cookie_mod;
			}
		}
	}

	if ( ! $default ) {
		$default = 'grid-four';
	}

	return apply_filters( 'reign_get_default_catalog_view_mod', $default );
}

add_action( 'woocommerce_before_shop_loop', 'reign_wc_catalog_view_mod', 29 );
/**
 * Display switcher mod view
 *
 * @return string
 */
function reign_wc_catalog_view_mod() {

	$reign_woo_layout_view_buttons = get_theme_mod( 'reign_woo_layout_view_buttons', true );

	if ( ! reign_is_truthy( $reign_woo_layout_view_buttons ) ) {
		return '';
	}

	if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() ) {

		$default = reign_get_default_catalog_view_mod();
		$columns = intval( wc_get_loop_prop( 'columns', 4 ) );
		?>
		<div class="rg-wc-view-switcher">
			<a class="wc-view-mod one <?php echo ( 'grid-one' == $default ) ? 'active' : ''; ?>" href="#" data-mod="1">
				<svg width="6" height="16" viewBox="0 0 6 16" fill="none" xmlns="http://www.w3.org/2000/svg"> <rect width="2" height="16" rx="1" fill="#dddddd"></rect> </svg>
			</a>
			<a class="wc-view-mod two <?php echo ( 'grid-two' == $default ) ? 'active' : ''; ?>" href="#" data-mod="2">
				<svg width="6" height="16" viewBox="0 0 6 16" fill="none" xmlns="http://www.w3.org/2000/svg"> <rect width="2" height="16" rx="1" fill="#dddddd"></rect> <rect x="4" width="2" height="16" rx="1" fill="#dddddd"></rect> </svg>
			</a>
			<a class="wc-view-mod three <?php echo ( 'grid-three' == $default ) ? 'active' : ''; ?>" href="#" data-mod="3">
				<svg width="10" height="16" viewBox="0 0 10 16" fill="none" xmlns="http://www.w3.org/2000/svg"> <rect width="2" height="16" rx="1" fill="#dddddd"></rect> <rect x="4" width="2" height="16" rx="1" fill="#dddddd"></rect> <rect x="8" width="2" height="16" rx="1" fill="#dddddd"></rect> </svg>
			</a>
			<a class="wc-view-mod four <?php echo ( 'grid-four' == $default ) ? 'active' : ''; ?>" href="#" data-mod="4">
				<svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg"> <rect width="2" height="16" rx="1" fill="#dddddd"></rect> <rect x="4" width="2" height="16" rx="1" fill="#dddddd"></rect> <rect x="8" width="2" height="16" rx="1" fill="#dddddd"></rect> <rect x="12" width="2" height="16" rx="1" fill="#dddddd"></rect> </svg>
			</a>
			<?php if ( 5 === $columns ) : ?>
				<a class="wc-view-mod five <?php echo ( 'grid-five' == $default ) ? 'active' : ''; ?>" href="#" data-mod="5">
					<svg width="14" height="16" viewBox="0 0 14 16" fill="none" xmlns="http://www.w3.org/2000/svg"> <rect width="2" height="16" rx="1" fill="#dddddd"></rect> <rect x="4" width="2" height="16" rx="1" fill="#dddddd"></rect> <rect x="8" width="2" height="16" rx="1" fill="#dddddd"></rect> <rect x="12" width="2" height="16" rx="1" fill="#dddddd"></rect> <rect x="12" width="2" height="16" rx="1" fill="#dddddd"></rect> </svg>
				</a>
			<?php endif; ?>
		</div>
		<?php
	}
}

if ( class_exists( 'WooCommerce' ) && reign_is_truthy( get_theme_mod( 'reign_woo_off_canvas_filter', false ) ) ) {
	add_action( 'woocommerce_before_shop_loop', 'off_canvas_filter_button', 29 );
}

if ( ! function_exists( 'rg_woocommerce_loader' ) ) {

	$reign_woo_layout_view_buttons = get_theme_mod( 'reign_woo_layout_view_buttons', true );

	/**
	 * Add WooCommerce shop page loader.
	 */
	function rg_woocommerce_loader() {
		if ( is_customize_preview() ) {
			return;
		}
		?>
		<div class="rg-woocommerce-loading-overlay">
			<div class="spinner"></div>
		</div>
		<?php
	}

	if ( reign_is_truthy( $reign_woo_layout_view_buttons ) ) {
		add_action( 'woocommerce_before_shop_loop', 'rg_woocommerce_loader', 30 );
	}
}

/**
 * Identify products with manage stock and only one instock
 * This is needed because since WooCommerce 7.4.0 the quantity input is automatically hidden when the product has only one instock or is defined to "Limit purchases to 1 item per order"
 */
function reign_woocommerce_post_class( $classes, $product ) {

	if ( ! empty( $product->get_gallery_image_ids() ) ) {
		$classes[] = 'has-gallery-images';
	}

	return $classes;
}
add_filter( 'woocommerce_post_class', 'reign_woocommerce_post_class', 10, 2 );

/**
 * Single product top area wrapper
 */
function reign_single_product_wrap_before() {
	$classes = array( 'product-gallery-summary' );

	// Gallery layout.
	$reign_woo_single_product_image = get_theme_mod( 'reign_woo_single_product_image', 'product_image_layout1' );
	if ( 'product_image_layout2' === $reign_woo_single_product_image ) {
		$classes[] = 'gallery-vertical';
	} else {
		$classes[] = 'gallery-default';
	}

	// Thumbs slider.
	$classes[] = 'has-thumbs-slider';

	// Output.
	echo '<div class="' . esc_attr( implode( ' ', $classes ) ) . '">';
}
add_action( 'woocommerce_before_single_product_summary', 'reign_single_product_wrap_before', -99 );

/**
 * Single product top area wrapper
 */
function reign_single_product_wrap_after() {
	echo '</div>';
}
add_action( 'woocommerce_after_single_product_summary', 'reign_single_product_wrap_after', 9 );


/**
 * Single product review tab — gated by WC presence so the hooks
 * are not registered against filters WC has not declared yet (which
 * would silently no-op on activation order race conditions).
 */
if ( class_exists( 'WooCommerce' ) ) {
	$reign_woo_review_position = get_theme_mod( 'reign_woo_review_position', 'inside' );

	if ( 'outside' === $reign_woo_review_position ) {
		add_filter( 'woocommerce_product_tabs', 'reign_remove_reviews_tab', 98 );
		add_action( 'woocommerce_after_single_product_summary', 'reign_show_reviews', 14 );
	}
}

/**
 * Function to remove the default Reviews tab
 *
 * @param array $tabs The existing tabs array.
 */
function reign_remove_reviews_tab( $tabs ) {
	unset( $tabs['reviews'] );
	return $tabs;
}
/**
 * Function to display the reviews section after the single product summary
 */
function reign_show_reviews() {
	comments_template();
}

if ( ! function_exists( 'reign_reviews_summary_bar' ) ) {
	/**
	 * Custom function to display reviews summary bar.
	 */
	function reign_reviews_summary_bar() {
		global $product;
		$reign_woo_summary_bar = get_theme_mod( 'reign_woo_summary_bar', 'on' );

		// Get the total number of ratings.
		$rating_count   = $product->get_rating_count();
		$average_rating = $product->get_average_rating();
		$rating_counts  = $product->get_rating_counts();
		$total_ratings  = array_sum( $rating_counts );

		// Hide summary bar if there are no ratings or the toggle is disabled.
		// Strict `false === ...` silently failed for saved 'off' / '0' values
		// (they are not === false); reign_is_truthy() normalises every shape.
		if ( 0 === $total_ratings || ! reign_is_truthy( $reign_woo_summary_bar ) ) {
			return;
		}

		?>
		<div class="reign-summary-bar-wrapper">
			<?php
			// Display stars for the average rating.
			echo '<div class="summary-bar-left">';
			echo '<span class="average-rating-value"><strong>' . esc_html( $average_rating ) . '</strong></span>';
			echo '<span class="average-rating">';
			for ( $i = 1; $i <= 5; $i++ ) {
				// Add a CSS class to color the stars based on the review count.
				$star_class = $i <= round( $average_rating ) ? 'star-filled' : 'star-empty';
				echo '<i class="fas fa-star ' . esc_html( $star_class ) . '"></i>';
			}
			echo '</span>';
			// translators: %s is the number of reviews.
			echo '<span class="rating-count">' . sprintf( esc_attr( _n( 'Based on %s review', 'Based on %s reviews', $rating_count, 'reign' ) ), esc_attr( $rating_count ) ) . '</span>';
			echo '</div>';

			if ( $total_ratings > 0 ) {
				echo '<div class="summary-bar-right">';
				// Loop through each rating value (1 to 5) and display a bar for each.
				for ( $i = 5; $i >= 1; $i-- ) {
					$rating_count = isset( $rating_counts[ $i ] ) ? $rating_counts[ $i ] : 0;
					$percentage   = ( $rating_count / $total_ratings ) * 100;

					// Output the rating bar with all five stars and corresponding percentage.
					echo '<div class="rating-bar">';
					echo '<span class="rating-label">';
					for ( $j = 1; $j <= 5; $j++ ) {
						// Add a CSS class to color the stars based on the review count.
						$star_class = $j <= $i ? 'star-filled' : 'star-empty';
						echo '<i class="fas fa-star ' . esc_html( $star_class ) . '"></i>';
					}
					echo '</span>';
					echo '<div class="bar-container">';
					echo '<div class="bar" style="width: ' . esc_attr( $percentage ) . '%;"></div>';
					echo '</div>';
					// Translators: %s is a placeholder for the rating count.
					echo '<span class="rating-count">' . sprintf( esc_html( _n( '(%s)', '(%s)', $rating_count, 'reign' ) ), esc_html( $rating_count ) ) . '</span>';
					echo '</div>';
				}
				echo '</div>';
			}
			?>
		</div>
		<?php
	}

	// Hook the custom function into the desired position.
	add_action( 'woocommerce_after_single_product_summary', 'reign_reviews_summary_bar', 13 );
}

/**
 * Cart page.
 *
 * @since 7.5.2
 */
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display', 10 );
