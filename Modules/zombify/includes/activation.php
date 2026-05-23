<?php
/**
 * Zombify Plugin Activation
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

if( !function_exists("zombify_activation_func") ) {

    /**
     * Zombify activation function
     */
    function zombify_activation_func()
    {

        // Getting current logged in admin ID
        $admin_id = get_current_user_id();

        if( !get_option("zombify_frontend_page") ) {

            // Create frontend page for Zombify
            $frontend_page_args = array(
                'post_author' => $admin_id,
                'post_content' => '',
                'post_content_filtered' => '',
                'post_title' => 'Frontend Page',
                'post_excerpt' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_parent' => 0,
            );
            $frontend_page_id = wp_insert_post($frontend_page_args);

            // Add Zombify frontend page options
            update_option("zombify_frontend_page", $frontend_page_id);
            update_option("zombify_default_frontend_page", $frontend_page_id);

        }

        if( !get_option("zombify_post_create_page") ) {

            // Create post create page for Zombify
            $post_create_page_args = array(
                'post_author' => $admin_id,
                'post_content' => '',
                'post_content_filtered' => '',
                'post_title' => 'Post Create Page',
                'post_excerpt' => '',
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_parent' => 0,
            );
            $post_create_page_id = wp_insert_post($post_create_page_args);

            // Add Zombify post create page options
            update_option("zombify_post_create_page", $post_create_page_id);
            update_option("zombify_default_post_create_page", $post_create_page_id);

        }

        if( !get_option("zombify_max_upload_size") ) {

            update_option("zombify_max_upload_size", zombify()->options_defaults["zombify_max_upload_size"]);

        }

        if( ! zombify()->get_active_formats() ) {

            $active_formats = array();

            foreach( zombify()->get_post_types() as $ptype => $ptype_label ) {
                $active_formats[] = $ptype;
            }

            update_option( "zombify_active_formats", $active_formats );

        }

        if( !get_option("zombify_post_tags") ) {

            update_option("zombify_post_tags", zombify()->get_default_options("zombify_post_tags"));

        }

        if( !get_option("zombify_post_categroies") ) {

            update_option("zombify_post_categroies", array());

        }
    }

    add_action( 'zombify_activation', "zombify_activation_func", 10, 0);

}