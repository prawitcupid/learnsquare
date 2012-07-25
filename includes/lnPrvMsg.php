<?php
/**
 * Internal Message
*/

function messageHead($vars) {
	// Get arguments from argument array
    extract($vars);

	$inbox = ($op == 'inbox') ? '<B>'._INBOX.'</B>' : '<U>'._INBOX.'</U>';
	$sentbox = ($op == 'sentbox') ? '<B>'._SENTBOX.'</B>' : '<U>'._SENTBOX.'</U>';
	$outbox = ($op == 'outbox') ? '<B>'._OUTBOX.'</B>' : '<U>'._OUTBOX.'</U>';
	$savebox = ($op == 'savebox') ? '<B>'._SAVEBOX.'</B>' : '<U>'._SAVEBOX.'</U>';
		
	echo '<TABLE  WIDTH=100% CELLPADDING=2 CELLSPACING=0>';
	echo '<TR VALIGN="MIDDLE" ALIGN="CENTER">';
	echo '<TD><A class=b  HREF="index.php?mod=Private_Messages&amp;op=inbox"><IMG SRC="modules/Private_Messages/images/msg_inbox.gif" WIDTH="28" HEIGHT="25" BORDER=0 ALT="">'.$inbox.'</A></TD>';
	echo '<TD><A class=b  HREF="index.php?mod=Private_Messages&amp;op=sentbox"><IMG SRC="modules/Private_Messages/images/msg_sentbox.gif" WIDTH="28" HEIGHT="25" BORDER=0 ALT="">'.$sentbox.'</A></TD>';
	echo '<TD><A class=b HREF="index.php?mod=Private_Messages&amp;op=outbox"><IMG SRC="modules/Private_Messages/images/msg_outbox.gif" WIDTH="28" HEIGHT="25" BORDER=0 ALT="">'.$outbox.'</A></TD>';
	echo '<TD><IMG SRC="modules/Private_Messages/images/msg_savebox.gif" WIDTH="28" HEIGHT="25" BORDER=0 ALT=""><A class=b HREF="index.php?mod=Private_Messages&amp;op=savebox">'.$savebox.'</A></TD>';
	echo '<TD ALIGN="RIGHT" bgcolor="#FFFFFF">';
	showQuota();
	echo '</TD>';
	echo '</TR>';
	ecHo '</TABLE>';
}


/**
*
*/
function getInboxSize() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$privmsgstable = $lntable['privmsgs'];
	$privmsgscolumn = &$lntable['privmsgs_column'];

	$query = "SELECT $privmsgscolumn[message] FROM $privmsgstable WHERE $privmsgscolumn[to_uid] = '".lnSessionGetVar('uid')."'";	$result = 

	$dbconn->Execute($query);
	$sum=0;
	while(list($message) = $result->fields) {
		$result->MoveNext();
		$sum += strlen($message);
	}
	
	return $sum;
}

function showQuota() {

$inboxsize = lnConfigGetVar('inboxsize');
$quota =  $inboxsize * 1024 * 1024; //1 MBytes
$full = 175; // width pixel

$total = getInboxSize();
$percent = sprintf("%3.2f",$total/$quota*100); 
$percents = round($total/$quota*100); 
//$percent=50;

$width = ($full*$percent)/100;

echo '
<TABLE CELLPADING=0 CELLSPACING=1 BGCOLOR="#669900">
<TR>
	<TD>
			 <table width="175" cellspacing="1" cellpadding="1" border="0"  bgcolor="#669900">
					<tr>
					  <td colspan="3" width="100%"  bgcolor="#669900" class=head>Your Inbox is '.$percent.'% full</td>
					</tr>
					<tr>
					  <td colspan="3" width="100%" bgcolor="#669966">
						<table cellspacing="0" cellpadding="1" border="0">
						  <tr>
							<td bgcolor="#669900" height="10">';
	if ($width) {
		echo '<img src="modules/Private_Messages/images/gray.gif" width="'.$width.'" height="10" alt="'.$percent.'%">';
	}
echo'					</td>
						 </tr>
						</table>
					  </td>
					</tr>
					<tr>
					  <td  bgcolor="#CFED8A" width="33%" >0%</td>
					  <td  bgcolor="#CFEF21" width="34%" align="center">50%</td>
					  <td  bgcolor="#94DC16" width="33%" align="right">100%</td>
					</tr>
				  </table>
		</TD>
</TR>
</TABLE>
';
}

function sendMessage($vars) {
	// Get arguments from argument array
    extract($vars);
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$privmsgstable = $lntable['privmsgs'];
	$privmsgscolumn = &$lntable['privmsgs_column'];

	$id = getMaxPrivMsgID();
	$type = _MESSAGESEND; 
	$send_time = time();
	$ip = getenv("REMOTE_ADDR");
	if (empty($from_uid)) {
		$from_uid = lnSessionGetVar('uid');
	}
	if (empty($to_uid)) {
		$to_uid = lnUserGetUid($nickname);
		messageHead($vars);
	}
	
	if (empty($to_uid)) {
		echo '<P><TABLE CELLPADDING=10 CELLSPACING=1 BGCOLOR="#FF0000" HEIGHT="50"><TR><TD BGCOLOR="#FFFFFF" ALIGN="CENTER">';
		echo '<B>Sorry but no such user exists</B>';
		echo '<P><< <A HREF="javascript:history.go(-1)">Back</A>';
		echo '</TD></TR></TABLE>';
	}
	else {
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
						'". lnVarPrepForStore($send_time) ."',
						'". lnVarPrepForStore($ip) ."',
						'1'
						) ";
			$result = $dbconn->Execute($query);

			/*
			echo '<P><TABLE CELLPADDING=10 CELLSPACING=1 BGCOLOR="#0066CC" HEIGHT="50"><TR><TD BGCOLOR="#FFFFFF" ALIGN="CENTER">';
			echo '<B>Your message has been sent</B>';
			echo '<P>Click <A HREF="index.php?mod=Private_Messages&amp;op=inbox">Here</A> to return to your Inbox';
			echo '</TD></TR></TABLE>';
			*/


	}
}

function getMaxPrivMsgID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$privmsgstable = $lntable['privmsgs'];
	$privmsgscolumn = &$lntable['privmsgs_column'];

	$query = "SELECT MAX($privmsgscolumn[id]) FROM $privmsgstable";
	$result = $dbconn->Execute($query);
	list ($maxid) = $result->fields;
	
	return $maxid + 1;
}

?>