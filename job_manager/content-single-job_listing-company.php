<?php
/**
 * Single view Company information box
 *
 * Hooked into single_job_listing_start priority 30
 *
 * This template can be overridden by copying it to yourtheme/job_manager/content-single-job_listing-company.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     WP Job Manager
 * @category    Template
 * @since       1.14.0
 * @version     1.32.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! get_the_company_name() ) {
	return;
}

global $post;
$company = '';
?>

<div class="single-job-listing-company company">
	<div class="single-job-listing-company__logo">
		<?php the_company_logo(); ?>
	</div>
	<div class="single-job-listing-company__details">
		<?php
		the_company_name( '<h3 class="single-job-listing-company__name">', '</h3>' );
		the_company_tagline( '<p class="single-job-listing-company__tagline">', '</p>' );

		jobmate_job_listing_job_type();
		?>
		<div class="single-job-listing-company__contact">
			
			<?php
			echo '<div class="job-location-type">';
			if ( get_the_job_location() ) {
				?>
				<div class="location"><i class="far fa-map-marker-alt"></i> <?php the_job_location( false ); ?></div>
				<?php
			}
			echo '</div>';

			the_company_twitter();

			if ( $website = get_the_company_website() ) :
				?>
				<a class="single-job-listing-company__contact--website" href="<?php echo esc_url( $website ); ?>" target="_blank" rel="nofollow"><?php echo wp_kses_post( $website ); ?></a>
			<?php elseif ( ! empty( $company && $website = get_the_company_website_link( $company ) ) ) : ?>
				<a class="single-job-listing-company__contact--website" href="<?php echo esc_url( $website ); ?>" target="_blank" rel="nofollow"><?php echo wp_kses_post( $website ); ?></a>
				<?php
			endif;

			if ( ! empty( $company && $phone = get_the_company_phone( $company, 'company' ) ) ) :
				?>
				<span class="single-job-listing-company__contact--phone"><?php echo wp_kses_post( $phone ); ?></span>
				<?php
			endif;
			if ( $apply = get_the_job_application_method() && isset( $apply->type ) && $apply->type == 'email' ) :
				$application_email = $apply->email;
				?>
				<a class="single-job-listing-company__contact--application-email" href="<?php echo esc_url( 'mailto:' . $application_email ); ?>" target="_blank" rel="nofollow"><?php echo wp_kses_post( $application_email ); ?></a>
			<?php elseif ( ! empty( $company && $email = get_the_company_email( $company ) ) ) : ?>
				<a class="single-job-listing-company__contact--application-email" href="<?php echo esc_url( 'mailto:' . $email ); ?>" target="_blank" rel="nofollow"><?php echo wp_kses_post( $email ); ?></a>
			<?php endif; ?>

			<?php jobmate_display_the_deadline(); ?>

			<div class="date-posted"><?php the_job_publish_date(); ?></div>

			<?php
			$job_salary = the_job_salary( '', '', false );
			if ( ! empty( $job_salary ) ) :
				?>
				<div class="salary"><?php echo esc_html( $job_salary ); ?> </div>
			<?php endif; ?>
			<?php if ( is_position_filled() ) : ?>
				<div class="position-filled"><?php esc_html_e( 'This position has been filled.', 'reign' ); ?></div>
			<?php elseif ( ! candidates_can_apply() && 'preview' !== $post->post_status ) : ?>
				<div class="listing-expired"><?php esc_html_e( 'Applications have closed.', 'reign' ); ?></div>
			<?php endif; ?>

			<?php do_action( 'single_job_listing_meta_end' ); ?>
		</div>
	</div>
	<?php
	if ( class_exists( 'Reign_WP_Job_Manager_Addon' ) ) {
		$layout = reign_job_mate_single_job_layout();

		if ( 'single_job_layout_three' === $layout ) {
			reign_job_manager_single_job_application();
		}
	}
	?>
</div>
