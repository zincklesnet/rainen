<?php
$field_input_name = $name_prefix;
$field_input_name .= $name_prefix!= '' ? '['.$name_index.']' : '';
$field_input_name .= '['.end($field_name).']';

$path_arr = array_filter( explode("][", substr($field_input_name, 1, -1)), function($item){ return !is_numeric($item); } );

$field_input_path = implode("/" , $path_arr );
$field_input_path_name = end( $path_arr );

$default_val = isset($args["default_val"]) ? $args["default_val"] : "";

if( isset($args["return_only_value"]) ){

    echo $field_value ? $field_value : $default_val;

} else {
    ?>
    <input type="hidden" name="zombify<?php echo $field_input_name; ?>"
           value="<?= htmlspecialchars( $field_value ? $field_value : $default_val ) ?>" data-zombify-name-index="<?php echo $name_index ?>"
           data-zombify-field-path="<?php echo $field_input_path; ?>" data-zombify-field-name="<?php echo $field_input_path_name; ?>" <?php echo $attributes; ?>>
<?php
}