<?php

/**
*   mediadb_page 
* 
*   Outputs code for the media database page.
*   $atts     -- array of attributes
*/ 
function mediadb_page() {

	// is user logged in?
	if ( is_user_logged_in() == FALSE  ) { 

		// output a user login form to prompt the user to login
		$content = '<link rel="stylesheet" href="' . get_bloginfo('url') . '/wp-admin/css/login.css" type="text/css" />';

		$content .=  '<h2>Please login in order to access the Of The Colour of the Blue Sky <em>Database</em>.</h2>';		

		$content .=  '<div id="login">';

		$content .=  '<form name="loginform" id="loginform" action="' . get_bloginfo('url') . '/wp-login.php" method="post">';
		$content .=  '	<p>
			<label for="user_login">Username<br />
			<input type="text" name="log" id="user_login" class="input" value="" size="10" tabindex="10" /></label>
			</p>';

		$content .=  '	<p>
			<label for="user_pass">Password<br />
			<input type="password" name="pwd" id="user_pass" class="input" value="" size="20" tabindex="20" /></label>
 			</p>';

		$content .=  '	<p class="forgetmenot">
			<label><input name="rememberme" type="checkbox" id="rememberme" value="forever" tabindex="90" /> Remember Me</label>
			</p>';

		$content .=  '	<p class="submit">
			  <input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="Log In" tabindex="100" />
			  <input type="hidden" name="redirect_to" value="' . get_permalink($wp_query->post->ID) . '" />
			  <input type="hidden" name="testcookie" value="1" />
			</p>';
		$content .=  '</form>';

	}
	else {

		// output the database description
		$content = '<p>Welcome! This is an experiment for us. The impetus behind the database is that we have a lot of material that was created for our latest album release, Of The Blue Colour Of The Sky, and not all of it fits into a retailer’s vision of what should live on the shelf of their store. So we thought we’d give everyone who buys the Extra Nice Edition of the album access to this webpage where you can download whatever else we think to release, without us having to find some other clever way of finding you.</p><p>To begin, we’ve got the first disc of the album, in case you bought the physical CD and didn’t feel like ripping it yourself. Second is a collection of new OK Go remixes that we are particularly excited about. So, like we said, it’s an experiment. Let us know what you think, and if anything doesn’t work quite right, please let us know that too by emailing us at: okgodatabasehelp<at>gmail dot com</p>';

		// output the database access forms
		$content .= '<form id="validcodeform">';
		$content .= '<div id="code-block">
				     <label for="code-input">Your Database Code:</label>
				     <input type="text" name="code-input" id="code-input" value="enter valid code" onfocus="if(this.value == \'enter valid code\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'enter valid code\'; }" />
				     <button id="code-submit" class="button-link">Validate</button>
				     <input type="hidden" id="processing-url" value="' . MEDIADB_PLUGIN_URL . '/js/codecheck.php" />
				     <input type="hidden" id="user-id" value="' . get_current_user_id() . '" />
				     </div>';
		$content .= '</form>';
		$content .= '<form id="mediaselectform">';
		$content .= '<select id="media-selection">';
		
		// add <option> tags 
		global $wpdb;
		$sql = "SELECT media_id, filename, description FROM wp_mediadb_media";
		$media = $wpdb->get_results($sql);
		foreach ($media as $item) {
			$content .= '<option value="' . $item->media_id . '">' . $item->description . '</option>';
		}
		$content .= '</select>';
		$content .= '<button id="media-submit" class="button-link">Download</button>';
		$content .= '</form>';
	
		// if the user has entered a code disable the form
		$db_code_is = get_user_meta( get_current_user_id(), 'mediadb_codestatus', true);
		if ( $db_code_is == "valid" ) {
			$search = array(
					'<input type="text" name="code-input" id="code-input" value="enter valid code" onfocus="if(this.value == \'enter valid code\') { this.value = \'\'; }" onblur="if(this.value == \'\') { this.value = \'enter valid code\'; }" />', 
					'<button id="code-submit" class="button-link">Validate</button>'
				);
			$replace = array(
					'<input type="text" name="valid-code" id="valid-code" disabled="true" value="' . get_user_meta( get_current_user_id(), 'mediadb_code', true) . '" />',
					''
					);
			$content = str_replace($search, $replace, $content);
		}

		// Now output the database contents
			}
	return $content;
}	
add_shortcode('mediadb-page', 'mediadb_page');

/**
* add_css_conditionally
*
* This function enqueues the necessary styles and scripts if the page 
* is using the mediadb-page shortcode
*
* Credit: Artem Russakovski (http://beerpla.net)
* 
*/
function conditionally_add_scripts_and_styles($posts){
	if (empty($posts)) return $posts;
 
	$shortcode_found = false; // use this flag to see if styles and scripts need to be enqueued
	foreach ($posts as $post) {
		if (stripos($post->post_content, '[mediadb-page]') !== false) {
			$shortcode_found = true; // bingo!
			break;
		}
	}
 
	if ($shortcode_found) {
		// enqueue here
		wp_enqueue_style('mediadb-style', MEDIADB_PLUGIN_URL . '/css/mediadb.css');
		wp_enqueue_script('mediadb-script', MEDIADB_PLUGIN_URL . '/js/mediadb-scripts.js');
	}
 
	return $posts;
}
add_filter('the_posts', 'conditionally_add_scripts_and_styles'); // the_posts gets triggered before wp_head

?>
