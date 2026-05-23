	<?php
	/*
	This page is used for the group post page
	
*/
	
	global $bpps_shortcode_group;
	
	$group_id = $bpps_shortcode_group;
	$q = new WP_Query( bpps_get_posts_query($group_id) );?>
	<?php if ($q->have_posts() ) : ?>
	<?php do_action( 'bp_before_group_blog_content' ) ?>
	<div class="pagination no-ajax">
		<div id="posts-count" class="pag-count">
			<?php bpps_posts_pagination_count( $q ) ?>
		</div>

		<div id="posts-pagination" class="pagination-links">
			<?php bpps_pagination( $q ) ?>
		</div>
	</div>

	<?php do_action( 'bp_before_group_blog_list' ) ?>
<?php
	global $post;
	bpps_loop_start();
	?><form><?php
	while( $q->have_posts() ):$q->the_post();
 ?>
	<div class="post" id="post-<?php esc_attr(the_ID()); ?>">
        <div class="post-content">
	        <?php if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( get_the_ID() ) ):?>

		        <div class="post-featured-image">
			        <?php  esc_html(the_post_thumbnail());?>
		        </div>

	        <?php endif;
			$slug = '';//fix for post_permalink not returning post slug?>
            <h2 class="posttitle"><a href="<?php echo esc_html(bpps_get_post_permalink($post, $group_id)) . esc_attr($slug); ?>" rel="bookmark" title="<?php sanitize_text_field(esc_attr_e( 'Link to', 'bp-post-status' )) ?> <?php esc_attr(the_title_attribute()); ?>"><?php esc_attr(the_title()); ?></a></h2>

            <p class="category"><em><?php sanitize_text_field(esc_attr_e( 'in', 'bp-post-status' )) ?> <?php esc_attr(the_category(', ')) ?> <?php printf( sanitize_text_field(esc_attr__( 'by %s', 'bp-post-status' )), esc_html(bp_core_get_userlink( $post->post_author ))) ?></em></p>

            <div class="entry">
                <?php bpps_get_content( $post, 'post-loop' ); ?>
            </div>
	        <p class="postmetadata"><span class="tags"><?php the_tags( sanitize_text_field(esc_attr__( 'Tags: ', 'bp-post-status' )), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( sanitize_text_field(esc_attr__( 'No Comments &#187;', 'bp-post-status' )), sanitize_text_field(esc_attr__( '1 Comment &#187;', 'bp-post-status' )), sanitize_text_field(esc_attr__( '% Comments &#187;', 'bp-post-status' )) ); ?></span></p>
			<p class="make-home"><?php bpps_set_as_home_page( $group_id); ?></p>
        </div>
    </div>
	<?php endwhile;?>
	</form>
	<?php 
        do_action( 'bp_after_group_blog_content' ) ;
        bpps_loop_end();
	?>
	<div class="pagination no-ajax">
		<div id="posts-count" class="pag-count">
			<?php bpps_posts_pagination_count( $q ) ?>
		</div>

		<div id="posts-pagination" class="pagination-links">
			<?php bpps_pagination( $q ) ?>
		</div>
	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php sanitize_text_field(esc_attr_e( 'This group has no group posts.', 'bp-post-status' )); ?></p>
	</div>
<?php endif; ?>
