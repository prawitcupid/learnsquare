<?php
/**
*  Show module permissions schemas
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

getmodulesinstanceschemainfo();

echo '<html>'
.'<head><title>'._PERMISSIONINFO.'</title></head>'
.'<style>'
.'TD		{FONT-FAMILY: Ms Sans Serif,Helvetica; FONT-SIZE: 11px}'
.'BODY		{FONT-FAMILY: Ms Sans Serif,Helvetica; FONT-SIZE: 11px}'
.'</style>'
.'<body bgcolor=#d3d3d3>';
echo '<B>&nbsp;&nbsp;<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> '._PERMISSIONINFO.'</B><BR>&nbsp;';
echo '<CENTER><table cellpadding=3 cellspacing=1 bgcolor=#CCCCCC border=0 width=98%>';
echo '<tr bgcolor=808080 align=center><td><FONT COLOR="#FFFFFF"><B>Registered Component</B></FONT></td><td> <FONT COLOR="#FFFFFF"><B>Instance Template</B></FONT></td></tr>';
ksort($schemas);
foreach ($schemas as $component => $instance) {
	echo '<tr bgcolor=FFFFFF><td>'. $component . '</td><td>' .$instance .'</td></tr>';
}
echo '</table></CENTER>';
echo '<P><CENTER><INPUT TYPE="button" OnClick="javascript:window.close()" VALUE="Close Window"></CENTER>';


?>
