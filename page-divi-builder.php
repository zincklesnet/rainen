<?php
/**
 * Divi Builder Template
 *
 * Description: Full-width template for Divi Builder compatibility.
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

get_header();

// Remove Reign's default content wrappers for Divi.
remove_action( 'reign_before_content', 'reign_theme_wrapper_start' );
remove_action( 'reign_after_content', 'reign_theme_wrapper_end' );
?>

<div id="primary" class="content-area">
	<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();
			// Output the content - Divi Builder will handle the rest.
			the_content();
		endwhile;
		?>
	</main>
</div>

<?php
get_footer();