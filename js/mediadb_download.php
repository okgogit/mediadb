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

//include_once($_SERVER["DOCUMENT_ROOT"].'/wp-load.php'); // load wordpress functions

// identify which file has bene requested
$db_path = 'home/okgo/okgo-cloudfront/extranice-db/extraniceedition';
$media_id = $_POST('media_id');
if ( $media_id = 'otbcots-disc1' ) {
	$file = $db_path . '/otbcots/OftheBlueColouroftheSky.zip';	
}
elseif ( $media_id = 'otbcots-disc1-lossless' ) {
	$file = $db_path . '/otbcots/otbcotsLL.zip';
}

@header("Content-type: application/zip");
@header("Content-Disposition: attachment; filename=$file");
echo file_get_contents('attachment.zip');

?>
