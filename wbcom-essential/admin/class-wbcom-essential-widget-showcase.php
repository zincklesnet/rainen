<?php
/**
 * Wbcom Essential Admin Page
 *
 * @package WBCOM_Essential
 */

namespace WBCOM_ESSENTIAL;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin page class
 */
class Wbcom_Essential_Widget_Showcase {

	/**
	 * Initialize the class
	 */
	public function __construct() {
		if ( ! $this->should_use_wrapper() ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
	}

	/**
	 * Check if we should use the Wbcom wrapper
	 */
	private function should_use_wrapper() {
		if ( class_exists( 'Wbcom_Shared_Loader' ) ) {
			return true;
		}

		$shared_loader_file = WBCOM_ESSENTIAL_PATH . '/includes/shared-admin/class-wbcom-shared-loader.php';
		if ( file_exists( $shared_loader_file ) ) {
			require_once $shared_loader_file;
			if ( class_exists( 'Wbcom_Shared_Loader' ) ) {
				return true;
			}
		}

		if ( function_exists( 'wbcom_integrate_plugin' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Static method to render admin page (for wrapper callback)
	 */
	public static function render_admin_page() {
		$instance = new self();
		$instance->render_page();
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_menu_page(
			esc_html__( 'Wbcom Designs', 'wbcom-essential' ),
			esc_html__( 'Wbcom Designs', 'wbcom-essential' ),
			'manage_options',
			'wbcom-designs',
			array( $this, 'render_page' ),
			$this->get_menu_icon(),
			58.5
		);

		add_submenu_page(
			'wbcom-designs',
			esc_html__( 'Essential', 'wbcom-essential' ),
			esc_html__( 'Essential', 'wbcom-essential' ),
			'manage_options',
			'wbcom-essential',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Get menu icon
	 */
	private function get_menu_icon() {
		$svg = '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M10 2L13.09 8.26L20 9L14 12L15 20L10 17L5 20L6 12L0 9L6.91 8.26L10 2Z" fill="#a7aaad"/>
		</svg>';
		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}

	/**
	 * Enqueue admin styles.
	 *
	 * @param string $hook The current admin page hook suffix.
	 */
	public function enqueue_admin_styles( $hook ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';

		if ( 'wbcom-essential' !== $current_page && 'wbcom-designs' !== $current_page ) {
			return;
		}

		wp_enqueue_style(
			'wbcom-essential-admin',
			plugin_dir_url( __FILE__ ) . 'css/admin.css',
			array(),
			WBCOM_ESSENTIAL_VERSION
		);
	}

	/**
	 * Render the admin page
	 */
	public function render_page() {
		$stats = $this->get_stats();
		?>
		<div class="wbcom-essential-wrap">
			<!-- Header -->
			<header class="wbcom-header">
				<div class="wbcom-header-content">
					<h1><?php esc_html_e( 'Wbcom Essential', 'wbcom-essential' ); ?></h1>
					<span class="wbcom-version">v<?php echo esc_html( WBCOM_ESSENTIAL_VERSION ); ?></span>
				</div>
				<p class="wbcom-tagline"><?php esc_html_e( 'Free companion plugin for BuddyX, BuddyX Pro, and Reign themes', 'wbcom-essential' ); ?></p>
			</header>
				<!-- Overview Tab -->
				<!-- Stats Cards -->
				<div class="wbcom-stats">
					<div class="wbcom-stat-card wbcom-stat-blocks">
						<span class="wbcom-stat-icon dashicons dashicons-block-default"></span>
						<div class="wbcom-stat-info">
							<span class="wbcom-stat-number"><?php echo esc_html( $stats['blocks'] ); ?></span>
							<span class="wbcom-stat-label"><?php esc_html_e( 'Gutenberg Blocks', 'wbcom-essential' ); ?></span>
						</div>
					</div>
					<div class="wbcom-stat-card wbcom-stat-widgets">
						<span class="wbcom-stat-icon dashicons dashicons-admin-customizer"></span>
						<div class="wbcom-stat-info">
							<span class="wbcom-stat-number"><?php echo esc_html( $stats['widgets'] ); ?></span>
							<span class="wbcom-stat-label"><?php esc_html_e( 'Elementor Widgets', 'wbcom-essential' ); ?></span>
						</div>
					</div>
					<div class="wbcom-stat-card wbcom-stat-price">
						<span class="wbcom-stat-icon dashicons dashicons-heart"></span>
						<div class="wbcom-stat-info">
							<span class="wbcom-stat-number">$0</span>
							<span class="wbcom-stat-label"><?php esc_html_e( 'Free Forever', 'wbcom-essential' ); ?></span>
						</div>
					</div>
				</div>

				<!-- Main Content -->
				<div class="wbcom-content">
					<!-- What's Included -->
					<section class="wbcom-section">
						<h2><?php esc_html_e( "What's Included", 'wbcom-essential' ); ?></h2>
						<div class="wbcom-features">
							<div class="wbcom-feature">
								<span class="dashicons dashicons-block-default"></span>
								<h3><?php esc_html_e( 'Gutenberg Blocks', 'wbcom-essential' ); ?></h3>
								<p><?php esc_html_e( 'Works natively with the WordPress Block Editor. No page builder required.', 'wbcom-essential' ); ?></p>
								<ul>
									<li><?php esc_html_e( '26 General blocks (Accordion, Tabs, Slider, etc.)', 'wbcom-essential' ); ?></li>
									<li><?php esc_html_e( '11 BuddyPress blocks (Members, Groups, Forums)', 'wbcom-essential' ); ?></li>
									<li><?php esc_html_e( '8 Blog blocks (Carousel, Timeline, Ticker)', 'wbcom-essential' ); ?></li>
								</ul>
							</div>
							<div class="wbcom-feature">
								<span class="dashicons dashicons-admin-customizer"></span>
								<h3><?php esc_html_e( 'Elementor Widgets', 'wbcom-essential' ); ?></h3>
								<p><?php esc_html_e( 'Premium widgets for Elementor with advanced styling options.', 'wbcom-essential' ); ?></p>
								<ul>
									<li><?php esc_html_e( '27 General widgets', 'wbcom-essential' ); ?></li>
									<li><?php esc_html_e( '11 BuddyPress widgets', 'wbcom-essential' ); ?></li>
									<li><?php esc_html_e( '5 WooCommerce widgets', 'wbcom-essential' ); ?></li>
								</ul>
							</div>
						</div>
					</section>

					<!-- How to Use -->
					<section class="wbcom-section">
						<h2><?php esc_html_e( 'How to Use', 'wbcom-essential' ); ?></h2>
						<div class="wbcom-howto">
							<div class="wbcom-howto-item">
								<span class="wbcom-howto-step">1</span>
								<div>
									<h4><?php esc_html_e( 'Edit a Page', 'wbcom-essential' ); ?></h4>
									<p><?php esc_html_e( 'Open any page or post in the Block Editor or Elementor.', 'wbcom-essential' ); ?></p>
								</div>
							</div>
							<div class="wbcom-howto-item">
								<span class="wbcom-howto-step">2</span>
								<div>
									<h4><?php esc_html_e( 'Search "Wbcom"', 'wbcom-essential' ); ?></h4>
									<p><?php esc_html_e( 'Click the + button and search for "Wbcom" to find all blocks.', 'wbcom-essential' ); ?></p>
								</div>
							</div>
							<div class="wbcom-howto-item">
								<span class="wbcom-howto-step">3</span>
								<div>
									<h4><?php esc_html_e( 'Insert & Customize', 'wbcom-essential' ); ?></h4>
									<p><?php esc_html_e( 'Add the block and customize using the sidebar settings.', 'wbcom-essential' ); ?></p>
								</div>
							</div>
						</div>
					</section>

					<!-- Requirements -->
					<section class="wbcom-section wbcom-requirements">
						<h2><?php esc_html_e( 'Requirements', 'wbcom-essential' ); ?></h2>
						<div class="wbcom-req-grid">
							<div class="wbcom-req-item">
								<span class="dashicons dashicons-yes-alt"></span>
								<span><?php esc_html_e( 'WordPress 6.0+', 'wbcom-essential' ); ?></span>
							</div>
							<div class="wbcom-req-item">
								<span class="dashicons dashicons-yes-alt"></span>
								<span><?php esc_html_e( 'PHP 8.0+', 'wbcom-essential' ); ?></span>
							</div>
							<div class="wbcom-req-item wbcom-req-optional">
								<span class="dashicons dashicons-info"></span>
								<span><?php esc_html_e( 'Elementor (optional, for widgets)', 'wbcom-essential' ); ?></span>
							</div>
							<div class="wbcom-req-item wbcom-req-optional">
								<span class="dashicons dashicons-info"></span>
								<span><?php esc_html_e( 'BuddyPress (for community blocks)', 'wbcom-essential' ); ?></span>
							</div>
						</div>
					</section>
				</div>

				<!-- Sidebar -->
				<aside class="wbcom-sidebar">
					<div class="wbcom-sidebar-card">
						<h3><?php esc_html_e( 'Need Help?', 'wbcom-essential' ); ?></h3>
						<ul>
							<li>
								<a href="https://docs.wbcomdesigns.com/docs/wbcom-essential/" target="_blank">
									<span class="dashicons dashicons-book"></span>
									<?php esc_html_e( 'Documentation', 'wbcom-essential' ); ?>
								</a>
							</li>
							<li>
								<a href="https://wbcomdesigns.com/support/" target="_blank">
									<span class="dashicons dashicons-sos"></span>
									<?php esc_html_e( 'Get Support', 'wbcom-essential' ); ?>
								</a>
							</li>
							<li>
								<a href="https://docs.wbcomdesigns.com/docs/wbcom-essential/changelog/" target="_blank">
									<span class="dashicons dashicons-backup"></span>
									<?php esc_html_e( 'Changelog', 'wbcom-essential' ); ?>
								</a>
							</li>
						</ul>
					</div>

					<div class="wbcom-sidebar-card wbcom-themes">
						<h3><?php esc_html_e( 'Best With Our Themes', 'wbcom-essential' ); ?></h3>
						<p><?php esc_html_e( 'Wbcom Essential is designed to work perfectly with:', 'wbcom-essential' ); ?></p>
						<ul>
							<li><strong>BuddyX</strong> - <?php esc_html_e( 'Free community theme', 'wbcom-essential' ); ?></li>
							<li><strong>BuddyX Pro</strong> - <?php esc_html_e( 'Premium features', 'wbcom-essential' ); ?></li>
							<li><strong>Reign</strong> - <?php esc_html_e( 'Community theme', 'wbcom-essential' ); ?></li>
						</ul>
						<a href="https://wbcomdesigns.com/themes/" target="_blank" class="wbcom-btn">
							<?php esc_html_e( 'View Themes', 'wbcom-essential' ); ?>
						</a>
					</div>
				</aside>
		</div>
		<?php
	}

	/**
	 * Get plugin stats
	 */
	private function get_stats() {
		return array(
			'blocks'  => 45,
			'widgets' => 43,
		);
	}
}
