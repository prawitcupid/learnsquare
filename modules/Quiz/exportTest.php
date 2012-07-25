<?php

if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

$vars= array_merge($_GET,$_POST);
extract($vars);

//sample
$getqid=$qid;

queryTest($getqid);

function queryTest($getqid) {

	$message='backup_quiz_learnsquare;';
	$quiz_messqge='tables=_quiz;'."\n";
	$quiz_test_messqge='tables=_quiz_test;'."\n";
	$quiz_multichoice_messqge='tables=_quiz_multichoice;'."\n";
	$quiz_choice_messqge='tables=_quiz_choice;'."\n";

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];
	$quiz_testtable = $lntable['quiz_test'];
	$quiz_testcolumn = &$lntable['quiz_test_column'];

	//seclect quiz
	$quiz_query = "SELECT $quizcolumn[qid],
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
	FROM $quiztable WHERE $quizcolumn[qid] =  '" . lnVarPrepForStore($getqid) . "'";

	$quiz_result = $dbconn->Execute($quiz_query);
	if ($quiz_result === false) die('Quiz Invalid query: ' . mysql_error());

	$quiz_numrows = $quiz_result->PO_RecordCount();
	if ($quiz_numrows > 0) {
		while(list($qid,$cid,$name,$intro,$attempts,$feedback,$correctanswers,$grademethod,$shufflequestions,$testtime,$grade,$assessment,$correctscore,$wrongscore,$noans,$difficulty,$difficultypriority) = $quiz_result->fields) {
			$quiz_result->MoveNext();
			//echo 'Quiz:: qid='.$qid.', cid='.$cid.', name='.$name.'<BR>';
			$quiz_messqge.=''.$qid.'||'.$cid.'||'.$name.'||'.$intro.'||'.$attempts.'||'.$feedback.'||'.$correctanswers.'||'.$grademethod.'||'.$shufflequestions.'||'.$testtime.'||'.$grade.'||'.$assessment.'||'.$correctscore.'||'.$wrongscore.'||'.$noans.'||'.$difficulty.'||'.$difficultypriority."\n";
				
			//select quiz_test
			$quiz_test_query = "SELECT $quiz_testcolumn[qid],
			$quiz_testcolumn[mcid],
			$quiz_testcolumn[weight]
			FROM $quiz_testtable WHERE $quiz_testcolumn[qid] =  '" . lnVarPrepForStore($qid) . "'";

			$quiz_test_result = $dbconn->Execute($quiz_test_query);
			if ($quiz_test_result === false) die('Quiz_Test Invalid query: ' . mysql_error());

			$quiz_test_numrows = $quiz_test_result->PO_RecordCount();
			if ($quiz_test_numrows > 0) {
				while(list($qid_qid,$mcid,$quizweight) = $quiz_test_result->fields) {
					$quiz_test_result->MoveNext();
					//echo 'Quiz_Test:: qid='.$qid.', mcid='.$mcid.'<BR>';
					$quiz_test_messqge.=''.$qid_qid.'||'.$mcid.'||'.$quizweight."\n";
				}
			}//end while quiz
			//get mcid
			$arrmcid = getQuizMember($qid);
			//echo '<pre>';
			//echo print_r($arrmcid);
			//echo '</pre>';exit();
			//echo 'count='.count($arrmcid);exit();
				
			for($i=0;$i<count($arrmcid);$i++){
				//-------------
				//seclect quiz_multichoice
				$quiz_multichoice_query = "SELECT $quiz_multichoicecolumn[mcid],
				$quiz_multichoicecolumn[uid],
				$quiz_multichoicecolumn[cid],
				$quiz_multichoicecolumn[question],
				$quiz_multichoicecolumn[answer],
				$quiz_multichoicecolumn[difficulty],
				$quiz_multichoicecolumn[type],
				$quiz_multichoicecolumn[keyword],
				$quiz_multichoicecolumn[share],
				$quiz_multichoicecolumn[guid]
					FROM $quiz_multichoicetable WHERE $quiz_multichoicecolumn[mcid] =  '" . lnVarPrepForStore($arrmcid[$i]) . "'";

				$quiz_multichoice_result = $dbconn->Execute($quiz_multichoice_query);
				if ($quiz_multichoice_result === false) die('Quiz_Multichoice Invalid query: ' . mysql_error());

				$quiz_multichoice_numrows = $quiz_multichoice_result->PO_RecordCount();
				if ($quiz_multichoice_numrows > 0) {
					while(list($mcid_mcid,$uid,$cid,$question,$mcidanswer,$mciddifficulty,$type,$keyword,$share,$guid) = $quiz_multichoice_result->fields) {
						$quiz_multichoice_result->MoveNext();
						//echo '>>Quiz_Multichoice:: mcid='.$mcid.', qid='.$qid.'<BR>';
						$quiz_multichoice_messqge.=''.$mcid_mcid.'||'.$uid.'||'.$cid.'||'.$question.'||'.$mcidanswer.'||'.$mciddifficulty.'||'.$type.'||'.$keyword.'||'.$share.'||'.$guid."\n";

						//seclect quiz_choice
						$quiz_choice_query = "SELECT $quiz_choicecolumn[chid],
						$quiz_choicecolumn[mcid],
						$quiz_choicecolumn[answer],
						$quiz_choicecolumn[feedback],
						$quiz_choicecolumn[weight]
								FROM $quiz_choicetable WHERE $quiz_choicecolumn[mcid] =  '" . lnVarPrepForStore($mcid_mcid) . "'";

						$quiz_choice_result = $dbconn->Execute($quiz_choice_query);
						if ($quiz_choice_result === false) die('Quiz_Choice Invalid query: ' . mysql_error());

						$quiz_choice_numrows = $quiz_choice_result->PO_RecordCount();
						if ($quiz_choice_numrows > 0) {
							while(list($chid,$chid_mcid,$chidanswer,$chidfeedback,$chidweight) = $quiz_choice_result->fields) {
								$quiz_choice_result->MoveNext();
								//echo '>>>>Quiz_Choice:: chid='.$chid.', mcid='.$mcid.'<BR>';
								$quiz_choice_messqge.=''.$chid.'||'.$chid_mcid.'||'.$chidanswer.'||'.$chidfeedback.'||'.$chidweight."\n";

							}//end while quiz_choice
						}
					}//end while quiz_multichoice
				}
				//--------------
			}
		}//end while quiz_test
	}

	//write test in file
	writeTestInFile($message, $quiz_messqge, $quiz_test_messqge, $quiz_multichoice_messqge, $quiz_choice_messqge);

}

function writeTestInFile($message, $quiz_messqge, $quiz_test_messqge, $quiz_multichoice_messqge, $quiz_choice_messqge) {
	//echo '<hr>writeTestInFile<hr>';

	$dir='courses/';
	$filename = 'bak_Test_'.date("Ymd").'.txt';
	$path=$dir.$filename;
	$somecontent = $message.$quiz_messqge.$quiz_test_messqge.$quiz_multichoice_messqge.$quiz_choice_messqge;

	if (!$handle = @fopen($path, 'w')) {
		echo "Cannot open file ($path)";
		exit;
	}
	if (fwrite($handle, $somecontent) === FALSE) {
		echo "Cannot write to file ($path)";
		exit;
	}
	fclose($handle);

	//export file save as
	exportTestFile($path,$filename);

}

function exportTestFile($path,$filename) {
	header("Pragma: public");
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Type: application/force-download");
	header("Content-Type: application/octet-stream");
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=".basename($filename).";");
	header("Content-Transfer-Encoding: binary ");
	header("Content-Length: ".filesize($path));
	readfile($path);
	unlink($path);

}

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
?>