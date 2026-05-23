<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Newsfeed
 * @subpackage Buddypress_Newsfeed/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Buddypress_Newsfeed
 * @subpackage Buddypress_Newsfeed/public
 * @author     wbcomdesigns <admin@wbcomdesigns.com>
 */
class Buddypress_Newsfeed_Public {

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

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Newsfeed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Newsfeed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$extension = is_rtl() ? '.rtl.css' : '.css';
			$path      = is_rtl() ? '/rtl' : '';
		} else {
			$extension = is_rtl() ? '.rtl.css' : '.min.css';
			$path      = is_rtl() ? '/rtl' : '/min';
		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css' . $path . '/buddypress-newsfeed-public' . $extension, array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Newsfeed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Newsfeed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) {
			$extension = '.js';
			$path      = '';
		} else {
			$extension = '.min.js';
			$path      = '/min';
		}

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js' . $path . '/buddypress-newsfeed-public' . $extension, array( 'jquery' ), $this->version, true );
	}

	/**
	 * Setup Activity Submenu.
	 */
	public function bnews_setup_submenu_activity_newsfeed() {
		if ( bp_is_user_activity() ) {
			global $bp;
			if ( is_object( $bp->displayed_user ) && property_exists( $bp->displayed_user, 'domain' ) ) {
				$parent_slug            = buddypress()->activity->id;
				$bnews_general_settings = get_option( 'bnews_general_settings' );
				$first_tab              = ( isset( $bnews_general_settings['first_tab'] ) ) ? $bnews_general_settings['first_tab'] : 'newsfeed';
				if ( 'newsfeed' == $first_tab ) {
					$default_subnav = ( 'newsfeed' === $first_tab ) ? 'newsfeed' : 'just-me';
					if( 'newsfeed' === $default_subnav ){
						$callback = 'bp_activity_screen_newsfeed';
					} else {
						$callback = 'bp_activity_screen_personal';
					}

					bp_core_new_nav_default(
						array(
							'parent_slug'     => buddypress()->activity->id,
							'subnav_slug'     => 'newsfeed',
							'screen_function' => array($this, $callback),
						)
					);


					// Add subnav item.
					bp_core_new_subnav_item(
						array(
							'name'            => __( 'Newsfeed', 'buddypress-newsfeed' ),
							'slug'            => 'newsfeed',
							'parent_url'      => $bp->displayed_user->domain . $parent_slug . '/',
							'parent_slug'     => $parent_slug,
							'screen_function' => array( $this, 'bp_activity_screen_newsfeed' ),
							'position'        => 0,
						)
					);
					bp_core_new_subnav_item(
						array(
							'name'            => __( 'Personal', 'buddypress-newsfeed' ),
							'slug'            => 'just-me',
							'parent_url'      => $bp->displayed_user->domain . $parent_slug . '/',
							'parent_slug'     => $parent_slug,
							'screen_function' => array( $this, 'bp_activity_screen_personal' ),
							'position'        => 20,
						)
					);
					
				} else {
					// Add subnav item.
					bp_core_new_subnav_item(
						array(
							'name'            => __( 'Personal', 'buddypress-newsfeed' ),
							'slug'            => 'just-me',
							'parent_url'      => $bp->displayed_user->domain . $parent_slug . '/',
							'parent_slug'     => $parent_slug,
							'screen_function' => array( $this, 'bp_activity_screen_personal' ),
							'position'        => 0,
						)
					);
					bp_core_new_subnav_item(
						array(
							'name'            => __( 'Newsfeed', 'buddypress-newsfeed' ),
							'slug'            => 'newsfeed',
							'parent_url'      => $bp->displayed_user->domain . $parent_slug . '/',
							'parent_slug'     => $parent_slug,
							'screen_function' => array( $this, 'bp_activity_screen_newsfeed' ),
							'position'        => 20,
						)
					);
				}
			}
		}
	}

	/**
	 * Display the newsfeed activity screen.
	 *
	 * @since 1.0.0
	 */
	public function bp_activity_screen_newsfeed() {
		add_action( 'bp_template_content', array( $this, 'bnews_newsfeed_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}
	
	/**
	 * Display the personal activity screen.
	 *
	 * @since 1.0.0
	 */
	public function bp_activity_screen_personal() {
		add_action( 'bp_template_content', array( $this, 'bnews_personal_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	/**
	 * Render Content.
	 */
	public function bnews_newsfeed_content() {
		if ( is_user_logged_in() ) {
			bp_get_template_part( 'activity/post-form' );
		}
	}

	/**
	 * Render Content.
	 */
	public function bnews_personal_content() {
		if ( is_user_logged_in() ) {
			bp_get_template_part( 'activity/post-form' );
		}
	}

	/**
	 * BuddyPress Activity Feed.
	 */
	public function bnews_newsfeed_activity_feed() {

		$bnews_general_settings = get_option( 'bnews_general_settings' );

		if ( isset( $bnews_general_settings['disable_all'] ) && $bnews_general_settings['disable_all'] === 'yes' ) {

				bp_core_remove_subnav_item( 'activity', 'favorites' );

				bp_core_remove_subnav_item( 'activity', 'friends' );

				bp_core_remove_subnav_item( 'activity', 'groups' );

				bp_core_remove_subnav_item( 'activity', 'mentions' );

				bp_core_remove_subnav_item( 'activity', 'following' );

		}
	}

	/**
	 * Alter Activity Arguments.
	 *
	 * @param  array $r Activity Arguments.
	 */
	public function bnews_alter_activities_parse_args( $r ) {
		$bp = buddypress();
		$bnews_general_settings = get_option( 'bnews_general_settings' );
		$first_tab              = ( isset( $bnews_general_settings['first_tab'] ) ) ? $bnews_general_settings['first_tab'] : 'newsfeed';
		$show_newsfeed_activity = false;
		if( 'newsfeed' === $first_tab && is_object( $bp->displayed_user ) && property_exists( $bp->displayed_user, 'domain' ) && isset( $_SERVER['HTTP_REFERER'] ) && $bp->displayed_user->domain === $_SERVER['HTTP_REFERER']){
			$show_newsfeed_activity = true;
		}

		if( class_exists( 'Youzify' ) ){
			if( ( 'newsfeed' === $first_tab && is_object( $bp->displayed_user ) && property_exists( $bp->displayed_user, 'domain' ) && isset( $_SERVER['HTTP_REFERER'] ) && $bp->displayed_user->domain === $_SERVER['HTTP_REFERER'] ) || bp_is_current_action( 'newsfeed' ) ){
				$show_newsfeed_activity = true;
			}
		}
		
		if( 'personal' === $first_tab && bp_is_current_action( 'newsfeed' ) ){
			$show_newsfeed_activity = true;
		}	

		if ( isset( $bnews_general_settings['disable_all'] ) && $bnews_general_settings['disable_all'] === 'yes' && $show_newsfeed_activity ) {
			if ( is_user_logged_in() ) {
				// unset( $r['scope'] );
				$bnews_general_settings['disable_all'] = array(
					'favorites',
					'friends',
					'groups',
					'mentions',
					'following',
					'just-me',
				);
				$act_list                              = implode( ',', $bnews_general_settings['disable_all'] );
				$r['scope']                            = $act_list;
			}
		}

		return $r;
	}


	/**
	 * WordPress Admin bar.
	 *
	 * @param  array $wp_admin_bar Admin Bar Menu.
	 * @return void
	 */
	public function bnews_admin_bar_menu( $wp_admin_bar ) {

		$bnews_general_settings = get_option( 'bnews_general_settings' );
		if ( isset( $bnews_general_settings['disable_all'] ) && $bnews_general_settings['disable_all'] === 'yes' ) {

				$wp_admin_bar->remove_node( 'my-account-activity-favorites' );

				$wp_admin_bar->remove_node( 'my-account-activity-friends' );

				$wp_admin_bar->remove_node( 'my-account-activity-groups' );

				$wp_admin_bar->remove_node( 'my-account-activity-mentions' );

				$wp_admin_bar->remove_node( 'my-account-activity-following' );

		}
		$wp_admin_bar->remove_node( 'my-account-activity-personal' );
	}

	/**
	 * WordPress Admin bar.
	 *
	 * @param  array $wp_admin_bar Admin Bar Menu.
	 * @return void
	 */
	public function bnews_update_wp_menus( $wp_admin_bar ) {
		$bnews_general_settings = get_option( 'bnews_general_settings' );
		$first_tab              = ( isset( $bnews_general_settings['first_tab'] ) ) ? $bnews_general_settings['first_tab'] : 'newsfeed';

		global $wp_admin_bar, $bp;
		$wp_admin_bar->remove_menu( 'my-account-activity-personal' );

		$domain        = $bp->loggedin_user->domain;
		$activity_link = trailingslashit( $domain . $bp->activity->slug );

		if ( 'newsfeed' == $first_tab ) {
			$personal_profile_slug = 'just-me';

			$wp_admin_bar->add_menu(
				array(
					'parent' => 'my-account-' . $bp->activity->id,
					'id'     => 'my-account-' . $bp->activity->id . '-newsfeed',
					'title'  => __( 'Newsfeed', 'buddypress-newsfeed' ),
					'href'   => trailingslashit( $activity_link . 'newsfeed' ),
				)
			);

			$wp_admin_bar->add_menu(
				array(
					'parent' => 'my-account-' . $bp->activity->id,
					'id'     => 'my-account-' . $bp->activity->id . '-just-me',
					'title'  => __( 'Personal', 'buddypress-newsfeed' ),
					'href'   => trailingslashit( $activity_link . $personal_profile_slug ),
				)
			);
		} else {
			$personal_profile_slug = '';
			$wp_admin_bar->add_menu(
				array(
					'parent' => 'my-account-' . $bp->activity->id,
					'id'     => 'my-account-' . $bp->activity->id . '-just-me',
					'title'  => __( 'Personal', 'buddypress-newsfeed' ),
					'href'   => trailingslashit( $activity_link . $personal_profile_slug ),
				)
			);
			$wp_admin_bar->add_menu(
				array(
					'parent' => 'my-account-' . $bp->activity->id,
					'id'     => 'my-account-' . $bp->activity->id . '-newsfeed',
					'title'  => __( 'Newsfeed', 'buddypress-newsfeed' ),
					'href'   => trailingslashit( $activity_link . 'newsfeed' ),
				)
			);
		}
	}

	/**
	 * Script Template Message..
	 */
	public function bnews_script_template_greeting() {
		if ( is_user_logged_in() ) {
			$greeting = '';
			if ( bp_is_group() ) {
				$greeting = sprintf( __( "What's new in %1\$s, %2\$s?", 'buddypress-newsfeed' ), bp_get_current_group_name(), bp_get_user_firstname() );
			} elseif ( ! bp_is_my_profile() && bp_is_user_activity() ) {
				/* translators: %s: Display Full Name */
				$greeting = sprintf( __( 'Write something to %s', 'buddypress-newsfeed' ), bp_get_displayed_user_fullname() );
			} else {
				/* translators: %s: Display First Name */
				$greeting = sprintf( __( "What's new, %s?", 'buddypress-newsfeed' ), bp_get_user_firstname() );
			}

			?>
			<script type="text/html" id="buddypress-newsfeed-tpl-greeting">
				<?php echo $greeting;  // phpcs:ignore WordPress.Security.EscapeOutput ?>
			</script>
			<?php
		}
	}

	/**
	 * Check Post From is Enable or Not.
	 */
	public function is_enabled() {
		$bnews_general_settings = get_option( 'bnews_general_settings' );
		$post_form_enable       = ( isset( $bnews_general_settings['post_form_enable'] ) ) ? $bnews_general_settings['post_form_enable'] : 'no';
		if ( 'yes' == $post_form_enable ) {
			return true;
		} elseif ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
			if ( 'newsfeed' == bp_current_action() && bp_is_my_profile() ) {
				?>
			<style>
				#bp-nouveau-activity-form{
					display:none;
				}
			</style>
				<?php
			}
		} else {
			return false;
		}
	}

	/**
	 * Post Form.
	 */
	public function bnews_post_form() {
		global $bp;

		if ( $this->is_enabled() && bp_is_user() && 'newsfeed' == bp_current_action() && bp_is_my_profile() ) {

			if ( ! is_user_logged_in() ) :
				?>

				<div id="message">
					<?php /* translators: %s: WordPress Login URL*/ ?>
					<p><?php printf( esc_html__( 'You need to <a href="%s" title="Log in">log in</a>', 'buddypress-newsfeed' ), esc_url( wp_login_url() ) ); ?>
									<?php
									if ( bp_get_signup_allowed() ) :
										?>
										<?php /* translators: %s: BuddyPress Sign up Page URL*/ ?>
										<?php printf( esc_html__( ' or <a class="create-account" href="%s" title="Create an account">create an account</a>', 'buddypress-newsfeed' ), esc_attr( bp_get_signup_page() ) ); ?><?php endif; ?><?php esc_html__( ' to post to this user\'s Wall.', 'buddypress-newsfeed' ); ?></p>
				</div>
			<?php else : ?>

				<?php if ( isset( $_GET['r'] ) ) :  //phpcs:ignore ?> 
					<div id="message" class="info">
						<?php /* translators: %1$s: Display Mentioned User Name*/ ?>
						<p><?php printf( esc_html__( 'You are mentioning %s in a new update, this user will be sent a notification of your message.', 'buddypress-newsfeed' ), esc_attr( bp_get_mentioned_user_display_name( $_GET['r'] ) ) );  // phpcs:ignore ?></p>
					</div>
				<?php endif; ?>

				<?php 
				if ( function_exists( 'buddypress' ) && ! isset( buddypress()->buddyboss ) ) {
					bp_get_template_part( 'activity/post-form' );
				}
				 ?>

			<?php endif; ?>

			<?php
		}
	}

	/**
	 * Fires right before returning the formatted activity notifications.
	 *
	 * @since BuddyPress 1.2.0
	 *
	 * @param string $notification            Array holding the content and permalink for the interaction notification.
	 * @param string $link            The permalink for the interaction.
	 * @param int    $total_items       Total amount of items to format.
	 * @param int    $activity_id           The activity ID.
	 * @param int   $user_id The user ID who initiated the interaction.
	 */
	public function bnews_change_mention_notification_link_on_merge( $notification, $link, $total_items, $activity_id, $user_id ) {

		$bnews_general_settings = get_option( 'bnews_general_settings' );
		if ( isset( $bnews_general_settings['tomerge'] ) ) {
			if ( in_array( 'mentions', $bnews_general_settings['tomerge'] ) ) {
				$first_tab = ( isset( $bnews_general_settings['first_tab'] ) ) ? $bnews_general_settings['first_tab'] : 'newsfeed';

				$user_fullname = bp_core_get_user_displayname( $user_id );
				if ( (int) $total_items > 1 ) {
					/* translators: %1$ds: Total Item*/
					$text = sprintf( __( 'You have %1$d new mentions', 'buddypress-newsfeed' ), (int) $total_items );
				} else {
					/* translators: %1$s: User full name*/
					$text = sprintf( __( '%1$s mentioned you', 'buddypress-newsfeed' ), $user_fullname );
				}

				if ( 'newsfeed' == $first_tab ) {
					$link = bp_loggedin_user_domain() . bp_get_activity_slug();
				} else {
					$link = bp_loggedin_user_domain() . bp_get_activity_slug() . '/newsfeed/';
				}
				$notification = '<a class="ab-item" href="' . esc_url( $link ) . '">' . esc_html( $text ) . '</a>';
			}
		}
		return $notification;
	}

	/**
	 * Enqueue Nouveau Scripts
	 */
	public function bnews_nouveau_activity_enqueue_scripts() {
		global $post;
		$_current_component = '';
		if ( isset( $post->ID ) && '' != $post->ID && '0' != $post->ID ) {
			$_elementor_controls_usage = get_post_meta( get_the_ID(), '_elementor_controls_usage', true );
			$_current_component        = '';
			if ( ! empty( $_elementor_controls_usage ) ) {
				foreach ( $_elementor_controls_usage as $key => $value ) {
					if ( 'buddypress_shortcode_activity_widget' == $key || 'bp_newsfeed_element_widget' == $key ) {
						$_current_component = 'activity';
						break;
					}
				}
			}
		}
		if ( '' != $_current_component ) {
			$current_component = static function () {
				return 'activity';
			};
			add_filter( 'bp_current_component', $current_component );
			add_filter( 'bp_is_current_component', $current_component );
		}
	}

	/**
	 * Register Script Arguments.
	 *
	 * @param  array $scripts_args Scripts Arguments.
	 */
	public function bpnewsfeed_bp_nouveau_register_scripts( $scripts_args ) {

		if ( function_exists( 'buddypress' ) && isset( buddypress()->buddyboss ) ) {
			if ( isset( $scripts_args['bp-nouveau-activity'] ) ) {
				$scripts_args['bp-nouveau-activity']['file'] = plugin_dir_url( __FILE__ ) . 'assets/js/buddypress-activity%s.js';
			}
			return $scripts_args;
		}

		if ( isset( $scripts_args['bp-nouveau'] ) ) {
			$scripts_args['bp-nouveau']['file'] = BNEWS_PLUGIN_URL . 'public/js/buddypress-nouveau%s.js';
		}
		return $scripts_args;
	}

	/**
	 * Filters the name of the current component.
	 *
	 * @since BuddyPress 1.0.0
	 *
	 * @param string|bool $current_component Current component if available or false.
	 */
	public function bpnewsfeed_bp_current_component( $current_component ) {

		if ( isset( $_REQUEST['action'] ) && 'post_update' == $_REQUEST['action'] ) {  // phpcs:ignore
			$current_component = 'activity';
		}

		return $current_component;
	}


	public function bpnewsfeed_get_template_part( $templates, $slug, $name, $args ) {

		if ( 'common/js-templates/activity/parts/bp-whats-new-toolbar' === $slug ) {
			bp_news_feed_get_template( 'parts/bp-whats-new-toolbar.php' );
		}

		return $templates;
	}

	/**
	 * Filters the activity feed to show relevant activities for the user.
	 *
	 * This function customizes the activity feed to include activities from the user's own activity,
	 * friends, followed members, groups the user has joined, forum discussions the user is subscribed to,
	 * and mentions of the user.
	 *
	 * @param array $args The activity query arguments.
	 * @return array The modified activity query arguments.
	 */
	public function bpnewsfeed_filter_relevant_activity( $args ) {
		// Ensure we are on the activity directory page
		if ( ! bp_is_activity_directory() ) {
			return $args;
		}
	
		// Retrieve general settings and check if the feature is enabled
		$bnews_general_settings = get_option( 'bnews_general_settings', array() );
		$enabled = isset( $bnews_general_settings['_bp_enable_relevant_feed'] ) ? $bnews_general_settings['_bp_enable_relevant_feed'] : '';
		if ( 'yes' !== $enabled ) {
			return $args;
		}
	
		// Initialize variables
		$user_id = get_current_user_id();
		$user_ids = array( $user_id ); // Include the user's own activity
		$friend_ids = array();
		$following_ids = array();
		$group_ids = array();
		$forum_subscriptions = array();
	
		// Include Friends' Activity (BuddyPress Friends plugin)
		if ( function_exists( 'friends_get_friend_user_ids' ) ) {
			$friend_ids = friends_get_friend_user_ids( $user_id );
			if ( ! empty( $friend_ids ) ) {
				$user_ids = array_merge( $user_ids, $friend_ids );
			}
		}
	
		// Include Followed Members' Activity (BuddyPress Follow plugin)
		if ( function_exists( 'bp_get_following_ids' ) ) {
			$following_ids = bp_get_following_ids( array( 'user_id' => $user_id ) );
			if ( ! empty( $following_ids ) ) {
				$user_ids = array_merge( $user_ids, explode( ',', $following_ids ) );
			}
		}
	
		// Include Group Activity (Groups user has joined)
		if ( function_exists( 'groups_get_user_groups' ) ) {
			$user_groups = groups_get_user_groups( $user_id );
			if ( ! empty( $user_groups['groups'] ) ) {
				$group_ids = $user_groups['groups'];
			}
		}
	
		// Include Forum Discussions the User is Subscribed To (bbPress)
		if ( class_exists( 'bbPress' ) ) {
			$forum_subscriptions = get_user_meta( $user_id, '_bbp_subscriptions', true );
			if ( empty( $forum_subscriptions ) || ! is_array( $forum_subscriptions ) ) {
				$forum_subscriptions = array();
			}
		}
	
		// Include Mentions Dynamically (Search for "@username" in activity content)
		$username = function_exists( 'bp_members_get_user_slug' ) ? bp_members_get_user_slug( $user_id ) : bp_core_get_username( $user_id );
	
		// Build the filter query
		$filter_query = array(
			'relation' => 'OR',
			array(
				'column'  => 'user_id',
				'value'   => array_unique( $user_ids ),
				'compare' => 'IN',
			),
			array(
				'column'  => 'item_id',
				'value'   => $group_ids,
				'compare' => 'IN',
			),
			array(
				'column'  => 'secondary_item_id',
				'value'   => $forum_subscriptions,
				'compare' => 'IN',
			),
			array(
				'column'  => 'content',
				'value'   => '@' . $username,
				'compare' => 'LIKE',
			),
		);
	
		$args['filter_query'] = $filter_query;
	
		return $args;
	}
	
}


