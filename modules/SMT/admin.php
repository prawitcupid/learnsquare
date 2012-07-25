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
$menus= array(_ADMINMENU,_SMT);
$links=array('index.php?mod=Admin','index.php?mod=SMT&amp;file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=SMT&amp;file=admin"><B>'._SMT.'</B></A><BR>&nbsp;';

$sel_SMT['0'] = '';
$sel_SMT['1'] = '';
$sel_SMT[lnConfigGetVar('SMTStatus')] = ' checked';
$sel_SUPARSIT['0'] = '';
$sel_SUPARSIT['1'] = '';
$sel_SUPARSIT[lnConfigGetVar('SUPARSITStatus')] = ' checked';
$sel_LEXITRON['0'] = '';
$sel_LEXITRON['1'] = '';
$sel_LEXITRON[lnConfigGetVar('LEXITRONStatus')] = ' checked';
$set_Addr= lnConfigGetVar('SMTServiceAddr') ;
$AddrLen =strlen( lnConfigGetVar('SMTServiceAddr'));
echo '<table border="0" cellpadding="2" cellspacing="0" width="100%">';

echo '<FORM METHOD=POST ACTION="index.php">';
echo '<INPUT TYPE="hidden" NAME="mod" VALUE="SMT">';
echo '<INPUT TYPE="hidden" NAME="file" VALUE="admin">';
echo '<INPUT TYPE="hidden" NAME="op" VALUE="save">';
echo '<tr><td width=150>'._AccessToSMT. '</td><td>'
."<input type=\"radio\" name=\"xSMTStatus\" value=\"1\" $sel_SMT[1]>"._Yuse.' &nbsp;'
."<input type=\"radio\" name=\"xSMTStatus\" value=\"0\" $sel_SMT[0]>"._Nuse.'</td></tr>';
echo '<tr><td width=150> Web Service Address </td><td>';
echo "<input type=\"text\" name=\"xSMTServiceAddr\" value=\"$set_Addr\">";
//suparsit
echo '<tr><td width=250>'._AccessToSUPARSIT. '</td><td>'
."<input type=\"radio\" name=\"xSUPARSITStatus\" value=\"1\" $sel_SUPARSIT[1]>"._Yuse.' &nbsp;'
."<input type=\"radio\" name=\"xSUPARSITStatus\" value=\"0\" $sel_SUPARSIT[0]>"._Nuse.'</td></tr>';
//lexitron
echo '<tr><td width=250>'._AccessToLEXITRON. '</td><td>'
."<input type=\"radio\" name=\"xLEXITRONStatus\" value=\"1\" $sel_LEXITRON[1]>"._Yuse.' &nbsp;'
."<input type=\"radio\" name=\"xLEXITRONStatus\" value=\"0\" $sel_LEXITRON[0]>"._Nuse.'</td></tr>';
echo '<tr><td>&nbsp;</td><td><BR><INPUT class="button_org" TYPE="submit" VALUE="'._SUBMIT.' "></td></tr>';
echo '</table>';
echo "<br><br>"._REFERANCESMT;
echo '</FORM>';


	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$module_varstable = $lntable['module_vars'];
	$module_varscolumn = &$lntable['module_vars_column'];
            
$query = "UPDATE $module_varstable
                      SET $module_varscolumn[value]='s:'.$AddrLen.':\"lnConfigGetVar('SMTService')\";'
                      WHERE $module_varscolumn[id]='41'";
            $dbconn->Execute($query);
            
$query = "UPDATE $module_varstable
                      SET $module_varscolumn[value]='s:'.strlen(lnConfigGetVar('SMTStatus')).':\"lnConfigGetVar('VajaStatus')\";'
                      WHERE $module_varscolumn[id]='42'";
            $dbconn->Execute($query);
            
CloseTable();
include 'footer.php';
/* - - - - - - - - - - - */
?>