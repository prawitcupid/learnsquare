<?php
function lnEnrollGetVars($eid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$vars = array();

	$enrollstable = $lntable['course_enrolls'];
	$enrollscolumn = &$lntable['course_enrolls_column'];

	$query = "SELECT *
              FROM $enrollstable
              WHERE $enrollscolumn[eid] = '" . lnVarPrepForStore($eid) ."'";
	$result = $dbconn->Execute($query);

	list($eid,$sid,$gid,$uid,$options,$status,$mentor,$start) = $result->fields;
	$vars['eid'] = $eid;
	$vars['sid'] = $sid;
	$vars['gid'] = $gid;
	$vars['uid'] = $uid;
	$vars['options'] = $options;
	$vars['status'] = $status;
	$vars['mentor'] = $mentor;
	$vars['start'] = $start;

	$result->Close();

	return($vars);
}

function lnSubmissionGetVars($sid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$vars = array();

	$submissionstable = $lntable['course_submissions'];
	$submissionscolumn = &$lntable['course_submissions_column'];

	$query = "SELECT $submissionscolumn[sid],$submissionscolumn[cid], $submissionscolumn[start], $submissionscolumn[instructor], $submissionscolumn[enroll], $submissionscolumn[active], $submissionscolumn[amountstd], $submissionscolumn[limitstd]
              FROM $submissionstable
              WHERE $submissionscolumn[sid] = '" . lnVarPrepForStore($sid) ."'";

	$result = $dbconn->Execute($query);

	list($sid,$cid,$start,$instructor,$enroll,$active,$amount,$limit) = $result->fields;
	$vars['sid'] = $sid;
	$vars['cid'] = $cid;
	$vars['start'] = $start;
	$vars['instructor'] = $instructor;
	$vars['enroll'] = $enroll;
	$vars['active'] = $active;
	$vars['amount'] = $amount;
	$vars['limit'] = $limit;

	$result->Close();

	return($vars);
}


/**
 * check instructor user
 */
function lnIsUserInstructor($sid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];

	$instid= lnSessionGetVar('uid');

	$query = "SELECT $course_submissionscolumn[sid]
						FROM $course_submissionstable 
						WHERE $course_submissionscolumn[sid]='". lnVarPrepForStore($sid) ."' AND $course_submissionscolumn[instructor]='". lnVarPrepForStore($instid)."' ";

	$result = $dbconn->Execute($query);

	return  $result->fields[0];
}


/**
 * check user mentor
 */
function lnIsUserMentor($sid,$uid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	$instid= lnSessionGetVar('uid');
	$query = "SELECT $course_enrollscolumn[eid]
						FROM $course_enrollstable 
						WHERE $course_enrollscolumn[sid]='". lnVarPrepForStore($sid) ."' AND $course_enrollscolumn[mentor]='". lnVarPrepForStore($instid)."' AND $course_enrollscolumn[uid]='". lnVarPrepForStore($uid)."'";

	$result = $dbconn->Execute($query);

	return  $result->fields[0];
}

function lnCourseInstructor($sid) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	//	$course_tatable = $lntable['course_ta'];
	//	$course_tacolumn = &$lntable['course_ta_column'];
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	$uid = lnSessionGetVar('uid');

	//	$query = "SELECT $course_submissionscolumn[cid] FROM $course_submissionstable LEFT JOIN $course_tatable ON  $course_tacolumn[sid]=$course_submissionscolumn[sid]  WHERE $course_submissionscolumn[sid]='". lnVarPrepForStore($sid) ."' and ($course_submissionscolumn[instructor] ='". lnVarPrepForStore($uid) ."' OR $course_tacolumn[uid]='". lnVarPrepForStore($uid) ."')";

	$query = "SELECT $course_submissionscolumn[cid] FROM $course_submissionstable LEFT JOIN $course_enrollstable ON  $course_enrollscolumn[sid]=$course_submissionscolumn[sid]  WHERE $course_submissionscolumn[sid]='". lnVarPrepForStore($sid) ."' and ($course_submissionscolumn[instructor] ='". lnVarPrepForStore($uid) ."' OR $course_enrollscolumn[mentor]='". lnVarPrepForStore($uid) ."')";

	//	echo $query;
	$result = $dbconn->Execute($query);

	return $result->fields[0];
}

function lnhasCourseMentor($sid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	$course_tatable = $lntable['course_ta'];
	$course_tacolumn = &$lntable['course_ta_column'];

	$uid = lnSessionGetVar('uid');
	$query = "SELECT $course_submissionscolumn[sid]
						FROM $course_submissionstable, $course_tatable 
						WHERE $course_tacolumn[sid]=$course_submissionscolumn[sid] AND $course_submissionscolumn[sid]='". lnVarPrepForStore($sid) ."' AND $course_submissionscolumn[instructor]='". lnVarPrepForStore($uid)."'";

	$result = $dbconn->Execute($query);

	return  $result->fields[0];
}


/*
 * get Learning Status
 */
function lnGetLearningStatus($uid,$lid,$sid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];

	$query = "SELECT $course_enrollscolumn[status]
						FROM $course_trackingtable, $course_enrollstable
						WHERE $course_trackingcolumn[eid]=$course_enrollscolumn[eid] 
						AND $course_trackingcolumn[lid]='". lnVarPrepForStore($lid) ."'
						AND $course_enrollscolumn[sid]='".lnVarPrepForStore($sid)."'
						AND $course_enrollscolumn[uid] = '".lnVarPrepForStore($uid)."' ";
	$result = $dbconn->Execute($query);
	/*
	$query = "SELECT $course_enrollscolumn[status]
						FROM $course_trackingtable, $course_enrollstable
						WHERE $course_trackingcolumn[eid]=$course_enrollscolumn[eid] AND $course_trackingcolumn[lid]='". lnVarPrepForStore($lid) ."' AND $course_enrollscolumn[uid] = '".lnVarPrepForStore($uid)."' ";
	$result = $dbconn->Execute($query);
	*/
	return  $result->fields[0];
}


/**
 * find Lesson Start date
 */
function lnStartLesson($uid,$cid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];

	$query = "SELECT MIN($course_trackingcolumn[atime]),MAX($course_trackingcolumn[atime])
						FROM $course_trackingtable, $course_enrollstable, $course_submissionstable 
						WHERE $course_trackingcolumn[eid]=$course_enrollscolumn[eid] AND $course_enrollscolumn[sid]=$course_submissionscolumn[sid] AND $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."' AND $course_enrollscolumn[uid] = '".lnVarPrepForStore($uid)."' ";

	$result = $dbconn->Execute($query);

	list($start,$end) = $result->fields;
	if (empty($start)) {
		return FALSE;
	}
	else {
		$sstart = date('Y-m-d',$start);
		$send = date('Y-m-d',$end);
		$stime = date('H:i', $start);
		$etime = date('H:i', $end);
		$ret = Date_Calc::dateFormat2($sstart, "%e/%m/%Y ").$stime .'-'. Date_Calc::dateFormat2($send, "%e/%m/%Y ").$etime;

		return $ret;
	}
}


/*
 * find number of learning lesson in a course
 */
function lnNoOfLearning($uid,$cid) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];

	$query = "SELECT COUNT($course_trackingcolumn[lid])
						FROM $course_trackingtable, $course_enrollstable, $course_submissionstable 
						WHERE $course_trackingcolumn[eid]=$course_enrollscolumn[eid] AND $course_enrollscolumn[sid]=$course_submissionscolumn[sid] AND $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."' AND $course_enrollscolumn[status]='"._LNSTATUS_STUDY."' AND $course_enrollscolumn[uid] = '".lnVarPrepForStore($uid)."'  GROUP BY $course_trackingcolumn[lid]";

	$result = $dbconn->Execute($query);

	$sum_lesson = $result->PO_RecordCount();

	return $sum_lesson;
}


/**
 * find number of lesson in a course
 */
function lnNoOfLesson($cid) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$query = "SELECT COUNT($lessonscolumn[lid])
						FROM $lessonstable 
						WHERE $lessonscolumn[cid]='". lnVarPrepForStore($cid) ."'";

	$result = $dbconn->Execute($query);
	list($sum_lesson) = $result->fields;

	return $sum_lesson;
}


/*
 * find compleate course
 */
function lnCompleteCourse($cid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$scorestable = $lntable['scores'];
	$scorescolumn = &$lntable['scores_column'];
	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];

	$query = "SELECT $lessonscolumn[lid] FROM $lessonstable WHERE $lessonscolumn[cid]='$cid'";
	$result = $dbconn->Execute($query);
	$pass = true;
	while(list($lid) = $result->fields) {
		$result->MoveNext();
		if (hasLessonTest($lid)) {
			$query = "SELECT MAX($scorescolumn[score]),$quizcolumn[option] FROM $scorestable,$quiztable
						WHERE $scorescolumn[qid]=$quizcolumn[qid] AND $quizcolumn[lid]='$lid' GROUP BY $quizcolumn[lid]";
			$result2 = $dbconn->Execute($query);
			list($maxscore,$option) = $result2->fields;
			if ($maxscore < _LNPASS_SCORE && $option & 2) {
				$pass=false;
				break;
			}
		}
		if (lnGetLearningStatus(lnSessionGetVar('uid'),$lid) == "") {
			$pass=false;
			break;
		}
	}

	return $pass;
}

/*
 * find sum course length
 */
function lnCourseLength($cid) {
	// Get arguments from argument array
	@extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$query = "SELECT SUM($lessonscolumn[duration])
						FROM $lessonstable 
						WHERE $lessonscolumn[cid]='". lnVarPrepForStore($cid) ."'";

	$result = $dbconn->Execute($query);
	list($course_length) = $result->fields;

	if (empty($course_length)) {
		$course_length = '1';
	}
	return $course_length;
}


/*
 * find Course date duration
 */
function lnCourseDate($cid) {
	$course_length = lnCourseLength($cid) - 1;
	$start = lnGetStartDateEnroll(lnGetEnroll($cid));
	$from = Date_Calc::dateFormat2($start, "%e %b");
	$to = Date_Calc::daysAddtoDate2($start, $course_length, "%e %b %y");

	$ret = $from . ' - ' . $to;

	return $ret;
}


/*
 * find start date without enroll
 */
function lnGetStartDateSubmission($sid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];

	$query = "SELECT $course_submissionscolumn[start] FROM $course_submissionstable WHERE $course_submissionscolumn[sid]='". lnVarPrepForStore($sid)."'";
	$result = $dbconn->Execute($query);

	return  $result->fields[0];
}

/*
 * find enroll with submissions ID
 * return SID
 */
function lnGetSubmission($cid) {

	$uid = lnSessionGetVar('uid');

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];

	$result = $dbconn->Execute("SELECT $course_enrollscolumn[sid] FROM $course_enrollstable, $course_submissionstable WHERE $course_enrollscolumn[sid]= $course_submissionscolumn[sid] and $course_enrollscolumn[uid]='". lnVarPrepForStore($uid) ."' and $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."' and $course_enrollscolumn[status]='0'");
	list($sid) = $result->fields;

	if (empty($sid)) {
		return false;
		
	}
	else {
		return $sid;
	}
}

/*
 * find enroll with submissions ID
 * return SID
 */
function lnGetSubmissionStudy($cid) {
	//add by nay for fix bug 20/09/2011
	$uid = lnSessionGetVar('uid');

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];

	$result = $dbconn->Execute("SELECT $course_enrollscolumn[sid] FROM $course_enrollstable, $course_submissionstable WHERE $course_enrollscolumn[sid]= $course_submissionscolumn[sid] and $course_enrollscolumn[uid]='". lnVarPrepForStore($uid) ."' and $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."' and $course_enrollscolumn[status]='1'");
	list($sid) = $result->fields;

	if (empty($sid)) {
		return "";
		
	}
	else {
		return $sid;
	}
}
/*
 * show course tab menu
 */
function tabMenu($vars,$no) {
	// Get arguments from argument array
	extract($vars);
	$uid = lnSessionGetVar('uid');
	//by Xeonkung
	$courseinfo = lnCourseGetVars($cid);
	$coursecode= $courseinfo['code'];
	$coursename = $courseinfo['title'];
	if(isset($eid))
	{
		$sid = lnGetSID($eid);
	}
	if (empty($sid)) {
		$sid = lnGetSubmissionID($cid);
	}
	if (!empty($sid)) {
		$start = lnGetStartDateSubmission($sid);
		$course_length = lnCourseLength($cid) - 1;
		$from = Date_Calc::dateFormat2($start, "%e %b");
		$to = Date_Calc::daysAddtoDate2($start, $course_length, "%e %b %y");
		$duration = $from . ' - ' . $to;
		echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <B>'.$courseinfo[code].': '.stripslashes($courseinfo[title]).' ('.$duration.')</B>';
	}
	else {
		echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <B>'.@$courseinfo[code].': '.stripslashes(@$courseinfo[title]).'</B>';
	}

	$menus = array();
	$ops = array();
	$imgs = array();

	echo '<BR>&nbsp;<div class="taMenu">';

	echo '<table  border="0" cellspacing="0" cellpadding="0"><tr><td>';

	if ($no == '1') {
		echo '<td><li id="current"><a class=tab href="index.php?mod=Courses&amp;op=course_detail&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._COURSEDESCRIPTION.'</span></a></li></td>';
	}
	else {
		echo '<td><li><a class=tab href="index.php?mod=Courses&amp;op=course_detail&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._COURSEDESCRIPTION.'</span></a></li></td>';
	}


	if ($no == '2') {
		echo '<td><li id="current"><a class="tab" href="index.php?mod=Courses&amp;op=course_lesson&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._COURSECONTENT.'</span></a></li></td>';
	}
	else {
		echo '<td><li><a class="tab" href="index.php?mod=Courses&amp;op=course_lesson&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._COURSECONTENT.'</span></a></li></td>';
	}

	if ((lnUserLoggedIn() && lnGetEnroll($cid)) || lnCourseInstructor($sid)) {

		if (lnModAvailable('Forums')) {
			if ($no == '3') {
				echo '<td><li id="current"><a class="tab" href="index.php?mod=Forums&amp;op=forum_list&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._WEBBOARD.'</span></a></li></td>';
			}
			else {
				echo '<td><li><a class="tab" href="index.php?mod=Forums&amp;op=forum_list&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._WEBBOARD.'</span></a></li></td>';
			}
		}
		/* Comment Chat Tab
		 if (lnModAvailable('Chat')) {
			if ($no == '4') {
			echo '<td><li id="current"><a class="tab" href="index.php?mod=Chat&amp;op=chat&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._CHATROOM.'</span></a></li></td>';
			}
			else {
			echo '<td><li><a class="tab" href="index.php?mod=Chat&amp;op=chat&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._CHATROOM.'</span></a></li></td>';
			}
			}
			*/
		if (!lnCourseInstructor($sid)){	//by Xeokung
			if ($no == '4') {
				echo '<td><li id="current"><a class="tab" href="index.php?mod=Courses&op=report_graph&uid='.$uid.'&cid='.$cid.'&sid='.$sid.'"><span>'._SHISTORY.'</span></a></li></td>';
			}
			else {
				echo '<td><li><a class="tab" href="index.php?mod=Courses&op=report_graph&uid='.$uid.'&cid='.$cid.'&sid='.$sid.'"><span>'._SHISTORY.'</span></a></li></td>';
			}
			if ($no == '5') {
				echo '<td><li id="current"><a class="tab" href="index.php?mod=Courses&amp;op=roster&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._ROSTER.'</span></a></li></td>';
			}
			else {
				echo '<td><li><a class="tab" href="index.php?mod=Courses&amp;op=roster&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._ROSTER.'</span></a></li></td>';
			}
		}
	}
	else {
	
	
			//echo $cid;
			//$sss = lnAllTime($cid);
			//echo $sss;	
			
	
		if  (lnUserLoggedIn() && lnSecAuthAction(0, "Courses::Student", "::", ACCESS_READ) &&  (lnGetEnrollStatus(lnSessionGetVar('uid'), $sid) != _LNSTATUS_STUDY)) {
		
			if (!lnAllTime($cid)) // condition for checking enrollment tab
			{
				
				if ($no == '3') {
					echo '<td><li id="current"><a class="tab" href="index.php?mod=Courses&amp;op=course_enroll&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._ENROLL.'</span></a></li></td>';
				}
				else {
					echo '<td><li><a class="tab" href="index.php?mod=Courses&amp;op=course_enroll&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._ENROLL.'</span></a></li></td>';
			}				
				
			}//end if lnAlltime		
		}
	}

	if (lnCourseInstructor($sid)) {
		if ($no == '6') {
			echo '<td><li id="current"><a class="tab" href="index.php?mod=Courses&amp;op=report&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._COURSEREPORT.'</span></a></li></td>';
		}
		else {
			echo '<td><li><a class="tab" href="index.php?mod=Courses&amp;op=report&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._COURSEREPORT.'</span></a></li></td>';
		}
		if ($no == '7') {
			echo '<td><li id="current"><a class="tab" href="index.php?mod=Courses&amp;op=report_detail&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._COURSEDETAIL.'</span></a></li></td>';
		}
		else {
			echo '<td><li><a class="tab"  href="index.php?mod=Courses&amp;op=report_detail&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._COURSEDETAIL.'</span></a></li></td>';
		}


		//Objective : Add Summary tab  , programmer : Orrawin
		if ($no == '8') {
			echo '<td><li id="current"><a class="tab" href="index.php?mod=Courses&amp;op=report_summary&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._COURSESUMMARY.'</span></a></li></td>';
		}
		else {
			echo '<td><li><a class="tab"  href="index.php?mod=Courses&amp;op=report_summary&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._COURSESUMMARY.'</span></a></li></td>';
		}
		//*************end Add Summary tab *****************

		//Objective : Add Questionaire Summary tab  , programmer : Orrawin
		if ($no == '9') {
			echo '<td><li id="current"><a class="tab" href="index.php?mod=Courses&amp;op=questionaire_summary&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._QUESTIONAIRESUMMARY.'</span></a></li></td>';
		}
		else {
			echo '<td><li><a class="tab"  href="index.php?mod=Courses&amp;op=questionaire_summary&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._QUESTIONAIRESUMMARY.'</span></a></li></td>';
		}
		//*************end Add Questionaire Summary tab *****************
	}

	/************** JoeJae Chat ***************/
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$JoeJae_activetable = $lntable['JoeJae_activetable'];
	$JoeJae_active_column = &$lntable['JoeJae_active_column'];

	$joe_query = mysql_query('SELECT '. $JoeJae_active_column['allow_chat'] .', '. $JoeJae_active_column['allow_member'] .' FROM '. $JoeJae_activetable .' WHERE '. $JoeJae_active_column['cid'] .' = "'. $cid .'" LIMIT 1');

	if(mysql_num_rows($joe_query) > 0)
	{

		list($allow_chat) = mysql_fetch_row($joe_query);

		if($allow_chat == 1)
		{
			if ($no == '10') {
				echo '<td><li id="current"><a class=tab href="index.php?mod=Courses&amp;op=joe_jae&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._JOEJAE.'</span></a></li></td>';
			}
			else {
				echo '<td><li><a class=tab href="index.php?mod=Courses&amp;op=joe_jae&amp;cid='.$cid.'&amp;sid='.$sid.'"><span>'._JOEJAE.'</span></a></li></td>';
			}
		}
	}

	/************** JoeJae Chat ***************/

	echo '</td></tr></table>';

	echo '</div>';
}


/*
 * tab admin course menu
 */
function tabCourseAdmin($cid,$no) {

	echo '<div class="taMenu">';

	echo '<table border="0" cellspacing="0" cellpadding="0"><tr>';
	if ($no == '1') {
		echo '<td><li id="current"><a class="tab" href="index.php?mod=Courses&amp;file=admin&amp;op=edit_course&amp;cid='.$cid.'"><span>'._COURSEDESCRIPTION.'</span></a></li></td>';
	}
	else {
		echo '<td><li><a class="tab"  href="index.php?mod=Courses&amp;file=admin&amp;op=edit_course&amp;cid='.$cid.'"><span>'._COURSEDESCRIPTION.'</span></a></li></td>';
	}
	if ($no == '2') {
		echo '<td><li id="current"><a class="tab" href="index.php?mod=Courses&amp;file=admin&amp;op=lesson&amp;cid='.$cid.'"><span>'._COURSELESSON.'</span></a></li></td>';
	}
	else {
		echo '<td><li><a class="tab" href="index.php?mod=Courses&amp;file=admin&amp;op=lesson&amp;cid='.$cid.'"><span>'._COURSELESSON.'</span></a></li></td>';
	}

	if ($no == '3') {
		echo '<td><li id="current"><a class="tab"  href="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;cid='.$cid.'"><span>'._COURSETEST.'</span></a></li></td>';
	}
	else {
		echo '<td><li><a class="tab" href="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;cid='.$cid.'"><span>'._COURSETEST.'</span></a></li></td>';
	}

	if (hasLesson($cid) && !lnAllTime($cid)) {

		if ($no == '4') {
			echo '<td><li id="current"><a class="tab"  href="index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;cid='.$cid.'"><span>'._COURSESCHEDULE.'</span></a></li></td>';
		}
		else {
			echo '<td><li><a class="tab" href="index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;cid='.$cid.'"><span>'._COURSESCHEDULE.'</span></a></li></td>';
		}

	}

	if ($no == '5') {
		echo '<td><li id="current"><a class="tab"  href="index.php?mod=Courses&amp;file=admin&amp;op=upfile&amp;cid='.$cid.'"><span>'._COURSEUPLOAD.'</span></a></li></td>';
	}
	else {
		echo '<td><li><a class="tab" href="index.php?mod=Courses&amp;file=admin&amp;op=upfile&amp;cid='.$cid.'"><span>'._COURSEUPLOAD.'</span></a></li></td>';
	}

	echo '</tr></table>';
	echo '</div>';
}


function lnAllTime($cid) {
	$courseinfo = lnCourseGetVars($cid);
	if ($courseinfo['sequence'] == '1') {
		return true;
	}
	else {
		return false;
	}
}

function lnCourseGetVars($cid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$vars = array();

	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];

	$query = "SELECT $coursescolumn[cid],$coursescolumn[code], $coursescolumn[sid], $coursescolumn[title], $coursescolumn[author], $coursescolumn[description],$coursescolumn[createon],$coursescolumn[purpose], $coursescolumn[prerequisite], $coursescolumn[reference],$coursescolumn[sequence]
              FROM $coursestable
              WHERE $coursescolumn[cid] = '" . lnVarPrepForStore($cid) ."'";

	$result = $dbconn->Execute($query);
	list($cid,$code,$sid, $title,$author,$description,$createon,$purpose,$prerequisite,$reference,$sequence) = $result->fields;
	$vars['cid'] = $cid;
	$vars['code'] = $code;
	$vars['sid'] = $sid;
	$vars['title'] = $title;
	$vars['author'] = $author;
	$vars['description'] = $description;
	$vars['createon'] = $createon;
	$vars['purpose'] = $purpose;
	$vars['prerequisite'] = $prerequisite;
	$vars['reference'] = $reference;
	$vars['sequence'] = $sequence;

	$result->Close();

	return($vars);
}

function lnLessonGetVars($lid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$vars = array();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

    $query = "SELECT $lessonscolumn[lid],$lessonscolumn[cid], $lessonscolumn[title], $lessonscolumn[description], $lessonscolumn[file], $lessonscolumn[duration],$lessonscolumn[weight],$lessonscolumn[lid_parent],$lessonscolumn[type],$lessonscolumn[smt]
              FROM $lessonstable
              WHERE $lessonscolumn[lid] = '" . lnVarPrepForStore($lid) ."'";

    $result = $dbconn->Execute($query);
	list($lid,$cid,$title,$description,$file,$duration,$weight,$lid_parent,$type,$smt) = $result->fields;
	$vars['lid'] = $lid;
	$vars['cid'] = $cid;
    $vars['title'] = $title;
    $vars['description'] = $description;
    $vars['file'] = $file;
    $vars['duration'] = $duration;
    $vars['weight'] = $weight;
    $vars['lid_parent'] = $lid_parent;
    $vars['type'] = $type;
    $vars['smt'] = $smt;
	$nos = lnGetLessonNo($lid,$nos=array());
	$no= join ('.',$nos);

	$vars['no']=$no;

	$result->Close();

	return($vars);
}

function lnQuizGetVars($qid) {		//bas edit: add assessment
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$vars = array();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];

	$query = "SELECT $quizcolumn[qid],$quizcolumn[cid],$quizcolumn[name],$quizcolumn[intro],$quizcolumn[attempts],$quizcolumn[feedback],$quizcolumn[correctanswers],$quizcolumn[grademethod],$quizcolumn[shufflequestions],$quizcolumn[testtime],$quizcolumn[grade],$quizcolumn[assessment],$quizcolumn[correctscore],$quizcolumn[wrongscore],$quizcolumn[noans],$quizcolumn[difficulty],$quizcolumn[difficultypriority]
              FROM $quiztable
              WHERE $quizcolumn[qid] = '" . lnVarPrepForStore($qid) ."'";
	//echo $query;
	$result = $dbconn->Execute($query);
	list($qid,$cid,$name,$intro, $attempts,$feedback,$correctanswers,$grademethod,$shufflequestions,$testtime,$grade,$assessment,$correctscore,$wrongscore,$noans,$difficulty,$difficultypriority) = $result->fields;
	$vars['qid'] = $qid;
	$vars['cid'] = $cid;
	$vars['name'] = $name;
	$vars['intro'] = $intro;
	$vars['attempts'] = $attempts;
	$vars['feedback'] = $feedback;
	$vars['correctanswers'] = $correctanswers;
	$vars['grademethod'] = $grademethod;
	$vars['shufflequestions'] = $shufflequestions;
	$vars['testtime'] = $testtime;
	$vars['grade'] = $grade;
	$vars['assessment'] = $assessment;
	$vars['correctscore'] = $correctscore;
	$vars['wrongscore'] = $wrongscore;
	$vars['noans'] = $noans;
	$vars['difficulty'] = $difficulty;
	$vars['difficultypriority'] = $difficultypriority;

	$result->Close();

	return($vars);
}


function lnGetLessonNo($lid,$nos) {

	if ($lid != 0 ) {
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$lessonstable = $lntable['lessons'];
		$lessonscolumn = &$lntable['lessons_column'];
		$result = $dbconn->Execute("SELECT $lessonscolumn[lid_parent],$lessonscolumn[weight] FROM $lessonstable WHERE $lessonscolumn[lid]='$lid'");
		list($lid_parent,$weight) = $result->fields;
		//		echo '>';
		//		print_r($nos);
		$nos = lnGetLessonNo($lid_parent,$nos);
		array_push($nos,$weight);
		//		echo '<BR>>>';
		//		print_r($nos);
		return $nos;
	}
	else {
		return $nos;
	}
}

function lnGetCourseID($sid) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];

	$uid = lnSessionGetVar('uid');

	$query = "SELECT $coursescolumn[cid] FROM $course_submissionstable,$coursestable WHERE $course_submissionscolumn[cid]=$coursescolumn[cid] and $course_submissionscolumn[sid]='$sid'";
	$result = $dbconn->Execute($query);

	return $result->fields[0];
}


/**
 * get Score
 */
function lnGetScore($uid, $lid) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$scorestable = $lntable['scores'];
	$scorescolumn = &$lntable['scores_column'];
	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$query0 = "SELECT $lessonscolumn[file] FROM $lessonstable WHERE $lessonscolumn[lid]='$lid'";
	$result0 = $dbconn->Execute($query0);
	list($qid) = $result0->fields;

	$query1 = "SELECT $quizcolumn[grademethod] FROM $quiztable WHERE $quizcolumn[qid]='$qid'";
	$result1 = $dbconn->Execute($query1);
	list($qrademethod) = $result1->fields;

	if($qrademethod=='1')
	{
		$query  = "SELECT max($scorescolumn[score]) FROM $scorestable, $course_enrollstable";
		$query .= " WHERE $course_enrollscolumn[eid]=$scorescolumn[eid] ";
		$query .= " AND $scorescolumn[lid]='$lid'  AND $course_enrollscolumn[uid]='$uid'";
	}
	else
	if($qrademethod=='2')
	{
		$query  = "SELECT avg($scorescolumn[score]) FROM $scorestable, $course_enrollstable";
		$query .= " WHERE $course_enrollscolumn[eid]=$scorescolumn[eid] ";
		$query .= " AND $scorescolumn[lid]='$lid'  AND $course_enrollscolumn[uid]='$uid'";
	}
	else
	if($qrademethod=='3')
	{
		$query  = "SELECT $scorescolumn[score] FROM $scorestable, $course_enrollstable";
		$query .= " WHERE $course_enrollscolumn[eid]=$scorescolumn[eid] ";
		$query .= " AND $scorescolumn[lid]='$lid'  AND $course_enrollscolumn[uid]='$uid' ORDER BY $scorescolumn[quiz_time] ASC";

		$result = $dbconn->Execute($query);

		$rets = array();
		while(list($score) = $result->fields) {
			$result->MoveNext();
			$rets[]=' '.$score.'%';
		}
		$score = $rets[count($rets)-1];
		return $score;
	}
	else
	{
		$query  = "SELECT avg($scorescolumn[score]) FROM $scorestable, $course_enrollstable";
		$query .= " WHERE $course_enrollscolumn[eid]=$scorescolumn[eid] ";
		$query .= " AND $scorescolumn[lid]='$lid'  AND $course_enrollscolumn[uid]='$uid'";
	}

	$result = $dbconn->Execute($query);
	return $result->fields[0];
}


function lnCourseNextID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
	$query = "SELECT MAX($coursescolumn[cid]) FROM $coursestable";

	$result = $dbconn->Execute($query);

	return $result->fields[0] + 1;
}


function lnLessonNextID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$query = "SELECT MAX($lessonscolumn[lid]) FROM $lessonstable";

	$result = $dbconn->Execute($query);

	return $result->fields[0] + 1;
}

function lnQuizNextID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$query = "SELECT MAX($quizcolumn[qid]) FROM $quiztable";

	$result = $dbconn->Execute($query);

	return $result->fields[0] + 1;
}

function lnQuizQuestionNextID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];
	$query = "SELECT MAX($quiz_questioncolumn[mcid]) FROM $quiz_questiontable";

	$result = $dbconn->Execute($query);

	return $result->fields[0] + 1;
}

function lnQuizChoiceNextID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];
	$query = "SELECT MAX($quiz_choicecolumn[chid]) FROM $quiz_choicetable";

	$result = $dbconn->Execute($query);

	return $result->fields[0] + 1;
}

function lnShowNew($createon) {
	$date1=date('Y-m-d');
	$date2=date('Y-m-d',$createon);
	if (Date_Calc::dateDiff2($date1,$date2) < 30) {;
	echo '<IMG SRC="images/new.gif" WIDTH="28" HEIGHT="11" BORDER=0 ALT="">';
	}
}


/*
 * show answer
 */
function lnShowAnswer($qid) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];

	$query = "SELECT $quizcolumn[option] FROM $quiztable WHERE $quizcolumn[qid]='$qid'";

	$result = $dbconn->Execute($query);

	return $result->fields[0] & 1;
}


/**
 *
 */
function  lnEnrollEnable($cid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];

	$query = "SELECT $course_submissionscolumn[start],
	$course_submissionscolumn[enroll]
										FROM $course_submissionstable WHERE $course_submissionscolumn[cid]='$cid' AND $course_submissionscolumn[active]='1'";
	$result = $dbconn->Execute($query);

	$m=0;
	while(list($start,$enroll) = $result->fields) {
		$result->MoveNext();
		if ($enroll != 1) {  // no enroll before
			$m++;
		}
	}

	if ($m==1 && Date_Calc::isPastDate2($start)) {
		return false;
	}
	else {
		return true;
	}


}

function lnGetEnrollStatus($uid, $sid) {
	$status = array('learning','complete','drop');
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	$query  = "SELECT $course_enrollscolumn[status] FROM $course_enrollstable";
	$query .= " WHERE $course_enrollscolumn[sid]='$sid' AND $course_enrollscolumn[uid]='$uid'";
	$result = $dbconn->Execute($query);

	return $result->fields[0];
}


function lnGetEnrollID($uid, $sid) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	$query  = "SELECT $course_enrollscolumn[eid] FROM $course_enrollstable";
	$query .= " WHERE $course_enrollscolumn[sid]='$sid' AND $course_enrollscolumn[uid]='$uid'";
	$result = $dbconn->Execute($query);
	list($eid) = $result->fields;
	return $eid;
}


/*
 * find enroll typewith couseID
 * return enroll ID
 */
function lnEnrollType($cid) {

	$uid = lnSessionGetVar('uid');

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];

	$result = $dbconn->Execute("SELECT $course_submissionscolumn[study] FROM $course_enrollstable, $course_submissionstable WHERE $course_enrollscolumn[sid]= $course_submissionscolumn[sid] and $course_enrollscolumn[uid]='". lnVarPrepForStore($uid) ."' and $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."' and $course_enrollscolumn[status]='0'");
	list($type) = $result->fields;

	if (empty($type)) {
		return false;
	}
	else {
		return $type;
	}
}

/*
 * find start date
 */
function lnGetStartDateEnroll($eid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];

	$query = "SELECT $course_submissionscolumn[start] FROM $course_enrollstable, $course_submissionstable WHERE $course_enrollscolumn[sid]=$course_submissionscolumn[sid] and $course_enrollscolumn[eid]='". lnVarPrepForStore($eid)."'";
	$result = $dbconn->Execute($query);

	return  $result->fields[0];
}
/*
 * find end date
 */
function lnGetfinishDateEnroll($cid,$eid ,$format = "%e %b %y"){
	$course_length = lnCourseLength($cid) - 1;
	$start = lnGetStartDateEnroll($eid);
	$to = Date_Calc::daysAddtoDate2($start, $course_length, $format);
	
	//return format "%e %b %y" : 1 ก.ย. 54,"%d-%m-%y" : 20-11-2011
	return $to;
}
/*
 * find enroll with couseID
 * return enroll ID
 */
function lnGetEnroll($cid) {

	$uid = lnSessionGetVar('uid');

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	$sql = "SELECT $course_enrollscolumn[eid] FROM $course_enrollstable, $course_submissionstable WHERE $course_enrollscolumn[sid]= $course_submissionscolumn[sid] and $course_enrollscolumn[uid]='". lnVarPrepForStore($uid) ."' and $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."' and $course_enrollscolumn[status]='"._LNSTATUS_STUDY."'";
	$result = $dbconn->Execute($sql);

	list($eid) = $result->fields;

	if (empty($eid)) {
		return false;
	}
	else {
		return $eid;
	}
}

function lnGetSubmissionID($cid) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	$course_tatable = $lntable['course_ta'];
	$course_tacolumn = &$lntable['course_ta_column'];

	// all guest users
	$sql = "SELECT $course_submissionscolumn[sid] FROM $course_submissionstable WHERE $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."' and $course_submissionscolumn[instructor]='"._LNSTUDENT_GUEST."'";
	//echo $sql;
	$result = $dbconn->Execute($sql);
	list($sid) = $result->fields;

	if (!empty($sid)) {
		return $sid;
	}

	// member users
	$sql = "SELECT $course_submissionscolumn[sid] FROM $course_submissionstable WHERE $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."' and $course_submissionscolumn[instructor]='"._LNSTUDENT_USER."'";
	//echo $sql;
	$result = $dbconn->Execute($sql);
	
	list($sid) = $result->fields;
	
	if (!empty($sid) && lnUserLoggedIn()) {
		return $sid;
	}


	// enroll or selected users
	$uid = lnSessionGetVar('uid');
	$sql = "SELECT $course_submissionscolumn[sid] FROM $course_enrollstable, $course_submissionstable WHERE $course_enrollscolumn[sid]= $course_submissionscolumn[sid] and $course_enrollscolumn[uid]='". lnVarPrepForStore($uid) ."' and $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."' and $course_enrollscolumn[status]='"._LNSTATUS_STUDY."'";
	$result = $dbconn->Execute($sql);
	list($sid) = $result->fields;
	
	if (!empty($sid)) {
		return $sid;
	}

	// teacher users
	$sql = "SELECT $course_submissionscolumn[sid] FROM $course_submissionstable WHERE $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."' and $course_submissionscolumn[instructor]='". lnVarPrepForStore($uid) ."'";
	$result = $dbconn->Execute($sql);
	list($sid) = $result->fields;

	if (!empty($sid)) {
		return $sid;
	}

	// ta users
	$result = $dbconn->Execute("SELECT $course_submissionscolumn[sid] FROM $course_submissionstable,$course_tatable WHERE $course_submissionscolumn[sid]=$course_tacolumn[sid] and $course_submissionscolumn[cid]='". lnVarPrepForStore($cid) ."' and $course_tacolumn[uid]='". lnVarPrepForStore($uid) ."'");
	list($sid) = $result->fields;

	if (!empty($sid)) {
		return $sid;
	}
	return false;
}

function lnGetSID($eid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$enrollstable = $lntable['course_enrolls'];
	$enrollscolumn = &$lntable['course_enrolls_column'];

	$query = "SELECT $enrollscolumn[sid]
              FROM $enrollstable
              WHERE $enrollscolumn[eid] = '" . lnVarPrepForStore($eid) ."'";

	$result = $dbconn->Execute($query);
	list($sid) = $result->fields;

	if (!empty($sid)) {
		return $sid;
	}
	return false;
}

function lnGetEID($uid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$enrollstable = $lntable['course_enrolls'];
	$enrollscolumn = &$lntable['course_enrolls_column'];

	$query = "SELECT $enrollscolumn[eid]
              FROM $enrollstable
              WHERE $enrollscolumn[uid] = '" . lnVarPrepForStore($uid) ."'";
	//echo $query."<br>";
	$result = $dbconn->Execute($query);
	list($eid) = $result->fields;
	$list_eid = array();
	while(list($eid) = $result->fields){
		$result->MoveNext();
		$list_eid[] =$eid;
	}
	if (!empty($list_eid)) {
		return $list_eid;
	}
	return false;
}


// To check score
/*
 function lnCheckScores($eid, $lid) {

 list($dbconn) = lnDBGetConn();
 $lntable = lnDBGetTables();

 $scorestable = $lntable['scores'];
 $scorescolumn = &$lntable['scores_column'];
 $quiztable = $lntable['quiz'];
 $quizcolumn = &$lntable['quiz_column'];
 $course_enrollstable = $lntable['course_enrolls'];
 $course_enrollscolumn = &$lntable['course_enrolls_column'];
 $lessonstable = $lntable['lessons'];
 $lessonscolumn = &$lntable['lessons_column'];
 $quiz_answertable = $lntable['quiz_answer'];
 $quiz_answercolumn = &$lntable['quiz_answer_column'];
 $quiz_questiontable = $lntable['quiz_multichoice'];
 $quiz_questioncolumn = &$lntable['quiz_multichoice_column'];

 $query0 = "SELECT $lessonscolumn[file] FROM $lessonstable WHERE $lessonscolumn[lid]='$lid'";
 $result0 = $dbconn->Execute($query0);
 list($qid) = $result0->fields;

 $query_grademethod = "SELECT $quizcolumn[grademethod] FROM $quiztable WHERE $quizcolumn[qid]='$qid'";
 $result_grademethod = $dbconn->Execute($query_grademethod);
 list($grademethod) = $result_grademethod->fields;

 $condition_score_query = "SELECT $quizcolumn[correctscore],$quizcolumn[wrongscore],$quizcolumn[noans] FROM $quiztable WHERE $quizcolumn[qid]='$qid'";
 $result_condition = $dbconn->Execute($condition_score_query);
 list($correctscore,$wrongscore,$noans) = $result_condition->fields;


 $query1 = "SELECT $quiz_questioncolumn[mcid] FROM $quiz_questiontable WHERE $quiz_questioncolumn[qid]='$qid'";
 $result1 = $dbconn->Execute($query1);

 $score=0;

 $query_attempts = "SELECT MAX($quiz_answercolumn[attempts]) FROM $quiz_answertable WHERE $quiz_answercolumn[qid]='$qid' AND $quiz_answercolumn[eid]='$eid'";
 $result_attempts = $dbconn->Execute($query_attempts);
 list($attempts) = $result_attempts->fields;


 while(list($mcid) = $result1->fields) {

 $query2 = "SELECT $quiz_answercolumn[useranswer] FROM $quiz_answertable
 WHERE $quiz_answercolumn[eid]='$eid'
 and $quiz_answercolumn[mcid]='$mcid'
 and $quiz_answercolumn[attempts]='$attempts'";
 $result2 = $dbconn->Execute($query2);
 list($uanswer) = $result2->fields;

 $query3 = "SELECT $quiz_questioncolumn[answer],$quiz_questioncolumn[score] FROM $quiz_questiontable WHERE $quiz_questioncolumn[mcid]='$mcid'";
 $result3 = $dbconn->Execute($query3);
 list($qanswer,$weight) = $result3->fields;


 if($weight!='0')
 {
 if($uanswer==null||$uanswer==0)
 {
 //$score_in_each_question = $noans;
 $score_in_each_question = ($noans)*($weight);
 }

 else
 {
 if($uanswer==$qanswer)
 {
 //$score_in_each_question = $correctscore;
 $score_in_each_question = ($correctscore)*($weight);
 }
 else
 {
 //$score_in_each_question = $wrongscore;
 $score_in_each_question = ($wrongscore)*($weight);
 }
 }//end else

 $score = $score+$score_in_each_question;

 }//end if $weight

 $result1->MoveNext();


 }//end while mcid


 $save_query = "INSERT INTO $scorestable VALUES ('".lnVarPrepForStore($eid)."', '".lnVarPrepForStore($lid)."', '".lnVarPrepForStore($score)."', null )";
 $save_result = $dbconn->Execute($save_query);


 return $score;

 }
 */

//lnCheckScores For V4
//edit by narananami
function lnCheckScores($eid, $lid) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$scorestable = $lntable['scores'];
	$scorescolumn = &$lntable['scores_column'];
	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_testtable = $lntable['quiz_test'];
	$quiz_testcolumn = &$lntable['quiz_test_column'];
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$quiz_answertable = $lntable['quiz_answer'];
	$quiz_answercolumn = &$lntable['quiz_answer_column'];
	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];

	$query0 = "SELECT $lessonscolumn[file], $quizcolumn[grade], $quizcolumn[difficultypriority],
	$quizcolumn[correctscore], $quizcolumn[wrongscore], $quizcolumn[noans]
	FROM $lessonstable,$quiztable 
	WHERE $lessonscolumn[file]=$quizcolumn[qid] AND $lessonscolumn[lid]='$lid'";
	$result0 = $dbconn->Execute($query0);
	list($qid,$grade,$difficultypriority,$correctscore,$wrongscore,$noans) = $result0->fields;

	//echo 'qid='.$qid.'::grade='.$grade.'::difficulty='.$difficultypriority;echo '<br>';
	//echo 'correctscore='.$correctscore.'::wrongscore='.$wrongscore.'::noans='.$noans;
	//echo '<br>';echo '<br>';

	//get max_attemprs
	$query_attempts = "SELECT MAX($quiz_answercolumn[attempts])
	FROM $quiz_answertable 
	WHERE $quiz_answercolumn[eid]='$eid'
	AND $quiz_answercolumn[qid]='$qid'
	AND	$quiz_answercolumn[lid]='$lid'";
	$result_attempts = $dbconn->Execute($query_attempts);
	list($attempts) = $result_attempts->fields;
	//echo 'MAX attemps='.$attempts;

	//get all multichoice on quiz_test
	$arr_multichoice = getMultichoiceMember($qid);
	//echo '<br>';
	//echo '<pre>';
	//print_r($arr_multichoice);
	//echo '</pre>';
	//echo '<br>';

	$score_difficulty = 0;
	$score_non_difficulty = 0;
	$student_correctscore_difficulty = 0;
	$student_correctscore = 0;
	$student_wrongscore_difficulty = 0;
	$student_wrongscore =0;
	$student_noansscore_difficulty = 0;
	$student_noansscore = 0;
	$score=0;

	for($i=0;$i<count($arr_multichoice);$i++){
		//get Total score
		$query_total_score = "SELECT $quiz_questioncolumn[mcid], $quiz_questioncolumn[answer], $quiz_questioncolumn[difficulty]
		FROM $quiz_questiontable  
		WHERE $quiz_questioncolumn[mcid]='$arr_multichoice[$i]'
		AND $quiz_questioncolumn[answer] <> 0
		AND $quiz_questioncolumn[difficulty] <> 0
		";
		//echo '<br>';echo $query_total_score;echo '<br>';
		$result_total_score = $dbconn->Execute($query_total_score);
		list($mcid,$answer,$mcid_difficulty) = $result_total_score->fields;
		if ($difficultypriority==1){
			$score_difficulty= $score_difficulty + $mcid_difficulty*$correctscore;
		}else{
			$score_non_difficulty= $score_non_difficulty + $correctscore;
		}

		//get user score
		$query_student_score= "SELECT $quiz_answercolumn[useranswer]
			FROM $quiz_answertable  
			WHERE $quiz_answercolumn[mcid]='$arr_multichoice[$i]' 
			AND $quiz_answercolumn[eid]='$eid' 
			AND $quiz_answercolumn[attempts]='$attempts'
			AND	$quiz_answercolumn[qid]='$qid'
			AND	$quiz_answercolumn[lid]='$lid'
			";
		//echo '<br>-->';echo $query_student_score;echo '<br>';
		$result_student_score = $dbconn->Execute($query_student_score);
		list($useranswer) = $result_student_score->fields;
		//echo '<br>=========>';echo $useranswer;echo '<br>';
		if($useranswer){
			if($answer==$useranswer)
			{	//echo 'get correctscore score<br>';
			if ($difficultypriority==1){
				$student_correctscore_difficulty= $student_correctscore_difficulty + ($correctscore*$mcid_difficulty);
			}else{
				$student_correctscore= $student_correctscore + $correctscore;
			}
			}else if(($answer!=$useranswer)&&($useranswer!=0))
			{	//echo 'get wrongscore score<br>';
			if ($difficultypriority==1){
				$student_wrongscore_difficulty= $student_wrongscore_difficulty + ($wrongscore*$mcid_difficulty);
			}else{
				$student_wrongscore= $student_wrongscore + $wrongscore;
			}
			}else if($useranswer==0)
			{	//echo 'get noansscore score<br>';
			if ($difficultypriority==1){
				$student_noansscore_difficulty= $student_noansscore_difficulty + ($noans*$mcid_difficulty);
			}else{
				$student_noansscore= $student_noansscore + $noans;
			}
			}
		}
	}


	if ($difficultypriority==1){
		$score=(($student_correctscore_difficulty+$student_wrongscore_difficulty+$student_noansscore_difficulty)/$score_difficulty)*$grade;
	}else{
		$score=(($student_correctscore+$student_wrongscore+$student_noansscore)/$score_non_difficulty)*$grade;
	}
	$score = round($score, 2);
	//echo 'score='.$score;

	//Score

	$query_savescore = "SELECT COUNT(*)
		FROM $scorestable 
		WHERE $scorescolumn[eid]=$eid
		AND $scorescolumn[lid]=$lid
		";
	$result_savescore = $dbconn->Execute($query_savescore);
	list($count) = $result_savescore->fields;

	//echo '<br>';echo 'Score='.$score;echo '<br>';
	//echo 'Count ='.$count; echo '<br>';
	//echo 'Attempts ='.$attempts; echo '<br>';

	if(($count!='null')||($count!='')){
		if($count<$attempts){
			//echo 'Insert';
			$save_query = "INSERT INTO $scorestable
			VALUES ('".lnVarPrepForStore($eid)."', '".lnVarPrepForStore($lid)."', '".lnVarPrepForStore($score)."', NOW( ), '".lnVarPrepForStore($attempts)."' )";
			$save_result = $dbconn->Execute($save_query);
		}else{
			//echo 'Update';
			//,$scorescolumn[quiz_time] = NOW( )
			$save_query = "UPDATE $scorestable
			SET $scorescolumn[score] = $score
			WHERE $scorescolumn[eid]=$eid
			AND $scorescolumn[lid]=$lid
			AND $scorescolumn[attempts]=$attempts";
			$save_result = $dbconn->Execute($save_query);
		}
		return $score;
	}

}

//lnCheckScoresHot For V4
//edit by narananami
function lnCheckScoresHot($eid, $lid) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$scorestable = $lntable['scores'];
	$scorescolumn = &$lntable['scores_column'];
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$query_savescore = "SELECT $scorescolumn[score]
		FROM $scorestable 
		WHERE $scorescolumn[eid]=$eid
		AND $scorescolumn[lid]=$lid
		ORDER BY $scorescolumn[quiz_time] DESC";
	$result_savescore = $dbconn->Execute($query_savescore);
	$score = $result_savescore->fields[0];

	return $score;

}

function getMultichoiceMember($qid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$quiztest_TB = $lntable['quiz_test'];
	$quiztest_COL = &$lntable['quiz_test_column'];
	$quizmulti_TB = $lntable['quiz_multichoice'];
	$quizmulti_COL = &$lntable['quiz_multichoice_column'];
	//=== Query Member's Header
	$sql = "SELECT $quiztest_COL[mcid],$quizmulti_COL[type] FROM
	$quiztest_TB,
	$quizmulti_TB WHERE
	$quiztest_COL[mcid] = $quizmulti_COL[mcid] AND
	$quiztest_COL[qid] = '$qid' ORDER BY
	$quiztest_COL[weight] ASC";
	$result = $dbconn->Execute($sql);

	//echo '<br>';
	//echo '<pre>';
	//echo $sql;
	//echo '</pre>';
	//echo '<br>';

	//== array mcid
	$arr_mcid;
	while(list($mcid,$type) = $result->fields){
		$result->MoveNext();
		if($type == 1){
			$arr_mcid[] = $mcid;
			continue;
		}
		//=== Query Tail
		$sql = "SELECT MIN($quizmulti_COL[mcid]) FROM
		$quizmulti_TB WHERE
		$quizmulti_COL[mcid] > '$mcid' AND
		$quizmulti_COL[answer] = '0' AND
		$quizmulti_COL[difficulty] = '0' ORDER BY
		$quizmulti_COL[mcid] ASC";
		$stopMcid = $dbconn->Execute($sql);
		list($stopMcid) = $stopMcid->fields;
		$sql = "SELECT $quizmulti_COL[mcid] FROM
		$quizmulti_TB WHERE
		$quizmulti_COL[mcid] >= '$mcid' AND
		$quizmulti_COL[mcid] <= '$stopMcid' ORDER BY
		$quizmulti_COL[mcid] ASC";
		$mcidS = $dbconn->Execute($sql);
		while(list($mcidT) = $mcidS->fields){
			$mcidS->MoveNext();
			$arr_mcid[] = $mcidT;
		}
	}
	return $arr_mcid;
}

function getGuid() {
  
   // The field names refer to RFC 4122 section 4.1.2

   return sprintf('%04x%04x-%04x-%03x4-%04x-%04x%04x%04x',
       mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
       mt_rand(0, 65535), // 16 bits for "time_mid"
       mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
       bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
           // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
           // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
           // 8 bits for "clk_seq_low"
       mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node" 
   ); 
}

function lnSchoolGetVars($sid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$vars = array();

	$schoolstable = $lntable['schools'];
	$schoolscolumn = &$lntable['schools_column'];

    $query = "SELECT $schoolscolumn[sid],$schoolscolumn[code], $schoolscolumn[name], $schoolscolumn[description], $schoolscolumn[logo]
              FROM $schoolstable
              WHERE $schoolscolumn[sid] = '" . lnVarPrepForStore($sid) ."'";
	//echo $query;
    $result = $dbconn->Execute($query);
	list($sid,$code,$name,$description,$logo) = $result->fields;
	$vars['lid'] = $sid;
	$vars['cid'] = $code;
    $vars['title'] = $name;
    $vars['description'] = $description;
    $vars['file'] = $logo;

	$result->Close();

	return($vars);
}

?>