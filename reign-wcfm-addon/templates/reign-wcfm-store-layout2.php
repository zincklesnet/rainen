<?php
/**
 * The Template for displaying store.
 *
 * @package WCfM Markeplace Views Store
 *
 * For edit coping this to yourtheme/wcfm/store
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $WCFM, $WCFMmp;

$wcfm_store_url  = wcfm_get_option( 'wcfm_store_url', 'store' );
$wcfm_store_name = apply_filters( 'wcfmmp_store_query_var', get_query_var( $wcfm_store_url ) );
if ( empty( $wcfm_store_name ) ) {
	return;
}
$seller_info = get_user_by( 'slug', $wcfm_store_name );
if ( ! $seller_info ) {
	return;
}

$store_user = wcfmmp_get_store( $seller_info->ID );
$store_info = $store_user->get_shop_info();

$store_sidebar_pos = isset( $WCFMmp->wcfmmp_marketplace_options['store_sidebar_pos'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_sidebar_pos'] : 'left';

$wcfm_store_wrapper_class = apply_filters( 'wcfm_store_wrapper_class', '' );

$wcfm_store_color_settings          = get_option( 'wcfm_store_color_settings', array() );
$mob_wcfmmp_header_background_color = ( isset( $wcfm_store_color_settings['header_background'] ) ) ? $wcfm_store_color_settings['header_background'] : '#3e3e3e';

get_header( 'shop' );
?>

<?php if ( $WCFMmp->wcfmmp_vendor->is_store_sidebar() && ( $store_sidebar_pos != 'left' ) ) { ?>
	<style>
		#wcfmmp-store .right_side{float:left !important;}
		#wcfmmp-store .left_sidebar{float:right !important;}
	</style>
<?php } ?>
<style>
@media screen and (max-width: 480px) {
	#wcfmmp-store .header_right {
		background: <?php echo esc_attr( $mob_wcfmmp_header_background_color ); ?>;
	}
}
</style>		
<?php // do_action( 'woocommerce_before_main_content' ); ?>
<?php echo '<div id="primary" class="content-area"><main id="main" class="site-main" role="main">'; ?>
<?php do_action( 'wcfmmp_before_store', $store_user->data, $store_info ); ?>

<div id="wcfmmp-store" class="wcfmmp-single-store-holder <?php echo esc_attr( $wcfm_store_wrapper_class ); ?>">
	<div id="wcfmmp-store-content" class="wcfmmp-store-page-wrap woocommerce" role="main">
			
		<?php
		if ( ! apply_filters( 'wcfm_is_allow_store_banner', true ) ) {
			return;
		}

		$banner_type    = $store_user->get_banner_type();
		$banner         = '';
		$default_banner = ! empty( $WCFMmp->wcfmmp_marketplace_options['store_default_banner'] ) ? wcfm_get_attachment_url( $WCFMmp->wcfmmp_marketplace_options['store_default_banner'] ) : esc_url( $WCFMmp->plugin_url . 'assets/images/default_banner.jpg' );

		if ( $banner_type == 'slider' ) {
			$banner_sliders = $store_user->get_banner_slider();
		} elseif ( $banner_type == 'video' ) {
			$banner_video = $store_user->get_banner_video();
		} else {
			$banner = $store_user->get_banner();
		}
		if ( ! $banner ) {
			$banner = $default_banner;
			$banner = apply_filters( 'wcfmmp_store_default_banner', $banner );
		}

		$mobile_banner = $store_user->get_mobile_banner();
		if ( ! $mobile_banner ) {
			$mobile_banner = $store_user->get_banner();
			if ( ! $mobile_banner ) {
				$mobile_banner = $default_banner;
				$mobile_banner = apply_filters( 'wcfmmp_store_default_banner', $mobile_banner );
			}
		}

		$store_banner_width   = isset( $WCFMmp->wcfmmp_marketplace_options['store_banner_width'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_banner_width'] : '1650';
		$store_banner_height  = isset( $WCFMmp->wcfmmp_marketplace_options['store_banner_height'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_banner_height'] : '350';
		$store_banner_mwidth  = isset( $WCFMmp->wcfmmp_marketplace_options['store_banner_mwidth'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_banner_mwidth'] : '520';
		$store_banner_mheight = isset( $WCFMmp->wcfmmp_marketplace_options['store_banner_mheight'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_banner_mheight'] : '250';

		?>
		
		<style>
		#wcfmmp-store .banner_img, #wcfmmp-store .wcfm_slideshow_container {
			max-height: <?php echo esc_attr( $store_banner_height ); ?>px;
		}
		#wcfmmp-store .banner_img {
			height: <?php echo esc_attr( $store_banner_height ); ?>px;
			background-image: url(<?php echo esc_url( $banner ); ?>);
		}
		#wcfmmp-store .banner_area_mobile .banner_img {
			height: <?php echo esc_attr( $store_banner_height ); ?>px;
			background-image: url(<?php echo esc_url( $mobile_banner ); ?>);
		}
		.banner_area_mobile{display:none !important;}
		@media screen and (max-width: 640px) {
			#wcfmmp-store .banner_img, #wcfmmp-store .wcfm_slideshow_container {
				max-height: <?php echo esc_attr( $store_banner_mheight ); ?>px;
			}
			#wcfmmp-store .banner_img {
				height: <?php echo esc_attr( $store_banner_mheight ); ?>px;
			}
			.banner_area_desktop{display:none !important;}
			.banner_area_mobile{display:block !important;}
		}
		</style>
		<?php do_action( 'wcfmmp_store_before_bannar', $store_user->get_id() ); ?>

		<div class="wcfm_banner_area">
			<div class="wcfm_banner_area_desktop">
				<?php if ( $banner_type == 'slider' ) { ?>
					
					<div class="wcfm_slider_area">
						<div class="wcfm_slideshow_container">
						 
							<?php foreach ( $banner_sliders as $banner_slider_key => $banner_slider ) { ?>
								<?php if ( ! empty( $banner_slider['image'] ) ) { ?>
									<div class="wcfmSlides wcfm_slide_fade">
										<a href="<?php echo $banner_slider['link'] ? esc_url( $banner_slider['link'] ) : esc_url( wcfm_get_attachment_url( $banner_slider['image'] ) ); ?>" target="_blank">
											<div class="numbertext"><?php echo esc_attr( $banner_slider_key ); ?> / <?php echo count( $banner_sliders ); ?></div>
											<img src="<?php echo esc_url( wcfm_get_attachment_url( $banner_slider['image'] ) ); ?>" style="width:100%" alt="<?php echo esc_html( $store_info['store_name'] ); ?>" title="<?php echo esc_html( $store_info['store_name'] ); ?>">
											<?php if ( ( $WCFMmp->wcfmmp_vendor->get_vendor_name_position( $store_user->get_id() ) == 'on_banner' ) && apply_filters( 'wcfm_is_allow_store_name_on_banner', true ) ) { ?>
												<div class="slider_text"><h1><?php echo wp_kses_post( apply_filters( 'wcfmmp_store_title', $store_info['store_name'], $store_user->get_id() ) ); ?></h1></div>
											<?php } ?>
										</a>
									</div>
								<?php } ?>
							<?php } ?>
					
							<!-- Next and previous buttons -->
							<a class="prev" >&#10094;</a>
							<a class="next">&#10095;</a>
						</div>
					</div>
				<?php } elseif ( $banner_type == 'video' ) { ?>
					<section class="banner_area">
						<?php if ( apply_filters( 'wcfm_is_allow_full_width_video', true ) ) { ?>
							<style>
							#wcfmmp-store .banner_area {
								position: relative;
								height: <?php echo esc_attr( $store_banner_height + 75 ); ?>px;
								overflow:hidden;
							}
							#wcfmmp-store .banner_video {
								position: relative;
								padding-bottom: 56.25%; /* 16:9 */
								height: 0;
							}
							#wcfmmp-store .banner_video iframe {
								position: absolute;
								top: -75px;
								left: 0;
								width: 100%;
								height: 100%;
							}
							@media screen and (max-width: 640px) {
								#wcfmmp-store .banner_area {
									height: <?php echo esc_attr( $store_banner_mheight - 50 ); ?>px;
								}
								#wcfmmp-store .banner_video iframe {
									top: 0px;
								}
							}
							</style>
						<?php } ?>
						<div class="banner_video">
							<?php echo apply_filters( 'wcfmmp_store_banner_display', preg_replace( '/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i', '<iframe width="100%" height="315" frameborder="0" allow="accelerometer; autoplay; encrypted-media" src="//www.youtube.com/embed/$2?iv_load_policy=3&enablejsapi=1&disablekb=1&autoplay=1&controls=0&showinfo=0&rel=0&loop=1&wmode=transparent&widgetid=1" allowfullscreen="1"></iframe>', $banner_video ), $banner_video ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						
							<?php if ( ( $WCFMmp->wcfmmp_vendor->get_vendor_name_position( $store_user->get_id() ) == 'on_banner' ) && apply_filters( 'wcfm_is_allow_store_name_on_banner', true ) ) { ?>
								<div class="video_text">
									<?php do_action( 'wcfmmp_store_before_bannar_text', $store_user->get_id() ); ?>
									
									<h1><?php echo apply_filters( 'wcfmmp_store_title', esc_html( $store_info['store_name'] ), $store_user->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
									
									<?php do_action( 'wcfmmp_store_after_bannar_text', $store_user->get_id() ); ?>
								</div>
							<?php } ?>
						</div>
					</section>
				<?php } else { ?>
					<section class="banner_area banner_area_desktop">
						<?php do_action( 'wcfmmp_store_before_bannar_image', $store_user->get_id() ); ?>
						
						<div class="banner_img"><img src="<?php echo esc_url( $banner ); ?>" alt="<?php echo esc_html( $store_info['store_name'] ); ?>" title="<?php echo esc_html( $store_info['store_name'] ); ?>" /></div>
						
						<?php do_action( 'wcfmmp_store_after_bannar_image', $store_user->get_id() ); ?>
						
					</section>
					
					<section class="banner_area banner_area_mobile">
						<?php do_action( 'wcfmmp_store_before_bannar_image', $store_user->get_id() ); ?>
						
						<div class="banner_img"><img src="<?php echo esc_url( $mobile_banner ); ?>" alt="<?php echo esc_html( $store_info['store_name'] ); ?>" title="<?php echo esc_html( $store_info['store_name'] ); ?>" /></div>
						
						<?php do_action( 'wcfmmp_store_after_bannar_image', $store_user->get_id() ); ?>
						
					</section>
				<?php } ?>
			</div>
		</div>
		
		<?php do_action( 'wcfmmp_store_after_bannar', $store_user->get_id() ); ?>
		
		<?php
		if ( apply_filters( 'wcfmmp_is_allow_legacy_header', false ) ) {
			$WCFMmp->template->get_template(
				'store/legacy/wcfmmp-view-store-header.php',
				array(
					'store_user' => $store_user,
					'store_info' => $store_info,
				)
			);
		} else {
			$gravatar = $store_user->get_avatar();
			$email    = $store_user->get_email();
			$phone    = $store_user->get_phone();
			$address  = $store_user->get_address_string();

			$store_lat = isset( $store_info['store_lat'] ) ? esc_attr( $store_info['store_lat'] ) : 0;
			$store_lng = isset( $store_info['store_lng'] ) ? esc_attr( $store_info['store_lng'] ) : 0;

			$store_address_info_class = 'wcfmmp-store-info-wrapper';

			do_action( 'wcfmmp_store_before_header', $store_user->get_id() );
			?>
			<div id="wcfm_store_header">
				<div class="header_wrapper">
					<div class="header_area">
						<div class="header_left">
						
							<?php do_action( 'wcfmmp_store_before_avatar', $store_user->get_id() ); ?>
							
							<div class="logo_area"><a href="#"><img src="<?php echo esc_url( $gravatar ); ?>" alt="Logo"/></a></div>
							
							<div class="logo_area_after">
								<?php do_action( 'wcfmmp_store_after_avatar', $store_user->get_id() ); ?>
								
								<?php
								if ( apply_filters( 'wcfm_is_pref_vendor_reviews', true ) && apply_filters( 'wcfm_is_allow_review_rating', true ) ) {
									$WCFMmp->wcfmmp_reviews->show_star_rating( 0, $store_user->get_id() ); }
								?>
								
								<?php if ( ! apply_filters( 'wcfm_is_allow_badges_with_store_name', false ) ) { ?>
									<div class="wcfmmp_store_mobile_badges">
										<?php do_action( 'wcfmmp_store_mobile_badges', $store_user->get_id() ); ?>
										<div class="spacer"></div> 
									</div>
								<?php } ?>
								<div class="spacer"></div>  
							</div>
							
						<div class="spacer"></div>    
						</div>
						<div class="header_center">
							<div class="bd_icon_area">

								<?php if ( ( $WCFMmp->wcfmmp_vendor->get_vendor_name_position( $store_user->get_id() ) == 'on_banner' ) && apply_filters( 'wcfm_is_allow_store_name_on_banner', true ) ) { ?>
									<div class="banner_text">
										<?php do_action( 'wcfmmp_store_before_bannar_text', $store_user->get_id() ); ?>
										
										<h1><?php echo apply_filters( 'wcfmmp_store_title', $store_info['store_name'], $store_user->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h1>
										
										<?php do_action( 'wcfmmp_store_after_bannar_text', $store_user->get_id() ); ?>
									</div>
								<?php } ?>

								<div class="address">
									<?php if ( ( $WCFMmp->wcfmmp_vendor->get_vendor_name_position( $store_user->get_id() ) == 'on_header' ) || apply_filters( 'wcfm_is_allow_store_name_on_header', false ) ) { ?>
										<h1 class="wcfm_store_title">
										<?php echo apply_filters( 'wcfmmp_store_title', esc_html( $store_info['store_name'] ), $store_user->get_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
										<?php if ( apply_filters( 'wcfm_is_allow_badges_with_store_name', false ) ) { ?>
													<div class="wcfmmp_store_mobile_badges wcfmmp_store_mobile_badges_with_store_name">
														<?php do_action( 'wcfmmp_store_mobile_badges', $store_user->get_id() ); ?>
														<div class="spacer"></div> 
													</div>
												<?php } ?>
										</h1>
									<?php $store_address_info_class = 'header_store_name'; } ?>
									
									<?php do_action( 'before_wcfmmp_store_header_info', $store_user->get_id() ); ?>
									<?php do_action( 'wcfmmp_store_before_address', $store_user->get_id() ); ?>
									
									<?php if ( $address && ( $store_info['store_hide_address'] == 'no' ) && wcfm_vendor_has_capability( $store_user->get_id(), 'vendor_address' ) ) { ?>
										<p class="<?php echo esc_attr( $store_address_info_class ); ?> wcfmmp_store_header_address">
										<i class="wcfmfa fa-map-marker-alt" aria-hidden="true"></i>
										<?php
										if ( apply_filters( 'wcfmmp_is_allow_address_map_linked', true ) ) {
											$map_search_link = 'https://google.com/maps/place/' . rawurlencode( $address ) . '/@' . $store_lat . ',' . $store_lng . '&z=16';
											if ( wcfm_is_mobile() || wcfm_is_tablet() ) {
												$map_search_link = 'https://maps.google.com/?q=' . rawurlencode( $address ) . '&z=16';
											}
											?>
											<a href="<?php echo esc_url( $map_search_link ); ?>" target="_blank"><span><?php echo esc_attr( $address ); ?></span></a>
										<?php } else { ?>
												<?php echo esc_attr( $address ); ?>
											<?php } ?>
										</p>
									<?php } ?>
									
									<?php do_action( 'wcfmmp_store_after_address', $store_user->get_id() ); ?>
									
									<div class="<?php echo esc_attr( $store_address_info_class ); ?>">
										
										<?php do_action( 'wcfmmp_store_before_phone', $store_user->get_id() ); ?>
											
										<?php if ( $phone && ( $store_info['store_hide_phone'] == 'no' ) && wcfm_vendor_has_capability( $store_user->get_id(), 'vendor_phone' ) ) { ?>
											<div class="store_info_parallal wcfmmp_store_header_phone" style="margin-right: 10px;">
											<i class="wcfmfa fa-phone" aria-hidden="true"></i>
											<span>
												<?php if ( apply_filters( 'wcfmmp_is_allow_tel_linked', true ) ) { ?>
												<a href="tel:<?php echo esc_attr( $phone ); ?>"><?php echo esc_attr( $phone ); ?></a>
												<?php } else { ?>
													<?php echo esc_attr( $phone ); ?>
											<?php } ?>
											</span>
											</div>
										<?php } ?>
										
										<?php do_action( 'wcfmmp_store_after_phone', $store_user->get_id() ); ?>
										<?php do_action( 'wcfmmp_store_before_email', $store_user->get_id() ); ?>
										
										<?php if ( $email && ( $store_info['store_hide_email'] == 'no' ) && wcfm_vendor_has_capability( $store_user->get_id(), 'vendor_email' ) ) { ?>
											<div class="store_info_parallal wcfmmp_store_header_email">
											<i class="wcfmfa fa-envelope" aria-hidden="true"></i>
											<span>
												<?php if ( apply_filters( 'wcfmmp_is_allow_mailto_linked', true ) ) { ?>
												<a href="mailto:<?php echo apply_filters( 'wcfmmp_mailto_email', $email, $store_user->get_id() ); ?>"><?php echo esc_attr( $email ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
												<?php } else { ?>
													<?php echo esc_attr( $email ); ?>
												<?php } ?>
											</span>
											</div>
										<?php } ?>
										
										<?php do_action( 'wcfmmp_store_after_email', $store_user->get_id() ); ?>
										
										<div class="spacer"></div>  
									</div>
									
									<?php do_action( 'after_wcfmmp_store_header_info', $store_user->get_id() ); ?>
								</div>
							
								<?php do_action( 'before_wcfmmp_store_header_actions', $store_user->get_id() ); ?>
							
								<?php do_action( 'wcfmmp_store_before_follow_me', $store_user->get_id() ); ?>
								
								<?php
								if ( apply_filters( 'wcfm_is_pref_vendor_followers', true ) && apply_filters( 'wcfm_is_allow_store_followers', true ) && wcfm_vendor_has_capability( $store_user->get_id(), 'vendor_follower' ) ) {
									do_action( 'wcfmmp_store_follow_me', $store_user->get_id() );
								}
								?>
								
								<?php do_action( 'wcfmmp_store_after_follow_me', $store_user->get_id() ); ?>
								
								<?php do_action( 'after_wcfmmp_store_header_actions', $store_user->get_id() ); ?>
								
								<div class="spacer"></div>   
							</div>
						</div>
						<div class="header_right">
							<?php do_action( 'wcfmmp_store_before_enquiry', $store_user->get_id() ); ?>
								
							<?php if ( apply_filters( 'wcfm_is_pref_enquiry', true ) && apply_filters( 'wcfmmp_is_allow_store_header_enquiry', true ) && wcfm_vendor_has_capability( $store_user->get_id(), 'enquiry' ) ) { ?>
								<?php do_action( 'wcfmmp_store_enquiry', $store_user->get_id() ); ?>
							<?php } ?>
							
							<?php do_action( 'wcfmmp_store_after_enquiry', $store_user->get_id() ); ?>

							<?php if ( ! empty( $store_info['social'] ) && $store_user->has_social() && wcfm_vendor_has_capability( $store_user->get_id(), 'vendor_social' ) ) { ?>
								<div class="social_area">
									<?php
									$WCFMmp->template->get_template(
										'store/wcfmmp-view-store-social.php',
										array(
											'store_user' => $store_user,
											'store_info' => $store_info,
										)
									);
									?>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<?php
			do_action( 'wcfmmp_store_after_header', $store_user->get_id() );
		}
		?>

		<?php do_action( 'wcfmmp_after_store_header', $store_user->data, $store_info ); ?>
			
	<div class="body_area">
	
		<?php
		if ( ! apply_filters( 'wcfmmp_is_allow_mobile_sidebar_at_bottom', true ) ) {
			$WCFMmp->template->get_template(
				'store/wcfmmp-view-store-sidebar.php',
				array(
					'store_user' => $store_user,
					'store_info' => $store_info,
				)
			);
		}
		?>
			<div class="right_side 
			<?php
			if ( ! $WCFMmp->wcfmmp_vendor->is_store_sidebar() ) {
				echo 'right_side_full';}
			?>
			">
				<div id="tabsWithStyle" class="tab_area">
					
					<?php do_action( 'wcfmmp_before_store_tabs', $store_user->data, $store_info ); ?>
					
					<?php
					$WCFMmp->template->get_template(
						'store/wcfmmp-view-store-tabs.php',
						array(
							'store_user' => $store_user,
							'store_info' => $store_info,
							'store_tab'  => $store_tab,
						)
					);
					?>
					
					<?php do_action( 'wcfmmp_after_store_tabs', $store_user->data, $store_info ); ?>
					
					<?php
					switch ( $store_tab ) {
						case 'about':
							$WCFMmp->template->get_template(
								'store/wcfmmp-view-store-about.php',
								array(
									'store_user' => $store_user,
									'store_info' => $store_info,
								)
							);
							break;

						case 'policies':
							$WCFMmp->template->get_template(
								'store/wcfmmp-view-store-policies.php',
								array(
									'store_user' => $store_user,
									'store_info' => $store_info,
								)
							);
							break;

						case 'reviews':
							$WCFMmp->template->get_template(
								'store/wcfmmp-view-store-reviews.php',
								array(
									'store_user' => $store_user,
									'store_info' => $store_info,
								)
							);
							break;

						case 'followers':
							$WCFMmp->template->get_template(
								'store/wcfmmp-view-store-followers.php',
								array(
									'store_user' => $store_user,
									'store_info' => $store_info,
								)
							);
							break;

						case 'followings':
							$WCFMmp->template->get_template(
								'store/wcfmmp-view-store-followings.php',
								array(
									'store_user' => $store_user,
									'store_info' => $store_info,
								)
							);
							break;

						case 'articles':
								$WCFMmp->template->get_template(
									'store/wcfmmp-view-store-articles.php',
									array(
										'store_user' => $store_user,
										'store_info' => $store_info,
									)
								);
							break;

						default:
							$WCFMmp->template->get_template(
								apply_filters( 'wcfmmp_store_default_template', apply_filters( 'wcfmp_store_default_template', 'store/wcfmmp-view-store-products.php', $store_tab ), $store_tab ),
								array(
									'store_user' => $store_user,
									'store_info' => $store_info,
								),
								'',
								apply_filters( 'wcfmp_store_default_template_path', '', $store_tab )
							);
							break;
					}
					?>
					
				</div><!-- .tab_area -->
			</div><!-- .right_side -->
			
			<?php
			if ( apply_filters( 'wcfmmp_is_allow_mobile_sidebar_at_bottom', true ) ) {
				$WCFMmp->template->get_template(
					'store/wcfmmp-view-store-sidebar.php',
					array(
						'store_user' => $store_user,
						'store_info' => $store_info,
					)
				);
			}
			?>
			 
			<div class="spacer"></div>
	</div><!-- .body_area -->

	<div class="wcfm-clearfix"></div>
	</div><!-- .wcfmmp-store-page-wrap -->
	<div class="wcfm-clearfix"></div>
</div><!-- .wcfmmp-single-store-holder -->

<div class="wcfm-clearfix"></div>

<?php do_action( 'wcfmmp_after_store', $store_user->data, $store_info ); ?>
<?php // do_action( 'woocommerce_after_main_content' ); ?>
<?php echo '</main></div>'; ?>
<script>
jQuery(document).ready(function($) {
	$('#tab_links_area').find('a').each(function() {
		$(this).off('click').on('click', function() {
			window.location.href = $(this).attr('href');
		});
	});
});
</script>
<?php get_footer( 'shop' ); ?>
