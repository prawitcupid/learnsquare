<?php
/*
Module : Statistic
Create on : 30/05/51
By : Narasak
*/


if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}


/* - - - - MAIN- - - - - */
//$vars= array_merge($GLOBALS['HTTP_GET_VARS'],$GLOBALS['HTTP_POST_VARS']);	
$vars = array_merge($_POST,$_GET);
include 'header.php';

/** Navigator **/
$menus = $links = array();
if (lnUserAdmin( lnSessionGetVar('uid'))) {
	$menus[] = _ADMINMENU;
	$links[]='index.php?mod=Admin';
}

$menus[]= _STATSMENU;
$links[]= 'index.php?mod=Statistic&amp;file=admin';
/** Navigator **/


if (!empty($op)) {
	// include more functions
	switch($op) {
		//case "view_stat": viewStat($id); break;
		case "save_stat": saveStats($vars); return;
	}
}

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

/** Navigator **/
lnBlockNav($menus,$links);
/** Navigator **/


OpenTable();

$message = "";

$message = "<table width= '100%' cellpadding=2 cellspacing=1 border=0><tr bgcolor='#D2E9FF'><td width='8%'><center><b><font size='2'>"._VIEWSTAT."</font></b></center></td></tr></table><BR>";

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$userstable = $lntable['users'];
$userscolumn = $lntable['users_column'];

$coursestable = $lntable['courses'];
$coursescolumn = $lntable['courses_column'];

$course_submissionstable = $lntable['course_submissions'];
$course_submissionscolumn = $lntable['course_submissions_column'];


$query = "select count($userscolumn[uid]) from $userstable";
//echo $query;
$result = $dbconn->Execute($query);

$users = $result->fields[0];

$message .= "<B>"._MSG1.$users." "._MSG2."</B><BR>";

$query = "select count($coursescolumn[cid]) from $coursestable;";
//echo $query;
$result = $dbconn->Execute($query);

$courses = $result->fields[0];

$message .= "<B>"._MSG3.$courses." "._MSG4."</B><BR>";

$query = "SELECT count($course_submissionscolumn[cid]) FROM $course_submissionstable WHERE $course_submissionscolumn[active] = 1";
//echo $query;
$result = $dbconn->Execute($query);

$course_submissions = $result->fields[0];

$message .= "<B>"._MSG5." ".$course_submissions." "._MSG6."</B><BR>";

$message .= "<p>"._MSG7."</p>
<table width= '100%' cellpadding=2 cellspacing=1 border=0>
<tr bgcolor='#D2E9FF'>
<td width='35%'><div align='center'><b>"._MSG8."</b></div></td>
<td width='35%'><div align='center'><b>"._MSG9."</b></div></td>
<td width='30%'><div align='center'><b>"._MSG10."</b></div></td>
</tr>
";

$query = "SELECT $course_submissionscolumn[cid], $course_submissionscolumn[start], $course_submissionscolumn[amountstd] FROM $course_submissionstable WHERE $course_submissionscolumn[active]=1 AND $course_submissionscolumn[enroll] = '1' ORDER BY  $course_submissionscolumn[start]";

$result = $dbconn->Execute($query);

while(list($cid,$start,$amountstd) = $result->fields) {
	$row="";
	$result->MoveNext();
	$now=date('Y-m-d');
	$courseinfo = lnCourseGetVars($cid);
	$course_length = lnCourseLength($cid) - 1;
	$from = Date_Calc::dateFormat2($start, "%e %b");
	$to = Date_Calc::daysAddtoDate2($start, $course_length, "%e %b %y");
	$start = $from . ' - ' . $to.'<BR>';
	$row = '<tr bgcolor="#cfed8a"><td>'.stripslashes($courseinfo[title]);
	$date1=date('Y-m-d');
	$date2=date('Y-m-d',$courseinfo['createon']);
	$row .= "</td>";
	$row .= '<td align="center">'.$start.'</td>';
	$row .= '<td align="center">'.$amountstd.'</td></tr>
';

	$message .= $row;
}
		
$message .= "</table>";

$message .= "<br><b>"._MSG11." : ".date('Y-m-d')."</b><br>";

echo $message;

echo "<BR><center><a href='modules/Statistic/statistic.zip'><b>"._MSG12."</b></a></center>";
writeStat($message);

zipStat();

CloseTable();

include 'footer.php';

/* - - - - END MAIN- - - - - */

function writeStat($message) {
	//write into html
	$filename = 'modules/Statistic/statistic.html';
	$somecontent = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Statistic</title>
</head>
<body>';

	$somecontent .= $message;
	$somecontent .= '
</body></html>';

	// Let's make sure the file exists and is writable first.
	if (is_writable($filename)) {

		// In our example we're opening $filename in append mode.
		// The file pointer is at the bottom of the file hence
		// that's where $somecontent will go when we fwrite() it.
		if (!$handle = fopen($filename, 'w+')) {
			echo "Cannot open file ($filename)";
			exit;
		}
	
		// Write $somecontent to our opened file.
		if (fwrite($handle, $somecontent) === FALSE) {
			echo "Cannot write to file ($filename)";
			exit;
		}

		//echo "Success, wrote ($somecontent) to file ($filename)";

		fclose($handle);
	
		//echo "<br><a href='".$filename."'>View File</a>";

	} else {
		echo "The file $filename is not writable";
	}

}

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function zipStat() {
	//zip file statistic.html
	$zip = new ZipArchive;
	if ($zip->open('modules/Statistic/statistic.zip') === TRUE) {
		$zip->addFile('modules/Statistic/statistic.html', 'statistic.html');
		$zip->close();
		//echo 'ok';
	} else {
		echo 'failed';
	}
}

?>
