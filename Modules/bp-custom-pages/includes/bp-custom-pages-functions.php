<?php

/**
 * The -functions.php file is a good place to store miscellaneous functions needed by your plugin.
 *
 * @package BuddyPress_Skeleton_Component
 * @since 1.6
 */

/**
 * bp_custom_pages_load_template_filter()
 *
 * You can define a custom load template filter for your component. This will allow
 * you to store and load template files from your plugin directory.
 *
 * This will also allow users to override these templates in their active theme and
 * replace the ones that are stored in the plugin directory.
 *
 * If you're not interested in using template files, then you don't need this function.
 *
 * This will become clearer in the function bp_custom_pages_user_screen_one() when you want to load
 * a template file.
 */
function bp_custom_pages_load_template_filter( $found_template, $templates ) {
	global $bp;

	/**
	 * Only filter the template location when we're on the example component pages.
	 */
	if ( $bp->current_component != $bp->bp_custom_pages->slug ) {
		return $found_template;
	}

	/*
	 * $found_template is not empty when the older template files are found in the
	 * parent and child theme.
	 *
	 * /wp-content/themes/YOUR-THEME/members/single/example.php
	 *
	 * The older template files utilize a full template ( get_header() +
	 * get_footer() ), which doesn't work for themes and theme compat.
	 *
	 * When the older template files are not found, we use our new template method,
	 * which will act more like a template part.
	 */
	if ( empty( $found_template ) ) {
		/*
		 * Register our theme compat directory.
		 *
		 * This tells BP to look for templates in our plugin directory last
		 * when the template isn't found in the parent / child theme
		 */
		bp_register_template_stack( 'bp_custom_pages_get_template_directory', 14 );

		/*
		 * locate_template() will attempt to find the plugins.php template in the
		 * child and parent theme and return the located template when found
		 *
		 * plugins.php is the preferred template to use, since all we'd need to do is
		 * inject our content into BP.
		 *
		 * Note: this is only really relevant for bp-default themes as theme compat
		 * will kick in on its own when this template isn't found.
		 */
		$found_template = locate_template( 'members/single/plugins.php', false, false );

		// Add our hook to inject content into BP.
		add_action(
			'bp_template_content',
			function() use ( $templates ) {
				foreach ( $templates as $template ) {
					$template_name = str_replace( '.php', '', $template );

					/*
					 * Only add the template to the content when it's not the generic
					 * plugins.php template  to avoid infinite loop.
					 */
					if ( 'members/single/plugins' !== $template_name ) {
						bp_get_template_part( $template_name );
					}
				}
			}
		);
	}

	return apply_filters( 'bp_custom_pages_load_template_filter', $found_template );
}
add_filter( 'bp_located_template', 'bp_custom_pages_load_template_filter', 10, 2 );


/***
 * From now on you will want to add your own functions that are specific to the component you are developing.
 * For example, in this section in the friends component, there would be functions like:
 *    friends_add_friend()
 *    friends_remove_friend()
 *    friends_check_friendship()
 *
 * Some guidelines:
 *    - Don't set up error messages in these functions, just return false if you hit a problem and
 *  deal with error messages in screen or action functions.
 *
 *    - Don't directly query the database in any of these functions. Use database access classes
 *  or functions in your bp-custom-pages-classes.php file to fetch what you need. Spraying database
 *  access all over your plugin turns into a maintenance nightmare, trust me.
 *
 *    - Try to include add_action() functions within all of these functions. That way others will
 *  find it easy to extend your component without hacking it to pieces.
 */
/**
 * Get the BP Card template directory.
 *
 * @since 1.7
 *
 * @uses apply_filters()
 * @return string
 */
function bp_custom_pages_get_template_directory() {
	return apply_filters( 'bp_custom_pages_get_template_directory', constant( 'BP_CUSTOM_PAGES_PLUGIN_DIR' ) . '/includes/templates' );
}

function bp_custom_pages_get_pagenames() {
	global $wpdb;
	$table = $wpdb->prefix.'posts';
	$sql = "SELECT post_name from " . $table;
	$sql.= " where post_type = 'bp-custom-pages' AND post_status = 'publish' ";
	if (!empty($_REQUEST['orderby'])) {
			$sql.= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
		$sql.= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
	}
	$sql.= " LIMIT 10";
	$result = $wpdb->get_results($sql, 'ARRAY_A');
	return $result;
	
}

/***
 * Object Caching Support ----
 *
 * It's a good idea to implement object caching support in your component if it is fairly database
 * intensive. This is not a requirement, but it will help ensure your component works better under
 * high load environments.
 *
 * In parts of this example component you will see calls to wp_cache_get() often in template tags
 * or custom loops where database access is common. This is where cached data is being fetched instead
 * of querying the database.
 *
 * However, you will need to make sure the cache is cleared and updated when something changes. For example,
 * the groups component caches groups details (such as description, name, news, number of members etc).
 * But when those details are updated by a group admin, we need to clear the group's cache so the new
 * details are shown when users view the group or find it in search results.
 *
 * We know that there is a do_action() call when the group details are updated called 'groups_settings_updated'
 * and the group_id is passed in that action. We need to create a function that will clear the cache for the
 * group, and then add an action that calls that function when the 'groups_settings_updated' is fired.
 *
 * Example:
 *
 *   function groups_clear_group_object_cache( $group_id ) {
 *       wp_cache_delete( 'groups_group_' . $group_id );
 *   }
 *   add_action( 'groups_settings_updated', 'groups_clear_group_object_cache' );
 *
 * The "'groups_group_' . $group_id" part refers to the unique identifier you gave the cached object in the
 * wp_cache_set() call in your code.
 *
 * If this has completely confused you, check the function documentation here:
 * http://codex.wordpress.org/Function_Reference/WP_Cache
 *
 * If you're still confused, check how it works in other BuddyPress components, or just don't use it,
 * but you should try to if you can (it makes a big difference). :)
 */


