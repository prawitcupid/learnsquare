<?php
/**
* Site configuration module

last edit :-----
programmer : Neetiwit B.
date : 23-01-2550
Description :
 1. แก้ไขให้สามารถแก้ไขข้อมูล ldap ผ่าน gui ได้
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
	require_once 'modules/Settings/modify_config.php';
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
	add_src_rep("isUse",$vars['ldapstatus']);
	add_src_rep("ldapserver", $vars['ldapip']);
	add_src_rep("ldapsitename", $vars['ldapsitename']);
	add_src_rep("sitesuffix", $vars['ldapsitesuffix']);
	add_src_rep("ou", $vars['ldapou']);
	modify_file("config.php", "config-old.php", $reg_src, $reg_rep);
	lnRedirect('index.php?mod=Admin');
}

/* - - - - - - - - - - - */
include 'header.php';

// site configuration form
// Set the current settings for select fields, radio buttons and checkboxes.
// Much better then using if() statements all over the place :-)
$sel_defaulttheme[lnConfigGetVar('Default_Theme')] = ' selected';
$sel_themechange['0'] = '';
$sel_themechange['1'] = '';
$sel_themechange[lnConfigGetVar('theme_change')] = ' checked';
$sel_admingraphic['0'] ='';
$sel_admingraphic['1'] ='';
$sel_admingraphic[lnConfigGetVar('admingraphic')] = ' checked';
$sel_htmleditor['0'] ='';
$sel_htmleditor['1'] ='';
$sel_htmleditor[lnConfigGetVar('htmleditor')] = ' checked';
$sel_lang[lnConfigGetVar('language')] = ' selected';
 $sel_seclevel['High'] = '';
$sel_seclevel['Medium'] = '';
$sel_seclevel['Low'] = '';
$sel_seclevel[lnConfigGetVar('seclevel')] = 'selected';

/** Navigator **/
$menus= array(_ADMINMENU,_SITECONFIG);
$links=array('index.php?mod=Admin','index.php?mod=Settings&amp;file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Settings&amp;file=admin"><B>'._SITECONFIG.'</B></A><BR>';


// General Site Information
    echo  '<form action="index.php" name="settings" method="post">';
    echo '<LI TYPE="circle"><B>'._GENSITEINFO.'</B><BR>&nbsp;';
	echo '<table border="0"><tr><td>'
	 ._SITENAME.":</td><td><input class=input type=\"text\" name=\"xsitename\" value=\"".lnConfigGetVar('sitename')."\" size=\"50\" maxlength=\"100\" >"
      .'</td></tr><tr><td>'
      ._SLOGAN.":</td><td><input class=input  type=\"text\" name=\"xslogan\" value=\"".lnConfigGetVar('slogan')."\" size=\"50\" maxlength=\"100\" >"
      .'</td></tr><tr><td>'
	  ._ADMINEMAIL.":</td><td><input class=input  type=\"text\" name=\"xadminmail\" value=\"".lnConfigGetVar('adminmail')."\" size=30 maxlength=100>"
       .'</td></tr><tr><td>'
       ._DEFAULTTHEME.':</td><td><select class=select name="xDefault_Theme" size="1" class="pn-normal">';
	   $handle = opendir('themes');
		while ($f = readdir($handle)) {
			if ($f != '.' && $f != '..' && $f != 'CVS' && !preg_match("/[.]/i",$f)) {
				$themelist[] = $f;
			}
		}
		closedir($handle);

		sort($themelist);
		foreach ($themelist as $v) {
			if (!isset($sel_defaulttheme[$v])) $sel_defaulttheme[$v]='';
			echo "<option value=\"$v\" $sel_defaulttheme[$v]>$v</option>\n";
		}
		echo '<tr><td>'._SELLANGUAGE.':</td><td><select name="xlanguage" size="1">';
		$lang = languagelist(); // see below
		foreach ($lang as $k=>$v) {
			echo '<option value="'.$k.'"';
			if (isset($sel_lang[$k])) {
				echo ' selected';
			}
			echo '>';
			echo "[$k] ";
			echo "$v";
			echo '</option>' . "\n";
		}
		echo '</select>'
		.'</td></tr>';

		echo '</select>'
		 .'</td></tr>'
//		 .'<tr><td>'
//		._THEMECHANGE.'</td><td>'
//     ."<input type=\"radio\" name=\"xtheme_change\" value=\"1\" $sel_themechange[1]>"._SYES.' &nbsp;'
//        ."<input type=\"radio\" name=\"xtheme_change\" value=\"0\" $sel_themechange[0]>"._SNO
//        .'</td></tr>'.
		.'<tr><td>'._ADMINGRAPHIC.'</td><td>'
        ."<input type=\"radio\" name=\"xadmingraphic\" value=\"1\" $sel_admingraphic[1]>"._SYES.' &nbsp;'
        ."<input type=\"radio\" name=\"xadmingraphic\" value=\"0\" $sel_admingraphic[0]>"._SNO
        ."</td></tr>"
		."<tr><td>"._HTMLEDITOR.":</td><td>"
		."<input type=\"radio\" name=\"xhtmleditor\" value=\"1\" $sel_htmleditor[1]>"._SYES.' &nbsp;'
        ."<input type=\"radio\" name=\"xhtmleditor\" value=\"0\" $sel_htmleditor[0]>"._SNO
        ."</td></tr>";

		echo '<tr><td>'._PAGESIZE.":</td><td><input type=\"text\" name=\"xpagesize\" value=\"".lnConfigGetVar('pagesize')."\" size=\"4\"></td></tr>";
//		echo '<tr><td>'._PAGELOGSIZE.":</td><td><input type=\"text\" name=\"xpagelog\" value=\"".lnConfigGetVar('pagelog')."\" size=\"4\"></td></tr>";

		echo '</table><BR><BR>';

	echo  '<LI TYPE="circle"><B>'._SECOPT.'</B><BR><BR>'

	.'<table border="0"><tr><td>'
	._SECLEVEL.':</td><td>'
	.'<select name="xseclevel" size="1">'
	."<option value=\"High\" $sel_seclevel[High]>" . _SECHIGH ."</option>\n"
	."<option value=\"Medium\" $sel_seclevel[Medium]>" . _SECMEDIUM . "</option>\n"
	."<option value=\"Low\" $sel_seclevel[Low]>" . _SECLOW . "</option>\n"
	.'</select>'
	.'</td></tr><tr><td>'
	._SECMEDLENGTH.":</td><td><input type=\"text\" name=\"xsecmeddays\" value=\"".lnConfigGetVar('secmeddays')."\" size=\"4\"> " .  _DAYS
	.'</td></tr>'
//	.'<tr><td class="pn-normal">'
//	._SECINACTIVELENGTH.":</td><td><input type=\"text\" name=\"xsecinactivemins\" value=\"".lnConfigGetVar('secinactivemins')."\" size=\"4\"> " .  _MINUTES
//	."</td></tr>"
	."</table>\n";
	
	echo '<BR><BR>';
	
	echo '<LI TYPE="circle"><B>LDAP Options</B><BR>';
	echo '<table border = "0"><tr>';
	echo '<td>สถานะ</td><td><select name="ldapstatus" size="1">';
	if($config['isUse'] == 1)
		$tmp = '<option value="1" selected="true">ใช้</option>' . "\n";
	else
		$tmp = '<option value="1">ใช้</option>' . "\n";
	echo $tmp;
	if($config['isUse'] == 0)
		$tmp = '<option value="0" selected="true">ไม่ใช้</option>' . "\n";
	else
		$tmp = '<option value="0">ไม่ใช้</option>' . "\n";
	echo $tmp;
	echo '</select></td></tr>' . "\n";
	echo '<tr><td>IP LDAP Server</td>';
	echo '<td><input class=input type=\"text\"  NAME="ldapip" size=30 maxlength=15 value="'.$config['ldapserver'].'"></td></tr>' . "\n";
	
	echo '<tr><td>sitename</td>';
	echo '<td><input class=input type=\"text\"  NAME="ldapsitename" size=30 maxlength=50 value="'.$config['ldapsitename'].'"></td></tr>' . "\n";

	echo '<tr><td>sitesuffix</td>';
	echo '<td><input class=input type=\"text\"  NAME="ldapsitesuffix" size=30 maxlength=50 value="'.$config['sitesuffix'].'"></td></tr>' . "\n";
	
	echo '<tr><td>ou</td>';
	echo '<td><input class=input type=\"text\"  NAME="ldapou" size=30 maxlength=50 value="'.$config['ou'].'"></td></tr>' . "\n";
	echo '</table>';

	echo '<BR><BR>';
	
    echo '<LI TYPE="circle"><B>'._FOOTERMSG.'</B><BR>'
        .'<BR><CENTER><textarea class=text name="xfoot" cols="80" rows="5" wrap="soft">'.htmlspecialchars(lnConfigGetVar('foot')).'</textarea></CENTER>';
	
	// Finish
	    echo '<input type="hidden" name="op" value="save">'
        .'<input type="hidden" name="file" value="admin">'
		.'<input type="hidden" name="mod" value="Settings">'
        .'<P><center><input class="button_org" type="submit" value="'._SAVECHANGES.'"  style="text-align:center"></center>'
        .'</form>';
	     
CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */

?>