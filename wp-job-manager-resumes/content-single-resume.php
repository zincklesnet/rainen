<?php
/**
 * Content for a single resume.
 *
 * This template can be overridden by copying it to yourtheme/wp-job-manager-resumes/content-single-resume.php.
 *
 * @see         https://wpjobmanager.com/document/template-overrides/
 * @author      Automattic
 * @package     wp-job-manager-resumes
 * @category    Template
 * @version     1.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;
if ( resume_manager_user_can_view_resume( $post->ID ) ) :

	do_action( 'single_resume_before' ); ?>

	<div class="single-resume-inner">
		<?php do_action( 'single_resume_head_before' ); ?>
		<div class="single-resume-head">
			<?php do_action( 'single_resume_head' ); ?>
		</div>
		<?php do_action( 'single_resume_head_after' ); ?>
		<div class="single-resume-content">
			<?php do_action( 'single_resume_content_navbar_before' ); ?>
			<div id="single-resume-content-navbar-tabs" class="single-resume-content-navbar">
				<?php do_action( 'single_resume_content_navbar' ); ?>
			</div>
			<?php do_action( 'single_resume_content_navbar_after' ); ?>
			<div class="single-resume-content_inner">
				<div class="single-resume__content-area">
					<?php do_action( 'single_resume_content_before' ); ?>
					<?php do_action( 'single_resume_content' ); ?>
					<?php do_action( 'single_resume_content_after' ); ?>
				</div>
				<?php do_action( 'single_resume_sidebar_before' ); ?>
				<div class="single-resume__sidebar-area">
					<?php do_action( 'single_resume_sidebar' ); ?>
				</div>
				<?php do_action( 'single_resume_sidebar_after' ); ?>
			</div>
		</div>
	</div>

	<?php
	do_action( 'single_resume_after' );

	else :
		?>

		<?php get_job_manager_template_part( 'access-denied', 'single-resume', 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' ); ?>

<?php endif; ?>
