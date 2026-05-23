<?php
/*
 * Template Tags for Post Status Group Posts
 *
 * @package bp-post-status
 *
 */

//if inside the post loop
function in_bpps_loop() {
	
	$bp = buddypress();
	
	return isset( $bp->bpps ) ? $bp->bpps->in_the_loop : false;
}

//use it to mark t5he start of bcg post loop
function bpps_loop_start() {
	$bp = buddypress();

	$bp->bpps = new stdClass();
	$bp->bpps->in_the_loop = true;
}

//use it to mark the end of bcg loop
function bpps_loop_end() {
	$bp = buddypress();

	$bp->bpps->in_the_loop = false;
}

//get post permalink which leads to group blog single post page
function bpps_get_post_permalink( $post, $group_id = NULL ) {
	
	if ( ! bp_is_active( 'groups' ) ) {
		return get_permalink( $post );
	}
	
	If ( ! isset( $post ) ) {
		
		return '$post variable not set';
		
	}
	
	$type = 'group-post';
	
	if ( ! isset( $group_id ) ) {
		
		if ( $post->post_status == 'group_post' ) {

			$group_id = get_post_meta( $post->ID, 'bpgps_group' );
			$group_id = $group_id[0];
	
		}
		
		if ( !isset( $group_id ) ) {
			
			$type = 'post';
			
		} else {
			
			$group = groups_get_group( $group_id );
			
		}
		
	} else {
		
		$group = groups_get_group( $group_id );
		
	}
	
	if ( $type == 'group-post' ) {
		
		if ( ! isset( $group ) ) {
		
		return '$group not found';
		
		} else {
			
			return bp_get_group_url($group ) . BPPS_GROUP_NAV_SLUG . '/' . $post->post_name;
			
		}
		
	} else if ( $type == 'post' ) {
		
		return get_permalink( $post );
		
	}
		
	

}

//get group post read more link
function bpps_post_read_more( $post, $group_id = NULL ) {

	If ( ! isset( $post ) ) {
		
		return '$post variable not set';
		
	}
	
	if ( ! isset( $group_id ) ) {
		
		$group = groups_get_current_group();
		if ( isset( $group->id ) ) {
			$link = bpps_get_post_permalink( $post, $group->id );
		} else {
			$link = bpps_get_post_permalink( $post );
		}
	} else {
		$link = bpps_get_post_permalink( $post, $group_id );
	}
	
	$link_text = esc_attr( esc_attr__( 'Read More', 'bp-post-status' ) );
	
	
	
	$output = ' <a class="read-more-link" href="' . $link . '">' . $link_text . '</a>';

	
	return $output;

}

/**
 * Generate Pagination Link for posts
 * @param type $q 
 */
function bpps_pagination( $q ) {

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
function bpps_posts_pagination_count( $q ) {

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
	$post_type_object = get_post_type_object( bpps_get_post_type() );

	printf( 'Viewing %1$s %2$s to %3$s (of %4$s )' , $post_type_object->labels->name, esc_attr($from_num), esc_attr($to_num), esc_attr($total )) . "&nbsp;";
	?>
	<span class="ajax-loader"></span><?php
}


//sub menu
function bpps_get_options_menu() {
	?>
	<li <?php if ( bpps_is_home() ): ?> class="current"<?php endif; ?>><a href="<?php echo esc_html(bpps_get_home_url()); ?>"><?php esc_attr_e( "Group Posts", "bp-post-status" ); ?></a></li>
	<?php
}

//home menu
function bpps_get_home_menu() {
	?>
	<li <?php if ( bpps_is_home() ): ?> class="current"<?php endif; ?>><a href="<?php echo esc_html(bpps_get_home_url()); ?>"><?php esc_attr_e( "Home", "bp-post-status" ); ?></a></li>
	<?php
}

// display set as homepage option
// since 1.2.0
function bpps_set_as_home_page($group_id = '' ) {
	
	global $post;
	global $bp;
	
	if ( $group_id == '' ) {
		$group_id = $bp->groups->current_group->id;
	}

	$home_disabled = groups_get_groupmeta( $group_id, 'bpps_home_disabled' );
	
	if ( ! bp_group_is_admin() || $home_disabled ) {
		
		return;
		
	}
	
	?>
	<input type="radio" name="home_select" class="home-select" id="select-home-<?php echo esc_attr($post->ID); ?>" value="<?php echo esc_attr($post->ID); ?>" <?php if ( is_group_home_page($group_id) ) : ?>checked="checked"<?php endif; ?>> <label for="home_select" id="home-select-label-<?php echo esc_attr($post->ID); ?>"><?php if ( is_group_home_page($group_id) ) : echo esc_attr__( 'Group homepage', 'bp-post-status' ); else : echo esc_attr__( 'Set as group homepage', 'bp-post-status' ); endif; ?></label>
	<?php
	
}
// Group Posts make sticky option
// @since 1.7.1
function bpps_make_post_sticky( $context = 'group-post' ) {
	
	global $post, $bp;
	
	if ( $context == 'group-post' ) {
		$group_id = $bp->groups->current_group->id;
		if ( $post->post_status != 'group_post' || ! bp_group_is_admin() ) {
			return;
		}
		$is_sticky = get_post_meta( $post->ID, 'bpps_group_post_sticky', true );
	} else if ( $context == 'my-posts' ) {
		if ( $post->post_author != get_current_user_id() && ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$is_sticky = get_post_meta( $post->ID, 'bpps_my_posts_sticky', true );
	}

	
	
	if ( isset( $is_sticky ) && $is_sticky == 1 ) {
		$sticky_message = esc_attr__( 'Unstick', 'bp-post-status' );
	} else {
		$sticky_message = esc_attr__( 'Make Sticky', 'bp-post-status' );
	}
	
	?>
	<input type="radio" name="sticky_select" data-context="<?php echo esc_attr($context); ?>" class="sticky-select" id="select-sticky-<?php echo esc_attr($post->ID); ?>" value="<?php echo esc_attr($post->ID); ?>" <?php if ( isset($is_sticky) && $is_sticky == 1 ) : ?>checked="checked"<?php endif; ?>> <label for="sticky_select" id="sticky-select-label-<?php echo esc_attr($post->ID); ?>"><?php echo esc_attr($sticky_message); ?></label>
	<?php
	
}

// Check if current page is home
// since 1.2.0
function is_group_home_page( $group_id = false ) {
	
	global $post;
	global $bp;
	
	if ( ! $group_id ) {
		
		$group_id = $bp->groups->current_group->id;
		
	}
	
	$home_page_ID = groups_get_groupmeta( $group_id, 'bpps_home_post_id' );
	if ( $home_page_ID == $post->ID ) {
		
		return true;
		
	} else {
		
		return false;
		
	}
	
}

//form for showing category lists
function bpps_admin_form() {
	
	global $bp;
	
	$group_id = $bp->groups->current_group->id;

	$group_post_creator = BPPS_Group_Admin::creator_type_is_selected();
	$group_post_notifier = BPPS_Group_Admin::notifier_type_is_selected();
	$group_activity_content = BPPS_Group_Admin::activity_content_is_selected();
	$group_post_content = BPPS_Group_Admin::post_content_is_selected();
	$approver_email = BPPS_Group_Admin::group_approver_is_selected();
	$roles = array ( 
		'member'	=> esc_attr( esc_attr__( 'Member', 'bp-post-status' ) ),
		'moderator'	=> esc_attr( esc_attr__( 'Moderator', 'bp-post-status' ) ),
		'administrator'	=> esc_attr( esc_attr__( 'Administrator', 'bp-post-status' ) )
		); 
	
	$activity_options = array(
		'content' 	=> 'Full Content',
		'summary' 	=> 'Content Summary',
		'excerpt' 	=> 'Excerpt',
		'none' 		=> 'None'
	);

	
	?>
	
	<div class="bpps-creator-settings">
		
		<label for="creator-role" class="hidden_if_js">
		
		<select name="creator_role" id="creator-role-bpps">
		<?php 

		foreach ( $roles as $creator_role => $creator_label ) {

			?><option name="creator_role" value="<?php echo esc_attr($creator_role);?>" <?php 
			 
			 if (isset( $group_post_creator ) ) : echo esc_attr( $group_post_creator ) == $creator_role ? 'selected="selected"' : ''; endif; ?>><?php echo esc_attr($creator_label); ?></option>
		
		<?php } ?>
		
		</select><?php echo esc_attr__( 'Which member level can create Group Posts', 'bp-post-status' ); ?>
		</label>
	</div>

	<div class="bpps-nav-disable">
		
		<label><input type="checkbox" name="group-disable-nav" id="group-disable-nav" value="1" <?php if ( BPPS_Group_Admin::bpps_nav_is_disabled_for_group() ): ?> checked="checked"<?php endif; ?>> <?php esc_attr_e( 'Disable Group Posts tab', 'bp-post-status' ) ?></label>
	
	</div>
	
	<div class="bpps-notif-enable">
		
		<label><input type="checkbox" name="group-notif-bpps" id="group-notif-bpps" value="1" <?php if ( BPPS_Group_Admin::bpps_notif_group_lookup() ): ?> checked="checked"<?php endif; ?>>
		
		<?php esc_attr_e( 'Enable Group Post Notifications', 'bp-post-status' ) ?></label>
	
	</div>
	
	<div class="bpps-notifier-settings">
		
		<label for="notifier-role" class="hidden_if_js">
		
		<select name="notifier_role" id="creator-role-bpps">
		
		<?php 

		foreach ( $roles as $notifier_role => $notifier_label ) {

			?><option name="notifier_role" value="<?php echo esc_attr($notifier_role);?>" <?php 
			 
			 if (isset( $group_post_notifier ) ) : echo esc_attr( $group_post_notifier ) == $notifier_role ? 'selected="selected"' : ''; endif; ?>><?php echo esc_attr($notifier_label); ?>
			 
			 </option>
		
		<?php } ?>
		
		</select><?php echo esc_attr__( 'Which member level can create Group Only post notifications', 'bp-post-status' ) ; ?>
		</label>
	</div>
	<div class="bpps-home-disable">
		
		<label><input type="checkbox" name="group-disable-home" id="group-disable-home" value="1" <?php if ( BPPS_Group_Admin::bpps_home_is_disabled_for_group() ): ?> checked="checked"<?php endif; ?>> <?php esc_attr_e( 'Disable group Home tab - if enabled, a group post will need to be assigned as the home page.', 'bp-post-status' ) ?></label>
	
	</div>		
	<div class="bpps-activity-settings">
		
		<label for="activity-content">

		<select name="activity_content" id="activity-content-bpps">
		
		<?php 

		foreach ( $activity_options as $option => $label ) {

			?><option name="activity_content" value="<?php echo esc_attr($option);?>" <?php 
			 
			 if (isset( $group_activity_content ) ) : echo esc_attr( $group_activity_content ) == $option ? 'selected="selected"' : ''; endif; ?>><?php echo esc_attr($label); ?>
			 
			 </option>
		
		<?php } ?>
		
		</select><?php echo esc_attr__( 'What content is shown in the group activity update for group posts', 'bp-post-status' ); ?>
		</label>
	</div>
	<div class="bpps-post-settings">
		
		<label for="post-content">

		<select name="post_content" id="activity-content-bpps">
		
		<?php 

		foreach ( $activity_options as $option => $label ) {

			?><option name="post_content" value="<?php echo esc_attr($option);?>" <?php 
			 
			 if (isset( $group_post_content ) ) : echo esc_attr( $group_post_content ) == $option ? 'selected="selected"' : ''; endif; ?>><?php echo esc_attr($label); ?>
			 
			 </option>
		
		<?php } ?>
		
		</select><?php echo esc_attr__( 'What content is shown for each post in the Group Posts tab?', 'bp-post-status' ); ?>
		</label>
	</div>
	<div class="bpps-approver-email">
		
		<label><input type="text" name="group_approver_email" id="group-approver-email" value="<?php echo esc_attr($approver_email); ?>" placeholder="<?php esc_attr_e('Enter the approver email here', 'bp-post-status' ) ?>"><?php esc_attr_e( 'Enter the email address for the person who will approve Group posts.', 'bp-post-status' ) ?></label>
	
	</div>		
	<?php
}

function _bpps_list_tax_terms( $tax, $terms, $selected_terms ) {


	echo "<div class='bpps-editable-terms-list clearfix'>";
	echo esc_html("<label class='bpps-taxonomy-name'>{$tax->labels->singular_name}</label>");
	foreach ( $terms as $term ) {//show the form
		//the back compat is killing the quality
		if ( $tax->name != $term->taxonomy ) {
			continue;
		}

		$checked = 0;

		if ( ! empty( $selected_terms ) && in_array( $term->term_id, $selected_terms ) ) {
			$checked = true;
		}
		?>
		<label  style="padding:5px;display:block;float:left;">
			<input type="checkbox" name="blog_cats[]"  value="<?php echo esc_attr($term->term_id); ?>" <?php if ( $checked ) echo "checked='checked'"; ?> />
			<?php echo esc_attr($term->name); ?>
		</label>

		<?php
	}
	echo "<div style='clear:both;'></div>";
	echo "</div>";
}
//post form if one quick pot is installed

function bpps_show_post_form( $group_id ) {
	
	$bp = buddypress();

	$cat_selected = bpps_get_categories( $group_id ); //selected cats
	
	if ( empty( $cat_selected ) ) {
		esc_attr_e( 'This group has no associated categories. To post to Group blog, you need to associate some categoris to it.', 'bp-post-status' );
		return;
	}

	$all_cats = (array) bpps_get_all_terms();
	$all_cats = wp_list_pluck( $all_cats, 'term_id' );
	
	$cats = array_diff( $all_cats, $cat_selected );

	//for form
	$url = bp_get_group_url(new BP_Groups_Group( $group_id ) ) . BPPS_GROUP_NAV_SLUG . "/create/";
	
	if ( function_exists( 'bp_get_simple_blog_post_form' ) ) {

		$form = esc_attr(bp_get_simple_blog_post_form( 'bpps_form' ));
		
		if ( $form ) {
			$form->show();
		}
	}

	do_action( 'bpps_post_form', $cats, $url ); //pass the categories as array and the url of the current page
}

/**
 * Load correct Template file - allows overloading of the templates in bp-post-status/templates.
 *
 * @since 1.1.0
 *
 */

function bpps_load_template( $template ) {
	
    if ( file_exists( STYLESHEETPATH . '/bpps/' . $template ) ) {
   		include STYLESHEETPATH . '/bpps/' . $template ;
	} elseif ( file_exists( TEMPLATEPATH . '/bpps/' . $template ) ) {
		include TEMPLATEPATH . '/bpps/' . $template ;
	} else {
        include BPPS_PLUGIN_DIR . 'templates/' . $template;
	}	
}

/**
 * Load choose the correct content for posts.php and for activity feeds.
 *
 * @since 1.2.2
 *
 * @param $post
 * @param $context - activity content
 *
 * @return string - Content
 *
 */

function bpps_get_content( $post, $context = '' ) {

    if ( $context == 'activity-groups' ) {

		$settings = get_option( "bpps_groups_settings" );
		$group_id = get_post_meta( $post->ID, 'bpgps_group', true );
		$group_setting = groups_get_groupmeta( $group_id, 'bpps_activity_content' );
		
		if ( isset( $group_setting ) ) {
			$option = $group_setting;
		} elseif ( isset($settings['groups_activity_content'] ) ) {
			$option = $settings['groups_activity_content'];
		} else {
			$option = 'excerpt';
		}
		
		if ( isset( $option ) ) {
		
			if ( $option == 'content' ) {
				
				$content = '<p>' . $post->post_content . '</p>';
			
			} else if ( $option == 'summary' ) {
				
				$content = bpps_the_summary( $post );
			
			} else if ( $option == 'excerpt' ) {
				
				setup_postdata( $post );
				$content = bpps_the_excerpt( $post );
			
			} else if ( $option == 'none' ) {
				
				$content = '';
			
			}

		}
	} elseif ( $context == 'activity-friends' ) {
		
		$settings = get_option( "bpps_friends_settings" );
		if ( isset($settings['friends_activity_content'] ) ) {
			$option = $settings['friends_activity_content'];
		} else {
			$option = 'excerpt';
		}
		if ( isset( $option ) ) {
		
			if ( $option == 'content' ) {
				
				$content = '<p>' . $post->post_content . '</p>';
			
			} else if ( $option == 'summary' ) {
				
				$content = bpps_the_summary( $post->ID );
			
			} else if ( $option == 'excerpt' ) {
				
				setup_postdata( $post );
				$content = bpps_the_excerpt( $post );
			
			} else if ( $option == 'none' ) {
				
				$content = '';
			
			}

		}
		
	} elseif ( $context == 'activity-members' ) {
		
		$settings = get_option( "bpps_members_settings" );
		if ( isset($settings['members_activity_content'] ) ) {
			$option = $settings['members_activity_content'];
		} else {
			$option = 'excerpt';
		}
		
		if ( $option == 'content' ) {
			
			$content = '<p>' . $post->post_content . '</p>';
		
		} else if ( $option == 'summary' ) {
			
			$content = bpps_the_summary( $post->ID );
		
		} else if ( $option == 'excerpt' ) {
			
			setup_postdata( $post );
			$content = bpps_the_excerpt( $post );
		
		} else if ( $option == 'none' ) {
			
			$content = '';
		
		}

	} else {
		
		if ( $post->post_status == 'group_post' ) {
			$group_id = get_post_meta( $post->ID, 'bpgps_group', true );
		}
		
		if ( isset( $group_id ) ) {
			
			$option = groups_get_groupmeta( $group_id, 'bpps_post_content' );
			
		}
			
		if ( ! isset( $option ) || ! in_array( $option, array( 'summary', 'excerpt', 'none', 'content' ) ) ) {
				
			$option = 'excerpt';
				
		}

		if ( $option == 'content' ) {
			
			$content = '<p>' . $post->post_content . '</p>';
		
		} else if ( $option == 'summary' ) {
			
			$content = bpps_the_summary( $post->ID );
		
		} else if ( $option == 'excerpt' ) {
			
			setup_postdata( $post );
			$content = bpps_the_excerpt( $post );
		
		} else if ( $option == 'none' ) {
			
			return false;
		
		}
		
		if ( isset( $content ) ) echo $content;
		return;

	}
	
	if( isset( $content ) ) return $content;
	
	return false;

}

/**
 * Create an excerpt of the content.
 *
 *
 * @since 1.6.2
 *
 * @param string $post_id  The post_id.
 * @return string $content
 */

function bpps_the_excerpt( $post_id = '' ) {
	
	if ( ! $post_id ) {
		
		global $post;
	
	} else {
		
		$post = get_post( $post_id );
		
	}
	
	$content = $post->post_content;
	
	$excerpt = $post->post_excerpt;
	
	if ( strlen( $excerpt ) < 1 ) {
	
		$excerpt = bp_create_excerpt( html_entity_decode( $content ), 225, array(
				'html' => false,
				'filter_shortcodes' => true,
				'strip_tags'        => true,
				'remove_links'      => false
			) );
	}
	
	$excerpt = '<p>' . $excerpt . '</p>';
	
	if ( strlen( $content ) > 255 ) {
		$excerpt .= bpps_post_read_more( $post );
	}
	
	return apply_filters( 'bpps_the_excerpt', $excerpt );
	
}

/**
 * Create a rich summary of content item for excerpts.
 *
 * More than just a simple excerpt, the summary could contain oEmbeds and other types of media.
 * Currently, it's only used for blog post items, but it will probably be used for all types of
 * activity in the future.
 *
 * @since 1.2.2
 *
 * @param string $post_id  The post_id of the post.
 *
 * @return string $summary
 */
function bpps_the_summary( $post_id ) {
	$args = array(
		'width' => isset( $GLOBALS['content_width'] ) ? (int) $GLOBALS['content_width'] : 'medium',
	);

	$content = get_post( $post_id );

	/**
	 * Filter the class name of the media extractor when creating an Activity summary.
	 *
	 * Use this filter to change the media extractor used to extract media info for the activity item.
	 *
	 * @since 1.2.2
	 *
	 * @param string $extractor Class name.
	 * @param string $content   The content of the activity item.
	 * @param array  $activity  The data passed to bp_activity_add() or the values from an Activity obj.
	 */
	$extractor = apply_filters( 'bpps_create_summary_extractor_class', 'BP_Media_Extractor', $content );
	$extractor = new $extractor;

	/**
	 * Filter the arguments passed to the media extractor when creating an Activity summary.
	 *
	 * @since 1.2.2
	 *
	 * @param array              $args      Array of bespoke data for the media extractor.
	 * @param string             $content   The content of the activity item.
	 * @param array              $activity  The data passed to bp_activity_add() or the values from an Activity obj.
	 * @param BP_Media_Extractor $extractor The media extractor object.
	 */
	$args = apply_filters( 'bpps_create_summary_extractor_args', $args, $content, $extractor );


	// Extract media information from the $content.
	$media = $extractor->extract( $content, BP_Media_Extractor::ALL, $args );

	// If we converted $content to an object earlier, flip it back to a string.
	if ( is_a( $content, 'WP_Post' ) ) {
		$content = $content->post_content;
	}

	$para_count     = substr_count( strtolower( wpautop( $content ) ), '<p>' );
	$has_audio      = ! empty( $media['has']['audio'] )           && $media['has']['audio'];
	$has_videos     = ! empty( $media['has']['videos'] )          && $media['has']['videos'];
	$has_feat_image = ! empty( $media['has']['featured_images'] ) && $media['has']['featured_images'];
	$has_galleries  = ! empty( $media['has']['galleries'] )       && $media['has']['galleries'];
	$has_images     = ! empty( $media['has']['images'] )          && $media['has']['images'];
	$has_embeds     = false;

	// Embeds must be subtracted from the paragraph count.
	if ( ! empty( $media['has']['embeds'] ) ) {
		$has_embeds = $media['has']['embeds'] > 0;
		$para_count -= count( $media['has']['embeds'] );
	}

	$extracted_media = array();
	$use_media_type  = '';
	$image_source    = '';

	// If it's a short article and there's an embed/audio/video, use it.
	if ( $para_count <= 3 ) {
		if ( $has_embeds ) {
			$use_media_type = 'embeds';
		} elseif ( $has_audio ) {
			$use_media_type = 'audio';
		} elseif ( $has_videos ) {
			$use_media_type = 'videos';
		}
	}

	// If not, or in any other situation, try to use an image.
	if ( ! $use_media_type && $has_images ) {
		$use_media_type = 'images';
		$image_source   = 'html';

		// Featured Image > Galleries > inline <img>.
		if ( $has_feat_image ) {
			$image_source = 'featured_images';

		} elseif ( $has_galleries ) {
			$image_source = 'galleries';
		}
	}

	// Extract an item from the $media results.
	if ( $use_media_type ) {
		if ( $use_media_type === 'images' ) {
			$extracted_media = wp_list_filter( $media[ $use_media_type ], array( 'source' => $image_source ) );
			$extracted_media = array_shift( $extracted_media );
		} else {
			$extracted_media = array_shift( $media[ $use_media_type ] );
		}

		/**
		 * Filter the results of the media extractor when creating a summary.
		 *
		 * @since 1.2.2
		 *
		 * @param array  $extracted_media Extracted media item. See {@link BP_Media_Extractor::extract()} for format.
		 * @param string $content         Content of the activity item.
		 * @param array  $media           All results from the media extraction.
		 *                                See {@link BP_Media_Extractor::extract()} for format.
		 * @param string $use_media_type  The kind of media item that was preferentially extracted.
		 * @param string $image_source    If $use_media_type was "images", the preferential source of the image.
		 *                                Otherwise empty.
		 */
		$extracted_media = apply_filters(
			'bpps_create_summary_extractor_result',
			$extracted_media,
			$content,
			$media,
			$use_media_type,
			$image_source
		);
	}

	// Generate a text excerpt for this activity item (and remove any oEmbeds URLs).
	$summary = bp_create_excerpt( html_entity_decode( $content ), 225, array(
		'html' => false,
		'filter_shortcodes' => true,
		'strip_tags'        => true,
		'remove_links'      => true
	) );

	if ( $use_media_type === 'embeds' ) {
		$summary .= PHP_EOL . PHP_EOL . $extracted_media['url'];
	} elseif ( $use_media_type === 'images' ) {
		$summary .= sprintf( ' <img src="%s">', esc_url( $extracted_media['url'] ) );
	} elseif ( in_array( $use_media_type, array( 'audio', 'videos' ), true ) ) {
		$summary .= PHP_EOL . PHP_EOL . $extracted_media['original'];  // Full shortcode.
	}

	
	/**
	 * Filters the newly-generated summary for the item. prior to any ream more link being added
	 *
	 * @since 1.6.2
	 *
	 * @param string $summary         Activity summary HTML.
	 * @param string $content         Content of the activity item.
	 * @param array  $extracted_media Media item extracted. See {@link BP_Media_Extractor::extract()} for format.
	 */
	apply_filters( 'bpps_summary_before_read_more', $summary, $content, $extracted_media );
	
	if ( strlen( $content ) < 255 ) {
		
		$summary .= bpps_post_read_more( $post );
		
	}


	/**
	 * Filters the summary after any read more link has been added.
	 *
	 * @since 1.2.2
	 *
	 * @param string $summary         Activity summary HTML.
	 * @param string $content         Content of the activity item.
	 * @param array  $extracted_media Media item extracted. See {@link BP_Media_Extractor::extract()} for format.
	 */
	return apply_filters( 'bpps_final_summary', $summary, $content, $extracted_media );
}

/**
 * Check Group permission.
 *
 *
 * @since 1.3.0
 *
 * @param int 	$user_id
 * @param int 	$group_id 
 *                         
 * @return array summary
 */

function bpps_core_group_lookup( $user_id, $group_id ) {


	$group_data = groups_get_groupmeta( $group_id );
							
	if ( isset( $group_data['bpps_creator'] ) ) {
		
		$required_role = $group_data['bpps_creator'][0];
	
	} else {
		
		$required_role = 'member';
	
	}
	
	if ( isset( $group_data['bpps_notifier'] ) ) {
		
		$notifier_role = $group_data['bpps_notifier'][0];
	
	} else {
		
		$notifier_role = 'member';
	
	}
	
	if ( isset( $group_data['bpps_is_disabled'] ) ) {
		
		$group_posts_disabled = $group_data['bpps_is_disabled'][0];
	
	} else {
		
		$group_posts_disabled = 0;
	
	}

	if ( isset( $group_data['bpps_notif_active'] ) ) {
		
		$group_notiv_enabled = $group_data['bpps_notif_active'][0];
	
	} else {
		
		$group_notiv_enabled = 0;
	
	}

	$member = groups_is_user_member( $user_id, $group_id );
	$mod = groups_is_user_mod($user_id, $group_id );
	if ( current_user_can( 'manage_options' ) || groups_is_user_admin( $user_id, $group_id ) ) {
		$admin = 1;
	} else {
		$admin=0;
	}
	
	switch ( $required_role ) {
		
		case 'member':
			if ( $member || $admin || $mod ) {
				$allowed = 1;
			}
			break;
		
		case 'moderator':
			if ( $admin || $mod ) {
				$allowed = 1;
			}
			break;
		
		case 'administrator':
			if ( $admin ) {
				$allowed = 1;
			}
			break;
		
		default :
			$allowed = 0;
			break;
	}
	
	switch ( $notifier_role ) {
		
		case 'member':
			if ( $member || $admin || $mod ) {
				$notif_allowed = 1;
			}
			break;
		
		case 'moderator':
			if ( $admin || $mod ) {
				$notif_allowed = 1;
			}
			break;
		
		case 'administrator':
			if ( $admin ) {
				$notif_allowed = 1;
			}
			break;
		
		default :
			$notif_allowed = 0;
			break;
	
	}
	if (! isset( $allowed ) ) { $allowed = 0; }
	If (! isset( $notif_allowed ) ) { $notif_allowed=0; }
	return array( $group_posts_disabled, $allowed, $group_notiv_enabled, $notif_allowed ) ;
	
}

/**
 * Check core posts permission.
 *
 *
 * @since 1.3.0
 *
 * @param string 	$component Component being queried (groups, friends etc.)
 *                          
 * @return bool 
 */
 
function bpps_core_posts_disabled( $component ) {


	if ( $component == 'friends' ) {
		$settings = get_option( "bpps_friends_settings" );
	} else if ( $component == 'groups' ) {
		$settings = get_option( "bpps_groups_settings" );
	} else if ( $component == 'members' ) {
		$settings = get_option( "bpps_members_settings" );
	}
	
	//General limitations set in Admin
			
	if ( isset( $settings[ $component . '_disable'] ) ) {
		
		return $settings[ $component . '_disable'];
	
	} else {
		
		return false;
	}
	
}

/**
 * Check user can post permission.
 *
 *
 * @since 1.3.0
 *
 * @param string 	$component Component being queried (groups, friends etc.)
 *                          
 * @return bool 
 */	
function bpps_core_user_can_post( $component ) {

	if ( $component == 'friends' ) {
		$settings = get_option( "bpps_friends_settings" );
	} else if ( $component == 'groups' ) {
		$settings = get_option( "bpps_groups_settings" );
	} else if ( $component == 'members' ) {
		$settings = get_option( "bpps_members_settings" );
	}

	if ( isset( $settings[ $component . '_cap'] ) ) {
		
		return current_user_can( $settings[ $component . '_cap'] );
	
	} else {
		
		return current_user_can( 'edit_posts' );
	
	}
	
}

/**
 * Check notify enabled.
 *
 *
 * @since 1.3.0
 *
 * @param string 	$component Component being queried (groups, friends etc.)
 *                          
 * @return bool 
 */		

 function bpps_core_posts_notify_enabled( $component ) {
	
	if ( $component == 'friends' ) {
		$settings = get_option( "bpps_friends_settings" );
	} else if ( $component == 'groups' ) {
		$settings = get_option( "bpps_groups_settings" );
	} else if ( $component == 'members' ) {
		$settings = get_option( "bpps_members_settings" );
	}

	if ( isset( $settings[ $component . '_notif_enable'] ) ) {
		
		return $settings[ $component . '_notif_enable'];
	
	} else {
		
		return false;
	
	}
	
}

/**
 * Check user can notify.
 *
 *
 * @since 1.3.0
 *
 * @param string 	$component Component being queried (groups, friends etc.)
 *                          
 * @return bool 
 */	

function bpps_core_user_can_notify( $component ) {
	
	if ( $component == 'friends' ) {
		$settings = get_option( "bpps_friends_settings" );
	} else if ( $component == 'groups' ) {
		$settings = get_option( "bpps_groups_settings" );
	} else if ( $component == 'members' ) {
		$settings = get_option( "bpps_members_settings" );
	}
	
	if ( isset( $settings[ $component . '_notif_cap'] ) ) {
		
		return current_user_can( $settings[ $component . '_notif_cap'] );
	
	} else {
		
		return current_user_can( 'edit_posts' );
	
	}
	
}

