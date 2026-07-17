<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'RTM_EDD_Customization' ) ) :

	/**
	 * @class RTM_EDD_Customization
	 */
	class RTM_EDD_Customization {

		/**
		 * The single instance of the class.
		 *
		 * @var RTM_EDD_Customization
		 */
		protected static $_instance = null;

		/**
		 * Main RTM_EDD_Customization Instance.
		 *
		 * Ensures only one instance of RTM_EDD_Customization is loaded or can be loaded.
		 *
		 * @return RTM_EDD_Customization - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * RTM_EDD_Customization Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {

			add_filter( 'reign_set_sidebar_id', array( $this, 'set_edd_downloads_archive_sidebar' ) );

			add_action( 'edd_product_details_widget_before_purchase_button', array( $this, 'rtm_show_price_on_product_details_widget' ), 10, 2 );

			add_action( 'edd_product_details_widget_before_categories_and_tags', array( $this, 'render_categories_and_tags' ), 10, 2 );

			add_action( 'init', array( $this, 'add_panels_and_sections' ) );
			add_action( 'init', array( $this, 'add_fields' ) );

			add_action( 'wp_head', array( $this, 'custom_edd_css' ) );

			if ( class_exists( 'EDD_Cross_Sell_And_Upsell' ) ) {
				add_filter( 'the_content', array( $this, 'rtm_edd_csau_single_download_upsells' ), 99, 1 );
				add_action( 'edd_after_checkout_cart', array( $this, 'rtm_edd_csau_display_on_checkout_page' ), 9 );
			}

			add_filter( 'edd_downloads_list_wrapper_class', array( $this, 'alter_edd_downloads_list_wrapper_class' ), 10, 2 );

			add_filter( 'reign_customizer_supported_post_types', array( $this, 'add_post_type' ), 10, 1 );
		}

		/**
		 * Adds a custom post type to the list of post types.
		 *
		 * This function appends the 'download' post type to the provided array of post types.
		 * It includes a slug and a localized name for the 'download' post type.
		 *
		 * @param array $post_types An array of existing post types.
		 * @return array The modified array of post types, including the 'download' post type.
		 */
		public function add_post_type( $post_types ) {
			$post_types[] = array(
				'slug' => 'download',
				'name' => __( 'Download', 'reign' ),
			);
			return $post_types;
		}

		/**
		 * Add panels and sections
		 */
		public function add_panels_and_sections() {
			\Reign\Customizer_Framework\Section::add(
				'reign_edd_support',
				array(
					'title'       => esc_html__( 'Easy Digital Downloads', 'reign' ),
					'priority'    => 10,
					'panel'       => 'reign_plugin_support_panel',
					'description' => '',
				)
			);
		}

		/**
		 * Add fields
		 */
		public function add_fields() {

			\Reign\Customizer_Framework\Field::add(
				'select',
				array(
					'settings'    => 'reign_edd_downloads_layouts',
					'label'       => esc_html__( 'Archive Downloads Layout', 'reign' ),
					'description' => esc_html__( 'This setting helps you manage the layout of download listings on the download archive page.', 'reign' ),
					'section'     => 'reign_edd_support',
					'default'     => 'default',
					'priority'    => 10,
					'choices'     => array(
						'default' => esc_html__( 'Default', 'reign' ),
						'layout1' => esc_html__( 'Layout 1', 'reign' ),
						'layout2' => esc_html__( 'Layout 2', 'reign' ),
						'layout3' => esc_html__( 'Layout 3', 'reign' ),
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'custom',
				array(
					'settings' => 'reign_edd_downloads_layouts_divider',
					'section'  => 'reign_edd_support',
					'choices'  => array(
						'color' => '#dcdcde',
					),
				)
			);

			\Reign\Customizer_Framework\Field::add(
				'slider',
				array(
					'settings'    => 'reign_edd_downloads_per_row',
					'label'       => esc_html__( 'Downloads Per Row', 'reign' ),
					'description' => esc_html__( 'This setting helps you manage the number of download items to show per row on the download archive page.', 'reign' ),
					'section'     => 'reign_edd_support',
					'default'     => 4,
					'priority'    => 10,
					'choices'     => array(
						'min'  => '1',
						'max'  => '5',
						'step' => '1',
					),
				)
			);
		}

		/**
		 * Alters the CSS class applied to the wrapper of the Easy Digital Downloads (EDD) downloads list.
		 *
		 * This function appends the class 'rtm_edd_list' to the existing wrapper classes for EDD downloads list.
		 * It is intended to add additional styling or functionality to the wrapper element.
		 *
		 * @param string $wrapper_classes The current CSS classes applied to the wrapper element.
		 * @param array  $atts            Additional attributes or parameters passed to the function.
		 * @return string The modified list of wrapper CSS classes with 'rtm_edd_list' appended.
		 */
		public function alter_edd_downloads_list_wrapper_class( $wrapper_classes, $atts ) {
			$wrapper_classes .= ' rtm_edd_list';
			return $wrapper_classes;
		}

		/**
		 * Outputs custom CSS for Easy Digital Downloads (EDD) product items.
		 *
		 * Dynamically sets the width of EDD product items based on theme settings.
		 *
		 * @return void
		 */
		public function custom_edd_css() {
			// Get the number of EDD downloads per row; default to 4.
			$rtm_edd_per_row = intval( get_theme_mod( 'reign_edd_downloads_per_row', '4' ) );

			// Determine the width percentage for each download item.
			$width = ( 3 === $rtm_edd_per_row ) ? 33.333333 : floor( 100 / $rtm_edd_per_row );

			// Output the CSS securely.
			echo '<style type="text/css">
					.rtm-download-item-article {
						width: calc(' . esc_attr( $width ) . '% - 30px) !important;
					}
				</style>';
		}

		/**
		 * Renders the categories and tags for an Easy Digital Downloads (EDD) product.
		 *
		 * This function outputs the categories and tags associated with a specific EDD download item.
		 * It checks if the display of categories or tags is enabled and retrieves the corresponding terms.
		 * If either categories or tags are present, it outputs them in a styled div container.
		 *
		 * @param array $instance    An array of settings from the widget instance or shortcode attributes.
		 *                           It includes 'categories' and 'tags' keys to determine if these should be displayed.
		 * @param int   $download_id The ID of the EDD download item for which categories and tags are to be displayed.
		 *
		 * @return void
		 */
		public function render_categories_and_tags( $instance, $download_id ) {
			$categories = ! empty( $instance['categories'] ) ? $instance['categories'] : '';
			$tags       = ! empty( $instance['tags'] ) ? $instance['tags'] : '';

			$category_list = $categories ? get_the_term_list( $download_id, 'download_category', '', ', ' ) : '';
			$tag_list      = $tags ? get_the_term_list( $download_id, 'download_tag', '', ', ' ) : '';

			if ( $category_list || $tag_list ) {
				echo '<div class="rtm-edd-pro-meta">';
				if ( $category_list ) {
					echo "<div class='rtm-edd-cat-list edd_meta'><label>" . esc_html__( 'Category', 'reign' ) . '</label><div>' . wp_kses_post( $category_list ) . '</div></div>';
				}
				if ( $tag_list ) {
					echo "<div class='rtm-edd-tag-list edd_meta'><label>" . esc_html__( 'Tag', 'reign' ) . '</label><div>' . wp_kses_post( $tag_list ) . '</div></div>';
				}
				echo '</div>';
			}
		}

		/**
		 * Displays the price of a product in a widget.
		 *
		 * This function retrieves and outputs the price HTML for a specific Easy Digital Downloads (EDD) product.
		 * The product's ID is used to determine which product's price to display. The price is displayed using
		 * the `rtm_get_edd_download_price_html` method.
		 *
		 * @param array $instance The widget instance settings.
		 * @param int   $download_id The ID of the EDD product whose price is to be displayed.
		 * @return void
		 */
		public function rtm_show_price_on_product_details_widget( $instance, $download_id ) {
			$this->rtm_get_edd_download_price_html();
		}

		/**
		 * Outputs the price HTML for an Easy Digital Downloads (EDD) product.
		 *
		 * This function generates and displays the price information for the current EDD product. It handles
		 * different pricing scenarios including free downloads, variable pricing, and standard pricing. The
		 * function also adds structured data markup for SEO purposes when applicable.
		 *
		 * The function performs the following checks:
		 * - If the download is free, it displays "Free".
		 * - If the download has variable prices, it displays the range of prices.
		 * - If the download has a single price, it displays that price.
		 *
		 * The output is wrapped in HTML with appropriate schema.org attributes for better search engine visibility.
		 *
		 * @return void
		 */
		public function rtm_get_edd_download_price_html() {
			// Determine if structured data should be added.
			$item_props = 'EDD_Structured_Data' ? ' itemprop="offers" itemscope itemtype="http://schema.org/Offer"' : '';

			?>
			<?php
			/**
			 * Free download.
			 */
			if ( edd_is_free_download( get_the_ID() ) ) :
				?>
				<div<?php echo $item_props; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static schema.org microdata attribute string. ?>>
					<div itemprop="price">
						<span class="edd_price" id="edd_price_<?php echo esc_attr( get_the_id() ); ?>">
							<?php esc_html_e( 'Free', 'reign' ); ?>
						</span>
					</div>
				</div>
				<?php
				/**
				 * Variable priced download
				 */
			elseif ( edd_has_variable_prices( get_the_ID() ) ) :
				$variable_prices = edd_get_variable_prices( get_the_ID() );
				$variable_prices = array_map(
					function ( $price ) {
						return $price['amount'];
					},
					$variable_prices
				);
				$variable_prices = array_unique( $variable_prices );
				sort( $variable_prices );
				if ( count( $variable_prices ) > 1 ) {
					$min_price = edd_currency_filter( edd_format_amount( $variable_prices[0] ) );
					$max_price = edd_currency_filter( edd_format_amount( $variable_prices[ count( $variable_prices ) - 1 ] ) );
					?>
				<div<?php echo $item_props; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static schema.org microdata attribute string. ?>>
					<div itemprop="price">
						<span class="edd_price"> 
							<?php echo esc_html( $min_price . ' - ' . $max_price ); ?>
						</span>
					</div>
				</div>
					<?php
				} else {
					?>
					<div<?php echo $item_props; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static schema.org microdata attribute string. ?>>
						<div itemprop="price"> 
							<?php edd_price( get_the_ID() ); ?>
						</div>
					</div>
					<?php
				}
				/**
				 * Normal priced download.
				 */
			elseif ( ! edd_has_variable_prices( get_the_ID() ) ) :
				?>
				<div<?php echo $item_props; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Static schema.org microdata attribute string. ?>>
					<div itemprop="price">
						<?php edd_price( get_the_ID() ); ?>
					</div>
				</div>
			<?php endif; ?>
			<?php
		}

		/**
		 * Sets a specific sidebar ID for Easy Digital Downloads (EDD) archive and single download pages.
		 *
		 * This function modifies the sidebar ID based on the current page type:
		 * - For EDD download archive pages (post type archive 'download'), it returns the sidebar ID 'edd-download-archive-sidebar'.
		 * - For single EDD download pages (singular 'download'), it returns the sidebar ID 'edd-single-download-sidebar'.
		 * If the current page is neither an EDD archive nor a single download page, it returns the original `$sidebar_id` parameter.
		 *
		 * @param string $sidebar_id The default sidebar ID.
		 * @return string The sidebar ID to use, depending on the current page type.
		 */
		public function set_edd_downloads_archive_sidebar( $sidebar_id ) {
			if ( is_post_type_archive( 'download' ) || is_singular( 'download' ) ) {
				return is_post_type_archive( 'download' ) ? 'edd-download-archive-sidebar' : 'edd-single-download-sidebar';
			}
			return $sidebar_id;
		}

		/**
		 * Appends custom upsell content to the EDD single download page.
		 *
		 * This function checks if the current page is a single download page and is the main query.
		 * If both conditions are met, it removes the existing filter for `edd_csau_single_download_upsells`
		 * from the content, generates new HTML content using the `rtm_edd_csau_html()` method, and appends
		 * this content to the original content of the download page.
		 *
		 * This allows for custom upsell content to be dynamically added to the end of the download page
		 * without altering the original content. The function ensures that the custom content is only
		 * added on the singular download pages and avoids applying the filter to other types of queries.
		 *
		 * @param string $content The original content of the single download page.
		 *
		 * @return string The modified content with appended custom upsell HTML if conditions are met,
		 *                 otherwise the original content.
		 */
		public function rtm_edd_csau_single_download_upsells( $content ) {
			if ( is_singular( 'download' ) && is_main_query() ) {
				remove_filter( 'the_content', 'edd_csau_single_download_upsells', 100 );
				$new_content = $this->rtm_edd_csau_html();
				return $content . $new_content;
			}
			return $content;
		}

		/**
		 * Displays custom upsell content on the Easy Digital Downloads (EDD) checkout page.
		 *
		 * This function checks if the current page is the EDD checkout page using `edd_is_checkout()`.
		 * If the condition is met, it removes the existing action for `edd_csau_display_on_checkout_page`
		 * from the `edd_after_checkout_cart` hook to prevent potential conflicts or duplicate output.
		 * Then, it outputs custom HTML content generated by the `rtm_edd_csau_html()` method.
		 *
		 * The `rtm_edd_csau_html()` method should ensure that any dynamic content it returns is properly
		 * escaped to avoid security vulnerabilities, such as Cross-Site Scripting (XSS) attacks.
		 *
		 * @return void
		 */
		public function rtm_edd_csau_display_on_checkout_page() {
			if ( edd_is_checkout() ) {
				remove_action( 'edd_after_checkout_cart', 'edd_csau_display_on_checkout_page' );
				echo wp_kses_post( $this->rtm_edd_csau_html() );
			}
		}

		/**
		 * Outputs upsell or cross-sell products based on the current page context.
		 *
		 * This function displays upsell products on a single download page or cross-sell products
		 * on the Easy Digital Downloads (EDD) checkout page, based on the context of the page.
		 * - On a single download page (`is_singular('download')`), it retrieves and displays upsell products
		 *   related to the current download.
		 * - On the EDD checkout page (`edd_is_checkout()`), it retrieves and displays cross-sell products
		 *   based on the items in the cart.
		 *
		 * @param string|int $columns Number of columns for displaying products. Default is '3'.
		 *
		 * @return string|null The HTML output of the upsell or cross-sell products if available, otherwise null.
		 *
		 * @since 1.0
		 */
		public function rtm_edd_csau_html( $columns = '3' ) {
			global $post, $edd_options;
			// upsell products for the single download page.
			if ( is_singular( 'download' ) ) {
				$products = get_transient( 'rtm_edd_upsell_' . get_the_ID() );
				if ( false === $products ) {
					$products = edd_csau_get_products( get_the_ID(), 'upsell' );
					set_transient( 'rtm_edd_upsell_' . get_the_ID(), $products, HOUR_IN_SECONDS );
				}
			} elseif ( edd_is_checkout() ) { // cross-sell products at checkout.
				$cart_items = edd_get_cart_contents();
				if ( ! $cart_items ) {
					return;
				}
				$product_list = array();
				foreach ( $cart_items as $cart_item ) {
					$product_list[] = get_post_meta( $cart_item['id'], '_edd_csau_cross_sell_products', false );
				}
				$products = array_unique( array_merge( ...array_filter( $product_list ) ) );
				$products = array_diff( $products, array_map( 'edd_item_in_cart', $products ) );
			} else {
				return;
			}
			if ( $products ) {
				?>
				<?php
				if ( edd_is_checkout() ) {
					$posts_per_page = isset( $edd_options['edd_csau_cross_sell_number'] ) && ! empty( $edd_options['edd_csau_cross_sell_number'] ) ? $edd_options['edd_csau_cross_sell_number'] : '3';
				} elseif ( is_singular( 'download' ) ) {
					$posts_per_page = isset( $edd_options['edd_csau_upsell_number'] ) && ! empty( $edd_options['edd_csau_upsell_number'] ) ? $edd_options['edd_csau_upsell_number'] : '3';
				}
				$query     = array(
					'post_type'      => 'download',
					'posts_per_page' => $posts_per_page,
					'orderby'        => 'date',
					'order'          => 'DESC',
					'post__in'       => $products,
				);
				$query     = apply_filters( 'edd_csau_query', $query );
				$downloads = new WP_Query( $query );
				if ( $downloads->have_posts() ) :
					// upsell heading.
					if ( is_singular( 'download' ) ) {
						$upsell_heading = get_post_meta( get_the_ID(), '_edd_csau_upsell_heading', true );
						// show singular heading.
						if ( $upsell_heading ) {
							$heading = esc_attr( $upsell_heading );
						} elseif ( isset( $edd_options['edd_csau_upsell_heading'] ) ) { // show default in settings.
							$heading = esc_attr( $edd_options['edd_csau_upsell_heading'] );
						} else {
							$heading = __( 'You may also like:', 'reign' );
						}
					} elseif ( edd_is_checkout() ) { // cross-sell heading.
						$ids = edd_csau_get_cart_trigger_ids();
						if ( count( $ids ) == 1 && get_post_meta( $ids[0], '_edd_csau_cross_sell_heading', true ) ) {
							$heading = esc_attr( get_post_meta( $ids[0], '_edd_csau_cross_sell_heading', true ) );
						} elseif ( isset( $edd_options['edd_csau_cross_sell_heading'] ) ) { // show default in settings.
							$heading = esc_attr( $edd_options['edd_csau_cross_sell_heading'] );
						} else {
							$heading = __( 'You may also like:', 'reign' );
						}
					} // end is_checkout.
					$i = 1;
					global $wp_query;
					$classes = array();
					$classes = apply_filters( 'edd_csau_classes', $classes );
					// default classes.
					$classes[] = 'edd-csau-products';
					// columns.
					if ( $columns ) {
						$classes[] = 'col-' . $columns;
					}
					// filter array and remove empty values.
					$classes    = array_filter( $classes );
					$classes    = ! empty( $classes ) ? implode( ' ', $classes ) : '';
					$class_list = ! empty( $classes ) ? 'class="' . $classes . '"' : '';
					ob_start();
					?>
					<div <?php echo esc_attr( $class_list ); ?>>
						<?php if ( $heading ) : ?>
							<h2><?php echo esc_html( $heading ); ?></h2>
						<?php endif; ?>
						<?php echo '<div class="rtm_edd_list">'; ?>
						<?php
						while ( $downloads->have_posts() ) :
							$downloads->the_post();
							?>
							<?php edd_get_template_part( 'shortcode', 'download' ); ?>
						<?php endwhile; ?>
						<?php echo '</div>'; ?>
						<?php wp_reset_postdata(); ?>
					</div>
					<?php
					$html = ob_get_clean();
					return apply_filters( 'rtm_edd_csau_html', $html, $downloads, $heading, $columns, $class_list );
				endif;
				?>
				<?php
			}
			?>
			<?php
		}
	}

endif;

/**
 * Main instance of RTM_EDD_Customization.
 *
 * Gated by EDD presence: the class registers EDD-specific filters
 * (edd_downloads_list_wrapper_class etc.) and a wp_head action that
 * emits inline CSS. Without this guard the inline CSS was fired on
 * every front-end page even on non-EDD installs.
 *
 * @return RTM_EDD_Customization
 */
if ( class_exists( 'Easy_Digital_Downloads' ) ) {
	RTM_EDD_Customization::instance();
}
