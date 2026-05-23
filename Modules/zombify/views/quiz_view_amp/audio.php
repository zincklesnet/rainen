<div id="zfWrap">
<?php
$audio_data = zf_array_values($data["audio"])[0];
?>

<figure class="zf-audio_media zf-<?php echo $audio_data["mediatype"]; ?>">
    <?php
    if( $audio_data["mediatype"] == "image" ){

        if( (int)$audio_data["videofile"] > 0 ){

            if( $video_url = wp_get_attachment_url((int)$audio_data["videofile"]) ) {

                $file_ext = strtolower( pathinfo($video_url, PATHINFO_EXTENSION) );

                if( in_array( $file_ext, zombify()->get_allowed_video_extensions() ) ){
                    ?>
                    <div class="zf-video-wrapper">
                        <?php echo zombify_mejs_video( $video_url, 'mejs__player zf-video-player zf-video-player-front', array( 'video_id' => (int)$audio_data["videofile"] ) ); ?>
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

        } else if ( isset($audio_data['video_external']) && $data['video_external'] !== '' ) {
            ?>
            <div class="zf-audio-wrapper">
                <?php echo zombify_mejs_audio( $audio_data['video_external'], 'mejs__player zf-video-player zf-video-player-front' ); ?>
            </div>
        <?php
        }

    } else { ?>
        <div class="zf-embedded-url"><?php echo Zombify_BaseQuiz::renderEmbed( $audio_data, true ); ?></div>
    <?php
    }

    if (isset($audio_data["audio_credit"])) { ?>
        <figcaption class="zfCaption">
            <cite class="zfCredit"><?php zf_showCredit($audio_data["audio_credit"], $audio_data["audio_credit_text"]); ?></cite>
        </figcaption>
    <?php } ?>
</figure>

<div class="zf-list_description"><p><?php echo $audio_data["audio_description"]; ?></p></div>

<?php do_action( 'zombify_after_post_layout' ); ?>
</div>