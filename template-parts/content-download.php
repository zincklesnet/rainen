<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Reign
 */
?>

<?php

$reign_edd_downloads_layouts = get_theme_mod( 'reign_edd_downloads_layouts', 'default' );

if ( 'layout1' === $reign_edd_downloads_layouts ) {
	get_template_part( 'template-parts/edd/download-layout-1' );
} elseif ( 'layout2' === $reign_edd_downloads_layouts ) {
	get_template_part( 'template-parts/edd/download-layout-2' );
} elseif ( 'layout3' === $reign_edd_downloads_layouts ) {
	get_template_part( 'template-parts/edd/download-layout-3' );
} else {
	get_template_part( 'template-parts/edd/download-layout-default' );
}
