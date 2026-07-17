<?php
/**
 * Support For WP Job Manager
 *
 * @package reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

/**
 * Jobs
 */
if ( ! function_exists( 'jobmate_wpjm_get_page_id' ) ) {
	/**
	 * Get job lisring page id
	 *
	 * @param  string $page name of page
	 *
	 * @return int
	 *
	 * @since 2.0.0
	 */
	function jobmate_wpjm_get_page_id( $page ) {

		$option_name = '';
		switch ( $page ) {
			case 'jobs':
				$option_name = 'job_manager_jobs_page_id';
				break;
			case 'jobs-dashboard':
				$option_name = 'job_manager_job_dashboard_page_id';
				break;
			case 'post-a-job':
				$option_name = 'job_manager_submit_job_form_page_id';
				break;
		}

		$page_id = 0;

		if ( ! empty( $option_name ) ) {
			$page_id = get_option( $option_name );
		}

		$page_id = apply_filters( 'jobmate_wpjm_get_' . $page . '_page_id', $page_id );
		return $page_id ? absint( $page_id ) : -1;
	}
}

if ( ! function_exists( 'jobmate_jobs_remove_sidebars' ) ) {
	function jobmate_jobs_remove_sidebars() {

		if ( is_page( jobmate_wpjm_get_page_id( 'post-a-job' ) ) ) {
			if ( class_exists( 'Reign_Theme_Structure' ) ) {
				remove_action( 'reign_before_content_section', array( Reign_Theme_Structure::instance(), 'render_left_sidebar_area' ) );
				remove_action( 'reign_after_content_section', array( Reign_Theme_Structure::instance(), 'render_right_sidebar_area' ) );
				remove_action( 'reign_before_content', array( Reign_Theme_Structure::instance(), 'render_page_header' ) );
			}
		}

		if ( is_singular( 'job_listing' ) ) {
			remove_action( 'reign_before_content_section', array( Reign_Theme_Structure::instance(), 'render_left_sidebar_area' ) );
			remove_action( 'reign_after_content_section', array( Reign_Theme_Structure::instance(), 'render_right_sidebar_area' ) );
		}
	}
}

add_action( 'reign_before_page', 'jobmate_jobs_remove_sidebars' );

if ( ! function_exists( 'jobmate_job_listing_job_type' ) ) {
	/**
	 * Get gob listing types
	 *
	 * @param  boolean $linkable If true the type will be clickable
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	function jobmate_job_listing_job_type( $linkable = false ) {
		if ( get_option( 'job_manager_enable_types' ) ) { ?>
			<?php $types = wpjm_get_the_job_types(); ?>
			<?php
			if ( ! empty( $types ) ) :
				echo '<ul class="job-types">';
				foreach ( $types as $type ) :
					?>
						<li class="job-type <?php echo esc_attr( sanitize_title( $type->slug ) ); ?>">
							<?php if ( $linkable ) : ?>
								<a href="<?php echo esc_url( get_term_link( $type ) ); ?>"><?php echo esc_html( $type->name ); ?></a>
								<?php
							else :
								echo esc_html( $type->name );
							endif;
							?>
						</li>
					<?php
					endforeach;
				echo '</ul>';
			endif;
		}
	}
}

if ( ! function_exists( 'reign_job_manager_single_job_application' ) ) {
	/**
	 * Adds apply button on single job page.
	 */
	function reign_job_manager_single_job_application() {

		if ( candidates_can_apply() ) {
			get_job_manager_template( 'job-application.php' );
		}
	}

	add_action( 'single_job_listing_sidebar', 'reign_job_manager_single_job_application' );
}

if ( ! function_exists( 'jobmate_is_wp_job_manager_application_deadline_activated' ) ) {
	/**
	 * Check if WP Job Manager Application Deadline is activated
	 */
	function jobmate_is_wp_job_manager_application_deadline_activated() {
		return class_exists( 'WP_Job_Manager_Application_Deadline' ) ? true : false;
	}
}

if ( ! function_exists( 'jobmate_display_the_deadline' ) ) {
	/**
	 * Show deadline on job pages
	 *
	 * @return string
	 *
	 * @since 2.0.0
	 */
	function jobmate_display_the_deadline() {
		global $post;

		if ( jobmate_is_wp_job_manager_application_deadline_activated() ) {
			$deadline = get_post_meta( $post->ID, '_application_deadline', true );
			if ( $deadline ) {
				$expiring_days = apply_filters( 'job_manager_application_deadline_expiring_days', 2 );
				$expiring      = ( floor( ( time() - strtotime( $deadline ) ) / ( 60 * 60 * 24 ) ) >= $expiring_days );
				$expired       = ( floor( ( time() - strtotime( $deadline ) ) / ( 60 * 60 * 24 ) ) >= 0 );

				if ( is_singular( 'job_listing' ) && $expired ) {
					return;
				}
				$deadline_label = $expired ? esc_html__( 'Closed', 'reign' ) : esc_html__( 'Closes', 'reign' );
				$deadline_date  = date_i18n( esc_html__( 'M j, Y', 'reign' ), strtotime( $deadline ) );
				printf(
					'<div class="application-deadline %1$s %2$s"><label>%3$s:</label> %4$s</div>',
					esc_attr( $expiring ? 'expiring' : '' ),
					esc_attr( $expired ? 'expired' : '' ),
					esc_html( $deadline_label ),
					esc_html( $deadline_date )
				);
			}
		}
	}
}

if ( ! function_exists( 'jobmate_modify_single_job_listing_hooks' ) ) {
	/**
	 * Manage single job listing layout.
	 *
	 * @since 2.0.0
	 */
	function jobmate_modify_single_job_listing_hooks() {

		do_action( 'jobmate_modify_single_job_listing_hooks_before' );

		remove_action( 'single_job_listing_start', 'job_listing_meta_display', 20 );

		do_action( 'jobmate_modify_single_job_listing_hooks_after' );
	}
}

add_action( 'single_job_listing_inner_before', 'jobmate_modify_single_job_listing_hooks', 10 );

if ( ! function_exists( 'jobmate_is_wp_job_manager_bookmark_activated' ) ) {
	/**
	 * Check if WP Job Manager Bookmarks is activated
	 */
	function jobmate_is_wp_job_manager_bookmark_activated() {
		return class_exists( 'WP_Job_Manager_Bookmarks' ) ? true : false;
	}
}

if ( ! function_exists( 'jobmate_modify_wp_job_manager_bookmark_hooks' ) ) {
	/**
	 * Manage bookmark hooks on single job pages.
	 *
	 * @param  string $layout Job page layout
	 *
	 * @since 2.0.0
	 */
	function jobmate_modify_wp_job_manager_bookmark_hooks( $layout ) {
		if ( jobmate_is_wp_job_manager_bookmark_activated() ) {

			global $job_manager_bookmarks;
			$wpjm_bookmark_proirity = 15;

			remove_action( 'single_job_listing_meta_after', array( $job_manager_bookmarks, 'bookmark_form' ) );
			add_action( 'single_job_listing_sidebar', array( $job_manager_bookmarks, 'bookmark_form' ), $wpjm_bookmark_proirity );
		}
	}
}

add_action( 'jobmate_modify_single_job_listing_hooks_before', 'jobmate_modify_wp_job_manager_bookmark_hooks' );

/**
 * Resumes
 */
add_action( 'single_resume_before', 'jobmate_modify_single_resume_layout', 10 );

add_action( 'single_resume_head', 'jobmate_single_resume_head_start', 5 );
add_action( 'single_resume_head', 'jobmate_single_resume_head_left_start', 10 );
add_action( 'single_resume_head', 'jobmate_resume_category', 20 );
add_action( 'single_resume_head', 'jobmate_single_resume_head_left_end', 40 );
add_action( 'single_resume_head', 'jobmate_single_resume_head_center_start', 50 );
add_action( 'single_resume_head', 'jobmate_candidate_info', 60 );
add_action( 'single_resume_head', 'jobmate_single_resume_head_center_end', 70 );
add_action( 'single_resume_head', 'jobmate_single_resume_head_right_start', 80 );
add_action( 'single_resume_head', 'jobmate_candidate_location_published_start', 90 );
add_action( 'single_resume_head', 'jobmate_candidate_location', 100 );
add_action( 'single_resume_head', 'jobmate_candidate_profle_published', 110 );
add_action( 'single_resume_head', 'jobmate_candidate_location_published_end', 120 );
add_action( 'single_resume_head', 'jobmate_the_resume_file', 130 );
add_action( 'single_resume_head', 'jobmate_single_resume_head_right_end', 140 );
add_action( 'single_resume_head', 'jobmate_single_resume_head_end', 999 );

add_action( 'single_resume_content_navbar', 'jobmate_single_candidate_content_navbar_start', 10 );
add_action( 'single_resume_content_navbar', 'jobmate_single_candidate_content_navbar_links', 20 );
add_action( 'single_resume_content_navbar', 'jobmate_single_candidate_content_navbar_end', 30 );

add_action( 'single_resume_content', 'jobmate_candidate_description', 20 );
add_action( 'single_resume_content', 'jobmate_candidate_qualification', 30 );
add_action( 'single_resume_content', 'jobmate_candidate_experience', 40 );
add_action( 'single_resume_content', 'jobmate_candidate_skill', 50 );
add_action( 'single_resume_content', 'jobmate_candidate_video', 70 );

add_action( 'single_resume_sidebar', 'jobmate_single_candidate_contact_form', 30 );

if ( ! function_exists( 'jobmate_modify_single_resume_layout' ) ) {
	/**
	 * Manage single resume template layout
	 */
	function jobmate_modify_single_resume_layout() {

		if ( jobmate_is_wp_job_manager_bookmark_activated() ) {
			global $job_manager_bookmarks;
			remove_action( 'single_resume_start', array( $job_manager_bookmarks, 'bookmark_form' ) );
			add_action( 'single_resume_sidebar', array( $job_manager_bookmarks, 'bookmark_form' ), 15 );
		}
	}
}

if ( ! function_exists( 'jobmate_single_resume_head_start' ) ) {
	/**
	 * Jobmate single header
	 */
	function jobmate_single_resume_head_start() {
		echo '<div class="single-resume-head-inner">';
	}
}

if ( ! function_exists( 'jobmate_single_resume_head_end' ) ) {
	function jobmate_single_resume_head_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'jobmate_single_resume_head_left_start' ) ) {
	function jobmate_single_resume_head_left_start() {
		echo '<div class="single-candidate-head-left">';
	}
}

if ( ! function_exists( 'jobmate_single_resume_head_left_end' ) ) {
	function jobmate_single_resume_head_left_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'jobmate_single_resume_head_center_start' ) ) {
	function jobmate_single_resume_head_center_start() {
		echo '<div class="single-candidate-details">';
	}
}

if ( ! function_exists( 'jobmate_single_resume_head_center_end' ) ) {
	function jobmate_single_resume_head_center_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'jobmate_single_resume_head_right_start' ) ) {
	function jobmate_single_resume_head_right_start() {
		echo '<div class="single-candidate-head-right">';
	}
}

if ( ! function_exists( 'jobmate_single_resume_head_right_end' ) ) {
	function jobmate_single_resume_head_right_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'jobmate_resume_category' ) ) {
	function jobmate_resume_category() {
		global $post;
		$resume_post = get_post( $post );
		if ( 'resume' !== $resume_post->post_type ) {
			return '';
		}

		if ( ! get_option( 'resume_manager_enable_categories' ) ) {
			return '';
		}

		$categories = wp_get_object_terms( $resume_post->ID, 'resume_category' );

		if ( is_wp_error( $categories ) ) {
			return '';
		}

		echo '<ul class="categories">';
		foreach ( $categories as $category ) {
			echo '<li><a href="' . esc_url( get_term_link( $category ) ) . '">' . esc_html( $category->name ) . '</a></li>';
		}
		echo '</ul>';
	}
}

if ( ! function_exists( 'jobmate_single_candidate_content_navbar_links' ) ) {
	function jobmate_single_candidate_content_navbar_links() {
		global $post;
		$skills = wp_get_object_terms( $post->ID, 'resume_skill', array( 'fields' => 'names' ) );
		if ( ! empty( get_the_content() || get_post_meta( $post->ID, '_candidate_education', true ) || get_post_meta( $post->ID, '_candidate_experience', true ) || ( $skills && is_array( $skills ) ) || get_the_candidate_video() ) ) {
			echo '<ul class="nav navbar-links">';
			if ( ! empty( get_the_content() ) ) :
				?>
					<li class="nav-item navbar-link"><a class="nav-link" href="#candidate-description"><i class="far fa-briefcase"></i><?php esc_html_e( 'Candidate About', 'reign' ); ?></a></li>
				<?php
				endif;
			if ( ! empty( get_post_meta( $post->ID, '_candidate_education', true ) ) ) :
				?>
					<li class="nav-item navbar-link"><a class="nav-link" href="#candidate-qualification"><i class="far fa-graduation-cap"></i><?php esc_html_e( 'Education', 'reign' ); ?></a></li>
				<?php
				endif;
			if ( ! empty( get_post_meta( $post->ID, '_candidate_experience', true ) ) ) :
				?>
					<li class="nav-item navbar-link"><a class="nav-link" href="#candidate-experience"><i class="far fa-chart-area"></i><?php esc_html_e( 'Work Experience', 'reign' ); ?></a></li>
				<?php
				endif;
			if ( $skills && is_array( $skills ) ) :
				?>
					<li class="nav-item navbar-link"><a class="nav-link" href="#candidate-skills"><i class="far fa-lightbulb"></i><?php esc_html_e( 'Professional Skills', 'reign' ); ?></a></li>
				<?php
				endif;
			if ( ! empty( get_the_candidate_video() ) ) :
				?>
					<li class="nav-item navbar-link"><a class="nav-link" href="#candidate-video"><i class="far fa-file-video"></i><?php esc_html_e( 'Candidate Video', 'reign' ); ?></a></li>
				<?php
				endif;
			echo '</ul>';
		}
	}
}


if ( ! function_exists( 'jobmate_template_candidate_detail_start' ) ) {
	function jobmate_template_candidate_detail_start() {
		echo '<div class="candidate-details">';
	}
}

if ( ! function_exists( 'jobmate_template_candidate_detail_end' ) ) {
	function jobmate_template_candidate_detail_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'jobmate_single_candidate_content_navbar_start' ) ) {
	function jobmate_single_candidate_content_navbar_start() {
		echo '<div class="single-resume-content-navbar-inner jobmate-stick-this">';
	}
}

if ( ! function_exists( 'jobmate_single_candidate_content_navbar_end' ) ) {
	function jobmate_single_candidate_content_navbar_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'jobmate_candidate_info' ) ) {
	function jobmate_candidate_info() {
		global $post;
		?>
		<div class="candidate-info">
			<div class="candidate-image">
				<?php the_candidate_photo(); ?>
			</div>
			<h4 class="candidate-name"><?php echo esc_html( apply_filters( 'jobmate_candidate_name', get_the_title() ) ); ?></h4>
			<p class="job-title"><?php echo esc_html( apply_filters( 'jobmate_candidate_title', get_the_candidate_title() ) ); ?></p>
			<?php
			$candidate_email = get_post_meta( $post->ID, '_candidate_email', true );
			if ( $candidate_email ) :
				?>
				<p class="candidate-e-mail"><a href="mailto:<?php echo esc_attr( antispambot( $candidate_email ) ); ?>"><i class="far fa-envelope"></i><?php echo esc_html( antispambot( $candidate_email ) ); ?></a></p>
				<?php
			endif;
			jobmate_the_resume_links();
			?>
		</div>
		<?php
	}
}


if ( ! function_exists( 'jobmate_the_resume_links' ) ) {
	function jobmate_the_resume_links() {
		global $post;
		if ( resume_has_links() ) :
			?>
			<ul class="resume-links">
				<?php foreach ( get_resume_links() as $link ) : ?>
					<?php
					get_job_manager_template(
						'content-resume-link.php',
						array(
							'post' => $post,
							'link' => $link,
						),
						'wp-job-manager-resumes',
						RESUME_MANAGER_PLUGIN_DIR . '/templates/'
					);
					?>
				<?php endforeach; ?>
			</ul>
			<?php
		endif;
	}
}


if ( ! function_exists( 'jobmate_candidate_location_published_start' ) ) {
	function jobmate_candidate_location_published_start() {
		global $post;
		if ( ! empty( get_the_candidate_location() ) ) :
			echo '<div class="candidate-location-published">';
		endif;
	}
}

if ( ! function_exists( 'jobmate_candidate_location' ) ) {
	function jobmate_candidate_location() {
		global $post;
		if ( ! empty( get_the_candidate_location() ) ) :
			?>
			<div class="location"><i class="far fa-map-marker-alt"></i><?php the_candidate_location(); ?></div>
			<?php
		endif;
	}
}

if ( ! function_exists( 'jobmate_candidate_profle_published' ) ) {
	function jobmate_candidate_profle_published() {
		global $post;
		?>
		<div class="published-date"><i class="far fa-history"></i><?php printf( '%s %s', esc_html__( 'Member Since ', 'reign' ), get_the_date( 'Y' ) ); ?></div>
		<?php
	}
}

if ( ! function_exists( 'jobmate_candidate_location_published_end' ) ) {
	function jobmate_candidate_location_published_end() {
		global $post;
		if ( ! empty( get_the_candidate_location() ) ) :
			echo '</div>';
		endif;
	}
}

if ( ! function_exists( 'jobmate_the_resume_file' ) ) {
	function jobmate_the_resume_file() {
		global $post;
		$resume_files = get_post_meta( $post->ID, '_resume_file', true );
		if ( ! empty( $resume_files ) && apply_filters( 'resume_manager_user_can_download_resume_file', true, $post->ID ) ) {
			echo '<div class="candidate-resume">';
			$resume_files = is_array( $resume_files ) ? $resume_files : array( $resume_files );
			foreach ( $resume_files as $key => $resume_file ) :
				?>
				<a rel="nofollow" target="_blank" href="<?php echo esc_url( get_resume_file_download_url( null, $key ) ); ?>"><?php echo esc_html__( 'Download CV', 'reign' ); ?><i class="far fa-file-download"></i></a>
				<?php
			endforeach;
			echo '</div>';
		}
	}
}


if ( ! function_exists( 'jobmate_single_candidate_content_area_start' ) ) {
	function jobmate_single_candidate_content_area_start() {
		echo '<div class="single-resume__content-area">';
	}
}

if ( ! function_exists( 'jobmate_single_candidate_content_area_end' ) ) {
	function jobmate_single_candidate_content_area_end() {
		echo '</div>';
	}
}

if ( ! function_exists( 'jobmate_candidate_description' ) ) {
	function jobmate_candidate_description() {
		if ( ! empty( get_the_content() ) ) :
			?>
			<div id="candidate-description" class="candidate-description">
				<h2><?php esc_html_e( 'Candidates About', 'reign' ); ?></h2>
				<?php echo wp_kses_post( apply_filters( 'the_resume_description', get_the_content() ) ); ?>
			</div>
			<?php
		endif;
	}
}

if ( ! function_exists( 'jobmate_candidate_qualification' ) ) {
	function jobmate_candidate_qualification() {
		global $post;
		$items = get_post_meta( $post->ID, '_candidate_education', true );
		if ( $items ) :
			?>
			<div id="candidate-qualification" class="candidate-qualification">
				<h2><?php esc_html_e( 'Education', 'reign' ); ?></h2>
				<dl class="resume-manager-education">
				<?php
				foreach ( $items as $item ) :
					?>

						<dt>
							<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
							<div class="timeline-title"><?php printf( '%s %s', '<strong class="location">' . esc_html( $item['location'] ) . '</strong>', '<span class="qualification">' . esc_html( $item['qualification'] ) . '</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Values escaped individually above. ?></div>
						</dt>
						<dd>
							<?php echo wp_kses_post( wpautop( wptexturize( $item['notes'] ) ) ); ?>
						</dd>

					<?php
					endforeach;
				?>
				</dl>
			</div>
			<?php
		endif;
	}
}

if ( ! function_exists( 'jobmate_candidate_experience' ) ) {
	function jobmate_candidate_experience() {
		global $post;
		$items = get_post_meta( $post->ID, '_candidate_experience', true );
		if ( $items ) :
			?>
			<div id="candidate-experience" class="candidate-experience">
				<h2><?php esc_html_e( 'Work & Experience', 'reign' ); ?></h2>
				<dl class="resume-manager-experience">
				<?php
				foreach ( $items as $item ) :
					?>

						<dt>
							<div class="timeline-title"><?php printf( '%s %s', '<strong class="job_title">' . esc_html( $item['job_title'] ) . '</strong>', '<span class="employer">' . esc_html( $item['employer'] ) . '</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Values escaped individually above. ?></div>
							<small class="date"><?php echo esc_html( $item['date'] ); ?></small>
						</dt>
						<dd>
							<?php echo wp_kses_post( wpautop( wptexturize( $item['notes'] ) ) ); ?>
						</dd>

					<?php
					endforeach;
				?>
				</dl>
			</div>
			<?php
		endif;
	}
}

if ( ! function_exists( 'jobmate_candidate_skill' ) ) {
	function jobmate_candidate_skill() {
		global $post;
		$skills = wp_get_object_terms( $post->ID, 'resume_skill', array( 'fields' => 'names' ) );
		if ( $skills && is_array( $skills ) ) :
			?>
			<div id="candidate-skills" class="candidate-skills">
				<h2><?php esc_html_e( 'Professional Skills', 'reign' ); ?></h2>
				<ul class="resume-manager-skills">
					<?php
					foreach ( $skills as $skill ) {
						echo '<li>' . esc_html( $skill ) . '</li>';
					}
					?>
				</ul>
			</div>
			<?php
		endif;
	}
}

if ( ! function_exists( 'jobmate_candidate_contact' ) ) {
	function jobmate_candidate_contact() {
		global $post;
		get_job_manager_template( 'contact-details.php', array( 'post' => $post ), 'wp-job-manager-resumes', RESUME_MANAGER_PLUGIN_DIR . '/templates/' );
	}
}

if ( ! function_exists( 'jobmate_candidate_video' ) ) {
	function jobmate_candidate_video() {
		if ( ! empty( get_the_candidate_video() ) ) :
			?>
			<div id="candidate-video" class="candidate-video">
				<h2><?php esc_html_e( 'Candidates Video', 'reign' ); ?></h2>
				<?php
				// the_candidate_video() echoes the embed markup itself; capture and allow oEmbed/iframe HTML.
				ob_start();
				the_candidate_video();
				$candidate_video_html = ob_get_clean();
				$video_allowed_html   = array_merge(
					wp_kses_allowed_html( 'post' ),
					array(
						'iframe' => array(
							'src'             => true,
							'width'           => true,
							'height'          => true,
							'frameborder'     => true,
							'allow'           => true,
							'allowfullscreen' => true,
							'title'           => true,
							'class'           => true,
						),
					)
				);
				echo wp_kses( apply_filters( 'the_candidate_video', $candidate_video_html ), $video_allowed_html );
				?>
			</div>
			<?php
		endif;
	}
}

if ( ! function_exists( 'jobmate_single_candidate_contact_form' ) ) {
	function jobmate_single_candidate_contact_form() {
		$form_id = get_option( 'resume_manager_single_resume_contact_form' );
		if ( ! empty( $form_id ) ) {
			if ( function_exists( 'wpforms' ) ) {
				$shortcode = sprintf( '[wpforms id="%1$d" title="%2$s" description="false"]', $form_id, get_the_title( $form_id ) );
				echo '<div class="contact-form contact-candidate">';
				echo '<h5 class="contact-form-title">' . esc_html__( 'Contact', 'reign' ) . '</h5>';
				echo '<div class="contact-candidate-inner">';
				echo do_shortcode( $shortcode );
				echo '</div>';
				echo '</div>';
			} elseif ( function_exists( 'wpcf7' ) ) {
				$shortcode = sprintf( '[contact-form-7 id="%1$d" title="%2$s"]', $form_id, get_the_title( $form_id ) );
				echo '<div class="contact-form contact-candidate">';
				echo '<h5 class="contact-form-title">' . esc_html__( 'Contact', 'reign' ) . '</h5>';
				echo '<div class="contact-candidate-inner">';
				echo do_shortcode( $shortcode );
				echo '</div>';
				echo '</div>';
			}
		} else {
			jobmate_candidate_contact();
		}
	}
}

if ( ! function_exists( 'jobmate_resume_remove_sidebars' ) ) {
	/**
	 * Remove sidebars from the JobMate single resume page.
	 */
	function jobmate_resume_remove_sidebars() {

		global $post;

		if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'submit_resume_form' ) ) {
			if ( class_exists( 'Reign_Theme_Structure' ) ) {
				remove_action( 'reign_before_content_section', array( Reign_Theme_Structure::instance(), 'render_left_sidebar_area' ) );
				remove_action( 'reign_after_content_section', array( Reign_Theme_Structure::instance(), 'render_right_sidebar_area' ) );
				remove_action( 'reign_before_content', array( Reign_Theme_Structure::instance(), 'render_page_header' ) );
			}
		}

		if ( is_singular( 'resume' ) ) {
			remove_action( 'reign_before_content_section', array( Reign_Theme_Structure::instance(), 'render_left_sidebar_area' ) );
			remove_action( 'reign_after_content_section', array( Reign_Theme_Structure::instance(), 'render_right_sidebar_area' ) );
		}
	}
}

add_action( 'reign_before_page', 'jobmate_resume_remove_sidebars' );