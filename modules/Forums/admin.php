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
				lnConfigSetVar($rname, $var);
			}
        }
    }
	lnRedirect('index.php?mod=Admin');
}

/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_FORUM);
$links=array('index.php?mod=Admin','index.php?mod=Forums&amp;file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Forums&amp;file=admin"><B>'._FORUM.'</B></A><BR>&nbsp;';

$sel_showsmiley['0'] = '';
$sel_showsmiley['1'] = '';
$sel_showsmiley[lnConfigGetVar('showsmiley')] = ' checked';
$sel_uploadpic['0'] = '';
$sel_uploadpic['1'] = '';
$sel_uploadpic[lnConfigGetVar('uploadpic')] = ' checked';

echo '<table border="0" cellpadding="2" cellspacing="0" width="100%">';

echo '<FORM METHOD=POST ACTION="index.php">';
echo '<INPUT TYPE="hidden" NAME="mod" VALUE="Forums">';
echo '<INPUT TYPE="hidden" NAME="file" VALUE="admin">';
echo '<INPUT TYPE="hidden" NAME="op" VALUE="save">';

echo '<tr><td width=150>'._SHOWSMILEY. '</td><td>'
."<input type=\"radio\" name=\"xshowsmiley\" value=\"1\" $sel_showsmiley[1]>"._SYES.' &nbsp;'
."<input type=\"radio\" name=\"xshowsmiley\" value=\"0\" $sel_showsmiley[0]>"._SNO.'</td></tr>';

echo '<tr><td width=150>'._UPLOADPIC. '</td><td>'
."<input type=\"radio\" name=\"xuploadpic\" value=\"1\" $sel_uploadpic[1]>"._SYES.' &nbsp;'
."<input type=\"radio\" name=\"xuploadpic\" value=\"0\" $sel_uploadpic[0]>"._SNO.'</td></tr>';
echo '<tr><td>&nbsp;</td><td><BR><INPUT class="button_org" TYPE="submit" VALUE="'._SUBMIT.'"></td></tr>';
echo '</table>';

echo '</FORM>';

CloseTable();
include 'footer.php';
/* - - - - - - - - - - - */
?>