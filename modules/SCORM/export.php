<?php
/**
*  Export Course
*/
/*
last edit :-----
programmer : Neetiwit B.
date : 04-08-2549
Description :
 1. เนเธเนเนเธเนเธซเนเธ–เธนเธเธ•เนเธญเธเธ•เธฒเธก SCORM 1.2 เนเธฅเธฐเธชเธฒเธกเธฒเธฃเธ–เน€เธเนเธฒเธเธฑเธ moodle 1.5 เนเธ”เน
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'SCORM::', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}


/* - - - - - - - - - - - */
define(AT_INCLUDE_PATH,'modules/SCORM/');
//require(AT_INCLUDE_PATH.'classes/zipfile.class.php');														/* for zipfile */
include_once(AT_INCLUDE_PATH.'ims/ims_template.inc.php');														/* for ims templates + print_organizations() */

$vars= array_merge($_GET,$_POST);

switch ($op) {
	case "export_course" :	
		/* to avoid timing out on large files */
		set_time_limit(0);
		$courseinfo = lnCourseGetVars($cid);
		$ims_course_title = str_replace(' ', '_', $courseinfo['title']);
		$full_course_title = $courseinfo['title'];
		$ims_course_description= htmlspecialchars($courseinfo['description']);
		$ims_course_description = str_replace("\r\n",':::', $ims_course_description);
		$ims_course_description = str_replace('"',"'", $ims_course_description);


		/* generate the imsmanifest.xml header attributes */
		$imsmanifest_xml = str_replace(array('{COURSE_TITLE}','{COURSE_DESCRIPTION}'), array($ims_course_title,$ims_course_description), $ims_template_xml['header']);

		//$zipfile = new zipfile();
		$zipfile = new ZipArchive(); 
		if ($lid) {
			$filename = 'lesson_'.$courseinfo['code'].'_'.$lid.'.zip';
		}else {
			$filename = 'lesson_'.$courseinfo['code'].'.zip';
		}
		$tmpfile=$filename;
		$filename=ini_get('upload_tmp_dir').'/'.$filename;
		//echo $filename;exit();
		if ($zipfile->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
    		exit("cannot open <$filename>\n");
		}
		
		/* get all the content */
		$content = array();
		$paths	 = array();
		$top_content_parent_id = 0;
		/*
		$handler=new MyHandler();
		$parser = new XML_HTMLSax();
		$parser->set_object($handler);
		$parser->set_element_handler('openHandler','closeHandler');
		$vars = array();
		*/
		$handler=new MyHandler();
		$parser = xml_parser_create();
		xml_set_object($parser,$handler);
		xml_set_element_handler($parser,'openHandler','closeHandler');
		$vars = array();

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
		
		$lessonstable = $lntable['lessons'];
		$lessonscolumn = &$lntable['lessons_column'];
		$quiztable = $lntable['quiz'];
		$quizcolumn = &$lntable['quiz_column'];
		$quiz_questiontable = $lntable['quiz_multichoice'];
		$quiz_questioncolumn = &$lntable['quiz_multichoice_column'];
		$quiz_choicetable = $lntable['quiz_choice'];
		$quiz_choicecolumn = &$lntable['quiz_choice_column'];

		$query="SELECT * FROM $lessonstable WHERE $lessonscolumn[cid]='$cid' ORDER BY  $lessonscolumn[weight]";		
		$result = $dbconn->Execute($query);
		//$file1 = fopen("modules/SCORM/filename.txt", 'x'); 
		while (list($lesson_id,$lesson_cid,$lesson_title,$lesson_description,$lesson_file,$duration,$weight,$lid_parent,$type)= $result->fields) {
			$result->MoveNext();
			$lesson_title =   htmlspecialchars(stripslashes($lesson_title));
			$lesson_description =  htmlspecialchars(stripslashes($lesson_description));
			$row = array('LessonID'=>"$lesson_id",'CourseID'=>"$lesson_cid",'LessonTitle'=>"$lesson_title",'Abstract'=>"$lesson_description",'LessonFile'=>"$lesson_file",'Length'=>"$duration",'weight'=>"$weight",'LessonParentID'=>"$lid_parent",'Type'=>"$type");

			$content[$row['LessonParentID']][] = $row;
			if ($lid == $row['LessonID']) {
				$top_content = $row;
				$top_content_parent_id = $row['LessonParentID'];
			}
		}

		// if export lesson
		if ($lid) {
			
			/* filter out the top level sections that we don't want */
			$top_level = $content[$top_content_parent_id];
			foreach($top_level as $page) {
				if ($page['LessonID'] == $lid) {
					$content[$top_content_parent_id] = array($page);
				} else {
					/* this is a page we don't want, so might as well remove it's children too */
					unset($content[$page['LessonID']]);
				}
			}
			
			$ims_course_title .= '-'. htmlspecialchars(str_replace(' ', '_', $content[$top_content_parent_id][0]['LessonTitle']));
			$full_course_title .= ': '. htmlspecialchars($content[$top_content_parent_id][0]['LessonTitle']);
		}

		/* get the first content page to default the body frame to */
		$first = $content[$top_content_parent_id][0];

		ob_start();

		print_organizations($top_content_parent_id, $content, 0, '', array(), $toc_html);

		$organizations_str = ob_get_contents();

		ob_clean();

		$toc_html = str_replace('{TOC}', $toc_html, $html_toc);

		$frame = str_replace(	array('{COURSE_TITLE}',		'{FIRST_ID}'),
								array($ims_course_title, $first['LessonFile']),
								$html_frame);

		$html_mainheader = str_replace('{COURSE_TITLE}', $full_course_title, $html_mainheader);
								

		/* append the Organizations and Resources to the imsmanifest */
		/* 2 */
		$imsmanifest_xml .= str_replace(	array('{COURSE_TITLE}','{ORGANIZATIONS}',	'{RESOURCES}'),
											array($ims_course_title, $organizations_str, $resources),
											$ims_template_xml['final']);
		
		/* export quiz */
		/*3*/
		/*
				$query = "SELECT $quizcolumn[qid], $quizcolumn[cid], $quizcolumn[name],$quizcolumn[intro], $quizcolumn[attempts], $quizcolumn[feedback],$quizcolumn[correctanswers],$quizcolumn[grademethod],$quizcolumn[shufflequestions],$quizcolumn[testtime],$quizcolumn[grade] FROM $quiztable WHERE $quizcolumn[cid]='".$courseinfo['cid']."'";
				$result = $dbconn->Execute($query);
				
				while(list($qid,$cid_l,$name,$intro,$attempts,$feedback,$correctanswers,$grademethod,$shufflequestions,$testtime,$grade) = $result->fields) {
						$result->MoveNext();
						$quizs='';
						$intro=stripslashes($intro);
						 extractAsset($intro);
						$name = escapedchar_pre($name);
						$intro = escapedchar_pre($intro);

						$quiztitle= "::$name#$intro#$attempts#$feedback#$correctanswers#$grademethod#$shufflequestions#$testtime#$grade::\n";
					
						$query = "SELECT $quiz_questioncolumn[mcid], $quiz_questioncolumn[question], $quiz_questioncolumn[answer], $quiz_questioncolumn[score] FROM $quiz_questiontable WHERE  $quiz_questioncolumn[qid]='$qid' ORDER BY $quiz_questioncolumn[weight]";
						$result2 = $dbconn->Execute($query);
						$lines='';
						while(list($mcid,$question,$answer,$score) = $result2->fields) {
								$result2->MoveNext();

								 extractAsset($question);
								$question = escapedchar_pre($question);
								$lines .= "$question#$score {";
								$query = "SELECT $quiz_choicecolumn[chid], $quiz_choicecolumn[answer], $quiz_choicecolumn[feedback] FROM $quiz_choicetable WHERE $quiz_choicecolumn[mcid]='$mcid' ORDER BY $quiz_choicecolumn[weight]";
								$result3 = $dbconn->Execute($query);
								$choices='';
								for($i=0; list($chid,$choice,$choice_desc) = $result3->fields; $i++) {
									$result3->MoveNext();
									 extractAsset($choice);
									 extractAsset($choice_desc);
									$choice = escapedchar_pre($choice);
									$choice_desc = escapedchar_pre($choice_desc);

									if (pow(2,$i) & $answer) {
										$ans = '=';
									}
									else {
										$ans = '~';
									}
									$choices .= "$ans$choice";
									if ($choice_desc)
										$choices .="#$choice_desc";
									$choices .= " "; 
								}

								$lines .= $choices.'}\n';
						}

						$quizs = $quiztitle.$lines;
					
						$zipfile->add_file($quizs, 'resources/'._QUESTIONPREFIX.$qid.'.txt');
					}
			*/
		$content_path = 'resources/';
		$zipfile->addEmptyDir('resources');
				
		$fileNotAdd=array("adlcp_rootv1p2.xsd","ims_xml.xsd","imscp_rootv1p1p2.xsd","imsmanifest.xml","imsmd_rootv1p2p1.xsd");

		foreach ( read_dir(COURSE_DIR.'/'.$cid) as $file) 
		{
			$file_path = $file;
			if (file_exists($file_path) && 	!in_array($file_path, $fileZiped)) {
				
				if (is_dir($file)) {
					//$zipfile->priv_add_dir($content_path.$dir, time());
					//$zipfile->priv_add_dir($content_path.substr($file,strlen(COURSE_DIR.'/'.$cid.'/')),time());
					$zipfile->addEmptyDir($content_path.substr($file,strlen(COURSE_DIR.'/'.$cid)));
					
					$paths[] = substr($file,strlen(COURSE_DIR.'/' 	.$cid.'/'));
				}
				else
				{
					if(!in_array($file_path, $$fileZiped) && !in_array(trim(strtolower(basename($file))),$fileNotAdd))
					{
						$file_info = stat( $file_path );
						//$zipfile->add_file(file_get_contents($file_path), $content_path.substr($file,strlen(COURSE_DIR.'/' 	.$cid.'/')),$file_info['mtime']);	
						//$zipfile->addFromString($content_path.substr($file,strlen(COURSE_DIR.'/'.$cid.'/')), file_get_contents($file_path));
						$zipfile->addfile($file_path,$content_path.substr($file,strlen(COURSE_DIR.'/'.$cid.'/')));
						$zipped_files[] = $file_path;
						$files[] = $file_path;
						$fileZiped[] = $file_path;
						
					}
					
				}
				//fwrite($file1, $file."\r\n");
			}
			
		}
		
		//fwrite($file1, $imsmanifest_xml ."\r\n");
		//fclose($file1);
		
/* zip the entire ims export directory and send to the user */
		/*
		$zipfile->add_file($frame, 'index.html');
		$zipfile->add_file($toc_html, 'toc.html');
	//	$zipfile->add_file(tis620_to_utf8($imsmanifest_xml), 'imsmanifest.xml');
		$zipfile->add_file($imsmanifest_xml,'imsmanifest.xml');
		$zipfile->add_file($html_mainheader, 'header.html');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/ims.css'), 'ims.css');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/footer.html'), 'footer.html');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/logo.jpg'), 'logo.jpg');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/imsmd_rootv1p2p1.xsd'), 'imsmd_rootv1p2p1.xsd');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/adlcp_rootv1p2.xsd'), 'adlcp_rootv1p2.xsd');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/ims_xml.xsd'), 'ims_xml.xsd');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/imscp_rootv1p1p2.xsd'), 'imscp_rootv1p1p2.xsd');
		*/
		/*
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/ims_md_rootv1p1.xsd'), 'ims_md_rootv1p1.xsd');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/imsss_v1p0auxresource.xsd'), 'imsss_v1p0auxresource.xsd');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/imsss_v1p0control.xsd'), 'imsss_v1p0control.xsd');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/imsss_v1p0delivery.xsd'), 'imsss_v1p0delivery.xsd');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/imsss_v1p0limit.xsd'), 'imsss_v1p0limit.xsd');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/imsss_v1p0objective.xsd'), 'imsss_v1p0objective.xsd');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/imsss_v1p0random.xsd'), 'imsss_v1p0random.xsd');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/imsss_v1p0rollup.xsd'), 'imsss_v1p0rollup.xsd');
		$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/imsss_v1p0seqrule.xsd'), 'imsss_v1p0seqrule.xsd');
		*/
		//$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/SCORMGenericLogic.js'), 'SCORMGenericLogic.js');
		
		//Add javascript for scorm Edit by Neetiwit At 02/02/2006
		/*$zipfile->add_file(file_get_contents(AT_INCLUDE_PATH.'ims/imsss_v1p0util.xsd'), 'imsss_v1p0util.xsd');*/

		//add file zip using zip php 30/04/55
		$zipfile->addFromString('index.html',$frame);
		$zipfile->addFromString('toc.html',$toc_html);
		$zipfile->addFromString('imsmanifest.xml',$imsmanifest_xml);
		$zipfile->addFromString('header.html',$html_mainheader);
		$zipfile->addFromString('ims.css',file_get_contents(AT_INCLUDE_PATH.'ims/ims.css'));
		$zipfile->addFromString('footer.html',file_get_contents(AT_INCLUDE_PATH.'ims/footer.html'));
		$zipfile->addFromString('logo.jpg',file_get_contents(AT_INCLUDE_PATH.'ims/logo.jpg'));
		$zipfile->addFromString('imsmd_rootv1p2p1.xsd',file_get_contents(AT_INCLUDE_PATH.'ims/imsmd_rootv1p2p1.xsd'));
		$zipfile->addFromString('adlcp_rootv1p2.xsd',file_get_contents(AT_INCLUDE_PATH.'ims/adlcp_rootv1p2.xsd'));
		$zipfile->addFromString('ims_xml.xsd',file_get_contents(AT_INCLUDE_PATH.'ims/ims_xml.xsd'));
		$zipfile->addFromString('imscp_rootv1p1p2.xsd',file_get_contents(AT_INCLUDE_PATH.'ims/imscp_rootv1p1p2.xsd'));
		$zipfile->addFromString('SCORMGenericLogic.js',file_get_contents(AT_INCLUDE_PATH.'ims/SCORMGenericLogic.js'));
		//add jw player
		$zipfile->addfile('includes/player.swf','player.swf');
		$zipfile->addfile('javascript/jwplayer.js','jwplayer.js');
		
 		foreach($html_player AS $index => $player){
 			//echo $index."=>".$player."<br>";
        	$zipfile->addFromString("$index",$player);
    	}
		
		zip_close($zipfile);
		
		/* create the archive */
		header('Content-Type: application/octet-stream');
		header('Content-transfer-encoding: binary'); 
		/*
		if ($lid) {
			$filename = 'lesson_'.$courseinfo['code'].'_'.$lid.'.zip';
		}
		else {
			$filename = 'lesson_'.$courseinfo['code'].'.zip';
		}
		*/
		header("Content-Disposition: attachment; filename=$tmpfile");
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		readfile($filename);
		
		//echo $zipfile->file();

		break;
	
	default :		
		
		include 'header.php';

		/** Navigator **/
		$menus= array(_ADMINMENU,_SCORMADMIN,_SCORMEXPORT);
		$links=array('index.php?mod=Admin','index.php?mod=SCORM&amp;file=admin','index.php?mod=SCORM&amp;file=export');
		lnBlockNav($menus,$links);
		/** Navigator **/

		OpenTable();
		exportForm();
		CloseTable();

		include 'footer.php';
}
/* - - - - - - - - - - - */



function extractAsset($text) {
	global $parser,$zipfile,$zipped_files,$my_files,$cid;

	$my_files = array();
	xml_parse($parser,$text);

	$files=array();
	foreach ($my_files as $file) {
		/* filter out full urls */
		$url_parts = @parse_url($file);
		if (isset($url_parts['scheme'])) {
			continue;
		}

		/* file should be relative to content. let's double check */
		if ((substr($file, 0, 1) == '/') && ( strpos($file, '..') !== false) ) {
			continue;
		}

		$file_path =  COURSE_DIR.'/' .$cid . '/' . $file;

		/* check if this file exists in the content dir, if not don't include it */
		if (file_exists($file_path) && 	!in_array($file_path, $zipped_files)) {
			$zipped_files[] = $file_path;

			$dir = dirname($file).'/';
			
			if (!in_array($dir, $paths)) {
				$zipfile->priv_add_dir('resources/'.$dir, time());
				$paths[] = $dir;
			}

			$file_info = stat( $file_path );
			$zipfile->add_file(file_get_contents($file_path), 'resources/'.$file, $file_info['mtime']);
		}
	}
}
				
function exportForm() {
	
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

	
		$schoolstable = $lntable['schools'];
		$schoolscolumn = &$lntable['schools_column'];
		$query = "SELECT  $schoolscolumn[sid],$schoolscolumn[code],$schoolscolumn[name] 
											  FROM  $schoolstable 
											  ORDER BY $schoolscolumn[sid]";
		$result1 = $dbconn->Execute($query);
	
					
?>
		<TABLE WIDTH=98% BORDER=0 CELLSPACING=0 CELLPADDING=3>	
		<TR>
			<TD>
			<IMG SRC="images/global/bul.jpg" WIDTH="8" HEIGHT="8" BORDER="0" ALT="">
			<FONT COLOR="#336666"><B><?=_EXPORTCOURSE?></B></FONT>
			<BR><BR><?=_EXPORTDESC?>
			<BR><BR>

			<CENTER>
			<FORM NAME="ImportForm" METHOD="post" ACTION="index.php" ENCTYPE="multipart/form-data">
			<INPUT TYPE="hidden" NAME="mod" VALUE="SCORM">
			<INPUT TYPE="hidden" NAME="file" VALUE="export">
			<INPUT TYPE="hidden" NAME="op" VALUE="export_course">
			
			<TABLE>
			<TR>
					<TD>เลือกกลุ่มวิชา</TD>
					<TD>	
					<SELECT NAME="sid" onchange="window.open(this.options[this.selectedIndex].value,'_self')">
					<OPTION VALUE="index.php?mod=SCORM&file=export" > </option>
					<?
						while (list($sid,$code,$name) = $result1->fields)
						{
							$result1->MoveNext();
							$name = stripslashes($name);
							$a = "<OPTION VALUE=\"index.php?mod=SCORM&file=export&con_category=".$sid ."\"";
							if($_GET['con_category'] == $sid)
							{ 
								$a .= "selected=\"selected\"";
							}
							$a .= ">".$name."</option>";
							echo $a;
						} 
					?>
							</SELECT>
					</TD>
			</TR>
			<TR>
			<TD><?=_COURSESELECT?></TD>
			<TD>
			<SELECT NAME="cid">
			<?
							$coursestable = $lntable['courses'];
							$coursescolumn = &$lntable['courses_column'];
							$query = "SELECT  $coursescolumn[cid],$coursescolumn[code],$coursescolumn[title] 
											  FROM  $coursestable  
											 WHERE ($coursescolumn[sid] = '".$_GET['con_category'] ."')  ";
											if (!lnUserAdmin(lnSessionGetVar('uid'))) {
											$query .= "  &&  ($coursescolumn[author]=".lnSessionGetVar('uid').")"; 
											}  /* Anita added WHERE Clues  */
						    					 $query .= "ORDER BY $coursescolumn[cid]" ; 
						    					 
						$result = $dbconn->Execute($query);

						while (list($cid,$code,$title) = $result->fields) {
							$result->MoveNext();
							$title = stripslashes($title);
							echo '<OPTION VALUE="'.$cid.'">'.$title.'</OPTION>';
						}
				
			
			echo "</SELECT>";
			echo "</TD>";
			echo	"</TR>";
			echo "<TR>";
			echo "<TD>&nbsp;</TD><TD><BR><INPUT TYPE=\"submit\" VALUE=\""._EXPORTCOURSE."\" class=\"button_org\"></TD>";
			echo "</TR>";
			echo "</TABLE>";
			echo "</CENTER>";
			echo "</FORM>";
			echo "</TD>";
			echo "</TR>";
			echo "</TABLE>";
			}
	?>
<?


  function escapedchar_pre($string) {
        //Replaces escaped control characters with a placeholder BEFORE processing
        
        $escapedcharacters = array("#",    "=",    "{",    "}",    "~"   );
        $placeholders      = array("&&035;", "&&061;", "&&123;", "&&125;", "&&126;");

        $string = str_replace("\\\\", "&&092;", $string);
        $string = str_replace($escapedcharacters, $placeholders, $string);
        $string = str_replace("&&092;", "\\", $string);

        return $string;
    }
	//Nay Add function To Get All file add to Zip file
	function read_dir($dir) {
	   $array = array();
	   $d = dir($dir);
	   while (false !== ($entry = $d->read())) {
		   if($entry!='.' && $entry!='..') {
			   $entry = $dir.'/'.$entry;
			   if(is_dir($entry)) {
				   $array[] = $entry;
				   $array = array_merge($array, read_dir($entry));
			   } else {
				   $array[] = $entry;
			   }
		   }
	   }
	   $d->close();
	   return $array;
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
?>
