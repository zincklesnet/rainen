<?php
/**
 * Theme functions and definitions.
 *
 * @package Reign
 */

/**
 * Main theme class.
 */
class REIGN_Theme_Class {

	/**
	 * Whether the class has been initialized.
	 *
	 * @var bool
	 */
	private static $initialized = false;


	/**
	 * Lightweight constructor.
	 *
	 * This constructor is lightweight and only sets up the basic things. The heavy initialization
	 * is deferred to the init method to prevent memory issues.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		// Only basic setup in constructor to prevent memory issues.
		if ( ! self::$initialized ) {
			// Defer heavy initialization.
			add_action( 'after_setup_theme', array( $this, 'init_theme' ), 0 );
			self::$initialized = true;
		}
	}


	/**
	 * Heavy initialization
	 *
	 * This method is called only when needed and can be cleared by memory management.
	 *
	 * @since 1.0.0
	 */
	public function init_theme() {
		// Define constants first.
		add_action( 'after_setup_theme', array( __CLASS__, 'constants' ), 1 );

		// Load files conditionally.
		add_action( 'after_setup_theme', array( __CLASS__, 'includes' ), 2 );

		// Setup BuddyPress hooks only if BP is active.
		if ( function_exists( 'buddypress' ) ) {
			$this->setup_buddypress_hooks();
		}

		// Add modern image support.
		add_filter( 'mime_types', array( $this, 'reign_theme_upload_mimes' ) );

		// Ensure file type validation for WebP/AVIF.
		add_filter( 'wp_check_filetype_and_ext', array( $this, 'reign_check_filetype' ), 10, 5 );

		// Implement lazy loading.
		add_filter( 'wp_lazy_loading_enabled', '__return_true' );
	}

	/**
	 * CONDITIONAL BUDDYPRESS SETUP - only load if needed.
	 */
	private function setup_buddypress_hooks() {
		add_filter( 'bp_get_template_stack', array( 'REIGN_Theme_Class', 'reign_bp_get_template_stack' ), 10, 1 );
		add_filter( 'bp_nouveau_get_loop_classes', array( 'REIGN_Theme_Class', 'reign_bp_nouveau_get_loop_classes' ), 10, 2 );
		add_filter( 'bp_nouveau_get_loop_classes', array( 'REIGN_Theme_Class', 'bpgt_filter_nouveau_get_loop_classes' ), 10, 2 );
		add_filter( 'bp_nouveau_get_loop_classes', array( 'REIGN_Theme_Class', 'reign_bp_nouveau_follow_get_loop_classes' ), 10, 2 );
		add_filter( 'bp_nouveau_avatar_args', array( 'REIGN_Theme_Class', 'reign_bp_nouveau_avatar_args_member_layout_1' ), 10, 1 );
		add_action( 'wp_enqueue_scripts', array( 'REIGN_Theme_Class', 'bp_nouveau_enqueue_script' ) );
	}

	/**
	 * Constants class
	 *
	 * @since 1.0.0
	 */
	public static function constants() {
		// Existing constants code stays the same.
		$theme   = wp_get_theme( get_template() );
		$version = $theme->get( 'Version' );

		// All existing constant definitions.
		define( 'REIGN_THEME_DIR', get_template_directory() );
		define( 'REIGN_THEME_URI', get_template_directory_uri() );

		if ( function_exists( 'bp_get_theme_package_id' ) ) {
			$theme_package_id = bp_get_theme_package_id();
		} else {
			$theme_package_id = 'legacy';
		}

		// Define the theme version constant.
		if ( ! defined( 'REIGN_THEME_VERSION' ) ) {
			define( 'REIGN_THEME_VERSION', $version );
		}

		// Javascript and CSS Paths.
		define( 'REIGN_JS_DIR_URI', REIGN_THEME_URI . '/assets/js/' );
		define( 'REIGN_CSS_DIR_URI', REIGN_THEME_URI . '/assets/css/' );

		// Include Paths.
		define( 'REIGN_INC_DIR', REIGN_THEME_DIR . '/inc/' );
		define( 'REIGN_INC_DIR_URI', REIGN_THEME_URI . '/inc/' );

		// Check if plugins are active.
		define( 'REIGN_ELEMENTOR_ACTIVE', class_exists( 'Elementor\Plugin' ) );
		define( 'REIGN_BEAVER_BUILDER_ACTIVE', class_exists( 'FLBuilder' ) );
		define( 'REIGN_WOOCOMMERCE_ACTIVE', class_exists( 'WooCommerce' ) );

		$option_key = 'reign_theme_is_activated';
		if ( ! get_option( $option_key ) ) {

			/**
			 * Initial setup of BuddyPress Nouveau settings
			 */
			$bp_nouveau_appearance = array(
				'members_layout'         => 3,
				'members_friends_layout' => 2,
				'groups_layout'          => 3,
				'members_group_layout'   => 2,
				'group_front_page'       => 0,
				'group_front_boxes'      => 0,
				'user_front_page'        => 0,
				'user_nav_display'       => 1,
				'group_nav_display'      => 1,
			);
			update_option( 'bp_nouveau_appearance', $bp_nouveau_appearance );
			update_option( $option_key, 1 );
		}
	}

	/**
	 * Enqueue scripts conditionally
	 *
	 * @since 1.0.0
	 */
	public static function bp_nouveau_enqueue_script() {

		// Set the JS extension based on SCRIPT_DEBUG.
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$js_extension = '.js';
		} else {
			$js_extension = '.min.js';
		}

		// Enqueue community scripts and dashicons only on BuddyPress pages.
		if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss ) && ! is_user_logged_in() && reign_is_buddypress_page() ) {
			do_action( 'bp_enqueue_community_scripts' );
			wp_enqueue_style( 'dashicons' );
		}

		// Enqueue xprofile and magnific-popup only on BuddyPress pages.
		if ( function_exists( 'buddypress' ) && ! is_user_logged_in() && reign_is_buddypress_page() ) {
			wp_enqueue_script( 'bp-nouveau-xprofile' );
			wp_enqueue_script( 'bp-nouveau-magnific-popup' );
		}

		// Enqueue custom script if theme package is 'nouveau'.
		if ( function_exists( 'bp_get_theme_package_id' ) ) {
			$theme_package_id = bp_get_theme_package_id();
		} else {
			$theme_package_id = 'legacy';
		}

		if ( 'nouveau' === $theme_package_id ) {
			wp_register_script(
				'reign-nouveau-js',
				get_template_directory_uri() . '/assets/js/reign-nouveau.min.js',
				array( 'jquery' ),
				REIGN_THEME_VERSION,
				true
			);
			wp_enqueue_script( 'reign-nouveau-js' );
		}

		// Enqueue scripts for infinite scroll pagination.
		$blog_list_layout = get_theme_mod( 'reign_blog_list_pagination', '' );

		if ( '' !== $blog_list_layout && 'reign_blog_infinite_scroll_pagination' === $blog_list_layout ) {
			wp_enqueue_script( 'reign-infinity-scroll-js', get_template_directory_uri() . '/assets/js/vendors/infinite-scroll.pkgd.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
			wp_enqueue_script( 'reign-custom-nouveau-js', get_template_directory_uri() . '/assets/js/custom.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
		}
	}

	/**
	 * Adjust avatar arguments for member layout 1.
	 *
	 * @param array $args Avatar arguments.
	 * @return array Modified avatar arguments.
	 */
	public static function reign_bp_nouveau_avatar_args_member_layout_1( $args ) {
		// Check if we are in the groups directory.
		if ( bp_is_groups_directory() ) {
			// Get BuddyPress Nouveau appearance settings.
			$bp_nouveau_appearance = bp_get_option( 'bp_nouveau_appearance', array() );

			// Set default groups layout if not set.
			if ( ! isset( $bp_nouveau_appearance['groups_layout'] ) ) {
				$bp_nouveau_appearance['groups_layout'] = 1;
			}

			// Check if groups layout is not the default.
			if ( 1 !== $bp_nouveau_appearance['groups_layout'] ) {
				// Access global settings.
				global $wbtm_reign_settings;
				$group_directory_type = isset( $wbtm_reign_settings['reign_buddyextender']['group_directory_type'] ) ? $wbtm_reign_settings['reign_buddyextender']['group_directory_type'] : 'wbtm-group-directory-type-2';

				// Adjust avatar dimensions for specific group directory type.
				if ( 'wbtm-group-directory-type-1' === $group_directory_type ) {
					$args['width']  = 300;
					$args['height'] = 300;
				}
			}
		}

		// Return modified arguments.
		return $args;
	}

	/**
	 * Modify loop classes for BuddyPress requests.
	 *
	 * Adjusts the classes applied to the member loop when viewing friend requests.
	 *
	 * @param array  $classes  Existing classes.
	 * @param string $component Component being viewed.
	 * @return array Modified classes.
	 */
	public static function reign_bp_nouveau_get_loop_classes( $classes, $component ) {
		// Check if the current view is a user page and the action is 'requests'.
		if ( bp_is_user() && 'requests' === bp_current_action() ) {
			// Remove specific classes if they exist in the current classes array.
			$index = array_search( 'friends-list', $classes, true );
			if ( false !== $index ) {
				unset( $classes[ $index ] );
			}

			$index = array_search( 'friends-request-list', $classes, true );
			if ( false !== $index ) {
				unset( $classes[ $index ] );
			}

			// Retrieve customization settings for layout preferences.
			$customizer_option = sprintf( '%s_layout', 'members_friends' );
			$layout_prefs      = bp_nouveau_get_temporary_setting(
				$customizer_option,
				bp_nouveau_get_appearance_settings( $customizer_option )
			);

			// If specific layout preferences are set, apply grid classes.
			if ( $layout_prefs && (int) $layout_prefs > 1 && function_exists( 'bp_nouveau_customizer_grid_choices' ) ) {
				$grid_classes = bp_nouveau_customizer_grid_choices( 'classes' );

				if ( isset( $grid_classes[ $layout_prefs ] ) ) {
					$classes = array_merge(
						$classes,
						array(
							'grid',
							$grid_classes[ $layout_prefs ],
						)
					);
				}
			}

			// Add specific classes for the requests view.
			$classes = array_merge(
				$classes,
				array(
					'members-list',
					'rg-nouveau-friends-request-list',
				)
			);
		}

		return $classes;
	}

	/**
	 * Filter loop classes for BP Follow component.
	 *
	 * @param array  $classes   Existing classes.
	 * @param string $component Component being viewed.
	 * @return array Modified classes.
	 */
	public static function reign_bp_nouveau_follow_get_loop_classes( $classes, $component ) {

		global $bp;
		if ( class_exists( 'BP_Follow_Component' ) ) {
			if ( bp_is_current_component( $bp->follow->followers->slug ) || bp_is_current_component( $bp->follow->following->slug ) ) {

				$layout_prefs = bp_nouveau_get_temporary_setting(
					'members_layout',
					bp_nouveau_get_appearance_settings( 'members_layout' )
				);
				if ( $layout_prefs && (int) $layout_prefs > 1 && function_exists( 'bp_nouveau_customizer_grid_choices' ) ) {
					$grid_classes = bp_nouveau_customizer_grid_choices( 'classes' );

					if ( isset( $grid_classes[ $layout_prefs ] ) ) {
						$classes = array_merge(
							$classes,
							array(
								'grid',
								$grid_classes[ $layout_prefs ],
							)
						);
					}
				}

				$classes = array_merge(
					$classes,
					array(
						'members-list',
						'rg-nouveau-friends-request-list',
					)
				);
			}
		}
		return $classes;
	}

	/**
	 * Filter loop classes for BP groups component.
	 *
	 * @param array  $classes   Existing classes.
	 * @param string $component Component being viewed.
	 * @return array Modified classes.
	 */
	public static function bpgt_filter_nouveau_get_loop_classes( $classes, $component ) {
		if ( 'groups' === $component ) {
			$layout_prefs = bp_nouveau_get_temporary_setting(
				'groups_layout',
				bp_nouveau_get_appearance_settings( 'groups_layout' )
			);
			if ( $layout_prefs && (int) $layout_prefs > 1 && function_exists( 'bp_nouveau_customizer_grid_choices' ) ) {
				$grid_classes = bp_nouveau_customizer_grid_choices( 'classes' );

				if ( isset( $grid_classes[ $layout_prefs ] ) ) {
					$classes = array_merge(
						$classes,
						array(
							'grid',
							$grid_classes[ $layout_prefs ],
						)
					);
				}
			}
		}

		return $classes;
	}

	/**
	 * Filter BuddyPress template stack to use BP Nouveau or BuddyBoss directories.
	 *
	 * @param array $stack Template stack.
	 * @return array Modified template stack.
	 */
	public static function reign_bp_get_template_stack( $stack ) {
		if ( function_exists( 'bp_get_theme_package_id' ) ) {
			$theme_package_id = bp_get_theme_package_id();
		} else {
			$theme_package_id = 'legacy';
		}

		if ( 'nouveau' === $theme_package_id ) {
			if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
				$index = array_search( get_stylesheet_directory() . '/buddypress', $stack, true );
				if ( false !== $index ) {
					$stack[ $index ] = get_stylesheet_directory() . '/bb-buddypress';
				}
				$index = array_search( get_template_directory() . '/buddypress', $stack, true );
				if ( false !== $index ) {
					$stack[ $index ] = get_template_directory() . '/bb-buddypress';
				}
			} else {
				$index = array_search( get_stylesheet_directory() . '/buddypress', $stack, true );
				if ( false !== $index ) {
					$stack[ $index ] = get_stylesheet_directory() . '/bp-nouveau';
				}
				$index = array_search( get_template_directory() . '/buddypress', $stack, true );
				if ( false !== $index ) {
					$stack[ $index ] = get_template_directory() . '/bp-nouveau';
				}
			}
		}
		return $stack;
	}

	/**
	 * Load all core theme function files
	 *
	 * @since 1.0.0
	 */
	public static function includes() {

		include_once REIGN_THEME_DIR . '/inc/reign-deprecated-hooks.php';

		/**
		 * Boot the in-house Customizer Framework + autoloader FIRST.
		 *
		 * Must come before class-reign-theme-structure.php (and any other
		 * include below) because the bootstrap file is the source of the
		 * reign_is_truthy() helper that other theme files call at file
		 * scope or from constructor-time hook registrations. Loading the
		 * structure class before this bootstrap caused a fatal in 8.0.0
		 * (Phase 7 truthy sweep landed render-time calls inside init_hooks(),
		 * which self-fires from the file-scope ::instance() at the bottom
		 * of class-reign-theme-structure.php).
		 *
		 * Also publishes the PSR-4 autoloader for Reign\Customizer_Framework\*
		 * etc., so any class instantiation below resolves cleanly.
		 *
		 * @since 8.0.0
		 */
		include_once REIGN_THEME_DIR . '/inc/customizer-framework-bootstrap.php';

		/**
		 * Performance hooks — front-end-only resource trims (disable WP
		 * emoji, dequeue jQuery Migrate, remove wp-embed + oEmbed
		 * discovery). Loaded early so its `init` and `wp_default_scripts`
		 * hooks register before WordPress core fires them.
		 *
		 * @since 8.0.0
		 */
		include_once REIGN_THEME_DIR . '/inc/performance-hooks.php';

		include_once REIGN_THEME_DIR . '/inc/theme-functions.php';
		include_once REIGN_THEME_DIR . '/inc/class-reign-theme-structure.php';
		include_once REIGN_THEME_DIR . '/inc/reign-color-setup.php';

		/**
		 * Register Reign block pattern categories (reign-hero / reign-about /
		 * reign-features / reign-social-proof / reign-pricing-faq / reign-cta /
		 * reign-footer / reign-query) used by the 27 universal patterns under
		 * patterns/ that WP auto-discovers.
		 *
		 * @since 8.0.0
		 */
		include_once REIGN_THEME_DIR . '/inc/block-pattern-categories.php';

		/**
		 * Load Theme JSON Bridge component for WordPress block editor integration.
		 */
		include_once REIGN_THEME_DIR . '/inc/theme-json-bridge.php';

		/**
		 * Load Reign's customizer panel/section/field registrations via the
		 * in-house Customizer Framework.
		 *
		 * Kirki was removed in 8.0.0; the framework is loaded by
		 * inc/customizer-framework-bootstrap.php above. This loader requires
		 * every file under inc/Customizer_Settings/Fields/ which self-register
		 * via singleton ::instance() calls.
		 *
		 * @since 8.0.0
		 */
		include_once REIGN_THEME_DIR . '/inc/Customizer_Settings/loader.php';

		/**
		 * Include the custom-styles file of Kirki.
		 */
		include_once REIGN_THEME_DIR . '/inc/custom-styles.php';

		/**
		 * Load reign theme blocks.
		 *
		 * @since 7.4.5
		 */
		include_once REIGN_THEME_DIR . '/blocks/blocks.php';

		/* Theme Core Setup */
		require_once REIGN_INC_DIR . 'init.php';

		/* Theme Added Functionality Setup */
		require_once REIGN_INC_DIR . 'extras.php';

		/* Support Added For Extra Plugins */
		require_once REIGN_INC_DIR . 'extra-plugins-support.php';

		/* Featured Image Position */
		require_once REIGN_INC_DIR . 'featured-image-position.php';

		/* Load conditional assets loader */
		require_once REIGN_INC_DIR . 'conditional-assets-loader.php';

		/* Load required plugins library */
		require_once REIGN_INC_DIR . 'reign-menu-icons/menu-icons.php';
		require_once REIGN_INC_DIR . 'reign-settings/class-left-menu-section.php';

		/* Custom Widgets */
		require REIGN_THEME_DIR . '/widgets/members-carousel-widget.php';
		require REIGN_THEME_DIR . '/widgets/sidewide-activity-widget.php';
		require REIGN_THEME_DIR . '/widgets/groups-widget.php';
		require REIGN_THEME_DIR . '/widgets/groups-carousel-widget.php';
		require REIGN_THEME_DIR . '/widgets/latest-news.php';
		require REIGN_THEME_DIR . '/widgets/bp-profile-completion-widget.php';
		require REIGN_THEME_DIR . '/widgets/login-widget.php';

		/* Added WooCommerce Support Files */
		if ( class_exists( 'WooCommerce' ) ) {
			require_once REIGN_INC_DIR . 'woocommerce/woocommerce-functions.php';
			require REIGN_THEME_DIR . '/widgets/woocommerce/class-reign-woo-widget-product-categories.php';
		}

		/* Added LearnDash Support Files */
		if ( class_exists( 'SFWD_LMS' ) && ! class_exists( 'LearnMate_LearnDash_Addon' ) ) {
			require_once REIGN_INC_DIR . 'plugins-support/learndash/reign-learndash-functions.php';
		}

		/* Added Dokan Support Files */
		if ( class_exists( 'WeDevs_Dokan' ) && ! class_exists( 'Reign_Dokan_Addon' ) ) {
			require_once REIGN_INC_DIR . 'plugins-support/dokan/reign-dokan-functions.php';
		}

		/* Added Sensei LMS Support Files */
		if ( class_exists( 'Sensei_Main' ) && ! class_exists( 'Reign_Sensei_Addon' ) ) {
			require_once REIGN_INC_DIR . 'plugins-support/sensei/reign-sensei-functions.php';
		}

		/* Added LifterLMS Support Files */
		if ( class_exists( 'LifterLMS' ) && ! class_exists( 'Reign_LifterLMS_Addon' ) ) {
			require_once REIGN_INC_DIR . 'plugins-support/lifterlms/reign-lifterlms-functions.php';
		}

		/* Added TutorLMS Support Files */
		if ( function_exists( 'tutor' ) && ! class_exists( 'Reign_Tutorlms_Addon' ) ) {
			require_once REIGN_INC_DIR . 'plugins-support/tutorlms/reign-tutorlms-functions.php';
		}

		/* Added WC Vendors Support Files */
		if ( class_exists( 'WC_Vendors' ) && ! class_exists( 'Reign_Wcvendors_Addon' ) ) {
			require_once REIGN_INC_DIR . 'plugins-support/wc-vendors/reign-wc-vendors-functions.php';
		}

		/* Added WP Job Manager Support Files */
		if ( class_exists( 'WP_Job_Manager' ) && ! class_exists( 'Reign_WP_Job_Manager_Addon' ) ) {
			require_once REIGN_INC_DIR . 'plugins-support/wp-job-manager/wp-job-manager-functions.php';
		}

		/* Added MediaPress Support Files */
		if ( class_exists( 'MediaPress' ) ) {
			require_once REIGN_INC_DIR . 'plugins-support/mediapress/class-rtm-mpp-customization.php';
		}

		if ( class_exists( 'PeepSo' ) ) {
			require REIGN_THEME_DIR . '/widgets/peepso/widgetuserbar.php';
		}

		/* Wbcom companion plugin installer (one-click EDD install, replaces TGMPA). */
		require_once REIGN_INC_DIR . 'required-plugins/class-reign-plugin-installer.php';

		/* Theme Options Panel In Admin Dashboard */
		require_once REIGN_INC_DIR . 'reign-settings/reign-theme-options-manager.php';
		require_once REIGN_INC_DIR . 'reign-settings/option-functions.php';

		/* Theme License And Update Management */
		require_once REIGN_INC_DIR . 'edd-updater/theme-updater.php';
		require_once REIGN_INC_DIR . 'reign-settings/class-reign-license-manager.php';

		/* Include Shortcodes file */
		require_once REIGN_INC_DIR . 'shortcodes/shortcodes.php';

		/* Include postmeta management file */
		include_once REIGN_THEME_DIR . '/inc/wbcom-postmeta-mgmt/wbcom-postmeta-mgmt.php';

		if ( class_exists( 'BuddyPress' ) ) {

			/* Special Support To BuddyPress */
			require REIGN_THEME_DIR . '/inc/buddypress/buddypress-functions.php';

			/* Social links xprofile customization file */
			include_once REIGN_THEME_DIR . '/inc/buddypress/reign-social-links-xprofile.php';

			/* Header view xprofile customization file */
			include_once REIGN_THEME_DIR . '/inc/buddypress/reign-header-view-xprofile.php';

			/* Include buddypress members customization file */
			include_once REIGN_THEME_DIR . '/inc/buddypress/reign-bp-member-customization.php';

			/* Include buddypress groups customization file */
			include_once REIGN_THEME_DIR . '/inc/buddypress/reign-bp-group-customization.php';
		} elseif ( defined( 'BUDDYNEXT_VERSION' ) ) {

			/* Special Support To BuddyNext (community engine; runtime-exclusive
			   with BuddyPress). Wires the BuddyNext header user section + feeds
			   the "User Profile" (menu-2) nav menu into its dropdown. */
			require_once REIGN_THEME_DIR . '/inc/plugins-support/buddynext/reign-buddynext-functions.php';
		}

		// Call rtmedia functions file.
		if ( class_exists( 'RTMedia' ) ) {
			require REIGN_THEME_DIR . '/inc/buddypress/rtmedia-functions.php';
		}

		/**
		 * Plugin specific support.
		 */
		if ( defined( 'PMPRO_VERSION' ) ) {
			include_once REIGN_INC_DIR . 'plugins-support/class-rtm-pmpro-customization.php';
		}

		if ( class_exists( 'Easy_Digital_Downloads' ) ) {
			include_once REIGN_INC_DIR . 'plugins-support/class-rtm-edd-customization.php';
		}

		if ( class_exists( 'PeepSo' ) ) {
			include_once REIGN_INC_DIR . 'plugins-support/class-reign-walker-nav-menu-checklist.php';
		}

		if ( class_exists( 'WooCommerce' ) ) {
			include_once REIGN_INC_DIR . 'plugins-support/class-rtm-woocommerce-customization.php';
		}

		// Custom Login.
		include_once REIGN_THEME_DIR . '/inc/login.php';

		// SureCart Support.
		if ( defined( 'SURECART_PLUGIN_FILE' ) ) {
			include_once REIGN_THEME_DIR . '/inc/surecart-support.php';
		}

		// FluentCart Support.
		if ( defined( 'FLUENTCART_PLUGIN_FILE_PATH' ) ) {
			include_once REIGN_THEME_DIR . '/inc/fluentcart-support.php';
		}

		// Page Builder Compatibility System.
		require REIGN_THEME_DIR . '/inc/compatibility/page-builders/class-reign-page-builder-manager.php';
	}

	/**
	 * Add Modern Image Support.
	 *
	 * @param array $file_types Allowed MIME types.
	 * @return array Modified MIME types.
	 */
	public function reign_theme_upload_mimes( $file_types ) {
		// Allow SVG.
		$file_types['svg'] = 'image/svg+xml';

		// Allow WebP.
		$file_types['webp'] = 'image/webp';

		// Allow AVIF (some browsers and older WordPress versions may not fully support this yet).
		$file_types['avif'] = 'image/avif';

		return $file_types;
	}

	/**
	 * Fix file type detection for WebP and AVIF.
	 *
	 * @param array       $data      File data array containing ext, type, proper_filename.
	 * @param string      $file      Full path to the file.
	 * @param string      $filename  The filename being sanitized.
	 * @param array       $mimes     Key is the file extension with value as the mime type.
	 * @param string|bool $real_mime The actual mime type or false if the type cannot be determined.
	 * @return array Modified file data.
	 */
	public function reign_check_filetype( $data, $file, $filename, $mimes, $real_mime ) {
		$ext = pathinfo( $filename, PATHINFO_EXTENSION );

		if ( 'webp' === $ext ) {
			$data['ext']  = 'webp';
			$data['type'] = 'image/webp';
		}

		if ( 'avif' === $ext ) {
			$data['ext']  = 'avif';
			$data['type'] = 'image/avif';
		}

		return $data;
	}
}


/**
 * Get REIGN theme instance.
 *
 * Singleton implementation - theme instance is created on first call
 * and reused on subsequent calls.
 *
 * @return REIGN_Theme_Class Theme instance
 */
function reign_theme() {
	static $theme = null;

	if ( null === $theme ) {
		$theme = new REIGN_Theme_Class();
	}

	return $theme;
}
reign_theme();



/**
 * Call switch theme to child theme and update parent theme mod to child theme.
 *
 * @param string   $new_name  The new theme's name.
 * @param WP_Theme $new_theme The new theme object.
 * @param WP_Theme $old_theme The old theme object.
 */
function reign_theme_switch_to_child_theme( $new_name, $new_theme, $old_theme ) {

	if ( 'REIGN Child' === $new_name ) {
		$theme_mods = get_option( 'theme_mods_' . get_template(), true );
		update_option( 'theme_mods_' . get_stylesheet(), $theme_mods );
	}
}

add_action( 'switch_theme', 'reign_theme_switch_to_child_theme', 99, 3 );

/**
 * WP_Navwalker class.
 */
class Reign_Left_Panel_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Starts the element output.
	 *
	 * @param string   $output Used to append additional content (passed by reference).
	 * @param WP_Post  $item Menu item data object.
	 * @param int      $depth Depth of menu item. Used for padding.
	 * @param stdClass $args An object of wp_nav_menu() arguments.
	 * @param int      $id Current item ID.
	 *
	 * @see Walker_Nav_Menu::start_el()
	 *
	 * @since Reign 7.1.2
	 * @since WP 4.4.0 The {@see 'nav_menu_item_args'} filter was added.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
			$t = '';
			$n = '';
		} else {
			$t = "\t";
			$n = "\n";
		}
		$indent = ( $depth ) ? str_repeat( $t, $depth ) : '';

		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		// Left panel section.
		if ( isset( $item->post_content ) && 'reign-theme-section' === $item->post_content ) {
			$classes[] = 'reign-menu-section';
		}

		// Stick to bottom of the menu.
		if ( isset( $item->stick_to_bottom ) && '1' === $item->stick_to_bottom ) {
			$classes[] = 'bp-menu-item-at-bottom';
		}

		// Add the count for the messages in Left panel.
		if (
			function_exists( 'bp_is_active' ) &&
			bp_is_active( 'messages' ) &&
			function_exists( 'bp_loggedin_user_id' ) &&
			function_exists( 'bp_get_messages_slug' ) &&
			in_array( 'bp-' . bp_get_messages_slug() . '-nav', $classes, true )
		) {
			$classes[] = 'bp-left-panel-menu-item-' . bp_get_messages_slug() . '-count-' . bp_loggedin_user_id();
		}

		/**
		 * Filters the arguments for a single nav menu item.
		 *
		 * @since 4.4.0
		 *
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param WP_Post  $item  Menu item data object.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

		/**
		 * Filters the CSS class(es) applied to a menu item's list item element.
		 *
		 * @since 3.0.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array    $classes The CSS classes that are applied to the menu item's `<li>` element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		/**
		 * Filters the ID applied to a menu item's list item element.
		 *
		 * @since 3.0.1
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
		 * @param WP_Post  $item    The current menu item.
		 * @param stdClass $args    An object of wp_nav_menu() arguments.
		 * @param int      $depth   Depth of menu item. Used for padding.
		 */
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $class_names . '>';

		$atts           = array();
		$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
		$atts['target'] = ! empty( $item->target ) ? $item->target : '';
		$atts['rel']    = ! empty( $item->xfn ) ? $item->xfn : '';
		$atts['href']   = ! empty( $item->url ) ? $item->url : '';

		/**
		 * Filters the HTML attributes applied to a menu item's anchor element.
		 *
		 * @since 3.6.0
		 * @since 4.1.0 The `$depth` parameter was added.
		 *
		 * @param array $atts {
		 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
		 *
		 *     @type string $title  Title attribute.
		 *     @type string $target Target attribute.
		 *     @type string $rel    The rel attribute.
		 *     @type string $href   The href attribute.
		 * }
		 * @param WP_Post  $item  The current menu item.
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';
		foreach ( $atts as $attr => $value ) {
			if ( ! empty( $value ) ) {
				$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
				$attributes .= ' ' . $attr . '="' . $value . '"';
			}
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );

		/**
		 * Filters a menu item's title.
		 *
		 * @since 4.4.0
		 *
		 * @param string   $title The menu item's title.
		 * @param WP_Post  $item  The current menu item.
		 * @param stdClass $args  An object of wp_nav_menu() arguments.
		 * @param int      $depth Depth of menu item. Used for padding.
		 */
		$title        = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
		$item_output  = ( isset( $args->before ) ? $args->before : '' );
		$item_output .= '<a' . $attributes . '>';
		$item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . $title . ( isset( $args->link_after ) ? $args->link_after : '' );
		$item_output .= '</a>';
		$item_output .= ( isset( $args->after ) ? $args->after : '' );

		/**
		 * Filters a menu item's starting output.
		 *
		 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
		 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
		 * no filter for modifying the opening and closing `<li>` for a menu item.
		 *
		 * @since 3.0.0
		 *
		 * @param string   $item_output The menu item's starting HTML output.
		 * @param WP_Post  $item        Menu item data object.
		 * @param int      $depth       Depth of menu item. Used for padding.
		 * @param stdClass $args        An object of wp_nav_menu() arguments.
		 */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

// Include minimal security plugin compatibility.
require_once get_template_directory() . '/inc/security-compatibility.php';

// Include Better Messages compatibility.
require_once get_template_directory() . '/inc/better-messages-support.php';
