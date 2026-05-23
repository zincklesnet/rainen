<?php

class FrmMCSettings{

    function frm_mycred_update($opt,$frm_id,$fld_id,$key){
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table = $prefix.'frm_forms';
        $old_opt_ser = $wpdb->get_var("SELECT options FROM $table WHERE id=$frm_id");
        $old_opt = maybe_unserialize($old_opt_ser);
        if($key == 'isfixed' and $opt['frm_mycred'][$fld_id][$key] == '1') {
            if(isset($old_opt['frm_mycred'][$fld_id]['amount'])){
                unset($old_opt['frm_mycred'][$fld_id]['amount']);
            }
            $old_opt['frm_mycred'][$fld_id][$key]=$opt['frm_mycred'][$fld_id][$key];
        }
        elseif($key == 'amount') {
            if(isset($old_opt['frm_mycred'][$fld_id]['fixed'])){
                unset($old_opt['frm_mycred'][$fld_id]['fixed']);
            }
            $old_opt['frm_mycred'][$fld_id][$key]=$opt['frm_mycred'][$fld_id][$key];
        }
        elseif($key == 'opt'){
            $meta = 0;
            if(isset($old_opt['frm_mycred'][$fld_id]['logic']) and count($old_opt['frm_mycred'][$fld_id]['logic']) > 1) $meta = count($old_opt['frm_mycred'][$fld_id]['logic']);
            else $old_opt['frm_mycred'][$fld_id]['logic'] = array();
            $old_opt['frm_mycred'][$fld_id]['logic'][$meta] = array(
                'logicfld' => $opt['frm_mycred'][$fld_id]['logic'][$meta]['logicfld'],
                'cond'     => $opt['frm_mycred'][$fld_id]['logic'][$meta]['cond'],
                'opt'      => $opt['frm_mycred'][$fld_id]['logic'][$meta]['opt']
            );

            //print_r($old_opt);
        }else{
            $old_opt['frm_mycred'][$fld_id][$key]=$opt['frm_mycred'][$fld_id][$key];
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

    function frm_mycred_get_opt($frm_id){
        global $wpdb;
        $prefix = $wpdb->prefix;
        $table = $prefix.'frm_forms';

        $opt_ser = $wpdb->get_var("SELECT options FROM $table WHERE id=$frm_id");
        $opt = maybe_unserialize($opt_ser);

        if(isset($opt['frm_mycred'])){
            return $opt['frm_mycred'];
        }else{
            return false;
        }
    }

}