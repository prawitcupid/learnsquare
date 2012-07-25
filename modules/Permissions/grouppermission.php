<?php
/**
*  Group Permission
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
	if (!lnSecAuthAction(0, 'Permissions::', "Group::$bid", ACCESS_EDIT)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
		return false;
	}
	$vars= array_merge($_GET,$_POST);
	switch($op) {
		case "increase_order": 
			 if (lnSecAuthAction(0, 'Permissions::', "Group::$gid", ACCESS_EDIT)) {
				increase_order($vars);
			 }
			break;
		case "decrease_order": 
			 if (lnSecAuthAction(0, 'Permissions::', "Group::$gid", ACCESS_EDIT)) {
				decrease_order($vars);
			}
			break;
		case "delete_permission":
			if (lnSecAuthAction(0, 'Permissions::', "Group::$gid", ACCESS_DELETE)) {
				delete_permission($vars);
			}
			break;
		case "add_permission":
			 if (lnSecAuthAction(0, 'Permissions::', "Group::$gid", ACCESS_ADMIN)) {
				add_permission($vars);
			}
			break;
	}
}

/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_PERMISSIONADMIN,_GROUPPERMISSION);
$links=array('index.php?mod=Admin','index.php?mod=Permissions&file=admin','index.php?mod=Permissions&amp;file=grouppermission');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Permissions&amp;file=grouppermission"><B>'._GROUPPERMISSION.'</B></A><BR>&nbsp;';

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$grouppermstable = $lntable['group_perms'];
$grouppermscolumn = &$lntable['group_perms_column'];
$groupstable = $lntable['groups'];
$groupscolumn = &$lntable['groups_column'];

// Group List
$gids[]=-1;
$gnames[]=_ALLGROUPS;
$gids[]=0;
$gnames[]=_UNREGUSERS;
$result2 = $dbconn->Execute("SELECT  $groupscolumn[gid], $groupscolumn[name]  FROM $groupstable");
while(list($gid2, $name2) = $result2->fields) {
	$result2->MoveNext();
	$gids[]=$gid2;
	$gnames[]=$name2;
}

// get permission level name
$alevelname = accesslevelnames();

// show all permission
$query ="SELECT * FROM $grouppermstable ORDER BY $grouppermscolumn[sequence]";
$result = $dbconn->Execute($query);
 
if ($dbconn->ErrorNo() <> 0) {
	echo $dbconn->ErrorNo() . "List Group Permission " . $dbconn->ErrorMsg() . "<br>";
	error_log ($dbconn->ErrorNo() . "List Group Permission " . $dbconn->ErrorMsg() . "<br>");
	return;
} 

$active_count = 0;
$total_count = $result->PO_RecordCount();

echo '<table class="list" width="100%" cellpadding="3" cellspacing="1">'
.'<tr align=center>'
.'<td class="head">'._ORDER.'</td><td class="head">'._GROUP.'</td><td class="head">'._COMPONENT.'</td><td class="head">'._INSTANCE.'</td><td class="head">'._PERMISSION.'</td><td class="head">&nbsp;</td>'
.'</tr>';

while (list($pid, $gid, $sequence, $realm, $component, $instance,$level,$bond) = $result->fields) {
	$result->MoveNext();

	if (lnSecAuthAction(0, 'Permissions::', "Group::$gid", ACCESS_READ)) {
		 $prev_active_count = $active_count;
		 $active_count++;
		 $groupname = findGroupName($gid);
		 $levelname = accesslevelname($level);
		
		if (!($op == "edit_permission_form" && $active_count == $item)) {
			echo '<tr bgcolor=#FFFFFF>';
			switch (true) {
				case ($active_count == 0):
					$arrows = "&nbsp";
					break;
				case ($active_count == 1):
					$arrows = "<a href=index.php?mod=Permissions&amp;file=grouppermission&amp;op=increase_order&amp;pid=$pid&amp;sequence=$sequence><img src=images/global/down.gif border=0></a>";
					break;
				case ($active_count== $total_count):
					$arrows = "<a href=index.php?mod=Permissions&amp;file=grouppermission&amp;op=decrease_order&amp;pid=$pid&amp;sequence=$sequence><img src=images/global/up.gif border=0></a>";
					break;
				default:
					$arrows = "<a href=index.php?mod=Permissions&amp;file=grouppermission&amp;op=decrease_order&amp;pid=$pid&amp;sequence=$sequence><img src=images/global/up.gif border=0></a>&nbsp;<a href=index.php?mod=Permissions&amp;file=grouppermission&amp;op=increase_order&amp;pid=$pid&amp;sequence=$sequence><img src=images/global/down.gif border=0></a>";
			} 
			 echo '<td align=center>'.$arrows 
			.'</td><td align=center>'.$groupname
			.'</td><td>'.$component
			.'</td><td>'.$instance
			.'</td><td align=center>'. $levelname
			.'</td><td align=center width=100>';
			 if (lnSecAuthAction(0, 'Permissions::', "Group::$gid", ACCESS_EDIT)) {
				 echo ' <A HREF="index.php?mod=Permissions&amp;file=grouppermission&amp;op=edit_permission_form&amp;item='.$active_count.'"><IMG SRC="images/global/edit.gif" WIDTH="14" HEIGHT="16" BORDER="0" ALT="'._EDIT.'"></A> ';
			 }
			 if (lnSecAuthAction(0, 'Permissions::', "Group::$gid", ACCESS_ADD)) {
				echo '<A HREF="index.php?mod=Permissions&amp;file=grouppermission&amp;op=add_permission_form&amp;item='.$active_count.'"><IMG SRC="images/global/insert.gif" WIDTH="14" HEIGHT="16" BORDER="0" ALT="'._INSERT.'"></A>  ';
			 }
			 if (lnSecAuthAction(0, 'Permissions::', "Group::$gid", ACCESS_DELETE)) {
				 echo " <a href=\"javascript:if(confirm('Delete permission ?')) window.open('index.php?mod=Permissions&amp;file=grouppermission&amp;op=delete_permission&amp;pid=$pid','_self')\"><IMG SRC=\"images/global/delete.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=\"0\" ALT=\""._DELETE."\"></a>";
			 }
			echo '</td></tr>';
		}

		//show add permission form
		if (($op == "edit_permission_form" || $op == "add_permission_form") && $active_count == $item) {
				echo '<FORM METHOD=POST ACTION="index.php">'
				.'<INPUT TYPE="hidden" NAME="mod" VALUE="Permissions">'
				.'<INPUT TYPE="hidden" NAME="file" VALUE="grouppermission">'
				.'<INPUT TYPE="hidden" NAME="op" VALUE="add_permission">'
				.'<INPUT TYPE="hidden" NAME="sequence" VALUE="'.$item.'">';
				
				if ($op == "edit_permission_form") {
					echo '<INPUT TYPE="hidden" NAME="action" VALUE="update">';
					echo '<INPUT TYPE="hidden" NAME="pid" VALUE="'.$pid.'">';
				}
				else {
					$gid = -2;
					$sequence=0;
					$component='';
					$instance='';
					$level=-1;
					echo '<INPUT TYPE="hidden" NAME="action" VALUE="insert">';			
				}
			
				echo '<tr bgcolor=#CCCCCC align=center valign=top>'
				.'<td>&nbsp;</td><td><SELECT class="select" NAME="gid">';
				for ($x=0; $x<count($gids); $x++) {
						if ($gids[$x] == $gid) {
							echo '<OPTION VALUE="'.$gids[$x].'" selected>'.$gnames[$x].'</OPTION>';
						}
						else {
							echo '<OPTION VALUE="'.$gids[$x].'">'.$gnames[$x].'</OPTION>';
						}
				}
				echo '</SELECT></td>'

				.'<td>'
				.'<TEXTAREA class="select" NAME="component" ROWS="2" COLS="15">'.$component.'</TEXTAREA></td>'
				.'<td>'
				.'<TEXTAREA class="select" NAME="instance" ROWS="2" COLS="15">'.$instance.'</TEXTAREA></td>'
				.'<td>'
				.'<SELECT class="select" NAME="level">';
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
				.' <INPUT CLASS="button_gray" TYPE="button" VALUE="'._CANCEL.'" onclick="javascript:window.open(\'index.php?mod=Permissions&amp;file=grouppermission\',\'_self\')"></td>'
				.'</tr>';
				echo '</FORM>';
		}
	}
}


echo '</table>';
 

echo "<DIV ALIGN=RIGHT><BR><A HREF=\"javascript:popup('index.php?mod=Permissions&amp;file=showpermission','_blank',550,550)\"><B>"._PERMISSIONINFO."</B></A>&nbsp;</DIV>";

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */


//Funtions//////////////////////////////////
function increase_order($var)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    if (!empty($var['pid']) && !empty($var['sequence'])) {
        $new_sequence = $var['sequence'] + 1;
        $column = &$lntable['group_perms_column'];
        $result = $dbconn->Execute("UPDATE $lntable[group_perms] SET $column[sequence]=" . lnVarPrepForStore($new_sequence) . "
                                    WHERE $column[pid]='" . $var['pid'] . "' AND $column[sequence]='" . $var['sequence'] . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Increase order 1" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Increase order 1: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 

        $result = $dbconn->Execute("UPDATE $lntable[group_perms] SET $column[sequence]=" . $var['sequence'] . "
                                    WHERE $column[pid]<>'" . $var['pid'] . "' AND $column[sequence]='" . lnVarPrepForStore($new_sequence) . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Increase order 2" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Increase order 2: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 
    } 

     resequencePermission('Group');
	
	 lnRedirect("index.php?mod=Permissions&file=grouppermission");
} 

function decrease_order($var)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    if (!empty($var['pid']) && !empty($var['sequence'])) {
        $new_sequence = $var['sequence'] - 1;
        $column = &$lntable['group_perms_column'];
        $result = $dbconn->Execute("UPDATE $lntable[group_perms] SET $column[sequence]=" . lnVarPrepForStore($new_sequence) . "
                                    WHERE $column[pid]='" . $var['pid'] . "' AND $column[sequence]='" . $var['sequence'] . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Decrease order 1" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Decrease order 1: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 

        $result = $dbconn->Execute("UPDATE $lntable[group_perms] SET $column[sequence]=" . $var['sequence'] . "
                                    WHERE $column[pid]<>'" . $var['pid'] . "' AND $column[sequence]='" . lnVarPrepForStore($new_sequence) . "'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Decrease order 2" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Decrease order 2: " . $dbconn->ErrorMsg() . "<br>");
            return;
        } 
    } 
	 resequencePermission('Group');

     lnRedirect("index.php?mod=Permissions&file=grouppermission");
} 


function delete_permission($var)
{
	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $column = &$lntable['group_perms_column'];
	if (!empty($var['pid'])) {
		$query = "DELETE FROM $lntable[group_perms]
				  WHERE $column[pid] = '" . lnVarPrepForStore($var['pid']) . "'";
		$dbconn->Execute($query);

		if ($dbconn->ErrorNo() != 0) {
			return false;
		} 

		resequencePermission('Group');
	}		

     lnRedirect("index.php?mod=Permissions&file=grouppermission");
} 

function add_permission($var)
{
	$pid=$var[pid];
	$gid=$var[gid];
	$sequence=$var[sequence];
	$component=$var[component];
	$instance=$var[instance];
	$level=$var[level];
	$action=$var[action];

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $table = $lntable['group_perms'];
    $columns = &$lntable['group_perms_column'];

	if ($action == "insert") {
		$sequence += 0.5;
		$query = "INSERT INTO $table
					  ($columns[gid],
					   $columns[sequence],
					   $columns[realm],
					   $columns[component],
					   $columns[instance],
					   $columns[level])
					  VALUES ('" . lnVarPrepForStore($gid) . "',
							  '" . lnVarPrepForStore($sequence) . "',
							  '0',
							  '" . lnVarPrepForStore($component) . "',
							  '" . lnVarPrepForStore($instance) . "',
							  '" . lnVarPrepForStore($level) . "')";
	}
	else if ($action == "update") {
		$query = "UPDATE  $table SET
					   $columns[gid]='" . lnVarPrepForStore($gid) . "',
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

	resequencePermission('Group');

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
        $permtable = $lntable['group_perms'];
        $permcolumn = &$lntable['group_perms_column'];
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

?>
