<?php
/**
 * Job listing in the loop.
 *
 * This template can be overridden by copying it to yourtheme/job_manager/content-job_listing.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager
 * @category    Template
 * @since       1.0.0
 * @version     1.34.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $post;

if ( class_exists( 'Reign_WP_Job_Manager_Addon' ) ) {
	$job_grid_layout    = reign_job_mate_options();
	$job_listing_layout = isset( $job_grid_layout['job_listing_layout'] ) ? $job_grid_layout['job_listing_layout'] : '';
	$layout             = isset( $job_grid_layout['job_listing_grid_layout'] ) ? $job_grid_layout['job_listing_grid_layout'] : '';

	if ( ! is_singular( 'job_listing' ) && 'job_listing_grid' === $job_listing_layout && isset( $layout ) && 'job_listing_grid_layout_one' === $layout ) {
		reign_job_mate_get_template( 'content-job_listing_grid_layout_one.php', array(), 'job_manager' );
	} elseif ( ! is_singular( 'job_listing' ) && 'job_listing_grid' === $job_listing_layout && isset( $layout ) && 'job_listing_grid_layout_two' === $layout ) {
		reign_job_mate_get_template( 'content-job_listing_grid_layout_two.php', array(), 'job_manager' );
	} elseif ( ! is_singular( 'job_listing' ) && 'job_listing_grid' === $job_listing_layout && isset( $layout ) && 'job_listing_grid_layout_three' === $layout ) {
		reign_job_mate_get_template( 'content-job_listing_grid_layout_three.php', array(), 'job_manager' );
	} else {
		reign_job_mate_get_template( 'content-job_listing_default_layout.php', array(), 'job_manager' );
	}
} else {
	?>
	<li <?php job_listing_class(); ?> data-longitude="<?php echo esc_attr( $post->geolocation_long ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_lat ); ?>">
		<a href="<?php the_job_permalink(); ?>">
			<div class="job-details-wrapper">
				<div class="job-listing-company-logo">
					<?php the_company_logo( 'thumbnail' ); ?>
				</div>
				<div class="job-details">
					<div class="job-details-inner">
						<h3 class="job-listing-loop-job__title"><?php wpjm_the_job_title(); ?></h3>
						<div class="job-listing-company company">
							<?php the_company_name( '<strong>', '</strong> ' ); ?>
							<?php the_company_tagline( '<span class="tagline">', '</span>' ); ?>
						</div>
						<div class="job-location location">
							<i class="far fa-map-marker-alt"></i>
							<?php the_job_location( false ); ?>
						</div>
					</div>
					<div class="job-listing-meta meta">
						
						<div class="job-location location">
							<i class="far fa-map-marker-alt"></i>
							<?php the_job_location( false ); ?>
						</div>
												
						<ul class="job-types">
							<?php do_action( 'job_listing_meta_start' ); ?>
							<?php if ( get_option( 'job_manager_enable_types' ) ) { ?>
								<?php $types = wpjm_get_the_job_types(); ?>
								<?php
								if ( ! empty( $types ) ) :
									foreach ( $types as $type ) :
										?>
									<li class="job-type <?php echo esc_attr( sanitize_title( $type->slug ) ); ?>"><?php echo esc_html( $type->name ); ?></li>
									<?php endforeach; ?>
								<?php endif; ?>
							<?php } ?>
						
						<?php do_action( 'job_listing_meta_end' ); ?>
						</ul>
						
						<span class="job-published-date date"><?php the_job_publish_date(); ?></span>
						
					</div>
				</div>
			</div>
		</a>
	</li>
	<?php
}
