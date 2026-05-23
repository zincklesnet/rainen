<?php

/********************************************************************************
 * Screen Functions
 *
 * Screen functions are the controllers of BuddyPress. They will execute when their
 * specific URL is caught. They will first save or manipulate data using business
 * functions, then pass on the user to a template file.
 */

/**
 * If your component uses a top-level directory, this function will catch the requests and load
 * the index page.
 *
 * @package BuddyPress_Template_Pack
 * @since 1.6
 */
function bp_custom_pages_directory_setup() {
	if ( bp_is_pages_component() && ! bp_current_action() && ! bp_current_item() ) {
		// This wrapper function sets the $bp->is_directory flag to true, which help other
		// content to display content properly on your directory.
		bp_update_is_directory( true, 'custom-pages' );

		// Add an action so that plugins can add content or modify behavior
		do_action( 'bp_custom_pages_directory_setup' );

		bp_core_load_template( apply_filters( 'bp_custom_pages_directory_template', 'bp-custom-pages/index' ) );
	}
}
//add_action( 'bp_screens', 'bp_custom_pages_directory_setup' );


/**
 * bp_custom_pages_user_screen_one()
 *
 * Sets up and displays the screen output for the sub nav item "bp-custom-pages/screen-one"
 */
function bp_custom_pages_user_screen() {
	global $bp;

	/**
	 * There are three global variables that you should know about and you will
	 * find yourself using often.
	 *
	 * $bp->current_component (string)
	 * This will tell you the current component the user is viewing.
	 *
	 * Example: If the user was on the page http://example.org/members/andy/groups/my-groups
	 *          $bp->current_component would equal 'groups'.
	 *
	 * $bp->current_action (string)
	 * This will tell you the current action the user is carrying out within a component.
	 *
	 * Example: If the user was on the page: http://example.org/members/andy/groups/leave/34
	 *          $bp->current_action would equal 'leave'.
	 *
	 * $bp->action_variables (array)
	 * This will tell you which action variables are set for a specific action
	 *
	 * Example: If the user was on the page: http://example.org/members/andy/groups/join/34
	 *          $bp->action_variables would equal array( '34' );
	 *
	 * There are three handy functions you can use for these purposes:
	 *   bp_is_current_component()
	 *   bp_is_current_action()
	 *   bp_is_action_variable()
	 */

	/* Add a do action here, so your component can be extended by others. */
	do_action( 'bp_custom_pages_user_screen' );

	/****
	 * Displaying Content
	 */

	/****
	 * OPTION 1:
	 * You've got a few options for displaying content. Your first option is to bundle template files
	 * with your plugin that will be used to output content.
	 *
	 * In an earlier function bp_custom_pages_load_template_filter() we set up a filter on the core BP template
	 * loading function that will make it first look in the plugin directory for template files.
	 * If it doesn't find any matching templates it will look in the active theme directory.
	 *
	 * This example component comes bundled with a template for screen one, so we can load that
	 * template to display what we need. If you copied this template from the plugin into your theme
	 * then it would load that one instead. This allows users to override templates in their theme.
	 */

	/* This is going to look in wp-content/plugins/[plugin-name]/includes/templates/ first */
	//bp_core_load_template( apply_filters( 'bp_custom_pages_template_screen', 'bp-custom-pages/custom-page' ) );

	/****
	 * OPTION 2 (NOT USED FOR THIS SCREEN):
	 * If your component is simple, and you just want to insert some HTML into the user's active theme
	 * then you can use the bundle plugin template.
	 *
	 * There are two actions you need to hook into. One for the title, and one for the content.
	 * The functions you hook these into should simply output the content you want to display on the
	 * page.
	 *
	 * The follow lines are commented out because we are not using this method for this screen.
	 * You'd want to remove the OPTION 1 parts above and uncomment these lines if you want to use
	 * this option instead.
	 *
	 * Generally, this method of adding content is preferred, as it makes your plugin
	 * work better with a wider variety of themes.
	 */

	//add_action( 'bp_template_title', 'bp_custom_pages_user_screen_one_title' );
	add_action( 'bp_template_content', 'bp_custom_pages_user_screen_one_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}
	/***
	 * The second argument of each of the above add_action() calls is a function that will
	 * display the corresponding information. The functions are presented below:
	 */
function bp_custom_pages_user_screen_one_title() {
	esc_attr_e( 'Custom Pages', 'bp-custom-pages' );
}

function bp_custom_pages_user_screen_one_content() {
	$user_id = bp_displayed_user_id();
	$user = get_userdata( $user_id );

	// WP_Query arguments
	$args = array(
		'post_type'              => array( 'bp-custom-pages' )
	);


	//query_posts( $args );
	$q = new WP_Query( $args );

	if ($q->have_posts() ) : ?>
	<?php do_action( 'bpcp_before_custom_posts_content' ) ?>
	<div class="pagination no-ajax">
		<div id="posts-count" class="pag-count">
			<?php bpcp_posts_pagination_count( $q ) ?>
		</div>

		<div id="posts-pagination" class="pagination-links">
			<?php bpcp_pagination( $q ) ?>
		</div>
	</div>

	<?php do_action( 'bpcp_before_custom_post_list' ) ?>
	<?php
	global $post;
	bpcp_loop_start();
	?><form><?php
	while( $q->have_posts() ):$q->the_post();
	?>
	<div class="post" id="post-<?php the_ID(); ?>">
		<div class="post-content">
			
			<h3 class="posttitle"><a href="<?php echo esc_url(trailingslashit( home_url() . '/' . bp_get_members_slug() . '/' . $user->user_login . '/' . BP_CUSTOM_PAGES_SLUG . '/' . get_post_field( 'post_name', get_post() ))); ?>" rel="bookmark" title="<?php esc_attr_e( 'Link to', 'bp-custom-pages' ) ?> <?php the_title_attribute(); ?>"><?php esc_textarea(the_title()); ?></a></h3>

		</div>
	</div>

	<?php endwhile;

	?>

	</form>

	<?php 
		do_action( 'bpcp_after_custom_post_content' ) ;
		bpcp_loop_end();
	?>

	<div class="pagination no-ajax">
		<div id="posts-count" class="pag-count">
			<?php bpcp_posts_pagination_count( $q ) ?>
		</div>

		<div id="posts-pagination" class="pagination-links">
			<?php bpcp_pagination( $q ) ?>
		</div>
	</div>

	<?php else: ?>

		<div id="message" class="info">
			<p><?php esc_attr_e( 'No posts found.', 'bp-custom-pages' ); ?></p>
		</div>
	<?php endif;

	wp_reset_postdata();

}

function bp_custom_pages_subpage_screen() {
	add_action( 'bp_template_title', 'bp_custom_pages_user_screen_custom_page_title' );
	add_action( 'bp_template_content', 'bp_custom_pages_user_screen_custom_page_content' );

	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );	
}
function bp_custom_pages_user_screen_custom_page_title() {
	// $requested_URI = $_SERVER['REQUEST_URI'];
	// $page_slugs = bp_custom_pages_get_pagenames();
	// foreach ( $page_slugs as $inner ) {
		// foreach ( $inner as $index => $page_slug ) {
			// if ( strpos($requested_URI, $page_slug ) ) {
				// global $wpdb;
				// $page_title = $wpdb->get_var( $wpdb->prepare( "SELECT post_title FROM $wpdb->posts WHERE post_name = %s AND post_type= 'bp_custom_pages'", $page_slug,));
				// echo $page_title;
			// }
		// }
	// }

}

function bp_custom_pages_user_screen_custom_page_content() {
	global $bp;
	$requested_URI = $_SERVER['REQUEST_URI'];
	$page_slugs = bp_custom_pages_get_pagenames();
	foreach ( $page_slugs as $inner ) {
		foreach ( $inner as $index => $page_slug ) {
			if ( $page_slug && strpos($requested_URI, $page_slug ) ) {
				global $wpdb;
				$page_ID = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type= 'bp-custom-pages'", $page_slug));
				$query = new WP_Query( array( 'p' => $page_ID, 'post_type' => 'bp-custom-pages' ) );
				$post_object = get_post( $page_ID );
				if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post();
					echo '<div class="entry">';
					the_content();
					echo '</div>';
					return;
					endwhile;
					wp_reset_postdata();
				else:
					echo '<p>Nothing found</p>';
					return;
				endif;
			}
		}
	}
	echo '<h3>' . esc_attr__( 'No Pages found', 'bp-custom-pages' ) . '<h3>';
}

