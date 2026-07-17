<?php
/**
 * Reign Block Pattern Categories.
 *
 * Registers the `reign-*` block pattern categories used by the 27 universal
 * patterns under patterns/ (synced from BuddyX 5.1.0 in Phase 6 of the
 * Kirki-removal migration). WP core auto-discovers the pattern files
 * themselves; we only need to register the category metadata so the editor
 * groups them nicely.
 *
 * @package reign
 * @since 8.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Register the reign-* pattern categories on init.
 */
function reign_register_block_pattern_categories(): void {
	if ( ! function_exists( 'register_block_pattern_category' ) ) {
		return;
	}

	$categories = array(
		'reign-hero'         => __( 'Reign Hero', 'reign' ),
		'reign-about'        => __( 'Reign About', 'reign' ),
		'reign-features'     => __( 'Reign Features', 'reign' ),
		'reign-social-proof' => __( 'Reign Social Proof', 'reign' ),
		'reign-pricing-faq'  => __( 'Reign Pricing & FAQ', 'reign' ),
		'reign-cta'          => __( 'Reign CTA', 'reign' ),
		'reign-footer'       => __( 'Reign Footer', 'reign' ),
		'reign-query'        => __( 'Reign Query', 'reign' ),
	);

	foreach ( $categories as $slug => $label ) {
		register_block_pattern_category(
			$slug,
			array( 'label' => $label )
		);
	}
}
add_action( 'init', 'reign_register_block_pattern_categories' );

/**
 * Register the Reign-specific patterns under patterns-local/.
 *
 * patterns/ is auto-discovered by WP 6.0+ (looks at active theme + parent).
 * patterns-local/ is OUR convention for Reign-native community patterns
 * (member-directory, leaderboard, activity-cta, community-hero) that
 * don't apply to other BuddyX-family themes. WP doesn't scan this
 * directory automatically, so we glob it and register each pattern
 * via register_block_pattern_from_file equivalent.
 *
 * @since 8.0.0
 */
function reign_register_local_patterns(): void {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}
	$dir = trailingslashit( get_template_directory() ) . 'patterns-local/';
	if ( ! is_dir( $dir ) ) {
		return;
	}

	$files = glob( $dir . '*.php' );
	if ( ! is_array( $files ) ) {
		return;
	}

	foreach ( $files as $file ) {
		$headers = get_file_data(
			$file,
			array(
				'title'         => 'Title',
				'slug'          => 'Slug',
				'description'   => 'Description',
				'viewportWidth' => 'Viewport Width',
				'categories'    => 'Categories',
				'keywords'      => 'Keywords',
			)
		);

		if ( empty( $headers['slug'] ) ) {
			continue;
		}

		$args = array(
			'title'    => translate( $headers['title'], 'reign' ), // phpcs:ignore WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralDomain
			'content'  => '',
			'source'   => 'theme',
			'filePath' => $file,
		);
		if ( ! empty( $headers['description'] ) ) {
			$args['description'] = translate( $headers['description'], 'reign' ); // phpcs:ignore WordPress.WP.I18n.LowLevelTranslationFunction,WordPress.WP.I18n.NonSingularStringLiteralText,WordPress.WP.I18n.NonSingularStringLiteralDomain
		}
		if ( ! empty( $headers['viewportWidth'] ) ) {
			$args['viewportWidth'] = (int) $headers['viewportWidth'];
		}
		if ( ! empty( $headers['categories'] ) ) {
			$args['categories'] = array_map( 'trim', explode( ',', $headers['categories'] ) );
		}
		if ( ! empty( $headers['keywords'] ) ) {
			$args['keywords'] = array_map( 'trim', explode( ',', $headers['keywords'] ) );
		}

		ob_start();
		include $file;
		$args['content'] = ob_get_clean();

		register_block_pattern( $headers['slug'], $args );
	}
}
add_action( 'init', 'reign_register_local_patterns', 11 );
