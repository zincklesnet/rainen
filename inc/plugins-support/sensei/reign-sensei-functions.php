<?php
/**
 * Support For Sensei LMS
 *
 * @package reign
 */

if ( ! function_exists( 'reign_sensei_single_course_header' ) ) {
	/**
	 * Displays the custom header on Sensei single course pages.
	 *
	 * @return void
	 */
	function reign_sensei_single_course_header() {

		if ( get_post_type() === 'course' && is_single() ) {
			remove_action( 'reign_post_content_begins', array( Reign_Theme_Structure::instance(), 'render_post_meta_section' ) );

			$breadcrumb = get_theme_mod( 'reign_site_enable_breadcrumb', true );
			$author_id  = array( get_post_field( 'post_author', get_the_ID() ) );
			$course_id  = get_the_ID();
			?>
			<div class="sensei-single-course-header">
				<div class="container">
					<div class="sensei-single-course-header-inner-wrap">
						<?php if ( $breadcrumb && function_exists( 'reign_breadcrumbs' ) ) : ?>
							<div class="lm-breadcrumbs-wrapper">
								<?php reign_breadcrumbs(); ?>
							</div>
						<?php endif; ?>
						<h2 class="entry-title"><?php the_title(); ?></h2>
						<p class="course-header-short-description">
							<?php the_excerpt(); ?>
						</p>
						<div class="sensei-course-info">

							<!-- Course teacher -->
							<div class="sensei-course-teacher">
								<?php
								if ( ! empty( $author_id ) ) {
									$teacher_image = '';
									$teacher_name  = '';
									$i             = 0;

									remove_filter( 'author_link', 'wpforo_change_author_default_page' );
									foreach ( $author_id as $teacher_id ) {
										$author_avatar_url = get_avatar_url( $teacher_id );
										$author_url        = apply_filters( 'lm_filter_course_author_url', get_author_posts_url( $teacher_id ), $teacher_id );
										$first_name        = get_the_author_meta( 'user_firstname', $teacher_id );
										$last_name         = get_the_author_meta( 'user_lastname', $teacher_id );
										$author_name       = get_the_author_meta( 'display_name', $teacher_id );
										if ( ! empty( $first_name ) && ! empty( $last_name ) && $author_name == '' ) {
											$author_name = $first_name . ' ' . $last_name;
										}
										if ( $i < 3 ) {
											$teacher_image .= '<img alt="teacher avatar" src="' . $author_avatar_url . '" class="lm-author-avatar" width="40" height="40">';
										}
										$teacher_name .= '<a href="' . $author_url . '" target="_blank">' . $author_name . '</a>, ';
										++$i;
									}
									?>
									<div class="teacher-avatar">
										<?php echo wp_kses_post( $teacher_image ); ?>
									</div>
									<div class="teacher-name">
										<?php echo wp_kses_post( substr( $teacher_name, 0, -2 ) ); ?>
									</div>
									<?php
								}
								?>
							</div>
							
							<!-- Last updated -->
							<div class="last-update-date">
								<span class="last-update-date_icon">
									<i class="far fa-clock"></i>
								</span>
								<span><?php printf( esc_html__( 'Last updated on %s', 'reign' ), the_modified_date( '', '', '', false ) ); ?> </span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
		}
	}

	add_action( 'reign_before_content', 'reign_sensei_single_course_header' );
}

if ( ! function_exists( 'reign_sensei_single_course_default_data' ) ) {
	/**
	 * Sets up default data for a single Sensei course.
	 *
	 * @return void
	 */
	function reign_sensei_single_course_default_data() {
		if ( get_post_type() === 'course' && is_single() ) {
			$course_id         = get_the_ID();
			$students_enrolled = Sensei_Course_Enrolment::get_course_instance( $course_id )->get_enrolled_user_ids();
			$course_info       = array();

			$lesson_count           = Sensei()->course->course_lesson_count( absint( $course_id ) );
			$course_info['lessons'] = $lesson_count;

			$course_quizzes = array();
			$lesson_ids     = Sensei()->course->course_lessons( $course_id, 'any', 'ids' );
			if ( ! empty( $lesson_ids ) ) {
				foreach ( $lesson_ids as $lesson_id ) {
					$has_questions = Sensei_Lesson::lesson_quiz_has_questions( $lesson_id );
					if ( $has_questions ) {
						$quiz_id          = Sensei()->lesson->lesson_quizzes( $lesson_id );
						$course_quizzes[] = $quiz_id;
					}
				}
			}
			$course_info['quizzes'] = count( $course_quizzes );

			$certificate = get_post_meta( $course_id, '_course_certificate_template', true );
			if ( $certificate ) {
				$course_info['certificate'] = esc_html__( 'Yes', 'reign' );
			} else {
				$course_info['certificate'] = esc_html__( 'No', 'reign' );
			}
			$course_info['students'] = count( $students_enrolled );

			$course_features = array(
				'lessons'     => array(
					'slug'  => 'lessons',
					'label' => esc_html__( 'Lessons', 'reign' ),
					'value' => $course_info['lessons'],
					'icon'  => 'far fa-file-alt',
				),
				'quizzes'     => array(
					'slug'  => 'quizzes',
					'label' => esc_html__( 'Quizzes', 'reign' ),
					'value' => $course_info['quizzes'],
					'icon'  => 'far fa-puzzle-piece',
				),
				'students'    => array(
					'slug'  => 'students',
					'label' => esc_html__( 'Students', 'reign' ),
					'value' => $course_info['students'],
					'icon'  => 'far fa-users',
				),
				'certificate' => array(
					'slug'  => 'certificate',
					'label' => esc_html__( 'Certificate', 'reign' ),
					'value' => $course_info['certificate'],
					'icon'  => 'far fa-graduation-cap',
				),
			);
			$course_features = apply_filters( 'learnmate_modify_course_features_in_tab', $course_features );
			?>

			<aside id="reign-sidebar-right" class="widget-area sensei-course-widget" role="complementary">
				<div class="widget-area-inner">
					<div class="sensei-course-widget-wrap">
						<div class="lm-course-thumbnail 
						<?php
						if ( ! has_post_thumbnail( $course_id ) ) :
							echo 'lm-no-course-thumbnail';
						endif;
						?>
						">
						<?php
						if ( has_post_thumbnail( $course_id ) ) {
							echo get_the_post_thumbnail( $course_id );
						}
						?>
						</div>
						<div class="lm-tab-course-info">
							<h3 class="title"><?php esc_html_e( 'Course Features', 'reign' ); ?></h3>
							<ul>
								<?php foreach ( $course_features as $course_feature ) { ?>
									<li class="<?php echo esc_attr( $course_feature['slug'] ); ?>">
										<i class="<?php echo esc_attr( $course_feature['icon'] ); ?>"></i>
										<span class="lm-course-feature-label"><?php echo esc_html( $course_feature['label'] ); ?></span>
										<span class="lm-course-feature-value"><?php echo esc_html( $course_feature['value'] ); ?></span>
									</li>
								<?php } ?>
							</ul>
						</div>
						
					</div>
				</div>
			</aside>
			<?php
		}
	}

	add_action( 'reign_after_content_section', 'reign_sensei_single_course_default_data', 5 );
}

if ( ! function_exists( 'remove_reign_sidebars_on_course_page' ) ) {
	add_action( 'template_redirect', 'remove_reign_sidebars_on_course_page' );

	/**
	 * Remove Sidebar
	 */
	function remove_reign_sidebars_on_course_page() {
		if ( class_exists( 'Sensei_Main' ) && is_singular( 'course' ) ) {
			remove_action( 'reign_before_content_section', array( Reign_Theme_Structure::instance(), 'render_left_sidebar_area' ) );
			remove_action( 'reign_after_content_section', array( Reign_Theme_Structure::instance(), 'render_right_sidebar_area' ) );
		}
	}
}
