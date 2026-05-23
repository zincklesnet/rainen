<?php
/**
 * This file contains general functions used throughout the plugin.
 *
 * @link       https://wbcomdesigns.com/
 * @since      1.0.0
 *
 * @package    Buddypress_Newsfeed
 * @subpackage Buddypress_Newsfeed/includes
 */

/**
 * Get Action Lists.
 */
function bnews_get_act_list() {

	$_bp_enable_activity_follow = get_option( '_bp_enable_activity_follow' );

	$act_list = array(
		'just-me'   => __( 'Personal (The corresponding Personal tab will still appear on the member activity page.)', 'buddypress-newsfeed' ),
		'mentions'  => __( 'Mentions', 'buddypress-newsfeed' ),
		'favorites' => __( 'Favorites', 'buddypress-newsfeed' ),
	);
	if ( bp_is_active( 'friends' ) ) {
		$act_list['friends'] = __( 'Friends', 'buddypress-newsfeed' );
	}
	if ( bp_is_active( 'groups' ) ) {
		$act_list['groups'] = __( 'Groups', 'buddypress-newsfeed' );
	}
	if ( class_exists( 'BP_Follow_Component' ) || 1 == $_bp_enable_activity_follow ) {
		$act_list['following'] = __( 'Following', 'buddypress-newsfeed' );
	}

	return $act_list = apply_filters( 'bnews_add_activity_lists_to_filter', $act_list );
}

/**
 *Retrieves and includes the specified template file.
 *
 * @since 1.7.0
 * @param mixed  $template_name
 * @param array  $args (default: array()).
 * @param string $template_path (default: '').
 * @param string $default_path (default: '').
 */
function bp_news_feed_get_template( $template_name, $args = array(), $template_path = 'nouveau', $default_path = '' ) {
	if ( $args && is_array( $args ) ) {
		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- Please, forgive us.
		extract( $args );
	}
	include bp_news_feed_locate_template( $template_name, $template_path, $default_path );
}

/**
 * Locate template.
 *
 * Locate the called template.
 * Search Order:
 *
 * @since 1.7.0
 *
 * @param   string $template_name          Template to load.
 * @param  @param   string $template_path  Path to templates.
 * @param   string $default_path           Default path to template files.
 * @return  string                          Path to the template file.
 */
function bp_news_feed_locate_template( $template_name, $template_path, $default_path = '' ) {
	// Look within the specified theme path first — this takes priority.
	$template = locate_template(
		array(
			trailingslashit( $template_path ) . $template_name,
			$template_name,
		)
	);

	// Get default template.
	if ( ! $template && false !== $default_path ) {
		$default_path = $default_path ? $default_path : BNEWS_PLUGIN_PATH . 'templates/';
		$full_path = trailingslashit( $default_path . $template_path ) . $template_name;
		if ( file_exists( $full_path ) ) {
			$template = $full_path;
		}
	}
	return apply_filters( 'bp_news_feed_locate_template', $template, $template_name, $template_path, $default_path );
}






