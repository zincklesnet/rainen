<?php
/**
 * "Build More With Reign" tab + options-page sidebar.
 *
 * Moves the grouped add-on / companion catalog out of the cramped sidebar into
 * its own full-width tab, and fills the sidebar with a curated set of OTHER
 * Wbcom plugins that are not already featured in the central area (the Getting
 * Started family grid) or in this tab, so nothing is duplicated.
 *
 * @package reign
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Reign_Build_More_Options' ) ) :

	/**
	 * @class Reign_Build_More_Options
	 */
	class Reign_Build_More_Options {

		/**
		 * Single instance.
		 *
		 * @var Reign_Build_More_Options
		 */
		protected static $_instance = null;
		protected static $_slug     = 'build_more';

		/**
		 * Main instance.
		 *
		 * @return Reign_Build_More_Options
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Constructor.
		 */
		public function __construct() {
			add_filter( 'alter_reign_admin_tabs', array( $this, 'alter_reign_admin_tabs' ), 20, 1 );
			add_action( 'render_content_after_form', array( $this, 'render_build_more_tab' ), 10, 1 );
			add_action( 'reign_options_sidebar', array( $this, 'render_sidebar' ), 10 );
		}

		/**
		 * Register the "Build More With Reign" tab.
		 *
		 * @param array $tabs Existing tabs.
		 * @return array
		 */
		public function alter_reign_admin_tabs( $tabs ) {
			$tabs[ self::$_slug ] = __( 'Build More', 'reign' );
			return $tabs;
		}

		/**
		 * Add-on + companion catalog, grouped by use case.
		 *
		 * type 'addon'  = Reign theme add-on  -> wbcomdesigns.com, "Buy Now".
		 * type 'plugin' = companion plugin    -> store.wbcomdesigns.com, "View Plugin".
		 * 'gate'        = class_exists check; the item hides when the product is active.
		 *
		 * @return array
		 */
		public function promo_groups() {
			$groups = array(
				array(
					'title' => __( 'Sell Courses Online', 'reign' ),
					'items' => array(
						array(
							'name' => __( 'Reign LearnDash Addon', 'reign' ),
							'desc' => __( 'Advanced styling and layouts for engaging LearnDash course pages.', 'reign' ),
							'url'  => 'https://wbcomdesigns.com/downloads/reign-learndash-addon/',
							'type' => 'addon',
							'gate' => 'LearnMate_LearnDash_Addon',
						),
						array(
							'name' => __( 'Reign LifterLMS Addon', 'reign' ),
							'desc' => __( 'Create, sell, and protect online courses in a distraction-free design.', 'reign' ),
							'url'  => 'https://wbcomdesigns.com/downloads/reign-lifterlms-addon/',
							'type' => 'addon',
							'gate' => 'Reign_LifterLMS_Addon',
						),
						array(
							'name' => __( 'Reign Sensei Addon', 'reign' ),
							'desc' => __( 'Build an online course marketplace like Udemy with Sensei.', 'reign' ),
							'url'  => 'https://wbcomdesigns.com/downloads/reign-sensei-addon/',
							'type' => 'addon',
							'gate' => 'Reign_Sensei_Addon',
						),
						array(
							'name' => __( 'Reign Tutor LMS Addon', 'reign' ),
							'desc' => __( 'Feature-packed Tutor LMS styling for your learning platform.', 'reign' ),
							'url'  => 'https://wbcomdesigns.com/downloads/reign-tutorlms-addon/',
							'type' => 'addon',
							'gate' => 'Reign_Tutorlms_Addon',
						),
						array(
							'name' => __( 'Dashboard for LearnDash', 'reign' ),
							'desc' => __( 'Front-end workspace where instructors manage courses without wp-admin.', 'reign' ),
							'url'  => 'https://store.wbcomdesigns.com/dashboard-for-learndash/',
							'type' => 'plugin',
						),
					),
				),
				array(
					'title' => __( 'Build a Marketplace', 'reign' ),
					'items' => array(
						array(
							'name' => __( 'Reign Dokan Addon', 'reign' ),
							'desc' => __( 'Polished Dokan store layouts for multi-vendor WooCommerce shops.', 'reign' ),
							'url'  => 'https://wbcomdesigns.com/downloads/reign-dokan-addon/',
							'type' => 'addon',
							'gate' => 'Reign_Dokan_Addon',
						),
						array(
							'name' => __( 'Reign WC Vendors Addon', 'reign' ),
							'desc' => __( 'Custom Single Store layouts and a modern WC Vendors store widget.', 'reign' ),
							'url'  => 'https://wbcomdesigns.com/downloads/reign-wc-vendors-addon/',
							'type' => 'addon',
							'gate' => 'Reign_Wcvendors_Addon',
						),
						array(
							'name' => __( 'Reign WCFM Addon', 'reign' ),
							'desc' => __( 'Run and expand your marketplace with WCFM and WooCommerce.', 'reign' ),
							'url'  => 'https://wbcomdesigns.com/downloads/reign-wcfm-addon/',
							'type' => 'addon',
							'gate' => 'Reign_Wcfm_Addon',
						),
					),
				),
				array(
					'title' => __( 'Engage Your Community', 'reign' ),
					'items' => array(
						array(
							'name' => __( 'Jetonomy', 'reign' ),
							'desc' => __( 'Forums, Q&A, and idea boards. A modern bbPress alternative.', 'reign' ),
							'url'  => 'https://store.wbcomdesigns.com/jetonomy/',
							'type' => 'plugin',
						),
						array(
							'name' => __( 'WB Gamification', 'reign' ),
							'desc' => __( 'Points, badges, and leaderboards. Zero configuration.', 'reign' ),
							'url'  => 'https://store.wbcomdesigns.com/wb-gamification/',
							'type' => 'plugin',
						),
						array(
							'name' => __( 'BuddyPress Business Profile', 'reign' ),
							'desc' => __( 'Business pages, reviews, and a searchable member business directory.', 'reign' ),
							'url'  => 'https://store.wbcomdesigns.com/buddypress-business-directory/',
							'type' => 'plugin',
						),
					),
				),
				array(
					'title' => __( 'Share Video & Media', 'reign' ),
					'items' => array(
						array(
							'name' => __( 'WPMediaVerse', 'reign' ),
							'desc' => __( 'Albums, video, cloud storage, and member galleries.', 'reign' ),
							'url'  => 'https://store.wbcomdesigns.com/wpmediaverse/',
							'type' => 'plugin',
						),
					),
				),
				array(
					'title' => __( 'Jobs & Directories', 'reign' ),
					'items' => array(
						array(
							'name' => __( 'Reign WP Job Manager Addon', 'reign' ),
							'desc' => __( 'Extend WP Job Manager and all of its extensions with Reign styling.', 'reign' ),
							'url'  => 'https://wbcomdesigns.com/downloads/reign-wp-job-manager-addon/',
							'type' => 'addon',
							'gate' => 'Reign_WP_Job_Manager_Addon',
						),
						array(
							'name' => __( 'Reign GeoDirectory Addon', 'reign' ),
							'desc' => __( 'Map-powered directories styled for Reign.', 'reign' ),
							'url'  => 'https://wbcomdesigns.com/downloads/reign-geodirectory-addon/',
							'type' => 'addon',
							'gate' => 'Reign_Geodirectory_Addon',
						),
						array(
							'name' => __( 'WP Career Board', 'reign' ),
							'desc' => __( 'Job board on Gutenberg with an ATS pipeline in Pro.', 'reign' ),
							'url'  => 'https://store.wbcomdesigns.com/wp-career-board/',
							'type' => 'plugin',
						),
						array(
							'name' => __( 'Listora', 'reign' ),
							'desc' => __( 'Complete directory plugin: listings, reviews, paid placements.', 'reign' ),
							'url'  => 'https://store.wbcomdesigns.com/listora/',
							'type' => 'plugin',
						),
					),
				),
			);

			/**
			 * Filters the grouped add-on / companion promos on the Reign options page.
			 *
			 * @param array $groups { title, items: { name, desc, url, type, gate? } }.
			 */
			return (array) apply_filters( 'reign_options_promo_groups', $groups );
		}

		/**
		 * "More from Wbcom" sidebar plugins.
		 *
		 * Deliberately disjoint from the central family grid and the Build More
		 * catalog above - these are additional community plugins shown nowhere
		 * else on the page, so the sidebar never repeats what is already on screen.
		 *
		 * @return array
		 */
		public function sidebar_plugins() {
			$plugins = array(
				array(
					'name' => __( 'WB Member Wiki', 'reign' ),
					'desc' => __( 'A collaborative wiki where members write and you keep editorial control.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/wb-member-wiki/',
				),
				array(
					'name' => __( 'SnipShare', 'reign' ),
					'desc' => __( 'Let your community paste, share, and manage code snippets.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/snipshare/',
				),
				array(
					'name' => __( 'MediaShield', 'reign' ),
					'desc' => __( 'One unified video player with watch analytics and content protection.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/mediashield/',
				),
				array(
					'name' => __( 'WB Polls', 'reign' ),
					'desc' => __( 'Polls and surveys right in the activity stream.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/buddypress-polls/',
				),
				array(
					'name' => __( 'WP Sell Services', 'reign' ),
					'desc' => __( 'Turn your community into a service marketplace with WooCommerce payouts.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/wp-sell-services/',
				),
				array(
					'name' => __( 'WB Ad Manager', 'reign' ),
					'desc' => __( 'Members sell ad placements while you keep the revenue.', 'reign' ),
					'url'  => 'https://wbcomdesigns.com/downloads/wb-ad-manager/',
				),
			);

			/**
			 * Filters the "More from Wbcom" sidebar plugin list.
			 *
			 * @param array $plugins { name, desc, url }.
			 */
			return (array) apply_filters( 'reign_options_sidebar_plugins', $plugins );
		}

		/**
		 * Append UTM params to a promo URL.
		 *
		 * @param string $url      Base URL.
		 * @param string $campaign Campaign tag.
		 * @return string
		 */
		private function promo_url( $url, $campaign ) {
			return add_query_arg(
				array(
					'utm_source'   => 'reign-theme',
					'utm_medium'   => 'admin-options',
					'utm_campaign' => $campaign,
				),
				$url
			);
		}

		/**
		 * Render the full-width "Build More With Reign" tab.
		 *
		 * @param string $tab Current tab slug.
		 * @return void
		 */
		public function render_build_more_tab( $tab ) {
			if ( $tab !== self::$_slug ) {
				return;
			}
			?>
			<div class="reign-gs reign-bm">
				<div class="reign-gs-hero">
					<span class="reign-gs-hero-mark">
						<img src="<?php echo esc_url( get_template_directory_uri() . '/inc/reign-settings/imgs/companions/wbcom.svg' ); ?>" alt="" width="56" height="56" />
					</span>
					<div class="reign-gs-hero-body">
						<h1 class="reign-gs-hero-title"><?php esc_html_e( 'Build more with Reign', 'reign' ); ?></h1>
						<p class="reign-gs-hero-text"><?php esc_html_e( 'Theme add-ons and community plugins by Wbcom Designs, grouped by what you want to build. Free versions are available for every plugin.', 'reign' ); ?></p>
						<div class="reign-gs-hero-actions">
							<a class="reign-gs-btn reign-gs-btn--primary" href="<?php echo esc_url( 'https://wbcomdesigns.com/downloads/?utm_source=reign-theme&utm_medium=admin-options&utm_campaign=build-more' ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Browse all add-ons', 'reign' ); ?></a>
						</div>
					</div>
				</div>

				<?php
				foreach ( $this->promo_groups() as $group ) :
					$items = array_filter(
						$group['items'],
						static function ( $item ) {
							return empty( $item['gate'] ) || ! class_exists( $item['gate'] );
						}
					);
					if ( empty( $items ) ) {
						continue;
					}
					?>
					<section class="reign-gs-section">
						<div class="reign-gs-section-head">
							<div class="reign-gs-section-head-text">
								<h2 class="reign-gs-section-title"><?php echo esc_html( $group['title'] ); ?></h2>
							</div>
						</div>
						<div class="reign-gs-grid reign-gs-grid--res">
							<?php foreach ( $items as $item ) : ?>
								<div class="reign-gs-card">
									<h3 class="reign-gs-card-title"><?php echo esc_html( $item['name'] ); ?></h3>
									<p class="reign-gs-card-desc"><?php echo esc_html( $item['desc'] ); ?></p>
									<a class="reign-gs-card-cta" href="<?php echo esc_url( $this->promo_url( $item['url'], 'build-more' ) ); ?>" target="_blank" rel="noopener">
										<?php echo 'addon' === $item['type'] ? esc_html_e( 'Buy Now', 'reign' ) : esc_html_e( 'View Plugin', 'reign' ); ?>
									</a>
								</div>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endforeach; ?>
			</div>
			<?php
		}

		/**
		 * Render the "More from Wbcom" options-page sidebar.
		 *
		 * @return void
		 */
		public function render_sidebar() {
			?>
			<div class="reign-side">
				<h3 class="reign-side-title"><?php esc_html_e( 'More from Wbcom', 'reign' ); ?></h3>
				<p class="reign-side-sub"><?php esc_html_e( 'Other community plugins to extend your site.', 'reign' ); ?></p>
				<div class="reign-side-list">
					<?php foreach ( $this->sidebar_plugins() as $plugin ) : ?>
						<a class="reign-side-card" href="<?php echo esc_url( $this->promo_url( $plugin['url'], 'sidebar' ) ); ?>" target="_blank" rel="noopener">
							<span class="reign-side-card-name"><?php echo esc_html( $plugin['name'] ); ?></span>
							<span class="reign-side-card-desc"><?php echo esc_html( $plugin['desc'] ); ?></span>
						</a>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
		}
	}

	endif;

Reign_Build_More_Options::instance();
