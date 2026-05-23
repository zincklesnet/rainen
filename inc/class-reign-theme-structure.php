<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Reign_Theme_Structure' ) ) :

	/**
	 * @class Reign_Theme_Structure
	 */
	class Reign_Theme_Structure {

		/**
		 * The single instance of the class.
		 *
		 * @var Reign_Theme_Structure
		 */
		protected static $_instance = null;

		/**
		 * Main Reign_Theme_Structure Instance.
		 *
		 * Ensures only one instance of Reign_Theme_Structure is loaded or can be loaded.
		 *
		 * @return Reign_Theme_Structure - Main instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Reign_Theme_Structure Constructor.
		 */
		public function __construct() {
			$this->init_hooks();
		}

		/**
		 * Hook into actions and filters.
		 */
		private function init_hooks() {
			add_action( 'reign_before_content_section', array( $this, 'render_left_sidebar_area' ) );
			add_action( 'reign_after_content_section', array( $this, 'render_right_sidebar_area' ) );

			/**
			 * WooCommerce left sidebar.
			 */
			add_action( 'woocommerce_before_main_content', array( $this, 'render_left_sidebar_area' ), 8 );
			/**
			 * WooCommerce right sidebar.
			 */
			add_action( 'woocommerce_sidebar', array( $this, 'render_right_sidebar_area' ) );

			remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

			/**
			 * Render page header.
			 */
			add_action( 'reign_before_content', array( $this, 'render_page_header' ) );

			/**
			 * Render website topbar.
			 */
			add_action( 'reign_before_masthead', array( $this, 'render_theme_topbar' ), 20 );

			/**
			 * Render website header desktop.
			 */
			add_action( 'reign_masthead', array( $this, 'render_theme_header_desktop' ), 20 );

			/**
			 * Render website header mobile.
			 */
			add_action( 'reign_masthead', array( $this, 'render_theme_header_mobile' ), 25 );

			/**
			 * Render website header mobile.
			 */
			add_action( 'reign_before_page', array( $this, 'render_theme_left_panel' ) );

			/**
			 * Render website footer.
			 */
			add_action( 'reign_footer', array( $this, 'render_theme_footer' ), 20 );

			/**
			 * Render post tags at bottom.
			 */
			add_action( 'reign_extra_info_on_single_post_end', array( $this, 'render_post_tags_at_bottom' ) );

			add_action( 'wp_head', array( $this, 'apply_theme_color' ) );

			/**
			 * Render Custom Dark Skin.
			 */
			add_action( 'wp_head', array( $this, 'render_theme_custom_styles' ) );

			// Clear cached inline CSS when customizer settings are saved.
			add_action( 'customize_save_after', array( $this, 'clear_inline_css_cache' ) );

			/**
			 * Set post excerpt.
			 */
			if ( true == get_theme_mod( 'reign_advanced_excerpt', true ) ) {
				add_filter( 'the_excerpt', array( $this, 'reign_the_excerpt' ), 20, 1 );
				add_filter( 'excerpt_length', array( $this, 'reign_excerpt_length' ), 999 );
			}

			/**
			 * Render post meta section.
			 */
			add_action( 'reign_post_content_begins', array( $this, 'render_post_meta_section' ) );

			/**
			 * Render post comment section.
			 */
			// add_action( 'reign_single_post_comment_section', array( $this, 'render_post_comment_section' ) );
			if ( class_exists( 'bbPress' ) ) {
				add_action( 'reign_bbpress_before_content', array( $this, 'reign_bbpress_page_title' ) );
			}
		}

		// public function render_post_comment_section() {
		// $reign_comment = get_theme_mod( 'reign_comment', '' );
		// if ( ! is_array( $reign_comment ) ) {
		// $reign_comment = array( 'post' );
		// }
		// if ( in_array( get_post_type(), $reign_comment ) ) {
		// If comments are open or we have at least one comment, load up the comment template.
		// if ( comments_open() || get_comments_number() ) :
		// comments_template();
		// endif;
		// }
		// }

		public function reign_bbpress_page_title() {
			if ( bbp_is_single_topic() ) {
				?>
				<header class="entry-header bb-single-forum">
					<h1 class="entry-title"><?php echo bbp_get_topic_title(); ?></h1>
				</header> <!--.entry-header -->
				<?php
			} elseif ( bbp_is_single_reply() ) {
				?>
				<header class="entry-header bb-single-forum">
					<h1 class="entry-title"><?php echo bbp_get_reply_title(); ?></h1>
				</header> <!--.entry-header -->
				<?php

			}
		}

		public function render_post_meta_section() {
			$post_meta_alignment      = get_theme_mod( 'reign_single_post_meta_alignment', 'left' );
			$reign_single_post_layout = get_theme_mod( 'reign_single_post_layout', 'default' );

			$single_header_enable         = false;
			$default_single_header_enable = get_theme_mod( 'reign_cpt_default_sub_header_switch', false );
			$cpt_single_header_enable     = get_theme_mod( 'reign_' . get_post_type() . '_single_header_enable', true );

			if ( false === $default_single_header_enable ) {
				if ( false === $cpt_single_header_enable ) {
					$single_header_enable = true;
				} else {
					$single_header_enable = false;
				}
			}
			$reign_subheader_settings = get_post_meta( get_the_ID(), '_subheader_overwrite', true );
			if ( $reign_subheader_settings == 'yes' ) {
				$single_header_enable = true;
			}
			if ( is_single() && 'post' === get_post_type() && ( $reign_single_post_layout === 'wide' || $reign_single_post_layout === 'wide_sidebar' ) ) {
				?>
				<div class="rg-post-meta-info-wrapper">
					<?php if ( get_post_type() == 'post' ) : ?>
					<div class="post-meta-info">
						<div class="entry-meta"><?php reign_entry_list_footer(); ?></div>
						<?php do_action( 'reign_extra_info_on_single_post_start' ); ?>
					</div>
					<?php endif; ?>
					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					</header><!-- .entry-header -->
				</div>
				<?php
			} elseif ( is_single() ) {
				?>
				<div class="rg-post-meta-info-wrapper align-<?php echo esc_attr( $post_meta_alignment ); ?>">
					<?php if ( ! $single_header_enable ) { ?>
						<header class="entry-header">
							<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
						</header><!-- .entry-header -->
					<?php } ?>
					<?php if ( get_post_type() == 'post' ) : ?>
						<div class="post-meta-info">
							<div class="entry-meta"><?php reign_entry_list_footer(); ?></div>
							<?php do_action( 'reign_extra_info_on_single_post_start' ); ?>
						</div>
					<?php endif; ?>
				</div>
					<?php

			}
		}

		function reign_excerpt_length( $length ) {
			$length = get_theme_mod( 'reign_blog_excerpt_length', 20 );
			return apply_filters( 'reign_excerpt_length', $length );
		}

		/**
		 * Custom function to modify the excerpt content.
		 *
		 * This function trims the excerpt based on the configured length,
		 * removes the "(more…)" link if present, and strips shortcodes
		 * before generating the final excerpt.
		 *
		 * @param string $excerpt The original excerpt content.
		 * @return string Modified excerpt.
		 */
		public function reign_the_excerpt( $excerpt ) {
			// Get the configured excerpt length from theme options.
			$length = get_theme_mod( 'reign_blog_excerpt_length', 20 );
			$length = apply_filters( 'reign_excerpt_length', $length );

			// Get the configured excerpt more link or use '...' by default.
			$excerpt_more = apply_filters( 'reign_excerpt_more', '...' );

			// Check for the more link using strpos.
			$more_position = strpos( $excerpt, '<!--more-->' );

			if ( false !== $more_position ) {
				// If more link is present, remove it.
				$excerpt = substr( $excerpt, 0, $more_position );
			}

			// Strip shortcodes and use wp_trim_words.
			$excerpt = wp_trim_words( strip_shortcodes( $excerpt ), $length, $excerpt_more );

			return $excerpt;
		}

		public function render_post_tags_at_bottom() {
			/* list of tags assigned to post */
			$tags_list = get_the_term_list( get_the_ID(), 'post_tag', $before      = '', $sep      = '', $after        = '' );
			if ( $tags_list ) {
				$tags_list = '<span class="tag-links">' . $tags_list . '</span>';
			}
			echo '<div class="rg-post-tags-wrapper">';
			echo apply_filters( 'reign_post_tags', $tags_list );
			echo '</div>';
		}

		public function apply_theme_color() {
			$cached = get_transient( 'reign_theme_color_css' );
			if ( false !== $cached && ! is_customize_preview() ) {
				echo $cached; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Cached output built from escaped values.
				return;
			}

			global $rtm_color_scheme;

			$color_schemes_set = reign_color_scheme_set();
			$default_theme_cs  = $color_schemes_set[ $rtm_color_scheme ]['reign_colors_theme'];
			$theme_color       = get_theme_mod( $rtm_color_scheme . '-' . 'reign_colors_theme', $default_theme_cs );
			// $theme_color = get_theme_mod( 'reign_colors_theme', '#3b5998' );
			$selector_for_color = '';
			$selector_for_color = apply_filters( 'reign_selector_set_to_apply_theme_color', $selector_for_color );
			$selector_for_color = trim( $selector_for_color, ',' );

			$selector_for_background = '';
			$selector_for_background = apply_filters( 'reign_selector_set_to_apply_theme_color_to_background', $selector_for_background );
			$selector_for_background = trim( $selector_for_background, ',' );

			$selector_for_border = '';
			$selector_for_border = apply_filters( 'reign_selector_set_to_apply_theme_color_to_border', $selector_for_border );
			$selector_for_border = trim( $selector_for_border, ',' );

			$reign_preloading_icon     = get_theme_mod( 'reign_preloading_icon', REIGN_THEME_URI . '/lib/images/loader-1.svg' );
			$reign_preloading_bg_color = get_theme_mod( 'reign_preloading_bg_color', '#ffffff' );

			$reign_blog_per_row = get_theme_mod( 'reign_blog_per_row', '3' );
			$width              = ( 100 / $reign_blog_per_row );

			$reign_site_header_sub_header_height = get_theme_mod( 'reign_site_header_sub_header_height', '286' );

			ob_start();
			?>
			<style type="text/css">
				.blog .wb-grid-view,
				.archive .wb-grid-view {
					width: calc(<?php echo esc_attr( $width ); ?>% - 30px);
				}

				.masonry {
					column-count: <?php echo esc_attr( $reign_blog_per_row ); ?>;
				}

				<?php echo esc_html( $selector_for_color ); ?> {
					color: <?php echo esc_attr( $theme_color ); ?>;
				}
				<?php echo esc_html( $selector_for_background ); ?> {
					background: <?php echo esc_attr( $theme_color ); ?>;
				}
				<?php echo esc_html( $selector_for_border ); ?> {
					border-color: <?php echo esc_attr( $theme_color ); ?>;
				}

				.rg-page-loader {
					background: url(<?php echo esc_url( $reign_preloading_icon ); ?>) center no-repeat <?php echo esc_attr( $reign_preloading_bg_color ); ?>;
				}

				.lm-site-header-section .lm-header-banner {
					height: <?php echo esc_attr( $reign_site_header_sub_header_height ); ?>px;
				}
			</style>
			<?php
			$css = ob_get_clean();
			set_transient( 'reign_theme_color_css', $css, DAY_IN_SECONDS );
			echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Built from escaped values above.
		}

		public function render_theme_custom_styles() {
			$cached = get_transient( 'reign_custom_styles_css' );
			if ( false !== $cached && ! is_customize_preview() ) {
				echo $cached; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Cached compressed CSS.
				return;
			}

			$output = reign_get_custom_styles();
			$css    = '<style id="reign-custom-styles" type="text/css">' . reign_css_compress( $output ) . '</style>' . "\n";
			set_transient( 'reign_custom_styles_css', $css, DAY_IN_SECONDS );
			echo $css; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Compressed CSS from theme functions.
		}

		/**
		 * Clear cached inline CSS transients when customizer saves.
		 */
		public function clear_inline_css_cache() {
			delete_transient( 'reign_theme_color_css' );
			delete_transient( 'reign_custom_styles_css' );
		}

		public function render_theme_header_desktop() {
			$header_version = get_theme_mod( 'reign_header_layout', 'v2' );
			get_template_part( 'template-parts/header/header', $header_version );
		}

		public function render_theme_header_mobile() {
			get_template_part( 'template-parts/header/header-mobile', '' );
		}

		public function render_theme_left_panel() {
			get_template_part( 'template-parts/header/reign-panel', '' );
		}

		public function render_theme_footer() {
			get_template_part( 'template-parts/footer/footer', '' );
		}

		public function render_theme_topbar() {
			$topbar_enable = get_theme_mod( 'reign_header_topbar_enable', '1' );
			if ( $topbar_enable ) {
				get_template_part( 'template-parts/header/header', 'topbar' );
			}
		}

		public function render_page_header() {

			global $wp_query;
			if ( is_front_page() && is_home() ) {
				// Default homepage.
				return;
			} elseif ( is_front_page() ) {
				// static homepage.
				return;
			}

			// Bail if customizer layout is wide and wide with sidebar.
			$reign_single_post_layout = get_theme_mod( 'reign_single_post_layout', 'default' );
			if ( is_single() && 'post' === get_post_type() && ( $reign_single_post_layout === 'wide' || $reign_single_post_layout === 'wide_sidebar' ) ) {
				return;
			}

			/* BuddyPress support added */
			if ( function_exists( 'is_buddypress' ) && is_buddypress() ) {

				if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss ) ) {
					global $post;
				}
				$bp_pages = get_option( 'bp-pages' );

				if ( bp_is_current_component( 'groups' ) && isset( $bp_pages['groups'] ) && ! bp_is_user() && ! bp_is_group_create() && ! bp_is_group() ) {
					$post = get_post( $bp_pages['groups'] );
				} elseif ( bp_is_current_component( 'members' ) && isset( $bp_pages['members'] ) && ! bp_is_user() ) {
					$post = get_post( $bp_pages['members'] );
				} elseif ( bp_is_current_component( 'activity' ) && isset( $bp_pages['activity'] ) && ! bp_is_user() ) {
					$post = get_post( $bp_pages['activity'] );
				} elseif ( bp_is_current_component( 'document' ) && isset( $bp_pages['document'] ) ) {
					$post = get_post( $bp_pages['document'] );
				} elseif ( bp_is_current_component( 'media' ) && isset( $bp_pages['media'] ) ) {
					$post = get_post( $bp_pages['media'] );
				} elseif ( bp_is_register_page() && isset( $bp_pages['register'] ) ) {
					$post = get_post( $bp_pages['register'] );
				} else {
					global $post;
				}

				if ( is_object( $post ) ) {
					$_subheader_overwrite = get_post_meta( $post->ID, '_subheader_overwrite', true );
					if ( $_subheader_overwrite != 'yes' || bp_is_group_create() ) {
						return;
					}
				}
			}

			// Check if Platform plugin is active.
			if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
				if ( bp_is_user() || bp_is_group() ) {
					return;
				}
			}

			$_subheader_overwrite = get_post_meta( get_the_ID(), '_subheader_overwrite', true );

			$reign_forum_archive_header_enable = get_theme_mod( 'reign_forum_archive_header_enable' );
			$reign_forum_single_header_enable  = get_theme_mod( 'reign_forum_single_header_enable' );
			$reign_topic_single_header_enable  = get_theme_mod( 'reign_topic_single_header_enable' );

			/* BBpress support added */
			if ( function_exists( 'is_bbpress' ) && is_bbpress() && ( bbp_is_forum_archive() || bbp_is_topic_archive() ) && $_subheader_overwrite != 'yes' && true === $reign_forum_archive_header_enable ) {
				echo '<div class="bbp-header-search-wrap">';
				echo '<div class="bbp-header-search-inner">';
				echo do_shortcode( '[bbp-search-form]' );
				echo '</div>';
				echo '</div>';
				return;
			} elseif ( function_exists( 'is_bbpress' ) && is_bbpress() && bbp_is_single_forum() && $_subheader_overwrite != 'yes' && true === $reign_forum_single_header_enable ) {
				echo '<div class="bbp-header-search-wrap">';
				echo '<div class="bbp-header-search-inner">';
				echo do_shortcode( '[bbp-search-form]' );
				echo '</div>';
				echo '</div>';
				return;
			} elseif ( function_exists( 'is_bbpress' ) && is_bbpress() && bbp_is_single_topic() && $_subheader_overwrite != 'yes' && true === $reign_topic_single_header_enable ) {
				echo '<div class="bbp-header-search-wrap">';
				echo '<div class="bbp-header-search-inner">';
				echo do_shortcode( '[bbp-search-form]' );
				echo '</div>';
				echo '</div>';
				return;
			}

			/* PeepSo support added */
			if ( class_exists( 'PeepSo' ) ) {
				$shortcodes = PeepSo::get_instance()->all_shortcodes();
				foreach ( $shortcodes as $sc => $method ) {
					$page                 = str_ireplace( 'peepso_', '', $sc );
					$page_key             = 'page_' . $page;
					$_subheader_overwrite = get_post_meta( get_the_ID(), '_subheader_overwrite', true );
					if ( isset( $wp_query->queried_object ) && isset( $wp_query->queried_object->post_name ) ) {
						if ( PeepSo::get_option( $page_key ) === $wp_query->queried_object->post_name && $_subheader_overwrite != 'yes' ) {
							return;
						}
					}
				}
			}

			/* content layout support added */
			global $post;
			if ( $post ) {
				$theme_slug               = apply_filters( 'wbcom_essential_theme_slug', 'reign' );
				$wbcom_metabox_data       = get_post_meta( $post->ID, $theme_slug . '_wbcom_metabox_data', true );
				$reign_subheader_settings = get_post_meta( $post->ID, '_subheader_overwrite', true );
				$site_layout              = isset( $wbcom_metabox_data['layout']['site_layout'] ) ? $wbcom_metabox_data['layout']['site_layout'] : '';
				if ( ( 'stretched_view_no_title' === $site_layout ) || ( 'full_width_no_title' === $site_layout ) ) {
					return;
				}
			}

			$post_type = get_post_type();

			$kirki_post_types_support_class = new Reign_Kirki_Post_Types_Support();
			$supported_post_types           = $kirki_post_types_support_class->get_post_types_to_support();
			if ( is_singular() ) {
				$single_header_enable         = false;
				$default_single_header_enable = get_theme_mod( 'reign_cpt_default_sub_header_switch', false );
				$cpt_single_header_enable     = get_theme_mod( 'reign_' . $post_type . '_single_header_enable', true );

				if ( false === $default_single_header_enable ) {
					if ( false === $cpt_single_header_enable ) {
						$single_header_enable = true;
					} else {
						$single_header_enable = false;
					}
				}

				if ( $reign_subheader_settings == 'yes' ) {
					$single_header_enable = true;
				}
				if ( $single_header_enable ) {
					get_template_part( 'template-parts/reign', 'page-header' );
				}
			} elseif ( is_search() ) {
				$search_header_enable = get_theme_mod( 'reign_search_header_enable', true );
				if ( ! $search_header_enable ) {
					get_template_part( 'template-parts/reign', 'page-header' );
				}
			} elseif ( is_archive() || is_home() ) {
				$archive_header_enable           = false;
				$default_single_header_enable    = get_theme_mod( 'reign_cpt_default_sub_header_switch', false );
				$archive_post_type_header_enable = get_theme_mod( 'reign_' . $post_type . '_archive_header_enable', true );
				if ( false === $default_single_header_enable ) {
					if ( false === $archive_post_type_header_enable ) {
						$archive_header_enable = true;
					} else {
						$archive_header_enable = false;
					}
				}
				if ( $archive_header_enable ) {
					get_template_part( 'template-parts/reign', 'page-header' );
				}
			} else {

				get_template_part( 'template-parts/reign', 'page-header' );
			}
		}

		public function render_left_sidebar_area() {
			$sidebar_id = apply_filters( 'reign_sidebar_id_for_left_sidebar', 'left' );

			// 1) Search page (Customizer)
			if ( is_search() ) {
				$search_content_layout = get_theme_mod( 'reign_search_page_layout', 'right_sidebar' );
				if ( in_array( $search_content_layout, array( 'both_sidebar', 'left_sidebar' ), true ) ) {
					get_sidebar( $sidebar_id );
				}
				return;
			}

			// 2) Skip left sidebar for single posts with wide layout.
			if ( is_single() && 'post' === get_post_type() ) {
				$layout = get_theme_mod( 'reign_single_post_layout', 'default' );
				if ( 'wide' === $layout ) {
					return;
				}
			}

			global $post, $wp_query;

			// Keep a small helper.
			$theme_slug = apply_filters( 'wbcom_essential_theme_slug', 'reign' );

			// Determine a candidate page ID that may control layout (page_for_posts, BP pages, Woo pages, etc).
			$page_id = 0;

			// Posts page
			if ( isset( $wp_query ) && (bool) $wp_query->is_posts_page ) {
				$page_id = (int) get_option( 'page_for_posts' );
			}

			// BuddyPress / BuddyBoss mapped pages (bp-pages)
			if ( function_exists( 'bp_is_current_component' ) ) {
				$bp_pages = (array) get_option( 'bp-pages', array() );

				if ( bp_is_current_component( 'groups' ) && ! bp_is_group() && ! bp_is_user() && ! bp_is_group_create() ) {
					$page_id = (int) ( $bp_pages['groups'] ?? $page_id );
				} elseif ( bp_is_current_component( 'members' ) && ! bp_is_user() ) {
					$page_id = (int) ( $bp_pages['members'] ?? $page_id );
				} elseif ( bp_is_current_component( 'activity' ) && ! bp_is_user() ) {
					$page_id = (int) ( $bp_pages['activity'] ?? $page_id );
				} elseif ( function_exists( 'bp_is_document_component' ) && bp_is_document_component() && ! bp_is_user() ) {
					$page_id = (int) ( $bp_pages['document'] ?? $page_id );
				} elseif ( function_exists( 'bp_is_media_component' ) && bp_is_media_component() && ! bp_is_user() ) {
					$page_id = (int) ( $bp_pages['media'] ?? $page_id );
				} elseif ( bp_is_user() || bp_is_group() || bp_is_register_page() ) {
					// user or single group views - nothing to show in left sidebar hook
					return;
				}
			}

			// WooCommerce special pages
			if ( class_exists( 'WooCommerce' ) && is_woocommerce() ) {
				if ( is_shop() ) {
					$page_id = (int) get_option( 'woocommerce_shop_page_id' );
				} elseif ( is_cart() ) {
					$page_id = (int) get_option( 'woocommerce_cart_page_id' );
				} elseif ( is_checkout() ) {
					$page_id = (int) get_option( 'woocommerce_checkout_page_id' );
				} elseif ( is_account_page() ) {
					$page_id = (int) get_option( 'woocommerce_myaccount_page_id' );
				}
			}

			// 3) If a page_id was determined (BP/BB/Woo/posts), check its per-page metabox and respect it.
			if ( $page_id ) {
				$meta = get_post_meta( $page_id, "{$theme_slug}_wbcom_metabox_data", true );
				$site_layout = $meta['layout']['site_layout'] ?? '';

				if ( in_array( $site_layout, array( 'left_sidebar', 'both_sidebar' ), true ) ) {
					get_sidebar( $sidebar_id );
					return;
				}

				// If layout explicitly set on that page but not left/both, honor it (skip left sidebar).
				if ( $site_layout ) {
					return;
				}
			}

			// 4) Also check the currently queried object itself (covers pages that are directly edited in WP admin).
			$queried_id = (int) get_queried_object_id();
			if ( $queried_id && $queried_id !== $page_id ) {
				$meta = get_post_meta( $queried_id, "{$theme_slug}_wbcom_metabox_data", true );
				$site_layout = $meta['layout']['site_layout'] ?? '';

				if ( in_array( $site_layout, array( 'left_sidebar', 'both_sidebar' ), true ) ) {
					get_sidebar( $sidebar_id );
					return;
				}

				if ( $site_layout ) {
					return;
				}
			}

			// 5) If we're on BuddyPress directory pages and per-page didn't decide, use the directory Customizer settings.
			if ( function_exists( 'bp_is_current_component' ) ) {
				// groups directory
				if ( bp_is_current_component( 'groups' ) && ! bp_is_group() && ! bp_is_user() && ! bp_is_group_create() ) {
					$groups_layout = get_theme_mod( 'reign_groups_directory_sidebar_layout', 'right_sidebar' );
					if ( in_array( $groups_layout, array( 'left_sidebar', 'both_sidebar' ), true ) ) {
						get_sidebar( $sidebar_id );
					}
					return;
				}

				// members directory
				if ( bp_is_current_component( 'members' ) && ! bp_is_user() ) {
					$members_layout = get_theme_mod( 'reign_members_directory_sidebar_layout', 'right_sidebar' );
					if ( in_array( $members_layout, array( 'left_sidebar', 'both_sidebar' ), true ) ) {
						get_sidebar( $sidebar_id );
					}
					return;
				}

				// activity directory
				if ( bp_is_current_component( 'activity' ) && ! bp_is_user() ) {
					$activity_layout = get_theme_mod( 'reign_activity_directory_sidebar_layout', 'right_sidebar' );
					if ( in_array( $activity_layout, array( 'left_sidebar', 'both_sidebar' ), true ) ) {
						get_sidebar( $sidebar_id );
					}
					return;
				}
			}

			// 6) LearnDash archive special-case: temporarily clear $post to avoid wrong metabox.
			if ( is_archive() && 'sfwd-courses' === get_post_type() ) {
				$course_post = $post;
				$post = null;
			}

			// 7) If $post exists, check its metabox (typical single/page per-post override).
			if ( isset( $post ) && $post ) {
				$meta = get_post_meta( $post->ID, "{$theme_slug}_wbcom_metabox_data", true );
				$site_layout = $meta['layout']['site_layout'] ?? '';

				if ( in_array( $site_layout, array( 'left_sidebar', 'both_sidebar' ), true ) ) {
					get_sidebar( $sidebar_id );
					// restore if changed above
					if ( isset( $course_post ) ) {
						global $post;
						$post = $course_post;
					}
					return;
				}

				if ( $site_layout ) {
					if ( isset( $course_post ) ) {
						global $post;
						$post = $course_post;
					}
					return;
				}
			}

			// restore LearnDash post if needed
			if ( isset( $course_post ) ) {
				global $post;
				$post = $course_post;
			}

			// 8) Finally, fallback to customizer per-post-type layout.
			$post_type = get_post_type();

			// On WooCommerce shop/taxonomy archive pages the left sidebar hook fires before
			// the product loop starts, so $post is still the shop page (post_type = 'page').
			// Force 'product' so the correct customizer key (reign_product_archive_layout) is used.
			if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_product_taxonomy() ) ) {
				$post_type = 'product';
			}

			$layout = is_singular()
				? get_theme_mod( "reign_{$post_type}_single_layout", 'right_sidebar' )
				: get_theme_mod( "reign_{$post_type}_archive_layout", 'right_sidebar' );

			if ( in_array( $layout, array( 'left_sidebar', 'both_sidebar' ), true ) ) {
				get_sidebar( $sidebar_id );
			}
		}

		public function render_right_sidebar_area() {
			if ( is_search() ) {
				$search_content_layout = get_theme_mod( 'reign_search_page_layout', 'right_sidebar' );
				if ( ( $search_content_layout == 'both_sidebar' ) || ( $search_content_layout == 'right_sidebar' ) ) {
					echo get_sidebar();

				}
				return;
			}

			// Bail if customizer layout is wide.
			$reign_single_post_layout = get_theme_mod( 'reign_single_post_layout', 'default' );
			if ( is_single() && 'post' === get_post_type() && ( $reign_single_post_layout === 'wide' ) ) {
				return;
			}

			global $wp_query;
			if ( isset( $wp_query ) && (bool) $wp_query->is_posts_page ) {
				$post_id = get_option( 'page_for_posts' );
				$post    = get_post( $post_id );
			} else {
				global $post;

				if ( class_exists( 'woocommerce' ) ) {
					if ( is_woocommerce() && is_archive() && get_post_type() == 'product' ) {
						$shop_page_id = get_option( 'woocommerce_shop_page_id' );
						$post         = get_post( $shop_page_id );
					}
					if ( is_woocommerce() && is_cart() ) {
						$cart_page_id = get_option( 'woocommerce_cart_page_id' );
						$post         = get_post( $cart_page_id );
					}
					if ( is_woocommerce() && is_checkout() ) {
						$checkout_page_id = get_option( 'woocommerce_checkout_page_id' );
						$post             = get_post( $checkout_page_id );
					}
					if ( is_woocommerce() && is_account_page() ) {
						$myaccount_page_id = get_option( 'woocommerce_myaccount_page_id' );
						$post              = get_post( $myaccount_page_id );
					}
				}
			}
			if ( $post ) {
				$theme_slug         = apply_filters( 'wbcom_essential_theme_slug', 'reign' );
				$wbcom_metabox_data = get_post_meta( $post->ID, $theme_slug . '_wbcom_metabox_data', true );
				$site_layout        = isset( $wbcom_metabox_data['layout']['site_layout'] ) ? $wbcom_metabox_data['layout']['site_layout'] : '';

				if ( ( $site_layout == 'both_sidebar' ) || ( $site_layout == 'right_sidebar' ) ) {
					echo get_sidebar();
					return;
				}

				if ( $site_layout ) {
					return;
				}
			}

			// global $wbtm_reign_settings;
			// $active_content_layout    = isset( $wbtm_reign_settings[ 'reign_pages' ][ 'active_content_layout' ] ) ? $wbtm_reign_settings[ 'reign_pages' ][ 'active_content_layout' ] : 'right_sidebar';

			$post_type = get_post_type();

			// On WooCommerce shop/taxonomy archive pages $post is the shop page (post_type = 'page'),
			// so force 'product' to look up the correct customizer key (reign_product_archive_layout).
			if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_product_taxonomy() ) ) {
				$post_type = 'product';
			}

			if ( is_singular() ) {
				$active_content_layout = get_theme_mod( 'reign_' . $post_type . '_single_layout', 'right_sidebar' );
			} else {
				$active_content_layout = get_theme_mod( 'reign_' . $post_type . '_archive_layout', 'right_sidebar' );
			}

			if ( ( $active_content_layout == 'both_sidebar' ) || ( $active_content_layout == 'right_sidebar' ) ) {
				echo get_sidebar();
				return;
			}
		}
	}

	endif;

/**
 * Main instance of Reign_Theme_Structure.
 *
 * @return Reign_Theme_Structure
 */
Reign_Theme_Structure::instance();
