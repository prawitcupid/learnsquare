<?php
/**
*  Private_Message module
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Private_Messages::', "::", ACCESS_COMMENT)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

$vars= array_merge($_GET,$_POST);

if ($op == "findnickname") {
	findNickname($vars);
	exit;
}

/* - - - - - - - - - - - */
include 'header.php';
OpenTable();

//echo lnBlockTitle($mod);
echo '<p class="header"><b>'._PRIVATE_MESSAGE_TITLE.'</b></p>';
//echo '<BR>'._NOTE_DESC.'<BR>&nbsp;';

echo '<TABLE  WIDTH=100% HEIGHT=400><TR VALIGN="TOP" ALIGN="CENTER"><TD>';

switch($op) {
	case "post":
			showPostForm($vars);
			break;
	case "send_message":
			sendMessage($vars);
			break;
	case "read_message":
			readMessage($vars);
			break;
	case "Save Marked":
			saveMessage($vars);
			showMessages($vars);
			break;
	case "Delete Message":
	case "Delete Marked":
			deleteMessage($vars);
			//showMessages($vars);
			break;
	case "inbox":
	case "sentbox":
	case "outbox":
	case "savebox":
	default:
			showMessages($vars);
}
echo '</TD></TR></TABLE>';
CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */


function findNickname($vars) {
	// Get arguments from argument array
    extract($vars);

	$thistheme = lnConfigGetVar('Default_Theme'); 
	echo '<html><head><title>Search</title></head>';
	echo "<link rel=\"StyleSheet\" href=\"themes/".$thistheme."/style/style.css\" type=\"text/css\">\n";
	echo '<FORM NAME="find_nickname" method="post" action="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Private_Messages">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="findnickname">';
	
	if(!isset($word)) $word='';
	echo '<P><CENTER><TABLE CELLPADDING=3 CELLSPACING=1 BGCOLOR="#0066CC" WIDTH="98%" HEIGHT="50">';
	echo '<TR><TD BGCOLOR="#0066CC" ALIGN="CENTER"> <FONT COLOR="#FF9900"><B>Find a nickname</B></FONT></TD></TR>';
	echo '<TR><TD BGCOLOR="#FFFFFF" ALIGN="LEFT">';
	echo '<B><INPUT TYPE="text" NAME="word" VALUE="'.$word.'"> <INPUT TYPE="submit" VALUE="Search"></B><BR>Use * as a wildcard for partial matches';
if (!empty($word)) {
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$userstable = $lntable['users'];
		$userscolumn = &$lntable['users_column'];
		$newword = str_replace("*","%",$word);
        $query = "SELECT $userscolumn[uname] FROM $userstable WHERE $userscolumn[uname] LIKE '". lnVarPrepForStore($newword) ."'";

		$result = $dbconn->Execute($query);
		
		?>
		<script language="javascript" type="text/javascript">
		<!--
		function refresh_username(selected_username)
		{
			opener.document.forms['post'].nickname.value = selected_username;
			opener.focus();
			window.close();
		}
		//-->
		</script>
		<?
		
		echo '<P><SELECT NAME="uname_list">';
		for($i=0; list($uname) = $result->fields; $i++) {
			$result->MoveNext();
			echo '<OPTION VALUE="'.$uname.'">'.$uname.'</OPTION>';
		}
		if ($i==0) {
			echo '<OPTION VALUE="">No matches found</OPTION>';		
		}
		echo '</SELECT>';
		echo ' <INPUT TYPE="submit" onClick="refresh_username(this.form.uname_list.options[this.form.uname_list.selectedIndex].value);return false;" name="use" value="Select">';

	}
	echo '<P><A class=b HREF="javascript:window.close()">Close Window</A>';
	echo '</TD></TR></TABLE></CENTER>';
	echo '</FORM>';
	
}

function saveMessage($vars) {
	// Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$privmsgstable = $lntable['privmsgs'];
	$privmsgscolumn = &$lntable['privmsgs_column'];
	
	foreach ($sel as $key => $val) {
		$query = "UPDATE $privmsgstable SET $privmsgscolumn[type] = '"._MESSAGESAVE."' WHERE $privmsgscolumn[id] = '".$key."'";
		$dbconn->Execute($query);
	}
}

function deleteMessage($vars) {
	// Get arguments from argument array
    extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$privmsgstable = $lntable['privmsgs'];
	$privmsgscolumn = &$lntable['privmsgs_column'];
	
	foreach ($sel as $key => $val) {
		$query = "DELETE FROM $privmsgstable WHERE $privmsgscolumn[id] = '".$key."'";
		$dbconn->Execute($query);
	}
	echo '<meta http-equiv="refresh" content="0;URL=index.php?mod=Private_Messages&amp;op='.$page.'" />';
	exit();
}

function readMessage($vars) {
	// Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$privmsgstable = $lntable['privmsgs'];
	$privmsgscolumn = &$lntable['privmsgs_column'];

	$query = "SELECT $privmsgscolumn[id],
						$privmsgscolumn[type],
						$privmsgscolumn[priority],
						$privmsgscolumn[subject],
						$privmsgscolumn[message],
						$privmsgscolumn[from_uid],
						$privmsgscolumn[to_uid],
						$privmsgscolumn[date],
						$privmsgscolumn[ip],
						$privmsgscolumn[enable] 
						FROM $privmsgstable
						WHERE $privmsgscolumn[id]='". lnVarPrepForStore($id) ."'";
	$result = $dbconn->Execute($query);	
	list($id,$type,$priority,$subject,$message,$from_uid,$to_uid,$date,$ip,$enable) = $result->fields;
	$subject = stripslashes($subject);
	$message = stripslashes($message);
	$message = nl2br($message);
	//editor : orrawin 
	$date = date('d-M-Y H:i', $date);
	
	messageHead($vars);

	echo '<FORM NAME="file_list" method="post" action="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Private_Messages">'
	.'<INPUT TYPE="hidden" NAME="sel['.$id.']" VALUE="1">';

	echo '<TABLE  WIDTH=100% CELLPADDING=2 CELLSPACING=0>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER">';
	echo '<TD ALIGN="LEFT"><A HREF="index.php?mod=Private_Messages&amp;op=post&amp;to='.$from_uid.'&amp;subject=Re: '.$subject.'"><IMG SRC="modules/Private_Messages/images/reply.gif" BORDER=0 ALT="Reply Message"></A></TD>';
	echo '<TD ALIGN="RIGHT">&nbsp;</TD>';
	echo '</TR>';
	ecHo '</TABLE>';

	echo '<TABLE  WIDTH=100% CELLPADDING=0 CELLSPACING=1 BGCOLOR="#006699">';
	echo '<TR><TD>';
	echo '<TABLE  WIDTH=100% CELLPADDING=2 CELLSPACING=1 BGCOLOR="#FFFFFF">';
	echo '<TR HEIGHT=20 VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#5B5BFF">';
	echo '<TD COLSPAN=2 ALIGN="CENTER"><FONT  COLOR="#FF9900"><B>Inbox :: Message</B></FONT></TD>';
	echo '</TR>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#E1E1FF">';
	echo '<TD ALIGN="LEFT" WIDTH=8%>'._FROM.':</TD>';
	echo '<TD ALIGN="LEFT">'. lnUserGetVar('uname',$from_uid).'</TD>';
	echo '</TR>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#E1E1FF">';
	echo '<TD ALIGN="LEFT" WIDTH=8%>'._TO.':</TD>';
	echo '<TD ALIGN="LEFT">'.lnUserGetVar('uname',$to_uid).'</TD>';
	echo '</TR>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#E1E1FF">';
	echo '<TD ALIGN="LEFT" WIDTH=8%>&nbsp;</TD>';
	//echo '<TD ALIGN="LEFT">'.Date_Calc::dateFormat3($date, "%e %b %y").$date.'</TD>';
	//editor : orrawin 
	echo '<TD ALIGN="LEFT">'.$date.'</TD>';
	echo '</TR>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#E1E1FF">';
	echo '<TD ALIGN="LEFT" VALIGN="TOP" WIDTH=8%>'._SUBJECT.':</TD>';
	echo '<TD ALIGN="LEFT">'.$subject.'</TD>';
	echo '</TR>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	echo '<TD ALIGN="LEFT" COLSPAN=2 HEIGHT="200" VALIGN="TOP">'.$message.'</TD>';
	echo '</TR>';
	
	echo '<TR HEIGHT=1 BGCOLOR=#CCE6FF><TD COLSPAN="5"></TD></TR>';
	echo '<TR HEIGHT=25 BGCOLOR=#99CCCC><TD COLSPAN="5" ALIGN="RIGHT"><INPUT TYPE="submit" NAME="op" VALUE="Save Message"> <INPUT TYPE="submit"   NAME="op" VALUE="Delete Message"></TD></TR>';

	echo '</TABLE>';
	echo '</TD></TR>';
	echo '</TABLE>';

	echo '<TABLE  WIDTH=100% CELLPADDING=2 CELLSPACING=0>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER">';
	echo '<TD ALIGN="LEFT"><A HREF="index.php?mod=Private_Messages&amp;op=post"><IMG SRC="modules/Private_Messages/images/reply.gif" BORDER=0 ALT="Reply to Message"></A></TD>';
	echo '<TD ALIGN="RIGHT">&nbsp;</TD>';
	echo '</TR>';
	ecHo '</TABLE>';
	
	//_MESSAGEREAD = 0 _MESSAGESENT = 2
	if(lnSessionGetVar('uid')!=$from_uid){
		if ($type != _MESSAGEREAD) {
			if($type !=_MESSAGESENT) {
				changeStateToRead($id);
				changeStateToSent($id);
			}
		}
	}
}

function changeStateToView($id) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$privmsgstable = $lntable['privmsgs'];
	$privmsgscolumn = &$lntable['privmsgs_column'];
	$query = "UPDATE $privmsgstable SET $privmsgscolumn[type]='"._MESSAGEVIEW."'
						WHERE $privmsgscolumn[id]='". lnVarPrepForStore($id) ."' AND $privmsgscolumn[type]='"._MESSAGESEND."'";
	$result = $dbconn->Execute($query);	
}

function changeStateToRead($id) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$privmsgstable = $lntable['privmsgs'];
	$privmsgscolumn = &$lntable['privmsgs_column'];
	$query = "UPDATE $privmsgstable SET $privmsgscolumn[type]='"._MESSAGEREAD."'
						WHERE $privmsgscolumn[id]='". lnVarPrepForStore($id) ."' AND $privmsgscolumn[type]='"._MESSAGEVIEW."'";
	$result = $dbconn->Execute($query);	
}

function changeStateToSent($id) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$privmsgstable = $lntable['privmsgs'];
	$privmsgscolumn = &$lntable['privmsgs_column'];
	$query = "SELECT $privmsgscolumn[id],
						$privmsgscolumn[type],
						$privmsgscolumn[priority],
						$privmsgscolumn[subject],
						$privmsgscolumn[message],
						$privmsgscolumn[from_uid],
						$privmsgscolumn[to_uid],
						$privmsgscolumn[date],
						$privmsgscolumn[ip],
						$privmsgscolumn[enable] 
						FROM $privmsgstable
						WHERE $privmsgscolumn[id]='". lnVarPrepForStore($id) ."'";
	$result = $dbconn->Execute($query);	
	list($id,$type,$priority,$subject,$message,$from_uid,$to_uid,$date,$ip,$enable) = $result->fields;
	
	$id = getMaxPrivMsgID();
	//_MESSAGESENT = 2
	$type = _MESSAGESENT; 
	
	$query = "INSERT INTO $privmsgstable (
						$privmsgscolumn[id],
						$privmsgscolumn[type],
						$privmsgscolumn[priority],
						$privmsgscolumn[subject],
						$privmsgscolumn[message],
						$privmsgscolumn[from_uid],
						$privmsgscolumn[to_uid],
						$privmsgscolumn[date],
						$privmsgscolumn[ip],
						$privmsgscolumn[enable]
						)
						VALUES (
						'". lnVarPrepForStore($id) ."',
						'". lnVarPrepForStore($type) ."',
						'". lnVarPrepForStore($priority) ."',
						'". lnVarPrepForStore($subject) ."',
						'". lnVarPrepForStore($message) ."',
						'". lnVarPrepForStore($from_uid) ."',
						'". lnVarPrepForStore($to_uid) ."',
						'". lnVarPrepForStore($date) ."',
						'". lnVarPrepForStore($ip) ."',
						'1'
						) ";

	$result = $dbconn->Execute($query);
}


function showPostForm($vars) {
	// Get arguments from argument array
    extract($vars);

	messageHead($vars);

?>

<!-- Edit by BAS 03/06/49 -->

    <script language="javaScript">
		
		function checkFields(theforms) {

			if (theforms.nickname.value == "" ) {
				
				alert("<?=_ALERTENAME?>");
				theforms.nickname.focus();
				return false;
			}

			if (theforms.subject.value == "" ) {
				
				alert("<?=_ALERTESUBJECT?>");
				theforms.subject.focus();
				return false;
			}

			if (theforms.message.value == "" ) {
				
				alert("<?=_ALERTEMESS?>");
				theforms.message.focus();
				return false;
			}

			return true;
		}
</script>




<?
	if(!isset($to)) $to='';
	if(!isset($subject)) $subject='';
	echo '<FORM NAME="post" METHOD=POST ACTION="index.php" onSubmit="return checkFields(this)">';
	echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Private_Messages">';
	echo '<INPUT TYPE="hidden" NAME="op" VALUE="send_message">';
	echo '<TABLE  WIDTH=100% CELLPADDING=0 CELLSPACING=1 BGCOLOR="#006699">';
	echo '<TR><TD>';
	echo '<TABLE  WIDTH=100% CELLPADDING=1 CELLSPACING=1 BGCOLOR="#FFFFFF">';
	echo '<TR HEIGHT=20 VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#5B5BFF">';
	echo '<TD COLSPAN=2 ALIGN="CENTER"><FONT  COLOR="#FF9900"><B>Send a new private message</B></FONT></TD>';
	echo '</TR>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	echo '<TD ALIGN="LEFT" WIDTH=15%><B>'._NICKNAME.'</B></TD>';
	echo '<TD ALIGN="LEFT"><INPUT TYPE="text" NAME="nickname" VALUE="'.lnUserGetVar('uname',$to).'">';
	echo " <INPUT TYPE=\"BUTTON\" VALUE=\""._FIND_NICKNAME."\" OnClick=\"javascript:popup('index.php?mod=Private_Messages&amp;op=findnickname','findnickname',400,200)\">";
	echo '</TD>';
	echo '</TR>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	echo '<TD ALIGN="LEFT" WIDTH=15%><B>'._SUBJECT.'</B></TD>';
	echo '<TD ALIGN="LEFT"><INPUT TYPE="text" NAME="subject" SIZE="40" value="'.$subject.'"></TD>';
	echo '</TR>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	echo '<TD ALIGN="LEFT" WIDTH=15%>&nbsp;</TD>';
	echo '<TD ALIGN="LEFT"><INPUT TYPE="radio" NAME="priority" VALUE="0"> <IMG SRC="modules/Private_Messages/images/high_priority.gif" WIDTH="4" HEIGHT="10" BORDER=0 ALT=""> High Priority <INPUT TYPE="radio" NAME="priority" VALUE="1" checked> <IMG SRC="modules/Private_Messages/images/blank.gif" WIDTH="1" HEIGHT="10" BORDER=0 ALT="">Normal Priority <INPUT TYPE="radio" NAME="priority" VALUE="2">  <IMG SRC="modules/Private_Messages/images/low_priority.gif" BORDER=0 ALT=""> Low Priority</TD>';
	echo '</TR>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#FFFFFF">';
	echo '<TD ALIGN="LEFT" VALIGN="TOP" WIDTH=15%><B>'._MESSAGE.'</B></TD>';
	echo '<TD ALIGN="LEFT"><TEXTAREA NAME="message" ROWS="10" COLS="40" STYLE=WIDTH:90%></TEXTAREA></TD>';
	echo '</TR>';
	
	echo '<TR HEIGHT=1 BGCOLOR=#CCE6FF><TD COLSPAN="5"></TD></TR>';
	echo '<TR HEIGHT=25 BGCOLOR=#99CCCC><TD COLSPAN="5" ALIGN="CENTER"><INPUT TYPE="submit" name="submit" VALUE="'._SEND_MESSAGE.'"></TD></TR>';

	echo '</TABLE>';
	echo '</TD></TR>';
	echo '</TABLE>';
	echo '</FORM>';

}

function showMessages($vars) { 
	// Get arguments from argument array
    extract($vars);	// Get arguments from argument array
	
	// how many rows to show per page
	$rowsPerPage = lnConfigGetVar('pagesize');

	// by default we show first page
	$pageNum = 1;

	// if $_GET['page'] defined, use it as page number
	if(isset($_GET['page']))
	{
		$pageNum = $_GET['page'];
	}

	// counting the offset
	$offset = ($pageNum - 1) * $rowsPerPage;

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$privmsgstable = $lntable['privmsgs'];
	$privmsgscolumn = &$lntable['privmsgs_column'];

	messageHead($vars);

	?>
	<script language="Javascript" type="text/javascript">
	function select_switch()
	{
		for (i = 0; i < document.file_list.length; i++)
		{
			if (document.file_list.selectall.checked == false) {
				document.file_list.elements[i].checked = true;
			}
			else {
				document.file_list.elements[i].checked = false;
			}
		}
		if (document.file_list.selectall.checked == false) {
			document.file_list.selectall.checked = true;
		}
		else {
			document.file_list.selectall.checked = false;
		}
	}
</script>
	<?

	echo '<FORM NAME="file_list" method="post" action="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Private_Messages">'
	.'<INPUT TYPE="hidden" NAME="page" VALUE="'.$op.'">';
	
	echo '<TABLE  WIDTH=100% CELLPADDING=2 CELLSPACING=0>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER">';
	echo '<TD ALIGN="LEFT"><A HREF="index.php?mod=Private_Messages&amp;op=post"><IMG SRC="modules/Private_Messages/images/msg_newpost.gif" BORDER=0 ALT="Post Message"></A></TD>';
	echo '<TD ALIGN="RIGHT">&nbsp;</TD>';
	echo '</TR>';
	ecHo '</TABLE>';

	echo '<TABLE  WIDTH=100% CELLPADDING=0 CELLSPACING=1 BGCOLOR="#006699">';
	echo '<TR><TD>';
	echo '<TABLE  WIDTH=100% CELLPADDING=1 CELLSPACING=1 BGCOLOR="#FFFFFF">';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#5B5BFF">';
	echo '<TD WIDTH="2%" ALIGN="CENTER"><FONT  COLOR="#FF9900"></FONT></TD>';
	echo '<TD WIDTH="10" ALIGN="CENTER"><FONT  COLOR="#FF9900"><INPUT TYPE="checkbox" NAME="selectall"  Onclick="javascript:select_switch()"></FONT></TD>';
	echo '<TD ALIGN="CENTER"><FONT  COLOR="#FF9900"><B>'._SUBJECT.'</B></FONT></TD>';
	echo '<TD WIDTH="100" ALIGN="CENTER"><FONT  COLOR="#FF9900"><B>'._FROM.'</B></FONT></TD>';
	echo '<TD WIDTH="100" ALIGN="CENTER"><FONT  COLOR="#FF9900"><B>'._DATE.'</B></FONT></TD>';
	echo '</TR>';
	
	$query = "SELECT $privmsgscolumn[id],
							$privmsgscolumn[type],
							$privmsgscolumn[priority],
							$privmsgscolumn[subject],
							$privmsgscolumn[message],
							$privmsgscolumn[from_uid],
							$privmsgscolumn[to_uid],
							$privmsgscolumn[date],
							$privmsgscolumn[ip],
							$privmsgscolumn[enable] 
							FROM $privmsgstable ";

	if ($op == "sentbox") {
		$query .= " WHERE $privmsgscolumn[from_uid] = '".lnSessionGetVar('uid')."' AND $privmsgscolumn[type]='"._MESSAGESENT."'";
	}
	else if ($op == "outbox") {
		$query .= " WHERE $privmsgscolumn[from_uid] = '".lnSessionGetVar('uid')."' AND ($privmsgscolumn[type]='"._MESSAGESEND."' OR $privmsgscolumn[type]='"._MESSAGEVIEW."')";
	}
	else if ($op == "savebox") {
		$query .= " WHERE $privmsgscolumn[to_uid] = '".lnSessionGetVar('uid')."' AND $privmsgscolumn[type]='"._MESSAGESAVE."'";
	}
	else {
		$query .= " WHERE $privmsgscolumn[to_uid] = '".lnSessionGetVar('uid')."' AND ($privmsgscolumn[type]='"._MESSAGESEND."' OR $privmsgscolumn[type]='"._MESSAGEVIEW."' OR $privmsgscolumn[type]='"._MESSAGEREAD."')";
	}

	$query .= " ORDER BY $privmsgscolumn[date] DESC";
	$priority_image = array('high_priority.gif','blank.gif','low_priority.gif');
	$result1 = $dbconn->Execute($query);

	//count
	$numrows = $result1->PO_RecordCount();

	// how many pages we have when using paging?
	$maxPage = ceil($numrows/$rowsPerPage);

	$query .= " LIMIT $offset, $rowsPerPage";
	$result = $dbconn->Execute($query);

	for($i=0; list($id,$type,$priority,$subject,$message,$from_uid,$to_uid,$date,$ip,$enable) = $result->fields; $i++) {
			$result->MoveNext();
			if ($op != "sentbox" && $op != "outbox" && $op != "savebox") {
				changeStateToView($id);
			}
			echo '<TR VALIGN="MIDDLE" ALIGN="CENTER" BGCOLOR="#FFFFFF">';
			echo '<TD ALIGN="CENTER"><IMG SRC="modules/Private_Messages/images/'.$priority_image[$priority].'"  BORDER=0 ALT=""></TD>';
			echo '<TD ALIGN="CENTER"><INPUT TYPE="checkbox" NAME="sel['.$id.']"></TD>';
			if ($type == _MESSAGEVIEW || $type == _MESSAGESEND) {
				$subject = '<B>'.$subject.'<B>';
			}
			echo '<TD ALIGN="LEFT"><A class=b HREF="index.php?mod=Private_Messages&amp;op=read_message&amp;id='.$id.'">'.$subject.'</A></TD>';
			echo '<TD ALIGN="CENTER">'.lnUserGetVar('uname',$from_uid).'</TD>';
				//editor : orrawin 
				$date = date('d-M-Y H:i', $date);
			echo '<TD ALIGN="CENTER">'.$date.'</TD>';
			echo '</TR>';
			echo '<TR HEIGHT=1 BGCOLOR=#CCE6FF><TD COLSPAN="5"></TD></TR>';
	}
	

	if ($i==0) { 
		echo '<TR BGCOLOR=#FFFFFF HEIGHT=30><TD COLSPAN="5" ALIGN="CENTER">You have no messages in this folder</TD></TR>';
	}
	echo '<TR BGCOLOR=#99CCCC><TD COLSPAN="5" ALIGN="RIGHT"><INPUT TYPE="SUBMIT" NAME="op" VALUE="Save Marked"> <INPUT NAME="op" TYPE="SUBMIT" VALUE="Delete Marked"> </TD></TR>';

	echo '</TABLE>';
	echo '</TD></TR>';
	echo '</TABLE>';

	echo '<TABLE  WIDTH=100% CELLPADDING=2 CELLSPACING=0>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER">';
	echo '<TD ALIGN="LEFT"><IMG SRC="modules/Private_Messages/images/msg_newpost.gif" BORDER=0 ALT="Post Message"></TD>';
	echo '<TD ALIGN="RIGHT">';
	//Paging
	// print the link to access each page
$self = $_SERVER['PHP_SELF'];
$nav = '';
for($page = 1; $page <= $maxPage; $page++)
{
	if ($page == $pageNum)
	{
		$nav .= " [$page] ";   // no need to create a link to current page
	}
	else
	{
		$nav .= " <a href=\"$self?mod=Private_Messages&op=".$op."&page=$page\">$page</a> ";
	}		
}

// creating previous and next link
// plus the link to go straight to
// the first and last page

if ($pageNum > 1)
{
	$page = $pageNum - 1;
	$prev = " <a href=\"$self?mod=Private_Messages&op=".$op."&page=$page\">[Prev]</a> ";
	
	$first = " <a href=\"$self?mod=Private_Messages&op=".$op."&page=1\">[First Page]</a> ";
} 
else
{
	$prev  = '&nbsp;'; // we're on page one, don't print previous link
	$first = '&nbsp;'; // nor the first page link
}

if ($pageNum < $maxPage)
{
	$page = $pageNum + 1;
	$next = " <a href=\"$self?mod=Private_Messages&op=".$op."&page=$page\">[Next]</a> ";
	
	$last = " <a href=\"$self?mod=Private_Messages&op=".$op."&page=$maxPage\">[Last Page]</a> ";
} 
else
{
	$next = '&nbsp;'; // we're on the last page, don't print next link
	$last = '&nbsp;'; // nor the last page link
}

// print the navigation link
echo $first . $prev . "<B>". $nav ."</B>". $next . $last;	
	echo '</TD>';
	echo '</TR>';
	echo '</TABLE>';
	echo '</FORM>';
}



?>