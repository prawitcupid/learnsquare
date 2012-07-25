<?php
header("Cache-Control: no-cache");
/**
 * Show message from file: about us, helpdesk
 */
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Message::', "$op::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

$vars = array_merge($_POST,$_GET);
extract($vars);
list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();
$counterstable = $lntable['counters'];
$counterscolumn = &$lntable['counters_column'];

$virtual_ip = $_SERVER['SERVER_ADDR']; //client ip, dhcp, fixed ip
$ip = $_SERVER['REMOTE_ADDR']; //real ip from rounter, adsl, server
if (empty($ip)) {
	$ip = getenv('REMOTE_ADDR');
}
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
	$ip = $_SERVER['HTTP_CLIENT_IP'];
}
$tmpipaddr = getenv('HTTP_CLIENT_IP');
if (!empty($tmpipaddr)) {
	$ip = $tmpipaddr;
}
if  (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
	$ip = preg_replace('/,.*/', '', $_SERVER['HTTP_X_FORWARDED_FOR']);
}
$tmpipaddr = getenv('HTTP_X_FORWARDED_FOR');
if  (!empty($tmpipaddr)) {
	$ip = preg_replace('/,.*/', '', $tmpipaddr);
}

$query = "INSERT INTO  $counterstable (
	$counterscolumn[file],
	$counterscolumn[server_addr],
	$counterscolumn[remote_addr]
	)
	VALUES ('$file_download', '$virtual_ip', '$ip');
	";
$result = $dbconn->Execute($query);
list($id) = $result->fields[0];

//echo $query;
header("Location: ".$file_download);

?>