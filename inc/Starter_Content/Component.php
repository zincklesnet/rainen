<?php
/**
 * Reign\Starter_Content\Component
 *
 * Registers starter content (demo pages, widgets, nav menu, theme_mods)
 * that WordPress applies on a freshly-activated install BEFORE the user
 * has published any content. Once the customer publishes their first post,
 * WordPress flips `fresh_site` to 0 and the starter content is never
 * applied again.
 *
 * Pages are composed from the bundled block patterns (patterns/ + the
 * Reign-local patterns under patterns-local/). The starter content
 * pre-seeds:
 *   - 4 demo pages (Home, About, Community, Contact)
 *   - Primary nav menu pointing at those pages
 *   - 2 footer widget areas with sample widgets
 *   - Default theme_mods: site_color_mode_toggle_show ON, default mode light
 *
 * @package reign
 * @since 8.0.0
 */

namespace Reign\Starter_Content;

defined( 'ABSPATH' ) || exit;

class Component {

	/**
	 * Idempotency guard.
	 *
	 * @var bool
	 */
	protected static bool $booted = false;

	/**
	 * Register starter content with WP via add_theme_support.
	 *
	 * MUST run on after_setup_theme priority 12 — after the customizer
	 * framework + field loader (5/6) and the tokens module (7), so any
	 * theme_mod defaults the field registrations declared are already
	 * known when starter content sets its own theme_mod overrides.
	 */
	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		self::$booted = true;
		add_action( 'after_setup_theme', array( __CLASS__, 'register' ), 12 );
	}

	/**
	 * The full starter content config.
	 */
	public static function register(): void {
		add_theme_support(
			'starter-content',
			array(
				'widgets'     => self::widgets(),
				'posts'       => self::posts(),
				'nav_menus'   => self::nav_menus(),
				'theme_mods'  => self::theme_mods(),
				'attachments' => self::attachments(),
			)
		);
	}

	/**
	 * Demo pages composed from bundled patterns.
	 *
	 * Each entry references a pattern by slug (e.g. "reign/community-hero"
	 * which lives under patterns-local/). WP includes the pattern file at
	 * starter-content application time and uses the result as post_content.
	 *
	 * @return array
	 */
	protected static function posts(): array {
		return array(
			'home'      => array(
				'post_type'    => 'page',
				'post_title'   => _x( 'Welcome', 'Starter Content', 'reign' ),
				'post_content' => '<!-- wp:pattern {"slug":"reign/community-hero"} /-->' .
					'<!-- wp:pattern {"slug":"reign/member-directory"} /-->' .
					'<!-- wp:pattern {"slug":"reign/activity-cta"} /-->',
				'template'     => 'template-fullwidth-page.php',
			),
			'about'     => array(
				'post_type'    => 'page',
				'post_title'   => _x( 'About', 'Starter Content', 'reign' ),
				'post_content' => '<!-- wp:pattern {"slug":"reign/about-founder"} /-->' .
					'<!-- wp:pattern {"slug":"reign/about-story"} /-->',
			),
			'community' => array(
				'post_type'    => 'page',
				'post_title'   => _x( 'Community', 'Starter Content', 'reign' ),
				'post_content' => '<!-- wp:pattern {"slug":"reign/leaderboard"} /-->' .
					'<!-- wp:pattern {"slug":"reign/social-proof-testimonials"} /-->',
			),
			'contact'   => array(
				'post_type'    => 'page',
				'post_title'   => _x( 'Contact', 'Starter Content', 'reign' ),
				'post_content' => '<!-- wp:pattern {"slug":"reign/cta-newsletter"} /-->',
			),
			'privacy'   => array(
				'post_type'    => 'page',
				'post_title'   => _x( 'Privacy Policy', 'Starter Content', 'reign' ),
				'post_status'  => 'draft',
				'post_content' => "<!-- wp:paragraph -->\n<p>" .
					esc_html__( 'This is your privacy policy page. Replace this text with your own. WordPress includes a guided policy template under Settings → Privacy.', 'reign' ) .
					"</p>\n<!-- /wp:paragraph -->",
			),
		);
	}

	/**
	 * Primary nav menu pointing at the four demo pages.
	 *
	 * @return array
	 */
	protected static function nav_menus(): array {
		return array(
			'menu-1' => array(
				'name'  => _x( 'Primary', 'Starter Content', 'reign' ),
				'items' => array(
					'page_home',
					'page_about',
					'page_community',
					'page_contact',
				),
			),
		);
	}

	/**
	 * Widget areas pre-populated.
	 *
	 * @return array
	 */
	protected static function widgets(): array {
		return array(
			'reign-footer-1' => array(
				'text_about' => array(
					'text',
					array(
						'title' => _x( 'About this community', 'Starter Content', 'reign' ),
						'text'  => esc_html__( 'A short description of what this community is about and who it is for. Replace this with your own copy from the Widgets panel.', 'reign' ),
					),
				),
			),
			'reign-footer-2' => array(
				'meta',
				'recent-posts',
			),
		);
	}

	/**
	 * theme_mod defaults for first-time visitors.
	 *
	 * @return array
	 */
	protected static function theme_mods(): array {
		return array(
			// Visitor color-mode toggle: visible, light default.
			'site_color_mode_toggle_show'     => 'on',
			'site_color_mode_toggle_position' => 'both',
			'site_color_mode'                 => 'light',
			// Style variation: empty (use saved customizer values).
			'site_style_variation'            => '',
		);
	}

	/**
	 * Demo attachments.
	 *
	 * @return array
	 */
	protected static function attachments(): array {
		return array();
	}
}
