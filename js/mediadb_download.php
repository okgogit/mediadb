<?php

require_once('../../../themes/okgo-theme/library/ChromePHP/ChromePhp.php');
ChromePhp::log('test');
global $wpdb;
if(!isset($wpdb)) // if the $wpdb variable is not set, need to load wp files for it
{
    require_once('../../../../wp-config.php');
    require_once('../../../../wp-includes/wp-db.php');
}

//**********//
// Settings //
//**********//

// Allow direct file download (hotlinking)?
// Empty - allow hotlinking
// If set to nonempty value (Example: example.com) will only allow downloads when referrer contains this text
define('ALLOWED_REFERRER', '');

// Download folder, i.e. folder where you keep all files for download.
// MUST end with slash (i.e. "/" )
define('BASE_DIR','/home/okgo/okgo-cloudfront/extranice-db/extraniceedition');

// Allowed extensions list in format 'extension' => 'mime type'
// If myme type is set to empty string then script will try to detect mime type 
// itself, which would only work if you have Mimetype or Fileinfo extensions
// installed on server.
$allowed_ext = array (
	'zip' => 'application/zip'
);

//*****************************//
// Code to Start Download      //
//*****************************//

// If hotlinking not allowed then make hackers think there are some server problems
if (ALLOWED_REFERRER !== '' 
    && (!isset($_SERVER['HTTP_REFERER']) 
    || strpos(strtoupper($_SERVER['HTTP_REFERER']),strtoupper(ALLOWED_REFERRER)) === false) ) {
	die("Internal server error. Please contact system administrator.");
}

// Make sure program execution doesn't time out
// Set maximum script execution time in seconds (0 means no limit)
set_time_limit(0);

if ( !isset($_GET['media_id']) || empty($_GET['media_id']) ) {
  die("Please specify file name for download.");
}

// identify which file has bene requested by querying database
$media_id = $_GET['media_id'];
$sql = "SELECT media_id, filename FROM wp_mediadb_media WHERE media_id = '" . $media_id . "'";
$result = $wpdb->get_results($sql);
if ( count($result) == 1 ) {
	$file = $result[0]->filename;
} else { die("Something went wrong."); }

// Check if the file exists
// Check in subfolders too
function find_file ($dirname, $fname, &$file_path) {
	$dir = opendir($dirname);
  	
	while ($file = readdir($dir)) {
		if (empty($file_path) && $file != '.' && $file != '..') {
      			if (is_dir($dirname.'/'.$file)) {
				find_file($dirname.'/'.$file, $fname, $file_path);
			}
			else {
        			if (file_exists($dirname.'/'.$fname)) {
					$file_path = $dirname.'/'.$fname;
					return;
				}	
			}
	    	}
	}
} // find_file

// get full file path (including subfolders)
$file_path = '';
find_file(BASE_DIR, $file, $file_path);
ChromePhp::log('file_path:',$file_path);

// if the file doesn't exist, kill process.
if (!is_file($file_path)) {
  die("File does not exist. Make sure you specified correct file name."); 
}

// file size in bytes
$fsize = filesize($file_path); 

// file extension
$fext = strtolower(substr(strrchr($file,"."),1));

// check if it is allowed to download this extension
if (!array_key_exists($fext, $allowed_ext)) {
  die("Not allowed file type."); 
}

// get mime type
if ($allowed_ext[$fext] == '') {
  $mtype = '';
  // mime type is not set, get from server settings
  if (function_exists('mime_content_type')) {
    $mtype = mime_content_type($file_path);
  }
  else if (function_exists('finfo_file')) {
    $finfo = finfo_open(FILEINFO_MIME); // return mime type
    $mtype = finfo_file($finfo, $file_path);
    finfo_close($finfo);  
  }
  if ($mtype == '') {
    $mtype = "application/force-download";
  }
}
else {
  // get mime type defined by admin
  $mtype = $allowed_ext[$fext];
}

// Remove any bad characters from filename
$filename = $file;
$filename = str_replace(array('"',"'",'\\','/'), '', $filename);
if ($file === '') $filename = 'NoName';

// set headers
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
header("Content-Description: File Transfer");
header("Content-Type: $mtype");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: " . $fsize);

// download
/*$fd = fopen($file_path, "rb");
while(!feof($fd)) {
        print( fread($fd, 2048) );
	flush();
        echo $buffer;
}
fclose($fd);*/

// @readfile($file_path);
$file_to_download = @fopen($file_path,"rb");
if ($file_to_download) {
  while(!feof($file_to_download)) {
    print(fread($file_to_downoad, 1024*8));
    flush();
    if (connection_status()!=0) {
      @fclose($file_to_download);
      die();
    }
  }
  @fclose($file_to_download);
}



?>
