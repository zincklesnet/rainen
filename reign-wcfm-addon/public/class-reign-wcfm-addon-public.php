<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Reign_Wcfm_Addon
 * @subpackage Reign_Wcfm_Addon/public
 */
// Prevent direct access to the file
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Reign_Wcfm_Addon
 * @subpackage Reign_Wcfm_Addon/public
 * @author     Wbcom Designs <admin@wbcomdesigns.com>
 */
class Reign_Wcfm_Addon_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$css_extension = '.css';
		} else {
			$css_extension = '.min.css';
		}

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Reign_Wcfm_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Reign_Wcfm_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		$css_path = is_rtl() ? 'css/rtl' : 'css';

		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . $css_path . '/reign-wcfm-addon-public' . $css_extension, array(), $this->version, 'all' ); 


		if( $this->reign_wcfm_should_enqueue_scripts_for_bp() || $this->reign_wcfm_should_enqueue_scripts_for_wc() ){

			wp_enqueue_style( $this->plugin_name ); 
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$js_extension = '.js';
		} else {
			$js_extension = '.min.js';
		}

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Reign_Wcfm_Addon_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Reign_Wcfm_Addon_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/reign-wcfm-addon-public' . $js_extension, array( 'jquery' ), $this->version, false );
		if( $this->reign_wcfm_should_enqueue_scripts_for_bp() || $this->reign_wcfm_should_enqueue_scripts_for_wc() ){

			wp_enqueue_script( $this->plugin_name ); 
		}
		wp_localize_script(
			$this->plugin_name,
			'reign_wcfm_addon_js_params',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'home_url' => get_home_url(),
				'nonce'    => wp_create_nonce( 'reign-wcfm-nonce' ),
			)
		);

		if ( class_exists( 'BuddyPress' ) && is_buddypress() ) {
			wp_enqueue_script( 'wp-embed' );
		}

	}

	/**
	 * Add favorite product tab on member profile
	 *
	 * @since 1.3.0
	 */
	public function reign_wcfm_buddypress_profile_tabs() {
		global $bp, $wbtm_reign_settings;
		$bp_pages    = bp_core_get_directory_pages();
		$member_slug = $bp_pages->members->slug;
		$name        = bp_get_displayed_user_username();
		$user_id     = bp_displayed_user_id();

		if ( bp_displayed_user_id() === bp_loggedin_user_id() ) {
			if ( isset( $wbtm_reign_settings['wcfm_option']['product_favourite'] ) && 'on' === $wbtm_reign_settings['wcfm_option']['product_favourite'] ) {
				bp_core_new_nav_item(
					array(
						'name'                    => apply_filters( 'reign_wcfm_favourite_product_tab_name', __( 'Favourite', 'reign-wcfm-addon' ) ),
						'slug'                    => apply_filters( 'reign_wcfm_favourite_product_tab_slug', 'favourite' ),
						'default_subnav_slug'     => 'favourite',
						'screen_function'         => array( $this, 'reign_wcfm_favourite_product_show_screen' ),
						'position'                => 80,
						'show_for_displayed_user' => true,
					)
				);

			}
		}

		if ( function_exists( 'wcfm_is_vendor' ) && wcfm_is_vendor( bp_displayed_user_id() ) ) {
			remove_action( 'bp_member_options_nav', 'bp_wcfmmp_store_nav_item', 99 );
			if ( isset( $wbtm_reign_settings['wcfm_option']['vendor_store'] ) && 'on' === $wbtm_reign_settings['wcfm_option']['vendor_store'] ) {
				bp_core_new_nav_item(
					array(
						'name'                    => apply_filters( 'reign_wcfm_vendor_store_tab_name', __( 'Store', 'reign-wcfm-addon' ) ),
						'slug'                    => apply_filters( 'reign_wcfm_vendor_store_tab_slug', 'store' ),
						'default_subnav_slug'     => 'store',
						'screen_function'         => array( $this, 'reign_wcfm_store_show_screen' ),
						'position'                => 81,
						'show_for_displayed_user' => true,
					)
				);

			}
		}

	}

	/**
	 * Load buddypress template for favorite product tab
	 *
	 * @since 1.3.0
	 */
	public function reign_wcfm_favourite_product_show_screen() {
		add_action( 'bp_template_content', array( $this, 'reign_wcfm_tab_show_favourite_product_screen_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );

	}

	/**
	 * Load buddypress template for store tab
	 *
	 * @since 1.3.0
	 */
	public function reign_wcfm_store_show_screen() {
		add_action( 'bp_template_content', array( $this, 'reign_wcfm_tab_show_store_screen_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );

	}

	/**
	 * List favorite products of user.
	 *
	 * @since 3.0.0
	 */
	public function reign_wcfm_tab_show_favourite_product_screen_content() {
		$posts_per_page = $GLOBALS['wp_query']->get( 'posts_per_page' );
		$posts_per_page = apply_filters( 'reign_dokan_fav_product_bp_tab_posts_per_page', $posts_per_page );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
		$fav_products = get_user_meta( bp_displayed_user_id(), 'rwcfma_favourite_products', true );
		if ( ! empty( $fav_products ) && count( $fav_products ) > 0 ) {
			echo do_shortcode( '[products columns="3" paginate=true limit="' . $posts_per_page . '" paginate="1"]' );
		} else {
			?>
				<aside class="bp-feedback bp-messages info">
					<span class="bp-icon" aria-hidden="true"></span>
					<p><?php esc_html_e( 'No products were found matching your selection.', 'reign-wcfm-addon' ); ?></p>
				</aside>
			<?php
		}
	}

	/**
	 * List favorite products of user.
	 *
	 * @since 3.0.0
	 */
	public function reign_wcfm_tab_show_store_screen_content() {
		$posts_per_page = $GLOBALS['wp_query']->get( 'posts_per_page' );
		$posts_per_page = apply_filters( 'reign_dokan_fav_product_bp_tab_posts_per_page', $posts_per_page );
		remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 10 );
		echo do_shortcode( '[products columns="3" paginate=true limit="' . $posts_per_page . '" paginate="1"]' );
	}

	/**
	 * Alter woocommerce produt query to show user's favorite products on there profile.
	 *
	 * @param  array $query_args
	 *
	 * @return array
	 *
	 * @since 1.3.0
	 */
	public function reign_wcfm_favourite_products_query( $query_args, $attributes, $type ) {

		if ( bp_displayed_user_id() === bp_loggedin_user_id() && 'favourite' === bp_current_action() ) {
			$fav_products = get_user_meta( bp_displayed_user_id(), 'rwcfma_favourite_products', true );
			if ( ! empty( $fav_products ) && count( $fav_products ) > 0 ) {
				$query_args['post__in'] = $fav_products;
			}
		}

		if ( 'store' === bp_current_action() ) {
				$query_args['author'] = bp_displayed_user_id();
		}

		return $query_args;
	}

	/**
	 * Add favuorite product icon on single product
	 *
	 * @since 1.3.0
	 */
	public function reign_wcfm_favourite_product_icon() {
		global $product,$wbtm_reign_settings;

		if ( ! isset( $wbtm_reign_settings['wcfm_option']['product_favourite'] ) || 'on' !== $wbtm_reign_settings['wcfm_option']['product_favourite'] ) {
			return;
		}

		$favuorite_products = get_user_meta( get_current_user_id(), 'rwcfma_favourite_products', true );
		$product_action     = 'mark-favuorite';
		$product_icon       = 'heart';
		if ( ! empty( $favuorite_products ) ) {
			if ( in_array( $product->get_id(), $favuorite_products ) ) {
				$product_action = 'remove-favuorite';
				$product_icon   = 'heart-fill';
			}
		}

		$icon_link = REIGN_WCFM_ADDON_URL . 'public/images/' . $product_icon . '.svg';

		if ( is_user_logged_in() ) {
			echo apply_filters( //phpcs:ignore
				'reign_wcfm_favourite_product_icon',
				sprintf(
					'<img src="%1$s" id="rwcfma-product-mark-favuorite" data-pid="%2$s" data-action="%3$s" height="40" width="40">',
					esc_url( $icon_link ),
					esc_attr( $product->get_id() ),
					esc_attr( $product_action )
				)
			);
		}
	}


	/**
	 * Mark favuorite or Remove favuorite in Buddypress Member Profile 'My Products'.
	 *
	 * @since  1.3.0
	 * @access public
	 */
	public function reign_wcfm_do_mark_favuorite() {
		if (! isset($_POST['nonce']) || ! wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'reign-wcfm-nonce') ) {
            return false;
        }
		$uid                = get_current_user_id();
		$favuorite_products = get_user_meta( get_current_user_id(), 'rwcfma_favourite_products', true );
		$pid                = isset( $_POST[ 'product_id' ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'product_id' ] ) ) : '';
		$product_meta       = get_post_meta( $pid, 'rwcfma_user_favourite_product', true );
		$product_users      = ( ( '' !== $product_meta ) ? $product_meta : 0 );
		if ( isset( $_POST[ 'product-action' ] ) && 'mark-favuorite' === sanitize_text_field( wp_unslash( $_POST[ 'product-action' ] ) ) ) {
			// Update user meta for get user's favuorite products.
			if ( ! empty( $favuorite_products ) ) {
				array_push( $favuorite_products, $pid );
				update_user_meta( $uid, 'rwcfma_favourite_products', $favuorite_products );
			} else {
				$pids = array();
				array_push( $pids, $pid );
				update_user_meta( $uid, 'rwcfma_favourite_products', $pids );
			}
			// Update product meta for get all users that marked this product as their favuorite product.
			if ( ! empty( $product_users ) ) {
				array_push( $product_users, $uid );
				update_post_meta( $pid, 'rwcfma_user_favourite_product', $product_users );
			} else {
				$uids = array();
				array_push( $uids, $uid );
				update_post_meta( $pid, 'rwcfma_user_favourite_product', $uids );
			}

			$response = array( 'btn-action' => 'remove-favuorite' );
		} else {
			if ( ! empty( $favuorite_products ) ) {
				if ( ( $key = array_search( $pid, $favuorite_products ) ) !== false ) {
					unset( $favuorite_products[ $key ] );
				}
				update_user_meta( $uid, 'rwcfma_favourite_products', $favuorite_products );
			}
			if ( ! empty( $product_users ) ) {
				if ( ( $key = array_search( $uid, $product_users ) ) !== false ) {
					unset( $product_users[ $key ] );
				}
				update_post_meta( $pid, 'rwcfma_user_favourite_product', $product_users );
			}
			$response = array( 'btn-action' => 'mark-favuorite' );
		}
		wp_send_json_success( $response );
	}


	/**
	 * Check if there is any product publish by vendor.
	 *
	 * @since  1.3.0
	 * @access public
	 * @param boject $results
	 * @return string message
	 */
	public function reign_wcfm_check_products_query_results( $results ) {

		if ( ! $results->total > 0 ) {
			if ( bp_displayed_user_id() === bp_loggedin_user_id() ) {
				?>
				<aside class="bp-feedback bp-messages info">
					<span class="bp-icon" aria-hidden="true"></span>
					<p><?php esc_html_e( 'No products were found matching your selection.', 'reign-wcfm-addon' ); ?></p>
				</aside>
				<?php
			} else {
				?>
				<aside class="bp-feedback bp-messages info">
					<span class="bp-icon" aria-hidden="true"></span>
					<?php // Translators: %s is the user name ?>
					<p><?php echo sprintf( esc_html__( '%s have not any products yet.', 'reign-wcfm-addon' ), esc_html( bp_get_displayed_user_fullname() ) ); ?></p>
				</aside>
				<?php

			}
		} else {
			return $results;
		}
	}

	/**
	 * Added Store link on vendors store tab.
	 */
	public function reign_wcfm_add_visit_store_link_on_product_tab() {
		global $bp;
		if ( class_exists( 'BuddyPress' ) && 'store' === bp_current_action() ) {
			$store_tab_slug = $bp->members->nav->store->slug;
			if ( is_buddypress() && bp_current_action() === $store_tab_slug ) {
				echo '<a class="button reign-wcfm-store" href="' . esc_url( wcfmmp_get_store_url( bp_displayed_user_id() ) ) . '" title="' . esc_attr__( 'View Store', 'reign-wcfm-addon' ) . '">';
				echo '<span class="dashicons dashicons-store"></span>';
				echo '<span>' . esc_html__( 'Visit Store', 'reign-wcfm-addon' ) . '</span>';
				echo '</a>';
			}
		}
	}

	/**
	 * Dispaly product review content and rating before activity conent
	 *
	 * @return string
	 */
	public function reign_wcfm_activity_content_body( $content ) {
		global $activities_template;

		if ( isset( $activities_template->activity ) && isset( $activities_template->activity->type ) && 'wcfm_product_review' === $activities_template->activity->type ) {
			$_content   = $content;
			$review     = '<blockquote class="woocommerce">';
			$product_id = $activities_template->activity->item_id;
			$review_id  = $activities_template->activity->secondary_item_id;
			$rating     = intval( get_comment_meta( $review_id, 'rating', true ) );

			if ( ! empty( $rating ) ) {
				$review .= wc_get_rating_html( $rating );
			}

			$review .= get_comment_text( $review_id );

			$review .= '</blockquote>';

			$review .= $_content;

			$content = $review;
		}

		return $content;
	}

	/**
	 * Add activity on new order created
	 *
	 * @since 1.6.5
	 *
	 * @param int $order_id
	 * @return void
	 */
	public function reign_wcfm_make_order_activity( $order_id ) {
		global $bp, $wbtm_reign_settings;

		if ( function_exists( 'bp_is_active' ) && bp_is_active( 'activity' ) && function_exists( 'bp_activity_add' ) ) {
			if ( isset( $wbtm_reign_settings['wcfm_option']['order_activity'] ) && 'on' === $wbtm_reign_settings['wcfm_option']['order_activity'] ) {

				if ( ! empty( get_post_meta( $order_id, 'order_activity_id', true ) ) ) {
					return;
				}
	
				$order         = wc_get_order( $order_id );
				$order_items   = $order->get_items( 'line_item' );
				$product_count = 0;
				foreach ( $order_items as $item_id => $item ) {
					$product = $item->get_product();
					if ( $product->get_catalog_visibility() !== 'hidden' ) {
						$product_count++;
					}
				}
	
				// skip if there is no product are visible in catalog.
				if ( $product_count === 0 ) {
					return;
				}
	
				$user_id   = $order->get_customer_id();
				$user_link = bp_core_get_userlink( $user_id );
				// Translators: %s is the use link.
				$action = sprintf( __( '%1$s made a purchase ', 'reign-wcfm-addon' ), $user_link );
	
				$args = array(
					'action'            => $action,
					'content'           => '',
					'component'         => $bp->members->id,
					'type'              => class_exists( 'Youzify' ) ? 'activity_status' : 'wcfm_order_create',
					'user_id'           => bp_loggedin_user_id(),
					'item_id'           => $order_id,
					'secondary_item_id' => $order_id,
					'hide_sitewide'     => false,
					'is_spam'           => false,
					'privacy'           => 'public',
					'error_type'        => 'bool',
				);
	
				$activity_id = bp_activity_add( $args );
				if ( $activity_id ) {
					bp_activity_add_meta( $activity_id, 'wbcom_activity_markup_orderid', $order_id );
					update_post_meta( $order_id, 'order_activity_id', $activity_id );
					if ( class_exists( 'Youzify' ) ) {
						bp_activity_add_meta( $activity_id, 'reign_wcfm_youzify_activity_action', 'reign_wcfm_purchased_a_product' );
					}
				}
			}
		}
	}

	/**
	 * Outputs embedded product previews for WCFM orders in the BuddyBoss activity feed.
	 *
	 * @return void
	 */
	public function reign_wcfm_order_activity_content() {
		global $activities_template;

		if ( 'wcfm_order_create' === $activities_template->activity->type || 'activity_status' === $activities_template->activity->type ) {
			
			$order_id    = $activities_template->activity->item_id;
			$order       = wc_get_order( $order_id );
			$order_items = $order->get_items( 'line_item' );

			// Embed products.
			$product_content = '';
			$product_count   = 0;
			bp_embed_init();
			$product_content .= '<div class="rwcfm-slider-wrapper">';

			foreach ( $order_items as $item_id => $item ) {
				if ( ! $item instanceof WC_Order_Item_Product || ! $item->get_product_id() ) {
					continue;
				}

				$product           = $item->get_product();
				$product_permalink = $product->get_permalink( $item );
				$embed_html        = wp_oembed_get( $product_permalink, array( 'width' => '500' ) );   		
				$product_content  .= '<div class="rwcfm-slider-item">';
				$product_content  .= $embed_html;
				$product_content  .= '</div>';
				$product_count++;
			}

			$product_content .= '</div>';

			$find_pos_absolute = ' style="position: absolute; clip: rect(1px, 1px, 1px, 1px);" ';
			if ( false !== strpos( $product_content, 'data-secret=' ) && false !== strpos( $product_content, $find_pos_absolute ) ) {
				$product_content = str_replace( $find_pos_absolute, '', $product_content );
			}			
			echo $product_content; //phpcs:ignore
		} else {
			return;
		}
	}

	/**
	 * Add action to youzify activities for new order/purchase activity.
	 *
	 * @since 1.7.2
	 *
	 * @param string $action
	 * @param object $activity
	 * @return string $action
	 */

	public function reign_wcfm_youzify_order_activity_action( $action, $activity ) {
		
		if ( class_exists( 'Youzify' ) ) {
			$activity_meta_data = bp_activity_get_meta( $activity->id, 'reign_wcfm_youzify_activity_action', true );
			if ( ! empty ( $activity_meta_data ) && ( 'reign_wcfm_purchased_a_product' === $activity_meta_data ) ) {
				$action = sprintf(
					/* translators: %s: the activity author user link */
					esc_html__( '%s made a purchase', 'reign-wcfm-addon' ),
					bp_core_get_userlink( $activity->user_id )
					
				);
			}
		}
		return $action;
	}


	/**
	 * Filters the WCFM store template based on the selected store layout in Reign theme settings.
	 *
	 * @param string $template   
	 * @param string $template_name 
	 * @param string $template_path 
	 * @param string $default_path 
	 *
	 * @return string
	 */
	public function reign_wcfm_locate_template( $template, $template_name, $template_path, $default_path ) {
		global $wbtm_reign_settings;
		// Get the layout mods.
		$rg_store_layout = isset( $wbtm_reign_settings['wcfm_option']['reign_wcfm_store_layout'] ) ? $wbtm_reign_settings['wcfm_option']['reign_wcfm_store_layout'] : 'layout_one';

		if ( 'layout_two' === $rg_store_layout ) {
			if ( 'store/wcfmmp-view-store.php' === $template_name ) {
				$template = REIGN_WCFM_ADDON_PATH . 'templates/reign-wcfm-store-layout2.php';
			}
		}

		return $template;
	}

	/**
     * Helper method to determine whether to enqueue scripts for woocommerce.
     *
     * @return bool
     */
    private function reign_wcfm_should_enqueue_scripts_for_wc() {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return false;
        }

        if ( is_user_logged_in() &&  ( is_shop() || is_product() ) ) {
            return true;
        }

        return false;
    }

	/**
     * Check if BuddyPress/BuddyBoss is active and on favorites, store, or activity tab.
	 * @return bool
     */
    private function reign_wcfm_should_enqueue_scripts_for_bp() {
        if ( ! function_exists( 'bp_is_active' ) ) {
            return false;
        }

        // Check for favorites tab: /members/username/favourites/
        if ( bp_is_current_component( 'favourite' )  ) {
            return true;
        }

        // Check for store tab: /members/username/store/
        if ( bp_is_current_component( 'store' ) ) {
            return true;
        }

        // Check for activity component in general
        if ( bp_is_activity_component() ) {
            return true;
        }

		if ( bp_is_user() ) {
            return true;
        }

        return false;
    }
}
