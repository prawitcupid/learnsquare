<?php
function countLogTime() {

//connect userlog
list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();
$user_logtable = $lntable['user_log'];
$user_logcolumn = &$lntable['user_log_column'];

//connect modules_vars
$module_varstable = $lntable['module_vars'];
$module_varscolumn = &$lntable['module_vars_column'];

//find userlog
$result = $dbconn->Execute("SELECT FROM_UNIXTIME(MAX($user_logcolumn[atime])) FROM $user_logtable ");
list($atime) = $result->fields;
	
//echo $atime;

//find module_var

$logtime=date ("Y-m-d H:i:s", lnConfigGetVar('logtime')); 

	if($atime == null){
	   $atime = $logtime; 
	}

$time =  Date_Calc::dateDiv($logtime,$atime); 
//print_r($time);
$dtime = $time['D'];

	
	$logdate =lnConfigGetVar('logdate');
	
	$totaltime =  $logdate - $dtime; 
	//echo $totaltime;

	if($totaltime<=0){
		$result = $dbconn->Execute("DELETE FROM $user_logtable WHERE $user_logcolumn[atime]");
		$activetime = time();
	
		$lenlogtime = strlen($activetime );
	
		lnConfigSetVar('logtime',$activetime);
		}
		
}
countLogTime();

?>