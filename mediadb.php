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

// Constants
define('MEDIADB_PLUGIN_PATH', dirname(__FILE__));
define('MEDIADB_PLUGIN_URL',WP_PLUGIN_URL.'/'.basename(MEDIADB_PLUGIN_PATH));

// Include PHP
require_once('mediadb_shortcodes.php');

// Register hooks
register_activation_hook(__FILE__,'mediadb_install');  // Runs when plugin is activated
register_deactivation_hook( __FILE__, 'mediadb_uninstall' ); // Runs on plugin deactivation

/**
 * Initialize custom scripts 
 */
function mediadb_enqueue($hook) {
	/*if ( $hook != 'profile.php' ) // do not enqueue if this is not the user profile view/edit page 
	return;
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'code-validator.js', plugins_url('resources/code-validator.js', __FILE__) );*/
}
//add_action( 'admin_enqueue_scripts', 'mediadb_enqueue' );

/** 
 * Add Administration Menu
 */
function mediadb_add_admin_menu() {
	add_options_page('Media Database', 'Media Database', 'administrator', 'mediadb', 'mediadb_admin_settings_page');
}
//add_action('admin_menu', 'mediadb_add_admin_menu');

/**
 * mediadb_install
 * 
 * Performs tasks for mediadb plugin install
 */
function mediadb_install() {
	// create tables
	mediadb_runsql('mediadb_validcodes.sql');
	mediadb_runsql('mediadb_media.sql');

	// fill tables
	mediadb_runsql('mediadb_validcodes_fill.sql'); // first set of valid codes
	
	// add second set of codes from merchdirect
	global $wpdb;
	$data = simplexml_load_file('http://merchdirect.com/x/xml/promoCodes.php?key=03dae3a2de4e92ead443d7cf413ca2cc');
	foreach ($data->code as $code) {
		$sql = "INSERT INTO " . $wpdb->prefix . "mediadb_validcodes VALUES ('" . $code . "')";
		$wpdb->query($sql);
	}

	// fill media table
	mediadb_runsql('mediadb_media_fill.sql');
}

/** 
 * mediadb_uninstall
 * 
 * Performs taks for mediadb plugin uninstall
 */
function mediadb_uninstall() {
	// remove valid codes table from wordpress db
	global $wpdb;
	$tables_to_drop = array( $wpdb->prefix.'mediadb_validcodes', $wpdb->prefix.'mediadb_media' );
	foreach ($tables_to_drop as $table) {
		$sql = "DROP TABLE ". $table;
		$wpdb->query($sql);
	}
}

/**
 * mediadb_run_sql
 *
 * Runs specified sql file using wordpress database 
 */
function mediadb_runsql($filename) {
	$file = MEDIADB_PLUGIN_PATH.'/sql/'.$filename;
	if( file_exists($file) ) {
		global $wpdb;  // the Wordpress database
		ob_start();
		include($file);
		$sql = ob_get_contents();
		ob_end_clean();
		$wpdb->query($sql);
	}
}


?>
