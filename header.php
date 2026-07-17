<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

?>
<!DOCTYPE html>
<?php do_action( 'reign_html_before' ); ?>
<html <?php language_attributes(); ?> class="<?php echo esc_attr( join( ' ', apply_filters( 'reign_html_class', array() ) ) ); ?>">
	<head>
		<?php do_action( 'reign_head_top' ); ?>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<?php wp_head(); ?>
		<?php do_action( 'reign_head_bottom' ); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php do_action( 'reign_body_top' ); ?>
		<?php wp_body_open(); ?>
		<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'reign' ); ?></a>
		<?php do_action( 'reign_before_page' ); ?>
		<div id="page" class="site">
			<?php do_action( 'reign_before_masthead' ); ?>
			<?php
			if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) {
				?>
			<header id="masthead" class="site-header <?php echo esc_attr( get_theme_mod( 'reign_header_layout', 'v2' ) ); ?>" role="banner">
				<?php do_action( 'reign_begin_masthead' ); ?>

				<?php do_action( 'reign_masthead' ); ?>

				<?php do_action( 'reign_end_masthead' ); ?>
			</header>
				<?php
			}
			?>
			<?php do_action( 'reign_after_masthead' ); ?>
			<?php do_action( 'reign_before_content' ); ?>
			<main id="content" class="site-content">
				<?php do_action( 'reign_content_top' ); ?>
