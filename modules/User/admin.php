<?php
/**
* User Administration Menu
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
$menus= array(_ADMINMENU,_USERADMIN);
$links=array('index.php?mod=Admin','index.php?mod=User&file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<TABLE CELLPADING=3 CELLSPACING=0 WIDTH="100%" HEIGHT="350">'
.'<TR VALIGN="TOP"><TD>';

 $menu =  array();

if (lnConfigGetVar('admingraphic')) {	
	if (lnSecAuthAction(0, 'User::', "useradd::", ACCESS_READ)) {
		$menu[] = '<a href=index.php?mod=User&file=useradd>'. lnBlockImage('User','add').'<BR>'._USERADD.'</a>';
	 } 
	if (lnSecAuthAction(0, 'User::', "useraddfile::", ACCESS_READ) && lnConfigGetVar('reg_allowfile')) {
		$menu[] = '<a href=index.php?mod=User&file=useraddfile>'. lnBlockImage('User','addfile').'<BR>'._USERADDFILE.'</a>';
	}
	if (lnSecAuthAction(0, 'User::', "useredit::", ACCESS_READ)) {
		$menu[] = '<a href=index.php?mod=User&file=useredit>'. lnBlockImage('User','edit').'<BR>'._USEREDIT.'</a>';
	} 
	if (lnSecAuthAction(0, 'User::', "userconfig::", ACCESS_READ)) {
		$menu[] = '<a href=index.php?mod=User&file=userconfig>'. lnBlockImage('User','userconfig').'<BR>'._USERREGCONFIG.'</a>';
	}
	if (lnSecAuthAction(0, 'User::', "userdyconfig::", ACCESS_READ)) {
		$menu[] = '<a href=index.php?mod=User&file=userdyconfig>'. lnBlockImage('User','dynamic').'<BR>'._USERDYCONFIG.'</a>';
	}
}
else {
	if (lnSecAuthAction(0, 'User::', "useradd::", ACCESS_READ)) {
		$menu[] = '<a href=index.php?mod=User&file=useradd>'._USERADD.'</a>';
	 } 
	if (lnSecAuthAction(0, 'User::', "useraddfile::", ACCESS_READ) && lnConfigGetVar('reg_allowfile')) {
		$menu[] = '<a href=index.php?mod=User&file=useraddfile>'._USERADDFILE.'</a>';
	}
	if (lnSecAuthAction(0, 'User::', "useredit::", ACCESS_READ)) {
		$menu[] = '<a href=index.php?mod=User&file=useredit>'._USEREDIT.'</a>';
	} 
	if (lnSecAuthAction(0, 'User::', "userconfig::", ACCESS_READ)) {
		$menu[] = '<a href=index.php?mod=User&file=userconfig>'._USERREGCONFIG.'</a>';
	}
	if (lnSecAuthAction(0, 'User::', "userdyconfig::", ACCESS_READ)) {
		$menu[] = '<a href=index.php?mod=User&file=userdyconfig>'._USERDYCONFIG.'</a>';
	}
}

// show menu
 echo '<P><table width=100% border=0 cellpadding=3 cellspacing=0>'
 .'<tr align=center>';
$wp = 100/count($menu);
for ($i=0; $i < count($menu); $i++) {
	echo "<td width=$wp%>".$menu[$i]."</td>";
}
echo '</tr></table>';

echo '</TD></TR>'
.'</TABLE>';

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */
?>