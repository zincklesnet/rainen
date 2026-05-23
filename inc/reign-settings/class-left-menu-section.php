<?php
/**
 * Reign Left Panel Section class.
 *
 * @since 7.1.2
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Reign_Left_Panel_Section Class
 *
 * This class handles to add sections into the Left Panel menus in BuddyBoss Theme.
 */
class Reign_Left_Panel_Section {

	/**
	 * The single instance of the class.
	 *
	 * @since 7.1.2
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Get the instance of this class.
	 *
	 * @since 7.1.2
	 *
	 * @return object
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			$class_name     = __CLASS__;
			self::$instance = new $class_name();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 *
	 * @since 7.1.2
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'load-nav-menus.php', array( $this, 'load_nav_menus' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );

		add_filter( 'wp_setup_nav_menu_item', array( $this, 'setup_nav_menu_item' ), 99, 1 );

	}

	/******************** HOOKS ********************/

	/**
	 * Add custom code when load the nav menus into backend.
	 *
	 * @since 7.1.2
	 *
	 * @return void
	 */
	public function load_nav_menus() {
		add_meta_box(
			'add-left-panel-sections-nav-menu',
			esc_html__( 'Reign Panel Sections', 'reign' ),
			array( $this, 'rg_admin_do_wp_nav_menu_meta_box_left_panel_sections' ),
			'nav-menus',
			'side'
		);
	}

	/**
	 * Load scripts into the admin.
	 *
	 * @since 7.1.2
	 *
	 * @return void
	 */
	public function load_scripts() {
		if ( 'nav-menus' === get_current_screen()->id ) {
			wp_register_script( 'reign-theme-left-panel-sections', get_template_directory_uri() . '/assets/js/left-panel-sections.min.js', array( 'jquery' ), REIGN_THEME_VERSION, true );
			wp_enqueue_script( 'reign-theme-left-panel-sections' );
		}
	}

	/******************** FILTERS ********************/

	/**
	 * Fires immediately after a new navigation menu item has been added.
	 *
	 * @since 7.1.2
	 *
	 * @param WP_Post $menu_item Nav menu object.
	 *
	 * @return mixed
	 */
	public function setup_nav_menu_item( $menu_item ) {

		if ( isset( $menu_item->post_content ) && 'reign-theme-section' === $menu_item->post_content ) {
			$menu_item->object     = 'section';
			$menu_item->type_label = esc_html__( 'Section', 'reign' );
		}

		return $menu_item;
	}


	/******************** FUNCTIONS ********************/

	/**
	 * Build and populate the Left Panel Sections accordion on Appearance > Menus.
	 *
	 * @since 7.1.2
	 *
	 * @global $nav_menu_selected_id , $menu_locations
	 */
	public static function rg_admin_do_wp_nav_menu_meta_box_left_panel_sections() {
		global $nav_menu_selected_id;
		$theme_locations = get_nav_menu_locations();

		if ( empty( $nav_menu_selected_id ) || empty( $theme_locations ) ) {
			return;
		}

		$ele_class = in_array( $nav_menu_selected_id, array_values( $theme_locations ), true ) ? 'style="display: none;"' : '';
		$menu_ids  = array( 'panel-menu', 'panel-menu-loggedout', 'mobile-menu-logged-in', 'mobile-menu-logged-out' );
		foreach ( $theme_locations as $key => $value ) {
			if ( $value === $nav_menu_selected_id && ! in_array( $key, $menu_ids, true ) ) {
				$ele_class = '';
				break;
			}
		}
		?>

		<div id="left-panel-menu" class="posttypediv">
			<p><?php esc_html_e( 'You can visually group menu items in the Left Panel and Mobile menu by indenting them within Sections.', 'reign' ); ?></p>

			<p class="button-controls" <?php echo empty( $ele_class ) ? 'style="display: none;"' : ''; ?>>
				<span class="add-to-menu add-left-panel-sections">
					<input type="submit" class="button-secondary right" value="<?php esc_attr_e( 'Add Section', 'reign' ); ?>" name="add-left-panel-sections-menu-item" id="submit-left-panel-section-menu"/>
					<span class="spinner"></span>
				</span>
			</p>
			<p class="warning" <?php echo wp_kses_post( $ele_class ); ?>>
				<?php
				printf(
					/* translators: Left Panel menu location text. */
					wp_kses_post( __( 'Link this menu to either %s only to add sections.', 'reign' ) ),
					'<strong>' . esc_html__( 'Left Panel location', 'reign' ) . '</strong>'
				);
				?>
			</p>
		</div>
		<?php
	}

}

if ( class_exists( 'Reign_Left_Panel_Section' ) ) {
	Reign_Left_Panel_Section::instance();
}
