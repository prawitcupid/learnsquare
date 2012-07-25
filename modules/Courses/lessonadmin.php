<?php
/**
 *  Lesson administration
 */
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}
/*
 * lessonsave fuctions
 */
// Inc +++SMT
require_once 'lib/nusoap.php';
require_once 'modules/SMT/class_smt.php';
//require_once 'modules/SMT/lexitran/getcid_en.php';



/*
 * lesson fuctions
 */
function lesson($vars) {

	global $menus, $links;

	// Get arguments from argument array
	extract($vars);
	if(!isset($action)) $action='';
	if(!isset($item)) $item='';
	switch (@$action) {
		case "add_lesson" : addLesson($vars); break;
		case "add_lesson_Hotpotatoes" : addHotpotatoes($vars); break;
		case "add_assignment" : addAssignment($vars); break;
		case "delete_lesson" : deleteLesson($vars); break;
		case "update_lesson": updateLesson($vars); break;
		case "increase_weight": increaseLessonWeight($vars); break;
		case "decrease_weight": decreaseLessonWeight($vars); break;
		case "shift_right": shiftRightLesson($vars); break;
		case "shift_left":		shiftLeftLesson($vars); break;
	}
	/** Navigator **/
	$courseinfo = lnCourseGetVars($cid);
	$menus[]= $courseinfo['title'];
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

	tabCourseAdmin($cid,2);

	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width= 100% cellpadding=0 cellspacing=0  border=0>';
	echo '<tr><td align=center valign=top><BR>';
	echo '<table cellpadding=0 cellspacing=0  border=0><tr>';
	echo '<td><a href="javascript:menu2.open()"><img src="images/treeview/open.gif" border=0/>&nbsp;'._OPENTREE.'</a></td><td>&nbsp;|&nbsp;</td><td><a href="javascript:menu2.close()"><img src="images/treeview/closed.gif" border=0/>&nbsp;'._CLOSETREE.'</a></td>';
	echo '</tr></table>';

	echo '<script language="JavaScript">'. "\n";
	echo 'var menu2 = new TREEMENU(false); '. "\n";
	echo '</script>'. "\n";
	$level = 1;
	$total = listLesson(@$action,$cid,@$item,0,0,$orderings=array(),$level,@$titlerepository,@$filerepository);
	echo '<script language="JavaScript">'. "\n";
	echo 'menu2.floating = false;';                    // we don't want a floating menu
	echo 'menu2.bgColor = "";';                        // we don't want a menu background
	echo 'menu2.title = "";';	 // we want a title
	echo 'menu2.titleBGColor = "";';                   // we don't want a title background
	echo 'menu2.itemBGColor = "";';                    // we don't want an item background
	echo 'menu2.itemBGColor1 = "";';                  // we don't want a level-one-item background
	echo 'menu2.itemBold = true;';                     // we want menu items with bold text
	echo 'menu2.create();';                            // we create the menu
	if(isset($_POST['cnode']) )
	{
		echo 'menu2.jumpTo('.$_POST['cnode'].');';
	}
	else if(isset($_GET['cnode']) )
	{
		echo 'menu2.jumpTo('.$_GET['cnode'].');';
	}

	echo '</script>';
	// if empty lesson
	if ($total == 1) {
		if (@$action == "insert_lesson") {
			addLessonForm($action,$cid,$item,0,0,1,'','');
		}
		if (@$action == "insert_assignment") {
			addAssignmentForm($action,$cid,$item,0,0);
		}
		if (@$action == "insert_lesson_Hotpotatoes") {
			addLessonFormHotpotatoe($action,$cid,$item,0,0);
		}
		if (@$action == "add_tts") {
			addTTSForm($action,$cid,$item,0,0);
		}// modify by xeonkung

		// bottom add lesson button
		if (@$action != "insert_lesson" && @$action != "edit_lesson" && @$action != "insert_lesson_Hotpotatoes" && @$action != "insert_assignment")  {

			echo '<BR><BR>'._NOLESSON;
			echo '<BR><BR><FORM METHOD=POST ACTION="index.php" >';
				
			// change add lesson button to drop down list menu
			echo '<select name="action">';
			echo '<option value="insert_lesson" selected>'._LESSON.'</option>';
			if (lnConfigGetVar('RepositoryStatus')){
				echo '<option value="repository" >'._INSERT_REPOSITORY.'</option>';
			}
			echo '<option value="insert_lesson_Hotpotatoes">'._ADD_HOT.'</option>';
			echo '<option value="insert_assignment">'._ADD_ASSIGNMENT.'</option>';
			echo '</select>';
			//***********
				
			echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
			.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
			.'<INPUT TYPE="hidden" NAME="op" VALUE="lesson">'
			//.'<INPUT TYPE="hidden" NAME="action" VALUE="insert_lesson">'
			.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
			.'<INPUT class="button_org" TYPE="SUBMIT" VALUE="'._ADD.'">';
			echo '</FORM>';

		}
	}

	echo '</td></tr></table>';

	echo '</TD></TR></TABLE>';

	include 'footer.php';

}

/**
 * add lesson form
 */
function addLessonForm($action,$cid,$weight,$lid,$lid_parent,$cnode,$titlerepository,$filerepository) {

	//echo "HELLO";
	if($titlerepository!=""){
		$title = $titlerepository;
		$description = $titlerepository;
	}

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];


	echo '<FORM NAME="Lesson" METHOD=POST ACTION="index.php">';

	if ($action == "insert_lesson") {
		echo '<INPUT TYPE="hidden" NAME="action" VALUE="add_lesson">';
		$weight += 0.5;
	}else if ($action == "edit_lesson") {
		$result = $dbconn->Execute("SELECT * FROM $lessonstable WHERE $lessonscolumn[lid]='". lnVarPrepForStore($lid) ."'");
		list($lid,$cid,$title,$description,$file,$duration,$_,$_,$type,$smt) = $result->fields;

		if ($type == 1) {
			addQuizForm('edit_quiz',$cid,$weight,$lid,$lid_parent);
			return;
		}
		echo '<INPUT TYPE="hidden" NAME="action" VALUE="update_lesson">'
		.'<INPUT TYPE="hidden" NAME="lid" VALUE="'.$lid.'">'
		.'<INPUT TYPE="hidden" NAME="cnode" VALUE="'.$cnode.'">';
	}

	$coursepath= COURSE_DIR . "/" .$cid;

	// create course directory
	if (!file_exists($coursepath)) {
		mkdir($coursepath);
	}
	// check blank keyword for add lesson
	?>
<script language="javaScript">
		function formSubmit() {
			if(checkFields()) document.forms.Lesson.submit();
		}
    	function checkFields() {
			var title = document.forms.Lesson.lesson_title.value;
			var description = document.forms.Lesson.lesson_desc.value;
			var duration = document.forms.Lesson.lesson_duration.value;
		
			if (title  == "" ) {
				alert("<?=_LESSONTITLE?>?");
				document.forms.Lesson.lesson_title.focus();
				return false;
			}
			if (description  == "" ) {
				alert("<?=_LESSONDESCRIPTION?>?");
				document.forms.Lesson.lesson_desc.focus();
				return false;
			}
			if (duration  == "" ) {
				alert("<?=_LESSONDURATION?>?");
				document.forms.Lesson.lesson_duration.focus();
				return false;
			}
			if (isComposedOfChars("0123456789.",duration)) {
				alert("must be number");
				document.forms.Lesson.lesson_duration.focus();
				return false;
			}

			return true; 
		}

		function isComposedOfChars(testSet, input) {
				for (var j=0; j<input.length; j++) {
					if (testSet.indexOf(input.charAt(j), 0) == -1){
						return true;
					}
				}
			return false;
		}	
</script>
	<?php

	if (empty($duration)) $duration=0;

	echo '<fieldset><legend>'._LESSON.'</legend>';  //lesson frame
	echo '<TABLE WIDTH="98%" CELLPADDING="2" CELLSPACING=0 BORDER=0">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="lesson">'
	.'<INPUT TYPE="hidden" NAME="lid_parent" VALUE="'.$lid_parent.'">'
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="lesson_weight" VALUE="'.$weight.'">';
	
	echo '<TR><TD WIDTH=90>'._LESSONTITLE.'</TD><TD><INPUT TYPE="text" NAME="lesson_title" SIZE="60"  style="width:90%" VALUE="'. lnVarPrepForDisplay(@$title).'"></TD></TR>'
	.'<TR><TD WIDTH=90 VALIGN="TOP">'._LESSONDESCRIPTION.'</TD><TD><TEXTAREA NAME="lesson_desc" ROWS="5" COLS="30" wrap="soft" style="width: 90%;">'.lnVarPrepForDisplay(@$description).'</TEXTAREA></TD></TR>'
	.'<TR><TD WIDTH=90>'._LESSONDURATION.'</TD><TD><INPUT TYPE="text" NAME="lesson_duration" SIZE="3" VALUE="'.lnVarPrepForDisplay($duration).'">&nbsp;'._DURATIONUNIT.'</TD></TR>'
	.'<TR><TD WIDTH=90>'._LESSONFILE.'</TD><TD><SELECT  CLASS="select" NAME="lesson_file">';   //define('_LESSONTITLE','เธเธทเนเธญเธเธ—เน€เธฃเธตเธขเธ');
	//define('_LESSONDESCRIPTION','เน€เธเธทเนเธญเธซเธฒเธขเนเธญ');
	//define('_LESSONDURATION','เธฃเธฐเธขเธฐเน€เธงเธฅเธฒ');
	//define('_DURATIONUNIT','เธงเธฑเธ') //define('_LESSONFILE','เนเธเธฅเนเธเธ—เน€เธฃเธตเธขเธ');
	 

	if ($action == "edit_lesson") {
		echo '<OPTION VALUE="'.$file.'" selected>'.$file.'</OPTION>';
	}
	else {
		$lessonfile = _LESSONPREFIX . getLastLessonID() . '.html';
		if($filerepository!=''){
			$lessonfile = $filerepository;
		}
		echo '<OPTION VALUE="'.$lessonfile.'">'.$lessonfile.'</OPTION>';
	}

	// list file in course directory
	$d = dir($coursepath);
	$files = array();
	for ($i=0; $entry=$d->read();$i++)
	{
		if ($entry != "." && $entry != "..")
		{
			if (strpos($entry, ".html") || strpos($entry, ".mht") || strpos($entry, ".htm") || strpos($entry, ".pdf") || strpos($entry, ".wmv") || strpos($entry,".swf") || strpos($entry,".ppt") || strpos($entry,".avi") || strpos($entry,".mp4") || strpos($entry,".flv") || strpos($entry,".mkv"))
			{ //เนเธเนเธ•เธฃเธเธเธตเนเธเธฐ
				$files[] = $entry;
				//echo '<OPTION>'.$entry.'</OPTION>';
			}
		}
	}
	sort($files);
	foreach($files as $key => $val)
	{
		echo '<OPTION>'.$val.'</OPTION>';
	}
	$d->close();

	echo '</SELECT>&nbsp;';
	echo '</TD></TR>';
	$SMTStatus = lnConfigGetVar('SMTStatus');
	if ($action == "edit_lesson") {
		if($SMTStatus){
			echo '<TR><TD>'._LEESONSMT.'</TD><TD><input type="checkbox" name="lesson_smt" value="1" ';
			if($smt)echo 'checked="checked"';
			echo '></TD></TR>';
		}
	}
	if ($action == "insert_lesson" ) {
		echo '<TR><TD WIDTH=90 VALIGN="TOP">&nbsp;<TD><BR><INPUT CLASS="button" TYPE="button" VALUE="'. _ADDLESSON. '" onclick="formSubmit()">';  //define('_ADDLESSON','เน€เธโฌเน€เธยเน€เธเธ”เน€เธยเน€เธเธเน€เธยเน€เธโ€”เน€เธโฌเน€เธเธเน€เธเธ•เน€เธเธเน€เธย');
	}
	else if ($action == "edit_lesson") {
		echo '<TR><TD WIDTH=90 VALIGN="TOP">&nbsp;<TD><BR><INPUT CLASS="button" TYPE="button" VALUE="'. _UPDATELESSON. '" onclick="formSubmit()">';   //define('_UPDATELESSON','เน€เธยเน€เธยเน€เธยเน€เธยเน€เธยเน€เธยเน€เธโ€”เน€เธโฌเน€เธเธเน€เธเธ•เน€เธเธเน€เธย');
	}
	echo " <INPUT CLASS=\"button\" TYPE=button VALUE="._CANCEL." OnClick=\"javascript:window.open('index.php?mod=Courses&file=admin&op=lesson&cid=$cid','_self')\"><BR><BR></TD></TR></FORM>";
	echo '</TABLE>';
	echo '</fieldset>';

}


/**
 * add Assignment form
 */
function addAssignmentForm($action,$cid,$weight,$lid,$lid_parent,$cnode) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	echo '<FORM NAME="Lesson" METHOD=POST ACTION="index.php">';

	if ($action == "insert_assignment") {
		echo '<INPUT TYPE="hidden" NAME="action" VALUE="add_assignment">';
		$weight += 0.5;
	}else if ($action == "edit_lesson") {
		$result = $dbconn->Execute("SELECT * FROM $lessonstable WHERE $lessonscolumn[lid]='". lnVarPrepForStore($lid) ."'");
		list($lid,$cid,$title,$description,$file,$duration,$_,$_,$type) = $result->fields;

		if ($type == 1) {
			addQuizForm('edit_quiz',$cid,$weight,$lid,$lid_parent);
			return;
		}
		echo '<INPUT TYPE="hidden" NAME="action" VALUE="update_lesson">'
		.'<INPUT TYPE="hidden" NAME="cnode" VALUE="'.$cnode.'">'
		.'<INPUT TYPE="hidden" NAME="lid" VALUE="'.$lid.'">';
	}

	$coursepath= COURSE_DIR . "/" .$cid;

	// create course directory
	if (!file_exists($coursepath)) {
		mkdir($coursepath);
	}

	?>
<script language="javaScript">
		function formSubmit() {
			if(checkFields()) document.forms.Lesson.submit();
		}
    	function checkFields() {
			var title = document.forms.Lesson.lesson_title.value;
			var description = document.forms.Lesson.lesson_desc.value;
			var duration = document.forms.Lesson.lesson_duration.value;
		
			if (title  == "" ) {
				alert("<?=_ASSIGNMENTTITLE?>?");
				document.forms.Lesson.lesson_title.focus();
				return false;
			}
			if (description  == "" ) {
				alert("<?=_ASSIGNMENTDESCRIPTION?>?");
				document.forms.Lesson.lesson_desc.focus();
				return false;
			}
			if (duration  == "" ) {
				alert("<?=_LESSONDURATION?>?");
				document.forms.Lesson.lesson_duration.focus();
				return false;
			}
			if (isComposedOfChars("0123456789.",duration)) {
				alert("must be number");
				document.forms.Lesson.lesson_duration.focus();
				return false;
			}

			return true; 
		}

		function isComposedOfChars(testSet, input) {
				for (var j=0; j<input.length; j++) {
					if (testSet.indexOf(input.charAt(j), 0) == -1){
						return true;
					}
				}
			return false;
		}	
</script>
	<?

	if (empty($duration)) $duration=0;


	echo '<fieldset><legend>'._ADD_ASSIGNMENT.'</legend>';  	 // assignment frame
	echo '<TABLE WIDTH="98%" CELLPADDING="2" CELLSPACING=0 BORDER=0">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="lesson">'
	.'<INPUT TYPE="hidden" NAME="lid_parent" VALUE="'.$lid_parent.'">'
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="cnode" VALUE="'.$cnode.'">'
	.'<INPUT TYPE="hidden" NAME="lesson_weight" VALUE="'.$weight.'">';

	echo '<TR><TD WIDTH=90>'._ASSIGNMENTTITLE.'</TD><TD><INPUT TYPE="text" NAME="lesson_title" SIZE="60"  style="width:90%" VALUE="'. lnVarPrepForDisplay(@$title).'"></TD></TR>'
	.'<TR><TD WIDTH=90 VALIGN="TOP">'._ASSIGNMENTDESCRIPTION.'</TD><TD><TEXTAREA NAME="lesson_desc" ROWS="5" COLS="30" wrap="soft" style="width: 90%;">'.lnVarPrepForDisplay(@$description).'</TEXTAREA></TD></TR>'
	.'<TR><TD WIDTH=90>'._LESSONDURATION.'</TD><TD><INPUT TYPE="text" NAME="lesson_duration" SIZE="3" VALUE="'.lnVarPrepForDisplay($duration).'">&nbsp;'._DURATIONUNIT.'</TD></TR>'
	.'<TR><TD WIDTH=90>'._ASSIGNMENTFILE.'</TD><TD><SELECT  CLASS="select" NAME="lesson_file">';   //define('_LESSONTITLE','เธเธทเนเธญเธเธ—เน€เธฃเธตเธขเธ');
	//define('_LESSONDESCRIPTION','เน€เธเธทเนเธญเธซเธฒเธขเนเธญ');
	//define('_LESSONDURATION','เธฃเธฐเธขเธฐเน€เธงเธฅเธฒ');
	//define('_DURATIONUNIT','เธงเธฑเธ') //define('_LESSONFILE','เนเธเธฅเนเธเธ—เน€เธฃเธตเธขเธ');

	if ($action == "edit_lesson") {
		echo '<OPTION VALUE="'.$file.'" selected>'.$file.'</OPTION>';
	}
	else {
		$lessonfile = _LESSONPREFIX . getLastLessonID() . '.html';
		echo '<OPTION VALUE="'.$lessonfile.'">'.$lessonfile.'</OPTION>';
	}

	// list file in course directory
	$d = dir($coursepath);
	for ($i=0; $entry=$d->read();$i++) {
		if ($entry != "." && $entry != "..") {
			if (strpos($entry, ".html") || strpos($entry, ".mht") || strpos($entry, ".htm") || strpos($entry, ".pdf") || strpos($entry, ".wmv") || strpos($entry,".swf") || strpos($entry,".mp4") || strpos($entry,".flv") || strpos($entry,".mkv"))
			{
				echo '<OPTION>'.$entry.'</OPTION>';
			}
		}
	}
	$d->close();

	echo '</SELECT>&nbsp;';
	echo  '</TD></TR>';
	if ($action == "insert_assignment" ) {
		echo '<TR><TD WIDTH=90 VALIGN="TOP">&nbsp;<TD><BR><INPUT CLASS="button" TYPE="button" VALUE="'. _INSERT_ASSIGNMENT. '" onclick="formSubmit()">';  //define('_ADDLESSON','เน€เธเธดเนเธกเนเธเธเธฒเธ');
	}
	else if ($action == "edit_lesson") {
		echo '<TR><TD WIDTH=90 VALIGN="TOP">&nbsp;<TD><BR><INPUT CLASS="button" TYPE="button" VALUE="'. _UPDATELESSON. '" onclick="formSubmit()">';  //define('_UPDATELESSON','เนเธเนเนเธเธเธ—เน€เธฃเธตเธขเธ');
	}
	echo " <INPUT CLASS=\"button\" TYPE=button VALUE="._CANCEL." OnClick=\"javascript:window.open('index.php?mod=Courses&file=admin&op=lesson&cid=$cid','_self')\"><BR><BR></TD></TR></FORM>";
	echo '</TABLE>';
	echo '</fieldset>';

}
/**
 * add Text To Speech form by Xeonkung , Peerapon
 */
function addTTSForm($action,$cid,$weight,$lid,$lid_parent,$cnode) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	echo '<FORM NAME="Lesson" METHOD=POST ACTION="index.php">';


	if ($action == "add_tts") {
		$result = $dbconn->Execute("SELECT * FROM $lessonstable WHERE $lessonscolumn[lid]='". lnVarPrepForStore($lid) ."'");
		list($lid,$cid,$title,$description,$file,$duration,$_,$_,$type) = $result->fields;
		echo '<INPUT TYPE="hidden" NAME="action" VALUE="add_tts">'
		.'<INPUT TYPE="hidden" NAME="cnode" VALUE="'.$cnode.'">'
		.'<INPUT TYPE="hidden" NAME="lid" VALUE="'.$lid.'">';
	}
	?>
<script language="javaScript">	
var xmlHttp;
function createXMLHttpRequest(){
	if(window.ActiveXObject){
		xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	else if(window.XMLHttpRequest){
		xmlHttp = new XMLHttpRequest();
	}
}
function requestCustomerInfo(){
	createXMLHttpRequest();
	document.getElementById("middle").innerHTML='<div id="wait"><img src="images/ajax-loader.gif" alt="" /><br /><br />Now Loading</div>';
	xmlHttp.open("get","tts.php?CID=<?=$cid?>&FILE=<?=$file?>&VAJA=<?=lnConfigGetVar('VajaServiceAddr')?>&VAJAWAV=<?=lnConfigGetVar('VajaServiceWav')?>",true);
	xmlHttp.onreadystatechange = function(){
		if(xmlHttp.readyState == 4){
			if(xmlHttp.status == 200){
				displayInfo(xmlHttp.responseText);
			}else{
				displayInfo("Error : " + xmlHttp.statusText);
			}
			document.forms.Lesson.btn_add.disabled=true;
		}
	};
	xmlHttp.send(null);
}
function displayInfo(){
	document.getElementById("middle").innerHTML = xmlHttp.responseText;
}
function runner(){
	window.open('index.php?mod=Courses&file=admin&op=lesson&cid=<?=$cid?>','_self');
}
</script>
	<?
	if (empty($duration)) $duration=0;
	echo 	'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="lesson">'
	.'<INPUT TYPE="hidden" NAME="lid_parent" VALUE="'.$lid_parent.'">'
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="lesson_weight" VALUE="'.$weight.'">';
	echo	'<div id="header" style="background-color:#696969; padding:5px; text-align:left; font-size:20pt; color:#FFFFFF;">'.
		'<h>'._EDITSOUND.'</h>'.
		'</div>'.
		'<div id="menu" style="padding:5px; vertical-align:middle; text-align:left; font-size:12px; padding-left: 10px;">'.
		' <input type="hidden" id="dataget" value="'. lnVarPrepForDisplay($file).'" disabled = "disabled" /><br><br>'	.
		'<input type="button" name="btn_add" value="'._ADDSOUND.'" onClick="requestCustomerInfo()"/>'.
		'<input type="button" value="'._BACK.'" onClick="runner()">'.
		'</div>'.
		'<div id="middle" style="padding-left: 10px; padding-top: 10px; overflow:auto;"></div>';
}

/**
 * add lesson form insert quiz Hotpotatoes
 */
function  addLessonFormHotpotatoe($action,$cid,$weight,$lid,$lid_parent,$cnode) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];


	echo '<FORM NAME="Lesson" METHOD=POST ACTION="index.php">';

	if ($action == "insert_lesson_Hotpotatoes") {
		echo '<INPUT TYPE="hidden" NAME="action" VALUE="add_lesson_Hotpotatoes">';
		$weight += 0.5;
	}else if ($action == "edit_lesson") {
		$result = $dbconn->Execute("SELECT * FROM $lessonstable WHERE $lessonscolumn[lid]='". lnVarPrepForStore($lid) ."'");
		list($lid,$cid,$title,$description,$file,$duration,$_,$_,$type) = $result->fields;

		if ($type == 1) {
			addQuizForm('edit_quiz',$cid,$weight,$lid,$lid_parent);
			return;
		}
		echo '<INPUT TYPE="hidden" NAME="action" VALUE="update_lesson">'
		.'<INPUT TYPE="hidden" NAME="cnode" VALUE="'.$cnode.'">'
		.'<INPUT TYPE="hidden" NAME="lid" VALUE="'.$lid.'">';
	}

	$coursepath= COURSE_DIR . "/" .$cid;

	// create course directory
	if (!file_exists($coursepath)) {
		mkdir($coursepath);
	}

	?>
<script language="javaScript">
		function formSubmit() {
			if(checkFields()) document.forms.Lesson.submit();
		}
    	function checkFields() {
			var title = document.forms.Lesson.lesson_title.value;
			var description = document.forms.Lesson.lesson_desc.value;
			var duration = document.forms.Lesson.lesson_duration.value;
		
			if (title  == "" ) {
				alert("<?=_LESSONTITLEHOT?>?");
				document.forms.Lesson.lesson_title.focus();
				return false;
			}
			if (description  == "" ) {
				alert("<?=_LESSONDESCRIPTIONHOT?>?");
				document.forms.Lesson.lesson_desc.focus();
				return false;
			}
			if (duration  == "" ) {
				alert("<?=_LESSONDURATION?>?");
				document.forms.Lesson.lesson_duration.focus();
				return false;
			}
			if (isComposedOfChars("0123456789.",duration)) {
				alert("must be number");
				document.forms.Lesson.lesson_duration.focus();
				return false;
			}

			return true; 
		}

		function isComposedOfChars(testSet, input) {
				for (var j=0; j<input.length; j++) {
					if (testSet.indexOf(input.charAt(j), 0) == -1){
						return true;
					}
				}
			return false;
		}	
</script>
	<?

	if (empty($duration)) $duration=0;

	echo '<fieldset><legend>'._LESSONHOT.'</legend>';  //define('_LESSON','เธเธ—เน€เธฃเธตเธขเธ');
	echo '<TABLE WIDTH="98%" CELLPADDING="2" CELLSPACING=0 BORDER=0">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="lesson">'
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="cnode" VALUE="'.$cnode.'">'
	.'<INPUT TYPE="hidden" NAME="lesson_weight" VALUE="'.$weight.'">';

	echo '<TR><TD WIDTH=90>'._LESSONTITLEHOT.'</TD><TD><INPUT TYPE="text" NAME="lesson_title" SIZE="60"  style="width:90%" VALUE="'. lnVarPrepForDisplay(@$title).'"></TD></TR>'
	.'<TR><TD WIDTH=90 VALIGN="TOP">'._LESSONDESCRIPTIONHOT.'</TD><TD><TEXTAREA NAME="lesson_desc" ROWS="5" COLS="30" wrap="soft" style="width: 90%;">'.lnVarPrepForDisplay(@$description).'</TEXTAREA></TD></TR>'
	.'<TR><TD WIDTH=90>'._LESSONDURATION.'</TD><TD><INPUT TYPE="text" NAME="lesson_duration" SIZE="3" VALUE="'.lnVarPrepForDisplay($duration).'">&nbsp;'._DURATIONUNIT.'</TD></TR>'
	.'<TR><TD WIDTH=90>'._LESSONFILEHOT.'</TD><TD><SELECT  CLASS="select" NAME="lesson_file">';   //define('_LESSONTITLE','เธเธทเนเธญเธเธ—เน€เธฃเธตเธขเธ');
	//define('_LESSONDESCRIPTION','เน€เธเธทเนเธญเธซเธฒเธขเนเธญ');
	//define('_LESSONDURATION','เธฃเธฐเธขเธฐเน€เธงเธฅเธฒ');
	//define('_DURATIONUNIT','เธงเธฑเธ') //define('_LESSONFILE','เนเธเธฅเนเธเธ—เน€เธฃเธตเธขเธ');
	 

	if ($action == "edit_lesson") {
		echo '<OPTION VALUE="'.$file.'" selected>'.$file.'</OPTION>';
	}
	else {
		$lessonfile = _LESSONPREFIX . getLastLessonID() . '.html';
		echo '<OPTION VALUE="'.$lessonfile.'">'.$lessonfile.'</OPTION>';
	}

	// list file in course directory
	$d = dir($coursepath);
	for ($i=0; $entry=$d->read();$i++) {
		if ($entry != "." && $entry != "..") {
			if (strpos($entry, ".html") || strpos($entry, ".htm")) { //เนเธเนเธ•เธฃเธเธเธตเนเธเธฐ
				echo '<OPTION>'.$entry.'</OPTION>';
			}
		}
	}
	$d->close();

	echo '</SELECT>&nbsp;';
	echo  '</TD></TR>';
	if ($action == "insert_lesson_Hotpotatoes") {
		echo '<TR><TD WIDTH=90 VALIGN="TOP">&nbsp;<TD><BR><INPUT CLASS="button" TYPE="button" VALUE="'. _ADDLESSONHOT. '" onclick="formSubmit()">';  //define('_ADDLESSONHOT','เน€เธเธดเนเธกเธเธ—เน€เธฃเธตเธขเธ');
	}
	else if ($action == "edit_lesson") {
		echo '<TR><TD WIDTH=90 VALIGN="TOP">&nbsp;<TD><BR><INPUT CLASS="button" TYPE="button" VALUE="'. _UPDATELESSON. '" onclick="formSubmit()">';   //define('_UPDATELESSON','เนเธเนเนเธเธเธ—เน€เธฃเธตเธขเธ');
	}
	echo " <INPUT CLASS=\"button\" TYPE=button VALUE="._CANCEL." OnClick=\"javascript:window.open('index.php?mod=Courses&file=admin&op=lesson&cid=$cid','_self')\"><BR><BR></TD></TR></FORM>";
	echo '</TABLE>';
	echo '</fieldset>';

}


/**
 * recursive list lesson
 */
function listLesson($action,$cid,$item,$lid_parent,$lid_prev_parent,$orderings,$level,$titlerepository,$filerepository) {
	static $active_count=0;

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];

	$query = "SELECT $lessonscolumn[lid],
	$lessonscolumn[title],
	$lessonscolumn[description],
	$lessonscolumn[file],
	$lessonscolumn[duration],
	$lessonscolumn[weight],
	$lessonscolumn[lid_parent],
	$lessonscolumn[type],
	$lessonscolumn[smt]
		FROM $lessonstable
		WHERE $lessonscolumn[cid]='". lnVarPrepForStore($cid) ."'
		AND $lessonscolumn[lid_parent]='".$lid_parent."'
		ORDER BY $lessonscolumn[weight]";
	$result = $dbconn->Execute($query);
	
	$numrows = $result->PO_RecordCount();
	$rownum = 1;

	/// List lesson
	while(list($lid,$title,$description,$file,$duration,$weight,$lid_parent,$type,$smt) = $result->fields) {
		//SMT - RUN
		//echo '>>>'.lnConfigGetVar('SMTServiceAddr');
		$SMTStatus = lnConfigGetVar('SMTStatus');
		if($smt&&$SMTStatus){
			$lessonfile = COURSE_DIR . '/' . $cid . '/' . $file;
			SMT_Run($lessonfile);
		}
		//-----------------
		$title = addslashes($title);
		$result->MoveNext();
			
		if ($type == 1) {
			$quizinfo = lnQuizGetVars($file);
			$title = $quizinfo['name'];
			$title .= '?';
		}
		$active_count++;

		array_push($orderings,$weight);
		$show_item=join('.',$orderings);
		$goto_item=join(',',$orderings);

		$tmp = array();
		$tmp = explode(",", $goto_item);
		$tmp1 = "";
		if(count($tmp) != 1)
		{
			for($j = 0;$j<=count($tmp) ;$j++)
			{
				if( $j<(count($tmp) - 1) )
				$tmp1 .= $tmp[$j].',';
				else
				{
					$tmp1 .= $tmp[$j];
				}
			}
		}
		else
		{
			$tmp1 = $tmp[0]+1;
		}
		$down = "index.php?mod=Courses&file=admin&op=lesson&action=increase_weight&cid=$cid&parent_lid=$lid_parent&lid=$lid&weight=$weight&cnode=$tmp1";

		$tmp = array();
		$tmp = explode(",", $goto_item);
		$tmp1 = "";
		if(count($tmp) != 1)
		{
			for($j = 0;$j<=count($tmp) - 1 ;$j++)
			{
				if( $j < count($tmp) - 1)
				$tmp1 .= $tmp[$j].',';
				else
				{
					$tmp1 .= $tmp[$j] - 1;
				}
			}
		}
		else
		{
			$tmp1 = $tmp[0] - 1;
		}
		$up = "index.php?mod=Courses&file=admin&op=lesson&action=decrease_weight&cid=$cid&parent_lid=$lid_parent&lid=$lid&weight=$weight&cnode=$tmp1";
		$tmp = array();
		$tmp = explode(",", $goto_item);
		$tmp1 = "";
		if(count($tmp) > 2)
		{
			for($j = 0;$j<=count($tmp) - 2;$j++)
			{
				$tmp1 .= $tmp[$j].',';
			}
			$tmp1 = substr($tmp1,0,strlen($tmp1) - 1);
		}
		else
		{
			$tmp1 = $tmp[0]+1;
		}

		$shift_left = "index.php?mod=Courses&file=admin&op=lesson&action=shift_left&cid=$cid&lid=$lid&parent_lid=$lid_parent&lid_prev_parent=$lid_prev_parent&weight=$weight&cnode=$tmp1";
		$tmp = array();
		$tmp = explode(",", $goto_item);
		$tmp1 = "";
		if(count($tmp) > 1)
		{
			for($j = 0;$j<=count($tmp) - 1;$j++)
			{
				if($j < count($tmp) - 1)
				$tmp1 .= $tmp[$j].',';
				else
				$tmp1 .= $tmp[$j] - 1;
			}
		}
		else
		{
			$tmp1 = $tmp[0] - 1;
			$tmp .= ',1';
		}
		$shift_right = "index.php?mod=Courses&file=admin&op=lesson&action=shift_right&cid=$cid&lid=$lid&parent_lid=$lid_parent&lid_prev_parent=$lid_prev_parent&weight=$weight&cnode=$tmp1";

		$lid_prev_parent=$lid;

		if ($action == "edit_lesson" && $active_count == $item) {
				
			if($type == 1) {
				addLessonForm($action,$cid,$item,$lid,$lid_parent,$goto_item);
			}else
				
			if($type == 0) {
				addLessonForm($action,$cid,$item,$lid,$lid_parent,$goto_item,$titlerepository,$filerepository);
			}
			else if($type == 3) {
				addAssignmentForm($action,$cid,$item,$lid,$lid_parent,$goto_item);
			}
			else if($type == 4) {
				addTTSForm($action,$cid,$item,$lid,$lid_parent,$goto_item);
			}// modify by xeonkung
			else if($type == 2) {
				addLessonFormHotpotatoe($action,$cid,$item,$lid,$lid_parent,$goto_item);
			}
			//echo '<BR>&nbsp;</td></tr>';
		}
		else {
			$title_line = '<FONT SIZE="12" face="Arial, Helvetica"><B>'.$show_item.' ' .$title.'</B></FONT>';
			//$title_line = '<IMG SRC="images/global/arrow.gif" WIDTH="7" HEIGHT="8" BORDER=0 ALT=""><FONT COLOR=#115E94> '.$show_item.". ". lnVarPrepForDisplay($title).'</FONT>';
			
			?>

<SCRIPT LANGUAGE="JavaScript">
var myMenu<?=$lid?> =
[
[null,'<?=$title_line?>',null,null,'Menu',
	['<img src="javascript/ThemeOffice/config.png" border=0/>','<?=_EDIT_LESSON?>','index.php?mod=Courses&file=admin&op=lesson&action=edit_lesson&item=<?=$active_count?>&weight=<?=$weight?>&cid=<?=$cid?>&lid=<?=$lid?>&parent_lid=<?=$lid_parent?>',null,'<?=_EDIT_LESSON?>'],

	<? if ($type==1) { ?>

	['<img src="javascript/ThemeOffice/preview.png" border=0 />','<?=_SHOW_LESSON?>','index.php?mod=Courses&op=lesson_show&cid=<?=$cid?>&lid=<?=$lid?>&qid=<?=$file?>',null,'<?=_SHOW_LESSON?>'],

<? 
	} else {
		if (strpos($file, ".html") || strpos($file, ".htm")) {
?>
   
	['<img src="javascript/ThemeOffice/edit.png" border=0 />','<?=_CREATE_LESSON?>                        ','javascript:popup("index.php?mod=spaw&type=Courses&cid=<?=$cid?>&lid=<?=$lid?>","_blank",750,480)',null,'<?=_CREATE_LESSON?>'],
		

<? } ?>	

	['<img src="javascript/ThemeOffice/preview.png" border=0 />','<?=_SHOW_LESSON?>','index.php?mod=Courses&op=lesson_show&cid=<?=$cid?>&lid=<?=$lid?>',null,'<?=_SHOW_LESSON?>'],

<? }?>
	
	_cmSplit,

<?				

		switch($rownum) {
						case 1:
							if ($numrows == 1) {
								$arrows = '';
							} else {
								?>
								['<img src="javascript/ThemeOffice/down.gif" border=0 />','<?=_MOVEDOWN?>','<?=$down?>',null,'<?=_MOVEDOWN?>'],
								<?
							}
							break;
						case $numrows:
								?>
								['<img src="javascript/ThemeOffice/up.gif" border=0 />','<?=_MOVEUP?>','<?=$up?>',null,'<?=_MOVEUP?>'],
								<?
							break;
						default:
								?>
								['<img src="javascript/ThemeOffice/up.gif" border=0 />','<?=_MOVEUP?>','<?=$up?>',null,'<?=_MOVEUP?>'],
								['<img src="javascript/ThemeOffice/down.gif" border=0 />','<?=_MOVEDOWN?>','<?=$down?>',null,'<?=_MOVEDOWN?>'],
								<?
							break;
				}

				switch($rownum) {
						case 1:
							if ($lid_parent != 0) {
								?>
								['<img src="javascript/ThemeOffice/shift_left.gif" border=0 />','<?=_MOVELEFT?>','<?=$shift_left?>',null,'<?=_MOVELEFT?>'],
								<?
							}
							break;
						default:
								if ($lid_parent != 0) {
									?>
									['<img src="javascript/ThemeOffice/shift_left.gif" border=0 />','<?=_MOVELEFT?>','<?=$shift_left?>',null,'<?=_MOVELEFT?>'],
									<?
								}
								?>
								['<img src="javascript/ThemeOffice/shift_right.gif" border=0 />','<?=_MOVERIGHT?>','<?=$shift_right?>',null,'<?=_MOVERIGHT?>'],
								<?
							break;
				}

?>

	['<img src="javascript/ThemeOffice/content.png" border=0 />','<?=_INSERT_LESSON?>','index.php?mod=Courses&file=admin&op=lesson&action=insert_lesson&item=<?=$active_count?>&weight=<?=$weight?>&cid=<?=$cid?>&lid=<?=$lid?>&parent_lid=<?=$lid_parent?>',null,'<?=_INSERT_LESSON?>'],
	<? if (lnConfigGetVar('RepositoryStatus')){ ?>	
	['<img src="javascript/ThemeOffice/content.png" border=0 />','<?=_INSERT_REPOSITORY?>','index.php?mod=Courses&file=admin&op=repository&action=search&item=<?=$active_count?>&weight=<?=$weight?>&cid=<?=$cid?>&lid=<?=$lid?>&parent_lid=<?=$lid_parent?>',null,'<?=_INSERT_LESSON?>'],
	<? } ?>
		['<img src="javascript/ThemeOffice/hot_logo.png" border=0 />','<?=_INSERT_LESSON_HOT?>','index.php?mod=Courses&file=admin&op=lesson&action=insert_lesson_Hotpotatoes&item=<?=$active_count?>&weight=<?=$weight?>&cid=<?=$cid?>&lid=<?=$lid?>&parent_lid=<?=$lid_parent?>',null,'<?=_INSERT_LESSON?>'],  /////ฟังก์ชันใน java script ?ี่ทำให้เกิดปุ่มสร้างข้อสอบจาก Hotpotatoes


	<? if (hasQuiz($cid)) {?>
	['<img src="javascript/ThemeOffice/help.png" border=0 />','<?=_INSERT_QUIZ?>','index.php?mod=Courses&file=admin&op=lesson&action=insert_quiz&item=<?=$active_count?>&weight=<?=$weight?>&cid=<?=$cid?>&lid=<?=$lid?>&parent_lid=<?=$lid_parent?>',null,'<?=_INSERT_QUIZ?>'],
	<? } ?>
	
	['<img src="javascript/ThemeOffice/assignment.png"  border=0 />','<?=_INSERT_ASSIGNMENT?>','index.php?mod=Courses&file=admin&op=lesson&action=insert_assignment&item=<?=$active_count?>&weight=<?=$weight?>&cid=<?=$cid?>&lid=<?=$lid?>&parent_lid=<?=$lid_parent?>',null,'<?=_INSERT_LESSON?>'],	//add assignment menu
	
	_cmSplit,



<? if ($type == 0) { ?>
	
	<? if (lnConfigGetVar('VajaStatus')){ ?>
		['<img src="javascript/ThemeOffice/speaker.png" border=0 />','<?=_EDITSOUND?>','index.php?mod=Courses&file=admin&op=lesson&action=add_tts&item=<?=$active_count?>&weight=<?=$weight?>&cid=<?=$cid?>&lid=<?=$lid?>&parent_lid=<?=$lid_parent?>',null,'<?=_EDITSOUND?>'],
	<? } ?>
	_cmSplit,

<? } ?>
	<?php
		$tmp = array();
		$tmp = explode(",", $goto_item);
		$tmp1 = "";
		for($j = 0;$j<=count($tmp) ;$j++)
		{
			if( $j<(count($tmp) - 1 ) )
				$tmp1 .= $tmp[$j].',';
		}
		$tmp1 = substr($tmp1,0,strlen($tmp1) - 1);
	?>

	['<img src="javascript/ThemeOffice/db.png" border=0 />','<?=_DELETE._LESSON?>','javascript: if(confirm("Delete lesson <?=$show_item?>?")) window.open("index.php?mod=Courses&file=admin&op=lesson&action=delete_lesson&cid=<?=$cid?>&lid=<?=$lid?>&parent_lid=<?=$lid_parent?>","_self")',null,'<?=_DELETE._LESSON?>']
	
]
];
</SCRIPT>
	<?php
	$order   = array("\r\n", "\n", "\r");
	$replace = '<br>';
	//$description = nl2br($description);
	//$description = strip_tags($description);
	//$description = nl2br($description,false);
	$description = str_replace($order, $replace, $description);
	?>
<script language="JavaScript" type="text/javascript">
	menu2.entry(<?php echo $level; ?>, "<span ID=myMenuID<?php echo $lid; ?>><?php echo $show_item. ' ' .$title.'<BR>'; ?><?php echo $description; ?>", "", "", "cmDraw('myMenuID<?php echo $lid; ?>', myMenu<?php echo $lid; ?>, 'hbr', cmThemeOffice,'ThemeOffice');");
</script>
	<?
	$rownum++;
		}

		if ($action == "insert_lesson" && $active_count == $item) {
			echo '<table width= 100% cellpadding=3 cellspacing=0 bgcolor=#EEEEEE border=0><tr valign=top valign=top>'
			.'<td  ALIGN="CENTER">';
			addLessonForm($action,$cid,$weight,$lid,$lid_parent,$goto_item,$titlerepository,$filerepository);
			echo '</td></tr></table>';
		}if ($action == "insert_lesson_Hotpotatoes" && $active_count == $item) {
			echo '<table width= 100% cellpadding=3 cellspacing=0 bgcolor=#EEEEEE border=0><tr valign=top valign=top>'
			.'<td  ALIGN="CENTER">';
			addLessonFormHotpotatoe($action,$cid,$weight,$lid,$lid_parent,$goto_item);
			echo '</td></tr></table>';
		}
		if ($action == "insert_assignment" && $active_count == $item) {
			echo '<table width= 100% cellpadding=3 cellspacing=0 bgcolor=#EEEEEE border=0><tr valign=top valign=top>'
			.'<td  ALIGN="CENTER">';
			addAssignmentForm($action,$cid,$weight,$lid,$lid_parent,$goto_item);
			echo '</td></tr></table>';
		}
		if ($action == "add_tts" && $active_count == $item) {
			echo '<table width= 100% cellpadding=3 cellspacing=0 bgcolor=#EEEEEE border=0><tr valign=top valign=top>'
			.'<td  ALIGN="CENTER">';
			addTTSForm($action,$cid,$weight,$lid,$lid_parent,$goto_item);
			echo '</td></tr></table>';
		}// modify by xeonkung
		if ($action == "insert_quiz" && $active_count == $item) {
			echo '<table width= 100% cellpadding=3 cellspacing=0 bgcolor=#EEEEEE border=0><tr valign=top valign=top>'
			.'<td  ALIGN="CENTER">';
			addQuizForm($action,$cid,$weight,$lid,$lid_parent);
			echo '</td></tr></table>';
		}

		//listLesson($action,$cid,$item,$lid,$lid_parent,$orderings);
		listLesson($action,$cid,$item,$lid,$lid_parent,$orderings,$level+1,$titlerepository,$filerepository);
		array_pop($orderings);
	}

	/// echo List lesson
	return $rownum;
}


/**
 * get next id lesson
 */
function getLastLessonID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$result = $dbconn->Execute("SELECT MAX($lessonscolumn[lid]) FROM $lessonstable");
	list($maxlid) = $result->fields;

	return $maxlid + 1;

}


/*
 * add quiz form
 */
function addQuizForm($action,$cid,$weight,$lid,$lid_parent) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$quiztable = $lntable['quiz'];
	$quizcolumn = &$lntable['quiz_column'];
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	?>
<script language="javaScript">
		function formSubmit() {
			if(checkFields()) document.forms.Lesson.submit();
			
			
		}
    	function checkFields() {
			var duration = document.forms.Lesson.lesson_duration.value;
		
			if (duration  == "" ) {
				alert("empty duration");
				document.forms.Lesson.lesson_duration.focus();
				return false;
			}
			if (isComposedOfChars("0123456789.",duration)) {
				alert("must be number");
				document.forms.Lesson.lesson_duration.focus();
				return false;
			}

			return true; 
		}

		function isComposedOfChars(testSet, input) {
				for (var j=0; j<input.length; j++) {
					if (testSet.indexOf(input.charAt(j), 0) == -1){
						return true;
					}
				}
			return false;
		}	
</script>
	<?
	echo '<BR><fieldset><legend>'._TEST.'</legend>';

	echo '<TABLE WIDTH="98%" CELLPADDING="2" CELLSPACING=0 BORDER=0">'
	.'<FORM NAME="Lesson" METHOD=POST ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="lesson">'
	.'<INPUT TYPE="hidden" NAME="lid_parent" VALUE="'.$lid_parent.'">'
	.'<INPUT TYPE="hidden" NAME="type" VALUE="1">' ////////////////////////////////////////////เธเธณเธซเธเธ”เนเธซเนเน€เธกเธทเนเธญเน€เธเธดเนเธกเธเธ—เน€เธฃเธตเธขเธเธกเธตเธเนเธฒเน€เธเนเธ 1 เธเธทเธญเธเธ—เน€เธฃเธตเธขเธ
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<INPUT TYPE="hidden" NAME="cnode" VALUE="'.$cnode.'">';

	if ($action == "insert_quiz") {
		echo '<INPUT TYPE="hidden" NAME="action" VALUE="add_lesson">';
		echo '<INPUT TYPE="hidden" NAME="subaction" VALUE="add_quiz">';
		$weight += 0.5;
	}
	else if ($action == "edit_quiz") {
		$result = $dbconn->Execute("SELECT * FROM $lessonstable WHERE $lessonscolumn[lid]='". lnVarPrepForStore($lid) ."'");
		list($lid,$cid,$title,$description,$file,$duration,$_) = $result->fields;
		echo '<INPUT TYPE="hidden" NAME="action" VALUE="update_lesson">'
		.'<INPUT TYPE="hidden" NAME="subaction" VALUE="update_quiz">'
		.'<INPUT TYPE="hidden" NAME="lid" VALUE="'.$lid.'">';
		$select[$file]='selected';
	}

	echo '<INPUT TYPE="hidden" NAME="lesson_weight" VALUE="'.$weight.'">';

	echo '<TR><TD WIDTH=90>ชุดแบบทดสอบ</TD><TD><SELECT  CLASS="select" NAME="lesson_file">';
	$query = "SELECT $quizcolumn[qid],$quizcolumn[name]
						FROM   $quiztable
						WHERE  $quizcolumn[cid] =  '" . lnVarPrepForStore($cid) . "'"; 
	$result = $dbconn->Execute($query);
	for($i=0; list($qid,$quiz_name) = $result->fields; $i++) {
		$result->MoveNext();
		$quiz_name = stripslashes($quiz_name);
		echo '<OPTION VALUE="'.$qid.'" '.$select[$qid].'>'.$quiz_name.'</OPTION>';
	}

	echo '</SELECT>&nbsp;';
	echo  '</TD></TR>';

	if (empty($duration)) $duration=0;

	echo '<TR><TD WIDTH=90>'._LESSONDURATION.'</TD><TD><INPUT CLASS="input" TYPE="text" NAME="lesson_duration" SIZE="3" VALUE="'.lnVarPrepForDisplay($duration).'">&nbsp;'._DURATIONUNIT.'</TD></TR>';

	if ($action == "insert_quiz") {
		echo '<TR><TD WIDTH=90 VALIGN="TOP">&nbsp;<TD><BR><INPUT CLASS="button_org" TYPE="button" VALUE="'. _ADDLESSONHOT. '" onclick="formSubmit()">';
	}
	else if ($action == "edit_quiz") {
		echo '<TR><TD WIDTH=90 VALIGN="TOP">&nbsp;<TD><BR><INPUT CLASS="button_org" TYPE="button" VALUE="'. _UPDATELESSON. '" onclick="formSubmit()">';
	}
	echo " <INPUT CLASS=\"button_org\" TYPE=button VALUE="._CANCEL." OnClick=\"javascript:window.open('index.php?mod=Courses&file=admin&op=lesson&cid=$cid','_self')\"></TD></TR></FORM>";
	echo '</TABLE>';
	echo '</fieldset>';

}


/*
 * insert lesson
 */
function addLesson($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	if (@$subaction == 'add_quiz') {
		$quizinfo = lnQuizGetVars($lesson_file);
		$lesson_title=$quizinfo['name'];
	}

	if(trim(lnVarPrepForStore(@$type)) == '')
	$tmp = '0';
	else
	$tmp=lnVarPrepForStore($type);
	$query = "INSERT INTO $lessonstable
				  ($lessonscolumn[cid],
				  $lessonscolumn[title],
				  $lessonscolumn[description],
				  $lessonscolumn[file],
				  $lessonscolumn[duration],
				  $lessonscolumn[weight],
				  $lessonscolumn[lid_parent],
				  $lessonscolumn[type],
				  $lessonscolumn[smt]
					  )
					VALUES ('" . lnVarPrepForStore($cid) . "',
						  '" . lnVarPrepForStore($lesson_title) . "',
						  '" . lnVarPrepForStore($lesson_desc) . "',
						  '" . lnVarPrepForStore($lesson_file) . "',
						  '" . lnVarPrepForStore($lesson_duration) . "',
						  '" . lnVarPrepForStore($lesson_weight) . "',
						  '" . lnVarPrepForStore($lid_parent) . "',
						  " . $tmp . ",
						  '" . lnVarPrepForStore(@$lesson_smt) . "'
					  )";

				  $dbconn->Execute($query);
				  $_POST['cid'];
				  if ($dbconn->ErrorNo() != 0) {
				  	return false;
				  }
				  else {
				  	resequenceLessons($cid,$lid_parent);
				  	return true;
				  }

}


/*
 * insert Hotpotatoes
 */
function addHotpotatoes($vars) {



	// Get arguments from argument array
	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	if ($subaction == 'add_quiz') {
		$quizinfo = lnQuizGetVars($lesson_file);
		$lesson_title=$quizinfo['name'];
	}


	$file = "courses" . '/' . $cid . '/' . $lesson_file;
	$file1 = "courses" . '/' . $cid . '/' . "test.htm";
	$string2  ="sent(Score);";
	$string6 = "<script type='text/javascript'>function sent(TotalScore){ window.open('modules/Courses/middle.php?TotalScore='+TotalScore,'','width=1000,height=700');}</script><!-- BeginTopNavButtons -->";
	if ($handle =  fopen($file,'r')){

		$fw = fopen($file1,"w");

		while (!feof($handle)) {
			$buffer = fgets($handle, 5000);
			if(strstr($buffer,$string2) || strstr($buffer,$string6) ){

			}else{
				fwrite($fw, $buffer);
			}

		}
		fclose($fw);
		fclose($handle);

		$file2 ="courses" . '/' . $cid . '/' . "save.htm";

		$handle =  fopen($file1,'r');
		$fw =  fopen($file2,'w');

		while (!feof($handle)) {

			$buffer = fgets($handle);

			$string1 = "<!-- BeginTopNavButtons -->";
			$string2  ="sent(Score);";
			$string6 = "setTimeout('Finish()', SubmissionTimeout);";
				
				
			if(strstr($buffer,$string6)){

				fwrite($fw,"sent(Score);");
				fwrite($fw, $buffer);

			}elseif(strstr($buffer,$string1)){

				/*fwrite($fw,"<form name='input' method='GET' action= '../../modules/Courses/middle.php'><center> <INPUT TYPE='hidden' NAME='bar'  id='ip' ></center></form>");

				fwrite($fw,'</form>');*/
				fwrite($fw,"<script type='text/javascript'>");
				fwrite($fw,'function sent(TotalScore){');
					
				//fwrite($fw,"var bar = document.xyz.ip();");
				//	fwrite($fw," window.open('../../modules/Courses/middle.php?TotalScore='+TotalScore+'&IP='+bar,'')");
				fwrite($fw," window.open('modules/Courses/middle.php?TotalScore='+TotalScore,'','width=1000,height=700');");
				fwrite($fw,"}");
				fwrite($fw,"</script>");
				fwrite($fw, $buffer);

			}else{
				fwrite($fw, $buffer);
			}
		}
		fclose($fw);
		fclose($handle);

	}
	$handle =  fopen($file2,'r');
	$fw = fopen($file,"w");
	while (!feof($handle)) {

		$buffer = fgets($handle, 5000);
		fwrite($fw, $buffer);
	}
	fclose($fw);
	fclose($handle);

	$handles =  fopen($file2,'r');

	$buffer = fread($handles,"30000000");


	$string2  ="sent(Score);";

	if(strstr($buffer,$string2)){
		$query = "INSERT INTO $lessonstable
				  (	$lessonscolumn[lid],
				  $lessonscolumn[cid],
				  $lessonscolumn[title],
				  $lessonscolumn[description],
				  $lessonscolumn[file],
				  $lessonscolumn[duration],
				  $lessonscolumn[weight],
				  $lessonscolumn[lid_parent],
				  $lessonscolumn[type]
					  )
					VALUES ('',
						  '" . lnVarPrepForStore($cid) . "',
						  '" . lnVarPrepForStore($lesson_title) . "',
						  '" . lnVarPrepForStore($lesson_desc) . "',
						  '" . lnVarPrepForStore($lesson_file) . "',
						  '" . lnVarPrepForStore($lesson_duration) . "',
						  '" . lnVarPrepForStore($lesson_weight) . "',
						  '" . lnVarPrepForStore($lid_parent) . "',
						  '2'
					  )";
				  $file2 ="courses" . '/' . $cid . '/' . "save.htm";
				  $file1 ="courses" . '/' . $cid . '/' . "test.htm";
				  unlink($file2);
				  unlink($file1);
				  $dbconn->Execute($query);
				  $_POST['cid'];
				  if ($dbconn->ErrorNo() != 0) {
				  	$file2 ="courses" . '/' . $cid . '/' . "save.htm";
				  	$file1 ="courses" . '/' . $cid . '/' . "test.htm";
				  	unlink($file2);
				  	unlink($file1);
				  	return false;
				  }
				  else {
				  	resequenceLessons($cid,$lid_parent);
				  	$file2 ="courses" . '/' . $cid . '/' . "save.htm";
				  	$file1 ="courses" . '/' . $cid . '/' . "test.htm";
				  	unlink($file2);
				  	unlink($file1);
				  	return true;
				  }
				  $file2 ="courses" . '/' . $cid . '/' . "save.htm";
				  $file1 ="courses" . '/' . $cid . '/' . "test.htm";
				  unlink($file2);
				  unlink($file1);
	}else{
		$query = "INSERT INTO $lessonstable
				  (	$lessonscolumn[lid],
				  $lessonscolumn[cid],
				  $lessonscolumn[title],
				  $lessonscolumn[description],
				  $lessonscolumn[file],
				  $lessonscolumn[duration],
				  $lessonscolumn[weight],
				  $lessonscolumn[lid_parent],
				  $lessonscolumn[type]
					  )
					VALUES ('',
						  '" . lnVarPrepForStore($cid) . "',
						  '" . lnVarPrepForStore($lesson_title) . "',
						  '" . lnVarPrepForStore($lesson_desc) . "',
						  '" . lnVarPrepForStore($lesson_file) . "',
						  '" . lnVarPrepForStore($lesson_duration) . "',
						  '" . lnVarPrepForStore($lesson_weight) . "',
						  '" . lnVarPrepForStore($lid_parent) . "',
						  '0'
					  )";
				  $file2 ="courses" . '/' . $cid . '/' . "save.htm";
				  $file1 ="courses" . '/' . $cid . '/' . "test.htm";
				  unlink($file2);
				  unlink($file1);
				  $dbconn->Execute($query);
				  $_POST['cid'];
				  if ($dbconn->ErrorNo() != 0) {
				  	$file2 ="courses" . '/' . $cid . '/' . "save.htm";
				  	$file1 ="courses" . '/' . $cid . '/' . "test.htm";
				  	unlink($file2);
				  	unlink($file1);
				  	return false;
				  }
				  else {
				  	resequenceLessons($cid,$lid_parent);
				  	$file2 ="courses" . '/' . $cid . '/' . "save.htm";
				  	$file1 ="courses" . '/' . $cid . '/' . "test.htm";
				  	unlink($file2);
				  	unlink($file1);
				  	return true;
				  }
				  	
				  fclose($handles);
				  $file2 ="courses" . '/' . $cid . '/' . "save.htm";
				  unlink($file2);
	}
	$file2 ="courses" . '/' . $cid . '/' . "save.htm";
	unlink($file2);



	/*
	 // Get arguments from argument array
	 extract($vars);

	 list($dbconn) = lnDBGetConn();
	 $lntable = lnDBGetTables();

	 $lessonstable = $lntable['lessons'];
	 $lessonscolumn = &$lntable['lessons_column'];

	 if ($subaction == 'add_quiz') {
		$quizinfo = lnQuizGetVars($lesson_file);
		$lesson_title=$quizinfo['name'];
		}

		if(trim(lnVarPrepForStore($type)) == '')
		$tmp = '2';
		else
		$tmp=lnVarPrepForStore($type);
		$query = "INSERT INTO $lessonstable
		($lessonscolumn[cid],
		$lessonscolumn[title],
		$lessonscolumn[description],
		$lessonscolumn[file],
		$lessonscolumn[duration],
		$lessonscolumn[weight],
		$lessonscolumn[lid_parent],
		$lessonscolumn[type]
		)
		VALUES ('" . lnVarPrepForStore($cid) . "',
		'" . lnVarPrepForStore($lesson_title) . "',
		'" . lnVarPrepForStore($lesson_desc) . "',
		'" . lnVarPrepForStore($lesson_file) . "',
		'" . lnVarPrepForStore($lesson_duration) . "',
		'" . lnVarPrepForStore($lesson_weight) . "',
		'" . lnVarPrepForStore($lid_parent) . "',
		" . $tmp . "
		)";

		$dbconn->Execute($query);
		$_POST['cid'];
	 if ($dbconn->ErrorNo() != 0) {
	 return false;
	 }
	 else {
		resequenceLessons($cid,$lid_parent);
		return true;
		}





		*/
}




/*
 * insert assignment
 */
function addAssignment($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	if ($subaction == 'add_quiz') {
		$quizinfo = lnQuizGetVars($lesson_file);
		$lesson_title=$quizinfo['name'];
	}

	if(trim(lnVarPrepForStore($type)) == '')
	$tmp = '3';
	else
	$tmp=lnVarPrepForStore($type);
	$query = "INSERT INTO $lessonstable
				  ($lessonscolumn[cid],
				  $lessonscolumn[title],
				  $lessonscolumn[description],
				  $lessonscolumn[file],
				  $lessonscolumn[duration],
				  $lessonscolumn[weight],
				  $lessonscolumn[lid_parent],
				  $lessonscolumn[type]
					  )
					VALUES ('" . lnVarPrepForStore($cid) . "',
						  '" . lnVarPrepForStore($lesson_title) . "',
						  '" . lnVarPrepForStore($lesson_desc) . "',
						  '" . lnVarPrepForStore($lesson_file) . "',
						  '" . lnVarPrepForStore($lesson_duration) . "',
						  '" . lnVarPrepForStore($lesson_weight) . "',
						  '" . lnVarPrepForStore($lid_parent) . "',
						  " . $tmp . "
					  )";

				  $dbconn->Execute($query);
				  $_POST['cid'];
				  if ($dbconn->ErrorNo() != 0) {
				  	return false;
				  }
				  else {
				  	resequenceLessons($cid,$lid_parent);
				  	return true;
				  }

}




/**
 * delete lesson
 */
function deleteLesson($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$query = "SELECT $lessonscolumn[lid_parent] FROM $lessonstable
			  WHERE $lessonscolumn[lid] = '". lnVarPrepForStore($lid) . "'";

	$result=$dbconn->Execute($query);
	list($lid_parent) = $result->fields;

	$query = "DELETE FROM $lessonstable
				  WHERE $lessonscolumn[lid] = '". lnVarPrepForStore($lid) . "'";

	$dbconn->Execute($query);

	deleteLessonLoop($lid);

	resequenceLessons($cid,$lid_parent);
}


/**
 * clear all child lesson
 */
function deleteLessonLoop($parent) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$query = "SELECT $lessonscolumn[lid],$lessonscolumn[cid],$lessonscolumn[file] FROM $lessonstable
			  WHERE $lessonscolumn[lid_parent] = '". lnVarPrepForStore($parent) . "'";

	$result=$dbconn->Execute($query);
	while(list($lid,$cid,$file) = $result->fields) {
		$result->MoveNext();

		$query = "DELETE FROM $lessonstable
			  WHERE $lessonscolumn[lid] = '". lnVarPrepForStore($lid) . "'";
		$dbconn->Execute($query);
		if (file_exists(COURSE_DIR.'/'.$cid.'/'.$file)) {
			unlink(COURSE_DIR.'/'.$cid.'/'.$file);
		}

		deleteLessonLoop($lid);
	}

}


/**
 * update lesson
 */
function updateLesson($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	if ($subaction == 'update_quiz') {
		$quizinfo = lnQuizGetVars($lesson_file);
		$lesson_title=$quizinfo['name'];
	}

	$query = "UPDATE $lessonstable SET
	$lessonscolumn[title] = '" . lnVarPrepForStore($lesson_title) . "',
	$lessonscolumn[description] = '" . lnVarPrepForStore($lesson_desc) . "',
	$lessonscolumn[duration] = '" . lnVarPrepForStore($lesson_duration) . "',
	$lessonscolumn[file] = '" . lnVarPrepForStore($lesson_file) . "',
	$lessonscolumn[smt] = '". lnVarPrepForStore($lesson_smt) . "'
					WHERE $lessonscolumn[lid]  = '" . lnVarPrepForStore($lid) . "'";

	$dbconn->Execute($query);

	if ($dbconn->ErrorNo() != 0) {
		return false;
	}
	else {
		//		resequenceLessons($cid,$);
		return true;
	}
}


/**
 * increase weight
 */
function increaseLessonWeight($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$seq = $weight;

	// Get info on displaced block
	$sql = "SELECT $lessonscolumn[lid],
	$lessonscolumn[weight]
            FROM $lessonstable
            WHERE $lessonscolumn[weight] >'" . lnVarPrepForStore($seq) . "'
            AND   $lessonscolumn[cid]='" . lnVarPrepForStore($cid) . "'
            AND   $lessonscolumn[lid_parent]='" . lnVarPrepForStore($parent_lid) . "'
            ORDER BY $lessonscolumn[weight] ASC";
	$result = $dbconn->SelectLimit($sql, 1);
	 
	if ($result->EOF) {
		return false;
	}
	list($altlid, $altseq) = $result->fields;
	$result->Close();

	// Swap sequence numbers
	$sql = "UPDATE $lessonstable
            SET $lessonscolumn[weight]=$seq
            WHERE $lessonscolumn[lid]='".lnVarPrepForStore($altlid)."'";
	$dbconn->Execute($sql);
	$sql = "UPDATE $lessonstable
            SET $lessonscolumn[weight]=$altseq
            WHERE $lessonscolumn[lid]='".lnVarPrepForStore($lid)."'";
	$dbconn->Execute($sql);

	resequenceLessons($cid,$parent_lid);

	return true;
}


/**
 * decrease weight
 */
function decreaseLessonWeight($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$seq = $weight;

	// Get info on displaced block
	$sql = "SELECT $lessonscolumn[lid],
	$lessonscolumn[weight]
            FROM $lessonstable
            WHERE $lessonscolumn[weight] < '" . lnVarPrepForStore($seq) . "'
            AND   $lessonscolumn[cid]='" . lnVarPrepForStore($cid) . "'
            AND   $lessonscolumn[lid_parent]='" . lnVarPrepForStore($parent_lid) . "'
            ORDER BY $lessonscolumn[weight] DESC";
	$result = $dbconn->SelectLimit($sql, 1);
	 
	if ($result->EOF) {
		return false;
	}
	list($altlid, $altseq) = $result->fields;
	$result->Close();

	// Swap sequence numbers
	$sql = "UPDATE $lessonstable
            SET $lessonscolumn[weight]=$seq
            WHERE $lessonscolumn[lid]='".lnVarPrepForStore($altlid)."'";
	$dbconn->Execute($sql);
	$sql = "UPDATE $lessonstable
            SET $lessonscolumn[weight]=$altseq
            WHERE $lessonscolumn[lid]='".lnVarPrepForStore($lid)."'";
	$dbconn->Execute($sql);

	resequenceLessons($cid,$parent_lid);

	return true;
}


/**
 * resequece weight
 */
function resequenceLessons($cid, $lid_parent) {

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	// Get the information
	$query = "SELECT $lessonscolumn[lid],
	$lessonscolumn[weight]
					 FROM $lessonstable 
					 WHERE $lessonscolumn[cid]= '". lnVarPrepForStore($cid)."'
					 AND $lessonscolumn[lid_parent]= '". lnVarPrepForStore($lid_parent)."'
               ORDER BY $lessonscolumn[weight]";
	$result = $dbconn->Execute($query);

	// Fix sequence numbers
	$seq=1;
	while(list($lid, $curseq) = $result->fields) {

		$result->MoveNext();
		if ($curseq != $seq) {
			$query = "UPDATE $lessonstable
                      SET $lessonscolumn[weight]='" . lnVarPrepForStore($seq) . "'
                      WHERE $lessonscolumn[lid]='" . lnVarPrepForStore($lid)."'";
			$dbconn->Execute($query);
		}
		$seq++;
	}
	$result->Close();

	return true;
}


/**
 * shift right Lesson
 */
function shiftRightLesson($vars) {
	// Get arguments from argument array
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$result = $dbconn->Execute("SELECT MAX($lessonscolumn[weight]) FROM $lessonstable WHERE $lessonscolumn[lid_parent]=$lid_prev_parent");
	list($max_weight) = $result->fields;

	$next_weight = $max_weight + 1;

	$sql = "UPDATE $lessonstable
            SET	$lessonscolumn[lid_parent]='".lnVarPrepForStore($lid_prev_parent)."',
            $lessonscolumn[weight]='$next_weight'
            WHERE $lessonscolumn[lid]='".lnVarPrepForStore($lid)."'";
            $dbconn->Execute($sql);

            resequenceLessons($cid,$lid_prev_parent);
            resequenceLessons($cid,$lid);
            resequenceLessons($cid,$parent_lid);

            return true;
}


/**
 * shift left Lesson
 */
function shiftLeftLesson($vars) {
	// Get arguments from argument array
	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];

	$result = $dbconn->Execute("SELECT $lessonscolumn[lid_parent],$lessonscolumn[weight] FROM $lessonstable WHERE $lessonscolumn[lid]=$parent_lid");
	list($upper_parent,$upper_weight) = $result->fields;

	$next_weight = $upper_weight + 0.5;

	$sql = "UPDATE $lessonstable
            SET	$lessonscolumn[lid_parent]='".lnVarPrepForStore($upper_parent)."',
            $lessonscolumn[weight]='$next_weight'
            WHERE $lessonscolumn[lid]='".lnVarPrepForStore($lid)."'";

            $dbconn->Execute($sql);

            resequenceLessons($cid,$upper_parent);
            resequenceLessons($cid,$lid);
            resequenceLessons($cid,$parent_lid);

            return true;
}
?>
