<?php
/**
 * The template for displaying single group content
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

	?>
	<?php
	endwhile; // End of the loop.
?>
</div>
<aside id="reign-sidebar-right" class="widget-area learndash-course-widget learndash-group-widget" role="complementary">
	<div class="widget-area-inner">
		<div class="learndash-course-widget-wrap learndash-group-widget-wrap">
			<?php
			$group_info                 = array();
			$group_id                   = get_the_ID();
			$userid                     = 0;
			$group_course               = learndash_get_groups_courses_ids( $userid, array( $group_id ) );
			$group_info['group_course'] = count( $group_course );
			$group_users                = learndash_get_groups_user_ids( $group_id );
			$group_info['group_users']  = count( $group_users );
			$gp_leader_args             = array(
				'meta_key'   => 'learndash_group_leaders_' . absint( $group_id ),
				'meta_value' => absint( $group_id ),
				'fields'     => 'ID', // Return only user IDs.
			);
			// Fetch group leader IDs directly using get_users for better performance.
			$gp_leader_ids = get_users(
				array(
					'meta_key'   => 'learndash_group_leaders_' . absint( $group_id ),
					'meta_value' => absint( $group_id ),
					'fields'     => 'ID', // Return only user IDs.
				)
			);

			$group_info['group_leaders'] = count( $gp_leader_ids );

			$group_info['group_leaders'] = count( $gp_leader_ids );
			$group_certificate           = get_post_meta( $group_id, '_ld_certificate', true );
			if ( ! empty( $group_certificate ) ) {
				$group_info['group_certificate'] = esc_html__( 'Yes', 'reign' );
			} else {
				$group_info['group_certificate'] = esc_html__( 'No', 'reign' );
			}
			if ( has_post_thumbnail( $group_id ) ) {
				echo '<div class="lm-group-thumbnail">';
				echo get_the_post_thumbnail( $group_id );
				echo '</div>';
			} else {
				echo '<div class="lm-course-thumbnail">';
				echo get_reign_ld_default_course_img_html(); //phpcs:ignore
				echo '</div>';
			}
			/* translators: %s: Group label. */
			$group_features_label = sprintf( esc_html_x( '%s Features', 'Group Features  Label', 'reign' ), LearnDash_Custom_Label::get_label( 'group' ) );
			echo '<div class="lm-tab-course-info lm-tab-group-info">';
			echo '<h3 class="title">' . esc_html( $group_features_label ) . '</h3>';
			$rla_gcf_enable   = get_post_meta( $group_id, 'rla_gcf_enable', true );
			$rla_gcf_features = get_post_meta( $group_id, 'rla_gcf_features', true );
			$group_features   = array(
				'courses'     => array(
					'slug'  => 'courses',
					'label' => LearnDash_Custom_Label::get_label( 'courses' ),
					'value' => $group_info['group_course'],
					'icon'  => 'far fa-file-alt',
				),
				'users'       => array(
					'slug'  => 'users',
					'label' => esc_html__( 'Users', 'reign' ),
					'value' => $group_info['group_users'],
					'icon'  => 'far fa-users',
				),
				'leaders'     => array(
					'slug'  => 'leaders',
					'label' => esc_html__( 'Leaders', 'reign' ),
					'value' => $group_info['group_leaders'],
					'icon'  => 'far fa-puzzle-piece',
				),
				'certificate' => array(
					'slug'  => 'certificate',
					'label' => esc_html__( 'Certificate', 'reign' ),
					'value' => $group_info['group_certificate'],
					'icon'  => 'far fa-graduation-cap',
				),
			);
			$group_features   = apply_filters( 'learnmate_modify_group_features_in_tab', $group_features );
			$features_icon    = isset( $rla_gcf_features['icon'] ) ? $rla_gcf_features['icon'] : '';
			if ( ! empty( $features_icon ) ) {
				$count_value = count( $features_icon );
			} else {
				$count_value = '';
			}
			echo '<ul>';
			if ( 'yes' === $rla_gcf_enable ) {
				for ( $i = 0; $i < $count_value; $i++ ) {
					?>
					<li class="<?php echo ( isset( $group_features['slug'] ) ) ? esc_attr( $group_features['slug'] ) : ''; ?>">
						<i class="<?php echo esc_attr( $rla_gcf_features['icon'][ $i ] ); ?>"></i>
						<span class="lm-course-feature-value"><?php echo esc_html( $rla_gcf_features['text'][ $i ] ); ?></span>
					</li>
					<?php
				}
			} else {
				foreach ( $group_features as $group_feature ) {
					?>
					<li class="<?php echo esc_attr( $group_feature['slug'] ); ?>">
						<i class="<?php echo esc_attr( $group_feature['icon'] ); ?>"></i>
						<span class="lm-course-feature-label lm-group-feature-label"><?php echo esc_html( $group_feature['label'] ); ?></span>
						<span class="lm-course-feature-value lm-group-feature-value"><?php echo esc_html( $group_feature['value'] ); ?></span>
					</li>
					<?php
				}
			}
			echo '</ul>';
			echo '</div>';
			?>
		</div>

		<?php
		if ( is_active_sidebar( 'ld-single-group-sidebar' ) ) {
			dynamic_sidebar( 'ld-single-group-sidebar' );
		}
		?>

	</div>
</aside>
<?php
get_footer();
