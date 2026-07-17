<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
?>
<?php
$download_list_layout = 'rtm-download-item-article rtm-download-layout-3';
$download_id          = get_the_ID();
$download             = edd_get_download( $download_id );
$is_variable_pricing  = $download->has_variable_prices();
$data_price           = '';
$is_single_price_mode = $download->is_single_price_mode() ? 'data-price-mode=multi' : 'data-price-mode=single';
$options              = array();
if ( edd_item_in_cart( $download_id, $options ) && ( ! $is_variable_pricing || ! $download->is_single_price_mode() ) ) {
	$button_text     = __( 'Checkout', 'reign' );
	$href            = esc_url( edd_get_checkout_uri() );
	$class_to_manage = '';

	$button_display   = 'style="display:none;"';
	$checkout_display = '';
} else {
	$button_text     = __( 'Buy Now', 'reign' );
	$href            = 'javascript:void(0);';
	$class_to_manage = 'edd_buy_now';

	$button_display   = '';
	$checkout_display = 'display:none;';
}
if ( ! $is_variable_pricing ) {
	$data_price_value = $download->price;
	$price            = $download->price;
	$data_price       = 'data-price="' . $data_price_value . '"';
}

$button_html = '<a href="' . esc_url( $href ) . '" class="button button-overlay-white edd-add-to-cart ' . esc_attr( $class_to_manage ) . '" data-nonce="' . wp_create_nonce( 'edd-add-to-cart-' . $download_id ) . '" data-action="edd_add_to_cart" data-download-id="' . esc_attr( $download_id ) . '"' . $is_single_price_mode . ' ' . $data_price . ' ' . $button_display . '><span class="edd-add-to-cart-label">' . esc_html( $button_text ) . '</span><span class="edd-loading" aria-label="' . esc_attr__( 'Loading', 'reign' ) . '"></span></a>';

$rtm_edd_customization_obj = RTM_EDD_Customization::instance();
?>
<div id="post-<?php the_ID(); ?>" <?php post_class( $download_list_layout ); ?> >
	<div class="rtm-download-item">
		<div class="rtm-download-item-inner">
			<div class="rtm-download-item-top">
				<div class="edd-download-default-image"></div>
				<?php edd_get_template_part( 'shortcode', 'content-image' ); ?>
			</div>
			<div class="rtm-download-item-bottom">
				<?php edd_get_template_part( 'shortcode', 'content-title' ); ?>
				<?php
				if ( class_exists( 'EDD_Front_End_Submissions' ) ) {
					$vendor_store_url = EDD_FES()->vendors->get_vendor_store_url( get_the_author_meta( 'ID' ) );
				} else {
					$vendor_store_url = '';
				}
				?>
				<?php
				do_action( 'edd_download_after_title' );
				?>
				<?php $rtm_edd_customization_obj->rtm_get_edd_download_price_html(); ?>
				<div class="rtm-download-overlay-layout-3">
					<div class="rtm-download-action">
						<?php
						if ( ! $is_variable_pricing ) {
							echo $button_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
						<?php echo '<a href="' . esc_url( edd_get_checkout_uri() ) . '" class="button button-overlay-white edd_go_to_checkout ' . esc_attr( $class_to_manage ) . '" style="' . esc_attr( $checkout_display ) . '">' . esc_html__( 'Checkout', 'reign' ) . '</a>'; ?>
						<?php if ( $is_variable_pricing ) : ?>
							<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="button button-overlay-white details-button">
								<?php esc_html_e( 'View Details', 'reign' ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<div class="rtm-download-checkout-popup" style="display: none;">
				<div class="rtm-download-popup-inners">
					<span class="close_edd_popup"><i class="fa fa-times"></i></span>
					<h3 class="section-title"><span><?php esc_html_e( 'Buying Options', 'reign' ); ?></span></h3>
					<?php echo do_shortcode( '[purchase_link id="' . $download_id . '" text="Purchase"]' ); ?>
				</div>
			</div>
		</div>
	</div>
</div><!-- #post-## -->
