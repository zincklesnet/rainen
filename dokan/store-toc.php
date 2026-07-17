<?php
/**
 * The Template for displaying all reviews.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$vendor       = dokan()->vendor->get( get_query_var( 'author' ) );
$vendor_info  = $vendor->get_shop_info();
$map_location = $vendor->get_location();
$store_user   = get_userdata( get_query_var( 'author' ) );
$store_info   = dokan_get_store_info( $store_user->ID );
$layout       = get_theme_mod( 'store_layout', 'left' );

get_header( 'shop' );
?>

<?php do_action( 'woocommerce_before_main_content' ); ?>
<div class="dokan-store-wrap wb-grid dokan-store-review-wrap dokan-store-toc-wrap layout-<?php echo esc_attr( $layout ); ?>">
	
	<?php if ( 'left' === $layout ) { ?>
		<?php
		dokan_get_template_part(
			'store-sidebar',
			'',
			array(
				'store_user'   => $store_user,
				'store_info'   => $store_info,
				'map_location' => $map_location,
			)
		);
		?>
	<?php } ?>

	<div id="dokan-primary" class="content-area dokan-single-store">
		<div id="dokan-content" class="site-content store-review-wrap woocommerce" role="main">

			<?php if ( function_exists( 'rda_is_render_inner_store_header' ) && rda_is_render_inner_store_header() ) : ?>
				<?php dokan_get_template_part( 'store-header' ); ?>
			<?php elseif ( ! class_exists( 'Reign_Dokan_Addon' ) ) : ?>
				<?php dokan_get_template_part( 'store-header' ); ?>
			<?php endif; ?>

			<div id="store-toc-wrapper">
				<div id="store-toc">
					<?php if ( ! empty( $vendor->get_store_tnc() ) ) : ?>
						<h2 class="headline"><?php esc_html_e( 'Terms And Conditions', 'reign' ); ?></h2>
						<div>
							<?php echo wp_kses_post( wpautop( wptexturize( $vendor->get_store_tnc() ) ) ); ?>
						</div>
						<?php
					endif;
					?>
				</div><!-- #store-toc -->
			</div><!-- #store-toc-wrap -->

		</div><!-- #content .site-content -->
	</div><!-- #primary .content-area -->

	<?php if ( 'right' === $layout ) { ?>
		<?php
		dokan_get_template_part(
			'store-sidebar',
			'',
			array(
				'store_user'   => $store_user,
				'store_info'   => $store_info,
				'map_location' => $map_location,
			)
		);
		?>
	<?php } ?>
	
</div>
<div class="dokan-clearfix"></div>

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer(); ?>
