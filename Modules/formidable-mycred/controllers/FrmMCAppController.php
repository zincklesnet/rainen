<?php

class FrmMCAppController{
    function FrmMCAppController(){
        add_action('admin_init', 'FrmMCAppController::include_updater', 1);

        add_action('frm_after_create_entry', 'FrmMCAppController::mycred_transition', 10, 2);
        add_action('frm_after_update_entry', 'FrmMCAppController::mycred_transition', 10, 2);
        add_action('frm_registered_form_actions', array(__CLASS__, 'mycred_actions') );


        add_action('frm_validate_entry', 'FrmMCAppController::validate_mycred_form', 20, 2);
		// add_action('admin_menu', array($this, 'register_mycred_global_setting_page'));
		
		
		
    }
    
    public static function path(){
        return dirname(dirname( __FILE__ ));
    }
 
    public static function include_updater(){
        include_once(self::path() .'/models/FrmMCUpdate.php');
        $update = new FrmMCUpdate();
    }
	
	/* Settings Page */
	public function register_mycred_global_setting_page()
	{
		add_submenu_page( 'options-general.php', 'MyCRED Formidable Settings', 'MyCRED Formidable', 'manage_options', 'mycred_formidable', array($this,'mycred_global_setting_page_callback' ));
		register_setting( 'mycred_formidable', 'defaultmycread_log' );  
		add_settings_section('global_section', 'MyCRED Settings', array($this,'global_section_callback'), 'mycred_formidable');
		add_settings_field( 'mycred_global_log_text', 'Set your default Log', array($this,'mycred_global_log_text_callback_function'), 'mycred_formidable' , 'global_section') ;
		
	}
	function global_section_callback(){
		 
	}
	function mycred_global_setting_page_callback() {
	
		echo '<div class="wrap"> ';
		  ?>
			<form action="options.php" method="post">
			<?php
				settings_fields( 'mycred_formidable' ); 
				do_settings_sections('mycred_formidable');
				submit_button();
			?>
				</form>
			<?php
		echo '</div>';

	}
	
	function mycred_global_log_text_callback_function()
	{
		 
		$content = get_option('defaultmycread_log');
		$editor_id = 'defaultmycread_log';
		
		if($content == '' )
			$content = '%form% %action%: %credit%';
		wp_editor( $content, $editor_id,array('textarea_rows'=>3,'media_buttons'=>false,'textarea_name'=>'defaultmycread_log') );
	}
	
	
	
    public static function mycred_actions($actions) {
        $actions['MyCRED'] = 'FrmMCAction';

        include_once(self::path() . '/models/FrmMCAction.php');
        return $actions;
    }

    public static function mycred_transition($entry_id, $form_id){

		 

        /*if($key = get_option('frm_mc_edd_licence_key'))
            $valid = FrmMCSettingsController::license_key_request('check_license',$key);
        else return;
        $validation = $valid->license;
        if(isset($validation) and $validation != 'valid')*/
		
		 
		if(0){
            //Go to licence template
            return;
        }else{
			
			global $wpdb;
            $prefix = $wpdb->prefix;
            $table_f  = $prefix.'frm_forms';
             
			$val = maybe_unserialize($wpdb->get_var("SELECT options FROM $table_f WHERE id=$form_id"));
			
			$version = '';
			if(isset($val['frm_mycred_version']))
			{
				$version = $val['frm_mycred_version'];
			}			
            $slct = new FrmMCSettings();
            $vals_all = $slct->frm_mycred_get_opt($form_id);
             
			 
            foreach($vals_all as $action_key=>$vals){
                // print_r($vals);
                
				$action = current_filter();
				
				
				

                if($action == 'frm_after_update_entry'){
					if (!isset($vals['update']) or $vals['update'] != '1')
					{
                        continue;
					}
				} 
				if($action == 'frm_after_create_entry'){
					if((!isset($vals['create_update']) or $vals['create_update'] != '1') && $version != VERSION){
                        continue;
					}	
				} 
				
                unset($action);

                global $wpdb;
                $prefix = $wpdb->prefix;
                $table  = $prefix.'frm_item_metas';
                $operate = array();
                if(isset($vals['logic']) and is_array($vals['logic'])){

                    //for now we are assuming that if all conditions are met, then the points will be calculated

                    $logic = $vals['logic'];
                    for($counter = 0; $counter < count($logic); $counter++){
                        $ele = $logic[$counter]['logicfld'];
                        $val = $wpdb->get_var("SELECT meta_value FROM $table WHERE field_id=$ele and item_id=$entry_id");
						$val = maybe_unserialize($val);
						 
                        $sign = $logic[$counter]['cond'];
                        $opt  = $logic[$counter]['opt'];
						$is_match = true;
						if(is_array($val))
						{
							foreach($val as $v)
							{	
								
								if(isset($opt) and !empty($opt)){
									if($sign == '==') $is_match = ($v == $opt)? 1 : 0;
									elseif($sign == '!=') $is_match = ($v != $opt)? 1 : 0;
									elseif($sign == '>') $is_match = ($v > $opt)? 1 : 0;
									elseif($sign == '<') $is_match = ($v < $opt)? 1 : 0;
									else $is_match = 1;
									$operate[] = $is_match;
									if($is_match)
									{
										
										break;
									}
									
								}else{
									$operate[] =  1;
								}
								
							}
							
							 
						}
						else
						{
							
							if(isset($opt) and !empty($opt)){
								if($sign == '==') $is_match = ($val == $opt)? 1 : 0;
								elseif($sign == '!=') $is_match = ($val != $opt)? 1 : 0;
								elseif($sign == '>') $is_match = ($val > $opt)? 1 : 0;
								elseif($sign == '<') $is_match = ($val < $opt)? 1 : 0;
								else $is_match = 1;

								$operate[] = $is_match;
							}else{
								$operate[] =  1;
							}
							
						}
                        
                    }

                }
				 
                //don't add this if conditional logic is not met
                $add = true;
                foreach($operate as $op)
                    if($op == 0)
                        $add=false;

                if(!$add) continue;
				
                unset($operate);
                unset($logic);
				 	
                $amount = 0;
				
                if(isset($vals['amount'])){
                    $amount_id = $vals['amount'];
                    $amount = (float)$wpdb->get_var("SELECT meta_value FROM $table WHERE field_id=$amount_id and item_id=$entry_id");

                }elseif(isset($vals['fixed'])){
                    $amount = (float)$vals['fixed'];
                }else{
                    continue;
                }
				
                $type = '';$t_key='';
                if(isset($vals['type'])){
                   
					$mycred_types = get_option('mycred_types');
                    $mycred_types = maybe_unserialize($mycred_types);
                    foreach($mycred_types as $k => $t) if($k == $vals['type']){$type=strip_tags($t); $t_key=$k;} 
					
					
					// $mycred_types = get_option('mycred_pref_core');
					// 
					// print_r($mycred_types);
					// die();
					/*echo '<pre>';
						print_r($mycred_types);
					echo '</pre>'; 
                    // $mycred_types = maybe_unserialize($mycred_types['name']['plural']);
					
					if( $mycred_types['cred_id'] == $vals['type'])
					{
						$type=strip_tags($mycred_types['name']['plural']); $t_key=$mycred_types['cred_id'];
					}*/
                }else{
                    $t_key="mycred_default";
                }
                if($vals['trans'] == 'sub'){
                    $mycred = mycred();
                    $balance = (float)mycred_get_users_cred( get_current_user_id(), $t_key);
                    $amount = $amount -(2*$amount) ;
                }
                $id = get_current_user_id();
                if ( !mycred_exclude_user( $id ) ) {
                    // Add points and save the current year as ref_id
                    //echo 'amount added.';
					 
					if(isset($vals['log_message']) && $vals['log_message']!='' )
						$log_message = $vals['log_message'];
					else if(get_option('defaultmycread_log')!='')
						$log_message = get_option('defaultmycread_log');
					else 
						$log_message = '%form% %action%: %credit%';
					 
					$current_form = $wpdb->get_var("SELECT name FROM ".$prefix."frm_forms WHERE id=$form_id");
					$action = get_post($action_key);
					$filter_content = str_replace("%form%", $current_form , $log_message);
					$filter_content = str_replace("%action%",$action->post_title,$filter_content);
					$filter_content = str_replace("%credit%",'%plural%',$filter_content);
					$filter_content = stripslashes($filter_content);
                    mycred_add( $type.'_for_submitting_form', $id, $amount, $filter_content ,0,0, $t_key );
                }
                //echo '('.$type.'ed->'.$amount.')';
            }
        }
    }


    public static function validate_mycred_form($errors, $values){

        /*Licencing here*/

            global $wpdb;
            $prefix = $wpdb->prefix;
            $table_f  = $prefix.'frm_forms';
            $fid= $values['form_id'];
            $val = maybe_unserialize($wpdb->get_var("SELECT options FROM $table_f WHERE id=$fid"));
            if(!isset($val['frm_mycred'])) return $errors;
			
			
            $valss = $val['frm_mycred'];
            $balance = array();
            $not_to_bal_check = true;			
			$error_balance_credit_type= '';
			$balance_error_type = '';
			$route_to_page = '';
			$low_balance_error = '';
			//$errors['ffff'] = 'stop';
			$version = '';
			if(isset($val['frm_mycred_version']))
			{
				$version = $val['frm_mycred_version'];
				if($val['frm_mycred_version'] != VERSION)
				{
					$mycred_types = get_option('mycred_types');
					$mycred_types = maybe_unserialize($mycred_types);
					foreach($mycred_types as $k => $t) $error_balance_credit_type[] = $k; 
				}
			} 
            foreach($valss as $action_key=>$vals){
                $amount = 0;
				if($balance_error_type == '' && isset($vals['balance_error_type']))
				{
					$balance_error_type = $vals['balance_error_type'];
					$route_to_page = $vals['route_page'] ;
					$low_balance_error = (isset($vals['error']))? $vals['error']:'The form submission has been blocked because you don\'t have enough balance.';
					$low_balance_error = stripslashes($low_balance_error);
					if(isset($vals['balance_error_credit_type']))
							$error_balance_credit_type = $vals['balance_error_credit_type'];
				 
					
				}
				if($values['frm_action']=='update' ){
					if (!isset($vals['update']) or $vals['update'] != '1')
					{
                        continue;
					}
				} 
				
				if($values['frm_action']=='create' ){
					if((!isset($vals['create_update']) or $vals['create_update'] != '1') && $version!=VERSION){
                        continue;
					}	
				} 
				 
                //--------------------------CONDITIONS START-------------------------
                $prefix = $wpdb->prefix;
                $table  = $prefix.'frm_item_metas';
                $operate = array();
				
                if(isset($vals['logic']) and is_array($vals['logic'])){

                    //for now we are assuming that if all conditions are met, then the user will be subscribed
					 
					if(isset($vals['balance_error_credit_type'])){
						
					}
                    $logic = $vals['logic'];
                    for($counter = 0; $counter < count($logic); $counter++){
                        $ele = $logic[$counter]['logicfld'];
                        $val = isset($values['item_meta'][$logic[$counter]['logicfld']])? $values['item_meta'][$logic[$counter]['logicfld']]:'';
						
                        $sign = $logic[$counter]['cond'];
                        $opt  = $logic[$counter]['opt'];
                        /*$subscribe = convert_logical($val,$sign,$opt);*/
						$is_match = true;
						// print_r($val);
						if(is_array($val))
						{
							foreach($val as $v)
							{	
								// echo $opt.$sign.$v;
								if(isset($opt) and !empty($opt)){
									if($sign == '==') $is_match = ($v == $opt)? 1 : 0;
									elseif($sign == '!=') $is_match = ($v != $opt)? 1 : 0;
									elseif($sign == '>') $is_match = ($v > $opt)? 1 : 0;
									elseif($sign == '<') $is_match = ($v < $opt)? 1 : 0;
									else $is_match = 1;
									$operate[] = $is_match;
									if($is_match)
									{										
										break;
									}
									
								}else{
									$operate[] =  1;
								}
								
							}
							
							 
						}
						else
						{
							
							if(isset($opt) and !empty($opt)){
								if($sign == '==') $is_match = ($val == $opt)? 1 : 0;
								elseif($sign == '!=') $is_match = ($val != $opt)? 1 : 0;
								elseif($sign == '>') $is_match = ($val > $opt)? 1 : 0;
								elseif($sign == '<') $is_match = ($val < $opt)? 1 : 0;
								else $is_match = 1;

								$operate[] = $is_match;
							}else{
								$operate[] =  1;
							}
							
						}
                        
                        
                    }

                }
				
                $add = true;
				// print_r($operate);
                foreach($operate as $op)
                    if($op == 0)
                        $add=false;

                if(!$add) continue;
                unset($operate);
                unset($logic);
                //--------------------------CONDITIONS END---------------------------


                if(isset($vals['amount'])){
                    $amount_id = $vals['amount'];
                    $amount = (float)$values['item_meta'][$vals['amount']];

                }elseif(isset($vals['fixed'])){
                    $amount = (float)$vals['fixed'];
                }else{
                    $errors['mycred'] ='No certain amount selected';
                }
				$amount_to_resolve = $amount;
                $type = '';$t_key='';
                if(isset($vals['type'])){
					// print_r($vals['type']);
					$mycred_types = get_option('mycred_types');
                    $mycred_types = maybe_unserialize($mycred_types);
                    foreach($mycred_types as $k => $t) if($k == $vals['type']){$type=strip_tags($t); $t_key=$k;}  
                }else{
                    $t_key="mycred_default";
                }
				
                if($vals['trans'] == 'sub'){
					//echo 'SUB-----<br>';
					//echo '<br>before'.$balance;
					if($amount > 0)
					{
						//echo $t_key;
						if(!isset($balance[$t_key]))
							$balance[$t_key] = (float)mycred_get_users_cred( get_current_user_id(), $t_key);
						
						// echo $amount;
						if(!isset($balance[$t_key]))
							$balance[$t_key] = (float)mycred_get_users_cred( get_current_user_id(), $t_key);
						
						if($balance[$t_key] < $amount){
							$errors['mycred'] = (isset($vals['error']))? $vals['error']:'The form submission has been blocked because you don\'t have enough balance.';
							$balance[$t_key] -= $amount;
						}else{
							$balance[$t_key] -= $amount;
							$amount = $amount -(2*$amount) ;
						}
						// echo '<br>after'.$balance[$t_key];
						// echo '<br>'.$action_key;
						
						$not_to_bal_check = false;
					}
                     
                     
					//echo '<br>after'.$balance;
					//echo '<br>'.$action_key;
					 
					
					
					/*
					if($balance > 0 )
					{
						foreach($error_balance_credit_type  as $item)
						{
							if($item != $t_key)
							{
								$new_bal = (float)mycred_get_users_cred( get_current_user_id(), $item);
								$new_bal -= $amount_to_resolve;
								if($new_bal < 0)
									break;
							}		
						}
						if($new_bal < 0)
							$errors['mycred'] = (isset($vals['error']))? $vals['error']:'The form submission has been blocked because you don\'t have enough balance.';
					}*/
                }
                if($vals['trans'] == 'add'){
                     
					//echo 'ADD-----<br>';
					//echo '<br>before'.$balance;
					if(!isset($balance[$t_key]))
						$balance[$t_key] = (float)mycred_get_users_cred( get_current_user_id(), $t_key);
                    $balance[$t_key] += $amount;
					//echo '<br>after'.$balance;
					//echo '<br>'.$action_key;
                }
                //echo $amount.' '.$vals['trans'].'ed -> '.$balance.'<br>';
				
						
				
            }
			// echo $balance ;
			// die();
			// var_dump($errors);
			// var_dump($errors['mycred']);
			//var_dump($error_balance_credit_type);
			//var_dump($balance);
			//var_dump($not_to_bal_check);
			//var_dump($balance_error_type);
			foreach($balance as $k=>$v)
			{
				if (in_array($k, $error_balance_credit_type)) {
					
					if($v < 0 && !$not_to_bal_check)
					{
						if($balance_error_type!='' && ($balance_error_type==0 || $balance_error_type==2 ))
						{
							$errors['mycred'] = $low_balance_error;
							 
							return $errors;
							
						}
						else if ( $balance_error_type!='' && $balance_error_type==1)
						{
							wp_redirect( get_permalink( $route_to_page )); 
							exit;
						}
						else if ( $balance_error_type!='' && $balance_error_type==3 && isset($errors['mycred']))
							unset($errors['mycred']);
						
						break;
					}
					else if ($v >= 0 && isset($errors['mycred']))
						unset($errors['mycred']);
						
				}
			}
             
			 
			return $errors;
			
    }
    

}
