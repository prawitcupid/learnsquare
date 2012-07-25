<?php
if(isset($_POST['save'])){
	//print_r($_POST);exit();
}
/**
 *  HTML editor Module
 */
/*
 * SPAW Editor v.2 Main include file
 *
 * Include this file in your script
 *
 * @package spaw2
 * @author Alan Mendelevich <alan@solmetra.lt>
 * @copyright UAB Solmetra
 * @edit by nay 23/03/2007
 */

if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'spaw::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

$host = $_SERVER["HTTP_HOST"];
$path= str_replace('/index.php','',$_SERVER["SCRIPT_NAME"]);


if (!empty($type)) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$blockstable=$lntable['blocks'];
	$blockscolumn = &$lntable['blocks_column'];


	// Edit Block
	if ($type == "Blocks") {
		// find upload picture directory
		$picdir = LN_BLOCK_IMAGE_DIR;
		$url= 'http://'.$host.$path.'/'. $picdir;
		$result = $dbconn->Execute("SELECT $blockscolumn[content] FROM  $blockstable WHERE $blockscolumn[bid]='".lnVarPrepForStore($bid)."'");
		if ($result->EOF) {
			return false;
		}
		
		//$strHTML = $result->fields[0];
		$strHTML = stripslashes($result->fields[0]);
		$strHTML = lnShowContent($strHTML,$url);//edit by nay 14/09/2007

		if (!empty($_POST['text'])) {
			$message =$_POST['text'];
			$message=stripslashes($message);
			$message = str_replace($url.'/','',$message);
			$message=addslashes($message);
			$message = str_replace($path.'/'. $picdir.'/','',$message);//edit by nay 20/03/2007
			$result = $dbconn->Execute("UPDATE $blockstable SET $blockscolumn[content] = '".$message."' WHERE $blockscolumn[bid]='".lnVarPrepForStore($bid)."'");
			echo "<SCRIPT language=JavaScript>window.close();</SCRIPT>";
		}
	}

	// Edit Lesson
	else if ($type == "Courses") {
		$coursepath= COURSE_DIR . "/" .$cid;
		//$picdir=COURSE_DIR . '/' .$cid . '/images';
		$url= 'http://'.$host.$path.'/'.$coursepath ;
		$lessonstable = $lntable['lessons'];
		$lessonscolumn = &$lntable['lessons_column'];
		$result = $dbconn->Execute("SELECT $lessonscolumn[file] FROM $lessonstable WHERE $lessonscolumn[lid]='". lnVarPrepForStore($lid) ."'");
		list($lessonfile) = $result->fields;
		$lessonfile = $coursepath .'/'. $lessonfile;
		if (!empty($_POST['text'])) {
			$message =$_POST['text'];
			$message=stripslashes($message);
			$message = str_replace($url.'/','',$message);
			$message = str_replace($path.'/'.$coursepath.'/','',$message);//edit by nay 20/03/2007
			/*
			if (!preg_match('/<!--RICHEDIT-->/i',$message)) {
				$message .= '<!--RICHEDIT-->';
			}
			*/
			$fp=fopen($lessonfile,"w");
			fwrite($fp,$message);
			fclose($fp);
			echo "<SCRIPT language=JavaScript>window.close();</SCRIPT>";
		}
		if (file_exists($lessonfile)) {
			$fp=fopen($lessonfile,"r");
			$strHTML=fread($fp,filesize($lessonfile));
			//echo strlen($strHTML);
			$strHTML = lnShowContent($strHTML,$url);
		}
	}

	// Edit Quiz
	else if ($type == "Quiz") {
		$coursepath=COURSE_DIR.'/'.$cid;
		//$picdir=COURSE_DIR . '/' .$cid . '/images';
		$url= 'http://'.$host.$path.'/'.$coursepath ;
		if (empty($_POST['text'])) {
			$quiztable = $lntable['quiz'];
			$quizcolumn = &$lntable['quiz_column'];
			$result = $dbconn->Execute("SELECT $quizcolumn[intro] FROM $quiztable WHERE $quizcolumn[qid]='". lnVarPrepForStore($qid) ."'");
			list($quiz_desc) = $result->fields;
			if (empty($quiz_desc)) {
				$quiz_desc = _DEFAULTQUIZTITLE;
			}
			$quiz_desc=stripslashes($quiz_desc);
			$quiz_desc=nl2br($quiz_desc);
			$quiz_desc=str_replace('\\"','"',$quiz_desc); //fix by narananami 2011/05/27
			$strHTML =$quiz_desc;
			$strHTML = lnShowContent($strHTML,$url);
		}
		else  {
			$message =$_POST['text'];
			$message=str_replace("\r\n","",$message);
			$message = str_replace($url.'/','',$message);
			$message=str_replace("<BR>","\\r\\n",$message);
			$message=addslashes($message);
			$message=str_replace('"',"\"",$message);
			$message = str_replace($path.'/'.$coursepath.'/','',$message);//edit by nay 20/03/2007
			?>
<SCRIPT language=JavaScript>
                    window.opener.document.quizform.quiz_desc.value='<?=$message?>'
                    window.close();
            </SCRIPT>
			<?
			//echo $message;
			return;
		}
	}

	// Edit Question
	else if ($type == "Question") {
		$coursepath=COURSE_DIR.'/'.$cid;
		//$picdir=COURSE_DIR . '/' .$cid . '/images';
		$url= 'http://'.$host.$path.'/'.$coursepath ;
		//$url= 'http://'.$host.$path;
		//echo 'url='.$url;echo '<br>host='.$host;echo '<br>path='.$path;echo '<br>coursepath='.$coursepath;echo '<br>picdir='.$picdir;
		if (empty($_POST['text'])) {
			//echo "<br>empty Text";
			$quiz_questiontable = $lntable['quiz_multichoice'];
			$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];
			$result = $dbconn->Execute("SELECT $quiz_questioncolumn[question] FROM $quiz_questiontable WHERE $quiz_questioncolumn[mcid]='". lnVarPrepForStore($mcid) ."'");
			list($question) = $result->fields;
			$question=stripslashes($question);
			$question=nl2br($question);
			$question=str_replace('\\"','"',$question); //fix by narananami 2011/05/27
			$strHTML =$question;
			$strHTML = lnShowContent($strHTML,$url);
		}
		else  {
			//echo "<br>Text";
			$message =$_POST['text'];
			$message=str_replace("\r\n","",$message);
			$message = str_replace($url.'/','',$message);
			$message=str_replace("<BR>","\\r\\n",$message);
			$message=addslashes($message);
			$message = str_replace($path.'/'.$coursepath.'/','',$message);//edit by nay 20/03/2007

			?>
<SCRIPT language=JavaScript>
                    window.opener.document.quizform.quizContent.value='<?=$message?>'
                    window.close();
                </SCRIPT>
			<?
		}
	}

	// Edit Choice
	else if ($type == "Choice") {
		$coursepath=COURSE_DIR.'/'.$cid;
		//$picdir=COURSE_DIR . '/' .$cid . '/images';
		$url= 'http://'.$host.$path.'/'.$coursepath ;
		if (empty($_POST['text'])) {
			$quiz_choicetable = $lntable['quiz_choice'];
			$quiz_choicecolumn = &$lntable['quiz_choice_column'];
			$result = $dbconn->Execute("SELECT $quiz_choicecolumn[answer] FROM $quiz_choicetable WHERE $quiz_choicecolumn[chid]='". lnVarPrepForStore($chid) ."'");
			list($choice) = $result->fields;
			$choice=stripslashes($choice);
			$choice=nl2br($choice);
			$choice=str_replace('\\"','"',$choice); //fix by narananami 2011/05/27
			$strHTML =$choice;
			$strHTML = lnShowContent($strHTML,$url);
		}
		else  {

			$message =$_POST['text'];
			$message=str_replace("\r\n","",$message);
			$message = str_replace($url.'/','',$message);
			$message=str_replace("<BR>","\\r\\n",$message);
			$message=addslashes($message);
			$message = str_replace($path.'/'.$coursepath.'/','',$message);//edit by nay 20/03/2007

			?>
<SCRIPT language=JavaScript>
                    var i = <?php echo $_GET['y']; ?>;
                    window.opener.document.getElementsByName("textCh<?php echo $_GET['x'];?>[]")[i].value='<?=$message?>';
                    window.close();
                </SCRIPT>
			<?
		}
	}
	
// Edit MultiQuestion
	else if ($type == "mqQuestion") {
		$coursepath=COURSE_DIR.'/'.$cid;
		//$picdir=COURSE_DIR . '/' .$cid . '/images';
		$url= 'http://'.$host.$path.'/'.$coursepath ;
		if (empty($_POST['text'])) {
			$quiz_questiontable = $lntable['quiz_multichoice'];
			$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];
			$result = $dbconn->Execute("SELECT $quiz_questioncolumn[question] FROM $quiz_questiontable WHERE $quiz_questioncolumn[mcid]='". lnVarPrepForStore($mcid) ."'");
			list($question) = $result->fields;
			$question=stripslashes($question);
			$question=nl2br($question);
			$question=str_replace('\\"','"',$question); //fix by narananami 2011/05/27
			$strHTML =$question;
			$strHTML = lnShowContent($strHTML,$url);
		}
		else  {
			$message =$_POST['text'];
			$message=str_replace("\r\n","",$message);
			$message = str_replace($url.'/','',$message);
			$message=str_replace("<BR>","\\r\\n",$message);
			$message=addslashes($message);
			$message = str_replace($path.'/'.$coursepath.'/','',$message);//edit by nay 20/03/2007
			?>
<SCRIPT language=JavaScript>
                    var i = <?php echo $_GET['x']; ?>;
                    window.opener.document.getElementsByName("mqQuestion[]")[i].value='<?=$message?>';
                    window.close();
                </SCRIPT>
			<?
		}
	}
// Edit MultiQuestionText
	else if ($type == "mqText") {
		$coursepath=COURSE_DIR.'/'.$cid;
		//$picdir=COURSE_DIR . '/' .$cid . '/images';
		$url= 'http://'.$host.$path.'/'.$coursepath ;
		if (empty($_POST['text'])) {
			$quiz_questiontable = $lntable['quiz_multichoice'];
			$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];
			$result = $dbconn->Execute("SELECT $quiz_questioncolumn[question] FROM $quiz_questiontable WHERE $quiz_questioncolumn[mcid]='". lnVarPrepForStore($mcid) ."'");
			list($question) = $result->fields;
			$question=stripslashes($question);
			$question=nl2br($question);
			$question=str_replace('\\"','"',$question); //fix by narananami 2011/05/27
			$strHTML =$question;
			$strHTML = lnShowContent($strHTML,$url);
		}
		else  {
			$message =$_POST['text'];
			$message=str_replace("\r\n","",$message);
			$message = str_replace($url.'/','',$message);
			$message=str_replace("<BR>","\\r\\n",$message);
			$message=addslashes($message);
			$message = str_replace($path.'/'.$coursepath.'/','',$message);//edit by nay 20/03/2007
			?>
<SCRIPT language=JavaScript>
                    window.opener.document.quizform.mqText.value='<?=$message?>';
                    //alert(window.opener.document.getElementsByName("mqText").value='<?=$message?>');
                    //window.opener.document.quizform.mqState2.mqText.value='<?=$message?>';
                    window.close();
                </SCRIPT>
			<?
		}
	}
}


?>
<HTML>
<HEAD>
<TITLE>HTML Editor - [<?=$type?>]</TITLE>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
</HEAD>

<?
function fixtags($text){
$text = htmlspecialchars($text);
$text = preg_replace("/=/", "=\"\"", $text);
$text = preg_replace("/&quot;/", "&quot;\"", $text);
$tags = "/&lt;(\/|)(\w*)(\ |)(\w*)([\\\=]*)(?|(\")\"&quot;\"|)(?|(.*)?&quot;(\")|)([\ ]?)(\/|)&gt;/i";
$replacement = "<$1$2$3$4$5$6$7$8$9$10>";
$text = preg_replace($tags, $replacement, $text);
$text = preg_replace("/=\"\"/", "=", $text);
return $text;
}


// find upload picture directory
function  upCoursePics($cid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
	$result = $dbconn->Execute("SELECT $coursescolumn[code] FROM $coursestable WHERE $coursescolumn[cid]='". lnVarPrepForStore($cid) ."'");
	list($coursecode) = $result->fields;
	$picdir=COURSE_DIR . '/' .$coursecode . '/images';

	return $picdir;
}
// find course path
function  coursePath($cid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
	$result = $dbconn->Execute("SELECT $coursescolumn[code] FROM $coursestable WHERE $coursescolumn[cid]='". lnVarPrepForStore($cid) ."'");
	list($coursecode,$coursename) = $result->fields;
	$coursepath= COURSE_DIR . "/" .$coursecode;

	return $coursepath;
}
?>
<BODY leftMargin=0 topMargin=0 scroll="no" style="border: 0"
	bgcolor="#FFFFFF">
<form method="post">
<?php
if($type != "Blocks")
{	//echo dirname($_SERVER["SCRIPT_NAME"]);
	$_cid1 = $_GET['cid'];
	$lnpath = preg_split("/\//",substr(dirname($_SERVER["SCRIPT_NAME"]),1));
	//echo "<pre>".print_r($lnpath)."</pre>";
	for($i = 0;$i < count($lnpath);$i++)
	{
		if($i!=0){
			//$tmp .= '/'.$lnpath[$i];
			$lnpath[0] .='/'.$lnpath[$i];
		}
	}
	$pathpic = trim('/'.$lnpath[0].'/courses/'.$_cid1.'/');
	
	//$picdir=COURSE_DIR . '/' .$cid . '/images';
	//echo "pathpic=".$pathpic;
	//$pathpic = trim('/'.$tmp.'/courses/'.$_cid1.'/');
}
else
{
	$lnpath = preg_split("/\//",substr(dirname($_SERVER["SCRIPT_NAME"]),1));
	for($i = 0;$i < count($lnpath);$i++)
	{
		if($i!=0){
			//$tmp .= '/'.$lnpath[$i];
			$lnpath[0] .='/'.$lnpath[$i];
		}
	}

	$_cid1 = LN_BLOCK_IMAGE_DIR;
	$pathpic = trim('/'.$lnpath[0].'/'.$_cid1.'/');
	//$pathpic = trim('/'.$tmp.'/'.$_cid1.'/');
}

if ($type == "Choice" || $type == "mqQuestion") {
	echo '<input type="hidden" id="x" name="x" value="'.$_GET['x'].'">';
	echo '<input type="hidden" id="y" name="y" value="'.$_GET['y'].'">';
}

include("modules/spaw/spaw.inc.php");
$spaw = new SpawEditor("text",@$strHTML);
//echo ">>>>".$pathpic;
$spaw->show($pathpic);
?> <INPUT TYPE="submit" name="save" id="save" value="save"></form>