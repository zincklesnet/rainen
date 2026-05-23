<?php
$post_id           = get_the_ID();
$author_id         = get_the_author_meta( 'ID' );
$post_status       = get_post_status();
$zombify_data_type = get_post_meta( $post_id, 'zombify_data_type', true );
$post_actions      = array();

if ( ( $author_id == get_current_user_id() ) && zf_user_can_edit( $post_id ) ) {
	if ( $zombify_data_type ) {
		$post_actions[] = array(
			'url'        => add_query_arg( array(
				'post_id' => $post_id,
				'action'  => 'update'
			), get_permalink( zf_get_option( 'zombify_post_create_page' ) ) ),
			'label'      => __( 'Edit', 'zombify' ),
			'attributes' => array()
		);

		$post = get_post( $post_id );
		if ( in_array( $post->post_status, zf_get_deletable_post_statuses() ) ) {
			$post_actions[] = array(
				'url'        => add_query_arg( array(
					'post_id' => $post_id,
					'action'  => 'delete',
					array( 'key' => wp_create_nonce( 'zf-delete-post' ) )
				) ),
				'label'      => __( 'Delete', 'zombify' ),
				'attributes' => array(
					'onclick="return confirm(\'' . esc_html__( 'Are you sure you want to delete this item?', 'zombify' ) . '\');"'
				)
			);
		}
	}
}

$post_actions = (array) apply_filters( 'zf_buddypress_submission_item_actions', $post_actions ); ?>
<article id="<?php echo $post_id; ?>" class="zf-post zf-type-<?php echo $zombify_data_type; ?>">

	<?php if ( $zombify_data_type ) { ?>
		<div class="zf-type zf-<?php echo $zombify_data_type; ?>"><?php echo $zombify_post_types[ $zombify_data_type ]['name']; ?></div>
	<?php } ?>

	<h2 class="zf-title">
		<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
	</h2>
	<div class="zf-post-thumbnail">
		<div class="zf-status zf-<?php echo $post_status; ?>"><?php echo $zombify_post_statuses[ $post_status ]; ?></div>
		<?php if ( ! empty( $post_actions ) ) { ?>
			<div class="zf-actions">
				<a class="zf-action_btn js-zf-actions-toggle" href="#">
					<i class="zf-icon zf-icon-actions"></i>
				</a>
				<div class="zf-actions_dropdown">
					<ul>
						<?php
						foreach ( $post_actions as $post_action ) {
							$attributes = '';
							if ( ! empty( $post_action['attributes'] ) ) {
								$attributes .= ' ' . join( ' ', $post_action['attributes'] );
							} ?>
							<li>
								<a href="<?php echo esc_url( $post_action['url'] ); ?>"<?php echo $attributes; ?>><?php echo esc_html( $post_action['label'] ); ?></a>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>
		<?php } ?>
		<a class="zf-link" href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( 'full', array( 'play' => true ) ); ?>
		</a>
	</div>
	<?php
	$author_avatar_size = apply_filters( 'boombox_author_avatar_size', 74 );
	$author_id          = get_the_author_meta( 'ID' );
	$author_url         = esc_url( get_author_posts_url( $author_id ) );
	$terms              = wp_get_post_terms( $post_id, 'category', array( 'fields' => 'all' ) );
	?>
	<div class="zf-post-meta">
		<div class="zf-author-info">
			<a class="zf-author_avatar" href="<?php echo $author_url; ?>"><?php echo get_avatar( $author_id, $author_avatar_size ); ?></a>
			<a class="zf-author_name" href="<?php echo $author_url; ?>"><?php echo wp_kses_post( get_the_author_meta( 'display_name', $author_id ) ); ?></a>
		</div>
		<span class="zf-date"><?php echo get_the_date(); ?></span>
	</div>
	<?php if ( ! empty( $terms ) ) { ?>
		<div class="zf-categories">
			<?php foreach ( $terms as $term ) { ?>
				<a class="zf-category" href="<?php echo get_term_link( $term ); ?>"><?php echo $term->name; ?></a>
			<?php } ?>
		</div>
	<?php } ?>

</article>