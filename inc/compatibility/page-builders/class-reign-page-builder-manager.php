<?php
/**
 * Page Builder Manager Class
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
 * Manages all page builder compatibility
 */
class Reign_Page_Builder_Manager {

	/**
	 * Instance of this class
	 *
	 * @var object
	 */
	private static $instance = null;

	/**
	 * Registered builders
	 *
	 * @var array
	 */
	private $builders = array();

	/**
	 * Active builders
	 *
	 * @var array
	 */
	private $active_builders = array();

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Get instance
	 *
	 * @return object
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize
	 */
	private function init() {
		// Load base class
		require_once get_template_directory() . '/inc/compatibility/page-builders/class-reign-page-builder-base.php';
		
		// Register builders
		add_action( 'after_setup_theme', array( $this, 'register_builders' ), 5 );
		
		// Initialize builders
		add_action( 'after_setup_theme', array( $this, 'init_builders' ), 10 );
		
		// Add common hooks
		add_action( 'init', array( $this, 'add_common_hooks' ) );
	}

	/**
	 * Register page builders
	 */
	public function register_builders() {
		// Get all builder files
		$builders_dir = get_template_directory() . '/inc/compatibility/page-builders/builders/';
		
		if ( is_dir( $builders_dir ) ) {
			$builder_files = glob( $builders_dir . 'class-reign-*.php' );
			
			foreach ( $builder_files as $file ) {
				require_once $file;
				
				// Get class name from file
				$filename = basename( $file, '.php' );
				$class_name = str_replace( '-', '_', $filename );
				$class_name = str_replace( 'class_', '', $class_name );
				
				// Check if class exists
				if ( class_exists( $class_name ) ) {
					$builder = new $class_name();
					if ( $builder instanceof Reign_Page_Builder_Base ) {
						$this->register_builder( $builder );
					}
				}
			}
		}
		
		// Allow third-party builders to register
		do_action( 'reign_register_page_builders', $this );
	}

	/**
	 * Register a builder
	 *
	 * @param Reign_Page_Builder_Base $builder Builder instance
	 */
	public function register_builder( Reign_Page_Builder_Base $builder ) {
		$slug = $builder->get_slug();
		$this->builders[ $slug ] = $builder;
		
		// Check if builder is active
		if ( $builder->is_builder_active() ) {
			$this->active_builders[ $slug ] = $builder;
		}
	}

	/**
	 * Initialize active builders
	 */
	public function init_builders() {
		foreach ( $this->active_builders as $builder ) {
			// Builder initialization is handled in its constructor
		}
	}

	/**
	 * Add common hooks for all builders
	 */
	public function add_common_hooks() {
		// Add common page builder styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_common_assets' ), 99 );
		
		// Add helper functions
		require_once get_template_directory() . '/inc/compatibility/page-builders/page-builder-functions.php';
	}

	/**
	 * Enqueue common assets
	 */
	public function enqueue_common_assets() {
		// Common CSS removed as it was causing issues
		// Each builder now handles its own styles independently
	}

	/**
	 * Create common CSS file
	 * @deprecated Common CSS removed - each builder handles its own styles
	 */
	private function create_common_css() {
		// Method kept for backward compatibility but does nothing
		return;
	}

	/**
	 * Check if any builder is used on current post
	 *
	 * @param int $post_id Post ID
	 * @return bool
	 */
	public function is_any_builder_used( $post_id = null ) {
		foreach ( $this->active_builders as $builder ) {
			if ( $builder->is_builder_used( $post_id ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get active builder for current post
	 *
	 * @param int $post_id Post ID
	 * @return Reign_Page_Builder_Base|false
	 */
	public function get_active_builder( $post_id = null ) {
		foreach ( $this->active_builders as $builder ) {
			if ( $builder->is_builder_used( $post_id ) ) {
				return $builder;
			}
		}
		return false;
	}

	/**
	 * Get all registered builders
	 *
	 * @return array
	 */
	public function get_builders() {
		return $this->builders;
	}

	/**
	 * Get active builders
	 *
	 * @return array
	 */
	public function get_active_builders() {
		return $this->active_builders;
	}

	/**
	 * Check if a specific builder is active
	 *
	 * @param string $slug Builder slug
	 * @return bool
	 */
	public function is_builder_active( $slug ) {
		return isset( $this->active_builders[ $slug ] );
	}

	/**
	 * Get builder by slug
	 *
	 * @param string $slug Builder slug
	 * @return Reign_Page_Builder_Base|null
	 */
	public function get_builder( $slug ) {
		return isset( $this->builders[ $slug ] ) ? $this->builders[ $slug ] : null;
	}
}

// Initialize the manager
Reign_Page_Builder_Manager::get_instance();