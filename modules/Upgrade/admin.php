<?php
/**
*  Upgrade
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */
include 'header.php';

$vars= array_merge($_GET,$_POST);

/** Navigator **/
$menus= array(_ADMINMENU,_UPGRADEADMIN);
$links=array('index.php?mod=Admin','index.php?mod=Upgrade&file=admin');
lnBlockNav($menus,$links);
/** Navigator **/


OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Upgrade&amp;file=admin"><B>'._UPGRADEADMIN.'</B></A><BR>';

$menu =  array();
if (lnSecAuthAction(0, 'Upgrade::', "backup/restore::", ACCESS_READ)) {
	 $menu[] = '<a href=index.php?mod=Upgrade&file=backup>'.lnBlockImage($mod,'backup').'<BR>'._BACKUP_RESTORE_ADMIN.'</a>';
} 

if (lnSecAuthAction(0, 'Upgrade::', "update::", ACCESS_READ)) {
	$menu[] = '<a href=index.php?mod=Upgrade&file=update>'.lnBlockImage($mod,'update').'<BR>'._UPDATE_ADMIN.'</a>';
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
?>