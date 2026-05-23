<?php
$data_attributes = "data-zombify-group=\"".end($group_name)."\" ";
$data_attributes .= "data-zombify-group-path=\"".implode("___", $group_name)."\" ";
$data_attributes .= isset($args["include-group"]) ? "data-include-group=\"".$args["include-group"]."\" " : "";
$data_attributes .= isset($args["reverse_order"]) && $args["reverse_order"] == 1 ? "data-zombify-position=\"first\" " : "";
?>
<button class="<?php if( isset($args["class"]) ) echo $args["class"]; else echo 'zf-add-button'; ?> zombify_add_group" <?php echo $data_attributes ?>>
    <?php if( isset($args["beforeText"]) ) echo $args["beforeText"]; ?>
    <?php echo $label; ?>
    <?php if( isset($args["afterText"]) ) echo $args["afterText"]; ?>
</button>