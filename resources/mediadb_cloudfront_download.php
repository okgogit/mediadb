<?php

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
$bucket = 'media.okgo.net';

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

//*****************************//
// Code to Start Download      //
//*****************************//

if ( !isset($_POST['media_id']) || empty($_POST['media_id']) ) {
  die("Please specify file name for download.");
}

// identify the filename of the requested media by querying wp_mediadb_media table in wp database
$media_id = $_POST['media_id'];
$sql = "SELECT media_id, filename FROM wp_mediadb_media WHERE media_id = '" . $media_id . "'";
$result = $wpdb->get_results($sql);
if ( count($result) == 1 ) {
	$filename = $result[0]->filename;
} else { die("Unable to retrieve the file name."); }

// find the file and file path in the amazon s3 bucket
if ( ($contents = $s3->getBucket($bucket)) !== FALSE ) { 
  foreach ($contents as $object) {
    if ( strpos($object['name'], $filename) !== FALSE ) {
      $fullpath = $object['name'];
    }
  }
}

//return an authenticated url
echo $s3->getAuthenticatedURL($bucket, $fullpath, 60);

//echo getSignedURL($unsigned_url, 3600);

/**
* getSignedURL
* 
* This PHP function will create a signed URL with a canned policy for serving CloudFront private content.
*
* To use this function pass it the resource url, and the amount of time the url will be active 
* for. To create a custom policy signed url, you will have to modify the function slightly to 
* add a url safe policy.
* 
* For example the following will create a signed url that is active for 60 seconds:
*   $url = getSignedURL("http://abcdefg.cloudfront.net/test.jpg", 60);
*
* Credit: http://aws.amazon.com/code/3514
* 
*/
function getSignedURL($resource, $timeout)
{
  //This comes from key pair you generated for cloudfront
  $keyPairId = "APKAJXTQ277YAD6ERDNQ";  // get from "security credentials" page in aws console

  $expires = time() + $timeout; //Time out in seconds
  $json = '{"Statement":[{"Resource":"'.$resource.'","Condition":{"DateLessThan":{"AWS:EpochTime":'.$expires.'}}}]}';   
  
  //Read Cloudfront Private Key Pair
  $fp = fopen("pk-APKAJXTQ277YAD6ERDNQ.pem","r"); 
  $priv_key =fread($fp,8192); 
  fclose($fp); 

  //Create the private key
  $key = openssl_get_privatekey($priv_key);
  if(!$key)
  {
    echo "<p>Failed to load private key!</p>";
    return;
  }
  
  //Sign the policy with the private key
  if(!openssl_sign($json, $signed_policy, $key, OPENSSL_ALGO_SHA1))
  {
    echo '<p>Failed to sign policy: '.openssl_error_string().'</p>';
    return;
  }
  
  //Create url safe signed policy
  $base64_signed_policy = base64_encode($signed_policy);
  $signature = str_replace(array('+','=','/'), array('-','_','~'), $base64_signed_policy);

  //Construct the URL
  $url = $resource.'?Expires='.$expires.'&Signature='.$signature.'&Key-Pair-Id='.$keyPairId;
  
  return $url;
}

?>
