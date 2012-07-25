<?php
/**
* Administration Panel
*/
if (!defined("LOADED_AS_MODULE")) {
    die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */
	include 'header.php';

	$column = 6; // column per row
	$wp=100/$column;
	$mods = lnModGetAdminMods();
	if($mods == false) { // there aren't admin modules
		return;
	}

/** Navigator **/
$menus= array(_ADMINMENU);
$links=array('index.php?mod=Admin');
lnBlockNav($menus,$links);
/** Navigator **/

	OpenTable();

	echo "<table width=100%  height=350 cellpadding=0 cellspacing=0 border=0><tr><td valign=top>";
	echo "<table width=100%  cellpadding=8 cellspacing=0 border=0>";
	$i=0;
	$imgfile='';
	$thistheme = lnConfigGetVar('Default_Theme');
	foreach ($mods as $mod) {
		if (lnSecAuthAction(0, "$mod[name]::", '::', ACCESS_EDIT)) {
			$imgfile='';
			if ($i%$column == 0)
				echo "<tr valign=top>";
			if((file_exists("modules/".lnVarPrepForOS($mod['directory'])."/admin.php")) &&(lnVarPrepForOS($mod['directory'])!='Submissions')) {
				echo "<td width=$wp% align=center>";
				echo "<a href=index.php?mod=".lnVarPrepForOS($mod['directory'])."&file=admin>";
				if (lnConfigGetVar('admingraphic')) {					
					echo lnBlockAdmin($mod['directory']);               ///////////////รูป
					echo "<BR>".lnVarPrepForOS($mod['displayname']);     ///////////////คำ
				}
				else{
					echo $mod['displayname'];
				}
				echo "</a>";
				echo "</td>";
				if ($i%$column == $column-1)
					echo "</tr>";
				$i++;
			}
		}
	}
	echo "</table>";
	echo "</td></tr></table>";

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */
?>