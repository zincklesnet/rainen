<?php
/**
 * Elementor Page Builder Compatibility Class
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
 * Elementor compatibility class
 */
class Reign_Elementor_Builder extends Reign_Page_Builder_Base {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->name = 'Elementor';
		$this->slug = 'elementor';
		
		parent::__construct();
	}

	/**
	 * Check if Elementor is active
	 *
	 * @return bool
	 */
	public function is_builder_active() {
		return defined( 'ELEMENTOR_VERSION' );
	}

	/**
	 * Check if Elementor is used on a post
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

		// Check if Elementor is used on this post
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$document = \Elementor\Plugin::$instance->documents->get( $post_id );
			if ( $document && $document->is_built_with_elementor() ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Initialize Elementor-specific features
	 */
	protected function init() {
		// Call common initialization
		parent::common_init();
		
		// Elementor-specific hooks
		add_action( 'elementor/theme/register_locations', array( $this, 'register_elementor_locations' ) );
		add_action( 'elementor/page_templates/canvas/before_content', array( $this, 'before_canvas_content' ) );
		add_action( 'elementor/page_templates/canvas/after_content', array( $this, 'after_canvas_content' ) );
		
		// Add theme support for Elementor
		add_action( 'after_setup_theme', array( $this, 'add_elementor_theme_support' ) );
		
		// Handle Elementor preview mode
		add_action( 'elementor/preview/init', array( $this, 'handle_preview_mode' ) );
		
		// Fix container width for Elementor
		add_action( 'wp', array( $this, 'elementor_content_width' ) );
		
		// Header/footer rendering is now handled by conditional checks in header.php and footer.php
		// No need to hook into reign_masthead or reign_footer actions
		
		// Handle template include for Canvas template only
		add_filter( 'template_include', array( $this, 'handle_elementor_template' ), 99 );
		
		// Add page title hiding functionality like Hello Elementor
		add_filter( 'reign_display_page_title', array( $this, 'check_hide_title' ) );

		// Remove the page header action
		if ( class_exists( 'Reign_Theme_Structure' ) ) {
			$Reign_Theme_Structure_OBJ = Reign_Theme_Structure::instance();
			remove_action( 'reign_before_content', array( $Reign_Theme_Structure_OBJ, 'render_page_header' ) );
		}
	}

	/**
	 * Check if in Elementor preview mode
	 *
	 * @return bool
	 */
	public function is_builder_preview() {
		if ( ! $this->is_builder_active() ) {
			return false;
		}

		// Check if we're in Elementor editor
		if ( class_exists( '\Elementor\Plugin' ) ) {
			return \Elementor\Plugin::$instance->preview->is_preview_mode() || 
			       \Elementor\Plugin::$instance->editor->is_edit_mode();
		}

		return false;
	}

	/**
	 * Register Elementor locations
	 *
	 * @param object $elementor_theme_manager Elementor theme manager
	 */
	public function register_elementor_locations( $elementor_theme_manager ) {
		// Don't register any theme locations - let Elementor handle everything natively
		// This prevents conflicts and single page issues
		return;
	}

	/**
	 * Add Elementor theme support
	 */
	public function add_elementor_theme_support() {
		// Add basic support for Elementor
		add_theme_support( 'elementor' );
		
		// Add WooCommerce support
		if ( class_exists( 'WooCommerce' ) ) {
			add_theme_support( 'wc-product-gallery-zoom' );
			add_theme_support( 'wc-product-gallery-lightbox' );
			add_theme_support( 'wc-product-gallery-slider' );
		}
	}

	/**
	 * Handle Elementor preview mode
	 */
	public function handle_preview_mode() {
		// Add specific body class for preview mode
		add_filter( 'body_class', function( $classes ) {
			$classes[] = 'elementor-preview-mode';
			$classes[] = 'reign-elementor-preview';
			return $classes;
		} );
	}

	/**
	 * Set content width for Elementor
	 */
	public function elementor_content_width() {
		if ( $this->is_builder_used() ) {
			// Get Elementor container width
			$container_width = get_option( 'elementor_container_width', 1140 );
			
			// Set global content width
			if ( ! isset( $GLOBALS['content_width'] ) ) {
				$GLOBALS['content_width'] = apply_filters( 'reign_elementor_content_width', $container_width );
			}
		}
	}

	/**
	 * Before canvas content
	 */
	public function before_canvas_content() {
		?>
		<div class="reign-elementor-canvas-wrapper">
		<?php
	}

	/**
	 * After canvas content
	 */
	public function after_canvas_content() {
		?>
		</div>
		<?php
	}

	/**
	 * Override parent's enqueue_assets to add Elementor-specific logic
	 */
	public function enqueue_assets() {
		parent::enqueue_assets();
		
		// Add Elementor-specific inline CSS
		if ( $this->is_builder_used() ) {
			$inline_css = $this->get_inline_css();
			if ( ! empty( $inline_css ) ) {
				wp_add_inline_style( 'reign-elementor-compatibility', $inline_css );
			}
		}
	}

	/**
	 * Get inline CSS for Elementor
	 *
	 * @return string
	 */
	private function get_inline_css() {
		$css = '';
		
		// Get Elementor settings
		$container_width = get_option( 'elementor_container_width', 1140 );
		$space_between_widgets = get_option( 'elementor_space_between_widgets', 20 );

		// Ensure values are valid integers
		$container_width = absint( $container_width );
		$space_between_widgets = absint( $space_between_widgets );
		
		// Add dynamic CSS based on Elementor settings
		// Only build CSS if values are valid
		if ( $container_width > 0 ) {
			$css .= '.elementor-section.elementor-section-boxed > .elementor-container {';
			$css .= 'max-width: ' . $container_width . 'px;';
			$css .= '}';
		}

		if ( $space_between_widgets > 0 ) {
			$css .= '.elementor-widget:not(:last-child) {';
			$css .= 'margin-bottom: ' . $space_between_widgets . 'px;';
			$css .= '}';
		}
		
		return $css;
	}

	/**
	 * Check if using Elementor Canvas template
	 *
	 * @return bool
	 */
	public function is_canvas_template() {
		if ( ! $this->is_builder_active() ) {
			return false;
		}

		$template = get_page_template_slug();
		return 'elementor_canvas' === $template;
	}

	/**
	 * Check if using Elementor Full Width template
	 *
	 * @return bool
	 */
	public function is_full_width_template() {
		if ( ! $this->is_builder_active() ) {
			return false;
		}

		$template = get_page_template_slug();
		return 'elementor_header_footer' === $template;
	}

	/**
	 * Override handle_theme_wrappers for Elementor-specific handling
	 */
	public function handle_theme_wrappers() {
		// Call parent method
		parent::handle_theme_wrappers();
		
		// Only handle Canvas template - header/footer templates are handled by header.php/footer.php conditionals
		if ( $this->is_canvas_template() ) {
			// Canvas template should have no theme header/footer at all
			add_filter( 'reign_display_header', '__return_false' );
			add_filter( 'reign_display_footer', '__return_false' );
		}
	}





	/**
	 * Handle Elementor template include
	 *
	 * @param string $template Template path
	 * @return string
	 */
	public function handle_elementor_template( $template ) {
		// Minimal handling - only remove page header for Canvas templates
		if ( $template && strpos( $template, '/elementor/modules/page-templates/templates/canvas.php' ) !== false ) {
			// Remove page header for Canvas template only
			remove_action( 'reign_before_content', array( Reign_Theme_Structure::instance(), 'render_page_header' ) );
			remove_action( 'reign_before_content_section', array( Reign_Theme_Structure::instance(), 'render_page_header' ) );
		}
		
		return $template;
	}
	
	/**
	 * Check whether to display the page title (following Hello Elementor's approach)
	 *
	 * @param bool $val default value
	 * @return bool
	 */
	public function check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = \Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}

}