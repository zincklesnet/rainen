<?php
/**
 * Store List Loop
 *
 * @package reign
 */

defined( 'ABSPATH' ) || exit; // Exit if accessed directly.

$store_layout = get_theme_mod( 'reign_dokan_store_list_layout', 'layout_one' );
$store_layout = isset( $store_layout ) ? $store_layout : 'layout_one';

?>

<div id="dokan-seller-listing-wrap" class="grid-view <?php echo esc_attr( $store_layout ); ?>">
	<div class="seller-listing-content">
		<?php if ( $sellers['users'] ) : ?>

			<?php
			$enable_slider    = isset( $enable_slider ) ? $enable_slider : false;
			$ul_wrapper_class = ( $enable_slider ) ? 'dokan-seller-wrap-slider-wrap' : '';
			?>

			<ul class="dokan-seller-wrap <?php echo esc_attr( $ul_wrapper_class ); ?>" data-slick='{"slidesToShow": <?php echo esc_attr( $per_row ); ?>, "slidesToScroll": 1}'>
				<?php
				foreach ( $sellers['users'] as $seller ) {
					$vendor                   = dokan()->vendor->get( $seller->ID );
					$store_banner_id          = $vendor->get_banner_id();
					$store_name               = $vendor->get_shop_name();
					$store_url                = $vendor->get_shop_url();
					$store_rating             = $vendor->get_rating();
					$is_store_featured        = $vendor->is_featured();
					$store_phone              = $vendor->get_phone();
					$store_info               = dokan_get_store_info( $seller->ID );
					$store_address            = dokan_get_seller_short_address( $seller->ID );
					$store_banner_url         = $store_banner_id ? wp_get_attachment_image_src( $store_banner_id, $image_size ) : DOKAN_PLUGIN_ASSEST . '/images/default-store-banner.png';
					$show_store_open_close    = dokan_get_option( 'store_open_close', 'dokan_appearance', 'on' );
					$dokan_store_time_enabled = isset( $store_info['dokan_store_time_enabled'] ) ? $store_info['dokan_store_time_enabled'] : '';
					$store_open_is_on         = ( 'on' === $show_store_open_close && 'yes' === $dokan_store_time_enabled && ! $is_store_featured ) ? 'store_open_is_on' : '';
					?>

					<li class="dokan-single-seller woocommerce coloum-<?php echo esc_attr( $per_row ); ?> <?php echo ( ! $store_banner_id ) ? 'no-banner-img' : ''; ?>">
						<div class="store-wrapper">
							<div class="rda-featured-favourite featured-favourite">
								<?php if ( ! empty( $is_store_featured ) && 'yes' == $is_store_featured ) : ?>
									<div class="featured-label"><?php esc_html_e( 'Featured', 'reign' ); ?></div>
								<?php endif ?>

								<?php do_action( 'dokan_seller_listing_after_featured', $seller, $store_info ); ?>
							</div>
							<?php if ( 'on' === $show_store_open_close && 'yes' === $dokan_store_time_enabled ) : ?>
								<?php if ( dokan_is_store_open( $seller->ID ) ) { ?>
									<span class="dokan-store-is-open-close-status dokan-store-is-open-status" title="<?php esc_attr_e( 'Store is Open', 'reign' ); ?>"><?php esc_html_e( 'Open', 'reign' ); ?></span>
								<?php } else { ?>
									<span class="dokan-store-is-open-close-status dokan-store-is-closed-status" title="<?php esc_attr_e( 'Store is Closed', 'reign' ); ?>"><?php esc_html_e( 'Closed', 'reign' ); ?></span>
								<?php } ?>
							<?php endif ?>
							<div class="rda-store-content-wrapper">
								<div class="store-content rda-wb-grid-store-content">
									<div class="store-banner">
										<a href="<?php echo esc_url( $store_url ); ?>">
											<img class="<?php echo is_array( $store_banner_url ) ? 'store-image-set' : 'store-image-default'; ?>" src="<?php echo is_array( $store_banner_url ) ? esc_attr( $store_banner_url[0] ) : esc_attr( $store_banner_url ); ?>">
										</a>
									</div>
								</div>
								<div class="store-footer">
									<div class="seller-avatar">
										<a href="<?php echo esc_url( $store_url ); ?>">
											<img src="<?php echo esc_url( $vendor->get_avatar() ); ?>"
												alt="<?php echo esc_attr( $vendor->get_shop_name() ); ?>"
												size="150">
										</a>
									</div>

									<div class="store-data">
										<h2 class="rda-wb-grid-view-only"><a href="<?php echo esc_url( $store_url ); ?>"><?php echo esc_html( $store_name ); ?></a></h2>

										<?php if ( ! empty( $store_rating['count'] ) ) : ?>
											<?php /* translators: %s: */ ?>
											<div class="star-rating dokan-seller-rating" title="<?php printf( esc_html__( 'Rated %s out of 5', 'reign' ), esc_attr( $store_rating['rating'] ) ); ?>">
												<span style="width: <?php echo esc_attr( ( $store_rating['rating'] / 5 ) * 100 - 1 ); ?>%">
													<strong class="rating"><?php echo esc_html( $store_rating['rating'] ); ?></strong> out of 5
												</span>
											</div>
										<?php else : ?>
											<div class="rda-no-star-rating">
												<?php esc_html_e( 'No Rating Yet', 'reign' ); ?>
											</div>
										<?php endif; ?>

										<?php do_action( 'dokan_seller_listing_after_store_data', $seller, $store_info ); ?>

									</div>

									<?php if ( class_exists( 'Reign_Dokan_Addon' ) ) { ?>
										<div class="rda-seller-products-wrapper rda-wb-grid-view-only">
											<?php
											$products_to_display = apply_filters( 'rda_store_list_loop_products_to_display', 5 );
											$args                = array(
												'context' => 'seller-listing',
												'products_to_display' => $products_to_display,
												'seller_id' => $seller->ID,
												'heading' => __( 'Recent Products', 'reign' ),
											);
											echo rda_get_sellers_products( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											?>
										</div>
									<?php } ?>

									<a href="<?php echo esc_url( $store_url ); ?>" class="dokan-btn dokan-btn-theme"><?php esc_html_e( 'Visit Store', 'reign' ); ?></a>

									<?php do_action( 'dokan_seller_listing_footer_content', $seller, $store_info ); ?>
								</div>
							</div>

							<div class="rda-store-extra-list-view">
								<div class="store-content">
									<div class="store-info">
										<div class="store-data-container">
											<div class="store-data">
												<h2><a href="<?php echo esc_url( $store_url ); ?>"><?php echo esc_html( $store_name ); ?></a></h2>
												<?php if ( ! dokan_is_vendor_info_hidden( 'address' ) && $store_address ) : ?>
													<?php
													$allowed_tags = array(
														'span' => array(
															'class' => array(),
														),
														'br'   => array(),
													);
													?>
													<p class="store-address"><?php echo wp_kses( $store_address, $allowed_tags ); ?></p>
												<?php endif ?>
												<?php if ( ! dokan_is_vendor_info_hidden( 'phone' ) && $store_phone ) { ?>
													<p class="store-phone">
														<i class="fas fa-phone-alt" aria-hidden="true"></i> <?php echo esc_html( $store_phone ); ?>
													</p>
												<?php } ?>
												<?php do_action( 'dokan_seller_listing_after_store_data', $seller, $store_info ); ?>
											</div>
										</div>
									</div>
								</div>

								<?php if ( class_exists( 'Reign_Dokan_Addon' ) ) { ?>
									<div class="rda-seller-products-wrapper">
										<?php
										$products_to_display = apply_filters( 'rda_store_list_loop_products_to_display', 5 );
										$args                = array(
											'context'   => 'seller-listing',
											'products_to_display' => $products_to_display,
											'seller_id' => $seller->ID,
											'heading'   => __( 'Recent Products', 'reign' ),
										);
										echo rda_get_sellers_products( $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										?>
									</div>
								<?php } ?>
							</div>
						</div>
					</li>

				<?php } ?>
				<div class="dokan-clearfix"></div>
			</ul> <!-- .dokan-seller-wrap -->

			<?php
			$user_count   = $sellers['count'];
			$num_of_pages = ceil( $user_count / $limit );

			if ( $num_of_pages > 1 ) {
				echo '<div class="pagination-container clearfix">';

				$pagination_args = array(
					'current'   => $paged,
					'total'     => $num_of_pages,
					'base'      => $pagination_base,
					'type'      => 'array',
					'prev_text' => __( '&larr; Previous', 'reign' ),
					'next_text' => __( 'Next &rarr;', 'reign' ),
				);

				if ( ! empty( $search_query ) ) {
					$pagination_args['add_args'] = array(
						'dokan_seller_search' => $search_query,
					);
				}

				$page_links = paginate_links( $pagination_args );

				if ( $page_links ) {
					$pagination_links  = '<div class="pagination-wrap">';
					$pagination_links .= '<ul class="pagination"><li>';
					$pagination_links .= join( "</li>\n\t<li>", $page_links );
					$pagination_links .= "</li>\n</ul>\n";
					$pagination_links .= '</div>';

					echo wp_kses_post( $pagination_links );
				}

				echo '</div>';
			}
			?>

		<?php else : ?>
			<p class="dokan-error"><?php esc_html_e( 'No vendors found!', 'reign' ); ?></p>
		<?php endif; ?>
	</div>
</div>
