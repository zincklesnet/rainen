<?php

class FrmMCAction extends FrmFormAction {

    function __construct() {
        $action_ops = array(
            'classes'   => 'fa-university',
            'limit'     => 99,
            'active'    => true,
            'force_event' => true,
            'priority'  => 33
        );
		
        $this->FrmFormAction('MyCRED', __('MyCRED', 'formidable'), $action_ops);
		
		
		 
		
    }
	 
	
	 
    function form( $form_action, $args = array() ) {
        
		
		
		extract($args);
		 
        global $wpdb;

        //$frm_field = new FrmField();
        //$fields = $frm_field->getAll($wpdb->prepare('fi.form_id=%d', $form->id) . " and fi.type not in ('end_divider', 'divider', 'html', 'break', 'captcha', 'rte')", ' ORDER BY field_order');
        //echo '<pre>';
        //print_r($args);
        $frmdb = new FrmDb();
        $values = maybe_unserialize($frmdb->get_var($frmdb->forms, array('id' => $form->id), 'options'));

        $form_id = $form->id;
        $f = $field['id'] = $action_key;
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table = $prefix.'frm_fields';
        $frmdb = new FrmDb();
        $values = maybe_unserialize($frmdb->get_var($frmdb->forms, array('id' => $form->id), 'options'));
        if(!isset($values['fields'])){
            $values['fields'] = $wpdb->get_results("SELECT id,name,type,options,field_options FROM $table WHERE form_id=$form->id",ARRAY_A);
        }
		
        $optiondb = $prefix.'options';
        $names = $wpdb->get_results("SELECT option_value FROM $optiondb WHERE option_name LIKE 'mycred_pref_core%'",ARRAY_A);
        $types = array();
        $counter = 0;
        foreach($names as $name){
            $counter++;
            foreach(maybe_unserialize($name['option_value']) as $k => $op){
                if($k == "name") {
                    $types[$counter]['name'] = $op['plural'];
                }elseif($k == "cred_id"){
                    $types[$counter]['id'] = $op;
                }else{
                    continue;
                }
            }
        }
        $meta_name = (!isset($values['frm_mycred'][$field['id']]['logic'][0]))? 0 : count($values['frm_mycred'][$field['id']]['logic'])-1;

        $disabled = '';
		
		if($key = get_option('frm_mc_edd_licence_key'))
            $valid = FrmMCSettingsController::license_key_request('check_license',$key);
         
        /*if($key = get_option('frm_mc_edd_licence_key'))
            $valid = FrmMCSettingsController::license_key_request('check_license',$key);
        else $disabled = ' disabled';
        //$validation = $valid->license;
        // if(isset($validation) and $validation != 'valid') */
		// $disabled = ' disabled';
			//print_r($values);
	 	
		if(!isset($values['frm_mycred']) || empty($values['frm_mycred']))
			$previous =  $wpdb->get_results("SELECT options FROM ".$wpdb->prefix."frm_forms WHERE status='published' and id!=$form->id ORDER BY `id` DESC LIMIT 1",ARRAY_A);
		else
			$previous =  $wpdb->get_results("SELECT options FROM ".$wpdb->prefix."frm_forms WHERE status='published' ORDER BY `id` DESC LIMIT 1",ARRAY_A);
		
		$previous = maybe_unserialize($previous[0]['options']);
		 
		$add_update_done = 0 ;
		 
		foreach($previous['frm_mycred'] as $item )
		{
			if(isset($item['update']) && $item['update'] !='')
				$add_update_done =$item['update'];
		}
		// echo $add_update_done;
		
        include(FrmMCAppController::path() .'/views/widget.php');
    }

    /*function get_defaults() {
        return FrmRegAppHelper::get_default_options();
    }*/
	
	public function duplicate_form_actions( $form_id, $old_id ) {
        if ( $form_id == $old_id ) {
            // don't duplicate the actions if this is a template getting updated
            return;
        }

        $this->form_id = $old_id;
        $actions = $this->get_all( $old_id );
		 
        $this->form_id = $form_id;
		$old_post_id = array();
		$new_post_id = array();
        foreach ( $actions as $action ) {
			
			$old_post_id[] = $action->ID;
			// print_r($action);
            $new_post_id[] =$this->duplicate_one($action, $form_id);
			 
            
			unset($action);
        }
		global $wpdb;
		$prefix = $wpdb->prefix;
		$table = $prefix.'frm_forms';

		$old_opt = $wpdb->get_var("SELECT options FROM $table WHERE id=$form_id");
		
		$filed = $prefix.'frm_fields';
		$new_opt = $old_opt ;
		// print_r($key_array);
		$new_opt =str_replace($old_post_id,$new_post_id,$old_opt);
		
		$old_opt_temp = maybe_unserialize($old_opt);
		// print_r($old_opt_temp['frm_mycred']);
		if(isset($old_opt_temp['frm_mycred']))
		{
			foreach($old_opt_temp['frm_mycred'] as $item)
			{ 
				if($item['isfixed']!=1 || !isset($item['isfixed']))
				{
					
					$old_fileds = $wpdb->get_results("SELECT * FROM $filed WHERE id=".$item['amount'],ARRAY_A);
					$old_field_name = (maybe_unserialize($old_fileds[0]['name']));
					$old_field_id = (maybe_unserialize($old_fileds[0]['id']));
					 
					$new_fileds = $wpdb->get_results("SELECT * FROM $filed WHERE form_id=$form_id",ARRAY_A);

					 
					foreach($new_fileds as $item)
					{
							if($item['name'] == $old_field_name)
								$new_filed_id =$item['id']; 
					}
					
					if(isset($new_filed_id) && $new_filed_id!='')
					{
						$new_opt =str_replace($old_field_id,$new_filed_id,$new_opt);
					}
					 
				}
				if(isset($item['logic']) && !empty($item['logic']))
				{
					foreach($item['logic'] as $logic)
					{
						$old_fileds = $wpdb->get_results("SELECT * FROM $filed WHERE id=".$logic['logicfld'],ARRAY_A);
						$old_field_name = (maybe_unserialize($old_fileds[0]['name']));
						$old_field_id = (maybe_unserialize($old_fileds[0]['id']));
						 
						$new_fileds = $wpdb->get_results("SELECT * FROM $filed WHERE form_id=$form_id",ARRAY_A);

						 
						foreach($new_fileds as $item)
						{
								if($item['name'] == $old_field_name)
									$new_filed_id =$item['id']; 
						}
						
						if(isset($new_filed_id) && $new_filed_id!='')
						{
							$new_opt =str_replace($old_field_id,$new_filed_id,$new_opt);
						}
					}
				}
				// echo '1<br>';
			}
		}
		// print_r(maybe_unserialize($new_opt));
		// die();
		$wpdb->update(
            $table,
            array( 'options' => $new_opt),
            array( 'id' => $form_id ),
            array( '%s'	),
            array( '%d' )
        );
		
		
		// echo $form_id.'--'.$old_id;
		// die();
    } 
	
	 

}
