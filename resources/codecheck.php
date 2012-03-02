<?php

global $wpdb;
if(!isset($wpdb)) // if the $wpdb variable is not set, need to load wp files for it
{
    require_once('../../../../wp-config.php');
    require_once('../../../../wp-includes/wp-db.php');
}

$code = strtoupper($_POST['code']); // get submitted code
$userid = $_POST['user_id'];      // get user id

// query table
$sql = "SELECT code FROM wp_mediadb_validcodes WHERE code = '". mysql_real_escape_string($code) . "'";
$results = $wpdb->get_results($sql);

// if there was a query result of 1, the code is valid
if ( count($results) == 1 ) {
	
	// check to see if the code is unique (ie not already used).
	if ( is_unique_code($userid, $code) ) { 
		$return = array(
				'code'   => $code,
				'status' => 'valid'
			);
	} 
	else { // code's not unique
		$return = array ( 
				'code'   => $code,
				'status' => 'already used'
			);
	}
} 
else {
	$return = array( 
			'code'   => $code,
			'status' => 'invalid'
		);
}

// add results to user meta data
update_user_meta( $userid, 'mediadb_code', $return['code'] ); 
update_user_meta( $userid, 'mediadb_codestatus', $return['status'] );

// return result to browser
echo json_encode($return);

/**
* is_unique_code
* 
* Returns TRUE if the code is unique, ie not already assocaited with
* another user's account.  Returns FALSE if it is not unique.
*/
function is_unique_code($user_id, $code) {

	global $wpdb;

	$sql = "SELECT * FROM wp_usermeta 
		WHERE meta_key = 'mediadb_code' 
		AND meta_value = '" . mysql_real_escape_string($code) . "' 
		AND user_id != '" . $user_id . "'";

        $results = $wpdb->get_results($sql);

        if ( count($results) == 0 ) { // code is unique
		return true;
	} else { return false; }

}
?>
