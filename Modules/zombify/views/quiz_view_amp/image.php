<div id="zfWrap">
	<?php
	if ( isset( zf_array_values( $data["image_image"] )[0]["attachment_id"] ) && zf_array_values( $data["image_image"] )[0]["attachment_id"] ) {
		$zf_media_html = zombify_get_img_tag( zf_array_values( $data["image_image"] )[0]["attachment_id"], 'full' );
		?>
		<figure class="zfMedia zfimage">
			<?php
			echo $zf_media_html;

			if ( isset( $data["image_credit"] ) ) { ?>
				<figcaption class="zfCaption">
					<cite class="zfCredit"><?php zf_showCredit( $data["image_credit"], $data["image_credit_text"] ); ?></cite>
				</figcaption>
			<?php } ?>
		</figure>
		<?php
	}
	echo $data["image_description"];
	do_action( 'zombify_after_post_layout' ); ?>
</div>