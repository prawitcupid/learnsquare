<?php
/**
*  SCORM main menu
*/

if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'SCORM::', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}


//===================================================================
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_SCORMADMIN);
$links=array('index.php?mod=Admin','index.php?mod=SCORM&file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();


// echo '<CENTER><font size="3"><B>'._USERADMIN.'</B></font></CENTER>';
if (lnSecAuthAction(0, 'SCORM::', "import::", ACCESS_READ)) {
	 $menu[] = '<a href=index.php?mod=SCORM&file=import>'.lnBlockImage($mod,'import').'<BR>'._SCORMIMPORT.'</a>';
 } 

if (lnSecAuthAction(0, 'SCORM::', "export::", ACCESS_READ)) {
	$menu[] = '<a href=index.php?mod=Repository&file=searchScormRepository>'.lnBlockImage($mod,'import_repository').'<BR>'._SCORMIMPORTREPOSITORY.'</a>';
} 

// show menu
 echo '<P><table width=100% border=0 cellpadding=3 cellspacing=0>'
 .'<tr align=center>';
$wp = 100/count($menu);
for ($i=0; $i < count($menu); $i++) {
	echo "<td width=$wp%>".$menu[$i]."</td>";
}
echo '</tr></table>';

CloseTable();

include 'footer.php';
//===================================================================
?>