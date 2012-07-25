<?php
/**
 *  Course Module

 last edit :-----
 programmer : orrawin
 date : 23-06-49

 last edit :23-06-49
 programmer : Neetiwit B.
 date : 04-08-2549
 Description :
 1. แก้ไขให้สามารถแสดงบทเรียนที่ผ่านมาตรฐาน SCORM 1.2 โดยเพิ่ม
 1.1 Class Scorm_function
 1.2 Class Scos
 1.3 Class SCORM
 1.4  CMI Initialization SCORM 1.2 (Data Model)
 2. เวลา import สามารถอ่าน UTF-8 หรือ TIS-620 ได้

 JoeJae Chat Plug-ins: ziiz_lover
 */

class_exists('Scorm_function') || require('modules/SCORM/DataModel1_2.php');

if(!isset($sco_user))
{
	global $scorm;
	global $sco_user ;
	global $mode ;
	global $navObj;
	global $sco;
	global $last;
}
$sco_user = new Scorm_function();
$mode  = 'normal';
$sco = new Scos;
$scorm = new SCORM();

// todo - -  define
define('MAX_FILESIZE','50000000'); //max. filesize in bytes for uploading images 50 M

if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}
//$ipadd = lnSessionGetVar('ip');
//Session_Start();
$name =  lnSessionGetVar('uid');
//	session_register('name');
setcookie("uidname", $name, time()+360000000);                  /////////////////////////////////////////////////////////Set session uid
//	session_register('name');
/* - - - - - - - - - - - */
$vars= array_merge($_GET,$_POST);

include_once "config.php";
include_once 'modules/Courses/quizPlay.php'; 
//var_dump(is_file('modules/Courses/quizPlay.php')) . "\n";
// Include function for showQuiz


if ($op == 'lesson_show') {
	$file2 ="courses" . '/' . $cid . '/' . "save.htm";
	@unlink($file2);

	if(lnSessionGetVar('lidnow') == $lid){

	}else{
		//echo lnSessionGetVar('lidnow') ."==". $lid;
		$usercidnow = lnSessionGetVar('cidnow');
		$usereidnow = lnSessionGetVar('eidnow');
		$userlidnow = lnSessionGetVar('lidnow');
		//outtime($userlidnow,$usercidnow,$usereidnow);
		//outtime($lid,$cid,$eid);
		lnSessionSetVar('lidnow',$lid);
	}

	$unamenow =  lnSessionGetVar('uname');
	$uidnow =  lnSessionGetVar('uid');
	$filenow = $unamenow.'_'.$uidnow.'.txt';
	$usercidnow = lnSessionGetVar('cidnow');
	$usereidnow = lnSessionGetVar('eidnow');
	$userlidnow = lnSessionGetVar('lidnow');



	$lessondir =  'modules' . '/' . 'Courses' . '/' . $filenow;
	$file =  fopen($lessondir,"w");
	fwrite($file,$usereidnow);
	fwrite($file,",");
	fwrite($file,$userlidnow);
	fwrite($file,",");
	fwrite($file,$usercidnow);
	fwrite($file,",");
	fwrite($file,$uidnow);
	fclose($file);
	$GLOBALS['expand']=1;
}

if ($op == 'showcontent') {
	lessonShow($vars);
	exit;
}

if ($op == 'check_score') {
	checkScore($vars);
	exit;
}

if ($op == 'check_score2') {
	//echo 'check_score2';exit();
	checkScore2($vars);
	exit;
}
//submit score By narananami
if ($op == 'ln_check_scores') {

	include 'header.php';
	OpenTable();

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
	tabMenu($vars,2);
	echo '</TD></TR><TR><TD>';
	echo '<BR><BR>';

	$score = lnCheckScores($eid,$lid);

	$get_quiz = lnQuizGetVars($qid);
	$get_assessment = $get_quiz['assessment'];
	$total = $get_quiz['grade'];

	//echo 'score = '.$score.'/ total = '.$total;
	checkPassed($cid,$lid,$qid,$score,$total);
	
	$feedback = $get_quiz['feedback'];
	//$correctanswers = $get_quiz['correctanswers'];
	//echo "feedback=".$feedback;
	if($feedback){
		include 'modules/Quiz/feedbackTest.php';
		feedback($vars);
	}

	echo '</TD></TR></TABLE>';

	CloseTable();
	include 'footer.php';
	//exit();
	exit;

	/*
	 include 'header.php';
	 OpenTable();

	 $total = getTotalScore($qid);
	 $score = lnCheckScores($eid,$lid);
	 checkPassed($cid,$lid,$qid,$score,$total);

	 CloseTable();
	 include 'footer.php';
	 //echo '<meta http-equiv="refresh" content="1;URL=index.php?mod=Courses&op=course_lesson&cid='.$cid.'&uid='.$uid.'&eid='.$eid.'" />';
	 exit;
	 */
}

if($op != 'joe_jae_send' && $op != 'joe_jae_load' && $op != 'joe_online')
include 'header.php';
/* options */
switch ($op) {
	case "course_detail" :						courseOverview($vars);break;
	case "course_lesson" :					courseOutline($vars);break;
	case "course_enroll" :						courseEnroll($vars);break;
	case "course_enroll_save":			courseEnrollSave($vars); break;
	case "lesson_show":						lessonShowFrame($vars); break;
	case "roster":									rosterShow($vars); break;
	case "report":									reportShow($vars); break;
	case "report_detail":						reportDetailShow($vars); break;
	case "report_user":							reportUserLogging($vars); break;
	case "report_graph":						report_graphData($vars); break;
	case "report_overview":				report_overview($vars); break;
	case "report_summary":						reportSummaryShow($vars); break;
	case "questionaire_summary":						questionaireSummaryShow($vars); break;
	case "print_report":						printReport($vars); break;
	case "assessment_assign":						assessmentAssignment($vars); break;

	case "joe_jae":						JoeJae($vars); break;
	case "joe_jae_send":						JoeJaeSend($vars); break;
	case "joe_jae_load":			JoeJaeLoad($vars); break;
	case "joe_online":				JoeOnline($vars); break;

	default :												courseList($vars);
}

if($op != 'joe_jae_send' && $op != 'joe_jae_load' && $op != 'joe_online')
include 'footer.php';
/* - - - - - - - - - - - */
/*
 * Show Course outline and Open Lesson
 */

function courseOutline($vars) {

	global $start;

	$usercidnow = lnSessionGetVar('cidnow');
	$usereidnow = lnSessionGetVar('eidnow');
	$userlidnow = lnSessionGetVar('lidnow');
	//outtime($userlidnow,$usercidnow,$usereidnow);
	// Get arguments from argument array
	extract($vars);

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
	tabMenu($vars,2);
	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width= "100%" cellpadding="0" cellspacing="0"  border="0">';
	echo '<tr><td valign="top"><BR>';
	echo '<table cellpadding="0" cellspacing="0"  border="0"><tr>';
	echo '<td><a href="javascript:menu2.open()"><img src="images/treeview/open.gif" border="0" />&nbsp;'._OPENTREE.'</a></td><td>&nbsp;|&nbsp;</td><td><a href="javascript:menu2.close()"><img src="images/treeview/closed.gif" border=0/>&nbsp;'._CLOSETREE.'</a></td>';
	echo '</tr></table>';

	echo '<script language="JavaScript">'. "\n";
	echo 'var menu2 = new TREEMENU(false); '. "\n";
	echo '</script>'. "\n";
	$level = 1;
	//echo "EID : ".$eid."<BR>";
	//echo "SID : ".$sid."<BR>";
	if(isset($eid))
	{
		$sid = lnGetSID($eid);
	}
	//echo "SID : ".$sid."<BR>";
	if (empty($sid)) {
		$sid = lnGetSubmissionID($cid);
	}
	$enroll_info = lnEnrollGetVars(lnGetEnroll($cid));
	$start = $enroll_info['start'];
	if ($start == '') {
		$submission_info = lnSubmissionGetVars($sid);
		$start = $submission_info['start'];
	}

	listLessonOutline($cid,$sid,0,$orderings=array(),$level);
	echo '<script language="JavaScript">'. "\n";
	echo 'menu2.floating = false;';                    // we don't want a floating menu
	echo 'menu2.bgColor = "";';                        // we don't want a menu background
	echo 'menu2.title = "";';	 // we want a title
	echo 'menu2.titleBGColor = "";';                   // we don't want a title background
	echo 'menu2.itemBGColor = "";';                    // we don't want an item background
	echo 'menu2.itemBGColor1 = "";';                  // we don't want a level-one-item background
	echo 'menu2.itemBold = true;';                     // we want menu items with bold text
	echo 'menu2.create();';                            // we create the menu
	if(isset($_POST['cnode']) )
	{
		echo 'menu2.jumpTo('.$_POST['cnode'].');';
	}
	else if(isset($_GET['cnode']) )
	{
		echo 'menu2.jumpTo('.$_GET['cnode'].');';
	}

	echo '</script>';

	echo '</td></tr></table>';
	echo '</TD></TR></TABLE>';
}
/**
 *out time
 */
function outtime($lid,$cid,$eid){
	//$lidthis = 0;
	$lidthis = $lid;
	$timethis = time();

	/*	$lessondir = "modules" . '/' . "Courses" . '/' . "time.txt";
	 $file =  fopen($lessondir ,"w");
	 $stime = date('H:i', $timethis);
	 fwrite($file,$lid);
	 fwrite($file,',');
	 fwrite($file,$timethis);
	 fwrite($file,',');
	 fwrite($file,$stime);
	 fclose($file);*/

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];


	$query = "SELECT  MAX($course_trackingcolumn[atime])
	FROM $course_trackingtable
	WHERE $course_trackingcolumn[eid]='$eid' AND $course_trackingcolumn[lid]='$lid'";
	$result = $dbconn->Execute($query);
	//echo $query;
	while(list($atime) = $result->fields) {
		$result->MoveNext();
		$atimes =$atime;
	}
	$query = "SELECT  MAX($course_trackingcolumn[outime])
	FROM $course_trackingtable
	WHERE $course_trackingcolumn[eid]='$eid' AND $course_trackingcolumn[lid]='$lid' AND $course_trackingcolumn[atime] = '$atimes'";
	$result = $dbconn->Execute($query);
	//echo $query;
	while(list($outime) = $result->fields) {
		$result->MoveNext();
		$outimes =$outime;
	}
	if($outimes ==""){
		$query1 = "UPDATE $course_trackingtable  SET  $course_trackingcolumn[outime]  = '$timethis'
		WHERE $course_trackingcolumn[eid]='$eid' AND $course_trackingcolumn[lid]='$lid' AND $course_trackingcolumn[atime] = '$atimes'";
		$result = $dbconn->Execute($query1);
		//echo $query;
	}
	/*$lessondir = "modules" . '/' . "Courses" . '/' . "time.txt";
	 $file =  fopen($lessondir ,"w");
	 $stime = date('H:i', $timethis);
	 fwrite($file,$lid);
	 fwrite($file,',');
	 fwrite($file,$timethis);
	 fwrite($file,',');
	 fwrite($file,$stime);
	 fwrite($file,',');
	 fwrite($file,$query1);
	 fclose($file);*/
}
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function report_overview($vars){
	// Get arguments from argument array
	extract($vars);

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

	tabMenu($vars,6);
	$uidnow =  lnSessionGetVar('uid');
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];
	$query = "SELECT  $userscolumn[uname]
	FROM $userstable WHERE $userscolumn[uid] = $uid";
	$result = $dbconn->Execute($query);
	while(list($uname) = $result->fields) {
		$result->MoveNext();
		$unames =$uname;
	}
	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr><td valign="top">&nbsp;';
	echo '<br><B>&nbsp;&nbsp;&nbsp;&nbsp;<U><center>ตารางประวัติการเรียนของนักเรียนทั้งหมด</U><BR><BR><IMG SRC="images/global/bul.jpg" WIDTH="10" HEIGHT="10" BORDER="0" ALT="" align="absmiddle"> </B><U><B>'.$unames.'</B></U><BR><BR>';

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$uidnow =  lnSessionGetVar('uid');
	$query = "SELECT  $course_enrollscolumn[eid]
	FROM $course_enrollstable
	WHERE $course_enrollscolumn[uid] = $uid AND  $course_enrollscolumn[sid] = $sid";
	$result = $dbconn->Execute($query);

	while(list($eid) = $result->fields) {
		$result->MoveNext();
		$rets =$eid;
	}

	$query = "SELECT  $course_trackingcolumn[eid],
	$course_trackingcolumn[weight],
	$course_trackingcolumn[atime],
	$course_trackingcolumn[outime]
	FROM $course_trackingtable
	WHERE $course_trackingcolumn[eid] = $rets  ORDER BY $course_trackingcolumn[weight]";
	$result = $dbconn->Execute($query);
	//	echo '<tr bgcolor=#FFFFFF><td width=100 align=center>$name</td>';

	for ($i=0;list($eid,$weight,$atime,$outime) = $result->fields; $i++) {
		$result->MoveNext();
		$dateatime = date('H:i:s',$atime);
		$dateoutime = date('H:i:s',$outime);
		$time =  $outime -  $atime;
		$timelearn =  date('i:s',$time);

		$list .= "<tr bgcolor=#FFFFFF><td width=100 align=center>$weight</td><td width=100 align=center>$dateatime</td><td width=100 align=center>$dateoutime</td><td width=100 align=center> $timelearn  (h:m:s)</td></tr>";
		$num = 0;
		$weightnow = $weight;

	}
	if ($i > 0) {
		echo '<center><table cellpadding="1" cellspacing="1" border="0" bgcolor="#888888" align=center name = Learn>';
		echo "<tr bgcolor=#CCCCCC align=center  width=100 >";
		echo "	<td width=100 align=center >บทเรียน</td>";
		echo "	<td width=100 align=center>เวลาที่เข้าเรียน</td>";
		echo "	<td  width=100  align = center >เวลาที่ออกเรียน</td>";
		echo "	<td width=100  align = center >ระยะเวลาในการเรียน(h:m:s):</td></tr>";
		echo $list;
		echo '</table></center>';

	}
	else {
		echo '<BR><B><CENTER> - No record. - </CENTER></B>';
	}



	echo '<tr><td>';
	//echo "<INPUT TYPE=\"button\" VALUE=\"สรุปการเข้าเรียนแต่ละบทเรียน\" class=\"button_org\"  onClick=\"javascript:window.open('index.php?mod=Courses&op=report_lesson&cid=$cid&uid=$uid&name=$unames&sid=$sid','_self')\">";
	echo '</tr></td>';
	echo '</table>';
	echo '</TD></TR></TABLE>';



}
/*
 function totaltime($uid,$sid,$cid)
 {
 list($dbconn) = lnDBGetConn();
 $lntable = lnDBGetTables();

 $course_trackingtable = $lntable['course_tracking'];
 $course_trackingcolumn = &$lntable['course_tracking_column'];
 $course_enrollstable = $lntable['course_enrolls'];
 $course_enrollscolumn = &$lntable['course_enrolls_column'];

 $eid =  lnGetEnrollID($uid,$sid);
 //echo $eid;

 $query = "SELECT  min($course_trackingcolumn[atime])
 FROM $course_trackingtable  WHERE $course_trackingcolumn[eid]=$eid ";

 $query2 = "SELECT  max($course_trackingcolumn[atime])
 FROM $course_trackingtable  WHERE $course_trackingcolumn[eid]=$eid ";

 $result = $dbconn->Execute($query);
 list($atime) = $result->fields;

 $result2 = $dbconn->Execute($query2);
 list($outime) = $result2->fields;

 //$datediff = $outime-$atime;

 $datediff = calcDateDiff($outime,$atime);

 //$datediff = date('H:i:s', $datediff);
 //$datediff = Date_Calc::dateFormat2($datediff, "%e %b");
 //$datediff = Date_Calc::dateDiff3($outime,$atime);
 return $datediff;


 }
 */
/*Not use
 function timelearntotel($uid,$sid,$cid){
 list($dbconn) = lnDBGetConn();
 $lntable = lnDBGetTables();

 $userstable = $lntable['users'];
 $userscolumn = &$lntable['users_column'];

 $query = "SELECT  $userscolumn[uname]
 FROM $userstable WHERE $userscolumn[uid] = $uid";

 $result = $dbconn->Execute($query);

 while(list($uname) = $result->fields) {
 $result->MoveNext();
 $unames =$uname;
 }

 $query = "SELECT  $userscolumn[name]
 FROM $userstable WHERE $userscolumn[uid] = $uid";

 $result = $dbconn->Execute($query);

 while(list($name) = $result->fields) {
 $result->MoveNext();
 $names =$name;
 }

 $course_trackingtable = $lntable['course_tracking'];
 $course_trackingcolumn = &$lntable['course_tracking_column'];
 $course_enrollstable = $lntable['course_enrolls'];
 $course_enrollscolumn = &$lntable['course_enrolls_column'];

 $uidnow =  lnSessionGetVar('uid');

 $query = "SELECT  $course_enrollscolumn[eid]
 FROM $course_enrollstable
 WHERE $course_enrollscolumn[uid] = $uid AND  $course_enrollscolumn[sid] = $sid";

 $result = $dbconn->Execute($query);

 while(list($eid) = $result->fields) {
 $result->MoveNext();
 $rets =$eid;
 }

 //select count(*),ln_weight from ln_course_tracking where ln_eid = '1' group by ln_weight; // count($course_trackingcolumn[weight]),COUNT(distinct($course_trackingcolumn[weight]))

 $course_trackingtable = $lntable['course_tracking'];
 $course_trackingcolumn = &$lntable['course_tracking_column'];

 $query1 = "SELECT distinct($course_trackingcolumn[weight])
 FROM $course_trackingtable
 WHERE $course_trackingcolumn[eid] = '$rets'  ORDER BY $course_trackingcolumn[weight]";

 $result1 = mysql_query($query1);

 if (!$result1)
 die ("result error");

 $i =0;
 while($rowdistance = mysql_fetch_array($result1))
 {
 $a = mysql_num_rows($result1);
 //echo $a;
 if($i == $a){
 exit;
 }else{
 $set[$i]=$rowdistance[0];
 }
 $i++;
 }
 // echo $set[1];  count(*),

 $a = mysql_num_rows($result1);
 $b = 0;
 $y=0;
 while($a > $b){
 $query2 = "SELECT  count($course_trackingcolumn[weight])
 FROM $course_trackingtable
 WHERE $course_trackingcolumn[eid] = '$rets' AND  $course_trackingcolumn[weight]  = '$set[$b]' ORDER BY $course_trackingcolumn[weight]";
 $result2 = mysql_query($query2);
 if (!$result2)
 die ("result error");
 while($row  = mysql_fetch_array($result2))
 {
 $aa = mysql_num_rows($result2);
 //echo $a;
 if($y == $aa){
 exit;
 }else{
 $setrow[$b]=$row[0];
 }
 }

 $b++;
 }
 //$course_trackingcolumn[outime]
 $a = mysql_num_rows($result1);
 $b = 0;
 $timeover = 0;
 while($a > $b){
 $query3 = "SELECT  *
 FROM $course_trackingtable
 WHERE $course_trackingcolumn[eid] = '$rets' ORDER BY $course_trackingcolumn[weight]";
 $result3 = mysql_query($query3);
 // echo $query3;
 if (!$result3)
 die ("result error");
 $i =0;
 while($rowdistance = mysql_fetch_array($result3))
 {
 $a = mysql_num_rows($result3);
 //echo $a;
 if($i == $a)
 {
 exit;
 }else
 {
 $setattime[$i]=$rowdistance[3];
 $setoutime[$i]=$rowdistance[6];
 $dateatime[$i] = date('H:i:s',$setattime[$i]);
 if($setoutime[$i]  == ""){
 $q[$i]  =  0;
 $dateoutime[$i] = "null";
 }else{
 $dateoutime[$i] = date('H:i:s',$setoutime[$i]);
 $q[$i]  =  $setoutime[$i] -  $setattime[$i];
 $f [$i]= $setoutime[$i];
 $da[$i] = date('H:i:s',$setattime[$i]);
 $dat[$i] = date('H:i:s', $f [$i]);
 $timelearn[$i] =  calcDateDiff($setattime[$i],$setoutime[$i]);
 }
 }
 $i++;
 }
 $b++;
 }
 $aaa = mysql_num_rows($result3);
 $b = 0;$t = 0;$f =0;$o=0;
 $Hs=0;$Is=0;$ss=0;
 $k = 0; $q = 0; $r = 0;
 //echo  $timeover[$aaa];
 while($aaa > $b){

 //	$o   =  $o  + $q[$b];
 $times[$b] = explode(':',$timelearn[$b] );
 $b++;
 }
 while($aaa > $r){

 $Hs   =  $Hs  + 	$times[$r][0];
 $Is   =  $Is  + 	$times[$r][1];
 $Ss   =  $Ss  + 	$times[$r][2] ;
 $r++;
 }

 if($Ss > 60){
 $Iss = $Ss % 60;                ///////// วินาทีที่ได้
 $sss = $Ss / 60;
 $snow = (integer)$sss;           ///////+นาที

 if($snow > 0){
 $Is= $Is + $snow;
 }
 if($Iss <  10){
 $Ss= '0'.$Iss;
 }else{
 $Ss= $Iss;
 }
 }else{
 if($Ss <  10){
 $Ss= '0'.$Ss;
 }else{
 $Ss= $Ss;
 }
 $Is=$Is;
 }

 if($Is > 60){
 $HIs = $Is % 60;
 $IIs = $Is / 60;
 $Inow = (integer)$IIs;

 if($HIs <  10){
 $Is= '0'.$HIs;
 }else{
 $Is= $HIs;
 }
 if($Inow > 0){
 $Hs= $Hs + $Inow;
 }
 }else{
 if($Is <  10){
 $Is= '0'.$Is;
 }else{
 $Is= $Is;
 }
 $Hs=$Hs;
 }
 if($Hs < 10){
 $Hs = '0'.$Hs;
 }else{
 $Hs= $Hs;
 }

 $gtime =  $Hs.':'.$Is.':'.$Ss;
 return $gtime;




 }
 */


function report_graphData($vars)
{
	// Get arguments from argument array
	extract($vars);

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

	tabMenu($vars,6);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];

	$query = "SELECT  $userscolumn[uname],$userscolumn[name] FROM $userstable WHERE $userscolumn[uid] = $uid";

	$result = $dbconn->Execute($query);

	while(!$result->EOF)
	{
		$unames =$result->fields[0];
		$names =$result->fields[1];
		$result->MoveNext();
	}

	echo '</TD></TR><TR><TD>';
	echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr><td valign="top">&nbsp;';
	echo '<br><br><B>&nbsp;&nbsp;&nbsp;&nbsp;<U>'._STUDYHISTORYTABLE.'</U><BR><BR><IMG SRC="images/global/bul.jpg" WIDTH="10" HEIGHT="10" BORDER="0" ALT="" align="absmiddle"> </B><U><B>'.$unames.' </U>  [ '.   $names  .'] </B><BR><BR>';

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	$uidnow =  lnSessionGetVar('uid');

	$query = "SELECT  $course_enrollscolumn[eid] FROM $course_enrollstable WHERE $course_enrollscolumn[uid] = $uid AND  $course_enrollscolumn[sid] = $sid";

	$result = $dbconn->Execute($query);

	while(!$result->EOF)
	{
		$rets =$result->fields[0];
		$result->MoveNext();
	}
	$timeArr = calcTotalTimetolearn($uid,$sid,$cid);
	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];

	$query = "SELECT  * FROM $course_trackingtable WHERE $course_trackingcolumn[eid] = '$rets' ORDER BY $course_trackingcolumn[weight]";

	$result = $dbconn->Execute($query);

	$totalRecord = $result->RecordCount( );

	$gtime =  $timeArr['H'].'&nbsp;'._HOUR.'&nbsp;'.$timeArr['M'].'&nbsp;'._MINUTE.'&nbsp;'.$timeArr['S'].'&nbsp;'._SECOND.'&nbsp;';
	//$gtime = "ทดสอบเวลาทั้งหมดใน Report_Graph";

	//ใช้ระยะเวลาในการเรียนทั้งหมด   '. $time.'

	echo '<center><B>'._LISTSTUDY.' '.$names.'<br><br>'._TOTALSTUDENT.' '. $totalRecord  .'  '._TOTALSTUDY.'  '.$gtime.' </B><br><br><br></center>';
	echo '<center><table cellpadding="1" cellspacing="1" border="0" bgcolor="#888888"  align=center name = Learn>';

	echo "<tr bgcolor=#D0D0D0 align=center  width=100 >";
	echo "	<td width=100 align=center >"._LESSONAT."</td>";
	echo "	<td width=100 align=center>"._ARRIVETIME."</td>";
	echo "	<td  width=100  align = center >"._LEAVETIME."</td>";
	echo "	<td width=150  align = center >"._TOTALTIME."</td></tr>";

	/*bas edit pook version 28/04/49*/

	$query = "SELECT  $course_trackingcolumn[lid],count(*)   FROM $course_trackingtable WHERE $course_trackingcolumn[eid] = '$rets' and $course_trackingcolumn[outime] is not null group by $course_trackingcolumn[lid] ORDER BY $course_trackingcolumn[atime]";
	//echo "<hr>".$query."<hr>";
	$result = $dbconn->Execute($query);
	$totalRecord = $result->RecordCount( );

	while(!$result->EOF)
	{
		$rowspan = $result->fields[1];
		$lid = $result->fields[0];
		$titlelesson = lnLessonGetVars($lid);
		echo "<tr align=center  width=100 bgcolor=#FFFFFF>\n";
		echo "	<td width=100 align=left  rowspan='$rowspan'>".$titlelesson['title']."</td>\n";

		tabl($uid,$sid,$rets,$lid);
		/*
		 echo "<td align=center >เวลาเข้า</td>\n";
		 echo "	<td align=center>&nbsp;เวลาออก</td>\n";
		 echo "	<td align=center >&nbsp;เวลาเรียนทั้งหมด</td>\n</tr>";
		 */
		$result->MoveNext();
	}

	echo '</table>';
	echo "<tr><td align=center width=\"100%\"><INPUT TYPE=\"button\" VALUE=\""._STUDYDETIAL."\" class=\"button_org\"  onClick=\"javascript:window.open('index.php?mod=Courses&op=report_user&cid=$cid&uid=$uid&name=$name&sid=$sid','_self')\"></td></tr>";
	echo '</table>';
	echo '</TD></TR>';
	echo '</TABLE>';
}

function tabl($uid,$sid,$eid,$lid)
{

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];
	$sql = "SELECT FROM_UNIXTIME($course_trackingcolumn[atime]) as atime,FROM_UNIXTIME($course_trackingcolumn[outime]) as outtime,$course_trackingcolumn[atime] , $course_trackingcolumn[outime] FROM $course_trackingtable
	WHERE $course_trackingcolumn[eid] = '$eid' and $course_trackingcolumn[lid] = '$lid' and $course_trackingcolumn[outime] is not null ORDER BY $course_trackingcolumn[atime]";
	$rs = $dbconn->Execute($sql);
	$totaltimes = array();
	while ($arr = $rs->FetchRow())
	{         	if($arr[1] == '')
	$arr[1] = $arr[0];
	$time =  Date_Calc::dateDiv($arr[1],$arr[0]);
	$ftrackdate = getdate($arr[2]);

	if($arr[3] == '')
	$ttrackdate = getdate($arr[2]);
	else
	$ttrackdate = getdate($arr[3]);
	$fromdate = Date_Calc::dateFormat($ftrackdate[mday],$ftrackdate[mon],$ftrackdate[year],'%d-%m-%Y') . " " . str_pad($ftrackdate[hours],2,"0",STR_PAD_LEFT).":". str_pad($ftrackdate[minutes],2,"0",STR_PAD_LEFT).":". str_pad($ftrackdate[seconds],2,"0",STR_PAD_LEFT);
	$todate = Date_Calc::dateFormat($ttrackdate[mday],$ttrackdate[mon],$ttrackdate[year],'%d-%m-%Y') . " " . str_pad($ttrackdate[hours],2,"0",STR_PAD_LEFT).":". str_pad($ttrackdate[minutes],2,"0",STR_PAD_LEFT).":". str_pad($ttrackdate[seconds],2,"0",STR_PAD_LEFT);
	//8888888888888888888888888888888888888888888888888888888888888888888888888888
	echo  "<td align=center >".$fromdate."</td>\n";
	echo "	<td align=center>&nbsp;".$todate."</td>\n";
	echo "	<td align=center >&nbsp;".str_pad($time['D'], 2, "0", STR_PAD_LEFT).':'.str_pad($time['H'], 2, "0", STR_PAD_LEFT).':'.str_pad($time['M'], 2, "0", STR_PAD_LEFT).':'.str_pad($time['S'], 2, "0", STR_PAD_LEFT)."</td>\n</tr>\n";

	if(!$rs->EOF)
	echo "	<tr align=center  width=2  bgcolor=#FFFFFF>\n";
	}

}

function  calcDateDiff ($date1 = 0, $date2 = 0)
{
	if ($date1 > $date2)
	return FALSE;
	$seconds  = $date2 - $date1;

	// Calculate each piece using simple subtraction
	$weeks     = floor($seconds / 604800);
	$seconds -= $weeks * 604800;
	$days       = floor($seconds / 86400);
	$seconds -= $days * 86400;

	$hours      = floor($seconds / 3600);
	$seconds -= $hours * 3600;
	$minutes   = floor($seconds / 60);
	$seconds -= $minutes * 60;
	// Return an associative array of results
	if($hours < 10)
	{
		if($hours == 0)
		{
			$hours = '00';
		}else{
			$hours = '0'.$hours;
		}
	}else{
		$hours = $hours;
	}

	if($minutes < 10)
	{
		if($minutes == 0)
		{
			$minutes = '00';
		}else{
			$minutes= '0'.$minutes;
		}
	}
	if($seconds < 10)
	{
		if($seconds == 0)
		{
			$seconds = '00';
		}else{
			$seconds = '0'.$seconds;
		}
	}
	return  $hours.":".$minutes.":".$seconds;
}
/**
 * List all Courses
 */
function courseList($vars) {
	// Get arguments from argument array
	extract($vars);

	//echo lnBlockTitle($mod);
	echo '<p class="header"><b>'._BROWSECOURSE.'</b></p>';

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
	$schoolstable = $lntable['schools'];
	$schoolscolumn = &$lntable['schools_column'];
	if(!isset($browse)) $browse=0;
	if($browse==1)
	{
		$query = "SELECT $coursescolumn[cid],
		$coursescolumn[code],
		$coursescolumn[title],
		$coursescolumn[createon],
		$schoolscolumn[name]
		FROM $coursestable LEFT JOIN $schoolstable ON $coursescolumn[sid]=$schoolscolumn[sid]  WHERE $coursescolumn[active]='1'
		ORDER BY $coursescolumn[sid],$coursescolumn[code]";

	}
	else {
		$query = "SELECT $coursescolumn[cid],
		$coursescolumn[code],
		$coursescolumn[title],
		$coursescolumn[createon],
		$schoolscolumn[name],
		$coursescolumn[sid]
		FROM $coursestable RIGHT JOIN $schoolstable ON $coursescolumn[sid]=$schoolscolumn[sid] WHERE $coursescolumn[active]='1' 
		ORDER BY $coursescolumn[sid],$coursescolumn[code]";
	}

	$result = $dbconn->Execute($query);

	echo '<table width="100%" cellpadding="3" cellspacing="0" border="0">';
	$osname='';
	for ($i=1; list($cid,$code,$cname,$createon,$sname,$sid) = $result->fields; $i++) {
		$result->MoveNext();
		$sname = stripslashes($sname);
		$cname = stripslashes($cname);
		if(($osname != $sname)&&($sch==$sid)) {
			$osname=$sname;
			echo '<tr><td colspan=3  class=head align=left>'.$sname.'</td></tr>';
		}
		if($sch==$sid){
			echo "<tr valign=top valign=middle>";
			echo '<td><IMG SRC="images/global/line.gif" WIDTH="12" HEIGHT="11" BORDER=0 ALT=""> <A class=b HREF="index.php?mod=Courses&amp;op=course_detail&amp;cid='.$cid.'&sid='.lnGetSubmissionStudy($cid).'">'.$code.' : '.$cname.'</A>';
			lnShowNew($createon);
			echo "</td></tr>";
		}
	}
	echo '</table>';
}


/**
 *  Course overview
 */
function courseOverview($vars) {
	// Get arguments from argument array
	extract($vars);

	$courseinfo = lnCourseGetVars($cid);
	$coursecode= $courseinfo['code'];
	$coursename = stripslashes($courseinfo['title']);
	$order   = array("\r\n", "\n", "\r");
	$replace = '<br>';
	//$description = nl2br(stripslashes($courseinfo['description']));
	$description = str_replace($order, $replace,$courseinfo['description']);
	//$purpose = nl2br(stripslashes($courseinfo['purpose']));
	$purpose = str_replace($order, $replace,$courseinfo['purpose']);
	//$prerequisite = nl2br(stripslashes($courseinfo['prerequisite']));
	$prerequisite = str_replace($order, $replace,$courseinfo['prerequisite']);
	//$reference = nl2br(stripslashes($courseinfo['reference']));
	$reference = str_replace($order, $replace,$courseinfo['reference']);
	$author = $courseinfo['author'];		//////////////////////////////(ผู้สร้าง)/////////////////////////////////////////
	$course_length = lnCourseLength($cid);
	$author_info=lnUserGetVars($author);

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
	tabMenu($vars,1);
	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width="100%" align="center" cellpadding="5" cellspacing="0" border="0">';
	echo '<tr><td valign="top">';

	echo '<table  width="100%" align="center" cellpadding="3" cellspacing="0" border="0">';

	echo '<tr><td height="10"></td><td></td></tr>';
	echo '<tr><td width="15%" align=left valign=middle><B>'._COURSETITLE.'</B> : </td><td>'.$coursecode.':'.$coursename.'</td></tr>';
	echo '<tr><td align=left><B>'._COURSEAUTHOR.'</B> : </td><td><A HREF="index.php?mod=User&op=profile&uid='.$author.'">'.$author_info['uname'].'</A></td></tr>';
	echo '<tr><td align=left><B>'._COURSELENGTH.'</B> : </td><td>'.$course_length.' '._LENGTHUNIT.'</td></tr>';

	if (lnGetEnroll($cid) &&  lnEnrollType($cid) == _LNSCHEDULE_BASED) {
		$course_date = lnCourseDate($cid);
		echo '<tr><td align=left><B>'._COURSEDURATION.'</B> : </td><td>'.$course_date.'</td></tr>';
	}

	echo '<tr><td colspan=2><BR><B>'._COURSEDESCRIPTION.'</B> :<BR>'.$description.'</td></tr>';
	if (!empty($purpose))
	echo '<tr><td colspan=2><BR><B>'._COURSEPURPOSE.'</B> :<BR>'.$purpose.'</td></tr>';
	if (!empty($prerequisite))
	echo '<tr><td colspan=2><BR><B>'._COURSEPREREQUISITE.'</B> :<BR>'.$prerequisite.'</td></tr>';
	if (!empty($reference))
	echo '<tr><td colspan=2><BR><B>'._COURSEREFERENCE.'</B> :<BR>'.$reference.'</td></tr>';

	echo '<tr><td colspan=2><BR><B>'._AUTHORINFO.'</B>: <BR>';

	if (@$author_info['_AVATAR']) {

		echo '<IMG SRC="'.$author_info['_AVATAR'].'"  BORDER=0 ALT="" align=absmiddle>';
	}
	else {
		echo '<IMG SRC="images/global/user_student.gif"  BORDER=0 ALT="" align=absmiddle>';
	}
	echo '&nbsp;<B><A HREF="index.php?mod=User&op=profile&uid='.$author.'"">' . $author_info['uname'].'</A></B><BR>'.nl2br(stripslashes(@$author_info[_EXTRAINFO])).'</td></tr>';

	if ($sid = lnGetSubmissionID($cid) ) {
		$submission_info = lnSubmissionGetVars($sid);
		$instructor_info = lnUserGetVars($submission_info['instructor']);
		if($instructor_info['_AVATAR']==""){
			$instructor_info['_AVATAR']='images/avatar/blank.gif';
		}
		//Path images/avatar/ -- Narasak Tai 24/10/2007 --
		if ($author != $submission_info['instructor']) {
			echo '<tr><td colspan=2><BR><FONT COLOR="#800000"><B>'._INSTRUCTORINFO.'</B></FONT><BR><IMG SRC="'.$instructor_info['_AVATAR'].'"  BORDER=0 ALT="" align=absmiddle>&nbsp;<B><A HREF="index.php?mod=User&op=profile&uid='.$instructor_info['uid'].'"">' . $instructor_info['uname'].'</A></B><BR>'.nl2br(stripslashes($instructor_info[_EXTRAINFO])).'</td></tr>';
		}
	}

	echo '</table>';

	echo '</td></tr></table>';
	echo '</TD></TR></TABLE>';
}

/**
 * recursive list lesson
 */
function listLessonOutline($cid,$sid,$lid_parent,$orderings,$level) {
	global $start;
	// lnSessionDelVar('eidnow');
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	$query = "SELECT  $lessonscolumn[lid],
	$lessonscolumn[title],
	$lessonscolumn[description],
	$lessonscolumn[file],
	$lessonscolumn[duration],
	$lessonscolumn[weight],
	$lessonscolumn[lid_parent],
	$lessonscolumn[type]
	FROM $lessonstable
	WHERE $lessonscolumn[cid]='". lnVarPrepForStore($cid) ."'
	AND $lessonscolumn[lid_parent]='".$lid_parent."'
	ORDER BY $lessonscolumn[weight]";
	$result = $dbconn->Execute($query);
	$uid = lnSessionGetVar('uid');
	// show form
	$query1 = "SELECT $course_enrollscolumn[eid]
	FROM $course_enrollstable WHERE $course_enrollscolumn[uid] = $uid AND $course_enrollscolumn[sid] = $sid";

	$result1 = $dbconn->Execute($query1);
	@list($eid) = $result1->fields;
	$eidnow = $eid;
	if(lnSessionGetVar('eidnow')==$eid){
		lnSessionSetVar('eidnow',$eid);
		lnSessionSetVar('cidnow',$cid);
	}else{
		lnSessionDelVar('eidnow');
		lnSessionDelVar('cidnow');
		lnSessionSetVar('eidnow',$eid);
		lnSessionSetVar('cidnow',$cid);
	}
	for($prev_lid = 0,$i=0; list($lid,$lesson_title,$lesson_description,$lesson_file,$lesson_length,$no,$lid_parent,$type) = $result->fields; $i++)
	{
		$result->MoveNext();
		$prev_lid = $lid;
		if ($type == 1) {
			$quizinfo = lnQuizGetVars($lesson_file);
			$lesson_title = $quizinfo['name'];
			$lesson_title .= '?';
		}
		if ($type == 2) {
			$lesson_title .= '?';
		}
		array_push($orderings,$no);
		$show_item=join('.',$orderings);
		for($blank=0,$j=0;$j<count($orderings);$j++) $blank += 10;
		$uid = lnSessionGetVar('uid');
		//check lid
		// access lesson
		$color = ($lid_parent == 0) ?  '#115E94' : '#6584D6';	/// Check User is regised in course ///// By Xeonkung
		if ((Date_Calc::isPastDate2($start) && ($eid != null)) || isSpecialUsers($sid)  || lnAllTime($cid)) {
			$prev_lid = $lid;
			if ($type == 1) {  // quiz
				//find eid จาก uid,sid
				//$lesson_link = '<A  HREF="index.php?mod=Courses&op=lesson_show&uid='.$uid.'&cid='.$cid.'&eid='.$eidnow.'&sid='.$sid.'&lid='.$lid.'&qid='.$lesson_file.'"><FONT COLOR="'.$color.'"><U>';

				//find last $quiz_anscolumn[attempts] By narananami

				$quiz_questiontable = $lntable['quiz_multichoice'];
				$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];

				$quiz_anstable = $lntable['quiz_answer'];
				$quiz_anscolumn = &$lntable['quiz_answer_column'];

				$queryattempts = "SELECT  MIN($quiz_questioncolumn[mcid])
				FROM  $quiz_questiontable
				WHERE 	$quiz_questioncolumn[qid] =  '" . lnVarPrepForStore($lesson_file) . "'";
				$resultattempts = $dbconn->Execute($queryattempts);
				list($mcid) = $resultattempts->fields;

				/*
				 $queryattempts = "SELECT MAX($quiz_anscolumn[attempts])
				 FROM  $quiz_anstable
				 WHERE 	$quiz_anscolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'
				 AND	$quiz_anscolumn[eid] =  '" . lnVarPrepForStore($eidnow) . "'";
				 $resultattempts = $dbconn->Execute($queryattempts);
				 list($quiz_ansattempts) = $resultattempts->fields;
				 */
				//edit max quiz_ansattempts by narananami
				$quiz_ansattempts = countAttempt($eidnow,$lid);

				if($quiz_ansattempts==null) $quiz_ansattempts = 1; else $quiz_ansattempts++;
				//end find last $quiz_anscolumn[attempts]

				$lesson_link = 'index.php?mod=Courses&op=lesson_show&uid='.$uid.'&cid='.$cid.'&eid='.$eidnow.'&sid='.$sid.'&lid='.$lid.'&qid='.$lesson_file.'&quiz_ansattempts='.$quiz_ansattempts;
				$quiz_info =	lnQuizGetVars($lesson_file);

			}
			else if($type == 2){
				//$lesson_link = '<A HREF="index.php?mod=Courses&op=lesson_show&uid='.$uid.'&cid='.$cid.'&eid='.$eidnow.'&sid='.$sid.'&lid='.$lid.'"><FONT COLOR="'.$color.'"><U>';
				$lesson_link = 'index.php?mod=Courses&op=lesson_show&uid='.$uid.'&cid='.$cid.'&eid='.$eidnow.'&sid='.$sid.'&lid='.$lid;
			}else {
				//$lesson_link = '<A HREF="index.php?mod=Courses&op=lesson_show&uid='.$uid.'&cid='.$cid.'&eid='.$eidnow.'&sid='.$sid.'&lid='.$lid.'"><FONT COLOR="'.$color.'"><U>';
				$lesson_link = 'index.php?mod=Courses&op=lesson_show&uid='.$uid.'&cid='.$cid.'&eid='.$eidnow.'&sid='.$sid.'&lid='.$lid;
			}
		}
		else {
			$lesson_link='';
		}

		//$lesson_title = '<FONT COLOR="'.$color.'"><B>'.$show_item.'. '.$lesson_link.stripslashes($lesson_title).'</U></FONT></A></B></FONT>';
			
		//echo '<table width= 100% cellpadding=3 cellspacing=0 border=1  bgcolor=#FFFFFF>';

		// show lesson & test & assignment
		//if ($lid_parent==0) {
		//	echo '<tr height=8><td colspan=3></td></tr>';
		//}

		//echo '<tr valign="bottom">';
		//echo '<td width='.$blank.'></td>';
		//echo '<td>';

		//		if ($lid_parent == 0) {
		//			echo '<IMG SRC="images/global/arrow.gif" WIDTH="7" HEIGHT="8" BORDER=0 ALT=""> ';
		//		}

		//echo $lesson_title;
			
		//echo '&nbsp;';
		if($type == 1){
			//echo '<BR>'.scoreHistory($cid,$lid);
			//close scoreHistory by narananami
			$get_quiz = lnQuizGetVars($lesson_file);
			$get_assessment = $get_quiz['assessment'];
			$total = $get_quiz['grade'];
			$score = scoreHistory($eid,$lid);
			$percent = ($score/$total)*100;
			$percent = round($percent,2);
			$lesson_title .= '('._QUESTIONSCORE.' '.$score.' '._QUIZFROM.' '.$total.' '._QUESTIONSCORE.' ['.$percent.'%])';
		}elseif($type == 2){
			//echo '<BR>'.scoreHistoryHot($cid,$lid);
			$lesson_title .= scoreHistoryHot($cid,$lid);
			//echo "Hello";
		}elseif($type == 3){ //score of assignment
			//echo '<BR>'.scoreAssignment($cid,$lid);
			$lesson_title .= scoreAssignment($cid,$lid);
		}

			
		if (@$quiz_info['attempts'] > 0) {
			//echo  ' [<'.$quiz_info['attempts'].'] ';
			$lesson_title .= ' [<'.$quiz_info['attempts'].'] ';
		}
		//echo '</td>';
		if (!lnUserAdmin(lnUserGetVar('uid')) && lnGetEnroll($cid)) {
			$from = Date_Calc::dateFormat2($start, "%e %b");
			$days_stop = Date_Calc::dateToDays2($start) + $lesson_length -1;
			$stop = Date_Calc::daysToDate2($days_stop);
			$to = Date_Calc::dateFormat2($stop, "%e %b %y");
			$days_next = $days_stop + 1;
			$next = Date_Calc::daysToDate2($days_next);
			$start = $next;
			if ($lesson_length != 0) {
				$lesson_title .= ' '.$from . ' - ' . $to;
				//echo '<td width="120" align=center valign=top>'. $from . ' - ' . $to.'</td>';
			}
			else {
				$lesson_title .= '-';
				//echo '<td width="120" align=center valign=top>-</td>';
			}
		}

		//echo '</tr>';
		$order   = array("\r\n", "\n", "\r");
		$replace = '<br>';
		?>
<script language="JavaScript" type="text/javascript">
	menu2.entry(<?=$level;?>, "<?=$show_item. ' ' .$lesson_title. '<BR>' .str_replace($order, $replace,$lesson_description)?>", "<?=$lesson_link ?>", "", "");
</script>
		<?php
			
		//echo '<tr>';
		//echo '<td width='.$blank.'></td>';
		//echo '<td>';
		//echo nl2br(stripslashes($lesson_description));
		//echo '</td><td></td>';
		//echo '</tr>';
		//echo '</table>';
		listLessonOutline($cid,$sid,$lid,$orderings,$level + 1);
		array_pop($orderings);

	}
}

function  disp_confirm(){
	$uidnow =  lnSessionGetVar('uid');
	echo '<body  onload ="return  disp_confirm()">';
	// return 1;
}
/**
 * check special users
 */
function isSpecialUsers($sid) {
	$submission_info = lnSubmissionGetVars($sid);
	if ( @$submission_info['student'] == @_LNSTUDENT_GUEST ||
	(@$submission_info['student'] == _LNSTUDENT_USER && lnUserLoggedIn())	||
	lnCourseInstructor($sid) ||
	lnUserAdmin(lnSessionGetVar('uid'))) {
		return true;
	}
	else {
		return false;
	}
}

/**
 * show quiz
 */
// LAst modified by Tammatisthan J. 10/24/2009 11:37:39 AM
function showQuiz($vars) {
	// Get arguments from argument array
	extract($vars);

	$sid = lnGetSubmissionID($cid);
	$uidnow =  lnSessionGetVar('uid');

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];
	$quiz_anstable = $lntable['quiz_answer'];
	$quiz_anscolumn = &$lntable['quiz_answer_column'];
	$quiz_test = $lntable['quiz_test'];
	$quiz_testcolumn = &$lntable['quiz_test_column'];


	$courseinfo = lnCourseGetVars($cid);
	$coursecode=$courseinfo['cid'];
	$url=COURSE_DIR.'/'.$coursecode;

	if (empty($lid)) $lid=0;

	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<LINK REL="STYLESHEET"
	HREF="themes/<?= lnConfigGetVar('Default_Theme')?>/style/style.css"
	type="text/css">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0"
	MARGINWIDTH="0">
	<?

	echo '<table width="100%" cellpadding="3" cellspacing="0" border="0">';

	$lesson = lnLessonGetVars($lid);


	echo '<tr><td height=350 bgcolor=#FFFFFF valign=top>';

	// show question order
	$quiz_info = lnQuizGetVars($qid);
	$attempts = $quiz_info['attempts'];
	if($attempts==0) $attempts=9999;

	if ((countAttempt($cid,$lid) >= $attempts && $attempts)||($quiz_ansattempts > $attempts)) {
		//echo $quiz_ansattempts;
		echo '<BR><BR><CENTER><FONT SIZE="1" COLOR="#FF3300"><B>ไม่สามารถทำแบบทดสอบได้อีก<BR><BR> เพราะคุณทำการสอบครบ '.$attempts.' ครั้งแล้ว !</B></FONT></CENTER>';
	}
	else {

		$quizdesc = $quiz_info['intro'];
		$random = $quiz_info['shufflequestions'];
		$timelimit = $quiz_info['testtime'];

		echo '<B>'.$lesson['no'].' '.$lesson[title].'</B><BR><BR>';
		$quizdesc = stripslashes($quizdesc);
		$quizdesc = lnShowContent($quizdesc,$url);
		echo $quizdesc;


		$quizdesc=nl2br(stripslashes($quizdesc));
		$quizdesc=lnShowContent($quizdesc,$url);

		//>>>> Questions
		echo '<FORM NAME="test" METHOD=POST ACTION="index.php">
		<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">	
		<INPUT TYPE="hidden" NAME="op" VALUE="check_score2">	
		<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">	
		<INPUT TYPE="hidden" NAME="sid" VALUE="'.$sid.'">	
		<INPUT TYPE="hidden" NAME="lid" VALUE="'.$lid.'">				
		<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">
		<INPUT TYPE="hidden" NAME="eid" VALUE="'.$eid.'">
		<INPUT TYPE="hidden" NAME="quiz_ansattempts" VALUE="'.$quiz_ansattempts.'">';

		$arrMcid = getQuizMember(lnVarPrepForStore($qid));
		$nmcid_m = count($arrMcid);

		$query =	"SELECT COUNT($quiz_anscolumn[useranswer])
		FROM $quiz_anstable 
		WHERE $quiz_anscolumn[qid]='".lnVarPrepForStore($qid)."'
		AND $quiz_anscolumn[useranswer]!='0'
		AND	$quiz_anscolumn[eid]='".lnVarPrepForStore($eid)."'
		AND	$quiz_anscolumn[attempts]='".lnVarPrepForStore($quiz_ansattempts)."'
		AND	$quiz_anscolumn[qid]='".lnVarPrepForStore($qid)."'
		AND	$quiz_anscolumn[lid]='".lnVarPrepForStore($lid)."'";		
		$result4 = $dbconn->Execute($query);
		list($userans2) = $result4->fields;
		//By Xeonkung
		if (($nmcid == null)||($nmcid <= 0)){
			$nmcid = 0;
		}
		else{
			$nmcid_b = $nmcid - 1;
		}
		if ($nmcid >= $nmcid_m - 1){
			$nmcid_n = $nmcid_m - 1;
		}
		else{
			$nmcid_n = $nmcid + 1;
		}
		//print_r($arrMcid);
		//New showQuiz by Tammatisthan J.
		switch ($arrMcid[$nmcid]['type']){
			case 1:
				$question = stripslashes(getInterrogativeSentence($arrMcid[$nmcid]['mcid']));
				$question = str_replace('\"','"',$question);
				$question = lnShowContent($question,$url);
				
				$qlist = "<b>"._ClozeTestQuiz." ".($nmcid+1)." $question</b><br/>";
				$qlist .= getHtmlChoices($arrMcid[$nmcid]['mcid'],$quiz_ansattempts,$eid,$random,$qid,$lid);
				break;
			case 2:
				$qlist = "";
				for ($i = $arrMcid[$nmcid]['head']; $i < $arrMcid[$nmcid]['foot']; $i++) {
					$question = stripslashes(getInterrogativeSentence($i));
					$question = str_replace('\"','"',$question);
					$question = lnShowContent($question,$url);
					
					$qlist .= "<b>$question</b><U>&nbsp;&nbsp;&nbsp;(".($i+$arrMcid[$nmcid]['weight']-$arrMcid[$nmcid]['head']).")".getUserAnswer($i,$eid,$quiz_ansattempts,$qid,$lid)."&nbsp;&nbsp;&nbsp;</U>";
					//echo '========'.$i.':'.$eid.':'.$quiz_ansattempts.':'.$qid.':'.$lid.'<br>';
				}
				$question = lnShowContent(stripslashes(getInterrogativeSentence($arrMcid[$nmcid]['foot'])),$url);
				$question = str_replace('\"','"',$question);
				$question = lnShowContent($question,$url);
				$qlist .= "<b>$question</b>";
				$qlist = "<fieldset><legend>Cloze Test</legend>$qlist</fieldset>";
				$qlist .= "<b>"._ClozeTestQuiz." ".($nmcid+1)."</b><br/>";
				$qlist .= getHtmlChoices($arrMcid[$nmcid]['mcid'],$quiz_ansattempts,$eid,$random,$qid,$lid);
				break;
			case 3:
				$questionH = stripslashes(getInterrogativeSentence($arrMcid[$nmcid]['foot']));
				$questionH = str_replace('\"','"',$questionH);
				$questionH = lnShowContent($questionH,$url);
				$qlist = "<fieldset><legend>Multi Question</legend><b>".$questionH."</b></fieldset>";
				$question = stripslashes(getInterrogativeSentence($arrMcid[$nmcid]['mcid']));
				$question = str_replace('\"','"',$question);
				$question = lnShowContent($question,$url);
				$qlist .= "<b>"._ClozeTestQuiz." ".($nmcid+1)." $question</b><br/>";
				$qlist .= getHtmlChoices($arrMcid[$nmcid]['mcid'],$quiz_ansattempts,$eid,$random,$qid,$lid);
				break;
		}
		//End new
		echo  	"<table width=\"100%\"><tr><td>&nbsp;&nbsp;$qlist</td>
				<td align=\"right\" style=\"vertical-align:top;\">"._GOTO." : <select name=\"jumpQuiz\" id=\"jumpQuiz\" onchange=\"window.open(this.options[this.selectedIndex].value,'_self')\">";
		echo 	'<option value="">-</option>';
		echo	listQuiz($lid,$cid,$qid,$sid,$page,$eid,$quiz_ansattempts,$nmcid_m);
		echo 	'</select></td></tr></table>';
		?>
<script language="JavaScript" type="text/javascript">
function back_btn2(){
	document.forms.test.submit();
	back_page("check_score2");
}
function back_page(option){
	if(option=="check_score2"){
		//alert(option);
		window.open('index.php?mod=Courses&&op=showcontent&cid=<?=$cid?>&qid=<?=$qid?>&lid=<?=$lid?>&sid=<?=$sid?>&page=<?=$page?>&nmcid=<?=$nmcid_b?>&eid=<?=$eid?>&quiz_ansattempts=<?=$quiz_ansattempts?>','_self');	
	}
}
function next_btn(){
	document.forms.test.submit();
}
function back_btn(){
	document.forms.test.submit();
	back_page("check_score2");	
	
}
function goto_check(){
	window.open('index.php?mod=Courses&&op=check_score&cid=<?=$cid?>&qid=<?=$qid?>&lid=<?=$lid?>&sid=<?=$sid?>&page=<?=$page?>&nmcid=<?=$nmcid?>&eid=<?=$eid?>&quizflag=1','_self');
}
function sendquiz()
			{
				var r=confirm("ยืนยันการส่งข้อสอบ");
				if (r==true){
				 	window.open('index.php?mod=Courses&op=ln_check_scores&cid=<?=$cid?>&sid=<?=$sid?>&eid=<?=$eid?>&lid=<?=$lid?>&qid=<?=$qid?>','_parent');
					//return page
				  }
			}
</script>
		<?
		echo '<INPUT TYPE="hidden" NAME="quid" VALUE="'.$arrMcid[$nmcid]['mcid'].'">';
		echo '<INPUT TYPE="hidden" NAME="nmcid_n" VALUE="'.$nmcid_n.'">';
		echo '<INPUT TYPE="hidden" NAME="total" VALUE="'.$total.'">';
		echo "<CENTER><INPUT CLASS=\"button\" TYPE=button VALUE=\"บันทึกและไปยังข้อก่อนหน้า\" OnClick=\"back_btn()\">";
		//echo "<CENTER><INPUT CLASS=\"button\" TYPE=button VALUE=\"บันทึกและไปยังข้อก่อนหน้า\" OnClick=\"back_btn2()\">";
		if(($nmcid_m)==($nmcid+1)) { //check the last question to show especially "บันทึก"
			echo "&nbsp;&nbsp;<INPUT CLASS=\"button\" TYPE=button VALUE=\"บันทีก\"
OnClick=\"next_btn()\"></CENTER>";
		} else {
			echo "&nbsp;&nbsp;<INPUT CLASS=\"button\" TYPE=button VALUE=\"บันทีกและไปยังข้อถัดไป\"
OnClick=\"next_btn()\"></CENTER>";		
		}
		echo _NOWQUIZMSG1.'<font color=#ff9900>'.$userans2.'</font>'._NOWQUIZMSG2.'<font color=#009900>'.($nmcid_m).'</font>'._NOWQUIZMSG3.'<BR>';
		echo '<div id="timeleft"></div><br>';
		// Send Answer
		echo "<INPUT TYPE=button VALUE=\"ส่งข้อสอบ\" OnClick=\"sendquiz()\" >";
		echo '</FORM>';

		// show timed quiz By Xeonkung

	}


	echo '</td></tr>';
	echo '</table>';
}



/**
 * count Attempts
 */
function countAttempt($eid,$lid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$scorestable = $lntable['scores'];
	$scorescolumn = &$lntable['scores_column'];

	$query = "SELECT  COUNT($scorescolumn[eid])
	FROM  $scorestable
	WHERE 	$scorescolumn[eid] = $eid and $scorescolumn[lid] =  $lid";
	$result = $dbconn->Execute($query);
	//echo '<pre>'.$query.'</pre>';
	$max_attempts = $result->fields[0];
	//echo 'Attempts='.$max_attempts;
	return $max_attempts;
}


/**
 * random quiz
 */
function rand_numbers($min,$max,$item_nbr) {

	if ($item_nbr == 0) {
		$item_nbr = $max+1;
	}

	$return_array = array();
	srand(time());

	if ($max < $item_nbr) {
		$item_nbr = $max+1;
	}

	while (count($return_array)<$item_nbr) {
		$return_array[] = rand($min,$max);
		$return_array   = array_unique($return_array);
	}

	return $return_array;
}


/**
 * check score
 */
function checkScore($vars) {
	// Get arguments from argument array
	extract($vars);
	//echo "ss".$cltype;

	$lesson = lnLessonGetVars($lid);

	?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<LINK REL="STYLESHEET"
	HREF="themes/<?= lnConfigGetVar('Default_Theme')?>/style/style.css"
	type="text/css">
</HEAD>
<BODY BGCOLOR="#FFFFFF" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0"
	MARGINWIDTH="0">
	<?

	echo '<table  width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr><td height=30 bgcolor="#FFFFFF" valign="middle">';
	echo '<FONT COLOR="#800000"><B>'.$lesson[title].'</B></FONT>';
	echo '</td></tr>';
	echo '<tr><td height=350 bgcolor=#FFFFFF valign=top>';
	if($cltype!=2){
		echo '<U><B>'._ANSWER.'</B></U><P>';
	} else {
		echo '<P>';
	}
	if($quizflag==1){
		$ans = createArrayAns($qid,$eid);
		$total = getTotalScore($qid);
	}
	//	print_r($ans);
	$sum=0;
	$quiz_info = lnQuizGetVars($qid);

	foreach ($ans as $quid => $n) {
		$choose = 0;
		foreach ($n as $i) {
			$choose += $i ;
		}
		$chscores = checkAnswer($quid,$choose,$sid,$quiz_info['feedback'],$quiz_info['correctanswers'],$cid);
		$sum +=  $chscores['raw'];
		echo "<P>";
	}
	if($cltype!=2){
		//echo $qid;
		checkPassed($cid,$lid,$qid,$sum,$total);

		echo '</td></tr>';
		echo '</table>';

		$percent = ($sum/$total)*100;
		$percent = sprintf("%2.2f", $percent);
	} else {
		//echo $qid;
		checkPassedcltype2($cid,$lid,$qid,$sum,$total,$cltype);

		echo '</td></tr>';
		echo '</table>';

		//$percent = ($sum/$total)*100;
		//$percent = sprintf("%2.2f", $percent);

	}
	$courseinfo = lnCourseGetVars($lid);

	courseTracking($eid,$lid,1);

	lnUpdateUserEvent("Quizs     Course:  $cid      Chapter:    $lesson[weight]    score: $percent%");
	//////////////////////////////////////////////////////////////////////// ที่แสดงว่าเป็น quiz เมื่อแสดงใน user_log////////////////////////////////////////////////////////////////////////////
}


function checkScore2($vars) {
	// Get arguments from argument array
	extract($vars);
	//echo '<pre>';
	//print_r($vars);
	//echo '</pre>';
	//exit();

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_anstable = $lntable['quiz_answer'];
	$quiz_anscolumn = &$lntable['quiz_answer_column'];
	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];

	$query = "SELECT $quiz_anscolumn[qaid]
	FROM  $quiz_anstable
	WHERE 	$quiz_anscolumn[mcid] =  '" . lnVarPrepForStore($quid) . "'
	AND	$quiz_anscolumn[eid] =  '" . lnVarPrepForStore($eid) . "'
	AND	$quiz_anscolumn[attempts]='".lnVarPrepForStore($quiz_ansattempts)."'
	AND	$quiz_anscolumn[qid]='".lnVarPrepForStore($qid)."'
	AND	$quiz_anscolumn[lid]='".lnVarPrepForStore($lid)."'";

	$result = $dbconn->Execute($query);
	list($qaid,$attempts) = $result->fields;

	$query2 = "SELECT $quiz_questioncolumn[answer]
	FROM  $quiz_questiontable
	WHERE 	$quiz_questioncolumn[mcid] =  '" . lnVarPrepForStore($quid) . "'";

	$result2 = $dbconn->Execute($query2);
	list($answer) = $result2->fields;

	if (checkChoiceType($quid,$answer) == 0) {
		$useranswer = $ans[$quid][0];

	}else{
		$useranswer = 0;
		$quiz_choicetable = $lntable['quiz_choice'];
		$quiz_choicecolumn = &$lntable['quiz_choice_column'];
		$result = $dbconn->Execute("SELECT COUNT($quiz_choicecolumn[mcid]) FROM $quiz_choicetable WHERE $quiz_choicecolumn[mcid]='$quid'");
		list ($sum) = $result->fields;

		for ($i=0,$count=0; $i<$sum; $i++) {
			if ($ans[$quid][$i] & pow(2,$i)) {
				$count++;
				$useranswer = $useranswer + $ans[$quid][$i];
			}
		}
	}

	if ($useranswer == null){
		$useranswer = 0;
	}
	if ($qaid != null){	// = Update
		updateQuizAns($qaid,$eid,$quid,$useranswer);
	}
	else{				// = New
		addQuizAns($eid,$quid,$useranswer,$quiz_ansattempts,$qid,$lid);
	}


	// DeBugs Test ---------------------
	/*
	 echo 'qaid = '.$qaid;
	 echo '<br>';
	 echo 'eid = '.$eid;
	 echo '<br>';
	 echo 'mcid = '.$quid;
	 echo '<br>ans = '.$useranswer;
	 echo '<br>';
	 $xout = createArrayAns(1,1);
	 print_r($ans);
	 */
	// ----------------------------------
	?>
<script language="JavaScript" type="text/javascript">
function next_btn(){
	window.open('index.php?mod=Courses&&op=showcontent&cid=<?=$cid?>&qid=<?=$qid?>&lid=<?=$lid?>&sid=<?=$sid?>&nmcid=<?=$nmcid_n?>&eid=<?=$eid?>&quiz_ansattempts=<?=$quiz_ansattempts?>','_self');
}
next_btn();
</script>
	<?
	echo '<input type="button" value="OK" onclick="next_btn()" />';
}

/**
 *  check answer
 */
function checkAnswer($quid,$choose,$sid,$show_desc,$show_correct_ans,$cid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	$query = "SELECT  $quiz_questioncolumn[question],
	$quiz_questioncolumn[weight],
	$quiz_questioncolumn[answer],
	$quiz_questioncolumn[score],
	$quiz_choicecolumn[answer],
	$quiz_choicecolumn[feedback]
	FROM  $quiz_questiontable,$quiz_choicetable
	WHERE 	$quiz_questioncolumn[mcid] = $quiz_choicecolumn[mcid] and $quiz_questioncolumn[mcid] =  '".lnVarPrepForStore($quid)."'
	ORDER BY $quiz_choicecolumn[weight]";

	$result = $dbconn->Execute($query);

	list($question,$weight,$answer,$score,$choice,$description) = $result->fields;
	$question = stripslashes($question);

	/*เพิ่มให้มันแสดงรูปภาพคำถามตอนเฉลยด้วย */

	$courseinfo = lnCourseGetVars($cid);
	$coursecode=$courseinfo['cid'];
	$url=COURSE_DIR.'/'.$coursecode;
	$question = lnShowContent($question,$url);
	//****************************************************

	echo '<B>'._QUESTION.': '.$question.'</B> ('.$score.' '._QUESTIONSCORE.')<BR>';
	for ($i=0; list($_,$_,$_,$_,$choice,$description) = $result->fields; $i++) {
		$result->MoveNext();
		$choice = stripslashes($choice);

		//เพิ่มให้แสดงรูปภาพคำตอบตอนเฉลย
		$choice = lnShowContent($choice,$url);
		//*****************************************************

		if ($answer & pow(2,$i)) {
			if ($show_correct_ans) {
				echo '<font color=#009900><B>'.sprintf("%c",$i+65).'. '.$choice.'</B></font>';
			}
			else {
				echo sprintf("%c",$i+65).'. '.$choice;
			}
		}
		else {
			echo sprintf("%c",$i+65).'. '.$choice;
		}
		if ($choose & pow(2,$i)) {
			$chooses[] = sprintf("%c",$i+65);
		}

		if (!empty($description) && $show_desc) {
			echo ' <font color=#009900>- '.$description.'</font>';
		}
		echo '<BR>';
	}

	$show_choose = join(',',$chooses);
	echo '<B>'._YOURANSWER.': ('.$show_choose.') ' ;

	if ($answer == $choose) {
		if($show_correct_ans == 1) //แก้ให้มันเฉลยคำตอบ
		{
			echo ' '._THATIS .' <IMG SRC="images/global/passed.gif" WIDTH="14" HEIGHT="12" BORDER="0" ALT=""> '._CORRECT.'</B><P>';
		}
		$data['raw']=$score;
	}
	else {
		if($show_correct_ans == 1) //แก้ให้มันเฉลยคำตอบ
		{
			echo ' '._THATIS.' <IMG SRC="images/global/wrong.gif" WIDTH="14" HEIGHT="12" BORDER="0" ALT=""> '._WRONG.'</B><P>';
		}
		$data['raw']=0;
	}

	$data['score']=$score;

	return $data;
}


/**
 *  check quiz status
 */


/***
 function checkPassed($cid,$lid,$qid,$sum,$total) {
 list($dbconn) = lnDBGetConn();
 $lntable = lnDBGetTables();

 $scorestable = $lntable['scores'];
 $scorescolumn = &$lntable['scores_column'];

 // bas add assessment **************************************************
 $quiztable = $lntable['quiz'];
 $quizcolumn = &$lntable['quiz_column'];
 $query2 = "SELECT $quizcolumn[assessment]
 FROM $quiztable WHERE $quizcolumn[qid] = $qid";
 $result2 = $dbconn->Execute($query2);
 list($assessment) = $result2->fields;

 //echo $qid;

 $lessonstable = $lntable['lessons'];
 $lessonscolumn = &$lntable['lessons_column'];

 $query = "SELECT  $lessonscolumn[type], $lessonscolumn[file]
 FROM $lessonstable
 WHERE $lessonscolumn[lid]='".$lid."'";
 $result3 = $dbconn->Execute($query);
 list($type,$lesson_file) = $result3->fields;

 //************************************************************************

 echo '<HR><CENTER><B>';
 echo _YOUGETSCORE.' '.$sum.' ';
 echo _FULLSCORE.'  ' .$total;
 $percent = ($sum/$total)*100;
 printf(" ( %2.2f", $percent);
 echo '%)';

 //change follow by assessment
 if ($percent >= $assessment) {

 if ($type==1) {

 echo '<P>'._PASSQUIZ.'</P>';
 }
 else if($type==2) {
 echo '<P><A HREF="index.php?mod=Courses&op=lesson_show&uid='.$uid.'&cid='.$cid.'&sid='.$sidnow.'&lid='.$lid.'" target="_parent">'._PASSQUIZ.'</A></P>';
 }
 }
 //-----------------------------------------

 else {
 $uid = lnSessionGetVar('uid');

 echo '<P><FONT  COLOR="#FF0000"><A HREF="index.php?mod=Courses&op=lesson_show&uid='.$uid.'&cid='.$cid.'&sid='.$sidnow.'&lid='.$lid.'&qid='.$qid.'" target="_parent">'._NOTTPASS.' '.$assessment.'% '._BACKTOLEARN.'</A></FONT>';
 }
 echo '</B></CENTER><P>';

 // insert to score table
 $eid = lnGetEnroll($cid);

 $query = "INSERT INTO $scorestable VALUES ('".lnVarPrepForStore($eid)."', '".lnVarPrepForStore($lid)."', '".lnVarPrepForStore($percent)."', null )";
 $result = $dbconn->Execute($query);
 }
 */

//checkPassed modify by narananami
function checkPassed($cid,$lid,$qid,$sum,$total) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$scorestable = $lntable['scores'];
	$scorescolumn = &$lntable['scores_column'];

	// bas add assessment **************************************************
	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$query2 = "SELECT $quizcolumn[assessment]
	FROM $quiztable WHERE $quizcolumn[qid] = $qid";
	$result2 = $dbconn->Execute($query2);
	list($assessment) = $result2->fields;

	//echo $qid;

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$query = "SELECT  $lessonscolumn[type], $lessonscolumn[file],$lessonscolumn[title]
	FROM $lessonstable
	WHERE $lessonscolumn[lid]='".$lid."'";
	$result3 = $dbconn->Execute($query);
	list($type,$lesson_file,$title) = $result3->fields;

	//************************************************************************

	//echo '<HR><CENTER><B>';
	echo '<CENTER><B>';
	echo _YOUGETSCORE.' '.$sum.' ';
	echo _FULLSCORE.'  ' .$total;
	$percent = ($sum/$total)*100;
	printf(" ( %2.2f", $percent);
	echo '%)';
	lnUpdateUserEvent("Quizs     Course:  $cid      Chapter:  $title  score: $percent%");
	//change follow by assessment
	if ($percent >= $assessment) {

		if ($type==1) {

			echo '<P>'._PASSQUIZ.'</P>';
			//echo '<meta http-equiv="refresh" content="2;URL=index.php?mod=Courses&op=course_lesson&cid='.$cid.'&uid='.$uid.'&eid='.$eid.'" />';
			echo '<P><FONT  COLOR="#FF0000"><A HREF="index.php?mod=Courses&op=course_lesson&cid='.$cid.'&uid='.$uid.'&eid='.$eid.'">'._BACK.'</A></FONT>';
		}
		else if($type==2) {
			echo '<P><A HREF="index.php?mod=Courses&op=lesson_show&uid='.$uid.'&cid='.$cid.'&sid='.$sidnow.'&lid='.$lid.'" target="_parent">'._PASSQUIZ.'</A></P>';
		}
	}
	//-----------------------------------------

	else {
		$uid = lnSessionGetVar('uid');
		//echo '<P><FONT  COLOR="#FF0000"><A HREF="index.php?mod=Courses&op=lesson_show&uid='.$uid.'&cid='.$cid.'&sid='.$sidnow.'&lid='.$lid.'&qid='.$qid.'" target="_parent">'._NOTTPASS.' '.$assessment.'% '._BACKTOLEARN.'</A></FONT>';
		echo '<P><FONT  COLOR="#FF0000"><A HREF="index.php?mod=Courses&op=course_lesson&cid='.$cid.'&uid='.$uid.'&eid='.$eid.'">'._NOTTPASS.' '.$assessment.'% '._BACKTOLEARN.'</A></FONT>';
	}
	echo '</B></CENTER><P>';

}


/**
 * Lesson Show
 */
function lessonShowFrame($vars) {
	// Get arguments from argument array
	extract($vars);
	if(!isset($cid)) $cid='';
	if(!isset($sid)) $sid='';
	if(!isset($lid)) $lid='';
	if(!isset($uid)) $uid='';
	if(!isset($page)) $page='';
	if(!isset($eid)) $eid='';
	if(!isset($qid)) $qid='';
	if(!isset($quiz_ansattempts)) $quiz_ansattempts='';
	$list = @extract($var);

	//start list box menu
	// todo - - security to access course;
	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
	tabMenu($vars,2);

	echo '</TD></TR><TR><TD>';
	echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr height="35">';
	echo '<td bgcolor=#FFFFFF valign=middle  align=left>';
	gotoLesson($cid, $sid, $lid);
	//echo '</td>';
	
	//Bookmarks
	if($uid){
		if(!$page) $page=0;
		if(!$sid) $sid=0;
		?>
<script language="JavaScript" src="javascript/jquery.min.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/jquery-ui.min.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/jquery.blockUI.js" type="text/javascript"></script>
<link type="text/css" href="css/ui-lightness/jquery-ui.css" rel="stylesheet" />

<script language="JavaScript" type="text/javascript">
	function bookmarks(){
		$(function(){
		    	var url="index.php?mod=User&file=bookmarks";
		    	var dataSet={op: $("#op").val(), uid: <?php echo $uid;?>, lid: <?php echo $lid;?>, cid: <?php echo $cid;?>, sid: <?php echo $sid;?>, page: <?php echo $page;?>};
				
		    	$.post(url, dataSet, function(data){
			    	if($("#op").val()=="add"){
			    		data = '<input name="op" id="op" type="hidden" value="delete"><a href="#" onClick="bookmarks()"><img src="images/global/bookmark_remove.png"  WIDTH="100" HEIGHT="84" border="0" title="<?php echo _REMOVEBOOKMARK;?>"></a>';
				    }else{
				    	data = '<input name="op" id="op" type="hidden" value="add"><a href="#" onClick="bookmarks()"><img src="images/global/bookmark_add.png"  WIDTH="100" HEIGHT="84" border="0" title="<?php echo _ADDBOOKMARK;?>"></a>';
					}
				    $('div#bookmarks').html(data);      
		    	});
		});
	}
</script>
	<?php
		include_once 'modules/User/bookmarks.php';
	  	$checkbookmarks = bookmarksCheck($uid,$lid);
	
	  	echo '<td align="right">';
	  	
		echo '<form id="form" method="post" action="">';
		echo '<div id="bookmarks" agian="left" name="bookmarks">';
		if($checkbookmarks){
			echo '<input name="op" id="op" type="hidden" value="delete">';
			//echo '<a href="#" onClick="bookmarks()">'._REMOVEBOOKMARK.'</a>';
			echo '<a href="#" onClick="bookmarks()"><img src="images/global/bookmark_remove.png"  WIDTH="100" HEIGHT="84" border="0" title="'._REMOVEBOOKMARK.'"></a>';
		}else{
			echo '<input name="op" id="op" type="hidden" value="add">';
			//echo '<a href="#" onClick="bookmarks()">'._ADDBOOKMARK.'</a>';
			echo '<a href="#" onClick="bookmarks()"><img src="images/global/bookmark_add.png"  WIDTH="100" HEIGHT="84" border="0" title="'._ADDBOOKMARK.'"></a>';
		}
		echo '</div>';
		echo '</form>';
		//end bookmarks
		echo '</td>';
	}
	echo '</td></tr>';
	echo '<tr><td height="350" colspan="2" bgcolor="#FFFFFF" valign="top">';
	$uidnow =  lnSessionGetVar('uid');

	//by Xeonkung ++ eid
	//add quiz_ansattempts By narananami
	$object = "index.php?mod=Courses&amp;op=showcontent&amp;cid=$cid&amp;qid=$qid&amp;lid=$lid&amp;sid=$sid&amp;page=$page&amp;uid=$uidnow&amp;eid=$eid&amp;quiz_ansattempts=$quiz_ansattempts";
	//end list box menu

	/*
	 //---- to change list box to tree menus : Programmer : Bas, 14/11/49)
	 list($dbconn) = lnDBGetConn();
	 $lntable = lnDBGetTables();

	 $lessonstable = $lntable['lessons'];
	 $lessonscolumn = &$lntable['lessons_column'];

	 $query2 = "SELECT  $lessonscolumn[title] FROM $lessonstable 	WHERE $lessonscolumn[cid]= $cid";
	 $result2 = $dbconn->Execute($query2);

	 // todo - - security to access course;
	 echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
	 tabMenu($vars,2);

	 echo '</TD></TR><TR><TD>';
	 echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	 echo '<tr height="35">';
	 echo '<td bgcolor=#FFFFFF valign=middle  align=left>';
	 echo '</td></tr>';
	 echo '<tr><td width="150" bgcolor=lightblue valign="top">';


	 gotoAnchor($cid, $sid, $lid);
	 //----end of to change list box to tree menus


	 $uidnow =  lnSessionGetVar('uid');
	 echo '</td>';

	 //---- to change list box to tree menus : Programmer : Bas, 14/11/49)
	 echo '<td width="660" height="350" bgcolor="#FFFFFF" valign="top">';
	 //----end of to change list box to tree menus

	 $object = "index.php?mod=Courses&amp;op=showcontent&amp;cid=$cid&amp;qid=$qid&amp;lid=$lid&amp;sid=$id&amp;page=$page&amp;uid=$uidnow";
	 */

	?>
<script language="JavaScript" type="text/javascript">
<!--
function SCORMapi() {
    var cmi= new Object();

    var errorCode = 0;
    
    var Initialized = false;

    function LMSInitialize (param) {
	if (param != "") {
	    errorCode = 201;
	    return "false";
	}
	if (!Initialized) {
	    Initialized = true;
	    errorCode = 0;

	    cmi.core = new Object();
	    cmi.core._children = "student_id,student_name,lesson_location,credit,lesson_status,exit,entry,session_time,total_time,lesson_mode,score,suspend_data,launch_data";
	    cmi.core.student_id = "<?php echo $sco_user->cmi_core_student_id; ?>";
	    cmi.core.student_name = "<?php echo $sco_user->cmi_core_student_name; ?>";
	    cmi.core.lesson_location = "<?php echo $sco_user->cmi_core_lesson_location; ?>";
	    cmi.core.credit = "<?php if ($mode != 'normal') {
	    				 echo "no-credit";
	    			     } else {
					 echo "credit";
				     }?>";
	    cmi.core.lesson_status = "<?php echo $sco_user->cmi_core_lesson_status; ?>";
	    cmi.core.exit = "<?php echo $sco_user->cmi_core_exit ?>";
	    cmi.core.entry = "<?php if ($sco_user->cmi_core_lesson_status == 'not attempted') {
					echo 'ab-initio'; 
				    } else {
					if ($sco_user->cmi_core_lesson_status != 'completed') {
					    echo 'resume'; 
				    	} else {
					    echo '';
					}
				    }?>";
	    cmi.core.session_time = "00:00:00";
	    cmi.core.total_time = "<?php echo $sco_user->cmi_core_total_time; ?>";
	    cmi.core.lesson_mode = "<?php echo $mode; ?>";
	    cmi.core.score = new Object();
	    cmi.core.score._children = "raw,min,max";
	    cmi.core.score.raw = "<?php echo $sco_user->cmi_core_score_raw; ?>";
	    cmi.suspend_data = "<?php echo $sco_user->cmi_suspend_data; ?>";
	    cmi.launch_data = "<?php echo $sco_user->cmi_launch_data; ?>";
	    
	    nav = new Object();
	    <?php 
	        if ($scorm->auto) {
	    	    echo 'nav.event = "continue";'."\n";
	    	} else {
	            echo 'nav.event = "";'."\n";
	        }
	    ?>

	    return "true";
	} else {
	    errorCode = 101;
	    return "false";
	}
    }
    
    function LMSGetValue (param) {
	if (Initialized) {
	    switch (param) {
		case "cmi.core._children":
		case "cmi.core.student_id":
		case "cmi.core.student_name":
		case "cmi.core.lesson_location":
		case "cmi.core.credit":
		case "cmi.core.lesson_status":
		case "cmi.core.entry":
		case "cmi.core.total_time":
		case "cmi.core.lesson_mode":
		case "cmi.core.score._children":
		case "cmi.core.score.raw":
		case "cmi.launch_data":
		case "cmi.suspend_data":
		    errorCode = 0;
		    return eval(param);
		break;
		case "cmi.core.exit":
		case "cmi.core.session_time":
		    errorCode = 404;
		    return "";
		break;
		default:
		    errorCode = 401;
		    return "";
		break;
	    }
	} else {
	    errorCode = 301;
	    return "";
	}
    }
    
    function LMSSetValue (param,value) {
	if (Initialized) {
	    switch (param) {
		case "cmi.core.session_time":
		    if (typeof(value) == "string") {
		        var matchedtime = value.match(/([0-9]{2,4}):([0-9]{2}):([0-9]{2})/g);
		        if (matchedtime != null) {
		            var parsedtime = value.match(/[0-9]+/g);
		            if (((parsedtime.length == 3) || (parsedtime.length == 4)) && (parsedtime[0]>=0) && (parsedtime[0]<=9999) && (parsedtime[1]>=0) && (parsedtime[1]<=59) && (parsedtime[2]>=0) && (parsedtime[2]<=59)) {
		            	if ((parsedtime.length == 4) && (parsedtime[3]<=0) && (parsedtime[3]>=99)) {
		                    errorCode = 405;
		        	    return "false";
		       	    	}
		                eval(param+'="'+value+'";');
		        	errorCode = 0;
		        	return "true";
		            } else {
		            	errorCode = 405;
		            	return "false";
		       	    }
		       	} else {
		       	    errorCode = 405;
		            return "false";
		       	}
		    } else {
		        errorCode = 405;
		        return "false";
		    }
		break;
		case "cmi.core.lesson_status":
		    if ((value!="passed")&&(value!="completed")&&(value!="failed")&&(value!="incomplete")&&(value!="browsed")) {
			errorCode = 405;
			return "false";
		    }
		    eval(param+'="'+value+'";');
		    errorCode = 0;
		    return "true";
		break;
		case "cmi.core.score.raw":
		case "cmi.core.score.min":
		case "cmi.core.score.max":
		    if ((parseFloat(value,10)).toString() != value) {
			errorCode = 405;
			return "false";
		    } else {
		    	rawvalue = parseFloat(value,10);
		        if ((rawvalue<0) || (rawvalue>100)) {
		           errorCode = 405;
		           return "false";
		        }
		    }
		    eval(param+'="'+value+'";');
		    errorCode = 0;
		    return "true";
		break;
		case "cmi.core.exit":
		    if ((value!="time-out")&&(value!="suspend")&&(value!="logout")&&(value!="")) {
			errorCode = 405;
			return "false";
		    }
		    eval(param+'="'+value+'";');
		    errorCode = 0;
		    return "true";
		break;
		case "cmi.core.lesson_location":
		case "cmi.suspend_data":
		    eval(param+'="'+value+'";');
		    errorCode = 0;
		    return "true";
		break;
		case "cmi.core._children":
		case "cmi.core.score._children":
		    errorCode = 402;
		    return "false";
		break;
		case "cmi.core.student_id":
		case "cmi.core.student_name":
		case "cmi.core.credit":
		case "cmi.core.entry":
		case "cmi.core.total_time":
		case "cmi.core.lesson_mode":
		case "cmi.launch_data":
		    errorCode = 403;
		    return "false";
		break;
		case "nav.event":
		    if ((value == "previous") || (value == "continue")) {
		       eval(param+'="'+value+'";');
		   	errorCode = 0;
		    	return "true";
		    } else {
		        erroCode = 405;
		        return "false";
		   }
		break;	
		default:

		    errorCode = 0;     
		    return "false";
		break;
	    }
	} else {
	    errorCode = 301;
	    return "false";
	}
    }

    function LMSCommit (param) {
	if (param != "") {
	    errorCode = 201;
	    return "false";
	}
	if (Initialized) {
	    if (<?php echo $navObj ?>document.theform) {
		cmiform = <?php echo $navObj ?>document.forms[0];
		cmiform.scoid.value = "<?php echo $sco->id; ?>";
		cmiform.cmi_core_lesson_location.value = cmi.core.lesson_location;
		cmiform.cmi_core_lesson_status.value = cmi.core.lesson_status;
		cmiform.cmi_core_exit.value = cmi.core.exit;
		cmiform.cmi_core_score_raw.value = cmi.core.score.raw;
		cmiform.cmi_suspend_data.value = cmi.suspend_data;
		cmiform.submit();
	    }
	    errorCode = 0;
	    return "true";
	} else {
	    errorCode = 301;
	    return "false";
	}
    }
    
    function LMSFinish(param) {
	if (param != ""){
	    errorCode = 201;
	    return "false";
	}
	if (!Initialized) {
	    errorCode = 301;
	    return "false";
	} else {
	    Initialized = false;
	    errorCode = 0;
	    cmi.core.total_time = AddTime(cmi.core.total_time, cmi.core.session_time);
	    if (<?php echo $navObj ?>document.theform) {
		cmiform = <?php echo $navObj ?>document.forms[0];
		cmiform.scoid.value = "<?php echo $sco->id; ?>";
		cmiform.cmi_core_total_time.value = cmi.core.total_time;
		cmiform.submit();
	    }
            if (nav.event != "") {
            <?php
		if ($sco != $last) {
	          echo "setTimeout('top.changeSco(nav.event);',500);\n";
		} else {

		} 
	    ?>
	    }
	    return "true";
	}    
    }
    
    function LMSGetLastError () {
	return errorCode;
    }
    
    function LMSGetErrorString (param) {
	var errorString = new Array();
	errorString["0"] = "No error";
	errorString["101"] = "General exception";
	errorString["201"] = "Invalid argument error";
	errorString["202"] = "Element cannot have children";
	errorString["203"] = "Element not an array - cannot have count";
	errorString["301"] = "Not initializated";
	errorString["401"] = "Not implemented error";
	errorString["402"] = "Invalid set value, element is a keyword";
	errorString["403"] = "Element is read only";
	errorString["404"] = "Element is write only";
	errorString["405"] = "Incorrect data type";
	return errorString[param];
    }
    
    function LMSGetDiagnostic (param) {
	return param;
    }
	
    function AddTime (first, second) {
	var sFirst = first.split(":");
	var sSecond = second.split(":");
	var change = 0;
	
	var secs = (Math.round((parseFloat(sFirst[2],10)+parseFloat(sSecond[2],10))*100))/100; 	
	if (secs > 60) {
	    secs = secs - 60;
	    change = 1;
	} else {
	    change = 0;
	}
	if (Math.floor(secs) < 10) secs = "0" + secs.toString();
	
	mins = parseInt(sFirst[1],10)+parseInt(sSecond[1],10)+change; 	
	if (mins > 60) 
	    change = 1;
	else
	    change = 0;
	if (mins < 10) mins = "0" + mins.toString();
	    
	hours = parseInt(sFirst[0],10)+parseInt(sSecond[0],10)+change; 	
	if (hours < 10) hours = "0" + hours.toString();
	
	return hours + ":" + mins + ":" + secs;
    }
    
    this.LMSInitialize = LMSInitialize;
    this.LMSGetValue = LMSGetValue;
    this.LMSSetValue = LMSSetValue;
    this.LMSCommit = LMSCommit;
    this.LMSFinish = LMSFinish;
    this.LMSGetLastError = LMSGetLastError;
    this.LMSGetErrorString = LMSGetErrorString;
    this.LMSGetDiagnostic = LMSGetDiagnostic;
}
var API = new SCORMapi();
//-->
</script>


	    <?php

	    /////////////////////////////////////////////////////////////////////////////ส่วนแสดงเนื้อหาใน lesson /////////////////////////////////////////////////////////////////////////////////////////////////

	    echo "<IFRAME marginWidth=0 marginHeight=0 id=Content  scrolling=auto name=Content src=\"".$object."\" frameBorder=0 width=100% height=585>";

	    echo "<BR><BR><CENTER>Alternate content for non-supporting browsers <P><A HREF=".$object." target=_blank><B>Click here!</B></A></CENTER></IFRAME>";

	    // show footer menu
	    echo 'somthing'; // <- squalltua
	    echo '</td></tr>';
	    echo '</table>';

	    echo '</TD></TR></TABLE>';

}

/**
 * Lesson Show (Show Content)
 */
function lessonShow($vars) {
	// Get arguments from argument array
	extract($vars);

	$lessoninfo = lnLessonGetVars($lid);
	$lessondir = COURSE_DIR . '/' . $lessoninfo['cid'];

	// SMT page file switching
	if($lessoninfo['smt']){
		$SMTStatus = lnConfigGetVar('SMTStatus');
		$SUPARSITStatus =lnConfigGetVar('SUPARSITStatus');
		$LEXITRONStatus =lnConfigGetVar('LEXITRONStatus');
		//-- Change file --
		if($SMTStatus) {
			if($SUPARSITStatus && $LEXITRONStatus){
				$lessoninfo['file'] = 'smt_'.$lessoninfo['file'];
			}else if($SUPARSITStatus){
				$lessoninfo['file'] = 'suparsit_'.$lessoninfo['file'];
			}else if($LEXITRONStatus){
				$lessoninfo['file'] = 'lexitron_'.$lessoninfo['file'];
			}
			//echo '======='.$lessoninfo['file'];
		}
	}
	
	$lessonfile = $lessondir.'/'.$lessoninfo['file'];
	$lessonmp4f = '../'.COURSE_DIR . '/'.$lessoninfo['cid'] .'/'.$lessoninfo['file'];
	$eid = lnGetEnroll($cid);

	//Track Session Student
	$trackname = lnSessionGetVar('uid');
	$trackvalues = array ( "eid" => $eid, "lid" => $lid);//array($eid,$lid);
	lnSessionSetVar($trackname,$trackvalues);
	//exit();

	$ext = pathinfo($lessonfile);
	$ext = $ext['extension'];
	////////////////////////////////////////////////////////////////////////check ว่าเป็นข้อสอบ hotpotatoes หรือเปล่าจาก type type ข้อสอบจาก hotpotatoes = 2 ///////////////////////////////////////
	if ($lessoninfo['type'] != '0' && $lessoninfo['type'] != '2' && $lessoninfo['type'] != '3') {
		courseTracking($eid,$lid,0);
		showQuiz($vars);
		return;
	}
	/////////////////////////////////////////////////////////////////////	/////////////////////////////////////////////////////////////////////	/////////////////////////////////////////////////////////////////////
	if (!file_exists($lessonfile)) {
		return;
	}

	if (is_dir($lessonfile)){
		return;
	}

	if (strtolower($ext) == 'html' || strtolower($ext) == 'htm') {
		$fp=fopen($lessonfile,"r");
		$content=fread($fp,filesize($lessonfile));
		$content=lnShowContent($content,$lessondir);
	}

	// squalltua debug here?
	if (preg_match('{PAGE}',$content) || preg_match('{PDF}',$content) || preg_match('{SWF}',$content) || preg_match('{WMV}',$content) || preg_match('{PPT}',$content)  || preg_match('{EGATLOGO}',$content) || preg_match('<!--RICHEDIT-->',$content)) {
		
		?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
<HEAD>
<LINK REL="STYLESHEET"
	HREF="themes/<?= lnConfigGetVar('Default_Theme')?>/style/style.css"
	type="text/css">
<script language="JavaScript" type="text/javascript"
	src="modules/SCORM/api1_2.php"></script>
</HEAD>
<BODY BGCOLOR="#FFFFFF" TOPMARGIN="0" LEFTMARGIN="0" MARGINHEIGHT="0"
	MARGINWIDTH="0">
		<?

		//- - pdf tag  {PDF}file.pdf{/PDF} */
		$pdfObjBegin="<OBJECT id='Acrobat Control for ActiveX' height=550 width=100% border=1 classid=CLSID:CA8A9780-280D-11CF-A24D-444553540000><PARAM NAME='_Version' VALUE='327680'><PARAM NAME='_ExtentX' VALUE='18812'><PARAM NAME='_ExtentY' VALUE='14552'><PARAM NAME='_StockProps' VALUE='0'><PARAM NAME='SRC' VALUE=";
		$pdfObjEnd="></OBJECT>";
		$content = str_replace("{PDF}","{PDF}$lessondir/",$content);
		$content = str_replace("{pdf}","{pdf}$lessondir/",$content);
		$content = preg_replace("/{pdf}(.*?){\/pdf}/si", "$pdfObjBegin\"\\1\"$pdfObjEnd", $content);
		$content = preg_replace("/{PDF}(.*?){\/PDF}/si", "$pdfObjBegin\"\\1\"$pdfObjEnd", $content);

		//- - ppt tag  {PPT}file.pdf{/PPT} */

		$pptObjBegin="<EMBED width=500 height=300 SRC=";
		$pptObjEnd="</EMBED>";
		$content = str_replace("{PPT}","{PPT}$lessondir/",$content);
		$content = str_replace("{ppt}","{ppt}$lessondir/",$content);
		$content = preg_replace("/{ppt}(.*?){\/ppt}/si", "$pptObjBegin\"\\1\"$pptObjEnd", $content);
		$content = preg_replace("/{PPT}(.*?){\/PPT}/si", "$pptObjBegin\"\\1\"$pptObjEnd", $content);


		//- - Flash {SWF}file.swff{/SWF}
		$swfObjBegin1="<OBJECT classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0'  id='Lesson'> <PARAM NAME=movie VALUE=\"";
		$swfObjBegin2="> <PARAM NAME=quality VALUE=high> <PARAM NAME=bgcolor VALUE=#FFFFFF> <EMBED src=\"";
		$swfObjEnd=" quality=high bgcolor=#FFFFFF  NAME='Lesson' TYPE='application/x-shockwave-flash' PLUGINSPAGE='http://www.macromedia.com/go/getflashplayer'></EMBED>";
		$content = str_replace("{SWF}","{SWF}$lessondir/",$content);
		$content = str_replace("{swf}","{swf}$lessondir/",$content);
		$content = preg_replace("/{swf}(.*?){\/swf}/si", "$swfObjBegin1\\1\"$swfObjBegin2\\1\"$swfObjEnd", $content) ;
		$content = preg_replace("/{SWF}(.*?){\/SWF}/si", "$swfObjBegin1\\1\"$swfObjBegin2\\1\"$swfObjEnd", $content);
		

		//- -  WMV
		$wmvObjBegin="<object id=MediaPlayer type=application/x-oleobject height=252 width=320 classid=CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6><param name=\"URL\" value=\"";
		$wmvObjEnd='"><param name="AutoStart" value="true"><param name="ShowControls" value="false"><param name="ShowStatusBar" value="true"><param name="AutoSize" value="ture"><param name="uiMode" value="mini"></object>';
		$content = str_replace("{WMV}","{WMV}$lessondir/",$content);
		$content = str_replace("{wmv}","{wmv}$lessondir/",$content);
		$content = preg_replace("/{wmv}(.*?){\/wmv}/si", "$wmvObjBegin\\1\"$wmvObjEnd", $content) ;
		$content = preg_replace("/{WMV}(.*?){\/WMV}/si", "$wmvObjBegin\\1\"$wmvObjEnd", $content) ;

		/* break page */
		$contents = explode(_LNBREAKPAGE1,$content);
		if (count($contents) <= 1) {
			$contents = explode(_LNBREAKPAGE2,$content);
		}
		if (count($contents) <= 1) {
			$contents = explode(_LNBREAKPAGE3,$content);
		}

		if (empty($page)) $page=1;
		$totalpages=count($contents);

		if ($totalpages > 1 && $page != 1) {
			$back=$page-1;
			$pages .= '<A HREF="index.php?mod=Courses&amp;op=showcontent&amp;cid='.$cid.'&amp;lid='.$lid.'&amp;eid='.$eid.'&amp;page='.$back.'">'
			.'<IMG SRC="images/button/previous.gif" WIDTH="28" HEIGHT="25" BORDER="0" ALT="" align="absmiddle"></A>&nbsp;';
		}

		if ($totalpages > 1 && $totalpages != $page) {
			$next=$page+1;
			if ($page != 1) $pages .= $page.' of '.count($contents);
			$pages .= '&nbsp;<A HREF="index.php?mod=Courses&amp;op=showcontent&amp;cid='.$cid.'&amp;lid='.$lid.'&amp;eid='.$eid.'&amp;page='.$next.'">'
			.'<IMG SRC="images/button/next.gif" WIDTH="28" HEIGHT="25" BORDER="0" ALT="" align="absmiddle"></A>&nbsp;';
		}

		if (!empty($eid)) {
			/////////////////////////////////////	/////////////////////////////////////	/////////////////////////////////////	/////////////////////////////////////	/////////////////////////////////////

			courseTracking($eid,$lid,$page);
			$no = $lessoninfo['no'];
			lnUpdateUserEvent("Reading  $courseinfo[code]  Course: $cid       Chapter:       $no        page:        $page ");
			///////////////////////////////////////ตัวส่ง event////////////////////////////////////////////////////
		}

		/* show document */
		echo '<div align="right">'.@$pages.'</div>';

		//** show content **//
		echo $contents[$page-1];
		//** show content **//
			
		// show next page
		echo '<HR size=1 color=#EEEEEE><div align="center">';
		showPageMenu($contents,$page,$vars);
		echo '</div><BR>';
	}

	// PDF extension
	else if (strtolower($ext) == 'pdf') {
		//$pdfObj="<CENTER><OBJECT id='Acrobat Control for ActiveX' height=550 width=100% border=1 classid=CLSID:CA8A9780-280D-11CF-A24D-444553540000><PARAM NAME='_Version' VALUE='327680'><PARAM NAME='_ExtentX' VALUE='18812'><PARAM NAME='_ExtentY' VALUE='14552'><PARAM NAME='_StockProps' VALUE='0'><PARAM NAME='SRC' VALUE=\"".$lessonfile."\"></OBJECT></CENTER>";
		$pdfObj= '<CENTER><object data="'.$lessonfile.'" type="application/pdf" width="100%" height="550"></object></CENTER>';
		echo $pdfObj;
		if (!empty($eid)) {
			courseTracking($eid,$lid,1);
			$no = $lessoninfo['no'];
			lnUpdateUserEvent("Reading  $courseinfo[code]  Course: $cid      Chapter:      $no  ");
		}
	}

	// Image extension
	else if (strtolower($ext) == 'jpg' || strtolower($ext) == 'gif' || strtolower($ext) == 'png') {

		$imageObj="<CENTER><img src=\"".$lessonfile."\" border=0></OBJECT></CENTER>";
		echo $imageObj;


		if (!empty($eid)) {
			courseTracking($eid,$lid,1);
			$no = $lessoninfo['no'];
			lnUpdateUserEvent("Reading  $courseinfo[code]  Course  $cid  Chapter:     $no  ");
		}
	}
	else if(strtolower($ext) == 'mp4'||strtolower($ext) == 'flv')
	{
		echo '<CENTER><BR><BR>';
		echo '<TABLE cellpadding=0 cellspacing=10 border=0 bgcolor="#CCCCCC">';
		echo '<TR>';
		echo '<TD bgcolor="#FFFFFF">';

		/*echo '<OBJECT CLASSID="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" CODEBASE="http://www.apple.com/qtactivex/qtplugin.cab" WIDTH="160"  HEIGHT="136" >
		 <PARAM NAME="src" VALUE="'.$lessonfile.'" >
		 <PARAM NAME="autoplay" VALUE="true" >
		 <EMBED SRC="'.$lessonfile.'" TYPE="image/x-macpaint"
		 PLUGINSPAGE="http://www.apple.com/quicktime/download" WIDTH="160" HEIGHT="136"
		 </OBJECT>';*/
		echo '<embed src="includes/flvplayer.swf" width="480" height="384" allowfullscreen="true" allowscriptaccess="always" flashvars="&displayheight=360&file='.$lessonmp4f.'&height=384&width=480&overstretch=fit&autostart=true" />';
		echo '</TD>';
		echo '</TR>';
		echo '</TABLE>';
		echo '<BR><BR>';
		echo '<FONT face="ms sans serif" SIZE="1"><B><A HREF="'.$lessonfile.'" target="_blank">Click here to open new window</A></B></FONT>';
		echo '</CENTER>';

		if (!empty($eid)) {
			courseTracking($eid,$lid,1);
			$no = $lessoninfo['no'];
			lnUpdateUserEvent("Reading  $courseinfo[code]  Course: $cid      Chapter:      $no  ");
		}

	}
	
	else if(strtolower($ext) == 'mkv')
	{
		echo '<CENTER><BR><BR>';
		echo '<TABLE cellpadding=0 cellspacing=10 border=0 bgcolor="#CCCCCC">';
		echo '<TR>';
		echo '<TD bgcolor="#FFFFFF">';

		echo '<video src="'.$lessonfile.'" width="704" height="396" controls="controls" preload="none">
			your browser does not support the video tag
			</video>';
		echo '</TD>';
		echo '</TR>';
		echo '</TABLE>';
		echo '<BR><BR>';
		echo '<FONT face="ms sans serif" SIZE="1"><B><A HREF="'.$lessonfile.'" target="_blank">Click here to open new window</A></B></FONT>';
		echo '</CENTER>';

		if (!empty($eid)) {
			courseTracking($eid,$lid,1);
			$no = $lessoninfo['no'];
			lnUpdateUserEvent("Reading  $courseinfo[code]  Course: $cid      Chapter:      $no  ");
		}

	}
	// windown movie extension show files in list
	else if (strtolower($ext) == 'wmv' || strtolower($ext) == 'wma' || strtolower($ext) == 'asf' || strtolower($ext) == 'mpg' || strtolower($ext) == 'mp3' || strtolower($ext) == 'wav' ) {

		?>
<!-- Check Media Player Version -->

<CENTER><BR>
<BR>
<TABLE cellpadding=0 cellspacing=10 border=0 bgcolor="#CCCCCC">
	<TR>
		<TD bgcolor="#FFFFFF"><SCRIPT LANGUAGE="JavaScript">

			var WMP7;

			if ( navigator.appName != "Netscape" ){   
				 WMP7 = new ActiveXObject('WMPlayer.OCX');
			}

			// Windows Media Player 7 Code
			if ( WMP7 )
			{
				 document.write ('<OBJECT ID=MediaPlayer ');
				 document.write (' CLASSID=CLSID:6BF52A52-394A-11D3-B153-00C04F79FAA6');
				 document.write (' standby="Loading Microsoft Windows Media Player components..."');
				 //document.write (' TYPE="application/x-oleobject" width="320" height="290">');
				 document.write (' TYPE="application/x-oleobject" width="100%" height="100%">');
				 document.write ('PARAM NAME="stretchToFit" VALUE="true"><PARAM NAME="url" VALUE="<?=$lessonfile?>">');
				 document.write ('<PARAM NAME="AutoStart" VALUE="true">');
				 document.write ('<PARAM NAME="ShowControls" VALUE="1">');
				 document.write ('<PARAM NAME="uiMode" VALUE="mini">');
				 document.write ('</OBJECT>');
			}

			// Windows Media Player 6.4 Code
			else
			{
				 //IE Code
				 document.write ('<OBJECT ID=MediaPlayer ');
				 document.write ('CLASSID=CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95 ');
				 docutent.write ('CODEBASE=http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=6,4,5,715 ');
				 document.write ('standby="Loading Microsoft Windows Media Player components..." ');
				 document.write ('TYPE="application/x-oleobject">');
				 document.write ('<PARAM NAME="FileName" VALUE="<?=$lessonfile?>">');
				 document.write ('<PARAM NAME="AutoStart" VALUE="true">');
				 document.write ('<PARAM NAME="ShowControls" VALUE="1">');

				 //Netscape code
				 document.write ('    <Embed type="application/x-mplayer2"');
				 document.write ('        pluginspage="http://www.microsoft.com/windows/windowsmedia/"');
				 document.write ('        filename="<?=$lessonfile?>"');
				 document.write ('        src="<?=$lessonfile?>"');
				 document.write ('        Name=MediaPlayer');
				 document.write ('        ShowControls=1');
				 document.write ('        ShowDisplay=1');
				 document.write ('        ShowStatusBar=1');
				 document.write ('        ');
				 document.write ('        >');
				 document.write ('    </embed>');

				 document.write ('</OBJECT>');
			}

			</SCRIPT></TD>
	</TR>
</TABLE>
<BR>
<BR>
<FONT face="ms sans serif" SIZE="1"><B><A HREF="<?=$lessonfile?>"
	target="_blank">Click here to open new window</A></B></FONT></CENTER>

		<?
		if (!empty($eid)) {

			courseTracking($eid,$lid,1);
			$no = $lessoninfo['no'];
			lnUpdateUserEvent("Reading  $courseinfo[code]  Course: $cid       Chapter:        $no  ");
		}
	}
	/////////////////////////////////////////////////////เพิ่มการแสดงคะแนนใน hotpotatoes////////////////////////////////////////////////////////////////////////////////////////////
	// others format
	else{
		
		//	courseTracking($eid,$lid,1);
		if($lessoninfo['type'] == '2'){

			if (!empty($eid)) {
				courseTracking($eid,$lid,1);
				$no = $lessoninfo['no'];
				lnUpdateUserEvent("Reading  $courseinfo[code]  Course: $cid     Chapter:      $no   Quiz  Hotpotatoes  ");
			}
		}
		if($lessoninfo['type'] != '2'){

			if (!empty($eid)) {
				courseTracking($eid,$lid,1);
				$no = $lessoninfo['no'];
				lnUpdateUserEvent("Reading  $courseinfo[code]  Course: $cid       Chapter:        $no  ");
			}
		}

		//header("location:$config[homeurl]$lessonfile", false);
		echo '<iframe src="'.$config[homeurl].$lessonfile.'" frameborder="0" width="100%"></iframe>';
	}


	//**********************  if this is assignment ****************************//

	if($lessoninfo['type'] == '3')
	{
		if ($action=='')
		{
			list($dbconn) = lnDBGetConn();
			$lntable = lnDBGetTables();

			$assignmenttable = $lntable['assignment'];
			$assignmentcolumn = &$lntable['assignment_column'];

			$query = "SELECT $assignmentcolumn[status]
		FROM $assignmenttable WHERE $assignmentcolumn[eid] = $eid AND $assignmentcolumn[lid] = $lid";
			$result = $dbconn->Execute($query);
			list($status) = $result->fields;
			//echo $status;

			if ($status=='')
			{
				//echo "action blank";
				$action = 'start';
			}
			else
			{
				//echo "action edit";
				$action = 'preeditAssignment';
			}
		}

		if($action=='addAssignment')
		{

			$uploaddir = ASSIGNMENT_DIR."/".$instructor_name."/".$cid."(".$submission_date.")/".$lesson_weight."_".$lesson_lid_parent."/";
			//$uploaddir = ASSIGNMENT_DIR."/".$instructor_name."/".$course_code."(".$course_date.")/".$lesson_title."/";

			@mkdir($uploaddir,0777,R);

			$uploaddir = ASSIGNMENT_DIR."/".$instructor_name."/".$cid."(".$submission_date.")/".$lesson_weight."_".$lesson_lid_parent."/";
			//$uploaddir = ASSIGNMENT_DIR."/".$instructor_name."/".$course_code."(".$course_date.")/".$lesson_title."/";

			$upfile = $_FILES['assignmentfile']['name'];

			$upfile = pathinfo($upfile);
			//echo $upfile['extension']."      ";

			//save file which change a file name into  userName_assignID
			$uploadfile = $uploaddir . $std_name ."_assignID" . $lid . "." . $upfile['extension'];


			if (move_uploaded_file($_FILES['assignmentfile']['tmp_name'], $uploadfile)) {

				list($dbconn) = lnDBGetConn();
				$lntable = lnDBGetTables();

				$assignmenttable = $lntable['assignment'];
				$assignmentcolumn = &$lntable['assignment_column'];

				$eid = lnGetEnroll($cid);
				$upfile2 = $std_name ."_assignID" . $lid . "." . $upfile['extension'];
				$status = '0';

				$time=time();

				$query = "INSERT INTO $assignmenttable
			($assignmentcolumn[eid],
			$assignmentcolumn[lid],
			$assignmentcolumn[file],
			$assignmentcolumn[status],
			$assignmentcolumn[date_sent]
			)
			VALUES ('" . lnVarPrepForStore($eid) . "',
						  '" . lnVarPrepForStore($lid) . "',
						  '" . lnVarPrepForStore($upfile2) . "',
						  " . $status . ",
						  ".$time."
					  )";

			$dbconn->Execute($query);
			if ($dbconn->ErrorNo() != 0) {
				echo "error";
				return false;
			}

			$query2 = "SELECT $assignmentcolumn[file] FROM $assignmenttable WHERE $assignmentcolumn[eid]=$eid AND $assignmentcolumn[lid]= $lid";
			$result2 = $dbconn->Execute($query2);
			list ($file_name) = $result2->fields;
			//echo $file_name;

			$enroll = lnEnrollGetVars($eid);

			$submission_info = lnSubmissionGetVars($sid);
			$submission_date = $submission_info['start'];

			$instructor_info = lnUserGetVars($enroll['mentor']);
			$instructor_name = $instructor_info['uname'];

			$std_info = lnUserGetVars($enroll['uid']);
			$std_name = $std_info['uname'];
			$std_no = $std_info['uno'];

			$lesson_info = lnLessonGetVars($lid);
			$lesson_weight = $lesson_info['weight'];
			$lesson_lid_parent = $lesson_info['lid_parent'];

			$file_dir = ASSIGNMENT_DIR."/".$instructor_name."/".$cid."(".$submission_date.")/".$lesson_weight."_".$lesson_lid_parent."/";
			//$file_dir = ASSIGNMENT_DIR."/".$instructor_name."/".$course_code."(".$course_date.")/".$lesson_title."/";

			echo '<fieldset><legend>'._SEND_ASSIGNMENT.'</legend>';
			echo '<div>';
			echo _YOUSENDCOMPLETE;
			echo '<br><br><a href="index.php?mod=Courses&amp;op=showcontent&amp;action=editAssignment&amp;lid='.$lid.'&amp;cid='.$cid.'&amp;uid='.$uid.'&amp;sid='.$sid.'&amp;eid='.$eid.'">';
			echo '<img src="images/global/edit.gif" BORDER=0>';
			echo '</a>';
			echo _EDITASS;
			echo '<br><a href="'.$file_dir.$file_name.'">';
			echo '<img src="images/global/view.gif" BORDER=0>';
			echo '</a>';
			echo  _PREVIEWASS;
			echo '</div>';
			echo '</fieldset>';

			} else {
				echo '<fieldset><legend>'._SEND_ASSIGNMENT.'</legend>';
				echo '<div>';
				echo _SENDASSFAIL;
				echo '<br></div>';
				echo '</fieldset>';
			}

		}// end if addAssignment


		if ($action=='preeditAssignment')
		{

			list($dbconn) = lnDBGetConn();
			$lntable = lnDBGetTables();

			$assignmenttable = $lntable['assignment'];
			$assignmentcolumn = &$lntable['assignment_column'];

			$query = "SELECT $assignmentcolumn[file],$assignmentcolumn[score]  FROM $assignmenttable WHERE $assignmentcolumn[eid]=$eid AND $assignmentcolumn[lid]= $lid";
			$result = $dbconn->Execute($query);
			list ($file_name,$ass_score) = $result->fields;

			$enroll = lnEnrollGetVars($eid);

			$instructor_info = lnUserGetVars($enroll['mentor']);
			$instructor_name = $instructor_info['uname'];

			$std_info = lnUserGetVars($enroll['uid']);
			$std_name = $std_info['uname'];
			$std_no = $std_info['uno'];

			$lesson_info = lnLessonGetVars($lid);
			$lesson_weight = $lesson_info['weight'];
			$lesson_lid_parent = $lesson_info['lid_parent'];

			$submission_info = lnSubmissionGetVars($sid);
			$submission_date = $submission_info['start'];

			$file_dir = ASSIGNMENT_DIR."/".$instructor_name."/".$cid."(".$submission_date.")/".$lesson_weight."_".$lesson_lid_parent."/";
			//$file_dir = ASSIGNMENT_DIR."/".$instructor_name."/".$course_code."(".$course_date.")/".$lesson_title."/";



			echo '<fieldset><legend>'._SEND_ASSIGNMENT.'</legend>';
			echo '<div>';
			echo _YOUSENDCOMPLETE;
			if($ass_score!=NULL)
			{
				echo '<br><FONT SIZE="1" COLOR="#336600">'._ANNOUNCESCORE.$ass_score.'%</FONT>';
			}
			else
			{
				echo '<br><br><a href="index.php?mod=Courses&amp;op=showcontent&amp;action=editAssignment&amp;lid='.$lid.'&amp;cid='.$cid.'&amp;uid='.$uid.'&amp;sid='.$sid.'&amp;eid='.$eid.'">';
				echo '<img src="images/global/edit.gif" BORDER=0>';
				echo '</a>';
				echo _EDITASS;
			}
			echo '<br><a href="'.$file_dir.$file_name.'">';
			echo '<img src="images/global/view.gif" BORDER=0>';
			echo '</a>';
			echo  _PREVIEWASS;
			echo '</div>';
			echo '</fieldset>';
		}



		if($action=='editAssignment')
		{

			if($type=='3') // tell us known that have already edit
			{


				$uploaddir = ASSIGNMENT_DIR."/".$instructor_name."/".$cid."(".$submission_date.")/".$lesson_weight."_".$lesson_lid_parent."/";
				//$uploaddir = ASSIGNMENT_DIR."/".$instructor_name."/".$course_code."(".$course_date.")/".$lesson_title."/";
				@mkdir($uploaddir,0777,R);

				$uploaddir = ASSIGNMENT_DIR."/".$instructor_name."/".$cid."(".$submission_date.")/".$lesson_weight."_".$lesson_lid_parent."/";
				//$uploaddir = ASSIGNMENT_DIR."/".$instructor_name."/".$course_code."(".$course_date.")/".$lesson_title."/";

				$upfile = $_FILES['assignmentfile']['name'];
				$upfile = pathinfo($upfile);

				//save file which change a file name into  userName_assignID
				$uploadfile = $uploaddir . $std_name ."_assignID" . $lid . "." . $upfile['extension'];

				if (move_uploaded_file($_FILES['assignmentfile']['tmp_name'], $uploadfile)) {

					list($dbconn) = lnDBGetConn();
					$lntable = lnDBGetTables();

					$assignmenttable = $lntable['assignment'];
					$assignmentcolumn = &$lntable['assignment_column'];

					$eid = lnGetEnroll($cid);
					$upfile2 = $std_name ."_assignID" . $lid . "." . $upfile['extension'];
					$status = '0';
					$time=time();

					$query = "UPDATE $assignmenttable SET $assignmentcolumn[file]='$upfile2', $assignmentcolumn[date_sent]='$time'  WHERE $assignmentcolumn[eid]='$eid' AND $assignmentcolumn[lid]='$lid'";
					$result = $dbconn->Execute($query);

					$dbconn->Execute($query);
					if ($dbconn->ErrorNo() != 0) {
						echo "error";
						return false;
					}

					$query2 = "SELECT $assignmentcolumn[file] FROM $assignmenttable WHERE $assignmentcolumn[eid]=$eid AND $assignmentcolumn[lid]= $lid";
					$result2 = $dbconn->Execute($query2);
					list ($file_name) = $result2->fields;

					$enroll = lnEnrollGetVars($eid);

					$instructor_info = lnUserGetVars($enroll['mentor']);
					$instructor_name = $instructor_info['uname'];

					$std_info = lnUserGetVars($enroll['uid']);
					$std_name = $std_info['uname'];
					$std_no = $std_info['uno'];

					$lesson_info = lnLessonGetVars($lid);
					$lesson_title = $lesson_info['title'];

					$submission_info = lnSubmissionGetVars($sid);
					$submission_date = $submission_info['start'];

					$file_dir = ASSIGNMENT_DIR."/".$instructor_name."/".$cid."(".$submission_date.")/".$lesson_weight."_".$lesson_lid_parent."/";
					//$file_dir = ASSIGNMENT_DIR."/".$instructor_name."/".$course_code."(".$course_date.")/".$lesson_title."/";

					echo '<fieldset><legend>'._SEND_ASSIGNMENT.'</legend>';
					echo '<div>';
					echo _EDITASSCOMPLETE;
					echo '<br><br><a href="index.php?mod=Courses&amp;op=showcontent&amp;action=editAssignment&amp;lid='.$lid.'&amp;cid='.$cid.'&amp;sid='.$sid.'&amp;uid='.$uid.'&amp;eid='.$eid.'">';
					echo '<img src="images/global/edit.gif" BORDER=0>';
					echo '</a>';
					echo _EDITASS;
					echo '<br><a href="'.$file_dir.$file_name.'">';
					echo '<img src="images/global/view.gif" BORDER=0>';
					echo '</a>';
					echo  _PREVIEWASS;
					echo '</div>';
					echo '</fieldset>';

				} else {
					echo '<fieldset><legend>'._SEND_ASSIGNMENT.'</legend>';
					echo '<div>';
					echo _EDITASSFAIL;
					echo '</div>';
					echo '</fieldset>';
				}

			}//end type=3
			else
			{

				$enroll = lnEnrollGetVars($eid);

				$instructor_info = lnUserGetVars($enroll['mentor']);
				$instructor_name = $instructor_info['uname'];

				$std_info = lnUserGetVars($enroll['uid']);
				$std_name = $std_info['uname'];
				$std_no = $std_info['uno'];

				$lesson_info = lnLessonGetVars($lid);
				$lesson_weight = $lesson_info['weight'];
				$lesson_lid_parent = $lesson_info['lid_parent'];

				$submission_info = lnSubmissionGetVars($sid);
				$submission_date = $submission_info['start'];

				echo '<fieldset><legend>'._EDITASS.'</legend>';  //lesson frame
				echo '<TABLE WIDTH="98%" CELLPADDING="2" CELLSPACING=0 BORDER=0">';
				echo '<form enctype="multipart/form-data" action="index.php" method="post">';
				echo '<input type="hidden" name="MAX_FILE_SIZE" value="30000">';
				echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">';
				echo '<INPUT TYPE="hidden" NAME="op" VALUE="showcontent">';
				echo '<INPUT TYPE="hidden" NAME="action" VALUE="editAssignment">';
				echo '<INPUT TYPE="hidden" NAME="type" VALUE="3">';
				echo '<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">';
				echo '<INPUT TYPE="hidden" NAME="instructor_name" VALUE="'.$instructor_name.'">';
				echo '<INPUT TYPE="hidden" NAME="lesson_weight" VALUE="'.$lesson_weight.'">';
				echo '<INPUT TYPE="hidden" NAME="submission_date" VALUE="'.$submission_date.'">';
				echo '<INPUT TYPE="hidden" NAME="lesson_lid_parent" VALUE="'.$lesson_lid_parent.'">';
				echo '<INPUT TYPE="hidden" NAME="std_name" VALUE="'.$std_name.'">';
				echo '<INPUT TYPE="hidden" NAME="std_no" VALUE="'.$std_no.'">';
				echo '<INPUT TYPE="hidden" NAME="lesson_title" VALUE="'.$lesson_title.'">';
				echo '<INPUT TYPE="hidden" NAME="sid" VALUE="'.$sid.'">';
				echo '<INPUT TYPE="hidden" NAME="lid" VALUE="'.$lid.'">';
				echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.MAX_FILESIZE.'">';
				echo '<tr><td width=90>'._ASSIGNMENTFILESEND.'</td><td>';
				echo '<input name="assignmentfile" type="file" style="width:90%" size="30"></td>';
				echo ' <tr><td></td><td><input class="button" type="submit" value="Send File"></td></tr>';
				echo '</form>';
				echo '</TABLE>';
				echo '</fieldset>';

			}


		} // end action = editAssignment


		/*send assignment button*/
		if($action=='start')
		{
			echo '<div align="center">';
			echo '<form enctype="multipart/form-data" action="index.php" method="post">';
			echo '<INPUT TYPE="hidden" NAME="op" VALUE="showcontent">';
			echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">';
			echo '<INPUT TYPE="hidden" NAME="lessoninfo[type]" VALUE="3">';
			echo '<INPUT TYPE="hidden" NAME="action" VALUE="uploadAssignment">';
			echo '<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">';
			echo '<INPUT TYPE="hidden" NAME="uid" VALUE="'.$uid.'">';
			echo '<INPUT TYPE="hidden" NAME="eid" VALUE="'.$eid.'">';
			echo '<INPUT TYPE="hidden" NAME="lid" VALUE="'.$lid.'">';
			echo '<INPUT TYPE="hidden" NAME="sid" VALUE="'.$sid.'">';
			echo ' <input class="button" type="submit" value="Send Assignment">';
			echo '</form>';
			echo '</div>';
		}


		if($action=='uploadAssignment')
		{

			$enroll = lnEnrollGetVars($eid);

			$lesson_info = lnLessonGetVars($lid);
			$lesson_weight = $lesson_info['weight'];
			$lesson_lid_parent = $lesson_info['lid_parent'];

			$instructor_info = lnUserGetVars($enroll['mentor']);
			$instructor_name = $instructor_info['uname'];

			$std_info = lnUserGetVars($enroll['uid']);
			$std_name = $std_info['uname'];
			$std_no = $std_info['uno'];

			$submission_info = lnSubmissionGetVars($sid);
			$submission_date = $submission_info['start'];

			echo '<fieldset><legend>'._SEND_ASSIGNMENT.'</legend>';  //lesson frame
			echo '<TABLE WIDTH="98%" CELLPADDING="2" CELLSPACING=0 BORDER=0">';
			echo '<form enctype="multipart/form-data" action="index.php" method="post">';
			echo '<input type="hidden" name="MAX_FILE_SIZE" value="30000">';
			echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">';
			echo '<INPUT TYPE="hidden" NAME="op" VALUE="showcontent">';
			echo '<INPUT TYPE="hidden" NAME="action" VALUE="addAssignment">';
			echo '<INPUT TYPE="hidden" NAME="type" VALUE="3">';
			echo '<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">';
			echo '<INPUT TYPE="hidden" NAME="instructor_name" VALUE="'.$instructor_name.'">';
			echo '<INPUT TYPE="hidden" NAME="lesson_weight" VALUE="'.$lesson_weight.'">';
			echo '<INPUT TYPE="hidden" NAME="submission_date" VALUE="'.$submission_date.'">';
			echo '<INPUT TYPE="hidden" NAME="lesson_lid_parent" VALUE="'.$lesson_lid_parent.'">';
			echo '<INPUT TYPE="hidden" NAME="std_name" VALUE="'.$std_name.'">';
			echo '<INPUT TYPE="hidden" NAME="std_no" VALUE="'.$std_no.'">';
			echo '<INPUT TYPE="hidden" NAME="lesson_title" VALUE="'.$lesson_title.'">';
			echo '<INPUT TYPE="hidden" NAME="lid" VALUE="'.$lid.'">';
			echo '<INPUT TYPE="hidden" NAME="sid" VALUE="'.$sid.'">';
			echo '<input type="hidden" name="MAX_FILE_SIZE" value="'.MAX_FILESIZE.'">';
			echo '<tr><td width=90>'._ASSIGNMENTFILESEND.'</td><td>';
			echo '<input name="assignmentfile" type="file" style="width:90%" size="30"></td>';
			echo ' <tr><td></td><td><input class="button" type="submit" value="Send File"></td></tr>';
			echo '</form>';
			echo '</TABLE>';
			echo '</fieldset>';
		}

	} // end if $lessoninfo['type'] ==3


}// end function



/**
 * course tracking
 */
function courseTracking($eid,$lid,$page) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];

	$activetime = time();
	$userIP = getenv("REMOTE_ADDR");

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$query = "SELECT  $lessonscolumn[weight]
	FROM $lessonstable
	WHERE $lessonscolumn[lid]= $lid";
	$result = $dbconn->Execute($query);

	while(list($weight) = $result->fields) {
		$result->MoveNext();
		$weights =$weight;
	}

	$query = "INSERT INTO $course_trackingtable
	($course_trackingcolumn[eid], $course_trackingcolumn[lid], $course_trackingcolumn[weight], $course_trackingcolumn[page], $course_trackingcolumn[atime],$course_trackingcolumn[ip])
	VALUES
	('".lnVarPrepForStore($eid)."' , '".lnVarPrepForStore($lid)."','$weights', '".lnVarPrepForStore($page)."', '".lnVarPrepForStore($activetime)."', '".lnVarPrepForStore($userIP)."')";

	$result = $dbconn->Execute($query);

	if ($result->EOF) {
		return false;
	}
	else {
		return true;
	}

}


/*
 * get first lid of selected course
 */
function getFirstLID($cid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$query = "SELECT  $lessonscolumn[lid]
	FROM $lessonstable
	WHERE $lessonscolumn[cid]='". lnVarPrepForStore($cid) ."'
	AND $lessonscolumn[weight] = '1'";
	$result = $dbconn->Execute($query);

	return $result->fields[0];
}



// start add gotoAnchor and gotoListAnchor Function (Programmer : Bas ,14/11/49)

function gotoAnchor($cid, $sid, $lid) {
	global $start;
	/*
	 global $manager;

	 $enroll_info = lnEnrollGetVars(lnGetEnroll($cid));
	 $start = $enroll_info['start'];


	 $lessonLinkList = $manager->getDisplayLessonID($lid);
	 gotoListAnchor($cid,$sid,$lid,0,$orderings=array(),$lessonLinkList );
	 */
	$enroll_info = lnEnrollGetVars(lnGetEnroll($cid));
	$start = $enroll_info['start'];

	gotoListAnchor($cid,$sid,$lid,0,$orderings=array());


}


function  gotoListAnchor($cid,$sid,$lid,$lid_parent,$orderings) {
	global $start;

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$query = "SELECT  $lessonscolumn[lid],
	$lessonscolumn[title],
	$lessonscolumn[description],
	$lessonscolumn[file],
	$lessonscolumn[duration],
	$lessonscolumn[weight],
	$lessonscolumn[lid_parent],
	$lessonscolumn[type]
	FROM $lessonstable
	WHERE $lessonscolumn[cid]='". lnVarPrepForStore($cid) ."'
	AND $lessonscolumn[lid_parent]='".$lid_parent."'
	ORDER BY $lessonscolumn[weight]";
	$result = $dbconn->Execute($query);

	$prev_lid=0;

	$uid = lnSessionGetVar('uid');
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$query1 = "SELECT $course_enrollscolumn[eid]
	FROM $course_enrollstable WHERE $course_enrollscolumn[uid] = $uid AND $course_enrollscolumn[sid] = $sid";
	$result1 = $dbconn->Execute($query1);
	list($eid) = $result1->fields;
	$eidnow = $eid;

	$eid = lnGetEnrollID($uid,$sid);

	while(list($lesson_lid,$title,$description,$file,$duration,$weight,$lid_parent,$type) = $result->fields) {
		$result->MoveNext();
		$prev_lid = $lid;
		array_push($orderings,$weight);
		$show_item=join('.',$orderings);
		for($blank='',$j=0;$j<count($orderings);$j++) $blank .= '&nbsp;';
			
		if ((Date_Calc::isPastDate2($start) && ($eid != null)) || isSpecialUsers($sid) || lnAllTime($cid)){
			//if (Date_Calc::isPastDate2($start) || isSpecialUsers($sid) || lnAllTime($cid)) {
			if ($type==1) {
				$uid = lnSessionGetVar('uid');
					
				echo "<A href=\"javascript:window.open('index.php?mod=Courses&op=lesson_show&cid=$cid&uid=$uid&lid=$lesson_lid&sid=$sid&eid=$eidnow&qid=$file ','_self')\" >$blank <u>$show_item. $title</u></A><br>";

			}
			/*
				if ($type==2) {
				$uid = lnSessionGetVar('uid');

				echo "<A href=\"javascript:window.open('index.php?mod=Courses&op=lesson_show&cid=$cid&uid=$uid&lid=$lesson_lid&sid=$sid&eid=$eidnow&qid=$file','_self')\" >$blank $show_item. $title</A><br>";
				}
				*/
			else {
				$uid = lnSessionGetVar('uid');

				echo "<A href=\"javascript:window.open('index.php?mod=Courses&op=lesson_show&uid=$uid&cid=$cid&lid=$lesson_lid&sid=$sid&eid=$eidnow&page=1','_self')\" >$blank <u>$show_item. $title</u></A><br>";

			}

		}
			
		$days_stop = Date_Calc::dateToDays2($start) + $duration -1;
		$days_next = $days_stop + 1;
		$next = Date_Calc::daysToDate2($days_next);
		$start = $next;

		gotoListAnchor($cid,$sid,$lid,$lesson_lid,$orderings);
		array_pop($orderings);

	}




}


// end of gotoAnchor and gotoListAnchor Function






/*
 * show page
 */
function showPageMenu($contents, $page,$vars) {
	// Get arguments from argument array
	extract($vars);

	$total_pages = count($contents);

	if ($total_pages > 1) {
		echo _PAGE.' :';
		$prev_page = $page - 1;
		if ( $prev_page > 0 ) {
			echo '<A HREF="index.php?mod=Courses&amp;op=showcontent&amp;cid='.$cid.'&amp;lid='.$lid.'&amp;page='.$prev_page.'">'
			.'<IMG SRC="modules/Courses/images/back.gif" WIDTH="19" HEIGHT="9" BORDER="0" ALT=Back"></A>';
		}
		for($n=1; $n <= $total_pages; $n++) {
			if ($n == $page) {
				echo "<B>$n</B> ";
			}
			else {
				echo '<A class="line" HREF="index.php?mod=Courses&amp;op=showcontent&amp;cid='.$cid.'&amp;lid='.$lid.'&amp;page='.$n.'">'.$n.'</A> ';
			}
		}

		$next_page = $page + 1;
		if ( $next_page <= $total_pages) {
			echo '<A HREF="index.php?mod=Courses&amp;op=showcontent&amp;cid='.$cid.'&amp;lid='.$lid.'&amp;page='.$next_page.'">'
			.'<IMG SRC="modules/Courses/images/next.gif" WIDTH="19" HEIGHT="9" BORDER="0" ALT="Next"></A>';
		}
	}
}

function scoreAssignment($cid,$lid)
{

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$assignmenttable = $lntable['assignment'];
	$assignmentcolumn = &$lntable['assignment_column'];

	$eid = lnGetEnroll($cid);

	$query = "SELECT $assignmentcolumn[score] FROM $assignmenttable WHERE $assignmentcolumn[eid]=$eid AND $assignmentcolumn[lid]=$lid";
	$result = $dbconn->Execute($query);
	list ($ass_score) = $result->fields;

	if($ass_score!=NULL)
	{
		$ret = $ass_score.'%';
		//$ret = ' <FONT SIZE="1" COLOR="#336600">'._ANNOUNCESCORE.$ret.' </FONT>';
		$ret = ' '._ANNOUNCESCORE.$ret.' ';
	}
	return $ret;

}


/**
 *  score historyHot
 */
function scoreHistoryHot($cid,$lid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$scorestable = $lntable['scores'];
	$scorescolumn = &$lntable['scores_column'];

	$eid = lnGetEnroll($cid);

	$query = "SELECT $scorescolumn[score]
	FROM $scorestable
	WHERE $scorescolumn[eid]='$eid' and $scorescolumn[lid]='$lid'
	ORDER BY	 $scorescolumn[quiz_time] ASC";
	$result = $dbconn->Execute($query);

	$rets = array();
	while(list($score) = $result->fields) {
		$result->MoveNext();
		$rets[]=' '.$score.'%';
	}
	$ret = join(',',$rets);

	if (!empty($ret)) {
		$lesson_info = lnLessonGetVars($lid);
		$quiz_info = lnQuizGetVars($lesson_info['file']);

		for ($score_sum=0, $i=0; $i<count($rets); $i++) {
			$score_sum+=$rets[$i];
		}
		$score = $score_sum/count($rets);

		$score = sprintf("%2.2f", $score);

		//$ret = ' <FONT SIZE="1" COLOR="#336600">คะแนนเฉลี่ย ('.$score.'%) : '.$ret.' </FONT>';
		$ret = ' คะแนนเฉลี่ย ('.$score.'%) : '.$ret.' ';
	}

	return $ret;
}

/**
 *  score history
 */
function scoreHistory($eid,$lid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$scorestable = $lntable['scores'];
	$scorescolumn = &$lntable['scores_column'];

	//$eid = lnGetEnroll($cid);

	$query = "SELECT $scorescolumn[score]
	FROM $scorestable
	WHERE $scorescolumn[eid]='$eid' and $scorescolumn[lid]='$lid'
	ORDER BY	 $scorescolumn[quiz_time] ASC";
	$result = $dbconn->Execute($query);

	$rets = array();
	while(list($score) = $result->fields) {
		$result->MoveNext();
		$rets[]=' '.$score;
	}
	$ret = join(',',$rets);

	if (!empty($ret)) {
		$lesson_info = lnLessonGetVars($lid);
		$quiz_info = lnQuizGetVars($lesson_info['file']);

		if ($quiz_info['grademethod']==_LNQUIZ_GRADE_MAX) {
			$score=max($rets);
		}

		else if ($quiz_info['grademethod']==_LNQUIZ_GRADE_AVG) {
			for ($score_sum=0, $i=0; $i<count($rets); $i++) {
				$score_sum+=$rets[$i];
			}
			$score = $score_sum/count($rets);
		}

		else if ($quiz_info['grademethod']==_LNQUIZ_GRADE_LAST) {
			$score = $rets[count($rets)-1];
		}

		//$score = sprintf("%2.2f", $score);
		$score = round($score,2);

		//$ret = ' <FONT SIZE="1" COLOR="#336600">คะแนน('.$score.'%) : '.$ret.' </FONT>';
		//$ret = ' คะแนน('.$score.'%) : '.$ret.' ';
	}

	//return $ret;
	return $score;
}


/**
 * show Schdule
 */
function courseEnroll($vars) {
	// Get arguments from argument array
	extract($vars);

	if (lnUserLoggedIn()) {
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$course_submissionstable = $lntable['course_submissions'];
		$course_submissionscolumn = &$lntable['course_submissions_column'];
		$course_enrollstable = $lntable['course_enrolls'];
		$course_enrollscolumn = &$lntable['course_enrolls_column'];

		if (!lnGetEnroll($cid)) {

			echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

			tabMenu($vars,3);

			echo '</TD></TR><TR><TD>';

			echo '<table class="main" width= 100% cellpadding=3 cellspacing=0 border=0>';
			//By Xeonkung

			$query = "SELECT $course_submissionscolumn[sid],
			$course_submissionscolumn[start],
			$course_submissionscolumn[instructor],
			$course_submissionscolumn[enroll],
			$course_submissionscolumn[amountstd],
			$course_submissionscolumn[limitstd]
			FROM $course_submissionstable
			WHERE  $course_submissionscolumn[active]='1' AND $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."'";
			$result = $dbconn->Execute($query);

			$starts1 = array();
			$starts2 = array();
			for($i=0,$m=0,$n=0; list($sid,$start,$instructor,$student,$amount,$limit) = $result->fields; $i++) {
				$result->MoveNext();
				if ($student == _LNSTUDENT_ENROLL) {
					$instructorinfo = lnUserGetVars($instructor);
					$length = lnCourseLength($cid) - 1;
					if (!Date_Calc::isPastDate2($start)) {
						$starts1[$m]['study'] = Date_Calc::dateFormat2($start, "%e %B");
						$starts1[$m]['study_end'] = Date_Calc::daysAddtoDate2($start, $length, "%e %B %Y");
						$starts1[$m]['instructor'] = $instructorinfo[uname];
						$starts1[$m]['instructor_name'] = $instructorinfo[name];
						$starts1[$m]['amount'] = $amount;			//By Xeonkung
						$starts1[$m]['limit'] = $limit;
						$starts1[$m++]['sid'] = $sid;
					}
				}
				else {
					$starts2[$n]['study'] = Date_Calc::dateFormat2($start, "%e %B %Y");
					$starts2[$n]['instructor'] = $instructorinfo[name];
					$starts2[$n]['instructor_name'] = $instructorinfo[name];
					$starts2[$n]['amount'] = $amount;
					$starts2[$n]['limit'] = $limit;
					$starts2[$n++]['sid'] = $sid;
				}
			}

			if ($m == 0 && $n==0) {
				echo '<tr><td bgcolor=#FFFFFF valign=top>';
				echo '<BR><BR><B><CENTER>'._NOSCHEDULE.'</CENTER></B>';
				echo '</td></tr></table>';
				echo '</TD></TR></TABLE>';
				return;
			}

			echo '<tr><td height=20 bgcolor=#FFFFFF valign=top><BR>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'._ENROLLDESC.'<BR><BR></td></tr>';
			echo '<tr><td bgcolor=#FFFFFF valign=top>';
			echo '<CENTER>';

			// 1 <=======
			echo '<FORM METHOD=POST ACTION="index.php">'
			.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
			.'<INPUT TYPE="hidden" NAME="file" VALUE="index">'
			.'<INPUT TYPE="hidden" NAME="op" VALUE="course_enroll_save">'
			.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">';

			echo '<table width="95%" cellpadding="3" cellspacing="1"border="0">';
			echo '<tr><td class="head" valign=middle> <IMG SRC="images/global/bl_red.gif" WIDTH="11" HEIGHT="7" BORDER=0 ALT=""> <B>'._SELECTSCHEDULE.'</B></td></tr>';
			echo '</table>';
			echo '<table width="95%" cellpadding="0" cellspacing="0" border="0">';
			echo '<tr><td height=20 width=2></td><td width=5></td>';
			echo '<td width=97% valign=top>';

			// show schedules
			if (count($starts1)) {
				$dis_sumit = 0;
				echo '<BR>&nbsp;&nbsp;<IMG SRC="images/arrow.gif" BORDER="0" ALT="" ALIGN="absmiddle"> <B><FONT COLOR="#800000">'._MUSTENROLL.'</FONT></B>';
				$checked[0] = "checked";
				echo '<BR>';
				for ($i=0; $i < count($starts1); $i++) {
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE="radio"';
					if($starts1[$i]['amount'] != 0){
						if(($starts1[$i]['limit']-$starts1[$i]['amount'])==0) {
							echo ' DISABLED="disabled"';
							$dis_sumit++;			//By Xeonkung
						}
					}
					echo ' NAME="sid" VALUE="'.$starts1[$i]['sid'].'" '.$checked[$i].'>'.$starts1[$i]['study'].' - '.$starts1[$i]['study_end'].'&nbsp;&nbsp;';
					echo ' ('.$starts1[$i]['instructor_name'].')';
					if($starts1[$i]['amount'] != 0) {
						if(($starts1[$i]['limit']-$starts1[$i]['amount'])==0) {
							echo '&nbsp;&nbsp;&nbsp;<font color="#FF0000">'._SECTIONFULL.'</font>';			//by Xeonkung
						}
					}
					echo '<BR>';
				}
			}

			if (count($starts2)) {
				echo '<BR>&nbsp;&nbsp;<IMG SRC="images/arrow.gif" BORDER="0" ALT="" ALIGN="absmiddle"> <B><FONT COLOR="#800000">'._FREEENROLL.'</FONT></B>';
				echo '<BR>';
				for ($i=0; $i < count($starts2); $i++) {
					echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE="radio"';
					if($starts2[$i]['amount'] != 0) {
						if(($starts2[$i]['limit']-$starts2[$i]['amount'])==0) {
							echo ' DISABLED="disabled"';
							$dis_sumit++;			//by Xeonkung
						}
					}
					echo ' NAME="sid" VALUE="'.$starts2[$i]['sid'].'"> '.$starts2[$i][study].'&nbsp;';
					echo ' ('.$starts2[$i]['instructor_name'].')';
					if($starts2[$i]['amount'] != 0) {
						if(($starts2[$i]['limit']-$starts2[$i]['amount'])==0) {
							echo '&nbsp;&nbsp;&nbsp;<font color="#FF0000">'._SECTIONFULL.'</font>';			//by Xeonkung
						}
					}
					echo '<BR>';
				}
			}

			echo '<BR></td></tr></table>';

			/*
			 // 2 <=======
			 echo '<table width="95%" cellpadding="3" cellspacing="1" border="0">';
			 echo '<tr><td class="head" valign="middle"> <IMG SRC="images/global/bl_red.gif" WIDTH="11" HEIGHT="7" BORDER=0 ALT=""> <B>'._ENROLLSETTING.'</B></td></tr>';
			 echo '</table>';
			 echo '<table width="95%" cellpadding="0" cellspacing="0" border="0">';
			 echo '<tr><td width="10"></td>';
			 echo '<td bgcolor=#FFFFFF width=97% valign=top>';
			 */

			echo '<BR>&nbsp;&nbsp;<INPUT TYPE="hidden" NAME="shownick" VALUE="'._LNSHOWNICKNAME.'" checked>';
			/*
			 echo '<BR>&nbsp;&nbsp;<INPUT TYPE="checkbox" NAME="news" VALUE="'._LNNOTIFY.'" checked>'._NOTIFY;
			 echo '<BR><BR>';
			 echo '</td></tr></table>';
			 */

			// 3 <=======
			echo '<table width= 95% cellpadding=0 cellspacing=0 bgcolor=#CCCCCC border=0>';
			echo '<tr><td bgcolor=#FFFFFF height=20 width=2></td><td width=5 bgcolor=#FFFFFF></td>';
			echo '<td bgcolor=#FFFFFF width=97% valign=top align=center>';
			echo '<BR>'._FINISHMSG;
			echo '<BR><BR><INPUT TYPE="submit"';
			if ((($dis_sumit == count($starts1))&&(count($starts2) == 0))||(($dis_sumit == count($starts2))&&(count($starts1) == 0))) {
				echo ' DISABLED="disabled"';			//by Xeonkung
			}
			echo ' CLASS="button_org" VALUE="'._SUBMITENROLL.'" >';
			echo '</td></tr></table>';

			echo '</FORM>';
		}
		else {
			echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

			tabMenu($vars,0);

			echo '</TD></TR><TR><TD>';

			echo '<table class="main" width= 100% cellpadding=3 cellspacing=0 border=0>';

			echo "<tr><td valign=top align=center><BR><BR><B>"._HASENROLL."</B>";
			$query = "SELECT $course_submissionscolumn[start] FROM $course_enrollstable, $course_submissionstable WHERE $course_enrollscolumn[sid]= $course_submissionscolumn[sid] and $course_enrollscolumn[uid]='". lnSessionGetVar('uid') ."' and $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."' and $course_enrollscolumn[status]='"._LNSTATUS_STUDY."'";  //define('_LNSTATUS_STUDY',1);

			$result = $dbconn->Execute($query);
			list($start) = $result->fields;
			echo '<B>'. Date_Calc::dateFormat2($start, "%e %B %Y") .'</B></td></tr>';
		}
	}
	else {
		echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

		tabMenu($vars,0);
			
		echo '</TD></TR><TR><TD>';

		echo '<table class="main" width= 100% cellpadding=3 cellspacing=0 border=0>';
		echo '<tr><td  align="center"><B>'._PLEASELOGIN.'</B></td></tr>';
	}

	echo '</table>';

	echo '</TD></TR></TABLE>';

}


/*
 * enroll save
 */
function courseEnrollSave($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	$course_submissionstable = $lntable['course_submissions'];				//by Xeonkung
	$course_submissionscolumn = &$lntable['course_submissions_column'];

	$maxeid = getMaxEID();

	$uid = lnSessionGetVar('uid');
	$options = $shownick + $news;
	$status = _LNSTATUS_STUDY;  // define('_LNSTATUS_STUDY',1);
	$schedule_info =  lnSubmissionGetVars($sid);
	$mentor = $schedule_info['instructor'];
	$enroll = $schedule_info['enroll'];
	$start = $schedule_info['start'];
	$amount = $schedule_info['amount'];
	if ($enroll != _LNSTUDENT_ENROLL && Date_Calc::isPastDate2($start)) {
		$start = date('Y-m-d');
	}

	$query = "INSERT INTO $course_enrollstable
	($course_enrollscolumn[eid],
	$course_enrollscolumn[sid],
	$course_enrollscolumn[gid],
	$course_enrollscolumn[uid],
	$course_enrollscolumn[options],
	$course_enrollscolumn[status],
	$course_enrollscolumn[mentor],
	$course_enrollscolumn[start])
	VALUES
	('".$maxeid."',
							'". lnVarPrepForStore($sid) ."',
							'0',
							'". lnVarPrepForStore($uid) ."',
							'". lnVarPrepForStore($options) ."',
							'". lnVarPrepForStore($status) ."',
							'". lnVarPrepForStore($mentor) ."',
							'". lnVarPrepForStore($start) ."'
							)";
	$result = $dbconn->Execute($query);
	//by Xeonkung
	$amount++;
	$query = "UPDATE $course_submissionstable SET
	$course_submissionscolumn[amountstd] = ". lnVarPrepForStore($amount) ."
	WHERE $course_submissionscolumn[sid] = ". lnVarPrepForStore($sid);

	$result = $dbconn->Execute($query);

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

	tabMenu($vars,0);

	echo '</TD></TR><TR><TD>';

	$info = lnCourseGetVars(lnGetCourseID($sid));
	$start  = Date_Calc::dateFormat2(lnGetStartDateSubmission($sid), "%e %b %Y");
	echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr><td height="350"  valign="top">';
	echo '<BR><BR><CENTER>';
	echo '<table border=0 width=400>';
	echo '<tr><td colspan="2" align=center><B>'._ENROLLCONFIRM.'</B><BR><BR><BR></td></tr>';
	echo '<tr><td><B>'._COURSETITLE.'</B></td><td>'.$info['title'].'</td></tr>';
	echo '<tr><td>&nbsp;</td><td>'.$start.'</td></tr>';
	echo '</table>';
	echo '</CENTER>';

	echo '</td></tr>';
	echo '</table>';
	echo '</TD></TR></TABLE>';

	// sendmessage
	$subject = _WELCOMECOURSESUBJECT.' '.$info['title'];
	$message = _WELCOMECOURSEMSG.' '.$start;
	$variables =  array ('priority'=>'1','subject'=>"$subject",'message'=>"$message",'from_uid'=>'1','to_uid'=>"$uid");
	$vars= array_merge($vars,$variables);
	sendMessage($vars);

}


/*
 * get next enroll  id
 */
function getMaxEID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	$query = "SELECT MAX($course_enrollscolumn[eid]) FROM $course_enrollstable";
	$result = $dbconn->Execute($query);
	list ($maxeid) = $result->fields;

	return $maxeid + 1;
}

function checkChoiceType($quid, $answer) {
	for ($i=0,$count=0; $i<10; $i++) {
		if ($answer & pow(2,$i)) {
			$count++;
		}
	}

	if ($count == 1) {
		return 0;
	}
	else {
		return 1;
	}
}


/**
 *	report user history
 */
function reportUserLogging($vars) {
	// Get arguments from argument array

	extract($vars);

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

	tabMenu($vars,6);

	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr><td valign="top">&nbsp;';

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$user_logtable = $lntable['user_log'];
	$user_logcolumn = &$lntable['user_log_column'];
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	$query = "SELECT  $user_logcolumn[uid], $user_logcolumn[atime], $user_logcolumn[event], $user_logcolumn[ip]
	FROM $user_logtable, $course_enrollstable
	WHERE $user_logcolumn[uid] = $course_enrollscolumn[uid] AND $user_logcolumn[uid]='". lnVarPrepForStore($uid) ."' AND  $course_enrollscolumn[sid]='". lnVarPrepForStore($sid) ."' AND $user_logcolumn[cid]='". lnVarPrepForStore($cid) ."' AND $user_logcolumn[event]  NOT  LIKE  'Log%'  ORDER BY $user_logcolumn[atime]";
	//echo $query;
	//exit();
	//NOT LIKE 'Log%'  ตรวจสอบค่าที่ไม่ได้ขึ้นต้นด้วย Logด้วย
	/*$query = "SELECT $user_logcolumn[uid], $user_logcolumn[atime], $user_logcolumn[event], $user_logcolumn[ip]
	 FROM $user_logtable WHERE  $user_logcolumn[cid]='". lnVarPrepForStore($cid) ."' AND $user_logcolumn[event] NOT LIKE 'Log%' ORDER BY $user_logcolumn[atime]";*/
	//////////////////////////////////////////////////////ให้แสดงตาม course///////////////////////////////////////////////////////////////////////////////////
	$result = $dbconn->Execute($query);
	$user = lnUserGetVars($uid);   //////////////////ต้องได้ uid มาแล้ว

	echo '<U><B>ประวัติการเรียน</U><BR><BR><IMG SRC="images/global/bul.jpg" WIDTH="10" HEIGHT="10" BORDER="0" ALT="" align="absmiddle"> '. $user['uname'].' ( '. $user['name'].')</B><BR><BR>';

	for ($i=0;list($uid,$atime,$event,$ip) = $result->fields; $i++) {
		$result->MoveNext();
		$datetime = date('d-m-Y H:i:s',$atime);
		$list .= "<tr bgcolor=#FFFFFF><td width=100 align=center>$datetime</td><td width=70 align=center>$ip</td><td>$event</td></tr>";
	}
	if ($i > 0) {
		echo '<table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#888888">';
		echo "<tr bgcolor=#CCCCCC align=center><td width=100>Datetime</td><td width=70>IP Address</td><td>Messages</td></tr>";
		echo $list;
		echo '</table>';

	}
	else {
		echo '<BR><B><CENTER> - No record. - </CENTER></B>';
	}
	$name = $user['uname'];
	$uid = $user['uid'] ;

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];

	$query = "SELECT  $coursescolumn[title]
	FROM $coursestable
	WHERE $coursescolumn[cid] =  '$cid'";
	$result = $dbconn->Execute($query);
	while(list($title) = $result->fields) {
		$result->MoveNext();
		$titles = $title;
	}

	echo '</td></tr>';

	echo '</table>';

	echo '</TD></TR></TABLE>';

}




/**
 * show student report
 */


function reportDetailShow($vars) {
	// Get arguments from argument array
	extract($vars);

	$statuss = array('learning','complete','drop');

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

	tabMenu($vars,7);

	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr><td valign="top">&nbsp;';

	global $progress;
	$progress=array();

	//create button graph per quiz in table.
	$btngraph= array();

	listProgress($cid,0);

	// show next page
	$count = count($progress);
	$pageno = 7;

	$totalpages=ceil($count/$pageno);

	if (!$from)
	$from=1;
	if (!$p)
	$p=1;

	$start=($p-1)*$pageno;
	$from = $start+1;

	$to = $from+$pageno-1;
	if ($count < $pageno) $to = $count;

	if ($totalpages > 1 && $p != 1) {
		$back=$p-1;
		$pages .= "<IMG SRC=images/back.gif WIDTH=19 HEIGHT=9 BORDER=0><a href=\"index.php?mod=Courses&op=report_detail&cid=$cid&sid=$sid&p=$back\" class=b>Previous ".$pageno."</a> |&nbsp;&nbsp; ";
	}

	$pages .= 'Chapter ' .$from .'-'. $to ;

	if ($totalpages > 1 && $totalpages != $p) {
		$next=$p+1;
		$pages .= "&nbsp;&nbsp; | <a href=\"index.php?mod=Courses&op=report_detail&cid=$cid&sid=$sid&p=$next\" class=b>Next ".$pageno."</a><IMG SRC=images/next.gif WIDTH=19 HEIGHT=9 BORDER=0>";
	}


	echo ' <div align="right">'.$pages.'&nbsp;&nbsp;</div>&nbsp; ';

	// show head
	echo '<table width="100%" cellpadding="0" cellspacing="1" border="0" bgcolor="#444444">';
	echo '<tr bgcolor="#EEF3A7" align="center" height="20">';
	echo '<td width=10 class="head"><B>No.</B></td><td  width=50 class="head"><B>'._NICKNAME.'</B></td><td class="head"><B>'._NAME.'</B></td>'.$chapters;

	for ($i=$from; $i<=$to; $i++) {
		if ($i <= $count) {
			$lesson_info = lnLessonGetVars($progress[$i-1]);
			echo  '<td colspan=2 class="head"><B>'.$lesson_info['no'].'</B><br>';

			//check quiz for add score conclusion *************
			//programmer : Bas
			/*
			 if($lesson_info['type']==1)
			 {
				echo "<a href=\"index.php?mod=Courses&op=report_detail&action=checkscores&cid=$cid&sid=$sid\"><IMG SRC=\"images/check.gif\" BORDER=0></a>";
				}
				*/
			//*************************************************

		}
		else {
			echo  '</td><td colspan=2 class="head"><B>&nbsp;</B></td>';
		}
	}

	// check progress
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	//********************** Save Score of assignment ******************************
	//Programmer : Bas

	if($action=='saveScore')
	{
		$assignmenttable = $lntable['assignment'];
		$assignmentcolumn = &$lntable['assignment_column'];

		//echo $ass_status;

		$time=time();

		$query3 = "UPDATE $assignmenttable SET $assignmentcolumn[score]='$ass_score', $assignmentcolumn[status]='$ass_status', $assignmentcolumn[date_check]='$time'  WHERE $assignmentcolumn[eid]='$eid' AND $assignmentcolumn[lid]='$lid'";
		$result3 = $dbconn->Execute($query3);

		$dbconn->Execute($query3);
	 if ($dbconn->ErrorNo() != 0) {
	 	echo "error";
	 	return false;
	 }
	}// end if action= saveScore ****************************************************



	$query = "SELECT $course_enrollscolumn[uid],$course_enrollscolumn[status]
	FROM $course_enrollstable
	WHERE $course_enrollscolumn[sid]='$sid' ";

	$result = $dbconn->Execute($query);

	for ($i=1;list($uid, $options) = $result->fields; $i++) {
		$result->MoveNext();
		if (lnCourseInstructor($sid) && (lnIsUserMentor($sid,$uid) ||  lnIsUserInstructor($sid))) {
			$user=lnUserGetVars($uid);
			echo '<tr bgcolor="#FFFFFF" height="20"><td align=center>'.$i.'</td><td> &nbsp;'.$user['uname'].'</td><td> &nbsp;'.$user['name'].'</td>';

			for($j=$from; $j<=$to; $j++) {
				$lesson_info = lnLessonGetVars($progress[$j-1]);
				/*---------------- เพิ่ม check ข้อสอบว่า type = 2 หรือเปล่า -------------------*/
				if ($lesson_info['type'] == '1'  || $lesson_info['type']=='2') {
					$score = lnGetScore($uid,$progress[$j-1]);
					array_push($btngraph, $j);
					//************ add a function to convert raw data to percentage
					//BAS
					//lid=$progress[$j-1], uid sid  cid = $lesson_info['cid']
					$quiztable = $lntable['quiz'];
					$quizcolumn = &$lntable['quiz_column'];
					$lessonstable = $lntable['lessons'];
					$lessonscolumn = &$lntable['lessons_column'];

					$eid =  lnGetEnrollID($uid,$sid);

					$querygetqid =	"SELECT $quizcolumn[qid]
					FROM $quiztable,$lessonstable
					WHERE $quizcolumn[qid]= $lessonscolumn[file]
					AND $lessonscolumn[file]='".lnVarPrepForStore($lesson_info['file'])."'
					AND $lessonscolumn[lid]='".lnVarPrepForStore($progress[$j-1])."'";
					//echo $querygetqid."<br>";
					$resultgetqid = $dbconn->Execute($querygetqid);
					list($qid) = $resultgetqid->fields;

					//$total = getTotalScore($qid);

					//$percent = ($score/$total)*100;

					//****************************************************


					if (!empty($percent)) {
						$percent = sprintf("%2.2f", $percent);
						$percent .= '%';
					}

				}
				//array_push($btngraph, $j);
				//echo "sid=".$sid;
				$status = lnGetLearningStatus($uid,$progress[$j-1],$sid);
				switch($status) {
					case _LNSTATUS_STUDY :  //define('_LNSTATUS_STUDY',1);
						$mesg = 'learning';
						$color = '#FFFF99';
						break;
					case _LNSTATUS_COMPLETE :
						$mesg = 'complete';
						$color = '#CCFFCC';
						break;
					case _LNSTATUS_DROP :
						$mesg = 'drop';
						$color = '#FF9999';
						break;
					case _LNSTATUS_FAIL :
						$mesg = 'fail';
						$color = '#99FFFF';
						break;
					default		:
						$mesg = '&nbsp;';
						$color = '#FFFFFF';
						break;
				}
				/*** type = 1 (ln2 quiz) and type = 2 (hotpotatoes quiz) *****/
				//if it is a quiz then show score
				if ($lesson_info['type']=='1' || $lesson_info['type']=='2') {
					//edit checkscore v4 by narananami
					//echo '>>>EID='.$eid;
					if($lesson_info['type']=='1'){
						$score = lnCheckScores($eid,$progress[$j-1]);
						$score = scoreHistory($eid,$progress[$j-1]);
						//recheck or examine the quiz score and show it on the screen ********************
						if ($action =='checkscores') //especially type == 1
						{
							$eid =  lnGetEnrollID($uid,$sid);
							//echo $eid;
							$score = lnCheckScores($eid,$progress[$j-1]);
						}

					}else{
						//check score hotpotatoes
						$score = lnCheckScoresHot($eid,$progress[$j-1]);
					}
					
					$mesg = $score;

					//********************************************************************************
				}//end check quiz type ( 1 or 2 )


					
				if ($lesson_info['type']=='3')
				{
					$eid =  lnGetEnrollID($uid,$sid);

					$assignmenttable = $lntable['assignment'];
					$assignmentcolumn = &$lntable['assignment_column'];

					$query1 = "SELECT $assignmentcolumn[status], $assignmentcolumn[score] FROM $assignmenttable WHERE $assignmentcolumn[eid]=$eid AND $assignmentcolumn[lid]=".$lesson_info['lid']."";
					$result1 = $dbconn->Execute($query1);
					list ($ass_status,$ass_score) = $result1->fields;
					//echo $status;
					$lid = $lesson_info['lid'];

					//if($ass_status!='' )
					//echo $ass_status;

					if($ass_status!=NULL)
					{
						$mesg = "<a href='index.php?mod=Courses&op=assessment_assign&cid=$cid&eid=$eid&sid=$sid&lid=$lid&uid=$uid'><IMG SRC=javascript/ThemeOffice/assignment.png BORDER=0></a>";
					}
					else if($status==_LNSTATUS_STUDY)
					{
						$mesg = "assignment";
					}

					if ($ass_status==1)
					{
						$mesg .= " [".$ass_score."%]";
					}

				} // end if ($lesson_info['type']=='3')

				echo '<td align="center" width=8% bgcolor='.$color.' colspan=2>'.$mesg.'</td>';

			}
			echo '</tr>';
		}
	}
	//Add Show graph and export to excel
	//Programmer by nay

	if(count($btngraph) != 0)
	{
		echo '<tr bgcolor="#FFFFFF" height="20"><td align=center colspan=3> &nbsp;</td>';
		for($j=$from; $j<=$to; $j++)
		{

			if(in_array($j, $btngraph))
			{

				$lesson_info = lnLessonGetVars($progress[$j-1]);
				$btn = "<input type=\"button\" class=\"button\" value=\"แสดงผลกราฟ\" onclick=\"window.open('index.php?mod=Courses&file=showgraphreport&cid=".$cid."&sid=".$sid."&lid=".$lesson_info['lid']."','graph','width=800,height=600,resizable=yes')\">";
				echo '<td align="center" width=8% bgcolor="#FFFFFF" colspan=2>'.$btn.'</td>';
			}else
			{
				echo '<td align="center" width=8% bgcolor="#FFFFFF" colspan=2>&nbsp;</td>';
			}
		}
	}
	echo '</tr>';
	echo '</table>';

	echo ' &nbsp;<div align="right">'.$pages.'&nbsp;&nbsp;</div>';
	//add recheck score
	echo "<input type=\"button\" class=\"button\" value=\"Export to Excel\" onclick=\"window.open('index.php?mod=Courses&file=exportexcel&cid=".$cid."&sid=".$sid."&lid=".$lesson_info['lid']."','export','width=50,height=50,resizable=yes')\">";
	echo " &nbsp;<input type=\"button\" class=\"button\" value=\"Check Score\" onclick=\"window.location.reload()\">";

	echo '</td></tr></table>';


	// end Show graph and export to excel
	echo '</TD></TR></TABLE>';
}


//To check and assessment a learner assignment
//Programmer : BAS
function assessmentAssignment($vars)
{
	extract($vars);

	$enroll = lnEnrollGetVars($eid);

	//$course_date = lnCourseDate($cid);
	/************* get course date ****************/
	$course_length = lnCourseLength($cid) - 1;
	$start = lnGetStartDateEnroll($eid);
	$from = Date_Calc::dateFormat2($start, "%e %b");
	$to = Date_Calc::daysAddtoDate2($start, $course_length, "%e %b %y");

	$course_date = $from . ' - ' . $to;
	//echo $course_date;
	/************************************************/

	$course_info = lnCourseGetVars($cid);
	$course_code = $course_info['code'];


	$enroll = lnEnrollGetVars($eid);

	$submission_info = lnSubmissionGetVars($sid);
	$submission_date = $submission_info['start'];

	$instructor_info = lnUserGetVars($enroll['mentor']);
	$instructor_name = $instructor_info['uname'];

	$std_info = lnUserGetVars($enroll['uid']);
	$std_name = $std_info['uname'];
	$std_no = $std_info['uno'];

	$lesson_info = lnLessonGetVars($lid);
	$lesson_weight = $lesson_info['weight'];
	$lesson_lid_parent = $lesson_info['lid_parent'];

	$file_dir = ASSIGNMENT_DIR."/".$instructor_name."/".$cid."(".$submission_date.")/".$lesson_weight."_".$lesson_lid_parent."/";





	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$assignmenttable = $lntable['assignment'];
	$assignmentcolumn = &$lntable['assignment_column'];

	$query = "SELECT $assignmentcolumn[date_sent], $assignmentcolumn[date_check], $assignmentcolumn[score] FROM $assignmenttable WHERE $assignmentcolumn[eid]=$eid AND $assignmentcolumn[lid]=$lid";
	$result = $dbconn->Execute($query);
	list ($date_sent,$date_check,$ass_score) = $result->fields;


	$query2 = "SELECT $assignmentcolumn[file] FROM $assignmenttable WHERE $assignmentcolumn[eid]=$eid AND $assignmentcolumn[lid]= $lid";
	$result2 = $dbconn->Execute($query2);
	list ($file_name) = $result2->fields;


	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

	tabMenu($vars,7);

	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr><td valign="top" align="center">&nbsp;';

	echo '<table width="80%"  border="0" cellspacing="1" border="0" bgcolor="#444444" cellpadding="2">';
	echo '<tr bgcolor="#EEF3A7">';
	echo '<td width="24%">';
	echo _STDFILE;
	echo '</td>';
	echo '<td width="76%">&nbsp;<a href="'.$file_dir.$file_name.'" target="_blank"><img src="images/page.gif" border="0"></a>';
	echo ' ['._LASTSTDSENT;

	$date_sent = date('Y-m-d',$date_sent);
	$date_sent2 =  Date_Calc::dateFormat2($date_sent, "%e %b %y");
	echo $date_sent2;

	echo ']';
	if($date_check!=NULL)
	{
		echo ' ['._LASTCHECK;
			
		$date_check = date('Y-m-d',$date_check);
		$date_check2 =  Date_Calc::dateFormat2($date_check, "%e %b %y");
		echo $date_check2;
			
		echo ']';
	}
	echo '</td>';
	echo '</tr>';
	echo ' <tr bgcolor="#EEF3A7">';
	echo '<td>'._ASSSCORE.'</td>';
	echo '<form name="form1" method="post" action="index.php">';
	echo '<td>';
	echo '<input type="hidden" name="action" value="saveScore">';
	echo '<input type="hidden" name="op" value="report_detail">';
	echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">';
	echo '<input type="hidden" name="cid" value="'.$cid.'">';
	echo '<input type="hidden" name="sid" value="'.$sid.'">';
	echo '<input type="hidden" name="lid" value="'.$lid.'">';
	echo '<input type="hidden" name="eid" value="'.$eid.'">';
	echo '<input type="hidden" name="ass_status" value="1">';
	echo '<input type="text" name="ass_score" size="3" VALUE="'.$ass_score.'">%';
	echo '&nbsp;&nbsp;&nbsp;<input class="button" type="submit" name="Submit" value="Submit">';
	echo '</td></form></tr></table>';




	echo '</td></tr></table>';

	echo '</TD></TR></TABLE>';


}






function listProgress($cid,$parent) {
	global $progress;

	// get lesson to array lids
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$query = "SELECT $lessonscolumn[lid]
	FROM $lessonstable
	WHERE $lessonscolumn[cid]='". lnVarPrepForStore($cid) ."' AND $lessonscolumn[lid_parent]='$parent' ORDER BY $lessonscolumn[weight]";

	$result = $dbconn->Execute($query);

	for ($i=$from;list($lid) = $result->fields; $i++) {
		$result->MoveNext();
		$progress[]=$lid;

		listProgress($cid,$lid);
	}

}


function reportShow($vars) {
	// Get arguments from argument array
	extract($vars);

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
	tabMenu($vars,6);
	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr><td valign="top">&nbsp;';



	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	if ($op2 == "update") {
		// update mentor
		foreach ($mentor_list as $u=>$v) {
			$query = "UPDATE $course_enrollstable SET $course_enrollscolumn[mentor]='$v' WHERE $course_enrollscolumn[uid]='$u' AND $course_enrollscolumn[sid]='$sid'";
			$result = $dbconn->Execute($query);
		}
		// update status
		foreach ($status_list as $u=>$v) {
			$query = "UPDATE $course_enrollstable SET $course_enrollscolumn[status]='$v' WHERE $course_enrollscolumn[uid]='$u' AND $course_enrollscolumn[sid]='$sid'";
			$result = $dbconn->Execute($query);
		}

		//>>> todo send inform mail
	}
	// table report


	echo '<table width="100%" cellpadding="1" cellspacing="1" border="0" bgcolor="#444444">';
	echo '<tr bgcolor=#EEF3A7 align=center>';
	echo '<td width=10 rowspan="2" class="head"><B>No.</B></td><td  width=50 rowspan="2" class="head"><B>'._NICKNAME.'</B></td>';

	echo '<td colspan=2 class="head"><B>no of content</B></td><td colspan=2 class="head"><B>Learning date</B></td><td width="20" rowspan="2" class="head" ><B>Total Time.</B></td>';
	// function in Incourse.php
	if ( lnIsUserInstructor($sid)) {
		echo '<td rowspan="2"  align="center" class="head"><B>Mentor</B></td>';
	}
	echo '<td colspan=4 width=50 class="head"><B>status</B></td><td width=50 rowspan="2" class="head">&nbsp;</td></tr>';
	echo '<tr bgcolor=#EEF3A7 align=center>';
	echo '<td width=40 class="head"><B>learning</B></td><td width=40 class="head"><B>chapter</B></td><td class="head"><B>start</B></td><td class="head"><B>stop</B></td><td class="head"><B>s</B></td><td class="head"><B>p</B></td><td class="head"><B>f</B></td><td class="head"><B>d</B></td></tr>';

	$query = "SELECT $course_enrollscolumn[uid],$course_enrollscolumn[options],$course_enrollscolumn[status],$course_enrollscolumn[mentor]
	FROM $course_enrollstable
	WHERE $course_enrollscolumn[sid]='$sid' ";
	$result = $dbconn->Execute($query);

	echo '<FORM METHOD=POST ACTION="index.php">';
	echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">';
	echo '<INPUT TYPE="hidden" NAME="op" VALUE="report">';
	echo '<INPUT TYPE="hidden" NAME="op2" VALUE="update">';
	echo '<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">';
	echo '<INPUT TYPE="hidden" NAME="sid" VALUE="'.$sid.'">';
	for ($i=1;list($uid, $option,$status,$mentor) = $result->fields; $i++) {
		$result->MoveNext();
		$link = '<A href="index.php?mod=User&op=profile&amp;uid='.$uid.'">';
		if (lnCourseInstructor($sid)) {
			$user=lnUserGetVars($uid);
			if (lnIsUserMentor($sid,$uid) ||  lnIsUserInstructor($sid)) {

				echo '<tr bgcolor=#FFFFFF><td align=center>'.$i.'</td><td>'.$link.$user['uname'].'</A></td>';
				//<td> '.$link.$user['name'].'</A></td>';
				$sum_lesson = lnNoOfLesson($cid);
				$learn_lesson = lnNoOfLearning($uid,$cid);
				echo '<td align="center">'.$learn_lesson.'</td><td align="center">'.$sum_lesson.'</td>';
				$learn_lesson = lnStartLesson($uid,$cid);
				list($start_lesson,$stop_lesson) = explode('-',$learn_lesson);
				echo '<td align="center">'.$start_lesson.'</td><td align="center">'.$stop_lesson.'</td>';
				$timelearn = calcTotalTimetolearn($uid,$sid,$cid);
				echo '<td align="center">'.$timelearn['D'].':'.$timelearn['H'].':'.$timelearn['M'].':'.$timelearn['S'].'</td>';

				if (lnIsUserInstructor($sid)) {
					echo '<td align="center"><SELECT NAME="mentor_list['.$uid.']">';
					echo '<OPTION VALUE="0">-</OPTION>';

					echo listInstructor($mentor);

					echo '</SELECT></td>';
				}

				$check=array();
				$check[$status]='checked';

				//echo $check[_LNSTATUS_DROP];

				//edit for separate fail and drop :: if student drop then instructor can't change everything
				//Programmer :: bas
					
				echo '<td align=center width="1"><INPUT TYPE="radio" ';
				//	if($check[_LNSTATUS_DROP]=="checked")
				//	{
				//		echo ' disabled="true" ';
				//	}
				echo 'NAME="status_list['.@$uid.']" VALUE="'._LNSTATUS_STUDY.'" '.@$check[_LNSTATUS_STUDY].'></td>';  // define('_LNSTATUS_STUDY',1);
				echo '<td align=center width="1"><INPUT TYPE="radio"';
				//			if($check[_LNSTATUS_DROP]=="checked")
				//	{
				//		echo ' disabled="true" ';
				//	}
				echo ' NAME="status_list['.@$uid.']" VALUE="'._LNSTATUS_COMPLETE.'" '.@$check[_LNSTATUS_COMPLETE].'></td>';
				echo '<td align=center width="1"><INPUT TYPE="radio"';
				//	if($check[_LNSTATUS_DROP]=="checked")
				//	{
				//		echo ' disabled="true" ';
				//	}
				echo ' NAME="status_list['.@$uid.']" VALUE="'._LNSTATUS_FAIL.'" '.@$check[_LNSTATUS_FAIL].'></td>';
				echo '<td align=center width="1"><INPUT TYPE="radio"  NAME="status_list['.$uid.']" VALUE="'._LNSTATUS_DROP.'" '.$check[_LNSTATUS_DROP].'></td>';
				@setcookie("uid",'$uid',time()+360000,"/courses/");
				echo "<td align=center width=\"50\"><INPUT TYPE=\"button\" VALUE=\"History\" class=\"button_org\"  onClick=\"javascript:window.open('index.php?mod=Courses&op=report_graph&uid=$uid&cid=$cid&sid=$sid','_self')\"></td></tr>";
				//Botton history
			}
		}
	}

	echo '</table>';

	echo '<BR><CENTER><INPUT TYPE="submit" VALUE="Submit Changes"></CENTER>';
	echo '</FORM>';
	echo '</td></tr>';
	echo '</table>';
	echo '</TD></TR></TABLE>';
}


/**
 * list teaching assistance
 */
function listTA($sid,$mentor) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];
	$course_tatable = $lntable['course_ta'];
	$course_tacolumn = &$lntable['course_ta_column'];

	$query = "SELECT $course_tacolumn[uid],$userscolumn[uname] "
	." FROM $course_tatable,$userstable WHERE $course_tacolumn[uid]=$userscolumn[uid] and $course_tacolumn[sid]='$sid' "
	." ORDER BY $course_tacolumn[uid]";
	$result = $dbconn->Execute($query);
	for ($i=1;list($uid,$uname) = $result->fields; $i++) {
		$result->MoveNext();
		if ($uid == $mentor) {
			$ret .=  '<OPTION VALUE="'.$uid.'" selected>'.$uname.'</OPTION>';
		}
		else {
			$ret .=  '<OPTION VALUE="'.$uid.'">'.$uname.'</OPTION>';
		}
	}
	return $ret;
}


/**
 * list  instructor
 */
function listInstructor($inst) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];
	$groupstable = $lntable['groups'];
	$groupscolumn = &$lntable['groups_column'];
	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];

	$query = "SELECT $group_membershipcolumn[uid],$userscolumn[uname],$userscolumn[name]
	FROM $groupstable,$group_membershiptable, $userstable
	WHERE $group_membershipcolumn[gid]=$groupscolumn[gid] and $group_membershipcolumn[uid]=$userscolumn[uid] and $groupscolumn[type]="._LNGROUP_INSTRUCTOR."
	ORDER BY $userscolumn[uname]";

	$result = $dbconn->Execute($query);

	$select_inst[$inst] = "selected";
	while(list($uid,$uname,$name) = $result->fields) {
		$result->MoveNext();
		$ret .=  '<OPTION VALUE="'.$uid.'" '.$select_inst[$uid].'>'.$uname.'</OPTION>';
	}

	return $ret;
}
/**
 * showuser
 */
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * show roster
 */
function rosterShow($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	//****************** Nara add

	$sessioninfotable = $lntable['session_info'];
	$sessioninfocolumn = $lntable['session_info_column'];

	//*************************
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	//	$sid = lnGetSubmissionID($cid);

	if (empty($sid)) {
		$sid = lnGetSubmission($cid);
	}

	$query = "SELECT $course_enrollscolumn[uid],$course_enrollscolumn[options]
	FROM $course_enrollstable
	WHERE $course_enrollscolumn[sid]='$sid' ";
	$result = $dbconn->Execute($query);

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
	tabMenu($vars,5);
	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width= 100% cellpadding=3 cellspacing=1 border=0>';
	echo '<tr><td height=350 bgcolor=#FFFFFF valign=top>';
	echo '&nbsp;<table width= 100% cellpadding=3 cellspacing=0 border=0>';
	echo '<tr bgcolor=#FFFFFF align=center><td width=10><B>No.</B></td><td  width=50><B>'._NICKNAME.'</B></td><td width=75><B>'._EMPN.'</B></td><td><B>'._NAME.'</B></td><td><B>'._EMAIL.'</B></td>
	<td><B>'._STATUS.'</B></td>
	<td width=100><B>'._SENDMESSAGE.'</B></td></tr>';
	echo '<tr bgcolor="#000000" height="1"><td colspan="7"></td></tr>';
	for ($i=1;list($uid, $option) = $result->fields; $i++) {
		$result->MoveNext();
		$user=lnUserGetVars($uid);
		$link = '<A href="index.php?mod=User&op=profile&amp;uid='.$uid.'">';

		if ($option & 1) {

			$userlog= lnSessionGetVar('uid');

			//		if ($option & 1 ||  lnSessionGetVar('uid') == $uid) {
			echo '<tr bgcolor=#FFFFFF><td align=center>'.$i.'</td><td>'.$link.$user['uname'].'</a></td><td align=center>'.$link.$user['uno'].'</A></td><td>'.$link.$user['name'].'</a></td><td align=center>'.$user['email'].'</td>
			<td><center>';

			//Check status user
			// send uid to function lnUserStatus check with _session_info table
			lnUserStatus($user['uid']);


			echo '</center></td>
			<td align=center><A HREF="index.php?mod=Private_Messages&op=post&amp;to='.$uid.'"><IMG SRC="images/global/mail2.gif" WIDTH="16" HEIGHT="15" BORDER=0 ALT="send message"></A></td></tr>';
		}
	}
	echo '<tr bgcolor="#000000" height="1"><td colspan="7"></td></tr>';
	echo '</table>';

	echo '</td></tr></table>';
	echo '</TD></TR></TABLE>';

}

/*
 show Summary Report : count a number of learning, drop, fail, pass
 Programmer : Bas
 */
/**********************************************************************************************************/

function reportSummaryShow($vars) {
	// Get arguments from argument array
	extract($vars);

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

	tabMenu($vars,8);

	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr><td valign="top">&nbsp;';


	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	$query = "SELECT COUNT($course_enrollscolumn[sid])
	FROM $course_enrollstable
	WHERE $course_enrollscolumn[sid]='$sid' ";

	$result = $dbconn->Execute($query);
	list($total) = $result->fields;


	$query = "SELECT COUNT($course_enrollscolumn[sid])
	FROM $course_enrollstable
	WHERE $course_enrollscolumn[sid]='$sid' AND $course_enrollscolumn[status]=1 ";

	$result = $dbconn->Execute($query);
	list($studying) = $result->fields;
	?>

<table width="50%" border="1" cellpadding="0" cellspacing="1"
	bordercolor="#FFFFFF" bgcolor="#669900" align="center">
	<tr>
		<td width="60%" bgcolor="#669900"><?echo _NUMALLSTUDENT;?></td>
		<td width="20%" bgcolor="#FFFFFF">
		<div align="center"><?echo $total.' ' ._PEOPLE;?></div>
		</td>
		<td width="20%" bgcolor="#FFFFFF">
		<div align="center"><?echo "<a href='index.php?mod=Courses&op=print_report&cid=".$cid."&amp;sid=".$sid."&amp;status=0'><img src=images/page.gif border=0></a>" ?></div>
		</td>
	</tr>
	<?

	$query = "SELECT COUNT($course_enrollscolumn[sid])
	FROM $course_enrollstable
	WHERE $course_enrollscolumn[sid]='$sid' AND $course_enrollscolumn[status]=1 ";

	$result = $dbconn->Execute($query);
	list($studying) = $result->fields;

	?>
	<tr>
		<td width="120" bgcolor="#669900"><?echo _NUMLEARNING;?></td>
		<td bgcolor="#FFFFFF">
		<div align="center"><?echo $studying.' '._PEOPLE;?></div>
		</td>
		<td bgcolor="#FFFFFF">
		<div align="center"><?echo "<a href='index.php?mod=Courses&op=print_report&cid=".$cid."&amp;sid=".$sid."&amp;status=1'><img src=images/page.gif border=0></a>" ?></div>
		</td>
	</tr>
	<?

	$query = "SELECT COUNT($course_enrollscolumn[sid])
	FROM $course_enrollstable
	WHERE $course_enrollscolumn[sid]='$sid' AND $course_enrollscolumn[status]=3";

	$result = $dbconn->Execute($query);
	list($drop) = $result->fields;


	?>
	<tr>
		<td width="120" bgcolor="#669900"><?echo _NUMDROP; ?></td>
		<td bgcolor="#FFFFFF">
		<div align="center"><?echo $drop.' '._PEOPLE;?></div>
		</td>
		<td bgcolor="#FFFFFF">
		<div align="center"><?echo "<a href='index.php?mod=Courses&op=print_report&cid=".$cid."&amp;sid=".$sid."&amp;status=3'><img src=images/page.gif border=0></a>" ?></div>
		</td>
	</tr>
	<?

	$query = "SELECT COUNT($course_enrollscolumn[sid])
	FROM $course_enrollstable
	WHERE $course_enrollscolumn[sid]='$sid' AND $course_enrollscolumn[status]=3";

	$result = $dbconn->Execute($query);
	list($fail) = $result->fields;


	?>
	<tr>
		<td width="120" bgcolor="#669900"><?echo _NUMFAIL; ?></td>
		<td bgcolor="#FFFFFF">
		<div align="center"><?echo $fail.' '._PEOPLE;?></div>
		</td>
		<td bgcolor="#FFFFFF">
		<div align="center"><?echo "<a href='index.php?mod=Courses&op=print_report&cid=".$cid."&amp;sid=".$sid."&amp;status=4'><img src=images/page.gif border=0></a>" ?></div>
		</td>
	</tr>
	<?

	$query = "SELECT COUNT($course_enrollscolumn[sid])
	FROM $course_enrollstable
	WHERE $course_enrollscolumn[sid]='$sid' AND $course_enrollscolumn[status]=2";

	$result = $dbconn->Execute($query);
	list($pass) = $result->fields;


	?>
	<tr>
		<td width="120" bgcolor="#669900"><?echo _NUMPASS;?></td>
		<td bgcolor="#FFFFFF">
		<div align="center"><?echo $pass.' '._PEOPLE;?></div>
		</td>
		<td bgcolor="#FFFFFF">
		<div align="center"><?echo "<a href='index.php?mod=Courses&op=print_report&cid=".$cid."&amp;sid=".$sid."&amp;status=2'><img src=images/page.gif border=0></a>" ?></div>
		</td>
	</tr>
</table>


	<?

	echo '</table>';

	echo '</td></tr></table>';

	echo '</TD></TR></TABLE>';
}

/**********************************************************************************************************/

/*
 JoeJae - Chat Online
 Programmer : JoeJae Team
 */
function JoeDate($time)
{
	return '<span style="font-family: Tahoma; font-size: 0.7em; color: #B6B6B6;">('.date('H:i:s', $time).')</span>';
}

/************ JOEJAE LOAD LOOPER ************/

function JoeJaeLoad($vars)
{
	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$JoeJae_enrollstable = $lntable['JoeJae_enrolls'];
	$JoeJae_enrollscolumn = &$lntable['JoeJae_enrolls_column'];

	$JoeJae_onlinetable = $lntable['JoeJae_onlinetable'];
	$JoeJae_onlinecolumn = &$lntable['JoeJae_online_column'];

	$uid =  lnSessionGetVar('uid');
	if(!isset($uid))
	{
		$userinfo['name'] = $_SESSION['joe_guest'];
		$uid = -1;
	}
	else
	{
		$userinfo = lnUserGetVars($uid);
		$userinfo['name'] = $userinfo['uname'];
	}

	$query = 'UPDATE '. $JoeJae_onlinetable .' SET '. $JoeJae_onlinecolumn['lastseen'] .' = "'. time() .'" WHERE '. $JoeJae_onlinecolumn['uname'] .' = "'. $userinfo['name'] .'" LIMIT 1';
	$dbconn->Execute($query) or die(mysql_error());

	$query = "SELECT $JoeJae_enrollscolumn[id] FROM $JoeJae_enrollstable WHERE $JoeJae_enrollscolumn[cid] = $vars[cid] ORDER BY `id` DESC LIMIT 1";
	$result = $dbconn->Execute($query);

	if(mysql_num_rows(mysql_query($query)) > 0)
	{
		list($joe_last_id) = $result->fields;

		if(!isset($_SESSION['joe_session']) || $_SESSION['joe_session'] == 0)
		{
			if(lnUserAdmin(lnSessionGetVar('uid')))
			{
				//แอดมินเห็นซิบของทุกคน
				$query = "SELECT $JoeJae_enrollscolumn[uid], $JoeJae_enrollscolumn[uname], $JoeJae_enrollscolumn[text], $JoeJae_enrollscolumn[wuid], $JoeJae_enrollscolumn[time], $JoeJae_enrollscolumn[ip] FROM $JoeJae_enrollstable WHERE
				$JoeJae_enrollscolumn[cid] = $vars[cid]
				ORDER BY `id` DESC LIMIT 10";
			}
			else
			{
				$query = "SELECT $JoeJae_enrollscolumn[uid], $JoeJae_enrollscolumn[uname], $JoeJae_enrollscolumn[text], $JoeJae_enrollscolumn[wuid], $JoeJae_enrollscolumn[time], $JoeJae_enrollscolumn[ip] FROM $JoeJae_enrollstable WHERE
				($JoeJae_enrollscolumn[cid] = $vars[cid] AND $JoeJae_enrollscolumn[wuid] = '$userinfo[name]') 
				OR ($JoeJae_enrollscolumn[cid] = $vars[cid] AND $JoeJae_enrollscolumn[wuid] = '') 
				OR ($JoeJae_enrollscolumn[cid] = $vars[cid] AND $JoeJae_enrollscolumn[uname] = '$userinfo[name]' AND $JoeJae_enrollscolumn[wuid] != '') 
				ORDER BY `id` DESC LIMIT 10";
			}
		}
		else if($_SESSION['joe_session'] > $joe_last_id || $_SESSION['joe_session'] < $joe_last_id)
		{
			if(lnUserAdmin(lnSessionGetVar('uid')))
			{
				//แอดมินเห็นซิบของทุกคน
				$query = "SELECT $JoeJae_enrollscolumn[uid], $JoeJae_enrollscolumn[uname], $JoeJae_enrollscolumn[text], $JoeJae_enrollscolumn[wuid], $JoeJae_enrollscolumn[time], $JoeJae_enrollscolumn[ip] FROM $JoeJae_enrollstable WHERE
				$JoeJae_enrollscolumn[id] > $_SESSION[joe_session] AND $JoeJae_enrollscolumn[cid] = $vars[cid]";
			}
			else
			{
				//echo '---@-> ' . $_SESSION['joe_session'] . ' : '. $joe_last_id . '<---<br />';
				$query = "SELECT $JoeJae_enrollscolumn[uid], $JoeJae_enrollscolumn[uname], $JoeJae_enrollscolumn[text], $JoeJae_enrollscolumn[wuid], $JoeJae_enrollscolumn[time], $JoeJae_enrollscolumn[ip] FROM $JoeJae_enrollstable WHERE
				
				($JoeJae_enrollscolumn[id] > $_SESSION[joe_session] AND $JoeJae_enrollscolumn[cid] = $vars[cid] AND $JoeJae_enrollscolumn[wuid] = '$userinfo[name]') 

				OR ($JoeJae_enrollscolumn[id] > $_SESSION[joe_session]  AND $JoeJae_enrollscolumn[cid] = $vars[cid] AND $JoeJae_enrollscolumn[wuid] = '') 
				
				OR ($JoeJae_enrollscolumn[id] > $_SESSION[joe_session]  AND $JoeJae_enrollscolumn[cid] = $vars[cid] AND $JoeJae_enrollscolumn[wuid] != '' AND $JoeJae_enrollscolumn[uname] = '$userinfo[name]')";
			}
		}
		else
		{
			$_SESSION['joe_session'] = $joe_last_id;
			die();
		}

		$_SESSION['joe_session'] = $joe_last_id;

		$result = $dbconn->Execute($query);
		$joe_temp = array();

		while(list($joe_uid, $joe_name, $joe_text, $joe_wuid, $joe_time, $joe_ip) = $result->fields)
		{
			$result->MoveNext();
			if($joe_uid > 0)
			{
				$Group_enrollstable = $lntable['group_membership'];
				$Group_enrollscolumn = &$lntable['group_membership_column'];
				$query2 = "SELECT $Group_enrollscolumn[gid] FROM $Group_enrollstable WHERE $Group_enrollscolumn[uid] = $joe_uid";
				$result2 = $dbconn->Execute($query2);
				list($joe_gid) = $result2->fields;
			}
			else //if Guest
			{
				$joe_gid = 0;
			}

			$Group_Name_enrollstable = $lntable['groups'];
			$Group_Name_enrollscolumn = &$lntable['groups_column'];

			$query3 = "SELECT $Group_Name_enrollscolumn[name] FROM $Group_Name_enrollstable WHERE $Group_Name_enrollscolumn[gid] = $joe_gid";
			$result3 = $dbconn->Execute($query3);
			list($joe_gname) = $result3->fields;

			if($joe_uid == -5 && $joe_name == $userinfo['name'])
			{
				$joe_temp[] = '<div style="cursor:crosshair;">'. JoeDate($joe_time) . $joe_name.': <font color="#C0C0C0">'. $joe_text .'</font></div>';
			}
			else if($joe_uid == -5 && $joe_name != $userinfo['name'])
			{
				$joe_temp[] = '<div style="cursor:pointer;" onclick="JoeWhis(\''. $joe_name .'\');">'. JoeDate($joe_time) . $joe_name.': <font color="#C0C0C0">'. $joe_text .'</font></div>';
			}
			else if($joe_name == $joe_wuid) //ซิบหาตัวเอง
			{
				$joe_temp[] = '<div style="cursor:crosshair;">'. JoeDate($joe_time) . $joe_name.': <font color="#C0C0C0">'. $joe_text .'</font></div>';

				//$joe_temp[] = $joe_uid.' @ '.$joe_name.' @ '.$joe_gid. ' @ '. $joe_gname .' @ '.$joe_text .'</font></div>';
			}
			else if($joe_name == $userinfo['name'] && $joe_wuid == '') //ข้อความตัวเอง
			{
				$joe_temp[] = '<div style="cursor:crosshair;">'. JoeDate($joe_time) . $joe_name.': <font color="#595959">'. $joe_text .'</font></div>';
				//$temp_text_start = '<div><i><font color="#00C100">';
				//$temp_text_end = '</font></i></div>';
				//$joe_temp[] = '<div><font color="#00C100">'.$joe_uid.' @ '.$joe_name.' @ '.$joe_gid. ' @ '. $joe_gname .' @ '.$joe_text .'</font></div>';
			}
			else if($joe_name == $userinfo['name'] && $joe_wuid != '') //ชื่อเรากระซิบ
			{
				$joe_temp[] = '<div style="cursor:pointer;" onclick="JoeWhis(\''. $joe_wuid .'\');">'. JoeDate($joe_time) . '' .$joe_name.' <font color="#FDA8B5">--&gt;</font> '.$joe_wuid.': <font color="#FF9900">'. $joe_text .'</font></div>';
				//$temp_text_start = '<div><i><font color="#FF9900">';
				//$joe_temp[] = '<div><font color="#FF9900">'.$joe_uid.' @ '.$joe_name.' @ '.$joe_gid. ' @ '. $joe_gname .' @ '.$joe_text .'</font></div>';
			}
			else if($joe_wuid != '') //คนอื่นซิบหาเรา
			{
				$joe_temp[] = '<div onclick="JoeWhis(\''. $joe_name .'\');" style="cursor:pointer;">'. JoeDate($joe_time) . $joe_name.' <font color="#76A7EF">--&gt;</font> '.$joe_wuid.': <font color="#FF9900">'. $joe_text .'</font></div>';
				//$temp_text_start = '<div onclick="JoeWhis(\''. $joe_name .'\');"><font color="#FF6B24">';
				//$temp_text_end = '</font></div>';
				//$joe_temp[] = '<div onclick="JoeWhis(\''. $joe_name .'\');"><font color="#0066FF">'.$joe_uid.' @ '.$joe_name.' @ '.$joe_gid. ' @ '. $joe_gname .' @ '.$joe_text .'</font></div>';
			}
			else //ข้อความทั่วไป
			{
				$joe_temp[] = '<div style="cursor:pointer;" onclick="JoeWhis(\''. $joe_name .'\');">'. JoeDate($joe_time) . $joe_name.': <font color="#000000">'. $joe_text .'</font></div>';
				//$temp_text_start = '<div onclick="JoeWhis(\''. $joe_name .'\');">';
				//$temp_text_end = '</div>';
				//$joe_temp[] = '<div onclick="JoeWhis(\''. $joe_name .'\');">'.$joe_uid.' @ '.$joe_name.' @ '.$joe_gid. ' @ '. $joe_gname .' @ '.$joe_text .'</div>';
			}

			//$joe_temp[] = $temp_text_start . JoeDate($joe_time) . $joe_uid.' @ '.$joe_name.' @ '.$joe_gid. ' @ '. $joe_gname .' @ '.$joe_text . $temp_text_end;

			//echo '['. $joe_gname .']' . $joe_name . ' says: ' . $joe_text;
			//echo '<br />';
		}

		$joe_temp = array_reverse($joe_temp);
		//print_r($joe_temp);

		foreach($joe_temp as $title => $value)
		{
			echo $value;
		}
	}
}

/**************!!!!!!! JOEJAE ONLINE FUNCTION !!!!!!!*****************/
function JoeOnline($vars)
{
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$JoeJae_enrollstable = $lntable['JoeJae_enrolls'];
	$JoeJae_enrollscolumn = &$lntable['JoeJae_enrolls_column'];
	$JoeJae_onlinetable = $lntable['JoeJae_onlinetable'];
	$JoeJae_onlinecolumn = &$lntable['JoeJae_online_column'];

	$uid =  lnSessionGetVar('uid');

	if(!isset($uid))
	{
		$uid = -1;

		//เข้ามาใหม่ หรือ SESSION GUEST หาย
		if(!isset($_SESSION['joe_guest']) || $_SESSION['joe_guest'] == '')
		{
			echo $_SESSION['joe_guest'];
			while(1)
			{
				$joe_temp_gen_name =  'Guest'. rand(1000,9999);
					
				$query = ' SELECT '. $JoeJae_onlinecolumn['uid'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['uname'] .' = "'. $joe_temp_gen_name .'" AND '. $JoeJae_onlinecolumn['cid'] .' = "'. $vars['cid'] .'"';

				if(mysql_num_rows(mysql_query($query)) <= 0)
				{
					//$_SESSION['joe_guest'] = $joe_temp_gen_name;
					break;
				}
			}

			//ได้ชื่อที่ไม่ซ้ำ
			$userinfo['name'] = $joe_temp_gen_name;
			$_SESSION['joe_guest'] = $joe_temp_gen_name;
			//$query = 'INSERT INTO '. $JoeJae_enrollstable .' ('.$JoeJae_enrollscolumn['uid'].', '. $JoeJae_enrollscolumn['uname'] .', '. $JoeJae_enrollscolumn['text'] .', '. $JoeJae_enrollscolumn['wuid'] .', '. $JoeJae_enrollscolumn['cid'] .', '. $JoeJae_enrollscolumn['time'] .', '. $JoeJae_enrollscolumn['ip'] .') VALUES ("-5", "'.  $userinfo['name'] .'", "ได้เข้าใช้งานห้องแล้ว,, สวัสดีจ๊ะ~*", "", "'. $vars['cid'] .'", "'. time(). '", "'. getenv('REMOTE_ADDR') .'")';
			//$dbconn->Execute($query) or die(mysql_error());
		}
		else
		$userinfo['name'] = $_SESSION['joe_guest'];
	}
	else
	{
		$userinfo = lnUserGetVars($uid);
		$userinfo['name'] = $userinfo['uname'];
	}

	/*
	 if(!isset($_SESSION['joe_incomming']) || $_SESSION['joe_incomming'] != $vars['cid'].$userinfo['name'])
	 {
		$_SESSION['joe_incomming'] = '';
		}
		*/
	//$query = mysql_query('SELECT '. $JoeJae_onlinecolumn['uname'] .', '. $JoeJae_onlinecolumn['cid'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['lastseen'] .' <= "'. (time()-60) .'" AND '. $JoeJae_onlinecolumn['uid'] .' = "'. $vars['cid']  .'"');

	$JoeJae_configtable = $lntable['JoeJae_configtable'];
	$JoeJae_config_column = &$lntable['JoeJae_config_column'];
	$disconnect_delay = JoeGetSQLConfig('disconnect_delay');

	$query = mysql_query('SELECT '. $JoeJae_onlinecolumn['uname'] .', '. $JoeJae_onlinecolumn['cid'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['lastseen'] .' <= "'. (time()-$disconnect_delay) .'"');

	//echo 'SELECT '. $JoeJae_onlinecolumn['uname'] .', '. $JoeJae_onlinecolumn['cid'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['lastseen'] .' <= "'. (time()-10) .'" AND '. $JoeJae_onlinecolumn['uid'] .' = "'. $vars['cid']  .'"';

	if(mysql_num_rows($query) > 0)
	{
		while(list($joe_temp_namez, $joe_temp_cid) = mysql_fetch_row($query))
		{
			$queryX = 'INSERT INTO '. $JoeJae_enrollstable .' ('.$JoeJae_enrollscolumn['uid'].', '. $JoeJae_enrollscolumn['uname'] .', '. $JoeJae_enrollscolumn['text'] .', '. $JoeJae_enrollscolumn['wuid'] .', '. $JoeJae_enrollscolumn['cid'] .', '. $JoeJae_enrollscolumn['time'] .', '. $JoeJae_enrollscolumn['ip'] .') VALUES ("-5", "'.  $joe_temp_namez .'", "'. _JOEJAE_SAY_EXIT .'", "", "'. $joe_temp_cid .'", "'. time(). '", "0.0.0.0")';
			$dbconn->Execute($queryX) or die(mysql_error());

			$queryX = 'DELETE FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['lastseen'] .' <= "'. (time()-$disconnect_delay) .'" AND '. $JoeJae_onlinecolumn['cid'] .' = "'. $joe_temp_cid  .'"';

			$dbconn->Execute($queryX) or die(mysql_error());
		}
	}

	if(!isset($_SESSION['joe_incomming']) || $_SESSION['joe_incomming'] != $vars['cid'].$userinfo['name'])
	{
		$query = ' SELECT '. $JoeJae_onlinecolumn['uid'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['uname'] .' = "'. $userinfo['name'] .'" AND '. $JoeJae_onlinecolumn['cid'] .' = "'. $vars['cid'] .'"';

		if(mysql_num_rows(mysql_query($query)) <= 0)
		{
			$query = 'INSERT INTO '. $JoeJae_onlinetable .' ('.$JoeJae_onlinecolumn['uid'].', '. $JoeJae_onlinecolumn['uname'] .', '. $JoeJae_onlinecolumn['cid'] .', '. $JoeJae_onlinecolumn['lastseen'] .') VALUES ("'. $uid .'", "'.  $userinfo['name'] .'", "'. $vars['cid'] .'", "'. time(). '")';
			$dbconn->Execute($query) or die(mysql_error());
			$query = 'INSERT INTO '. $JoeJae_enrollstable .' ('.$JoeJae_enrollscolumn['uid'].', '. $JoeJae_enrollscolumn['uname'] .', '. $JoeJae_enrollscolumn['text'] .', '. $JoeJae_enrollscolumn['wuid'] .', '. $JoeJae_enrollscolumn['cid'] .', '. $JoeJae_enrollscolumn['time'] .', '. $JoeJae_enrollscolumn['ip'] .') VALUES ("-5", "'.  $userinfo['name'] .'", "'. _JOEJAE_SAY_JOIN .'", "", "'. $vars['cid'] .'", "'. time(). '", "'. getenv('REMOTE_ADDR') .'")';
			$dbconn->Execute($query) or die(mysql_error());
		}

		$_SESSION['joe_incomming'] = $vars['cid'].$userinfo['name'];
	}

	$query = mysql_query(' SELECT '. $JoeJae_onlinecolumn['uid'] .', '. $JoeJae_onlinecolumn['uname'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['cid'] .' = "'. $vars['cid'] .'"');

	if(mysql_num_rows($query) <= 0)
	{
		echo '<div style="cursor:crosshair;">'. _JOEJAE_SAY_NO_ONE .'</div>';
	}
	else
	{
		while(list($joe_uid, $joe_name) = mysql_fetch_row($query))
		{
			if($userinfo['name'] != $joe_name)
			echo '<div style="cursor:pointer;" onclick="JoeWhis(\''. $joe_name .'\');">+ '. $joe_name .'</div>';
			else
			echo '<div style="cursor:crosshair;">- '. $joe_name .'</div>';
		}
	}

}

/**************!!!!!!! JOEJAE MAIN FUNCTION !!!!!!!*****************/

function JoeJae($vars)
{
	extract($vars);
	//ล้างค่า SESSION
	//$_SESSION['joe_guest'] = '';
	$_SESSION['joe_session'] = 0;

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$JoeJae_enrollstable = $lntable['JoeJae_enrolls'];
	$JoeJae_enrollscolumn = &$lntable['JoeJae_enrolls_column'];

	$uid =  lnSessionGetVar('uid');

	$JoeJae_onlinetable = $lntable['JoeJae_onlinetable'];
	$JoeJae_onlinecolumn = &$lntable['JoeJae_online_column'];

	$JoeJae_activetable = $lntable['JoeJae_activetable'];
	$JoeJae_active_column = &$lntable['JoeJae_active_column'];

	$joe_query = mysql_query('SELECT '. $JoeJae_active_column['allow_chat'] .', '. $JoeJae_active_column['allow_member'] .' FROM '. $JoeJae_activetable .' WHERE '. $JoeJae_active_column['cid'] .' = "'. $vars['cid'] .'" LIMIT 1');

	if(mysql_num_rows($joe_query) <= 0)
	{
		$temp_error = true;
	}
	else
	{
		list($joe_allow_chat, $joe_allow_member) = mysql_fetch_row($joe_query);
		if($joe_allow_chat != 1)
		{
			$temp_error = true;
		}
		else
		{
			if($joe_allow_member == 1)
			{
				if(!lnUserLoggedIn())
				{
					$temp_error = true;
				}
			}
		}
	}

	if($temp_error)
	{
		echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
		tabMenu($vars,10);
		echo '</TD></TR><TR><TD>';
		echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
		echo '<tr><td valign="top">';
		echo '<center><br /><br /><br />-- Permission Denied --</center>';
		echo '</td></tr></table>';
		echo "</td></table>";

	}
	else
	{
		//echo 'a';
		if(!isset($uid))
		{
			//echo 'b';
			$uid = -1;

			//เข้ามาใหม่ หรือ SESSION GUEST หาย
			if(!isset($_SESSION['joe_guest']) || $_SESSION['joe_guest'] == '')
			{
				//echo $_SESSION['joe_guest'];
				while(1)
				{
					$joe_temp_gen_name =  'Guest'. rand(1000,9999);

					$query = ' SELECT '. $JoeJae_onlinecolumn['uid'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['uname'] .' = "'. $joe_temp_gen_name .'" AND '. $JoeJae_onlinecolumn['cid'] .' = "'. $vars['cid'] .'"';

					if(mysql_num_rows(mysql_query($query)) <= 0)
					{
						//$_SESSION['joe_guest'] = $joe_temp_gen_name;
						break;
					}
				}

				//ได้ชื่อที่ไม่ซ้ำ
				$userinfo['name'] = $joe_temp_gen_name;
				$_SESSION['joe_guest'] = $joe_temp_gen_name;
				//$query = 'INSERT INTO '. $JoeJae_enrollstable .' ('.$JoeJae_enrollscolumn['uid'].', '. $JoeJae_enrollscolumn['uname'] .', '. $JoeJae_enrollscolumn['text'] .', '. $JoeJae_enrollscolumn['wuid'] .', '. $JoeJae_enrollscolumn['cid'] .', '. $JoeJae_enrollscolumn['time'] .', '. $JoeJae_enrollscolumn['ip'] .') VALUES ("-5", "'.  $userinfo['name'] .'", "ได้เข้าใช้งานห้องแล้ว,, สวัสดีจ๊ะ~*", "", "'. $vars['cid'] .'", "'. time(). '", "'. getenv('REMOTE_ADDR') .'")';
				//$dbconn->Execute($query) or die(mysql_error());
			}
			else
			$userinfo['name'] = $_SESSION['joe_guest'];
		}
		else
		{
			$userinfo = lnUserGetVars($uid);
			$userinfo['name'] = $userinfo['uname'];
		}

		/*
		 if(!isset($_SESSION['joe_incomming']) || $_SESSION['joe_incomming'] != $vars['cid'].$userinfo['name'])
		 {
			$_SESSION['joe_incomming'] = '';
			}
			*/

		if(!isset($_SESSION['joe_incomming']) || $_SESSION['joe_incomming'] != $vars['cid'].$userinfo['name'])
		{
			$query = ' SELECT '. $JoeJae_onlinecolumn['uid'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['uname'] .' = "'. $userinfo['name'] .'" AND '. $JoeJae_onlinecolumn['cid'] .' = "'. $vars['cid'] .'"';

			if(mysql_num_rows(mysql_query($query)) <= 0)
			{
				$query = 'INSERT INTO '. $JoeJae_onlinetable .' ('.$JoeJae_onlinecolumn['uid'].', '. $JoeJae_onlinecolumn['uname'] .', '. $JoeJae_onlinecolumn['cid'] .', '. $JoeJae_onlinecolumn['lastseen'] .') VALUES ("'. $uid .'", "'.  $userinfo['name'] .'", "'. $vars['cid'] .'", "'. time(). '")';
				$dbconn->Execute($query) or die(mysql_error());
				$query = 'INSERT INTO '. $JoeJae_enrollstable .' ('.$JoeJae_enrollscolumn['uid'].', '. $JoeJae_enrollscolumn['uname'] .', '. $JoeJae_enrollscolumn['text'] .', '. $JoeJae_enrollscolumn['wuid'] .', '. $JoeJae_enrollscolumn['cid'] .', '. $JoeJae_enrollscolumn['time'] .', '. $JoeJae_enrollscolumn['ip'] .') VALUES ("-5", "'.  $userinfo['name'] .'", "'. _JOEJAE_SAY_JOIN .'", "", "'. $vars['cid'] .'", "'. time(). '", "'. getenv('REMOTE_ADDR') .'")';
				$dbconn->Execute($query) or die(mysql_error());
			}

			$_SESSION['joe_incomming'] = $vars['cid'].$userinfo['name'];
		}
		else
		{
			$query = ' SELECT '. $JoeJae_onlinecolumn['uid'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['uname'] .' = "'. $userinfo['name'] .'" AND '. $JoeJae_onlinecolumn['cid'] .' = "'. $vars['cid'] .'"';

			if(mysql_num_rows(mysql_query($query)) <= 0)
			{
				$query = 'INSERT INTO '. $JoeJae_onlinetable .' ('.$JoeJae_onlinecolumn['uid'].', '. $JoeJae_onlinecolumn['uname'] .', '. $JoeJae_onlinecolumn['cid'] .', '. $JoeJae_onlinecolumn['lastseen'] .') VALUES ("'. $uid .'", "'.  $userinfo['name'] .'", "'. $vars['cid'] .'", "'. time(). '")';
				$dbconn->Execute($query) or die(mysql_error());
				$query = 'INSERT INTO '. $JoeJae_enrollstable .' ('.$JoeJae_enrollscolumn['uid'].', '. $JoeJae_enrollscolumn['uname'] .', '. $JoeJae_enrollscolumn['text'] .', '. $JoeJae_enrollscolumn['wuid'] .', '. $JoeJae_enrollscolumn['cid'] .', '. $JoeJae_enrollscolumn['time'] .', '. $JoeJae_enrollscolumn['ip'] .') VALUES ("-5", "'.  $userinfo['name'] .'", "'. _JOEJAE_SAY_JOIN .'", "", "'. $vars['cid'] .'", "'. time(). '", "'. getenv('REMOTE_ADDR') .'")';
				$dbconn->Execute($query) or die(mysql_error());
			}
		}

		echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

		tabMenu($vars,10);

		$JoeJae_configtable = $lntable['JoeJae_configtable'];
		$JoeJae_config_column = &$lntable['JoeJae_config_column'];
		$refresh_delay = JoeGetSQLConfig('refresh_delay');
		$userlist_delay = JoeGetSQLConfig('userlist_delay');

		echo '</TD></TR><TR><TD>';

		echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
		echo '<tr><td valign="top">';
		?>
<script language="javascript">
		var whisper = '';
		 /* ----- ฟังค์ชั่น อ่านข่านที่วนลูป ----- */
		 function JoeLoadingz()
		 {
			$('chatbox').scrollTop = $('chatbox').scrollHeight;
			args='';
			<?php
				echo 'var do_ajax=new Ajax.Request(\'index.php?mod=Courses&op=joe_jae_load&cid='. $vars['cid'] .'&sid='. $vars['sid'] .'\',{method:\'post\',parameters:args, onComplete:handle_response2});';
				echo 'setTimeout(\'JoeLoadingz()\', '. $refresh_delay .');';
			?>
			//setTimeout('JoeLoadingz()',2000);
		 }	
		 /* ----- ฟังค์ชั่น เชคคนออนไลน์ วนลูป ---- */
		 function JoeOnline()
		 {
			<?php
			echo 'new Ajax.Updater(\'joe_online\',\'index.php?mod=Courses&op=joe_online&cid='. $vars['cid'] .'&sid='. $vars['sid'].'\');';
			echo 'setTimeout(\'JoeOnline()\', '. $userlist_delay .');';
			?>
			//setTimeout('JoeOnline()',5000);
		 }	
		 /* ----- ฟังค์ชั่น กระซิบ ----- */
		 function JoeWhis(name)
		 {
			if(name == '')
			{
				whisper = '';
				$('joe_title').innerHTML = 'ข้อความ:';
			}
			else
			{
				whisper = name;
				$('joe_title').innerHTML = '<a onclick="JoeWhis(\'\');">กระซิบถึง ('+name+'):</a>';
			}
			$('joe_text').focus();
		 }	
		 /* ----- ฟังค์ชั่น เมื่อเข้าครั้งแรก ----- */
		 function JoeLoad()
		 {
			<?php
				echo 'setTimeout(\'JoeLoadingz()\', '. $refresh_delay .');';
				echo 'setTimeout(\'JoeOnline()\', '. $userlist_delay .');';
			?>
			//setTimeout('JoeLoadingz()',2000);
			//setTimeout('JoeOnline()',5000);
			$('chatbox').scrollTop = $('chatbox').scrollHeight;
		 }
		 /* ----- ฟังค์ชั่น ส่งค่าข้อความ ----- */
		 function JoeSending()
		 {	
			if($('joe_text').value == '')
			{
				<?php echo 'alert(\''. _JOEJAE_SAY_NO_TEXT .'\');'; ?>
				return false;
			}
			text = $('joe_text').value;
			var yreq = {  
				onCreate: function(){
				$('joe_text').disabled = true;
				$('joe_send').disabled = true;
				//$('joe_text').value = '';
			  }
			}; 
			$('chatbox').scrollTop = $('chatbox').scrollHeight;
			Ajax.Responders.register(yreq); 	
			if(whisper != '')
				args='joe_text='+encodeURIComponent(text)+'&joe_wuid='+encodeURIComponent(whisper)+'&jae_random='+Math.random();
			else
				args='joe_text='+encodeURIComponent(text)+'&jae_random='+Math.random();
			<?php
				echo 'var do_ajax=new Ajax.Request(\'index.php?mod=Courses&op=joe_jae_send&cid='. $vars['cid'] .'&sid='. $vars['sid'] .'\',{method:\'post\',parameters:args, onComplete:handle_response});';
			?>
			Ajax.Responders.unregister(yreq);	
		 }
		 /* ----- ฟังค์ชั่น ลบข้อความ ----- */
		 function JoeDelete()
		 {
			$('chatbox').innerHTML = '<font color="#ff0000"><?php echo _JOEJAE_SAY_DELETE; ?></font>';
			$('joe_text').focus();
		 }
		 /* ----- ฟังค์ชั่น รับค่าเมื่อส่งข้อความ ----- */
		 function handle_response(request)
		 {
			var response=request.responseText;	
			//alert(response);
			$('chatbox').innerHTML +=  '<div>'+response+'</div>';
			$('joe_send').disabled = false;
			$('joe_text').disabled = false;
			$('joe_text').value = '';
			$('joe_text').focus();
		 }
		  /* ----- ฟังค์ชั่น รับค่าจากลูป ----- */
		 function handle_response2(request)
		 {
			var response=request.responseText;	
			/*
			if($('chatbox').innerHTML.length > 2000)
			{
				$('chatbox').innerHTML = '...'+$('chatbox').innerHTML.substr(1000);
			}
			*/
			$('chatbox').innerHTML +=  '<div>'+response+'</div>';
			//alert(response);
		 }
		 window.onload=JoeLoad;
	</script>
			<?php
			echo '
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr>
				<td width="77%" valign="top">';
			echo '<div id="chatbox" style="background-color:#FFEEEE; padding:7px 0 0 7px; overflow:auto; height: 300px; width:500px; float:left;">';
			JoeJaeLoad($vars);
			echo '</div>
				</td>
				<td width="23%" valign="top" bgcolor="#DDEEFF" style="padding: 5px 0 0 5px;">
					<b>'. _JOEJAE_USER_LIST .':</b>
					<div id="joe_online">';

			$query = mysql_query(' SELECT '. $JoeJae_onlinecolumn['uid'] .', '. $JoeJae_onlinecolumn['uname'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['cid'] .' = "'. $vars['cid'] .'"');

			if(mysql_num_rows($query) <= 0)
			{
				echo '<div style="cursor:crosshair;">'. _JOEJAE_SAY_NO_ONE .'</div>';
			}
			else
			{
				while(list($joe_uid, $joe_name) = mysql_fetch_row($query))
				{
					if($userinfo['name'] != $joe_name)
					echo '<div style="cursor:pointer;" onclick="JoeWhis(\''. $joe_name .'\');">+ '. $joe_name .'</div>';
					else
					echo '<div style="cursor:crosshair;">- '. $joe_name .'</div>';
				}
			}
			echo '</div></td>
			  </tr>
			</table>';

			echo '<div id="joe_form" style="margin-top: 5px; margin-left: 5px"><span id="joe_title">ข้อความ: </span><input id="joe_text" type="text" size="50" />&nbsp;<input type="submit" id="joe_send" onclick="JoeSending();" value="'. _JOEJAE_SEND_BUTTON .'" />&nbsp;<input type="submit" id="joe_send" onclick="return JoeDelete();" value="'. _JOEJAE_DELETE_BUTTON .'" /></div>';

			echo "</td></table>";

			echo '</table>';

			echo '</td></tr></table>';

			echo '</TD></TR></TABLE>';
	}
}

/*********** JOEJAE SEND FUNTION********************/

function JoeJaeSend($vars)
{
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$JoeJae_enrollstable = $lntable['JoeJae_enrolls'];
	$JoeJae_enrollscolumn = &$lntable['JoeJae_enrolls_column'];

	$uid =  lnSessionGetVar('uid');

	$JoeJae_onlinetable = $lntable['JoeJae_onlinetable'];
	$JoeJae_onlinecolumn = &$lntable['JoeJae_online_column'];

	if(!isset($uid))
	{
		$uid = -1;

		//เข้ามาใหม่ หรือ SESSION GUEST หาย
		if(!isset($_SESSION['joe_guest']) || $_SESSION['joe_guest'] == '')
		{
			echo $_SESSION['joe_guest'];
			while(1)
			{
				$joe_temp_gen_name =  'Guest'. rand(1000,9999);
					
				$query = ' SELECT '. $JoeJae_onlinecolumn['uid'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['uname'] .' = "'. $joe_temp_gen_name .'" AND '. $JoeJae_onlinecolumn['cid'] .' = "'. $vars['cid'] .'"';

				if(mysql_num_rows(mysql_query($query)) <= 0)
				{
					//$_SESSION['joe_guest'] = $joe_temp_gen_name;
					break;
				}
			}

			//ได้ชื่อที่ไม่ซ้ำ
			$userinfo['name'] = $joe_temp_gen_name;
			$_SESSION['joe_guest'] = $joe_temp_gen_name;
			//$query = 'INSERT INTO '. $JoeJae_enrollstable .' ('.$JoeJae_enrollscolumn['uid'].', '. $JoeJae_enrollscolumn['uname'] .', '. $JoeJae_enrollscolumn['text'] .', '. $JoeJae_enrollscolumn['wuid'] .', '. $JoeJae_enrollscolumn['cid'] .', '. $JoeJae_enrollscolumn['time'] .', '. $JoeJae_enrollscolumn['ip'] .') VALUES ("-5", "'.  $userinfo['name'] .'", "ได้เข้าใช้งานห้องแล้ว,, สวัสดีจ๊ะ~*", "", "'. $vars['cid'] .'", "'. time(). '", "'. getenv('REMOTE_ADDR') .'")';
			//$dbconn->Execute($query) or die(mysql_error());
		}
		else
		$userinfo['name'] = $_SESSION['joe_guest'];
	}
	else
	{
		$userinfo = lnUserGetVars($uid);
		$userinfo['name'] = $userinfo['uname'];
	}

	/*
	 if(!isset($_SESSION['joe_incomming']) || $_SESSION['joe_incomming'] != $vars['cid'].$userinfo['name'])
	 {
		$_SESSION['joe_incomming'] = '';
		}
		*/

	if(!isset($_SESSION['joe_incomming']) || $_SESSION['joe_incomming'] != $vars['cid'].$userinfo['name'])
	{

		$query = ' SELECT '. $JoeJae_onlinecolumn['uid'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['uname'] .' = "'. $userinfo['name'] .'" AND '. $JoeJae_onlinecolumn['cid'] .' = "'. $vars['cid'] .'"';

		if(mysql_num_rows(mysql_query($query)) <= 0)
		{
			$query = 'INSERT INTO '. $JoeJae_onlinetable .' ('.$JoeJae_onlinecolumn['uid'].', '. $JoeJae_onlinecolumn['uname'] .', '. $JoeJae_onlinecolumn['cid'] .', '. $JoeJae_onlinecolumn['lastseen'] .') VALUES ("'. $uid .'", "'.  $userinfo['name'] .'", "'. $vars['cid'] .'", "'. time(). '")';
			$dbconn->Execute($query) or die(mysql_error());
			$query = 'INSERT INTO '. $JoeJae_enrollstable .' ('.$JoeJae_enrollscolumn['uid'].', '. $JoeJae_enrollscolumn['uname'] .', '. $JoeJae_enrollscolumn['text'] .', '. $JoeJae_enrollscolumn['wuid'] .', '. $JoeJae_enrollscolumn['cid'] .', '. $JoeJae_enrollscolumn['time'] .', '. $JoeJae_enrollscolumn['ip'] .') VALUES ("-5", "'.  $userinfo['name'] .'", "'. _JOEJAE_SAY_JOIN .'", "", "'. $vars['cid'] .'", "'. time(). '", "'. getenv('REMOTE_ADDR') .'")';
			$dbconn->Execute($query) or die(mysql_error());
		}

		$_SESSION['joe_incomming'] = $vars['cid'].$userinfo['name'];
	}
	else
	{
		$query = ' SELECT '. $JoeJae_onlinecolumn['uid'] .' FROM '. $JoeJae_onlinetable .' WHERE '. $JoeJae_onlinecolumn['uname'] .' = "'. $userinfo['name'] .'" AND '. $JoeJae_onlinecolumn['cid'] .' = "'. $vars['cid'] .'"';

		if(mysql_num_rows(mysql_query($query)) <= 0)
		{
			$query = 'INSERT INTO '. $JoeJae_onlinetable .' ('.$JoeJae_onlinecolumn['uid'].', '. $JoeJae_onlinecolumn['uname'] .', '. $JoeJae_onlinecolumn['cid'] .', '. $JoeJae_onlinecolumn['lastseen'] .') VALUES ("'. $uid .'", "'.  $userinfo['name'] .'", "'. $vars['cid'] .'", "'. time(). '")';
			$dbconn->Execute($query) or die(mysql_error());
			$query = 'INSERT INTO '. $JoeJae_enrollstable .' ('.$JoeJae_enrollscolumn['uid'].', '. $JoeJae_enrollscolumn['uname'] .', '. $JoeJae_enrollscolumn['text'] .', '. $JoeJae_enrollscolumn['wuid'] .', '. $JoeJae_enrollscolumn['cid'] .', '. $JoeJae_enrollscolumn['time'] .', '. $JoeJae_enrollscolumn['ip'] .') VALUES ("-5", "'.  $userinfo['name'] .'", "'. _JOEJAE_SAY_JOIN .'", "", "'. $vars['cid'] .'", "'. time(). '", "'. getenv('REMOTE_ADDR') .'")';
			$dbconn->Execute($query) or die(mysql_error());
		}
	}

	if(!isset($joe_wuid))
	{
		$joe_wuid = '';
	}

	//$joe_text =  $userinfo['name'] . '' . $vars['joe_text'];

	$query = 'INSERT INTO '. $JoeJae_enrollstable .' ('.$JoeJae_enrollscolumn['uid'].', '. $JoeJae_enrollscolumn['uname'] .', '. $JoeJae_enrollscolumn['text'] .', '. $JoeJae_enrollscolumn['wuid'] .', '. $JoeJae_enrollscolumn['cid'] .', '. $JoeJae_enrollscolumn['time'] .', '. $JoeJae_enrollscolumn['ip'] .') VALUES ("'. $uid .'", "'.  $userinfo['name'] .'", "'. $vars['joe_text'] .'", "'. $vars['joe_wuid'] .'", "'. $vars['cid'] .'", "'. time(). '", "'. getenv('REMOTE_ADDR') .'")';
	//echo $query;
	$result = $dbconn->Execute($query) or die(mysql_error());
}


/**********************************************************************************************************/

/*
 Print detail of student who learning, pass ,drop or fail
 Programmer : Bas
 */

function printReport($vars) {
	// Get arguments from argument array
	extract($vars);

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

	tabMenu($vars,8);

	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr><td valign="top">&nbsp;';

	if($status==0)
	{
		echo '<div align="center">'._ALLSTD.'</div>';
	}
	else if($status==1)
	{
		echo '<div align="center">'._STUDYING.'</div>';
	}
	else if($status==2)
	{
		echo '<div align="center">'._GRADUATED.'</div>';
	}
	else if($status==3)
	{
		echo '<div align="center">'._DROPED.'</div>';
	}
	else if($status==4)
	{
		echo '<div align="center">'._FAILED.'</div>';
	}

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();


	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];

	if($status==0)
	{
		$query = "SELECT $course_enrollscolumn[uid], $course_enrollscolumn[start]
		FROM $course_enrollstable
		WHERE $course_enrollscolumn[sid]='$sid'";
		$result = $dbconn->Execute($query);
	}
	else
	{
		$query = "SELECT $course_enrollscolumn[uid], $course_enrollscolumn[start]
		FROM $course_enrollstable
		WHERE $course_enrollscolumn[sid]='$sid' AND $course_enrollscolumn[status]=$status";
		$result = $dbconn->Execute($query);
	}
	$number = 0;


	?>

<table width="450" border="0" cellpadding="0" cellspacing="1"
	bgcolor="#444444" align="center">
	<tr>
		<td class="head" width="50">
		<div align="center"><?echo _NUM;?></div>
		</td>
		<td class="head" width="100">
		<div align="center"><?echo _USERNAME;?></div>
		</td>
		<td class="head" width="170">
		<div align="center"><?echo _STDNAME;?></div>
		</td>
		<td class="head" width="80">
		<div align="center"><?echo _STARTLEARN;?></div>
		</td>
	</tr>

	<?

	while(list($uid,$start) = $result->fields){
		$result->MoveNext();
		$query2 = "SELECT $userscolumn[name], $userscolumn[uname]
		FROM $userstable
		WHERE $userscolumn[uid]='$uid' ";
		$result2 = $dbconn->Execute($query2);
		list($name,$uname) = $result2->fields;
		$number++;

		?>


	<tr bgcolor="#FFFFFF">
		<td width="50">
		<div align="center"><?echo $number;?></div>
		</td>
		<td width="100"><?echo  $uname;?></td>
		<td width="170"><?echo  $name;?></td>
		<td width="80">
		<div align="center"><?echo  $start;?></div>
		</td>
	</tr>


	<?

	} // end while list
	echo "</table>";




	echo '</table>';

	echo '</td></tr></table>';

	echo '</TD></TR></TABLE>';
}


function questionaireSummaryShow($vars)
{

	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$questionairetable = $lntable['questionaire'];
	$questionairecolumn = &$lntable['questionaire_column'];
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	$qnum = "SELECT COUNT( $questionairecolumn[eid])  FROM $questionairetable  WHERE  $questionairecolumn[sid] = $sid";
	$result_qnum = $dbconn->Execute($qnum);
	list ($qnum) = $result_qnum->fields;

	$all = "SELECT COUNT($course_enrollscolumn[eid]) FROM $course_enrollstable WHERE $course_enrollscolumn[sid]=$sid";
	$result_all = $dbconn->Execute($all);
	list ($all) = $result_all->fields;
	//echo $sid;
	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

	tabMenu($vars,9);

	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width="100%" cellpadding="3" cellspacing="0" border="0">';
	echo '<tr><td valign="top">&nbsp;';
	echo '<table width="70%" border="0" align="center" bgcolor="#669900">';
	echo '<tr bgcolor="#FFFFFF"><td align="center">';
	echo '<B>ผลสรุปจากแบบประเมินผู้สอน<B><br><br>';
	echo 'ขณะนี้มีจำนวนผู้ตอบแบบสอบถาม '.$qnum .' คน<br>';
	echo 'จำนวนผู้เรียนทั้งหมด '.$all.' คน<br>';
	echo '</tr></td>';
	echo '</table>';



	for($i=1;$i<=5;$i++)
	{
		$query_questionaire1 = "SELECT COUNT( $questionairecolumn[t1_1])  FROM $questionairetable  WHERE  $questionairecolumn[sid] = $sid AND $questionairecolumn[t1_1]=$i";
		$result1 = $dbconn->Execute($query_questionaire1);
		list ($t1_1[$i]) = $result1->fields;
		$query_questionaire2 = "SELECT COUNT( $questionairecolumn[t1_2])  FROM $questionairetable  WHERE  $questionairecolumn[sid] = $sid AND $questionairecolumn[t1_2]=$i";
		$result2 = $dbconn->Execute($query_questionaire2);
		list ($t1_2[$i]) = $result2->fields;
		$query_questionaire3 = "SELECT COUNT( $questionairecolumn[t1_3])  FROM $questionairetable  WHERE  $questionairecolumn[sid] = $sid AND $questionairecolumn[t1_3]=$i";
		$result3 = $dbconn->Execute($query_questionaire3);
		list ($t1_3[$i]) = $result3->fields;
		$query_questionaire4 = "SELECT COUNT( $questionairecolumn[t1_4])  FROM $questionairetable  WHERE  $questionairecolumn[sid] = $sid AND $questionairecolumn[t1_4]=$i";
		$result4 = $dbconn->Execute($query_questionaire4);
		list ($t1_4[$i]) = $result4->fields;
		$query_questionaire5 = "SELECT COUNT( $questionairecolumn[t1_5])  FROM $questionairetable  WHERE  $questionairecolumn[sid] = $sid AND $questionairecolumn[t1_5]=$i";
		$result5 = $dbconn->Execute($query_questionaire5);
		list ($t1_5[$i]) = $result5->fields;
		$query_questionaire6 = "SELECT COUNT( $questionairecolumn[t2_1])  FROM $questionairetable  WHERE  $questionairecolumn[sid] = $sid AND $questionairecolumn[t2_1]=$i";
		$result6 = $dbconn->Execute($query_questionaire6);
		list ($t2_1[$i]) = $result6->fields;
		$query_questionaire7 = "SELECT COUNT( $questionairecolumn[t2_2])  FROM $questionairetable  WHERE  $questionairecolumn[sid] = $sid AND $questionairecolumn[t2_2]=$i";
		$result7 = $dbconn->Execute($query_questionaire7);
		list ($t2_2[$i]) = $result7->fields;
		$query_questionaire8 = "SELECT COUNT( $questionairecolumn[t3_1])  FROM $questionairetable  WHERE  $questionairecolumn[sid] = $sid AND $questionairecolumn[t3_1]=$i";
		$result8 = $dbconn->Execute($query_questionaire8);
		list ($t3_1[$i]) = $result8->fields;
		$query_questionaire9 = "SELECT COUNT( $questionairecolumn[t3_2])  FROM $questionairetable  WHERE  $questionairecolumn[sid] = $sid AND $questionairecolumn[t3_2]=$i";
		$result9 = $dbconn->Execute($query_questionaire9);
		list ($t3_2[$i]) = $result9->fields;
		$query_questionaire10 = "SELECT COUNT( $questionairecolumn[t3_3])  FROM $questionairetable  WHERE  $questionairecolumn[sid] = $sid AND $questionairecolumn[t3_3]=$i";
		$result10 = $dbconn->Execute($query_questionaire10);
		list ($t3_3[$i]) = $result10->fields;

	}
	?>

	<p><b>1. เนื้อหาหลักสูตร</b></p>

	<table width="100%" border="1" cellpadding="0" cellspacing="1"
		bordercolor="#FFFFFF" bgcolor="#669900">
		<tr bgcolor="#669900">
			<td width="50%" bgcolor="#669900">
			<div align="center"><FONT COLOR="#FFFFFF"><B>หัวข้อประเมิน</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>น้อยมาก</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>น้อย</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>ปานกลาง</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>ดี</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>ดีมาก</B></div>
			</td>
		</tr>
		<tr>
			<td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
			ความรู้ของเนื้อหาที่ได้รับตรงตามจุดประสงค์การเรียนรู้</td>
			<?
			for($j=1;$j<=5;$j++)
			{
				echo '<td width="10%" bgcolor="#FFFFFF"><div align="center">';
				echo  $t1_1[$j] ;
				echo '</div></td>';
			}
			?>
		</tr>
		<tr>
			<td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
			ความรู้ที่ได้รับมีประโยชน์และสามารถนำไปใช้ได้จริง</td>
			<?
			for($j=1;$j<=5;$j++)
			{
				echo '<td width="10%" bgcolor="#FFFFFF"><div align="center">';
				echo  $t1_2[$j] ;
				echo '</div></td>';
			}
			?>
		</tr>
		<tr>
			<td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
			ลำดับของเนื้อหาหลักสูตรในการนำเสนอ</td>
			<?
			for($j=1;$j<=5;$j++)
			{
				echo '<td width="10%" bgcolor="#FFFFFF"><div align="center">';
				echo  $t1_3[$j] ;
				echo '</div></td>';
			}
			?>
		</tr>
		<tr>
			<td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
			ความเหมาะสมของเนื้อหาหลักสูตร</td>
			<?
			for($j=1;$j<=5;$j++)
			{
				echo '<td width="10%" bgcolor="#FFFFFF"><div align="center">';
				echo  $t1_4[$j] ;
				echo '</div></td>';
			}
			?>
		</tr>
		<tr>
			<td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
			ความเหมาะสมของแบบทดสอบ<FONT FACE="Times New Roman, serif">/</FONT>งานที่ได้รับมอบหมาย</td>
			<?
			for($j=1;$j<=5;$j++)
			{
				echo '<td width="10%" bgcolor="#FFFFFF"><div align="center">';
				echo  $t1_5[$j] ;
				echo '</div></td>';
			}
			?>
		</tr>
	</table>

	<br>

	<p><b>2. เทคนิคการถ่ายทอดและนำเสนอ</b></p>
	<table width="100%" border="1" cellpadding="0" cellspacing="1"
		bordercolor="#FFFFFF" bgcolor="#669900">
		<tr bgcolor="#669900">
			<td width="50%" bgcolor="#669900">
			<div align="center"><FONT COLOR="#FFFFFF"><B>หัวข้อประเมิน</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>น้อยมาก</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>น้อย</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>ปานกลาง</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>ดี</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>ดีมาก</B></div>
			</td>
		</tr>
		<tr>
			<td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
			ความเอาใจใส่ต่อผู้เรียนของผู้สอนในแบบออนไลน</td>
			<?
			for($j=1;$j<=5;$j++)
			{
				echo '<td width="10%" bgcolor="#FFFFFF"><div align="center">';
				echo  $t2_1[$j] ;
				echo '</div></td>';
			}
			?>
		</tr>
		<tr>
			<td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
			การถามตอบระหว่างเรียนผ่านกระดานข่าว (Webboard)</td>
			<?
			for($j=1;$j<=5;$j++)
			{
				echo '<td width="10%" bgcolor="#FFFFFF"><div align="center">';
				echo  $t2_2[$j] ;
				echo '</div></td>';
			}
			?>
		</tr>
	</table>

	<p><b>3. สื่อ/เครื่องมือสำหรับเรียนออนไลน์</b></p>
	<table width="100%" border="1" cellpadding="0" cellspacing="1"
		bordercolor="#FFFFFF" bgcolor="#669900">
		<tr bgcolor="#669900">
			<td width="50%" bgcolor="#669900">
			<div align="center"><FONT COLOR="#FFFFFF"><B>หัวข้อประเมิน</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>น้อยมาก</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>น้อย</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>ปานกลาง</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>ดี</B></div>
			</td>
			<td width="10%">
			<div align="center"><FONT COLOR="#FFFFFF"><B>ดีมาก</B></div>
			</td>
		</tr>
		<tr>
			<td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
			ความสวยงาม การออกแบบสื่อบทเรียนออนไลน์</td>
			<?
			for($j=1;$j<=5;$j++)
			{
				echo '<td width="10%" bgcolor="#FFFFFF"><div align="center">';
				echo  $t3_1[$j] ;
				echo '</div></td>';
			}
			?>
		</tr>
		<tr>
			<td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
			ความรวดเร็วในการเข้าถึงระบบ/บทเรียนออนไลน์</td>
			<?
			for($j=1;$j<=5;$j++)
			{
				echo '<td width="10%" bgcolor="#FFFFFF"><div align="center">';
				echo  $t3_2[$j] ;
				echo '</div></td>';
			}
			?>
		</tr>
		<tr>
			<td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
			ความยากง่ายในการใช้งานระบบ e-Learning</td>
			<?
			for($j=1;$j<=5;$j++)
			{
				echo '<td width="10%" bgcolor="#FFFFFF"><div align="center">';
				echo  $t3_3[$j] ;
				echo '</div></td>';
			}
			?>
		</tr>
	</table>

	<p><b>4. ข้อเสนอแนะอื่นๆ</b></p>
	<table width="100%" border="1" cellpadding="0" cellspacing="1"
		bordercolor="#FFFFFF" bgcolor="#669900">
		<tr bgcolor="#669900">
			<td width="10%" bgcolor="#669900">
			<div align="center"><FONT COLOR="#FFFFFF"><B>ลำดับที่</B></div>
			</td>
			<td width="90%" bgcolor="#669900">
			<div align="center"><FONT COLOR="#FFFFFF"><B>ข้อเสนอแนะ</B></div>
			</td>
		</tr>

		<?

		$query = "SELECT  $questionairecolumn[t4] FROM $questionairetable WHERE $questionairecolumn[sid] = $sid";

		$result = $dbconn->Execute($query);
		$k=0;

		while(list($t4) = $result->fields) {
			$result->MoveNext();

			if($t4!='')
			{
				$k++;
				echo '<tr  bgcolor="#FFFFFF">';
				echo '<td width="10%"><div align="center">'.$k.'</div></td>';
				echo '<td width="90%">'.$t4.'</td>';
				echo '</tr>';
			}

		}//end while

		echo '</table>';

		echo '</td></tr></table>';

		echo '</TD></TR></TABLE>';
}


/**
 * goto lesson
 */
function gotoLesson($cid, $sid, $lid) {
	global $start;

	echo  _GOTOLESSON. ": <SELECT NAME='goto' onchange=\"window.open(this.options[this.selectedIndex].value,'_self')\">";
	/////////////////////////////////////////////////////////////////code ในหน้าโชว์เนื้อหาตรงที่แสดงว่า couse นี้มีกี่ lesson//////////////////////////////////////////////////////////
	$enroll_info = lnEnrollGetVars(lnGetEnroll($cid));
	$start = $enroll_info['start'];

	gotoListLesson($cid,$sid,$lid,0,$orderings=array());

	echo '</SELECT>';

}

/**
 * list select option for fuction goto lesson
 */
function  gotoListLesson($cid,$sid,$lid,$lid_parent,$orderings) {
	global $start;

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$query = "SELECT  $lessonscolumn[lid],
	$lessonscolumn[title],
	$lessonscolumn[description],
	$lessonscolumn[file],
	$lessonscolumn[duration],
	$lessonscolumn[weight],
	$lessonscolumn[lid_parent],
	$lessonscolumn[type]
	FROM $lessonstable
	WHERE $lessonscolumn[cid]='". lnVarPrepForStore($cid) ."'
	AND $lessonscolumn[lid_parent]='".$lid_parent."'
	ORDER BY $lessonscolumn[weight]";
	$result = $dbconn->Execute($query);

	$prev_lid=0;
	$select[$lid]="selected";

	$uid = lnSessionGetVar('uid');
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$query1 = "SELECT $course_enrollscolumn[eid]
	FROM $course_enrollstable WHERE $course_enrollscolumn[uid] = $uid AND $course_enrollscolumn[sid] = $sid";
	$result1 = $dbconn->Execute($query1);
	list($eid) = $result1->fields;
	$eidnow = $eid;

	//find last $quiz_anscolumn[attempts] By narananami

	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];

	$quiz_anstable = $lntable['quiz_answer'];
	$quiz_anscolumn = &$lntable['quiz_answer_column'];

	$queryattempts = "SELECT  MIN($quiz_questioncolumn[mcid])
	FROM  $quiz_questiontable
	WHERE 	$quiz_questioncolumn[qid] =  '" . lnVarPrepForStore($lesson_file) . "'";
	$resultattempts = $dbconn->Execute($queryattempts);
	list($mcid) = $resultattempts->fields;
	/*
	 $queryattempts = "SELECT MAX($quiz_anscolumn[attempts])
	 FROM  $quiz_anstable
	 WHERE 	$quiz_anscolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'
	 AND	$quiz_anscolumn[eid] =  '" . lnVarPrepForStore($eidnow) . "'";
	 $resultattempts = $dbconn->Execute($queryattempts);
	 list($quiz_ansattempts) = $resultattempts->fields;

	 if($quiz_ansattempts==null) $quiz_ansattempts = 1; else $quiz_ansattempts++;
	 */

	//echo "<OPTION VALUE=''>quiz_ansattempts=$quiz_ansattempts</OPTION>";


	while(list($lesson_lid,$title,$description,$file,$duration,$weight,$lid_parent,$type) = $result->fields) {
		$result->MoveNext();
		$prev_lid = $lid;
		array_push($orderings,$weight);
		$show_item=join('.',$orderings);
		for($blank='',$j=0;$j<count($orderings);$j++) $blank .= '&nbsp;';

		if (Date_Calc::isPastDate2($start) || isSpecialUsers($sid) || lnAllTime($cid)) {
			if ($type==1) {
				$uid = lnSessionGetVar('uid');
				//edit max quiz_ansattempts by narananami
				$quiz_ansattempts=0;
				$quiz_ansattempts = countAttempt($eidnow,$lesson_lid);
				if($quiz_ansattempts==null) $quiz_ansattempts = 1; else $quiz_ansattempts++;

				//echo "<OPTION VALUE=''>eid=$eidnow::lid=$lesson_lid::quiz_ansattempts=$quiz_ansattempts</OPTION>";
				echo "<OPTION VALUE=index.php?mod=Courses&op=lesson_show&cid=$cid&uid=$uid&lid=$lesson_lid&sid=$sid&eid=$eidnow&qid=$file&quiz_ansattempts=$quiz_ansattempts ".$select[$lesson_lid].">$blank $show_item. $title</OPTION>";
				/*
				 }if ($type==2) {
				 $uid = lnSessionGetVar('uid');

					echo "<OPTION VALUE = index.php?mod=Courses&op=lesson_show&cid=$cid&uid=$uid&lid=$lesson_lid&sid=$sid&eid=$eidnow&qid=$file ".$select[$lesson_lid]." >$blank $show_item. $title</OPTION>";
					*/
					
					
			}else {
				$uid = lnSessionGetVar('uid');
				echo "<OPTION VALUE=index.php?mod=Courses&op=lesson_show&uid=$uid&cid=$cid&lid=$lesson_lid&sid=$sid&eid=$eidnow&page=1 ".$select[$lesson_lid].">$blank $show_item. $title</OPTION>";

			}

		}
			
		$days_stop = Date_Calc::dateToDays2($start) + $duration -1;
		$days_next = $days_stop + 1;
		$next = Date_Calc::daysToDate2($days_next);
		$start = $next;

		gotoListLesson($cid,$sid,$lid,$lesson_lid,$orderings);
		array_pop($orderings);

	}

}

function calcTotalTimetolearn($uid,$sid,$cid)
{
	include_once("includes/calc.php");
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$uidnow =  lnSessionGetVar('uid');
	$sql = "SELECT  $course_enrollscolumn[eid] FROM $course_enrollstable
	WHERE $course_enrollscolumn[uid] = $uid AND  $course_enrollscolumn[sid] = $sid";
	$result = $dbconn->Execute($sql);
	while(list($eid) = $result->fields)
	{
		$result->MoveNext();
		$rets =$eid;
	}
	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];
	unset($result);
	$sql = "SELECT FROM_UNIXTIME($course_trackingcolumn[atime]) as atime,FROM_UNIXTIME($course_trackingcolumn[outime]) as outtime FROM $course_trackingtable WHERE $course_trackingcolumn[eid] = '$rets'  ORDER BY $course_trackingcolumn[weight]";
	$rs = $dbconn->Execute($sql);
	$totaltimes = array();
	while ($arr = $rs->FetchRow())
	{         	if($arr[1] == '')
	$arr[1] = $arr[0];
	$time =  Date_Calc::dateDiv($arr[1],$arr[0]);
	$totaltimes = array_merge_recursive($time,$totaltimes);

	}

	$tmp = array('D'=>0,'H'=>0,'M'=>0,'S'=>0);

	foreach ($totaltimes as $key=>$value)
	{
		$tmp1 = 0;
		if(!is_array($value))
		{
			$tmp1 = $value;
		}
		else
		{
			foreach ($value as $values)
			{
				$tmp1 += $values;
			}
		}

		if($key == 'D')
		$tmp['D'] = $tmp1;
		if($key == 'H')
		$tmp['H'] = $tmp1;
		if($key == 'M')
		$tmp['M'] = $tmp1;
		if($key == 'S')
		$tmp['S'] = $tmp1;
	}

	foreach ($tmp as $key=>$value)
	{

		if($key == 'S')
		{
			$S += $value;
			if($S >= 60)
			{
				$M += floor($S / 60);
				$S = $S % 60;
			}
		}
		if($key == 'M')
		{
			$M = $M+ $value;
			if($M >= 60)
			{
				$H += floor($M / 60);
				$M = $M % 60;
			}
		}
		if($key == 'H')
		{
			$H = $H + $value;
			if($H >= 24)
			{
				$D += floor($M / 24);
				$H = $H % 24;
			}
		}
		if($key == 'D')
		{
			$D = $D + $value;
		}

	}
	$tmp = array('D'=>0,'H'=>0,'M'=>0,'S'=>0);
	$tmp['D'] = str_pad($D, 2, "0", STR_PAD_LEFT);
	$tmp['H'] = str_pad($H, 2, "0", STR_PAD_LEFT);
	$tmp['M'] = str_pad($M, 2, "0", STR_PAD_LEFT);
	$tmp['S'] = str_pad($S, 2, "0", STR_PAD_LEFT);

	return $tmp;
}

function addQuizAns($eid,$mcid,$useranswer,$attempts,$qid,$lid){
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quizanstable = $lntable['quiz_answer'];
	$quizanscolumn = &$lntable['quiz_answer_column'];

	$query = "INSERT INTO $quizanstable
	(	$quizanscolumn[eid],
	$quizanscolumn[mcid],
	$quizanscolumn[useranswer],
	$quizanscolumn[attempts],
	$quizanscolumn[qid],
	$quizanscolumn[lid]
	)
	VALUES ('" . lnVarPrepForStore($eid) . "',
						  '" . lnVarPrepForStore($mcid) . "',
						  '" . lnVarPrepForStore($useranswer) . "',
						  '" . lnVarPrepForStore($attempts) . "',
						  '" . lnVarPrepForStore($qid) . "',
						  '" . lnVarPrepForStore($lid) . "'
					  )";

	$dbconn->Execute($query);
	if ($dbconn->ErrorNo() != 0) {
		return false;
	}
	else {
		return true;
	}
}
function updateQuizAns($qaid,$eid,$mcid,$useranswer){

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quizanstable = $lntable['quiz_answer'];
	$quizanscolumn = &$lntable['quiz_answer_column'];

	$query = "UPDATE $quizanstable SET
	$quizanscolumn[eid] = '" . lnVarPrepForStore($eid) . "',
	$quizanscolumn[mcid] = '" . lnVarPrepForStore($mcid) . "',
	$quizanscolumn[useranswer] = '" . lnVarPrepForStore($useranswer) . "'
	WHERE $quizanscolumn[qaid]  = '" . lnVarPrepForStore($qaid) . "'";

	$dbconn->Execute($query);

	if ($dbconn->ErrorNo() != 0) {
		return false;
	}
	else {
		//		resequenceLessons($cid,$);
		return true;
	}
}
function listQuiz($lid,$cid,$qid,$sid,$page,$eid,$quiz_ansattempts,$mcid_m) {
	// LAst modified by Tammatisthan J. 10/24/2009 11:37:39 AM
	for ($w=0; $w<$mcid_m; $w++) {
		//++ $quiz_ansattempts by narananami

		$ret .=  '<OPTION VALUE="index.php?mod=Courses&&op=showcontent&cid='.$cid.'&qid='.$qid.'&lid='.$lid.'&sid='.$sid.'&page='.$page.'&nmcid='.$w.'&eid='.$eid.'&quiz_ansattempts='.$quiz_ansattempts.'">'._NOWQUIZMSG3.' '.($w+1).'</OPTION>';
	}
	return $ret;
}
function createArrayAns($qid,$eid){
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];
	$quiz_anstable = $lntable['quiz_answer'];
	$quiz_anscolumn = &$lntable['quiz_answer_column'];

	$query =	"SELECT $quiz_anscolumn[mcid],$quiz_anscolumn[useranswer]
	FROM $quiz_anstable,$quiz_questiontable
	WHERE $quiz_anscolumn[mcid]=$quiz_questioncolumn[mcid]
	and $quiz_questioncolumn[qid]='".lnVarPrepForStore($qid)."'
	ORDER BY $quiz_anscolumn[mcid]";
	$result = $dbconn->Execute($query);
	for ($i=1;list($mcid,$userans) = $result->fields; $i++) {
		$result->MoveNext();
		$ans[$mcid][0] = $userans;
	}
	return $ans;
}

//getTotalScore edit by narananami
function getTotalScore($qid){
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];

	$query =	"SELECT $quizcolumn[correctscore]
	FROM $quiztable
	WHERE $quizcolumn[qid]='".lnVarPrepForStore($qid)."'";
	$result = $dbconn->Execute($query);
	list($correctscore) = $result->fields;

	$query =	"SELECT $quiz_questioncolumn[score]
	FROM $quiz_questiontable
	WHERE $quiz_questioncolumn[qid]='".lnVarPrepForStore($qid)."'";
	$result = $dbconn->Execute($query);
	$total=0;
	for ($i=1;list($score) = $result->fields; $i++) {
		$result->MoveNext();
		$total += $score*$correctscore;
	}
	return $total;
}

?>