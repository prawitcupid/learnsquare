<?php
/**
*  Import Course
*/
/*
last edit :-----
programmer : Neetiwit B.
date : 04-08-2549
Description :
 1. แก้ไขให้ถูกต้องตาม SCORM 1.2 และสามารถเข้ากับ moodle 1.5 ได้
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'SCORM::', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}


/* - - - - - - - - - - - */
$vars= array_merge($_GET,$_POST);
include 'header.php';
switch ($op) {
	case "import_course" :	
		$menus= array(_ADMINMENU,_SCORMADMIN,_SCORMIMPORT);
		$links=array('index.php?mod=Admin','index.php?mod=SCORM&amp;file=admin','index.php?mod=SCORM&amp;file=selectimport');
		lnBlockNav($menus,$links);
		OpenTable();
		importCourse($vars);
		CloseTable();
		break;
	default :	
		//include 'header.php';
		
		/** Navigator **/
		$menus= array(_ADMINMENU,_SCORMADMIN,_SCORMIMPORT);
		$links=array('index.php?mod=Admin','index.php?mod=SCORM&amp;file=admin','index.php?mod=SCORM&amp;file=selectimport');
		lnBlockNav($menus,$links);
		/** Navigator **/

		OpenTable();
		importForm();
		CloseTable();
		
		//include 'footer.php';
}
include 'footer.php';
/* - - - - - - - - - - - */


/**
* Import Form
*/
function importForm() {

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$schoolstable = $lntable['schools'];
		$schoolscolumn = &$lntable['schools_column'];
		$query = "SELECT  $schoolscolumn[sid],$schoolscolumn[code],$schoolscolumn[name] 
											  FROM  $schoolstable 
											  ORDER BY $schoolscolumn[sid]";
		$result = $dbconn->Execute($query);

		?>
		<TABLE WIDTH=98% BORDER=0 CELLSPACING=0 CELLPADDING=3>	
		<TR>
			<TD>
			<IMG SRC="images/global/bul.jpg" WIDTH="8" HEIGHT="8" BORDER="0" ALT="">
			<FONT COLOR="#336666"><B><?=_IMPORTCOURSE?></B></FONT>
			<BR><BR><?=_IMPORTDESC?>
			<BR><BR>

			<CENTER>
			<FORM NAME="ImportForm" METHOD="post" ACTION="index.php" ENCTYPE="multipart/form-data">
			<INPUT TYPE="hidden" NAME="mod" VALUE="SCORM">
			<INPUT TYPE="hidden" NAME="file" VALUE="import">
			<INPUT TYPE="hidden" NAME="op" VALUE="import_course">
			<TABLE>
	<?php
			echo ''
			.'<TR><TD WIDTH=100>'._SCHOOL.'</TD><TD>'
			.'<SELECT class="select" NAME="scode" onchange="document.forms.ImportForm.course_code.value=document.forms.ImportForm.scode.options[this.selectedIndex].value;">';
			list($_,$sscode,$_) = $result->fields;
			while(list($sid,$scode,$name) = $result->fields) {
				$result->MoveNext();
				$name = stripslashes($name);
				echo '<OPTION VALUE="'.$scode.'">'.$name.'</OPTION>';
			}
			echo '</SELECT></TD></TR>';
	?>
			<TR>
				<TD><?=_COURSECODE?></TD><TD><INPUT TYPE="text" NAME="course_code" SIZE="8" VALUE="<?=$sscode?>"></TD>
			</TR>
			<TR>
				<TD><?=_PACKAGE?></TD><TD><INPUT TYPE="file" NAME="fileimport"></TD>
			</TR>
			<TR>
			<?php 
				$php_ini = ini_get_all();
				$upload_max_filesize = $php_ini["upload_max_filesize"]; 
			?>
				<TD><?=_FILESIZE?></TD><TD><B><?php echo $upload_max_filesize["global_value"]; ?>B</B></TD>
			</TR>
			<TR>
				<TD>&nbsp;</TD>
				<TD><BR><INPUT TYPE="submit" VALUE="<?=_IMPORTCOURSE?>" class="button_org"></TD>
			</TR>
			</TABLE>

			</FORM>
			
			</CENTER>

			</TD>
		</TR>
		</TABLE>

		<?php
}


/**
* do import course
*/
function importCourse($vars) {
// Get arguments from argument array
    extract($vars);

	require('modules/SCORM/classes/pclzip.lib.php');

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_questiontable = $lntable['quiz_multichoice'];
	$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];
	
/* get variables from import form */
	if (empty($cid)) 	{
		$cid = lnCourseNextID();
	}

	if (empty($lid)) {
		// import course
		$lid=0;
		$order_offset=0;
		$parent_lid=0;
	}
	else {
		// import lesson
		$result = $dbconn->Execute("SELECT $lessonscolumn[lid_parent], $lessonscolumn[weight] FROM $lessonstable WHERE $lessonscolumn[lid]=$lid");
		list($parent_lid, $weight) = $result->fields;

		$order_offset = $weight - 1;

		// delete lesson 
		deleteLesson($cid,$lid);
	}

/* check if ../content/import/ exists */
	$import_path = COURSE_DIR.'/import/';
	$content_path = COURSE_DIR.'/';
	
	/* to avoid timing out on large files */
	@set_time_limit(0);
	
	$ext = pathinfo($_FILES['fileimport']['name'] );
	$ext = $ext['extension'];

	/* Copy package To Course/Import folder*/
	if (  !$_FILES['fileimport']['name'] || !is_uploaded_file($_FILES['fileimport']['tmp_name']) || ($ext != 'zip') ||  ($_FILES['fileimport']['size'] == 0) ) {
		echo 'File: '.$_FILES['fileimport']['name'].' upload problem.'.$_FILES['fileimport']['size'];
		exit;
	}else{
		echo "<BR>upload Complete";
	}
	
	if (!is_dir($import_path)) {
		if (!@mkdir($import_path, 0700)) {
			echo 'Cannot make import directory.';
			exit;
		}
	}

	$import_path .= $cid.'/';

	if (!is_dir($import_path)) {
		if (!@mkdir($import_path, 0700)) {
			echo 'Cannot make import for a course directory.';
			exit;
		}
	}

	/* extract the entire archive into ../../content/import/$course using the call back function to filter out php files */
	$archive = new PclZip($_FILES['fileimport']['tmp_name']);
	if ($archive->extract(	PCLZIP_OPT_PATH,	$import_path,
							PCLZIP_CB_PRE_EXTRACT,	'preImportCallBack') == 0) {
		echo 'Cannot extract to $import_path';
		clr_dir($import_path);
		exit;
	}
	else {
		echo "<BR>Extract Complete";
	}
	
	unlink($_FILES['fileimport']['tmp_name']);

	/* XML*/
	//global $path, $items, $order,$tag,$gettitle,$getdescription,$course_name,$course_description;	$package_base_path = '';
	global $path, $items, $order;
	global $course_name,$course_description;
	global  $item_tag,$get_title,$get_description,$get_item_description,$get_item_duration;

	$items = array(); /* all the content pages */
	$order = array(); /* keeps track of the ordering for each content page */
	$path  = array();  /* the hierarchy path taken in the menu to get to the current item in the manifest */

	$package_base_path = '';

	$ims_manifest_xml = @file_get_contents($import_path.'imsmanifest.xml');

	$xml_parser = xml_parser_create();

	xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, false); /* conform to W3C specs */
	xml_set_element_handler($xml_parser, 'startElement', 'endElement');
	xml_set_character_data_handler($xml_parser, 'characterData');

	if (!xml_parse($xml_parser, $ims_manifest_xml, true)) {
		die(sprintf("XML error: %s at line %d",
					xml_error_string(xml_get_error_code($xml_parser)),
					xml_get_current_line_number($xml_parser)));
	}
	echo "<BR>load XML complete";

	xml_parser_free($xml_parser);

	
	$course_title = str_replace('_',' ',$course_name);
	$course_description = addslashes($course_description);
	$sid = findSchoolID($scode);
	$course_author=lnSessionGetVar('uid');

	
	$time=time();
	$query = "INSERT INTO $coursestable
				  (	$coursescolumn[cid],
					$coursescolumn[code],
					$coursescolumn[sid],
					$coursescolumn[title],
					$coursescolumn[author],
					$coursescolumn[description],
					$coursescolumn[prerequisite],
					$coursescolumn[purpose],
					$coursescolumn[credit],
					$coursescolumn[reference],
					$coursescolumn[active],
					$coursescolumn[createon],
					$coursescolumn[sequence]
					  )
					VALUES ('$cid',
						  '" . lnVarPrepForStore($course_code) . "',
						  '" . lnVarPrepForStore($sid) . "',
						  '" . lnVarPrepForStore($course_title) . "',
						  '" . lnVarPrepForStore($course_author) . "',
						  '" . lnVarPrepForStore($course_description) . "',
						  '',
						  '',
						  NULL,
						  '',
						  '0',
						  '$time','1' )";

	$dbconn->Execute($query);
	//echo $query;
	//echo "<br>insert into " .$coursestable ." Complete";

	//$weight=0;

	$new_paths=array();
	
	foreach ($items as $item_id => $content_info) {
		$content_parent_id = $parent_lid;
		if ($content_info['parent_content_id'] !== 0) {
			$content_parent_id = $items[$content_info['parent_content_id']]['real_content_id'];
		}

		$my_offset = 0;
		if ($content_parent_id == $parent_lid) {
			$my_offset = $order_offset;
		}

		if (!in_array($content_info['new_path'],$new_paths)) {
			$new_paths[] = $content_info['new_path'];
		}
		
		$filesco = str_replace('resources/','',$content_info['href']);
		$description = str_replace(':::',"\r\n",$content_info['description']);

		list($_,$d) = explode('P',$content_info['duration']);
		list($duration,$_) = explode('D',$d);

		$ordering = $content_info['ordering'] + $my_offset +1;

		$next_lid = lnLessonNextID();

		if (preg_match('/quiz/i',$filesco)) {
			$type='1';
			// insert quiz
			$quiz_file = @file_get_contents($import_path.$filesco);	

			list($_,$quiz_title,$quiz_content)=explode('::',$quiz_file);		
			list($name,$intro,$attempts,$feedback,$correctanswers,$grademethod,$shufflequestions,$testtime,$grade)=explode('#',$quiz_title);
			$name = escapedchar_post($name);
			$intro = escapedchar_post($intro);
			
			$next_qid = lnQuizNextID();
			$query = "INSERT INTO $quiztable VALUES ('$next_qid','$cid','$name','$intro','$attempts','$feedback','$correctanswers','$grademethod','$shufflequestions','$testtime','$grade')";	 
			$dbconn->Execute($query);
			
			$quizs=array();
			$quizs=explode('\n',$quiz_content);		

			for($i=0; $i < count($quizs); $i++) {
				$line = $quizs[$i];
				 if (substr($line, 0, 2) == "//") {  // comment line
					continue;
				 }
				 if ($line == "") {  
					continue;
				 }
				$answerstart = strpos($line, "{");
		        $answerfinish = strpos($line, "}");
				$answerlength = $answerfinish - $answerstart;
				$questiontext = trim(substr($line, 1, $answerstart - 1));
				$answertext = trim(substr($line, $answerstart + 1, $answerlength - 1));
				list($questiontext,$score)=explode('#',$questiontext);

				$answertext = str_replace("=", "~=", $answertext);
				$answers = explode("~", $answertext);
				for ($answer_score=0,$j=1;$j<count($answers);$j++) {
					 if ($answers[$j][0] == "=") {
                        $answer_score += pow(2,$j-1);
                        $answers[$j] = substr($answers[$j], 1);
					 }
				}

				// insert multichoice
				 $next_mcid = lnQuizQuestionNextID();
				$item=$i+1;
				$questiontext = escapedchar_post($questiontext);
				$query = "INSERT INTO $quiz_questiontable VALUES ('$next_mcid','$next_qid','$questiontext','$answer_score','$score','$item')";	 
				$dbconn->Execute($query);

				// insert choice
				for ($j=1;$j<count($answers);$j++) {
					$ch_item = $j+1;
					$next_chid = lnQuizChoiceNextID();
					list($ans,$feedback)=explode('#',$answers[$j]);
					$ans = escapedchar_post($ans);
					$feedback = escapedchar_post($feedback);
					$query = "INSERT INTO $quiz_choicetable VALUES ('$next_chid','$next_mcid','$ans','$feedback','$ch_item')";	 
					$dbconn->Execute($query);				
				}
			}

			$filesco=$next_qid;
			
		}
		else {
			$type='0';
		}
		/* เก็บ path ของ sco */
		if(trim($duration) == '')
			$duration = 'NULL';
		$query = "INSERT INTO $lessonstable($lessonscolumn[lid], $lessonscolumn[cid],$lessonscolumn[title], $lessonscolumn[description],$lessonscolumn[file], $lessonscolumn[duration],$lessonscolumn[weight], $lessonscolumn[lid_parent],$lessonscolumn[type]) 
        VALUES ('$next_lid','$cid','$content_info[title]','$description','$filesco',$duration,'$ordering','$content_parent_id','$type')";    
        $dbconn->Execute($query);

		/* get the content id and update $items */
		$items[$item_id]['real_content_id'] = $next_lid;

		// insert quiz
		$item_idtemp=str_replace('MANIFEST01_RESOURCE','',$item_id);

	}

	
	foreach ($new_paths as $source) {
		if ($source == '.') {
			$source = $import_path;
		}
		else {
			$source = $import_path.$source;
		}
		$dest = $content_path. $cid;
		//copys($source,$dest);
		if(!is_dir(COURSE_DIR.'/import/' . $cid .'/resources'))
		{
			dircpy( COURSE_DIR,'/import/'.$cid,'/'.$cid,true);
		}
		else
		{
			dircpy( COURSE_DIR,'/import/'.$cid .'/resources','/'.$cid,true);
		}
		echo "<BR>Copy Sco file to Couses Complete";
		clr_dir($import_path);
		convertSpecialTags($dest);
	}
	
	if ( $lid==0 ) {
			echo("<meta http-equiv='refresh' content='1;URL=index.php?mod=Courses&file=admin&op=lesson&cid=$cid'>");
			//header("Location:index.php?mod=Courses&file=admin&op=lesson&cid=$cid");
			exit;
	}
	else {
		?>
			<SCRIPT LANGUAGE="JavaScript">
			<!--
				window.close();
			//-->
			</SCRIPT>
		<?
	}

}

function escapedchar_post($string) {
        //Replaces placeholders with corresponding character AFTER processing is done
        $placeholders = array("&&035;", "&&061;", "&&123;", "&&125;", "&&126;");
        $characters   = array("#",      "=",      "{",      "}",      "~"     );
        $string = str_replace($placeholders, $characters, $string);
        return $string;
 }

function convertSpecialTags($dir) {
		$d = dir($dir);
		for($i=0;$entry=$d->read();$i++) {
			if (strpos($entry,".html")) {
				$html = @file_get_contents($dir.'/'.$entry);

		//- - Break Page
				$html =  str_replace('<!--BREAK-->','{PAGE}',$html);
			
		//- - PDF format.. {PDF}file.pdf{/PDF}
				$pdfObjBegin="<OBJECT id='Acrobat Control for ActiveX' height=550 width=100% border=1 classid=CLSID:CA8A9780-280D-11CF-A24D-444553540000><PARAM NAME='_Version' VALUE='327680'><PARAM NAME='_ExtentX' VALUE='18812'><PARAM NAME='_ExtentY' VALUE='14552'><PARAM NAME='_StockProps' VALUE='0'><PARAM NAME='SRC' VALUE=\"";
				$pdfObjEnd="\"></OBJECT>";
				$html =  str_replace($pdfObjBegin,'{PDF}',$html);
				$html =  str_replace($pdfObjEnd,'{/PDF}',$html);

				$fp=fopen($dir.'/'.$entry,"w");
				fwrite($fp,$html);
				fclose($fp);
			}
		}
}

/* called at the start of en element */
/* builds the $path array which is the path from the root to the current element */
$item_tag = false;
$Utf8 = false;
function startElement($parser, $name, $attrs) {
	global $items, $path, $package_base_path;
	global $item_tag,$get_title,$get_description,$get_item_description,$get_item_duration;
	
	if (($name == 'item')) {
		if ($attrs['encoding'] != '') {
			if(utf8_compliant($attrs['encoding']))
			{
				$Utf8 = true;
			}
		}
	}
	if (($name == 'item')) {
		if ($attrs['identifierref'] != '') {
			$path[] = $attrs['identifierref'];
		}
		else {
			$path[] = $attrs['identifier'];
		}
		$item_tag = true;
	}
	else if (($name == 'resource') && is_array($items[$attrs['identifier']]))  {
		$items[$attrs['identifier']]['href'] = $attrs['href'];

		$temp_path = pathinfo($attrs['href']);
		$temp_path = explode('/', $temp_path['dirname']);
		
		if ($package_base_path == '') {
			$package_base_path = $temp_path;
		}
		else {
			$package_base_path = array_intersect($package_base_path, $temp_path);
		}
		
		$items[$attrs['identifier']]['new_path'] = implode('/', $temp_path);
	}

	// get metadata
	if (($name == 'imsmd:title' || $name == 'title' ) && !$item_tag) {
		$get_title = true;
	}
	else if (($name == 'imsmd:description' || $name == 'description') && !$item_tag) {
		$get_description = true;
	}
	else if (($name == 'imsmd:description' || $name == 'description') && $item_tag) {
		$get_item_description = true;
	}
	else if (($name == 'imsmd:duration' || $name == 'duration') && $item_tag) {
		$get_item_duration = true;
	}
}

/* called when an element ends */
/* removed the current element from the $path */
function endElement($parser, $name) {
	global $path;
	global $item_tag,$get_title,$get_description,$get_item_description,$get_item_duration;

	if ($name == 'item') {
		array_pop($path);
		$item_tag = false;
	}
	else if ($name == 'imsmd:title' || $name == 'title') {
		$get_title=false;
	}
	else if (($name == 'imsmd:description' || $name == 'description') && !$item_tag) {
		$get_description=false;
	}
	else if (($name == 'imsmd:description' || $name == 'description') && $item_tag) {
		$get_item_description=false;
	}
	else if (($name == 'imsmd:duration' || $name == 'duration') && $item_tag) {
		$get_item_duration=false;
	}
}

/* called when there is character data within elements */
/* constructs the $items array using the last entry in $path as the parent element */
function characterData($parser, $data){
	global $path, $items, $order;
	global $course_name,$course_description;
	global  $item_tag,$get_title,$get_description,$get_item_description,$get_item_duration;
	
	
	if(Utf8==true)
	{
		$str_trimmed_data = utf8_to_tis620(trim($data));
	}
	else
	{
		$str_trimmed_data = trim($data);
	}
			
	if (!empty($str_trimmed_data)) {
	
		if ($get_title && empty($course_name)) {
			$course_name=$str_trimmed_data;
			return;
		}

		if ($get_description && empty($course_description)) {
			$course_description.=$str_trimmed_data;
			return;
		}

		$size = count($path);
		if ($size > 0) {
			$current_item_id = $path[$size-1];
			if ($size > 1) {
				$parent_item_id = $path[$size-2];
			} 
			else {
				$parent_item_id = 0;
			}
			if (is_array($items[$current_item_id])) {
				/* this item already exists, append the title		*/
				/* this fixes {\n, \t, `, &} characters in elements */
				if (empty($items[$current_item_id]['description']) && $get_item_description) 
				{
					if(Utf8==true)
					{
						$items[$current_item_id]['description'] =  utf8_to_tis620(trim($data));	
					}
					else
					{
						$items[$current_item_id]['description'] =  trim($data);	
					}
				}
				else if (empty($items[$current_item_id]['duration']) && $get_item_duration) 
				{
					if(Utf8==true)
					{
						$items[$current_item_id]['duration'] = utf8_to_tis620(trim($data));
					}
					else
					{
						$items[$current_item_id]['duration'] = trim($data);
					}

				}

			} else {
					$order[$parent_item_id] ++;
					if(Utf8==true)
					{
						$items[$current_item_id] = array('title'	=> utf8_to_tis620(trim($data)),
													'parent_content_id' => $parent_item_id,
													'ordering'			=> $order[$parent_item_id]-1);
					}
					else
				{
								$items[$current_item_id] = array('title'	=> trim($data),
													'parent_content_id' => $parent_item_id,
													'ordering'			=> $order[$parent_item_id]-1);
				}
			}
		}
	}
}

function findSchoolID($schoolcode) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$schoolstable = $lntable['schools'];
	$schoolscolumn = &$lntable['schools_column'];

	$query = "SELECT  $schoolscolumn[sid] 
										  FROM  $schoolstable 
										  WHERE $schoolscolumn[code]= '".lnVarPrepForStore($schoolcode)."'";

	$result = $dbconn->Execute($query);

	if($dbconn->ErrorNo() != 0) {
		return;
	}
	else {
		list($schoolid) = $result->fields;
		return $schoolid;
	}
}

function buildQuiz($cid,$lid,$quiz_data) {

	$quiz_data = addslashes($quiz_data);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$quiz_questiontable = $lntable['quiz_question'];
	$quiz_questioncolumn = &$lntable['quiz_question_column'];
	$quiz_choicetable = $lntable['quiz_choice'];
	$quiz_choicecolumn = &$lntable['quiz_choice_column'];

	list($quiz_order,$question_content) = explode('@@@',$quiz_data);
	list($qid_old,$lid,$description,$quiz_type,$random,$timelimit,$option) = explode('::',$quiz_order);
	$qid = lnQuizNextID();

	$query = "INSERT INTO $quiztable VALUES ('$qid','$cid','$lid','$description','$quiz_type','$random','$timelimit','$option')"; 
	$dbconn->Execute($query);

	$question = array();
	$question = explode('###', $question_content);

	for ($m=1,$i=0; $i<count($question); $i++,$m++) {
		list($questions,$choice_text) = explode('|||', $question[$i]);
		list($qtext,$answer,$score) = explode('::',$questions);
		if (!empty($qtext)) {
			$quid = lnQuizQuestionNextID();
			$query = "INSERT INTO $quiz_questiontable VALUES ('$quid','$qid','$qtext','$answer','$score','$m')"; 
			$dbconn->Execute($query);

			$choices = array();
			$choices = explode('==',$choice_text);
			for ($n=1,$j=0;$j<count($choices);$j++,$n++) {
				list($choice,$desc) = explode('::',$choices[$j]);
				if (!empty($choice)) {
					$chid = lnQuizChoiceNextID();
					$query = "INSERT INTO $quiz_choicetable VALUES ('$chid', '$quid', '$choice', '$desc', '$n')";
					$dbconn->Execute($query);
				}
			}
		}
	}	
}

function deleteLesson($cid,$lid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$sql = "SELECT $lessonscolumn[lid] FROM $lessonstable WHERE $lessonscolumn[cid]='$cid' AND $lessonscolumn[lid_parent]='$lid'";
	$result = $dbconn->Execute($sql);

	while(list($child_lid) = $result->fields) {
		deleteLesson($cid,$child_lid);
	}

	$sql = "DELETE FROM $lessonstable WHERE $lessonscolumn[lid]='$lid'";	
	$dbconn->Execute($sql);
}

function utf8_to_tis620($string) {
  /*$str = $string;
  $res = "";
  for ($i = 0; $i < strlen($str); $i++) {
    if (ord($str[$i]) == 224) {
      $unicode = ord($str[$i+2]) & 0x3F;
      $unicode |= (ord($str[$i+1]) & 0x3F) << 6;
      $unicode |= (ord($str[$i]) & 0x0F) << 12;
      $res .= chr($unicode-0x0E00+0xA0);
      $i += 2;
    } else {
      $res .= $str[$i];
    }
  }
  return $res;*/
  return $string;
}
function tis620_to_utf8($text) {
  $utf8 = "";
  for ($i = 0; $i < strlen($text); $i++) {
    $a = substr($text, $i, 1);
    $val = ord($a);
 
    if ($val < 0x80) {
      $utf8 .= $a;
    } elseif ((0xA1 <= $val && $val < 0xDA) || (0xDF <= $val && $val <= 0xFB)) {
      $unicode = 0x0E00+$val-0xA0;
      $utf8 .= chr(0xE0 | ($unicode >> 12));
      $utf8 .= chr(0x80 | (($unicode >> 6) & 0x3F));
      $utf8 .= chr(0x80 | ($unicode & 0x3F));
    }
  }
  return $utf8;
}

function isUTF8($string)
{
   $string_utf8 = utf8_encode($string);
   
   if( strpos($string_utf8,"?",0) !== false ) // "?" is ALT+159
         return true;  // the original string was utf8
   else
         return false; // otherwise

}
function utf8_compliant($str) {
    if ( strlen($str) == 0 ) {
        return TRUE;
    }
    // If even just the first character can be matched, when the /u
    // modifier is used, then it's valid UTF-8. If the UTF-8 is somehow
    // invalid, nothing at all will match, even if the string contains
    // some valid sequences
    return (preg_match('/^.{1}/us',$str,$ar) == 1);
}
/*function dircpy($basePath, $source, $dest, $overwrite = false){
   if(!is_dir($basePath . $dest)) //Lets just make sure our new folder is already created. Alright so its not efficient to check each time... bite me
   mkdir($basePath . $dest);
   if($handle = opendir($basePath . $source)){        // if the folder exploration is sucsessful, continue
       while(false !== ($file = readdir($handle))){ // as long as storing the next file to $file is successful, continue
           if($file != '.' && $file != '..'){
               $path = $source . '/' . $file;
               if(is_file($basePath . $path)){
                   if(!is_file($basePath . $dest . '/' . $file) || $overwrite)
                   if(!@copy($basePath . $path, $basePath . $dest . '/' . $file)){
                       echo '<font color="red">File ('.$path.') could not be copied, likely a permissions problem.</font>';
                   }
               } elseif(is_dir($basePath . $path)){
                   if(!is_dir($basePath . $dest . '/' . $file))
                   mkdir($basePath . $dest . '/' . $file); // make subdirectory before subdirectory is copied
                   dircpy($basePath, $path, $dest . '/' . $file, $overwrite); //recurse!
               }
           }
       }
       closedir($handle);
   }
} */
function dircpy($basePath, $source, $dest, $overwrite = false)
{
   if(!is_dir($basePath . $dest)) //Lets just make sure our new folder is already created. Alright so its not efficient to check each time... bite me
   mkdir($basePath . $dest);
   if($handle = opendir($basePath.$source))
   {        // if the folder exploration is sucsessful, continue
       while(false !== ($file = readdir($handle)))
       { // as long as storing the next file to $file is successful, continue
           if($file != '.' && $file != '..')
           {
               $path = $source . '/' . $file;
               //echo "source : " . $path."<BR>";
               if(is_file($basePath . $path))
               {
                   $c = pathinfo($basePath . $path);
                   if((!is_file($basePath  . $dest . '/' . $file) || $overwrite) && ($c['extension']== "htm" || $c['extension']== "html" || $c['extension']== "php" || $c['extension']== "txt"))
                   {
                        //$a = iconv("TIS-620","UTF-8",@file_get_contents($basePath . $path)); 
                        //$a = utf8_encode(file_get_contents($path));
                        $a = file_get_contents($basePath . $path);
                        $f=fopen($basePath . $dest . '/' . $file, "wb"); 
                        //$text=utf8_encode("?a?!"); 
                            // adding header 
                        //$a = "\xEF\xBB\xBF".$a; 
                        fputs($f, $a); 
                        fclose($f); 
                        //echo "desc : " .$dest . '/' . $file ." Convert <BR>"; 
                        
                   }
                   elseif(!is_file($basePath . $dest . '/' . $file) || $overwrite)
                   {
                       $a = @file_get_contents($basePath . $path); 
                        $f=fopen($basePath . $dest . '/' . $file, "wb"); 
                        fputs($f, $a); 
                        fclose($f); 
                       
                        //echo "desc : " .$dest . '/' . $file ." Copy<BR>";
        
                   }
               
               } 
                elseif(is_dir($basePath . $path)){
                   if(!is_dir($basePath . $dest . '/' . $file))
                   mkdir($basePath . $dest . '/' . $file); // make subdirectory before subdirectory is copied
                   dircpy($basePath, $path, $dest . '/' . $file, $overwrite); //recurse!
               }
           }
       }
       closedir($handle);
   }
}
?>