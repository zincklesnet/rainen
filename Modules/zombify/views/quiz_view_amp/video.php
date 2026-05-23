<?php
$video_data     = zf_array_values($data["video"])[0];
$poster_img_url = isset( $video_data['videofile'] ) ? zf_get_video_image_url( $video_data['videofile'] ) : false;
?>


<div id="zfWrap">
    <figure class="zfMedia zf-<?php echo $video_data["mediatype"]; ?>">
        <?php
        if( $video_data["mediatype"] == "image" ){

            if( (int)$video_data["videofile"] > 0 ){

                if( $video_url = wp_get_attachment_url((int)$video_data["videofile"]) ) {

                    $file_ext = strtolower( pathinfo($video_url, PATHINFO_EXTENSION) );

                    if( in_array( $file_ext, zombify()->get_allowed_video_extensions() ) ){
                        ?>
                        <div class="zf-video-wrapper">
                            <?php echo zombify_mejs_video( $video_url, 'mejs__player zf-video-player zf-video-player-front', array( 'poster' => $poster_img_url, 'video_id' => (int)$video_data["videofile"] ) ); ?>
                        </div>
                    <?php
                    }

                    if( in_array( $file_ext, zombify()->get_allowed_audio_extensions() ) ){
                        ?>
                        <div class="zf-audio-wrapper">
                            <?php echo zombify_mejs_audio( $video_url, 'mejs__player zf-video-player zf-video-player-front' ); ?>
                        </div>
                    <?php
                    }

                }

            } else if ( isset($video_data['video_external']) && $video_data['video_external'] !== '' ) {
                ?>
                <div class="zf-video-wrapper">
                    <?php echo zombify_mejs_video( 'video', $video_data['video_external'], 'mejs__player zf-video-player zf-video-player-front' ); ?>
                </div>
            <?php
            }

        } else {
            ?>
            <div class="zf-embedded-url"><?php echo Zombify_BaseQuiz::renderEmbed( $video_data, true ); ?></div>
            <?php
        } ?>

        <?php
        if (isset($video_data["video_credit"])) { ?>
            <figcaption class="zfCaption">
                <cite class="zfCredit"><?php zf_showCredit($video_data["video_credit"], $video_data["video_credit_text"]); ?></cite>
            </figcaption>
        <?php } ?>
    </figure>

	<?php echo $video_data["video_description"]; ?>


    <?php do_action( 'zombify_after_post_layout' ); ?>
</div>