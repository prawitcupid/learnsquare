
<?
//echo '<body onload="javascript:top.window.close()">  ';
$time = time();
//echo   'timeout = '.$time.'<br>';
$stime = date('H:i:s', $time);
$uid =$_GET['uid'];
		$token = explode(",",$uid);
		$uids = $token[0];
		$lids= $token[1];
		$eids= $token[2];
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
$time = time();
echo   'timeout = '.$time.'<br>';
$stime = date('H:i:s', $time);
$uid =$_GET['uid'];
		$token = explode(",",$uid);
		$uids = $token[0];
		$lids= $token[1];
		$eids= $token[2];
	$userscolumnuid= $prefix.'_'."uid";
	$userstable = $prefix.'_'."users";

    $query1 = "SELECT *
              FROM $userstable
              WHERE $userscolumnuid= '" . $uids . "'";

    $result1 = mysql_query($query1);

while($rets = mysql_fetch_row($result1)) {
  $uname= $rets[2];
	}	
echo 'name'.$uname.'<br>';

$time = time();
echo   'timeout = '.$time.'<br>';
$stime = date('H:i:s', $time);
$uid =$_GET['uid'];
		$token = explode(",",$uid);
		$uids = $token[0];
		$lids= $token[1];
		$eids= $token[2];
							

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

	$course_trackingcolumneid= $prefix.'_'."eid";
	$course_trackingcolumnlid= $prefix.'_'."lid";
	$course_trackingcolumnatime= $prefix.'_'."atime";
		$course_trackingcolumoutime= $prefix.'_'."outime";
	$course_trackingtable = $prefix.'_'."course_tracking";

$query2 = "SELECT  MAX($course_trackingcolumnatime)
		FROM $course_trackingtable
			WHERE $course_trackingcolumneid= '" . $eids . "'   AND $course_trackingcolumnlid = '" . $lids . "' ";

   $result1 = mysql_query($query2);
while($rets = mysql_fetch_row($result1)) {
  $atime= $rets[0];
	}	
	echo   'findatime = '.$query2.'<br>';	
	echo   'atime = '.$atime.'<br>';

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
	
$uids = $token[0];
		$lids= $token[1];
		$eids= $token[2];


	$course_trackingcolumneid= $prefix.'_'."eid";
	$course_trackingcolumnlid= $prefix.'_'."lid";
	$course_trackingcolumnatime= $prefix.'_'."atime";
		$course_trackingcolumoutime= $prefix.'_'."outime";
	$course_trackingtable = $prefix.'_'."course_tracking";


$time = time();
$stime = date('H:i:s', $time);
$uid =$_GET['uid'];

		$query3 = "UPDATE  $course_trackingtable  SET  $course_trackingcolumoutime =  $time  
		WHERE $course_trackingcolumneid= '" . $eids . "'   AND $course_trackingcolumnlid = '" . $lids . "'   AND $course_trackingcolumnatime =  '" . $atime . "'  ";
	     $result1 = mysql_query($query3);

echo   $query3.'<br>';
       $filename = $uname.'_'.$uids.'.txt';
 //        unlink($filename);
echo     $filename ;
	}

?>