 
 
<table><tbody>
<tr>
<?php // print_r($values); ?>
<?php // echo $disabled;  ; ?>
    <td width="100px">Amount Field: </td>
    <td width="300px">
        <select class="frm_mycred" id="mycred_frm_amount_fld_<?php echo $field['id']; ?>" name="options[frm_mycred][<?php echo $field['id']; ?>][amount]" <?php echo $disabled; ?>>
            <option value="">- Select the amount field -</option>
            <?php
            foreach($values['fields'] as $f){
                if($f['id'] == $field['id']) continue;
				if($f['type']!='number' && $f['type']!='hidden' &&  $f['type']!='text' ) continue;
                ?>
                <option value="<?php echo $f['id']; ?>" <?php echo (isset($values['frm_mycred'][$field['id']]['amount']) and $values['frm_mycred'][$field['id']]['amount']==$f['id'])? 'selected':''; ?>><?php echo $f['name']; ?></option>
            <?php
            }
            ?>
        </select>
    </td>
</tr>
<tr>
    <td colspan="2"><label for="mycred_frm_isfixed_fld_<?php echo $field['id']; ?>"><input  class="frm_mycred" type="checkbox" value="<?php echo (isset($values['frm_mycred'][$field['id']]['isfixed']))? $values['frm_mycred'][$field['id']]['isfixed']:0; ?>" id="mycred_frm_isfixed_fld_<?php echo $field['id']; ?>" name="options[frm_mycred][<?php echo $field['id']; ?>][isfixed]" <?php echo (isset($values['frm_mycred'][$field['id']]['isfixed']) and $values['frm_mycred'][$field['id']]['isfixed']=="1")? 'checked':''; ?> <?php echo $disabled; ?>> Enter a fixed amount instead.</label></td>
</tr>
<tr class="mycred_frm_fixed" style="<?php echo $hidden; ?>">
    <td>Amount</td><td><input class="frm_mycred" type="text" id="mycred_frm_fixed_fld_<?php echo $field['id']; ?>" name="options[frm_mycred][<?php echo $field['id']; ?>][fixed]" value="<?php echo (isset($values['frm_mycred'][$field['id']]['fixed']))? $values['frm_mycred'][$field['id']]['fixed']:''; ?>" <?php echo $disabled; ?>></td>
</tr>
<tr>
    <td colspan="2">
	<?php  
		foreach($previous['frm_mycred'] as $k=>$v)
		{
			$previous_key = $k;
			break;
		}
		 
	?>
	<label for="mycred_frm_update_fld_<?php echo $field['id']; ?>"><input class="frm_mycred" value="<?php echo (isset($values['frm_mycred'][$field['id']]['update']))? $values['frm_mycred'][$field['id']]['update']:1; ?>" type="checkbox" id="mycred_frm_update_fld_<?php echo $field['id']; ?>" <?php echo (isset($values['frm_mycred'][$field['id']]['update']) and $values['frm_mycred'][$field['id']]['update']=='1')? 'checked':((!isset($values['frm_mycred'][$field['id']]))?((isset($add_update_done) && $add_update_done == 1)?'checked':''):''); ?> name="options[frm_mycred][<?php echo $field['id']; ?>][update]" <?php echo $disabled; ?>>Update when editing an entry</label>
		
	</td>
</tr>
<tr>

    <td colspan="2">
		
		<label for="mycred_frm_create_update_fld_<?php echo $field['id']; ?>"><input class="frm_mycred" value="<?php echo (isset($values['frm_mycred'][$field['id']]['create_update']))? $values['frm_mycred'][$field['id']]['create_update']:1; ?>" type="checkbox" id="mycred_frm_create_update_fld_<?php echo $field['id']; ?>" <?php echo (isset($values['frm_mycred'][$field['id']]['create_update']) and $values['frm_mycred'][$field['id']]['create_update']=='1')? 'checked':(( !isset($values['frm_mycred'][$field['id']]) )?'checked':(($values['frm_mycred'][$field['id']]['version']!=VERSION)?'checked':'')); ?> name="options[frm_mycred][<?php echo $field['id']; ?>][create_update]" <?php echo $disabled; ?>>Update when creating an entry</label>
	</td>
</tr>
<tr>
    <td width="200px">Transaction type:</td>
    <td>
        <label for="mycred_frm_add_fld_<?php echo $field['id']; ?>"><input class="frm_mycred"  type="radio" id="mycred_frm_add_fld_id_<?php echo $field['id']; ?>" name="options[frm_mycred][<?php echo $field['id']; ?>][trans]" value="add" <?php echo (isset($values['frm_mycred'][$field['id']]['trans']) and $values['frm_mycred'][$field['id']]['trans']=='add')? 'checked':''; ?> <?php echo $disabled; ?>> Credit</label>
        <label for="mycred_frm_sub_fld_<?php echo $field['id']; ?>"><input class="frm_mycred"  type="radio" id="mycred_frm_sub_fld_id_<?php echo $field['id']; ?>" name="options[frm_mycred][<?php echo $field['id']; ?>][trans]" value="sub" <?php echo (isset($values['frm_mycred'][$field['id']]['trans']) and $values['frm_mycred'][$field['id']]['trans']=='sub')? 'checked':''; ?> <?php echo $disabled; ?>> Debit</label>
    </td>
</tr>

	<tr>
		<td>Credit Type</td>
		
    <td>
	 
        <select class="frm_mycred"  id="mycred_frm_type_fld_<?php echo $field['id']; ?>" name="options[frm_mycred][<?php echo $field['id']; ?>][type]" <?php echo $disabled; ?>>
            <option value="">- choose type -</option>
            <?php
			
            foreach($types as $t){

                ?>
                <option value="<?php echo $t['id'];?>" <?php echo (isset($values['frm_mycred'][$field['id']]['type']) and $values['frm_mycred'][$field['id']]['type']== $t['id'])? 'selected':''; ?>><?php echo $t['name'];;?></option>
            <?php
                    }
            $cred_types = maybe_unserialize( get_option('mycred_types') );
		
            if(count($types) < count($cred_types)){
                $count = 0;
                foreach($cred_types as $k => $ct){
                    $count++;
                    if(!isset($types[$count])) {
                        ?>
                        <option value="<?php echo $k;?>" <?php echo (isset($values['frm_mycred'][$field['id']]['type']) and $values['frm_mycred'][$field['id']]['type']== $k)? 'selected':''; ?>><?php echo $ct;?></option>
            <?php
                    }
                }
            }
            ?>

        </select>
		 

    </td>
</tr>

<tr>
<td colspan="2">
<table class="frm_logic_rows" id="frm_logic_row_<?php echo $field['id'] ?>">
    <tr><td colspan="2"><p>Run this action if all of the following match:</p></td></tr>
<?php
$field_id = $field['id'];
if(isset($values['frm_mycred'][$field['id']]['logic']))
    foreach($values['frm_mycred'][$field['id']]['logic'] as $meta_name => $logics) include('_logic_row.php');
?>
    <?php $fid=$field['id'];?>
    </table>
</td>
</tr>
<tr><td colspan="2" class="hotaha"><a class="button" href="javascript:frmMCAddLogicRow(<?php echo $fid; ?>);">+ <?php _e('Add Conditional Logic', 'formidable') ?></a></td></tr>
<tr>
	<td colspan="2"><hr></td>
<tr>
<tr>
    <td>Log text</td><td style="width: 50%"> 
		<?php 
		
		if(isset($values['frm_mycred'][$field['id']]['log_message']) && $values['frm_mycred'][$field['id']]['log_message']!='' )
			$content = $values['frm_mycred'][$field['id']]['log_message'];
		else
			$content = get_option('defaultmycread_log');
		
		$editor_id = 'mycred_frm_error_fld_'.$field['id'];
		
		if($content == '' )
			$content = '%form% %action%: %credit%';
		
		$content = stripslashes($content);
		 
		 ?>
		<textarea style="width: 90%"  rows="5" class="frm_mycred" id="<?php echo $editor_id?>" name="options[frm_mycred][<?php echo $field['id']; ?>][log_message]"><?php echo $content;?></textarea>
		<p style="margin:3px; padding:0"><small>%form% : Replaced with the name of the form <br>%action%: Replaced with the name of the form action<br>%credit%: Replaced with the name of the credit type</small></p>
	
	</td>
</tr>
<?php 

 
global $wpdb;
$prefix = $wpdb->prefix;
$table = $prefix.'frm_forms';
$form_is = $wpdb->get_var("SELECT options from $table where id=$form_id");
$form_is  = maybe_unserialize($form_is);
// print_r($form_is);
// print_r($form_is);
if(isset($form_is['frm_mycred']) && !empty($form_is['frm_mycred']))
{
	foreach($form_is['frm_mycred'] as $key=>$val) 
	{
		if(isset($val['balance_error_type']))
		{
			$_set_key = $key;
		}
	}
}
if( (count($form_is['frm_mycred']) > 0 && $field['id'] == $_set_key && isset($_set_key) ) || empty($form_is['frm_mycred']) || !isset($_set_key))
{ 
?>
<tr>
	<td colspan="2"><hr></td>
<tr>
<tr>
    <td>Balance Error Handling</td><td style="width: 50%"> 
		<select class="frm_mycred frm_mycred_balance_error"  name="options[frm_mycred][<?php echo $field['id']; ?>][balance_error_type]" id="mycred_frm_balance_error_type_fld_<?php echo $field['id']; ?>" class="frm_mycred"   >
			<option data-id="balance-handing-message" value="0" <?php echo (isset($values['frm_mycred'][$field['id']]['balance_error_type']) and $values['frm_mycred'][$field['id']]['balance_error_type']== 0)? 'selected':''; ?>>Show a message</option>
			<option data-id="balance-handing-route-page" value="1" <?php echo (isset($values['frm_mycred'][$field['id']]['balance_error_type']) and $values['frm_mycred'][$field['id']]['balance_error_type']== 1)? 'selected':''; ?> >Re-route to another page </option>
			<option data-id="balance-handing-open-overlay" value="2" <?php echo (isset($values['frm_mycred'][$field['id']]['balance_error_type']) and $values['frm_mycred'][$field['id']]['balance_error_type']== 2)? 'selected':''; ?>>Open a form in an overlay window</option>
			<option value="3" <?php echo (isset($values['frm_mycred'][$field['id']]['balance_error_type']) and $values['frm_mycred'][$field['id']]['balance_error_type']== 3)? 'selected':''; ?>>Do nothing</option>
		</select>
	</td>
</tr>

<tr>
	<td>Credit Type</td>
	<td>
		<?php 
			$error_credit_type = isset($values['frm_mycred'][$field['id']]['balance_error_credit_type'])?$values['frm_mycred'][$field['id']]['balance_error_credit_type']:'';
				 
                
				
				foreach($types as $t){
					$checked = (!isset($values['frm_mycred'][$field['id']]))?'checked':'';
					if(is_array($error_credit_type))
					{
						if (in_array($t['id'], $error_credit_type))
						{
							$checked = 'checked';
						}
					}
					else
					{
						if($t['id']==$error_credit_type)
							$checked = 'checked';
					}
					?>
						<label><input type="checkbox" id="frm_mycred_balance_type_error_<?php echo $field['id']; ?>" name="options[frm_mycred][<?php echo $field['id']; ?>][balance_error_credit_type][]" value="<?php echo $t['id'];?>" <?php echo $checked; ?> ><?php echo $t['name'];;?></label>
					<?php
							}
					$cred_types = maybe_unserialize( get_option('mycred_types') );
				
					if(count($types) < count($cred_types)){
						$count = 0;
						foreach($cred_types as $k => $ct){
							$count++;
							$checked =(!isset($values['frm_mycred'][$field['id']]))?'checked':'';
							if(is_array($error_credit_type))
							{
								if (in_array($k, $error_credit_type))
								{
									$checked = 'checked';
								}
							}
							else
							{
								if($k==$error_credit_type)
									$checked = 'checked';
							}
							if(!isset($types[$count])) {
								?>
								<label><input type="checkbox" id="frm_mycred_balance_type_error_<?php echo $field['id']; ?>" name="options[frm_mycred][<?php echo $field['id']; ?>][balance_error_credit_type][]" value="<?php echo $k;?>" <?php echo $checked; ?> ><?php echo $ct;?></label>
					<?php
							}
						}
					}
              
             
		?>
	</td>
</tr>

<tr id="balance-handing-message" class="frm_mycred_balance_error_hide">
    <td>Balance Error Handling Message</td><td style="width: 50%"><textarea rows="5" class="frm_mycred" type="text" style="width: 90%" id="mycred_frm_error_fld_<?php echo $field['id']; ?>"  name="options[frm_mycred][<?php echo $field['id']; ?>][error]"  <?php echo $disabled; ?>><?php echo (isset($values['frm_mycred'][$field['id']]['error']))? stripslashes($values['frm_mycred'][$field['id']]['error']):'The form submission has been blocked because you don\'t have enough balance.'; ?></textarea></td>
	 
	
	 
</tr>
<tr id="balance-handing-route-page" class="frm_mycred_balance_error_hide">
    <td>Select Page to Route</td><td style="width: 50%"> 
	<select class="frm_mycred"  name="options[frm_mycred][<?php echo $field['id']; ?>][route_page]" id="mycred_frm_route_page_fld_<?php echo $field['id']; ?>" class="frm_mycred"   >
	<?php $posts_array = get_posts( array('posts_per_page'=>-1,'post_type'=>'page') );
	foreach ( $posts_array as $post ) :  
		?>
		 <option value="<?php echo $post->ID;?>" <?php echo (isset($values['frm_mycred'][$field['id']]['route_page']) and $values['frm_mycred'][$field['id']]['route_page']== $post->ID)? 'selected':''; ?>><?php echo $post->post_title;?></option>

	<?php		
	endforeach; wp_reset_postdata();
	?>
	</select>
		
	</td>
</tr>
<tr id="balance-handing-open-overlay" class="frm_mycred_balance_error_hide">
	
    <td>Select Form to overlay</td><td style="width: 50%">
		<?php global $wpdb;
        $prefix = $wpdb->prefix;
        $table = $prefix.'frm_forms';

        $posts_array = $wpdb->get_results("SELECT * FROM $table WHERE status='published' && is_template!='1' ");
		 
        $posts_array = maybe_unserialize($posts_array);
		
		 ?>
		<select class="frm_mycred"  name="options[frm_mycred][<?php echo $field['id']; ?>][open_overlay]" id="mycred_frm_open_overlay_fld_<?php echo $field['id']; ?>" class="frm_mycred"   >
	<?php 
		
	foreach ( $posts_array as $post ) :  
		?>
		 <option value="<?php echo $post->id;?>" <?php echo (isset($values['frm_mycred'][$field['id']]['open_overlay']) and $values['frm_mycred'][$field['id']]['open_overlay']== $post->id)? 'selected':''; ?>><?php echo $post->name;?></option>

	<?php		
	endforeach; wp_reset_postdata();
	?>
	</select>
	</td>
</tr>
<?php } else if(isset($_set_key))
{ 
	$post = get_post($_set_key);
	 
?>
	<tr>
		<td>Balance handling is handled in the action: </td>
		<td><strong><?php echo $post->post_title; ?><strong></td>
	</tr>
<?php }	?>



</tbody></table><!--<table style="display: none;"><tbody>-->
 
<style type="text/css">

    .mycred_frm_remove_tag:link,
    .mycred_frm_remove_tag:hover{
        text-decoration: none;
        background-color: #adadad;
        color: #ddd;
        border-radius: 15px;
        padding: 1px 4px 2px 8px;
    }
    h3#logic_link_<?php echo $fid; ?> {
        display: none;
    }
</style>

<script type="text/javascript">
    var frm= <?php echo $form_id; ?>;
    jQuery(document).ready(function($){

        $('#frm_delete_field<?php echo $field['id']; ?>').click(function(){
            var f_id = $(this).attr('id').replace('frm_delete_field','');
            $.ajax({
                type:"POST",
                url:"<?php echo admin_url( 'admin-ajax.php' ) ?>",
                data:"action=frm_mc_garbage_collector&f="+f_id+"&frm="+frm,
                success:function(html){ /*alert(html);*/
                }
            });
        });
		 
		 
        $('input#mycred_frm_isfixed_fld_<?php echo $field['id']; ?>').click(function(){
            if(this.checked){
                $('.mycred_frm_fixed').fadeIn();
                $('#mycred_frm_amount_fld_<?php echo $field['id']; ?>').val("").prop( "disabled", true );
            }
            else{
                $('.mycred_frm_fixed').val("").fadeOut();
                $('#mycred_frm_amount_fld_<?php echo $field['id']; ?>').prop( "disabled", false );
            }
        });

        if($('input#mycred_frm_isfixed_fld_<?php echo $field['id']; ?>').val() == '1'){
            $('.mycred_frm_fixed').show();
            $('#mycred_frm_amount_fld_<?php echo $field['id']; ?>').val("").prop( "disabled", true );
        }else{
            $('.mycred_frm_fixed').hide();
        }
        $('.frm_mycred').on('change',function(){
            if($(this).attr('type') == 'checkbox') if($(this).prop('checked') == false) $(this).val('0'); else $(this).val('1');
        });
        /*$('.frm_mycred').on('change',function(){
            if($(this).attr('type') == 'checkbox') if($(this).prop('checked') == false) $(this).val('0'); else $(this).val('1');
            //alert($(this).val());
            var val = $(this).val();
            var id= $(this).attr('id');
            $.ajax({
                type:"POST",
                url:"<?php //echo admin_url( 'admin-ajax.php' ) ?>",
                data:"action=mycred_frm_store_options&val="+val+"&name="+id+"&frm="+frm,
                success:function(html){ *//*alert(html);*//*
                }
            });
        });*/
        $('.frm_logic_rows').on('click', '.mycred_frm_remove_tag', frmMCRemoveLogicRow);
		$('.frm_mycred_balance_error').change(function(){
			$('.frm_mycred_balance_error_hide').hide();
			var id =$(this).find(':selected').data('id');
			if(id!='')
			{
				$('#'+id).show();
			}
				
			
		}).change();

    });
 
    function frmLogicChange(val,id,meta,name){
        /*alert('Got it');*/
        var total = jQuery('#mycred_frm_logicfld_fld_'+id+'_'+meta).val()+'*'+jQuery('#mycred_frm_cond_fld_'+id+'_'+meta).val()+'*'+jQuery('#mycred_frm_opt_fld_'+id+'_'+meta).val();

        jQuery.ajax({
            type:"POST",
            url:"<?php echo admin_url( 'admin-ajax.php' ) ?>",
            data:"action=mycred_frm_store_options&val="+total+"&name="+name+"&frm="+frm,
            success:function(html){ //**//*jQuery('.hotaha').append('<pre>'+html);*//**//*
				
				 
            }
        });
    }
    function frmMCGetFieldValues(selected_id,fld_id,cond_id){
        if(selected_id){
            jQuery.ajax({
                type:"POST",url:ajaxurl,
                data:"action=frm_mc_get_field_values&form_id="+frm+"&cond_id="+cond_id+"&selected_id="+selected_id+"&field_id="+fld_id,
                success:function(msg){jQuery('#frm_show_selected_values_'+fld_id+'_'+cond_id).html(msg);}
            });
        }
    }
    function frmMCRemoveLogicRow(){
        jQuery('#'+jQuery(this).closest('.mycred_frm_logic_row').attr('id')).fadeOut(1000, function(){
            jQuery(this).closest('.mycred_frm_logic_row').replaceWith('');
        });
        var ele = jQuery(this).attr('id');
        jQuery.ajax({
            type:"POST",url:ajaxurl,
            data:"action=frm_mc_remove_logic_row&form_id="+frm+"&ele="+ele,
            success:function(html){}
        });
    }
    function frmMCAddLogicRow(id){
        if(jQuery('#frm_logic_row_'+id+' .mycred_frm_logic_row').length)
            var len=1+parseInt(jQuery('#frm_logic_row_'+id+' .mycred_frm_logic_row:last').attr('id').replace('mycred_frm_'+id+'_', ''));
        else var len=0;

        jQuery.ajax({
            type:"POST",url:ajaxurl,
            data:"action=frm_mc_add_logic_row&form_id="+frm+"&field_id="+id+"&meta_name="+len,
            success:function(html){/*jQuery('.frm_logic_label').show();*/jQuery('#frm_logic_row_'+id).append(html);}
        });
    }
</script>