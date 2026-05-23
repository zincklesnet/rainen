<?php
/**
 * Core component classes.
 *
 * @package Reign
 * @subpackage Core
 * @since 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Create a set of Peepso-specific links for use in the Menus admin UI.
 *
 * Borrowed heavily from {@link Walker_Nav_Menu_Checklist}, but modified so as not
 * to require an actual post type or taxonomy, and to force certain CSS classes.
 *
 * @since 1.9.0
 */
class Reign_Walker_Nav_Menu_Checklist extends Walker_Nav_Menu {

	/**
	 * Constructor.
	 *
	 * @see Walker_Nav_Menu::__construct() for a description of parameters.
	 *
	 * @param array|bool $fields See {@link Walker_Nav_Menu::__construct()}.
	 */
	public function __construct( $fields = false ) {
		if ( $fields ) {
			$this->db_fields = $fields;
		}
	}

	/**
	 * Create the markup to start a tree level.
	 *
	 * @see Walker_Nav_Menu::start_lvl() for description of parameters.
	 *
	 * @param string $output See {@Walker_Nav_Menu::start_lvl()}.
	 * @param int    $depth  See {@Walker_Nav_Menu::start_lvl()}.
	 * @param array  $args   See {@Walker_Nav_Menu::start_lvl()}.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class='children'>\n";
	}

	/**
	 * Create the markup to end a tree level.
	 *
	 * @see Walker_Nav_Menu::end_lvl() for description of parameters.
	 *
	 * @param string $output See {@Walker_Nav_Menu::end_lvl()}.
	 * @param int    $depth  See {@Walker_Nav_Menu::end_lvl()}.
	 * @param array  $args   See {@Walker_Nav_Menu::end_lvl()}.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent</ul>";
	}

	/**
	 * Create the markup to start an element.
	 *
	 * @see Walker::start_el() for description of parameters.
	 *
	 * @param string       $output Passed by reference. Used to append additional
	 *                             content.
	 * @param object       $item   Menu item data object.
	 * @param int          $depth  Depth of menu item. Used for padding.
	 * @param object|array $args   See {@Walker::start_el()}.
	 * @param int          $id     See {@Walker::start_el()}.
	 */
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $_nav_menu_placeholder;

		$_nav_menu_placeholder = ( 0 > $_nav_menu_placeholder ) ? intval($_nav_menu_placeholder) - 1 : -1;
		$possible_object_id = isset( $item->post_type ) && 'nav_menu_item' == $item->post_type ? $item->object_id : $_nav_menu_placeholder;
		$possible_db_id = ( ! empty( $item->ID ) ) && ( 0 < $possible_object_id ) ? (int) $item->ID : 0;

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$output .= $indent . '<li>';
		$output .= '<label class="menu-item-title">';
		$output .= '<input type="checkbox" class="menu-item-checkbox';

		if ( property_exists( $item, 'label' ) ) {
			$title = $item->label;
		}

		$output .= '" name="menu-item[' . $possible_object_id . '][menu-item-object-id]" value="'. esc_attr( $item->object_id ) .'" /> ';
		$output .= isset( $title ) ? esc_html( $title ) : esc_html( $item->title );
		$output .= '</label>';

		if ( empty( $item->url ) ) {
			$item->url = $item->guid;
		}

		if ( ! in_array( array( 'peepso-menu', 'peepso-'. $item->post_excerpt .'-nav' ), $item->classes ) ) {
			$item->classes[] = 'peepso-menu';
			$item->classes[] = 'peepso-'. $item->post_excerpt .'-nav';
		}

		// Menu item hidden fields.
		$output .= '<input type="hidden" class="menu-item-db-id" name="menu-item[' . $possible_object_id . '][menu-item-db-id]" value="' . $possible_db_id . '" />';
		$output .= '<input type="hidden" class="menu-item-object" name="menu-item[' . $possible_object_id . '][menu-item-object]" value="'. esc_attr( $item->object ) .'" />';
		$output .= '<input type="hidden" class="menu-item-parent-id" name="menu-item[' . $possible_object_id . '][menu-item-parent-id]" value="'. esc_attr( $item->menu_item_parent ) .'" />';
		$output .= '<input type="hidden" class="menu-item-type" name="menu-item[' . $possible_object_id . '][menu-item-type]" value="custom" />';
		$output .= '<input type="hidden" class="menu-item-title" name="menu-item[' . $possible_object_id . '][menu-item-title]" value="'. esc_attr( $item->title ) .'" />';
		$output .= '<input type="hidden" class="menu-item-url" name="menu-item[' . $possible_object_id . '][menu-item-url]" value="'. esc_attr( $item->url ) .'" />';
		$output .= '<input type="hidden" class="menu-item-target" name="menu-item[' . $possible_object_id . '][menu-item-target]" value="'. esc_attr( $item->target ) .'" />';
		$output .= '<input type="hidden" class="menu-item-attr_title" name="menu-item[' . $possible_object_id . '][menu-item-attr_title]" value="'. esc_attr( $item->attr_title ) .'" />';
		$output .= '<input type="hidden" class="menu-item-classes" name="menu-item[' . $possible_object_id . '][menu-item-classes]" value="'. esc_attr( implode( ' ', $item->classes ) ) .'" />';
		$output .= '<input type="hidden" class="menu-item-xfn" name="menu-item[' . $possible_object_id . '][menu-item-xfn]" value="'. esc_attr( $item->xfn ) .'" />';
	}
}


/*
 * Add Nav Menu option 
 * 
 * Date: 2020-07-28
 */

add_action( 'load-nav-menus.php', 'reign_theme_admin_wp_nav_menu_meta_box' );
function reign_theme_admin_wp_nav_menu_meta_box() {

	add_meta_box( 'add-peppso-nav-menu', __( 'Peepso', 'reign' ), 'reign_theme_admin_peepso_nav_menu_meta_box', 'nav-menus', 'side', 'default' );
	
}
function reign_theme_admin_peepso_nav_menu_meta_box() {
	global $nav_menu_selected_id;

	$walker = new Reign_Walker_Nav_Menu_Checklist( false );
	$args   = array( 'walker' => $walker );

	$post_type_name = 'peepso';

	$tabs = array();

	$tabs['loggedin']['label'] = __( 'Logged-In', 'reign' );
	$tabs['loggedin']['pages'] = reign_peepso_nav_menu_get_loggedin_pages();

	?>

	<div id="reign-menu" class="posttypediv">
		<h4><?php _e( 'Logged-In', 'reign' ); ?></h4>
		<p><?php _e( '<em>Logged-In</em> links are relative to the current user, and are not visible to visitors who are not logged in.', 'reign' ); ?></p>

		<div id="tabs-panel-posttype-<?php echo $post_type_name; ?>-loggedin" class="tabs-panel tabs-panel-active">
			<ul id="reign-menu-checklist-loggedin" class="categorychecklist form-no-clear">
				<?php echo walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $tabs['loggedin']['pages'] ), 0, (object) $args ); ?>
			</ul>
		</div>

		<?php
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);
		?>

		<p class="button-controls">			
			<span class="add-to-menu">
				<input type="submit"
				<?php
				if ( function_exists( 'wp_nav_menu_disabled_check' ) ) :
					wp_nav_menu_disabled_check( $nav_menu_selected_id );
				endif;
				?>
				 class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'reign' ); ?>" name="add-custom-menu-item" id="submit-reign-menu" />
				<span class="spinner"></span>
			</span>
		</p>
	</div><!-- /#reign-menu -->

	<?php
}


function reign_peepso_nav_menu_get_loggedin_pages() {
	
	 // List of links to be displayed
	$links = apply_filters('peepso_navigation_profile', array('_user_id'=>get_current_user_id()));
	$instance['links'] = $links;
	$instance['links']['preferences'] = array(
		'href' => 'preferences',
		'icon' => 'gcis gci-user-edit',
		'label' => __('Preferences', 'reign'),
	);

	$instance['links']['log-out'] = array(
		'href' => PeepSo::get_page('logout'),
		'icon' => 'gcis gci-power-off',
		'label' => __('Log Out', 'reign'),
		'widget'=>TRUE,
	);

	foreach( $instance['links'] as $key=>$value){
		$peepso_menu_items[] = array(
								'name' => $value['label'],
								'slug' => $key,
								'link' => $value['href'],
								'icon' => $value['icon'],								
							);
	}
	
	

	// If there's nothing to show, we're done.
	if ( count( $peepso_menu_items ) < 1 ) {
		return false;
	}

	$page_args = array();

	foreach ( $peepso_menu_items as $peepso_item ) {

		// Remove <span>number</span>.
		$item_name = $peepso_item['name'];

		$page_args[ $peepso_item['slug'] ] = (object) array(
			'ID'             => -1,
			'post_title'     => $item_name,
			'post_author'    => 0,
			'post_date'      => 0,
			'post_excerpt'   => $peepso_item['slug'],
			'post_name'      => 'peepso-'.$peepso_item['slug'],
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'guid'           => $peepso_item['link']
		);
	}

	return $page_args;
}

function reign_peepso_walker_nav_menu_start_el( $item_output, $item, $depth, $args){
	
	$links = apply_filters('peepso_navigation_profile', array('_user_id'=>get_current_user_id()));
	$instance['links'] = $links;
	$instance['links']['preferences'] = array(
		'href' => 'preferences',
		'icon' => 'gcis gci-user-edit',
		'label' => __('Preferences', 'reign'),
	);

	$instance['links']['log-out'] = array(
		'href' => PeepSo::get_page('logout'),
		'icon' => 'gcis gci-power-off',
		'label' => __('Log Out', 'reign'),
		'widget'=>TRUE,
	);
	
	$peepso_link = array();
	foreach( $instance['links'] as $key=>$value){		
		$peepso_link[] = ( $key != 'videos') ? $key : 'audio-video';
	}	
	
	if ( in_array( $item->post_name,$peepso_link) && !is_user_logged_in() ) {
		return '';
	}
	return $item_output;
}
function reign_peepso_nav_menu_link_attributes( $atts, $item, $args, $depth){
	
	$user_id    = get_current_user_id();
    $current_user      = PeepSoUser::get_instance($user_id);
	
	$links = apply_filters('peepso_navigation_profile', array('_user_id'=>get_current_user_id()));
	$instance['links'] = $links;
	$instance['links']['preferences'] = array(
		'href' => $current_user->get_profileurl() . 'about/preferences/',
		'icon' => 'gcis gci-user-edit',
		'label' => __('Preferences', 'reign'),
	);

	$instance['links']['log-out'] = array(
		'href' => PeepSo::get_page('logout'),
		'icon' => 'gcis gci-power-off',
		'label' => __('Log Out', 'reign'),
		'widget'=>TRUE,
	);
	
	$peepso_link = array();
	foreach( $instance['links'] as $key=>$value){		
		$peepso_link[] = ( $key != 'videos') ? $key : 'audio-video';
	}
	
	if ( in_array( $item->post_name, $peepso_link) && is_user_logged_in() ) {
		
		$item->post_name = ($item->post_name == 'audio-video') ? 'videos' : $item->post_name ;
		
		$href = $current_user->get_profileurl(). $instance['links'][$item->post_name]['href'];
		if('http' == substr(strtolower($instance['links'][$item->post_name]['href']), 0,4)) {
			$href = $instance['links'][$item->post_name]['href'];
		}
		$atts['href'] = $href;
	}	
	
	return $atts;
}
add_filter('walker_nav_menu_start_el', 'reign_peepso_walker_nav_menu_start_el', 99, 4 );
add_filter('nav_menu_link_attributes', 'reign_peepso_nav_menu_link_attributes', 99, 4 );