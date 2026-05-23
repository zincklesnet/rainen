<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Quotes
 * @subpackage Buddypress_Quotes/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buddypress_Quotes
 * @subpackage Buddypress_Quotes/admin
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Quotes_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of this plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Quotes_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Quotes_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( ! isset( $_GET['page'] ) ) { // phpcs:ignore
			return;
		}

		$rtl_css = is_rtl() ? '-rtl' : '';

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$css_extension = '.css';
		} else {
			$css_extension = '.min.css';
		}

		if ( isset( $_GET['page'] ) && 'bp-activity' === $_GET['page']  ) {	// phpcs:ignore
			wp_enqueue_style( $this->plugin_name, BPQUOTES_PLUGIN_URL . 'public/css' . $rtl_css . '/buddypress-quotes-public' . $css_extension, array(), $this->version, 'all' );
		}

		if ( isset( $_GET['page'] ) && 'buddypress-quotes' === $_GET['page'] || 'wbcom-plugins-page'=== $_GET['page'] || 'wbcomplugins' === $_GET['page'] ) {	// phpcs:ignore
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css' . $rtl_css . '/buddypress-quotes-admin' . $css_extension, array(), $this->version, 'all' );
			if ( ! wp_style_is( 'font-awesome', 'enqueued' ) ) {
				wp_enqueue_style( 'font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css', array(), $this->version, 'all' );
			}

			wp_enqueue_style( 'selectize-css', plugin_dir_url( __FILE__ ) . 'css/vendor/selectize.css', array(), $this->version, 'all' );

			wp_enqueue_style( 'wp-color-picker' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Quotes_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Quotes_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( isset( $_GET['page'] ) && 'buddypress-quotes' === $_GET['page'] ) {	// phpcs:ignore

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$js_extension = '.js';
			} else {
				$js_extension = '.min.js';
			}

			if ( ! wp_script_is( 'jquery-ui-sortable' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}
			wp_enqueue_media();
			wp_enqueue_script( 'selectize-js', plugin_dir_url( __FILE__ ) . 'js/vendor/selectize.min.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/buddypress-quotes-admin' . $js_extension, array( 'jquery', 'wp-color-picker' ), $this->version, true );

		}

	}

	/**
	 * Remove admin notices when the current page is plugin wrapper page.
	 *
	 * @since    1.0.0
	 */
	public function wbcom_hide_all_admin_notices_from_setting_page() {
		$wbcom_pages_array  = array( 'wbcomplugins', 'wbcom-plugins-page', 'wbcom-support-page', 'buddypress-quotes' );
		$wbcom_setting_page = filter_input( INPUT_GET, 'page' ) ? filter_input( INPUT_GET, 'page' ) : '';

		if ( in_array( $wbcom_setting_page, $wbcom_pages_array, true ) ) {
			remove_all_actions( 'admin_notices' );
			remove_all_actions( 'all_admin_notices' );
		}
	}

	/**
	 * Register admin menu for plugin.
	 *
	 * @since    1.0.0
	 */
	public function bpquotes_add_admin_menu() {

		if ( empty( $GLOBALS['admin_page_hooks']['wbcomplugins'] ) ) {

			add_menu_page( esc_html__( 'WB Plugins', 'buddypress-quotes' ), esc_html__( 'WB Plugins', 'buddypress-quotes' ), 'manage_options', 'wbcomplugins', array( $this, 'bpquotes_settings_page' ), 'dashicons-lightbulb', 59 );
			add_submenu_page( 'wbcomplugins', esc_html__( 'General', 'buddypress-quotes' ), esc_html__( 'General', 'buddypress-quotes' ), 'manage_options', 'wbcomplugins' );
		}
		add_submenu_page( 'wbcomplugins', esc_html__( 'BuddyPress Quotes Settings Page', 'buddypress-quotes' ), esc_html__( 'Quotes', 'buddypress-quotes' ), 'manage_options', 'buddypress-quotes', array( $this, 'bpquotes_settings_page' ) );
	}

	/**
	 * BuddyPress Quote Admin Setting.
	 */
	public function bpquotes_settings_page() {
		$current = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'welcome';	// phpcs:ignore
		?>

		<div class="wrap">
			<div class="wbcom-bb-plugins-offer-wrapper">
				<div id="wb_admin_logo">
				</div>
			</div>
			<div class="wbcom-wrap">
				<div class="blpro-header">
					<div class="wbcom_admin_header-wrapper">
						<div id="wb_admin_plugin_name">							
							<?php esc_html_e( 'BuddyPress Quotes', 'buddypress-quotes' ); ?>
							<span><?php printf( esc_html__( 'Version %s', 'buddypress-quotes' ), esc_html( BPQUOTES_NAME_VERSION ) ); ?></span>
						</div>
							<?php echo do_shortcode( '[wbcom_admin_setting_header]' ); ?>
					</div>
				</div>
				<?php 
				// Display settings save notice
				if (isset($_GET['settings-updated']) && $_GET['settings-updated']) {	// phpcs:ignore
					add_settings_error('bpquotes_messages', 'bpquotes_message', __('Settings saved.', 'buddypress-quotes'), 'updated');
				}

				settings_errors('bpquotes_messages'); // Display notices
				?>
			<div class="wbcom-admin-settings-page">
		<?php
		$bpquotes_tabs = array(
			'welcome' => esc_html__( 'Welcome', 'buddypress-quotes' ),
			'general' => esc_html__( 'General', 'buddypress-quotes' ),
		);

		$tab_html = '<div class="wbcom-tabs-section"><div class="nav-tab-wrapper"><div class="wb-responsive-menu"><span>' . esc_html( 'Menu' ) . '</span><input class="wb-toggle-btn" type="checkbox" id="wb-toggle-btn"><label class="wb-toggle-icon" for="wb-toggle-btn"><span class="wb-icon-bars"></span></label></div><ul>';
		foreach ( $bpquotes_tabs as $bpquotes_tab => $bpquotes_name ) {
			$class     = ( $bpquotes_tab === $current ) ? 'nav-tab-active' : '';
			$tab_html .= '<li class="' . $bpquotes_name . '"><a class="nav-tab ' . $class . '" href="admin.php?page=buddypress-quotes&tab=' . $bpquotes_tab . '">' . $bpquotes_name . '</a></li>';
		}
		$tab_html .= '</div></ul></div>';
		echo wp_kses_post( $tab_html );
		include 'inc/bpquotes-tabs-options.php';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Register Setting.
	 */
	public function bpquotes_add_admin_register_setting() {
		register_setting( 'bpquotes_gnrl_settings_section', 'bpquotes_gnrl_settings' );
	}

}
