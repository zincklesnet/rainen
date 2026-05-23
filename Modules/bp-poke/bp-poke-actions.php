<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add poke button on user profile.
 */
function bpdev_poke_me_button() {

	if ( ! is_user_logged_in() || ! bp_is_active( 'activity' ) ) {
		return;
	}

	if ( ! bp_is_user() || bp_is_my_profile() ) {
		return;
	}

	$disabled   = '';
	$to_poke_id = bp_displayed_user_id();

	if ( bp_poke_user_did_poke( $to_poke_id ) ) {
		$url   = bp_poke_get_poke_url( $to_poke_id, 'poke_back' );
		$label = __( 'Poke back', 'bp-poke' );
	} else {
		$url   = bp_poke_get_poke_url( $to_poke_id, 'poke' );
		$label = __( 'Poke', 'bp-poke' );

		if ( ! bp_poke_can_user_poke( get_current_user_id(), $to_poke_id ) ) {
			$disabled = disabled( false, false, false ); // life is so full of false promises, I am sorry I am not being helpful.
			$label    = __( 'Already Poked', 'bp-poke' );
		}
	}

	?>
    <div class="generic-button poke-button">
        <a class="poke-user-button" title="<?php _e( 'Poke', 'bp-poke' ); ?>"
           href="<?php echo $url; ?>" <?php echo $disabled; ?>><?php echo $label; ?> </a>
    </div>
	<?php
}
add_action( 'bp_member_header_actions', 'bpdev_poke_me_button' );
add_action( 'yz_social_buttons', 'bpdev_poke_me_button' );

/**
 * Delete notifications
 */
function poke_clear_notifications() {

	if ( ! bp_poke_is_poke_action() || ! bp_is_active( 'notifications' ) ) {
		return;
	}

	bp_notifications_mark_notifications_by_type( bp_loggedin_user_id(), 'poke', 'user_poked' );
	bp_notifications_mark_notifications_by_type( bp_loggedin_user_id(), 'poke', 'user_poked_back' );
}
add_action( 'bp_init', 'poke_clear_notifications' );

/**
 * Handle poke, poke back.
 */
function bp_poke_action_poking() {

	if ( ! bp_poke_is_poke_action() ) {
		return;
	}

	$action  = isset( $_REQUEST['poke_action'] ) ? $_REQUEST['poke_action'] : '';
	$user_id = isset( $_REQUEST['user_id'] ) ? (int) $_REQUEST['user_id'] : 0;

	if ( ! $action || ! $user_id ) {
		return;
	}

	if ( get_current_user_id() === $user_id ) {
		return;
	} // can't poke yourself dude

	if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'poke_action' ) ) {
		return;
	}

	$return_url = bp_core_get_user_domain( $user_id );

	if ( 'poke' === $action ) {

		if ( ! bp_poke_can_user_poke( get_current_user_id(), $user_id ) ) {
			bp_core_add_message( sprintf( __( 'You have already poked %s. Please wait for a poke back.', 'bp-poke' ), bp_core_get_user_displayname( $user_id ) ), 'error' );
		} else {
			// poke if we are here.
			bp_poke_poke( $user_id );
			bp_core_add_message( sprintf( __( 'You have poked %s.', 'bp-poke' ), bp_core_get_user_displayname( $user_id ) ) );
		}

		bp_core_redirect( $return_url );

	} elseif ( 'poke_back' === $action ) {

		// if we are here, check for
		// poke/repoke if we are here.
		if ( ! bp_poke_can_user_poke_back( get_current_user_id(), $user_id ) ) {
			bp_core_add_message( sprintf( __( 'You have already poked back %s. Please wait for a poke back.', 'bp-poke' ), bp_core_get_user_displayname( $user_id ) ), 'error' );
		} else {
			// poke if we are here.
			bp_core_add_message( sprintf( __( 'You have poked back %s.', 'bp-poke' ), bp_core_get_user_displayname( $user_id ) ) );
			bp_poke_poke_back( $user_id );
		}

		bp_core_redirect( $return_url );
	}

}
add_action( 'bp_actions', 'bp_poke_action_poking' );
