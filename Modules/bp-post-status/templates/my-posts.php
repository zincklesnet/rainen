<?php
/*
	This page is used for the profile my posts page
	
*/
$user_id = bp_displayed_user_id();
$user = get_userdata( $user_id );

if ( ( $user_id == get_current_user_id() ) || current_user_can( 'manage_options' ) ) {
	$author_view = true;
}

if ( ! empty( $_GET['bpps-search-type'] ) ) {
	$is_search = esc_attr( $_GET['bpps-search-type'] );
	$search_string = esc_attr( $_GET['s'] );
}

if ( function_exists( 'bpsp_get_add_post_url' ) ) {
	$add_post_url = bpsp_get_add_post_url();
}

$slug = '';//fix for post_permalink not returning post slug

//query_posts( $args );
if ( isset( $is_search ) ) {
	$q = new WP_Query( bpps_get_my_posts_query( 'search', $search_string ) );
} else {
	$q = new WP_Query( bpps_get_my_posts_query() );
}

if ( isset( $add_post_url ) && current_user_can( 'edit_posts' ) ) :?>

<input type="button" value="<?php sanitize_text_field(esc_attr_e( 'Add Post', 'bp-post-status' )); ?>" onclick="window.location.href='<?php echo esc_html($add_post_url); ?>'" /><br /> 

<?php endif; ?>

<div class="search-form">
	<form class="searchform" method="get" action="<?php esc_html( bp_displayed_user_domain()) . sanitize_text_field(esc_attr__('/my-posts', 'bp-post-status' )); ?>">
		<div class="input-group">
			
			<input type="text" name="s" class="form-control" placeholder="<?php sanitize_text_field(esc_attr_e( 'Search ', 'bp-post-status' )); echo esc_attr(bp_core_get_user_displayname( $user_id )); sanitize_text_field(esc_attr_e( '\'s posts', 'bp-post-status' )); ?>">
			
			<input type="hidden" value="<?php echo esc_attr($user->user_login); ?>" name="author_name" />
			
			<input type="hidden" value="my-posts-search" name="bpps-search-type" />
			
			<span class="input-btn">
				<button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button>
			</span>
		</div>
		<br/>
		<?php do_action('bpps_my_posts_search_form'); ?>
	</form>
</div>

<?php bpps_load_template( 'sticky-posts.php' );
if ( isset( $is_search ) && $q->have_posts() ) : ?>
	<h1 class="page-title archive-title">
		<?php
			/* translators: %1$d is the number of results found, %2$s is the search term */
			printf( esc_attr(_n( '%1$d result for %2$s', 
						'%1$d results for %2$s', $q->found_posts, 'bp-post-status')), 
					esc_attr(number_format_i18n( $q->found_posts ) ), 
					'<span>' . get_search_query() . '</span>' 
			);
		?>
	</h1><br />
<?php endif;

if ($q->have_posts() ) : ?>
<?php do_action( 'bpps_before_my_posts_content' ) ?>
<div class="pagination no-ajax">
	<div id="posts-count" class="pag-count">
		<?php bpps_posts_pagination_count( $q ) ?>
	</div>

	<div id="posts-pagination" class="pagination-links">
		<?php bpps_pagination( $q ) ?>
	</div>
</div>

<?php do_action( 'bpps_before_my_post_list' ) ?>
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
		
		<h2 class="posttitle"><a href="<?php esc_html(the_permalink($post)) . $slug; ?>" rel="bookmark" title="<?php esc_attr_e( 'Link to', 'bp-post-status' ) ?> <?php the_title_attribute(); ?>"><?php esc_attr(the_title()); ?></a></h2>

		<p class="category"> <em><?php sanitize_text_field(esc_attr_e( 'in', 'bp-post-status' )) ?> <?php esc_attr(the_category(', ')) ?></em></p>

		<div class="entry">
			<?php echo bpps_the_excerpt( $post->ID ); ?>
		</div>
		<?php if ( isset( $author_view ) ) :?>
			
			<a href="<?php echo esc_html( home_url()); ?>" class="post-delete-link" data-post="<?php esc_attr( $post->ID ); ?>" title="<?php sanitize_text_field(esc_attr_e( 'Delete Post', 'bp-post-status' )); ?>">	<?php sanitize_text_field(esc_attr_e( 'Delete Post', 'bp-post-status' )); ?></a>
		
		<?php endif; ?>
		
		<p class="postmetadata"><span class="tags"><?php the_tags( esc_attr__( 'Tags: ', 'bp-post-status' ), ', ', '<br />'); ?></span> <span class="comments"><?php comments_popup_link( sanitize_text_field(esc_attr__( 'No Comments &#187;', 'bp-post-status' )), sanitize_text_field(esc_attr__( '1 Comment &#187;', 'bp-post-status' )), sanitize_text_field(esc_attr__( '% Comments &#187;', 'bp-post-status' )) ); ?></span></p>
		
		<?php if ( isset( $author_view ) ) :?>
		
				<p class="make-sticky"><?php bpps_make_post_sticky( 'my-posts' ); ?></p>
				
				<p id="post-label-<?php the_ID(); ?>" style="display:none;"></p>
		
		<?php endif; ?>

		<em class="post-status"><?php sanitize_text_field(esc_attr_e( 'Post Status: ', 'bp-post-status' )); echo esc_attr($post->post_status);?></em>
		
		<?php if ( $post->post_status == 'group_post' ) : ?>
		
			<em class="post-group"><?php sanitize_text_field(esc_attr_e( 'Group ', 'bp-post-status' )); echo esc_attr( bp_get_group_name( groups_get_group( get_post_meta( $post->ID, 'bpgps_group', true ) ) ) ); ?></em>
		
		<?php endif; ?>
		
		<em class="post-date"><?php sanitize_text_field(esc_attr_e( 'Post Date: ', 'bp-post-status' )); echo esc_attr(get_the_time(get_option( 'date_format' )));?></em>
	
	</div>
</div>

<?php endwhile;?>

</form>

<?php 
	do_action( 'bpps_after_my_post_content' ) ;
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
