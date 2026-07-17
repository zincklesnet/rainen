<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

get_header();
?>
	
<div <?php body_class( 'content-wrapper' ); ?>>
	<section class="error-404 not-found">
		<header class="page-header">

			<h2><?php esc_html_e( '404', 'reign' ); ?></h2>
			<h3><?php esc_html_e( 'page not found', 'reign' ); ?></h3>
			<p><?php esc_html_e( 'The page you were looking for doesn\'t exist.', 'reign' ); ?></p>
			<span><a class="button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Go Back', 'reign' ); ?></a></span>

		</header><!-- .page-header -->
	</section><!-- .error-404 -->
</div>

<?php
get_footer();
