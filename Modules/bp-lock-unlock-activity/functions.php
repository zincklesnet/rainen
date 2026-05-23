<?php
/**
 * Check is activity close get link
 * 
 */

/**
 * Close the acvity
 * @param type $activity_id 
 */
function bplua_close_activity( $activity_id ){
    
   $insatance = bp_activity_lock_get_helper();
   return  $insatance->close( $activity_id );
   
}


/**
 * Open activity 
 * @param type $activity_id 
 */
function bplua_open_activity( $activity_id ){
    
    $insatance = bp_activity_lock_get_helper();
    return $insatance->open( $activity_id );   
}

/**
 *
 * @param int $activity_id
 * @return boolean 
 */

function bplua_is_activity_closed( $activity_id ){
    $insatance = bp_activity_lock_get_helper();
    return $insatance->is_closed( $activity_id );
}
