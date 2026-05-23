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

			// Redirect admin after theme switch.
			add_action( 'after_switch_theme', array( $this, 'redirect_admin' ) );

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
			if ( is_admin() && ( isset( $_GET['page'] ) && 'reign-options' == $_GET['page'] ) ) {
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
				header( 'Location:' . admin_url() . 'admin.php?page=reign-options' );
			}
		}

		/**
		 * Include required core files used in admin and on the frontend.
		 */
		public function includes() {
			include_once REIGN_INC_DIR . 'reign-settings/get-started-options.php';
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
							if ( isset( $_GET['updated'] ) && 'true' === esc_attr( $_GET['updated'] ) ) {
								echo '<div class="updated"><p>' . esc_html__( 'Theme Settings updated.', 'reign' ) . '</p></div>';
							}

							$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'get_started';
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
											if ( $pagenow == 'admin.php' && $_GET['page'] == 'reign-options' ) {
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
					<div class="reign-option-info-wrapper reign-addons-info-wrapper">
						<div class="option-wrapper reign-option-support-container">
							<h3 class="option-title"><?php esc_html_e( 'Reign Add-ons', 'reign' ); ?></h3>
							<div class="reign-addon-listing">
								<?php if ( ! class_exists( 'LearnMate_LearnDash_Addon' ) ) : ?>
									<div class="reign-addon-listing-row" data-category="learning">
										<div class="reign-addon-listin-block">
											<h4 class="reign-addon-title"><?php esc_html_e( 'Reign LeanDash', 'reign' ); ?></h4>
											<p class="reign-addon-desc"><?php esc_html_e( 'Reign LearnDash addon provides advanced styling options and layout customization to create visually engaging and user-friendly course pages.', 'reign' ); ?></p>
										</div>
										<div class="reign-addon-listing-btn">
											<a class="option-link" href="<?php echo esc_url( 'https://wbcomdesigns.com/downloads/reign-learndash-addon/' ); ?>" target="_blank">
												<?php esc_html_e( 'Buy Now', 'reign' ); ?>
											</a>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( ! class_exists( 'Reign_Dokan_Addon' ) ) : ?>
									<div class="reign-addon-listing-row" data-category="multivendor">
										<div class="reign-addon-listin-block">
											<h4 class="reign-addon-title"><?php esc_html_e( 'Reign Dokan', 'reign' ); ?></h4>
											<p class="reign-addon-desc"><?php esc_html_e( 'Reign Dokan Addon is a premium plugin optimized for creating WooCommerce online stores that provide a super-fast interface for the ultimate user experience.', 'reign' ); ?></p>
										</div>
										<div class="reign-addon-listing-btn">
											<a class="option-link" href="<?php echo esc_url( 'https://wbcomdesigns.com/downloads/reign-dokan-addon/' ); ?>" target="_blank">
												<?php esc_html_e( 'Buy Now', 'reign' ); ?>
											</a>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( ! class_exists( 'Reign_Wcvendors_Addon' ) ) : ?>
									<div class="reign-addon-listing-row" data-category="multivendor">
										<div class="reign-addon-listin-block">
											<h4 class="reign-addon-title"><?php esc_html_e( 'Reign WC Vendors', 'reign' ); ?></h4>
											<p class="reign-addon-desc"><?php esc_html_e( 'Reign WC Vendors comes with custom designs for Single Store page layout, including a clean & modern WC Vendors Store extra widget.', 'reign' ); ?></p>
										</div>
										<div class="reign-addon-listing-btn">
											<a class="option-link" href="<?php echo esc_url( 'https://wbcomdesigns.com/downloads/reign-wc-vendors-addon/' ); ?>" target="_blank">
												<?php esc_html_e( 'Buy Now', 'reign' ); ?>
											</a>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( ! class_exists( 'Reign_Wcfm_Addon' ) ) : ?>
									<div class="reign-addon-listing-row" data-category="multivendor">
										<div class="reign-addon-listin-block">
											<h4 class="reign-addon-title"><?php esc_html_e( 'Reign WCFM', 'reign' ); ?></h4>
											<p class="reign-addon-desc"><?php esc_html_e( 'Build Run and expand your marketplace with Reign WCFM Addon. This addon ensures you to have each and every functionality of WCFM and WooCommerce.', 'reign' ); ?></p>
										</div>
										<div class="reign-addon-listing-btn">
											<a class="option-link" href="<?php echo esc_url( 'https://wbcomdesigns.com/downloads/reign-wcfm-addon/' ); ?>" target="_blank">
												<?php esc_html_e( 'Buy Now', 'reign' ); ?>
											</a>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( ! class_exists( 'Reign_LifterLMS_Addon' ) ) : ?>
									<div class="reign-addon-listing-row" data-category="learning">
										<div class="reign-addon-listin-block">
											<h4 class="reign-addon-title"><?php esc_html_e( 'Reign LifterLMS', 'reign' ); ?></h4>
											<p class="reign-addon-desc"><?php esc_html_e( 'Reign LifterLMS addon has been designed to enable you to create, sell, and protect engaging online courses in a distraction-free environment.', 'reign' ); ?></p>
										</div>
										<div class="reign-addon-listing-btn">
											<a class="option-link" href="<?php echo esc_url( 'https://wbcomdesigns.com/downloads/reign-lifterlms-addon/' ); ?>" target="_blank">
												<?php esc_html_e( 'Buy Now', 'reign' ); ?>
											</a>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( ! class_exists( 'Reign_Sensei_Addon' ) ) : ?>
									<div class="reign-addon-listing-row" data-category="learning">
										<div class="reign-addon-listin-block">
											<h4 class="reign-addon-title"><?php esc_html_e( 'Reign Sensei', 'reign' ); ?></h4>
											<p class="reign-addon-desc"><?php esc_html_e( 'If you are looking to earn via an online course marketplace like Udemy. Reign Sensei add-on is just the perfect match for you.', 'reign' ); ?></p>
										</div>
										<div class="reign-addon-listing-btn">
											<a class="option-link" href="<?php echo esc_url( 'https://wbcomdesigns.com/downloads/reign-sensei-addon/' ); ?>" target="_blank">
												<?php esc_html_e( 'Buy Now', 'reign' ); ?>
											</a>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( ! class_exists( 'Reign_Tutorlms_Addon' ) ) : ?>
									<div class="reign-addon-listing-row" data-category="learning">
										<div class="reign-addon-listin-block">
											<h4 class="reign-addon-title"><?php esc_html_e( 'Reign Tutor LMS', 'reign' ); ?></h4>
											<p class="reign-addon-desc"><?php esc_html_e( 'Create and Manage your Learning Management System on WordPress using our advanced and feature-packed Reign Tutor Lms Add-on.', 'reign' ); ?></p>
										</div>
										<div class="reign-addon-listing-btn">
											<a class="option-link" href="<?php echo esc_url( 'https://wbcomdesigns.com/downloads/reign-tutor-lms-addon/' ); ?>" target="_blank">
												<?php esc_html_e( 'Buy Now', 'reign' ); ?>
											</a>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( ! class_exists( 'Reign_WP_Job_Manager_Addon' ) ) : ?>
									<div class="reign-addon-listing-row" data-category="other">
										<div class="reign-addon-listin-block">
											<h4 class="reign-addon-title"><?php esc_html_e( 'Reign WP Job Manager', 'reign' ); ?></h4>
											<p class="reign-addon-desc"><?php esc_html_e( 'The plugin has been developed from the ground up to extend WP Job Manager Plugin and all of its extensions.', 'reign' ); ?></p>
										</div>
										<div class="reign-addon-listing-btn">
											<a class="option-link" href="<?php echo esc_url( 'https://wbcomdesigns.com/downloads/reign-wp-job-manager-addon/' ); ?>" target="_blank">
												<?php esc_html_e( 'Buy Now', 'reign' ); ?>
											</a>
										</div>
									</div>
								<?php endif; ?>
								<?php if ( ! class_exists( 'Reign_Geodirectory_Addon' ) ) : ?>
									<div class="reign-addon-listing-row" data-category="other">
										<div class="reign-addon-listin-block">
											<h4 class="reign-addon-title"><?php esc_html_e( 'Reign GeoDirectory', 'reign' ); ?></h4>
											<p class="reign-addon-desc"><?php esc_html_e( 'Reign Geo Directory Add-On offers a solution for site owners to have maps for their directories.', 'reign' ); ?></p>
										</div>
										<div class="reign-addon-listing-btn">
											<a class="option-link" href="<?php echo esc_url( 'https://wbcomdesigns.com/downloads/reign-geodirectory-addon/' ); ?>" target="_blank">
												<?php esc_html_e( 'Buy Now', 'reign' ); ?>
											</a>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
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
			if ( ( ! isset( $_GET['page'] ) ) || ( 'reign-options' !== $_GET['page'] ) ) {
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
