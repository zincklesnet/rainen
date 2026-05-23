<?php

//if inside the post loop
function in_bpcp_loop() {
	
	$bp = buddypress();
	
	return isset( $bp->bp_custom_pages ) ? $bp->bp_custom_pages->in_the_loop : false;
}

//use it to mark t5he start of bcg post loop
function bpcp_loop_start() {
	$bp = buddypress();

	$bp->bp_custom_pages = new stdClass();
	$bp->bp_custom_pages->in_the_loop = true;
}

//use it to mark the end of bcg loop
function bpcp_loop_end() {
	$bp = buddypress();

	$bp->bp_custom_pages->in_the_loop = false;
}

/**
 * Generate Pagination Link for posts
 * @param type $q 
 */
function bpcp_pagination( $q ) {

	$posts_per_page = intval( get_query_var( 'posts_per_page' ) );
	$paged = intval( get_query_var( 'paged' ) );
	
	$numposts = $q->found_posts;
	$max_page = $q->max_num_pages;
	
	if ( empty( $paged ) || $paged == 0 ) {
		$paged = 1;
	}

	$pag_links = paginate_links( array(
		'base'		=> add_query_arg( array( 'paged' => '%#%', 'num' => $posts_per_page ) ),
		'format'	=> '',
		'total'		=> ceil( $numposts / $posts_per_page ),
		'current'	=> $paged,
		'prev_text'	=> '&larr;',
		'next_text'	=> '&rarr;',
		'mid_size'	=> 1
	) );
	
	echo $pag_links;
}

//viewing x of z posts
function bpcp_posts_pagination_count( $q ) {

	$posts_per_page = intval( get_query_var( 'posts_per_page' ) );
	$paged = intval( get_query_var( 'paged' ) );
	
	$numposts = $q->found_posts;
	$max_page = $q->max_num_pages;
	
	if ( empty( $paged ) || $paged == 0 ) {
		$paged = 1;
	}

	$start_num = intval( $posts_per_page * ( $paged - 1 ) ) + 1;
	$from_num = bp_core_number_format( $start_num );
	$to_num = bp_core_number_format( ( $start_num + ( $posts_per_page - 1 ) > $numposts ) ? $numposts : $start_num + ( $posts_per_page - 1 )  );
	
	$total = bp_core_number_format( $numposts );

	//$taxonomy = get_taxonomy( bcg_get_taxonomies() );
		$menu_name = get_option( 'bp-custom-pages-menu-name' );
		if ( $menu_name === false ) {
			$menu_name = 'Custom Pages';
		}
	/* translators: Test supporting pagination for multiple pages of results.*/
	printf( esc_attr__( 'Viewing %1$s %2$s to %3$s (of %4$s )', 'bp-custom-pages' ), esc_textarea($menu_name), esc_attr($from_num), esc_attr($to_num), esc_attr($total) ) . "&nbsp;";
	?>
	<span class="ajax-loader"></span><?php
}




/**
 * @since 1.0.0
 *
 * @uses bp_is_current_component()
 * @uses apply_filters() to allow this value to be filtered
 * @return bool True if it's the example component, false otherwise
 */
function bp_is_pages_component() {
	$is_pages_component = bp_is_current_component( 'custom-pages' );

	return apply_filters( 'bp_is_custom_pages_component', $is_pages_component );
}

/**
 * Echo the component's slug
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */
function bp_custom_pages_slug() {
	echo esc_textarea(bp_get_custom_pages_slug());
}

/**
 * Return the component's slug
 *
 * Having a template function for this purpose is not absolutely necessary, but it helps to
 * avoid too-frequent direct calls to the $bp global.
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 *
 * @uses apply_filters() Filter 'bp_get_pages_slug' to change the output
 * @return str $example_slug The slug from $bp->bp_custom_pages->slug, if it exists
 */
function bp_get_custom_pages_slug() {
	global $bp;

	// Avoid PHP warnings, in case the value is not set for some reason
	$example_slug = isset( $bp->bp_custom_pages->slug ) ? $bp->bp_custom_pages->slug : '';

	return apply_filters( 'bp_get_custom_pages_slug', $example_slug );
}

/**
 * Echo the component's root slug
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */
function bp_custom_pages_root_slug() {
	echo esc_textarea(bp_get_custom_pages_root_slug());
}

/**
 * Return the component's root slug
 *
 * Having a template function for this purpose is not absolutely necessary, but it helps to
 * avoid too-frequent direct calls to the $bp global.
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 *
 * @uses apply_filters() Filter 'bp_get_pages_root_slug' to change the output
 * @return str $example_root_slug The slug from $bp->bp_custom_pages->root_slug, if it exists
 */
function bp_get_custom_pages_root_slug() {
	global $bp;

	// Avoid PHP warnings, in case the value is not set for some reason
	$example_root_slug = isset( $bp->bp_custom_pages->root_slug ) ? $bp->bp_custom_pages->root_slug : 'bp-custom-pages';

	return apply_filters( 'bp_get_custom_pages_root_slug', $example_root_slug );
}



