<?php
/**
*  Select Group users
*/

if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Submissions::', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

$thistheme = lnConfigGetVar('Default_Theme'); 
echo '<html><head><title>Select Users</title></head>';
echo "<link rel=\"StyleSheet\" href=\"themes/".$thistheme."/style/style.css\" type=\"text/css\">\n";
echo '<body>';

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();
$groupstable = $lntable['groups'];
$groupscolumn = &$lntable['groups_column'];

$query = "SELECT $groupscolumn[gid],$groupscolumn[name] FROM $groupstable ORDER BY $groupscolumn[name]";

$result = $dbconn->Execute($query);
//echo $query;
?>
<SCRIPT LANGUAGE="JavaScript">
<!--
	function refresh_username() {
		for (i = 0; i < document.ulist.length; i++) {
			if (document.ulist.elements[i]==document.ulist.selectall || document.ulist.elements[i]==document.ulist.submit_button)
				continue;

			if (document.ulist.elements[i].checked == true) {
				for (j = 0; j < opener.document.forms['submission'].list6.length; j++) {
					if (opener.document.forms['submission'].list6.options[j].text == document.ulist.elements[i].value) {
						opener.document.forms['submission'].list6.options[j].selected = true;
					}
				}
			}
		}
		opener.focus();
		window.close();
	}

	function select_switch(){
		for (i = 0; i < document.ulist.length; i++) {
			if (document.ulist.selectall.checked == false) {
					document.ulist.elements[i].checked = true;
			}
			else {
				document.ulist.elements[i].checked = false;
			}
		}

		if (document.ulist.selectall.checked == false) {
			document.ulist.selectall.checked = true;
		}
		else {
			document.ulist.selectall.checked = false;
		}
	}

//-->
</SCRIPT>

<?
if (empty($word)) $word='*';
echo '<FORM NAME="nickname" method="post" action="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Submissions">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="grouplist">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="search">';

echo '<CENTER><TABLE CELLPADDING=3 CELLSPACING=1 BGCOLOR="#0066CC" WIDTH="98%" HEIGHT="50">';
echo '<TR><TD BGCOLOR="#0066CC" ALIGN="CENTER" > <FONT COLOR="#FF9900"><B>Find users</B></FONT></TD></TR>';
echo '<TR HEIGHT=60><TD BGCOLOR="#FFFFFF" ALIGN="CENTER">';
echo '<B>Nickname: <INPUT TYPE="text" NAME="word" VALUE="'.$word.'"></B>';

echo '&nbsp;<B>from</B>&nbsp;';
if (!empty($group)) {
	$select[$group]="selected";
}
echo '<SELECT CLASS="select_gray" NAME="group">';
echo '<OPTION VALUE="0">All</OPTION>';
for (;list($gid, $groupname) = $result->fields;) {
		$result->MoveNext();
		echo '<OPTION VALUE="'.$gid.'" '.$select[$gid].'>'.$groupname.'</OPTION>';
}
echo '</SELECT>';
echo ' <INPUT TYPE="submit" VALUE="Search"><BR>Use * as a wildcard for partial matches</TD></TR>';
echo '</TABLE></FORM></CENTER>';


if ($op == "search") {
		$newword = str_replace("*","%",$word);
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$userstable = $lntable['users'];
		$userscolumn = &$lntable['users_column'];
		$groupstable = $lntable['groups'];
		$groupscolumn = &$lntable['groups_column'];
		$group_membershiptable = $lntable['group_membership'];
		$group_membershipcolumn = &$lntable['group_membership_column'];
		$query = "SELECT $userscolumn[uid],$userscolumn[uname] FROM $userstable,$group_membershiptable,$groupstable"
		." WHERE $userscolumn[uid]=$group_membershipcolumn[uid] AND $group_membershipcolumn[gid]=$groupscolumn[gid] ";
		if ($group != '0') {
			$query .= " AND $groupscolumn[gid]='$group'";
		}
		$query .= " AND $userscolumn[uname] like '$newword'"
		." GROUP BY $userscolumn[uid] ORDER BY $userscolumn[uname]";
		$result = $dbconn->Execute($query);
		if ($result->RecordCount()) {
			echo '<FORM NAME="ulist">';
			for($i=0; list($uid, $uname) = $result->fields; $i++) {
				$result->MoveNext();
				echo '<INPUT TYPE="checkbox" NAME="" checked VALUE="'.$uname.'">'.$uname.'<BR>';
			}
			echo '<HR><INPUT TYPE="checkbox" NAME="selectall"  Onclick="javascript:select_switch()">De-Select/Select All <BR><CENTER><INPUT TYPE="button" NAME="submit_button" VALUE="Select" class="button_org" OnClick="javascript:refresh_username()"></CENTER>';
			echo '</FORM>';
		}
}


?>