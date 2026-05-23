<?php
$field_input_name = $name_prefix;
$field_input_name .= $name_prefix!= '' ? '['.$name_index.']' : '';
$field_input_name .= '['.end($field_name).']';

$field_input_path = implode("/" , array_filter( explode("][", substr($field_input_name, 1, -1)), function($item){ return !is_numeric($item); } ) );

if( !isset($args["hideContainer"]) || $args["hideContainer"] == false ){
    ?>
    <div class="zf-form-group <?php if( isset($error["error"]) && $error["error"] == true ) echo 'zf-error'; ?>" data-zombify-fieldgroup-path="<?php echo $field_input_path; ?>">
    <?php
}

    if( isset($args["showLabel"]) && $args["showLabel"] ){
        ?>
        <label><?php echo $field["label"]; ?></label>
        <?php
    }

    if( isset($args["showPlaceholder"]) && $args["showPlaceholder"] ){
        $placeholder = 'placeholder="'.$field["label"].'"';
    } else {
        $placeholder = '';
    }
    ?>
    <input type="number" name="zombify<?php echo $field_input_name; ?>" value="<?= htmlspecialchars($field_value) ?>" data-zombify-name-index="<?php echo $name_index ?>" data-zombify-field-path="<?php echo $field_input_path; ?>" <?php echo $attributes; ?> <?php echo $placeholder; ?>>
    <?php
    if( isset($error["error"]) && $error["error"] == true ){
        ?>
        <span class="zf-help"><?php echo isset($error["errorMessage"]) ? $error["errorMessage"] : __('Field contains errors', 'zombify') ?></span>
        <?php
    }

if( !isset($args["hideContainer"]) || $args["hideContainer"] == false ) {
    ?>
    </div>
    <?php
}
?>