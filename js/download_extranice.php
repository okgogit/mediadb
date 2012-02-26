<?php
ini_set('memory_limit',-1);
set_time_limit(86400);
ini_set('max_execution_time',86400);
ini_set('max_input_time',86400);
//@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
/*
http://www.okgodevhost.net/download_extranice.php?file=Of%20The%20Blue%20Colour%20of%20the%20Sky/01%20What%20the%20Fuck%20Is%20Happening.mp3

*/
include_once($_SERVER["DOCUMENT_ROOT"].'/wp-load.php'); // load wordpress functions

$user = wp_get_current_user();

ChromePhp::useFile('chromephplog','chromephplog');
ChromePhp::log($user);

if (!empty($user->code) && $user->valid_code=='valid') {
	if (empty($_GET['file']) || !file_exists('/home/okgo/okgo.net/files/extraniceedition/'.urldecode($_GET['file']))) {
		header('Location: /');
		exit;
	}
	//echo 'You have access.';
	$file = '/home/okgo/okgo.net/files/extraniceedition/'.urldecode($_GET['file']);
	
	header('Location: /files/extraniceedition/'.$_GET['file']);
	exit;
	
	header ('Cache-Control: must-revalidate, post-check=0, pre-check=0'); 
	header ('Content-Type: application/octet-stream'); 
	header ('Content-Length: ' . filesize($file)); 
	header ("Content-Disposition: attachment; filename=\"".basename($file)."\""); 
	
	passthru('cat "'.$file.'"');
	//readfile($file);
	/*$chunksize = 1 * (1024 * 1024); // how many bytes per chunk
	if (intval(sprintf("%u", filesize($file))) > $chunksize) {
	  $handle = fopen($file, 'rb');
	  $buffer = '';
	  while (!feof($handle)) {
	    $buffer = fread($handle, $chunksize);
	    echo $buffer;
	    ob_flush();
	    flush();
	  }
	  fclose($handle);
	} else {
	  readfile($file);
	}*/
	exit;
} else {
	header('Location: /wp-login.php');
	exit;
}
?>
