<?php
/**
 * Visual Composer (WPBakery) Page Builder Compatibility Class
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
 * Visual Composer compatibility class
 */
class Reign_Visual_Composer_Builder extends Reign_Page_Builder_Base {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->name = 'Visual Composer';
		$this->slug = 'visual-composer';
		
		parent::__construct();
	}

	/**
	 * Check if Visual Composer is active
	 *
	 * @return bool
	 */
	public function is_builder_active() {
		// Check for WPBakery Page Builder (formerly Visual Composer)
		return defined( 'WPB_VC_VERSION' ) || class_exists( 'Vc_Manager' );
	}

	/**
	 * Check if Visual Composer is used on a post
	 *
	 * @param int $post_id Post ID
	 * @return bool
	 */
	public function is_builder_used( $post_id = null ) {
		if ( ! $this->is_builder_active() ) {
			return false;
		}

		if ( null === $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return false;
		}

		// Check if Visual Composer is enabled for this post
		$vc_enabled = get_post_meta( $post_id, '_wpb_vc_js_status', true );
		if ( 'true' === $vc_enabled ) {
			return true;
		}

		// Alternative check: Look for VC shortcodes in content
		$post = get_post( $post_id );
		if ( $post && has_shortcode( $post->post_content, 'vc_row' ) ) {
			return true;
		}

		// Check if using VC templates
		$template = get_post_meta( $post_id, '_wp_page_template', true );
		if ( $template && strpos( $template, 'vc_' ) !== false ) {
			return true;
		}

		return false;
	}

	/**
	 * Initialize Visual Composer-specific features
	 */
	protected function init() {
		// Call common initialization
		parent::common_init();
		
		// Visual Composer specific hooks
		add_action( 'vc_before_init', array( $this, 'vc_before_init_actions' ) );
		
		// Handle VC frontend editor
		add_action( 'vc_frontend_editor_render', array( $this, 'handle_frontend_editor' ) );
		
		// Fix VC Row stretch
		add_filter( 'vc_shortcode_output', array( $this, 'fix_row_stretch' ), 10, 3 );
		
		// Handle VC templates
		add_filter( 'template_include', array( $this, 'handle_vc_template' ), 99 );
		
		// Add theme integration
		add_action( 'vc_after_init', array( $this, 'integrate_with_vc' ) );
	}

	/**
	 * Check if in Visual Composer preview mode
	 *
	 * @return bool
	 */
	public function is_builder_preview() {
		if ( ! $this->is_builder_active() ) {
			return false;
		}

		// Check if we're in VC frontend editor
		if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
			return true;
		}

		// Check if we're in VC backend editor
		if ( isset( $_GET['vc_editable'] ) && 'true' === $_GET['vc_editable'] ) {
			return true;
		}

		// Check for VC frontend editor mode
		if ( isset( $_GET['vc_action'] ) && 'vc_inline' === $_GET['vc_action'] ) {
			return true;
		}

		return false;
	}

	/**
	 * Actions to run before VC init
	 */
	public function vc_before_init_actions() {
		// Set VC to run in theme mode
		if ( function_exists( 'vc_set_as_theme' ) ) {
			vc_set_as_theme();
		}

		// Disable VC frontend editor CSS
		if ( function_exists( 'vc_disable_frontend' ) && apply_filters( 'reign_vc_disable_frontend', false ) ) {
			vc_disable_frontend();
		}
	}

	/**
	 * Handle VC frontend editor
	 */
	public function handle_frontend_editor() {
		// Add specific class for frontend editor
		add_filter( 'body_class', function( $classes ) {
			$classes[] = 'reign-vc-frontend-editor';
			return $classes;
		} );
	}

	/**
	 * Fix VC row stretch functionality
	 *
	 * @param string $output Shortcode output
	 * @param object $obj Shortcode object
	 * @param array $atts Shortcode attributes
	 * @return string
	 */
	public function fix_row_stretch( $output, $obj, $atts ) {
		if ( 'vc_row' === $obj->settings( 'base' ) ) {
			// Add data attributes for row stretch
			if ( ! empty( $atts['full_width'] ) && 'stretch_row' === $atts['full_width'] ) {
				$output = str_replace( 'vc_row', 'vc_row data-vc-full-width="true" data-vc-full-width-init="false"', $output );
			} elseif ( ! empty( $atts['full_width'] ) && 'stretch_row_content' === $atts['full_width'] ) {
				$output = str_replace( 'vc_row', 'vc_row data-vc-full-width="true" data-vc-full-width-init="false" data-vc-stretch-content="true"', $output );
			} elseif ( ! empty( $atts['full_width'] ) && 'stretch_row_content_no_spaces' === $atts['full_width'] ) {
				$output = str_replace( 'vc_row', 'vc_row data-vc-full-width="true" data-vc-full-width-init="false" data-vc-stretch-content="true" data-vc-full-width-init="true"', $output );
			}
		}
		
		return $output;
	}

	/**
	 * Handle VC template include
	 *
	 * @param string $template Template path
	 * @return string
	 */
	public function handle_vc_template( $template ) {
		// Check if using VC blank template
		if ( is_page() ) {
			$page_template = get_post_meta( get_the_ID(), '_wp_page_template', true );
			
			if ( 'vc_blank_template' === $page_template ) {
				// Remove header and footer for blank template
				remove_action( 'reign_header', 'reign_header_markup' );
				remove_action( 'reign_footer', 'reign_footer_markup' );
				add_filter( 'reign_display_header', '__return_false' );
				add_filter( 'reign_display_footer', '__return_false' );
			}
		}
		
		return $template;
	}

	/**
	 * Integrate with Visual Composer
	 */
	public function integrate_with_vc() {
		// Remove VC welcome screen
		if ( function_exists( 'vc_remove_wp_admin_bar_button' ) ) {
			vc_remove_wp_admin_bar_button();
		}

		// Set default post types for VC
		if ( function_exists( 'vc_set_default_editor_post_types' ) ) {
			$post_types = apply_filters( 'reign_vc_post_types', array( 'page', 'post' ) );
			vc_set_default_editor_post_types( $post_types );
		}

		// Add custom CSS for VC elements
		$this->add_vc_custom_css();
	}

	/**
	 * Add custom CSS for VC elements
	 */
	private function add_vc_custom_css() {
		// Add inline CSS for VC specific fixes
		add_action( 'wp_head', function() {
			if ( $this->is_builder_used() ) {
				?>
				<style type="text/css">
					/* VC Row fixes for Reign theme */
					.vc_row[data-vc-full-width="true"] {
						position: relative;
						box-sizing: border-box;
					}
					
					/* Fix for VC in Reign container */
					.reign-vc-content .vc_row-fluid {
						margin-left: -15px;
						margin-right: -15px;
					}
				</style>
				<?php
			}
		} );
	}

	/**
	 * Override parent's enqueue_assets to add VC-specific logic
	 */
	public function enqueue_assets() {
		parent::enqueue_assets();
		
		// Enqueue VC-specific scripts if needed
		if ( $this->is_builder_used() && ! $this->is_builder_preview() ) {
			// Ensure VC frontend JS is loaded
			if ( function_exists( 'vc_frontend_editor_enqueue_js_css' ) ) {
				vc_frontend_editor_enqueue_js_css();
			}
		}
	}

	/**
	 * Get the page template for pages
	 *
	 * @return string
	 */
	public function get_page_template() {
		// Visual Composer uses its own template system
		return '';
	}

	/**
	 * Get the template for single posts
	 *
	 * @return string
	 */
	public function get_single_template() {
		// Visual Composer uses its own template system
		return '';
	}

	/**
	 * Override handle_theme_wrappers for VC-specific handling
	 */
	public function handle_theme_wrappers() {
		// Call parent method
		parent::handle_theme_wrappers();
		
		// Additional handling for VC blank template
		if ( is_page() ) {
			$template = get_post_meta( get_the_ID(), '_wp_page_template', true );
			if ( 'vc_blank_template' === $template ) {
				// Remove all theme wrappers for blank template
				remove_all_actions( 'reign_before_content' );
				remove_all_actions( 'reign_after_content' );
			}
		}
	}
}