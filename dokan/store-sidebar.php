<?php
/**
 * The Template for displaying all reviews.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

defined( 'ABSPATH' ) || exit;
?>

<?php if ( dokan_get_option( 'enable_theme_store_sidebar', 'dokan_appearance', 'off' ) === 'off' ) { ?>
	<aside id="dokan-secondary" class="dokan-store-sidebar dokan-widget-area widget-area" role="complementary">
		<div class="dokan-widget-area widget-collapse">
			<?php do_action( 'dokan_sidebar_store_before', $store_user->data, $store_info ); ?>
			<?php
			if ( ! dynamic_sidebar( 'sidebar-store' ) ) {
				$args = array(
					'before_widget' => '<section class="widget widget-area dokan-store-widget %s">',
					'after_widget'  => '</section>',
					'before_title'  => '<h3 class="widget-title">',
					'after_title'   => '</h3>',
				);

				dokan_store_category_widget();

				if ( ! empty( $map_location ) ) {
					dokan_store_location_widget();
				}

				dokan_store_time_widget();
				dokan_store_contact_widget();
			}
			?>

			<?php do_action( 'dokan_sidebar_store_after', $store_user->data, $store_info ); ?>
		</div>
	</aside><!-- #secondary .widget-area -->
<?php } else { ?>
	<?php get_sidebar( 'store' ); ?>
<?php } ?>
