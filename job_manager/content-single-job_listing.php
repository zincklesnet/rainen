<?php
/**
 * Single job listing.
 *
 * This template can be overridden by copying it to yourtheme/job_manager/content-single-job_listing.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager
 * @category    Template
 * @since       1.0.0
 * @version     1.37.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post;
do_action( 'job_content_start' );
?>
<div class="single_job_listing">
	<?php if ( get_option( 'job_manager_hide_expired_content', 1 ) && 'expired' === $post->post_status ) : ?>
		<div class="job-manager-info"><?php esc_html_e( 'This listing has expired.', 'reign' ); ?></div>
	<?php else : ?>
		<?php do_action( 'single_job_listing_inner_before' ); ?>
		<div class="single-job-listing__inner">

			<?php do_action( 'reign_jobmate_single_job_listing_content_area_before' ); ?>

			<div class="single-job-listing__content-area">

				<?php do_action( 'single_job_listing_start' ); ?>

				<?php do_action( 'single_job_listing' ); ?>

				<?php the_company_video(); ?>
				
				<div class="job_description">
					<?php wpjm_the_job_description(); ?>
				</div>

				<?php do_action( 'single_job_listing_end' ); ?>

			</div>

			<div class="single-job-listing__sidebar-area">

				<?php do_action( 'single_job_listing_sidebar' ); ?>

			</div>

			<?php do_action( 'single_job_listing_content_area_after' ); ?>

		</div>
		<?php do_action( 'single_job_listing_inner_after' ); ?>
	<?php endif; ?>
</div>
<?php do_action( 'job_content_end' ); ?>
