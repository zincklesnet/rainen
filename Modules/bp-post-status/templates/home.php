
<?php
	$args = bpps_get_home_query();
	if ( isset( $args ) ) {
		
		$q = new WP_Query( bpps_get_home_query() );
		
	}
	
	global $post;
	if ( $q->have_posts() ) : 
?>
	<?php do_action( 'bp_before_group_blog_post_content' ) ?>

	<?php bpps_loop_start(); ?>
	<?php while( $q->have_posts() ):$q->the_post();?>
	<div class="post" id="post-<?php esc_attr__(the_ID()); ?>">
         <?php edit_post_link(sanitize_text_field(esc_attr__( 'Edit', 'bp-post-status' ))); ?>
		 <div class="post-content">
            
            <div class="entry">
                <?php the_content(); ?>
				
                <?php wp_link_pages(array('before' => '<p><strong>' . sanitize_text_field(esc_attr__( 'Pages:', 'bp-post-status')) . '</strong>', 'after' => '</p>', 'next_or_number' => 'number')); ?>
            </div>
        </div>

    </div>
<?php endwhile;?>
<?php do_action( 'bp_after_group_blog_content' ) ; ?>
<?php bpps_loop_end(); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php sanitize_text_field(esc_attr_e( 'Group homepage not set, go to Group Posts and select the required post.', 'bp-post-status' )); ?></p>
	</div>

<?php endif; ?>

