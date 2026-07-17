<?php
/**
 * The Template for displaying vendor biography.
 *
 * @package dokan
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$store_user   = get_userdata( get_query_var( 'author' ) );
$store_info   = dokan_get_store_info( $store_user->ID );
$map_location = isset( $store_info['location'] ) ? esc_attr( $store_info['location'] ) : '';
$layout       = get_theme_mod( 'store_layout', 'left' );

get_header( 'shop' );
?>

<?php do_action( 'woocommerce_before_main_content' ); ?>

<div class="dokan-store-wrap wb-grid dokan-store-review-wrap dokan-store-biography-wrap layout-<?php echo esc_attr( $layout ); ?>">
	
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
		<div id="dokan-content" class="store-review-wrap woocommerce" role="main">

			<?php if ( function_exists( 'rda_is_render_inner_store_header' ) && rda_is_render_inner_store_header() ) : ?>
				<?php dokan_get_template_part( 'store-header' ); ?>
			<?php elseif ( ! class_exists( 'Reign_Dokan_Addon' ) ) : ?>
				<?php dokan_get_template_part( 'store-header' ); ?>
			<?php endif; ?>

			<div id="vendor-biography">
				<div id="comments">
				<?php do_action( 'dokan_vendor_biography_tab_before', $store_user, $store_info ); ?>

				<h2 class="headline"><?php echo esc_html( apply_filters( 'dokan_vendor_biography_title', __( 'Vendor Biography', 'reign' ) ) ); ?></h2>

				<?php
				if ( ! empty( $store_info['vendor_biography'] ) ) {
					$reign_vendor_biography = apply_filters(
						'the_content',
						apply_filters(
							'dokan_get_vendor_biography_text',
							$store_info['vendor_biography'],
							$store_user->ID
						)
					);
					echo $reign_vendor_biography; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Vendor biography runs through the_content filter, equivalent to the_content() output (matches Dokan core).
				}
				?>

				<?php do_action( 'dokan_vendor_biography_tab_after', $store_user, $store_info ); ?>
				</div>
			</div>

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

<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer(); ?>
