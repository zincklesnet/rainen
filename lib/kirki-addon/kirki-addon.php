<?php
/**
 * Wbcom Kirki Theme Customizer
 *
 * @package Reign_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Wbcom_Kirki_Theme_Customizer' ) ) :

	/**
	 * Main Wbcom_Kirki_Theme_Customizer Class.
	 *
	 * @class Wbcom_Kirki_Theme_Customizer
	 * @version 1.0.0
	 */
	class Wbcom_Kirki_Theme_Customizer {

		/**
		 * Wbcom_Kirki_Theme_Customizer version.
		 *
		 * @var string
		 */
		public $version = '1.0.0';

		/**
		 * The single instance of the class.
		 *
		 * @var Wbcom_Kirki_Theme_Customizer
		 * @since 1.0.0
		 */
		protected static $_instance = null;

		/**
		 * Main Wbcom_Kirki_Theme_Customizer Instance.
		 *
		 * Ensures only one instance of Wbcom_Kirki_Theme_Customizer is loaded or can be loaded.
		 *
		 * @since 1.0.0
		 * @static
		 * @see INSTANTIATE_Wbcom_Kirki_Theme_Customizer()
		 * @return Wbcom_Kirki_Theme_Customizer - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Wbcom_Kirki_Theme_Customizer Constructor.
		 */
		public function __construct() {
			// Use dynamic version if available
			$this->version = defined( 'REIGN_THEME_VERSION' ) ? REIGN_THEME_VERSION : '1.0.0';
			
			$this->init_hooks();
			$this->includes();
			do_action( 'reign_kirki_theme_customizer_loaded' );

			// CSS enqueue
			add_action(
				'customize_controls_enqueue_scripts',
				function () {
					$css_file = REIGN_THEME_DIR . '/lib/css/customizer.css';
					$css_uri = get_theme_file_uri( '/lib/css/customizer.css' );
					
					// Only enqueue if file exists
					if ( file_exists( $css_file ) ) {
						$version = filemtime( $css_file ); // Better cache busting
						wp_enqueue_style( 'reign-customizer', $css_uri, '', $version );
					}
				}
			);
		}

		public function includes() {
			// Define all include files with safety checks
			$includes = array(
				'/lib/kirki-addon/inc/class-kirki-installer-section.php',
				'/lib/kirki-addon/general-functions/general-functions.php',
				'/lib/kirki-addon/options/colors/class-reign-kirki-colors.php',
				'/lib/kirki-addon/options/colors/class-reign-kirki-dark-mode.php',
				'/lib/kirki-addon/options/general/class-reign-kirki-site-logo.php',
				'/lib/kirki-addon/options/general/class-reign-kirki-typography.php',
				'/lib/kirki-addon/options/general/class-reign-kirki-site-layout.php',
				'/lib/kirki-addon/options/general/class-reign-kirki-sub-header.php',
				'/lib/kirki-addon/options/general/class-reign-kirki-page-mapping.php',
				'/lib/kirki-addon/options/general/class-reign-kirki-custom-code.php',
				'/lib/kirki-addon/options/general/class-reign-kirki-login-popup.php',
				'/lib/kirki-addon/options/general/class-reign-kirki-site-performance.php',
				'/lib/kirki-addon/options/header/class-reign-kirki-header.php',
				'/lib/kirki-addon/options/wp-login-screen/class-reign-kirki-wp-login-screen.php',
				'/lib/kirki-addon/options/post-types/class-reign-kirki-post-types.php',
				'/lib/kirki-addon/options/footer/class-reign-kirki-footer.php',
				'/lib/kirki-addon/options/extras/class-reign-kirki-plugins-support.php',
				'/lib/kirki-addon/options/extras/class-reign-dropdown-select.php',
			);

			// Include files with existence checks
			foreach ( $includes as $include ) {
				$file_path = REIGN_THEME_DIR . $include;
				if ( file_exists( $file_path ) ) {
					include_once $file_path;
				}
			}

			// Conditionally include BuddyPress and WooCommerce files with safety checks
			if ( class_exists( 'BuddyPress' ) ) {
				$file_path = REIGN_THEME_DIR . '/lib/kirki-addon/options/buddypress/class-reign-kirki-buddypress.php';
				if ( file_exists( $file_path ) ) {
					include_once $file_path;
				}
			}

			if ( class_exists( 'WooCommerce' ) ) {
				$file_path = REIGN_THEME_DIR . '/lib/kirki-addon/options/woocommerce/class-reign-kirki-woocommerce.php';
				if ( file_exists( $file_path ) ) {
					include_once $file_path;
				}
			}
		}

		/**
		 * Hook into actions and filters.
		 *
		 * @since  1.0.0
		 */
		private function init_hooks() {
			// Check Kirki availability (non-intrusive)
			add_action( 'after_setup_theme', array( $this, 'check_kirki_availability' ), 20 );

			/**
			 * Configure Kirki to use the proper URL path.
			 *
			 * Kirki loads some files when in the customizer and therefore needs you to tell it exactly where these files are located.
			 */
			add_filter( 'kirki/config', array( $this, 'reign_kirki_configuration' ) );
		}

		/**
		 * Check if Kirki is available (non-intrusive check)
		 */
		public function check_kirki_availability() {
			if ( ! class_exists( 'Kirki' ) && current_user_can( 'activate_plugins' ) ) {
				add_action( 'admin_notices', array( $this, 'kirki_missing_notice' ) );
			}
		}

		/**
		 * Show admin notice if Kirki is missing (only to admins)
		 */
		public function kirki_missing_notice() {
			if ( ! current_user_can( 'activate_plugins' ) ) {
				return;
			}
			?>
			<div class="notice notice-warning is-dismissible">
				<p>
					<?php
					printf(
						/* translators: 1: Theme name, 2: Kirki plugin name */
						esc_html__( '%1$s recommends %2$s plugin for full customization features.', 'reign' ),
						'<strong>' . esc_html__( 'Reign Theme', 'reign' ) . '</strong>',
						'<strong>' . esc_html__( 'Kirki Customizer Framework', 'reign' ) . '</strong>'
					);
					?>
				</p>
			</div>
			<?php
		}

		public function reign_kirki_configuration() {
			return array( 'url_path' => get_template_directory_uri() . '/lib/kirki/' );
		}
	}

endif;

/**
 * Main instance of Wbcom_Kirki_Theme_Customizer.
 */
Wbcom_Kirki_Theme_Customizer::instance();
