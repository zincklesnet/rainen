<?php
$field_input_name = $name_prefix;
$field_input_name .= $name_prefix!= '' ? '['.$name_index.']' : '';
$field_input_name .= '['.end($field_name).']';

$field_input_path = implode("/" , array_filter( explode("][", substr($field_input_name, 1, -1)), function($item){ return !is_numeric($item); } ) );

$dependency_options = array();
$dependency_attribute = '';

if( isset($field["dependency"]) && $field["dependency"] != '' ){

    if( count($path_prefix) > 0 ){

        $field["dependency"] = implode("/", $path_prefix).'/'.$field["dependency"];

    }

    $dependency_attribute = 'data-zombify-dependency="'.$field["dependency"].'"';

    $dependencies = explode("/", $field["dependency"]);

    if( count( $dependencies ) == 1 ) {

        $dependency_options[0] = $this->data[ $dependencies[0] ];

    }

    if( count( $dependencies ) == 2 ) {

        if( isset( $this->data[ $dependencies[0] ] ) )

            foreach( $this->data[ $dependencies[0] ] as $dep_index=>$dep_arr ){

                $dependency_options[ $dep_index ] = $dep_arr[ $dependencies[1] ];

            }

    }

}
?>
<div class="zf-form-group <?php if( isset($error["error"]) && $error["error"] == true ) echo 'zf-error'; ?>" data-zombify-fieldgroup-path="<?php echo $field_input_path; ?>">
    <?php
    if( isset($args["showLabel"]) && $args["showLabel"] ){
        ?>
        <label><?php echo $field["label"]; ?></label>
        <?php
    }
    ?>
    <select name="zombify<?php echo $field_input_name; ?>" data-zf-seled-val="<?php echo $field_value; ?>" data-zombify-name-index="<?php echo $name_index ?>" data-zombify-field-path="<?php echo $field_input_path; ?>" <?php echo $attributes; ?> <?php echo $dependency_attribute; ?>>
        <?php
        if( !isset( $field["empty_value"] ) || $field["empty_value"] == true ){
            ?>
            <option value=""><?php echo ( isset($args["showPlaceholder"]) && $args["showPlaceholder"] ) ? $field["label"] : '' ?></option>
            <?php
        }

        if( isset( $field["default_value"] ) )
            foreach( $field["default_value"] as $option_value => $option_label ){
                ?>
                <option value="<?php echo $option_value; ?>" <?php echo ( $field_value == $option_value && $field_value!='' ) ? 'selected' : '' ?>><?php echo $option_label ?></option>
                <?php
            }

        if( count($dependency_options) > 0 )
            foreach( $dependency_options as $option_value => $option_label ){
                ?>
                <option value="<?php echo $option_value; ?>" <?php echo ( $field_value == $option_value && $field_value!='' ) ? 'selected' : '' ?>><?php echo $option_label ?></option>
            <?php
            }
        ?>
    </select>
    <?php
    if( isset($error["error"]) && $error["error"] == true ){
        ?>
        <span class="zf-help"><?php echo isset($error["errorMessage"]) ? $error["errorMessage"] : __('Field contains errors', 'zombify') ?></span>
        <?php
    }
    ?>
</div>