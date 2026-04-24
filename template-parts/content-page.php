<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Reign
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'rtm_post_begins' ); ?>
	<?php
	if ( ! is_front_page() ) {
		$post_type = get_post_type();

		$wbcom_metabox_data = get_post_meta( get_the_ID(), 'reign_wbcom_metabox_data', true );

		$page_option = isset( $wbcom_metabox_data['layout']['display_page_title'] ) ? $wbcom_metabox_data['layout']['display_page_title'] : 'off';

		$hide_title = get_theme_mod( 'reign_' . $post_type . '_single_pagetitle_enable' );

		$default_single_header_enable    = get_theme_mod( 'reign_cpt_default_sub_header_switch', false );
		$reign_page_single_header_enable = get_theme_mod( 'reign_page_single_header_enable', true );

		$hide = true;
		if ( $page_option == 'on' ) {
			$hide = false;
		} elseif ( $page_option == '' ) {
			$hide = true;
		} elseif ( $reign_page_single_header_enable && ! $hide_title ) {
			$hide = false;
		} elseif ( $reign_page_single_header_enable && $hide_title ) {
			$hide = true;
		} elseif ( $default_single_header_enable && ! $hide_title ) {
			$hide = false;
		} elseif ( $default_single_header_enable && $hide_title ) {
			$hide = true;
		}

		$reign_subheader_settings = get_post_meta( get_the_ID(), '_subheader_overwrite', true );
		if ( $reign_subheader_settings == 'yes' ) {
			$hide = true;
		}

		if ( ! $hide ) {
			?>
				<header class="entry-header">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header><!-- .entry-header -->
			<?php
		}
	}
	?>

	<?php
	if ( has_post_thumbnail() && ! post_password_required() && is_singular() ) {
		if ( is_singular() ) {
			$switch_header_image = get_theme_mod( 'reign_single_' . get_post_type() . '_switch_header_image', false );

			if ( ! $switch_header_image ) {
				?>
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'View %s', 'reign' ), the_title_attribute( 'echo=0' ) ) ); ?>" class="entry-media rg-post-thumbnail">
					<?php
					the_post_thumbnail( 'reign-featured-large' );
					?>
				</a>
				<?php
			}
		} else {
			?>
			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'View %s', 'reign' ), the_title_attribute( 'echo=0' ) ) ); ?>" class="entry-media rg-post-thumbnail">
				<?php
				the_post_thumbnail( 'reign-featured-large' );
				?>
			</a>
			<?php
		}
	}
	?>

	<div class="entry-content">
		<?php
			the_content();

			wp_link_pages(
				array(
					'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'reign' ),
					'after'  => '</div>',
				)
			);
			?>
	</div><!-- .entry-content -->

	<?php if ( false && get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				sprintf(
				/* translators: %s: Name of current post */
					esc_html__( 'Edit %s', 'reign' ),
					the_title( '<span class="screen-reader-text">"', '"</span>', false )
				),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-## -->
