<?php
/*
This page is used for the group post page

*/
if ( ! empty( $_GET['bpps-search-type'] ) ) {
	$is_search = sanitize_text_field(esc_attr( $_GET['bpps-search-type'] ));
	$search_string = sanitize_text_field(esc_attr( $_GET['s'] ));
}

$slug = '';//fix for post_permalink not returning post slug

if ( function_exists( 'bpsp_get_add_post_url' ) ) {
	$add_post_url = bpsp_get_add_post_url();
}
$user_id = get_current_user_id();
$group_id = bp_get_current_group_id();
$post_status = bpps_core_group_lookup( $user_id, $group_id );
if ( is_super_admin() || groups_is_user_admin( get_current_user_id(), $group_id ) ) {
	$admin_view = true;
} ?>

<?php if ( isset( $add_post_url ) && ! $post_status[0] && $post_status[1] ) :?>
	
	<input type="button" value="<?php sanitize_text_field(esc_attr_e( 'Add Post', 'bp-post-status' )); ?>" onclick="window.location.href='<?php echo esc_html($add_post_url); ?>'" /><br /> 

<?php endif; ?>

<div class="search-form">
	<form class="searchform" method="get" action="<?php echo esc_html(bp_get_group_url()) . '/group-posts'; ?>">
		<div class="input-group">
			<input type="text" name="s" class="form-control" placeholder="<?php sanitize_text_field(esc_attr_e( 'Search group posts', 'bp-post-status' )); ?>">
			<input type="hidden" value="group-posts-search" name="bpps-search-type" />
			<span class="input-group-btn">
				<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
			</span>
		</div>
		<br/>
		<?php do_action('bpps_group_posts_search_form'); ?>
	</form>
</div>

<?php if ( isset( $is_search ) ) {
	$q = new WP_Query( bpps_get_group_posts_query( $group_id, 'search', $search_string ) );
} else {
	$q = new WP_Query( bpps_get_group_posts_query( $group_id ) );
}

if ($q->have_posts() ) : ?>

<?php bpps_load_template( 'sticky-posts.php' ); ?>

<?php if ( isset( $is_search ) && $q->have_posts() ) : ?>
	<h1 class="page-title archive-title">
		<?php
			/* translators: %1$d is the number of results found, %2$s is the search term */
			printf( esc_attr(_n( '%1$d result for %2$s', 
						'%1$d results for %2$s', $q->found_posts, 'bp-post-status') ), 
					esc_attr(number_format_i18n( $q->found_posts ) ), 
					'<span>' . get_search_query() . '</span>' 
			);
		?>
	</h1><br />
<?php endif;

do_action( 'bpps_before_group_posts_content' ) ?>
<div class="pagination no-ajax">
	<div id="posts-count" class="pag-count">
		<?php bpps_posts_pagination_count( $q ) ?>
	</div>

	<div id="posts-pagination" class="pagination-links">
		<?php bpps_pagination( $q ) ?>
	</div>
</div>

<?php do_action( 'bpps_before_group_posts_list' ) ?>

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
				<?php the_post_thumbnail();?>
			</div>

		<?php endif;
		?>
		
		<h2 class="posttitle"><a href="<?php esc_html(the_permalink( $post )) . $slug; ?>" rel="bookmark" title="<?php sanitize_text_field(esc_attr_e( 'Link to', 'bp-post-status' )) ?> <?php the_title_attribute(); ?>"><?php sanitize_text_field(esc_attr(the_title())); ?></a></h2>

		<p class="category"><em><?php sanitize_text_field(esc_attr_e( 'in', 'bp-post-status' )) ?> <?php the_category(', ') ?> <?php printf( sanitize_text_field(esc_attr__('by ', 'bp-post-status')) . '%s', bp_core_get_userlink( esc_attr($post->post_author)) ) ?></em></p>

		<div class="entry">
			<?php bpps_get_content( $post, 'post-loop' ); ?>
		</div>
		
		<?php if ( isset( $admin_view ) ) :?>
		
			<a href="<?php esc_html(home_url()); ?>" class="post-delete-link" data-post="<?php esc_attr($post->ID); ?>" title="<?php echo sanitize_text_field(esc_attr__( 'Delete Post', 'bp-post-status' )); ?>">	<?php  sanitize_text_field(esc_attr_e( 'Delete Post', 'bp-post-status' )); ?></a>
		
		<?php endif; ?>
		<p class="postmetadata"><span class="tags"><?php the_tags( sanitize_text_field(esc_attr__( 'Tags: ', 'bp-post-status' )), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( sanitize_text_field(esc_attr__( 'No Comments ', 'bp-post-status')) . '&#187;', sanitize_text_field(esc_attr__( '1 Comment ', 'bp-post-status')) . '&#187;', esc_attr('%') . sanitize_text_field(esc_attr__(' Comments ', 'bp-post-status')) . '&#187;' ); ?></span></p>
		
		<?php if ( isset( $admin_view ) ) :?>
		
			<p class="make-home"><?php bpps_set_as_home_page(); ?></p>
			
			<p class="make-sticky"><?php bpps_make_post_sticky( 'group-post' ); ?></p>
			
		<?php endif; ?>
		</div>
</div>
<?php endwhile;?>
</form>
<?php 
	do_action( 'bpps_after_group_posts_content' ) ;
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
<?php endif;
wp_reset_postdata(); ?>
