<?php
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache"); 
// Get (actual) client IP addr
$ip = $_SERVER['REMOTE_ADDR'];
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

$timeOnPage = $_POST['time'];
$url = $_SERVER['HTTP_REFERER'];
$ref = $_POST['referrer'];

// [Security]
if(empty($_SERVER['HTTP_REFERER'])) exit;
if(empty($_POST) || count($_POST) == 0 ) exit;
// [/Security]


// [FOR LearSquare 1.0-4.0 ONLY]
include ("includes/lnAPI.php");
date_default_timezone_set ("Asia/Bangkok");
lnInit();
$uid = is_numeric(lnSessionGetVar('uid')) ? lnSessionGetVar('uid') : 0;
//-[FOR LearSquare 1.0-4.0 ONLY]

/*
// [Save to Log file]
$data = sprintf("%s UID:%s onPage  %s by %s duration : %d\n",$ip,$uid,$url,$ref,$timeOnPage);
file_put_contents("logs.txt", $data, FILE_APPEND);
// [/Save to Log File]
*/

// [Conect to DB mySQL]
include 'config.php';
$user = base64_decode($config['dbuname']);
$pass = base64_decode($config['dbpass']);
$dbname = $config['dbname'];
$host = $config['dbhost'];
try {
	$db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'"));
} catch (PDOException $e) {
	die ($e->getMessage());
}
// [/Conect to DB mySQL]

$stm = $db->prepare("INSERT INTO ln_xestat (ip,uid,url,refUrl,dura) VALUES(?,?,?,?,?);");
$stm->execute(array(ip2long($ip),$uid,addslashes($url),addslashes($ref),$timeOnPage));
?>