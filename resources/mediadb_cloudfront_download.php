<?php

ini_set('display_errors',1); 
 error_reporting(E_ALL);

// Load S3 PHP Class
if (!class_exists('S3')) require_once( 'S3.php' );

// If the user is not logged, then this might be a hack attempt; so give not found error.
require_once( $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php' );
if ( ! is_user_logged_in() ) { 
    die('You do not have access. Sorry.'); 
}

global $wpdb;
if(!isset($wpdb)) // if the $wpdb variable is not set, need to load wp files for it
{
    require_once('../../../../wp-config.php');
    require_once('../../../../wp-includes/wp-db.php');
}  

//******************//
// Settings & Setup //
//******************//

// Download folder, i.e. folder where you keep all files for download.
// MUST end with slash (i.e. "/" )
define('BASE_URL','http://media.okgo.net');

// Initialize S3 Class Object

// AWS access info
if (!defined('awsAccessKey')) define('awsAccessKey', 'AKIAIIFKXR2UUAMU7NWA');
if (!defined('awsSecretKey')) define('awsSecretKey', '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');

// Check for CURL
if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
  die("\nERROR: CURL extension not loaded\n\n");

// Pointless without your keys!
if (awsAccessKey !== 'AKIAIIFKXR2UUAMU7NWA' || awsSecretKey !== 'MLyi4q/mEvf84DC4axVf/DKrhDwGqPTnv6UOO8pC')
  die("\nERROR: AWS access information required\n\nPlease edit the following lines in this file:\n\n".
  "define('awsAccessKey', 'change-me');\ndefine('awsSecretKey', 'change-me');\n\n");

// Create S3 Objcet
$s3 = new s3(awsAccessKey, awsSecretKey);
//print_r($s3->listBuckets());
//echo "\n";
//print_r($s3->getObjectInfo('media.okgo.net', 'extranice-db/extraniceedition/otbcots/otbcotsLL.zip'));

//*****************************//
// Code to Start Download      //
//*****************************//

/*if ( !isset($_POST['media_id']) || empty($_POST['media_id']) ) {
  die("Please specify file name for download.");
}

// identify the filename of the requested media by querying wp_mediadb_media table in wp database
$media_id = $_GET['media_id'];
$sql = "SELECT media_id, filename FROM wp_mediadb_media WHERE media_id = '" . $media_id . "'";
$result = $wpdb->get_results($sql);
if ( count($result) == 1 ) {
	$filename = $result[0]->filename;
} else { die("Something went wrong."); }*/

echo 'http://media.okgo.net/extranice-db/extraniceedition/otbcots/otbcotsLL.zip';

?>
