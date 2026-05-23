<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Wbcom_Support_Tab' ) ) :

	/**
	 * @class Reign_Wbcom_Support_Tab
	 */
	class Reign_Wbcom_Support_Tab {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Wbcom_Support_Tab
		 */
		protected static $_instance = null;
		protected static $_slug     = 'wbcom-support';

		/**
		 * Main Reign_Wbcom_Support_Tab Instance.
		 *
		 * Ensures only one instance of Reign_Wbcom_Support_Tab is loaded or can be loaded.
		 *
		 * @return Reign_Wbcom_Support_Tab - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Wbcom_Support_Tab Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_filter( 'alter_reign_admin_tabs', array( $this, 'alter_reign_admin_tabs' ), 50, 1 );
			add_action( 'render_content_after_form', array( $this, 'render_get_started_with_customization_section' ), 10, 1 );

			add_action( 'admin_menu', array( $this, 'add_reign_setting_submenu' ), 50 );
		}

		public function add_reign_setting_submenu() {
			add_submenu_page(
				'reign-settings',
				__( 'Support', 'reign' ),
				__( 'Support', 'reign' ),
				'manage_options',
				admin_url( 'admin.php?page=reign-options&tab=' . self::$_slug )
			);
		}

		public function alter_reign_admin_tabs( $tabs ) {
			$tabs[ self::$_slug ] = __( 'Support', 'reign' );
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
			<div class="reign_support_faq">
				<div class="reign_support_faq_links">
					<div class="reign-support-faq-inner support-doc-links-wrapper">
						<div class="reign-option-boxes">
							<div class="reign-option-box">
								<div class="option-wrapper">
									<div class="option-icon">
										<svg width="20px" height="20px" viewBox="0 0 24 24" fill="#1d76da">
											<path fill-rule="evenodd" clip-rule="evenodd" d="M12 1.25C6.06294 1.25 1.25 6.06294 1.25 12C1.25 17.9371 6.06294 22.75 12 22.75C17.9371 22.75 22.75 17.9371 22.75 12C22.75 6.06294 17.9371 1.25 12 1.25ZM2.75 12C2.75 9.71567 3.57804 7.62475 4.95034 6.011L8.1528 9.21346C7.58488 9.99619 7.25 10.959 7.25 12C7.25 13.041 7.58488 14.0038 8.1528 14.7865L4.95033 17.989C3.57804 16.3753 2.75 14.2843 2.75 12ZM9.21346 8.1528L6.011 4.95034C7.62475 3.57804 9.71567 2.75 12 2.75C14.2843 2.75 16.3753 3.57804 17.989 4.95034L14.7865 8.1528C14.0038 7.58488 13.041 7.25 12 7.25C10.959 7.25 9.99619 7.58488 9.21346 8.1528ZM6.01099 19.0497C7.62474 20.422 9.71567 21.25 12 21.25C14.2843 21.25 16.3753 20.422 17.989 19.0497L14.7865 15.8472C14.0038 16.4151 13.041 16.75 12 16.75C10.959 16.75 9.99619 16.4151 9.21346 15.8472L6.01099 19.0497ZM15.8472 14.7865L19.0497 17.989C20.422 16.3753 21.25 14.2843 21.25 12C21.25 9.71567 20.422 7.62475 19.0497 6.011L15.8472 9.21346C16.4151 9.99619 16.75 10.959 16.75 12C16.75 13.041 16.4151 14.0038 15.8472 14.7865ZM8.75 12C8.75 10.2051 10.2051 8.75 12 8.75C13.7949 8.75 15.25 10.2051 15.25 12C15.25 13.7949 13.7949 15.25 12 15.25C10.2051 15.25 8.75 13.7949 8.75 12Z" fill="#1d76da"/>
										</svg>
									</div>
									<div class="options-content-wrapper">
										<h3 class="option-title"><?php esc_html_e( 'Need Some Help?', 'reign' ); ?></h3>
										<p class="option-desc"><?php esc_html_e( 'We\'re here to help! Our support team is ready to assist with any questions you have about your Reign theme.', 'reign' ); ?></p>
									</div>
									<div class="option-link-area">
										<a class="option-link" href="https://wbcomdesigns.com/support/" target="_blank"><?php esc_html_e( 'Support', 'reign' ); ?></a>
									</div>
								</div>
							</div>
							<div class="reign-option-box">
								<div class="option-wrapper">
									<div class="option-icon">
										<svg width="20px" height="20px" viewBox="0 0 24 24" fill="none">
											<path d="M15.5 9L15.6716 9.17157C17.0049 10.5049 17.6716 11.1716 17.6716 12C17.6716 12.8284 17.0049 13.4951 15.6716 14.8284L15.5 15" stroke="#1d76da" stroke-width="1.5" stroke-linecap="round"/>
											<path d="M13.2942 7.17041L12.0001 12L10.706 16.8297" stroke="#1d76da" stroke-width="1.5" stroke-linecap="round"/>
											<path d="M8.49994 9L8.32837 9.17157C6.99504 10.5049 6.32837 11.1716 6.32837 12C6.32837 12.8284 6.99504 13.4951 8.32837 14.8284L8.49994 15" stroke="#1d76da" stroke-width="1.5" stroke-linecap="round"/>
											<path d="M7 3.33782C8.47087 2.48697 10.1786 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 10.1786 2.48697 8.47087 3.33782 7" stroke="#1d76da" stroke-width="1.5" stroke-linecap="round"/>
										</svg>
									</div>
									<div class="options-content-wrapper">
										<h3 class="option-title"><?php esc_html_e( 'Looking for additional Features?', 'reign' ); ?></h3>
										<p class="option-desc"><?php esc_html_e( 'Need custom WordPress or BuddyPress solutions? Our team can help bring your ideas to life!', 'reign' ); ?></p>
									</div>
									<div class="option-link-area">
										<a class="option-link" href="https://wbcomdesigns.com/start-a-project/" target="_blank"><?php esc_html_e( 'Hire us!', 'reign' ); ?></a>
									</div>
								</div>
							</div>
							<div class="reign-option-box">
								<div class="option-wrapper">
									<div class="option-icon">
										<svg width="20" height="20" viewBox="0 0 20 20" fill="#1d76da">
											<path d="M18,2h-2v16h2c1.1,0,2-0.9,2-2V4C20,2.9,19.1,2,18,2z"></path><path d="M13.1,0H1.9C0.8,0,0,0.9,0,2v16c0,1.1,0.8,2,1.9,2h11.2c1,0,1.9-0.9,1.9-2V2C15,0.9,14.2,0,13.1,0zM13,16c0,0.5-0.5,1-1,1H3c-0.5,0-1-0.5-1-1v-2c0-0.5,0.5-1,1-1h9c0.5,0,1,0.5,1,1V16zM12.5,11h-10C2.2,11,2,10.8,2,10.5C2,10.2,2.2,10,2.5,10h10c0.3,0,0.5,0.2,0.5,0.5C13,10.8,12.8,11,12.5,11z M12.5,8h-10C2.2,8,2,7.8,2,7.5C2,7.2,2.2,7,2.5,7h10C12.8,7,13,7.2,13,7.5C13,7.8,12.8,8,12.5,8zM12.5,5h-10C2.2,5,2,4.8,2,4.5C2,4.2,2.2,4,2.5,4h10C12.8,4,13,4.2,13,4.5C13,4.8,12.8,5,12.5,5z"></path>
										</svg>
									</div>
									<div class="options-content-wrapper">
										<h3 class="option-title"><?php esc_html_e( 'Documentation', 'reign' ); ?></h3>
										<p class="option-desc"><?php esc_html_e( 'Find step-by-step guides on installation, customization, and getting the most from your Reign theme.', 'reign' ); ?></p>
									</div>
									<div class="option-link-area">
										<a class="option-link" href="https://docs.wbcomdesigns.com/doc_category/wb-reign-theme/getting-started/" target="_blank"><?php esc_html_e( 'Start Reading', 'reign' ); ?></a>
									</div>
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
 * Main instance of Reign_Wbcom_Support_Tab.
 *
 * @return Reign_Wbcom_Support_Tab
 */
Reign_Wbcom_Support_Tab::instance();
