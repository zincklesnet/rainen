<?php
$data  = $story_data;
$index = count( $data["list"] );
?>
<ol class="zfListSet">
	<?php
	if ( isset( $data["list"] ) ) {
		foreach ( $data["list"] as $list ) {
			?>
			<li class="zfSetItem">
				<h3 class="zfTitle zfTitleNum">
					<span class="zfNum"><?php echo $index; ?></span>
					<?php echo $list["title"]; ?>
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
									<a class="zfBtnBuy" href="<?php echo $list["affiliate_url"]; ?>" target="_blank" rel="nofollow noopener"><?php esc_html_e( "Buy Now", "zombify" ); ?></a>
								<?php } ?>
							</figcaption>
						<?php } ?>
					</figure>
					<?php
				}

				if ( $list["description"] ) {
					echo $list["description"];
				} ?>
			</li>
			<?php
			$index --;
		}
	} ?>
</ol>