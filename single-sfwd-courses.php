<?php
/**
 * The template for displaying single course content
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit;

global $wbtm_reign_settings;

get_header();
?>

<div class="content-wrapper">
<?php
while ( have_posts() ) :
	the_post();

	get_template_part( 'template-parts/content', get_post_format() );

	$args = array(
		'prev_text' => '<span class="rg-next-prev">' . __( 'Previous', 'reign' ) . '	</span><span class="nav-title">%title</span>',
		'next_text' => '<span class="rg-next-prev">' . __( 'Next', 'reign' ) . '</span><span class="nav-title">%title</span>',
	);
	the_post_navigation( $args );

	do_action( 'reign_single_post_comment_section' );

	endwhile; // End of the loop.
?>
</div>
<?php // Always show Udemy layout sidebar. ?>
<aside id="reign-sidebar-right" class="widget-area learndash-course-widget" role="complementary">
	<div class="widget-area-inner">
		<div class="learndash-course-widget-wrap">
		<?php
		$course_info = array();
		$course_id   = learndash_get_course_id( get_the_ID() );
		$course      = get_post( $course_id );

		$rla_ccf_enable   = get_post_meta( $course_id, 'rla_ccf_enable', true );
		$rla_ccf_features = get_post_meta( $course_id, 'rla_ccf_features', true );
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
		} else {
			$user_id = 0;
		}

		$lessons                = learndash_get_course_lessons_list( $course );
		$course_info['lessons'] = count( $lessons );

		$topics_count  = 0;
		$quizzes_count = 0;
		foreach ( $lessons as $lesson_index => $lesson ) {
			$topics = learndash_get_topic_list( $lesson['post']->ID, $course_id );
			if ( $topics ) {
				$topics_count += count( $topics );
			}
			$lesson_quiz_list = learndash_get_lesson_quiz_list( $lesson['post']->ID, $user_id, $course_id );
			$quizzes_count   += count( $lesson_quiz_list );
		}
		$course_info['topics'] = $topics_count;

		$quizzes                = learndash_get_course_quiz_list( $course );
		$course_info['quizzes'] = $quizzes_count + count( $quizzes );
		$lms_veriosn            = version_compare( LEARNDASH_VERSION, '2.6.4' );

		if ( $lms_veriosn >= 0 ) {
			$certificate = learndash_get_course_meta_setting( $course_id, 'certificate' );
		} else {
			$certificate = get_course_meta_setting( $course_id, 'certificate' );
		}
		if ( $certificate ) {
			$course_info['certificate'] = esc_html__( 'Yes', 'reign' );
		} else {
			$course_info['certificate'] = esc_html__( 'No', 'reign' );
		}

		// Student count and avatars removed.

		$course_info['assignment'] = esc_html__( 'No', 'reign' );
		foreach ( $lessons as $key => $lesson ) {
			$course_step_post = get_post( $lesson['post']->ID );
			$post_settings    = learndash_get_setting( $course_step_post );
			if ( isset( $post_settings['lesson_assignment_upload'] ) && ( 'on' === $post_settings['lesson_assignment_upload'] ) ) {
				$course_info['assignment'] = esc_html__( 'Yes', 'reign' );
				break;
			}
		}

		$_learndash_course_grid_video_embed_code = get_post_meta( $course_id, '_learndash_course_grid_video_embed_code', true );
		if ( '' !== $_learndash_course_grid_video_embed_code ) :
			echo '<div class="lm-course-thumbnail">';
			echo wp_oembed_get( $_learndash_course_grid_video_embed_code ); //phpcs:ignore
			echo '</div>';
		elseif ( has_post_thumbnail( $course_id ) ) :
				echo '<div class="lm-course-thumbnail">';
				echo get_the_post_thumbnail( $course_id );
				echo '</div>';
			else :
				echo '<div class="lm-course-thumbnail">';
				echo get_reign_ld_default_course_img_html(); //phpcs:ignore
				echo '</div>';
		endif;
			// Student avatars section removed.

			/**
			 * Course info bar
			 */
			learndash_get_template_part(
				'modules/infobar.php',
				array(
					'context'       => 'course',
					'course_id'     => $course_id,
					'user_id'       => $user_id,
					'has_access'    => sfwd_lms_has_access( $course_id, $user_id ),
					'course_status' => learndash_course_status( $course_id, $user_id ),
					'post'          => $post,
				),
				true
			);

			echo do_shortcode( '[ld_course_resume course_id ="' . $course_id . '" user_id ="' . $user_id . '" label="' . esc_html__( 'Continue', 'reign' ) . '"]' );


			/* translators: %s: Course label. */
			$course_features_label = sprintf( esc_html_x( '%s Features', 'Course Features  Label', 'reign' ), LearnDash_Custom_Label::get_label( 'course' ) );

			echo '<div class="lm-tab-course-info">';
			echo '<h3 class="title">' . esc_html( $course_features_label ) . '</h3>';
			$course_features = array(
				'lessons' => array(
					'slug'  => 'lessons',
					'label' => LearnDash_Custom_Label::get_label( 'lessons' ),
					'value' => $course_info['lessons'],
					'icon'  => 'far fa-file-alt',
				),
				'topics'  => array(
					'slug'  => 'topics',
					'label' => LearnDash_Custom_Label::get_label( 'topics' ),
					'value' => $course_info['topics'],
					'icon'  => 'far fa-bookmark',
				),
				'quizzes' => array(
					'slug'  => 'quizzes',
					'label' => LearnDash_Custom_Label::get_label( 'quizzes' ),
					'value' => $course_info['quizzes'],
					'icon'  => 'far fa-puzzle-piece',
				),
			);

			// Students count removed from course features.

			// Add the remaining elements.
			$course_features = array_merge(
				$course_features,
				array(
					'certificate' => array(
						'slug'  => 'certificate',
						'label' => esc_html__( 'Certificate', 'reign' ),
						'value' => $course_info['certificate'],
						'icon'  => 'far fa-graduation-cap',
					),
					'assignment'  => array(
						'slug'  => 'assignment',
						'label' => esc_html__( 'Assignment', 'reign' ),
						'value' => $course_info['assignment'],
						'icon'  => 'far fa-edit',
					),
				)
			);

			$course_features = apply_filters( 'learnmate_modify_course_features_in_tab', $course_features );
			$features_icon   = isset( $rla_ccf_features['icon'] ) ? $rla_ccf_features['icon'] : '';
			if ( ! empty( $features_icon ) ) {
				$count_value = count( $features_icon );
			} else {
				$count_value = '';
			}
			echo '<ul>';

			if ( 'yes' === $rla_ccf_enable ) {
				for ( $i = 0; $i < $count_value; $i++ ) {
					?>
				<li class="<?php echo ( isset( $course_feature['slug'] ) ) ? esc_attr( $course_feature['slug'] ) : ''; ?>">
					<i class="<?php echo esc_attr( $rla_ccf_features['icon'][ $i ] ); ?>"></i>
					<span class="lm-course-feature-value"><?php echo esc_html( $rla_ccf_features['text'][ $i ] ); ?></span>
				</li>
					<?php
				}
			} else {
				foreach ( $course_features as $course_feature ) {
					?>
				<li class="<?php echo esc_attr( $course_feature['slug'] ); ?>">
					<i class="<?php echo esc_attr( $course_feature['icon'] ); ?>"></i>
					<span class="lm-course-feature-label"><?php echo esc_html( $course_feature['label'] ); ?></span>
					<span class="lm-course-feature-value"><?php echo esc_html( $course_feature['value'] ); ?></span>
				</li>
					<?php
				}
			}
			echo '</ul>';
			echo '</div>';
			?>
		</div>

		<?php
		if ( is_active_sidebar( 'ld-single-course-sidebar' ) ) {
			dynamic_sidebar( 'ld-single-course-sidebar' );
		}
		?>

	</div>
</aside>
<?php

get_footer();
