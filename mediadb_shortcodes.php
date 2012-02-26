<?php

/**
*   mediadb_page 
* 
*   Outputs code for the media database page.
*   $atts     -- array of attributes
*/ 
function mediadb_page() {

	// is user logged in?
	if ( is_user_logged_in() == true  ) { 

		// output a user login form to prompt the user to login
		$content = '<link rel="stylesheet" href="' . get_bloginfo('url') . '/wp-admin/css/login.css" type="text/css" />';

		$content .=  '<h2>Please login in order to access the Of The Colour of the Blue Sky <em>Database</em>.</h2>';		

		$content .=  '<div id="login">';

		$content .=  '<form name="loginform" id="loginform" action="<?=$blogurl ?>/wp-login.php" method="post">';
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
	return $content;
}	
add_shortcode('mediadb-page', 'mediadb_page');

?>
