<?php

if(!defined('ABSPATH')) {
	exit;
}

// @package bp-post-status


if ( ! class_exists( 'BPPS_Groups' ) ) :


// BuddyPress Groups for BP Post Status
// Added in version 1.0.1
// @package bp-post-status


class BPPS_Groups {

	/**

	 * Plugin's main instance

	 *

	 * @var object

	 */

	protected static $instance;



	
	function __construct() {
		
//		if ( class_exists( 'BP_Group_Extension' ) ) {

			// add Group Posts nav to group pages
			add_action( 'groups_setup_nav', array( $this, 'setup_nav' ) );

			// Reorder group nav if homepage is set
			add_action( 'bp_head', array( $this, 'bpps_get_nav_order' ) );

			// set the groups default landing tab
			add_filter('bp_groups_default_extension', array( $this,'bpps_group_default_tab' ) );
			
			// Enqueue scripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			
			//Manipulate bp_current_user_can to reveal private groups pages where appropriate
			add_filter( 'bp_current_user_can', array( $this, 'reveal_private_group_public_pages' ) );
			
			//Setup BP Nouveau group posts nav count
			add_filter( 'bp_nouveau_nav_has_count', array( $this, 'nouveau_nav_has_count' ), 10, 3 );
			add_filter( 'bp_nouveau_get_nav_count', array( $this, 'nouveau_get_nav_count' ), 10, 3 );
//		}

	}
	

	/**

	 * Return an instance of this class.

	 *

	 * @since 1.0.1

	 *

	 * @return object A single instance of this class.

	 */

	public static function start() {



		// If the single instance hasn't been set, set it now.

		if ( null == self::$instance ) {

			self::$instance = new self;

		}



		return self::$instance;

	}


	/**

	 * Load scripts.

	 *

	 * @since 1.2.0

	 *

	 * 

	 */

	 function enqueue_scripts() {
		
	wp_register_script( 'bp-post-status-js-translations', BPPS_PLUGIN_URL . 'includes/js/bpps-posts-pages.js', array('jquery'));
	$translation_array = array(
		'homeSelected'			=> esc_attr__( 'Group homepage', 'bp-post-status' ),
		'homeSelect'			=> esc_attr__( 'Set as group homepage', 'bp-post-status' ),
		'error'					=> esc_attr__( 'There was an error setting the homepage, please refer to support', 'bp-post-status' ),
		'update'				=> esc_attr__( 'Please wait - updating settings', 'bp-post-status' ),
		'deletingPost'			=> esc_attr__( 'Deleting Post....', 'bp-post-status' ),
		'postDeleted'			=> esc_attr__( 'Post Deleted', 'bp-post-status' ),
		'postDeleteError'		=> esc_attr__( 'There was an error deleting the post, please try again.', 'bp-post-status' ),
		'publishingPost'		=> esc_attr__( 'Publishing Post....', 'bp-post-status' ),
		'postPublished'			=> esc_attr__( 'Post Published', 'bp-post-status' ),
		'postPublishError'		=> esc_attr__( 'There was an error publishing the post, please try again.', 'bp-post-status' ),
		'makeSticky'			=> esc_attr__( 'Make Sticky', 'bp-post-status' ),
		'unstick'				=> esc_attr__( 'Unstick', 'bp-post-status' ),
		'stickyError'			=> esc_attr__( 'There was an error, please try again', 'bp-post-status' )
		);
	
	wp_localize_script( 'bp-post-status-js-translations', 'bpps_translate', $translation_array );
	wp_localize_script( 'bp-post-status-js-translations', 'my_ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php'), 'check_nonce' => wp_create_nonce('bpps-nonce') ) );		
	wp_enqueue_script( 'bp-post-status-js-translations');
	//wp_enqueue_script( 'bp-post-status', BPPS_PLUGIN_URL . '/includes/js/bpps-posts-pages.js', array('jquery'));
	}

	/**
	 * Setup Group Posts and homepage Nav.
	 *
	 * @since 1.0.1
	 * @revised 1.8.0 Added Moderation page
	 * @return.
	 */

	 public function setup_nav ( $current_user_access ) {
		
		$user_id = get_current_user_id();
		$bp = buddypress();
		
		if ( ! bp_is_group() ) {
			return;
		}

		$group_id = bp_get_current_group_id();
		$count = bpps_get_group_post_count ( $group_id, 'group_post' );
		$mod_count = bpps_get_group_post_count( $group_id, 'group_post_pending' );
		$is_admin = false;
		
		if ( groups_is_user_admin( $user_id, $group_id ) || current_user_can( 'manage_options' ) ) {
			$is_admin = true;
		}

		if ( $this->bpps_group_posts_nav_is_disabled( $group_id ) || bpps_is_disabled( $group_id ) ) {
			return;
		}

		$current_group = groups_get_current_group();
		$group_link = bp_get_group_url( $bp->groups->current_group->id ).'/';
		
		bp_core_new_subnav_item( array(
			'name'					=> esc_attr__( 'Group Posts ', 'bp-post-status' ) . '<span class="count">' . $count . '</span>',
			'slug'					=> BPPS_GROUP_NAV_SLUG,
			'parent_url'			=> $group_link,
			'parent_slug'			=> $bp->groups->current_group->slug,
			'screen_function'		=> array( $this, 'display' ),
			'position'				=> 10,
			'user_has_access'		=> $current_user_access,
			'default_subnav_slug' 	=> BPPS_GROUP_NAV_SLUG,
			'item_css_id'			=> 'bpps-group-posts'
		) );
		
		$home_disable = groups_get_groupmeta( $bp->groups->current_group->id, 'bpps_home_disabled' );
	
		if ( ! $home_disable ) {
			
			buddypress()->groups->nav->edit_nav( array( 'name' => esc_attr__( 'Activity', 'buddypress' ) ), 'home', bp_current_item() );
			
			bp_core_new_subnav_item( array(
				'name'				=> esc_attr__( 'Home', 'bp-post-status' ),
				'slug'				=> 'group-home',
				'parent_url'		=> $group_link,
				'parent_slug'		=> $current_group->slug,
				'screen_function'	=> array( $this, 'display_home' ),
				'position'			=> 12,
				'user_has_access'	=> $current_user_access,
				'item_css_id'		=> 'bpps-group-home'
			) );
			
			buddypress()->groups->nav->edit_nav( array( 'position' => 1 ), 'group-home', bp_current_item() );
			buddypress()->groups->nav->edit_nav( array( 'position' => 2 ), 'activity', bp_current_item() );

		}
		
		if ( $is_admin ) {
			
			bp_core_new_subnav_item( array(
				'name'				=> esc_attr__( 'Posts Moderation ', 'bp-post-status' ) . '<span class="count">' . $mod_count . '</span>',
				'slug'				=> BPPS_GROUP_MODERATION_SLUG,
				'parent_url'		=> $group_link,
				'parent_slug'		=> $current_group->slug,
				'screen_function'	=> array( $this, 'display_moderation' ),
				'position'			=> 11,
				'user_has_access'	=> $is_admin,
				'item_css_id'		=> 'bpps-group-posts-moderation'
			) );
			
			
		}
	}

	/**
	 * Let bp nouveau know the group posts, my posts and pending posts nav have a count.
	 *
	 * @since 1.7.3
	 * @revised 1.7.7 - added group moderation tab
	 * @return bool
	 */

	public function nouveau_nav_has_count( $status, $nav_item, $displayed_nav ) {
		
		if ( $nav_item->slug == BPPS_GROUP_NAV_SLUG ) {
			$group_id = bp_get_current_group_id();
			$count = bpps_get_group_post_count( $group_id, 'group_post' );
			if ( $count >= 1 ) {
				return true;
			}
		} else if ( $nav_item->slug == BPPS_GROUP_MODERATION_SLUG ) {
			$group_id = bp_get_current_group_id();
			$count = bpps_get_group_post_count( $group_id, 'group_post_pending' );
			if ( $count >= 1 ) {
				return true;
			}
		} else if ( $nav_item->slug == BPPS_PROFILE_PENDING_POSTS_NAV_SLUG ) {
			$count = bpps_count_pending_posts();
			if ( $count >= 1 ) {
				return true;
			}
		} else if ( $nav_item->slug == BPPS_PROFILE_MODERATION_POSTS_NAV_SLUG ) {
			$count = bpps_count_moderation_posts();
			if ( $count >= 1 ) {
				return true;
			}
		} else if ( $nav_item->slug == BPPS_MY_POSTS_NAV_SLUG ) {
			$mod_count = bpps_count_moderation_posts();
			$pen_count = bpps_count_pending_posts();
			$count = count_user_posts( bp_displayed_user_id(), 'post' );
			if ( $count + $pen_count + $mod_count >= 1 ) {
				return true;
			}
		}
		return $status;
		
	}

	/**
	 * Let bp nouveau know the group posts, my posts and pending posts count.
	 *
	 * @since 1.7.3
	 * @revised 1.7.7 - added group moderation tab
	 * @return int
	 */

	public function nouveau_get_nav_count( $count, $nav_item, $displayed_nav ) {
		
		if ( $nav_item->slug == BPPS_GROUP_NAV_SLUG ) {
			$group_id = bp_get_current_group_id();
			$count = bpps_get_group_post_count( $group_id, 'group_post' );
			return $count;
		} else if ( $nav_item->slug == BPPS_GROUP_MODERATION_SLUG ) {
			$group_id = bp_get_current_group_id();
			$count = bpps_get_group_post_count( $group_id, 'group_post_pending' );
			return $count;
		} else if ( $nav_item->slug == BPPS_PROFILE_PENDING_POSTS_NAV_SLUG ) {
			$count = bpps_count_pending_posts();
			return $count;
		} else if ( $nav_item->slug == BPPS_PROFILE_MODERATION_POSTS_NAV_SLUG ) {
			$count = bpps_count_moderation_posts();
			return $count;
		} else if ( $nav_item->slug == BPPS_MY_POSTS_NAV_SLUG ) {
			$mod_count = 0;
			$pen_count = 0;
			$user_id = bp_displayed_user_id();
			if ( get_current_user_id() == $user_id ) {
				if ( current_user_can( 'edit_others_posts' ) ) $mod_count = bpps_count_moderation_posts();
				$pen_count = bpps_count_pending_posts();
			}
			$count = bpps_count_users_posts( bp_displayed_user_id() );
			return $count + $mod_count + $pen_count;
		}
		return $count;
		
	}

	/**

	 * set group-home as new default tab.

	 *

	 * @since 1.2.0

	 *

	 * @return $default_tab

	 */


	function bpps_group_default_tab( $default_tab ){
		
		$group = groups_get_current_group();//get the current group
				
		if( empty( $group ) ) {
		 return $default_tab;
		}
		if ( $group->status == 'private' || $group->status == 'hidden' ) {
			$private_group = true;
		} else {
			$private_group = false;
		}
		
		//$user_is_member = groups_is_user_member( bp_loggedin_user_id(), $group->id );
		
		$home_disabled = groups_get_groupmeta( $group->id, 'bpps_home_disabled' );
		
		if ( ! $home_disabled ) {
			$group_home_post_id = groups_get_groupmeta( $group->id, 'bpps_home_post_id' );
		}
		
		$group_home_visible = false;
		
		if ( isset( $group_home_post_id ) && is_numeric( $group_home_post_id ) ) {
			$group_home_status = get_post_meta( $group_home_post_id, 'bpgps_group_post_status' );
			$group_home_status = $group_home_status[0];

			if ( $group_home_status == 'public' || ( is_user_logged_in() && $group_home_status == 'members_only' ) || ! $private_group ) {
				
				$group_home_visible = true;
				
			}
			
		}
		
		$group_posts_tab_disabled = bpps_group_nav_is_disabled( $group->id );
		global $groups_template;

		// Site admins always have access.
		if ( bp_current_user_can( 'bp_moderate' ) ) {
			$bp_group_user_is_member = true;
		}

		if ( ! isset( $groups_template->group ) ) {
			$bp_group_user_is_member = false;
		} else {
			$bp_group_user_is_member = true;
		}
		
		if ( $private_group && ! $bp_group_user_is_member && ! $group_home_visible ) {
			
			return $default_tab;
			
		} else if ( ! $home_disabled && $group_posts_tab_disabled == 0  && $group_home_visible ) {
			
			$default_tab='group-home';
		
		}
		
		return $default_tab;
		
	}

	/**

	 * Choose to reveal private groups public pages.

	 *

	 * @since 1.6.0

	 *

	 * @return Bool

	 */

	public function reveal_private_group_public_pages( $retval ) {
		
		if ( ! bp_is_group() ) {
			
			return $retval;
			
		}
		
		$group = groups_get_current_group();
		
		if ( $group->status != 'private' && $group->status != 'hidden' ) {
			
			return $retval;
			
		}
		
		$logged_in = is_user_logged_in();
		
		// Get list of users group IDs
		$users_groups = array();
		if ( $logged_in ) {
			
			$users_groups = BP_Groups_Member::get_group_ids( get_current_user_id() );
			$users_groups = $users_groups['groups'];
			$current_group = bp_get_current_group_id();
			if ( in_array( $current_group, $users_groups ) ) {
				return $retval;
			}
			
		}
		
		
		if ( bp_is_current_action( 'group-home' ) ) {
	
			$home_disabled = groups_get_groupmeta( $group->id, 'bpps_home_disabled' );
			
			if ( ! $home_disabled ) $group_home_post_id = groups_get_groupmeta( $group->id, 'bpps_home_post_id' );
			
			$post_status = get_post_meta( $group_home_post_id, 'bpgps_group_post_status', true );
		
		} else if (  bp_is_current_action( 'group-posts' ) ) {
			
			//if group-posts page contains public posts then allow it to be viewed.
			global $wpdb;
			$group_posts_page_public_for_group = false;
			// Get IDs for Group only posts
			$group_query = "SELECT ID from $wpdb->posts WHERE post_status = 'group_post'";
			$group_only_ids = $wpdb->get_results($group_query, ARRAY_N);
			// Match group names with post id's
			foreach ( $group_only_ids as $group_post_id ) {
				
				foreach ( $group_post_id as $post_id ) {
					
					if ( $group->id == get_post_meta( $post_id, 'bpgps_group', true ) ) {
						
						$group_post_status = get_post_meta( $post_id, 'bpgps_group_post_status', true );
						
						if ( $group_post_status == 'members_only' ) {
							
							if ( $group_posts_status != 'public' ){
								
								$group_posts_status = 'members_only';
								
							}
							
						} else if ( $group_post_status == 'public' ) {
							
							$group_posts_status = 'public';
						}
					
					}
				}
				
			}
			
			if ( $group_posts_status == 'members_only' && is_user_logged_in() ) {
				
				$group_posts_page_public_for_group = true;
				
			}
			
			if ( $group_posts_status == 'public' ) {
				
				$group_posts_page_public_for_group = true;
				
			}
			
			//Derive post the page slug for the current page
			$page_slug = bpps_get_page_slug();

			if ( $page_slug == 'group-posts' && $group_posts_page_public_for_group ) {
				
				$post_status = 'public';
			
			} else {
				
				//Derive post status from the post slug
				$args = array(
					'name'        			=> $page_slug,
					'post_status'		=> 'group_post',
					'numberposts' 		=> 1
				);

				$post = get_page_by_path($page_slug, 'OBJECT', array( 'post') );
				
				if( $post ) {
					$post_status = get_post_meta( $post->ID, 'bpgps_group_post_status' );
					$post_status = $post_status[0];
				}
			
			}

		} else {
			
			return $retval;
			
		}
		
		if ( $post_status == 'public' || ( is_user_logged_in() && $post_status == 'members_only' ) ) {
			
			return true;
			
		} else {
			
			return $retval;
			
		}
		
	}
	/**

	 * Reorder group nav links.

	 *

	 * @since 1.2.0

	 *

	 * @return

	 */

	
	public function bpps_get_nav_order() {
		
		global $bp;

		if ( empty( $bp->groups->current_group->id ) ) {
			
			return;
			
		}
		
		$home_disabled = groups_get_groupmeta( $bp->groups->current_group->id, 'bpps_home_disabled' );

		if ( bp_is_group() && bp_is_single_item() && $home_disabled ) {
//			$order = groups_get_groupmeta( $bp->groups->current_group->id, 'bpps_nav_order' );//for future use
			$order = array (
				'home' => 2,
				'bpps-home' => 1
			);

			if ( ! empty( $order ) && is_array( $order ) ) {
				foreach ( $order as $slug => $position ) {
					if ( $this->bpps_is_bp_26() ) {
						buddypress()->groups->nav->edit_nav( array( 'position' => $position ), $slug, bp_current_item() );
					} else {
						if ( isset( $bp->bp_options_nav[ $bp->groups->current_group->slug ][ $slug ] ) ) {
							$bp->bp_options_nav[ $bp->groups->current_group->slug ][ $slug ]['position'] = $position;
						}
					}
				}
			}

			do_action( 'bpps_get_nav_order' );

			return $this->bpps_get_group_nav();
		}

		return false;
	}

	/**

	 * Check that we are on BuddyPress 2.6+.

	 *

	 * @since 1.2.0

	 *

	 * @return bool

	 */

	public function bpps_is_bp_26() {
		return version_compare( bp_get_version(), '2.6', '>=' );
	}


	/**

	 * Get the group navigation array.

	 *

	 * @since 1.2.0

	 *

	 * @return array

	 */

 function bpps_get_group_nav() {
	if ( $this->bpps_is_bp_26() ) {
		$nav = buddypress()->groups->nav->get_secondary( array(
			'parent_slug' => bp_get_current_group_slug(),
		) );
	} else {
		$bp  = buddypress();
		$nav = $bp->bp_options_nav[ $bp->groups->current_group->slug ];
	}

	return $nav;
}

	/**
	 * Display group page contents.
	 *
	 * @since 1.0.1
	 *
	 * @return.
	 */

	public function display() {
		//switch based on current view
		$current_action = bp_action_variable( 0 );
		
		if ( bpps_is_single_post() ) {
			$this->view_single();
		} else {
			$this->view_group_posts();
		}
		//just load the plugins template, above functions will attach the content generators
		bp_core_load_template( 'groups/single/plugins' );
	}

	/**

	 * Display group home contents.

	 *

	 * @since 1.2.0

	 *

	 * @return.

	 */

	public function display_home() {
		//switch based on current view
		$current_action = bp_action_variable( 0 );
		
		if ( bpps_is_single_post() ) {
			$this->view_single();
		} else {
			$this->view_group_home();
		}
		//just load the plugins template, above functions will attach the content generators
		bp_core_load_template( 'groups/single/plugins' );
	}

	/**
	 * Display group moderation page contents.
	 *
	 * @since 1.8.0
	 *
	 * @return.
	 */

	public function display_moderation() {
		//switch based on current view
		$current_action = bp_action_variable( 0 );
		
		if ( bpps_is_single_post() ) {
			$this->view_single();
		} else {
			$this->view_group_moderation();
		}
		//just load the plugins template, above functions will attach the content generators
		bp_core_load_template( 'groups/single/plugins' );
	}

	/**
	 * Display Group Posts page.
	 *
	 * @since 1.0.1
	 *
	 * @return.
	 */

	public function view_group_posts () {
			
		add_action( 'bp_template_content', array( $this, 'get_group_posts_content' ) );
	
	}
	
	/**
	 * Display Group Posts Moderation page.
	 *
	 * @since 1.8.0
	 *
	 * @return.
	 */

	public function view_group_moderation () {
			
		add_action( 'bp_template_content', array( $this, 'get_group_moderation_content' ) );
	
	}
	/**

	 * Display Group Home page.

	 *

	 * @since 1.2.0

	 *

	 * @return.

	 */

	public function view_group_home () {
			
		add_action( 'bp_template_content', array( $this, 'get_group_home_content' ) );
	
	}

	/**
	 * Display Group Posts page.
	 *
	 * @since 1.1.0
	 *
	 * @return.
	 */


	public function get_group_posts_content() {
		
		bpps_load_template( 'group-posts.php' ); 
	}

	/**

	 * Display Group Home page.

	 *

	 * @since 1.2.0

	 *

	 * @return.

	 */


	public function get_group_home_content() {
		//$this->display_home_nav();
		bpps_load_template( 'home.php' );
	}

	/**
	 * Display Group Posts page.
	 *
	 * @since 1.1.0
	 *
	 * @return.
	 */


	public function get_group_moderation_content() {
		
		bpps_load_template( 'group-posts-moderation.php' ); 
	}

	/**

	 * Display single post in group context.

	 *

	 * @since 1.0.1

	 *

	 * @return.

	 */	
	
	public function get_single_post_content() {
		//$this->display_options_nav();
		bpps_load_template( 'single-post.php' );
		
	}	

	//for single post screen
	public function view_single () {
		
		$bp = buddypress();
		
		if ( function_exists( 'bp_is_group' ) && !bp_is_group() ) {
			return;
		}

		//do not catch the request for creating new post
		if ( bp_is_action_variable( 'create', 0 ) ) {
			return;
		}
		
		$args = array(
			'name'        		=> bpps_get_page_slug(),
			'post_status'		=> 'group_post',
			'numberposts' 		=> 1
		);
		
		$post = get_page_by_path(bpps_get_page_slug(), 'OBJECT', array( 'post' ) );
		$current_group = groups_get_current_group();
			
		if( $post ) {
			$post_status = get_post_meta( $post->ID, 'bpgps_group_post_status' );
			$post_status = $post_status[0];
		}
		
		if ( isset( $post_status ) && $post_status == 'public' ) {
			$post_viewable = true;
		} else if ( isset( $post_status ) && $post_status == 'members_only' && is_user_logged_in() ){
			$post_viewable = true;
		} else {
			$post_viewable = false;
		}

		if ( bpps_is_disabled( $current_group->id ) ) {
			return;
		}
		//if the group is private/hidden and user is not member, return
		if ( ( ( $current_group->status == 'private' && ! $post_viewable ) || $current_group->status == 'hidden' ) && (! is_user_logged_in() || ! groups_is_user_member( bp_loggedin_user_id(), $current_group->id ) ) ) {
			return; //avoid privacy troubles
		}

		if ( bpps_is_component() && ! empty( $bp->action_variables[0] ) ) {
			//should we check for the existence of the post?
			
			add_action( 'bp_template_content', array( $this, 'get_single_post_content' ) );
		}
	}
	
	/**

	 * Display options nav.

	 *

	 * @since 1.0.1

	 *

	 * @return object html for options nav item.

	 */
	
	public function display_options_nav() {?>
		<div id="subnav" class="item-list-tabs no-ajax">
			<ul>
                <?php bpps_get_options_menu();?>
			</ul>
		</div>
	<?php }

	/**

	 * Display the group home nav

	 *

	 * @since 1.0.1

	 *

	 * @return home menu html

	 */

	public function display_home_nav() {?>
		<div id="subnav" class="item-list-tabs no-ajax">
			<ul>
                <?php bpps_get_home_menu();?>
			</ul>
		</div>
	<?php }

	/**

	 * Check Group Posts nav is enabled.

	 *

	 * @since 1.0.1

	 *

	 * @return object A single instance of this class.

	 */
	 
	function bpps_group_posts_nav_is_disabled( $group_id ) {
		
		if ( empty( $group_id ) ) {
			return false; //if group id is empty, it is active
		}
		
		$is_disabled = groups_get_groupmeta( $group_id, 'bpps_group_nav_is_disabled' );
		
		return apply_filters( 'bpps_group_nav_is_disabled', intval( $is_disabled ), $group_id );
	}
	
}
endif;

/**

 * Boot the plugin.

 *

 * @since 1.0.0

 */

function bpps_groups() {

	return BPPS_Groups::start();

}

add_action( 'plugins_loaded', 'bpps_groups', 5 );	