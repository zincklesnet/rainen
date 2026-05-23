<?php
$field_input_name = $name_prefix;
if( !isset($args["use_index_as_value"]) || !$args["use_index_as_value"] ) $field_input_name .= $name_prefix!= '' ? '['.$name_index.']' : '';
$field_input_name .= '['.end($field_name).']';

$field_input_path = implode("/" , array_filter( explode("][", substr($field_input_name, 1, -1)), function($item){ return !is_numeric($item); } ) );

$is_checked = false;
if(
    ( $field_value != '' && $field_value == $args['value'] && (!isset($args["use_index_as_value"]) || !$args["use_index_as_value"]) ) ||
    ( $field_value!='' && $field_value == $name_index && isset($args["use_index_as_value"]) && $args["use_index_as_value"]==1 ) ||
    ( $field_value == '' && isset($args['checked']) && $args['checked'] ) ||
    ( isset($args['checked_important']) && $args['checked_important'] )
){
    $is_checked = true;
}

if( isset($args["showContainer"]) && $args["showContainer"] == true ){
    ?>
    <div class="zf-form-group <?php if (isset($error["error"]) && $error["error"] == true) echo 'zf-error'; ?>"
     data-zombify-fieldgroup-path="<?php echo $field_input_path; ?>">
    <?php
}
?>

<input type="radio" name="zombify<?php echo $field_input_name; ?>" value="<?php if( !isset($args["use_index_as_value"]) || !$args["use_index_as_value"] ) echo $args['value']; else echo $name_index; ?>" <?php echo $attributes; ?> data-zombify-name-index="<?php echo $name_index ?>" data-zombify-field-path="<?php echo $field_input_path; ?>" <?php if( isset($args['format']) ){ ?>data-format="<?php echo $args['format']; ?>"<?php } ?> <?php if($is_checked) echo 'checked'; ?> class="<?= isset($args["class"]) ? $args["class"] : '' ?>" <?= isset($args["style"]) ? 'style="'.$args["style"].'"' : '' ?> <?= isset($args["use_index_as_value"]) && $args["use_index_as_value"]==1 ? 'data-zf-use-index-as-value="1"' : '' ?>>
<?php
echo $args["label"];

if( isset($error["error"]) && $error["error"] == true && isset($args["showerrors"]) && $args["showerrors"] ){
    ?>
    <span class="zf-help"><?php echo isset($error["errorMessage"]) ? $error["errorMessage"] : __('Field contains errors', 'zombify') ?></span>
    <?php
}

if( isset($args["showContainer"]) && $args["showContainer"] == true ) {
    ?>
    </div>
    <?php
}