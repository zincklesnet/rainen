<?php
/**
 * Beaver Builder Page Builder Compatibility Class
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
 * Beaver Builder compatibility class
 */
class Reign_Beaver_Builder extends Reign_Page_Builder_Base {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->name = 'Beaver Builder';
		$this->slug = 'beaver-builder';
		
		parent::__construct();
	}

	/**
	 * Check if Beaver Builder is active
	 *
	 * @return bool
	 */
	public function is_builder_active() {
		return class_exists( 'FLBuilder' ) || defined( 'FL_BUILDER_VERSION' );
	}

	/**
	 * Check if Beaver Builder is used on a post
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

		// Use Beaver Builder's built-in function to check if it's enabled
		if ( class_exists( 'FLBuilderModel' ) && method_exists( 'FLBuilderModel', 'is_builder_enabled' ) ) {
			return FLBuilderModel::is_builder_enabled( $post_id );
		}

		// Fallback: Check post meta
		$enabled = get_post_meta( $post_id, '_fl_builder_enabled', true );
		return ( $enabled && $enabled !== '0' );
	}

	/**
	 * Initialize Beaver Builder-specific features
	 */
	protected function init() {
		// Call common initialization
		parent::common_init();
		
		// Beaver Builder specific hooks
		add_action( 'fl_builder_before_render_content', array( $this, 'before_render_content' ) );
		add_action( 'fl_builder_after_render_content', array( $this, 'after_render_content' ) );
		
		// Handle BB templates
		add_filter( 'fl_builder_template_selector_data', array( $this, 'add_theme_support' ) );
		
		// Fix row widths
		add_filter( 'fl_builder_row_custom_class', array( $this, 'add_row_classes' ), 10, 2 );
		
		// Handle BB theme layouts if BB Theme is active
		if ( class_exists( 'FLThemeBuilderLayoutData' ) ) {
			add_action( 'wp', array( $this, 'before_theme_builder_header' ) );
			add_action( 'wp', array( $this, 'before_theme_builder_footer' ) );
		}
		
		// Add support for BB templates
		if ( class_exists( 'FLThemeBuilderLoader' ) ) {
			add_theme_support( 'fl-theme-builder-headers' );
			add_theme_support( 'fl-theme-builder-footers' );
			add_theme_support( 'fl-theme-builder-parts' );
		}
		
		// Handle full width rows
		add_filter( 'fl_builder_content_wrapper_class', array( $this, 'content_wrapper_class' ) );
		
		// Integrate with BB settings
		add_filter( 'fl_builder_settings_form_defaults', array( $this, 'form_defaults' ), 10, 2 );
	}

	/**
	 * Check if in Beaver Builder preview mode
	 *
	 * @return bool
	 */
	public function is_builder_preview() {
		if ( ! $this->is_builder_active() ) {
			return false;
		}

		// Check if we're in BB editor
		if ( class_exists( 'FLBuilderModel' ) && FLBuilderModel::is_builder_active() ) {
			return true;
		}

		// Check URL parameters
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only page-builder editor-mode detection (presence check only).
		if ( isset( $_GET['fl_builder'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Before render content
	 */
	public function before_render_content() {
		// Add wrapper div for styling
		echo '<div class="reign-bb-content-wrapper">';
	}

	/**
	 * After render content
	 */
	public function after_render_content() {
		echo '</div>';
	}

	/**
	 * Add theme support data
	 *
	 * @param array $data Template selector data
	 * @return array
	 */
	public function add_theme_support( $data ) {
		$data['theme']['name'] = 'Reign Theme';
		$data['theme']['screenshot'] = get_template_directory_uri() . '/screenshot.png';
		return $data;
	}

	/**
	 * Add custom classes to BB rows
	 *
	 * @param string $class Row class
	 * @param object $row Row settings
	 * @return string
	 */
	public function add_row_classes( $class, $row ) {
		// Add class for full width rows
		if ( isset( $row->settings->width ) && 'full' === $row->settings->width ) {
			$class .= ' reign-bb-full-width';
		}
		
		// Add class for content width
		if ( isset( $row->settings->content_width ) && 'full' === $row->settings->content_width ) {
			$class .= ' reign-bb-full-content';
		}
		
		return $class;
	}

	/**
	 * Before theme builder header
	 */
	public function before_theme_builder_header() {
		// Remove Reign's header when BB Theme Builder header is used.
		$header_ids = FLThemeBuilderLayoutData::get_current_page_header_ids(); // Get the header ID.

		// If we have a header, remove the theme header and hook in Theme Builder's.
		if ( ! empty( $header_ids ) ) {
			if ( class_exists( 'Reign_Theme_Structure' ) ) {
				$reign_structure = Reign_Theme_Structure::instance();

				remove_action( 'reign_before_masthead', array( $reign_structure, 'render_theme_topbar' ), 20 );
				remove_action( 'reign_masthead', array( $reign_structure, 'render_theme_header_desktop' ), 20 );
				remove_action( 'reign_masthead', array( $reign_structure, 'render_theme_header_mobile' ), 25 );
			}

			add_action( 'reign_masthead', array( 'FLThemeBuilderLayoutRenderer', 'render_header' ) );
		}
	}

	/**
	 * Before theme builder footer
	 */
	public function before_theme_builder_footer() {
		// Remove Reign's footer when BB Theme Builder footer is used.
		$footer_ids = FLThemeBuilderLayoutData::get_current_page_footer_ids(); // Get the footer ID.

		// If we have a footer, remove the theme footer and hook in Theme Builder's.
		if ( ! empty( $footer_ids ) ) {
			if ( class_exists( 'Reign_Theme_Structure' ) ) {
				$reign_structure = Reign_Theme_Structure::instance();
				remove_action( 'reign_footer', array( $reign_structure, 'render_theme_footer' ), 20 );
			}

			add_action( 'reign_footer', array( 'FLThemeBuilderLayoutRenderer', 'render_footer' ) );
		}
	}

	/**
	 * Modify content wrapper class
	 *
	 * @param string $class Content wrapper class
	 * @return string
	 */
	public function content_wrapper_class( $class ) {
		$class .= ' reign-bb-content';
		return $class;
	}

	/**
	 * Set form defaults
	 *
	 * @param array $defaults Form defaults
	 * @param string $form_type Form type
	 * @return array
	 */
	public function form_defaults( $defaults, $form_type ) {
		// Set default row width for Reign theme
		if ( 'row' === $form_type ) {
			$defaults->width = 'fixed';
			$defaults->max_content_width = '1140';
		}
		
		return $defaults;
	}

	/**
	 * Override parent's enqueue_assets to add BB-specific logic
	 */
	public function enqueue_assets() {
		parent::enqueue_assets();
		
		// Add inline CSS for dynamic adjustments
		if ( $this->is_builder_used() ) {
			$css = $this->get_dynamic_css();
			if ( ! empty( $css ) ) {
				wp_add_inline_style( 'reign-beaver-builder-compatibility', $css );
			}
		}
	}

	/**
	 * Get dynamic CSS
	 *
	 * @return string
	 */
	private function get_dynamic_css() {
		$css = '';

		// Get theme content width. The registered setting is
		// 'site_container_width' (a dimension control storing the value WITH
		// its unit, e.g. '1170px' or '90%'); bare numbers from legacy saves
		// get 'px' appended.
		$content_width = get_theme_mod( 'site_container_width', '1170px' );
		$content_width = trim( (string) $content_width );
		if ( ! preg_match( '/^\d+(\.\d+)?(px|%|em|rem|vw)$/', $content_width ) ) {
			$content_width = absint( $content_width ) ? absint( $content_width ) . 'px' : '1170px';
		}

		// Apply to BB fixed width rows.
		$css .= '.fl-builder-content .fl-row-fixed-width {';
		$css .= 'max-width: ' . $content_width . ';';
		$css .= '}';

		return $css;
	}

	/**
	 * Get the page template for pages
	 *
	 * @return string
	 */
	public function get_page_template() {
		// Beaver Builder doesn't require custom templates
		return '';
	}

	/**
	 * Get the template for single posts
	 *
	 * @return string
	 */
	public function get_single_template() {
		// Beaver Builder doesn't require custom templates
		return '';
	}

	/**
	 * Override handle_theme_wrappers for BB-specific handling
	 */
	public function handle_theme_wrappers() {
		// Call parent method
		parent::handle_theme_wrappers();
		
		// Additional handling for BB templates
		if ( $this->is_builder_used() ) {
			// Check if using BB Theme Builder
			if ( class_exists( 'FLThemeBuilderLayoutData' ) ) {
				$header_ids = FLThemeBuilderLayoutData::get_current_page_header_ids();
				$footer_ids = FLThemeBuilderLayoutData::get_current_page_footer_ids();
				
				// Remove theme header if BB header exists
				if ( ! empty( $header_ids ) ) {
					remove_action( 'reign_header', 'reign_header_markup' );
					add_filter( 'reign_display_header', '__return_false' );
				}
				
				// Remove theme footer if BB footer exists
				if ( ! empty( $footer_ids ) ) {
					remove_action( 'reign_footer', 'reign_footer_markup' );
					add_filter( 'reign_display_footer', '__return_false' );
				}
			}
		}
	}

	/**
	 * Override add_body_classes to add BB-specific classes
	 *
	 * @param array $classes Body classes
	 * @return array
	 */
	public function add_body_classes( $classes ) {
		$classes = parent::add_body_classes( $classes );
		
		// Add editing class
		if ( FLBuilderModel::is_builder_active() ) {
			$classes[] = 'fl-builder-edit';
			$classes[] = 'reign-bb-editor-active';
		}
		
		// Add template class
		if ( $this->is_builder_used() ) {
			$template = get_post_meta( get_the_ID(), '_fl_builder_template', true );
			if ( $template ) {
				$classes[] = 'fl-builder-template-' . sanitize_html_class( $template );
			}
		}
		
		return $classes;
	}
}