<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function pp_events_load_content( $content ) {
    global $post, $wp_query;
	
	if ( 'event' == get_post_type() && is_single() && is_main_query() && in_the_loop() ) {

		$theme_template = locate_template( 'events-single.php' );

		if ( file_exists( $theme_template ) )
			$template = $theme_template;
		else
			$template = PP_EVENTS_DIR . '/templates/events-single.php';

		ob_start();

			// since we aren't using template parts, don't bother with get_template_part( $template );
		require_once $template;
		
		remove_filter( 'the_content', 'pp_event_load_content' );

        return ob_get_clean();

    } elseif ( is_main_query() && ! is_buddypress() && ! bp_is_user()  && in_the_loop() ) {

		if ( isset( $wp_query->query['pagename'] ) ) {

			if ( $wp_query->query['pagename'] == 'events' ) {

				$theme_template = locate_template( 'events-loop.php' );

				if ( file_exists( $theme_template ) )
				   $template = $theme_template;
				else
				   $template = PP_EVENTS_DIR . '/templates/events-loop.php';

				ob_start();

				require_once $template;
				
				remove_filter( 'the_content', 'pp_event_load_content' );

				return ob_get_clean();

			}

			else {

				return $content;

			}
		}

	}

	return $content;

}
add_filter( 'the_content', 'pp_events_load_content', 189 );

// profile templates
function pp_events_register_template_location() {
    return PP_EVENTS_DIR . '/templates/';
}

function pp_events_template_start() {

    if ( function_exists( 'bp_register_template_stack' ) )
        bp_register_template_stack( 'pp_events_register_template_location' );

}
add_action( 'bp_init', 'pp_events_template_start' );

