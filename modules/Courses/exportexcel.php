<?php
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}
$vars= array_merge($_GET,$_POST);

$header = array();
$data = array();
$dataN = array();

$statuss = array('learning','complete','drop');

global $progress;
$progress=array();

listProgress($cid,0);

// show next page
$count = count($progress);

$header[] = "No.";
$header[] = _NICKNAME;
$header[] =_NAME;

for ($i=0; $i<=$count; $i++)
{
	if ($i <= $count)
	{
		$lesson_info = lnLessonGetVars($progress[$i]);
		$header[]= $lesson_info['no'];
	}
}

// check progress
list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();
$course_enrollstable = $lntable['course_enrolls'];
$course_enrollscolumn = &$lntable['course_enrolls_column'];

$query = "SELECT $course_enrollscolumn[uid],$course_enrollscolumn[status]
	FROM $course_enrollstable
	WHERE $course_enrollscolumn[sid]='$sid' ";

$result = $dbconn->Execute($query);

for ($i=1;list($uid, $options) = $result->fields; $i++)
{
	$result->MoveNext();
	if (lnCourseInstructor($sid) && (lnIsUserMentor($sid,$uid) ||  lnIsUserInstructor($sid)))
	{
		$user=lnUserGetVars($uid);
		$data[] = $i;
		$data[] = $user['uname'];
		if(trim($user['name']) == "")
			$data[] = " ";
		else
			$data[] = $user['name'];
			
		for($j=1; $j<=$count; $j++)
		{
			$lesson_info = lnLessonGetVars($progress[$j-1]);
			/*---------------- เพิ่ม check ข้อสอบว่า type = 2 หรือเปล่า -------------------*/
			if ($lesson_info['type'] == '1'  || $lesson_info['type']=='2') {
				$score = lnGetScore($uid,$progress[$j-1]);
				$quiztable = $lntable['quiz'];
				$quizcolumn = &$lntable['quiz_column'];
				$lessonstable = $lntable['lessons'];
				$lessonscolumn = &$lntable['lessons_column'];

				$eid =  lnGetEnrollID($uid,$sid);

				$querygetqid =	"SELECT $quizcolumn[qid]
					FROM $quiztable,$lessonstable
					WHERE $quizcolumn[qid]= $lessonscolumn[file]
					AND $lessonscolumn[file]='".lnVarPrepForStore($lesson_info['file'])."'
					AND $lessonscolumn[lid]='".lnVarPrepForStore($progress[$j-1])."'";
				//echo $querygetqid."<br>";
				$resultgetqid = $dbconn->Execute($querygetqid);
				list($qid) = $resultgetqid->fields;

			}

			$status = lnGetLearningStatus($uid,$progress[$j-1]);
			switch($status) {
				case _LNSTATUS_STUDY :  //define('_LNSTATUS_STUDY',1);
					$mesg = 'learning';
					$color = '#FFFF99';
					break;
				case _LNSTATUS_COMPLETE :
					$mesg = 'complete';
					$color = '#CCFFCC';
					break;
				case _LNSTATUS_DROP :
					$mesg = 'drop';
					$color = '#FF9999';
					break;
				case _LNSTATUS_FAIL :
					$mesg = 'fail';
					$color = '#99FFFF';
					break;
				default		:
					$mesg = '';
					$color = '#FFFFFF';
					break;
			}
			/*** type = 1 (ln2 quiz) and type = 2 (hotpotatoes quiz) *****/
			//if it is a quiz then show score
			if ($lesson_info['type']=='1' || $lesson_info['type']=='2') 
			{

				//recheck or examine the quiz score and show it on the screen ********************
				if ($action =='checkscores') //especially type == 1
				{
					$eid =  lnGetEnrollID($uid,$sid);
					//echo $eid;
					$score = lnCheckScores($eid,$progress[$j-1]);
				}


				$mesg = $score;


				//********************************************************************************
			}//end check quiz type ( 1 or 2 )



			if ($lesson_info['type']=='3')
			{
				$eid =  lnGetEnrollID($uid,$sid);

				$assignmenttable = $lntable['assignment'];
				$assignmentcolumn = &$lntable['assignment_column'];

				$query1 = "SELECT $assignmentcolumn[status], $assignmentcolumn[score] FROM $assignmenttable WHERE $assignmentcolumn[eid]=$eid AND $assignmentcolumn[lid]=".$lesson_info['lid']."";
				$result1 = $dbconn->Execute($query1);
				list ($ass_status,$ass_score) = $result1->fields;
				//echo $status;
				$lid = $lesson_info['lid'];

				//if($ass_status!='' )
				//echo $ass_status;

				if($ass_status!=NULL)
				{
					$mesg = "";
				}
				else if($status==_LNSTATUS_STUDY)
				{
					$mesg = "assignment";
				}

				if ($ass_status==1)
				{
					$mesg .= " [".$ass_score."%]";
				}

			} // end if ($lesson_info['type']=='3')

			$data[] = $mesg;
				
		}
		array_push($dataN, $data);
		unset($data);
		$data = array();
		  
	}
}
$cid_info = lnCourseGetVars($cid);
set_time_limit(0);
	/*echo "<PRE>";
	print_r($header);
	print_r($dataN);
	echo "</PRE>";
	exit();*/
$thead = "ผลการเรียน";

lnwriteexcel($thead,$header,$dataN);
//echo "complete";

function listProgress($cid,$parent) {
	global $progress;

	// get lesson to array lids
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$query = "SELECT $lessonscolumn[lid]
	FROM $lessonstable
	WHERE $lessonscolumn[cid]='". lnVarPrepForStore($cid) ."' AND $lessonscolumn[lid_parent]='$parent' ORDER BY $lessonscolumn[weight]";

	$result = $dbconn->Execute($query);

	for ($i=$from;list($lid) = $result->fields; $i++) {
		$result->MoveNext();
		$progress[]=$lid;

		listProgress($cid,$lid);
	}

}
?>