<?php
/*
*  Quiz administration
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}


/* options */
$vars= array_merge($_GET,$_POST);	
switch($op) {
	case "add_question" : addQuestion($vars); break;
	case "delete_question" : deleteQuestion($vars); break;
	case "increase_weight":   increaseQuestionWeight($vars); break;
	case "decrease_weight": decreaseQuestionWeight($vars); break;
	case "use_question": useQuestion($vars); break;
}
/* options */

	/*
	* show question 
	*/
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_questiontable = $lntable['quiz_question'];
	$quiz_questioncolumn = &$lntable['quiz_question_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];



//	$result = $dbconn->Execute("SELECT $lessonscolumn[title],$lessonscolumn[weight] FROM $lessonstable WHERE $lessonscolumn[lid]='". lnVarPrepForStore($lid) ."'");
//	list($lessontitle,$lessonno) = $result->fields;

	$query = "SELECT $quizcolumn[cid],$quizcolumn[lid],$quizcolumn[type],$lessonscolumn[title],$lessonscolumn[weight]
						FROM $quiztable LEFT JOIN $lessonstable ON $quizcolumn[lid]=$lessonscolumn[lid] 
						WHERE $quizcolumn[qid]='". lnVarPrepForStore($qid) ."'";
	$result = $dbconn->Execute($query);

	list($cid,$lid,$type,$lesson_title,$lesson_no) = $result->fields;
	
	$courseinfo = lnCourseGetVars($cid);
	$coursecode=$courseinfo['code'];
	$coursename=$courseinfo['title'];
	$url=COURSE_DIR.'/'.$coursecode;
	$htmltitle = $coursecode.': '.$coursename; 
	if ($type == '0') { 
		$strtype = _PRETEST;
	}
	else if ($type == '1') {
		$strtype = _LESSONTEST .' : '._LESSONNO.' '. $lesson_no . ' '. $lesson_title;
	}
	else if ($type == '2') {
		$strtype = _POSTEST;
	}

	
	?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
	<HTML>
	<HEAD>
	<TITLE> <?=$htmltitle?> </TITLE>
	<script language="JavaScript" src="javascript/popup.js"></script>
	<link rel="StyleSheet" href="themes/Simple/style/style.css" type="text/css">
	</HEAD>

	<BODY BGCOLOR=#FFFFFF  topmargin=0 leftmargin=0 marginheight=0 marginwidth=0>

	<?
	/* show header */
	echo '<table width= 100% cellpadding=3 cellspacing=1 bgcolor=#EEF3A7 border=0>';
	echo '<tr><td bgcolor=#EEF3A7 align=Left>&nbsp;<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <B><FONT COLOR="#800000">'. $strtype. '</FONT><BR></B></td></tr>'
	.'</table><center>';
	/* show header */


			//>>>> Show Questions
				$query = "SELECT  $quiz_questioncolumn[quid],
								$quiz_questioncolumn[question], 
								$quiz_questioncolumn[answer], 
								$quiz_questioncolumn[score], 
								$quiz_questioncolumn[weight]  
								FROM  $quiz_questiontable
								WHERE 	$quiz_questioncolumn[qid] =  '" . lnVarPrepForStore($qid) . "'
								ORDER BY $quiz_questioncolumn[weight]";
				$result = $dbconn->Execute($query);
	
				$question_numrows = $result->PO_RecordCount();
				
				$rownum = 1;
				$lastpos = '';
				$active_count = 0;

				if ($question_numrows > 0) {
					echo '<table width= 100% cellpadding=3 cellspacing=1 bgcolor=#999999 border=0>';

					// list questions
					while(list($quid,$question,$answer,$score,$weight) = $result->fields) {
						$question=lnShowContent($question,$url);
						$result->MoveNext();
						$active_count++;
						$down = "<a href=index.php?mod=Courses&amp;file=questionadmin&amp;op=increase_weight&amp;qid=$qid&amp;cid=$cid&amp;quid=$quid&amp;weight=$weight>" . '<img src=images/global/down.gif border=0>' . '</a>';
						$up = "<a href=index.php?mod=Courses&amp;file=questionadmin&amp;op=decrease_weight&amp;qid=$qid&amp;cid=$cid&amp;quid=$quid&amp;weight=$weight>" . '<img src=images/global/up.gif border=0>' . '</a>';
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
							
						// if edit
						if ($op == "edit_question_form" && $active_count == $item) {
							echo "<tr valign=top bgcolor=#EEF3A7 valign=top>";
							echo '<td width=5% align=center><B>'.$item.'</B></td>';
							echo "<td bgcolor=#EEF3A7>";
							editQuestionForm($vars);
							echo '</td></tr>';
						} 
						else {
							// show question
							echo "<tr valign=top bgcolor=#ffffff valign=top>";
							echo '<td width=5% align=center>'.$arrows.'</td>';
							echo "<td>";
							echo "<table cellpadding=0 cellspacing=0 border=0 width=100%>";
							echo "<tr><td valign=top><B>".$weight.". ".stripslashes($question)."</B> ("._QUESTIONSCORE." $score)</td>";
							echo "<td align=right valign=top>";
							
							echo "<A HREF=\"index.php?mod=Courses&amp;file=questionadmin&amp;op=edit_question_form&amp;qid=$qid&amp;cid=$cid&amp;quid=$quid&amp;item=$weight\"><IMG SRC=images/global/edit.gif  BORDER=0 ALT="._EDIT."></A>  &nbsp;";

							echo "<A HREF=\"index.php?mod=Courses&amp;file=questionadmin&amp;op=insert_question_form&amp;qid=$qid&amp;cid=$cid&amp;item=$weight\"><IMG SRC=images/global/insert.gif  BORDER=0 ALT="._INSERT."></A>  &nbsp;";
							
							echo "<A HREF=\"javascript: if(confirm('Delete quiz $weight?')) window.open('index.php?mod=Courses&amp;file=questionadmin&amp;op=delete_question&amp;qid=$qid&amp;cid=$cid&amp;quid=$quid','_self')\"><IMG SRC=images/global/delete.gif  BORDER=0 ALT="._DELETE."></A>";		
							echo "</td></tr></table>";

						//>>>> Choice
							$query = "SELECT  $quiz_choicecolumn[chid],
									$quiz_choicecolumn[choice], 
									$quiz_choicecolumn[description] 
									FROM  $quiz_choicetable
									WHERE 	$quiz_choicecolumn[quid] =  '" . lnVarPrepForStore($quid) . "' 
									ORDER BY $quiz_choicecolumn[weight]";
							$result2 = $dbconn->Execute($query);
							$choice_numrows = $result2->PO_RecordCount();
							
							if ($choice_numrows > 0) {
								echo '<table width= 100% cellpadding=0 cellspacing=0 border=0>';

								// list choices
								for($i=0; list($chid,$choice,$description) = $result2->fields; $i++) {
									$choice=lnShowContent($choice,$url);
									$result2->MoveNext();
									echo '<tr valign=top bgcolor=#ffffff valign=top>';
									echo '<td>';
									if (checkChoiceType($quid,$answer) == 0) {
										if ($answer & pow(2,$i)) {
											echo '<INPUT checked TYPE="radio" NAME="">';
										}
										else {
											echo '<INPUT TYPE="radio" NAME="">';
										}
									}
									else {
										if ($answer & pow(2,$i)) {
											echo '<INPUT checked TYPE="checkbox" NAME="">';
										}
										else {
											echo '<INPUT TYPE="checkbox" NAME="">';
										}
									}
									echo  stripslashes($choice);
									if (!empty($description)) {
										echo '<FONT  COLOR="#444444"> - '. stripslashes($description) .'</FONT>';
									}
									echo '</td>';
									echo '</tr>';
								}
								echo '</table>';

							}
						
							echo '</td></tr>';
						}

						// if edit 
						if ($op == "insert_question_form" && $active_count == $item) {
							echo "<tr valign=top bgcolor=#EEF3A7 valign=top>";
							echo '<td width=6% align=center>&nbsp;</td>';
							echo "<td bgcolor=#EEF3A7>";
							editQuestionForm($vars);
							echo '</td></tr>';
						} 
					} 
					//>>>> Choice
					echo '</table>';
				}
				
				if ($op == "add_question_form") {
					editQuestionForm($vars);
				} 
				else if ($op != "insert_question_form" && $op != "edit_question_form"){
					echo '<BR><BR>';
					addQuestionButton($vars);
				}

				//>> Show Questions


/*- - - -*/


/*
* show add new question button
*/
function addQuestionButton($vars) {
	// Get arguments from argument array
    extract($vars);

	echo '<FORM METHOD=POST ACTION="index.php">'
		.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
		.'<INPUT TYPE="hidden" NAME="file" VALUE="questionadmin">'
		.'<INPUT TYPE="hidden" NAME="op" VALUE="add_question_form">'
		.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
		.'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">';
	echo '<P><INPUT CLASS="button_org" TYPE=submit VALUE="'._ADDQUESTION.'">';
	echo '</FORM><BR><BR>';		
}

/*
*show use same quiz for pre/post-test

function useQuestionButton($vars,$type) {
	// Get arguments from argument array
    extract($vars);

	echo '<FORM METHOD=POST ACTION="index.php">'
		.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
		.'<INPUT TYPE="hidden" NAME="file" VALUE="quizadmin">'
		.'<INPUT TYPE="hidden" NAME="op" VALUE="use_question">'
		.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
		.'<INPUT TYPE="hidden" NAME="lid" VALUE="'.$lid.'">'
		.'<INPUT TYPE="hidden" NAME="quiztype" VALUE="3">';
	echo '<P><INPUT CLASS="button_org" TYPE=submit VALUE="'._USEQUESTION.' '.$type.'">';
	echo '</FORM>';		
}
*/
/*
* change type of quiz to 3

function useQuestion($vars) {
	// Get arguments from argument array
    extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];

	$query = "UPDATE $quiztable SET 
					$quizcolumn[type] =   '" . lnVarPrepForStore($quiztype) . "' 
					WHERE $quizcolumn[lid] = '" . lnVarPrepForStore($lid) . "'";	

	$result = $dbconn->Execute($query);
}
*/

/*
* check question type
*  - single answer or multiple answer choice
*/
function checkChoiceType($quid, $answer) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];
	$result = $dbconn->Execute("SELECT COUNT($quiz_choicecolumn[quid]) FROM $quiz_choicetable WHERE $quiz_choicecolumn[quid]='$quid'");
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


/*
*	edit question
*/
function editQuestionForm($vars) {
		// Get arguments from argument array
	    extract($vars);

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
		if (!empty($quid)) {
			list($dbconn) = lnDBGetConn();
			$lntable = lnDBGetTables();
			
			$quiz_questiontable = $lntable['quiz_question'];
			$quiz_questioncolumn = &$lntable['quiz_question_column'];
			$quiz_choicetable = $lntable['quiz_choice'];
			$quiz_choicecolumn = &$lntable['quiz_choice_column'];
			
			$query = "SELECT $quiz_questioncolumn[qid],
								$quiz_questioncolumn[question],
								$quiz_questioncolumn[answer],
								$quiz_questioncolumn[score],
								$quiz_questioncolumn[weight],
								$quiz_choicecolumn[chid],
								$quiz_choicecolumn[choice],
								$quiz_choicecolumn[description],
								$quiz_choicecolumn[weight]
								FROM  $quiz_questiontable LEFT JOIN $quiz_choicetable ON $quiz_questioncolumn[quid] = $quiz_choicecolumn[quid]
								WHERE 	$quiz_questioncolumn[quid] =  '" . lnVarPrepForStore($quid) . "' 
								ORDER BY $quiz_choicecolumn[weight]";

				$result = $dbconn->Execute($query);
				list($qid,$question,$answer,$score,$weight,$cd,$ch,$dc,$cw) = $result->fields;

				$chid =  array();
				$choice = array();
				$desc = array();
				for ($i=0,$nochoice=0; list($_,$_,$_,$_,$_,$cd,$ch,$dc,$cw) = $result->fields; $i++) {
						$result->MoveNext();
						$choice[$i] = $ch;
						$desc[$i] = $dc;
						$chid[$i]=$cd;
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
		
		echo '<table cellpadding=3 cellspacing=0 border=0 width=100% bgcolor=#EEF3A7>';
		echo "<FORM NAME=\"questionform\" METHOD=POST ACTION=\"index.php\" onSubmit=\"if (empty = checkFields()) {alert(empty); return false;} else { return true;} \">";
		echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
			.'<INPUT TYPE="hidden" NAME="file" VALUE="questionadmin">'
			.'<INPUT TYPE="hidden" NAME="op" VALUE="add_question">'
			.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
//			.'<INPUT TYPE="hidden" NAME="lid" VALUE="'.$lid.'">'
			.'<INPUT TYPE="hidden" NAME="qid" VALUE="'.$qid.'">'
			.'<INPUT TYPE="hidden" NAME="quid" VALUE="'.$quid.'">'
			.'<INPUT TYPE="hidden" NAME="weight" VALUE="'.$item.'">'
			.'<INPUT TYPE="hidden" NAME="quiztype" VALUE="'.$quiztype.'">';
		
		echo '<tr><td width=10% valign=top align=right><B>'._QUESTION.'</B>:</td>';
		echo '<td>';
		echo '<TEXTAREA CLASS="input" NAME="question" ROWS="3" COLS="35" wrap="soft" style="width: 95%;">'.lnVarPrepForDisplay($question).'</TEXTAREA>';
		echo "<INPUT class='button_white'  TYPE=button VALUE='&nbsp;...&nbsp;' onclick=\"javascript:popup('index.php?mod=spaw&amp;type=Question&amp;cid=$cid&amp;quid=$quid','_blank',750,480)\">";
		echo '</td></tr>';	
		
		
		echo '<tr><td valign=top align=right><B>'._ANSWER.'</B>:</td><td>';
		echo '<table cellpadding=1 cellspacing=0 border=0 width="100%">';
		
		// form head
		echo '<tr align=center><td>'._CORRECTANS.'</td><td>'._CHOICE.'</td><td align="left">&nbsp;&nbsp;&nbsp;&nbsp;'._DESCRIPTION.'</td></tr>';
		
		if ($action == "delete_item") {
			$nochoice--;
		}
		else if ($action =="add_item" && $op != "edit_question_form") {
			$nochoice++;
		}
		if ($nochoice < 1) $nochoice = 1;

		for ($i=0,$n=1; $i<$nochoice; $i++,$n++) {
			
				echo '<tr><td align="center">';
				$ans = pow(2,$i);
				if ($answer & pow(2,$i)) {  // edit choice
					echo '<INPUT checked TYPE="checkbox" NAME="ans['.$i.']" VALUE="'.$ans.'">';
				}
				else {
					echo '<INPUT TYPE="checkbox" NAME="ans['.$i.']" VALUE="'.$ans.'">';
				}
				echo '</td>';
				echo '<td align="left" width=270>'.$n.'. <TEXTAREA CLASS="input" NAME="choice['.$i.']" ROWS="1" COLS="35" wrap="soft">'.lnVarPrepForDisplay($choice[$i]).'</TEXTAREA>';

				echo "<INPUT class='button_white' TYPE=button VALUE='&nbsp;...&nbsp;' onclick=\"javascript:popup('index.php?mod=spaw&amp;type=Choice&amp;cid=$cid&amp;chid=".$chid[$i]."&amp;n=".$i."','_blank',750,480)\">";
				echo '</td><td align="left"><input  CLASS="input"  type=text NAME="desc['.$i.']" size="15" VALUE="'.lnVarPrepForDisplay($desc[$i]).'">';
	
				
				if (!empty($quid) && $op == "edit_question_form") {
					echo " <input  class='button_org' type=button value='  -  ' Onclick=\"javascript: if(confirm('Delete choice $n?')) window.open('index.php?mod=Courses&amp;file=questionadmin&amp;op=$op&amp;action=delete_choice&amp;qid=$qid&amp;quid=$quid&amp;chid=".$chid[$i]."&amp;item=$weight','_self')\">";		
					echo '<INPUT TYPE="hidden" NAME="chid['.$i.']" VALUE="'.$chid[$i].'">';
				}
				else if ($i == $nochoice -1) { // show at last of choice
					echo " <input class='button_org'  type=button value=' + '  Onclick=\"javascript:window.open('index.php?mod=Courses&amp;file=questionadmin&amp;op=$op&amp;action=add_item&amp;qid=$qid&amp;quid=$quid&amp;item=$itemold&amp;nochoice=$nochoice','_self')\"> ";
					if ($n != 1) {
						echo "<input class='button_org' type=button value='  -  ' Onclick=\"javascript: window.open('index.php?mod=Courses&amp;file=questionadmin&amp;op=$op&amp;action=delete_item&amp;qid=$qid&amp;quid=$quid&amp;item=$itemold&amp;nochoice=$nochoice','_self')\">";		
					}
					echo '<INPUT class="inpurt" TYPE="hidden" NAME="chid['.$i.']" VALUE="'.$chid[$i].'">';
				}
				echo '</td></tr>';

				// add question
				if ($op == "edit_question_form"&& $action =="add_item" && $n==$item) {
					echo '<tr><td>';
					echo '<INPUT TYPE="checkbox" NAME="ans['.$i.']" VALUE="'.$ans.'">';					
					echo '</td>';
					echo '<td>'.$n.' <TEXTAREA class="inpurt" NAME="choice['.$i.']" ROWS="1" COLS="32" wrap="soft"></TEXTAREA>';
					echo "<INPUT TYPE=button VALUE='&nbsp;...&nbsp;' onclick=\"javascript:popup('index.php?mod=spaw&amp;type=Choice&amp;cid=$cid&amp;chid=$chid','_blank',750,480)\">";
					echo '</td><td><TEXTAREA class="inpurt" NAME="desc['.$i.']" ROWS="1" COLS="20" wrap="soft"></TEXTAREA>';
					echo '</td></tr>';
				}
				
			}

		echo '</table>';
		echo '</td></tr>';

		// score
		echo '<tr><td width=10%  align=right><B>'._QUESTIONSCORE.'</B>:</td>';
		echo '<td><INPUT class="input" TYPE="text" NAME="score" SIZE="2" VALUE="'.$score.'"></td></tr>';
		echo '<tr><td>&nbsp;</td><td>';
		
		// submit buttons
		if (empty($quid)) {
			echo '<INPUT CLASS="button_org" TYPE="submit" VALUE="'._ADDQUESTION.'">';
		}
		else {
			echo '<INPUT CLASS="button_org" TYPE="submit" VALUE="'._UPDATEQUIZ.'">';
		}

		echo " <INPUT CLASS='button_org' TYPE=button VALUE=Cancel OnClick=\"javascript:window.open('index.php?mod=Courses&amp;file=questionadmin&amp;qid=$qid&amp;cid=$cid','_self')\"></td></tr>";
		echo '</FORM>';
		
		echo '</table>';		

		// focus to input text question 
?>
		<script language="javascript">
					  document.questionform.question.focus();
		</script>
<?
}

/*
* add question
*/
function addQuestion($vars) {
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
		
/*
		if (empty($qid)) {
			if (empty($random)) $random = _LNDEFUALT_RANDOM;
			if (empty($timelimit)) $timelimit = _LNDEFUALT_TIMELIMIT;
			$max_qid = getMaxQID();
			$query = "INSERT INTO $quiztable
				  (	$quizcolumn[qid],
					$quizcolumn[lid],
					$quizcolumn[description],
					$quizcolumn[random],
					$quizcolumn[timelimit],
					$quizcolumn[type]
					  )
					VALUES ('" . lnVarPrepForStore($max_qid) . "',
							'" . lnVarPrepForStore($lid) . "',
							'" . _DEFAULTQUIZTITLE . "',
							'".$random."',
							'".$timelimit."',
							'" . lnVarPrepForStore($quiztype) . "')";	
		  	$dbconn->Execute($query);	
			$qid = $max_qid;
		}
*/

		// calculate answer
		$answer = 0;
		foreach ($ans as $val) {
			if (!empty($val)) {
				$answer += $val;
			}
		}

		// add question
		$question=stripslashes($question);
		if (empty($quid)) {
			$max_quid= getMaxQUID();
			if (empty($weight)) {
				$weight = getNextWeight($qid);
			}
			$query = "INSERT INTO $quiz_questiontable
				  (	$quiz_questioncolumn[quid],
					$quiz_questioncolumn[qid],
					$quiz_questioncolumn[question],
					$quiz_questioncolumn[answer],
					$quiz_questioncolumn[score],
					$quiz_questioncolumn[weight]
					  )
					VALUES ('" . lnVarPrepForStore($max_quid) . "',
						  '" . lnVarPrepForStore($qid) . "',
						  '" . lnVarPrepForStore($question) . "',
						  '" . lnVarPrepForStore($answer) . "',
						  '" . lnVarPrepForStore($score) . "',
						  '" . lnVarPrepForStore($weight) . "')";	
			
			$dbconn->Execute($query);	

			$quid = $max_quid;
			for ($i=0,$n=0; $i < count($choice); $i++) {
				if (!empty($choice[$i])) {
					$max_chid= getMaxCHID();
					$choice[$i]=stripslashes($choice[$i]);
					$desc[$i]=addslashes($desc[$i]);
					$n++;
					$query = "INSERT INTO $quiz_choicetable
						  (	$quiz_choicecolumn[chid],
							$quiz_choicecolumn[quid],
							$quiz_choicecolumn[choice],
							$quiz_choicecolumn[description],
							$quiz_choicecolumn[weight]
							  )
							VALUES ('" . lnVarPrepForStore($max_chid) . "',
								  '" . lnVarPrepForStore($quid) . "',
								  '" . lnVarPrepForStore($choice[$i]) . "',
								  '" . lnVarPrepForStore($desc[$i]) . "',
								  '" . lnVarPrepForStore($n) . "')";	
			
					$dbconn->Execute($query);	
				
				}
			}
		}

		// update question
		else {


			$query = "UPDATE $quiz_questiontable SET 
								$quiz_questioncolumn[question] =  '" . lnVarPrepForStore($question) . "',
								$quiz_questioncolumn[answer] = '" . lnVarPrepForStore($answer) . "',
								$quiz_questioncolumn[score] =	'" . lnVarPrepForStore($score) . "',
								$quiz_questioncolumn[weight] = '" . lnVarPrepForStore($weight) . "'
								WHERE 	$quiz_questioncolumn[quid] =  '" . lnVarPrepForStore($quid) . "'"; 

			$dbconn->Execute($query);	

			for ($i=0,$n=0; $i < count($choice); $i++) {
				if (!empty($choice[$i])) {
					$max_chid= getMaxCHID();
					$choice[$i]=stripslashes($choice[$i]);
					$desc[$i]=addslashes($desc[$i]);
					$n++;
					$query = "UPDATE $quiz_choicetable SET
							$quiz_choicecolumn[choice] =  '" . lnVarPrepForStore($choice[$i]) . "',
							$quiz_choicecolumn[description] =  '" . lnVarPrepForStore($desc[$i]) . "',
							$quiz_choicecolumn[weight] = '" . lnVarPrepForStore($n) . "'
							WHERE 	$quiz_choicecolumn[chid] =  '" . lnVarPrepForStore($chid[$i]) . "'"; 
					$dbconn->Execute($query);	
				}
			}

		}

		resequenceQuestions($qid);
}


/*
* delete question
*/
function deleteQuestion($vars) {
		// Get arguments from argument array
	    extract($vars);

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$quiz_questiontable = $lntable['quiz_question'];
		$quiz_questioncolumn = &$lntable['quiz_question_column'];
		$quiz_choicetable = $lntable['quiz_choice'];
		$quiz_choicecolumn = &$lntable['quiz_choice_column'];

		if (!empty($quid)) {
			// delete question
			$query = "DELETE FROM $quiz_questiontable WHERE $quiz_questioncolumn[quid] =  '" . lnVarPrepForStore($quid) . "'";
			$dbconn->Execute($query);	

			// delete choice
			$query = "DELETE FROM $quiz_choicetable WHERE $quiz_choicecolumn[quid] =  '" . lnVarPrepForStore($quid) . "'";
			$dbconn->Execute($query);	
		}

		resequenceQuestions($qid);
}

/*
*	delete choice
*/
function deleteChoice($vars) {
		// Get arguments from argument array
	    extract($vars);

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$quiz_choicetable = $lntable['quiz_choice'];
		$quiz_choicecolumn = &$lntable['quiz_choice_column'];

		if (!empty($chid)) {
			// delete choice
			$query = "DELETE FROM $quiz_choicetable WHERE $quiz_choicecolumn[chid] =  '" . lnVarPrepForStore($chid) . "'";
			$dbconn->Execute($query);	
		}

		resequenceChoices($quid);
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


/*
* get max weight of qid
*/
function getNextWeight($qid) {
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
		
		$quiz_questiontable = $lntable['quiz_question'];
		$quiz_questioncolumn = &$lntable['quiz_question_column'];

		$result = $dbconn->Execute("SELECT MAX($quiz_questioncolumn[weight]) FROM $quiz_questiontable WHERE $quiz_questioncolumn[qid]='$qid'");
		list($max_weight) = $result->fields;

		return $max_weight + 1;
}

/*
* get next quiz_question id
*/
function getMaxQUID() {
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
		
		$quiz_questiontable = $lntable['quiz_question'];
		$quiz_questioncolumn = &$lntable['quiz_question_column'];

		$result = $dbconn->Execute("SELECT MAX($quiz_questioncolumn[quid]) FROM $quiz_questiontable");
		list($max_quid) = $result->fields;

		return $max_quid + 1;
}

/*
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

/* 
* resequence quiz
*/
function resequenceQuestions($qid) {

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $quiz_questiontable = $lntable['quiz_question'];
    $quiz_questioncolumn = &$lntable['quiz_question_column'];
	
    // Get the information
    $query = "SELECT $quiz_questioncolumn[quid],
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
                      WHERE $quiz_questioncolumn[quid]='" . lnVarPrepForStore($quid)."'";
            $dbconn->Execute($query);
        }
        $seq++;
    }
    $result->Close();

    return true;
}

/*
* resequence choice
*/
function resequenceChoices($quid) {

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	$quiz_choicetable = $lntable['quiz_choice'];
    $quiz_choicecolumn = &$lntable['quiz_choice_column'];
	
    // Get the information
    $query = "SELECT $quiz_choicecolumn[chid],
                     $quiz_choicecolumn[weight]
					 FROM $quiz_choicetable 
					 WHERE $quiz_choicecolumn[quid]= '". lnVarPrepForStore($quid)."'
               ORDER BY $quiz_choicecolumn[weight]";
    $result = $dbconn->Execute($query);

    // Fix sequence numbers
    $seq=1;
    while(list($chid, $curseq) = $result->fields) {

        $result->MoveNext();
        if ($curseq != $seq) {
            $query = "UPDATE $quiz_choicetable
                      SET $quiz_choicecolumn[weight]='" . lnVarPrepForStore($seq) . "'
                      WHERE $quiz_choicecolumn[chid]='" . lnVarPrepForStore($chid)."'";
            $dbconn->Execute($query);
        }
        $seq++;
    }
    $result->Close();

    return true;
}

/*
* move down item
*/
function increaseQuestionWeight($vars) {
   // Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $quiz_questiontable = $lntable['quiz_question'];
    $quiz_questioncolumn = &$lntable['quiz_question_column'];

    $seq = $weight;

	// Get info on displaced block
    $sql = "SELECT $quiz_questioncolumn[quid],
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
            WHERE $quiz_questioncolumn[quid]='".lnVarPrepForStore($altquid)."'";
    $dbconn->Execute($sql);
    $sql = "UPDATE $quiz_questiontable
            SET $quiz_questioncolumn[weight]=$altseq
            WHERE $quiz_questioncolumn[quid]='".lnVarPrepForStore($quid)."'";
    $dbconn->Execute($sql);

	resequenceQuestions($qid);

    return true;
}

/*
*	move up item
*/
function decreaseQuestionWeight($vars) {
   // Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $quiz_questiontable = $lntable['quiz_question'];
    $quiz_questioncolumn = &$lntable['quiz_question_column'];

    $seq = $weight;

	// Get info on displaced block
    $sql = "SELECT $quiz_questioncolumn[quid],
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
            WHERE $quiz_questioncolumn[quid]='".lnVarPrepForStore($altquid)."'";
    $dbconn->Execute($sql);
    
	$sql = "UPDATE $quiz_questiontable
            SET $quiz_questioncolumn[weight]=$altseq
            WHERE $quiz_questioncolumn[quid]='".lnVarPrepForStore($quid)."'";
    $dbconn->Execute($sql);

	resequenceQuestions($qid);

    return true;
}
?>