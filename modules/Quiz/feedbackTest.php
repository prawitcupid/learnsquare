<?php

function feedback($vars){
	extract($vars);
	feedbackQuiz($vars);
}

function getQuizTestMember($qid) {
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

function feedbackQuiz($vars){
	extract($vars);
	//echo'<pre>';print_r($vars);echo'</pre>';
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$quiztesttable = $lntable['quiz_test'];
	$quiztestColumn = &$lntable['quiz_test_column'];
	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];
	$uid = lnSessionGetVar('uid');

	$arrmcid = getQuizTestMember($qid);
	for($i=0;$i<count($arrmcid);$i++){
		$sql = "SELECT $multichoiceColumn[mcid],$multichoiceColumn[uid],$multichoiceColumn[question],$multichoiceColumn[answer],$multichoiceColumn[difficulty],$multichoiceColumn[type],$multichoiceColumn[share] 
		FROM $multichoice 
		WHERE $multichoiceColumn[mcid]='$arrmcid[$i]'";
		//echo $sql."<br>";
		$result = $dbconn->Execute($sql);
		list($mcid,$uid,$ques,$ans,$diff,$type,$key,$share) = $result->fields;
		//echo "$mcid,$uid,$ques,$ans,$diff,$type,$key,$share<br>";
		if($type == 1){
			$quiz[] = array($mcid,$ques,$ans,$diff,$type,$key,$share);
			$quizSection[] = $quiz;
			unset($quiz);
		}else{
			$quiz[] = array($mcid,$ques,$ans,$diff,$type,$key,$share);
		}
		if($ans == 0 && $diff == 0){
			//echo '555';
			$quizSection[] = $quiz;
			//print_r($quiz);
			//unset($quiz);
		}
		
	}
	//echo'<pre>';print_r($quiz);echo'</pre>';
	feedbackrmslashesextended($quizSection);
	//--html
	?>
<style>
<!--
a img {
	border: 0;
	margin: 2px;
}
-->
</style>
<div id="mainQuiz" style="width: 100%; margin: 0;">

<div id="dQList">
<table border="1px" cellspacing="0" width="100%" cellpadding="0">
<?
$quizNumber = 1;

$host = $_SERVER["HTTP_HOST"];
$path= str_replace('/index.php','',$_SERVER["SCRIPT_NAME"]);
$coursepath= COURSE_DIR . "/" .$cid;
$url= 'http://'.$host.$path.'/'.$coursepath ;

foreach ($quizSection as $qs) {
	echo "<tr><td>\n";
	echo '<div align="right"></div>';
	$qsCH = "";
	$qsText = "";
	foreach ($qs as $sq) {
		switch ($sq[4]) {
			case 1:
				$sq[1]=str_replace('\"','"',$sq[1]);
				$sq[1] = lnShowContent($sq[1],$url);
				
				$qsCH .= "<fieldset><legend>"._QUESTION_NUMB." ".$quizNumber."</legend>";
				$qsCH .= "<div><strong>"._QUESTION." :: </strong>".$sq[1]."</div>";//question
				$qsCH .= "<div>";
				$sql = "SELECT $quizChoiceColumn[answer],$quizChoiceColumn[feedback] FROM $quizChoice WHERE $quizChoiceColumn[mcid]='$sq[0]' ORDER BY $quizChoiceColumn[weight]";
				$result = $dbconn->Execute($sql);
				$j = 0;
				while (list($answer,$feedback) = feedbackrmslashesextended($result->fields)) {
					$result->MoveNext();
					
					$answer = str_replace('\"','"',$answer);
					$feedback = str_replace('\"','"',$feedback);
					$answer = lnShowContent($answer,$url);
					$feedback = lnShowContent($feedback,$url);
					
					$char = sprintf("%c",$j+65);
					if($sq[2] & pow(2,$j)){
						$qsCH .= $char.". <font color=#009900>".$answer." - "._FEEDBACKCHOICE." :: ".$feedback."</font><br/>";
						$j++;
					}else{
						$qsCH .= $char.". ".$answer." - "._FEEDBACKCHOICE." :: ".$feedback."<br/>";
						$j++;
					}
				}
				$qsCH .= "</div>";
				$qsCH .= feedbackCheckAnswer($eid, $lid, $qid, $sq[0], $sq[2]);
				$qsCH .= "</fieldset>";
				break;
					
			case 2:
				$sq[1] = str_replace('\"','"',$sq[1]);
				$sq[1] = lnShowContent($sq[1],$url);
				
				if($sq[2] == 0 && $sq[3] == 0){
					$qsText .= $sq[1];
					$quizNumber--;
				}else{
					$qsText .= $sq[1] . "<u> (ข้อที่ $quizNumber) </u>";
					$qsCH .= "<fieldset><legend>"._QUESTION_NUMB." ".$quizNumber."</legend>";
					$qsCH .= "<div>";
					$sql = "SELECT $quizChoiceColumn[answer],$quizChoiceColumn[feedback] FROM $quizChoice WHERE $quizChoiceColumn[mcid]='$sq[0]' ORDER BY $quizChoiceColumn[weight]";
					$result = $dbconn->Execute($sql);
					$j = 0;
					while (list($answer,$feedback) = feedbackrmslashesextended($result->fields)) {
						$result->MoveNext();
						
						$answer = str_replace('\"','"',$answer);
						$feedback = str_replace('\"','"',$feedback);
						$answer = lnShowContent($answer,$url);
						$feedback = lnShowContent($feedback,$url);
					
						$char = sprintf("%c",$j+65);
						if($sq[2] & pow(2,$j)){
							$qsCH .= $char.". <font color=#009900>".$answer." - "._FEEDBACKCHOICE." :: ".$feedback."</font><br/>";
							$j++;
						}else{
							$qsCH .= $char.". ".$answer." - "._FEEDBACKCHOICE." :: ".$feedback."<br/>";
							$j++;
						}
					}
					$qsCH .= "</div>";
					$qsCH .= feedbackCheckAnswer($eid, $lid, $qid, $sq[0], $sq[2]);
					$qsCH .= "</fieldset>";
				}
				break;
			case 3:
				$sq[1] = str_replace('\"','"',$sq[1]);
				$sq[1] = lnShowContent($sq[1],$url);
				
				if($sq[2] == 0 && $sq[3] == 0){
					$qsText = $sq[1];
					$quizNumber--;
				}else{
					$qsCH .= "<fieldset><legend>"._QUESTION_NUMB." ".$quizNumber."</legend>";
					$qsCH .= "<div><strong>"._QUESTION." :: </strong>".$sq[1]."</div>";//question
					$qsCH .= "<div>";
					$sql = "SELECT $quizChoiceColumn[answer],$quizChoiceColumn[feedback] FROM $quizChoice WHERE $quizChoiceColumn[mcid]='$sq[0]' ORDER BY $quizChoiceColumn[weight]";
					$result = $dbconn->Execute($sql);
					$j = 0;
					while (list($answer,$feedback) = ($result->fields)) {
						$result->MoveNext();
						
						$answer = str_replace('\\\"','"',$answer);
						$feedback = str_replace('\\\"','"',$feedback);
						$answer = str_replace('\"','"',$answer);
						$feedback = str_replace('\"','"',$feedback);
						$answer = lnShowContent($answer,$url);
						$feedback = lnShowContent($feedback,$url);
						
						$char = sprintf("%c",$j+65);
						if($sq[2] & pow(2,$j)){
							$qsCH .= $char.". <font color=#009900>".$answer." - "._FEEDBACKCHOICE." :: ".$feedback."</font><br/>";
							$j++;
						}else{
							$qsCH .= $char.". ".$answer." - "._FEEDBACKCHOICE." :: ".$feedback."<br/>";
							$j++;
						}
					}
					$qsCH .= "</div>";
					$qsCH .= feedbackCheckAnswer($eid, $lid, $qid, $sq[0], $sq[2]);
					$qsCH .= "</fieldset>";
				}
				//echo "<fieldset><legend>"._QUESTION."</legend>$qsText</fieldset>\n$qsCH";
				break;
		}
		$quizNumber++;
	}
	if($qsText != "")echo "<fieldset><legend>"._QUESTION."</legend>".stripslashes($qsText)."</fieldset>\n";

	echo stripslashes($qsCH);
	echo "<tr><td>\n";
}

?>
</table>
</div>
</div>

<?php
}

function feedbackrmslashesextended(&$arr_r)
{
	if(is_array($arr_r))
	{
		foreach ($arr_r as &$val)
		is_array($val) ? feedbackrmslashesextended($val):$val=stripslashes($val);
		unset($val);
	}
	else
	$arr_r=stripslashes($arr_r);
	return $arr_r;
}

function feedbackchoiceTypeChecker($answer){
	$count = 0;
	for ($i=0; $i<10; $i++) {
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

function feedbackCheckAnswer($eid, $lid, $qid, $mcid, $ans){
	
	if(!$eid)$eid=0;
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_answertable = $lntable['quiz_answer'];
	$quiz_answercolumn = &$lntable['quiz_answer_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	//get max_attemprs
	$query_attempts = "SELECT MAX($quiz_answercolumn[attempts])
	FROM $quiz_answertable 
	WHERE $quiz_answercolumn[eid]='$eid'
	AND $quiz_answercolumn[qid]='$qid'
	AND	$quiz_answercolumn[lid]='$lid'";
	$result_attempts = $dbconn->Execute($query_attempts);
	list($attempts) = $result_attempts->fields;

	$query_choice= "SELECT COUNT($quiz_choicecolumn[chid])
		FROM $quiz_choicetable  
		WHERE $quiz_choicecolumn[mcid]='$mcid'
		";
	//echo $query_choice;
	$result_choice = $dbconn->Execute($query_choice);
	list($choicelength) = $result_choice->fields;
	
	$query_student_score= "SELECT $quiz_answercolumn[useranswer]
		FROM $quiz_answertable  
		WHERE $quiz_answercolumn[mcid]='$mcid' 
		AND $quiz_answercolumn[eid]='$eid' 
		AND $quiz_answercolumn[attempts]='$attempts'
		AND	$quiz_answercolumn[qid]='$qid'
		AND	$quiz_answercolumn[lid]='$lid'
		";
	$result_student_score = $dbconn->Execute($query_student_score);
	list($useranswer) = $result_student_score->fields;
	
	for($i=0;$i<$choicelength;$i++){
		//$msg = ">>".$useranswer." - ".pow(2,$i)."<br>";
		if ($useranswer & pow(2,$i)) {
			
			$useranswers[] = sprintf("%c",$i+65);
		}
	}

	$show_choose = join(',',$useranswers);
	$msg .= '<B>'._YOURANSWER.': ('.$show_choose.') ' ;
	
	if($useranswer==$ans){
		$msg .= ' '._THATIS .' <IMG SRC="images/global/passed.gif" WIDTH="14" HEIGHT="12" BORDER="0" ALT=""> '._CORRECT.'</B>';
	}else{
		$msg .= ' '._THATIS.' <IMG SRC="images/global/wrong.gif" WIDTH="14" HEIGHT="12" BORDER="0" ALT=""> '._WRONG.'</B>';
	}
	return $msg ;

}
?>