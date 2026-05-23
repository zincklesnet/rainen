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
    <div class="zf-uploader zf-featured-image <?php if( is_array( $field_value ) && count($field_value) > 0 ) echo 'zf-uploader-uploaded'; ?>">
        <button class="zf-remove-media"><i class="zf-icon-delete"></i></button>
        <label class="zf-image-label">
            <div class="zf-label">
                <i class="zf-icon zf-icon-image "></i>
                <span class="zf-label_text"><?php echo $field["label"]; ?></span>
            </div>
            <input type="file" name="zombify<?php echo $field_input_name; ?>[]" value="" <?php echo $attributes; ?> data-zombify-name-index="<?php echo $name_index ?>" data-zombify-field-path="<?php echo $field_input_path; ?>" <?php echo implode(" ", $validation_rules); ?>>
            <img src="<?php if( is_array( $field_value ) && count($field_value) > 0 ) echo wp_get_attachment_url( zf_array_values($field_value)[0]['attachment_id'] ); ?>" class="zf-preview-img" style="display: <?php echo ( is_array( $field_value ) && count($field_value) > 0 ) ? 'block' : 'none'; ?>">
            <div class="zf-preview-video-block zf-image-preview-block" style="display: none;">
                <div class="zf-progressbar-container">
                    <div class="zf-progressbar">
                        <div class="zf-progressbar-active"></div>
                    </div>
                </div>
            </div>
        </label>
        <?php
        if( is_array( $field_value ) && count($field_value) > 0 ){

            ?>
            <div class="zombify_uploaded_image_data">
                <?php
                $nf_index = 0;

                foreach( $field_value as $findex=>$fvalue )
                {

                    ?>
                    <div class="zombify_uploaded_image_item">
                        <input type="hidden" name="zombify_existing_data<?php echo $field_input_name; ?>[<?php echo $nf_index+1000 ?>][attachment_id]" value="<?php echo $fvalue['attachment_id'] ?>">
                        <input type="hidden" name="zombify_existing_data<?php echo $field_input_name; ?>[existingfile]" value="1">
                    </div>
                    <?php
                    $nf_index++;
                }
                ?>
            </div>
        <?php

        } else {
            ?>
            <div class="zombify_uploaded_image_data"></div>
            <?php
        }
        ?>
    </div>
    <?php
    if( isset($error["error"]) && $error["error"] == true ){
        ?>
        <span class="zf-help"><?php echo isset($error["errorMessage"]) ? $error["errorMessage"] : __('Field contains errors', 'zombify') ?></span>
        <?php
    }


    ?>
</div>