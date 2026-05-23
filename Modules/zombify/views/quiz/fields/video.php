<?php
$field_input_name = $name_prefix;
$field_input_name .= $name_prefix!= '' ? '['.$name_index.']' : '';
$field_input_name .= '['.end($field_name).']';

$field_input_path = implode("/" , array_filter( explode("][", substr($field_input_name, 1, -1)), function($item){ return !is_numeric($item); } ) );

$validation_rules = array();

if( isset( $field["rules"] ) ){

    foreach( $field["rules"] as $rule_slug => $rule ){

        $validation_rules[] = 'zf-validation-'.$rule_slug.'="'.$rule.'"';

    }

}

$aliased_group_path = isset($args["aliased_group_path"]) ? $args["aliased_group_path"] : '';
$field_name_prefix = isset($args["field_name_prefix"]) ? $args["field_name_prefix"] : '';
$get_url_field_path = isset($args["get_url_field_path"]) ? $args["get_url_field_path"] : array();

?>
<div class="zf-form-group  <?php if( isset($error["error"]) && $error["error"] == true ) echo 'zf-error'; ?> <?php if( isset($field["field_visibility"]) && $field["field_visibility"] == 'hidden' ) echo 'zf-hidden-field'; ?>" data-zombify-fieldgroup-path="<?php echo $field_input_path; ?>">

    <div class="zf-uploader zf-uploader-video-audio <?php if( $field_value || isset( $this->data['video_external'] ) && $this->data['video_external'] !== '' ) echo 'zf-uploader-uploaded'; ?>">
        <button class="zf-remove-media zf-remove-media-trash"><i class="zf-icon-trash"></i></button>
        <input type="hidden" name="zombify<?php echo $field_input_name; ?>" data-zf-media-id="<?php echo $field_value; ?>" value="<?php echo $field_value; ?>" <?php echo $attributes; ?> data-zombify-name-index="<?php echo $name_index ?>" data-zombify-field-path="<?php echo $field_input_path; ?>" <?php echo implode(" ", $validation_rules); ?>>

        <!--div class="zf-get-url-popup">
            <a class="zf-popup-close" href="#"><i class="zf-icon-delete"></i></a>
            <div class="zf-popup_body">
                <div class="zf-form-group">
                    <label><?php printf( esc_html__("Paste %s URL", "zombify"), $field["label"] ); ?></label>

                    <div class="zf-form-group-popup">
                        <?php echo $this->renderField($this->fieldPath($get_url_field_path, $aliased_group_path, $path_prefix), $field_name_prefix, $name_index, $data, array('class' => 'zf-image_url'), '', array('showPlaceholder'=>false, 'showLabel'=>false), $path_prefix); ?>
                        <button class="zf-submit_url zf-button" type="button"><?php esc_html_e("Submit", "zombify"); ?></button>

                    </div>
                </div>
            </div>
        </div-->

        <div class="zf-image-label zf-video-label">
            <div class="zf-extensions">
                <?php
                if( isset($field["rules"]["extensions"]) ){

                    $valid_exts = explode(",", $field["rules"]["extensions"]);

                    foreach( $valid_exts as $ext ){
                        ?>
                        <span><?php echo trim($ext); ?></span>
                        <?php
                    }

                }
                ?>
            </div>
            <div class="zf-label">
                <i class="zf-icon zf-icon-type-<?php echo strtolower( $field["label"] ); ?>"></i>
                <a class="zf-label_text zf-video-upload" data-zombify-field-path="<?php echo $field_input_path; ?>"><?php printf( esc_html__("Upload %s", "zombify"), $field["label"] ); ?></a>

            </div>

        </div>

        <div class="zf-preview-video-block" style="display: <?php echo ( is_array( $field_value ) && count($field_value) > 0 ) ? 'block' : 'none'; ?>">
            <div class="zf-progressbar-container">
                <div class="zf-progressbar">
                    <div class="zf-progressbar-active"></div>
                </div>

                <div class="zf-progress-cancel-btn"><?php printf( esc_html__("Cancel", "zombify"), $field["label"] ); ?></div>
            </div>
        </div>

        <?php if( $field["field_format"] === 'video' ) { ?>
            <video controls width="100%" height="auto" src="" class="zf-video-player zf-video-player-preview zf_media_player"></video>
        <?php } ?>

        <?php if( $field["field_format"] === 'audio' ) { ?>
            <audio controls width="100%"  src="" class="zf-video-player zf-video-player-preview zf_media_player"></audio>
        <?php } ?>

        <?php if( $field_value ){

            if( $video_url = wp_get_attachment_url((int)$field_value) ) {

                $file_ext = strtolower( pathinfo($video_url, PATHINFO_EXTENSION) );

                if( in_array( $file_ext, zombify()->get_allowed_video_extensions() ) ){ ?>
                    <video controls width="100%" height="auto" src="<?php echo $video_url; ?>" class="zf-video-player zf-video-player-show-on-draft zf_media_player"></video>
                <?php }

                if( in_array( $file_ext, zombify()->get_allowed_audio_extensions() ) ){
                    ?>
                    <audio controls width="100%" src="<?= $video_url ?>" class="zf-video-player zf-video-player-show-on-draft zf_media_player"></audio>
                <?php
                }

            }
        } else if ( isset( $this->data['video_external'] ) && $this->data['video_external'] !== '' ) { ?>

            <?php if( strtolower( $field["label"] ) === 'video' ) { ?>
                <video controls width="100%" height="auto" src="<?php echo $this->data['video_external']; ?>" class="zf-video-player zf-video-player-show-on-draft zf_media_player"></video>
            <?php } ?>

            <?php if( strtolower( $field["label"] ) === 'audio' ) { ?>
                <audio controls width="100%" src="<?php echo $this->data['video_external']; ?>" class="zf-video-player zf-video-player-show-on-draft zf_media_player"></audio>
            <?php } ?>

        <?php } ?>

        <?php
            if( isset($error["error"]) && $error["error"] == true ){
                ?>
                <span class="zf-help"><?php echo isset($error["errorMessage"]) ? $error["errorMessage"] : __('Field contains errors', 'zombify') ?></span>
                <?php
            }

            ?>
    </div>

    <?php
    if( isset($field["rules"]["maxSize"]) ){
        ?>
        <p class="zf-file-info"><?php printf( esc_html__("Maximum upload file size: %s MB.", "zombify"), round($field["rules"]["maxSize"]/1024/1024, 2) ); ?></p>
        <?php
    }
    ?>
</div>