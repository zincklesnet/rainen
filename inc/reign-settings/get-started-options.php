<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Get_Started_Options' ) ) :

	/**
	 * @class Reign_Get_Started_Options
	 */
	class Reign_Get_Started_Options {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Get_Started_Options
		 */
		protected static $_instance = null;
		protected static $_slug     = 'get_started';

		/**
		 * Main Reign_Get_Started_Options Instance.
		 *
		 * Ensures only one instance of Reign_Get_Started_Options is loaded or can be loaded.
		 *
		 * @return Reign_Get_Started_Options - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Get_Started_Options Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_filter( 'alter_reign_admin_tabs', array( $this, 'alter_reign_admin_tabs' ), 10, 1 );
			add_action( 'render_content_after_form', array( $this, 'render_get_started_with_customization_section' ), 10, 1 );
		}

		public function alter_reign_admin_tabs( $tabs ) {
			$tabs[ self::$_slug ] = __( 'Getting Started', 'reign' );
			return $tabs;
		}

		public function render_get_started_with_customization_section( $tab ) {
			if ( $tab != self::$_slug ) {
				return;
			}

			?>
			<style type="text/css">
				div#rg-poststuff {
					display: none;
				}
			</style>
			<?php
			$theme_options_quick_links               = array();
			$theme_options_quick_links['site_logo']  = array(
				'option_title' => __( 'Upload Logo', 'reign' ),
				'option_desc'  => __( 'Add your own logo here.', 'reign' ),
				'link_title'   => __( 'Go to option', 'reign' ),
				'link_url'     => esc_url( admin_url( 'customize.php?autofocus[control]=custom_logo&return=' . admin_url( 'admin.php?page=reign-options' ) ) ),
			);
			$theme_options_quick_links['typography'] = array(
				'option_title' => __( 'Set Typography', 'reign' ),
				'option_desc'  => __( 'Choose your own typography for any parts of your website.', 'reign' ),
				'link_title'   => __( 'Go to option', 'reign' ),
				'link_url'     => esc_url( admin_url( 'customize.php?autofocus[section]=reign_typography&return=' . admin_url( 'admin.php?page=reign-options' ) ) ),
			);

			$theme_options_quick_links['page_mapping'] = array(
				'option_title' => __( 'Page Mapping', 'reign' ),
				'option_desc'  => __( 'Map login, register and 404 page with custom pages.', 'reign' ),
				'link_title'   => __( 'Go to option', 'reign' ),
				'link_url'     => esc_url( admin_url( 'customize.php?autofocus[section]=reign_page_mapping&return=' . admin_url( 'admin.php?page=reign-options' ) ) ),
			);

			$theme_options_quick_links['colors'] = array(
				'option_title' => __( 'Color Options', 'reign' ),
				'option_desc'  => __( 'Replace the default primary and hover color by your own colors.', 'reign' ),
				'link_title'   => __( 'Go to option', 'reign' ),
				'link_url'     => esc_url( admin_url( 'customize.php?autofocus[section]=colors&return=' . admin_url( 'admin.php?page=reign-options' ) ) ),
			);

			$theme_options_quick_links['site_header'] = array(
				'option_title' => __( 'Header Customization', 'reign' ),
				'option_desc'  => __( 'Manage the look of your header in all way possible.', 'reign' ),
				'link_title'   => __( 'Go to option', 'reign' ),
				'link_url'     => esc_url( admin_url( 'customize.php?autofocus[panel]=reign_header_panel&return=' . admin_url( 'admin.php?page=reign-options' ) ) ),
			);

			$theme_options_quick_links['site_footer'] = array(
				'option_title' => __( 'Footer Customization', 'reign' ),
				'option_desc'  => __( 'Manage the copyright text, widgets and colors for footer.', 'reign' ),
				'link_title'   => __( 'Go to option', 'reign' ),
				'link_url'     => esc_url( admin_url( 'customize.php?autofocus[panel]=reign_footer_panel&return=' . admin_url( 'admin.php?page=reign-options' ) ) ),
			);

			$theme_options_quick_links = apply_filters( 'reign_alter_theme_options_quick_links', $theme_options_quick_links );

			?>
			<div class="reign-option-section">
				<div class="reign-option-info-wrapper">
					<div class="reign-option-info">
						<h2><?php esc_html_e( 'General Settings', 'reign' ); ?></h2>
						<span><a class="option-link customizer-redirect-link" href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php esc_html_e( 'Go to Customizer', 'reign' ); ?></a></span>
					</div>
					<div class="reign-option-boxes">
						<?php
						foreach ( $theme_options_quick_links as $key => $theme_option ) {
							?>
							<div class="reign-option-box">
								<div class="option-wrapper">
									<div class="reign-option-row">
										<h3 class="option-title"><?php echo esc_html( $theme_option['option_title'] ); ?></h3>
										<p class="option-desc"><?php echo esc_html( $theme_option['option_desc'] ); ?></p>
									</div>
									<div class="option-link-area">
										<a class="option-link" href="<?php echo esc_url( $theme_option['link_url'] ); ?>"><?php echo esc_html( $theme_option['link_title'] ); ?></a>
									</div>
								</div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
				<div class="reign-option-info-wrapper reign-option-downloads-wrapper">
					<div class="reign-option-info">
						<h2><?php esc_html_e( 'Recommended Downloads', 'reign' ); ?></h2>
						<p><?php esc_html_e( 'The Install Recommended Plugins feature simplifies the addition of essential add-ons that enhance your website\'s functionality and features.', 'reign' ); ?></p>
					</div>
					<div class="reign-option-boxes">
						<div class="reign-option-box">
							<div class="option-wrapper">	
								<div class="reign-option-row">
									<h3 class="option-title"><?php esc_html_e( 'Install Recommended Plugins', 'reign' ); ?></h3>
									<p class="option-desc"><?php esc_html_e( 'Enhancing your website\'s functionality with WordPress plugins is easy. You can install, activate, and begin using them in a matter of minutes.', 'reign' ); ?></p>
								</div>
								<div class="option-link-area">
									<?php
									if ( class_exists( 'BuddyPress' ) ) {
										if ( class_exists( 'Buddypress_Share' ) && class_exists( 'Buddypress_Reactions' ) && function_exists( 'wbcom_essential' ) ) {
											?>
											<a class="option-link all-plugin-installed" href="javascript:void(0)"><?php esc_html_e( 'Installed', 'reign' ); ?></a>
										<?php } else { ?>
											<a class="option-link" href="<?php echo esc_url( admin_url() . 'admin.php?page=install-required-plugins' ); ?>" target="_blank"><?php esc_html_e( 'Install now', 'reign' ); ?></a>
											<?php
										}
									} elseif ( function_exists( 'wbcom_essential' ) ) {
										?>
										<a class="option-link all-plugin-installed" href="javascript:void(0)"><?php esc_html_e( 'Installed', 'reign' ); ?></a>
									<?php } else { ?>
										<a class="option-link" href="<?php echo esc_url( admin_url() . 'admin.php?page=install-required-plugins' ); ?>" target="_blank"><?php esc_html_e( 'Install now', 'reign' ); ?></a>
										<?php
									}
									?>
								</div>
							</div>
						</div>
						<div class="reign-option-box">
							<div class="option-wrapper">	
								<div class="reign-option-row">
									<h3 class="option-title"><?php esc_html_e( 'Reign Child Theme', 'reign' ); ?></h3>
									<p class="option-desc"><?php esc_html_e( 'By using a child theme, you can modify any file without fear of breaking something in the parent theme.', 'reign' ); ?></p>
								</div>
								<div class="option-link-area">
									<a class="option-link" href="<?php echo esc_url( 'https://github.com/wbcomdesigns/reign-child-theme/releases/download/4.0.0/reign-child-theme.zip' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Download now', 'reign' ); ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>


				<div class="reign-option-info-wrapper reign-option-need-help-wrapper">
					<div class="reign-option-info-wrapper need-help-advice">
						<div class="reign-option-support-container">
							<h3 class="option-title"><?php esc_html_e( 'Need help or advice?', 'reign' ); ?></h3>
							<p class="option-desc"><?php esc_html_e( 'Got a question or need help with the theme? You can always submit a support ticket or ask for help in our friendly Facebook community.', 'reign' ); ?></p>
							<div class="option-link-area">
								<a class="option-link" href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" target="_blank"><?php esc_html_e( 'Submit a Support Ticket', 'reign' ); ?></a>
								<a class="option-link reign-facebook-community" href="<?php echo esc_url( 'https://www.facebook.com/groups/191523257634994' ); ?>" target="_blank"><?php esc_html_e( 'Join Facebook Community', 'reign' ); ?></a>
							</div>						
						</div>
					</div>
				</div>

				<div class="reign-option-info-wrapper reign-option-tutorials-wrapper">
					<div class="reign-option-boxes">
						<div class="reign-option-box reign-theme-videos">
							<div class="reign-theme-video">
								<div class="video-container">
									<iframe src="https://www.youtube.com/embed/Gep2E7YhW8g" title="Reign BuddyPress Theme - Demo Setup 2023 - Create Social Community Website in 10 Minutes" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
								</div>
							</div>

							<div class="option-wrapper">
								<div class="reign-option-row">
									<h3 class="option-title"><?php esc_html_e( 'Reign Theme Video Tutorials', 'reign' ); ?></h3>
									<p class="option-desc"><?php esc_html_e( 'A theme video series is a collection of short videos that visually and concisely guide users through various aspects of a specific theme or template, often used for websites or applications. These videos offer step-by-step instructions on installation, customization, and troubleshooting, enhancing users\' understanding and facilitating a smoother experience with the theme. This multimedia approach to documentation provides a dynamic and engaging way for users to master the theme\'s features and create stunning online platforms.', 'reign' ); ?></p>
								</div>
								<div class="option-link-area">
									<a class="option-link" href="<?php echo esc_url( 'https://www.youtube.com/watch?v=Gep2E7YhW8g&list=PLlkJGdi68l-9eWBbEwNFUQciw15x4bR5n&ab_channel=WbcomDesigns' ); ?>" target="_blank"><?php esc_html_e( 'Watch now', 'reign' ); ?></a>
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div>

			<?php
		}
	}

	endif;

/**
 * Main instance of Reign_Get_Started_Options.
 *
 * @return Reign_Get_Started_Options
 */
Reign_Get_Started_Options::instance();
