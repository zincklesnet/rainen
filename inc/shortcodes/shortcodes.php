<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Shortcodes' ) ) :

	/**
	 * Includes settings to display PeepSo WooCommerce Integration settings tab
	 *
	 * @class Reign_Shortcodes
	 */
	class Reign_Shortcodes {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Shortcodes
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Shortcodes Instance.
		 *
		 * Ensures only one instance of Reign_Shortcodes is loaded or can be loaded.
		 *
		 * @return Reign_Shortcodes - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Shortcodes Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_shortcode( 'reign_display_posts', array( $this, 'reign_get_posts' ) );
		}

		/**
		 * Retrieves and displays posts based on the provided attributes.
		 *
		 * @param array $atts Shortcode attributes passed to the function.
		 * @return string HTML content to display posts.
		 */
		public function reign_get_posts( $atts ) {
			global $blog_list_layout, $post;

			$_blog_list_layout  = get_theme_mod( 'reign_blog_list_layout', 'default-view' );
			$reign_blog_per_row = get_theme_mod( 'reign_blog_per_row', '3' );

			// Add 'column_count' to shortcode attributes with a default of 3 columns.
			// posts_per_page defaults to the site-wide setting (not -1) to avoid
			// unbounded queries on large blogs. Authors can override per shortcode.
			$atts = shortcode_atts(
				array(
					'category'       => array(),
					'posts_view'     => $_blog_list_layout,
					'posts_per_page' => (int) get_option( 'posts_per_page', 10 ),
					'column_count'   => $reign_blog_per_row, // Default column count.
				),
				$atts,
				'reign_display_posts'
			);

			// Sanitize the category input.
			$category = ! empty( $atts['category'] ) ? array_map( 'intval', explode( ',', $atts['category'] ) ) : array();

			$blog_list_layout = $atts['posts_view'];

			// Prepare query arguments.
			$paged = get_query_var( 'paged', 1 );
			$args  = array(
				'posts_per_page' => intval( $atts['posts_per_page'] ),
				'cat'            => $category,
				'paged'          => $paged, // Ensure pagination is respected.
			);
			$args = apply_filters( 'alter_reign_display_posts_args', $args );

			// Save original global objects
			$original_wp_query = $GLOBALS['wp_query'];
			$original_post     = $post;

			// Run the query without affecting the global $wp_query
			$query            = new WP_Query( $args );
			$GLOBALS['wp_query'] = $query;

			ob_start();

			// Start the layout wrapper.
			echo '<div class="reign-blog-shortcode">';

			if ( $blog_list_layout == 'masonry-view' ) {
				echo '<div class="masonry wb-post-listing col-' . esc_attr( $atts['column_count'] ) . '">';
				echo '<div class="reign-grid-sizer"></div>';
			} elseif ( $blog_list_layout == 'wb-grid-view' ) {
				echo '<div class="wb-grid-view-wrap wb-post-listing col-' . esc_attr( $atts['column_count'] ) . '">';
			} else {
				echo '<div class="wb-lists-view-wrap wb-post-listing">';
			}

			// Check if the query has posts.
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					get_template_part( 'template-parts/content', get_post_format() );
				}

				// Custom post navigation.
				reign_custom_post_navigation();

				// Reset post data after custom query.
				wp_reset_postdata();
			} else {
				// No posts found message.
				echo '<p>' . esc_html__( 'No posts found.', 'reign' ) . '</p>';
			}

			// Close the layout wrapper.
			echo '</div></div>';

			// Modify comments display if on a singular post type.
			if ( is_singular() && post_type_supports( get_post_type(), 'comments' ) ) {
				add_filter( 'comments_open', '__return_false' );
				add_filter( 'comments_array', '__return_empty_array' );
			}

			$output = ob_get_clean();

			// Restore globals exactly
			$GLOBALS['wp_query'] = $original_wp_query;
			$post                 = $original_post;
			setup_postdata( $post );

			return $output;
		}
	}

	endif;

/**
 * Main instance of Reign_Shortcodes.
 *
 * @return Reign_Shortcodes
 */
Reign_Shortcodes::instance();
