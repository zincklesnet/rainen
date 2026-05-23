<?php // print_r($fld_vals); //echo $type; 
	
	
	 
	 
?>

<select id="mycred_frm_opt_fld_<?php echo $field_id; ?>_<?php echo $meta_name; ?>" name="options[frm_mycred][<?php echo $field_id; ?>][logic][<?php echo $meta_name; ?>][opt]" onchange="frmLogicChange(this.value,<?php echo $field_id; ?>,<?php echo $meta_name ?>,'mycred_frm_opt_fld_<?php echo $field_id; ?>_<?php echo $meta_name; ?>')" <?php //echo $disabled; ?>>
    <option value="">- <?php _e('select value','formidable'); ?> -</option>

    <?php

    if($type == 'checkbox'){
		 
        foreach($fld_vals as $fv){
            $selected = '';
			 
			 
				if(!is_array($fv))
				{
					if(isset($selected_opt) and !empty($selected_opt))
                $selected = (FrmMCSettingsController::check_perfect_match($selected_opt,$fv))? 'selected':''; 
				?>
				<option value="<?php echo $fv; ?>" <?php echo $selected; ?>><?php echo $fv; ?></option>


				
				
			
				<?php  
				}
				else
				{
					 // BACKWORD COMPITIBILITY  
				if($fv['label'] == '') continue;
				if(isset($selected_opt) and !empty($selected_opt))
							$selected = ($selected_opt == $fv['label'])? 'selected':'';  
					?>
					<option value="<?php echo $fv['label']; ?>" <?php echo $selected; ?>><?php echo $fv['label']; ?></option>
						
				<?php 
				}
			 
		}
    }elseif($type == 'radio'){
        foreach($fld_vals as $fv){
            $selected = '';
          
			if(!is_array($fv))
			{
				if(isset($selected_opt) and !empty($selected_opt))
			$selected = (FrmMCSettingsController::check_perfect_match($selected_opt,$fv))? 'selected':''; 
			?>
			<option value="<?php echo $fv; ?>" <?php echo $selected; ?>><?php echo $fv; ?></option>


			
			
		
			<?php  
			}
			else
			{
				 // BACKWORD COMPITIBILITY  
				if($fv['label'] == '') continue;
				if(isset($selected_opt) and !empty($selected_opt))
						$selected = ($selected_opt == $fv['label'])? 'selected':'';  
				?>
				<option value="<?php echo $fv['label']; ?>" <?php echo $selected; ?>><?php echo $fv['label']; ?></option>
					
			<?php 
			}
			 
         }
    }elseif($type == 'data'){
        //echo 'got in';
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table = $prefix.'frm_fields';
        $field_valz = maybe_unserialize($wpdb->get_var("SELECT field_options FROM $table WHERE id=$hide_field"));
        $op = (int)$field_valz['form_select'];
        $table = $prefix.'frm_item_metas';
        $the_opts = $wpdb->get_results("SELECT meta_value FROM $table WHERE field_id=$op",ARRAY_A);
        //var_dump($field_valz);
        //var_dump($the_opts);
        foreach($the_opts as $key => $op){
            $selected = '';
            if($op['meta_value'] == '') continue;
            if(isset($selected_opt) and !empty($selected_opt))
                $selected = ($selected_opt == $op['meta_value'])? 'selected':'';
            ?>
            <option value="<?php echo $op['meta_value']; ?>" <?php echo $selected; ?>><?php echo $op['meta_value']; ?></option>
        <?php }
    }else{
        foreach($fld_vals as $fv){
            $selected = '';
            if($fv == '') continue;
            if(isset($selected_opt) and !empty($selected_opt))
                $selected = ($selected_opt == $fv)? 'selected':'';
            ?>
        <option value="<?php echo $fv; ?>" <?php echo $selected; ?>><?php echo $fv; ?></option>
        <?php }
    }?>
</select>