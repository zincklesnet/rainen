<?php
/**
 * Reign Thrive Architect Compatibility
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Thrive Architect page builder compatibility class
 */
class Reign_Thrive_Architect_Builder extends Reign_Page_Builder_Base {

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->name = 'Thrive Architect';
		$this->slug = 'thrive-architect';
		parent::__construct();
	}

	/**
	 * Check if Thrive Architect is active
	 *
	 * @return bool
	 */
	public function is_builder_active() {
		return defined( 'TVE_IN_ARCHITECT' );
	}

	/**
	 * Check if Thrive Architect is used on a specific post
	 *
	 * @param int|null $post_id Post ID.
	 * @return bool
	 */
	public function is_builder_used( $post_id = null ) {
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}

		if ( ! $post_id ) {
			return false;
		}

		// Check if Thrive Architect content exists
		$tve_content = get_post_meta( $post_id, 'tve_updated_post', true );
		
		return ! empty( $tve_content );
	}

	/**
	 * Initialize Thrive Architect specific functionality
	 */
	protected function init() {
		parent::common_init();

		// Thrive Architect specific hooks
		add_action( 'template_redirect', array( $this, 'handle_thrive_editor' ), 5 );
		
		// Handle Thrive Architect landing pages
		add_filter( 'tve_landing_page_template', array( $this, 'handle_landing_page_template' ) );
		
		// Remove theme elements when in Thrive Architect editor
		if ( $this->is_thrive_editor_active() ) {
			// Remove header and footer in editor mode
			remove_action( 'reign_header', 'reign_header', 10 );
			remove_action( 'reign_footer', 'reign_footer', 10 );
			
			// Add minimal wrapper for editor
			add_action( 'reign_before_content', array( $this, 'editor_wrapper_start' ) );
			add_action( 'reign_after_content', array( $this, 'editor_wrapper_end' ) );
		}
	}

	/**
	 * Check if Thrive Architect editor is active
	 *
	 * @return bool
	 */
	public function is_thrive_editor_active() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only page-builder editor-mode detection.
		return isset( $_GET['tve'] ) && 'true' === sanitize_key( wp_unslash( $_GET['tve'] ) );
	}

	/**
	 * Handle Thrive Architect editor mode
	 */
	public function handle_thrive_editor() {
		if ( $this->is_thrive_editor_active() && $this->is_builder_used() ) {
			// Force full-width layout in editor
			add_filter( 'reign_content_layout', function() {
				return 'full_width';
			});
			
			// Remove sub-header in editor
			$this->maybe_disable_subheader();
		}
	}

	/**
	 * Handle Thrive Architect landing page templates
	 *
	 * @param string $template Template path.
	 * @return string
	 */
	public function handle_landing_page_template( $template ) {
		// Let Thrive Architect handle its own landing page templates
		return $template;
	}

	/**
	 * Add editor wrapper start
	 */
	public function editor_wrapper_start() {
		echo '<div class="reign-thrive-editor-wrapper">';
	}

	/**
	 * Add editor wrapper end
	 */
	public function editor_wrapper_end() {
		echo '</div>';
	}

	/**
	 * Get content wrapper classes
	 *
	 * @return string
	 */
	public function get_content_wrapper_classes() {
		$classes = parent::get_content_wrapper_classes();
		
		if ( $this->is_thrive_editor_active() ) {
			$classes .= ' reign-thrive-editor-active';
		}
		
		return $classes;
	}

	/**
	 * Output Thrive Architect content
	 *
	 * @param null|WP_Post $post Post object.
	 */
	public function output_content( $post = null ) {
		if ( ! $post ) {
			$post = get_post();
		}

		if ( ! $post ) {
			return;
		}

		// Get Thrive Architect content
		$tve_content = get_post_meta( $post->ID, 'tve_updated_post', true );
		
		if ( ! empty( $tve_content ) ) {
			// Thrive Architect applies its own filters, so we just need to output the content
			echo apply_filters( 'tve_editor_content', $tve_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Thrive Architect builder markup, rendered like the_content(); kses/esc would strip the builder's layout HTML.
		} else {
			// Fallback to regular content
			the_content();
		}
	}

	/**
	 * Check if the current request is for a Thrive Architect landing page
	 *
	 * @return bool
	 */
	public function is_landing_page() {
		if ( ! is_singular() ) {
			return false;
		}
		
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return false;
		}
		
		// Check if it's a Thrive landing page
		$is_landing_page = get_post_meta( $post_id, 'tve_landing_page', true );
		
		return ! empty( $is_landing_page );
	}

	/**
	 * Enqueue Thrive Architect compatibility assets
	 */
	public function enqueue_assets() {
		if ( ! $this->should_load_assets() ) {
			return;
		}

		// Enqueue compatibility CSS
		wp_enqueue_style(
			'reign-thrive-architect-compat',
			get_template_directory_uri() . '/assets/css/compatibility/thrive-architect.css',
			array(),
			defined( 'REIGN_THEME_VERSION' ) ? REIGN_THEME_VERSION : '1.0.0'
		);
		
		// Add inline styles for editor mode
		if ( $this->is_thrive_editor_active() ) {
			$inline_css = '
				body.reign-thrive-editor-active .site-header,
				body.reign-thrive-editor-active .site-footer,
				body.reign-thrive-editor-active .reign-page-header {
					display: none !important;
				}
			';
			wp_add_inline_style( 'reign-thrive-architect-compat', $inline_css );
		}
	}
}