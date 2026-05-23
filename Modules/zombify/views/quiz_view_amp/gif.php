<div id="zfWrap">
	<?php
	$gif_data = zf_array_values( $data["gif"] )[0];

	if ( isset( zf_array_values( $gif_data["image_image"] )[0]["attachment_id"] ) && zf_array_values( $gif_data["image_image"] )[0]["attachment_id"] ) {
		$file_mime_type = get_post_mime_type( zf_array_values( $gif_data["image_image"] )[0]["attachment_id"] );

		if ( $file_mime_type == 'video/mp4' ) {
			$zf_media_html = zombify_get_video_tag( zf_array_values( $gif_data["image_image"] )[0]["attachment_id"], 'full' );
		} else {
			$zf_media_html = zombify_get_img_tag( zf_array_values( $gif_data["image_image"] )[0]["attachment_id"], 'full' );
		} ?>
		<figure class="zfMedia zfImg">
			<?php
			echo $zf_media_html;

			if ( isset( $gif_data["image_credit"] ) ) { ?>
				<figcaption class="zfCaption">
					<cite class="zfCredit"><?php zf_showCredit( $gif_data["image_credit"], $gif_data["image_credit_text"] ); ?></cite>
				</figcaption>
			<?php } ?>
		</figure>
	<?php }

	echo $gif_data["image_description"];
	do_action( 'zombify_after_post_layout' ); ?>
</div>