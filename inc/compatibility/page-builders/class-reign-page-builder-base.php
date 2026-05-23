<?php
/**
 * Abstract Base Class for Page Builder Compatibility
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
 * Abstract class for page builder compatibility
 */
abstract class Reign_Page_Builder_Base {

	/**
	 * Builder name
	 *
	 * @var string
	 */
	protected $name = '';

	/**
	 * Builder slug
	 *
	 * @var string
	 */
	protected $slug = '';

	/**
	 * Initialize the builder compatibility
	 */
	public function __construct() {
		if ( $this->is_builder_active() ) {
			$this->init();
		}
	}

	/**
	 * Initialize hooks and filters
	 * Must be implemented by child classes
	 */
	abstract protected function init();

	/**
	 * Check if builder is active
	 * Must be implemented by child classes
	 *
	 * @return bool
	 */
	abstract public function is_builder_active();

	/**
	 * Check if builder is used on a specific post
	 * Must be implemented by child classes
	 *
	 * @param int $post_id Post ID
	 * @return bool
	 */
	abstract public function is_builder_used( $post_id = null );

	/**
	 * Common initialization for all builders
	 */
	protected function common_init() {
		// Enqueue compatibility assets
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ), 100 );
		
		// Add body classes
		add_filter( 'body_class', array( $this, 'add_body_classes' ) );
		
		// Handle theme wrappers
		add_action( 'template_redirect', array( $this, 'handle_theme_wrappers' ) );
		
		// Filter content layout
		add_filter( 'reign_content_layout', array( $this, 'filter_content_layout' ) );
		
		// Disable sub-header when page builder is used
		add_action( 'wp', array( $this, 'maybe_disable_subheader' ) );
	}

	/**
	 * Enqueue compatibility assets
	 */
	public function enqueue_assets() {
		if ( ! $this->should_load_assets() ) {
			return;
		}

		// Load builder-specific CSS
		$css_file = '/assets/css/compatibility/' . $this->slug . '.css';
		if ( file_exists( get_template_directory() . $css_file ) ) {
			wp_enqueue_style( 
				'reign-' . $this->slug . '-compatibility', 
				get_template_directory_uri() . $css_file, 
				array(), 
				wp_get_theme()->get( 'Version' ) 
			);
		}

		// Load builder-specific JS if exists
		$js_file = '/assets/js/compatibility/' . $this->slug . '.js';
		if ( file_exists( get_template_directory() . $js_file ) ) {
			wp_enqueue_script(
				'reign-' . $this->slug . '-compatibility',
				get_template_directory_uri() . $js_file,
				array( 'jquery' ),
				wp_get_theme()->get( 'Version' ),
				true
			);
		}
	}

	/**
	 * Check if assets should be loaded
	 *
	 * @return bool
	 */
	protected function should_load_assets() {
		// Load on specific templates
		if ( $this->is_builder_template() ) {
			return true;
		}

		// Load if builder is used on current post/page
		if ( is_singular() && $this->is_builder_used() ) {
			return true;
		}

		// Load in builder preview/edit mode
		if ( $this->is_builder_preview() ) {
			return true;
		}

		return false;
	}

	/**
	 * Check if using builder template
	 *
	 * @return bool
	 */
	public function is_builder_template() {
		return is_page_template( 'page-' . $this->slug . '.php' ) || 
		       is_page_template( 'single-' . $this->slug . '.php' );
	}

	/**
	 * Check if in builder preview mode
	 * Can be overridden by child classes
	 *
	 * @return bool
	 */
	public function is_builder_preview() {
		return false;
	}

	/**
	 * Add body classes for builder
	 *
	 * @param array $classes Body classes
	 * @return array
	 */
	public function add_body_classes( $classes ) {
		if ( is_singular() && $this->is_builder_used() ) {
			$post_type = get_post_type();
			$classes[] = $this->slug . '-builder-active';
			$classes[] = $this->slug . '-builder-active-' . $post_type;
			$classes[] = 'reign-page-builder-active';
			
			// Add specific class for posts vs pages
			if ( 'post' === $post_type ) {
				$classes[] = $this->slug . '-builder-active-post';
			} elseif ( 'page' === $post_type ) {
				$classes[] = $this->slug . '-builder-active-page';
			}
		}

		if ( $this->is_builder_preview() ) {
			$classes[] = $this->slug . '-builder-preview';
			$classes[] = 'reign-page-builder-preview';
		}

		return $classes;
	}

	/**
	 * Handle theme wrappers for builder content
	 */
	public function handle_theme_wrappers() {
		if ( is_singular() && $this->is_builder_used() ) {
			// Remove Reign theme wrappers
			remove_action( 'reign_before_content', 'reign_theme_wrapper_start' );
			remove_action( 'reign_after_content', 'reign_theme_wrapper_end' );
			
			// Remove sidebar
			add_filter( 'reign_sidebar_display', '__return_false' );
		}
	}

	/**
	 * Filter content layout for builder pages
	 *
	 * @param string $layout Current layout
	 * @return string
	 */
	public function filter_content_layout( $layout ) {
		if ( $this->should_use_builder_layout() ) {
			return 'page-builder';
		}
		return $layout;
	}

	/**
	 * Check if should use builder layout
	 *
	 * @return bool
	 */
	public function should_use_builder_layout() {
		// Check manual template selection
		if ( $this->is_builder_template() ) {
			return true;
		}

		// Check automatic detection
		if ( is_singular() && $this->is_builder_used() ) {
			return true;
		}

		return false;
	}

	/**
	 * Get content wrapper classes
	 *
	 * @return string
	 */
	public function get_content_wrapper_classes() {
		$classes = array( 'content-wrapper' );

		if ( $this->should_use_builder_layout() ) {
			$classes[] = $this->slug . '-builder-active';
			$classes[] = 'reign-page-builder-wrapper';
		}

		return implode( ' ', $classes );
	}

	/**
	 * Output builder content
	 *
	 * @param WP_Post $post Post object
	 */
	public function output_builder_content( $post = null ) {
		if ( ! $post ) {
			global $post;
		}

		if ( $this->is_builder_used( $post->ID ) ) {
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php the_content(); ?>
			</article>
			<?php
		}
	}

	/**
	 * Maybe wrap comments
	 *
	 * @param bool $wrap Whether to wrap
	 */
	public function maybe_wrap_comments( $wrap = true ) {
		if ( $this->is_builder_used() && ( comments_open() || get_comments_number() ) ) {
			if ( $wrap ) {
				echo '<div class="reign-' . esc_attr( $this->slug ) . '-comments-wrapper reign-builder-comments-wrapper">';
			} else {
				echo '</div>';
			}
		}
	}

	/**
	 * Get builder name
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get builder slug
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

	/**
	 * Maybe disable sub-header when page builder is used
	 */
	public function maybe_disable_subheader() {
		// Only run on singular pages
		if ( ! is_singular() ) {
			return;
		}
		
		// Check if page builder is used on this post
		if ( $this->is_builder_used() ) {
			// Remove the page header action
			remove_action( 'reign_before_content', array( Reign_Theme_Structure::instance(), 'render_page_header' ) );
			
			// Also set post meta to disable sub-header
			// This ensures it's disabled even if other plugins try to re-add it
			add_filter( 'get_post_metadata', array( $this, 'filter_subheader_meta' ), 10, 4 );
		}
	}

	/**
	 * Filter post meta to disable sub-header
	 *
	 * @param mixed  $value     The value get_metadata() should return
	 * @param int    $object_id Object ID
	 * @param string $meta_key  Meta key
	 * @param bool   $single    Whether to return only the first value
	 * @return mixed
	 */
	public function filter_subheader_meta( $value, $object_id, $meta_key, $single ) {
		// Check if this is for the current post and the sub-header meta key
		if ( get_the_ID() === $object_id && '_subheader_overwrite' === $meta_key ) {
			// Return 'yes' to indicate sub-header should be hidden
			return $single ? 'yes' : array( 'yes' );
		}
		
		return $value;
	}
}