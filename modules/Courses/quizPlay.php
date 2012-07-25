<?php
function getQuizMember($qid) {
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
	$arr_mcid = array();
	while(list($mcid,$type) = $result->fields){
		$result->MoveNext();
		$weight = count($arr_mcid) + 1;
		if($type == 1){
			$arr_mcid[] = array("mcid"=>$mcid,"type"=>$type,"weight"=>$weight);
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
				$quizmulti_COL[mcid] < '$stopMcid' ORDER BY 
				$quizmulti_COL[mcid] ASC";
		$mcidS = $dbconn->Execute($sql);
		while(list($mcidT) = $mcidS->fields){
			$mcidS->MoveNext();
			$arr_mcid[] = array("mcid"=>$mcidT,"type"=>$type,"head"=>$mcid,"foot"=>$stopMcid,"weight"=>$weight);
		}
	}
	return $arr_mcid;
}
function getHtmlChoices($mcid,$quiz_ansattempts,$eid,$shuffle,$qid,$lid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];
	$quiz_anstable = $lntable['quiz_answer'];
	$quiz_anscolumn = &$lntable['quiz_answer_column'];
	$quizmulti_TB = $lntable['quiz_multichoice'];
	$quizmulti_COL = &$lntable['quiz_multichoice_column'];
	
	$sql = "SELECT $quizmulti_COL[answer], $quizmulti_COL[cid] FROM 
				$quizmulti_TB WHERE
				$quizmulti_COL[mcid] = '$mcid'";
	$answer = $dbconn->Execute($sql);
	list($answer,$cid) = $answer->fields;
	
	$courseinfo = lnCourseGetVars($cid);
	$coursecode=$courseinfo['cid'];
	$url=COURSE_DIR.'/'.$coursecode;
	
	$query = "SELECT  $quiz_choicecolumn[chid],
			$quiz_choicecolumn[answer],
			$quiz_choicecolumn[feedback]
			FROM  $quiz_choicetable
			WHERE 	$quiz_choicecolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'
			ORDER BY $quiz_choicecolumn[weight]";
	$result2 = $dbconn->Execute($query);

	$query = "SELECT $quiz_anscolumn[useranswer]
			FROM  $quiz_anstable
			WHERE 	$quiz_anscolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'
			AND	$quiz_anscolumn[eid] =  '" . lnVarPrepForStore($eid) . "'
			AND	$quiz_anscolumn[attempts]='".lnVarPrepForStore($quiz_ansattempts)."'
			AND	$quiz_anscolumn[qid]='".lnVarPrepForStore($qid)."'
			AND	$quiz_anscolumn[lid]='".lnVarPrepForStore($lid)."'";

	$result = $dbconn->Execute($query);
	list($userans) = $result->fields;
	$i = 0;
	for($j=0; list($chid,$choice,$description) = rmslashesextended($result2->fields); $j++) {
		$result2->MoveNext();
		$choice = stripslashes($choice);
		//$choice = str_replace('/','',$choice);
		$choice = lnShowContent($choice,$url);
		if (checkChoiceType2($mcid,$answer) == 0) {
			$textchoice[$j] .= ' &nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE="radio" NAME="ans['.$mcid.'][0]" VALUE="'.pow(2,$j).'" ';
			//if(pow(2,$j)== $userans)
			if($userans & pow(2,$j))
			$textchoice[$j] .= 'checked="checked"';
			$textchoice[$j] .= '>'. $choice.'<br/>';
			
			$i=0;
		}
		else {
			$textchoice[$j] .= ' &nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE="checkbox" NAME="ans['.$mcid.']['.$j.']" VALUE="'.pow(2,$j).'" ';
			//if(pow(2,$j)== $userans)
			if($userans & pow(2,$j))
			$textchoice[$j] .= 'checked="checked"';
			$textchoice[$j] .= '>'. $choice.'<br/>';
			
			$i=1;
		}
	}
	if(!empty($shuffle)){
		shuffle($textchoice);
	}
	if($i==0){
		$temp .= '<br/> &nbsp;&nbsp;&nbsp;&nbsp;<INPUT TYPE="radio" NAME="ans['.$mcid.'][0]" VALUE="0"';
		if($userans== 0 && $userans!=null)$temp .= 'checked="checked"';
		$temp .= '>'._NOANS.'<br/>';
		$textchoice[] = $temp;
	}
	return implode("",$textchoice) . "<br/>";
}
function getInterrogativeSentence($mcid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();	
	$quizmulti_TB = $lntable['quiz_multichoice'];
	$quizmulti_COL = &$lntable['quiz_multichoice_column'];
	
	$sql = "SELECT $quizmulti_COL[question] FROM 
				$quizmulti_TB WHERE
				$quizmulti_COL[mcid] = '$mcid'";
		$return = $dbconn->Execute($sql);
		list($return) = $return->fields;	
	return rmslashesextended($return);
}
function getUserAnswer($mcid,$eid,$attemp,$qid,$lid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();	
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];
	$quiz_anstable = $lntable['quiz_answer'];
	$quiz_anscolumn = &$lntable['quiz_answer_column'];
	if ($eid == '')$eid = 0;
	if ($attemp == '')$attemp = 0;
	$query = "SELECT $quiz_anscolumn[useranswer]
				FROM  $quiz_anstable
				WHERE 	$quiz_anscolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'
				AND	$quiz_anscolumn[eid] =  '" . lnVarPrepForStore($eid) . "'
				AND	$quiz_anscolumn[attempts]='".lnVarPrepForStore($attemp)."'
				AND	$quiz_anscolumn[qid]='".lnVarPrepForStore($qid)."'
				AND	$quiz_anscolumn[lid]='".lnVarPrepForStore($lid)."'";	
	$result3 = $dbconn->Execute($query);
	list($userans) = $result3->fields;
	$query = "SELECT 	$quiz_choicecolumn[answer]
					FROM  $quiz_choicetable
					WHERE 	$quiz_choicecolumn[mcid] =  '" . lnVarPrepForStore($mcid) . "'
					ORDER BY $quiz_choicecolumn[weight]";
	$result2 = $dbconn->Execute($query);
	for($j=0; list($choice) = $result2->fields; $j++){
		$result2->MoveNext();
		$choice = stripslashes($choice);
		$choice = lnShowContent($choice,$url);
		$a_choice[$j] = $choice;
	}
	if($userans > 0){
		$return = $a_choice[(log($userans)/log(2))];
	}else{
		$return = "";
	}
	return $return;
}
function checkChoiceType2($quid, $answer) {
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
function rmslashesextended(&$arr_r)
{
    if(is_array($arr_r))
    {
        foreach ($arr_r as &$val)
            is_array($val) ? rmslashesextended($val):$val=stripslashes($val);
        unset($val);
    }
    else
        $arr_r=stripslashes($arr_r);
    return $arr_r;
}
?>
