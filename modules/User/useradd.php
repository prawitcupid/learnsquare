<?php
/**
* Add user by admin
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Admin::', "$file::", ACCESS_ADMIN)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to read '".$mod." module' </h1></CENTER>";
		return false;
}

/* Add users options */
if ($op=="save") {
	if (!lnSecAuthAction(0, 'User::', "$file::", ACCESS_ADD)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to add '".$mod." module'</h1></CENTER>";
		return false;
	}
	list($op,$newLeft,$uservars) = lnVarCleanFromInput('op','newLeft','uservars');
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$table = $lntable['users'];
	$column = &$lntable['users_column'];
	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];

	$err_msg="";
	$keys=$vals=array();
	foreach ($uservars as $key=>$value) {
			if (empty($value)) {
				$err_msg = "<font color=red><b>"._EPTYVALUE."</b></font><BR>";
				break;
			}
			if ($key != '_PASSWORD') {
				if($return = lnUserCheck('',$value,$value,$value)) {
					$err_msg = "<font color=red><b> '".$value."' ".$return ."</b></font><BR>";
					break;
				}
			}
			
			switch($key) {
				case "_UNO":				$keycolumn=$column[uno];break;
				case '_NICKNAME':		$keycolumn=$column[uname];break;
				case '_PASSWORD':	$keycolumn=$column[pass];  $value=md5($value);break;
				case '_EMAIL':				$keycolumn=$column[email];break;
				case '_NAME':				$keycolumn=$column[name];break;
			}
			$keys[] =  $keycolumn;
			$vals[] = "'".$value."'";		
	}	


	
	// Instert users
	if (empty($err_msg)) {
		// Insert User table
		$regdate = time();
		$nid=getNextUserID();
		$qkey= join(',',$keys);
		$qval= join(',',$vals);
		$query = "INSERT INTO $table ($column[uid],$column[regdate], $qkey) VALUES ('$nid','$regdate', $qval)";
		$result = $dbconn->Execute($query);
		if ($dbconn->ErrorNo() <> 0) {
			echo "<br>". $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg() . "<br>";
			return;
		} 
		else {
			$msg = "<BR>"._SAVEDONE."<BR>";
			lnUpdateUserEvent("Add user $vals");
		}
		
		// Insert Group Membership table
		$groupList = explode(",",$newLeft);
		for ($i=0; $i < count($groupList); $i++) {
			$query = "INSERT INTO $group_membershiptable VALUES ('".lnVarPrepForStore($groupList[$i])."','".lnVarPrepForStore($nid)."')";
			$result = $dbconn->Execute($query);
			if ($dbconn->ErrorNo() <> 0) {
						echo "$query<br>". $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg() . "<br>";
						return;
			} 
		}
	}
}

/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_USERADMIN,_USERADD);
$links=array('index.php?mod=Admin','index.php?mod=User&file=admin','index.php?mod=User&file=useradd');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=User&file=useradd"><B>'._USERADD.'</B></A><BR><BR>'._USERADD_DESC.'<BR>';

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();
$table = $lntable['user_property'];
$column = &$lntable['user_property_column'];

$result = $dbconn->Execute("SELECT $column[prop_id], $column[prop_label],$column[prop_dtype],
						  $column[prop_length], $column[prop_weight], $column[prop_validation]
						  FROM $table WHERE $column[prop_dtype] = "._CORE." ORDER BY $column[prop_weight]");

if ($dbconn->ErrorNo() <> 0) {
	echo $dbconn->ErrorNo() . "List User Properties: " . $dbconn->ErrorMsg() . "<br>";
	error_log ($dbconn->ErrorNo() . "List User Properties: " . $dbconn->ErrorMsg() . "<br>");
	return;
} 

// User add form
// Type 1.

 echo '<P><table width=500 cellpadding=2 cellspacing=0 border=0>'
 .'<FORM METHOD=POST NAME="Register" ACTION="index.php">'
 .'<INPUT TYPE="hidden" NAME="mod" VALUE="User">'
 .'<INPUT TYPE="hidden" NAME="file" VALUE="useradd">'
 .'<INPUT TYPE="hidden" NAME="op" VALUE="save">';
	while (list($prop_id, $prop_label, $prop_dtype, $prop_length, $prop_weight, $prop_validation) = $result->fields) {
			$result->MoveNext();
			 $eval_cmd = "\$prop_label_text=$prop_label;";
			@eval($eval_cmd); 
			echo "<TR><TD width=80 align=left>".$prop_label_text ."</TD><TD>";
			echo '<INPUT CLASS="input" TYPE="text" SIZE="20" NAME="uservars['.$prop_label.']" VALUE="">';
			echo '</TD></TR>';
	 }

// Select Groups
   list($dbconn) = lnDBGetConn();
   $lntable = lnDBGetTables();

   $groupstable = $lntable['groups'];
   $groupscolumn = &$lntable['groups_column'];

   $default_group = lnConfigGetVar('default_group');
   $default_group_name = findGroupName($default_group);

	echo "<TR VALIGN=TOP>";
	echo '<TD ALIGN="left">'._SELECTGROUP.'</TD><TD>';
	echo '
	<INPUT TYPE="hidden" NAME="newLeft">
	<TABLE BORDER=0>
	<TR ALIGN="TOP">
		<TD>
		<SELECT CLASS="select" NAME="list1" MULTIPLE SIZE=9 onDblClick="opt.transferRight()" style="width:150px">
		<OPTION VALUE="'.$default_group.'">'.$default_group_name.'</OPTION>
	    </SELECT>
	</TD>
	<TD VALIGN=MIDDLE ALIGN=CENTER>
		<INPUT CLASS="button_white" TYPE="button" NAME="right" VALUE="&gt;&gt;" ONCLICK="opt.transferRight()"><BR><BR>
		<INPUT CLASS="button_white"  TYPE="button" NAME="right" VALUE="All &gt;&gt;" ONCLICK="opt.transferAllRight()"><BR><BR>
		<INPUT CLASS="button_white"  TYPE="button" NAME="left" VALUE="&lt;&lt;" ONCLICK="opt.transferLeft()"><BR><BR>
		<INPUT CLASS="button_white"  TYPE="button" NAME="left" VALUE="All &lt;&lt;" ONCLICK="opt.transferAllLeft()">
	</TD>
	<TD>
	<SELECT CLASS="select_gray" NAME="list2" MULTIPLE SIZE=9 onDblClick="opt.transferLeft()" style="width:150px">';
	  $result = $dbconn->Execute("SELECT  $groupscolumn[gid], $groupscolumn[name], $groupscolumn[description]  FROM $groupstable");
	   for ($i=1;list($gid, $name,$description) = $result->fields; $i++) {
			$result->MoveNext();
			if ($gid != $default_group) { 
				echo '<OPTION VALUE="'.$gid.'">'.$name.'</OPTION>';
			}
		}

	echo '</SELECT>
		</TD>
	</TR>
	</TABLE>
	';

	echo "</TD></TR>";

echo ' <TR><TD>&nbsp;</TD><TD>&nbsp;<INPUT CLASS="button_org" TYPE="submit" VALUE="'._SUBMITADD.'"><BR><B>'.$err_msg.'&nbsp;'.$msg.'</B></TD></TR>';
echo '</FORM></table>';

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */
?>