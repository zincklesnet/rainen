<?php
/*
* Filters
*
* @package bp-post-status
*/

if(!defined('ABSPATH')) {
	exit;
}

/**
 * Overloads the permalink for group posts.
 *
 * @since 1.6.3
 *
 * @param link $permalink the original link
 * @param int|WP_Post $post Optional. Post ID or post object. Default is the global `$post`.
 */

 function bpps_the_post_link( $permalink, $post, $leavename ) {
	if ( ! is_object( $post ) ) {
		global $post;
	}
	
	if ( empty( $post ) ) {
		return $permalink;
	}

	if ( $post->post_status == 'group_post' ) {
		$permalink = bpps_get_post_permalink( $post );
	}
	/**
	 * Filters the display of the permalink for the current post.
	 *
	 * @since 1.6.3
	 *
	 * @param string      $permalink The permalink for the current post.
	 * @param int|WP_Post $post      Post ID, WP_Post object, or 0. Default 0.
	 */
	return esc_url( apply_filters( 'bpps_the_post_link', $permalink, $post ) );
}

add_action( 'post_link', 'bpps_the_post_link', 10, 3 );

/**
 * Overloads the search template to use bp profile template where appropriate.
 *
 * @since 1.6.4
 *
 * @param template the originally selected template
 * @param $type The type of page requested
 * $param $templates The template list
 */

function bpps_search_template( $template, $type, $templates ) {

	
	if ( $type == 'search' ) {
		$ps_type = $urlbits = explode( "/", $_SERVER["HTTP_REFERER"]);//esc_attr( $_GET['bpps-search-type'] );
		if ( in_array( 'my-posts', $urlbits ) ) {//isset( $ps_type ) && $ps_type === 'my-post-search' ) {
			if ( file_exists( STYLESHEETPATH . '/bpps/my-posts-search.php' ) ) {
				$template = STYLESHEETPATH . '/bpps/my-posts-search.php' ;
			} elseif ( file_exists( TEMPLATEPATH . '/bpps/my-posts-search.php' ) ) {
				$template = TEMPLATEPATH . '/bpps/my-posts-search.php' ;
			} else {
				$template = BPPS_PLUGIN_DIR . '/templates/my-posts-search.php';
			}	
		} else if ( in_array( 'group-posts', $urlbits ) ) {//isset( $ps_type ) && $ps_type === 'group-search' ) {
			if ( file_exists( STYLESHEETPATH . '/bpps/group-posts-search.php' ) ) {
				$template = STYLESHEETPATH . '/bpps/group-posts-search.php' ;
			} elseif ( file_exists( TEMPLATEPATH . '/bpps/group-posts-search.php' ) ) {
				$template = TEMPLATEPATH . '/bpps/group-posts-search.php' ;
			} else {
				$template = BPPS_PLUGIN_DIR . '/templates/group-posts-search.php';
			}	
		}
	}
	 
	return $template;
}

//add_filter( 'search_template', 'bpps_search_template', 10, 3 );