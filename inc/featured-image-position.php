<?php
/**
 * Featured Image Position - per-post featured image layout options.
 *
 * Adds a sidebar panel in the block editor to select how the featured image
 * is displayed on single posts: above content, behind title (hero), beside
 * title, or hidden.
 *
 * @package Reign
 * @since 7.9.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the post meta for featured image position.
 */
function reign_register_featured_image_meta() {
	$post_types = apply_filters( 'reign_featured_image_position_post_types', array( 'post' ) );

	foreach ( $post_types as $post_type ) {
		register_post_meta(
			$post_type,
			'reign_featured_image_position',
			array(
				'show_in_rest'  => true,
				'single'        => true,
				'type'          => 'string',
				'default'       => '',
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'reign_register_featured_image_meta' );

/**
 * Enqueue the editor sidebar script.
 */
function reign_featured_image_editor_assets() {
	$screen = get_current_screen();
	if ( ! $screen || ! $screen->is_block_editor() ) {
		return;
	}

	$post_types = apply_filters( 'reign_featured_image_position_post_types', array( 'post' ) );
	if ( ! in_array( $screen->post_type, $post_types, true ) ) {
		return;
	}

	wp_enqueue_script(
		'reign-featured-image-position',
		get_template_directory_uri() . '/assets/js/featured-image-position.js',
		array( 'wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components', 'wp-data', 'wp-i18n' ),
		wp_get_theme()->get( 'Version' ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'reign_featured_image_editor_assets' );

/**
 * Get the featured image position for a post.
 *
 * @param int $post_id Post ID.
 * @return string Position value: '', 'behind', 'beside', 'hidden'.
 */
function reign_get_featured_image_position( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}
	$position = get_post_meta( $post_id, 'reign_featured_image_position', true );
	return $position ? $position : '';
}

/**
 * Add body class for featured image position.
 */
function reign_featured_image_body_class( $classes ) {
	if ( is_singular( 'post' ) ) {
		$position = reign_get_featured_image_position();
		if ( $position ) {
			$classes[] = 'reign-fi-' . sanitize_html_class( $position );
		}
	}
	return $classes;
}
add_filter( 'body_class', 'reign_featured_image_body_class' );

/**
 * Render the featured image based on position setting.
 * Called from template-parts/content.php on singular posts.
 *
 * @param int $post_id Post ID.
 * @return string|false HTML output or false if default behavior should apply.
 */
function reign_render_featured_image( $post_id = null ) {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	if ( ! has_post_thumbnail( $post_id ) ) {
		return false;
	}

	$position = reign_get_featured_image_position( $post_id );

	switch ( $position ) {
		case 'hidden':
			return '';

		case 'behind':
			$image_url = get_the_post_thumbnail_url( $post_id, 'full' );
			ob_start();
			?>
			<div class="reign-fi-hero" style="background-image: url('<?php echo esc_url( $image_url ); ?>');">
				<div class="reign-fi-hero-overlay">
					<div class="reign-fi-hero-content">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
						<div class="entry-meta"><?php reign_entry_list_footer(); ?></div>
					</div>
				</div>
			</div>
			<?php
			return ob_get_clean();

		case 'beside':
			ob_start();
			?>
			<div class="reign-fi-beside">
				<div class="reign-fi-beside-image">
					<?php the_post_thumbnail( 'reign-featured-large' ); ?>
				</div>
				<div class="reign-fi-beside-content">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					<div class="entry-meta"><?php reign_entry_list_footer(); ?></div>
				</div>
			</div>
			<?php
			return ob_get_clean();

		default:
			return false;
	}
}
