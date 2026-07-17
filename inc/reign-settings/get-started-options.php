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

		/**
		 * Customizer quick-link tiles shown in the "Customize your site" block.
		 *
		 * @return array
		 */
		private function get_quick_links() {
			$return_url = admin_url( 'admin.php?page=reign-options' );

			$quick_links = array(
				'site_logo'    => array(
					'option_title' => __( 'Upload Logo', 'reign' ),
					'option_desc'  => __( 'Add your own logo to the header.', 'reign' ),
					'icon'         => 'image',
					'link_url'     => admin_url( 'customize.php?autofocus[control]=custom_logo&return=' . $return_url ),
				),
				'typography'   => array(
					'option_title' => __( 'Typography', 'reign' ),
					'option_desc'  => __( 'Pick fonts for any part of your site.', 'reign' ),
					'icon'         => 'type',
					'link_url'     => admin_url( 'customize.php?autofocus[section]=reign_typography&return=' . $return_url ),
				),
				'colors'       => array(
					'option_title' => __( 'Colors', 'reign' ),
					'option_desc'  => __( 'Set your primary and hover colors.', 'reign' ),
					'icon'         => 'droplet',
					'link_url'     => admin_url( 'customize.php?autofocus[section]=colors&return=' . $return_url ),
				),
				'site_header'  => array(
					'option_title' => __( 'Header', 'reign' ),
					'option_desc'  => __( 'Shape the header exactly how you want.', 'reign' ),
					'icon'         => 'panel-top',
					'link_url'     => admin_url( 'customize.php?autofocus[panel]=reign_header_panel&return=' . $return_url ),
				),
				'site_footer'  => array(
					'option_title' => __( 'Footer', 'reign' ),
					'option_desc'  => __( 'Edit copyright, widgets, and colors.', 'reign' ),
					'icon'         => 'panel-bottom',
					'link_url'     => admin_url( 'customize.php?autofocus[panel]=reign_footer_panel&return=' . $return_url ),
				),
				'page_mapping' => array(
					'option_title' => __( 'Page Mapping', 'reign' ),
					'option_desc'  => __( 'Map login, register, and 404 pages.', 'reign' ),
					'icon'         => 'link',
					'link_url'     => admin_url( 'customize.php?autofocus[section]=reign_page_mapping&return=' . $return_url ),
				),
			);

			return apply_filters( 'reign_alter_theme_options_quick_links', $quick_links );
		}

		/**
		 * The Wbcom community plugin family promoted on the Getting Started screen.
		 *
		 * Each plugin works standalone; `active` toggles the card to a status pill.
		 *
		 * @return array
		 */
		private function get_family_products() {
			return array(
				array(
					'name' => __( 'BuddyNext', 'reign' ),
					'slug' => 'buddynext', // Flagship - store link, not one-click installable.
					'logo' => 'buddynext.svg',
					'desc' => __( 'Community engine: profiles, activity feeds, and member spaces.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/buddynext/',
				),
				array(
					'name' => __( 'WB Gamification', 'reign' ),
					'slug' => 'wb-gamification',
					'logo' => 'wb-gamification.svg',
					'desc' => __( 'Points, badges, levels, and leaderboards for any activity.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/wordpress-gamification-plugin/',
				),
				array(
					'name' => __( 'Jetonomy', 'reign' ),
					'slug' => 'jetonomy',
					'logo' => 'jetonomy.svg',
					'desc' => __( 'Threaded discussions, Q&A spaces, and a token economy.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/jetonomy/',
				),
				array(
					'name' => __( 'Learnomy', 'reign' ),
					'slug' => 'learnomy',
					'logo' => 'learnomy.svg',
					'desc' => __( 'A full LMS: courses, lessons, quizzes, and certificates.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/learnomy/',
				),
				array(
					'name' => __( 'MediaVerse', 'reign' ),
					'slug' => 'wpmediaverse',
					'logo' => 'wpmediaverse.svg',
					'desc' => __( 'Direct messaging and media galleries for members.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/mediaverse/',
				),
				array(
					'name' => __( 'WP Career Board', 'reign' ),
					'slug' => 'wp-career-board',
					'logo' => 'wp-career-board.svg',
					'desc' => __( 'Job listings and applicant management.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/wp-career-board/',
				),
				array(
					'name' => __( 'Listora', 'reign' ),
					'slug' => 'wb-listora',
					'logo' => 'wb-listora.svg',
					'desc' => __( 'Member-submitted directory listings.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/listora/',
				),
			);
		}

		/**
		 * The companion installer instance, or null when unavailable.
		 *
		 * @return Reign_Plugin_Installer|null
		 */
		private function installer() {
			return class_exists( 'Reign_Plugin_Installer' ) ? Reign_Plugin_Installer::instance() : null;
		}

		/**
		 * Resolve a family product's lifecycle status for the UI.
		 *
		 * Catalog plugins use the installer's runtime probe; the flagship
		 * (BuddyNext) falls back to its own version constant.
		 *
		 * @param array $product Product row from get_family_products().
		 * @return string 'active' | 'inactive' | 'not_installed' | 'unknown'.
		 */
		private function family_status( $product ) {
			$installer = $this->installer();
			$slug      = $product['slug'];
			if ( $installer && null !== $installer->get( $slug ) ) {
				return $installer->status( $slug );
			}
			return defined( 'BUDDYNEXT_VERSION' ) && 'buddynext' === $slug ? 'active' : 'not_installed';
		}

		/**
		 * Whether a family product can be installed one-click (it is in the catalog).
		 *
		 * @param array $product Product row.
		 * @return bool
		 */
		private function is_installable( $product ) {
			$installer = $this->installer();
			return $installer && null !== $installer->get( $product['slug'] );
		}

		public function render_get_started_with_customization_section( $tab ) {
			if ( $tab != self::$_slug ) {
				return;
			}

			$companions_url   = get_template_directory_uri() . '/inc/reign-settings/imgs/companions/';
			$quick_links      = $this->get_quick_links();
			$family_products  = $this->get_family_products();
			$installer        = $this->installer();
			$essential_status = $installer ? $installer->status( 'wbcom-essential' ) : ( function_exists( 'wbcom_essential' ) ? 'active' : 'not_installed' );
			$essential_active = ( 'active' === $essential_status );
			$customizer_url   = admin_url( 'customize.php' );
			$migration_done   = ( function_exists( 'reign_color_setup_is_complete' ) && reign_color_setup_is_complete() );
			?>
			<div class="reign-gs">

				<div class="reign-gs-hero">
					<span class="reign-gs-hero-mark">
						<img src="<?php echo esc_url( $companions_url . 'wbcom.svg' ); ?>" alt="" width="56" height="56" />
					</span>
					<div class="reign-gs-hero-body">
						<h1 class="reign-gs-hero-title"><?php esc_html_e( 'Welcome to Reign', 'reign' ); ?></h1>
						<p class="reign-gs-hero-text"><?php esc_html_e( 'Reign is the home theme for the Wbcom community stack. Set up how your site looks, then power it up with the family of plugins below.', 'reign' ); ?></p>
						<div class="reign-gs-hero-actions">
							<a class="reign-gs-btn reign-gs-btn--primary" href="<?php echo esc_url( $customizer_url ); ?>"><?php esc_html_e( 'Open Customizer', 'reign' ); ?></a>
							<?php if ( $essential_active ) : ?>
								<span class="reign-gs-btn reign-gs-btn--done"><?php esc_html_e( 'Wbcom Essential installed', 'reign' ); ?></span>
							<?php elseif ( $installer ) : ?>
								<button type="button" class="reign-gs-btn reign-gs-btn--ghost reign-gs-install" data-slug="wbcom-essential"><?php esc_html_e( 'Install Wbcom Essential', 'reign' ); ?></button>
							<?php else : ?>
								<a class="reign-gs-btn reign-gs-btn--ghost" href="<?php echo esc_url( 'https://wbcomdesigns.com/downloads/wbcom-essential/' ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Get Wbcom Essential', 'reign' ); ?></a>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<section class="reign-gs-section reign-gs-family">
					<div class="reign-gs-section-head">
						<div class="reign-gs-section-head-text">
							<h2 class="reign-gs-section-title"><?php esc_html_e( 'Part of the Wbcom family', 'reign' ); ?></h2>
							<p class="reign-gs-section-sub"><?php esc_html_e( 'Social, gamification, discussions, courses, messaging, listings, and jobs. Every plugin works on its own, and the family keeps growing.', 'reign' ); ?></p>
						</div>
						<a class="reign-gs-link" href="<?php echo esc_url( 'https://wbcomdesigns.com/downloads/?utm_source=reign-theme&utm_medium=getting-started&utm_campaign=wbcom-family' ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Explore all', 'reign' ); ?></a>
					</div>
					<div class="reign-gs-grid reign-gs-grid--family">
						<?php
						foreach ( $family_products as $product ) :
							$status      = $this->family_status( $product );
							$is_active   = ( 'active' === $status );
							$installable = $this->is_installable( $product );
							?>
							<div class="reign-gs-product<?php echo $is_active ? ' is-active' : ''; ?>">
								<div class="reign-gs-product-top">
									<span class="reign-gs-product-logo">
										<img src="<?php echo esc_url( $companions_url . $product['logo'] ); ?>" alt="" width="32" height="32" />
									</span>
									<?php if ( $is_active ) : ?>
										<span class="reign-gs-pill reign-gs-pill--active"><?php esc_html_e( 'Active', 'reign' ); ?></span>
									<?php endif; ?>
								</div>
								<h3 class="reign-gs-product-name"><?php echo esc_html( $product['name'] ); ?></h3>
								<p class="reign-gs-product-desc"><?php echo esc_html( $product['desc'] ); ?></p>
								<?php if ( ! $is_active ) : ?>
									<?php if ( $installable ) : ?>
										<button type="button" class="reign-gs-product-install reign-gs-install" data-slug="<?php echo esc_attr( $product['slug'] ); ?>">
											<?php echo 'inactive' === $status ? esc_html__( 'Activate', 'reign' ) : esc_html__( 'Install', 'reign' ); ?>
										</button>
									<?php else : ?>
										<a class="reign-gs-product-cta" href="<?php echo esc_url( $product['url'] ); ?>" target="_blank" rel="noopener">
											<?php esc_html_e( 'Get it', 'reign' ); ?><span class="reign-gs-product-cta-arrow" aria-hidden="true"></span>
										</a>
									<?php endif; ?>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</section>

				<section class="reign-gs-section">
					<div class="reign-gs-section-head">
						<div class="reign-gs-section-head-text">
							<h2 class="reign-gs-section-title"><?php esc_html_e( 'Customize your site', 'reign' ); ?></h2>
							<p class="reign-gs-section-sub"><?php esc_html_e( 'Jump straight to the settings most people change first.', 'reign' ); ?></p>
						</div>
						<a class="reign-gs-link" href="<?php echo esc_url( $customizer_url ); ?>"><?php esc_html_e( 'Go to Customizer', 'reign' ); ?></a>
					</div>
					<div class="reign-gs-grid reign-gs-grid--tiles">
						<?php foreach ( $quick_links as $quick_link ) : ?>
							<a class="reign-gs-tile" href="<?php echo esc_url( $quick_link['link_url'] ); ?>">
								<?php if ( ! empty( $quick_link['icon'] ) && function_exists( 'reign_options_icon' ) ) : ?>
									<span class="reign-gs-tile-icon">
										<?php echo reign_options_icon( $quick_link['icon'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted inline SVG from a fixed allowlist. ?>
									</span>
								<?php endif; ?>
								<span class="reign-gs-tile-body">
									<span class="reign-gs-tile-title"><?php echo esc_html( $quick_link['option_title'] ); ?></span>
									<span class="reign-gs-tile-desc"><?php echo esc_html( $quick_link['option_desc'] ); ?></span>
								</span>
								<span class="reign-gs-tile-arrow" aria-hidden="true"></span>
							</a>
						<?php endforeach; ?>
					</div>
				</section>

				<section class="reign-gs-section">
					<div class="reign-gs-section-head">
						<div class="reign-gs-section-head-text">
							<h2 class="reign-gs-section-title"><?php esc_html_e( 'Resources &amp; setup', 'reign' ); ?></h2>
							<p class="reign-gs-section-sub"><?php esc_html_e( 'Grab the child theme and re-apply the 8.0.0 defaults.', 'reign' ); ?></p>
						</div>
					</div>
					<div class="reign-gs-grid reign-gs-grid--res">
						<div class="reign-gs-card">
							<h3 class="reign-gs-card-title"><?php esc_html_e( 'Reign child theme', 'reign' ); ?></h3>
							<p class="reign-gs-card-desc"><?php esc_html_e( 'Customize any file safely without touching the parent theme. Recommended for code changes.', 'reign' ); ?></p>
							<a class="reign-gs-card-cta" href="<?php echo esc_url( 'https://github.com/wbcomdesigns/reign-child-theme/releases/download/4.0.0/reign-child-theme.zip' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Download', 'reign' ); ?></a>
						</div>

						<div class="reign-gs-card">
							<h3 class="reign-gs-card-title"><?php esc_html_e( 'Re-run 8.0.0 setup', 'reign' ); ?></h3>
							<p class="reign-gs-card-desc">
								<?php esc_html_e( 'Safely re-applies the 8.0.0 defaults without overwriting anything you have saved. Use it if the one-time migration was skipped.', 'reign' ); ?>
							</p>
							<?php if ( $migration_done ) : ?>
								<p class="reign-gs-card-note"><?php esc_html_e( 'Migration already completed on this site.', 'reign' ); ?></p>
							<?php endif; ?>
							<a class="reign-gs-card-cta" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=reign_run_color_setup' ), 'reign_run_color_setup' ) ); ?>"><?php esc_html_e( 'Re-run setup', 'reign' ); ?></a>
						</div>
					</div>
				</section>

				<section class="reign-gs-section">
					<div class="reign-gs-grid reign-gs-grid--foot">
						<div class="reign-gs-card reign-gs-help">
							<h3 class="reign-gs-card-title"><?php esc_html_e( 'Need help or advice?', 'reign' ); ?></h3>
							<p class="reign-gs-card-desc"><?php esc_html_e( 'Got a question about the theme? Open a support ticket or ask in our friendly Facebook community.', 'reign' ); ?></p>
							<div class="reign-gs-help-actions">
								<a class="reign-gs-btn reign-gs-btn--primary" href="<?php echo esc_url( 'https://wbcomdesigns.com/support/' ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Submit a Support Ticket', 'reign' ); ?></a>
								<a class="reign-gs-btn reign-gs-btn--ghost" href="<?php echo esc_url( 'https://www.facebook.com/groups/191523257634994' ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Join Facebook Community', 'reign' ); ?></a>
							</div>
						</div>

						<div class="reign-gs-card reign-gs-video">
							<div class="reign-gs-video-frame">
								<iframe src="https://www.youtube.com/embed/Gep2E7YhW8g" title="<?php esc_attr_e( 'Reign BuddyPress Theme - Demo Setup', 'reign' ); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
							</div>
							<div class="reign-gs-video-body">
								<h3 class="reign-gs-card-title"><?php esc_html_e( 'Video tutorials', 'reign' ); ?></h3>
								<p class="reign-gs-card-desc"><?php esc_html_e( 'Short, step-by-step videos covering setup, customization, and troubleshooting.', 'reign' ); ?></p>
								<a class="reign-gs-link" href="<?php echo esc_url( 'https://www.youtube.com/watch?v=Gep2E7YhW8g&list=PLlkJGdi68l-9eWBbEwNFUQciw15x4bR5n&ab_channel=WbcomDesigns' ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Watch the series', 'reign' ); ?></a>
							</div>
						</div>
					</div>
				</section>

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
