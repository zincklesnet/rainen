<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers poke as a BuddyPress component
 */
class BP_Poke_Component extends BP_Component {

	/**
	 * Start the component creation process
	 */
	public function __construct() {

		$bp = buddypress();
		parent::start(
			'poke',
			__( 'Poke', 'bp-poke' ),
			BP_POKE_DIR_URL
		);

		$bp->active_components[ $this->id ] = 1;

		// add_action( 'bp_setup_nav', array( $this, 'setup_settings_nav' ), 11 );
	}

	/**
	 * Include files
	 *
	 * @param array $files an array of file paths to be loaded.
	 */
	public function includes( $files = array() ) {
	}

	/**
	 * Setup globals
	 *
	 * @param array $globals globals to be registered.
	 */
	public function setup_globals( $globals = array() ) {

		if ( ! defined( 'BP_POKE_SLUG' ) ) {
			define( 'BP_POKE_SLUG', 'pokes' );
		}

		// Note that global_tables is included in this array.
		$globals = array(
			'slug'                  => BP_POKE_SLUG,
			'has_directory'         => false,
			'notification_callback' => 'bp_poke_format_notifications',
			'global_tables'         => false,
		);

		parent::setup_globals( $globals );

	}
}

/**
 * Setup poke component
 */
function bp_poke_setup_component() {

	$bp       = buddypress();
	$bp->poke = new BP_Poke_Component();

}

add_action( 'bp_setup_components', 'bp_poke_setup_component', 6 );
