<?php
/**
 * Template part for displaying archive course list
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

global $wbtm_reign_settings, $post, $wpdb;

$is_enrolled            = false;
$current_user_id        = get_current_user_id();
$course_id              = get_the_ID();
$lesson_count           = learndash_get_course_lessons_list( $course_id, null, array( 'num' => - 1 ) );
$lesson_count           = array_column( $lesson_count, 'post' );
$course_price           = trim( learndash_get_course_meta_setting( $course_id, 'course_price' ) ?? '' );
$course_price_type      = learndash_get_course_meta_setting( $course_id, 'course_price_type' );
$admin_enrolled         = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Section_General_Admin_User', 'courses_autoenroll_admin_users' );
$course_pricing         = learndash_get_course_price( $course_id );
$user_course_has_access = sfwd_lms_has_access( $course_id, $current_user_id );

$author_id   = get_post_field( 'post_author', $course_id );
$first_name  = get_the_author_meta( 'user_firstname', $author_id );
$last_name   = get_the_author_meta( 'user_lastname', $author_id );
$author_name = get_the_author_meta( 'display_name', $author_id );
if ( ! empty( $first_name ) ) {
	$author_name = $first_name . ' ' . $last_name;
}
$author_url        = apply_filters( 'lm_filter_course_author_url', get_author_posts_url( $author_id ) );
$author_avatar_url = get_avatar_url( $author_id );


if ( $user_course_has_access ) {
	$is_enrolled = true;
} else {
	$is_enrolled = false;
}

// if admins are enrolled.
if ( current_user_can( 'manage_options' ) && 'yes' === $admin_enrolled ) {
	$is_enrolled = true;
}

$class = '';
if ( ! empty( $course_price ) && ( 'paynow' === $course_price_type || 'subscribe' === $course_price_type || 'closed' === $course_price_type ) ) {
	$class = 'lm-course-paid';
}

$ribbon_text = get_post_meta( $course_id, '_learndash_course_grid_custom_ribbon_text', true );
?>

<div class="lm-course-item-wrapper lm-course-item-wrap lm-course-item-layout2">

	<div class="lm-cover-list-item <?php echo esc_attr( $class ); ?>">
		<div class="lm-course-cover">
			<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>" class="lm-cover-wrap">
				<?php
				$progress = learndash_course_progress(
					array(
						'user_id'   => $current_user_id,
						'course_id' => $course_id,
						'array'     => true,
					)
				);

				if ( empty( $progress ) ) {
					$progress = array(
						'percentage' => 0,
						'completed'  => 0,
						'total'      => 0,
					);
				}
				$progress_status = ( 100 === (int) $progress['percentage'] ) ? 'completed' : 'notcompleted';

				if ( $progress['percentage'] > 0 && 100 !== $progress['percentage'] ) {
					$progress_status = 'progress';
				}
				if ( 'sfwd-courses' === get_post_type() && ! isset( $wbtm_reign_settings['learndash']['hide_course_ribbon'] ) ) :
					if ( defined( 'LEARNDASH_COURSE_GRID_FILE' ) && ! empty( $ribbon_text ) ) {
						echo '<div class="ld-status ld-status-start ld-primary-background ld-custom-ribbon-text">' . esc_html( $ribbon_text ) . '</div>';
					} elseif ( is_user_logged_in() && isset( $user_course_has_access ) && $user_course_has_access ) {

						if ( ( 'open' === $course_pricing['type'] && 0 === (int) $progress['percentage'] ) || ( 'open' !== $course_pricing['type'] && $user_course_has_access && 0 === $progress['percentage'] ) ) {

							echo '<div class="ld-status ld-status-start ld-primary-background">' .
								esc_html__( 'Start ', 'reign' ) .
								esc_html( LearnDash_Custom_Label::get_label( 'course' ) ) .
							'</div>';

						} else {

							learndash_status_bubble( $progress_status );

						}
					} elseif ( 'free' === $course_pricing['type'] ) {

						echo '<div class="ld-status ld-status-incomplete ld-third-background">' . esc_html__( 'Free', 'reign' ) . '</div>';

					} elseif ( 'open' !== $course_pricing['type'] ) {

						echo '<div class="ld-status ld-status-incomplete ld-third-background">' . esc_html__( 'Not Enrolled', 'reign' ) . '</div>';

					} else {

						echo '<div class="ld-status ld-status-start ld-primary-background">' .
							esc_html__( 'Start ', 'reign' ) .
							esc_html( LearnDash_Custom_Label::get_label( 'course' ) ) .
						'</div>';

					}
				endif;
				?>

				<?php
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( 'medium' );
				} else {
					echo get_reign_ld_default_course_img_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</a>
		</div>

		<div class="lm-card-course-details <?php echo ( is_user_logged_in() && isset( $user_course_has_access ) && $user_course_has_access ) ? 'lm-card-course-details--hasAccess' : 'lm-card-course-details--noAccess'; ?>">
			<?php
			$lessons_count = count( $lesson_count );
			$total_lessons = (
				$lessons_count > 1
				? sprintf(
					/* translators: 1: Lesson count, 2: Lessons label (plural). */
					esc_html__( '%1$s %2$s', 'reign' ),
					absint( $lessons_count ),
					esc_html( LearnDash_Custom_Label::get_label( 'lessons' ) )
				)
				: sprintf(
					/* translators: 1: Lesson count, 2: Lesson label (singular). */
					esc_html__( '%1$s %2$s', 'reign' ),
					absint( $lessons_count ),
					esc_html( LearnDash_Custom_Label::get_label( 'lesson' ) )
				)
			);

			if ( $lessons_count > 0 ) {
				echo '<div class="course-lesson-count">' . esc_html( $total_lessons ) . '</div>';
			} else {
				echo '<div class="course-lesson-count">' .
					sprintf(
						/* translators: %s: Lesson label. */
						esc_html__( '0 %s', 'reign' ),
						esc_html( LearnDash_Custom_Label::get_label( 'lessons' ) )
					) .
				'</div>';
			}
			$title_class = '';
			if ( function_exists( 'is_plugin_active' ) && is_plugin_active( 'wdm-course-review/wdm-course-review.php' ) ) {
				$title_class = 'lm-course-title-with-review';
			}
			?>
			<h2 class="lm-course-title <?php echo esc_attr( $title_class ); ?>">
				<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</h2>

			<div class="lm-course-author-meta" itemscope="" itemtype="http://schema.org/Person">
				<a href="<?php echo esc_url( $author_url ); ?>">
					<img alt="Admin bar avatar" src="<?php echo esc_url( $author_avatar_url ); ?>" class="lm-author-avatar" width="40" height="40">
				</a>
				<div class="author-contain">
					<a href="<?php echo esc_url( $author_url ); ?>">
						<?php echo esc_html( $author_name ); ?>
					</a>
				</div>
			</div>

			<?php
			if ( class_exists( 'LearnMate_LearnDash_Addon' ) && isset( $wbtm_reign_settings['learndash']['show_course_progress_bar'] ) && 'on' === $wbtm_reign_settings['learndash']['show_course_progress_bar'] ) :
				if ( is_user_logged_in() && isset( $user_course_has_access ) && $user_course_has_access ) {
					?>
				<div class="course-progress-wrap">
					<?php
					learndash_get_template_part(
						'modules/progress.php',
						array(
							'context'   => 'course',
							'user_id'   => $current_user_id,
							'course_id' => $course_id,
						),
						true
					);
					?>
				</div>
					<?php
				}
			endif;
			?>

			<!-- Show course progress bar only theme activate. -->
			<div class="course-progress-wrap">
				<?php
				learndash_get_template_part(
					'modules/progress.php',
					array(
						'context'   => 'course',
						'user_id'   => $current_user_id,
						'course_id' => $course_id,
					),
					true
				);
				?>
			</div>

			<?php if ( ! isset( $wbtm_reign_settings['learndash']['hide_course_description'] ) ) : ?>
				<div class="lm-course-excerpt">
					<?php
					if ( ! is_singular( 'sfwd-courses' ) ) {
						echo wp_kses_post( get_the_excerpt( $course_id ) );
					}
					?>
				</div>
			<?php endif; ?>

			<?php
			// Price.
			if ( ! empty( $course_price ) && empty( $is_enrolled ) ) {
				?>
				<div class="lm-course-footer lm-course-pay">
					<span class="course-fee">
						<?php
						echo '<span class="ld-currency">' . wp_kses_post( function_exists( 'learndash_get_currency_symbol' ) ? learndash_get_currency_symbol() : learndash_30_get_currency_symbol() ) . '</span> ' . wp_kses_post( $course_pricing['price'] );
						?>
					</span>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
