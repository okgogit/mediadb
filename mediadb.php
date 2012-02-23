<?php
/*
Plugin Name: Media Database
Plugin URI: 
Description: A media access system for wordpress users.
Version: 0.1
Author: eThan 
Author URI: 
License: GPL2
*/
?>
<?php

// Include other PHP files
require_once dirname(__FILE__) . '/pages/mediadb-settings-page.php';

/**
 * Initialize custom scripts 
 */
function mediadb_enqueue($hook) {
    if ( $hook != 'profile.php' ) // do not enqueue if this is not the user profile view/edit page 
        return;
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'code-validator.js', plugins_url('js/code-validator.js', __FILE__) );
}
//add_action( 'admin_enqueue_scripts', 'mediadb_enqueue' );

/** 
 * Add Administration Menu
 */
add_action('admin_menu', 'mediadb_add_admin_menu');
function mediadb_add_admin_menu() {
    add_options_page('Media Database', 'Media Database', 'administrator', 'mediadb', 'mediadb_admin_settings_page');
}


?>
