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

?>
<div class="zf-form-group <?php if( isset($error["error"]) && $error["error"] == true ) echo 'zf-error'; ?> <?php if( isset($field["field_visibility"]) && $field["field_visibility"] == 'hidden' ) echo 'zf-hidden-field'; ?>" data-zombify-fieldgroup-path="<?php echo $field_input_path; ?>">
    <div class="zf-uploader <?php if( is_array( $field_value ) && count($field_value) > 0 ) echo 'zf-uploader-uploaded'; ?>">
        <button class="zf-remove-media zf-remove-media-trash"><i class="zf-icon-trash"></i></button>
        <div class="zf-get-url-popup">
            <a class="zf-popup-close" href="#"><i class="zf-icon-delete"></i></a>
            <div class="zf-popup_body">
                <div class="zf-form-group">
                    <label><?php esc_html_e("Paste Image URL", "zombify"); ?></label>
                    <div class="zf-form-group-popup">
                        <input class="zf-image_url" name="zombify[file_url]<?php echo $field_input_name; ?>" type="url" data-zombify-field-path="<?php echo $field_input_path; ?>_file_url">
                        <button class="zf-submit_url zf-button" type="button"><?php esc_html_e("Submit", "zombify"); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <label class="zf-image-label">
            <div class="zf-extensions">
                <?php
                if( isset($field["rules"]["extensions"]) ){

                    $valid_exts = explode(",", $field["rules"]["extensions"]);

                    foreach( $valid_exts as $ext ){
                        if( trim($ext) == 'jpeg' ) continue;
                        ?>
                        <span><?php echo trim($ext); ?></span>
                    <?php
                    }

                }
                ?>
            </div>
            <div class="zf-label">
                <i class="zf-icon zf-icon-image "></i>
                <span class="zf-label_text"><?php echo $field["label"]; ?></span>
                <span class="zf_or "><?php esc_html_e("or", "zombify"); ?></span>
                <a class="zf-get_url js-zf-get_url" href="#"><?php esc_html_e("Get by URL", "zombify"); ?></a>
            </div>
            <input type="file" name="zombify<?php echo $field_input_name; ?>[]" value="" <?php echo $attributes; ?> data-zombify-name-index="<?php echo $name_index ?>" data-zombify-field-path="<?php echo $field_input_path; ?>" <?php echo implode(" ", $validation_rules); ?>>

            <?php
            $url            = '';
            $attachment_id  = '';

            if( is_array( $field_value ) && count($field_value) > 0 && isset(zf_array_values($field_value)[0]) ) {
                $attachment_id  = zf_array_values($field_value)[0]["attachment_id"];
                $url = wp_get_attachment_url($attachment_id);
                $file_mime_type = get_post_mime_type($attachment_id);
            }

            if( is_array( $field_value ) && count($field_value) > 0 && isset(zf_array_values($field_value)[0]) && $file_mime_type == 'video/mp4' ) {
                echo zombify_get_video_tag($attachment_id, 'full', array( 'class' => 'gif-video-wrapper' ) );
            } else { ?>
                <img class="gif-video-wrapper zf-preview-gif" src="<?php echo $url; ?>" width="100%">
            <?php } ?>

            <div class="gif-video-wrapper zf-preview-gif-mp4" style="display: none">
                <video class="gif-video zf-gif-video-preview" poster="" loop="" muted="" autoplay="" width="100%" height="auto">
                    <source src="" type="video/mp4">
                    <?php __('Your browser does not support the video tag.', 'zombify'); ?>
                </video>
            </div>

            <img class="gif-video-wrapper zf-preview-img" src="" width="100%" style="display: none">

            <div class="zf-preview-video-block zf-image-preview-block" style="display: none;">
                <div class="zf-progressbar-container">
                    <div class="zf-progressbar">
                        <div class="zf-progressbar-active"></div>
                    </div>
                </div>
            </div>
        </label>
        <div class="zombify_uploaded_image_data">
        <?php
        if( is_array( $field_value ) && count($field_value) > 0 && ( !isset($error["error"]) || $error["error"] == false )  ){

                $nf_index = 0;

                foreach( $field_value as $findex=>$fvalue )
                {
                    if( !isset($fvalue['attachment_id']) || (int)$fvalue['attachment_id'] <= 0 ) continue;

                    ?>
                    <div class="zombify_uploaded_image_item">
                        <input type="hidden" name="zombify_existing_data<?php echo $field_input_name; ?>[<?php echo $nf_index+1000 ?>][attachment_id]" data-zf-media-id="<?php echo $fvalue['attachment_id']; ?>" value="<?php echo $fvalue['attachment_id'] ?>">
                        <input type="hidden" name="zombify_existing_data<?php echo $field_input_name; ?>[existingfile]" value="1">
                    </div>
                    <?php
                    $nf_index++;
                }

        }
        ?>
        </div>
    </div>
        <?php
        if( isset($error["error"]) && $error["error"] == true ){
            ?>
            <span class="zf-help"><?php echo isset($error["errorMessage"]) ? $error["errorMessage"] : __('Field contains errors', 'zombify') ?></span>
            <?php
        }


        ?>
</div>