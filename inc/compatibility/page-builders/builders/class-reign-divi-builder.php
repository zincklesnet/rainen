<?php
/**
 * Divi Builder Compatibility Class
 *
 * @package Reign
 * @subpackage Page Builder Compatibility
 * @since 1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Divi Builder compatibility class
 */
class Reign_Divi_Builder extends Reign_Page_Builder_Base {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->name = 'Divi Builder';
		$this->slug = 'divi-builder';
		
		parent::__construct();
	}

	/**
	 * Check if Divi Builder is active
	 *
	 * @return bool
	 */
	public function is_builder_active() {
		return class_exists( 'ET_Builder_Plugin' ) || function_exists( 'et_divi_load_scripts_styles' );
	}

	/**
	 * Check if Divi Builder is used on a post
	 *
	 * @param int $post_id Post ID
	 * @return bool
	 */
	public function is_builder_used( $post_id = null ) {
		if ( ! function_exists( 'et_pb_is_pagebuilder_used' ) ) {
			return false;
		}

		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		return et_pb_is_pagebuilder_used( $post_id );
	}

	/**
	 * Initialize Divi-specific features
	 */
	protected function init() {
		// Call common initialization
		parent::common_init();
		
		// Divi-specific hooks
		add_filter( 'et_builder_post_types', array( $this, 'add_divi_support_for_cpts' ) );
		add_action( 'template_redirect', array( $this, 'setup_divi_frontend_builder' ) );

		// Hook to run after the theme header is displayed in Divi Theme Builder.
		add_action( 'et_theme_builder_template_after_header', array( $this, 'reign_divi_builder_after_header' ) );

		// Hook to run before the theme footer is displayed in Divi Theme Builder.
		add_action( 'et_theme_builder_template_before_footer', array( $this, 'reign_divi_builder_before_footer' ) );

		// Register custom templates only if Divi is active
		if ( $this->is_builder_active() ) {
			add_filter( 'theme_page_templates', array( $this, 'register_divi_templates' ), 10, 4 );
			add_filter( 'template_include', array( $this, 'load_divi_templates' ) );
		}
	}

	/**
	 * Register Divi page templates conditionally
	 */
	public function register_divi_templates( $post_templates, $wp_theme, $post, $post_type ) {
		$post_templates['page-divi-builder.php']   = __( 'Divi Builder Template', 'reign' );
		$post_templates['single-divi-builder.php'] = __( 'Divi Builder Post Template', 'reign' );
		return $post_templates;
	}

	/**
	 * Load template file if selected
	 */
	public function load_divi_templates( $template ) {
		if ( is_page() ) {
			$page_template = get_page_template_slug( get_queried_object_id() );

			if ( $page_template === 'page-divi-builder.php' ) {
				$template = get_stylesheet_directory() . '/page-divi-builder.php';
			}

			if ( $page_template === 'single-divi-builder.php' ) {
				$template = get_stylesheet_directory() . '/single-divi-builder.php';
			}
		}

		return $template;
	}

	/**
	 * Check if in Divi Builder preview mode
	 *
	 * @return bool
	 */
	public function is_builder_preview() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only page-builder editor-mode detection.
		return isset( $_GET['et_fb'] ) && '1' === sanitize_key( wp_unslash( $_GET['et_fb'] ) );
	}

	/**
	 * Add Divi support for custom post types
	 *
	 * @param array $post_types Post types
	 * @return array
	 */
	public function add_divi_support_for_cpts( $post_types ) {
		// Add common custom post types
		$additional_cpts = array(
			'product',          // WooCommerce
			'download',         // Easy Digital Downloads
			'sfwd-courses',     // LearnDash
			'llms_course',      // LifterLMS
			'job_listing',      // WP Job Manager
			'company',          // Custom CPT
		);

		foreach ( $additional_cpts as $cpt ) {
			if ( post_type_exists( $cpt ) && ! in_array( $cpt, $post_types ) ) {
				$post_types[] = $cpt;
			}
		}

		return $post_types;
	}

	/**
	 * Setup for Divi frontend builder
	 */
	public function setup_divi_frontend_builder() {
		// Check if we're in Divi Builder mode
		if ( $this->is_builder_preview() ) {
			// Remove any theme elements that might interfere
			add_filter( 'reign_display_header', '__return_false' );
			add_filter( 'reign_display_footer', '__return_false' );
			
			// Ensure full-width layout
			add_filter( 'body_class', function( $classes ) {
				$classes[] = 'et-fb-enabled';
				$classes[] = 'divi-frontend-builder-active';
				return $classes;
			} );
		} 

		if ( function_exists( 'et_pb_is_pagebuilder_used' ) && et_pb_is_pagebuilder_used( get_the_ID() ) && ! ET_GB_Block_Layout::is_layout_block_preview() ) {
			add_filter( 'body_class', function( $classes ) {
				$classes[] = 'et_pb_pagebuilder_layout';
				return $classes;
			} );
		}
	}

	/**
	 * Adds custom HTML structure after the header in Divi Theme Builder.
	 * This ensures that the theme page container is included when Theme Builder is active.
	 */
	public function reign_divi_builder_after_header() {
		?>
		<div id="content" class="site-content">
			<div class="container">
				<div class="wb-grid site-content-grid">
		<?php
	}

	/**
	 * Closes the custom HTML structure before the footer in Divi Theme Builder.
	 * This ensures that the theme page container is properly closed when Theme Builder is active.
	 */
	public function reign_divi_builder_before_footer() {
		?>
				</div><!-- .wb-grid -->
			</div><!-- .container -->
		</div><!-- #content -->
		<?php
	}
}
