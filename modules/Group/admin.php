<?php
/**
*	define user group module
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "$name::$gid", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */

include 'header.php';

switch($op) {
	case "addgroup": 
		addGroup($groupname,$groupdesc,$grouptype);
		break;
	case "deletegroup": 
		deleteGroup($gid);
		break;
	case "editgroup": 
		editGroup($gid);
		return;
	case "updategroup": 
		updateGroup($gid,$groupname,$groupdesc,$grouptype);
		break;
	case "listuser": 
		listUser($gid);
		return;
}

/** Navigator **/
$menus= array(_ADMINMENU,_GROUPADMIN);
$links=array('index.php?mod=Admin','index.php?mod=Group&file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

	OpenTable();

   echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Group&file=admin"><B>'._GROUPADMIN.'</B></A><BR>';

   echo '<CENTER>';

   // Add group form
	if (lnSecAuthAction(0, 'Group::', "$name::$gid", ACCESS_ADD)) {
		echo '<FORM METHOD=POST ACTION="index.php">'
		.'<INPUT TYPE="hidden" NAME="mod" VALUE="Group">'
		.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
		.'<INPUT TYPE="hidden" NAME="op" VALUE="addgroup">';

		echo '<table cellpadding=4 cellspacing=0  width=450 border=0>'
		
		.'<tr><td align=right>'._GROUPNAME.' : </td><td><input class="input" type=text name="groupname" value="" SIZE="45"><td></tr>';

		echo '<tr><td align=right>'._GROUPTYPE.' : </td><td>';
		echo '<SELECT NAME="grouptype">';
		echo '<OPTION VALUE="'._LNGROUP_STUDENT.'">'._GROUP_STUDENT.'</OPTION>';
		echo '<OPTION VALUE="'._LNGROUP_INSTRUCTOR.'">'._GROUP_INSTRUCTOR.'</OPTION>';
		echo '<OPTION VALUE="'._LNGROUP_TA.'">'._GROUP_TA.'</OPTION>';
		echo '<OPTION VALUE="'._LNGROUP_ADMIN.'">'._GROUP_ADMIN.'</OPTION>';
		echo '</SELECT>';

		echo '<tr><td align=right valign="top">'._GROUPDESC.' : </td><td><TEXTAREA class="input" name="groupdesc" NAME="" ROWS="3" COLS="45"></TEXTAREA><td></tr>'
		.'<td></tr>'
		.'<tr><td align=right>&nbsp;</td><td><input class="button_org" type=submit value="'._ADDGROUP.'"><td></tr>'
		.'</TABLE>'
		.'</FORM>';
	}


	// Group List

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$groupstable = $lntable['groups'];
	$groupscolumn = &$lntable['groups_column'];
	$query ="SELECT  $groupscolumn[gid], $groupscolumn[name], $groupscolumn[description]  FROM $groupstable ORDER BY $groupscolumn[gid]";
	$result = $dbconn->Execute($query);
	
	echo '<table class="list" cellpadding="2" cellspacing="1" width=450>';
	echo '<tr><td colspan="3" height=20 class="head">&nbsp;<B>'._GROUPLIST.'</B></td></tr>';
	for ($i=1;list($gid, $name,$description) = $result->fields; $i++) {
        $result->MoveNext();
		$name=stripslashes($name);
		$description=nl2br(stripslashes($description));
		if (lnSecAuthAction(0, 'Group::', "$name::$gid", ACCESS_READ)) {
			echo '<tr valign=top bgcolor=#FFFFFF height=30>'
			.'<td width=20 align=center>'.$i.'</td><td><A HREF="index.php?mod=Group&amp;file=admin&amp;op=listuser&amp;gid='.$gid.'"><B>'.$name.'</B></A><BR>'.$description.'</td>'
			.'<td width=40 align=center> ';

			if (lnSecAuthAction(0, 'Group::', "$name::$gid", ACCESS_EDIT)) {
				echo '<A class=menu HREF=index.php?mod=Group&amp;file=admin&amp;op=editgroup&amp;gid='.$gid.'><IMG SRC="images/global/edit.gif" WIDTH="14" HEIGHT="16" BORDER=0 ALT="Edit"></A>';
			}
		   echo  '&nbsp;';
			if (lnSecAuthAction(0, 'Group::', "$name::$gid", ACCESS_DELETE)) {
				echo "<A class=menu HREF=\"javascript:if(confirm('Delete?')) window.open('index.php?mod=Group&amp;file=admin&amp;op=deletegroup&amp;gid=".$gid."','_self')\"><IMG SRC=\"images/global/delete.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=0 ALT=\"Delete\"></A>";
			}
  		   
		   echo '</td></tr>';
		}
  }

echo  '</table><BR><BR>';

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */

/**
* add group function
*/
function addGroup($name,$description,$type) {
	if (!lnSecAuthAction(0, 'Group::', "$name::$gid", ACCESS_ADD)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to add ".$mod." module!</h1></CENTER>";
		return false;
	}
	if (!empty($name)){
		  list($dbconn) = lnDBGetConn();
		  $lntable = lnDBGetTables();

		  $groupstable = $lntable['groups'];
		  $groupscolumn = &$lntable['groups_column'];
		  $groupid = getNextGroupID();
		  $query ="INSERT INTO  $groupstable ($groupscolumn[gid], $groupscolumn[name], $groupscolumn[description], $groupscolumn[type]) VALUES ('$groupid',
		  '".lnVarPrepForStore($name)."', '".lnVarPrepForStore($description)."', '".lnVarPrepForStore($type)."')";
		  $result = $dbconn->Execute($query);

		    if ($dbconn->ErrorNo() <> 0) {
				  echo $dbconn->ErrorNo() . "Add Group" . $dbconn->ErrorMsg() . "<br>";
			}

			return true;
	 }

	 return false;
}

function getNextGroupID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$groupstable = $lntable['groups'];
	$groupscolumn = &$lntable['groups_column'];

	 $result = $dbconn->Execute("SELECT  Max($groupscolumn[gid])  FROM $groupstable");
	 list($maxid) = $result->fields;
	  
	 return $maxid+1;
}


/**
* delete group function 
*/
function deleteGroup($gid) {
	if (!lnSecAuthAction(0, 'Group::', "::$gid", ACCESS_DELETE)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to delete ".$mod." module!</h1></CENTER>";
		return false;
	}
	if (!empty($gid)){
		  list($dbconn) = lnDBGetConn();
		  $lntable = lnDBGetTables();
		  $groupstable = $lntable['groups'];
		  $groupscolumn = &$lntable['groups_column'];
		  $group_membershiptable = $lntable['group_membership'];
		  $group_membershipcolumn = &$lntable['group_membership_column'];
	
		// delete from groups table
		  $query ="DELETE FROM $groupstable WHERE $groupscolumn[gid] ='".lnVarPrepForStore($gid)."'";

		  $result = $dbconn->Execute($query);

		    if ($dbconn->ErrorNo() <> 0) {
				  echo $dbconn->ErrorNo() . "Delete Group" . $dbconn->ErrorMsg() . "<br>";
				   return false;
			}

		 // delete from group membership table
		  $query ="DELETE FROM $group_membershiptable WHERE $group_membershipcolumn[gid] ='".lnVarPrepForStore($gid)."'";
		  $result = $dbconn->Execute($query);

		    if ($dbconn->ErrorNo() <> 0) {
				  echo $dbconn->ErrorNo() . "Delete Group" . $dbconn->ErrorMsg() . "<br>";
				   return false;
			}

			return true;
	 }

	 return false;
}


/**
* edit group fuction 
*/
function editGroup($gid) {
	if (!lnSecAuthAction(0, 'Group::', "::$gid", ACCESS_EDIT)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
		return false;
	}
	if (!empty($gid)){
		  list($dbconn) = lnDBGetConn();
		  $lntable = lnDBGetTables();
	
		  $groupstable = $lntable['groups'];
		  $groupscolumn = &$lntable['groups_column'];
		  $query ="SELECT $groupscolumn[gid], $groupscolumn[name],$groupscolumn[description],$groupscolumn[type] FROM $groupstable WHERE $groupscolumn[gid] ='".lnVarPrepForStore($gid)."'";

		  $result = $dbconn->Execute($query);

		    if ($dbconn->ErrorNo() <> 0) {
				  echo $dbconn->ErrorNo() . "Delete Group" . $dbconn->ErrorMsg() . "<br>";
			}

			list($gid, $name, $description,$type) = $result->fields;
			$name=stripslashes($name);
			$description=stripslashes($description);
			
			/** Navigator **/
			echo '<IMG SRC="images/global/bul.jpg" WIDTH="8" HEIGHT="8" BORDER="0" ALT=""> <A HREF="index.php?mod=Admin">'._ADMINMENU.'</A>&nbsp;&gt;&nbsp;'
				.'<A HREF="index.php?mod=Group&amp;file=admin">'._GROUPADMIN.'</A>&nbsp;&gt;&nbsp;'
				.'<B>'._GROUPEDIT.'</B><BR>&nbsp;';
			/** Navigator **/

			OpenTable();

			echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Group&amp;file=admin&amp;op=editgroup&amp;gid='.$gid.'"><B>'._GROUPEDIT.'</B></A><BR>&nbsp;';

			// show edti form
			echo '<CENTER><BR>'
			.'<TABLE cellpadding=0 cellspacing=0 width=350><TR valign=middle>'
			.'<FORM METHOD=POST ACTION="index.php">'
			.'<INPUT TYPE="hidden" NAME="mod" VALUE="Group">'
			.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
			.'<INPUT TYPE="hidden" NAME="op" VALUE="updategroup">'
			.'<INPUT TYPE="hidden" NAME="gid" VALUE="'.$gid.'">'
			.'<TD>'
			.'<table cellpadding=3 cellspacing=0 width=450 bgcolor=#FFFFFF border=0>'
			.'<tr bgcolor=#FFFFFF><td align=right><BR>'._GROUPNAME.': </td><td><BR><INPUT CLASS="input" TYPE="text" NAME="groupname" VALUE="'.$name.'"></tr>';

			$select[$type]="selected";
			echo '<tr><td align=right><B>'._GROUPTYPE.':</B></td><td>';
			echo '<SELECT NAME="grouptype">';
			echo '<OPTION VALUE="'._LNGROUP_STUDENT.'" '.$select[_LNGROUP_STUDENT].'>'._GROUP_STUDENT.'</OPTION>';
			echo '<OPTION VALUE="'._LNGROUP_INSTRUCTOR.'" '.$select[_LNGROUP_INSTRUCTOR].'>'._GROUP_INSTRUCTOR.'</OPTION>';
			echo '<OPTION VALUE="'._LNGROUP_TA.'" '.$select[_LNGROUP_TA].'>'._GROUP_TA.'</OPTION>';
			echo '<OPTION VALUE="'._LNGROUP_ADMIN.'" '.$select[_LNGROUP_ADMIN].'>'._GROUP_ADMIN.'</OPTION>';
			echo '</SELECT>';

			echo '<tr bgcolor=#FFFFFF><td align=right>'._GROUPDESC.': </td><td><TEXTAREA class="input" name="groupdesc" NAME="" ROWS="3" COLS="45">'.$description.'</TEXTAREA><td></tr>'
			.'<tr bgcolor=#FFFFFF><td>&nbsp;</td><td><INPUT class="button_org" TYPE="submit" VALUE="'._SAVECHANGES.'">'
			.'</tr></tr></table>'
			.'</TD></TR></FORM></TABLE></CENTER>';
			
			CloseTable();
			include 'footer.php';
	}
}


/**
* update group after submit from edit form
*/
function updateGroup($gid, $name, $description,$type) {
	if (!lnSecAuthAction(0, 'Group::', "::$gid", ACCESS_EDIT)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
		return false;
	}
	if (!empty($gid) && !empty($name)) {
		  list($dbconn) = lnDBGetConn();
		  $lntable = lnDBGetTables();

		  $groupstable = $lntable['groups'];
		  $groupscolumn = &$lntable['groups_column'];
		  $query ="UPDATE $groupstable SET $groupscolumn[name]='".lnVarPrepForStore($name)."', $groupscolumn[description]='".lnVarPrepForStore($description)."', $groupscolumn[type]='".lnVarPrepForStore($type)."'  WHERE $groupscolumn[gid] ='".lnVarPrepForStore($gid)."'";
		  $result = $dbconn->Execute($query);

		    if ($dbconn->ErrorNo() <> 0) {
				  echo $dbconn->ErrorNo() . "Add Group" . $dbconn->ErrorMsg() . "<br>";
			}

			return true;
	 }

	 return false;
}

/**
* list all user in selected group
*/
function listUser($gid) {
	if (!lnSecAuthAction(0, 'Group::', "::$gid", ACCESS_READ)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to delete ".$mod." module!</h1></CENTER>";
		return false;
	}

	if (!empty($gid)){
		  list($dbconn) = lnDBGetConn();
		  $lntable = lnDBGetTables();
		  $userstable = $lntable['users'];
		  $userscolumn = &$lntable['users_column'];
		  $group_membershipstable = $lntable['group_membership'];
		  $group_membershipscolumn = &$lntable['group_membership_column'];
	
		  $query ="SELECT $userscolumn[uid],$userscolumn[uname],$userscolumn[name],$userscolumn[email],$userscolumn[phone]  FROM $userstable , $group_membershipstable WHERE  $group_membershipscolumn[uid]=$userscolumn[uid] AND $group_membershipscolumn[gid] = '".lnVarPrepForStore($gid)."'";
		  $result = $dbconn->Execute($query);

			// Navigator
			echo '<IMG SRC="images/global/bul.jpg" WIDTH="8" HEIGHT="8" BORDER="0" ALT=""> <A HREF="index.php?mod=Admin">'._ADMINMENU.'</A>&nbsp;&gt;&nbsp;'
				.'<A HREF="index.php?mod=Group&amp;file=admin">'._GROUPADMIN.'</A>&nbsp;&gt;&nbsp;'
				.'<B>'._LISTUSER.'</B><BR>&nbsp;';


			OpenTable();
			echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Group&amp;file=admin&amp;op=listuser&amp;gid='.$gid.'"><B>'._LISTUSER.'</B></A><BR>&nbsp;';

			echo '<center>';
			echo '<table cellpadding=2 cellspacing=0 bgcolor="#808080" width=500>';
			echo '<tr height=20><td class=head>&nbsp;</td><td class=head>'._NICKNAME.'</td><td class=head>'._NAME.'</td><td class=head>'._EMAIL.'</td><td class=head>'._PHONE.'</td><td>&nbsp;</td></tr>';

			for ($i=1;list($uid, $uname,$name, $email, $phone) = $result->fields; $i++) {
				$result->MoveNext();
				echo '<tr bgcolor=#FFFFFF><td>'.$i.'</td><td>'.$uname.'</td><td>'.$name.'</td><td>'.$email.'</td><td>'.$phone.'</td>';
				
				echo '<td width=40 align=center> ';

				if (lnSecAuthAction(0, 'User::', "::", ACCESS_EDIT)) {
					echo '<A class=menu HREF="index.php?mod=User&file=useredit&op=edituser&uid='.$uid.'"><IMG SRC="images/global/edit.gif" WIDTH="14" HEIGHT="16" BORDER=0 ALT="Edit"></A>';
				}
			   echo  '&nbsp;';
				if (lnSecAuthAction(0, 'User::', "::", ACCESS_DELETE)) {
					echo "<A class=menu HREF=\"javascript:if(confirm('Delete $name?')) window.open('index.php?mod=User&file=useredit&op=deleteuser&uid=$uid','_self')\"><IMG SRC=\"images/global/delete.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=0 ALT=\"Delete\"></A>";
				}

				echo '</td>';
				echo '</tr>';
				echo '<tr bgcolor=#CCCCCC height=1><td colspan=6></td></tr>';
			}
			echo '</table></center>';

			CloseTable();
			include 'footer.php';
	}

}

?>