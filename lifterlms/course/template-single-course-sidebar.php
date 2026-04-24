<?php
/**
 * Single course sidebar
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit;

$is_enrolled        = false;
$current_user_id    = get_current_user_id();
$course_id          = get_the_ID();
$course_video_embed = get_post_meta( $course_id, '_llms_video_embed', true );
$course_audio_embed = get_post_meta( $course_id, '_llms_audio_embed', true );
$file_info          = pathinfo( $course_video_embed );

$product = class_exists( 'LLMS_Product' ) ? new LLMS_Product( $course_id ) : null;
$course  = class_exists( 'LLMS_Course' ) ? new LLMS_Course( get_post() ) : null;
if ( empty( $course ) ) {
	return;
}
?>

<aside class="rlla-single-course-sidebar rlla-preview-wrap widget-area">
	<div class="widget-area-inner">
		<div class="widget rlla-enroll-widget">
			<div class="rlla-enroll-widget flex-1 push-right">
				<div class="rlla-course-preview-wrap rlla-thumbnail-preview">
					<?php
					if ( ! empty( $course_video_embed ) ) {
						?>
						<div class="rlla-preview-course-link-wrap">
							<div class="thumbnail-container">
								<div class="rlla-course-video-overlay">
									<div>
										<span class="rlla-course-play-btn-wrapper"><span class="rlla-course-play-btn"></span></span>
										<div><?php esc_html_e( 'Preview This Course', 'reign' ); ?></div>
									</div>
								</div>
								<?php
								if ( has_post_thumbnail() ) {
									the_post_thumbnail();
								}
								?>
							</div>
						</div>
						<?php
					} else {
						?>
						<div class="rlla-preview-course-link-wrap">
							<div class="thumbnail-container">
								<?php
								if ( has_post_thumbnail() ) {
									the_post_thumbnail( 'full' );
								}
								?>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			
			<div class="rlla-course-preview-content">
				

				<div class="lifterlms_pricing_button">
					<?php if ( $product->get_access_plans() && ! llms_is_user_enrolled( $current_user_id, $course_id ) && has_block( 'llms/pricing-table' ) ) { ?>

						<?php if ( 'yes' === $course->get( 'enrollment_period' ) ) { ?>
							<div class="llms-notice llms-notice---sidebar">
								<?php esc_html_e( 'Enrolled.', 'reign' ); ?>
							</div>
						<?php } elseif ( ! $course->has_capacity() ) { ?>
							<div class="llms-notice llms-error llms-notice---sidebar">
								<?php esc_html_e( 'Enrollment has closed because the maximum number of allowed students has been reached.', 'reign' ); ?>
							</div>
						<?php } else { ?>
							<a href="#" class="button llms-button-action link-to-llms-access-plans"><?php esc_html_e( 'See Plans', 'reign' ); ?></a>
						<?php } ?>
					<?php } ?>
				</div>

				<?php
				$course_length     = $course->get( 'length' );
				$course_difficulty = $course->get_difficulty();
				$course_tracks     = get_the_term_list( get_the_ID(), 'course_track' );
				$course_cats       = get_the_term_list( get_the_ID(), 'course_cat' );
				$course_tags       = get_the_term_list( get_the_ID(), 'course_tag' );

				if ( ! empty( $course_length ) || ! empty( $course_difficulty ) || ! empty( $course_tracks ) || ! empty( $course_cats ) || ! empty( $course_tags ) ) {
					?>
					<div class="lifterlms_course_information">
						<h3>
						<?php
							esc_html_e( 'Course Information', 'reign' );
						?>
						</h3>
						<div class="llms-meta-info">
						<?php
						if ( ! empty( $course_length ) ) {
							lifterlms_template_single_length();
						}

						if ( ! empty( $course_difficulty ) ) {
							$terms = get_the_terms( get_the_ID(), 'course_difficulty' );
							$term  = wp_list_pluck( $terms, 'name' );
							?>
							<div class="llms-meta llms-difficulty">
								<p>
								<?php
								/* translators: %s: Terms.  */
								printf( wp_kses_post( __( 'Difficulty: <span class="difficulty">%s</span>', 'reign' ) ), implode( ', ', $term ) );//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
								</p>
							</div>
							<?php
						}

						if ( ! empty( $course_tracks ) ) {
							lifterlms_template_single_course_tracks();
						}

						if ( ! empty( $course_cats ) ) {
							lifterlms_template_single_course_categories();
						}

						if ( ! empty( $course_tags ) ) {
							lifterlms_template_single_course_tags();
						}
						?>
						</div>
					</div>
					<?php
				}
				?>
				<?php
				$course        = class_exists( 'LLMS_Course' ) ? new LLMS_Course( get_the_ID() ) : null;
				$lessons       = $course ? $course->get_lessons( 'ids' ) : array();
				$lessons_count = count( $lessons );

				$quizzes       = $course ? $course->get_quizzes() : array();
				$quizzes_count = count( $quizzes );

				$students_count = $course ? $course->get_student_count() : 0;

				if ( class_exists( 'Reign_LifterLMS_Addon' ) ) {
					$review_count = get_rlla_course_review_count( get_the_ID() );
					$certificate  = rlla_is_certificate_associated( get_the_ID() );
				}

				if ( function_exists( 'llms_lesson_has_assignment' ) ) {
					$assignment = rlla_is_assignment_associated( get_the_ID() );
				}

				echo '<div class="rlla-tab-course-info">';
				echo '<h3 class="title">' . esc_html__( 'Course Features', 'reign' ) . '</h3>';

				$course_features['lessons'] = array(
					'slug'  => 'lessons',
					'label' => __( 'Lessons', 'reign' ),
					'value' => $lessons_count,
					'icon'  => 'far fa-file-alt',
				);

				$course_features['quizzes'] = array(
					'slug'  => 'quizzes',
					'label' => __( 'Quizzes', 'reign' ),
					'value' => $quizzes_count,
					'icon'  => 'far fa-puzzle-piece',
				);

				$course_features['students'] = array(
					'slug'  => 'students',
					'label' => __( 'Students', 'reign' ),
					'value' => $students_count,
					'icon'  => 'far fa-users',
				);

				if ( class_exists( 'Reign_LifterLMS_Addon' ) ) {
					$course_features['reviews'] = array(
						'slug'  => 'reviews',
						'label' => __( 'Reviews', 'reign' ),
						'value' => $review_count,
						'icon'  => 'far fa-comment',
					);

					$course_features['certificate'] = array(
						'slug'  => 'certificate',
						'label' => __( 'Certificate', 'reign' ),
						'value' => $certificate,
						'icon'  => 'far fa-graduation-cap',
					);
				}

				if ( isset( $assignment ) ) {
					$course_features['assignment'] = array(
						'slug'  => 'assignment',
						'label' => __( 'Assignment', 'reign' ),
						'value' => $assignment,
						'icon'  => 'far fa-edit',
					);
				}

				$course_features = apply_filters( 'rlla_modify_course_features_in_tab', $course_features );

				echo '<ul>';
				foreach ( $course_features as $course_feature ) {
					?>
					<li class="<?php echo esc_attr( $course_feature['slug'] ); ?>">
						<i class="<?php echo esc_attr( $course_feature['icon'] ); ?>"></i>
						<span class="rlla-course-feature-label"><?php echo esc_html( $course_feature['label'] ); ?></span>
						<span class="rlla-course-feature-value"><?php echo esc_html( $course_feature['value'] ); ?></span>
					</li>
					<?php
				}
				echo '</ul>';
				echo '</div>';
				?>
			</div>
		</div>
		<?php
		if ( is_active_sidebar( 'llms_course_widgets_side' ) ) {
			?>
			<ul class="lifter-sidebar-widgets">
				<?php dynamic_sidebar( 'llms_course_widgets_side' ); ?>
			</ul>
			<?php
		}
		?>
	</div>
</aside>
