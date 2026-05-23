<?php
/**
 * This File Contains the General Functions of this Plugin
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Quotes
 * @subpackage Buddypress_Quotes/includes
 */

/**
 * Function to check if the buddypress activtity is quoted.
 *
 * @param int $activity_id Activity ID.
 */
function bpquotes_is_quoted_activity( $activity_id ) {

	$quoted        = false;
	$activity_meta = bp_activity_get_meta( $activity_id, 'bpquotes_meta' );
	if ( $activity_meta ) {
		$quoted = true;
	}
	return apply_filters( 'alter_is_quoted_activity', $quoted );
}

/**
 * Get quote type.
 *
 * @param int $activity_id Activity ID.
 */
function bpquotes_get_quote_class( $activity_id ) {

	$bpquotes_meta = bp_activity_get_meta( $activity_id, 'bpquotes_meta' );
	$quote_class   = ( isset( $bpquotes_meta['bg-type'] ) ) ? $bpquotes_meta['bg-type'] : '';
	return apply_filters( 'alter_bpquotes_get_quote_class', $quote_class );
}

/**
 * Get Quote by Value.
 *
 * @param int $activity_id Activity ID.
 */
function bpquotes_get_quote_bg_value( $activity_id ) {
	$bpquotes_meta = bp_activity_get_meta( $activity_id, 'bpquotes_meta' );
	$quote_value   = ( isset( $bpquotes_meta['bg-value'] ) ) ? $bpquotes_meta['bg-value'] : '';
	return apply_filters( 'alter_bpquotes_get_quote_value', $quote_value );
}

/**
 * Get Quote by inverted value.
 *
 * @param int $activity_id Activity ID.
 */
function bpquotes_get_quote_bg_inverted_value( $activity_id ) {
	$bpquotes_meta = bp_activity_get_meta( $activity_id, 'bpquotes_meta' );
	$quote_value   = ( isset( $bpquotes_meta['bg-inverted-value'] ) ) ? $bpquotes_meta['bg-inverted-value'] : '';
	return apply_filters( 'alter_bpquotes_get_quote_inverted_value', $quote_value );
}


