<?php
/**
 * Register the reign news block.
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'reign_news_widget_block_init' ) ) {
	/**
	 * Registers the block using the metadata loaded from the `block.json` file.
	 * Specifies the render callback for server-side rendering.
	 *
	 * @see https://developer.wordpress.org/block-editor/tutorials/block-tutorial/writing-your-first-block-type/
	 */
	function reign_news_widget_block_init() {
		register_block_type_from_metadata(
			__DIR__,
			array(
				'render_callback' => 'reign_news_widget_render',
			)
		);
	}
	add_action( 'init', 'reign_news_widget_block_init' );
}

if ( ! function_exists( 'reign_news_widget_render' ) ) {
	/**
	 * Server-side rendering of the `reign-news/news-widget` block.
	 *
	 * @param array $attributes The block attributes.
	 *
	 * @return string Returns the post content with latest posts output.
	 */
	function reign_news_widget_render( $attributes ) {
		// Extract attributes.
		$title          = isset( $attributes['title'] ) ? $attributes['title'] : '';
		$posts_per_page = isset( $attributes['postsPerPage'] ) ? intval( $attributes['postsPerPage'] ) : 5;
		$post_category  = isset( $attributes['postCategory'] ) ? $attributes['postCategory'] : array();
		$show_author    = isset( $attributes['showAuthor'] ) ? $attributes['showAuthor'] : false;

		// Query arguments.
		$args = array(
			'posts_per_page'      => $posts_per_page,
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
		);

		// Include categories in the query only if a category is selected.
		if ( ! empty( $post_category ) && '' !== $post_category[0] ) {
			$args['category__in'] = $post_category;
		}

		// Fetch posts.
		$posts = get_posts( $args );

		// Start output buffering.
		ob_start();

		// Block content.
		echo '<div class="latest-posts-preview">';
		if ( ! empty( $title ) ) {
			echo '<h2 class="wp-block-heading">' . esc_html( $title ) . '</h2>';
		}

		if ( ! empty( $posts ) ) {
			echo '<ul class="latest-posts-list">';
			foreach ( $posts as $post ) {
				setup_postdata( $post );
				$permalink   = get_permalink( $post->ID );
				$author_id   = $post->post_author;
				$author_url  = get_author_posts_url( $author_id );
				$author_name = get_the_author_meta( 'display_name', $author_id );

				echo '<li class="latest-post-item">';
				if ( has_post_thumbnail( $post->ID ) ) {
					echo '<div class="latest-post-thumb">';
					echo '<a href="' . esc_url( $permalink ) . '">';
					echo get_the_post_thumbnail( $post->ID, 'medium', array( 'class' => 'latest-posts-thumbnail' ) );
					echo '</a>';
					echo '</div>';
				}
				echo '<div class="latest-post-content">';
				echo '<a href="' . esc_url( $permalink ) . '">';
				echo '<h4 class="post-title">' . esc_html( get_the_title( $post->ID ) ) . '</h4>';
				echo '</a>';
				if ( $show_author ) {
					echo '<p class="post-author">By <a href="' . esc_url( $author_url ) . '">' . esc_html( $author_name ) . '</a></p>';
				}
				echo '<p class="latest-posts-date">' . esc_html( get_the_date( '', $post->ID ) ) . '</p>';
				echo '</div>';
				echo '</li>';
			}
			wp_reset_postdata();
			echo '</ul>';
		} else {
			echo '<p>' . esc_html__( 'No posts found', 'reign' ) . '</p>';
		}
		echo '</div>';

		// Return the buffered content.
		return ob_get_clean();
	}
}
