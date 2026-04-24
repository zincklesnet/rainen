<?php
/**
 * Template part for social share a post
 *
 * @package Reign
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

$post_social = get_theme_mod( 'single_post_social_box', true );

if ( empty( $post_social ) ) {
	return;
}

$post_social_link = get_theme_mod( 'single_post_social_link', array( 'facebook', 'twitter' ) );

if ( isset( $post_social_link ) && ! empty( $post_social_link ) ) {
	?>
	<div class="reign-social-box-wrap">
		<div class="reign-social-box">
			<ul class="reign-post-social-share">
				<?php
				foreach ( $post_social_link as $social ) {
					switch ( $social ) {
						case 'facebook':
							?>
							<li>
								<a class="btn-link btn-icon--left social-facebook" href="http://www.facebook.com/sharer.php?u=<?php echo rawurlencode( get_the_permalink() ); ?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=220,width=600'); return false;" target="_blank" title="<?php esc_attr_e( 'Facebook', 'reign' ); ?>">
									<i class="fab fa-facebook-f"></i>
								</a>
							</li>
							<?php
							break;
						case 'twitter':
							?>
							<li>
								<a class="btn-link btn-icon--left social-twitter" href="https://twitter.com/share?url=<?php echo rawurlencode( get_the_permalink() ); ?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=260,width=600'); return false;" target="_blank" title="<?php esc_attr_e( 'Twitter', 'reign' ); ?>">
									<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M8 2H1L9.26086 13.0145L1.44995 21.9999H4.09998L10.4883 14.651L16 22H23L14.3917 10.5223L21.8001 2H19.1501L13.1643 8.88578L8 2ZM17 20L5 4H7L19 20H17Z"></path></svg>
								</a>
								</li>
								<?php
							break;
						case 'pinterest':
							$params = array(
								'media=' . ( function_exists( 'the_post_thumbnail' ) ? wp_get_attachment_url( get_post_thumbnail_id() ) : '' ),
								'description=' . strip_tags( get_the_title() ),
							);
							?>
								<li>
									<a class="btn-link btn-icon--left social-pinterest" href="http://pinterest.com/pin/create/button/?url=<?php echo rawurlencode( get_the_permalink() ) . '&' . implode( '&', $params ); ?>" target="_blank" data-pin-custom="true" data-pin-do="buttonBookmark" title="<?php esc_attr_e( 'Pinterest', 'reign' ); ?>">
										<i class="fab fa-pinterest-p"></i>
									</a>
								</li>
								<?php
							break;
						case 'linkedin':
							?>
								<li>
									<a class="btn-link btn-icon--left social-linkedin" href="https://www.linkedin.com/shareArticle?url=<?php echo rawurlencode( get_the_permalink() ); ?>" onclick="javascript:window.open(this.href,'', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600'); return false;" target="_blank" title="<?php esc_attr_e( 'LinkedIn', 'reign' ); ?>">
										<i class="fab fa-linkedin-in"></i>
									</a>
								</li>
								<?php
							break;
						case 'whatsapp':
							$whatsapp_url = 'https://web.whatsapp.com/send';
							if ( wp_is_mobile() ) {
								$whatsapp_url = 'https://api.whatsapp.com/send';
							}
							?>
								<li>
									<a class="btn-link btn-icon--left social-whatsapp" href="<?php echo esc_attr( $whatsapp_url ) . '?text=' . rawurlencode( get_the_title() . ' ' . get_the_permalink() ); ?>" target="_blank" title="<?php esc_attr_e( 'WhatsApp', 'reign' ); ?>">
										<i class="fab fa-whatsapp"></i>
									</a>
								</li>
								<?php
							break;
						default:
							return;
					}
				}
				?>
			</ul>
		</div>
	</div>
	<?php
	wp_reset_postdata();
}
