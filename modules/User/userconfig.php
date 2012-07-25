<?php
/**
*  Config user module
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "$file::", ACCESS_ADMIN)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to read  ".$mod." module!</h1></CENTER>";
		return false;
}

/* options */
if ($op=="save") {
	if (!lnSecAuthAction(0, 'User::', "$file::", ACCESS_EDIT)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
		return false;
	}
	$vars= array_merge($_GET,$_POST);
	 foreach($vars as $name => $value) {
        if (substr($name, 0, 1) == 'x') {
            $var = lnVarCleanFromInput($name);
            lnConfigSetVar(substr($name, 1), $var);
        }
    }
	lnUpdateUserEvent("Change user config");
	lnRedirect('index.php?mod=User&file=admin');
}

/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_USERADMIN,_USERREGCONFIG);
$links=array('index.php?mod=Admin','index.php?mod=User&file=admin','index.php?mod=User&amp;file=userconfig');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

 echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=User&amp;file=userconfig"><B>'._USERREGCONFIG.'</B></A><BR>&nbsp;';

	$sel_reg_allowreg['0'] = '';
	$sel_reg_allowreg['1'] = '';
	$sel_reg_allowreg[lnConfigGetVar('reg_allowreg')] = ' checked';
	$sel_reg_uniid['0'] = '';
	$sel_reg_uniid['1'] = '';
	$sel_reg_uniid[lnConfigGetVar('reg_uniid')] = ' checked';
	$sel_reg_uniuname['0'] = '';
	$sel_reg_uniuname['1'] = '';
	$sel_reg_uniuname[lnConfigGetVar('reg_uniuname')] = ' checked';
	$sel_reg_uniemail['0'] = '';
	$sel_reg_uniemail['1'] = '';
	$sel_reg_uniemail[lnConfigGetVar('reg_uniemail')] = ' checked';
	$sel_reg_allowfile['0'] = '';
	$sel_reg_allowfile['1'] = '';
	$sel_reg_allowfile[lnConfigGetVar('reg_allowfile')] = ' checked';



	 echo '<table border="0" cellpadding="3" cellspacing="0">';
	 echo  '<form action="index.php" name="settings" method="post">';
	 echo '<tr><td>'
	 ._ALLOWREG.':</td><td>'
	 .'<input type="radio" name="xreg_allowreg" value="1" '.$sel_reg_allowreg['1'].'>'._SYES.' &nbsp;'
      .'<input type="radio" name="xreg_allowreg" value="0" '.$sel_reg_allowreg['0'].'>'._SNO.'&nbsp;'
      .'</td></tr>';
	  
	  if (lnUserReqProp("_UNO")) {
		  echo '<tr><td>'
		 ._UNIID.':</td><td>'
		 .'<input type="radio" name="xreg_uniid" value="1" '.$sel_reg_uniid['1'].'>'._SYES.' &nbsp;'
		  .'<input type="radio" name="xreg_uniid" value="0" '.$sel_reg_uniid['0'].'>'._SNO.'&nbsp;'
		  .'</td></tr>';
	  }
	  
	  if (lnUserReqProp("_NICKNAME")) {
		  echo '<tr><td>'    
		 ._UNINICKNAME.':</td><td>'
		 .'<input type="radio" name="xreg_uniuname" value="1" '.$sel_reg_uniuname['1'].'>'._SYES.' &nbsp;'
		  .'<input type="radio" name="xreg_uniuname" value="0" '.$sel_reg_uniuname['0'].'>'._SNO.'&nbsp;'
		  .'</td></tr>';
	  }	  

  	  if (lnUserReqProp("_EMAIL")) {
			echo '<tr><td>'    
			 ._UNIEMAIL.':</td><td>'
			 .'<input type="radio" name="xreg_uniemail" value="1" '.$sel_reg_uniemail['1'].'>'._SYES.' &nbsp;'
			  .'<input type="radio" name="xreg_uniemail" value="0" '.$sel_reg_uniemail['0'].'>'._SNO.'&nbsp;'
			  .'</td></tr>';
	  }

	  if (lnUserReqProp("_UNO")) {
		  echo '<tr><td>'
		  ._LENID.':</td><td>'
		 .'<input type="text" name="xreg_id_len" size="5" value="'.lnConfigGetVar('reg_id_len').'">'
		  .'</td></tr>';
	  }	  
	  
	  echo '<tr><td>'
	   ._LENMINNICK.':</td><td>'
	 .'<input type="text" name="xreg_min_nickname" size="5" value="'.lnConfigGetVar('reg_min_nickname').'">'
      .'</td></tr><tr><td>'
	   ._LENMAXNICK.':</td><td>'
	 .'<input type="text" name="xreg_max_nickname" size="5" value="'.lnConfigGetVar('reg_max_nickname').'">'
      .'</td></tr><tr><td>'
	   ._LENMINPASS.':</td><td>'
	 .'<input type="text" name="xreg_min_password" size="5" value="'.lnConfigGetVar('reg_min_password').'">'
      .'</td></tr><tr><td>'
	   ._LENMAXPASS.':</td><td>'
	 .'<input type="text" name="xreg_max_password" size="5" value="'.lnConfigGetVar('reg_max_password').'">'
      .'</td></tr>'
	.'<tr><td><tr><td>'

	   ._DEFAULTGROUP.':</td><td>';
   		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
		$groupstable = $lntable['groups'];
		$groupscolumn = &$lntable['groups_column'];
		echo '<SELECT NAME="xdefault_group">';
		$result = $dbconn->Execute("SELECT  $groupscolumn[gid], $groupscolumn[name], $groupscolumn[description],$groupscolumn[type]  FROM $groupstable");
		$default_group = lnConfigGetVar('default_group');
	   while( list($gid, $name,$description,$type) = $result->fields ) {
			$result->MoveNext();
			if ($type == $default_group) { 
				echo '<OPTION VALUE="'.$type.'" selected>'.$name.'</OPTION>';
			}
			else {
				echo '<OPTION VALUE="'.$type.'">'.$name.'</OPTION>';
			}
		}
	  echo '</SELECT>'
      .'</td></tr><tr><td>'
	 ._ALLOWFILEUSER.':</td><td>'
	 .'<input type="radio" name="xreg_allowfile" value="1" '.$sel_reg_allowfile['1'].'>'._SYES.' &nbsp;'
      .'<input type="radio" name="xreg_allowfile" value="0" '.$sel_reg_allowfile['0'].'>'._SNO.'&nbsp;'
      .'</td></tr>'

	.'</table>';	
	// Finish
	    echo '<input type="hidden" name="op" value="save">'
        .'<input type="hidden" name="file" value="userconfig">'
		.'<input type="hidden" name="mod" value="User">'
        .'<BR><BR><center><input class="button_org" type="submit" value="'._SAVECHANGES.'"  style="text-align:center"></center>'
        .'</form>';


CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */
?>