<?php

class FrmMCSettingsController{
	
	
    
	function FrmMCSettingsController(){
        add_action('frm_add_settings_section', 'FrmMCSettingsController::mycred_add_settings_section',10,1);
        //add_filter('frm_available_fields','FrmMCSettingsController::mycred_add_new_field',1);
        //add_filter('frm_field_type', 'FrmMCSettingsController::mycred_change_field_type',10, 2);
        //add_filter('frm_display_field_options','FrmMCSettingsController::mycred_configure_field',1);
        //add_action('frm_field_options_form', 'FrmMCSettingsController::mycred_create_field_widget',10,3 );
        //add_filter('frm_custom_html', 'FrmMCSettingsController::mycred_frm_customize_html',10, 2);
        add_action('wp_ajax_mycred_frm_store_options', 'FrmMCSettingsController::mycred_frm_store_options');
        add_action('wp_ajax_frm_mc_add_logic_row', 'FrmMCSettingsController::add_logic_row');
        add_action('wp_ajax_frm_mc_remove_logic_row', 'FrmMCSettingsController::remove_logic_row');
        add_action('wp_ajax_frm_mc_get_field_values', 'FrmMCSettingsController::get_field_values');
        add_action('wp_ajax_frm_mc_garbage_collector', 'FrmMCSettingsController::collect_garbage');
        add_action('admin_init','FrmMCSettingsController::frm_mc_icon_style');
        add_action('wp_enqueue_scripts','FrmMCSettingsController::frm_magnific_pop');
        
        add_filter('frm_setup_new_form_vars', 'FrmMCSettingsController::setup_new_vars');
        add_filter('frm_setup_edit_form_vars', 'FrmMCSettingsController::setup_edit_vars');
        add_filter('frm_form_options_before_update', 'FrmMCSettingsController::update_options', 20, 2);
        add_filter('validate_edd_licence_key','FrmMCSettingsController::validate_frm_mc_edd_licence_key');
		// Form OVER LAY
		$this->check_before_submit = 0 ;
		add_action('frm_form_classes', array($this,'frm_form_classes'));
		add_action('frm_entries_footer_scripts',array($this,'set_script_for_overlay'), 20, 2) ;
		
		add_action( 'wp_ajax_check_balance',array($this,'validate_balance') );
		add_action( 'wp_ajax_nopriv_check_balance', array($this,'validate_balance') );
		
		add_action('frm_duplicate_field',array($this,'frm_duplicate_field'),10,2);
		// add_action('admin_head',array($this,'header_Script'));
	}

	 	
	public function frm_duplicate_field($copy_field,$form_id)
	{
		print_r($copy_field);
		die();
	}
	public function frm_form_classes($form){
		 
		 
		if(isset($form->options['frm_mycred']) && !empty($form->options['frm_mycred']) )
		{
			foreach($form->options['frm_mycred'] as $key=>$val)
			{
					//if(isset($form->options['frm_mycred']['balance_error_type']))
					 
						
					if(isset($val['balance_error_type']) && $val['balance_error_type'] == 2)
					{
						 
						echo 'check-bal-before-submit';
						$this->check_before_submit = 1;
					}
					 
			}
			
		}
		
	}
	public function set_script_for_overlay($fields, $form)
	{
		if($this->check_before_submit == 0 )
			return;
		?>
			var $=jQuery;
			var in_process = false;
			$(document).ready(function(){
				
				 
				$('.check-bal-before-submit').bind('submit',submit_form);
				
			
				
			});
			
			function  submit_form_as_success()
			{
				
				ele =$('.check-bal-before-submit');
				ele.unbind('submit');
				ele.removeClass('check-bal-before-submit');
				$('.frm_forms form').submit();
			}
			
			function submit_form()
			{
				var done = false;  
				console.log(in_process);
				if(in_process)
					return false;
				 
				
				in_process = true;
				 
				 
				$.ajax({
				  type: "POST",
				  url: '<?php echo admin_url('admin-ajax.php'); ?>',
				  data: {	
							'action': 'check_balance',
							'data':  $(this).serializeArray()
						},
				  success: function(data){
					  console.log(data[0]);
					 if(data[0] == 'low-balance')
					 {
						 
						 
						$.magnificPopup.open({
						  items: {
							src: '<div class="white-popup">'+data[1]+'</div>', // can be a HTML string, jQuery object, or CSS selector
							type: 'inline'
						  }
						});
						
						done = false;
					 } else if( data[0] =='success')
					 {
						
						done = true;
						console.log(done);
						 
						
					 }
					 
					 
					 in_process = false;
				  },
				 
				dataType :'json',
				}).complete(function(){
					
					if(done === true)
					{
						console.log(done);
						 
						submit_form_as_success();
					}
				});
				return done;
			}
		<?php
	}
	
	public function validate_balance()
	{
		/*Licencing here*/
			 
			$values = array();
			$temp = array();
			foreach($_REQUEST['data'] as $val)
			{
				$values[$val['name']] = $val['value'];
				 
				 
			}
			$values = http_build_query($values) . "\n";
			 
			// print_r($_REQUEST['data']);
			
			parse_str($values, $values); 
			 
            global $wpdb;
            $prefix = $wpdb->prefix;
            $table_f  = $prefix.'frm_forms';
            $fid= $values['form_id'];
            $val = maybe_unserialize($wpdb->get_var("SELECT options FROM $table_f WHERE id=$fid"));
			
			
            if(!isset($val['frm_mycred']))
			{	
				 
				$data = array('no-value');
				echo json_encode($data);
				die();
			} 
			$version = '';
			if(isset($val['frm_mycred_version']))
			{
				$version = $val['frm_mycred_version'];
			}
            $valss = $val['frm_mycred'];
            $balance = array();
			$not_to_bal_check = true;
            $error_balance_credit_type= '';
			$overlay_form_id='';
			$overlay_form='';
			//$errors['ffff'] = 'stop';
			
            foreach($valss as $vals){
                $amount = 0;
               
				 
				if(isset($vals['open_overlay']))
				{
					$overlay_form_id =$vals['open_overlay'];
					$overlay_form = do_shortcode("[formidable id=$overlay_form_id]");
				} 
				if(isset($vals['version']) && $version==VERSION)
				{
					if(isset($vals['balance_error_credit_type'])){
					$error_balance_credit_type = $vals['balance_error_credit_type'];
					}
				} 
				else
				{
					$mycred_types = get_option('mycred_types');
					foreach($mycred_types as $k => $t) 
						$error_balance_credit_type[] = $k; 
				}
				
                //--------------------------CONDITIONS START-------------------------
                $prefix = $wpdb->prefix;
                $table  = $prefix.'frm_item_metas';
                $operate = array();
                if(isset($vals['logic']) and is_array($vals['logic'])){

                    //for now we are assuming that if all conditions are met, then the user will be subscribed

                    $logic = $vals['logic'];
                    for($counter = 0; $counter < count($logic); $counter++){
                        $ele = $logic[$counter]['logicfld'];
                        $val = isset($values['item_meta'][$logic[$counter]['logicfld']])? $values['item_meta'][$logic[$counter]['logicfld']]:'';

                        $sign = $logic[$counter]['cond'];
                        $opt  = $logic[$counter]['opt'];
                        /*$subscribe = convert_logical($val,$sign,$opt);*/

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

                $add = true;
                foreach($operate as $op)
                    if($op == 0)
                        $add=false;

                if(!$add){
					 continue;
				} 
                unset($operate);
                unset($logic);
                //--------------------------CONDITIONS END---------------------------//

				
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
					// echo 'SUB-----<br>';
					// echo '<br>before'.$balance[$t_key];
					
					if($amount > 0)
					{ 
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
					/*
					$error_balance_credit_type = $vals['balance_error_credit_type'];
					
					if($balance > 0)
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
					}*/
					 
                }
                if($vals['trans'] == 'add'){
					// echo 'ADD-----<br>';
					// echo '<br>before'.$balance;
                    if(!isset($balance[$t_key]))
						$balance[$t_key] = (float)mycred_get_users_cred( get_current_user_id(), $t_key);
                    $balance[$t_key] += $amount;
					// echo '<br>after'.$balance[$t_key];
					// echo '<br>'.$action_key;
					
                }
                //echo $amount.' '.$vals['trans'].'ed -> '.$balance.'<br>';
				 
				
            }
			// print_r($error_balance_credit_type);
			// print_r($balance);
			foreach($balance as $k=>$v)
			{
				if (in_array($k, $error_balance_credit_type)) {
					
					if($v < 0 && !$not_to_bal_check)
					{
						$data = array('low-balance',$overlay_form);
						break;
					}
					else
						$data = array('success');
						
				}
			}	
			 
			echo json_encode($data);
						
			die();
	}
    public static function mycred_add_settings_section($sections){
        $sections['mycred'] = array('class' => 'FrmMCSettingsController', 'function' => 'mycred_activate');
        return $sections;
    }

    public static function frm_mc_icon_style(){
        wp_register_style( 'mc-icons', plugins_url('formidable-mycred').'/css/font-awesome.css');
        wp_enqueue_style( 'mc-icons' );
    }
	 
	public static function frm_magnific_pop(){
        wp_register_style( 'mc-magnific-popup', plugins_url('formidable-mycred').'/css/magnific-popup.css');
        wp_register_style( 'mc-main', plugins_url('formidable-mycred').'/css/main.css');
        wp_enqueue_style( 'mc-magnific-popup' );
        wp_enqueue_style( 'mc-main' );
		wp_enqueue_script( 'mc-magnific-popup-js',plugins_url('formidable-mycred'). '/js/jquery.magnific-popup.min.js', array('jquery'));
    }

    /*public static function mycred_frm_save_license_key(){
        if(isset($_POST['frm_mc_api_key']) and !empty($_POST['frm_mc_api_key'])){
            $key = get_option('frm_mc_edd_licence_key');

            $data = self::license_key_request('activate_license',$key);
            //$data->license;
            if(isset($key) and !empty($key)){
                update_option('frm_mc_edd_licence_key',$_POST['frm_mc_api_key']);
            }else{
                add_option('frm_mc_edd_licence_key',$_POST['frm_mc_api_key']);
            }
        }
    }*/
    public static function mycred_activate(){
        if(isset($_POST['activate_mc_licence']) and isset($_POST['frm_mc_api_key'])){ //when key submitted

            $key = $_POST['frm_mc_api_key'];
            $data =  $data = self::license_key_request('activate_license',$key); //activate_license
            $validation = $data->license;
            //echo '<pre>';
            //print_r($data);
            /////////////////////////////////////User's Submitted key is valid////////////////////////////////
            if($data->license == 'valid'){
                $opt = get_option('frm_mc_edd_licence_key');
                if( isset($opt) and !empty($opt) ){
                    update_option('frm_mc_edd_licence_key',$key);
                }else{
                    add_option('frm_mc_edd_licence_key',$key);
                }
            }
            /////////////////////////////////User submitted an valid or invalid key//////////////////////////
            include(FrmMCAppController::path() .'/views/form.php');

            ////////////////////////////////Deactivating the License key///////////////////////////
        }elseif(isset($_POST['deactivate_mc_licence']) and isset($_POST['frm_mc_api_key'])){
            $key = $_POST['frm_mc_api_key'];
            $data =  $data = self::license_key_request('deactivate_license',$key); //deactivate_license
            delete_option('frm_mc_edd_licence_key');
            include(FrmMCAppController::path() .'/views/form.php');
        }else{
            ////////////////////////////////User Open the plugin for the first time///////////////////////////
            $validation = 'none';
			 
            if($opt = get_option('frm_mc_edd_licence_key')) $validation = self::validate_frm_mc_edd_licence_key($opt);
            include(FrmMCAppController::path() .'/views/form.php');
        }
        /*End licencing*/
    }

    /*public static function mycred_add_new_field($fields){
        $fields['mycred'] = __('MyCRED', 'formidable');
        return $fields;
    }

    public static function mycred_change_field_type($type, $field){
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table = $prefix.'frm_fields';
        $fieldID = $field->id;
        $result = $wpdb->get_var("SELECT type FROM $table WHERE id=$fieldID");

        if($result == 'mycred')  $type='mycred';
        return $type;
    }

    public static function mycred_configure_field($display){
        if(isset($display)){
            global $wpdb;
            $prefix = $wpdb->prefix;
            $table = $prefix.'frm_fields';
            $fieldID = $display['field_data']['id'];
            $result = $wpdb->get_var("SELECT type FROM $table WHERE id=$fieldID");
            if($result == 'mycred'){
                $display['mycred'] = true;
                $display['required'] = false;
                $display['label_position'] = false;
                $display['css'] = false;
            }
        }
        return $display;
    }

    public static function mycred_create_field_widget($field, $display, $values){

        if(isset($display['mycred']) and $display['mycred']){

            $f = $field['id'];
            global $wpdb;
            $prefix = $wpdb->prefix;
            $table = $prefix.'frm_fields';
            $form_id = $wpdb->get_var("SELECT form_id FROM $table WHERE id=$f");
            if(!isset($values['fields'])){
                $values = array('fields'=>array());
                $values['fields'] = $wpdb->get_results("SELECT id,name,type FROM $table WHERE form_id=$form_id",ARRAY_A);
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

                    echo '<pre>';
                    echo '</pre>';
            $hidden = 'display:none;';
            $meta_name = (!isset($values['frm_mycred'][$field['id']]['logic'][0]))? 0 : count($values['frm_mycred'][$field['id']]['logic'])-1;

            include(FrmMCAppController::path(). '/views/widget.php');
        }
    }

    public static function mycred_frm_customize_html($default_html, $field_type){
        if($field_type == 'mycred'){
            $default_html = '';
        }
        return $default_html;
    }*/

    public static function collect_garbage(){
        $f = $_POST['f'];
        $frm = $_POST['frm'];
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table = $prefix.'frm_forms';
        $opt = unserialize($wpdb->get_var("SELECT options FROM $table WHERE id=$frm"));

        if(isset($opt['frm_mycred'][$f])){
            unset($opt['frm_mycred'][$f]);
            $new_opt = serialize($opt);
            $wpdb->update(
                $table,
                array( 'options' => $new_opt),
                array( 'id' => $frm ),
                array( '%s'	),
                array( '%d' )
            );
        }
        die();
    }

    public static function remove_logic_row(){
        $ele = $_POST['ele'];
        $arr = explode('_',$ele);
        $frm_id = $_POST['form_id'];
        $id   = $arr[1];
        $meta = $arr[2];

        unset($ele);
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table = $prefix.'frm_forms';

        $old_opt_ser = $wpdb->get_var("SELECT options FROM $table WHERE id=$frm_id");
        $old_opt = maybe_unserialize($old_opt_ser);
        if(count($old_opt['frm_mycred'][$id]['logic'])>1){
            unset($old_opt['frm_mycred'][$id]['logic'][$meta]);
        }else{
            unset($old_opt['frm_mycred'][$id]['logic']);
        }
        $new_opt = serialize($old_opt);
        $wpdb->update(
            $table,
            array( 'options' => $new_opt),
            array( 'id' => $frm_id ),
            array( '%s'	),
            array( '%d' )
        );
    }

    public static function add_logic_row(){
        if(!isset($_POST) or !isset($_POST['field_id']))
            die();

        $field_id = $_POST['field_id'];
        $form_id = (int)$_POST['form_id'];
        $meta_name = $_POST['meta_name'];
        $hide_field = '';

        $frm_field = new FrmField();
        $form_fields = $frm_field->getAll("fi.form_id = ". (int)$form_id ." and (type in ('select','radio','checkbox','10radio','scale','data') or (type = 'data' and (field_options LIKE '\"data_type\";s:6:\"select\"%' OR field_options LIKE '%\"data_type\";s:5:\"radio\"%' OR field_options LIKE '%\"data_type\";s:8:\"checkbox\"%') ))", "field_order");

        $frmdb = new FrmDb();
        $form_options = $frmdb->get_var($frmdb->forms, array('id' => $form_id), 'options');
        $form_options = maybe_unserialize($form_options);

        global $wpdb;
        $prefix = $wpdb->prefix;
        $table = $prefix.'frm_fields';
        $values = array('fields'=>array());
        $values['fields'] = $wpdb->get_results("SELECT id,name,type,options,field_options FROM $table WHERE form_id=$form_id",ARRAY_A);
        $values = maybe_unserialize($values);
        if(isset($form_options['frm_mycred'][$field_id]['logic']))
            $list_options = $form_options['frm_mycred'][$field_id]['logic'][$meta_name];
        else
            $list_options[$meta_name] = array('logicfld' => array(), 'cond' => array(), 'opt' => array());

        if(!isset($list_options[$meta_name]['cond']))
            $list_options[$meta_name]['cond'] = '==';

        include(FrmMCAppController::path(). '/views/_logic_row.php');

        die();
    }
	public static function check_perfect_match ($str1,$str2)
	{
		$str1 = str_replace(' ', '-', $str1);
		$str1 = preg_replace('/[^A-Za-z0-9\-]/', '', $str1);
		$str2 = str_replace(' ', '-', $str2);
		$str2 = preg_replace('/[^A-Za-z0-9\-]/', '', $str2);
		
		return ($str1 == $str2);
	}
    public static function get_field_values(){
        $hide_field = $_POST['selected_id'];
        $form_id = (int)$_POST['form_id'];
        $meta_name = $_POST['cond_id'];
        $field_id = $_POST['field_id'];

        global $wpdb;
        $prefix = $wpdb->prefix;
        $table = $prefix.'frm_fields';
        $fld_vals = $wpdb->get_var("SELECT options FROM $table WHERE id=$hide_field");
        $type = $wpdb->get_var("SELECT type FROM $table WHERE id=$hide_field");
        $fld_vals = maybe_unserialize($fld_vals);


        require(FrmMCAppController::path(). '/views/_field_values.php');
        die();
    }

    public static function mycred_frm_store_options(){
        $frm_id = $_POST['frm'];
        $opt = array();
        $opt['frm_mycred'] = array();
		// print_r($_POST);
		
        $val = (isset($_POST['val']))? $_POST['val']: '';
        $name = (isset($_POST['name']))? $_POST['name']: '';

        if(!empty($name)){
            $name_arr = explode('_',$name);
            $key = $name_arr[2];
            $id = $name_arr[4];
			// print_r($name_arr);
            if($key == 'opt'){
                $meta = $name_arr[5];
                $valo = explode('*',$val);
                $opt['frm_mycred'][$id]['logic'][$meta]['logicfld'] = $valo[0];
                $opt['frm_mycred'][$id]['logic'][$meta]['cond'] = $valo[1];
                $opt['frm_mycred'][$id]['logic'][$meta]['opt'] = $valo[2];

            }
			else{
                $opt['frm_mycred'][$id][$key] = $val;
            }
 			
            $update = new FrmMCSettings();
			
			 
            $update->frm_mycred_update($opt,$frm_id,$id,$key);
            echo 'Saved!'.$val.$frm_id.$id.$key;
        }else{
            echo 'Error';
        }
		 
		
        die();
    }


    /*public static function mycred_frm_set_mark($values, $field){
        //print_r($field);
        return $values;
    }*/

    public static function setup_new_vars($values){
        $defaults = array(
            'frm_mycred' => array()
        );
        foreach ($defaults as $opt => $default){
            $values[$opt] = FrmAppHelper::get_param($opt, $default);
            unset($default);
            unset($opt);
        }
        return $values;
    }

    public static function setup_edit_vars($values){
        $defaults = array(
            'frm_mycred' => array()
        );

        /*echo '<pre>';
        print_r($values);
        echo '</pre>';*/
        foreach ($defaults as $opt => $default){
            if (!isset($values[$opt]))
                $values[$opt] = ($_POST and isset($_POST['options'][$opt])) ? $_POST['options'][$opt] : $default;
            unset($default);
            unset($opt);

        }
        return $values;
    }

    public static function update_options($options, $values){
        $defaults = array(
            'frm_mycred' => array()
        );
		 
		
        $frmdb = new FrmDb();
		if(isset($values['id']))
        $vals = maybe_unserialize($frmdb->get_var($frmdb->forms, array('id' => $values['id']), 'options'));
        //if(isset($_POST) and isset($_POST['options']['frm_mycred']))

		if(isset($vals['frm_mycred']))
        $defaults['frm_mycred'] = $vals['frm_mycred'];
		if(!empty($defaults))
		{
			foreach($defaults as $opt => $default){
            $options[$opt] = (isset($vals['options'][$opt])) ? $vals['options'][$opt] : $default;
            unset($default);
            unset($opt);
			}
		}
        if(isset($vals['frm_mycred']))
		{
			global $wpdb;
			$prefix = $wpdb->prefix;
			foreach($vals['frm_mycred'] as $key => $val){
				$check = $wpdb->get_var("SELECT post_title FROM ". $prefix ."posts WHERE post_excerpt='mycred' AND ID=".$key." AND menu_order=".$values['id']);
				if($check == null){ unset($options['frm_mycred'][$key]);}
			}
		}
        
        if(isset($_POST) and isset($_POST['options']['frm_mycred']))
		{
			 
            foreach($_POST['options']['frm_mycred'] as $key => $vals){
				
				
                $options['frm_mycred'][$key] = $_POST['options']['frm_mycred'][$key];
				
            }
		 
			$options['frm_mycred_version']=VERSION;
		}
        //$options['frm_mycred']  = array();
        unset($defaults);
        return $options;
    }


    public static function validate_frm_mc_edd_licence_key($key){
        $data = self::license_key_request('check_license',$key);
        return $data->license;
    }

    public static function license_key_request($action, $license){
        $key = trim($license);
        $website = home_url();
        $plugin_name = urlencode('Formidable MyCRED');

        $api_params = array(
            'edd_action'=>$action,
            'license'=>$key,
            'item_name'=>$plugin_name,
            'url'=>$website
        );
        $return = wp_remote_get(
            add_query_arg( $api_params, "http://extend.bt4.me/" ),
            array(
                'timeout' => 15,
                'sslverify' => false,
                'body' => $api_params )
        );

        if ( is_wp_error( $return ) )
            return false;

        $data =  json_decode( wp_remote_retrieve_body( $return ) );
        return $data;
    }

}