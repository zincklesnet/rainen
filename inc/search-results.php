<?php
/**
 * Search results UX helpers.
 *
 * Powers the search.php / content-search.php experience: results count header,
 * post-type badges, search-term highlighting and the thumbnail card layout.
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

if ( ! function_exists( 'reign_search_results_count_text' ) ) {

	/**
	 * Build the human-readable "N results for "term"" header string.
	 *
	 * @since 8.0.0
	 *
	 * @param WP_Query|null $query Optional query object. Defaults to the main query.
	 * @return string Escaped-safe markup for the results count header.
	 */
	function reign_search_results_count_text( $query = null ) {
		if ( ! $query instanceof WP_Query ) {
			global $wp_query;
			$query = $wp_query;
		}

		$total = isset( $query->found_posts ) ? (int) $query->found_posts : 0;
		$term  = get_search_query();

		if ( '' === trim( $term ) ) {
			/* translators: %s: number of search results. */
			$text = sprintf(
				/* translators: %s: formatted number of results. */
				_n( '%s result found', '%s results found', $total, 'reign' ),
				'<span class="reign-search-summary__count">' . esc_html( number_format_i18n( $total ) ) . '</span>'
			);
		} else {
			$text = sprintf(
				/* translators: 1: formatted number of results, 2: search term. */
				_n(
					'%1$s result for %2$s',
					'%1$s results for %2$s',
					$total,
					'reign'
				),
				'<span class="reign-search-summary__count">' . esc_html( number_format_i18n( $total ) ) . '</span>',
				'<span class="reign-search-summary__term">&ldquo;' . esc_html( $term ) . '&rdquo;</span>'
			);
		}

		return $text;
	}
}

if ( ! function_exists( 'reign_search_results_header' ) ) {

	/**
	 * Output the search results count header.
	 *
	 * @since 8.0.0
	 *
	 * @param WP_Query|null $query Optional query object.
	 * @return void
	 */
	function reign_search_results_header( $query = null ) {
		$allowed = array(
			'span' => array( 'class' => array() ),
		);
		?>
		<header class="reign-search-summary">
			<h1 class="reign-search-summary__title"><?php echo wp_kses( reign_search_results_count_text( $query ), $allowed ); ?></h1>
		</header>
		<?php
	}
}

if ( ! function_exists( 'reign_search_result_type_label' ) ) {

	/**
	 * Resolve a friendly singular label for the current result's post type.
	 *
	 * @since 8.0.0
	 *
	 * @param int|WP_Post|null $post Optional post. Defaults to the current post.
	 * @return string Friendly label (e.g. "Post", "Page", "Product").
	 */
	function reign_search_result_type_label( $post = null ) {
		$post = get_post( $post );

		if ( ! $post instanceof WP_Post ) {
			return '';
		}

		$obj = get_post_type_object( $post->post_type );

		if ( $obj && isset( $obj->labels->singular_name ) && '' !== $obj->labels->singular_name ) {
			$label = $obj->labels->singular_name;
		} else {
			$label = $post->post_type;
		}

		/**
		 * Filter the friendly post-type label shown on the search result badge.
		 *
		 * @since 8.0.0
		 *
		 * @param string  $label Friendly label.
		 * @param WP_Post $post  Current post object.
		 */
		return apply_filters( 'reign_search_result_type_label', $label, $post );
	}
}

if ( ! function_exists( 'reign_search_result_type_badge' ) ) {

	/**
	 * Output the post-type badge for the current search result.
	 *
	 * @since 8.0.0
	 *
	 * @param int|WP_Post|null $post Optional post. Defaults to the current post.
	 * @return void
	 */
	function reign_search_result_type_badge( $post = null ) {
		$post = get_post( $post );

		if ( ! $post instanceof WP_Post ) {
			return;
		}

		$label = reign_search_result_type_label( $post );

		if ( '' === $label ) {
			return;
		}

		printf(
			'<span class="reign-search-result__badge reign-search-result__badge--%1$s">%2$s</span>',
			esc_attr( sanitize_html_class( $post->post_type ) ),
			esc_html( $label )
		);
	}
}

if ( ! function_exists( 'reign_search_highlight' ) ) {

	/**
	 * Wrap occurrences of the active search term in a highlight element.
	 *
	 * Operates on plain text only (the input is escaped first), so it is safe to
	 * echo the return value directly. Each search word is matched case-insensitively
	 * on word boundaries where possible.
	 *
	 * @since 8.0.0
	 *
	 * @param string $text The plain-text string to highlight within.
	 * @return string Escaped text with <mark> wrappers around matched terms.
	 */
	function reign_search_highlight( $text ) {
		$text = (string) $text;

		if ( '' === $text ) {
			return '';
		}

		$escaped = esc_html( $text );
		$term    = trim( get_search_query() );

		if ( '' === $term ) {
			return $escaped;
		}

		// Split the search query into individual words, longest first so that
		// multi-word phrases win over their sub-words.
		$words = preg_split( '/\s+/', $term );
		$words = array_filter(
			array_map( 'trim', (array) $words ),
			static function ( $word ) {
				return '' !== $word && strlen( $word ) > 1;
			}
		);

		if ( empty( $words ) ) {
			return $escaped;
		}

		usort(
			$words,
			static function ( $a, $b ) {
				return strlen( $b ) - strlen( $a );
			}
		);

		// The term is user supplied: escape it the same way the haystack was
		// escaped (esc_html) before quoting for the regex so the patterns match.
		$patterns = array();
		foreach ( $words as $word ) {
			$patterns[] = preg_quote( esc_html( $word ), '/' );
		}

		$regex = '/(' . implode( '|', $patterns ) . ')/iu';

		$highlighted = preg_replace( $regex, '<mark class="reign-search-highlight">$1</mark>', $escaped );

		// preg_replace returns null on failure (e.g. malformed UTF-8); fall back.
		return null === $highlighted ? $escaped : $highlighted;
	}
}

if ( ! function_exists( 'reign_search_highlighted_title' ) ) {

	/**
	 * Echo the current post title with the search term highlighted, wrapped in a link.
	 *
	 * @since 8.0.0
	 *
	 * @return void
	 */
	function reign_search_highlighted_title() {
		$allowed = array(
			'mark' => array( 'class' => array() ),
		);
		printf(
			'<a href="%1$s" rel="bookmark">%2$s</a>',
			esc_url( get_permalink() ),
			wp_kses( reign_search_highlight( get_the_title() ), $allowed )
		);
	}
}

if ( ! function_exists( 'reign_search_highlighted_excerpt' ) ) {

	/**
	 * Echo a trimmed, highlighted excerpt for the current search result.
	 *
	 * @since 8.0.0
	 *
	 * @param int $word_count Number of words to keep. Default 30.
	 * @return void
	 */
	function reign_search_highlighted_excerpt( $word_count = 30 ) {
		$raw = get_the_excerpt();

		if ( '' === trim( (string) $raw ) ) {
			return;
		}

		$trimmed = wp_trim_words( $raw, absint( $word_count ), '&hellip;' );
		$allowed = array(
			'mark' => array( 'class' => array() ),
		);

		echo wp_kses( reign_search_highlight( $trimmed ), $allowed ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Highlighted output is sanitized via wp_kses on the line above.
	}
}
