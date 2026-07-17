<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>

<section class="no-results not-found">
	<header class="page-header">
		<h1 class="page-title"><?php esc_html_e( 'Nothing Found', 'reign' ); ?></h1>
	</header><!-- .page-header -->

	<?php do_action( 'reign_rg_post_content_before' ); ?>

	<div class="rg-post-content">
		<div class="page-content">
			<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

				<?php /* translators: %1$s: URL to the new post screen. */ ?>
					<p><?php printf( wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'reign' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( admin_url( 'post-new.php' ) ) ); ?></p>

			<?php elseif ( is_search() ) : ?>

				<div class="reign-empty-state reign-search-empty">
					<span class="reign-empty-state__icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="11" cy="11" r="7"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
					</span>
					<p class="reign-empty-state__message">
						<?php
						$reign_search_term = get_search_query();
						if ( '' !== trim( $reign_search_term ) ) {
							printf(
								/* translators: %s: search term. */
								esc_html__( 'No results for &ldquo;%s&rdquo;. Try a different keyword or check your spelling.', 'reign' ),
								esc_html( $reign_search_term )
							);
						} else {
							esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'reign' );
						}
						?>
					</p>
				</div>
				<?php
				get_search_form();

			else :
				?>

				<p><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'reign' ); ?></p>
				<?php
				get_search_form();

			endif;
			?>
		</div><!-- .page-content -->
	</div>

	<?php do_action( 'reign_rg_post_content_after' ); ?>

</section><!-- .no-results -->
