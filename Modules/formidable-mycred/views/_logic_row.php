<tr id="mycred_frm_<?php echo $field_id .'_'. $meta_name ?>" class="mycred_frm_logic_row"  >
    <td colspan="2">
<span><a href="javascript:void(0)" id="remove_<?php echo $field_id .'_'. $meta_name; ?>" class="mycred_frm_remove_tag"> x </a></span>
&nbsp;
    <?php
    $hide_field = (isset($values['frm_mycred'][$field_id]['logic'][$meta_name]['logicfld']))? $values['frm_mycred'][$field_id]['logic'][$meta_name]['logicfld']: 0;

    ?>
<select name="options[frm_mycred][<?php echo $field_id; ?>][logic][<?php echo $meta_name; ?>][logicfld]" id="mycred_frm_logicfld_fld_<?php echo $field_id; ?>_<?php echo $meta_name; ?>" onchange="frmMCGetFieldValues(this.value,'<?php echo $field_id; ?>',<?php echo $meta_name ?>)" <?php //echo $disabled; ?>>
    <option value="">- <?php _e('Select Field', 'formidable') ?> -</option>

    <?php foreach ($values['fields'] as $ff){
        if(!in_array($ff['type'], array('select','radio','checkbox','10radio','scale','data')))
            continue;
        $selected = ($ff['id'] == $hide_field) ?' selected="selected"':''; ?>
    <option value="<?php echo $ff['id'] ?>"<?php echo $selected ?>><?php echo $ff['name']; ?></option>
    <?php } ?>
</select>
<?php _e('is', 'formidable'); 

if(!isset($values['frm_mycred'][$field_id]['logic'][$meta_name]))
    $values['frm_mycred'][$field_id]['logic'][$meta_name] = array('cond' => '==');

if(!isset($values['frm_mycred'][$field_id]['logic'][$meta_name]['cond']))
    $values['frm_mycred'][$field_id]['logic'][$meta_name]['cond'] = '==';
?>

<select name="options[frm_mycred][<?php echo $field_id; ?>][logic][<?php echo $meta_name; ?>][cond]" id="mycred_frm_cond_fld_<?php echo $field_id; ?>_<?php echo $meta_name; ?>" onchange=" frmLogicChange(this.value,<?php echo $field_id; ?>,<?php echo $meta_name ?>,'mycred_frm_opt_fld_<?php echo $field_id; ?>_<?php echo $meta_name; ?>')" <?php //echo $disabled; ?>>
    <option value="==" <?php selected($values['frm_mycred'][$field_id]['logic'][$meta_name]['cond'], '==') ?>><?php _e('equal to', 'formidable') ?></option>
    <option value="!=" <?php selected($values['frm_mycred'][$field_id]['logic'][$meta_name]['cond'], '!=') ?>><?php _e('NOT equal to', 'formidable') ?> &nbsp;</option>
    <option value=">" <?php selected($values['frm_mycred'][$field_id]['logic'][$meta_name]['cond'], '>') ?>><?php _e('greater than', 'formidable') ?></option>
    <option value="<" <?php selected($values['frm_mycred'][$field_id]['logic'][$meta_name]['cond'], '<') ?>><?php _e('less than', 'formidable') ?></option>
</select>
<?php // print_r (maybe_unserialize($values['fields'])); ?>
<span id="frm_show_selected_values_<?php echo $field_id ?>_<?php echo $meta_name ?>" class="no_taglist">
    <?php if ($hide_field and is_numeric($hide_field)){
        $selected_opt = $values['frm_mycred'][$field_id]['logic'][$meta_name]['opt'];
		// echo  $selected_opt;
		//print_r($values['frm_mycred']);
        foreach($values['fields'] as $fld) if($fld['id'] == $hide_field) $fld_vals = maybe_unserialize($fld['options']);
        foreach($values['fields'] as $fld) if($fld['id'] == $hide_field) $type = $fld['type'];
        include('_field_values.php');
    } ?>
</span>
    </td>
</tr>