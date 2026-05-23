<?php
/*
	This page is used for the sticky posts page component
	
*/
	
if ( ! empty( $_GET['bpps-search-type'] ) ) {
	$is_search = esc_attr( $_GET['bpps-search-type'] );
}

if ( isset( $is_search ) ) {
	return;
}

$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

if ( bp_is_group() ) {
	$meta_query = 'bpps_group_post_sticky';
	$group_id = bp_get_current_group_id();
	$is_group = true;
} else if ( bp_is_user() ) {
	$meta_query = 'bpps_my_posts_sticky';
	$is_group = false;
}

$user_id = get_current_user_id();

if ( ! $is_group ) {
	$context = 'my-posts';
	if ( ( bp_displayed_user_id() == $user_id ) || current_user_can( 'manage_options' ) ) {
		$author_view = true;
	}
} else {
	$context = 'group-post';
	if ( groups_is_user_admin( $user_id, $group_id ) || current_user_can( 'manage_options' ) ) {
		$group_admin = true;
	}
}

//query_posts( $args );
$q = new WP_Query( bpps_get_sticky_posts_query() );

if ($q->have_posts() ) : ?>
<?php do_action( 'bpps_before_sticky_posts_content' ) ?>
<?php
	global $post;
	bpps_loop_start();
	?><form><?php
	while( $q->have_posts() ):$q->the_post();
 ?>
	<div class="sticky-posts" id="post-<?php esc_attr(the_ID()); ?>">
        <div class="post-content">
	        <?php if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( get_the_ID() ) ):?>

		        <div class="post-featured-image">
			        <?php  esc_html(the_post_thumbnail());?>
		        </div>

	        <?php endif;
			$slug = '';//fix for post_permalink not returning post slug?>
            <h2 class="posttitle"><a href="<?php echo esc_html(bpps_get_post_permalink($post)) . esc_attr($slug); ?>" rel="bookmark" title="<?php sanitize_text_field(esc_attr_e( 'Link to', 'bp-post-status' )) ?> <?php esc_attr(the_title_attribute()); ?>"><?php sanitize_text_field(esc_attr(the_title())); ?></a></h2>

            <p class="category"> <em><?php sanitize_text_field(esc_attr_e( 'in', 'bp-post-status' )) ?> <?php the_category(', ') ?></em></p>

            <div class="entry">
                <?php echo bpps_the_excerpt( $post->ID ); ?>
            </div>
	        
			<?php if ( isset( $author_view ) || isset( $group_admin) ) :?>
			
				<a href="<?php echo esc_html(home_url()); ?>" class="post-delete-link" data-post="<?php echo esc_attr($post->ID); ?>" title="<?php sanitize_text_field(esc_attr_e( 'Delete Post', 'bp-post-status' )); ?>"><?php sanitize_text_field(esc_attr_e( 'Delete Post', 'bp-post-status' )); ?></a>
			
			<?php endif; ?>
    			<p class="postmetadata"><span class="tags"><?php the_tags( sanitize_text_field(esc_attr__( 'Tags: ', 'bp-post-status' )), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( sanitize_text_field(esc_attr__( 'No Comments &#187;', 'bp-post-status' )), sanitize_text_field(esc_attr__( '1 Comment &#187;', 'bp-post-status' )), sanitize_text_field(esc_attr__( '% Comments &#187;', 'bp-post-status' )) ); ?></span></p>
			
			<?php if ( isset( $author_view ) || isset( $group_admin) ) :?>
			
				<?php if ( isset( $group_admin ) ) : ?>
				
					<p class="make-home"><?php bpps_set_as_home_page(); ?></p>
					
				<?php endif; ?>
				
				<p class="make-sticky"><?php bpps_make_post_sticky( $context ); ?></p>
			
				<em class="post-status"><?php sanitize_text_field(esc_attr_e( 'Post Status: ', 'bp-post-status' )); echo esc_attr(post->post_status);?></em>
			
				<em class="post-date"><?php sanitize_text_field(esc_attr_e( 'Post Date: ', 'bp-post-status', )); echo esc_attr(get_the_time(get_option( 'date_format' )));?></em>
				
				<a href="<?php echo esc_url(home_url()); ?>" class="post-delete-link" data-post="<?php echo esc_attr($post->ID); ?>" title="<?php sanitize_text_field(esc_attr_e( 'Delete Post', 'bp-post-status' )); ?>"><?php sanitize_text_field(esc_attr__( 'Delete Post', 'bp-post-status' )); ?></a>
			
				<p id="post-label-<?php the_ID(); ?>" style="display:none;"></p>
			
			<?php endif; ?>
        </div>
    </div>
	<?php endwhile;?>
	</form>
	<?php 
        do_action( 'bpps_after_sticky_posts_content' ) ;
        bpps_loop_end(); ?>

<?php else: ?>

<?php endif;
wp_reset_postdata(); ?>
