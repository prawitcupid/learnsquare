<?php
/**
*  Message Note
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Note::', "::", ACCESS_COMMENT)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}


/* - - - - - - - - - - - */
include 'header.php';

OpenTable();

//echo lnBlockTitle($mod);
echo '<p class="header"><b>'._NOTE_TITLE.'</b></p>';
echo '<BR>'._NOTE_DESC.'<BR>&nbsp;';

folderMenu();
echo '<HR SIZE="1">';

$config['foldericons']=array("modules/Note/images/foldericon.gif","modules/Note/images/msgpost.gif","modules/Note/images/i_mesg.gif"); // folder icons

$vars= array_merge($_GET,$_POST);	

if (!hasFolder()) {
	createFolder();
}

/* options */
switch ($op) {
	case "noteadd":			noteAdd($vars); break;
	case "noteaddsave": noteAddSave($vars); break;
	case "notedelete":		noteDelete($vars); break;
	case "postview":			postView($vars); break;
	case "foldernew":			folderNew($vars); break;
	case "foldernewsave":	folderNewSave(-1,-1,-1,1,$vars); break;
	case "folderedit":			folderEdit($vars); break;
	case "folderdelete":	folderDelete($vars); break;
	default :								folder($vars);
}

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */

function hasFolder() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];

	$query = "SELECT $notecolumn[folder_id] FROM $notetable WHERE $notecolumn[uid] = '".lnSessionGetVar('uid')."'";
	$result = $dbconn->Execute($query);
	list($folder_id)=$result->fields;
	if (empty($folder_id)) {
		return FALSE;
	}
	else {
		return TRUE;
	}
}

function createFolder() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];

	$fid = getnextFolder();
	$notetime = time();
	$query = "INSERT INTO $notetable ($notecolumn[folder_id],$notecolumn[uid],$notecolumn[subject],$notecolumn[type],$notecolumn[note],$notecolumn[notetime],$notecolumn[parent]) VALUES ('$fid','".lnSessionGetVar('uid')."','My Folder', '0', '', '$notetime', '0')";

	$result = $dbconn->Execute($query);
}

function getnextFolder() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];

	$query = "SELECT MAX($notecolumn[folder_id]) FROM $notetable";
	$result = $dbconn->Execute($query);
	list($max_id)=$result->fields;
	
	return $max_id + 1;
}

/*- - - แฟ้มข้อมูลสุดบันทึก - - -*/
function folder() {

?>
<script language="JavaScript" src="modules/Note/javascript/tree.js"></script>
<script language="JavaScript">
		var TREE_ITEMS = [['<? echo _CONTENT; ?>', 'index.php?mod=Note&amp;op=folder',
<?
	print_menu_tree(0,0);	
?>
		]];
</script>
<script language="JavaScript" src="modules/Note/javascript/tree_tpl.js"></script>
<script language="JavaScript">
	new tree (TREE_ITEMS, tree_tpl);
</script>

<?

}

/*- - - เมนูบันทึกข้อความ - - -*/
function folderMenu() {

	echo "<table width=100%>";
	echo "<tr valign=top><td><A HREF='index.php?mod=Note' class=a><img src='modules/Note/javascript/icons/base.gif' border=0>"._CONTENT."</A>&nbsp;&nbsp; <A HREF='index.php?mod=Note&amp;op=noteadd' class=a><img src='modules/Note/images/i_mesg.gif' border=0>"._ADDNOTE."</A>&nbsp;&nbsp; <A HREF='index.php?mod=Note&amp;op=foldernew' class=a><img src='modules/Note/images/folder.gif' border=0>"._ADDFOLDER."</A>&nbsp;&nbsp; <A HREF='index.php?mod=Note&amp;op=folderedit' class=a><img src='modules/Note/images/folders.gif' border=0>"._MANAGEFOLDER."</A></td></tr>";
	echo "</table>";
}

// function: display complete menu tree
// returns: HTML list
function print_menu_tree($id = 0,$edit) 
{
	global $config;

	$result = get_children($id);	
	for ($x=0; $x<sizeof($result); $x++)
	{
		if ($edit && $result[$x]["subject"] != "My Folder") {
			$renfolder ="<A HREF=index.php?mod=Note&amp;op=foldernew&folderid=".$result[$x]["id"]." class=b><img src=modules/Note/images/edit.gif border=0></A> ";
			$renfolder.="<A HREF=javascript:if(confirm(&quot;Are%20you%20sure?&quot;))window.open(&quot;index.php?mod=Note&amp;op=folderdelete&folderid=".$result[$x]["id"]."&quot;,&quot;_self&quot;);  class=b><img src=modules/Note/images/delete.gif border=0></A>";
		}
		else {
			$renfolder="";
		}

		if ($result[$x]["type"] == 0)  {
			if (get_children($result[$x]["id"]) <= 0 ) {
				echo "['" . $result[$x]["subject"]." $renfolder ',null";
			}
			else {
				echo "['" . $result[$x]["subject"]." $renfolder  ',null, ";
			}
		}
		else {
	//		echo "['" . $result[$x]["subject"]." <img src=".$config['foldericons'][$result[$x]["type"]]." border=0>','index.php?mod=Note&amp;op=postview&folderid=".$result[$x]["id"]."'";
			echo "['" . $result[$x]["subject"]."','index.php?mod=Note&amp;op=postview&folderid=".$result[$x]["id"]."'";
		}

		print_menu_tree($result[$x]["id"],$edit);	

		echo "],";
	}
}

// function: get next level of menu tree
// returns: array
function get_children($id)
{
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];

	$query = "SELECT $notecolumn[folder_id], $notecolumn[subject], $notecolumn[type], $notecolumn[note], $notecolumn[notetime] FROM $notetable WHERE $notecolumn[parent] = '$id' and $notecolumn[uid]='".lnSessionGetVar('uid')."' ORDER BY $notecolumn[subject]";	

	$result = $dbconn->Execute($query);

	for($count=0; list($folder_id,$subject,$type,$note,$notetime)=$result->fields; $count++ ) {
		$result->MoveNext();
		$children[$count]["id"] = $folder_id;	
		$children[$count]["subject"] = $subject;
		$children[$count]["type"] = $type;
		$children[$count]["note"] = $note;
	}

	return $children;
}

/*- - - ฟอร์มรับบันทึกข้อความ - - -*/
function noteAdd($vars) {
	// Get arguments from argument array
    extract($vars);

//	echo _NOTEADDDESC.'<BR>&nbsp;';

	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];

	if (@$fid) {
		$sql = "SELECT $notecolumn[subject], $notecolumn[note], $notecolumn[notetime], $notecolumn[parent] FROM $notetable";
		$sql .= " WHERE $notecolumn[folder_id]='$fid'";

		$result = $dbconn->Execute($sql);
		list($subject,$message,$notetime,$parent) = $result->fields;
	}
	
	?>
	  <script language="javaScript">
		function formSubmit(val) {
			document.forms.Noteadd.op.value = val;
			if(val == "Clear") document.forms.Noteadd.submit();
			else if(checkFields()) document.forms.Noteadd.submit();
		}
		
		function clearFields() {
			document.forms.Noteadd.reset();
		}
		
    	function checkFields() { 
			var subject = document.forms.Noteadd.subject.value;	
			if (subject  == "" ) {
				alert("ใส่ชื่อหัวข้อด้วยค่ะ");
				document.forms.Noteadd.subject.focus();
				return false;
			} 
			return true
		}
		</script>
	<?
	echo "<center><table bgcolor=#DDFFDD width=95% cellpadding=3 cellspacing=0 border=0>";
	echo "<FORM NAME=Noteadd METHOD=POST ACTION='index.php'>";
	echo "<INPUT TYPE=hidden name='mod' value='Note'>";
	echo "<INPUT TYPE=hidden name=op>";
	echo "<INPUT TYPE=hidden name=fid value=".@$fid.">";
	echo "<tr valign=top><td colspan=2 bgcolor=#669900 class=head>&nbsp;<B>"._ADDNOTE."</B></td></tr>";
	echo "<tr valign=top><td align=right width=100>หัวข้อ:</td><td><INPUT TYPE=\"text\" NAME=\"subject\" value=\"".@$subject."\" style='width:80%'></td></tr>";
	echo "<tr valign=top><td align=right>ข้อความ:</td><td><TEXTAREA NAME=\"message\" ROWS=10 COLS=30 style='width:90%'>".@$message."";
	echo "</TEXTAREA></td></tr>";
	echo "<tr><td align=right>เลือกเก็บที่แฟ้ม:</td><td><SELECT NAME=\"parent\">";
	$result = get_folder();	
	for ($x=0; $x<sizeof($result); $x++)
	{
		if ($result[$x]["id"] == $parent)
			$select="selected";
		else 
			$select="";
		echo "<OPTION VALUE=".$result[$x]["id"]." $select>".$result[$x]["subject"]."</OPTION>";
	}
	echo "</SELECT></td></tr>";
	echo "<tr valign=top><td></td><td>&nbsp;<BR><A href=\"javascript:formSubmit('noteaddsave')\"><img SRC=\"images/button/tha/save.gif\" border=0></A>&nbsp;<A href=\"javascript:window.open('index.php?mod=Note','_self')\"><img SRC=\"images/button/tha/cancel.gif\" border=0></A></td></tr></table>";
	echo "</FORM>";
}


// function: get folder
// returns: array
function get_folder()
{
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];

	$query = "SELECT $notecolumn[folder_id], $notecolumn[subject] FROM $notetable WHERE $notecolumn[type] = '0' and $notecolumn[uid]='".lnSessionGetVar('uid')."'";	
	$result = $dbconn->Execute($query);

	for($count=0; list($folder_id,$subject)=$result->fields; $count++ ) {
		$result->MoveNext();
		$folder[$count]["id"] = $folder_id;	
		$folder[$count]["subject"] = $subject;
	}

	return $folder;
}

/*- - - เก็บข้อมูลลง database - - -*/
function noteAddSave($vars) {
	// Get arguments from argument array
    extract($vars);
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];

	$subject = addslashes($subject);
	$message = addslashes($message);
	$notedate = time();

	if ($fid) {
		$sql = "UPDATE $notetable SET $notecolumn[subject]='$subject', $notecolumn[note]='$message', $notecolumn[parent]='$parent', $notecolumn[notetime]='$notedate' WHERE $notecolumn[folder_id]='$fid' ";
	}
	else {
		$sql = "INSERT INTO $notetable ($notecolumn[uid], $notecolumn[subject], $notecolumn[type], $notecolumn[note], $notecolumn[notetime], $notecolumn[parent]) ";
		$sql .= "VALUES ('".lnSessionGetVar('uid')."','$subject','2','$message','$notedate','$parent')";
	}

	$result = $dbconn->Execute($sql);

	folder();
}

/*- - - ลบบันทึก - - -*/
function noteDelete($vars) {
	// Get arguments from argument array
    extract($vars);
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];

	// Delete Note
	$sql = "DELETE FROM $notetable WHERE $notecolumn[folder_id]='$fid' ";
	$result = $dbconn->Execute($sql);

	folder();
}

/*- - - แสดงข้อมูล- - -*/
function postView($vars) {
	// Get arguments from argument array
    extract($vars);

//	echo "<BR>"._NOTEVIEWDESC."<HR SIZE=1>";
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];

	$sql = "SELECT $notecolumn[subject], $notecolumn[note], $notecolumn[notetime], $notecolumn[parent] FROM $notetable";
	$sql .= " WHERE $notecolumn[folder_id]='$folderid'";
	$result = $dbconn->Execute($sql);

	list($subject,$message,$notetime,$parent) = $result->fields;

	$subject=stripslashes($subject);
//	$subject=filter($subject,1);
	$message=nl2br(stripslashes($message));

//	$message=filter($message,1);
	
	$date=date('Y-m-d',$notetime);
	$notedate =Date_Calc::dateFormat2($date,"%e %B %Y")
	?>
	  <script language="javaScript">
		function formSubmit(val) {
			document.forms.Noteadd.op.value = val;
			document.forms.Noteadd.submit();
		}		
		</script>
	<?
	echo "<CENTER><table bgcolor=#FFFFFF width=95% cellpadding=3 cellspacing=2  border=0>";
	echo "<FORM NAME=Noteadd METHOD=POST ACTION='index.php'>";
	echo "<INPUT TYPE=hidden name=mod value=Note>";
	echo "<INPUT TYPE=hidden name=op>";
	echo "<INPUT TYPE=hidden name=fid value=$folderid>";
	echo "<tr valign=top><td bgcolor=#669900 class=head>&nbsp;<B>$subject</B></B></td></tr>";
	echo "<tr valign=top height=100><td bgcolor=#FFFFCC>$message</td></tr>";
	echo "<tr valign=top><td align=right>วันที่: $notedate</td></tr>";
	echo "<tr><td align=center> &nbsp;<A href=\"javascript:formSubmit('noteadd')\"><img SRC=\"images/button/tha/edit.gif\" border=0></A>&nbsp;<A href=\"javascript:if(confirm('Are you sure?')) formSubmit('notedelete')\"><img SRC=\"images/button/tha/delete.gif\" border=0></A>&nbsp;</td></tr></table></CENTER>";
	echo "</FORM>";
}

/*- - - สร้างแฟ้มข้อมูลใหม่ - - -*/
function folderNew($vars) {
	// Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];
	
	if (@$folderid) {
		$sql = "SELECT $notecolumn[subject], $notecolumn[parent] FROM $notetable";
		$sql .= " WHERE $notecolumn[folder_id]='$folderid'";

		$result = $dbconn->Execute($sql);
		
		list($subject,$parent) =$result->fields;
	}

//	echo "<BR>"._FOLDERNEWDESC."<HR SIZE=1>";

	?>
	    <script language="javaScript">
		function formSubmit(val) {
			document.forms.Foldernew.op.value = val;
			if(checkFields()) document.forms.Foldernew.submit();
		}
		
    	function checkFields() { 
			var name = document.forms.Foldernew.foldername.value;
		
			if (name  == "" ) {
				alert("ใส่ชื่อ folder ด้วยค่ะ");
				document.forms.Foldernew.foldername.focus();
				return false;
			} 
			else {
				return true
			}
		}
		</script>
	<?
	echo "<table bgcolor=#DDFFDD width=100% cellpadding=3 cellspacing=2  border=0>";
	echo "<FORM NAME=Foldernew METHOD=POST ACTION='index.php'>";
	echo "<INPUT TYPE=\"hidden\" name=mod value=Note>";
	echo "<INPUT TYPE=\"hidden\" name=op>";
	echo "<INPUT TYPE=\"hidden\" name=fid VALUE='".@$folderid."'>";
	echo "<tr valign=top><td colspan=2 bgcolor=#669900 class=head>&nbsp;<B>"._ADDFOLDER."</B></td></tr>";
	echo "<tr><td align=right width=20%>ตั้งชื่อแฟ้มใหม่:</td><td><INPUT TYPE=\"text\" NAME=\"foldername\" VALUE=\"".@$subject."\" style='width:80%'></td></tr>";
	echo "<tr><td align=right>อยู่ภายใต้แฟ้ม:</td><td><SELECT NAME=\"parent\">";

	$result = get_folder();
	if ($parent==0) {
		echo "<OPTION VALUE=0 selected><< สารบัญ >></OPTION>";
	}
	else  {
		echo "<OPTION VALUE=0><< สารบัญ >></OPTION>";
	}
	for ($x=0; $x<sizeof($result); $x++)
	{
		if ($result[$x]["id"] == $parent)
			$select="selected";
		else 
			$select="";
		echo "<OPTION VALUE=".$result[$x]["id"]." $select>".$result[$x]["subject"]."</OPTION>";
	}
	echo "</SELECT></td></tr>";
	
	echo "<tr><td></td><td>&nbsp;<BR>";
	if (@$folderid) {
		echo "&nbsp;<a href=\"javascript:formSubmit('foldernewsave')\"><img SRC=\"images/button/tha/edit.gif\" ALT='แก้ไข' border=0></A>";
	}
	else {
		echo "&nbsp;<a href=\"javascript:formSubmit('foldernewsave')\"><img SRC=\"images/button/tha/create.gif\" ALT='สร้าง' border=0></A>";
}
	echo "</td></tr></table>";
	echo "</FORM>";
}

/*- - - เพิ่มแฟ้มข้อมูลลงใน database - - -*/
function folderNewSave($fdn,$p,$f,$show,$vars) {
	// Get arguments from argument array
    extract($vars);

	if ($fdn != -1) {
		$foldername = $fdn;
	}
	if ($p != -1) {
		$parent = $p;
	}
	if ($f != -1) {
		$fid = $f;
	}

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];

	$foldername =  addslashes($foldername);
	$notetime=time();

	if ($fid) {
		// Rename folder
		$sql = "UPDATE $notetable SET $notecolumn[subject]='$foldername', $notecolumn[parent]='$parent' WHERE $notecolumn[folder_id]='$fid' ";
	}
	else {
		// Add folder
		$sql = "INSERT INTO $notetable ($notecolumn[uid], $notecolumn[subject], $notecolumn[type], $notecolumn[notetime], $notecolumn[parent]) ";
		$sql .= "VALUES ('".lnSessionGetVar('uid')."','$foldername','0','$notetime','$parent')";
	}
	
	$result = $dbconn->Execute($sql);

	if ($show) { 
		folder();
	}
}

/*- - - แก้ไขชื่อแฟ้ม - - -*/
function folderEdit($vars) {
		
	?>
	<TABLE WIDTH=98% cellspacing=5 cellpadding=0>
	<TR>
	<TD ALIGN="LEFT">
	<?

	?>
	<script language="javaScript">
		function formSubmit(val) {
			document.forms.Noteadd.action.value = val;
			document.forms.Noteadd.submit();
		}		
		</script>
	<?
	echo "<FORM NAME=Noteadd METHOD=POST ACTION='index.php'>";
	echo "<INPUT TYPE=\"hidden\" name=action>";

	?>
	<script language="JavaScript" src="modules/Note/javascript/tree.js"></script>
	<script language="JavaScript">
			var TREE_ITEMS = [['<? echo _CONTENT; ?>', 'index.php?mnod=Note&amp;op=folder',
	<?
		print_menu_tree(0,1);	
	?>
			]];
	</script>
	<script language="JavaScript" src="modules/Note/javascript/tree_tpl.js"></script>
	<script language="JavaScript">
		new tree (TREE_ITEMS, tree_tpl);
	</script>

	</TD>
	</TR>
	</TABLE>
	<?
}

/*- - - ลบแฟ้ม- - - -*/
function folderDelete($vars) {
	// Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];
	

	$sql = "DELETE FROM $notetable WHERE $notecolumn[folder_id]='$folderid'";

	$result = $dbconn->Execute($sql);

	//folderDeleteChild($folderid);

	folder();
}

/*- - - ลบลูกของแฟ้มข้อมูล- - - -*/
function folderDeleteChild ($folderid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$notetable = $lntable['note'];
	$notecolumn = &$lntable['note_column'];

	$result = get_children($folderid);	
	for ($x=0; $x<sizeof($result); $x++)
	{
		if ($result[$x]["subject"] != "My Folder") {
			$sql = "DELETE FROM $notetable WHERE $notecolumn[parent]='".$result[$x]["id"]."' ";
			$result = $dbconn->Execute($sql);
		}
		folderDeleteChild($result[$x]["id"]);	
	}
}

?>