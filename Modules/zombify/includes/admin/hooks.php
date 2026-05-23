<?php
/**
 * Zombify Admin Hooks
 */

// Prevent direct script access.
if ( ! defined( 'ABSPATH' ) ) {
    die( 'No direct script access allowed' );
}

/**
 * Load javascript & css for admin
 */
add_action( 'admin_enqueue_scripts', 'zombify_admin_enqueue_scripts' );
function zombify_admin_enqueue_scripts() {

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'jquery-ui-sortable' );

    wp_enqueue_script( 'zombify-admin', zombify()->assets_url . 'js/admin/zombify_admin.js', array( 'wp-color-picker' ), zombify()->get_plugin_data()->version );

    wp_enqueue_script( 'zombify-yoast', zombify()->assets_url . 'js/admin/zombify_yoast.js', array( 'zombify-admin' ), zombify()->get_plugin_data()->version );

    $zombify_content = trim(preg_replace('/\s+/', ' ', strip_tags( zombify_post_shortcode(array()) )));

    wp_localize_script( 'zombify-yoast', 'zf_yoast_plugin', array(
        'description'   => wp_trim_words( $zombify_content ),
        'content'       => $zombify_content,
    ) );

    if( function_exists( 'wp_enqueue_media' )){
        wp_enqueue_media();
    } else {
        wp_enqueue_style('thickbox');
        wp_enqueue_script('media-upload');
        wp_enqueue_script('thickbox');
    }

    wp_add_inline_style( 'wp-color-picker', ".zf-table label { cursor: all-scroll; }" );

}

/**
 * Add custom columns to posts table
 */
add_filter( 'manage_post_posts_columns' , 'bbz_add_posts_columns', 10, 1 );
function bbz_add_posts_columns( $columns ) {
    return array_merge( $columns, array(
        'zombify' => __('Zombify', 'zombify')
    ));
}

/**
 * Render custom columns contents
 */
add_action( 'manage_post_posts_custom_column' , 'bbz_custom_columns', 10, 2 );
function bbz_custom_columns( $column, $post_id ) {
    if ( $column == 'zombify'){

        $zombify_data_type = get_post_meta( $post_id, 'zombify_data_type', true );

        if( $zombify_data_type ) {
            $url = add_query_arg( array( 'action' => 'update', 'post_id' => $post_id ), get_permalink( zf_get_option("zombify_post_create_page") ) );
            $title = sprintf( '%s editor', $zombify_data_type ); // todo change to human readable format

            printf( '<a href="%1$s">%2$s</a>', $url, $title );
        }
    }
}

/**
 * Add post edit button to post row actions
 */
add_filter( 'post_row_actions', 'zombify_post_row_actions',10, 2 );
function zombify_post_row_actions( $actions, $post ) {
    if ( get_post_meta($post->ID, 'zombify_data_type', true) != '' ) {
        $link = add_query_arg( array( 'action' => 'update', 'post_id' => $post->ID ), get_permalink( zf_get_option("zombify_post_create_page") ) );
        $actions['publish'] = sprintf( '<a href="%1$s">%2$s</a>', $link, esc_html__( 'Zombify Edit', 'zombify' ) );
    }
    return $actions;
}

/**
 * Register met boxes
 */
add_action( 'add_meta_boxes', 'zombify_register_meta_boxes', 10, 2);
function zombify_register_meta_boxes( $post_type, $post ) {
	
	$data_type = get_post_meta( $post->ID, 'zombify_data_type', true );
	if( ! $data_type ) {
		return;
	}

	$quiz_class_name = zf_get_quiz_class_name( $data_type );
	if( ! class_exists( $quiz_class_name ) ) {
		return;
	}

	$quiz = new $quiz_class_name();
	if( count( $quiz->pagination_path ) <= 0 ) {
		return;
	}
	
	add_meta_box('zombify_metabox', 'Zombify', 'zombify_render_meta_box', 'post', 'side', 'high');
	
}

/**
 * Render meta box content
 * @param $post
 */
function zombify_render_meta_box( $post ) {
	
	$items_per_page = get_post_meta( $post->ID, 'zombify_items_per_page', true );
	$items_per_page = $items_per_page ? $items_per_page : '';
	
	$html = sprintf( '<label for="zombify_items_per_page">%s</label>', __( 'Items per page', 'zombify' ) );
	$html .= sprintf( '<input type="number" id= "zombify_items_per_page" name="zombify_items_per_page" value="%s" size="25" />', $items_per_page );
	
	echo $html;
}

/**
 * Save meta box data
 */
add_action( 'save_post', 'zombify_save_postdata' );
function zombify_save_postdata( $post_id ) {

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
        return;
    }

    $post_type = ( isset( $_POST['post_type'] ) && $_POST['post_type'] ) ? $_POST['post_type'] : '';
    if( $post_type != 'post' ){
    	return;
    }
    
    if( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    
    if( ! isset( $_POST['zombify_items_per_page'] ) ) {
    	return;
    }
	
	update_post_meta( $post_id, 'zombify_items_per_page', sanitize_text_field( $_POST['zombify_items_per_page'] ) );
}

/**
 * Add custom columns to list_item post type list table
 */
add_filter( 'manage_list_item_posts_columns' , 'bbz_add_posts_columns_list_items', 10, 1 );
function bbz_add_posts_columns_list_items( $columns ) {

    return array_merge( $columns, array(
        'zombify_sub_to' => __('Submitted to', 'zombify'),
        'zombify_featured_img' => __('Featured Image', 'zombify'),
    ));

}

/**
 * Render custom columns contents for list_item post type
 */
add_action( 'manage_list_item_posts_custom_column' , 'bbz_custom_columns_list_items', 10, 2 );
function bbz_custom_columns_list_items( $column, $post_id ) {

    global $post;

    switch ( $column ) {
	    case 'zombify_sub_to':
	    	$content = '-';
	    	if( $post->post_parent > 0 && $parent_post = get_post( $post->post_parent ) ) {
			    $content = sprintf( '<a href="%1$s">%2$s</a>', get_edit_post_link( $post->post_parent ),$parent_post->post_title  );
		    }
		    
		    echo $content;
	    	break;
	    case 'zombify_featured_img':
		    echo get_the_post_thumbnail( $post->ID, array( 100, 100 ) );
	    	break;
    }
}