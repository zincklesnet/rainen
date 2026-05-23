<?php
$field_input_name = $name_prefix;
$field_input_name .= $name_prefix!= '' ? '['.$name_index.']' : '';
$field_input_name .= '['.end($field_name).']';

$path_arr = array_filter( explode("][", substr($field_input_name, 1, -1)), function($item){ return !is_numeric($item); } );

$field_input_path = implode("/" , $path_arr );
$field_input_path_name = end( $path_arr );

if( isset($field["show_dependency"]) && $field["show_dependency"] != '' ){

    if( count($path_prefix) > 0 ){

        if( is_array($field["show_dependency"]) ) {

            foreach( $field["show_dependency"] as $show_index=>$show_val ) {

                $field["show_dependency"][$show_index] = implode("/", $path_prefix) . '/' . $field["show_dependency"][$show_index];

            }

        } else {

            $field["show_dependency"] = implode("/", $path_prefix) . '/' . $field["show_dependency"];

        }

    }

    $show_dependency_attribute = 'data-zombify-show-dependency="'.$field["show_dependency"].'"';

} else {

    $show_dependency_attribute = "";

}



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
    <input type="text" name="zombify<?php echo $field_input_name; ?>" value="<?= htmlspecialchars($field_value) ?>" data-zombify-name-index="<?php echo $name_index ?>" data-zombify-field-path="<?php echo $field_input_path; ?>" data-zombify-field-name="<?php echo $field_input_path_name; ?>" <?php echo $attributes; ?> <?php echo $placeholder; ?> <?php echo $show_dependency_attribute; ?>>
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