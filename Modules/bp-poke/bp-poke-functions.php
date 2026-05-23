<?php
/**
 * Poke Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Checks if the $from user can poke $to user
 *
 * @param int $from numeric user id who is poking.
 * @param int $to numeric user id who is being poked.
 *
 * @return bool
 */
function bp_poke_can_user_poke( $from, $to ) {

	$pokes = bp_get_user_meta( $to, 'pokes', true );

	if ( is_array( $pokes ) && isset( $pokes[ $from ] ) ) {
		return false;
	}

	return true;
}

/**
 * Check if the $from user poke back to $to
 *
 * @param int $from numeric user id who is poking.
 * @param int $to numeric user id who is being poked.
 *
 * @return bool
 */
function bp_poke_can_user_poke_back( $from, $to ) {

	$pokes = bp_get_user_meta( $from, 'pokes', true );

	if ( is_array( $pokes ) && isset( $pokes[ $to ] ) ) {
		return true;
	}

	return false;
}

/**
 * Check if current user has poked the given user
 *
 * @param int $user_id numeric user id of the other user.
 *
 * @return bool
 */
function bp_poke_user_did_poke( $user_id ) {

	$other_user_id = get_current_user_id();

	return bp_poke_can_user_poke_back( $other_user_id, $user_id );
}

/**
 * Get pokes tab url.
 *
 * @param int $user_id numeric user id for which we want to find the url.
 *
 * @return string
 */
function bp_poke_get_poke_list_url( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	return bp_core_get_user_domain( $user_id ) . bp_get_activity_slug() . '/' . BP_POKE_SLUG . '/';
}

/**
 * Get the pokeback url.
 *
 * @param int $user_id numeric user id.
 *
 * @return string
 */
function bp_poke_get_poke_back_url( $user_id ) {
	return bp_poke_get_poke_url( $user_id, 'poke_back' );
}

/**
 * Get poke url.
 *
 * @param int $user_id numeric user id.
 * @param string $action poke or poke_back .
 *
 * @return string
 */
function bp_poke_get_poke_url( $user_id, $action ) {
	$url = bp_poke_get_poke_list_url() . '?poke_action=' . $action . '&user_id=' . $user_id . '&_wpnonce=' . wp_create_nonce( 'poke_action' );

	return $url;
}

/**
 * Format poke notifications
 *
 * @param string $action action.
 * @param int $item_id context related item.
 * @param int $secondary_item_id context related secondary item.
 * @param int $total_items no. of items in the notification.
 * @param string $format output format.
 *
 * @return array|string
 */
function bp_poke_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {

	if ( 'user_poked' === $action || 'user_poked_back' === $action ) {
		$link           = bp_poke_get_poke_list_url();
		$logged_user_id = $secondary_item_id;
		$poked_by       = $item_id;
		$title          = __( 'poke', 'bp-poke' );

		$text = '';

		if ( $total_items > 1 ) {
			$text = sprintf( __( '%d people poked you.', 'bp-poke' ), $total_items );
		} else {
			$text = sprintf( __( '%s poked you', 'bp-poke' ), bp_core_get_user_displayname( $poked_by ) );
		}

		if ( 'string' === $format ) {
			return '<a href="' . $link . '" title="' . $title . '">' . $text . '</a>';
		}

		return array(
			'link' => $link,
			'text' => $text,
		);
	}
}

/**
 *  Is it poke action?
 *
 * @return boolean
 */
function bp_poke_is_poke_action() {

	if ( is_user_logged_in() && bp_is_my_profile() && bp_is_activity_component() && bp_is_current_action( BP_POKE_SLUG ) ) {
		return true;
	}

	return false;
}

/**
 * Poke given user.
 *
 * @param int $user_id id of the user to be poked.
 */
function bp_poke_poke( $user_id ) {

	$bp = buddypress();

	$poked_by  = get_current_user_id();
	$component = $bp->poke->id;
	$action    = 'user_poked';
	$time      = current_time( 'timestamp', 1 );

	// Get past poke details for this user.
	$pokes = bp_get_user_meta( $user_id, 'pokes', true );
	$pokes = ( empty( $pokes ) || ! is_array( $pokes ) ) ? array() : $pokes;

	// Assuming one user can poke only once.
	$pokes[ $poked_by ] = array( 'poked_by' => $poked_by, 'time' => $time );

	// add data in user_meta table.
	bp_update_user_meta( $user_id, 'pokes', $pokes );

	if ( ! bp_is_active( 'notifications' ) ) {
		return;
	}

	bp_notifications_add_notification( array(
		'item_id'          => $poked_by,
		'user_id'          => $user_id,
		'component_name'   => $component,
		'component_action' => $action,
	) );
}

/**
 * Poke back to the user
 *
 * @param int $user_id numeric user id to be poked back.
 */
function bp_poke_poke_back( $user_id ) {

	$bp        = buddypress();
	$poked_by  = get_current_user_id();
	$component = $bp->poke->id;
	$action    = 'user_poked_back';

	// we need to delete the pokes of the user whom the current user poked back, in current user;s meta.
	$logged_pokes = bp_get_user_meta( $poked_by, 'pokes', true );
	$logged_pokes = ( empty( $logged_pokes ) || ! is_array( $logged_pokes ) ) ? array() : $logged_pokes;

	// unset the poke from the user whom we just poked back
	// delete the old poke info.
	unset( $logged_pokes[ $user_id ] );

	// now store back the updated pokes to current users meta.
	bp_update_user_meta( $poked_by, 'pokes', $logged_pokes );

	// update for the user whom we have poked.
	$time = current_time( 'timestamp', 1 );

	// get past poke details for this user.
	$pokes = bp_get_user_meta( $user_id, 'pokes', true );

	$pokes = ( empty( $pokes ) || ! is_array( $pokes ) ) ? array() : $pokes;

	// assuming one user can poke only once.
	$pokes[ $poked_by ] = array( 'poked_by' => $poked_by, 'time' => $time );

	bp_update_user_meta( $user_id, 'pokes', $pokes );

	if ( ! bp_is_active( 'notifications' ) ) {
		return;
	}

	bp_notifications_add_notification( array(
		'item_id'          => $poked_by,
		'user_id'          => $user_id,
		'component_name'   => $component,
		'component_action' => $action,
	) );
}
