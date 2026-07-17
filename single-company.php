<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

add_action(
	'reign_before_content',
	function () {
		get_template_part( 'template-parts/reign', 'page-header' );
	}
);

add_filter(
	'body_class',
	function ( $classes ) {
		$classes[] = 'reign-wpjm-company-single';
		return $classes;
	}
);

add_filter(
	'reign_page_header_section_title',
	function ( $title ) {
		$company_page_slug = apply_filters( 'wp_job_manager_companies_company_slug', __( 'company', 'reign' ) );
		if ( get_query_var( $company_page_slug ) ) {
			global $wp_query;
			/* translators: %s: Company name. */
			$title = sprintf( __( 'Jobs at %s', 'reign' ), esc_attr( urldecode( get_query_var( apply_filters( 'wp_job_manager_companies_company_slug', 'company' ) ) ) ) );
		}
		return $title;
	},
	10,
	1
);

add_action(
	'reign_page_header_extra',
	function () {
		$company_page_slug = apply_filters( 'wp_job_manager_companies_company_slug', __( 'company', 'reign' ) );
		if ( get_query_var( $company_page_slug ) ) {
			global $wp_query;
			/* translators: %d: Number of jobs. */
			$page_title_html = '<div class="lm-breadcrumbs-wrapper wpjm-companies-page-title container" style="text-align:center;">' . sprintf( _n( '%d Job Available', '%d Jobs Available', $wp_query->found_posts, 'reign' ), $wp_query->found_posts ) . '</div>';
			echo $page_title_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
);

get_header();
?>

<?php do_action( 'reign_before_content_section' ); ?>

<div class="content-wrapper">
	<article>
		<div class="entry-content">
			<?php if ( have_posts() ) : ?>
				<div class="job_listings">
				<?php get_job_manager_template( 'job-listings-start.php' ); ?>
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					<?php get_job_manager_template_part( 'content', 'job_listing' ); ?>
				<?php endwhile; ?>
					<?php get_job_manager_template( 'job-listings-end.php' ); ?>
				</div>
				<?php else : ?>
					<?php do_action( 'job_manager_output_jobs_no_results' ); ?>
			<?php endif; ?>
		</div>
	</article>
</div>

<?php do_action( 'reign_after_content_section' ); ?>

<?php
get_footer();
