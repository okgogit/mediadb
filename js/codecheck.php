<?php
ChromePhp::log('in codecheck.php');
$code = strtoupper($_GET['code']);

mysql_connect('db.okgo.net','okgo_wp','0kg00kg0');
mysql_select_db('okgo_wp');
$q = mysql_query("SELECT code FROM codes_valid WHERE code = '".mysql_real_escape_string($code)."'");

if (mysql_num_rows($q)==1) {
	$return = array('code'=>$code,
	'status'=>'valid');
	echo json_encode($return);
	exit;
}

$data = simplexml_load_file('http://merchdirect.com/x/xml/promoCodes.php?key=03dae3a2de4e92ead443d7cf413ca2cc');

$search = $data->xpath("//code[.='".$code."']");
if ($search[0]==$code) {
	$return = array('code'=>$code,
	'status'=>'valid');
	echo json_encode($return);
} else {
	$return = array('code'=>$code,
	'status'=>'invalid');
	echo json_encode($return);
}
?>
