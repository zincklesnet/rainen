<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

get_header();

do_action( 'reign_before_content_section' ); ?>

<div class="content-wrapper">
	<?php if ( bbp_is_forum_archive() ) { ?>
		<header class="entry-header">
			<h1 class="entry-title"><?php echo esc_html( get_the_title() ); ?></h1>
		</header>
		<?php
	}

	if ( have_posts() ) :

		while ( have_posts() ) :
			the_post();

			/*
			* Include the Post-Format-specific template for the content.
			* If you want to override this in a child theme, then include a file
			* called content-___.php (where ___ is the Post Format name) and that will be used instead.
			*/
			get_template_part( 'template-parts/content', 'bbpress' );

		endwhile; // End of the loop.
		?>

		<?php

		else :

			get_template_part( 'template-parts/content', 'none' );

			?>

	<?php endif; ?>
</div>

<?php do_action( 'reign_after_content_section' ); ?>

<?php
get_footer();
