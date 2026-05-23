<?php
/*
	This page is used for the profile pending posts page
	
*/

if ( is_super_admin() || groups_is_user_admin( get_current_user_id(), $group_id ) ) {
	$admin_view = true;
}

$group_id = bp_get_current_group_id();

$args = array( 
	'post_status' 	=> 'group_post_pending',
	'meta_key' 		=> 'bpgps_group',
	'meta_value'	=> $group_id
);

$slug = '';

//query_posts( $args );
$q = new WP_Query( $args );

if ($q->have_posts() ) : ?>
<?php do_action( 'bpps_before_group_moderation_content' ) ?>
<div class="pagination no-ajax">
	<div id="posts-count" class="pag-count">
		<?php bpps_posts_pagination_count( $q ) ?>
	</div>

	<div id="posts-pagination" class="pagination-links">
		<?php bpps_pagination( $q ) ?>
	</div>
</div>

<?php do_action( 'bpps_before_group_moderation_list' ) ?>
<?php
global $post;
bpps_loop_start();
?><form><?php
while( $q->have_posts() ):$q->the_post();
?>
<div class="post" id="post-<?php the_ID(); ?>">
	<div class="post-content">
		
		<?php if ( function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( get_the_ID() ) ):?>

			<div class="post-featured-image">
				<?php  esc_html(the_post_thumbnail());?>
			</div>

		<?php endif; ?>
		
		<h2 class="posttitle"><a href="<?php esc_html(the_permalink($post )) . $slug; ?>" rel="bookmark" title="<?php esc_attr_e( 'Link to', 'bp-post-status' ) ?> <?php the_title_attribute(); ?>"><?php sanitize_text_field(esc_attr(the_title())); ?></a></h2>

		<p class="category"><?php// the_time() ?> <em><?php sanitize_text_field(esc_attr_e( 'in', 'bp-post-status' )) ?> <?php esc_attr(the_category(', ')) ?></em></p>

		<div class="entry">
			<?php echo the_content( $post->ID ); ?>
		</div>

		<a href="<?php echo esc_html(home_url()); ?>" class="post-delete-link" data-post="<?php echo esc_attr($post->ID); ?>" title="<?php echo sanitize_text_field(esc_attr__( 'Delete Post', 'bp-post-status' )); ?>">	<?php echo sanitize_text_field(esc_attr__( 'Delete Post', 'bp-post-status' )); ?></a>

		<a href="<?php echo esc_html(home_url()); ?>" class="post-publish-link" data-post="<?php echo esc_attr($post->ID); ?>" title="<?php echo sanitize_text_field(esc_attr__( 'Publish Post', 'bp-post-status' )); ?>">	<?php echo sanitize_text_field(esc_attr__( 'Publish Post', 'bp-post-status' )); ?></a>
		
		<p class="postmetadata"><span class="tags"><?php the_tags( sanitize_text_field(esc_attr__( 'Tags: ', 'bp-post-status' )), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( sanitize_text_field(esc_attr__( 'No Comments &#187;', 'bp-post-status' )), sanitize_text_field(esc_attr__( '1 Comment &#187;', 'bp-post-status' )), sanitize_text_field(esc_attr__( '% Comments &#187;', 'bp-post-status' )) ); ?></span></p>
		
		<em class="post-status"><?php echo sanitize_text_field(esc_attr__( 'Post Status: ', 'bp-post-status' )); echo esc_attr($post->post_status);?></em>
		
		<em class="post-date"><?php echo sanitize_text_field(esc_attr__( 'Post Date: ', 'bp-post-status' )); echo esc_attr(get_the_time(get_option( 'date_format' )));?></em>
		
		<p id="post-label-<?php esc_attr(the_ID()); ?>" style="display:none;"></p>

	</div>
</div>

<?php endwhile;?>

</form>

<?php 
	do_action( 'bpps_after_group_moderation_content' ) ;
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
		<p><?php sanitize_text_field(esc_attr_e( 'No posts found.', 'bp-post-status' )); ?></p>
	</div>
<?php endif;

wp_reset_postdata(); ?>
