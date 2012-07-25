<?php
/**
*  User Permission
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* options */
if ($op) {
	$vars= array_merge($_GET,$_POST);
	switch($op) {
		case "increase_order": 
			 if (lnSecAuthAction(0, 'Permissions::', "User::$uid", ACCESS_EDIT)) {
				increase_order($vars); 
			}
			break;
		case "decrease_order":
			 if (lnSecAuthAction(0, 'Permissions::', "User::$uid", ACCESS_EDIT)) {
				decrease_order($vars);
			}
			break;
		case "delete_permission":
			 if (lnSecAuthAction(0, 'Permissions::', "User::$uid", ACCESS_DELETE)) {
				delete_permission($vars);
			}
			break;
		case "add_permission":
			 if (lnSecAuthAction(0, 'Permissions::', "User::$uid", ACCESS_ADMIN)) {
				add_permission($vars);
			}
			break;
	}
}

/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_PERMISSIONADMIN,_USERPERMISSION);
$links=array('index.php?mod=Admin','index.php?mod=Permissions&file=admin','index.php?mod=Permissions&file=userpermission');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Permissions&amp;file=grouppermission"><B>'._USERPERMISSION.'</B></A><BR>&nbsp;';

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$userpermstable = $lntable['user_perms'];
$userpermscolumn = &$lntable['user_perms_column'];

$alevelname = accesslevelnames();


$query ="SELECT * FROM $userpermstable ORDER BY $userpermscolumn[sequence]";
$result = $dbconn->Execute($query);
 
if ($dbconn->ErrorNo() <> 0) {
	echo $dbconn->ErrorNo() . "List User Permission " . $dbconn->ErrorMsg() . "<br>";
	error_log ($dbconn->ErrorNo() . "List User Permission " . $dbconn->ErrorMsg() . "<br>");
	return;
} 

$active_count = 0;
$total_count = $result->PO_RecordCount();

// add first
if ($total_count == 0 && $op !="add_permission_form") {
		echo '<CENTER>* * * '._EMPTYPERMISSION.' * * *</CENTER>';
		echo '<FORM METHOD=POST ACTION="index.php">'
		.'<INPUT TYPE="hidden" NAME="mod" VALUE="Permissions">'
		.'<INPUT TYPE="hidden" NAME="file" VALUE="userpermission">'
		.'<INPUT TYPE="hidden" NAME="op" VALUE="add_permission_form">'
		.'<INPUT TYPE="hidden" NAME="sequence" VALUE="0">'
		.'<CENTER><INPUT class="button_org" TYPE="submit" VALUE="'._ADDPERMISSION.'"></CENTER>';
}
else {
	echo '<table class="list" width="100%" cellpadding="3" cellspacing="1">'
	.'<tr align=center>'
	.'<td class="head">Order</td><td class="head">User</td><td class="head">Component</td><td class="head">Instance</td><td class="head">Permission</td><td class="head">&nbsp;</td>'
	.'</tr>';

	if ($sequence == "0" && $op=="add_permission_form") {
		echo '<FORM METHOD=POST ACTION="index.php">'
		.'<INPUT TYPE="hidden" NAME="mod" VALUE="Permissions">'
		.'<INPUT TYPE="hidden" NAME="file" VALUE="userpermission">'
		.'<INPUT TYPE="hidden" NAME="op" VALUE="add_permission">'
		.'<INPUT TYPE="hidden" NAME="action" VALUE="insert">'
		.'<INPUT TYPE="hidden" NAME="sequence" VALUE="1">';				
		echo '<tr bgcolor=#FFFFFF align=center>'
		.'<td>&nbsp;</td>'
		.'<td><INPUT  CLASS="input" TYPE="text" NAME="uname" size=10></td>'
		.'<td><TEXTAREA CLASS="input" NAME="component" ROWS="2" COLS="20"></TEXTAREA></td>'
		.'<td><TEXTAREA CLASS="input" NAME="instance" ROWS="2" COLS="20"></TEXTAREA></td>'
		.'<td>'
		.'<SELECT NAME="level">';
		  foreach ($alevelname as $nlevel=> $name) {
			 if ($nlevel == $level) {
				echo  "<OPTION VALUE=$nlevel selected>$name</OPTION>";	
			 }
			 else {
				echo  "<OPTION VALUE=$nlevel>$name</OPTION>";	
			 }
		  }
		echo '</SELECT></td>'
		.'<td>'
		.'<INPUT CLASS="button_gray" TYPE="submit" VALUE="'._SUBMIT.'">'
		.' <INPUT CLASS="button_gray" TYPE="button" VALUE="'._CANCEL.'" onclick="javascript:window.open(\'index.php?mod=Permissions&amp;file=userpermission\',\'_self\')"></td>'
		.'</tr>';
		echo '</FORM>';
	}

	
	while (list($pid, $uid, $sequence, $realm, $component, $instance,$level,$bond) = $result->fields) {
		$result->MoveNext();

		if (lnSecAuthAction(0, 'Permissions::', "User::$uid", ACCESS_READ)) {
			 $prev_active_count = $active_count;
			 $active_count++;
			 $uname = findUserName($uid);
			 $levelname = accesslevelname($level);
			
			if (!($op == "edit_permission_form" && $active_count == $item)) {
				echo '<tr bgcolor=#FFFFFF>';
				switch (true) {
					case ($active_count == 0):
						$arrows = "&nbsp";
						break;
					case ($active_count == 1):
						$arrows = "<a href=index.php?mod=Permissions&amp;file=userpermission&amp;op=increase_order&amp;pid=$pid&amp;sequence=$sequence><img src=images/global/down.gif border=0></a>";
						break;
					case ($active_count== $total_count):
						$arrows = "<a href=index.php?mod=Permissions&amp;file=userpermission&amp;op=decrease_order&amp;pid=$pid&amp;sequence=$sequence><img src=images/global/up.gif border=0></a>";
						break;
					default:
						$arrows = "<a href=index.php?mod=Permissions&amp;file=userpermission&amp;op=decrease_order&amp;pid=$pid&amp;sequence=$sequence><img src=images/global/up.gif border=0></a>&nbsp;<a href=index.php?mod=Permissions&amp;file=userpermission&amp;op=increase_order&amp;pid=$pid&amp;sequence=$sequence><img src=images/global/down.gif border=0></a>";
				} 
				 echo '<td align=center>'.$arrows 
				.'</td><td align=center>'.$uname
				.'</td><td>'.$component
				.'</td><td>'.$instance
				.'</td><td align=center>'. $levelname
				.'</td><td align=center>';
				 if (lnSecAuthAction(0, 'Permissions::', "User::$uid", ACCESS_EDIT)) {
					echo '<A HREF="index.php?mod=Permissions&amp;file=userpermission&amp;op=edit_permission_form&amp;item='.$active_count.'"><IMG SRC="images/global/edit.gif" WIDTH="14" HEIGHT="16" BORDER="0" ALT="'._EDIT.'"></A>  ';
				 }
				 if (lnSecAuthAction(0, 'Permissions::', "User::$uid", ACCESS_ADD)) {
					echo '<A HREF="index.php?mod=Permissions&amp;file=userpermission&amp;op=add_permission_form&amp;item='.$active_count.'"><IMG SRC="images/global/insert.gif" WIDTH="14" HEIGHT="16" BORDER="0" ALT="'._INSERT.'"></A>  ';
				 }
				 if (lnSecAuthAction(0, 'Permissions::', "User::$uid", ACCESS_DELETE)) {
					echo "<a href=\"javascript:if(confirm('Delete permission ?')) window.open('index.php?mod=Permissions&amp;file=userpermission&amp;op=delete_permission&amp;pid=$pid','_self')\"><IMG SRC=\"images/global/delete.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=\"0\" ALT=\""._DELETE."\"></a>";
				 }
				echo '</td></tr>';
			}

			//show add permission form
			if (($op == "edit_permission_form" || $op == "add_permission_form") && $active_count == $item) {
					echo '<FORM METHOD=POST ACTION="index.php">'
					.'<INPUT TYPE="hidden" NAME="mod" VALUE="Permissions">'
					.'<INPUT TYPE="hidden" NAME="file" VALUE="userpermission">'
					.'<INPUT TYPE="hidden" NAME="op" VALUE="add_permission">'
					.'<INPUT TYPE="hidden" NAME="sequence" VALUE="'.$item.'">';
					
					if ($op == "edit_permission_form") {
						echo '<INPUT TYPE="hidden" NAME="action" VALUE="update">';
						echo '<INPUT TYPE="hidden" NAME="pid" VALUE="'.$pid.'">';
					}
					else {
						$uid = -1;
						$uname = "";
						$sequence=0;
						$component='';
						$instance='';
						$level=-1;
						echo '<INPUT TYPE="hidden" NAME="action" VALUE="insert">';			
					}

					echo '<tr bgcolor=#CCCCCC align=center>'
					.'<td>&nbsp;</td>'
					.'<td><INPUT CLASS="select" TYPE="text" NAME="uname" VALUE="'.$uname.'" size=10></td>'
					.'<td>'
					.'<TEXTAREA CLASS="select"NAME="component" ROWS="2" COLS="15">'.$component.'</TEXTAREA></td>'
					.'<td>'
					.'<TEXTAREA CLASS="select" NAME="instance" ROWS="2" COLS="15">'.$instance.'</TEXTAREA></td>'
					.'<td>'
					.'<SELECT CLASS="select"  NAME="level">';

					  foreach ($alevelname as $nlevel=> $name) {
						 if ($nlevel == $level) {
							echo  "<OPTION VALUE=$nlevel selected>$name</OPTION>";	
						 }
						 else {
							echo  "<OPTION VALUE=$nlevel>$name</OPTION>";	
						 }
					  }
					echo '</SELECT></td>'
					.'<td>'
					.'<INPUT CLASS="button_gray" TYPE="submit" VALUE="'._SUBMIT.'">'
					.' <INPUT CLASS="button_gray" TYPE="button" VALUE="'._CANCEL.'" onclick="javascript:window.open(\'index.php?mod=Permissions&amp;file=userpermission\',\'_self\')"></td>'
					.'</tr>';
					echo '</FORM>';
			}
		}
	}


	echo '</table>';
}


echo "<DIV ALIGN=RIGHT><BR><A HREF=\"javascript:popup('index.php?mod=Permissions&amp;file=showpermission','_blank',550,550)\"><B>"._PERMISSIONINFO."</B></A>&nbsp;</DIV>";

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */


//Funtions//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function increase_order($var)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    if (!empty($var['pid']) && !empty($var['sequence'])) {
        $new_sequence = $var['sequence'] + 1;
        $column = &$lntable['user_perms_column'];
        $result = $dbconn->Execute("UPDATE $lntable[user_perms] SET $column[sequence]=" . lnVarPrepForStore($new_sequence) . "
                                    WHERE $column[pid]='" . $var['pid'] . "' AND $column[sequence]='" . $var['sequence'] . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Increase order 1" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Increase order 1: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 

        $result = $dbconn->Execute("UPDATE $lntable[user_perms] SET $column[sequence]=" . $var['sequence'] . "
                                    WHERE $column[pid]<>'" . $var['pid'] . "' AND $column[sequence]='" . lnVarPrepForStore($new_sequence) . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Increase order 2" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Increase order 2: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 
    } 

     resequencePermission('user');
	
	 lnRedirect("index.php?mod=Permissions&file=userpermission");
} 

function decrease_order($var)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    if (!empty($var['pid']) && !empty($var['sequence'])) {
        $new_sequence = $var['sequence'] - 1;
        $column = &$lntable['user_perms_column'];
        $result = $dbconn->Execute("UPDATE $lntable[user_perms] SET $column[sequence]=" . lnVarPrepForStore($new_sequence) . "
                                    WHERE $column[pid]='" . $var['pid'] . "' AND $column[sequence]='" . $var['sequence'] . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Decrease order 1" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Decrease order 1: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 

        $result = $dbconn->Execute("UPDATE $lntable[user_perms] SET $column[sequence]=" . $var['sequence'] . "
                                    WHERE $column[pid]<>'" . $var['pid'] . "' AND $column[sequence]='" . lnVarPrepForStore($new_sequence) . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Decrease order 2" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Decrease order 2: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 
    } 
	 resequencePermission('user');

     lnRedirect("index.php?mod=Permissions&file=userpermission");
} 


function delete_permission($var)
{
	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $column = &$lntable['user_perms_column'];
	if (!empty($var['pid'])) {
		$query = "DELETE FROM $lntable[user_perms]
				  WHERE $column[pid] = '" . lnVarPrepForStore($var['pid']) . "'";
		$dbconn->Execute($query);

		if ($dbconn->ErrorNo() != 0) {
			return false;
		} 

		resequencePermission('user');
	}		

     lnRedirect("index.php?mod=Permissions&file=userpermission");
} 

function add_permission($var)
{
	$pid=$var[pid];
	$uname=$var[uname];
	if (!($uid=findUID($uname)))  {
		return false;
	}
	$sequence=$var[sequence];
	$component=$var[component];
	$instance=$var[instance];
	$level=$var[level];
	$action=$var[action];
	

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $table = $lntable['user_perms'];
    $columns = &$lntable['user_perms_column'];

	if ($action == "insert") {
		$sequence += 0.5;
		$query = "INSERT INTO $table
					  ($columns[uid],
					   $columns[sequence],
					   $columns[realm],
					   $columns[component],
					   $columns[instance],
					   $columns[level])
					  VALUES ('" . lnVarPrepForStore($uid) . "',
							  '" . lnVarPrepForStore($sequence) . "',
							  '0',
							  '" . lnVarPrepForStore($component) . "',
							  '" . lnVarPrepForStore($instance) . "',
							  '" . lnVarPrepForStore($level) . "')";
	}
	else if ($action == "update") {
		$query = "UPDATE  $table SET
					   $columns[uid]='" . lnVarPrepForStore($uid) . "',
					   $columns[sequence]='" . lnVarPrepForStore($sequence) . "',
					   $columns[realm]='0',
					   $columns[component]='" . lnVarPrepForStore($component) . "',
					   $columns[instance]='" . lnVarPrepForStore($instance) . "',
					   $columns[level]='" . lnVarPrepForStore($level) . "'
					   WHERE $columns[pid]='" . lnVarPrepForStore($pid) . "'";		  
	}
	
    if (!empty($action)) {
		$dbconn->Execute($query);
	}

    if ($dbconn->ErrorNo() != 0) {
		echo $query;
        return false;
    } 

	resequencePermission('user');

    return true;
} 


function resequencePermission($type) {

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    // Work out which tables to operate against
    if ($type == "user") {
        $permtable = $lntable['user_perms'];
        $permcolumn = &$lntable['user_perms_column'];
    } else {
        $permtable = $lntable['user_perms'];
        $permcolumn = &$lntable['user_perms_column'];
    }

    // Get the information
    $query = "SELECT $permcolumn[pid],
                     $permcolumn[sequence]
               FROM $permtable
              ORDER BY $permcolumn[sequence]";
    $result = $dbconn->Execute($query);

    // Fix sequence numbers
    $seq=1;
    while(list($pid, $curseq) = $result->fields) {

        $result->MoveNext();
        if ($curseq != $seq) {
            $query = "UPDATE $permtable
                      SET $permcolumn[sequence]=" . lnVarPrepForStore($seq) . "
                      WHERE $permcolumn[pid]=" . lnVarPrepForStore($pid);
            $dbconn->Execute($query);
        }
        $seq++;
    }
    $result->Close();

    return;
}

// === Utility Functions ===============
function findUID($uname) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];
	$result = $dbconn->Execute("SELECT  $userscolumn[uid] FROM $userstable WHERE $userscolumn[uname]='". lnVarPrepForStore($uname)."'");
	list($uid) = $result->fields;
	if (empty($uid)) {
		return false;
	} 
	else {
		return $uid;
	}
}

function findUsername($uid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];
	$result = $dbconn->Execute("SELECT  $userscolumn[uname] FROM $userstable WHERE $userscolumn[uid]='". lnVarPrepForStore($uid)."'");
	list($uname) = $result->fields;
	if (empty($uname)) {
		return false;
	}
	else {
		return $uname;
	}
}


?>
