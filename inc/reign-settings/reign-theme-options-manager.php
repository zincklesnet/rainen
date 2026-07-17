<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Theme_Options_Manager' ) ) :

	/**
	 * @class Reign_Theme_Options_Manager
	 */
	class Reign_Theme_Options_Manager {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Theme_Options_Manager
		 */
		protected static $_instance = null;
		protected static $_slug     = 'reign_pages';

		/**
		 * Main Reign_Theme_Options_Manager Instance.
		 *
		 * Ensures only one instance of Reign_Theme_Options_Manager is loaded or can be loaded.
		 *
		 * @return Reign_Theme_Options_Manager - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Theme_Options_Manager Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
			$this->includes();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'admin_menu', array( $this, 'reign_settings_page_init' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'render_vertical_skeleton_scripts' ) );

			// Redirect admin after theme switch. PHP_INT_MAX so every other
			// after_switch_theme handler (e.g. integration defaults setters)
			// runs before the redirect exits.
			add_action( 'after_switch_theme', array( $this, 'redirect_admin' ), PHP_INT_MAX );

			// Hide all admin notices.
			add_action( 'admin_init', array( $this, 'reign_hide_all_admin_notices_from_reign_options' ) );

			// Enqueue block editor assets.
			add_action( 'enqueue_block_editor_assets', array( $this, 'reign_block_editor_styles' ), 1 );
		}

		/**
		 * Hide Notices
		 *
		 * @since 7.3.5
		 * @access public
		 * @return void
		 */
		public function reign_hide_all_admin_notices_from_reign_options() {
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin-page detection.
			if ( is_admin() && isset( $_GET['page'] ) && 'reign-options' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
				remove_all_actions( 'admin_notices' );
				remove_all_actions( 'all_admin_notices' );
			}
		}

		/**
		 * Redirect Admin
		 *
		 * @since 7.3.5
		 * @access public
		 * @return void
		 */
		public function redirect_admin() {
			if ( current_user_can( 'edit_theme_options' ) ) {
				// Core fires 'after_switch_theme' from check_theme_switched() on every
				// load until it can run flush_rewrite_rules() + update_option(
				// 'theme_switched', false ) AFTER the action. Our exit below would kill
				// PHP first, leaving 'theme_switched' set and re-triggering this
				// redirect on every request (ERR_TOO_MANY_REDIRECTS). Mirror core's
				// post-action cleanup before exiting. 'theme_switched_via_customizer'
				// needs no handling - core clears it before firing the action.
				flush_rewrite_rules();
				update_option( 'theme_switched', false );

				wp_safe_redirect( admin_url( 'admin.php?page=reign-options' ) );
				exit;
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {
			include_once REIGN_INC_DIR . 'reign-settings/reign-options-icons.php';
			include_once REIGN_INC_DIR . 'reign-settings/get-started-options.php';
			include_once REIGN_INC_DIR . 'reign-settings/build-more-options.php';
			include_once REIGN_INC_DIR . 'reign-settings/buddy-extender-options.php';
			include_once REIGN_INC_DIR . 'reign-settings/peepso-extender-options.php';
			include_once REIGN_INC_DIR . 'reign-settings/wbcom-support-tab.php';
		}

		public function reign_settings_page_init() {
			// Submenu pages.
			add_submenu_page(
				'reign-settings',
				__( 'Reign Settings', 'reign' ),
				__( 'Reign Settings', 'reign' ),
				'manage_options',
				'reign-options',
				array( $this, 'reign_settings_page' )
			);
		}

		public function reign_settings_page() {
			global $pagenow;
			?>
			<div class="reign-theme-admin-wrap-main">
				<div class="reign-theme-admin-wrap-container">
					<div class="reign-theme-admin-wrap">
						<div id="reign-setting-left-panel-id" class="reign-setting-left-panel">
							<div class="reign-setting-left-header">
								<div class="reign-theme-logo">
									<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/reign-logo.png' ); ?>" alt="<?php esc_attr_e( 'Reign', 'reign' ); ?>" />
								</div>

								<div class="reign-setting-changelog">
									<strong class="theme-version">
										<?php
										/* translators: %s is the theme version. */
										printf( esc_html__( 'v%s', 'reign' ), esc_html( REIGN_THEME_VERSION ) );
										?>
									</strong>
									<span class="reign-change-url"><a href="<?php echo esc_url( 'https://wbcomdesigns.com/product/reign-theme/' ); ?>" target="_blank"><img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/extrnal-link.svg' ); ?>" alt="<?php esc_attr_e( 'Reign', 'reign' ); ?>" /></a></span>
								</div>
							</div>

							<?php
							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Display-only success notice; flag is set by the nonce-checked save redirect.
							if ( isset( $_GET['updated'] ) && 'true' === sanitize_key( wp_unslash( $_GET['updated'] ) ) ) {
								echo '<div class="updated"><p>' . esc_html__( 'Theme Settings updated.', 'reign' ) . '</p></div>';
							}

							// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin tab routing.
							$tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : 'get_started';
							$this->reign_admin_tabs( $tab );
							?>
						</div>

						<div id="reign-theme-admin-content-id" class="reign-theme-admin-content">
							<div class="reign-theme-admin-option-header">
								<button type="button" class="reign-setting-menu-toggle" id="setting-menu-toggle"><span></span></button>
								<?php
								$current_user = wp_get_current_user();
								$avatar       = get_avatar( $current_user->ID, 64 );
								echo '<div class="user-option-header"><span>' . esc_html__( 'Hello, ', 'reign' ) . esc_html( $current_user->display_name ) . '</span>';
								echo wp_kses_post( $avatar );
								echo '</div>';
								?>
							</div>

							<?php do_action( 'render_content_after_form', $tab ); ?>

							<div id="rg-poststuff">
								<div class="rg-poststuff-inner">
									<div class="reign-theme-options-wrapper">
										<div class="reign-settings-loader"></div>
										<form id="reign-theme-options-form" method="post" action="<?php admin_url( 'admin.php?page=reign-options' ); ?>" style="display:none;">
											<?php
												wp_nonce_field( 'reign-options' );
											// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin-page detection.
											if ( 'admin.php' === $pagenow && isset( $_GET['page'] ) && 'reign-options' === sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
												do_action( 'render_theme_options_page_for_' . $tab );
											}
											?>
										</form>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div><!-- .reign-theme-admin-wrap-container -->

				<div class="reign-theme-admin-sidebar">
					<?php do_action( 'reign_options_sidebar' ); ?>
				</div><!-- .reign-theme-admin-sidebar -->

			</div><!-- .reign-theme-admin-wrap-main -->
			<?php
		}

		public function reign_admin_tabs( $current ) {
			$tabs = array();
			$tabs = apply_filters( 'alter_reign_admin_tabs', $tabs );

			echo '<div class="nav-tab-wrapper reign-nav-tab-wrapper"><ul class="nav-tab-wrapper-inner">';
			foreach ( $tabs as $tab => $name ) {
				$class = ( $tab === $current ) ? 'nav-tab-active' : '';
				echo '<li class="' . esc_attr( $class ) . '">';
				echo '<a class="' . esc_attr( strtolower( $name ) ) . ' nav-tab" href="' . esc_url( '?page=reign-options&tab=' . $tab ) . '">';
				echo esc_html( $name );
				echo '</a></li>';
			}
			echo '</ul></div>';
		}

		public function render_vertical_skeleton_scripts() {
			// $screen = get_current_screen();
			// if ( $screen->id != 'reign-settings_page_reign-options' ) {
			// return;
			// }
			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only admin-page detection for asset loading.
			if ( ( ! isset( $_GET['page'] ) ) || ( 'reign-options' !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) ) {
				return;
			}

			wp_register_script(
				$handle    = 'reign_vertical_tabs_skeleton_js',
				$src       = get_template_directory_uri() . '/assets/js/vertical-tabs-skeleton.min.js',
				$deps      = array( 'jquery' ),
				$ver       = REIGN_THEME_VERSION,
				$in_footer = true
			);

			$wb_social_links_html = '';
			ob_start();
			?>
			<div class="wbtm_social_links_container">
				<div class="wbtm_social_link_section">
					<h3 class="wbtm_social_link_toggle_head">
						<?php esc_html_e( 'New Site', 'reign' ); ?><span class="required">*</spam>
					</h3>
					<div class="wbtm_social_link_info_box">
						<div class="img_section">
							<?php if ( class_exists( 'PeepSo' ) ) { ?>
								<input class="reign_default_cover_image_url" type="hidden" name="reign_peepsoextender[wbtm_social_links][{{unique_key}}][img_url]" value="
								<?php
								if ( isset( $social_link['img_url'] ) ) {
									echo esc_url( $social_link['img_url'] );
								}
								?>
								" required="required" />
								<?php } else { ?>
									<input class="reign_default_cover_image_url" type="hidden" name="reign_buddyextender[wbtm_social_links][{{unique_key}}][img_url]" value="<?php echo isset( $social_link['img_url'] ) ? esc_url( $social_link['img_url'] ) : ''; ?>" required="required" />
							<?php } ?>
							<img class="reign_default_cover_image" src="
							<?php
							if ( isset( $social_link['img_url'] ) ) {
								echo esc_url( $social_link['img_url'] );
							}
							?>
							" alt="" style="display: none;" />
							
							<input id="reign-upload-button" type="button" class="button reign-upload-button" value="<?php esc_attr_e( 'Upload Icon', 'reign' ); ?>" />
							<a href="#" class="reign-remove-file-button" rel="avatar_default_image" style="display: none;" >
								<?php esc_html_e( 'Remove Icon', 'reign' ); ?>
							</a>
						</div>
						<div class="name_section">
							<?php if ( class_exists( 'PeepSo' ) ) { ?>
								<input type="text" class="wbtm-social-link-inp" name="reign_peepsoextender[wbtm_social_links][{{unique_key}}][name]" placeholder="<?php esc_attr_e( 'New Site', 'reign' ); ?>" required="required" />
							<?php } else { ?>
								<input type="text" class="wbtm-social-link-inp" name="reign_buddyextender[wbtm_social_links][{{unique_key}}][name]" placeholder="<?php esc_attr_e( 'New Site', 'reign' ); ?>" required="required" />
							<?php } ?>
						</div>
						<div class="del_section">
							<button><?php esc_html_e( 'Delete', 'reign' ); ?></button>
						</div>
					</div>
				</div>
			</div>
			<?php
			$wb_social_links_html = ob_get_clean();
			wp_localize_script(
				'reign_vertical_tabs_skeleton_js',
				'reign_vertical_tabs_skeleton_js_params',
				array(
					'ajax_url'                => admin_url( 'admin-ajax.php' ),
					'home_url'                => get_home_url(),
					'wb_social_links_html'    => $wb_social_links_html,
					'wb_social_links_default' => __( 'New Site', 'reign' ),
				)
			);
			wp_enqueue_script( 'reign_vertical_tabs_skeleton_js' );

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			if ( ! wp_script_is( 'jquery-ui-accordion', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery-ui-accordion' );
			}
			if ( ! wp_script_is( 'jquery-ui-sortable', 'enqueued' ) ) {
				wp_enqueue_script( 'jquery-ui-sortable' );
			}

			$rtl_css = is_rtl() ? '-rtl' : '';

			wp_register_style(
				$handle = 'reign-vertical-tabs-skeleton-css',
				$src    = get_template_directory_uri() . '/assets/css' . $rtl_css . '/vertical-tabs-skeleton.min.css',
				$deps   = array(),
				$ver    = REIGN_THEME_VERSION,
				$media  = 'all'
			);
			wp_enqueue_style( 'reign-vertical-tabs-skeleton-css' );

			wp_enqueue_style(
				'reign-options-ui',
				get_template_directory_uri() . '/assets/css/reign-options-ui.css',
				array(),
				REIGN_THEME_VERSION
			);
			wp_style_add_data( 'reign-options-ui', 'rtl', 'replace' );

			wp_enqueue_script(
				'reign-get-started',
				get_template_directory_uri() . '/assets/js/reign-get-started.js',
				array(),
				REIGN_THEME_VERSION,
				true
			);
			wp_localize_script(
				'reign-get-started',
				'ReignGetStarted',
				array(
					'ajaxurl' => admin_url( 'admin-ajax.php' ),
					'nonce'   => wp_create_nonce( 'reign_install_plugin' ),
					'i18n'    => array(
						'installing'         => __( 'Installing…', 'reign' ),
						'active'             => __( 'Active', 'reign' ),
						'essentialInstalled' => __( 'Wbcom Essential installed', 'reign' ),
						'failed'             => __( 'Install failed. Please try again.', 'reign' ),
					),
				)
			);
		}

		/**
		 * Block editor styles.
		 */
		public function reign_block_editor_styles() {
			$rtl_css = is_rtl() ? '-rtl' : '';

			wp_enqueue_style( 'reign-block-editor-styles', get_template_directory_uri() . '/assets/css' . $rtl_css . '/style-editor-block.min.css', '', REIGN_THEME_VERSION );
		}
	}

	endif;

/**
 * Main instance of Reign_Theme_Options_Manager.
 *
 * @return Reign_Theme_Options_Manager
 */
Reign_Theme_Options_Manager::instance();
?>
