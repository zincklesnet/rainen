<?php
$audio_data = zf_array_values($data["audio"])[0];
$figure_class   = 'zf-audio_media zf-media-wrapper';
$figure_class .= ( 'image' == $audio_data["mediatype"] ) ? ' zf-type-audio' : ' zf-type-embed'; ?>

<div id="zombify-main-section-front" class="<?php echo zombify_get_front_main_section_classes( 'zombify-main-section-front zombify-screen' ); ?>">
    <div class="zf-container">
        <div id="zf-audio" class="zf-audio">
            <figure class="<?php echo esc_attr( $figure_class ); ?>">
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
                    <figcaption class="zf-figcaption">
                        <cite><?php zf_showCredit($audio_data["audio_credit"], $audio_data["audio_credit_text"]); ?></cite>
                    </figcaption>
                <?php } ?>
            </figure>
            <div class="zf-list_description"><?php echo $audio_data["audio_description"]; ?></div>
        </div>
    </div>

    <?php do_action( 'zombify_after_post_layout' ); ?>

</div>