<?php
/**
 * Template for resume content inside a list of resumes.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-resumes/content-resume.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     wp-job-manager-resumes
 * @category    Template
 * @version     1.18.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $post;

if ( class_exists( 'Reign_WP_Job_Manager_Addon' ) ) {
	$resume_grid_layout    = reign_job_mate_options();
	$resume_listing_layout = isset( $resume_grid_layout['resume_listing_layout'] ) ? $resume_grid_layout['resume_listing_layout'] : '';
	$layout                = isset( $resume_grid_layout['resume_listing_grid_layout'] ) ? $resume_grid_layout['resume_listing_grid_layout'] : '';

	if ( ! is_singular( 'resume' ) && 'resume_listing_layout_grid' === $resume_listing_layout && isset( $layout ) && 'resume_listing_grid_layout_one' === $layout ) {
		reign_job_mate_get_template( 'content-resume-grid-layout-one.php', array(), 'wp-job-manager-resumes' );
	} elseif ( ! is_singular( 'resume' ) && 'resume_listing_layout_grid' === $resume_listing_layout && isset( $layout ) && 'resume_listing_grid_layout_two' === $layout ) {
		reign_job_mate_get_template( 'content-resume-grid-layout-two.php', array(), 'wp-job-manager-resumes' );
	} elseif ( ! is_singular( 'resume' ) && 'resume_listing_layout_grid' === $resume_listing_layout && isset( $layout ) && 'resume_listing_grid_layout_three' === $layout ) {
		reign_job_mate_get_template( 'content-resume-grid-layout-three.php', array(), 'wp-job-manager-resumes' );
	} else {
		reign_job_mate_get_template( 'content-resume-layout-default.php', array(), 'wp-job-manager-resumes' );
	}
} else {
	$category = get_the_resume_category();
	?>
	<li <?php resume_class(); ?> data-longitude="<?php echo esc_attr( $post->geolocation_long ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_lat ); ?>">
		<a href="<?php the_resume_permalink(); ?>">
			<div class="resume-details-wrapper">
				<div class="candidate-image">
					<?php the_candidate_photo(); ?>
				</div>
				<div class="candidate-details">
					<div class="candidate-details-inner">
						<h3><?php the_title(); ?></h3>
						<div class="candidate-title">
							<?php the_candidate_title( '<strong>', '</strong> ' ); ?>
						</div>
						<div class="candidate-location">
							<i class="far fa-map-marker-alt"></i> <?php the_candidate_location( false ); ?>
						</div>
					</div>
					<div class="resume-posted 
					<?php
					if ( $category ) :
						?>
						resume-meta<?php endif; ?>">
						<?php /* translators: %s: Human-readable time difference (e.g. "2 days"). */ ?>
						<date><?php printf( esc_html__( '%s ago', 'reign' ), esc_html( human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) ) ); ?></date>
						<?php if ( $category ) : ?>
							<div class="resume-category">
							<?php echo esc_html( $category ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</a>
	</li>
	<?php
}
