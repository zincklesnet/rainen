<?php

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.
$download_id          = get_the_ID();
$download             = edd_get_download( $download_id );
$is_variable_pricing  = $download->has_variable_prices();
$is_single_price_mode = $download->is_single_price_mode() ? 'data-price-mode=multi' : 'data-price-mode=single';
$data_price           = '';
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
<?php
$reign_edd_downloads_layouts = get_theme_mod( 'reign_edd_downloads_layouts', 'default' );

if ( 'layout1' === $reign_edd_downloads_layouts ) {
	$download_list_layout = 'rtm-download-item-article rtm-download-layout-1';
	?>
	<div id="post-<?php the_ID(); ?>" <?php post_class( $download_list_layout ); ?> >
		<div class="rtm-download-item">
			<div class="rtm-download-item-inner">
				<div class="rtm-download-item-top">
					<div class="edd-download-default-image"></div>
					<?php edd_get_template_part( 'shortcode', 'content-image' ); ?>
					<div class="rtm-download-overlay-layout-1">
						<div class="rtm-hover-lines">
							<div class="rtm-download-action">
								<?php echo '<a href="' . esc_url( edd_get_checkout_uri() ) . '" class="button button-overlay-white edd_go_to_checkout ' . esc_attr( $class_to_manage ) . '" style="' . esc_attr( $checkout_display ) . '">' . esc_html__( 'Checkout', 'reign' ) . '</a>'; ?>
								<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="button button-overlay-white">
									<?php esc_html_e( 'View Details', 'reign' ); ?>
								</a>
								<?php $rtm_edd_customization_obj->rtm_get_edd_download_price_html(); ?>
								<?php
								if ( class_exists( 'EDD_Front_End_Submissions' ) ) {
									$vendor_store_url = EDD_FES()->vendors->get_vendor_store_url( get_the_author_meta( 'ID' ) );
								} else {
									$vendor_store_url = '';
								}
								do_action( 'edd_download_after_title' );
								?>
								<?php
								if ( ! $is_variable_pricing ) {
									echo $button_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
							</div>
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
	<?php
} elseif ( 'layout2' === $reign_edd_downloads_layouts ) {
	$download_list_layout = 'rtm-download-item-article rtm-download-layout-2';
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

					edd_get_template_part( 'shortcode', 'content-excerpt' );
					do_action( 'edd_download_after_content' );
					?>
					<div class="rtm-download-overlay-layout-2">

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
							<div class="separator">-</div>
							<?php $rtm_edd_customization_obj->rtm_get_edd_download_price_html(); ?>
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
	<?php
} elseif ( 'layout3' === $reign_edd_downloads_layouts ) {
	$download_list_layout = 'rtm-download-item-article rtm-download-layout-3';
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
	<?php
} else {
	$download_list_layout = 'rtm-download-item-article rtm-download-layout-default';
	?>
	<div id="post-<?php the_ID(); ?>" <?php post_class( $download_list_layout ); ?> >
		<div class="rtm-download-item">
			<div class="rtm-download-item-inner">
				<div class="rtm-download-item-top">
					<div class="edd-download-default-image"></div>
					<?php edd_get_template_part( 'shortcode', 'content-image' ); ?>
					<div class="rtm-download-overlay">
						<div class="rtm-download-action">
							<?php
							if ( ! $is_variable_pricing ) {
								echo $button_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
							<?php echo '<a href="' . esc_url( edd_get_checkout_uri() ) . '" class="button button-overlay-white edd_go_to_checkout ' . esc_attr( $class_to_manage ) . '" style="' . esc_attr( $checkout_display ) . '">' . esc_html__( 'Checkout', 'reign' ) . '</a>'; ?>
							<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="button button-overlay-white">
								<?php esc_html_e( 'View Details', 'reign' ); ?>
							</a>
							<?php $rtm_edd_customization_obj->rtm_get_edd_download_price_html(); ?>
						</div>
					</div>
				</div>
				<div class="rtm-download-item-bottom">
					<?php edd_get_template_part( 'shortcode', 'content-title' ); ?>
					<?php
					if ( class_exists( 'EDD_Front_End_Submissions' ) ) {
						$vendor_store_url = EDD_FES()->vendors->get_vendor_store_url( get_the_author_meta( 'ID' ) );
					} else {
						$vendor_store_url = '';
					}
						/* translators: %s: Author display name. */
						$author_title = sprintf( __( 'View all posts by %s', 'reign' ), get_the_author_meta( 'display_name' ) );
						$byline_inner = sprintf(
							'<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s %4$s</a></span>',
							esc_url( $vendor_store_url ),
							esc_attr( $author_title ),
							esc_html( get_the_author_meta( 'display_name' ) ),
							get_avatar( get_the_author_meta( 'ID' ), 50 )
						);
						/* translators: %1$s: Author byline link markup. */
						$byline_template = __( '<span class="byline"> by %1$s</span>', 'reign' );
						printf(
							wp_kses_post( $byline_template ),
							wp_kses_post( $byline_inner )
						);
					?>
					<?php
					do_action( 'edd_download_after_title' );
					?>
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
<?php } ?>
