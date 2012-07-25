<?php
/**
 *  Quiz administration
 */
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/**
 * quiz functions
 */
function quiz($vars) {
	global $menus, $links;

	// Get arguments from argument array
	extract($vars);

	/** Navigator **/
	$courseinfo = lnCourseGetVars($cid);
	$menus[]= $courseinfo['title'];
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
	tabCourseAdmin($cid,3);
	echo '</TD></TR><TR><TD>';

	echo '<table width= 100% class="main" cellpadding=3 cellspacing=1 border="0">';
	echo '<tr><td valign="top">';

	/* options */
	switch($action) {
		case "add_quiz_form" :
		case "edit_quiz" : editQuizForm($vars); return;
		case "add_quiz" : addQuiz($vars);
		case "add_question_form":	addQuestionForm($vars);  listQuestion($vars); return;
		case "add_multichoice_form": addMultiChoiceForm($vars);return; //ปรนัย
		case "add_clozetest_form": addClozeTestForm($vars);return;	//เติมคำ
		case "add_multiquestion_form": addMultiQuestionForm($vars);return; //หลายคำถามในตัวเลือก1ชุด
		case "add_multichoice": addMultiChoice($vars);addQuestionForm($vars);listQuestion($vars); return;//choice ปรันัย
		case "delete_quiz" : deleteQuiz($vars); break;
		case "delete_question" : deleteQuestion($vars); addQuestionForm($vars); listQuestion($vars); return;
		case "increase_weight":   increaseQuestionWeight($vars); addQuestionForm($vars); listQuestion($vars); return;
		case "decrease_weight": decreaseQuestionWeight($vars); addQuestionForm($vars); listQuestion($vars); return;
		case "add_question_clozetest_form": addQuestionClozeTestForm($vars); return; //เติมคำ รูปแบบ
		case "add_choice_clozetest_form": addChoiceClozeTestForm($vars); return; //choice เติมคำ
		case "add_multiquestion_form": addMultiQuestionForm($vars); return;
		case "add_question_multiquestion_form": addQuestionMultiQuestionForm($vars); return; //คำถามมัลติ
		case "add_choice_multiquestion_form": addChoiceMultiQuestionForm($vars); return; //รูปแบบคำถามมัลติ
		case "add_choice_multiquestion": addChoiceMultiQuestion($vars); return; //บันทึกคำถามมัลติ

	}
	/* options */

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];

	$query = "SELECT $quizcolumn[qid],$quizcolumn[name]
	FROM $quiztable
	WHERE $quizcolumn[cid]='". lnVarPrepForStore($cid) ."'";

	$result = $dbconn->Execute($query);

	echo '<table width="100%" cellspacing="0" cellpading=3>';
	for($count=0; list($qid,$quiz_name) = $result->fields;) {
		$result->MoveNext();
		if (!empty($qid)) {
			echo '<tr><td height="20">';
			echo "<B><A HREF=\"index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=edit_quiz&amp;qid=$qid&amp;cid=$cid\">";
			echo '<IMG SRC="images/global/line.gif"  BORDER="0" ALT="" align="absmiddle"> '.$quiz_name;
			$viewlink= "<A HREF=index.php?mod=Courses&amp;op=lesson_show&amp;cid=$cid&amp;lid=$lid&amp;qid=$qid><IMG SRC=images/global/view.gif  BORDER=0 ALT="._VIEW._TEST."></A>  &nbsp;";
			$count++;
			echo '</A></B></td>';
			echo '<td align="right" width=150>';

			echo "<A HREF=\"index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=edit_quiz&amp;qid=$qid&amp;cid=$cid\"><IMG SRC=images/global/view1.gif  BORDER=0 ALT="._EDIT."></A>  &nbsp;";

			echo $viewlink;

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

	echo '<center>';
	addQuestionButton($vars);
	echo '</center>';

	echo '</td></tr>';
	echo '</table>';
	echo '</TD></TR></TABLE>';

}


/**
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


/**
 *	add or edit quiz form
 */
function editQuizForm($vars) {
	// Get arguments from argument array
	extract($vars);

	if ($action == "edit_quiz") {
		$quizinfo = lnQuizGetVars($qid);
		$csq[$quizinfo['shufflequestions']]='checked';
		$cca[$quizinfo['correctanswers']]='checked';
		$cfb[$quizinfo['feedback']]='checked';
		$cgr[$quizinfo['grademethod']]='checked';
		$sgr[$quizinfo['grade']]='selected';

	}
	else {
		$csq[0]='checked';
		$cca[1]='checked';
		$cfb[1]='checked';
		$cgr[1]='checked';
		$quizinfo['testtime']='0';
		$quizinfo['attempts']='0';
		$quizinfo['assessment']='60';
		$quizinfo['correctscore']='1';
		$quizinfo['wrongscore']='0';
		$quizinfo['noans']='0';
	}


	if (empty($quiz_desc)) $quiz_desc = _DEFAULTQUIZTITLE;

	echo '<IMG SRC="images/global/linkicon.gif" BORDER="0" ALT=""> <A HREF="index.php?mod=Courses&file=admin&op=quiz&cid='.$cid.'">'._TEST.'</A> &gt; <B>'.$quizinfo['name'].'</B>';

	echo '<center><BR><fieldset><legend>'._TEST.'</legend>';
	echo '<table cellpadding=2 cellspacing=0 border=0 width=100%>';

	?>
<SCRIPT LANGUAGE="JavaScript">
		<!--
			function checkFields() {
				if (document.forms.quizform.quiz_name.value == "" ) {
					alert('<?=_QUIZNAME?>?');
					document.forms.quizform.quiz_name.focus();
					return false;
				}
				if (document.forms.quizform.quiz_desc.value == "" ) {
					alert('<?=_QUESTIONORDER?>?');
					document.forms.quizform.quiz_desc.focus();
					return false;
				}
				return true; 
			}
		//-->
		</SCRIPT>
	<?

	echo '<FORM NAME="quizform" METHOD=POST ACTION="index.php" onSubmit=" if (checkFields()) {return true;} else {return false;} ">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="quiz">'
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="action" VALUE="add_quiz">';
	if ($action == 'edit_quiz') {
		echo '<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">';
	}

	// list lessons
	echo '<tr><td>&nbsp;&nbsp;'._QUIZNAME.' : </td>';
	echo '<td><INPUT TYPE="text" NAME="quiz_name" value="'.$quizinfo['name'].'" size="60" style="width: 90%;">';
	echo '</td></tr>';

	echo '<tr><td width="20%" valign="top">&nbsp;&nbsp;'._QUESTIONORDER.' : </td>';
	echo '<td valign="top">';
	echo '<TEXTAREA  NAME="quiz_desc" ROWS="4" COLS="30" wrap="soft" style="width: 90%;">'.$quizinfo['intro'].'</TEXTAREA>';
	echo "<INPUT class=button TYPE=button size=1 VALUE=' ... ' onclick=\"javascript:popup('index.php?mod=spaw&amp;type=Quiz&amp;cid=$cid&amp;lid=$lid&amp;qid=$qid','_blank',750,480)\">";
	echo '</td></tr>';

	echo '<tr><td>&nbsp;&nbsp;'._SHUFFLEQUESTION.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="radio" NAME="shufflequestions" VALUE="1" '.$csq[1].'> '._SYES.' &nbsp;&nbsp;<INPUT TYPE="radio" NAME="shufflequestions" VALUE="0" '.$csq[0].'> '._SNO.' ';
	echo '</td></tr>';

	//************ SCORE Condition ************************/
	/************* editor : Orrawin ***********************/

	echo '<tr><td>&nbsp;&nbsp;'._CORRECTANS.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="text" NAME="correctscore" SIZE="2" VALUE="'.$quizinfo['correctscore'].'"> ';
	echo '</td></tr>';
	echo '<tr><td>&nbsp;&nbsp;'._WRONGANS.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="text" NAME="wrongscore" SIZE="2" VALUE="'.$quizinfo['wrongscore'].'"> ';
	echo '</td></tr>';
	echo '<tr><td>&nbsp;&nbsp;'._NOANS.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="text" NAME="noans" SIZE="2" VALUE="'.$quizinfo['noans'].'"> ';
	echo '</td></tr>';
	/*****************************************************/


	echo '<tr><td>&nbsp;&nbsp;'._SHOWFEEDBACK.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="radio" NAME="feedback" VALUE="1" '.$cfb[1].'> '._SYES.' &nbsp;&nbsp;<INPUT TYPE="radio" NAME="feedback" VALUE="0" '.$cfb[0].'> '._SNO.'';
	echo '</td></tr>';

	echo '<tr><td>&nbsp;&nbsp;'._SHOWANSWER.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="radio" NAME="correctanswers" VALUE="1" '.$cca[1].'> '._SYES.'  &nbsp;&nbsp;<INPUT TYPE="radio" NAME="correctanswers" VALUE="0" '.$cca[0].'>  '._SNO.' ';
	echo '</td></tr>';

	echo '<tr><td>&nbsp;&nbsp;'._GRADEMETHOD.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="radio" NAME="grademethod" VALUE="'._LNQUIZ_GRADE_MAX.'" '.$cgr[1].'> '._GRADEMAXSCORE.' &nbsp;&nbsp;<INPUT TYPE="radio" NAME="grademethod" VALUE="'._LNQUIZ_GRADE_AVG.'" '.$cgr[2].'> '._GRADEAVGSCORE.' &nbsp;&nbsp;<INPUT TYPE="radio" NAME="grademethod" VALUE="'._LNQUIZ_GRADE_LAST.'" '.$cgr[3].'> '._GRADELASTSCORE.' ';
	echo '</td></tr>';
/*
	echo '<tr><td>&nbsp;&nbsp;'._TIMELIMITQUIZ.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="text" NAME="testtime" SIZE="2" VALUE="'.$quizinfo['testtime'].'"> '._TIMELIMITUNIT .' (0 = '._NOTIMELIMIT.')';
	echo '</td></tr>';
*/
	echo '<tr><td>&nbsp;&nbsp;'._QUIZTIME.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="text" NAME="attempts" SIZE="2" VALUE="'.$quizinfo['attempts'].'"> (0 = '._NOQUIZLIMIT.')';
	echo '</td></tr>';

	/********** Add by bas : Quiz Assessment ***************************/

	echo '<tr><td>&nbsp;&nbsp;'._QUIZASSESSMENT.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="text" NAME="assessment" SIZE="2" VALUE="'.$quizinfo['assessment'].'"> %';
	echo '</td></tr>';

	/********** ---------------------------------------  -------- ***************************/
	/*
		echo '<tr><td>&nbsp;&nbsp;'._FULLSCORE.' : </td>';
		echo '<td>';
		echo '<select name="grade">';
		echo '<option value="0">0</option>';
		for ($i=1;$i<=100;$i++) {
		echo '<option value="'.$i.'" '.$sgr[$i].'>'.$i.'</option>';
		}
		echo '</select>';
		echo '</td></tr>';
		*/
	echo '<tr><td>&nbsp;</td><td>';
	echo '<BR>&nbsp;<INPUT CLASS="button" TYPE="submit" VALUE="'._CONTINUE.'">';
	echo " <INPUT CLASS=\"button\" TYPE=button VALUE="._CANCEL." OnClick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;cid=$cid','_self')\"><BR>&nbsp;</td></tr>";
	echo '</FORM>';
	echo '</table>';
	echo '</fieldset></center>';

}


/**
 *	get lessson to listbox

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
 */

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
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$quiz_desc=stripslashes($quiz_desc);

	if (empty($qid)) {
		if (empty($testtime)) $testtime = 0;

		$max_qid = getMaxQID();
		/*
			$query = "INSERT INTO $quiztable
			(	$quizcolumn[qid],
			$quizcolumn[cid],
			$quizcolumn[name],
			$quizcolumn[intro],
			$quizcolumn[attempts],
			$quizcolumn[feedback],
			$quizcolumn[correctanswers],
			$quizcolumn[grademethod],
			$quizcolumn[shufflequestions],
			$quizcolumn[testtime],
			$quizcolumn[grade],
			$quizcolumn[assessment]
			)
			VALUES ('" . lnVarPrepForStore($max_qid) . "',
			'" . lnVarPrepForStore($cid) . "',
			'" . $quiz_name . "',
			'" . $quiz_desc . "',
			'" . lnVarPrepForStore($attempts) . "',
			'" . lnVarPrepForStore($feedback) . "',
			'" . lnVarPrepForStore($correctanswers) . "',
			'" . lnVarPrepForStore($grademethod) . "',
			'" . lnVarPrepForStore($shufflequestions) . "',
			'" . lnVarPrepForStore($testtime) . "',
			'" . lnVarPrepForStore($grade) . "',
			'" . lnVarPrepForStore($assessment) . "')";
			*/
		$query = "INSERT INTO $quiztable
		(	$quizcolumn[qid],
		$quizcolumn[cid],
		$quizcolumn[name],
		$quizcolumn[intro],
		$quizcolumn[attempts],
		$quizcolumn[feedback],
		$quizcolumn[correctanswers],
		$quizcolumn[grademethod],
		$quizcolumn[shufflequestions],
		$quizcolumn[testtime],
		$quizcolumn[grade],
		$quizcolumn[assessment],
		$quizcolumn[correctscore],
		$quizcolumn[wrongscore],
		$quizcolumn[noans]
		)
		VALUES ('" . lnVarPrepForStore($max_qid) . "',
			'" . lnVarPrepForStore($cid) . "',
			'" . $quiz_name . "',
			'" . $quiz_desc . "',
			'" . lnVarPrepForStore($attempts) . "',
			'" . lnVarPrepForStore($feedback) . "',
			'" . lnVarPrepForStore($correctanswers) . "',
			'" . lnVarPrepForStore($grademethod) . "',
			'" . lnVarPrepForStore($shufflequestions) . "',
			'" . lnVarPrepForStore($testtime) . "',
			'" . lnVarPrepForStore($grade) . "',
			'" . lnVarPrepForStore($assessment) . "',
			'" . lnVarPrepForStore($correctscore) . "',
			'" . lnVarPrepForStore($wrongscore) . "',
			'" . lnVarPrepForStore($noans) . "')";	
		$dbconn->Execute($query);

	}
	else {
		if (empty($testtime)) $testtime = 0;
		$query = "UPDATE $quiztable SET
		$quizcolumn[name] =   '" . $quiz_name . "' ,
		$quizcolumn[intro] =   '" . $quiz_desc . "' ,
		$quizcolumn[attempts] =   '" . lnVarPrepForStore($attempts) . "',
		$quizcolumn[feedback] =   '" . lnVarPrepForStore($feedback) . "',
		$quizcolumn[correctanswers] =   '" . lnVarPrepForStore($correctanswers) . "',
		$quizcolumn[grademethod] =   '" . lnVarPrepForStore($grademethod) . "',
		$quizcolumn[shufflequestions] =   '" . lnVarPrepForStore($shufflequestions) . "',
		$quizcolumn[testtime] =   '" . lnVarPrepForStore($testtime) . "',
		$quizcolumn[grade] =   '" . lnVarPrepForStore($grade) . "' ,
		$quizcolumn[assessment] =   '" . lnVarPrepForStore($assessment) . "',
		$quizcolumn[correctscore] =   '" . lnVarPrepForStore($correctscore) . "' ,
		$quizcolumn[wrongscore] =   '" . lnVarPrepForStore($wrongscore) . "' ,
		$quizcolumn[noans] =   '" . lnVarPrepForStore($noans) . "'
		WHERE $quizcolumn[qid] = '" . lnVarPrepForStore($qid) . "'";
			
		$dbconn->Execute($query);
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
	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	if (!empty($qid)) {

		//detete quiz table เธ�เธธเธ”เธ�เน�เธญเธชเธญเธ�
		$query = "DELETE FROM $quiztable WHERE $quizcolumn[qid] =  '" . lnVarPrepForStore($qid) . "'";
		$dbconn->Execute($query);

		// delete choice table เธ�เน�เธญเธชเธญเธ�เน�เธ•เน�เธฅเธฐเธ�เน�เธญ
		$query = "SELECT $quiz_multichoicecolumn[mcid] FROM $quiz_multichoicetable WHERE $quiz_multichoicecolumn[qid] =  '" . lnVarPrepForStore($qid) . "'";
		$result = $dbconn->Execute($query);
		$numrows = $result->PO_RecordCount();
		while(list($quid) = $result->fields) {
			$result->MoveNext();
			$dbconn->Execute("DELETE FROM $quiz_choicetable WHERE $quiz_choicecolumn[mcid] =  '" . lnVarPrepForStore($quid) . "'");
		}
			
		// delete question table
		if ($numrows > 0) {
			$query = "DELETE FROM $quiz_multichoicetable WHERE $quiz_multichoicecolumn[qid] =  '" . lnVarPrepForStore($qid) . "'";
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


/**
 *	add question form
 */
function addQuestionForm($vars) {
	// Get arguments from argument array
	extract($vars);

	if (empty($qid)) {
		$qid = getMaxQID()-1;
	}

	$quizinfo =  lnQuizGetVars($qid);

	echo '<IMG SRC="images/global/linkicon.gif" BORDER="0" ALT=""> <A HREF="index.php?mod=Courses&file=admin&op=quiz&cid='.$cid.'">'._TEST.'</A> &gt; <A HREF="index.php?mod=Courses&file=admin&op=quiz&action=edit_quiz&cid='.$cid.'&qid='.$qid.'">'.$quizinfo['name'].'</A> &gt;  <B>'._QUESTION.'</B>';

	echo '<form name=addquestion><fieldset><table border=0 cellpadding=3 cellspacing=0 width=100%>';
	echo '<tr><td width=50%>'._CREATEQUESTION.' : ';
	echo '<SELECT NAME="popup" onchange="self.location=document.addquestion.popup.options[document.addquestion.popup.selectedIndex].value">';
	echo '<OPTION>'._SELECTQUESTIONTYPE.'</OPTION>';
	echo '<OPTION value="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_multichoice_form&amp;cid='.$cid.'&amp;qid='.$qid.'">'._MULTICHOICE.'</OPTION>';
	echo '<OPTION value="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_clozetest_form&amp;cid='.$cid.'&amp;qid='.$qid.'">'._CLOZETEST.'</OPTION>';
	echo '<OPTION value="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_multiquestion_form&amp;cid='.$cid.'&amp;qid='.$qid.'">'._MULTIQUESTION.'</OPTION>';
	echo '</SELECT></td>';

	echo '</td></tr>';
	echo '</table></fieldset></form>';
}


/**
 * add Mutiple Choice Question
 */
function addMultiChoiceForm($vars) {
	// Get arguments from argument array
	extract($vars);

	$quizinfo =  lnQuizGetVars($qid);

	echo '<IMG SRC="images/global/linkicon.gif" BORDER="0" ALT=""> <A HREF="index.php?mod=Courses&file=admin&op=quiz&cid='.$cid.'">'._TEST.'</A> &gt; <A HREF="index.php?mod=Courses&file=admin&op=quiz&action=edit_quiz&cid='.$cid.'&qid='.$qid.'">'.$quizinfo['name'].'</A>  &gt; <A HREF="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_question_form&amp;cid='.$cid.'&amp;qid='.$qid.'">'._QUESTION.'</A> &gt;  <B>'._CREATEQUESTION.'</B>';

	if (empty($nochoice)) { // default no of choice = 4
		$nochoice = _LNCHOICE;
	}
	if (empty($score)) {	// default score=1
		$score = _LNSCORE;
	}

	// increase item 0.5 before resequence
	if ($op == "insert_question_form") {
		$itemold = $item;
		$item += 0.5;
	}
	// click button delete choice
	if ($action == "delete_choice") {
		deleteChoice($vars);
	}

	// edit question
	if (!empty($mcid)) {
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
			
		$quiz_multichoicetable = $lntable['quiz_multichoice'];
		$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
		$quiz_choicetable = $lntable['quiz_choice'];
		$quiz_choicecolumn = &$lntable['quiz_choice_column'];
			
		$query = "SELECT $quiz_multichoicecolumn[qid],
		$quiz_multichoicecolumn[question],
		$quiz_multichoicecolumn[answer],
		$quiz_multichoicecolumn[score],
		$quiz_multichoicecolumn[weight],
		$quiz_choicecolumn[chid],
		$quiz_choicecolumn[mcid],
		$quiz_choicecolumn[answer],
		$quiz_choicecolumn[feedback],
		$quiz_choicecolumn[weight]
		FROM  $quiz_multichoicetable LEFT JOIN $quiz_choicetable ON $quiz_multichoicecolumn[mcid] = $quiz_choicecolumn[mcid]
		WHERE 	$quiz_multichoicecolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'
		ORDER BY $quiz_choicecolumn[weight]";

		$result = $dbconn->Execute($query);
		list($qid,$question,$answer,$score,$weight,$_,$_,$_,$_,$_) = $result->fields;

		$chid =  array();
		$choice = array();
		$desc = array();
		for ($i=0,$nochoice=0; list($_,$_,$_,$_,$_,$cd,$cm,$ch,$dc,$cw) = $result->fields; $i++) {
			$result->MoveNext();
			$chid[$i]=$cd;
			$choice[$i] = $ch;
			$desc[$i] = $dc;
			$nochoice++;
		}
	}

	// show input form
	?>
<SCRIPT LANGUAGE="JavaScript">
		<!--
			function checkFields() {
				var select_choice=0;
				var select_ans=0;
				for (i = 0; i < document.forms.questionform.length; i++) {
					name="";
					value="";
					name=document.forms.questionform.elements[i].name;
					value=document.forms.questionform.elements[i].value;
					if (document.forms.questionform.elements[i].type == 'textarea') {
						if (name == "question" && value == "" ) {
							return 'question ?';
						}
						if (name.substring(0,6) == "choice" && value != "" && select_choice==0) {
								select_choice=1;
						}					
					}
					if (document.forms.questionform.elements[i].type == 'checkbox') {
						value=document.forms.questionform.elements[i].checked;
						if (value == true && select_ans == 0) {
							select_ans = 1;
						}
					}
				}
				if (select_choice == 0) {
					return "choice ?";
				}

				if (select_ans == 0) {
					return "choose any answer(s)";
				}
				return false;
			}
		//-->
		</SCRIPT>
	<?

	echo '<BR>&nbsp;<fieldset><legend>'._QUESTION.'</legend><table cellpadding=3 cellspacing=0 border=0 width=100%>';
	echo "<FORM NAME=\"questionform\" METHOD=POST ACTION=\"index.php\" onSubmit=\"if (empty = checkFields()) {alert(empty); return false;} else { return true;} \">";
	echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="quiz">'
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">'
	.'<INPUT TYPE="hidden" NAME="weight" VALUE="'.$item.'">';

	echo '<INPUT TYPE="hidden" NAME="action" VALUE="add_multichoice">';
	echo '<INPUT TYPE="hidden" NAME="mcid" VALUE="'.$mcid.'">';

	echo '<tr><td width=10% valign=top>&nbsp;&nbsp;'._QUESTION.' :</td>';
	echo '<td>';
	echo '<TEXTAREA NAME="question" ROWS="4" COLS="35" wrap="soft" style="width: 90%;">'.lnVarPrepForDisplay($question).'</TEXTAREA>';
	echo "<INPUT  TYPE=button VALUE=' ... ' onclick=\"javascript:popup('index.php?mod=spaw&amp;type=Question&amp;cid=$cid&amp;mcid=$mcid','_blank',750,480)\">";
	echo '</td></tr>';


	echo '<tr><td  valign=top>&nbsp;&nbsp;'._ANSWER.' :</td><td>';
	echo '<table cellpadding=1 cellspacing=0 border=0 width="100%">';

	// form head
	echo '<tr align=center><td>'._CORRECTANS.'</td><td>'._CHOICE.'</td><td align="left">&nbsp;&nbsp;&nbsp;&nbsp;'._DESCRIPTION.'</td></tr>';

	if ($subaction == "delete_item") {
		$nochoice--;
	}
	else if ($subaction =="add_item" && $op != "edit_question_form") {
		$nochoice++;
	}
	if ($nochoice < 1) $nochoice = 1;

	for ($i=0,$n=1; $i<$nochoice; $i++,$n++) {
			
		echo '<tr><td valign=bottom align="center">';
		$ans = pow(2,$i);
		if ($answer & pow(2,$i)) {  // edit choice
			echo '<INPUT checked TYPE="checkbox" NAME="ans['.$i.']" VALUE="'.$ans.'">';
		}
		else {
			echo '<INPUT TYPE="checkbox" NAME="ans['.$i.']" VALUE="'.$ans.'">';
		}
		echo '</td>';
		echo '<td valign="top" width=300>'.$n.'. <TEXTAREA NAME="choice['.$i.']"  style="width: 250;" ROWS="2" COLS="40" wrap="soft">'.lnVarPrepForDisplay($choice[$i]).'</TEXTAREA>';

		echo "<INPUT  class=button TYPE=button VALUE=' ... ' onclick=\"javascript:popup('index.php?mod=spaw&amp;type=Choice&amp;cid=$cid&amp;chid=".$chid[$i]."&amp;n=".$i."','_blank',750,480)\">";
		echo '</td><td valign="top" align="left"><TEXTAREA NAME="desc['.$i.']" style="width: 120;" ROWS="2" COLS="18" wrap="soft">'.lnVarPrepForDisplay($desc[$i]).'</TEXTAREA>';

		echo '<INPUT class="inpurt" TYPE="hidden" NAME="chid['.$i.']" VALUE="'.$chid[$i].'">';

		if (empty($mcid)) {
			if ($i == $nochoice -1) { // show at last of choice
				echo " <input class='button'  type=button value=' + '  Onclick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin&amp;op=$op&amp;action=add_multichoice_form&amp;subaction=add_item&amp;cid=$cid&amp;qid=$qid&amp;item=$itemold&amp;nochoice=$nochoice','_self')\"> ";
				if ($n != 1) {
					echo "<input class='button' type=button value='  -  ' Onclick=\"javascript: window.open('index.php?mod=Courses&amp;file=admin&amp;op=$op&amp;action=add_multichoice_form&amp;subaction=delete_item&amp;cid=$cid&amp;qid=$qid&amp;item=$itemold&amp;nochoice=$nochoice','_self')\">";
				}
			}
		}

		echo '</td></tr>';

	}

	echo '</table>';
	echo '</td></tr>';

	// score
	echo '<tr><td width=10%>&nbsp;&nbsp;'._QUESTIONSCORE.' :</td>';
	echo '<td><INPUT class="input" TYPE="text" NAME="score" SIZE="2" VALUE="'.$score.'"></td></tr>';
	echo '<tr><td>&nbsp;</td><td><BR>';

	// submit buttons
	if (empty($quid)) {
		echo '<INPUT CLASS="button_org" TYPE="submit" VALUE="'._ADDQUESTION.'">';
	}
	else {
		echo '<INPUT CLASS="button_org" TYPE="submit" VALUE="'._UPDATEQUIZ.'">';
	}

	echo " <INPUT CLASS='button_org' TYPE=button VALUE="._CANCEL." OnClick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&action=add_question_form&amp;qid=$qid&amp;cid=$cid','_self')\"><BR><BR></td></tr>";
	echo '</FORM>';

	echo '</table></fieldset>';
}


/**
 * add question
 */
function addMultiChoice($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	// calculate answer
	$answer = 0;
	foreach ($ans as $val) {
		if (!empty($val)) {
			$answer += $val;
		}
	}

	// add question
	$question=stripslashes($question);
	if (empty($mcid)) {
		$max_mcid= getMaxMCID();
		if (empty($weight)) {
			$weight = getNextMCWeight($qid);
		}
		$query = "INSERT INTO $quiz_multichoicetable
		(	$quiz_multichoicecolumn[mcid],
		$quiz_multichoicecolumn[qid],
		$quiz_multichoicecolumn[question],
		$quiz_multichoicecolumn[answer],
		$quiz_multichoicecolumn[score],
		$quiz_multichoicecolumn[weight]
		)
		VALUES ('" . lnVarPrepForStore($max_mcid) . "',
						  '" . lnVarPrepForStore($qid) . "',
						  '" . lnVarPrepForStore($question) . "',
						  '" . lnVarPrepForStore($answer) . "',
						  '" . lnVarPrepForStore($score) . "',
						  '" . lnVarPrepForStore($weight) . "')";	
			
		$dbconn->Execute($query);

		for ($i=0,$n=0; $i < count($choice); $i++) {
			//	if (!empty($choice[$i])) {
			$max_chid= getMaxCHID();
			$choice[$i]=stripslashes($choice[$i]);
			$desc[$i]=addslashes($desc[$i]);
			$n++;
			$query = "INSERT INTO $quiz_choicetable
			(	$quiz_choicecolumn[chid],
			$quiz_choicecolumn[mcid],
			$quiz_choicecolumn[answer],
			$quiz_choicecolumn[feedback],
			$quiz_choicecolumn[weight]
			)
			VALUES ('" . lnVarPrepForStore($max_chid) . "',
								  '" . lnVarPrepForStore($max_mcid) . "',
								  '" . lnVarPrepForStore($choice[$i]) . "',
								  '" . lnVarPrepForStore($desc[$i]) . "',
								  '" . lnVarPrepForStore($n) . "')";	

			$dbconn->Execute($query);

			//	}
		}
	}

	// update question
	else {


		$query = "UPDATE $quiz_multichoicetable SET
		$quiz_multichoicecolumn[question] =  '" . lnVarPrepForStore($question) . "',
		$quiz_multichoicecolumn[answer] = '" . lnVarPrepForStore($answer) . "',
		$quiz_multichoicecolumn[score] =	'" . lnVarPrepForStore($score) . "',
		$quiz_multichoicecolumn[weight] = '" . lnVarPrepForStore($weight) . "'
		WHERE 	$quiz_multichoicecolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'";

		$dbconn->Execute($query);

		for ($i=0,$n=0; $i < count($choice); $i++) {
			if (!empty($choice[$i])) {
				$max_chid= getMaxCHID();
				$choice[$i]=stripslashes($choice[$i]);
				$desc[$i]=addslashes($desc[$i]);
				$n++;
				$query = "UPDATE $quiz_choicetable SET
				$quiz_choicecolumn[answer] =  '" . lnVarPrepForStore($choice[$i]) . "',
				$quiz_choicecolumn[feedback] =  '" . lnVarPrepForStore($desc[$i]) . "',
				$quiz_choicecolumn[weight] = '" . lnVarPrepForStore($n) . "'
				WHERE 	$quiz_choicecolumn[chid] =  '" . lnVarPrepForStore($chid[$i]) . "'";
				$dbconn->Execute($query);
			}
		}

	}

	//resequenceQuestions($qid);
}


/**
 * get max weight of qid
 */
function getNextMCWeight($qid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];

	$result = $dbconn->Execute("SELECT MAX($quiz_multichoicecolumn[weight]) FROM $quiz_multichoicetable WHERE $quiz_multichoicecolumn[qid]='$qid'");
	list($max_weight) = $result->fields;

	return $max_weight + 1;
}


/**
 * get next quiz_question id
 */
function getMaxMCID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];

	$result = $dbconn->Execute("SELECT MAX($quiz_multichoicecolumn[mcid]) FROM $quiz_multichoicetable");
	list($max_quid) = $result->fields;

	return $max_quid + 1;
}


/**
 * get next choice id
 */
function getMaxCHID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	$result = $dbconn->Execute("SELECT MAX($quiz_choicecolumn[chid]) FROM $quiz_choicetable");
	list($max_chid) = $result->fields;

	return $max_chid + 1;
}


/**
 * list question
 */
function listQuestion($vars) {
	// Get arguments from argument array
	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	//>>>> Show Questions
	$query = "SELECT  $quiz_multichoicecolumn[mcid],
	$quiz_multichoicecolumn[qid],
	$quiz_multichoicecolumn[question],
	$quiz_multichoicecolumn[answer],
	$quiz_multichoicecolumn[score],
	$quiz_multichoicecolumn[weight],
	$quiz_multichoicecolumn[type]
	FROM  $quiz_multichoicetable
	WHERE 	$quiz_multichoicecolumn[qid] =  '" . lnVarPrepForStore($qid) . "'
	ORDER BY $quiz_multichoicecolumn[weight]";

	$result = $dbconn->Execute($query);

	$question_numrows = $result->PO_RecordCount();

	$rownum = 1;
	$lastpos = '';
	$active_count = 0;

	if ($question_numrows > 0) {
		echo '<table width= 100% cellpadding=3 cellspacing=1 bgcolor=#999999 border=0>';

		// list questions
		$url=COURSE_DIR.'/'.$cid;
		$no = 1;
		while(list($quid,$qid,$question,$answer,$score,$weight,$type) = $result->fields) {
			$result->MoveNext();
			$next = 0;
			//check no. question
			$arraynext = array(0,0);
			$uncount = 0;

			$active_count++;
			$down = "<a href=index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=increase_weight&amp;qid=$qid&amp;cid=$cid&amp;quid=$quid&amp;weight=$weight><img src=images/global/down.gif border=0 alt='Move down'></a>";
			$up = "<a href=index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=decrease_weight&amp;qid=$qid&amp;cid=$cid&amp;quid=$quid&amp;weight=$weight><img src=images/global/up.gif border=0 alt='Move up'></a>";
			switch($rownum) { // show position
				case 1:
					if ($nextpos != $position) {
						$arrows = '';
					} else {
						$arrows = "$down";
					}
					break;
				case $question_numrows:
					if ($lastpos != $position) {
						$arrows = '';
					} else {
						$arrows = "$up";
					}
					break;
				default:
					$arrows = "$up $down";
					break;
			}
			$rownum++;
			$lastpos = $position;

			// Check Type Multichoice=1 ClozeTest=2
			if($type=='1'){
				$next = listQuestionMultiChoice($cid,$quid,$qid,$question,$answer,$score,$weight,$type,$no);
			}else if(($type=='2')||($type=='0')){
				$arraynext = listQuestionClozeTest($cid,$quid,$qid,$question,$answer,$score,$weight,$type,$no);
				$next = $arraynext[0];
				$uncount = $arraynext[1];
			}else if($type=='3'){
				$arraynext = listQuestionMultiQuestion($cid,$quid,$qid,$question,$answer,$score,$weight,$type,$no);
				$next = $arraynext[0];
				$uncount = $arraynext[1];
			}

			if($next!=0){
				for($movenext=0;$movenext<$next;$movenext++){
					$result->MoveNext();
					$no++;
				}
				if($uncount!=0){
					$no--;
				}
			}
			$no++;
		}

		echo '</table>';
			
	}

}

//listQuestionMultiChoice
function listQuestionMultiChoice($cid,$quid,$qid,$question,$answer,$score,$weight,$type,$no){

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];


	$next = 0;
	//echo "Type = ".$type."<br>";
	//echo "Weight = ".$weight."<br>";

	// show question
	$queston=nl2br(stripslashes($question));
	$question=lnShowContent($question,$url);
	echo "<tr valign=top bgcolor=#ffffff valign=top>";
	//comment weigth arrows
	//echo '<td width=5% align=center>'.$arrows.'</td>';
	echo "<td>";
	echo "<table cellpadding=0 cellspacing=0 border=0 width=100%>";
	echo "<tr><td valign=top><B>".$no.". ".$question."</B> ("._QUESTIONSCORE." $score)</td>";
	echo "<td align=right valign=top>";

	echo "<A HREF=\"index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_multichoice_form&amp;qid=$qid&amp;cid=$cid&amp;mcid=$quid&amp;item=$weight&amp;type=$type\"><IMG SRC=images/global/edit.gif  BORDER=0 ALT="._EDIT."></A>  &nbsp;";

	echo "<A HREF=\"javascript: if(confirm('Delete quiz $weight?')) window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=delete_question&amp;qid=$qid&amp;cid=$cid&amp;mcid=$quid&amp;weight=$weight&amp;type=$type','_self')\"><IMG SRC=images/global/delete.gif  BORDER=0 ALT="._DELETE."></A>";
	echo "</td></tr></table>";

	//>>>> Choice
	$query = "SELECT  $quiz_choicecolumn[chid],
	$quiz_choicecolumn[answer],
	$quiz_choicecolumn[feedback]
	FROM  $quiz_choicetable
	WHERE 	$quiz_choicecolumn[mcid] =  '" . lnVarPrepForStore($quid) . "'
	ORDER BY $quiz_choicecolumn[weight]";
	$result2 = $dbconn->Execute($query);
	$choice_numrows = $result2->PO_RecordCount();

	if ($choice_numrows > 0) {
		echo '<table width= 100% cellpadding=0 cellspacing=0 border=0>';

		// list choices
		for($i=0; list($chid,$choice,$description) = $result2->fields; $i++) {
			$choice=nl2br(stripslashes($choice));
			$choice=lnShowContent($choice,$url);
			$description=nl2br(stripslashes($description));
			$description=lnShowContent($description,$url);
			$result2->MoveNext();
			echo '<tr valign=top bgcolor=#ffffff valign=top>';
			echo '<td>';
			if (checkChoiceType($quid,$answer) == 0) {
				if ($answer & pow(2,$i)) {
					echo '<INPUT checked TYPE="radio" NAME="'.$weight.'">';
				}
				else {
					echo '<INPUT TYPE="radio" NAME="'.$weight.'">';
				}
			}
			else {
				if ($answer & pow(2,$i)) {
					echo '<INPUT checked TYPE="checkbox" NAME="'.$weight.'">';
				}
				else {
					echo '<INPUT TYPE="checkbox" NAME="'.$weight.'">';
				}
			}
			echo  $choice;
			if (!empty($description)) {
				echo '<FONT  COLOR="#444444"> - '. $description .'</FONT>';
			}
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';

	}

	echo '</td></tr>';

	//>>>> Choice
	return $next;
}

//listQuestionClozeTest
function listQuestionClozeTest($cid,$quid,$qid,$question,$answer,$score,$weight,$type,$no){

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	$no2 = $no;
	$next= array(0,0);
	$index = 0;
	$allanswer;
	//echo "Type = ".$type."<br>";
	//echo "Weight = ".$weight."<br>";
	//SELECT * FROM `ln_quiz_multichoice`WHERE `ln_weight` =5 AND `ln_qid` =1 ORDER BY `ln_mcid`

	//Show Question ClozeQuestion
	$query = "SELECT  $quiz_multichoicecolumn[mcid],
	$quiz_multichoicecolumn[qid],
	$quiz_multichoicecolumn[question],
	$quiz_multichoicecolumn[answer],
	$quiz_multichoicecolumn[score],
	$quiz_multichoicecolumn[weight],
	$quiz_multichoicecolumn[type]
	FROM  $quiz_multichoicetable
	WHERE 	$quiz_multichoicecolumn[weight] =  '" .$weight . "' AND $quiz_multichoicecolumn[qid] = '" .$qid. "'
	ORDER BY $quiz_multichoicecolumn[mcid]";

	//echo $query."<br>";

	$result = $dbconn->Execute($query);

	$question_numrows = $result->PO_RecordCount();
	echo "<tr valign=top bgcolor=#ffffff valign=top>";
	echo "<td>";

	echo "<table cellpadding=0 cellspacing=0 border=0 width=100%>";

	echo "<tr align='right'><td>";
	//echo "<A HREF=\"index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_multichoice_form&amp;qid=$qid&amp;cid=$cid&amp;mcid=$quid&amp;item=$weight&amp;type=$type\"><IMG SRC=images/global/edit.gif  BORDER=0 ALT="._EDIT."></A>  &nbsp;";

	echo "<A HREF=\"javascript: if(confirm('Delete quiz $weight?')) window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=delete_question&amp;qid=$qid&amp;cid=$cid&amp;mcid=$quid&amp;weight=$weight&amp;type=$type','_self')\"><IMG SRC=images/global/delete.gif  BORDER=0 ALT="._DELETE."></A>";
	echo "</td></tr>";

	echo "<tr><td valign=top>";
	echo "<B>ClozeTest</B><BR>";
	echo '<fieldset><legend><B>'.$_CLOZETESTNAME.' ClozeTest'.'</B></legend>';
	echo "<BR>";
	//echo "<B>ข้อสอบเติมคำ</B><br>";
	while(list($quid,$qid,$question,$answer,$score,$weight,$type) = $result->fields) {
	 $queston=nl2br(stripslashes($question));
	 $question=lnShowContent($question,$url);
	 $result->MoveNext();

	 echo $question;
	 if($type!=0){
	  echo " ____<u> ("._ClozeTestQuiz." ".$no." "._QUESTIONSCORE." $score) </u>_____ ";
	  // score
	  $no++;
	 }else{
	 	$next[1]++;
	 }
	 //echo $quid." ".$qid." ".$question." ".$answer." ".$score." ".$weight." ".$type." <br>";

	 //list Choice
	 $query = "SELECT  $quiz_choicecolumn[chid],
	 $quiz_choicecolumn[mcid],
	 $quiz_choicecolumn[answer],
	 $quiz_choicecolumn[feedback]
	 FROM  $quiz_choicetable
	 WHERE 	$quiz_choicecolumn[mcid] =  '" . lnVarPrepForStore($quid) . "'
	 ORDER BY $quiz_choicecolumn[weight]";
	 $result2 = $dbconn->Execute($query);
	 $choice_numrows = $result2->PO_RecordCount();

	 $listanswer = array();

		if ($choice_numrows > 0) {

			// list choices
			for($i=0; list($chid,$mcid,$choice,$description) = $result2->fields; $i++) {
				$choice=nl2br(stripslashes($choice));
				$choice=lnShowContent($choice,$url);
				$description=nl2br(stripslashes($description));
				$description=lnShowContent($description,$url);
				$listanswer[$i] = array ( "chid" => $chid, "mcid" => $mcid, "choice" => $choice, "description" => $description, "answer" => $answer);
				$result2->MoveNext();

			}
			$allanswer[$index] = $listanswer;
			unset($listanswer);
		}

		$index++;

		//End list Choice
	 $next[0]++;
	}
	echo '</fieldset></td></tr></table>';

	echo "</B>";
	echo "<br><br>";
	echo "<B>"._ClozeTestCommand."</B><br>";

	//show Choice
	//print_r($allanswer);
	$cnum = 1;

	echo '<table width= 100% cellpadding=0 cellspacing=0 border=0>';

	foreach($allanswer as $arrayanswer){
		echo '<tr valign=top bgcolor=#ffffff valign=top>';
		echo '<td>';
		echo "<BR><p><B>"._NOWQUIZMSG3." ".$no2."</B></p>";
		//echo "<hr>";
		$num=0;
		foreach($arrayanswer as $eachanswer){
			echo '<tr valign=top bgcolor=#ffffff valign=top>';
			echo '<td>';
			//echo $eachanswer['answer'];
			if ($eachanswer['answer'] & pow(2,$num)) {
				echo ' <INPUT checked TYPE="radio" NAME="'.$eachanswer['mcid'].'">';
			}else {
				echo '<INPUT TYPE="radio" NAME="'.$eachanswer['mcid'].'">';
			}

			echo $eachanswer['choice'];
			//echo $num." ".$eachanswer['choice']."<br>";
			$num++;
			if (!empty($description)) {
				echo '<FONT  COLOR="#444444"> - '. $description .'</FONT>';
			}
			echo '</td>';
			echo '</tr>';
		}
		$no2++;
		echo '</td>';
		echo '</tr>';
	}
	echo '</table><BR>';
	//echo "<hr>";

	//End Show Question ClozeQuestion
	echo "</td></tr>";
	$next[0]--;
	return $next;
}

//listQuestionMultiQuestion
function listQuestionMultiQuestion($cid,$quid,$qid,$question,$answer,$score,$weight,$type,$no){

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	//$no2 = $no;
	$next= array(0,0);
	$index = 0;

	//Show Question ClozeQuestion
	$query = "SELECT $quiz_multichoicecolumn[mcid],
	$quiz_multichoicecolumn[question],
	$quiz_multichoicecolumn[type],
	$quiz_multichoicecolumn[weight]
	FROM $quiz_multichoicetable
	WHERE $quiz_multichoicecolumn[qid]= '". lnVarPrepForStore($qid)."' and
	$quiz_multichoicecolumn[type] = '3' and $quiz_multichoicecolumn[weight] = '".$weight."'
	ORDER BY $quiz_multichoicecolumn[mcid]";

	$result = $dbconn->Execute($query);
	$numrow=$result->RecordCount();

	//echo '<form id="questionform" name="questionform" method="post" action="">';
	echo "<tr valign=top bgcolor=#ffffff valign=top>";
	echo "<td>";

	echo "<table cellpadding=0 cellspacing=0 border=0 width=100%>";
	echo "<tr align='right'><td>";
	//echo "<A HREF=\"index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_multichoice_form&amp;qid=$qid&amp;cid=$cid&amp;mcid=$quid&amp;item=$weight&amp;type=$type\"><IMG SRC=images/global/edit.gif  BORDER=0 ALT="._EDIT."></A>  &nbsp;";

	echo "<A HREF=\"javascript: if(confirm('Delete quiz $weight?')) window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=delete_question&amp;qid=$qid&amp;cid=$cid&amp;mcid=$quid&amp;weight=$weight&amp;type=$type','_self')\"><IMG SRC=images/global/delete.gif  BORDER=0 ALT="._DELETE."></A>";
	echo "</td></tr></table>";

	echo '<B>MultiQuestion</B><BR>';
	//show multiquestion
	//for ($i=0; list($cid,$questionstroy,$type,$weight) = $result->fields; $i++) {
	//$result->MoveNext();
	$questionstroy = $result->fields[1];
	echo '<fieldset><legend><B>'.'เนื้อเรื่อง MultiQuestion'.'</B></legend>';
	echo "<BR>";
	echo $questionstroy;
	$oldweight = $weight;
	echo '</fieldset>';
	//break;
	//}

	//Selection
	$query = "SELECT $quiz_multichoicecolumn[mcid],
	$quiz_multichoicecolumn[qid],
	$quiz_multichoicecolumn[question],
	$quiz_multichoicecolumn[answer],
	$quiz_multichoicecolumn[score],
	$quiz_multichoicecolumn[weight],
	$quiz_multichoicecolumn[type]
	FROM $quiz_multichoicetable
	WHERE $quiz_multichoicecolumn[qid]= '". lnVarPrepForStore($qid)."' and
	$quiz_multichoicecolumn[type] = '3' and $quiz_multichoicecolumn[weight] = '".$weight."'
	and $quiz_multichoicecolumn[answer] <> '0'
	ORDER BY $quiz_multichoicecolumn[mcid]";

	$result = $dbconn->Execute($query);
	$numrow=$result->RecordCount();
	echo "<BR>";
	//$next = 0;


	for ($i=0; list($mcid,$quid,$question,$answer,$score,$weight,$type) = $result->fields; $i++) {
		$result->MoveNext();
		// show question
		$queston=nl2br(stripslashes($question));
		$question=lnShowContent($question,$url);
		echo "<table cellpadding=0 cellspacing=0 border=0 width=100%>";
		echo "<tr><td valign=top><B>".$no.". ".$question."</B> ("._QUESTIONSCORE." $score)</td>";
		echo "</td></tr></table>";

		//>>>> Choice
		$query = "SELECT  $quiz_choicecolumn[chid],
		$quiz_choicecolumn[answer],
		$quiz_choicecolumn[feedback]
		FROM  $quiz_choicetable
		WHERE 	$quiz_choicecolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'
		ORDER BY $quiz_choicecolumn[weight]";
		$result2 = $dbconn->Execute($query);
		$choice_numrows = $result2->PO_RecordCount();

		if ($choice_numrows > 0) {
			echo '<table width= 100% cellpadding=0 cellspacing=0 border=0>';

			// list choices
			for($i=0; list($chid,$choice,$description) = $result2->fields; $i++) {
				$choice=nl2br(stripslashes($choice));
				$choice=lnShowContent($choice,$url);
				$description=nl2br(stripslashes($description));
				$description=lnShowContent($description,$url);
				$result2->MoveNext();
				echo '<tr valign=top bgcolor=#ffffff valign=top>';
				echo '<td>';
				if (checkChoiceType($quid,$answer) == 0) {
					if ($answer & pow(2,$i)) {
						echo '<INPUT checked TYPE="radio" NAME="'.$chid.$weight.'">';
					}
					else {
						echo '<INPUT TYPE="radio" NAME="'.$chid.$weight.'">';
					}
				}
				else {
					if ($answer & pow(2,$i)) {
						echo '<INPUT checked TYPE="checkbox" NAME="'.$chid.$weight.'">';
					}
					else {
						echo '<INPUT TYPE="checkbox" NAME="'.$chid.$weight.'">';
					}
				}
				echo  $choice;
				if (!empty($description)) {
					echo '<FONT  COLOR="#444444"> - '. $description .'</FONT>';
				}
				echo '</td>';
				echo '</tr>';
			}
			echo '</table><BR>';

		}

		//echo '</table>';
		$no++;
		$next[0]++;
	}
	//$next[0]++;
	$next[1]++;

	echo '</td></tr>';
	return $next;
}

/**
 * check question type
 *  - single answer or multiple answer choice
 */
function checkChoiceType($quid, $answer) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];
	$result = $dbconn->Execute("SELECT COUNT($quiz_choicecolumn[mcid]) FROM $quiz_choicetable WHERE $quiz_choicecolumn[mcid]='$quid'");
	list ($sum) = $result->fields;

	for ($i=0,$count=0; $i<$sum; $i++) {
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
 * delete question
 */
function deleteQuestion($vars) {
	// Get arguments from argument array
	extract($vars);
	//print_r($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	if($type == '1'){
		if (!empty($mcid)) {
			// delete question
			$query = "DELETE FROM $quiz_multichoicetable WHERE $quiz_multichoicecolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'";
			$dbconn->Execute($query);

			// delete choice
			$query = "DELETE FROM $quiz_choicetable WHERE $quiz_choicecolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'";
			$dbconn->Execute($query);
		}
	}else{
		//Show Question ClozeQuestion
		$query = "SELECT  $quiz_multichoicecolumn[mcid],
		$quiz_multichoicecolumn[qid],
		$quiz_multichoicecolumn[question],
		$quiz_multichoicecolumn[answer],
		$quiz_multichoicecolumn[score],
		$quiz_multichoicecolumn[weight],
		$quiz_multichoicecolumn[type]
		FROM  $quiz_multichoicetable
		WHERE 	$quiz_multichoicecolumn[weight] =  '" .$weight . "' AND $quiz_multichoicecolumn[qid] = '" .$qid. "'
		ORDER BY $quiz_multichoicecolumn[mcid]";
		//echo $query."<br>";
		$result = $dbconn->Execute($query);
		$question_numrows = $result->PO_RecordCount();

		while(list($mcid,$qid,$question,$answer,$score,$weight,$type) = $result->fields) {
			$queston=nl2br(stripslashes($question));
			$question=lnShowContent($question,$url);
			$result->MoveNext();

			if (!empty($mcid)) {
				// delete question
				$query = "DELETE FROM $quiz_multichoicetable WHERE $quiz_multichoicecolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'";
				$dbconn->Execute($query);

				// delete choice
				$query = "DELETE FROM $quiz_choicetable WHERE $quiz_choicecolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'";
				$dbconn->Execute($query);
			}
		}

	}
	//resequenceQuestions($qid);
}


/**
 * resequence quiz
 */
function resequenceQuestions($qid) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];

	// Get the information
	$query = "SELECT $quiz_questioncolumn[mcid],
	$quiz_questioncolumn[weight]
	FROM $quiz_questiontable
	WHERE $quiz_questioncolumn[qid]= '". lnVarPrepForStore($qid)."'
	ORDER BY $quiz_questioncolumn[weight]";
	$result = $dbconn->Execute($query);

	// Fix sequence numbers
	$seq=1;
	while(list($quid, $curseq) = $result->fields) {

		$result->MoveNext();
		if ($curseq != $seq) {
			$query = "UPDATE $quiz_questiontable
			SET $quiz_questioncolumn[weight]='" . lnVarPrepForStore($seq) . "'
			WHERE $quiz_questioncolumn[mcid]='" . lnVarPrepForStore($quid)."'";
			$dbconn->Execute($query);
		}
		$seq++;
	}
	$result->Close();

	return true;
}


/**
 * move down item
 */
function increaseQuestionWeight($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];

	$seq = $weight;

	// Get info on displaced block
	$sql = "SELECT $quiz_questioncolumn[mcid],
	$quiz_questioncolumn[weight]
	FROM $quiz_questiontable
	WHERE $quiz_questioncolumn[weight] >'" . lnVarPrepForStore($seq) . "'
	AND   $quiz_questioncolumn[qid]='" . lnVarPrepForStore($qid) . "'
	ORDER BY $quiz_questioncolumn[weight] ASC";
	$result = $dbconn->SelectLimit($sql, 1);

	if ($result->EOF) {
		return false;
	}
	list($altquid, $altseq) = $result->fields;
	$result->Close();

	// Swap sequence numbers
	$sql = "UPDATE $quiz_questiontable
	SET $quiz_questioncolumn[weight]=$seq
	WHERE $quiz_questioncolumn[mcid]='".lnVarPrepForStore($altquid)."'";
	$dbconn->Execute($sql);
	$sql = "UPDATE $quiz_questiontable
	SET $quiz_questioncolumn[weight]=$altseq
	WHERE $quiz_questioncolumn[mcid]='".lnVarPrepForStore($quid)."'";
	$dbconn->Execute($sql);

	resequenceQuestions($qid);

	return true;
}


/**
 *	move up item
 */
function decreaseQuestionWeight($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];

	$seq = $weight;

	// Get info on displaced block
	$sql = "SELECT $quiz_questioncolumn[mcid],
	$quiz_questioncolumn[weight]
	FROM $quiz_questiontable
	WHERE $quiz_questioncolumn[weight] < '" . lnVarPrepForStore($seq) . "'
	AND   $quiz_questioncolumn[qid]='" . lnVarPrepForStore($qid) . "'
	ORDER BY $quiz_questioncolumn[weight] DESC";
	$result = $dbconn->SelectLimit($sql, 1);

	if ($result->EOF) {
		return false;
	}
	list($altquid, $altseq) = $result->fields;
	$result->Close();

	// Swap sequence numbers
	$sql = "UPDATE $quiz_questiontable
	SET $quiz_questioncolumn[weight]=$seq
	WHERE $quiz_questioncolumn[mcid]='".lnVarPrepForStore($altquid)."'";
	$dbconn->Execute($sql);

	$sql = "UPDATE $quiz_questiontable
	SET $quiz_questioncolumn[weight]=$altseq
	WHERE $quiz_questioncolumn[mcid]='".lnVarPrepForStore($quid)."'";
	$dbconn->Execute($sql);

	resequenceQuestions($qid);

	return true;
}
//ClozeTest
function addClozeTestForm($vars){
	// Get arguments from argument array
	extract($vars);
	/*
	 echo "addClozeTestForm()<br>";
	 $quizinfo =  lnQuizGetVars($qid);

	 echo '<IMG SRC="images/global/linkicon.gif" BORDER="0" ALT=""> <A HREF="index.php?mod=Courses&file=admin&op=quiz&cid='.$cid.'">'._TEST.'</A> &gt; <A HREF="index.php?mod=Courses&file=admin&op=quiz&action=edit_quiz&cid='.$cid.'&qid='.$qid.'">'.$quizinfo['name'].'</A>  &gt; <A HREF="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_question_form&amp;cid='.$cid.'&amp;qid='.$qid.'">'._QUESTION.'</A> &gt;  <B>'._CREATEQUESTION.'</B>';

	 if (empty($nochoice)) { // default no of choice = 4
		$nochoice = _LNCHOICE;
		}
		if (empty($score)) {	// default score=1
		$score = _LNSCORE;
		}

		?>
		<SCRIPT LANGUAGE="JavaScript">
		<!--
		function checkFields() {
		var select_choice=0;
		var select_ans=0;
		for (i = 0; i < document.forms.questionform.length; i++) {
		name="";
		value="";
		name=document.forms.questionform.elements[i].name;
		value=document.forms.questionform.elements[i].value;
		if (document.forms.questionform.elements[i].type == 'textarea') {
		if (name == "question" && value == "" ) {
		return 'question ?';
		}
		if (name.substring(0,6) == "choice" && value != "" && select_choice==0) {
		select_choice=1;
		}
		}
		if (document.forms.questionform.elements[i].type == 'checkbox') {
		value=document.forms.questionform.elements[i].checked;
		if (value == true && select_ans == 0) {
		select_ans = 1;
		}
		}
		}
		if (select_choice == 0) {
		return "choice ?";
		}

		if (select_ans == 0) {
		return "choose any answer(s)";
		}
		return false;
		}
		</SCRIPT>
		<?
		*/
	echo '<BR>&nbsp;<fieldset><legend>'._QUESTION.'</legend><table cellpadding=3 cellspacing=0 border=0 width=100%>';
	echo '<form name=addquestion>';
	/*
	 echo "<FORM NAME=\"questionform\" METHOD=POST ACTION=\"index.php\" onSubmit=\"if (empty = checkFields()) {alert(empty); return false;} else { return true;} \">";
	 echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	 .'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	 .'<INPUT TYPE="hidden" NAME="op" VALUE="quiz">'
	 .'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	 .'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">'
	 .'<INPUT TYPE="hidden" NAME="numQ" VALUE="'.$i.'">';
	 echo '<INPUT TYPE="hidden" NAME="action" VALUE="add_question_clozetest_form">';
	 echo '<INPUT TYPE="hidden" NAME="mcid" VALUE="'.$mcid.'">';
	 */
	echo'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">';

	echo '<fieldset><table border=0 cellpadding=3 cellspacing=0 width=100%>';
	echo '<tr><td width=50%>'._NUMQUESTION.' : ';
	echo '<SELECT NAME="popup" onchange="self.location=document.addquestion.popup.options[document.addquestion.popup.selectedIndex].value">';
	echo '<OPTION>'._SELECTNUMQUESTION.'</OPTION>';
	for($i=1;$i<=100;$i++){
		echo '<OPTION value="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_question_clozetest_form&amp;cid='.$cid.'&amp;qid='.$qid.'&amp;numQ='.$i.'">'.$i.'</OPTION>';
	}
	echo '</SELECT></td>';

	echo '</td></tr>';
	echo '</table></fieldset></FORM>';
}

function addQuestionClozeTestForm($vars){
	extract($vars);
	//echo "addQuestionClozeTestForm()<br>";

	echo '<form id="form1" name="form1" method="post" action="">';
	/*
	 echo "<FORM NAME=\"questionform\" METHOD=POST ACTION=\"index.php\" onSubmit=\"if (empty = checkFields()) {alert(empty); return false;} else { return true;} \">";
	 echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	 .'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	 .'<INPUT TYPE="hidden" NAME="op" VALUE="quiz">'
	 .'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	 .'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">'
	 .'<INPUT TYPE="hidden" NAME="type" VALUE="'.$type.'">'
	 .'<INPUT TYPE="hidden" NAME="weight" VALUE="'.$weight.'">'
	 .'<INPUT TYPE="hidden" NAME="weight" VALUE="'.$item.'">';

	 echo '<INPUT TYPE="hidden" NAME="action" VALUE="add_choice_clozetest_form">';
	 echo '<INPUT TYPE="hidden" NAME="mcid" VALUE="'.$mcid.'">';
	 */
	echo'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">';
	echo '<IMG SRC="images/global/linkicon.gif" BORDER="0" ALT=""> <A HREF="index.php?mod=Courses&file=admin&op=quiz&cid='.$cid.'">'._TEST.'</A> &gt; <A HREF="index.php?mod=Courses&file=admin&op=quiz&action=edit_quiz&cid='.$cid.'&qid='.$qid.'">'.$quizinfo['name'].'</A>  &gt; <A HREF="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_question_form&amp;cid='.$cid.'&amp;qid='.$qid.'">'._QUESTION.'</A> &gt;  <B>'._CREATEQUESTION.'</B>';

	echo '<fieldset>
			  <legend>'._QUESTION.'</legend>
			  <br><table width="80%" border="0" cellspacing="1" cellpadding="2" align="center">';
	for($i=1;$i<=$numQ;$i++){
		echo '<tr>
				  <td width="60%">ข้อที่'.$i.' <input type="text" name="question['.$i.']" id="question['.$i.']" size="50" /></td>
				  <td width="30%">(คำตอบข้อที่'.$i.')</td>
				';
		// score
		echo '<td width=10%>&nbsp;&nbsp;'._QUESTIONSCORE.' :</td>';
		echo '<td><INPUT class="input" TYPE="text" NAME="score['.$i.']" id="question['.$i.']" SIZE="2" VALUE="1" /></td>';
		echo '<td>&nbsp;</td></tr>';
	}
	echo '<tr>
				  <td>&nbsp;<input type="text" name="question2" id="question2" size="50" /></td>
				  <td>&nbsp;คำถามสุดท้าย (ไม่มีโปรดเว้นว่าง)</td>
				</tr>
				<tr>
				  <td colspan="2"><input type="button" name="button2" id="button2" value="PREVIOUS" />
          <input type="submit" name="button" id="button" value="    NEXT    " /></td>
				</tr>
			  </table>
			  <br />
			</fieldset>
		  </form>';

	if($button!=""){
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$quiz_multichoicetable = $lntable['quiz_multichoice'];
		$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
		//$quiz_choicetable = $lntable['quiz_choice'];
		//$quiz_choicecolumn = &$lntable['quiz_choice_column'];
		//type = 2
		for($i=1;$i<=$numQ;$i++){
			//echo "<br>$i = ".$question[$i];
			if($question[$i]!=""){
				if (empty($mcid)) {
					$max_mcid= getMaxMCID();
					if (empty($weight)) {
						$weight = getNextMCWeight($qid);
					}
					$type=2;
					//$score=1;
					$query = "INSERT INTO " . $quiz_multichoicetable . " (" .
					$quiz_multichoicecolumn['mcid'] ."," .
					$quiz_multichoicecolumn['qid'] .",".
					$quiz_multichoicecolumn['question'] .",".
					$quiz_multichoicecolumn['answer'] .",".
					$quiz_multichoicecolumn['score'].",".
					$quiz_multichoicecolumn['weight'].",".
					$quiz_multichoicecolumn['type']."
								  )
								VALUES ('" . lnVarPrepForStore($max_mcid) . "',
									  '" . lnVarPrepForStore($qid) . "',
									  '" . lnVarPrepForStore($question[$i]) . "',
									  '" . lnVarPrepForStore($answer) . "',
									  '" . lnVarPrepForStore($score[$i]) . "',
									  '" . lnVarPrepForStore($weight) . "',
									  '" . lnVarPrepForStore($type) . "')";	
					//echo "<br>$query";
					$dbconn->Execute($query);
				}
			}
		}
		//type = 0
		if($question2!=""){
			if (empty($mcid)) {
				$max_mcid= getMaxMCID();
				if (empty($weight)) {
					$weight = getNextMCWeight($qid);
				}
				$type=0;
				$score=0;
				$query = "INSERT INTO " . $quiz_multichoicetable . " (" .
				$quiz_multichoicecolumn['mcid'] ."," .
				$quiz_multichoicecolumn['qid'] .",".
				$quiz_multichoicecolumn['question'] .",".
				$quiz_multichoicecolumn['answer'] .",".
				$quiz_multichoicecolumn['score'].",".
				$quiz_multichoicecolumn['weight'].",".
				$quiz_multichoicecolumn['type']."
							  )
							VALUES ('" . lnVarPrepForStore($max_mcid) . "',
								  '" . lnVarPrepForStore($qid) . "',
								  '" . lnVarPrepForStore($question2) . "',
								  '" . lnVarPrepForStore($answer) . "',
								  '" . lnVarPrepForStore($score) . "',
								  '" . lnVarPrepForStore($weight) . "',
								  '" . lnVarPrepForStore($type) . "')";	
				//echo "<br>$query";
				$dbconn->Execute($query);
			}

		}//close $question2
		echo '<meta http-equiv="refresh" content="0.5;URL=index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_choice_clozetest_form&amp;cid='.$cid.'&amp;qid='.$qid.'&amp;type='.$type.'&amp;weight='.$weight.'" />';
	}
}

function addChoiceClozeTestForm($vars){
	extract($vars);
	//print_r($vars);
	//echo "addChoiceClozeTestForm()<br>";

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];
	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	// Get the information
	$query = "SELECT $quiz_questioncolumn[mcid],

	$quiz_questioncolumn[question],
	$quiz_questioncolumn[type],
	$quiz_questioncolumn[score]
	FROM $quiz_questiontable
	WHERE $quiz_questioncolumn[qid]= '". lnVarPrepForStore($qid)."' and
	$quiz_questioncolumn[type] <> '1' and $quiz_questioncolumn[weight] = '".$weight."'
	ORDER BY $quiz_questioncolumn[mcid]";
	$result = $dbconn->Execute($query);
	$numrow=$result->RecordCount();
	//	and $quiz_multichoicecolumn[weight] = '".lnVarPrepForStore($weight)."'

	//echo $query;
	/*
	 echo '<FORM METHOD=POST ACTION="index.php">'
		.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
		.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
		.'<INPUT TYPE="hidden" NAME="op" VALUE="quiz">'
		.'<INPUT TYPE="hidden" NAME="action" VALUE="add_multichoice">'
		.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">';
		*/
	/*
	 echo '<P><INPUT CLASS="button_org" TYPE=submit VALUE="'._ADDQUIZ.'">';
	 echo '</FORM>';
	 */
	echo '<form id="form1" name="form1" method="post" action="">';

	echo'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">';

	echo '<IMG SRC="images/global/linkicon.gif" BORDER="0" ALT=""> <A HREF="index.php?mod=Courses&file=admin&op=quiz&cid='.$cid.'">'._TEST.'</A> &gt; <A HREF="index.php?mod=Courses&file=admin&op=quiz&action=edit_quiz&cid='.$cid.'&qid='.$qid.'">'.$quizinfo['name'].'</A>  &gt; <A HREF="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_question_form&amp;cid='.$cid.'&amp;qid='.$qid.'">'._QUESTION.'</A> &gt;  <B>'._CREATEQUESTION.'</B>';

	echo '<fieldset>
			  <legend>'._QUESTION.'</legend>';
	echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	if($numrow>0){
		for ($i=0; list($mcid,$question,$type,$score) = $result->fields; $i++) {
			$t=$i+1;
			$result->MoveNext();
			//echo "<br>".$mcid;
			echo $question;
			if($type!=0)
			 echo " ____<u> ("._ClozeTestQuiz." ".$t." "._QUESTIONSCORE." $score) </u>_____ ";
			 //echo " ____<u> (ข้อที่ ".$t.") </u>_____ ";
		}
		//$numrow--;
		echo "<hr>";
		$result = $dbconn->Execute($query);
		$numrow=$result->RecordCount();

		//check + - choice
		if (empty($nochoice)) { // default no of choice = 4
			$nochoice = _LNCHOICE;
		}
		if (empty($score)) {	// default score=1
			$score = _LNSCORE;
		}

		// increase item 0.5 before resequence
		if ($op == "insert_question_form") {
			$itemold = $item;
			$item += 0.5;
		}
		// click button delete choice
		if ($action == "delete_choice") {
			deleteChoice($vars);
		}
		//end check + - choice
		//+ - choice
		if ($subaction == "delete_item") {
			$nochoice--;
		}
		else if ($subaction =="add_item" && $op != "edit_question_form") {
			$nochoice++;
		}
		if ($nochoice < 1) $nochoice = 1;
		//end + - choice

		for ($i=0; list($mcid,$question,$type) = $result->fields; $i++) {
			$t=$i+1;
			//echo '<input name="mcid['.$t.']" type="text" id="mcid['.$t.']" value="'.$mcid.'" />';
			$result->MoveNext();
			if($type!=0){
				echo "<br>ข้อที่ ".$t;
				//for($j=1;$j<=4;$j++){
				for ($j=1,$n=1; $j<=$nochoice; $j++,$n++) {
					echo '<br>'.$n.' <input type="radio" name="a['.$t.']" id="a['.$t.']" value="'.$j.'" />&nbsp;<input name="answer['.$t.']['.$j.']" type="text" id="answer['.$t.']['.$j.']" size="30" />&nbsp;&nbsp;&nbsp;&nbsp;';
				}
				//+ -
				echo " <input class='button'  type=button value=' + '  Onclick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin&amp;op=$op&amp;action=add_choice_clozetest_form&amp;cid=$cid&amp;qid=$qid&amp;type=$type&amp;weight=$weight&amp;subaction=add_item&amp;cid=$cid&amp;qid=$qid&amp;item=$itemold&amp;nochoice=$nochoice','_self')\"> ";
				if ($n > 2) {
					echo "<input class='button' type=button value='  -  ' Onclick=\"javascript: window.open('index.php?mod=Courses&amp;file=admin&amp;op=$op&amp;action=add_choice_clozetest_form&amp;cid=$cid&amp;qid=$qid&amp;type=$type&amp;weight=$weight&amp;subaction=delete_item&amp;cid=$cid&amp;qid=$qid&amp;item=$itemold&amp;nochoice=$nochoice','_self')\">";
				}
				//end + -
			}
		}
		//add delete choice

	}
	echo '<br><br><center><input type="submit" name="button" id="button" value="  Finish  " /></center>';
	echo '</fieldset>
		  </form>';
	if($button!=""){
		$result = $dbconn->Execute($query);
		for ($i=0; list($mcid,$question,$type) = $result->fields; $i++) {
			$result->MoveNext();$t=$i+1;
			if($type!=0){
				for($j=1;$j<=$nochoice;$j++){
					$aa = $answer[$t][$j];
					//echo "<br>".$aa;
					//$answer=$aa*$aa;
					$max_chid= getMaxCHID();
					$query = "INSERT INTO $quiz_choicetable
					(	$quiz_choicecolumn[chid],
					$quiz_choicecolumn[mcid],
					$quiz_choicecolumn[answer],
					$quiz_choicecolumn[feedback],
					$quiz_choicecolumn[weight]
					)
					VALUES ('" . lnVarPrepForStore($max_chid) . "',
									  '" . lnVarPrepForStore($mcid) . "',
									  '" . lnVarPrepForStore($aa) . "',
									  '',
									  '".$j."')";	

					$dbconn->Execute($query);
					//echo "<br>$query";
				}
				$temp = pow(2,$a[$t]-1);
				$query = "UPDATE $quiz_multichoicetable SET $quiz_multichoicecolumn[answer] = '" . lnVarPrepForStore($temp) . "' WHERE $quiz_multichoicecolumn[mcid] =  '" .$mcid. "'";
				//echo "<br>$query";
				$dbconn->Execute($query);
				//echo "radio=".$a[$t];
				echo '<meta http-equiv="refresh" content="0;URL=index.php?mod=Courses&file=admin&op=quiz&amp;action=add_question_form&amp;cid='.$cid.'&amp;qid='.$qid.'" />';

			}
		}
	}
}

//MultiQuestion
function addMultiQuestionForm($vars){
	// Get arguments from argument array
	extract($vars);
	//print_r($vars);

	//add_question_multiquestion_form
	echo '<BR>&nbsp;<fieldset><legend>'._SELECTNUMQUESTION.'</legend><table cellpadding=3 cellspacing=0 border=0 width=100%>';
	echo '<form name=addquestion>';
	echo'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">';

	echo '<fieldset><table border=0 cellpadding=3 cellspacing=0 width=100%>';
	echo '<tr><td width=50%>'._NUMQUESTION.' : ';
	echo '<SELECT NAME="popup" onchange="self.location=document.addquestion.popup.options[document.addquestion.popup.selectedIndex].value">';
	echo '<OPTION>'._SELECTNUMQUESTION.'</OPTION>';
	for($i=1;$i<=100;$i++){
		echo '<OPTION value="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_question_multiquestion_form&amp;cid='.$cid.'&amp;qid='.$qid.'&amp;numQ='.$i.'">'.$i.'</OPTION>';
	}
	echo '</SELECT></td>';

	echo '</td></tr>';
	echo '</table></fieldset></FORM>';
}

function addQuestionMultiQuestionForm($vars){
	extract($vars);
	//print_r($vars);
	echo '<form name="questionform" method="post" action="">';
	echo'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">';
	echo '<IMG SRC="images/global/linkicon.gif" BORDER="0" ALT=""> <A HREF="index.php?mod=Courses&file=admin&op=quiz&cid='.$cid.'">'._TEST.'</A> &gt; <A HREF="index.php?mod=Courses&file=admin&op=quiz&action=edit_quiz&cid='.$cid.'&qid='.$qid.'">'.$quizinfo['name'].'</A>  &gt; <A HREF="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_question_form&amp;cid='.$cid.'&amp;qid='.$qid.'">'._QUESTION.'</A> &gt;  <B>'._CREATEQUESTION.'</B>';

	echo '<fieldset><legend>'._QUESTION.'</legend><br>';
	echo '<table width="80%" border="0" cellspacing="1" cellpadding="2" align="center">';

	echo '<tr><td>';
	echo '<TEXTAREA NAME="question" ROWS="6" COLS="35" wrap="soft" style="width: 90%;">'.$question.'</TEXTAREA>';
	echo "<INPUT  TYPE=button VALUE=' ... ' onclick=\"javascript:popup('index.php?mod=spaw&amp;type=Question&amp;cid=$cid&amp;mcid=$mcid','_blank',750,480)\">";
	echo '</td></tr>';
	echo '<tr><td colspan="2"><input type="button" name="button2" id="button2" value="PREVIOUS" />
          <input type="submit" name="button" id="button" value="    NEXT    " /></td>
          </tr></table>
			  <br />
			</fieldset>
		  </form>';

	if($button!=""){
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
		//echo $question;
		$quiz_multichoicetable = $lntable['quiz_multichoice'];
		$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
		//type = 2
		//for($i=1;$i<=$numQ;$i++){
		//if($question[$i]!=""){
		if (empty($mcid)) {
			$max_mcid= getMaxMCID();
			if (empty($weight)) {
				$weight = getNextMCWeight($qid);
			}
			$type=3;
			$score=0;
			$answer=0;
			$query = "INSERT INTO " . $quiz_multichoicetable . " (" .
			$quiz_multichoicecolumn['mcid'] ."," .
			$quiz_multichoicecolumn['qid'] .",".
			$quiz_multichoicecolumn['question'] .",".
			$quiz_multichoicecolumn['answer'] .",".
			$quiz_multichoicecolumn['score'].",".
			$quiz_multichoicecolumn['weight'].",".
			$quiz_multichoicecolumn['type']."
								  )
								VALUES ('" . lnVarPrepForStore($max_mcid) . "',
									  '" . lnVarPrepForStore($qid) . "',
									  '" . $question . "',
									  '" . lnVarPrepForStore($answer) . "',
									  '" . lnVarPrepForStore($score) . "',
									  '" . lnVarPrepForStore($weight) . "',
									  '" . lnVarPrepForStore($type) . "')";	
			//echo "<br>$query";
			$dbconn->Execute($query);
		}
		//}
		//}

		echo '<meta http-equiv="refresh" content="0;URL=index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_choice_multiquestion_form&amp;cid='.$cid.'&amp;qid='.$qid.'&amp;type='.$type.'&amp;weight='.$weight.'&amp;numQ='.$numQ.'" />';
	}
}

function addChoiceMultiQuestionForm($vars){
	// Get arguments from argument array
	extract($vars);
	//print_r($vars);
	$quizinfo =  lnQuizGetVars($qid);

	echo '<IMG SRC="images/global/linkicon.gif" BORDER="0" ALT=""> <A HREF="index.php?mod=Courses&file=admin&op=quiz&cid='.$cid.'">'._TEST.'</A> &gt; <A HREF="index.php?mod=Courses&file=admin&op=quiz&action=edit_quiz&cid='.$cid.'&qid='.$qid.'">'.$quizinfo['name'].'</A>  &gt; <A HREF="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_question_form&amp;cid='.$cid.'&amp;qid='.$qid.'">'._QUESTION.'</A> &gt;  <B>'._CREATEQUESTION.'</B>';
	if(empty($noquestion)){
		$noquestion = 1;
	}

	if (empty($nochoice)) { // default no of choice = 4
		$nochoice = _LNCHOICE;
	}
	if (empty($score)) {	// default score=1
		$score = _LNSCORE;
	}

	// increase item 0.5 before resequence
	if ($op == "insert_question_form") {
		$itemold = $item;
		$item += 0.5;
	}
	// click button delete choice
	if ($action == "delete_choice") {
		deleteChoice($vars);
	}

	// edit question
	if (!empty($mcid)) {
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
			
		$quiz_multichoicetable = $lntable['quiz_multichoice'];
		$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
		$quiz_choicetable = $lntable['quiz_choice'];
		$quiz_choicecolumn = &$lntable['quiz_choice_column'];
			
		$query = "SELECT $quiz_multichoicecolumn[qid],
		$quiz_multichoicecolumn[question],
		$quiz_multichoicecolumn[answer],
		$quiz_multichoicecolumn[score],
		$quiz_multichoicecolumn[weight],
		$quiz_choicecolumn[chid],
		$quiz_choicecolumn[mcid],
		$quiz_choicecolumn[answer],
		$quiz_choicecolumn[feedback],
		$quiz_choicecolumn[weight]
		FROM  $quiz_multichoicetable LEFT JOIN $quiz_choicetable ON $quiz_multichoicecolumn[mcid] = $quiz_choicecolumn[mcid]
		WHERE 	$quiz_multichoicecolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'
		ORDER BY $quiz_choicecolumn[weight]";

		$result = $dbconn->Execute($query);
		list($qid,$question,$answer,$score,$weight,$_,$_,$_,$_,$_) = $result->fields;

		$chid =  array();
		$choice = array();
		$desc = array();
		for ($i=0,$nochoice=0; list($_,$_,$_,$_,$_,$cd,$cm,$ch,$dc,$cw) = $result->fields; $i++) {
			$result->MoveNext();
			$chid[$i]=$cd;
			$choice[$i] = $ch;
			$desc[$i] = $dc;
			$nochoice++;
		}
	}

	// show input form
	?>
<SCRIPT LANGUAGE="JavaScript">
		<!--
			function checkFields() {
				var select_choice=0;
				var select_ans=0;
				for (i = 0; i < document.forms.questionform.length; i++) {
					name="";
					value="";
					name=document.forms.questionform.elements[i].name;
					value=document.forms.questionform.elements[i].value;
					if (document.forms.questionform.elements[i].type == 'textarea') {
						if (name == "question" && value == "" ) {
							return 'question ?';
						}
						if (name.substring(0,6) == "choice" && value != "" && select_choice==0) {
								select_choice=1;
						}					
					}
					if (document.forms.questionform.elements[i].type == 'checkbox') {
						value=document.forms.questionform.elements[i].checked;
						if (value == true && select_ans == 0) {
							select_ans = 1;
						}
					}
				}
				if (select_choice == 0) {
					return "choice ?";
				}

				if (select_ans == 0) {
					return "choose any answer(s)";
				}
				return false;
			}
		//-->
		</SCRIPT>
	<?

	// Get MultiQuestion
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	$query = "SELECT $quiz_multichoicecolumn[mcid],
	$quiz_multichoicecolumn[question],
	$quiz_multichoicecolumn[type],
	$quiz_multichoicecolumn[weight]
	FROM $quiz_multichoicetable
	WHERE $quiz_multichoicecolumn[qid]= '". lnVarPrepForStore($qid)."' and
	$quiz_multichoicecolumn[type] = '3' and $quiz_multichoicecolumn[weight] = '".$weight."'
	and $quiz_multichoicecolumn[answer] = '0'
	ORDER BY $quiz_multichoicecolumn[mcid]";

	$result = $dbconn->Execute($query);
	$numrow=$result->RecordCount();

	//show multiquestion
	for ($i=0; list($mcid,$questionstroy,$type,$weight) = $result->fields; $i++) {
		$result->MoveNext();
		echo '<fieldset><legend>'.'เนื้อหา'.'</legend>';
		echo "<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
		echo $questionstroy;
		$oldweight = $weight;
		echo '</fieldset>';
		break;
	}


	echo '<BR>&nbsp;<fieldset><legend>'._QUESTION.'ข้อที่ '.$noquestion.'</legend><table cellpadding=3 cellspacing=0 border=0 width=100%>';
	echo "<FORM NAME=\"questionform\" METHOD=POST ACTION=\"index.php\" onSubmit=\"if (empty = checkFields()) {alert(empty); return false;} else { return true;} \">";
	echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="quiz">'
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">'
	.'<INPUT TYPE="hidden" NAME="type" VALUE="'.$type.'">'
	.'<INPUT TYPE="hidden" NAME="numQ" VALUE="'.$numQ.'">'
	.'<INPUT TYPE="hidden" NAME="weight" VALUE="'.$oldweight.'">'
	.'<INPUT TYPE="hidden" NAME="noquestion" VALUE="'.$noquestion.'">';

	echo '<INPUT TYPE="hidden" NAME="action" VALUE="add_choice_multiquestion">';
	//echo '<INPUT TYPE="hidden" NAME="mcid" VALUE="'.$mcid.'">';

	echo '<tr><td width=10% valign=top>&nbsp;&nbsp;'._QUESTION.' :</td>';
	echo '<td>';
	echo '<TEXTAREA NAME="question" ROWS="4" COLS="35" wrap="soft" style="width: 90%;">'.lnVarPrepForDisplay($question).'</TEXTAREA>';
	echo "<INPUT  TYPE=button VALUE=' ... ' onclick=\"javascript:popup('index.php?mod=spaw&amp;type=Question&amp;cid=$cid&amp;mcid=$mcid','_blank',750,480)\">";
	echo '</td></tr>';


	echo '<tr><td  valign=top>&nbsp;&nbsp;'._ANSWER.' :</td><td>';
	echo '<table cellpadding=1 cellspacing=0 border=0 width="100%">';

	// form head
	echo '<tr align=center><td>'._CORRECTANS.'</td><td>'._CHOICE.'</td><td align="left">&nbsp;&nbsp;&nbsp;&nbsp;'._DESCRIPTION.'</td></tr>';

	if ($subaction == "delete_item") {
		$nochoice--;
	}
	else if ($subaction =="add_item" && $op != "edit_question_form") {
		$nochoice++;
	}
	if ($nochoice < 1) $nochoice = 1;

	for ($i=0,$n=1; $i<$nochoice; $i++,$n++) {
			
		echo '<tr><td valign=bottom align="center">';
		$ans = pow(2,$i);
		if ($answer & pow(2,$i)) {  // edit choice
			echo '<INPUT checked TYPE="checkbox" NAME="ans['.$i.']" VALUE="'.$ans.'">';
		}
		else {
			echo '<INPUT TYPE="checkbox" NAME="ans['.$i.']" VALUE="'.$ans.'">';
		}
		echo '</td>';
		echo '<td valign="top" width=300>'.$n.'. <TEXTAREA NAME="choice['.$i.']"  style="width: 250;" ROWS="2" COLS="40" wrap="soft">'.lnVarPrepForDisplay($choice[$i]).'</TEXTAREA>';

		echo "<INPUT  class=button TYPE=button VALUE=' ... ' onclick=\"javascript:popup('index.php?mod=spaw&amp;type=Choice&amp;cid=$cid&amp;chid=".$chid[$i]."&amp;n=".$i."','_blank',750,480)\">";
		echo '</td><td valign="top" align="left"><TEXTAREA NAME="desc['.$i.']" style="width: 120;" ROWS="2" COLS="18" wrap="soft">'.lnVarPrepForDisplay($desc[$i]).'</TEXTAREA>';

		echo '<INPUT class="inpurt" TYPE="hidden" NAME="chid['.$i.']" VALUE="'.$chid[$i].'">';

		if (empty($mcid)) {
			if ($i == $nochoice -1) { // show at last of choice
				echo " <input class='button'  type=button value=' + '  Onclick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin&amp;op=$op&amp;action=add_multichoice_form&amp;subaction=add_item&amp;cid=$cid&amp;qid=$qid&amp;item=$itemold&amp;nochoice=$nochoice','_self')\"> ";
				if ($n != 1) {
					echo "<input class='button' type=button value='  -  ' Onclick=\"javascript: window.open('index.php?mod=Courses&amp;file=admin&amp;op=$op&amp;action=add_multichoice_form&amp;subaction=delete_item&amp;cid=$cid&amp;qid=$qid&amp;item=$itemold&amp;nochoice=$nochoice','_self')\">";
				}
			}
		}

		echo '</td></tr>';

	}

	echo '</table>';
	echo '</td></tr>';

	// score
	echo '<tr><td width=10%>&nbsp;&nbsp;'._QUESTIONSCORE.' :</td>';
	echo '<td><INPUT class="input" TYPE="text" NAME="score" SIZE="2" VALUE="'.$score.'"></td></tr>';
	echo '<tr><td>&nbsp;</td><td><BR>';

	// submit buttons
	if (empty($quid)) {
		echo '<INPUT CLASS="button_org" TYPE="submit" VALUE="'._ADDQUESTION.'">';
	}
	else {
		echo '<INPUT CLASS="button_org" TYPE="submit" VALUE="'._UPDATEQUIZ.'">';
	}

	echo " <INPUT CLASS='button_org' TYPE=button VALUE="._CANCEL." OnClick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&action=add_question_form&amp;qid=$qid&amp;cid=$cid','_self')\"><BR><BR></td></tr>";
	echo '</FORM>';

	echo '</table></fieldset>';

}


function addChoiceMultiQuestion($vars){
	// Get arguments from argument array
	extract($vars);

	$oldweight = $weight;
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	// calculate answer
	$answer = 0;
	foreach ($ans as $val) {
		if (!empty($val)) {
			$answer += $val;
		}
	}

	// add question
	$question=stripslashes($question);
	if (empty($mcid)) {
		$max_mcid= getMaxMCID();
		if (empty($weight)) {
			$weight = getNextMCWeight($qid);
		}
		$query = "INSERT INTO $quiz_multichoicetable
		(	$quiz_multichoicecolumn[mcid],
		$quiz_multichoicecolumn[qid],
		$quiz_multichoicecolumn[question],
		$quiz_multichoicecolumn[answer],
		$quiz_multichoicecolumn[score],
		$quiz_multichoicecolumn[weight],
		$quiz_multichoicecolumn[type]
		)
		VALUES ('" . lnVarPrepForStore($max_mcid) . "',
						  '" . lnVarPrepForStore($qid) . "',
						  '" . lnVarPrepForStore($question) . "',
						  '" . lnVarPrepForStore($answer) . "',
						  '" . lnVarPrepForStore($score) . "',
						  '" . lnVarPrepForStore($weight) . "',
						  '" . lnVarPrepForStore($type) . "')";	
			
		$dbconn->Execute($query);

		for ($i=0,$n=0; $i < count($choice); $i++) {
			//	if (!empty($choice[$i])) {
			$max_chid= getMaxCHID();
			$choice[$i]=stripslashes($choice[$i]);
			$desc[$i]=addslashes($desc[$i]);
			$n++;
			$query = "INSERT INTO $quiz_choicetable
			(	$quiz_choicecolumn[chid],
			$quiz_choicecolumn[mcid],
			$quiz_choicecolumn[answer],
			$quiz_choicecolumn[feedback],
			$quiz_choicecolumn[weight]
			)
			VALUES ('" . lnVarPrepForStore($max_chid) . "',
								  '" . lnVarPrepForStore($max_mcid) . "',
								  '" . lnVarPrepForStore($choice[$i]) . "',
								  '" . lnVarPrepForStore($desc[$i]) . "',
								  '" . lnVarPrepForStore($n) . "')";	

			$dbconn->Execute($query);

			//	}
		}
	}

	// update question
	else {


		$query = "UPDATE $quiz_multichoicetable SET
		$quiz_multichoicecolumn[question] =  '" . lnVarPrepForStore($question) . "',
		$quiz_multichoicecolumn[answer] = '" . lnVarPrepForStore($answer) . "',
		$quiz_multichoicecolumn[score] =	'" . lnVarPrepForStore($score) . "',
		$quiz_multichoicecolumn[weight] = '" . lnVarPrepForStore($weight) . "'
		WHERE 	$quiz_multichoicecolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'";

		$dbconn->Execute($query);

		for ($i=0,$n=0; $i < count($choice); $i++) {
			if (!empty($choice[$i])) {
				$max_chid= getMaxCHID();
				$choice[$i]=stripslashes($choice[$i]);
				$desc[$i]=addslashes($desc[$i]);
				$n++;
				$query = "UPDATE $quiz_choicetable SET
				$quiz_choicecolumn[answer] =  '" . lnVarPrepForStore($choice[$i]) . "',
				$quiz_choicecolumn[feedback] =  '" . lnVarPrepForStore($desc[$i]) . "',
				$quiz_choicecolumn[weight] = '" . lnVarPrepForStore($n) . "'
				WHERE 	$quiz_choicecolumn[chid] =  '" . lnVarPrepForStore($chid[$i]) . "'";
				$dbconn->Execute($query);
			}
		}

	}

	$numQ--;
	$noquestion++;

	if($numQ>0){
		echo '<meta http-equiv="refresh" content="0;URL=index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=add_choice_multiquestion_form&amp;cid='.$cid.'&amp;qid='.$qid.'&amp;type='.$type.'&amp;weight='.$oldweight.'&amp;numQ='.$numQ.'&amp;noquestion='.$noquestion.'"/>';
	}else{
		echo '<meta http-equiv="refresh" content="0;URL=index.php?mod=Courses&file=admin&op=quiz&amp;action=add_question_form&amp;cid='.$cid.'&amp;qid='.$qid.'" />';
	}
	//resequenceQuestions($qid);
}

?>