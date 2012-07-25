<body onload="javascript:top.window.close()">

<?
/**
 *  middle.php
 */

$TotalScore =$_GET['TotalScore'];
echo $TotalScore.'<br>';
$IP =$_GET['IP'];
//echo $IP.'<br>';
echo $_COOKIE["uidname"].'<br>';
/*echo $_SESSION['name'].'<br>';
 echo $name.'<br>';*/
include '../../includes/lnSession.php';
include '../../includes/lnAPI.php';

global $config;
$config = array();
include "../../config.php";


findeid();

function findeid(){


	global $HTTP_SERVER_VARS;
	global $config;

	// Connect to database
	if ($config['encoded']) {
		$config['dbuname'] = base64_decode($config['dbuname']);
		$config['dbpass'] = base64_decode($config['dbpass']);
		$config['encoded'] = 0;
	}
	$dbtype = $config['dbtype'];
	$dbhost = $config['dbhost'];
	$dbname = $config['dbname'];
	$dbuname = $config['dbuname'];
	$dbpass = $config['dbpass'];
	$prefix = $config['prefix'];
	global $dbconn;

	@$db =  mysql_pconnect($dbhost,$dbuname,$dbpass);
	mysql_select_db($dbname);
	if(!$db){
		echo "เกิดข้อผิดพลาด";
		exit;
	}

	$userIP = getenv("REMOTE_ADDR");

	/*$event_usercolumnuid= $prefix.'_'."uid";
	 $event_usercolumnipaddr  = $prefix.'_'."ipaddr";
	 $event_usercolumnippro  = $prefix.'_'."ippro";
	 $event_usertable = $prefix.'_'."event_user";

	 //$ip =$_GET['bar'];

	 $TotalScore =$_GET['TotalScore'];
	 $IP =$_GET['IP'];

	 $query1 = "SELECT *
	 FROM $event_usertable
	 WHERE $event_usercolumnipaddr = '$IP'  AND  $event_usercolumnippro = '$userIP'";

	 $result1 = mysql_query($query1);

	 while($rets = mysql_fetch_row($result1)) {
	 $uidnow= $rets[0];
	 }	*/
	$uidnow = $_COOKIE["uidname"];
	//echo $uidnow.'<br>';
	/*	$file =  fopen('uidnow.txt',"w");
	 fwrite($file,$uid);
	 fwrite($file,',');
	 fwrite($file,$query1);
	 fwrite($file,',');
	 fclose($file);*/

	$userscolumnuid= $prefix.'_'."uid";
	$userstable = $prefix.'_'."users";

	$query1 = "SELECT *
              FROM $userstable
              WHERE $userscolumnuid= '" . $uidnow . "'";

	$result1 = mysql_query($query1);

	while($rets = mysql_fetch_row($result1)) {
		$uname= $rets[2];
	}
	echo $uname.'<br>';
	//echo "uid = ".$uidnow."";
	//echo "<br>uname = ".$uname."";

	$filename = $uname.'_'.$uidnow.'.txt';
	if (!($file = fopen($filename,"r")))
	exit;
	while (!feof($file)) {
		$buffer = fgets($file,50);
		$token = explode(",",$buffer);
		$eidnows = $token[0];
		$lidnows= $token[1];
		$cidnows= $token[2];
		$uidnows= $token[3];
	}
	fclose($file);
		


	//echo  "<br>Scorce";
	//echo  $_GET['oTotalScore'];
	//echo  "<br>eid = ".$eidnows."   lid = ".$lidnows."   cid = ".$cidnows."   uid = ".$uidnow."<br>";

	global $config;
	// Connect to database

	if ($config['encoded']) {
		$config['dbuname'] = base64_decode($config['dbuname']);
		$config['dbpass'] = base64_decode($config['dbpass']);
		$config['encoded'] = 0;
	}

	$dbtype = $config['dbtype'];
	$dbhost = $config['dbhost'];
	$dbname = $config['dbname'];
	$dbuname = $config['dbuname'];
	$dbpass = $config['dbpass'];
	$prefix = $config['prefix'];
	global $dbconn;

	@$db =  mysql_pconnect($dbhost,$dbuname,$dbpass);
	mysql_select_db($dbname);
	if(!$db){
		echo "เกิดข้อผิดพลาด";
		exit;
	}
	$table = $prefix.'_'."scores";
	/*$TotalScore =$_GET['TotalScore'];
	 $IP =$_GET['IP'];*/
	$query2 = "INSERT  INTO " .$table. "  VALUES ('".$eidnows."', '".$lidnows."', '".$_GET['TotalScore']."', null ,1)";
	if ($eidnows == ""){
		echo("<center>คุณยังไม่ได้ลงทะเบียนเรียนค่ะ</center>");
		exit;
	}else{
		$result2 = mysql_query($query2);
	}
	echo $query2 .'<br>';
	echo "<center><h4>การบันทึกคะแนนของคุณเรียบร้อยแล้วค่ะ";

	@$db =  mysql_pconnect($dbhost,$dbuname,$dbpass);
	mysql_select_db($dbname);
	if(!$db){
		echo "เกิดข้อผิดพลาด";
		exit;
	}
	$tablelesson = $prefix.'_'."lessons";
	$lessonscolumnlid  = $prefix.'_'."lid";

	$querylesson = "SELECT  *
   FROM $tablelesson
	   WHERE $lessonscolumnlid = '$lidnows'";

	$result4 = mysql_query($querylesson);

	while($rets = mysql_fetch_row($result4)) {
		$chapterlesson = $rets[6];
		$namefile = $rets[4];
	}

	@$db =  mysql_pconnect($dbhost,$dbuname,$dbpass);
	mysql_select_db($dbname);
	if(!$db){
		echo "เกิดข้อผิดพลาด";
		exit;
	}
	$tableuser_loguid = $prefix.'_'."uid";
	$tableuser_logcid  = $prefix.'_'."cid";
	$tableuser_logatime  = $prefix.'_'."atime";
	$tableuser_logevent  = $prefix.'_'."event";
	$tableuser_logip = $prefix.'_'."ip";
	$tableuser_log = $prefix.'_'."user_log";
	$time = time();
	$userIP = getenv("REMOTE_ADDR");

	$event = "Quizs     Course:  ".$cidnows."      Chapter:       ". $chapterlesson ."      name  file :             ".$namefile ."             score:     ".$_GET['TotalScore']." % ";


	$queryuser_log = "INSERT INTO $tableuser_log
						($tableuser_loguid, $tableuser_logcid, $tableuser_logatime, $tableuser_logevent, $tableuser_logip)
						VALUES
						( '".$uidnow."',  '".$cidnows."' ,'".$time."','".$event."', '".lnVarPrepForStore($userIP)."')";
	echo $queryuser_log .'<br>';
	$result = mysql_query($queryuser_log);


}

?>