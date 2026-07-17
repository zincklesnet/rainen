<?php
/**
 * BuddyPress - Members/Blogs Registration forms
 *
 * @since 3.0.0
 * @version 8.0.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$bp_pages = get_option( 'bp-pages' );
$register = isset( $bp_pages['register'] ) ? get_permalink( $bp_pages['register'] ) : site_url( 'wp-login.php?action=register&type=internal', 'login_post' );
$register = bp_get_signup_page();
?>

<?php
if ( function_exists( 'youzify' ) ) {
	if ( function_exists( 'youzify_is_membership_system_active' ) ) {
		if ( youzify_is_membership_system_active() ) {
			echo do_shortcode( '[youzify_register]' );
		} else {
			echo '<p class="youzify-error">' . esc_html__( 'The Youzify membership system is currently inactive. Please activate it from the Youzify settings.', 'reign' ) . '</p>';
		}
	}
} else {
	?>
	<?php bp_nouveau_signup_hook( 'before', 'page' ); ?>

	<div id="register-page"class="page register-page">

		<?php bp_nouveau_template_notices(); ?>
		

			<form name="signup_form" id="signup-form" class="standard-form signup-form clearfix" method="post" enctype="multipart/form-data" action="<?php echo esc_url( $register ); ?>">

			<div class="layout-wrap">

			<?php bp_nouveau_signup_hook( 'before', 'account_details' ); ?>

			<div class="register-section default-profile" id="basic-details-section">

				<?php bp_nouveau_signup_form(); ?>

			</div><!-- #basic-details-section -->

			<?php bp_nouveau_signup_hook( 'after', 'account_details' ); ?>

			<?php /***** Extra Profile Details ******/ ?>

			<?php if ( bp_is_active( 'xprofile' ) && bp_nouveau_has_signup_xprofile_fields( true ) ) : ?>

				<?php bp_nouveau_signup_hook( 'before', 'signup_profile' ); ?>

				<div class="register-section extended-profile" id="profile-details-section">
					<?php /* Use the profile field loop to render input fields for the 'base' profile field group */ ?>
					<?php
					while ( bp_profile_groups() ) :
						bp_the_profile_group();
						?>

						<?php
						while ( bp_profile_fields() ) :
							bp_the_profile_field();
							?>

							<div<?php bp_field_css_class( 'editfield' ); ?>>
								<fieldset>

								<?php
								$field_type = bp_xprofile_create_field_type( bp_get_the_profile_field_type() );
								$field_type->edit_field_html();

								bp_nouveau_xprofile_edit_visibilty();
								?>

								</fieldset>
							</div>

						<?php endwhile; ?>

					<input type="hidden" name="signup_profile_field_ids" id="signup_profile_field_ids" value="<?php bp_the_profile_field_ids(); ?>" />

					<?php endwhile; ?>

					<?php bp_nouveau_signup_hook( '', 'signup_profile' ); ?>

				</div><!-- #profile-details-section -->

				<?php bp_nouveau_signup_hook( 'after', 'signup_profile' ); ?>

			<?php endif; ?>

			<?php if ( bp_get_blog_signup_allowed() ) : ?>

				<?php bp_nouveau_signup_hook( 'before', 'blog_details' ); ?>

				<?php /***** Blog Creation Details ******/ ?>

				<div class="register-section blog-details" id="blog-details-section">

					<p><label for="signup_with_blog"><input type="checkbox" name="signup_with_blog" id="signup_with_blog" value="1" <?php checked( (int) bp_get_signup_with_blog_value(), 1 ); ?>/> <?php esc_html_e( "Yes, i'd like to create a new site", 'reign' ); ?></label></p>

					<div id="blog-details"
					<?php
					if ( (int) bp_get_signup_with_blog_value() ) :
						?>
						class="show"<?php endif; ?>>

						<?php bp_nouveau_signup_form( 'blog_details' ); ?>

					</div>

				</div><!-- #blog-details-section -->

				<?php bp_nouveau_signup_hook( 'after', 'blog_details' ); ?>

			<?php endif; ?>

			</div><!-- //.layout-wrap -->

			<?php bp_nouveau_signup_hook( 'custom', 'steps' ); ?>

			<?php if ( bp_signup_requires_privacy_policy_acceptance() ) : ?>
				<?php bp_nouveau_signup_privacy_policy_acceptance_section(); ?>
			<?php endif; ?>

			<?php bp_nouveau_submit_button( 'register' ); ?>

			</form>

	</div>

	<?php
	bp_nouveau_signup_hook( 'after', 'page' );
}
?>