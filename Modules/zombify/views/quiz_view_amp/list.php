<?php
$items_per_page = zf_get_items_per_page( $zf_shortcode_args );
$current_page   = zf_get_current_page( $zf_shortcode_args );
?>
<div id="zfWrap">
	<ol class="zfListSet">
		<?php
		$index        = ( $current_page - 1 ) * $items_per_page + 1;
		$data["list"] = array_slice( $data["list"], ( $current_page - 1 ) * $items_per_page, $items_per_page );
		if ( isset( $data["list"] ) ) {
			foreach ( $data["list"] as $list ) { ?>
				<li class="zfSetItem">
					<h3 class="zfTitle zfTitleNum">
						<span class="zfNum"><?php echo $index; ?></span><?php echo $list["title"]; ?>
					</h3>
					<?php
					if ( ( $list["mediatype"] == 'image' && isset( zf_array_values( $list["image"] )[0]["attachment_id"] ) ) || ( $list["mediatype"] == 'embed' && $list["embed_url"] != '' ) ) {
						switch ( $list["mediatype"] ) {
							case "image":
								$zf_media_html = '';
								if ( isset( zf_array_values( $list["image"] )[0]["attachment_id"] ) ) {
									$zf_media_html = zombify_get_img_tag( zf_array_values( $list["image"] )[0]["attachment_id"], 'full' );
								}
								break;
							case "embed":
								$zf_media_html = sprintf( '<div class="zf-embedded-url">%s</div>', Zombify_BaseQuiz::renderEmbed( $list, true ) );
								break;
							default:
								$zf_media_html = '';
						} ?>
						<figure class="zfMedia <?php echo "zf-" . $list["mediatype"]; ?>">
							<?php
							echo $zf_media_html;

							if ( isset( $list["image_credit"] ) ) { ?>
								<figcaption class="zfCaption">
									<cite class="zfCredit"><?php zf_showCredit( $list["image_credit"], $list["image_credit_text"] ); ?></cite>

									<?php if ( isset( $list["affiliate"] ) && $list["affiliate"] && $list["affiliate_url"] != '' ) { ?>
										<a class="zfBtnBuy" href="<?php echo $list["affiliate_url"]; ?>" target="_blank" rel="nofollow noopener">
											<span><?php esc_html_e( "Buy Now", "zombify" ); ?></span>
										</a>
									<?php } ?>
								</figcaption>
							<?php } ?>
						</figure>
						<?php

					}

					if ( $list["description"] ) {
						echo $list["description"];
					}
					?>
				</li>
				<?php
				$index ++;
			}
		} ?>
	</ol>

	<?php do_action( 'zombify_after_post_layout' ); ?>
</div>


