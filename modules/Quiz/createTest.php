<?php


if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}





/* options */
	if(!isset($func)) $func='';
	switch($func) {
		case "add_quiz_form" : 
		case "edit_quiz" : editQuizForm($vars); return;
		case "add_quiz" : addQuiz($vars);
		case "search_question":	searchQuestionForm($vars); return; 
		case "add_question": addQuestion($vars);		
		case "search": searchQuestion($vars); return;
		case "manage_question": manageQuestion($vars);return;	
		case "delete_quiz" : deleteQuiz($vars); break;
		case "delete_question" : deleteQuestion($vars); manageQuestion($vars); return;
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



//********show test
list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];

	$query = "SELECT $quizcolumn[qid],$quizcolumn[name]
	FROM $quiztable
	WHERE $quizcolumn[cid]='". lnVarPrepForStore($cid) ."'";

	$result = $dbconn->Execute($query);
	
	
	if($func=="" || $func=="delete_quiz")
	{
	addQuestionButton($vars);
	}
	
	
	echo '<table width="100%" cellspacing="0" cellpading=3>';
	for($count=0; list($qid,$quiz_name) = $result->fields;) {
		$result->MoveNext();
		if (!empty($qid)) {
			echo '<tr><td height="20">';
			echo "<B><A HREF=\"index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=edit_quiz&amp;qid=$qid&amp;cid=$cid\">";
			echo '<IMG SRC="images/global/line.gif"  BORDER="0" ALT="" align="absmiddle"> '.$quiz_name;
			$viewlink= "<A HREF=index.php?mod=Courses&amp;op=lesson_show&amp;cid=$cid&amp;lid=$lid&amp;qid=$qid><IMG SRC=images/global/view.gif  BORDER=0 ALT="._VIEW._TEST."></A>  &nbsp;";
			$count++;
			echo '</A></B></td>';
			echo '<td align="right" width=150>';

			echo "<A HREF=\"index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=edit_quiz&amp;qid=$qid&amp;cid=$cid\"><IMG SRC=images/global/view1.gif  BORDER=0 ALT="._EDIT."></A>  &nbsp;";

			//echo $viewlink;

			echo "<A HREF=\"javascript: if(confirm('Delete Quiz ?')) window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=delete_quiz&amp;qid=$qid&amp;cid=$cid','_self')\"><IMG SRC=images/global/delete.gif  BORDER=0 ALT="._DELETE."></A>   &nbsp;";


			echo '<A HREF="index.php?mod=Quiz&amp;file=exportTest&amp;qid='.$qid.'"><IMG SRC=images/global/export.gif  BORDER=0 ALT='._EXPORTTEST.'></A>   &nbsp;';


			echo '</td></tr>';
			echo '<tr><td colspan="2" height="1" background="images/line.gif"></td></tr>';
		}
	}
	echo '</table>';

	// no quiz
	if ($count == 0 && $action != "add_quiz_form") {
		echo '<center><br><br>'._NOQUIZ.'<br><br></center>';
	}

//**************end show test





function addQuestionButton($vars) {
	// Get arguments from argument array
	extract($vars);

echo '<table width= 100% cellpadding=0 cellspacing=0 border=0>';
echo '<tr><td>&nbsp;</td><td align=right><A HREF="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=add_quiz_form&amp;cid='.$cid.'"><IMG SRC="modules/Courses/images/create.gif"  BORDER=0 ALT=""></A>&nbsp;&nbsp;&nbsp;</td></tr>';
echo '</table>';

}


/**
 *	add or edit quiz form
 */
function editQuizForm($vars) {
	// Get arguments from argument array
	extract($vars);

	if ($func == "edit_quiz") {
		$quizinfo = lnQuizGetVars($qid);
		$csq[$quizinfo['shufflequestions']]='checked';
		$cca[$quizinfo['correctanswers']]='checked';
		$cfb[$quizinfo['feedback']]='checked';
		$cgr[$quizinfo['grademethod']]='checked';
		$sgr[$quizinfo['grade']]='selected';
		$cdf[$quizinfo['difficultypriority']]='checked';
	}
	else {
		$csq[0]='checked';
		$cca[1]='checked';
		$cfb[1]='checked';
		$cgr[1]='checked';
		$cdf[1]='checked';
		$quizinfo['testtime']='0';
		$quizinfo['attempts']='0';
		$quizinfo['assessment']='60';
		$quizinfo['correctscore']='1';
		$quizinfo['wrongscore']='0';
		$quizinfo['noans']='0';
	}


	if (empty($quiz_desc)) $quiz_desc = _DEFAULTQUIZTITLE;

	//echo '<IMG SRC="images/global/linkicon.gif" BORDER="0" ALT=""> <A HREF="index.php?mod=Courses&file=admin&op=quiz&cid='.$cid.'">'._TESTBANK.'</A> &gt; <B>'.$quizinfo['name'].'</B>';

	echo '<center><BR><fieldset><legend>'._CREATETEST.'</legend>';
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
	.'<INPUT TYPE="hidden" NAME="action" VALUE="createtest">';

	if ($func == 'edit_quiz' || $func == 'manage_question' ) {
		echo '<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">';
		echo '<INPUT TYPE="hidden" NAME="edit" VALUE="1">';
		$funcs = "manage_question";
	}
	else
	{
		//echo '<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">';
		$funcs = "add_quiz";
	}

		echo '<INPUT TYPE="hidden" NAME="func" VALUE="'.$funcs.'">';
	
	
	

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
	
	echo '<tr><td>&nbsp;&nbsp;'._SHOWANSWERFEEDBACK.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="radio" NAME="feedback" VALUE="1" '.$cfb[1].'> '._SYES.' &nbsp;&nbsp;<INPUT TYPE="radio" NAME="feedback" VALUE="0" '.$cfb[0].'> '._SNO.'';
	echo '</td></tr>';

	//echo '<tr><td>&nbsp;&nbsp;'._SHOWANSWER.' : </td>';
	//echo '<td>';
	//echo '<INPUT TYPE="radio" NAME="correctanswers" VALUE="1" '.$cca[1].'> '._SYES.'  &nbsp;&nbsp;<INPUT TYPE="radio" NAME="correctanswers" VALUE="0" '.$cca[0].'>  '._SNO.' ';
	//echo '</td></tr>';
	
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

	echo '<tr><td>&nbsp;&nbsp;'._DIFFICULTY.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="radio" NAME="difficultypriority" VALUE="1" '.$cdf[1].'> '._SYES.'  &nbsp;&nbsp;<INPUT TYPE="radio" NAME="difficultypriority" VALUE="0" '.$cdf[0].'>  '._SNO.' ';
	echo '</td></tr>';

/*
	echo '<tr><td>&nbsp;&nbsp;'._SHOWFEEDBACK.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="radio" NAME="feedback" VALUE="1" '.$cfb[1].'> '._SYES.' &nbsp;&nbsp;<INPUT TYPE="radio" NAME="feedback" VALUE="0" '.$cfb[0].'> '._SNO.'';
	echo '</td></tr>';

	echo '<tr><td>&nbsp;&nbsp;'._SHOWANSWER.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="radio" NAME="correctanswers" VALUE="1" '.$cca[1].'> '._SYES.'  &nbsp;&nbsp;<INPUT TYPE="radio" NAME="correctanswers" VALUE="0" '.$cca[0].'>  '._SNO.' ';
	echo '</td></tr>';
*/
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

	echo '<tr><td>&nbsp;&nbsp;'._GRADE.' : </td>';
	echo '<td>';
	echo '<INPUT TYPE="text" NAME="grade" SIZE="2" VALUE="'.$quizinfo['grade'].'"> คะแนน';
	echo '</td></tr>';

	echo '<tr><td>&nbsp;</td><td>';
	echo '<BR>&nbsp;<INPUT CLASS="button" TYPE="submit" VALUE="'._CONTINUE.'">';
	echo " <INPUT CLASS=\"button\" TYPE=button VALUE="._CANCEL." OnClick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;cid=$cid&amp;qid=$qid','_self')\"><BR>&nbsp;</td></tr>";
	echo '</FORM>';
	echo '</table>';
	echo '</fieldset></center>';

}


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

	if($dbconn->ErrorNo() != 0) {
		return;
	}	

if (empty($qid)) {
		if (empty($testtime)) $testtime = 0;

		$max_qid = getMaxQID();

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
		$quizcolumn[noans],
		$quizcolumn[difficulty],
		$quizcolumn[difficultypriority]
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
			'" . lnVarPrepForStore($noans) . "',
			'" . lnVarPrepForStore($difficulty) . "',
			'" . lnVarPrepForStore($difficultypriority) . "')";	
		
		$result = $dbconn->Execute($query);
	 if (!$result) die ("result error");
	
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
		$quizcolumn[noans] =   '" . lnVarPrepForStore($noans) . "',
		$quizcolumn[difficulty] =   '" . lnVarPrepForStore($difficulty) . "',
		$quizcolumn[difficultypriority] =   '" . lnVarPrepForStore($difficultypriority) . "'
		WHERE $quizcolumn[qid] = '" . lnVarPrepForStore($qid) . "'";
			
		$dbconn->Execute($query);
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
function searchQuestionForm($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$schoolstable = $lntable['schools'];
	$schoolscolumn = &$lntable['schools_column'];
	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];

	
	if($qid==null)
	{
	$qid=getMaxQID();
	$qid=$qid-1;
	}


echo '<FORM NAME="Course" METHOD=POST ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="quiz">'
	.'<INPUT TYPE="hidden" NAME="action" VALUE="createtest">'
	.'<INPUT TYPE="hidden" NAME="func" VALUE="search">'
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">'	
	.'<INPUT TYPE="hidden" NAME="question_author" VALUE="'.lnSessionGetVar('uid').'">';
	
	
//Field Set begin
	echo '<center><BR><fieldset><legend><b>'._SEARCHQUESTION.'</B></legend>';
	echo '<table cellpadding=2 cellspacing=0 border=0 width=100%>';

/* Keyword field******************/
	echo '<TR><TD WIDTH=100>'._KEYWORDSEARCH.'</TD><TD><INPUT TYPE="text" NAME="keyword" SIZE="20" VALUE="" style="width:50%"></TD></TR>';

/* School field*****************
	echo ''
	.'<TR><TD WIDTH=100>'._SCHOOL.'</TD><TD>'
	.'<SELECT class="select" NAME="scode" onchange="document.forms.Course.course_code.value=document.forms.Course.scode.options[this.selectedIndex].value;">';
	echo '<OPTION VALUE="allschool">'._ALLSCHOOL.'</OPTION>';
	list($_,$sscode,$_) = $result->fields;
	while(list($sid,$scode,$name) = $result->fields) {
		$name=stripslashes($name);
		$result->MoveNext();
		echo '<OPTION VALUE="'.$scode.'">'.$name.'</OPTION>';
	}
	echo '</SELECT></TD></TR>';
*/
/* Course field*****************
	echo ''
	.'<TR><TD WIDTH=100>'._COURSENAME.'</TD><TD>'
	.'<SELECT class="select" NAME="ctitle" onchange="document.forms.Course.course_code.value=document.forms.Course.ctitle.options[this.selectedIndex].value;">';
	list($cid,$code,$title,$author) = $result2->fields;
	while(list($cid,$code,$title,$author) = $result2->fields) {
		//$name=stripslashes($name);
		$result2->MoveNext();
		echo '<OPTION VALUE="'.$ctitle.'">'.$code.' '.$title.'</OPTION>';
	}
	echo '</SELECT></TD></TR>';
*/


/* Permission field*****************/
echo '<TR><TD WIDTH=100>'._PRIORITY.'</TD>';
echo '<TD>';
echo '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
echo '<tr>';
echo '<TD><INPUT  TYPE="radio" NAME="permission" VALUE="1" checked>'._ALLPRIORITY.'</TD>';
echo '<TD><INPUT  TYPE="radio" NAME="permission" VALUE="2">'._OWNER.'</TD>';
echo '<TD><INPUT  TYPE="radio" NAME="permission" VALUE="3">'._PUBLIC.'</TD>';
echo '</tr>';
echo '</table>';
echo '</TD></TR>';


echo '<TR><TD WIDTH=100 VALIGN="TOP">&nbsp;<TD><BR><INPUT class="button" TYPE="submit"  VALUE="'. _SEARCH. '"> ';

	echo '</FORM></table>';
	echo '</fieldset></center>';

}




function searchQuestion($vars) {

searchQuestionForm($vars);

extract($vars);

		 if ($keyword == '')
		 {
		 	$keyword='%';
		 }
		 
		 	$keyword_all = $keyword;
	
    		list($dbconn) = lnDBGetConn();
    		$lntable = lnDBGetTables();
			//$quiz_keywordstable = $lntable['quiz_keywords'];
    		//$quiz_keywordscolumn = &$lntable['quiz_keywords_column'];
			$lessonstable = $lntable['lessons'];
			$lessonscolumn = &$lntable['lessons_column'];			
			$coursestable = $lntable['courses'];
			$coursescolumn = &$lntable['courses_column'];
			$schoolstable = $lntable['schools'];
			$schoolscolumn = &$lntable['schools_column'];
			$quiztable = $lntable['quiz'];
			$quizcolumn = &$lntable['quiz_column'];
			$quiz_multichoicetable = $lntable['quiz_multichoice'];
			$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
			$quiz_testtable = $lntable['quiz_test'];
			$quiz_testcolumn = &$lntable['quiz_test_column'];

			$keyword_for_repeat = $keyword;

			//segment keyword
			$keyword = explode(" ", $keyword);		
			$i=0;
			while($keyword[$i]!=null)
			{
				if($i==0)
				{
					$keyword_query = " $quiz_multichoicecolumn[keyword] like '%$keyword[$i]%'";	
				}
				else
				{
					$keyword_query .= " or ";
					$keyword_query .= " $quiz_multichoicecolumn[keyword] like '%$keyword[$i]%'";	
					
				}

				//echo $keyword[$i];
				$i++;
			}				
			

			if($question_author=='')
			{
				$question_author = lnSessionGetVar('uid');
			}


			//echo $permission;
			if($permission==1)
			{
				$permission1=" and ($quiz_multichoicecolumn[share]=1 or $quiz_multichoicecolumn[uid]=".$question_author.")";
			}
			else if($permission==2)
				{
					$permission2="and $quiz_multichoicecolumn[uid]=".$question_author."";
				}else if($permission==3)
						{
						 	$permission3=" and $quiz_multichoicecolumn[share]=1 and $quiz_multichoicecolumn[uid]<>".$question_author."";
						}
/*
  				$sql = "SELECT $quiz_multichoicecolumn[mcid],$quiz_multichoicecolumn[type],$quiz_multichoicecolumn[question],$quiz_multichoicecolumn[difficulty] FROM $quiz_multichoicetable WHERE $quiz_multichoicecolumn[keyword] like '%$keyword%'";
*/


  				$sql = "SELECT $quiz_multichoicecolumn[mcid],$quiz_multichoicecolumn[type],$quiz_multichoicecolumn[question],$quiz_multichoicecolumn[difficulty] FROM $quiz_multichoicetable WHERE ";
			$sql .= $keyword_query;
			$sql .= $permission1;	
			$sql .= $permission2;	
			$sql .= $permission3;					
			$sql .= " ORDER BY $quiz_multichoicecolumn[mcid]";
			
		
/*							
			$sql = "SELECT $quiz_multichoicecolumn[mcid],$quiz_multichoicecolumn[type],$quiz_multichoicecolumn[question],$quiz_multichoicecolumn[difficulty] FROM $quiz_multichoicetable WHERE $quiz_multichoicecolumn[keyword] like '%$keyword%' and $quiz_multichoicecolumn[uid]=".$question_author."";	
*/
/*	
			$sql = "SELECT $quiz_multichoicecolumn[mcid],$quiz_multichoicecolumn[type],$quiz_multichoicecolumn[question],$quiz_multichoicecolumn[difficulty] FROM (( $quiz_multichoicetable JOIN $quiztable ON $quizcolumn[qid]=$quiz_multichoicecolumn[qid]) JOIN $coursestable ON $coursescolumn[cid]=$quizcolumn[cid]) WHERE $coursescolumn[author]=".$question_author." and $quiz_multichoicecolumn[keyword] like '%$keyword%'";
			*/
			

			
   		 	$result_record = $dbconn->Execute($sql);
			if ($dbconn->ErrorNo() != 0) {
       			echo $sql;
				echo "Query error";
				//echo $permission;
    		}

			$num_result = $result_record->RecordCount();	


?>

 <script language="javascript" type="text/javascript">
//<![CDATA[
<!-- Beginning of JavaScript -

function checkUncheck()
{
        if ( document.quizform.checkEm.checked == 1)
        {
                var form = document.quizform;
                for ( var ix = 0; ix < form.elements.length; ++ix )
                {
                        var fld = form.elements[ix];
                        if ( fld.name.substring(0,20) == "add_question_to_test" ) fld.checked = true;
                }
        }
        if ( document.quizform.checkEm.checked == 0)
        {
                var form = document.quizform;
                for ( var ix = 0; ix < form.elements.length; ++ix )
                {
                        var fld = form.elements[ix];
                        if ( fld.name.substring(0,20) == "add_question_to_test" ) fld.checked = false;
                }
        }
}

// - End of JavaScript - -->
//]]>
</script>




<?
if($num_result!=0)
{

			echo '<FORM NAME="quizform" METHOD=POST ACTION="index.php">'
			.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
			.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
			.'<INPUT TYPE="hidden" NAME="op" VALUE="quiz">'
			.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
			.'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">'
			.'<INPUT TYPE="hidden" NAME="keyword" VALUE="'.$keyword_for_repeat.'">'			
			.'<INPUT TYPE="hidden" NAME="permission" VALUE="'.$permission.'">'		
			.'<INPUT TYPE="hidden" NAME="action" VALUE="createtest">'
			.'<INPUT TYPE="hidden" NAME="func" VALUE="add_question">';

			echo '<table width="100%" border="1" cellspacing="0" cellpadding="3" bordercolor="#DDDDDD">'
				  .'<tr>'
				  .'<td width="5%" bgcolor="#CCCCCC"><B>No.</B></td>'
				  .'<td width="15%" align="center" bgcolor="#CCCCCC"><B>'._QUESTIONTYPE.'</B></td>'
				  .'<td width="55%" align="center" bgcolor="#CCCCCC"><B>'._QUESTIONITEM.'</B></td>'
				  .'<td width="15%" align="center" bgcolor="#CCCCCC"><B>'._DIFFICULTYLEVEL.'</B></td>'
				  .'<td width="10%" align="center" bgcolor="#CCCCCC"><B>เลือกคำถาม</B><br>'
				  .'<input type="checkbox" name="checkEm" value="TRUE" onclick="checkUncheck()">'
				  //.'<input CLASS="button" type="button" name="myBtn" value="Select All" onclick="handler();">'
				  .'</td>'
				  .'</tr>';
			
			$j=1;
			$k=0;
			$n=0;
			for ($i=1; (list($mcid,$type,$question,$difficulty) = $result_record->fields); $i++) {
			$result_record->MoveNext();	
			//echo $mcid;
				
				//******echo type in text 
				if($type==1){$type_text=_QCHOICE;}
					else if($type==2){$type_text=_QFILL;}
						else if($type==3){$type_text=_QMULTIQ;}
			

if($type==1)
{
			$k++;
			
			$question=stripslashes($question);
			$question=nl2br($question);
			$question=str_replace('\\"','"',$question);
			
			echo '<tr>'
				 .'<td align="center" bordercolor="#EEEEEE">'.$k.'</td>'
				 .'<td align="center" bordercolor="#EEEEEE">'.$type_text.'</td>'
				 .'<td bordercolor="#EEEEEE">'.$question.'</td>'
				 .'<td align="center" bordercolor="#EEEEEE">'.$difficulty.'</td>'		
				 .'<td align="center" bordercolor="#EEEEEE">';
			

		//******used or unuse question**********
		$query_repeat = "SELECT $quiz_testcolumn[qid] FROM $quiz_testtable WHERE $quiz_testcolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "' and $quiz_testcolumn[qid] =  '" . lnVarPrepForStore($qid) . "'";
		$result_repeat = $dbconn->Execute($query_repeat);
		$numrows_repeat = $result_repeat->RecordCount();
		if ($dbconn->ErrorNo() != 0) {
				echo "Query error";
    		}	
			//echo $numrows_repeat;

			list($qid_repeat) = $result_repeat->fields;
			if($qid	== $qid_repeat)
			{
				echo '<IMG SRC="images/global/accept.png" BORDER=0 ALT='._ALREADYUSE.'>';
			}
			else
			{
				echo '<INPUT  TYPE="checkbox" NAME="add_question_to_test['.$j.']" VALUE="'.$mcid.'">';
				echo '<INPUT TYPE="hidden" NAME="j" VALUE="'.$j.'">';
				$j++;
			}
		//******end used or unuse question**********
			
			echo '</td>'
 				 .'</tr>';

}//end if type==1



if($type==2)
{
		$n++;
		if($question_display=='')
		{
		$i_start = $i;
		$mcid_start = $mcid;
		$question_display=$question_display.$question;	
		}
		else
		{
		$question_display=$question_display.' __ '.$question;		
		}

		$m=$m+5;	
		$difficulty_display = $difficulty_display+$difficulty;
	
		
		if($difficulty==0)
		{
			$difficulty_display = ($difficulty_display/($m-5))*5;
			$difficulty_display = sprintf("%2.2f", $difficulty_display);
			
			$k++;
			echo '<tr>'
				 .'<td align="center" bordercolor="#EEEEEE">'.$k.'</td>'
				 .'<td align="center" bordercolor="#EEEEEE">'.$type_text.'</td>'
				 .'<td bordercolor="#EEEEEE">'.$question_display.' ('._NUMQUESTIONS.' '.($n-1).' '._UNITS.')</td>'
				 .'<td align="center" bordercolor="#EEEEEE">'.$difficulty_display.'</td>'		
				 .'<td align="center" bordercolor="#EEEEEE">';
			

		//******used or unuse question**********
		$query_repeat = "SELECT $quiz_testcolumn[qid] FROM $quiz_testtable WHERE $quiz_testcolumn[mcid] =  '" . lnVarPrepForStore($mcid_start) . "' and $quiz_testcolumn[qid] =  '" . lnVarPrepForStore($qid) . "'";
		$result_repeat = $dbconn->Execute($query_repeat);
		$numrows_repeat = $result_repeat->RecordCount();
		if ($dbconn->ErrorNo() != 0) {
				echo "Query error";
    		}	
			//echo $numrows_repeat;

			list($qid_repeat) = $result_repeat->fields;
			if($qid	== $qid_repeat)
			{
				echo '<IMG SRC="images/global/accept.png" BORDER=0 ALT='._ALREADYUSE.'>';
			}
			else
			{
				echo '<INPUT  TYPE="checkbox" NAME="add_question_to_test['.$j.']" VALUE="'.$mcid_start.'">';
				echo '<INPUT TYPE="hidden" NAME="j" VALUE="'.$j.'">';
				$j++;
			}
		//******end used or unuse question**********
			
			echo '</td>'
 				 .'</tr>';
		
		$question_display='';
		$n=0;
		$difficulty_display=0;
		$m=0;

		
		}//end 	if($difficulty==0)	 
				 
}//end type =2



if($type==3)
{

		$n++;

		if($m==null)
		{
		$i_start = $i;
		$mcid_start = $mcid;
		}

		$m=$m+5;	
		$difficulty_display = $difficulty_display+$difficulty;
	
		
		if($difficulty==0)
		{
			$difficulty_display = ($difficulty_display/($m-5))*5;
			$k++;
			echo '<tr>'
				 .'<td align="center" bordercolor="#EEEEEE">'.$k.'</td>'
				 .'<td align="center" bordercolor="#EEEEEE">'.$type_text.'</td>'
				 .'<td bordercolor="#EEEEEE">'.$question.' ('._NUMQUESTIONS.' '.($n-1).' '._UNITS.')</td>'
				 .'<td align="center" bordercolor="#EEEEEE">'.$difficulty_display.'</td>'		
				 .'<td align="center" bordercolor="#EEEEEE">';
			

		//******used or unuse question**********
		$query_repeat = "SELECT $quiz_testcolumn[qid] FROM $quiz_testtable WHERE $quiz_testcolumn[mcid] =  '" . lnVarPrepForStore($mcid_start) . "' and $quiz_testcolumn[qid] =  '" . lnVarPrepForStore($qid) . "'";
		$result_repeat = $dbconn->Execute($query_repeat);
		$numrows_repeat = $result_repeat->RecordCount();
		if ($dbconn->ErrorNo() != 0) {
				echo "Query error";
    		}	
			//echo $numrows_repeat;

			list($qid_repeat) = $result_repeat->fields;
			if($qid	== $qid_repeat)
			{
				echo '<IMG SRC="images/global/accept.png" BORDER=0 ALT='._ALREADYUSE.'>';
			}
			else
			{
				echo '<INPUT  TYPE="checkbox" NAME="add_question_to_test['.$j.']" VALUE="'.$mcid_start.'">';
				echo '<INPUT TYPE="hidden" NAME="j" VALUE="'.$j.'">';
				$j++;
			}
		//******end used or unuse question**********
			
			echo '</td>'
 				 .'</tr>';
		
		$question_display='';
		$n=0;
		$difficulty_display=0;
		$m=0;

		
		}//end 	if($difficulty==0)	



}//end type =3



			
			}//end for			
			
			
			echo '<table width="100%" border="0" cellspacing="0" cellpadding="5">';
			echo '<tr>';
			echo '<td align="center"><INPUT CLASS="button" TYPE="submit" VALUE="'._ADDQUESTIONTOTEST.'"></td>';
			echo '</tr>';
			echo '<tr>';
			echo '<td align="center"><a href="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=manage_question&amp;cid='.$cid.'&amp;qid='.$qid.'">'._SHOWALLQUESTION.'</a></td>';
			echo '</tr>';
			echo '</table>';
			
			echo '</table>';
			echo '</FORM>';

}//end if($num_result!=0)
else
{
echo '<br>';
echo '<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0">';
echo '<tr>';
echo '<td align="center">'._MISMATCH.' "'.$keyword_all.'"</td>';
echo '</tr>';
echo '</table>';
}



//}//end if !$keyword==''



}


function getMaxWeight($qid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_testtable = $lntable['quiz_test'];
	$quiz_testcolumn = &$lntable['quiz_test_column'];

	$result = $dbconn->Execute("SELECT MAX($quiz_testcolumn[weight]) FROM $quiz_testtable WHERE $quiz_testcolumn[qid]=".lnVarPrepForStore($qid)."");
	if (!$result) die ("result error");	
	
	list($max_weight) = $result->fields;

	return $max_weight + 1;
	//return $qid;
}


function addQuestion($vars) {

	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$quiz_testtable = $lntable['quiz_test'];
	$quiz_testcolumn = &$lntable['quiz_test_column'];

	if($dbconn->ErrorNo() != 0) {
		return;
	}	
	
	/**
	echo $keyword;
	echo "<br>j=1 : ".$add_question_to_test[1];
	echo "<br>j=2 : ".$add_question_to_test[2];
	echo "<br>j=3 : ".$add_question_to_test[3];
	*/
	$weight = getMaxWeight($qid);
	//echo $weight;
	
	for($i=1;$i<=$j;$i++)
	{
		
	if($add_question_to_test[$i]!=null)
		{
		
		$query = "INSERT INTO $quiz_testtable
		(	$quiz_testcolumn[qid],
		$quiz_testcolumn[mcid],
		$quiz_testcolumn[weight]
		)
		VALUES ('" . lnVarPrepForStore($qid) . "',
		'" . lnVarPrepForStore($add_question_to_test[$i]) . "',
			'" . lnVarPrepForStore($weight) . "')";	
		
		$result = $dbconn->Execute($query);
	 	if (!$result) die ("result error");	
		
		$weight++;		
		
		}//end if($add_question_to_test[$i]!=null)
	
	}// end for
	
	//return $permission;
}


function manageQuestion($vars) {

	// Get arguments from argument array
	extract($vars);

	if($edit==1)
	{
		addQuiz($vars);
	}

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$quiz_testtable = $lntable['quiz_test'];
	$quiz_testcolumn = &$lntable['quiz_test_column'];
	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];

	if($dbconn->ErrorNo() != 0) {
		return;
	}

	//select information from quiz table
	$query = "SELECT  $quizcolumn[name],$quizcolumn[grade]
	FROM  $quiztable
	WHERE $quizcolumn[qid]=".lnVarPrepForStore($qid)."";
	$result = $dbconn->Execute($query);
	list($quiz_name,$quiz_grade) = $result->fields;
	



	//select number of questions in test
	$query1 = "SELECT $quiz_testcolumn[mcid] FROM $quiz_testtable WHERE $quiz_testcolumn[qid]=".lnVarPrepForStore($qid)."";
	$result1 = $dbconn->Execute($query1);
	$numrows = $result1->RecordCount();
	if (!$result1) die ("result1 error");	
	

	
//*************************************************	
	
	//Calculate a difficult level of the test
	$query2 = "SELECT $quiz_multichoicecolumn[mcid],$quiz_multichoicecolumn[difficulty],$quiz_multichoicecolumn[type] FROM $quiz_multichoicetable JOIN $quiz_testtable ON $quiz_testcolumn[mcid]=$quiz_multichoicecolumn[mcid] WHERE $quiz_testcolumn[qid]=".lnVarPrepForStore($qid)."";
	$result2 = $dbconn->Execute($query2);
	if (!$result2) die ("result2 error");
	
	$num=0;
	for ($i=1; (list($mcid,$difficulty,$type) = $result2->fields); $i++) 
	{
		$result2->MoveNext();		
		$num++;
		//echo "mcid : ".$mcid." type : ".$type;
		
		if($type==2||$type==3)
		{
	
			$difficulty_type2=100;
			while($difficulty_type2!=0)
			{	
				$mcid++;
				
				if($difficulty_type2!=100)
				{
					$num++;
				}
				
				$query_type2 = "SELECT $quiz_multichoicecolumn[difficulty] FROM $quiz_multichoicetable WHERE $quiz_multichoicecolumn[mcid]=".$mcid."";
				$result_type2 = $dbconn->Execute($query_type2);
				if (!$result_type2) die ("result2 error");
				list($difficulty_type2) = $result_type2->fields;
				
				$diff_new = $difficulty_type2 + $diff_new;
				
			}// end while
			
			
		}// end if
		
		//echo " diff : ".$diff_new." difficulty : ".$difficulty;
		//echo "<br>";
		
		$difficulty_all = $difficulty + $diff_new;
		$difficulty_result = $difficulty_result + $difficulty_all;
		$diff_new=0;
	}//end for

		//echo "<br>difficulty = ".$difficulty_result;		
		$diff_percent = ($difficulty_result/(5*$num))*100;
		$diff_percent = sprintf("%2.2f", $diff_percent);


//****************************************	

	
	
	
	
	echo '<center><BR><fieldset><legend><B>'._TESTINFORMATION.'</B></legend>';
	echo '<table cellpadding=3 cellspacing=0 border=0 width=100%>';
	echo '<tr>';
	echo '<td><B>'._LESSONTITLEHOT.'</B> : '.$quiz_name.'</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td><B>'._NUMQUESTION.'</B> : '.$num.' '._UNITS.'</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td><B>'._GRADE.'</B> : '.$quiz_grade.' '._QUESTIONSCORE.'</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td><B>'._DIFFICULTYLEVEL.'</B> : '.$diff_percent.' %</td>';
	echo '</tr>';
	echo '</table>';
	echo '</fieldset></center>';

	echo '<br>';

	echo '<table width="100%" border="1" cellspacing="0" cellpadding="3" bordercolor="#DDDDDD">';
	echo '<tr>';
	echo '<td width="5%" align="center" bgcolor="#CCCCCC"><B>No.</B></td>';
	//echo '<td width="10%" align="center" bgcolor="#CCCCCC"><B>'._ORDER.'</B></td>';
	echo '<td width="15%" align="center" bgcolor="#CCCCCC"><B>'._QUESTIONTYPE.'</B></td>';
	echo '<td width="50%" align="center" bgcolor="#CCCCCC"><B>'._QUESTIONITEM.'</B></td>';
	echo '<td width="15%" align="center" bgcolor="#CCCCCC"><B>'._DIFFICULTYLEVEL.'</B></td>';
	echo '<td width="15%" align="center" bgcolor="#CCCCCC"><B>'._DELETEQUESTION.'</B></td>';
	echo '</tr>';
	

	$query3 = "SELECT $quiz_multichoicecolumn[mcid],$quiz_multichoicecolumn[type],$quiz_multichoicecolumn[question],$quiz_multichoicecolumn[difficulty],$quiz_testcolumn[weight] FROM $quiz_multichoicetable JOIN $quiz_testtable ON $quiz_testcolumn[mcid]=$quiz_multichoicecolumn[mcid] WHERE $quiz_testcolumn[qid]=".lnVarPrepForStore($qid)." ORDER BY $quiz_testcolumn[weight]";
	$result3 = $dbconn->Execute($query3);
	if (!$result3) die ("result3 error");
	
	for ($i=1; (list($mcid,$type,$question,$difficulty,$weight) = $result3->fields); $i++) 
	{
		//******echo type in text 
				if($type==1){$type_text=_QCHOICE;}
					else if($type==2){$type_text=_QFILL;}
						else if($type==3){$type_text=_QMULTIQ;}
	
		$result3->MoveNext();	


if($type==1)
{
	echo '<tr>';
	echo '<td align="center" bordercolor="#EEEEEE">'.$i.'</td>';
	//******************Move Up, Move Down***************
	//echo '<td align="center" bordercolor="#EEEEEE">';
	//echo '<A HREF="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=changePosition&amp;cid='.$cid.'&amp;qid='.$qid.'&amp;weightnow='.$weight.'&amp;down=1"><IMG SRC=images/global/down.gif  BORDER=0 ALT='._MOVEDOWN.'></a>';
	//echo ' <IMG SRC=images/global/up.gif  BORDER=0 ALT='._MOVEUP.'>';
	//echo '</td>';
	//***************************************************
	
	$question=stripslashes($question);
	$question=nl2br($question);
	$question=str_replace('\\"','"',$question);
			
	echo '<td align="center" bordercolor="#EEEEEE">'.$type_text.'</td>';
	echo '<td bordercolor="#EEEEEE">'.$question.'</td>';
	echo '<td align="center" bordercolor="#EEEEEE">'.$difficulty.'</td>';
	echo '<td align="center" bordercolor="#EEEEEE">';
	echo "<A HREF=\"javascript: if(confirm('Delete Question?')) window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=delete_question&amp;qid=$qid&amp;mcid=$mcid&amp;type=$type&amp;cid=$cid','_self')\"><IMG SRC=images/global/delete.gif  BORDER=0 ALT="._DELETE."></A>";
	echo '</td>';
	echo '</tr>';

}// end if type = 1		


if($type==2)
{
	$j=0;
	while($difficulty!=0)
	{
	$j++;
	$query_type2 = "SELECT $quiz_multichoicecolumn[mcid], $quiz_multichoicecolumn[question],$quiz_multichoicecolumn[difficulty] FROM $quiz_multichoicetable WHERE $quiz_multichoicecolumn[mcid]=".lnVarPrepForStore($mcid)." ";
	$result_type2 = $dbconn->Execute($query_type2);
	if (!$result_type2) die ("result_type2 error");	
	
	list($mcid,$question,$difficulty) = $result_type2->fields;
	
	if($question_display=='')
	{
		$i_start = $i;
		$mcid_start = $mcid;
		$question_display=$question_display.$question;	
	}
	else
	{
		$question_display=$question_display.' __ '.$question;		
	}	
	
	$m=$m+5;	
	$difficulty_display = $difficulty_display+$difficulty;	

	$mcid++;
	
	}//end while
	
	if($difficulty==0)
	{
	$difficulty_display = ($difficulty_display/($m-5))*5;
	$difficulty_display = sprintf("%2.2f", $difficulty_display);

	echo '<tr>';
	echo '<td align="center" bordercolor="#EEEEEE">'.$i.'</td>';
	//******************Move Up, Move Down***************
	//echo '<td align="center" bordercolor="#EEEEEE">';
	//echo '<A HREF="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=changePosition&amp;cid='.$cid.'&amp;qid='.$qid.'&amp;weightnow='.$weight.'&amp;down=1"><IMG SRC=images/global/down.gif  BORDER=0 ALT='._MOVEDOWN.'></a>';
	//echo ' <IMG SRC=images/global/up.gif  BORDER=0 ALT='._MOVEUP.'>';
	//echo '</td>';
	//***************************************************
	echo '<td align="center" bordercolor="#EEEEEE">'.$type_text.'</td>';
	echo '<td bordercolor="#EEEEEE">'.$question_display.' ('._NUMQUESTIONS.' '.($j-1).' '._UNITS.')</td>';
	echo '<td align="center" bordercolor="#EEEEEE">'.$difficulty_display.'</td>';
	echo '<td align="center" bordercolor="#EEEEEE">';
	echo "<A HREF=\"javascript: if(confirm('Delete Question?')) window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=delete_question&amp;qid=$qid&amp;mcid=$mcid_start&amp;type=$type&amp;cid=$cid','_self')\"><IMG SRC=images/global/delete.gif  BORDER=0 ALT="._DELETE."></A>";
	echo '</td>';
	echo '</tr>';
		
	}//end  if($difficulty==0)
	
	$question_display='';
	$difficulty_display=0;
	$m=0;
	
	
}// end if type = 2


if($type==3)
{

	$j=0;
	while($difficulty!=0)
	{
	$j++;
	$query_type2 = "SELECT $quiz_multichoicecolumn[mcid], $quiz_multichoicecolumn[question],$quiz_multichoicecolumn[difficulty] FROM $quiz_multichoicetable WHERE $quiz_multichoicecolumn[mcid]=".lnVarPrepForStore($mcid)." ";
	$result_type2 = $dbconn->Execute($query_type2);
	if (!$result_type2) die ("result_type2 error");	
	
	list($mcid,$question,$difficulty) = $result_type2->fields;
	
	if($m==null)
	{
		$i_start = $i;
		$mcid_start = $mcid;
	}
	
	
	$m=$m+5;	
	$difficulty_display = $difficulty_display+$difficulty;	

	$mcid++;
	
	}//end while
	
	if($difficulty==0)
	{
	$difficulty_display = ($difficulty_display/($m-5))*5;
	$difficulty_display = sprintf("%2.2f", $difficulty_display);

	echo '<tr>';
	echo '<td align="center" bordercolor="#EEEEEE">'.$i.'</td>';
	//******************Move Up, Move Down***************
	//echo '<td align="center" bordercolor="#EEEEEE">';
	//echo '<A HREF="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=changePosition&amp;cid='.$cid.'&amp;qid='.$qid.'&amp;weightnow='.$weight.'&amp;down=1"><IMG SRC=images/global/down.gif  BORDER=0 ALT='._MOVEDOWN.'></a>';
	//echo ' <IMG SRC=images/global/up.gif  BORDER=0 ALT='._MOVEUP.'>';
	//echo '</td>';
	//***************************************************
	echo '<td align="center" bordercolor="#EEEEEE">'.$type_text.'</td>';
	echo '<td bordercolor="#EEEEEE">'.$question.' ('._NUMQUESTIONS.' '.($j-1).' '._UNITS.')</td>';
	echo '<td align="center" bordercolor="#EEEEEE">'.$difficulty_display.'</td>';
	echo '<td align="center" bordercolor="#EEEEEE">';
	echo "<A HREF=\"javascript: if(confirm('Delete Question?')) window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=delete_question&amp;qid=$qid&amp;mcid=$mcid_start&amp;type=$type&amp;cid=$cid','_self')\"><IMG SRC=images/global/delete.gif  BORDER=0 ALT="._DELETE."></A>";
	echo '</td>';
	echo '</tr>';
		
	}//end  if($difficulty==0)
	
	$question_display='';
	$difficulty_display=0;
	$m=0;

}
	
	}//end for
	
	echo '</table>';
	
	echo '<table width="100%" border="0" cellspacing="0" cellpadding="5">';
	echo '<tr>';
	echo '<td align="center">';
	
	echo "<input class='button'  type=button value='เพิ่มข้อสอบ'  Onclick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=search_question&amp;cid=$cid&amp;qid=$qid','_self')\"> ";
	
	echo "<input class='button'  type=button value='แก้ไขการตั้งค่าชุดแบบทดสอบ'  Onclick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;func=edit_quiz&amp;cid=$cid&amp;qid=$qid','_self')\"> ";
	
	echo "<input class='button'  type=button value='เสร็จสิ้น'  Onclick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;cid=$cid','_self')\"> ";
	
	echo '</td>';
	echo '</tr>';
	echo '</table>';
	
	

}





function deleteQuestion($vars) {
	// Get arguments from argument array
	extract($vars);
	//print_r($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_testtable = $lntable['quiz_test'];
	$quiz_testcolumn = &$lntable['quiz_test_column'];
	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];	


	//echo $mcid;
	//echo '<br>'.$qid;



		if (!empty($mcid)) {
			// delete question
			$query = "DELETE FROM $quiz_testtable WHERE $quiz_testcolumn[mcid] = '" . lnVarPrepForStore($mcid) . "' and $quiz_testcolumn[qid] =  '" . lnVarPrepForStore($qid) . "' ";
			$dbconn->Execute($query);
			}

		
		/*
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

	}*/
	//resequenceQuestions($qid);



}

function deleteQuiz($vars)
{
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_testtable = $lntable['quiz_test'];
	$quiz_testcolumn = &$lntable['quiz_test_column'];

	if (!empty($qid)) {

		//detete quiz table 
		$query = "DELETE FROM $quiztable WHERE $quizcolumn[qid] =  '" . lnVarPrepForStore($qid) . "'";
		$dbconn->Execute($query);

		// delete quiz_test table
		$query = "SELECT $quiz_testcolumn[qid] FROM $quiz_testtable WHERE $quiz_testcolumn[qid] =  '" . lnVarPrepForStore($qid) . "'";
		$result = $dbconn->Execute($query);
		$numrows = $result->PO_RecordCount();
		while(list($quid) = $result->fields) {
			$result->MoveNext();
			$dbconn->Execute("DELETE FROM $quiz_testtable WHERE $quiz_testcolumn[qid] =  '" . lnVarPrepForStore($quid) . "'");
		}
	}


}




?>
