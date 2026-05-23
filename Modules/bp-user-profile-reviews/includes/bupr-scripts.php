<?php
/**
 * Class to add custom scripts and styles.
 *
 * @since    1.0.0
 * @author   Wbcom Designs
 * @package BuddyPress_Member_Reviews
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BUPRScriptsStyles' ) ) {
	/**
	 * Class to add custom scripts and styles.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @author   Wbcom Designs
	 */
	class BUPRScriptsStyles {

		/**
		 * Constructor.
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function __construct() {

			// Add Scripts only on reviews tab.
			add_action( 'wp_enqueue_scripts', array( $this, 'bupr_public_enqueue_scripts' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'bupr_public_enqueue_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'bupr_admin_enqueue_scripts' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'bupr_admin_enqueue_styles' ) );
		}


		/**
         * Determine if scripts should be loaded on current page.
         *
         * @access   private
         * @return   boolean  Whether scripts should be loaded.
         */
        private function should_load_frontend_assets() {
            // Don't load on admin or login pages
            if (is_admin() || wp_doing_ajax()) {
                return false;
            }
            
            // Skip if BuddyPress is not active
            if (!function_exists('buddypress') || !function_exists('bp_is_active')) {
                return false;
            }
            
            // Always load on these BuddyPress pages
            $load_on_pages = array(
                'bp_is_members_directory', // Members directory
                'bp_is_user',              // User profiles
                'bp_is_user_profile',      // User profile specific page
                'bp_is_activity_component' // Activity pages (for review activities)
            );
            
            foreach ($load_on_pages as $conditional) {
                if (function_exists($conditional) && call_user_func($conditional)) {
                    return true;
                }
            }
            
            // Check for shortcodes in current post content
            global $post;
            if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'bupr_display_top_members')) {
                return true;
            }
            
            // Allow other plugins to add their own conditions
            return apply_filters('bupr_should_load_assets', false);
        }

		/**
		 * Actions performed for enqueuing styles for site front
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_public_enqueue_styles() {
			if (!$this->should_load_frontend_assets()) {
                return;
            }
			
			if( ! bp_is_groups_directory() ) {
				global $bupr;

				if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
					$extension = is_rtl() ? '.rtl.css' : '.css';
					$path      = is_rtl() ? '/rtl' : '';
				} else {
					$extension = is_rtl() ? '.rtl.css' : '.min.css';
					$path      = is_rtl() ? '/rtl' : '/min';
				}
				 // Determine file paths based on debug mode and RTL support
				wp_enqueue_style( 'bupr-reviews-css', BUPR_PLUGIN_URL . 'assets/css' .$path. '/bupr-reviews'. $extension, array(), BUPR_PLUGIN_VERSION, false );
				wp_enqueue_style( 'bupr-front-css', BUPR_PLUGIN_URL . 'assets/css' .$path. '/bupr-front'. $extension, array(), BUPR_PLUGIN_VERSION, false );
				// Load Font Awesome from registered WordPress source if available, otherwise use CDN
				if (!wp_style_is('font-awesome', 'registered')) {
					wp_register_style('bupr-font-awesome', 'https://use.fontawesome.com/releases/v5.4.2/css/all.css', array(), '5.4.2');
				} else {
					wp_register_style('bupr-font-awesome', '', array('font-awesome'), null);
				}

				// If BuddyBoss theme is active, load its Font Awesome version
				if ( get_template() === 'buddyboss-theme') {
					wp_enqueue_style('bupr-font-awesome-bb-theme', 'https://use.fontawesome.com/releases/v5.4.2/css/all.css', array(), '5.4.2');
				}
				wp_enqueue_style('bupr-font-awesome');
				if (isset($bupr['rating_color']) && !empty($bupr['rating_color'])) {
					$bupr_star_color = $bupr['rating_color'];
					$custom_css = ".bupr-star-rate { color: {$bupr_star_color} !important; }";
					wp_add_inline_style( 'bupr-front-css', $custom_css );
				}
			}
		}

		
		/**
		 * Actions performed for enqueuing scripts for site front
		 *
		 * @since    3.3.2
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_public_enqueue_scripts() {

			 // Only load assets when needed
            if (!$this->should_load_frontend_assets()) {
                return;
            }

			if( ! bp_is_groups_directory() ) {
				
				global $bupr;
			
				if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
					$extension = '.js';
					$path      = '';
				} else {
					$extension = '.min.js';
					$path      = '/min';
				}
				wp_register_script( 'bupr-front-js', BUPR_PLUGIN_URL . 'assets/js' .$path. '/bupr-front'. $extension, array( 'jquery' ), BUPR_PLUGIN_VERSION, true );
				wp_enqueue_script( 'bupr-front-js' );
				$title_review   = bupr_profile_review_tab_singular_slug();
				$cur_name       = bp_get_displayed_user_fullname();
				$reviews_titles = array(
					'cur_username' => $cur_name,
					'review_title' => $title_review,
					'review_nonce' => wp_create_nonce('review-nonce'),
				);
				wp_localize_script( 'bupr-front-js', 'mail_title', $reviews_titles );
				wp_set_script_translations( 'bupr-front-js', 'bp-member-reviews' );
			}
		}

		/**
		 * Actions performed for enqueuing scripts for admin page
		 *
		 * @since    1.0.0
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_admin_enqueue_scripts($hook) {

			if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
				$extension = '.js';
				$path      = '';
			} else {
				$extension = '.min.js';
				$path      = '/min';
			}
			 $bupr_pages = array(
                'toplevel_page_bp-member-review-settings',
                'reviews_page_bp-member-review-settings',
                'edit.php' // For post type review
            );
			
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				 $is_review_page = isset($screen->post_type) && 'review' === $screen->post_type;
				 $is_settings_page = isset($_GET['page']) && ('bp-member-review-settings' === $_GET['page'] || 'wbcomplugins' === $_GET['page']);
            
				if (!$is_review_page && !$is_settings_page && !in_array($hook, $bupr_pages)) {
					return;
				}
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				
				if ( ! wp_script_is( 'wbcom-select2-js', 'enqueued' ) ) {
					wp_enqueue_script( 'wbcom-select2-js', BUPR_PLUGIN_URL . 'admin/assets/js/vendor/select2.min.js', array( 'jquery' ), BUPR_PLUGIN_VERSION, false );
				}
				wp_register_script( 'bupr-js-admin', BUPR_PLUGIN_URL . 'admin/assets/js' .$path. '/bupr-admin' .$extension, array( 'jquery' ), BUPR_PLUGIN_VERSION, false );
				wp_enqueue_script( 'bupr-js-admin' );

				wp_localize_script(
					'bupr-js-admin',
					'bupr_admin_ajax_object',
					array(
						'ajaxurl'     		  => admin_url( 'admin-ajax.php' ),
						'success_msz' 		  => esc_html__( 'Already Installed & Activated', 'bp-member-reviews' ),
						'error_msz'   		  => esc_html__( 'There was a problem performing the action.', 'bp-member-reviews' ),
						'number_validation'   => esc_html__( '* Number input is not allowed *', 'bp-member-reviews' ),
						'nonce'           	  => wp_create_nonce( 'bupr_member_review_ajax' ),
					)
				);
				
				if ($is_settings_page) {
					/* add wp color picker */
					wp_enqueue_script( 'bupr-color-picker', BUPR_PLUGIN_URL . 'admin/assets/js' .$path. '/bupr-color-picker'. $extension, array( 'wp-color-picker' ), BUPR_PLUGIN_VERSION, false );
				}
				wp_set_script_translations( 'bupr-js-admin', 'bp-member-reviews' );
			}
		}

		/**
		 * Actions performed for enqueuing styles for admin page
		 *
		 * @since    3.3.2
		 * @access   public
		 * @author   Wbcom Designs
		 */
		public function bupr_admin_enqueue_styles() {
			global $typenow, $bupr;
			if ( function_exists( 'get_current_screen' ) ) {
				$screen = get_current_screen();
				$is_review_page = isset($screen->post_type) && 'review' === $screen->post_type;
				$is_settings_page = isset($_GET['page']) && ('bp-member-review-settings' === $_GET['page'] || 'wbcomplugins' === $_GET['page']);
				// Only load on relevant pages
				if (!$is_review_page && !$is_settings_page) {
					return;
				}
				
				if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
					$extension = is_rtl() ? '.rtl.css' : '.css';
					$path      = is_rtl() ? '/rtl' : '';
				} else {
					$extension = is_rtl() ? '.rtl.css' : '.min.css';
					$path      = is_rtl() ? '/rtl' : '/min';
				}

				
				if ( ! wp_style_is( 'font-awesome', 'enqueued' ) ) {
					wp_enqueue_style( 'font-awesome', '//use.fontawesome.com/releases/v5.5.0/css/all.css', array(), BUPR_PLUGIN_VERSION, false );
				}
				if ( ! wp_style_is( 'wbcom-select2-css', 'enqueued' ) ) {
					wp_enqueue_style( 'wbcom-select2-css', BUPR_PLUGIN_URL . 'admin/assets/css/vendor/select2.min.css', array(), BUPR_PLUGIN_VERSION, false );
				}
				wp_enqueue_style( 'bupr-css-admin', BUPR_PLUGIN_URL . 'admin/assets/css' .$path. '/bupr-admin'. $extension, array(), BUPR_PLUGIN_VERSION, false  );
				if ( ! wp_style_is( 'wp-color-picker', 'enqueued' ) && $is_settings_page)  {
					wp_enqueue_style( 'wp-color-picker' );
				}
			}
			
			if( $is_review_page) {

				wp_enqueue_style( 'bupr-front-css', BUPR_PLUGIN_URL . 'assets/css' .$path. '/bupr-front'. $extension, array(), BUPR_PLUGIN_VERSION, false );
				if (isset($bupr['rating_color']) && !empty($bupr['rating_color'])) {
					$bupr_star_color = $bupr['rating_color'];
					$custom_css = ".bupr-star-rate { color: {$bupr_star_color} !important; }";
					wp_add_inline_style( 'bupr-front-css', $custom_css );
				}
			}
		}
	}
	new BUPRScriptsStyles();
}
