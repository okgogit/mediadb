<?php

/**
 * Action hooks for adding extra fields to user profiles
 */
add_action( 'show_user_profile', 'mediadb_show_extra_profile_fields' );
add_action( 'edit_user_profile', 'mediadb_show_extra_profile_fields' );

/** 
 * Action functions to add media database functions to user profiles
 */
function mediadb_show_extra_profile_fields( $user ) { 
//echo get_the_author_meta('valid_code', $user->ID);
if (get_the_author_meta('valid_code', $user->ID)!='valid') : ?>
	<h3>Extra Nice Edition Database Code</h3>

	<table class="form-table">

		<tr>
			<th><label for="code">Database Code</label></th>

			<td>
				<input type="text" name="code" id="code" value="<?php echo esc_attr( get_the_author_meta( 'code', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description">Please enter your code if you have one.</span>
			</td>
		</tr>

	</table>
<?php else : ?>
	<h3>Extra Nice Edition Database Code</h3>
	
		<table class="form-table">
	
			<tr>
				<th><label for="code">Database Code</label></th>
	
				<td>
					<input type="text" name="codeDisabled" id="codeDisabled" value="<?php echo esc_attr( get_the_author_meta( 'code', $user->ID ) ); ?>" class="regular-text" disabled="disabled" /><br />
					<span class="description">You have already entered a valid code!.</span>
				</td>
			</tr>
	
		</table>
<?php endif;  
}


/**
 * Action hooks for saving code validity.
 */
add_action( 'personal_options_update', 'mediadb_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'mediadb_save_extra_profile_fields' );

/**
 * Action function for saving code validity to user meta
 */
function mediadb_save_extra_profile_fields( $user_id ) {
	global $wpdb;
	if ( current_user_can( 'edit_user', $user_id ) ) { // if user can edit profile, then...
	
		if (!empty($_POST['code'])) { // if the user has provide a code...

			// first check and see if code is in the valid codes table.
			$query = "SELECT code FROM codes_valid WHERE code = '" . mysql_real_escape_string(strtoupper($_POST['code'])) . "'"; 
			$dbresults = $wpdb->get_results($query);
		
			// if the code was not found in the valid codes table, check the merchdirect list.
			if ( mysql_num_rows($dbresults) != 1 ) { 
				$data = simplexml_load_file('http://merchdirect.com/x/xml/promoCodes.php?key=03dae3a2de4e92ead443d7cf413ca2cc');
				$search = $data->xpath("//code[.='".strtoupper($_POST['code'])."']");
			}

			// now record results in user meta data (wp_usermeta)
			if ( mysql_num_rows($dbresults) == 1 || $search[0] == strtoupper($_POST['code']) ) {  // the code is valid
			
				// check to see if any other user has tried to use this code
				if ( isUniqueCode($user_id, strtoupper($_POST['code'])) ) { // nope, the code is unique
					update_usermeta( $user_id, 'valid_code', 'valid' );
					update_usermeta( $user_id, 'code', strtoupper($_POST['code']) );
				}

			} 
			else { // the code was invalid
				update_usermeta( $user_id, 'valid_code', 'invalid' );
				update_usermeta( $user_id, 'code', strtoupper($_POST['code']) );
			}
		}	
	
	}
}

/**
 * Checks to see if provided code is unique, ie no other user has registerd the same code.
 */
function isUniqueCode($user_id, $code) {
	$query = mysql_query("SELECT COUNT(*) FROM wp_usermeta WHERE meta_key = 'code' AND meta_value = '".mysql_real_escape_string($code)."' AND user_id != '".mysql_real_escape_string($user_id)."'");
	
	$count = mysql_result($query,0,0);
	
	if ($count==0) return true;
	return false;
}

?>
