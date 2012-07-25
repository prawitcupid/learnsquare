<?php
/**
* User Loging & Logout & show profile
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

$vars= array_merge($_GET,$_POST);

switch ($op) {
	case "login":		$ret = userLogin(); break;
	case "logout":	lnUserLogOut(); break;
	case "profile":	showuserProfile($vars);exit;
	
}

// redirect userlogin function to core module
function userLogin() {
    list($nickname,
        $password,
        $url,
        $rememberme) = lnVarCleanFromInput('nickname',
        'password',
        'url',
        'rememberme',
		'bar');


	if (!isset($rememberme)) {
        $rememberme = '';
    }

	if (!lnUserLogIn($nickname, $password, $rememberme)) {
		$error = _LOGINERROR;
		return $error;
	}

}
function showUserProfile($vars) {
	// Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];
	$datatable = $lntable['user_data'];
	$datacolumn = &$lntable['user_data_column'];
	$propertiestable = $lntable['user_property'];
	$propcolumn = &$lntable['user_property_column'];

/* - - - - - - - - - - - */
	include 'header.php';
	OpenTable();

	//lnBlockTitle($mod,'user_profile');
	echo '<p class="header"><b>'._USER_TITLE.'</b></p>';
//	echo '<BR>'._PERSONALINFO;

	?>
	<script language="JavaScript">
	 function showimage()
	   {
		  if (!document.images)
			 return

		  //Path 'images/avatar/' + ---Narasak Tai------ 24/10/2007 
		  document.images.avatar.src=  document.Register.user_avatar.options[document.Register.user_avatar.selectedIndex].value
	   }
	</script>
	<?


	$query = "SELECT $propcolumn[prop_id] as id, $propcolumn[prop_label] as label, $propcolumn[prop_dtype] as dtype,
							  $propcolumn[prop_length] as length, $propcolumn[prop_weight] as weight, $propcolumn[prop_validation] as validation
			  FROM $propertiestable WHERE $propcolumn[prop_weight] <> '0' ORDER BY $propcolumn[prop_weight]";

	$result = $dbconn->Execute($query);

	$userinfo=lnUserGetVars($uid);

	$shows = explode(',',$userinfo['show']);

	echo '<FORM METHOD=POST NAME="Register" ACTION="index.php">';
	echo '<INPUT TYPE="hidden" NAME="mod" VALUE="User">';
	echo '<INPUT TYPE="hidden" NAME="file" VALUE="profile">';
	echo '<INPUT TYPE="hidden" NAME="op" VALUE="edituser">';

	echo "<CENTER><P>&nbsp;";
	echo "<TABLE WIDTH=400 CELLPADDING=0 CELLSPACING=0 BORDER=0 BGCOLOR=#000000>";
	echo "<TR><TD  ALIGN=LEFT VALIGN=TOP BACKGROUND=themes/Nectec/images/bg_icon_g.gif><IMG SRC=modules/Forums/images/icon_profile1.gif  BORDER=0 >";
	echo "</TD></TR></TABLE>";
	echo "<TABLE WIDTH=400 CELLPADDING=5 CELLSPACING=0 BORDER=0 BGCOLOR=#999999>";
	echo "<TR HEIGHT=200><TD WIDTH=70 ALIGN=CENTER VALIGN=TOP BGCOLOR=#ADD556><BR>";
					$avatar=userVarsGetID(4,$uid);
					//Path images/avatar/
					if (empty($avatar)) {
						$avatar = "images/avatar/blank.gif";
					}
					//Path images/avatar/
					echo '&nbsp;&nbsp;<img src="'.$avatar.'" name="avatar"  alt="" align="top" border=0>';
					
	echo "<BR><B>".$userinfo['uname']."</B></TD>";
	echo "<TD BGCOLOR=#CFED8A VALIGN=TOP>";

	echo "<TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0>";
	while (!$result->EOF) {
	   $uservars = $result->GetRowAssoc(false);
	   	$result->MoveNext();
	   if ($uservars['weight']) {
		   $eval_cmd = "\$prop_label_text=$uservars[label];";
			@eval($eval_cmd); 
			if (!in_array($uservars['id'],$shows)) {
				continue;
			}
			echo "<TR BGCOLOR=#CFED8A>";
			switch ($uservars['label']) {
				case "_UNO" : 
						echo "<TD>".$prop_label_text ."</TD><TD>";
						echo $userinfo['uno'];
					break;

				case "_EMAIL" : 
						echo "<TD>".$prop_label_text ."</TD><TD>";
						echo '<A HREF="mailto:'.$userinfo['email'].'">'.$userinfo['email'].'</A>'; 
						break;
				
				case "_NAME" : 
					echo "<TD>".$prop_label_text ."</TD><TD>";
					echo $userinfo['name'];
					break;
				
				case "_PASSWORD" : 
					break;
					
				case "_PHONE" : 
					echo "<TD>".$prop_label_text ."</TD><TD>";
					echo $userinfo['phone'];
					break;

				default:
					echo "<TD>".$prop_label_text ."</TD><TD>";
					echo userVarsGet($uservars['id']);
			}

	   }
	   echo "</TD>";
	   echo "</TR>";
	}

	echo '</TABLE>';
	echo '</TD></TR></TABLE>';
	echo "<TABLE WIDTH=400 CELLPADDING=0 CELLSPACING=0 BORDER=0 BGCOLOR=#000000>";
	echo "<TR><TD  ALIGN=LEFT VALIGN=TOP BACKGROUND=themes/Nectec/images/bg_icon_g.gif><A HREF=index.php?mod=Private_Messages&op=post&to=".$uid."><IMG SRC=themes/Nectec/images/User/tha/send.gif BORDER=0 ALT=send></A>";
	echo "</TD></TR></TABLE>";
	//echo '<P><B><A HREF="index.php?mod=Private_Messages&op=post"><IMG SRC="themes/Simple/images/User/tha/send.gif" BORDER="0" ALT="send"></A></B>';

	CloseTable();

include 'footer.php';

/* - - - - - - - - - - - */
}

global $index;
$index=1;

include 'header.php';

include 'footer.php';


?>
