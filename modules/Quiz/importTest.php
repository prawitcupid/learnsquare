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
echo "<pre>";
//print_r($vars);
echo "</pre>";

$filetest = $_FILES['filetest']['tmp_name'];
if(!empty($filetest)){
	readTest($vars); return;
}

echo '<FORM METHOD=POST ENCTYPE="multipart/form-data" ACTION="index.php">'
.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
.'<INPUT TYPE="hidden" NAME="op" VALUE="quiz">'
.'<INPUT TYPE="hidden" NAME="action" VALUE="importtest">'
.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
.'<INPUT TYPE="hidden" NAME="uid" VALUE="'.lnSessionGetVar('uid').'">';
echo '<P><B>'._IMPORTTEST.'</B></P><br>';
echo ''._FILEUPLOADIMPORT.'::';
echo '<input id="fileupload" name="filetest" type="file" >&nbsp;';
echo '<input id="btnUpload" type="submit" value="'._IMPORTUPLOAD.'">';
echo '<P><FONT COLOR="RED">'._IMPORTNOTE.'</FONT></P>';
echo '</FORM>';

//Read File Test
function readTest($vars) {
	extract($vars);

	//echo "readTest<hr>";
	$filename = $_FILES['filetest']['name'];
	$filetype = explode(".", $filename);
	//check error file
	if($filetype[(sizeof($filetype)-1)]!='txt'){
		echo "Invalid File Type Exit";exit();
	}
	//read file
	$filename = $_FILES['filetest']['tmp_name'];
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle);

	$table = explode("tables=", $contents);
	//check error data
	if($table[0]!="backup_quiz_learnsquare;"){
		echo "Invalid Data in File Exit";exit();
	}

	for($i=1;$i<sizeof($table);$i++){

		$data = explode(";\n", $table[$i]);
		$tablename=$data[0];
		//echo "table=".$tablename."<br>";

		$record = explode("\n", $data[1]);
		for($j=0;$j<sizeof($record);$j++){

			if($record[$j]!=''){
				//echo "record=".$record[$j]."<br>";
				$value = explode("||", $record[$j]);
				//quiz
				if($tablename=="_quiz"){
					//echo "add array quiz<br>";
					$quiz[$j]['ln_qid']= $value[0];
					$quiz[$j]['ln_cid']= $value[1];
					$quiz[$j]['ln_name']= $value[2];
					$quiz[$j]['ln_intro']= $value[3];
					$quiz[$j]['ln_attempts']= $value[4];
					$quiz[$j]['ln_feedback']= $value[5];
					$quiz[$j]['ln_correctanswers']= $value[6];
					$quiz[$j]['ln_grademethod']= $value[7];
					$quiz[$j]['ln_shufflequestions']= $value[8];
					$quiz[$j]['ln_testtime']= $value[9];
					$quiz[$j]['ln_grade']= $value[10];
					$quiz[$j]['ln_assessment']= $value[11];
					$quiz[$j]['ln_correctscore']= $value[12];
					$quiz[$j]['ln_wrongscore']= $value[13];
					$quiz[$j]['ln_noans']= $value[14];
					$quiz[$j]['ln_difficulty']= $value[15];
					$quiz[$j]['ln_difficultypriority']= $value[16];

					//quiz_multichoice
				}else if($tablename=="_quiz_multichoice"){
					//echo "add array quiz_multichoice<br>";
					$quiz_multichoice[$j]['ln_mcid']= $value[0];
					$quiz_multichoice[$j]['ln_uid']= $value[1] ;
					$quiz_multichoice[$j]['ln_cid']= $value[2] ;
					$quiz_multichoice[$j]['ln_question']= $value[3] ;
					$quiz_multichoice[$j]['ln_answer']= $value[4] ;
					$quiz_multichoice[$j]['ln_difficulty']= $value[5] ;
					$quiz_multichoice[$j]['ln_type']= $value[6] ;
					$quiz_multichoice[$j]['ln_keyword']= $value[7] ;
					$quiz_multichoice[$j]['ln_share']= $value[8];
					$quiz_multichoice[$j]['ln_guid']= $value[9];

					//quiz_choice
				}else if($tablename=="_quiz_choice"){
					//echo "add array quiz_choice<br>";
					$quiz_choice[$j]['ln_chid']= $value[0];
					$quiz_choice[$j]['ln_mcid']= $value[1];
					$quiz_choice[$j]['ln_answer']= $value[2];
					$quiz_choice[$j]['ln_feedback']= $value[3];
					$quiz_choice[$j]['ln_weight']= $value[4];

					//quiz_test
				}else if($tablename=="_quiz_test"){
					//echo "add array quiz_test<br>";
					$quiz_test[$j]['ln_qid']= $value[0];
					$quiz_test[$j]['ln_mcid']= $value[1];
					$quiz_test[$j]['ln_weight']= $value[2];
				}

			}
		}
		//echo '<hr>';
	}

	//update Test
	//echo '<pre>'.print_r($quiz).'</pre>';
	//echo '<pre>'.print_r($quiz_multichoice).'</pre>';
	//echo '<pre>'.print_r($quiz_choice).'</pre>';
	//echo '<pre>'.print_r($quiz_test).'</pre>';
	importTest($uid, $cid, $quiz, $quiz_multichoice, $quiz_choice, $quiz_test);

}

function importTest($uid, $cid, $quiz, $quiz_multichoice, $quiz_choice, $quiz_test) {
	//echo "importTest:uid=$uid:cid=$cid<hr>";
	//echo '<pre>'; print_r($quiz_multichoice); echo '</pre>';

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

	//for($i=0;$i<sizeof($quiz);$i++){
	$old_qid = $quiz[$i]['ln_qid'];
	$new_qid = getMaxQID();
	//echo "new_qid=$new_qid<br>";

	//update quiz > new_qid, cid, ...
	$quiz_query = "INSERT INTO $quiztable
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
		VALUES ( '".$new_qid."',
		'".$cid."',
		'".$quiz[0]['ln_name']."',
		'".$quiz[0]['ln_intro']."',
		'".$quiz[0]['ln_attempts']."',
		'".$quiz[0]['ln_feedback']."',
		'".$quiz[0]['ln_correctanswers']."',
		'".$quiz[0]['ln_grademethod']."',
		'".$quiz[0]['ln_shufflequestions']."',
		'".$quiz[0]['ln_testtime']."',
		'".$quiz[0]['ln_grade']."',
		'".$quiz[0]['ln_assessment']."',
		'".$quiz[0]['ln_correctscore']."',
		'".$quiz[0]['ln_wrongscore']."',
		'".$quiz[0]['ln_noans']."',
		'".$quiz[0]['ln_difficulty']."',
		'".$quiz[0]['ln_difficultypriority']."'
		)";	
		//echo '<pre>'.$quiz_query.'</pre>';
		$quiz_result=$dbconn->Execute($quiz_query);
		if ($quiz_result === false) die('Quiz Invalid query: ' . mysql_error());

		echo '<P><B>'._IMPORTTEST.'</B></P><br>';
		//check quiz_test new qid
		for($k=0;$k<sizeof($quiz_multichoice);$k++){
			//for($j=0;$j<sizeof($quiz_test);$j++){
				
			$old_mcid = $quiz_multichoice[$k]['ln_mcid'];
			$new_mcid = getMaxMCID();
			//echo 'old_mcid='.$old_mcid.'<br>';
			//echo 'new_mcid='.$new_mcid.'<br>';
			//echo 'k='.$k.'<br>';
			//check quiz_multichoice new mcid

			//check guid if duplicate quiz
			$guid = $quiz_multichoice[$k]['ln_guid'];
			$quiz_multichoice_query = "SELECT $quiz_multichoicecolumn[mcid]
					FROM $quiz_multichoicetable 
					WHERE $quiz_multichoicecolumn[guid] =  '".$guid."'";
			$quiz_multichoice_result = $dbconn->Execute($quiz_multichoice_query);
			$mcid = $quiz_multichoice_result->fields[0];
			//echo $quiz_multichoice_query.'<br>';
			if(empty($mcid)){
				//echo 'No Quiz duplicate<br>';
			}else{
				$new_mcid = $mcid;
				//echo 'Quiz duplicate Replace exits quiz<br>';
			}
				
			for($j=0;$j<sizeof($quiz_test);$j++){
				//for($k=0;$k<sizeof($quiz_multichoice);$k++){
				//echo "check quiz_test mcid =>".$quiz_test[$j]['ln_mcid']." <br>";
				//echo "qid =>".$quiz_test[$j]['ln_qid']." <br>";
				//if($quiz_test[$j]['ln_qid']==$old_qid){
				//echo "check quiz_test qid<br>";
					
				if($quiz_test[$j]['ln_mcid']==$old_mcid){

					//echo "new_mcid=$new_mcid<br>";
					//echo "Insert<br>";
					//update quiz_test > new_qid,new_mcid
					//
					$quiz_test_query = "INSERT INTO $quiz_testtable
						(	$quiz_testcolumn[qid],
						$quiz_testcolumn[mcid],
						$quiz_testcolumn[weight]
						)
						VALUES ( '".$new_qid."',
						'".$new_mcid."',
						'".$quiz_test[$j]['ln_weight']."'
						)";
						$quiz_test_result = $dbconn->Execute($quiz_test_query);
						if ($quiz_test_result === false) die('Test Invalid query: ' . mysql_error());
						//echo '<pre>'.$quiz_test_query.'</pre>';
				}
				//}//End For check quiz_multichoice new mcid
			}
			
			//start check duplicate quiz_multichoice
			if(empty($mcid)){
				echo $quiz_multichoice[$k]['ln_question'].'::'._NOTDUPLICATE.'<br>';
				//update quiz_multichoice > new_mcid, uid, cid
				//
				$quiz_multichoice_query = "INSERT INTO $quiz_multichoicetable
						(	$quiz_multichoicecolumn[mcid],
						$quiz_multichoicecolumn[uid],
						$quiz_multichoicecolumn[cid],
						$quiz_multichoicecolumn[question],
						$quiz_multichoicecolumn[answer],
						$quiz_multichoicecolumn[difficulty],
						$quiz_multichoicecolumn[type],
						$quiz_multichoicecolumn[keyword],
						$quiz_multichoicecolumn[share],
						$quiz_multichoicecolumn[guid]
						)
						VALUES ( '".$new_mcid."',
						'".$uid."',
						'".$cid."',
						'".$quiz_multichoice[$k]['ln_question']."',
						'".$quiz_multichoice[$k]['ln_answer']."',
						'".$quiz_multichoice[$k]['ln_difficulty']."',
						'".$quiz_multichoice[$k]['ln_type']."',
						'".$quiz_multichoice[$k]['ln_keyword']."',
						'".$quiz_multichoice[$k]['ln_share']."',
						'".$quiz_multichoice[$k]['ln_guid']."'
						)";
						//echo '<pre>'.$quiz_multichoice_query.'</pre>';
						$quiz_multichoice_result = $dbconn->Execute($quiz_multichoice_query);
						if ($quiz_multichoice_result === false) die('Multichoice Invalid query: ' . mysql_error());
						//echo '<pre>'.$quiz_multichoice_query.'</pre>';

						//check quiz_choice new chid
						for($l=0;$l<sizeof($quiz_choice);$l++){
							if($quiz_choice[$l]['ln_mcid']==$old_mcid){
								$new_chid = getMaxCHID();
								//echo "new_chid=$new_chid<br>";

								//update quiz_choice > new_chid
								//
								$quiz_choice_query = "INSERT INTO $quiz_choicetable
								(	$quiz_choicecolumn[chid],
								$quiz_choicecolumn[mcid],
								$quiz_choicecolumn[answer],
								$quiz_choicecolumn[feedback],
								$quiz_choicecolumn[weight]
								)
								VALUES ( '".$new_chid."',
								'".$new_mcid."',
								'".$quiz_choice[$l]['ln_answer']."',
								'".$quiz_choice[$l]['ln_feedback']."',
								'".$quiz_choice[$l]['ln_weight']."'
								)";
								$quiz_choice_result = $dbconn->Execute($quiz_choice_query);
								if ($quiz_choice_result === false) die('Choice Invalid query: ' . mysql_error());
								//echo '<pre>'.$quiz_choice_query.'</pre>';

							}
						}//End For check quiz_choice new chid
			}else{
				echo $quiz_multichoice[$k]['ln_question'].'::'._DUPLICATE.'<br>';
			}	//end

		}//End For check quiz_test new qid

		//}
		
		echo '<br><P><B>'._IMPORTFINISH.'!!</B></P><br>';
}

function getMaxQID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];

	$result = $dbconn->Execute("SELECT MAX($quizcolumn[qid]) FROM $quiztable");
	list($max_qid) = $result->fields;

	return $max_qid + 1;
}

function getMaxMCID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_multichoicetable = $lntable['quiz_multichoice'];
	$quiz_multichoicecolumn = &$lntable['quiz_multichoice_column'];

	$result = $dbconn->Execute("SELECT MAX($quiz_multichoicecolumn[mcid]) FROM $quiz_multichoicetable");
	list($max_mcid) = $result->fields;

	return $max_mcid + 1;
}

function getMaxCHID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	$result = $dbconn->Execute("SELECT MAX($quiz_choicecolumn[chid]) FROM $quiz_choicetable");
	list($max_chid) = $result->fields;

	return $max_chid + 1;
}

?>