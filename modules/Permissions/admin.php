<?php
/**
*  Permission main menu
*/

if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_PERMISSIONADMIN);
$links=array('index.php?mod=Admin','index.php?mod=Permissions&file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();
	 
 echo '<BR><TABLE WIDTH=100%>'
 .'<TR ALIGN=CENTER>';
 
if (lnSecAuthAction(0, 'Permissions::', "Group::", ACCESS_READ)) {
	 echo '<TD><a href=index.php?mod=Permissions&file=grouppermission>'.lnBlockImage('Permissions','group').'<BR>'._GROUPPERMISSION.'</a> </TD> ';
}
 
if (lnSecAuthAction(0, 'Permissions::', "User::", ACCESS_READ)) {
	echo '<TD><a href=index.php?mod=Permissions&file=userpermission>'.lnBlockImage('Permissions','user').'<BR>'._USERPERMISSION.'</a> <TD> ';
}

echo '</TR></TABLE>';

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */
?>