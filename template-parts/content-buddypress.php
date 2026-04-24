<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Reign
 */
global $post;
$bp_pages = get_option( 'bp-pages' );
if ( bp_is_current_component( 'groups' ) && isset( $bp_pages['groups'] ) && ! bp_is_user() && ! bp_is_group_create() && ! bp_is_group() ) {
	$post = get_post( $bp_pages['groups'] );
} elseif ( bp_is_current_component( 'members' ) && isset( $bp_pages['members'] ) && ! bp_is_user() ) {
	$post = get_post( $bp_pages['members'] );
} elseif ( bp_is_current_component( 'activity' ) && isset( $bp_pages['activity'] ) && ! bp_is_user() ) {
	$post = get_post( $bp_pages['activity'] );
} elseif ( bp_is_current_component( 'document' ) && isset( $bp_pages['document'] ) ) {
	$post = get_post( $bp_pages['document'] );
} elseif ( bp_is_current_component( 'media' ) && isset( $bp_pages['media'] ) ) {
	$post = get_post( $bp_pages['media'] );
} elseif ( bp_is_register_page() && isset( $bp_pages['register'] ) ) {
	$post = get_post( $bp_pages['register'] );
}

$hide = true;

if ( ! empty( $post ) && $post->ID != 0 ) {
	$wbcom_metabox_data = get_post_meta( $post->ID, 'reign_wbcom_metabox_data', true );
	$page_option        = isset( $wbcom_metabox_data['layout']['display_page_title'] ) ? $wbcom_metabox_data['layout']['display_page_title'] : 'on';
	if ( $post->ID == 0 || bp_is_user() ) {
		$page_option = 'off';
	}

	if ( $page_option == 'on' ) {
		$hide = false;
	} elseif ( $page_option == '' ) {
		$hide = true;
	}
	$reign_subheader_settings = get_post_meta( $post->ID, '_subheader_overwrite', true );
	if ( $reign_subheader_settings == 'yes' ) {
		$hide = true;
	}
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( bp_is_group_create() ) { ?>
		<header class="entry-header page-header">
			<h1 class="entry-title"><?php esc_html_e( 'Create Group', 'reign' ); ?></h1>
		</header><!-- .entry-header -->
	<?php } elseif ( ( function_exists( 'bp_is_register_page' ) && bp_is_register_page() ) || ( function_exists( 'bp_is_activation_page' ) && bp_is_activation_page() ) ) { ?>
		<?php
		$logo_id = get_theme_mod( 'custom_logo' );
		$logo    = ( $logo_id ) ? wp_get_attachment_image( $logo_id, 'full', '', array( 'class' => 'rg-logo' ) ) : get_bloginfo( 'name' );
		?>
		<div class="register-section-logo">
			<h2>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<?php echo wp_kses_post( $logo ); ?>
				</a>
			</h2>
		</div>
	<?php } elseif ( ! $hide ) { ?>
		<header class="page-title">
			<h1 class="entry-title"><?php the_title(); ?></h1>
			<?php
			if ( function_exists( 'bp_is_register_page' ) ) {
				if ( ! is_user_logged_in() && bp_is_register_page() && 'request-details' === bp_get_current_signup_step() ) {
					?>
					<span><?php esc_html_e( 'or', 'reign' ); ?> <a href="<?php echo esc_url( wp_login_url() ); ?>"><?php esc_html_e( 'Sign in', 'reign' ); ?></a></span>
					<?php
				}
			}
			?>
		</header><!-- .entry-header -->
	<?php } ?>

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
