<h3 class="zfTitle"><?php echo $question["question"]; ?></h3>
<?php if ( ( $question["mediatype"] == 'image' && isset( zf_array_values( $question["image"] )[0]["attachment_id"] ) ) || ( $question["mediatype"] == 'embed' && $question["embed_url"] != '' ) ) {
	switch ( $question["mediatype"] ) {
		case "image":
			$zf_media_html = '';
			if ( isset( zf_array_values( $question["image"] )[0]["attachment_id"] ) ) {
				$zf_media_html = zombify_get_img_tag( zf_array_values( $question["image"] )[0]["attachment_id"], 'full' );
			}
			break;
		case "embed":
			$zf_media_html = sprintf( '<div class="zf-embedded-url">%s</div>' );
			break;
		default:
			$zf_media_html = '';

	} ?>
	<p class="zfMedia <?php echo "zf" . $question["mediatype"]; ?>">
		<?php
		echo $zf_media_html;

		if ( isset( $question["image_credit"] ) && $question["image_credit"] ) { ?>
			<cite class="zfCredit">
				<a href="<?php echo $question["image_credit"]; ?>"
				   target="_blank"
				   rel="nofollow noopener"><?php echo ( isset( $question["image_credit_text"] ) && $question["image_credit_text"] ) ? $question["image_credit_text"] : __( 'Credit', 'zombify' ); ?></a>
			</cite>
		<?php } ?>
	</p>
<?php } ?>
<?php if ( $question["description"] ) {
	echo $question["description"];
} ?>