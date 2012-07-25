<?php
/**
 *  Course Administration Module
 */
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}


/* - - - - MAIN- - - - - */
$vars= array_merge($_GET,$_POST);

if ($op == 'import_lesson_form') { // import scorm to lesson
	importLesson($vars);
	exit;
}

include 'header.php';

/** Navigator **/
$menus = $links = array();
if (lnUserAdmin( lnSessionGetVar('uid'))) {
	$menus[] = _ADMINMENU;
	$links[]='index.php?mod=Admin';
}

$menus[]= _ADDCOURSE;
$links[]= 'index.php?mod=Courses&amp;file=admin';
/** Navigator **/

if (!empty($op)) {
	// include more functions

	switch($op) {
		case "delete_course": deleteCourse($cid); break;
		case "add_course": addCourse($vars); return;
		case "add_form" : addCourseForm(); return;
		case "edit_course": editCourse($cid); return;
		case "update_course": updateCourse($vars); editCourse($cid); return;

		//Quiz Bank
		case "quiz" :
		  	include("modules/Quiz/admin.php");
			quiz($vars); 
			return;

			//+ schedule.php
		case "schedule" :
			include("schedule.php");
			schedule($vars); return;

			//+ lessonadmin.php
		case "lesson" :
			include("lessonadmin.php");
			lesson($vars); return;

			//+ upfileadmin.php
		case "upfile":
			include("upfileadmin.php");
			upfile($vars); return;
			
		//+ searchRepository.php
		case "repository":
			include("modules/Repository/searchRepository.php");
			search($vars); return;
	}
}

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$coursestable = $lntable['courses'];
$coursescolumn = &$lntable['courses_column'];
$schoolstable = $lntable['schools'];
$schoolscolumn = &$lntable['schools_column'];

$query = "SELECT $coursescolumn[cid],
$coursescolumn[code],
$coursescolumn[title],
$schoolscolumn[name]
FROM $coursestable LEFT JOIN $schoolstable ON $coursescolumn[sid]=$schoolscolumn[sid] ";
if (!lnUserAdmin(lnSessionGetVar('uid'))) {
	$query .= "  WHERE $coursescolumn[author]=".lnSessionGetVar('uid');
}

$query .= " ORDER BY $coursescolumn[sid],$coursescolumn[code]";

$result = $dbconn->Execute($query);

if($dbconn->ErrorNo() != 0) {
	return;
}


/** Navigator **/
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<table width= 100% cellpadding=0 cellspacing=0 border=0>';
echo '<tr><td>&nbsp;</td><td align=right><A HREF="index.php?mod=Courses&amp;file=admin&amp;op=add_form"><IMG SRC="modules/Courses/images/create.gif"  BORDER=0 ALT=""></A>&nbsp;&nbsp;&nbsp;</td></tr>';
echo '</table>';

if ($result->EOF) {
	echo '<BR><B><CENTER>'. _NOCOURSE.'</CENTER></B><P>';
}

echo '<table width= 100% cellpadding=2 cellspacing=1 border=0>';
for ($osname='',$i=1; list($cid,$code,$cname,$sname) = $result->fields; $i++) {
	$sname=stripslashes($sname);
	$cname=stripslashes($cname);
	$result->MoveNext();
	if($osname != $sname) {
		$osname=$sname;
		echo '<tr><td class="head" colspan=3 align=left><B>'.$sname.'</B></td></tr>';
	}

	echo "<tr valign=middle>";
	echo '<td width=60 align=left>&nbsp;<IMG SRC="images/global/line.gif" BORDER="0" ALT="" align="absmiddle">&nbsp;'.$code.'</td>';
	echo "<td><A HREF=\"index.php?mod=Courses&amp;file=admin&amp;op=edit_course&amp;cid=$cid\">$cname</A></td>";
	echo "<td width=90 align=center>";
	echo "<A HREF=\"index.php?mod=Courses&amp;file=admin&amp;op=edit_course&amp;cid=$cid\"><IMG SRC=\"images/global/edit.gif\"  BORDER=0 ALT="._EDIT."></A>&nbsp;";
	$cname=str_replace("'","",$cname);
	$cname=str_replace('"',"",$cname);
	echo "<A HREF=\"javascript: if(confirm('Delete $cname?')) window.open('index.php?mod=Courses&amp;file=admin&amp;op=delete_course&amp;cid=$cid','_self')\"><IMG SRC=\"images/global/delete.gif\"  BORDER=0 ALT="._DELETE."></A></td>";
	echo "</tr>";
}

echo '</table>';

CloseTable();

include 'footer.php';

/* - - - - END MAIN- - - - - */



/**
 *  add course form
 */
function addCourseForm() {
	global $menus,$links;

	/** Navigator **/
	$menus[]= _ADDCOURSE;
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/

	?>
<script language="javaScript">
		function formSubmit() {
			if(checkFields()) document.forms.Course.submit();
		}
    	function checkFields() {
			var code = document.forms.Course.course_code.value;
			var title = document.forms.Course.course_title.value;
			var description = document.forms.Course.course_desc.value;
		
			if (code  == "" ) {
				alert("empty code");
				document.forms.Course.course_code.focus();
				return false;
			}
			if (title  == "" ) {
				alert("empty title");
				document.forms.Course.course_title.focus();
				return false;
			}
			if (description  == "" ) {
				alert("empty description");
				document.forms.Course.course_desc.focus();
				return false;
			}

			return true; 
		}
</script>
	<?
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$schoolstable = $lntable['schools'];
	$schoolscolumn = &$lntable['schools_column'];
	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];
	$groupstable = $lntable['groups'];
	$groupscolumn = &$lntable['groups_column'];
	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];

	$query = "SELECT  $schoolscolumn[sid],$schoolscolumn[code],$schoolscolumn[name]
	FROM  $schoolstable
	ORDER BY $schoolscolumn[sid]";
	$result = $dbconn->Execute($query);

	if($dbconn->ErrorNo() != 0) {
		return;
	}

	echo '<BR><fieldset><legend>'._ADDCOURSE.'</legend>'
	.'<TABLE WIDTH="550" CELLPADDING=1 CELLSPACING=0 BORDER=0>'
	.'<FORM NAME="Course" METHOD=POST ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="add_course">'
	.'<INPUT TYPE="hidden" NAME="course_author" VALUE="'.lnSessionGetVar('uid').'">';

	echo ''
	.'<TR><TD WIDTH=100>'._SCHOOL.'</TD><TD>'
	.'<SELECT class="select" NAME="scode" onchange="document.forms.Course.course_code.value=document.forms.Course.scode.options[this.selectedIndex].value;">';
	list($_,$sscode,$_) = $result->fields;
	while(list($sid,$scode,$name) = $result->fields) {
		$name=stripslashes($name);
		$result->MoveNext();
		echo '<OPTION VALUE="'.$scode.'">'.$name.'</OPTION>';
	}
	echo '</SELECT></TD></TR>'
	.'<TR><TD WIDTH=100">'._COURSECODE.' <B>*</B></TD><TD><INPUT TYPE="text" NAME="course_code" SIZE="10"  VALUE="'.$sscode.'"></TD></TR>'
	.'<TR><TD WIDTH=100>'._COURSETITLE.' <B>*</B></TD><TD><INPUT TYPE="text" NAME="course_title" SIZE="50" VALUE="" style="width:90%"></TD></TR>';
	echo '<TR><TD WIDTH=100 VALIGN="TOP">'._COURSEDESCRIPTION.' <B>*</B></TD><TD><TEXTAREA " NAME="course_desc" ROWS="5" COLS="30" wrap="soft" style="width: 90%;"></TEXTAREA></TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">'._COURSEPURPOSE.'</TD><TD><TEXTAREA  NAME="course_purpose" ROWS="5" COLS="30" wrap="soft" style="width: 90%;"></TEXTAREA></TD></TR>'
	.'<TR><TD WIDTH=120 VALIGN="TOP">'._COURSEPREREQUISITE.'</TD><TD><TEXTAREA NAME="course_prerequisite" ROWS="3" COLS="30" wrap="soft" style="width: 90%;"></TEXTAREA></TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">'._COURSEREFERENCE.'</TD><TD><TEXTAREA NAME="course_ref" ROWS="3" COLS="30" wrap="soft" style="width: 90%;"></TEXTAREA></TD></TR>'
	.'<TR><TD WIDTH=100  VALIGN="TOP">'._COURSECREDIT.'</TD><TD><INPUT TYPE="text" NAME="course_credit" SIZE="2" VALUE=""><BR><BR></TD></TR>'
	.'<TR><TD WIDTH=100></TD><TD><INPUT  TYPE="checkbox" NAME="joe_allow_chat" VALUE="1">'._JOE_ALLOW_CHAT.'</TD></TR>'
	.'<TR><TD WIDTH=100></TD><TD><INPUT  TYPE="checkbox" NAME="joe_allow_member" VALUE="1">'._JOE_ALLOW_MEMBER.'</TD></TR>'
	.'<TR><TD WIDTH=100></TD><TD><INPUT  TYPE="checkbox" NAME="course_sequence" VALUE="1">'._ALLTIME.'</TD></TR>'
	.'<TR><TD WIDTH=100></TD><TD><INPUT  TYPE="checkbox" NAME="course_active" VALUE="1">'._COURSEACTIVE.'</TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">&nbsp;<TD><BR><INPUT class="button" TYPE="button"  VALUE="'. _ADDCOURSE. '" onclick="formSubmit()"> ';
	echo "<INPUT class=\"button\" TYPE=\"button\" VALUE=\"". _CANCEL. "\" onclick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin','_self')\">";
	echo '<BR><BR></TD></TR></FORM>'
	.'</TABLE>'
	.'</fieldset>';

	include 'footer.php';
}


/**
 * add course
 */
function addCourse($vars) {
	// Get arguments from argument array
	extract($vars);

	$sid = findSchoolID($scode);
	$time=time();
	$cid= lnCourseNextID();

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$coursestable = $lntable['courses'];
	$column = &$lntable['courses_column'];

	$tmpcourse_credit =  trim( lnVarPrepForStore($course_credit)) ;
	if($tmpcourse_credit == '')
	$tmpcourse_credit = 'NULL';

	$query = "INSERT INTO $coursestable
	(	$column[cid],
	$column[code],
	$column[sid],
	$column[title],
	$column[author],
	$column[description],
	$column[prerequisite],
	$column[purpose],
	$column[credit],
	$column[reference],
	$column[active],
	$column[createon],
	$column[sequence]
	)
	VALUES ('$cid',
	'" . lnVarPrepForStore($course_code) . "',
						  '" . lnVarPrepForStore($sid) . "',
						  '" . lnVarPrepForStore($course_title) . "',
						  '" . lnVarPrepForStore($course_author) . "',
						  '" . lnVarPrepForStore($course_desc) . "',
						  '" . lnVarPrepForStore($course_prerequisite) . "',
						  '" . lnVarPrepForStore($course_purpose) . "',
						  " . $tmpcourse_credit . ",
  						  '" . lnVarPrepForStore($course_ref) . "',
						  '" . lnVarPrepForStore(@$course_active) . "',
	'$time',
	'".@$course_sequence."'
	)";

	$dbconn->Execute($query);

	$JoeJae_activetable = $lntable['JoeJae_activetable'];
	$JoeJae_active_column = &$lntable['JoeJae_active_column'];

	$joe_query = mysql_query('INSERT INTO '. $JoeJae_activetable .' ('. $JoeJae_active_column['cid'] .', '. $JoeJae_active_column['allow_chat'] .', '. $JoeJae_active_column['allow_member'] .') VALUES ("'. $cid .'", "'. @$vars['joe_allow_chat'] .'", "'. @$vars['joe_allow_member'] .'")');

	if ($dbconn->ErrorNo() != 0) {
		return false;
	}
	else {
		// make directory for this course
		$coursepath= COURSE_DIR . "/" .$cid;
		if (!file_exists($coursepath)) {
			mkdir($coursepath);
		}

		// show next, add lesson
		include("lessonadmin.php");
		$vars= array_merge($vars,array(cid=>$cid));
		lesson($vars);
		return true;
	}

}


/**
 * delete course
 */
function deleteCourse($cid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$column = &$lntable['courses_column'];
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	//By momilk
	$enrollstable = $lntable['course_enrolls'];
	$enrollscolumn = &$lntable['course_enrolls_column'];
	$course_trackingtable = $lntable['course_tracking'];
	$course_trackingcolumn = &$lntable['course_tracking_column'];
	$quiz_anstable = $lntable['quiz_answer'];
	$quiz_anscolumn =$lntable['quiz_answer_column'];
	$scorestable = $lntable['scores'];
	$scorescolumn = $lntable['scores_column'];

	// delete directory
	$result = $dbconn->Execute("SELECT $column[cid] FROM $coursestable  WHERE $column[cid] = '". lnVarPrepForStore($cid) . "'");
	list($coursedir) = $result->fields;
	$coursepath= COURSE_DIR . "/" .$coursedir;
	if (file_exists($coursepath)) {
		if ($coursepath != 'courses/') {
			clr_dir($coursepath);
		}
	}

	//By Momilk
	//find sid
	$result = $dbconn->Execute("SELECT $course_submissionscolumn[sid] FROM $course_submissionstable  WHERE $course_submissionscolumn[cid] = '".     lnVarPrepForStore($cid) . "'");
	list($sid) = $result->fields;
	//echo $sid;

	//delete tracking
	$result = $dbconn->Execute("SELECT $enrollscolumn[eid] FROM   $enrollstable  WHERE $enrollscolumn[sid] = '"
	. lnVarPrepForStore($sid) . "'");
	while (list($eid) = $result->fields) {
		$result->MoveNext();
		//echo $eid."<br>";

		$del1 = "DELETE FROM $course_trackingtable WHERE $course_trackingcolumn[eid] = '$eid'";
		$dbconn->Execute ($del1);
		//echo "<br>";
			
		//delete score
		$del = "DELETE FROM $scorestable WHERE $scorescolumn[eid] = '$eid'";
		$dbconn->Execute ($del);

		//delete quiz_answer
		$del = "DELETE FROM $quiz_anstable WHERE $quiz_anscolumn[eid] = '$eid'";
		$dbconn->Execute ($del);

	}

	//delete enrolls
	$del2 = "DELETE FROM $enrollstable WHERE $enrollscolumn[sid] = '$sid'";
	$dbconn->Execute ($del2);
	//echo $del;
	//end

	// delete course
	$dbconn->Execute("DELETE FROM $coursestable WHERE $column[cid] = '$cid'");

	// delete lessons
	$dbconn->Execute("DELETE  FROM $lessonstable WHERE $lessonscolumn[cid] = '$cid'");

	// delete submisssions
	$dbconn->Execute("DELETE  FROM $course_submissionstable WHERE $course_submissionscolumn[cid] = '$cid'");

	//delete quiz, question and choice table
	$result = $dbconn->Execute("SELECT $quizcolumn[qid] FROM $quiztable WHERE $quizcolumn[cid] = '$cid'");
	while (list($qid) = $result->fields) {
		$result->MoveNext();
		$result2 = $dbconn->Execute("SELECT $quiz_questioncolumn[mcid] FROM $quiz_questiontable WHERE $quiz_questioncolumn[qid] = '$qid'");
		while (list($mcid) = $result2->fields) {
			$result2->MoveNext();
			$result3 = $dbconn->Execute("SELECT $quiz_choicecolumn[chid] FROM $quiz_choicetable WHERE $quiz_choicecolumn[mcid] = '$mcid'");
			while (list($chid) = $result3->fields) {
				$result3->MoveNext();

				// delete choices
				$dbconn->Execute("DELETE  FROM $quiz_choicetable WHERE $quiz_choicecolumn[chid] = '$chid'");
			}

			// delete question
			$dbconn->Execute("DELETE  FROM $quiz_questiontable WHERE $quiz_questioncolumn[mcid] = '$mcid'");
		}

		// delete quiz
		$dbconn->Execute("DELETE  FROM $quiztable WHERE $quizcolumn[qid] = '$qid'");
	}

}


/**
 * edit course detail , objective, prerequisite
 */
function editCourse($cid) {
	global $menus, $links;

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$coursestable = $lntable['courses'];
	$column = &$lntable['courses_column'];

	$result = $dbconn->Execute("SELECT $column[author] FROM $coursestable  WHERE $column[cid] = '". lnVarPrepForStore($cid) . "'");
	list($author) = $result->fields;
	if ($author == lnSessionGetVar('uid') || lnUserAdmin(lnSessionGetVar('uid'))) {
		editCourseForm($cid);
	}
	else {
		echo '<BR><CENTER><B>'. _NOTACCESS.'</B></CENTER>';
	}

	include 'footer.php';
}


/**
 * show edit course form
 */
function editCourseForm($cid) {
	global $menus, $links;

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
	$schoolstable = $lntable['schools'];
	$schoolscolumn = &$lntable['schools_column'];
	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];
	$groupstable = $lntable['groups'];
	$groupscolumn = &$lntable['groups_column'];
	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];


	// get course data
	$query = "SELECT
	$coursescolumn[code],
	$coursescolumn[sid],
	$coursescolumn[title],
	$coursescolumn[author],
	$coursescolumn[description],
	$coursescolumn[prerequisite],
	$coursescolumn[purpose],
	$coursescolumn[credit],
	$coursescolumn[reference],
	$coursescolumn[active],
	$coursescolumn[sequence]
	FROM  $coursestable
	WHERE $coursescolumn[cid] =  '". lnVarPrepForStore($cid) . "'";

	$result = $dbconn->Execute($query);
	list($code,$ssid,$title,$author,$description,$prerequisite,$purpose,$credit,$reference,$active,$sequence) = $result->fields;
	$title = stripslashes($title);
	$description = stripslashes($description);
	$prerequisite = stripslashes($prerequisite);
	$purpose = stripslashes($purpose);
	$reference = stripslashes($reference);


	/** Navigator **/
	$menus[]= $title;
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/

	$checkseq[$sequence]="checked";
	$checkstudent[@$enroll]="checked";

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0"><TR><TD>';

	tabCourseAdmin($cid,1);

	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width= 100% cellpadding=0 cellspacing=0  border=0>';
	echo '<tr><td>';

	echo '<BR><table width= 100% cellpadding=2 cellspacing=0 border=0>'
	.'<FORM NAME="Course" METHOD=POST ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="update_course">'
	.'<INPUT TYPE="hidden" NAME="course_author" VALUE="'.lnSessionGetVar('uid').'">'
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">';

	echo ''
	.'<TR><TD WIDTH=100 VALIGN="MIDDLE">&nbsp;&nbsp;'._SCHOOL.'</TD><TD>'
	.'<SELECT CLASS="select" NAME="scode" onchange="document.forms.Course.course_code.value=document.forms.Course.scode.options[this.selectedIndex].value;">';

	// school list
	$query = "SELECT  $schoolscolumn[sid],$schoolscolumn[code],$schoolscolumn[name]  FROM  $schoolstable  ORDER BY $schoolscolumn[sid]";
	$result = $dbconn->Execute($query);

	$select=array();
	$select[$ssid]="selected";
	while(list($sid,$scode,$name) = $result->fields) {
		$name = stripslashes($name);
		$result->MoveNext();
		echo '<OPTION VALUE="'.$scode.'" '.$select[$sid].'>'.$name.'</OPTION>';
	}
	echo '</SELECT></TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="MIDDLE">&nbsp;&nbsp;'._COURSECODE.'</TD><TD><INPUT CLASS="input" TYPE="text" NAME="course_code" SIZE="6"  VALUE="'.$code.'"></TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="MIDDLE">&nbsp;&nbsp;'._COURSETITLE.'</TD><TD><INPUT  CLASS="input" TYPE="text" NAME="course_title" SIZE="40" VALUE="'.$title.'"></TD></TR>';
	echo '<TR><TD WIDTH=100 VALIGN="TOP">&nbsp;&nbsp;'._COURSEDESCRIPTION.'</TD><TD><TEXTAREA  CLASS="input" NAME="course_desc" ROWS="5" COLS="30" wrap="soft" style="width: 90%;">'.$description.'</TEXTAREA></TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">&nbsp;&nbsp;'._COURSEPURPOSE.'</TD><TD><TEXTAREA  CLASS="input" NAME="course_purpose" ROWS="5" COLS="30" wrap="soft" style="width: 90%;">'.$purpose.'</TEXTAREA></TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">&nbsp;&nbsp;'._COURSEPREREQUISITE.'</TD><TD><TEXTAREA  CLASS="input" NAME="course_prerequisite" ROWS="3" COLS="30" wrap="soft" style="width: 90%;">'.$prerequisite.'</TEXTAREA></TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">&nbsp;&nbsp;'._COURSEREFERENCE.'</TD><TD><TEXTAREA  CLASS="input" NAME="course_ref" ROWS="3" COLS="30" wrap="soft" style="width: 90%;">'.$reference.'</TEXTAREA></TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">&nbsp;&nbsp;'._COURSECREDIT.'</TD><TD><INPUT  CLASS="input" TYPE="text" NAME="course_credit" SIZE="2" VALUE="'.$credit.'"><BR><BR></TD></TR>';

	$JoeJae_activetable = $lntable['JoeJae_activetable'];
	$JoeJae_active_column = &$lntable['JoeJae_active_column'];

	$joe_query = mysql_query('SELECT '. $JoeJae_active_column['allow_chat'] .', '. $JoeJae_active_column['allow_member'] .' FROM '. $JoeJae_activetable .' WHERE '. $JoeJae_active_column['cid'] .' = "'. $cid .'" LIMIT 1');
	
	//echo 'SELECT '. $JoeJae_active_column['allow_chat'] .', '. $JoeJae_active_column['allow_member'] .' FROM '. $JoeJae_activetable .' WHERE '. $JoeJae_active_column['cid'] .' = "'. $cid .'" LIMIT 1';

	if(mysql_num_rows($joe_query) <= 0)
	{
		echo '<TR><TD WIDTH=100></TD><TD><INPUT  TYPE="checkbox" NAME="joe_allow_chat" VALUE="1">'. _JOE_ALLOW_CHAT .'</TD></TR>';
		echo '<TR><TD WIDTH=100></TD><TD><INPUT  TYPE="checkbox" NAME="joe_allow_member" VALUE="1">'. _JOE_ALLOW_MEMBER .'</TD></TR>';
	}
	else
	{
		list($allow_chat, $allow_member) = mysql_fetch_row($joe_query);
		echo '<TR><TD WIDTH=100></TD><TD><INPUT  TYPE="checkbox" NAME="joe_allow_chat" VALUE="1" '. ($allow_chat == 1 ? 'checked' : '') .'>'. _JOE_ALLOW_CHAT .'</TD></TR>';
		echo '<TR><TD WIDTH=100></TD><TD><INPUT  TYPE="checkbox" NAME="joe_allow_member" VALUE="1" '. ($allow_member == 1 ? 'checked' : '') .'>'. _JOE_ALLOW_MEMBER .'</TD></TR>';
	}

	
	if ($sequence=='1') {
		echo '<TR><TD WIDTH=100></TD><TD><INPUT  TYPE="checkbox" NAME="course_sequence" VALUE="1" checked>'._ALLTIME.'</TD></TR>';
	}
	else {
		echo '<TR><TD WIDTH=100></TD><TD><INPUT  TYPE="checkbox" NAME="course_sequence" VALUE="1">'._ALLTIME.'</TD></TR>';
	}

	echo '<TR><TD WIDTH=100 VALIGN="TOP"></TD><TD>';
	if ($active) {
		echo '<INPUT TYPE="checkbox" checked NAME="course_active" VALUE="1">';
	}
	else {
		echo '<INPUT TYPE="checkbox" NAME="course_active" VALUE="1">';
	}

	echo _COURSEACTIVE.'</TD></TR>'
	.'<TR height=40><TD WIDTH=100>&nbsp;<TD VALIGN="MIDDLE"><INPUT  TYPE="submit" style=" cursor: hand" VALUE="'. _UPDATECOURSE. '"></TD></TR>'
	.'</FORM>';
	echo '</TABLE>';

	echo '</td></tr></table>';
	echo '</TD></TR></TABLE>';

}


/*
 * update course
 */
function updateCourse($vars) {
	// Get arguments from argument array
	extract($vars);

	$sid = findSchoolID($scode);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];

	// get course data
	$tmpcourse_credit =  trim(lnVarPrepForStore($course_credit)) ;
	if($tmpcourse_credit == '')
	$tmpcourse_credit = 'NULL';
	$query = "UPDATE $coursestable SET
	$coursescolumn[code] = '" . lnVarPrepForStore($course_code) . "',
	$coursescolumn[sid] = '" . lnVarPrepForStore($sid) . "',
	$coursescolumn[title] = '" . lnVarPrepForStore($course_title) . "',
	$coursescolumn[description] = '" . lnVarPrepForStore($course_desc) . "',
	$coursescolumn[prerequisite] = '" . lnVarPrepForStore($course_prerequisite) . "',
	$coursescolumn[purpose] = '" . lnVarPrepForStore($course_purpose) . "',
	$coursescolumn[credit] = " . $tmpcourse_credit . ",
	$coursescolumn[reference] = '" . lnVarPrepForStore($course_ref) . "',
	$coursescolumn[active] = '" . lnVarPrepForStore($course_active) . "',
	$coursescolumn[sequence] = '" . lnVarPrepForStore($course_sequence) . "'
	WHERE $coursescolumn[cid] =  '". lnVarPrepForStore($cid) . "'";

	$result = $dbconn->Execute($query);

	$JoeJae_activetable = $lntable['JoeJae_activetable'];
	$JoeJae_active_column = &$lntable['JoeJae_active_column'];

	$joe_query = 'SELECT '. $JoeJae_active_column['id'] .' FROM '. $JoeJae_activetable .' WHERE '. $JoeJae_active_column['cid'] .' = "'. $vars['cid'] .'" LIMIT 1';

	if(mysql_num_rows(mysql_query($joe_query)) <= 0)
	{
		//echo 'INSERT INTO '. $JoeJae_activetable .' ('. $JoeJae_active_column['cid'] .', '. $JoeJae_active_column['allow_chat'] .', '. $JoeJae_active_column['allow_member'] .') VALUES ("'. $vars['cid'] .'", "'. $vars['joe_allow_chat'] .'", "'. $vars['joe_allow_member'] .'" )';
		$joe_query = mysql_query('INSERT INTO '. $JoeJae_activetable .' ('. $JoeJae_active_column['cid'] .', '. $JoeJae_active_column['allow_chat'] .', '. $JoeJae_active_column['allow_member'] .') VALUES ("'. $vars['cid'] .'", "'. $vars['joe_allow_chat'] .'", "'. $vars['joe_allow_member'] .'")');
	}
	else
	{
		//echo 'UPDATE '. $JoeJae_activetable .' SET '. $JoeJae_active_column['allow_chat'] .' =  "'. $vars['joe_allow_chat'] .'", '. $JoeJae_active_column['allow_member'] .' = "'. $vars['joe_allow_member'] .'" WHERE '. $JoeJae_active_column['cid'] .' = "'. $vars['cid'] .'" LIMIT 1';
		$joe_query = mysql_query('UPDATE '. $JoeJae_activetable .' SET '. $JoeJae_active_column['allow_chat'] .' =  "'. $vars['joe_allow_chat'] .'", '. $JoeJae_active_column['allow_member'] .' = "'. $vars['joe_allow_member'] .'" WHERE '. $JoeJae_active_column['cid'] .' = "'. $vars['cid'] .'" LIMIT 1');
	}

	if($dbconn->ErrorNo() != 0) {
		return;
	}

}


/* *
 * find school ID from school code
 */
function findSchoolID($schoolcode) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$schoolstable = $lntable['schools'];
	$schoolscolumn = &$lntable['schools_column'];

	$query = "SELECT  $schoolscolumn[sid]
	FROM  $schoolstable
	WHERE $schoolscolumn[code]= '".lnVarPrepForStore($schoolcode)."'";

	$result = $dbconn->Execute($query);

	if($dbconn->ErrorNo() != 0) {
		return;
	}
	else {
		list($schoolid) = $result->fields;
		return $schoolid;
	}
}


/**
 * find lesson
 */
function hasLesson($cid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	// List  lessons
	$query = "SELECT $lessonscolumn[lid]
	FROM   $lessonstable
	WHERE $lessonscolumn[cid] =  '" . lnVarPrepForStore($cid) . "'";

	$result = $dbconn->Execute($query);
	$numrows = $result->PO_RecordCount();

	return $numrows;
}


/**
 * find quiz
 */
function hasQuiz($cid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];

	// List  lessons
	$query = "SELECT $quizcolumn[qid]
	FROM   $quiztable
	WHERE $quizcolumn[cid] =  '" . lnVarPrepForStore($cid) . "'";

	$result = $dbconn->Execute($query);
	$numrows = $result->PO_RecordCount();

	return $numrows;
}


/**
 * import Lesson package
 */
function importLesson($vars) {
	// Get arguments from argument array
	extract($vars);

	echo "<html>";
	echo "<head><title>Import Lesson</title>";
	echo "<LINK REL=STYLESHEET HREF='theme/$config[theme]/style/default.css' type='text/css'></head>";
	echo '<BODY TOPMARGIN="0" LEFTMARGIN="10" MARGINHEIGHT="0" MARGINWIDTH="0" LINK="#000000" ALINK="#000000" VLINK="#000000" bgcolor=#BBBBBB>';
	?>
<BR>
<FORM NAME="ImportForm" METHOD="post" ACTION="index.php"
	ENCTYPE="multipart/form-data"><INPUT TYPE="hidden" NAME="mod"
	VALUE="SCORM"> <INPUT TYPE="hidden" NAME="file" VALUE="import"> <INPUT
	TYPE="hidden" NAME="op" VALUE="import_course"> <input type="hidden"
	name="cid" value="<?=$cid?>"> <input type="hidden" name="lid"
	value="<?=$lid?>">
<fieldset><legend
	style="color: #111111; font-family: verdana; font-size: 8pt;">Select
package file (*.zip)</legend>
<TABLE>
	<TR>
		<TD><BR>
		<INPUT TYPE="file" NAME="fileimport"></TD>
	</TR>
	<TR>
		<TD><INPUT TYPE="submit" VALUE="Import"></TD>
	</TR>
</TABLE>

</fieldset>
</FORM>

	<?
}
?>