<?php
/**
 * Reign WCFM Buddypress Activity Functions.
 *
 * These functions handle the recording, deleting and formatting of activity
 * for the user and for this specific component.
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


/**
 * Register activity actions for the Groups component.
 *
 * @return false|null False on failure.
 */
function reign_wcfm_register_activity_actions() {
	$bp = buddypress();

	// Bail out if activity component not activated.
	if ( ! bp_is_active( 'activity' ) ) {
		return false;
	}

	bp_activity_set_action(
		$bp->members->id,
		'wcfm_product_create',
		__( 'Added a product', 'reign-wcfm-addon' ),
		'reign_wcfm_format_activity_action_product_create',
		__( 'Added a product', 'reign-wcfm-addon' ),
		array( 'activity', 'member', 'member_groups', 'group' )
	);

	bp_activity_set_action(
		$bp->members->id,
		'wcfm_product_review',
		__( 'Added a review', 'reign-wcfm-addon' ),
		'reign_wcfm_format_activity_action_product_review',
		__( 'Added a review', 'reign-wcfm-addon' ),
		array( 'activity', 'member', 'member_groups', 'group' )
	);
	
}

add_action( 'bp_register_activity_actions', 'reign_wcfm_register_activity_actions' );

/**
 * Format 'wbcom_product_review' activity actions.
 *
 * @param $action
 * @param $activity
 * @return mixed|void
 */function reign_wcfm_format_activity_action_product_review( $action, $activity ) {

	$user_link         = bp_core_get_userlink( $activity->user_id );
	$product_id        = $activity->item_id;
	$product_title     = get_the_title( $product_id );
	$product_link      = get_permalink( $product_id );
	$product_link_html = '<a href="' . esc_url( $product_link ) . '">' . $product_title . '</a>';
	// Translators: %s is the name.
	$action = sprintf( __( '%2$s wrote a review ', 'reign-wcfm-addon' ), $product_link_html, $user_link );

	return apply_filters( 'reign_wcfm_format_activity_action_product_review', $action, $activity );

}

/**
 * Format 'wbcom_product_create' activity actions.
 *
 * @param $action
 * @param $activity
 * @return mixed|void
 */
function reign_wcfm_format_activity_action_product_create( $action, $activity ) {

	$product_id        = $activity->secondary_item_id;
	$user_link         = bp_core_get_userlink( $activity->user_id );
	$product_title     = get_the_title( $product_id );
	$product_link      = get_permalink( $product_id );
	$product_link_html = '<a href="' . esc_url( $product_link ) . '">' . $product_title . '</a>';
	// Translators: %s is the product name.
	$action = sprintf( __( '%2$s added a new product ', 'reign-wcfm-addon' ), $product_link_html, $user_link );

	return apply_filters( 'reign_wcfm_format_activity_action_product_create', $action, $activity );
}
