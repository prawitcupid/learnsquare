<?php
/****************************************************************/
/* ATutor														*/
/****************************************************************/
/* Copyright (c) 2002-2003 by Greg Gay & Joel Kronenberg        */
/* Adaptive Technology Resource Centre / University of Toronto  */
/* http://atutor.ca												*/
/*                                                              */
/* This program is free software. You can redistribute it and/or*/
/* modify it under the terms of the GNU General Public License  */
/* as published by the Free Software Foundation.				*/
/****************************************************************/
/*
 last edit :-----
 programmer : Neetiwit B.
 date : 04-08-2549
 Description :
 1. แก้ไขให้ imsmifest.xml ถูกต้องตาม SCORM 1.2 และสามารถเข้ากับ moodle 1.5 ได้
 */

if (!defined('AT_INCLUDE_PATH')) { exit; }


function print_organizations($parent_id,
&$_menu,
$depth,
$path='',
$children,
&$string) {

	global $html_template, $zipfile, $resources, $ims_template_xml, $parser, $my_files,$config,$file1,$fileZiped, $html_player;
	static $paths, $zipped_files;

	$space  = '    ';
	$prefix = '                    ';

	if ($depth == 0) {
		$string .= '<ul>';
	}

	$top_level = $_menu[$parent_id];

	if (!is_array($paths)) {
		$paths = array();
	}
	if (!is_array($zipped_files)) {
		$zipped_files = array();
	}

	//Nay
	if(!is_array($fileZiped))
	{
		$fileZiped= array();
	}
	//end nay
	$content_path = 'resources/';

	if ( is_array($top_level) ) {
		$counter = 1;
		$num_items = count($top_level);
		foreach ($top_level as $garbage => $content) {

			$link = '';
			//if (is_array($temp_path)) {
			//$this = current($temp_path);
			//}
			//			if ($content['content_path']) {
			//				$content['content_path'] .= '/';
			//			}

			$link = $prevfix.'<item identifier="MANIFEST01_ITEM'.$content['LessonID'].'"  identifierref="MANIFEST01_RESOURCE'.$content['LessonID'].'" isvisible="true">'."\n";
			//echo ">>>>".$content_path.$content['LessonFile'];
			$html_link = '<a href="'.$content_path.$content['LessonFile'].'" target="body">'.$content['LessonTitle'].'</a>';
			//echo "link>>>".$html_link;
			/* save the content as HTML files */
			/* @See: include/lib/format_content.inc.php */


			$filename = basename(COURSE_DIR.'/'.$content['CourseID'].'/'.$content['LessonFile']);
			$filename = explode(".",$filename);
			//print_r($filetype);
			$filetype = $filename[(sizeof($filename)-1)];
			//echo "<br>filetype=".$filetype;//exit();
			//echo "basecontent=".basename(COURSE_DIR.'/'.$content['CourseID'].'/'.$content['LessonFile'])."<br>";
			$fileNotAdd=array('mp4','wmv','wma','mp3','swf','flv','pdf');
			//echo in_array($filetype[(sizeof($filetype)-1),$fileNotAdd);
			if(!in_array($filetype,$fileNotAdd)){
				$content['text'] = file_get_contents(COURSE_DIR.'/'.$content['CourseID'].'/'.$content['LessonFile']);
				$content['text'] = str_replace('{PAGE}','<!--BREAK-->',$content['text']);
			}else{
				if(in_array('pdf',$fileNotAdd)){
					$player = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
	<object data="'.$content_path.$content['LessonFile'].'" type="application/pdf" width="100%" height="550"></object></center>
</body>
</html>';
				}else if(in_array('wmv',$fileNotAdd)){
					//content wmv
					$player= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>
<object width="100%" height="100%"
type="video/x-ms-asf" url="'.$content_path.$content['LessonFile'].'" data="'.$content_path.$content['LessonFile'].'"
classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6">
<param name="url" value="'.$content_path.$content['LessonFile'].'">
<param name="filename" value="'.$content['LessonFile'].'">
<param name="autostart" value="1">
<param name="uiMode" value="full" />
<param name="autosize" value="1">
<param name="playcount" value="1"> 
<embed type="application/x-mplayer2" src="'.$content_path.$content['LessonFile'].'" width="100%" height="100%" autostart="true" showcontrols="true" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/"></embed>
</object>
</body>
</html>';
				}else{
					//content mp4 flv swf
					$player = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script type="text/javascript" src="jwplayer.js"></script>
</head>
<body>
	<div id="container">Loading the player ...</div>
	<script type="text/javascript">
		jwplayer("container").setup({
			flashplayer: "player.swf",
			file: "'.$content_path.$content['LessonFile'].'",
			height: 270,
			width: 480
		});
	</script>
</body>
</html>';
				}
				$index = $content['LessonFile'].'.html';
				$playerarray = array($index => $player);
				$html_player = array_merge((array)$html_player,(array)$playerarray);

				$html_link = '<a href="'.$index.'" target="body">'.$content['LessonTitle'].'</a>';
			}
			/*
			 if(in_array('mp4',$fileNotAdd)){
				$content['text']= '<iframe src="'.$content_path.$content['LessonFile'].'"></iframe>';
				}else if(in_array('flv',$fileNotAdd)){
				$content['text']= '<iframe src="'.$content_path.$content['LessonFile'].'"></iframe>';
				}else if(in_array('swf',$fileNotAdd)){
				$content['text']= '<iframe src="'.$content_path.$content['LessonFile'].'"></iframe>';
				}
				*/
			//$content['text']=file_get_contents(COURSE_DIR.'/'.$content['CourseID'].'/'.$content['LessonFile']);
			//$content['text'] =  str_replace('{PAGE}','<!--BREAK-->',$content['text']);

			//echo "<br>file=".basename(COURSE_DIR.'/'.$content['CourseID'].'/'.$content['LessonFile']);
			//$content['text']= '<iframe src="'.$content_path.$content['LessonFile'].'"></iframe>';
			/*
			//- - PDF format.. {PDF}file.pdf{/PDF}

			$pdfObjBegin="<OBJECT id='Acrobat Control for ActiveX' height=550 width=100% border=1 classid=CLSID:CA8A9780-280D-11CF-A24D-444553540000><PARAM NAME='_Version' VALUE='327680'><PARAM NAME='_ExtentX' VALUE='18812'><PARAM NAME='_ExtentY' VALUE='14552'><PARAM NAME='_StockProps' VALUE='0'><PARAM NAME='SRC' VALUE=\"";
			$pdfObjEnd="></OBJECT>";
			$content['text'] = preg_replace("/{pdf}(.*?){\/pdf}/si", "$pdfObjBegin\\1\"$pdfObjEnd", $content['text']) ;
			$content['text'] = preg_replace("/{PDF}(.*?){\/PDF}/si", "$pdfObjBegin\\1\"$pdfObjEnd", $content['text']) ;
			*/

			/* add HTML header and footers to the files */
			$content['text'] = str_replace(	array('{TITLE}',	'{CONTENT}', '{KEYWORDS}'),
			array($content['LessonTitle'],	$content['text'], $content['keywords']),
			$html_template);

			//$zipfile->add_file($content['text'], $content_path.$content['LessonFile'], time());

			/* add the resource dependancies */
			//$my_files = array();
			$content_files = "\n";
			xml_parse($parser,$content['text']);

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
					

				//				$file_path =  $config['courseurl'].'/' . $parent_id. '/' . $file;
				$file_path =  COURSE_DIR.'/' .$content['CourseID'] . '/' . $file;


				/* check if this file exists in the content dir, if not don't include it */
				if (file_exists($file_path) && 	!in_array($file_path, $zipped_files)) {
					$zipped_files[] = $file_path;

					$dir = dirname($file).'/';
					//echo "path==".$content_path.$dir."<br>";
					if (!in_array($dir, $paths)) {
						//$zipfile->priv_add_dir($content_path.$dir, time());
						$zipfile->addEmptyDir($content_path.$dir);
						$paths[] = $dir;
					}

					$file_info = stat( $file_path );
					//					$zipfile->add_file(file_get_contents($file_path), 'resources/' . $content['content_path'] . $file, $file_info['mtime']);
					//$zipfile->add_file(file_get_contents($file_path), $content_path.$file, $file_info['mtime']);
					$zipfile->addFromString($content_path.$file,file_get_contents($file_path));
					//					fwrite($file1,$file_path."\r\n");
					$fileZiped[] = $file_path;
				}

				if (file_exists($file_path) && 	!in_array($file_path, $files)) {
					$files[] = $file_path;
					$content_files .= str_replace('{FILE}',  $file, $ims_template_xml['file']);
				}
					
			}

			/******************************/
			if ($content['Type']=='1') {
				$content['LessonFile'] = _QUESTIONPREFIX.$content['LessonFile'].'.txt';
			}
			$resources .= str_replace(	array('{LESSON_ID}', '{LESSON_FILE}', '{FILES}'),
			array($content['LessonID'], $content['LessonFile'], $content_files),
			$ims_template_xml['resource']);

			for ($i=0; $i<$depth; $i++) {
				$link .= $space;
			}

			$lessontitle = str_replace("\r\n",':::',$content['LessonTitle']);
			$title = $prefix.$space.'<title>'.$lessontitle.'</title>';

			$abstract = str_replace("\r\n",':::',$content['Abstract']);

			if(strlen(trim($abstract)) > 0)
			{
				$metadata = '<metadata>
          <imsmd:lom>
            <imsmd:general>
              <imsmd:description>
                <imsmd:langstring xml:lang="en">'.$abstract.'</imsmd:langstring>
              </imsmd:description>
            </imsmd:general>
            <imsmd:technical>
              <imsmd:duration>
                <imsmd:datetime>P'.$content['Length'].'D</imsmd:datetime>
              </imsmd:duration>
            </imsmd:technical>
          </imsmd:lom>
        </metadata>';
			}
			else
			{
				$metadata = '';
			}

			if ( is_array($_menu[$content['LessonID']]) ) {
				/* has children */

				$html_link = '<li>'.$html_link.'<ul>';
				for ($i=0; $i<$depth; $i++) {
					if ($children[$i] == 1) {
						echo $space;
						//$html_link = $space.$html_link;
					} else {
						echo $space;
						//$html_link = $space.$html_link;
					}
				}

			} else {
				/* doesn't have children */
				$html_link = '<li>'.$html_link.'</li>';
				if ($counter == $num_items) {
					for ($i=0; $i<$depth; $i++) {
						if ($children[$i] == 1) {
							echo $space;
							//$html_link = $space.$html_link;
						} else {
							echo $space;
							//$html_link = $space.$html_link;
						}
					}
				} else {
					for ($i=0; $i<$depth; $i++) {
						echo $space;
						//$html_link = $space.$html_link;
					}
				}
				$title = $space.$title;
			}

			echo $prefix.$link;
			echo $title;
			echo "\n";
			echo $metadata;
			echo "\n";

			$string .= $html_link."\n";

			$depth ++;
			print_organizations($content['LessonID'],
			$_menu,
			$depth,
			$path.$counter.'.',
			$children,
			$string);
			$depth--;

			$counter++;
			for ($i=0; $i<$depth; $i++) {
				echo $space;
			}
			echo $prefix.'</item>';
			echo "\n";
		}
		$string .= '</ul>';
		if ($depth > 0) {
			$string .= '</li>';
		}

	}
}
/*<?xml version="1.0" encoding="utf-8"  standalone="no" ?>*/
$ims_template_xml['header'] = '<?xml version="1.0" encoding="UTF-8"?>
<manifest xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.imsglobal.org/xsd/imscp_v1p1" xmlns:adlcp="http://www.adlnet.org/xsd/adlcp_rootv1p2" xmlns:imsmd="http://www.imsglobal.org/xsd/imsmd_v1p2" xsi:schemaLocation="http://www.adlnet.org/xsd/adlcp_rootv1p2 adlcp_rootv1p2.xsd http://www.imsglobal.org/xsd/imscp_v1p1 imscp_v1p1.xsd http://www.imsglobal.org/xsd/imsmd_v1p2 imsmd_v1p2p4.xsd" identifier="{COURSE_TITLE}">
 <metadata>
    <schema>ADL SCORM</schema>
    <schemaversion>1.2</schemaversion>
    <imsmd:lom>
      <imsmd:general>
        <imsmd:title>
          <imsmd:langstring xml:lang="en">{COURSE_TITLE}</imsmd:langstring>
        </imsmd:title>
        <imsmd:description>
          <imsmd:langstring xml:lang="en">{COURSE_DESCRIPTION}</imsmd:langstring>
        </imsmd:description>
      </imsmd:general>
    </imsmd:lom>
  </metadata>	
  ';
/*$ims_template_xml['resource'] = '		<resource identifier="MANIFEST01_RESOURCE{LESSON_ID}"  type="webcontent" adlcp:scormtype="sco" href="resources/{LESSON_FILE}">
 <metadata/>
 {FILES}
 </resource>'."\n";*/
$ims_template_xml['resource'] = '		<resource identifier="MANIFEST01_RESOURCE{LESSON_ID}"  type="webcontent" adlcp:scormtype="sco" href="resources/{LESSON_FILE}">
		</resource>'."\n";

$ims_template_xml['file'] = '			<file href="resources/{FILE}"/>'."\n";

$ims_template_xml['final'] = '
	<organizations default="MANIFEST01_ORG1">
		<organization identifier="MANIFEST01_ORG1" structure="hierarchical">
			<title>{COURSE_TITLE}</title>
{ORGANIZATIONS}
		</organization>
	</organizations>
	<resources>
{RESOURCES}
	</resources>
</manifest>';

$html_template = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<style type="text/css">
	body { font-family: Verdana, Arial, Helvetica, sans-serif;}
	</style>
	<title>{TITLE}</title>
	<meta name="Generator" content="ATutor'.VERSION.'">
	<meta name="Keywords" content="{KEYWORDS}">
</head>
<body>{CONTENT}</body>
</html>';



//output this as header.html
$html_mainheader = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="ims.css"/>
	<title></title>
</head>
<body class="headerbody"><h3>{COURSE_TITLE}</h3></body></html>';


$html_toc = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="ims.css" />
	<title></title>
</head>
<body>{TOC}</body></html>';

//output this Index.html Edit by Neetiwit At 31/12/2006
$html_frame = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
   "http://www.w3.org/TR/html4/frameset.dtd">
<html>
<head>
	<title>{COURSE_TITLE}</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<script	language="javascript" src="SCORMGenericLogic.js"></script>
	<script type="text/javascript" language="JavaScript"> 
			function EvalMultipleChoiceItem(obj) 
			{ 
				var v = obj.value; 
				if (!isNaN(v)) 
				{ 
					SCOSetValue("cmi.core.score.raw",v); 
				} 
			} 
			function ShowSCORMStatus()
			{ 
				alert("Current status is " + SCOGetValue("cmi.core.lesson_status")) 
			} 
			function ShowFullNameStudent()
			{
				alert("Hello " + SCOGetValue("cmi.core.student_name"))
			}
	</script> 
</head>
<frameset rows="50,*,50">
<frame src="header.html" name="header" title="header" scrolling="no">
	<frameset cols="25%, *" frameborder="1" framespacing="3" onload="SCOInitialize()" onunload="SCOFinish()" onbeforeunload="SCOFinish()">
		<frame frameborder="2" marginwidth="0" marginheight="0" src="toc.html" name="frame" title="TOC">
		<frame frameborder="2" src="resources/{FIRST_ID}" name="body" title="{COURSE_TITLE}">
	</frameset>
<frame src="footer.html" name="footer" title="footer" scrolling="no">
	<noframes>
      <p><br />
	  </p>
  </noframes>
</frameset>
</html>';

?>