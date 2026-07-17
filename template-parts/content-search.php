<?php
/**
 * Template part for displaying a single search result.
 *
 * Thumbnail card layout with a post-type badge, search-term highlighted title
 * and excerpt, and the standard entry meta.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$reign_has_thumb = has_post_thumbnail();
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'reign-search-result' . ( $reign_has_thumb ? ' reign-search-result--has-thumb' : ' reign-search-result--no-thumb' ) ); ?>>

	<?php do_action( 'reign_rg_post_content_before' ); ?>

	<?php if ( $reign_has_thumb ) : ?>
		<a class="reign-search-result__thumb" href="<?php the_permalink(); ?>" aria-hidden="true" tabindex="-1">
			<?php the_post_thumbnail( 'medium', array( 'alt' => the_title_attribute( 'echo=0' ) ) ); ?>
		</a>
	<?php endif; ?>

	<div class="reign-search-result__body rg-post-content">
		<header class="entry-header">
			<?php
			if ( function_exists( 'reign_search_result_type_badge' ) ) {
				reign_search_result_type_badge();
			}
			?>
			<h2 class="entry-title reign-search-result__title">
				<?php
				if ( function_exists( 'reign_search_highlighted_title' ) ) {
					reign_search_highlighted_title();
				} else {
					the_title( sprintf( '<a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a>' );
				}
				?>
			</h2>
			<?php
			// reign_entry_list_footer() renders nothing for hierarchical post
			// types (pages) — buffer it so we never emit an empty .entry-meta.
			ob_start();
			reign_entry_list_footer();
			$reign_entry_meta = trim( ob_get_clean() );
			if ( '' !== $reign_entry_meta ) :
				?>
				<div class="entry-meta"><?php echo $reign_entry_meta; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- reign_entry_list_footer() escapes its own output. ?></div>
			<?php endif; ?>
		</header><!-- .entry-header -->

		<div class="entry-content reign-search-result__excerpt">
			<?php
			if ( function_exists( 'reign_search_highlighted_excerpt' ) ) {
				reign_search_highlighted_excerpt();
			} else {
				the_excerpt();
			}
			?>
		</div><!-- .entry-content -->
	</div><!-- .reign-search-result__body -->

	<?php do_action( 'reign_rg_post_content_after' ); ?>

</article><!-- #post-## -->
