<?php
/**
 *  Private_message administration
 */
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* options */
if ($op=="save") {
	$vars= array_merge($_GET,$_POST);
	foreach($vars as $name => $value) {
		if (substr($name, 0, 1) == 'x') {
			$var = lnVarCleanFromInput($name);
			$rname=substr($name, 1);
				
			if (lnSecAuthAction(0, 'Settings::', "$rname::", ACCESS_EDIT)) {
				//alert($rname+"::"+$var);
				lnConfigSetVar($rname, $var);
			}
		}
	}
	lnRedirect('index.php?mod=Mining&file=admin');
}

/* - - - - - - - - - - - */
include 'header.php';
/** Navigator **/
$menus= array(_ADMINMENU,_MINING);
$links=array('index.php?mod=Admin','index.php?mod=Mining&amp;file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Mining&amp;file=admin"><B>'._MINING.'</B></A><BR>&nbsp;';

$sel_Repository['0'] = '';
$sel_Repository['1'] = '';
$sel_Repository[lnConfigGetVar('MiningStatus')] = ' checked';

echo '<table border="0" cellpadding="2" cellspacing="0" width="100%">';

echo '<FORM METHOD=POST ACTION="index.php">';
echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Mining">';
echo '<INPUT TYPE="hidden" NAME="file" VALUE="admin">';
echo '<INPUT TYPE="hidden" NAME="op" VALUE="save">';
echo '<tr><td width=150>'._AccessToMINING. '</td><td>'
."<input type=\"radio\" name=\"xMiningStatus\" value=\"1\" $sel_Repository[1]>"._Yuse.' &nbsp;'
."<input type=\"radio\" name=\"xMiningStatus\" value=\"0\" $sel_Repository[0]>"._Nuse.'</td></tr>';

echo '<tr><td>&nbsp;</td><td><BR><INPUT class="button_org" TYPE="submit" VALUE="'._SUBMIT.' "></td></tr>';

echo '</table>';
echo "<br><br>"._REFERANCEMINING;
echo '</FORM>';


list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$module_varstable = $lntable['module_vars'];
$module_varscolumn = &$lntable['module_vars_column'];

$query = "UPDATE $module_varstable
		SET $module_varscolumn[value]='s:1:\"lnConfigGetVar('MiningStatus')\";'
		WHERE $module_varscolumn[id]='47'";
$dbconn->Execute($query);

CloseTable();
include 'footer.php';
/* - - - - - - - - - - - */
?>