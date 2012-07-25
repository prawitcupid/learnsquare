<?php
/**
*  Modules
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "$mid::", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_MODULEADMIN);
$links=array('index.php?mod=Admin','index.php?mod=Modules&file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Modules&file=admin"><B>'._MODULEADMIN.'</B></A><BR>&nbsp;';

switch($op) {
	case "deactivate_module": deactivateModule($mid); break;
	case "activate_module": activateModule($mid); break;
	case "initialise_module": initialiseModule($mid); break;
	case "delete": deleteModule($mid); break;
	case "savechange": saveModule($mid,$dname,$description); break;
	case "regenerate": regenerateModule(); break;
	case "edit": editModule($mid); return;
}
 
echo '<table class="list" cellspacing="1" cellpadding="3" width="100%">'
 .'<tr align=center><td class="head">'._STATE.'</td><td class="head">'._MODULENAME.'</td><td class="head">'._DISPLAYNAME.'</td><td class="head">'._DISCRIPTION.'</td><td class="head">&nbsp;</td></tr>';

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$modulestable = $lntable['modules'];
$modulescolumn = &$lntable['modules_column'];
$query = "SELECT $modulescolumn[id], $modulescolumn[name], 
					$modulescolumn[displayname], $modulescolumn[description], $modulescolumn[state], $modulescolumn[type]
		  FROM $modulestable WHERE (($modulescolumn[name] <> 'Modules')&&($modulescolumn[name]<>'Submissions')) ORDER BY $modulescolumn[type],$modulescolumn[name]";

$result = $dbconn->Execute($query);

 while(list($id, $name, $displayname, $description, $state,$type) = $result->fields) {
        $result->MoveNext();
			switch ($state) {
			case _LNMODULE_STATE_ACTIVE : 
				$img_cmd = "<a href=index.php?mod=Modules&amp;file=admin&amp;op=deactivate_module&amp;mid=$id>" . '<img src="images/global/green_dot.gif" border=0 ALT="' . _MODULEDEACTIVATE . '">' . '</a>';
				break;
			case _LNMODULE_STATE_UNINITIALISED:
				$img_cmd = "<a href=index.php?mod=Modules&amp;file=admin&amp;op=initialise_module&amp;mid=$id>" . '<img src="images/global/gray_dot.gif" border=0 ALT="'._INITIALISED.'"></a>';
				break;
			case _LNMODULE_STATE_MISSING:
				$img_cmd = "<a href=index.php?mod=Modules&amp;file=admin&amp;op=activate_module&amp;mid=$id>" . '<img src="images/global/white_dot.gif" border=0 ALT="'._MISSING.'"></a>';
				break;
			case _LNMODULE_STATE_UPGRADED: 
				$img_cmd = "<a href=index.php?mod=Modules&amp;file=admin&amp;op=activate_module&amp;mid=$id>" . '<img src="images/global/yellow_dot.gif" border=0 ALT="'._UPGRADED.'"></a>';
				break;
			case _LNMODULE_STATE_INACTIVE :
				$img_cmd = "<a href=index.php?mod=Modules&amp;file=admin&amp;op=activate_module&amp;mid=$id>" . '<img src="images/global/red_dot.gif" border=0 ALT="'._INACTIVE.'"></a>';
				break;
		   }
			if ($type == _LNMODULE_TYPE_CORE) {
				$img_cmd .= _MODULECORE ;
			}
	
		echo '<tr align=center bgcolor=#FFFFFF>'
		.'<td>'.$img_cmd.'</td>'
		.'<td>'.$name.'</td>'
		.'<td>'.$displayname.'</td>'
		.'<td>'.$description.'</td>';
		echo '<td>';
		echo '<A HREF="index.php?mod=Modules&amp;file=admin&amp;op=edit&amp;mid='.$id.'"><IMG SRC="images/global/edit.gif" WIDTH="14" HEIGHT="16" BORDER="0" ALT="'._EDIT.'"></A>';
		if ($state == _LNMODULE_STATE_INACTIVE ) {
			echo " &nbsp;<A HREF=\"javascript:if(confirm('Delete ?')) window.open('index.php?mod=Modules&amp;file=admin&amp;op=delete&amp;mid=".$id."','_self')\"><IMG SRC=\"images/global/delete.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=\"0\" ALT=\""._DELETE."\"></A>";
		}			
		echo '</td>';
		echo '</tr>';
  }

$result->Close(); 
 
 echo '</table>';
 echo '<BR><CENTER><input class="button_org" type="button" onclick="javascript: window.open(\'index.php?mod=Modules&amp;file=admin&amp;op=regenerate\',\'_self\')" value="'._REGENERATE.'">';
 echo '</CENTER>';
 echo '<UL TYPE="circle">';
 echo '<LI><img src="images/global/green_dot.gif" border=0 ALT="' . _ACTIVATE . '"> '._ACTIVE;
 echo '&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/global/red_dot.gif" border=0 ALT="' . _INACTIVE . '"> '._INACTIVE;
 echo '&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/global/gray_dot.gif" border=0 ALT="' . _INITIALISED . '"> '._INITIALISED;
 echo '&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/global/white_dot.gif" border=0 ALT="' . _MISSING . '"> '._MISSING;
 echo '&nbsp;&nbsp;&nbsp;&nbsp;<img src="images/global/yellow_dot.gif" border=0 ALT="' . _UPGRADED . '"> '._UPGRADED;
 echo '<LI><IMG SRC="images/global/edit.gif" WIDTH="14" HEIGHT="16" BORDER="0" ALT="'._EDIT.'"> '._EDIT
.'&nbsp;&nbsp;<IMG SRC="images/global/delete.gif" WIDTH="14" HEIGHT="16" BORDER="0" ALT="'._DELETE.'"> '._DELETE;
 echo '<LI>('._MODULECORE.') Core Module ';
 echo '</UL>';
CloseTable();



include 'footer.php';


///Funtions/////////////////////////////////////////////////////////////////////////////////
// deactive Module
function deactivateModule($mid)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	if (!empty($mid)) {
		$modulestable = $lntable['modules'];
		$modulescolumn = &$lntable['modules_column'];

        $result = $dbconn->Execute("UPDATE $modulestable SET $modulescolumn[state]="._LNMODULE_STATE_INACTIVE."
                                    WHERE $modulescolumn[id]='" . lnVarPrepForStore($mid) . "'");

		if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Deactivate Module" . $dbconn->ErrorMsg() . "<br>";
            return;
        }  
    } 
} 

// active Module
function activateModule($mid)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	if (!empty($mid)) {
		$modulestable = $lntable['modules'];
		$modulescolumn = &$lntable['modules_column'];

        $result = $dbconn->Execute("UPDATE $modulestable SET $modulescolumn[state]="._LNMODULE_STATE_ACTIVE."
                                    WHERE $modulescolumn[id]='" . lnVarPrepForStore($mid) . "'");

		if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Deactivate Module" . $dbconn->ErrorMsg() . "<br>";
            return;
        }  
    } 
} 

function editModule($mid) {
	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	$modulestable = $lntable['modules'];
	$modulescolumn = &$lntable['modules_column'];
    $result = $dbconn->Execute("SELECT $modulescolumn[displayname], $modulescolumn[description] FROM $modulestable
                                    WHERE $modulescolumn[id]='" . lnVarPrepForStore($mid) . "'");

	list($display_name, $description) = $result->fields;

	// show input form
	echo '<FORM METHOD=POST ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Modules">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="savechange">'
	.'<INPUT TYPE="hidden" NAME="mid" VALUE="'.$mid.'">';

	echo '<CENTER><table cellpadding=3>'
	.'<tr><td colspan=2 align=center bgcolor=#808080 class="head" height=20><B>'._MODULEEDIT.'</B></td>'
	.'<tr><td>'._ENTERDISPLAYNAME.'</td><td><INPUT TYPE="text" NAME="dname" VALUE="'.$display_name.'" SIZE="20"></td></tr>'
	.'<tr><td>'._ENTERDISCRIPTION.'</td><td><INPUT TYPE="text" NAME="description" VALUE="'.$description.'" SIZE="40"></td></tr>'
	.'<tr><td>&nbsp;</td><td><INPUT class="button_org" TYPE="submit" VALUE="'._SUBMIT.'"></td></tr>';
	echo '</table></CENTER>';
	echo '</FORM>';

}

function saveModule($mid,$dname,$description) {

	 list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	if (!empty($mid)) {
		$modulestable = $lntable['modules'];
		$modulescolumn = &$lntable['modules_column'];

        $result = $dbconn->Execute("UPDATE $modulestable SET $modulescolumn[displayname]='".lnVarPrepForStore($dname)."',
									$modulescolumn[description]='".lnVarPrepForStore($description)."'
                                    WHERE $modulescolumn[id]='" . lnVarPrepForStore($mid) . "'");

		if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Deactivate Module" . $dbconn->ErrorMsg() . "<br>";
            return;
        }  
    } 
}

function deleteModule($mid) {
	 list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	if (!empty($mid)) {
		$modulestable = $lntable['modules'];
		$modulescolumn = &$lntable['modules_column'];

	 // Get module information
		$modinfo = lnModGetInfo($mid);

        $result = $dbconn->Execute("DELETE FROM $modulestable 
                                    WHERE $modulescolumn[id]='" . lnVarPrepForStore($mid) . "'");

		if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Delete Module" . $dbconn->ErrorMsg() . "<br>";
            return;
        }  

		if (empty($modinfo)) {
//			pnSessionSetVar('errormsg', _MODNOSUCHMOD);
			return false;
		}                

	
		// Get module database info
		lnModDBInfoLoad($modinfo['name'], $modinfo['directory']);
	

		// Module deletion function
		$osdir = lnVarPrepForOS($modinfo['directory']);
		@include("modules/$osdir/init.php");
		$func = $modinfo['name'] . '_delete';
		if (function_exists($func)) {
			if ($func() != true) {
				return false;
			}
		}
	
	} 
}


function regenerateModule() {

// Get all modules on filesystem
    $filemodules = array();
    $dh = opendir('modules');
    while ($dir = readdir($dh)) {
		unset($modtype);
        if ((is_dir("modules/$dir")) && ($dir != '.') && ($dir != '..') && ($dir != 'CVS')) {
			// Work out if admin-capable
			if (file_exists("modules/$dir/admin.php")) {
                $adminCapable = _YES;
            } else {
                $adminCapable = _NO;
            }
			// Work out if user-capable
            if (file_exists("modules/$dir/index.php")) {
                $userCapable=_YES;
            } else {
                $userCapable = _NO;
            }
			// Get the module version
            $modversion['version'] = '0';
            $modversion['description'] = '';
            $modversion['displayname'] = '';
			@include("modules/$dir/version.php");
			$modtype = $modversion['modtype'];
			$displayname = $modversion['displayname'];
			$version = $modversion['version'];
            $description = $modversion['description'];
			
			$filemodules[$dir] = array('directory' => $dir,
						'name' => $dir,
						'type' => $modtype,
						'displayname' => $displayname,
						'version' => $version,
						'description' => $description,
						'admincapable' => $adminCapable,
						'usercapable' => $userCapable);
		}
	}
	closedir($dh);

  // Get all modules in DB
    $dbmodules = array();
	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $modulestable = $lntable['modules'];
    $modulescolumn = &$lntable['modules_column'];
    $query = "SELECT $modulescolumn[id],
                     $modulescolumn[name],
                     $modulescolumn[type],
                     $modulescolumn[displayname],
                     $modulescolumn[directory],
                     $modulescolumn[admin_capable],
                     $modulescolumn[user_capable],
                     $modulescolumn[version],
                     $modulescolumn[state]
              FROM $modulestable";
    $result = $dbconn->Execute($query);
    while(list($mid, $name, $modtype, $displayname, $directory, $adminCapable, $userCapable, $version, $state) = $result->fields) {
        $result->MoveNext();
        $dbmodules[$name] = array('id' => $mid,
                                  'directory' => $directory,
								  'displayname' => $displayname,
                                  'admincapable' => $adminCapable,
                                  'usercapable' => $userCapable,
                                  'version' => $version,
                                  'state' => $state);
    }
    $result->Close();	
	
	// See if we have lost any modules since last generation
    foreach ($dbmodules as $name => $modinfo) {
        if (empty($filemodules[$name])) {
            // Old module
			echo $filemodules[$name];

            // Get module ID
            $query = "SELECT $modulescolumn[id]
                      FROM $modulestable
                      WHERE $modulescolumn[name] = '" . lnVarPrepForStore($name) . "'";
            $result = $dbconn->Execute($query);

            if ($result->EOF) {
                die("Failed to get module ID");
            }

            list($mid) = $result->fields;
            $result->Close();
			
            // Set state of module to 'missing'
            modulesSetstate(array('mid'=>$mid,'state'=> _LNMODULE_STATE_MISSING));
            unset($dbmodules[$name]);
        }
    }

	// See if we have gained any modules since last generation,
    // or if any current modules have been upgraded
    foreach ($filemodules as $name => $modinfo) {
        if (empty($dbmodules[$name])) {
            // New module
            $modid = $dbconn->GenId($lntable['modules']);
            $sql = "INSERT INTO $modulestable
                      ($modulescolumn[id],
                       $modulescolumn[name],
                       $modulescolumn[type],
                       $modulescolumn[displayname],
                       $modulescolumn[directory],
                       $modulescolumn[admin_capable],
                       $modulescolumn[user_capable],
                       $modulescolumn[state],
                       $modulescolumn[version],
                       $modulescolumn[description])
                    VALUES
                      (" . lnVarPrepForStore($modid) . ",
                       '" . lnVarPrepForStore($modinfo['name']) . "',
                       '" . lnVarPrepForStore($modinfo['type']) . "',
                       '" . lnVarPrepForStore($modinfo['displayname']) . "',
                       '" . lnVarPrepForStore($modinfo['directory']) . "',
                       " . lnVarPrepForStore($modinfo['admincapable']) . ",
                       " . lnVarPrepForStore($modinfo['usercapable']) . ",
                       " . _LNMODULE_STATE_UNINITIALISED . ",
                       '" . lnVarPrepForStore($modinfo['version']) . "',
                       '" . lnVarPrepForStore($modinfo['description']) . "')";
					 
            $dbconn->Execute($sql);
			
        } else {
            if ($dbmodules[$name]['version'] != $modinfo['version']) {
                if ($dbmodules[$name]['state'] != _LNMODULE_STATE_UNINITIALISED) {
					$sql = "UPDATE $modulestable
                            SET $modulescolumn[version]='".lnVarPrepForStore($modinfo[version])."' ,
									  $modulescolumn[displayname]='".lnVarPrepForStore($modinfo[displayname])."' ,
									  $modulescolumn[description]='".lnVarPrepForStore($modinfo[description])."' ,
									  $modulescolumn[state] = " . _LNMODULE_STATE_UPGRADED . "
                            WHERE $modulescolumn[name] <> 'Modules' and $modulescolumn[id] = " . lnVarPrepForStore($dbmodules[$name]['id']);
                    $dbconn->Execute($sql);
					
                }
            }
        }
    }

    return true;
}

/**
 * set the state of a module
 * @param $args['mid'] the module id
 * @param $args['state'] the state
 */
function modulesSetstate($args)
{
    // Get arguments from argument array
    extract($args);

    // Set state
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $modulestable = $lntable['modules'];
    $modulescolumn = &$lntable['modules_column'];

    $sql = "UPDATE $modulestable
            SET $modulescolumn[state] = " . lnVarPrepForStore($state) . "
            WHERE $modulescolumn[id] = " .(int)lnVarPrepForStore($mid);
    $result = $dbconn->Execute($sql);

    return true;
}

/**
 * initialise a module
 */
function initialiseModule($mid) {
	// Argument check
    if (!isset($mid)||!is_numeric($mid)) {
        return false;
    }

    // Get module information
    $modinfo = lnModGetInfo($mid);
    if (empty($modinfo)) {
        return false;
    }                

    // Get module database info
    lnModDBInfoLoad($modinfo['name'], $modinfo['directory']);

    // Module initialisation function
    $osdir = lnVarPrepForOS($modinfo['directory']);
    @include("modules/$osdir/init.php");
	
    $func = $modinfo['name'] . '_init';
	if (function_exists($func)) {
        if ($func() != true) {
            return false;
        }
    }

	// Update state of module
    if (!modulesSetstate(array('mid' => $mid,
                                         'state' => _LNMODULE_STATE_INACTIVE))) {
        return false;
    }

    // Success
    return true;
}

?>