<?php
/*
*  Quiz administration
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

/*
* quiz functions
*/
function quiz($vars) {
	global $menus, $links;

	// Get arguments from argument array
    extract($vars);

	$courseinfo = lnCourseGetVars($cid);
	$coursecode=$courseinfo['code'];
	$coursename=$courseinfo['title'];
	
	/** Navigator **/
	$menus[]= $coursecode.' : '.$coursename;
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
	tabCourseAdmin($cid,3);
	echo '</TD></TR><TR><TD>';

	echo '<table width= 100% class="main" cellpadding=3 cellspacing=1 bgcolor="#FFFFFF" border="0">';
	echo '<tr><td valign="top">';

	/* options */
	switch($action) {
		case "add_quiz_form" : 
		case "edit_quiz" : editQuizForm($vars); return;
		case "add_quiz" : addQuiz($vars); break;
		case "delete_quiz" : deleteQuiz($vars); break;

	}
	/* options */

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];

	
	if ($sum=hasLesson($cid)) {

		$query = "SELECT $quizcolumn[qid],$quizcolumn[lid],$quizcolumn[type]
							FROM $quiztable LEFT JOIN $lessonstable ON $quizcolumn[lid]=$lessonscolumn[lid] 
							WHERE $quizcolumn[cid]='". lnVarPrepForStore($cid) ."'
							ORDER BY $quizcolumn[type],$lessonscolumn[weight]";

		$result = $dbconn->Execute($query);
	
		echo '<table width="100%" cellspacing="0" cellpading=3>';
		for($count=0; list($qid,$lid,$type) = $result->fields;) {
			$result->MoveNext();
			if (!empty($qid)) {
				echo '<tr><td height="20">';
				echo "<B><A HREF=\"javascript:popup('index.php?mod=Courses&amp;file=questionadmin&amp;qid=$qid&amp;cid=$cid','_blank',600,480)\">";
				if ($type == '1') {
					$lessoninfo = lnLessonGetVars($lid);
					echo '<IMG SRC="images/global/line.gif"  BORDER="0" ALT="" align="absmiddle"> ' ._LESSONNO.' '.$lessoninfo['no'] .' '. $lessoninfo['title'];
					$viewlink= "<A HREF=index.php?mod=Courses&amp;op=lesson_test&amp;cid=$cid&amp;lid=$lid target='_blank'><IMG SRC=images/global/view.gif  BORDER=0 ALT="._VIEW._TEST."></A>  &nbsp;";
				}
				else if ($type == '0') {
					echo '<IMG SRC="images/page.gif" WIDTH="18" HEIGHT="18" BORDER="0" ALT="" align="absmiddle">';
					echo _PRETEST ;
					$viewlink= "<A HREF=\"index.php?mod=Courses&amp;op=pre_test&amp;cid=$cid&amp;lid=0\" target='_blank'><IMG SRC=images/global/view.gif  BORDER=0 ALT="._VIEW._TEST."></A>  &nbsp;";
				}
				else if ($type == '2') {
					echo '<IMG SRC="images/page.gif" WIDTH="18" HEIGHT="18" BORDER="0" ALT="" align="absmiddle">';
					echo _POSTEST;	
					$viewlink= "<A HREF=\"index.php?mod=Courses&amp;op=post_test&amp;cid=$cid&amp;lid=0\" target='_blank'><IMG SRC=images/global/view.gif  BORDER=0 ALT="._VIEW._TEST."></A>  &nbsp;";
				}
				$count++;
				echo '</A></B></td>';
				echo '<td align="right" width=150>';
				
				echo "<A HREF=\"javascript:popup('index.php?mod=Courses&amp;file=questionadmin&amp;qid=$qid&amp;cid=$cid','_blank',600,480)\"><IMG SRC=images/global/view1.gif  BORDER=0 ALT="._ADDQUESTION."></A>  &nbsp;";

				echo $viewlink;
				
				echo "<A HREF=\"index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=edit_quiz&amp;qid=$qid&amp;cid=$cid\"><IMG SRC=images/global/edit.gif  BORDER=0 ALT="._EDIT."></A>  &nbsp;";


				echo "<A HREF=\"javascript: if(confirm('Delete ?')) window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=delete_quiz&amp;qid=$qid&amp;cid=$cid','_self')\"><IMG SRC=images/global/delete.gif  BORDER=0 ALT="._DELETE."></A>";	

				echo '</td></tr>';
				echo '<tr><td colspan="2" height="1" background="images/line.gif"></td></tr>';
			}
		}
		echo '</table>';
		// no quiz
		if ($count == 0 && $action != "add_quiz_form") {
			echo '<center><br><br>'._NOQUIZ.'<br><br></center>';
		}

		if ($count < $sum + 2) {
			echo '<center>';
			addQuestionButton($vars);
			echo '</center>';
		}

	}
	// no lesson
	else {
		echo '<center><br><br>'._NOQUIZ.'<br><br>'._CREATELESSON.'</center>';
	}
	echo '</td></tr>';
	echo '</table>';
	echo '</TD></TR></TABLE>';
	
}


/*
* show add new question button
*/
function addQuestionButton($vars) {
	// Get arguments from argument array
    extract($vars);

	echo '<FORM METHOD=POST ACTION="index.php">'
		.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
		.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
		.'<INPUT TYPE="hidden" NAME="op" VALUE="quiz">'
		.'<INPUT TYPE="hidden" NAME="action" VALUE="add_quiz_form">'
		.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">';
	echo '<P><INPUT CLASS="button_org" TYPE=submit VALUE="'._ADDQUIZ.'">';
	echo '</FORM>';		
}


/*
*	add or edit quiz form
*/
function editQuizForm($vars) {
		// Get arguments from argument array
	    extract($vars);
		
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$quiztable = $lntable['quiz'];
		$quizcolumn = &$lntable['quiz_column'];
		$lessonstable = $lntable['lessons'];
		$lessonscolumn = &$lntable['lessons_column'];

		
		if ($action == "edit_quiz") {
			$query = "SELECT $lessonscolumn[weight],$lessonscolumn[title],
								$quizcolumn[description],$quizcolumn[random],$quizcolumn[timelimit],$quizcolumn[option],$quizcolumn[type]
								FROM $quiztable LEFT JOIN $lessonstable ON $quizcolumn[lid]=$lessonscolumn[lid]
								WHERE $quizcolumn[qid]='". lnVarPrepForStore($qid) ."'";

			$result = $dbconn->Execute($query);
			
			list($lesson_no,$lesson_title,$quiz_desc,$random,$timelimit,$option,$type) = $result->fields;
			if ($type=='1')
				$test_title =_LESSONNO.' '.$lesson_no.' '.$lesson_title;
			else if ($type == '0')
				$test_title = _PRETEST;
			else if ($type == '2')
				$test_title = _POSTEST;
		}
		

		if (empty($quiz_desc)) $quiz_desc = _DEFAULTQUIZTITLE;
		if (empty($random)) $random=0;
		if (empty($timelimit)) $timelimit=0;
		if ($option & 1) $checkshowanswer = "checked";
		if ($option & 2) $checkreqtest = "checked";

		echo '<center><fieldset style="width: 96%;"><legend style="color: #999999; font-family: ms sans serif; font-size: 10pt;">'._TEST.'</legend>';
		echo '<table cellpadding=2 cellspacing=0 border=0 width=100%>';
		
		?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
			function checkFields() {
				if (document.forms.quizform.quiz_desc.value == "" ) {
					return false;
				}
				else {
					return true; 
				}
			}
		//-->
		</SCRIPT>
		<?
		
		echo '<FORM NAME="quizform" METHOD=POST ACTION="index.php" onSubmit=" if (checkFields()) {return true;} else {alert(\''._QUESTIONORDER.' ?\');return false;} ">'
			.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
			.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
			.'<INPUT TYPE="hidden" NAME="op" VALUE="quiz">'
			.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
			.'<INPUT TYPE="hidden" NAME="action" VALUE="add_quiz">';

		// list lessons
		if ($action == "edit_quiz") {
				echo '<tr><td width=100 align=right>&nbsp;</td>';
				echo '<td><b>'.$test_title.'</b></td></tr>';
				echo '<input type="hidden" name="qid" value="'.$qid.'">';
		}
		else {
				echo '<tr><td width=100 align=right><b>'._TEST.' : </b></td>';
				echo '<td><select name="quiz">';
				if (!hasPreTest($cid)) {
					echo '<option value="0-'._LNQUIZ_PRETEST.'">'._PRETEST.'</option>'; 				//  value = lid - quiz type ,0 = after learn SCO ,1 = pre-test, 2 = post-test 
				}
			
				listBoxLesson($cid,0,$orderings=array());

				if (!hasPostTest($cid)) {
					echo '<option value="0-'._LNQUIZ_POSTEST.'">'._POSTEST.'</option>';
				}
				echo '</select></td></tr>';
		}

		echo '<tr><td width="13%" valign="top" align="right"><B>'._QUESTIONORDER.' : </B></td>';
		echo '<td valign="top">';
		echo '<TEXTAREA CLASS="input" NAME="quiz_desc" ROWS="4" COLS="30" wrap="soft" style="width: 90%;">'.$quiz_desc.'</TEXTAREA>';
		echo "<INPUT  TYPE=button VALUE='&nbsp;...&nbsp;' onclick=\"javascript:popup('index.php?mod=spaw&amp;type=Quiz&amp;cid=$cid&amp;lid=$lid&amp;qid=$qid','_blank',750,480)\">";
		echo '</td></tr>';
		echo '<tr><td align="right"><B>'._RANDOMQUIZ.'</B> : </td>';
		echo '<td>';
		echo '<INPUT TYPE="text" NAME="random" SIZE="2" VALUE="'.$random.'"> '._QUIZUNIT;
		echo '</td></tr>';

		echo '<tr><td align="right"><B>'._TIMELIMITQUIZ.'</B> : </td>';
		echo '<td>';
		echo '<INPUT TYPE="text" NAME="timelimit" SIZE="2" VALUE="'.$timelimit.'"> '._TIMELIMITUNIT;
		echo '</td></tr>';

		echo '<tr><td>&nbsp;</td><td>';
		echo '<input type="checkbox" name="showanswer" '.$checkshowanswer.' value="'._LNTEST_SHOWANS.'"> '._SHOWANSWER.'&nbsp;&nbsp;&nbsp;&nbsp;';
		echo '<input type="checkbox" name="reqtest" '.$checkreqtest.' value="'._LNTEST_REQUIRED.'"> '._REQTEST;
		echo '</td></tr>';

		
		echo '<tr><td>&nbsp;</td><td>';
		if ($action == "edit_quiz") {
			echo '<BR>&nbsp;<INPUT CLASS="button_org" TYPE="submit" VALUE="'._UPDATEQUIZ.'">';
		}
		else {
			echo '<BR>&nbsp;<INPUT CLASS="button_org" TYPE="submit" VALUE="'._ADDQUIZ.'">';
		}
		echo " <INPUT CLASS=\"button_org\" TYPE=button VALUE=Cancel OnClick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;cid=$cid','_self')\"><BR>&nbsp;</td></tr>";
		echo '</FORM>';
		echo '</table>';
		echo '</fieldset></center>';

}


/**
*	get lessson to listbox
*/
function listBoxLesson($cid,$parent_lid,$orderings) {

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$quiztable = $lntable['quiz'];
		$quizcolumn = &$lntable['quiz_column'];
		$lessonstable = $lntable['lessons'];
		$lessonscolumn = &$lntable['lessons_column'];
		
		$query = "SELECT $lessonscolumn[lid],$lessonscolumn[weight],$lessonscolumn[title],$lessonscolumn[lid_parent]
						FROM   $lessonstable LEFT JOIN $quiztable ON $quizcolumn[lid] = $lessonscolumn[lid]
						WHERE  $lessonscolumn[cid] =  '" . lnVarPrepForStore($cid) . "' 
											AND $lessonscolumn[lid_parent]='".$parent_lid."'"; 

		if ($action != "edit_quiz") { 
//			$query .= " AND $quizcolumn[lid] is NULL";
		}
		
		$query .= " ORDER BY $lessonscolumn[weight] ";
	
		$result = $dbconn->Execute($query);	

		for($i=0; list($lid,$weight,$title,$parent) = $result->fields; $i++) {
				$result->MoveNext();
				$title = stripslashes($title);
				array_push($orderings,$weight);
				$show_item=join('.',$orderings);
				for($blank='',$j=0;$j<count($orderings);$j++) $blank .= ' &nbsp;&nbsp;&nbsp;';

				echo '<option value="'.$lid.'-'._LNQUIZ_LESSON.'">'.$blank.' '.$show_item.'. '.$title.'</option>';
				listBoxLesson($cid,$lid,$orderings);
				array_pop($orderings);

		}
		
}


/** 
* add quiz
*/
function addQuiz($vars) {
		// Get arguments from argument array
	    extract($vars);

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
		$quiztable = $lntable['quiz'];
		$quizcolumn = &$lntable['quiz_column'];
		
		list ($lid,$quiztype) = explode('-',$quiz);
		$option = $showanswer + $reqtest;
		$quiz_desc=stripslashes($quiz_desc);

		if (empty($qid)) {
//			if (empty($random)) $random = _LNDEFUALT_RANDOM;
//			if (empty($timelimit)) $timelimit = _LNDEFUALT_TIMELIMIT;
			if (empty($random)) $random = 0;
			if (empty($timelimit)) $timelimit = 0;

			$max_qid = getMaxQID();
			$query = "INSERT INTO $quiztable
				  (	$quizcolumn[qid],
					$quizcolumn[cid],
					$quizcolumn[lid],
					$quizcolumn[description],
					$quizcolumn[random],
					$quizcolumn[timelimit],
					$quizcolumn[type],
					$quizcolumn[option]
					  )
					VALUES ('" . lnVarPrepForStore($max_qid) . "',
						  '" . lnVarPrepForStore($cid) . "',
						  '" . lnVarPrepForStore($lid) . "',
						  '" . lnVarPrepForStore($quiz_desc) . "',
						  '" . lnVarPrepForStore($random) . "',
						  '" . lnVarPrepForStore($timelimit) . "',
						  '" . lnVarPrepForStore($quiztype) . "',
					  	  '" . lnVarPrepForStore($option) . "')";	
		}
		else {
			$query = "UPDATE $quiztable SET 
					$quizcolumn[description] =   '" . lnVarPrepForStore($quiz_desc) . "' ,
					$quizcolumn[random] =   '" . lnVarPrepForStore($random) . "' ,
					$quizcolumn[timelimit] =   '" . lnVarPrepForStore($timelimit) . "', 
					$quizcolumn[option] =   '" . lnVarPrepForStore($option) . "' 
					WHERE $quizcolumn[qid] = '" . lnVarPrepForStore($qid) . "'";	
		}

		$dbconn->Execute($query);	
		 if ($dbconn->ErrorNo() != 0) {
			return false;
		} 
		else {
			return true;
		}
}


/* 
* delete pre/post-test
*/
function deleteQuiz($vars) {
		// Get arguments from argument array
	    extract($vars);
	
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$quiztable = $lntable['quiz'];
		$quizcolumn = &$lntable['quiz_column'];
		$quiz_questiontable = $lntable['quiz_question'];
		$quiz_questioncolumn = &$lntable['quiz_question_column'];
		$quiz_choicetable = $lntable['quiz_choice'];
		$quiz_choicecolumn = &$lntable['quiz_choice_column'];

		if (!empty($qid)) {

			//detete quiz table
			$query = "DELETE FROM $quiztable WHERE $quizcolumn[qid] =  '" . lnVarPrepForStore($qid) . "'";
			$dbconn->Execute($query);	

			// delete choice table
			$query = "SELECT $quiz_questioncolumn[quid] FROM $quiz_questiontable WHERE $quiz_questioncolumn[qid] =  '" . lnVarPrepForStore($qid) . "'";
			$result = $dbconn->Execute($query);
			$numrows = $result->PO_RecordCount();
			while(list($quid) = $result->fields) {
				$result->MoveNext();
				$dbconn->Execute("DELETE FROM $quiz_choicetable WHERE $quiz_choicecolumn[quid] =  '" . lnVarPrepForStore($quid) . "'");	
			}
			
			// delete question table
			if ($numrows > 0) {
				$query = "DELETE FROM $quiz_questiontable WHERE $quiz_questioncolumn[qid] =  '" . lnVarPrepForStore($qid) . "'";
				$dbconn->Execute($query);	
			}
		}
		
}


/*
* get next quiz id
*/
function getMaxQID() {
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
		
		$quiztable = $lntable['quiz'];
		$quizcolumn = &$lntable['quiz_column'];

		$result = $dbconn->Execute("SELECT MAX($quizcolumn[qid]) FROM $quiztable");
		list($max_qid) = $result->fields;

		return $max_qid + 1;
}


?>