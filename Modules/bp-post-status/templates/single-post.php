
<?php 
	$q = new WP_Query( bpps_get_group_posts_query() );
	global $post;
	if ( $q->have_posts() ) : 
?>
	<?php do_action( 'bp_before_group_blog_post_content' ) ?>

	<?php bpps_loop_start(); ?>
	<?php while( $q->have_posts() ):$q->the_post();?>
	<div class="post" id="post-<?php esc_attr(the_ID()); ?>">
		<?php edit_post_link(esc_attr__( 'Edit', 'bp-post-status' )); ?>
        <div class="post-content">
	        <?php if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( get_the_ID() ) ):?>

		        <div class="post-featured-image">
			        <?php  esc_attr(the_post_thumbnail());?>
		        </div>

	        <?php endif;?>

            <h2 class="posttitle"><a href="<?php echo esc_html(bpps_get_post_permalink($post));?>" rel="bookmark" title="<?php sanitize_text_field(esc_attr_e( 'Link to', 'bp-post-status' )) ?> <?php esc_attr(the_title_attribute()); ?>"><?php esc_attr(the_title()); ?></a></h2>
            <p class="category"> <em><?php sanitize_text_field(esc_attr_e( 'in', 'bp-post-status' )) ?> <?php esc_attr(the_category(', ')) ?> <?php printf( sanitize_text_field(esc_attr__( 'by %s', 'bp-post-status' )), esc_html(bp_core_get_userlink( $post->post_author ))) ?></em></p>
            <div class="entry">
                <?php the_content(); ?>

                <?php wp_link_pages(array('before' => '<p><strong>'. sanitize_text_field(esc_attr__('Pages: ', 'bp-post-status' )) . '</strong>', 'after' => '</p>', 'next_or_number' => 'number')); ?>
            </div>

            <p class="postmetadata"><span class="tags"><?php the_tags( sanitize_text_field(esc_attr__( 'Tags: ', 'bp-post-status' )), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( sanitize_text_field(esc_attr__( 'No Comments &#187;', 'bp-post-status' )), sanitize_text_field(esc_attr__( '1 Comment &#187;', 'bp-post-status' )), sanitize_text_field(esc_attr__( '% Comments &#187;', 'bp-post-status' )) ); ?></span></p>
        </div>

    </div>
    <?php comments_template(); ?>
<?php endwhile;?>
<?php do_action( 'bp_after_group_blog_content' ) ; ?>
<?php bpps_loop_end(); ?>

<?php else: ?>

	<div id="message" class="info">
		<p><?php sanitize_text_field(esc_attr_e( 'Post not found.', 'bp-post-status' )); ?></p>
	</div>

<?php endif; ?>
