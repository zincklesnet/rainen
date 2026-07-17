<?php
/**
 * Search file
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$reign_header_layout = get_theme_mod( 'reign_header_layout', 'v1' );
?>

<div class="search-wrap rg-icon-wrap">
	<span class="rg-search-icon far fa-magnifying-glass" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Open search', 'reign' ); ?>" aria-expanded="false" aria-controls="rg-search-form-wrap"></span>
	<div class="rg-search-form-wrap" id="rg-search-form-wrap">
		<span class="rg-search-close far fa-circle-xmark" role="button" tabindex="0" aria-label="<?php esc_attr_e( 'Close search', 'reign' ); ?>"></span>
		<?php
		if ( 'v4' === $reign_header_layout ) {
			do_action( 'reign_header_v4_middle_section_html' );
		} else {
			// Check cache for the search form.
			$search_form = wp_cache_get( 'reign_search_form' );
			if ( false === $search_form ) {
				ob_start();
				get_search_form();
				$search_form = ob_get_clean();
				wp_cache_set( 'reign_search_form', $search_form );
			}
			echo $search_form; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
	</div>
</div>
