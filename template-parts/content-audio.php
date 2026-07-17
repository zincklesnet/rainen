<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

global $blog_list_layout;
if ( ! isset( $blog_list_layout ) ) {
	$blog_list_layout = get_theme_mod( 'reign_blog_list_layout', 'default-view' );
}
$kirki_post_types_support_class = new Reign_Customizer_Post_Types_Fields();
$supported_post_types           = $kirki_post_types_support_class->get_post_types_to_support();
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( $blog_list_layout ); ?>>

	<?php do_action( 'reign_post_content_begins' ); ?>

	<?php
	$post_audio          = get_post_meta( get_the_ID(), '_reign_post_audio', true );
	$post_audio_embed    = wp_oembed_get( $post_audio );
	$reign_post_type     = get_post_type();
	$has_thumbnail       = has_post_thumbnail();
	$switch_header_image = false;

	if ( $post_audio && $post_audio_embed ) {
		?>
		<div class="rg-audio-block rg-post-thumbnail">
			<?php echo $post_audio_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_oembed_get() returns sanitized provider markup. ?>
		</div>
		<?php
	} elseif ( $has_thumbnail ) {
		if ( is_singular() && ( 'post' === $reign_post_type || ! in_array( $reign_post_type, array_column( $supported_post_types, 'slug' ) ) ) ) {
			$switch_header_image = ( 'post' === $reign_post_type )
				? get_theme_mod( 'reign_single_post_switch_header_image', false )
				: get_theme_mod( 'reign_single_' . $reign_post_type . '_switch_header_image', false );
		}

		if ( ! reign_is_truthy( $switch_header_image ) ) {
			?>
			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( /* translators: %s: Post title. */ __( 'View %s', 'reign' ), get_the_title() ) ); ?>" class="entry-media rg-post-thumbnail">
				<?php the_post_thumbnail( 'reign-featured-large' ); ?>
			</a>
			<?php
		}
	}
	?>

	<?php do_action( 'reign_rg_post_content_before' ); ?>

	<div class="rg-post-content">
		<?php
		if ( $post_audio && false === $post_audio_embed ) :
			?>
			<div class="rg-audio-block rg-post-thumbnail">
				<?php echo do_shortcode( '[audio src="' . esc_url( $post_audio ) . '"]' ); ?>
			</div>
		<?php endif; ?>

		<?php if ( ! is_single() ) { ?>
			<header class="entry-header">
				<?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
				<div class="entry-meta"><?php reign_entry_list_footer(); ?></div>
			</header><!-- .entry-header -->
		<?php } ?>

		<div class="entry-content">
			<?php
			if ( is_singular() ) {
				the_content(
					sprintf(
						wp_kses(
							/* translators: %s: Name of current post. */
							__( 'Continue reading %s <span class="meta-nav">&rarr;</span>', 'reign' ),
							array( 'span' => array( 'class' => array() ) )
						),
						the_title( '<span class="screen-reader-text">"', '"</span>', false )
					)
				);
			} else {
				echo '<p>' . wp_kses_post( get_the_excerpt() ) . '</p>';
			}
			?>

			<?php
			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'reign' ),
					'after'  => '</div>',
				)
			);
			?>

			<?php if ( ! is_singular() ) { ?>
				<p class="no-margin"><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( /* translators: %s: Post title. */ __( 'View %s', 'reign' ), the_title_attribute( 'echo=0' ) ) ); ?>" class="read-more button"><?php esc_html_e( 'Read More', 'reign' ); ?></a></p>
			<?php } ?>

		</div><!-- .entry-content -->
	</div>

	<?php do_action( 'reign_rg_post_content_after' ); ?>

	<?php
	if ( is_single() ) {
		do_action( 'reign_extra_info_on_single_post_end' );
	}
	?>
</article><!-- #post-## -->
