<?php
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

$vars= array_merge($_GET,$_POST);
include ("lib/jpgraph/jpgraph.php");
include ("lib/jpgraph/jpgraph_bar.php");
$lesson_info = lnLessonGetVars($lid);
//print_r($lesson_info );
//$title = iconv('TIS-620', 'UTF-8',"รายงานแสดงค่าใช้จ่ายต่อเดือน");

set_time_limit(0);

$quiztable = $lntable['quiz'];
$quizcolumn = &$lntable['quiz_column'];
$lessonstable = $lntable['lessons'];
$lessonscolumn = &$lntable['lessons_column'];


$querygetqid =	"SELECT $quizcolumn[name],$lessonscolumn[title]
					FROM $quiztable,$lessonstable
					WHERE $quizcolumn[qid]= $lessonscolumn[file]
					AND $lessonscolumn[file]='".lnVarPrepForStore($lesson_info['file'])."'
					AND $lessonscolumn[lid]='".lnVarPrepForStore($lid)."'";
//echo $querygetqid."<br>";
$resultgetqid = $dbconn->Execute($querygetqid);
list($qidname,$lessonname) = $resultgetqid->fields;
//$total = getTotalScore($qid);
$title = _GRAPHNAME . ' ' .$lessonname;
$scorestable = $lntable['scores'];
$scorescolumn = &$lntable['scores_column'];

//*********************

$datay=array();
$datax=array();

$queryx  = "SELECT $scorescolumn[eid] FROM $scorestable ";
$queryx .= " WHERE  $scorescolumn[lid]='$lid' GROUP BY $scorescolumn[eid] ORDER BY $scorescolumn[score]";
$resultx = $dbconn->Execute($queryx);
//echo $queryx;

$j=0;
for ($i=1;list($eid) = $resultx->fields; $i++) {
	$resultx->MoveNext();
	
	//echo $eid." : ";
	$score = scoreHistory($eid,$lid);
	//echo $score;
	//echo "<br>";
	
	
	 if (in_array($score,$datax))
	 {
	 	$count[$score]++;
		$c[$j] = $count[$score];
		//echo $j.' : ';
		//echo $score.': '.$c[$j];
		//echo "<br>";
	 }		
	 else
	 {	 
	 	 $j++;
		 $datax[] = $score;
		 $count[$score] = 1;
		 $c[$j] = $count[$score];
		// echo $j.' : ';
		// echo $score.': '.$c[$j];
		// echo "<br>";
	 }
}
	
	for($m=1;$m<=$j;$m++)
	{
		//echo $c[$m];
		$datay[]=$c[$m];
	}
	

//***********************

/*
$query  = "SELECT $scorescolumn[score],count(*) FROM $scorestable ";
$query .= " WHERE  $scorescolumn[lid]='$lid' GROUP BY $scorescolumn[score] ORDER BY $scorescolumn[score] ";

//echo $query;
$result = $dbconn->Execute($query);
$datay=array();
$datax=array();
//$datay=array(1.13,0.25,0.21,0.35,0.31,0.06);
//$datax=array("Jan","Feb","Mar","Apr","May","June");

for ($i=1;list($score,$count) = $result->fields; $i++) {

		
		$result->MoveNext();

		$datay[] = $count;
		$datax[] = $score;
}
*/


// Setup the graph.
/*$graph = new Graph(800,600,"auto"); 
$graph->img->SetMargin(60,40,50,120);
$graph->SetScale("textlin");
$graph->SetMarginColor("lightblue");
$graph->SetShadow();
*/
$graph = new Graph(800,600,'auto');	
$graph->img->SetMargin(60,30,60,60);
$graph->SetScale("textint");
$graph->SetShadow();
$graph->SetFrame(true, 'blue', 10);

// Set up the title for the graph
$graph->title->Set($title);
$graph->title->SetFont(FF_TAHOMA, FS_BOLD, 12);
$graph->xaxis->title->SetFont(FF_TAHOMA,FS_BOLD);
//$graph->title->SetFont(FF_TAHOMA,FS_NORMAL,12);
//$graph->title->SetColor("blue");
$graph->legend->SetFont(FF_TAHOMA,FS_BOLD);
// Setup font for axis
$graph->xaxis->SetFont(FF_TAHOMA,FS_NORMAL,10);
$graph->xaxis->title->SetFont(FF_TAHOMA,FS_NORMAL,15);
$graph->xaxis->title->Set(_QUESTIONSCORE);
$graph->yaxis->SetFont(FF_TAHOMA,FS_NORMAL,10);
$graph->yaxis->title->SetFont(FF_TAHOMA,FS_NORMAL,15);
$graph->yaxis->title->Set("จำนวนคน");
// Show 0 label on Y-axis (default is not to show)
$graph->yscale->ticks->SupressZeroLabel(false);
$graph->yaxis->scale->SetGrace(10);
// Setup X-axis labels
sort($datax);
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetLabelAngle(0);
// Create the bar pot
$bplot = new BarPlot($datay);
$bplot->SetWidth(0.2);
// Setup color for gradient fill style
//$bplot->SetFillGradient("brown1","brown",GRAD_LEFT_REFLECTION);
// Set color for the frame of each bar
$bplot->SetColor("white");
$bplot->SetFillColor('orange');
$graph->Add($bplot);
$bplot->value->Show();
// Finally send the graph to the browser
$graph->Stroke();


function scoreHistory($eid,$lid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$scorestable = $lntable['scores'];
	$scorescolumn = &$lntable['scores_column'];

	//$eid = lnGetEnroll($cid);

	$query = "SELECT $scorescolumn[score]
	FROM $scorestable
	WHERE $scorescolumn[eid]='$eid' and $scorescolumn[lid]='$lid'
	ORDER BY	 $scorescolumn[quiz_time] ASC";
	$result = $dbconn->Execute($query);
	//echo $query;
	$rets = array();
	while(list($score) = $result->fields) {
		$result->MoveNext();
		$rets[]=' '.$score;
	}
	$ret = join(',',$rets);
	
	if (!empty($ret)) {
		$lesson_info = lnLessonGetVars($lid);
		$quiz_info = lnQuizGetVars($lesson_info['file']);
		
		if ($quiz_info['grademethod']==_LNQUIZ_GRADE_MAX) {
			$score=max($rets);
		}

		else if ($quiz_info['grademethod']==_LNQUIZ_GRADE_AVG) {
			for ($score_sum=0, $i=0; $i<count($rets); $i++) {
				$score_sum+=$rets[$i];
			}
			$score = $score_sum/count($rets);
		}

		else if ($quiz_info['grademethod']==_LNQUIZ_GRADE_LAST) {
			$score = $rets[count($rets)-1];
		}
		//hot potatoes
		else {
			for ($score_sum=0, $i=0; $i<count($rets); $i++) {
				$score_sum+=$rets[$i];
			}
			$score = $score_sum/count($rets);
		}

		//$score = sprintf("%2.2f", $score);
		$score = round($score,2);

		//$ret = ' <FONT SIZE="1" COLOR="#336600">คะแนน('.$score.'%) : '.$ret.' </FONT>';
		//$ret = ' คะแนน('.$score.'%) : '.$ret.' ';
	}

	//return $ret;
	return $score;
}

?>