<?php

if(!defined('ABSPATH')) {
	exit;
}

// @package bp-post-status

// Since 1.2.8

function bpps_custom_email_messages() {
 
    // Do not create if it already exists and is not in the trash
    $post_exists = post_exists( '[{{{site.name}}}] New post comment.' );
 
    if ( $post_exists != 0 && get_post_status( $post_exists ) == 'publish' )
       return;
  
    // Create post object
    $my_post = array(
      'post_title'    => esc_attr__( '[{{{site.name}}}] New post comment.', 'bp-post-status' ),
      'post_content'  => esc_attr__( '{{commenter.name}} commented on your blog post.', 'bp-post-status' ),  // HTML email content.
      'post_excerpt'  => esc_attr__( '{{commenter.name}} commented on your blog post.', 'bp-post-status' ),  // Plain text email content.
      'post_status'   => 'publish',
      'post_type' => bp_get_email_post_type() // this is the post type for emails
    );
 
    // Insert the email post into the database
    $post_id = wp_insert_post( $my_post );
 
    if ( $post_id ) {
    // add our email to the taxonomy term 'post_received_comment'
        // Email is a custom post type, therefore use wp_set_object_terms
 
        $tt_ids = wp_set_object_terms( $post_id, 'post_received_comment', bp_get_email_tax_type() );
        foreach ( $tt_ids as $tt_id ) {
            $term = get_term_by( 'term_taxonomy_id', (int) $tt_id, bp_get_email_tax_type() );
            wp_update_term( (int) $term->term_id, bp_get_email_tax_type(), array(
                'description' => esc_attr( esc_attr__( 'A member comments on a post', 'bp-post-status' ) ),
            ) );
        }
    }

	// Approval request email notification
    // Do not create if it already exists and is not in the trash
    $post_exists = post_exists( '[{{{site.name}}}] New post for approval.' );
 
    if ( $post_exists != 0 && ( get_post_status( $post_exists ) == 'pending' || get_post_status( $post_exists ) == 'group_only_pending' || get_post_status( $post_exists ) == 'members_only_pending' ) )
       return;
  
    // Create post object
    $my_post = array(
      'post_title'    => esc_attr__( '[{{{site.name}}}] New post for approval.', 'bp-post-status' ),
      'post_content'  => esc_attr__( '{{commenter.name}} added a new post for approval.', 'bp-post-status' ),  // HTML email content.
      'post_excerpt'  => esc_attr__( '{{commenter.name}} added a new post for approval.', 'bp-post-status' ),  // Plain text email content.
      'post_status'   => 'publish',
      'post_type' => bp_get_email_post_type() // this is the post type for emails
    );
 
    // Insert the email post into the database
    $post_id = wp_insert_post( $my_post );
 
    if ( $post_id ) {
    // add our email to the taxonomy term 'post_received_comment'
        // Email is a custom post type, therefore use wp_set_object_terms
 
        $tt_ids = wp_set_object_terms( $post_id, 'post_received_for_approval', bp_get_email_tax_type() );
        foreach ( $tt_ids as $tt_id ) {
            $term = get_term_by( 'term_taxonomy_id', (int) $tt_id, bp_get_email_tax_type() );
            wp_update_term( (int) $term->term_id, bp_get_email_tax_type(), array(
                'description' => esc_attr( esc_attr__( 'A member post needs approval', 'bp-post-status' ) ),
            ) );
        }
    }
 
}
add_action( 'bp_core_install_emails', 'bpps_custom_email_messages' );

function bpps_comment_inserted( $comment_id, $comment_object ) {
 
	$settings = get_option( "bpps_general_settings" );
	
	If ( isset( $settings['comment_email'] ) ) {
		
		$comment_reply_emails = $settings['comment_email'];
	
	} else {
		
		$comment_reply_emails = 0;
	
	}    
	
	if ( $comment_reply_emails == 1 ) {
		if ( $comment_object ) {
			// get the post data
			$post = get_post( $comment_object->comment_post_ID );
			// add tokens to parse in email
			$args = array(
				'tokens' => array(
					'site.name' => get_bloginfo( 'name' ),
					'commenter.name' => $comment_object->comment_author,
				),
			);
			// send args and user ID to receive email
			bp_send_email( 'post_received_comment', (int) $post->post_author, $args );
		}
	}
}
add_action( 'wp_insert_comment','bpps_comment_inserted', 99, 2 );


// since 1.3.0
// Add pending status notification emails

function bpps_pending_submission_notifications_send_email( $new_status, $old_status, $post ) {

	$settings = get_option( 'bpps_general_settings' );
	// Notify Admin that Contributor has written a post.
	if ( ! isset( $settings['approve_email'] ) ) {
		return;
	}
	if ( $post->post_status == 'pending' ) {
		$component_type = 'approval';
		$notifications = BPPS_Notifications::bpps_add_notification( $post->ID, $component_type );
	}	
	if ( 'pending' === $new_status && user_can( $post->post_author, 'edit_posts' ) && ! user_can( $post->post_author, 'publish_posts' ) ) {
		if ( isset( $settings['admin_notification_emails'] ) ) {
			$site_admin_email = $settings['admin_notification_emails'];
		} else {
			$site_admin_email = get_option( 'admin_email' );
		}
		$admins                   = $site_admin_email;
		$edit_link                = get_edit_post_link( $post->ID, '' );
		$preview_link             = get_permalink( $post->ID ) . '&preview=true';
		$username                 = get_userdata( $post->post_author );
		$subject                  = esc_attr__( 'New submission pending review', 'bp-post-status' ) . ': "' . $post->post_title . '"';
		$message                  = esc_attr__( 'A new submission is pending review.', 'bp-post-status' );
		$message                 .= "\r\n\r\n";
		$message                 .= esc_attr__( 'Author', 'bp-post-status' ) . ': ' . $username->user_login . "\r\n";
		$message                 .= esc_attr__( 'Title', 'bp-post-status' ) . ': ' . $post->post_title . "\r\n";
		$message                 .= esc_attr__( 'Last edit date', 'bp-post-status' ) . ': ' . $post->post_modified;
		$message                 .= "\r\n\r\n";
		$message                 .= esc_attr__( 'Edit the submission', 'bp-post-status' ) . ': ' . $edit_link . "\r\n";
		$message                 .= esc_attr__( 'Preview the submission', 'bp-post-status' ) . ': ' . $preview_link;
		$result                   = wp_mail( $admins, $subject, $message );
	} // Notify Contributor that Admin has published their post.
	elseif ( 'pending' === $old_status && 'publish' === $new_status && user_can( $post->post_author, 'edit_posts' ) && ! user_can( $post->post_author, 'publish_posts' ) ) {
		$username = get_userdata( $post->post_author );
		$url      = get_permalink( $post->ID );
		$subject  = esc_attr__( 'Your submission is now live! ', 'bp-post-status' );
		$message  = '"' . $post->post_title . '" ' . esc_attr__( 'was just published ', 'bp-post-status' ) . "! \r\n\r\n";
		$message .= $url;
		$result   = wp_mail( $username->user_email, $subject, $message );
	} else 	if ( 'members_only_pending' === $new_status && user_can( $post->post_author, 'edit_posts' ) && ! user_can( $post->post_author, 'publish_posts' ) ) {
		if ( isset( $settings['admin_notification_emails'] ) ) {
			$site_admin_email = $settings['admin_notification_emails'];
		} else {
			$site_admin_email = get_option( 'admin_email' );
		}
		$admins                   = $site_admin_email;
		$edit_link                = get_edit_post_link( $post->ID, '' );
		$preview_link             = get_permalink( $post->ID ) . '&preview=true';
		$username                 = get_userdata( $post->post_author );
		$subject                  = esc_attr__( 'New submission pending review', 'bp-post-status' ) . ': "' . $post->post_title . '"';
		$message                  = esc_attr__( 'A new submission is pending review.', 'bp-post-status' );
		$message                 .= "\r\n\r\n";
		$message                 .= esc_attr__( 'Author', 'bp-post-status' ) . ': ' . $username->user_login . "\r\n";
		$message                 .= esc_attr__( 'Title', 'bp-post-status' ) . ': ' . $post->post_title . "\r\n";
		$message                 .= esc_attr__( 'Last edit date', 'bp-post-status' ) . ': ' . $post->post_modified;
		$message                 .= "\r\n\r\n";
		$message                 .= esc_attr__( 'Edit the submission', 'bp-post-status' ) . ': ' . $edit_link . "\r\n";
		$message                 .= esc_attr__( 'Preview the submission', 'bp-post-status' ) . ': ' . $preview_link;
		$result                   = wp_mail( $admins, $subject, $message );
	} // Notify Contributor that Admin has published their post.
	elseif ( 'members_only_pending' === $old_status && 'members_only' === $new_status && user_can( $post->post_author, 'edit_posts' ) && ! user_can( $post->post_author, 'publish_posts' ) ) {
		$username = get_userdata( $post->post_author );
		$url      = get_permalink( $post->ID );
		$subject  = esc_attr__( 'Your submission is now live! ', 'bp-post-status' );
		$message  = '"' . $post->post_title . '" ' . esc_attr__( 'was just published ', 'bp-post-status' ) . "! \r\n\r\n";
		$message .= $url;
		$result   = wp_mail( $username->user_email, $subject, $message );
	} else 	if ( 'group_post_pending' === $new_status && user_can( $post->post_author, 'edit_posts' ) && ! user_can( $post->post_author, 'publish_posts' ) ) {
//		$stored_meta = get_post_meta( $post->ID );
		if ( isset( $_POST[ 'post_group' ] ) ) {
			$group_id = $_POST[ 'post_group' ];
		}
		
		$group_data = groups_get_group( $group_id );
		
		$approver_id = $group_data->creator_id;
		$approver_details = get_userdata( $approver_id );
		$group_approver_email = $approver_details->user_email;
		$approver_email = esc_attr( groups_get_groupmeta( $group_id, 'bpps_approver_email' ) );
		
		if ( isset( $approver_email ) ) {
			
			$group_approver_email = $approver_email;
		
		}
		
		if ( isset( $settings['admin_notification_emails'] ) ) {
			
			$site_admin_email = $settings['admin_notification_emails'];
		
		} else {
			
			$site_admin_email = get_option( 'admin_email' );
		
		}
		
		if ( $group_approver_email && class_exists( 'CoAuthors_Plus' ) ) {
			
			$admins = $group_approver_email;
		
		} else {
			
			$admins = $site_admin_email;
		
		}
		
		$edit_link 					= get_edit_post_link( $post->ID, '' );
		$preview_link           	= get_permalink( $post->ID ) . '&preview=true';
		$username                 	= get_userdata( $post->post_author );
		$subject                  	= esc_attr__( 'New submission pending review', 'bp-post-status' ) . ': "' . $post->post_title . '"';
		$message                  	= esc_attr__( 'A new submission is pending review.', 'bp-post-status' );
		$message                 	.= "\r\n\r\n";
		$message                 	.= esc_attr__( 'Author', 'bp-post-status' ) . ': ' . $username->user_login . "\r\n";
		$message                 	.= esc_attr__( 'Title', 'bp-post-status' ) . ': ' . $post->post_title . "\r\n";
		$message                 	.= esc_attr__( 'Last edit date', 'bp-post-status' ) . ': ' . $post->post_modified;
		$message                 	.= "\r\n\r\n";
		$message                 	.= esc_attr__( 'Edit the submission', 'bp-post-status' ) . ': ' . $edit_link . "\r\n";
		$message                 	.= esc_attr__( 'Preview the submission', 'bp-post-status' ) . ': ' . $preview_link;
		$result                  	= wp_mail( $admins, $subject, $message );
	} // Notify Contributor that Admin has published their post.
	elseif ( 'group_post_pending' === $old_status && 'group_post' === $new_status && user_can( $post->post_author, 'edit_posts' ) && ! user_can( $post->post_author, 'publish_posts' ) ) {
		$username = get_userdata( $post->post_author );
		$url      = get_permalink( $post->ID );
		$subject  = esc_attr__( 'Your submission is now live! ', 'bp-post-status' );
		$message  = '"' . $post->post_title . '" ' . esc_attr__( 'was just published ', 'bp-post-status' ) . "! \r\n\r\n";
		$message .= $url;
		$result   = wp_mail( $username->user_email, $subject, $message );
	}
}
add_action( 'transition_post_status', 'bpps_pending_submission_notifications_send_email', 10, 3 );