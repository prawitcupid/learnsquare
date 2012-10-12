<?php
/*
* show study report
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}


/* - - - - - - - - - - - */
include 'header.php';
OpenTable();

echo '<TABLE  WIDTH=100% HEIGHT=400><TR VALIGN="TOP"><TD>';
//echo lnBlockTitle($mod,'report');
echo '<p class="header"><b>'._REPORT_TITLE.'</b></p>';
echo '<BR>'._REPORTDESC;

$vars= array_merge($_GET,$_POST);	
/* options */

switch($op) {
	case "edit_enroll":		editEnroll($vars); break;
	case "show_report":  		showReport($vars); break;
	case "save_questionaire":	saveQuestionaire($eid,$sid,$vars);showReport($var); break;
	default :			showReport($vars);
}

echo '</TD></TR></TABLE>';

CloseTable();
include 'footer.php';
/* - - - - - - - - - - - */


/*
* edit enrollment
*/
function editEnroll($vars) {
    extract($vars);
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	// update enroll settings
	if ($op2 == "update") {
		$optionsum = $shownick+$notify;
		if (empty($drop)) $drop=1;
		$query = "UPDATE $course_enrollstable SET $course_enrollscolumn[status]='$drop' , $course_enrollscolumn[options]='$optionsum' WHERE $course_enrollscolumn[eid] ='$eid'";

		$dbconn->Execute($query);
	}
	

	// show form
	$query = "SELECT $course_enrollscolumn[options],$course_enrollscolumn[status] FROM $course_enrollstable WHERE $course_enrollscolumn[eid] ='$eid'";
	$result = $dbconn->Execute($query);
	list($options,$status) = $result->fields;
	if ($status == 2) $statuschecked='checked';
	if ($options & 1) $nickchecked='checked';
	if ($options & 2) $notifychecked='checked';

	$courseinfo = lnCourseGetVars($cid);
	echo '<P><IMG SRC="images/global/bl_org.gif" WIDTH="11" HEIGHT="18" BORDER="0" ALT="" align="absmiddle"> <B>'._EDITENROLL.'&nbsp;'.$courseinfo['code'].' : '.$courseinfo['title'].'</B><P>';

	echo '<FORM METHOD=POST ACTION="index.php">';
	echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">';
	echo '<INPUT TYPE="hidden" NAME="file" VALUE="report">';
	echo '<INPUT TYPE="hidden" NAME="op" VALUE="edit_enroll">';
	echo '<INPUT TYPE="hidden" NAME="op2" VALUE="update">';
	echo '<INPUT TYPE="hidden" NAME="eid" VALUE="'.$eid.'">';
	
	echo '<IMG SRC="images/global/bul.jpg" WIDTH="8" HEIGHT="8" BORDER=0 ALT=""> <FONT COLOR="#006666"><B>'._DROP.'</B></FONT>&nbsp;';
	echo '&nbsp;'._DROPDESC.'<P>';
	echo '<INPUT TYPE="checkbox" NAME="drop" VALUE="'._LNSTATUS_DROP.'" '.$statuschecked.'><B> '._DROPCHECK.'</B><HR>';

	
	echo '<IMG SRC="images/global/bul.jpg" WIDTH="8" HEIGHT="8" BORDER=0 ALT=""> <FONT COLOR="#006666"><B>'._SHOWNICKNAME.'</B></FONT>&nbsp;';
	echo '&nbsp;'._SHOWNICKNAMEDESC.'<P>';
	echo '<INPUT TYPE="checkbox" NAME="shownick" VALUE="1" '.$nickchecked.'><B> '._SHOWNICKNAMECHECK.'</B><HR>';

//interface ให้เลือกรับข่าวสาร
	//echo '<IMG SRC="images/global/bul.jpg" WIDTH="8" HEIGHT="8" BORDER=0 ALT=""> <FONT COLOR="#006666"><B>'._NOTIFY.'</B></FONT>&nbsp;';
	//echo '&nbsp;'._NOTIFYDESC.'<P>';
	//echo '<INPUT TYPE="checkbox" NAME="notify" VALUE="2" '.$notifychecked.'><B> '._NOTIFYCHECK.'</B><HR>';
//++++++++++++++

	echo "<CENTER><INPUT TYPE=\"submit\" VALUE=\"Submit\" CLASS=\"button_org\">";
	echo "&nbsp; <INPUT TYPE=\"button\" VALUE=\"Cancel\" CLASS=\"button_org\" onclick=\"javascript:window.open('index.php?mod=Courses&amp;file=report','_self')\"></CENTER><BR><BR>";

	echo '</FORM>';

}


/*
* show report
*/
function showReport($vars) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];  

	// 1.  study courses
	echo '<P>';
	echo '<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=0 BORDER=0>';
	echo '<TR BGCOLOR=#FFFFFF HEIGHT=20><TD colsapn="3"><IMG SRC="images/global/bl_red.gif" WIDTH="11" HEIGHT="7" BORDER="0" ALT=""><B>'._COURSESTART.'</B></TD></TR>';
	echo '<TR BGCOLOR=#669900 HEIGHT=20><TD><FONT  COLOR="#FFFFFF"><B>'._BROWSECOURSE.'</B></FONT></TD><TD ALIGN=CENTER><FONT  COLOR="#FFFFFF"><B>'._DATESTART.'</B></FONT></TD><TD>&nbsp;</TD></TR>';

	$query = "SELECT $course_submissionscolumn[cid], $course_submissionscolumn[start], $course_enrollscolumn[eid] FROM $course_enrollstable, $course_submissionstable WHERE  $course_submissionscolumn[sid] = $course_enrollscolumn[sid] AND  $course_enrollscolumn[uid]='". lnSessionGetVar('uid') ."' AND $course_enrollscolumn[status]='"._LNSTATUS_STUDY."'";
	$result = $dbconn->Execute($query);
	for ($i=1; list($cid,$start,$eid) = $result->fields; $i++) {
		$result->MoveNext();
		$courseinfo = lnCourseGetVars($cid);
		$course_length = lnCourseLength($cid) - 1;
		$from = Date_Calc::dateFormat2($start, "%e %b");
		$to = Date_Calc::daysAddtoDate2($start, $course_length, "%e %b %y");
		$duration = $from . ' - ' . $to;
		echo '<TR BGCOLOR=#CFED8A ><TD>'.$i.'. <A HREF="index.php?mod=Courses&op=course_lesson&cid='.$cid.'&eid='.$eid.'">'.$courseinfo['title'].'</A></TD>';
		echo '<TD ALIGN="CENTER" WIDTH="120">'.$duration.'</TD>';
		echo "<TD ALIGN=\"CENTER\" WIDTH=\"50\"><INPUT TYPE=\"submit\" VALUE=\"Edit\" CLASS=\"button_org\" OnClick=\"javascript:window.open('index.php?mod=Courses&amp;file=report&amp;op=edit_enroll&amp;cid=$cid&amp;eid=$eid','_self')\"></TD>";
		echo '</TR>';
	}

	echo '</TABLE>';

	// 2.  passed courses
	echo '<P>';
	echo '<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=0 BORDER=0>';
	echo '<TR BGCOLOR=#FFFFFF HEIGHT=20><TD colsapn="3"><IMG SRC="images/global/bl_red.gif" WIDTH="11" HEIGHT="7" BORDER="0" ALT=""><B>'._COURSEPASSED.'</B></TD></TR>';
	echo '<TR BGCOLOR=#669900 HEIGHT=20><TD><FONT  COLOR="#FFFFFF"><B>'._BROWSECOURSE.'</B></FONT></TD>';
	echo '<TD ALIGN=CENTER WIDTH="100"><FONT  COLOR="#FFFFFF"><B>'._QUESTIONAIRE.'</B></FONT></TD>';
	echo '<TD ALIGN=CENTER WIDTH="200"><FONT  COLOR="#FFFFFF"><B>'._DATESTART.'</B></FONT></TD></TR>';

	$query = "SELECT $course_submissionscolumn[cid], $course_submissionscolumn[start], $course_enrollscolumn[eid] FROM $course_enrollstable, $course_submissionstable WHERE  $course_submissionscolumn[sid] = $course_enrollscolumn[sid] AND  $course_enrollscolumn[uid]='". lnSessionGetVar('uid') ."' AND $course_enrollscolumn[status]='"._LNSTATUS_COMPLETE."'";

	$result = $dbconn->Execute($query);
	for ($i=1; list($cid,$start,$eid) = $result->fields; $i++) {
		$result->MoveNext();
		$courseinfo = lnCourseGetVars($cid);
		$course_length = lnCourseLength($cid) - 1;
		$from = Date_Calc::dateFormat2($start, "%e %b");
		$to = Date_Calc::daysAddtoDate2($start, $course_length, "%e %b %y");
		$duration = $from . ' - ' . $to;
		echo '<TR BGCOLOR=#CFED8A ><TD>'.$i.'. <IMG SRC="images/cer.gif" WIDTH="15" HEIGHT="12" BORDER="0" ALT="" align="absmiddle"><A HREF="index.php?mod=Courses&file=certificate&eid='.$eid.'" target="_blank">'.$courseinfo['title'].'</A></TD>';
		    
		extract($vars);
		
		$questionairetable = $lntable['questionaire'];
		$questionairecolumn = &$lntable['questionaire_column'];
		$query_questionaire = "SELECT $questionairecolumn[eid] FROM $questionairetable  WHERE  $questionairecolumn[eid] = $eid";
		$result_questionaire = $dbconn->Execute($query_questionaire);
		list ($eid_count) = $result_questionaire->fields;
		
		$sid=lnGetSID($eid); //send $sid for check course submission
	
		/**
		 * Fixed Bug
		 * fixed error loop saveQuestionaire
		 * solve: 	remove it
		 * solution: 	save from $op type $op = save_questionair
		 * by: 		pukkapol.tan@nectec.or.th
		 * date:	2012.10.11 
		 **/
			
		if($eid_count==null)
		{
			// ***** remove it
			//if($action=="savequestionaire")
			//{		
			//	echo "<TD ALIGN=\"CENTER\" WIDTH=\"100\">";
			//	$check_q = saveQuestionaire($eid,$sid,$vars);
			//	echo $check_q;
			//	echo "</TD>";										
			//}
			//else
			//{
				echo "<TD ALIGN=\"CENTER\" WIDTH=\"100\"><INPUT TYPE=\"submit\" VALUE=\"Reply\" CLASS=\"button_org\" OnClick=\"javascript:window.open('index.php?mod=Courses&amp;file=questionaire&amp;op=qtForm&amp;eid=$eid&amp;sid=$sid','_self')\"></TD>";						
			
			//}
		}else{
			echo "<TD ALIGN=\"CENTER\" WIDTH=\"100\">";
			echo "ส่งแล้ว";	
			echo "</TD>";
		}
		
		
		echo '<TD ALIGN="center">'.$duration.'</TD></TR>';
	}//end for

	echo '</TABLE>';

	// 3. Fail
	echo '<P>';
	echo '<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=0 BORDER=0>';
	echo '<TR BGCOLOR=#FFFFFF HEIGHT=20><TD colsapn="2"><IMG SRC="images/global/bl_red.gif" WIDTH="11" HEIGHT="7" BORDER="0" ALT=""><B>'._COURSEFAILED.'</B></TD></TR>';
	echo '<TR BGCOLOR=#669900 HEIGHT=20><TD><FONT  COLOR="#FFFFFF"><B>'._BROWSECOURSE.'</B></FONT></TD><TD ALIGN=CENTER WIDTH="200"><FONT  COLOR="#FFFFFF"><B>'._DATESTART.'</B></FONT></TD></TR>';

	$query = "SELECT $course_submissionscolumn[cid], $course_submissionscolumn[start], $course_enrollscolumn[eid] FROM $course_enrollstable, $course_submissionstable WHERE  $course_submissionscolumn[sid] = $course_enrollscolumn[sid] AND  $course_enrollscolumn[uid]='". lnSessionGetVar('uid') ."' AND $course_enrollscolumn[status]='"._LNSTATUS_FAIL."'";

	$result = $dbconn->Execute($query);
	for ($i=1; list($cid,$start,$eid) = $result->fields; $i++) {
		$result->MoveNext();
		$courseinfo = lnCourseGetVars($cid);
		$course_length = lnCourseLength($cid) - 1;
		$from = Date_Calc::dateFormat2($start, "%e %b");
		$to = Date_Calc::daysAddtoDate2($start, $course_length, "%e %b %y");
		$duration = $from . ' - ' . $to;
		echo '<TR BGCOLOR=#CFED8A ><TD>'.$i.'. <A HREF="index.php?mod=Courses&op=course_lesson&cid='.$cid.'&eid='.$eid.'">'.$courseinfo['title'].'</A></TD>';
		echo '<TD ALIGN="center">'.$duration.'</TD></TR>';
	}

	echo '</TABLE>';

	//separate drop and fail
	//programmer : bas
	// 4. drop courses
	echo '<P>';
	echo '<TABLE WIDTH=100% CELLPADDING=2 CELLSPACING=0 BORDER=0>';
	echo '<TR BGCOLOR=#FFFFFF HEIGHT=20><TD colsapn="2"><IMG SRC="images/global/bl_red.gif" WIDTH="11" HEIGHT="7" BORDER="0" ALT=""><B>'._COURSEDROP.'</B></TD></TR>';
	echo '<TR BGCOLOR=#669900 HEIGHT=20><TD><FONT  COLOR="#FFFFFF"><B>'._BROWSECOURSE.'</B></FONT></TD><TD ALIGN=CENTER WIDTH="200"><FONT  COLOR="#FFFFFF"><B>'._DATESTART.'</B></FONT></TD></TR>';

	$query = "SELECT $course_submissionscolumn[cid], $course_submissionscolumn[start], $course_enrollscolumn[eid] FROM $course_enrollstable, $course_submissionstable WHERE  $course_submissionscolumn[sid] = $course_enrollscolumn[sid] AND  $course_enrollscolumn[uid]='". lnSessionGetVar('uid') ."' AND $course_enrollscolumn[status]='"._LNSTATUS_DROP."'";

	$result = $dbconn->Execute($query);
	for ($i=1; list($cid,$start,$eid) = $result->fields; $i++) {
		$result->MoveNext();
		$courseinfo = lnCourseGetVars($cid);
		$course_length = lnCourseLength($cid) - 1;
		$from = Date_Calc::dateFormat2($start, "%e %b");
		$to = Date_Calc::daysAddtoDate2($start, $course_length, "%e %b %y");
		$duration = $from . ' - ' . $to;
		echo '<TR BGCOLOR=#CFED8A ><TD>'.$i.'. <A HREF="index.php?mod=Courses&op=course_lesson&cid='.$cid.'&eid='.$eid.'">'.$courseinfo['title'].'</A></TD>';
		echo '<TD ALIGN="center">'.$duration.'</TD></TR>';
	}

	echo '</TABLE>';
	//end
	
}


function saveQuestionaire($eid,$sid,$vars)
{
	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	
	$questionairetable = $lntable['questionaire'];
	$questionairecolumn = &$lntable['questionaire_column'];

	$query = "INSERT INTO $questionairetable
				  ($questionairecolumn[eid],
				 	 $questionairecolumn[sid],
					$questionairecolumn[t1_1],
					$questionairecolumn[t1_2],
					$questionairecolumn[t1_3],
					$questionairecolumn[t1_4],
					$questionairecolumn[t1_5],
					$questionairecolumn[t2_1],
					$questionairecolumn[t2_2],
					$questionairecolumn[t3_1],
					$questionairecolumn[t3_2],
					$questionairecolumn[t3_3],
					$questionairecolumn[t4]
					  )
					VALUES ('" . lnVarPrepForStore($eid) . "',
						  '" . lnVarPrepForStore($sid) . "',
						  '" . lnVarPrepForStore($t1_1) . "',
						  '" . lnVarPrepForStore($t1_2) . "',
						  '" . lnVarPrepForStore($t1_3) . "',
						  '" . lnVarPrepForStore($t1_4) . "',
						  '" . lnVarPrepForStore($t1_5) . "',						  						  						  						  
						  '" . lnVarPrepForStore($t2_1) . "',
						  '" . lnVarPrepForStore($t2_2) . "',
						  '" . lnVarPrepForStore($t3_1) . "',
						  '" . lnVarPrepForStore($t3_2) . "',
						  '" . lnVarPrepForStore($t3_3) . "',						  						  						  						  
						  '" . lnVarPrepForStore($t4) . "'
					  )";

			$dbconn->Execute($query);
			// remove by pukkapol.tan@nectec
			/*if ($dbconn->ErrorNo() != 0) {
			 	//echo "error";
        			return "ส่งไม่สำเร็จ";
    			} 
    			else
    			{
    				return "ส่งแล้ว";
    			}*/
			// end remove
}
?>
