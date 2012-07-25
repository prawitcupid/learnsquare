<?php
/**
*  Change user profile 
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'User::Profile', "::", ACCESS_COMMENT)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$userstable = $lntable['users'];
$userscolumn = &$lntable['users_column'];
$datatable = $lntable['user_data'];
$datacolumn = &$lntable['user_data_column'];
$propertiestable = $lntable['user_property'];
$propcolumn = &$lntable['user_property_column'];


if  ($op == "edituser")  {
	list($nickname,$uno,$name, $password1, $password2, $email, $phone,$news,$uservars,$showvars,$user_avatar) = lnVarCleanFromInput('nickname','uno','name','password1', 'password2','email','phone','news','uservars','showvars','user_avatar');

	// Check duplicate data
	if ($return = lnUserCheck('',$nickname,$uno,$email)) { 
		switch ($return) {
			case _UNOTAKEN: $err_uno = "<font color=#FF0000><B>'$uno' $return</B></font>"; break;
			case _NICKTAKEN: $err_nickname = "<font color=#FF0000><B>'$nickname' $return</B></font>"; break;
			case _EMAILTAKEN : $err_email = "<font color=#FF0000><B>'$email' $return</B></font>"; break;
		}
	}
	else {
		$userinfo=lnUserGetVars(lnSessionGetVar('uid'));
		$show = join(',',$showvars);

	// build query for user table
		// update normal properties
		$query = "UPDATE $userstable SET $userscolumn[name] = '".lnVarPrepForStore($name)."'  ,$userscolumn[phone] = '".lnVarPrepForStore($phone)."' ,$userscolumn[news] = '".lnVarPrepForStore($news)."', $userscolumn[show] = '".lnVarPrepForStore($show)."' WHERE $userscolumn[uid] = '". $userinfo['uid']."'";

		$result = $dbconn->Execute($query);
		if ($dbconn->ErrorNo() <> 0) {
			echo "<br>". $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg() . "<br>";
			return;
		} 

		// update CORE properties - maybe unique properties
		$query = "UPDATE $userstable SET $userscolumn[uid] = '".lnVarPrepForStore($userinfo['uid'])."'";
		if (!empty($uno)) {
			$query .= ", $userscolumn[uno] = '".lnVarPrepForStore($uno)."'";
		}
		if (!empty($nickname)) {
			$query .= ", $userscolumn[uname] = '".lnVarPrepForStore($nickname)."'";
		}
		if (!empty($email)) {
			$query .= ", $userscolumn[email] = '".lnVarPrepForStore($email)."'";
		}
		
		$query .= " WHERE $userscolumn[uid] = '". $userinfo['uid']."'";

		$result = $dbconn->Execute($query);
		if ($dbconn->ErrorNo() <> 0) {
			echo "<br>". $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg() . "<br>";
			return;
		} 

		// update new password
		if ($password1 && $password2 && ($password1 == $password2)) {
			$password = md5($password1);
			$query = "UPDATE $userstable SET $userscolumn[pass] = '".lnVarPrepForStore($password)."' WHERE $userscolumn[uid] = ". $userinfo['uid'];
		}

		$result = $dbconn->Execute($query);
		if ($dbconn->ErrorNo() <> 0) {
			echo "$query<br>". $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg() . "<br>";
			return;
		} 

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
		$datatable = $lntable['user_data'];
		$datacolumn = &$lntable['user_data_column'];

		// build Query  for dynamic data
		for ($i=0; $i < sizeof($uservars); $i++) {
			$key = key($uservars);
			$value = current($uservars);

			$existingdata = $dbconn->Execute("SELECT $datacolumn[uda_id] FROM $datatable WHERE $datacolumn[uda_propid]='" . lnVarPrepForStore($key) . "' and $datacolumn[uda_uid]='" . lnVarPrepForStore($userinfo['uid']) . "'");
			if (!$existingdata->EOF) {
				$sql[$i] =  "UPDATE $datatable  SET $datacolumn[uda_propid]='".lnVarPrepForStore($key)."', $datacolumn[uda_uid]='".lnVarPrepForStore($userinfo['uid'])."',$datacolumn[uda_value]='".lnVarPrepForStore($value)."'WHERE $datacolumn[uda_propid]='" . lnVarPrepForStore($key) . "' and $datacolumn[uda_uid]='" . lnVarPrepForStore($userinfo['uid']) . "'";
			}
			else {
				
				$sql[$i] =  "INSERT INTO $datatable ($datacolumn[uda_propid],$datacolumn[uda_uid],$datacolumn[uda_value] ) VALUES ('".lnVarPrepForStore($key)."','".lnVarPrepForStore($userinfo['uid'])."','".lnVarPrepForStore($value)."') ";
			}

			$existingdata->Close();

			next($uservars);
		} 
		
		// for Avatars
			$existingdata = $dbconn->Execute("SELECT $datacolumn[uda_id] FROM $datatable WHERE $datacolumn[uda_propid]='" . lnVarPrepForStore(4) . "' and $datacolumn[uda_uid]='" . lnVarPrepForStore($userinfo['uid']) . "'");
			if (!$existingdata->EOF) {
				$sql[]="UPDATE $datatable  SET $datacolumn[uda_value]='".lnVarPrepForStore($user_avatar)."' WHERE $datacolumn[uda_propid]='" . lnVarPrepForStore(4) . "' and $datacolumn[uda_uid]='" . lnVarPrepForStore($userinfo['uid']) . "' ";
			}
			else {

				$sql[] =  "INSERT INTO $datatable ($datacolumn[uda_propid],$datacolumn[uda_uid],$datacolumn[uda_value] ) VALUES ('".lnVarPrepForStore(4)."','".lnVarPrepForStore($userinfo['uid'])."','".lnVarPrepForStore($user_avatar)."') ";
			}

		// do query
		for ($i=0; $i < sizeof($sql); $i++) {
			if ($sql[$i]) {
				$result = $dbconn->Execute($sql[$i]);
				if ($dbconn->ErrorNo() <> 0) {
					echo "$sql[$i]<br>". $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg() . "<br>";
					return;
				} 
			}
		}
		$message= _SAVECHANGE;
	}
}



/* - - - - - - - - - - - */
include 'header.php';

OpenTable();

//echo lnBlockTitle($mod);
echo '<p class="header"><b>'._USER_TITLE.'</b></p>';
echo '<BR>'._PERSONALINFO;

?>
<script language="JavaScript">
 function showimage()
   {
      if (!document.images)
         return
		//use path form $avatardir
	  document.images.avatar.src=
		  document.Register.user_avatar.options[document.Register.user_avatar.selectedIndex].value
   }
</script>
<?


$query = "SELECT $propcolumn[prop_id] as id, $propcolumn[prop_label] as label, $propcolumn[prop_dtype] as dtype,
						  $propcolumn[prop_length] as length, $propcolumn[prop_weight] as weight, $propcolumn[prop_validation] as validation
		  FROM $propertiestable WHERE $propcolumn[prop_weight] <> '0' ORDER BY $propcolumn[prop_weight]";

$result = $dbconn->Execute($query);

$userinfo=lnUserGetVars(lnSessionGetVar('uid'));

$shows = explode(',',$userinfo['show']);
for($i=0; $i<count($shows); $i++) {
	$sel_show[$shows[$i]]="checked";
}
echo '<FORM METHOD=POST NAME="Register" ACTION="index.php">';
echo '<INPUT TYPE="hidden" NAME="mod" VALUE="User">';
echo '<INPUT TYPE="hidden" NAME="file" VALUE="profile">';
echo '<INPUT TYPE="hidden" NAME="op" VALUE="edituser">';

echo "<CENTER><TABLE CELLPADDING=3 CELLSPACING=0 BORDER=0 WIDTH=500>";
while (!$result->EOF) {
   $uservars = $result->GetRowAssoc(false);
   if ($uservars['weight']) {
	   $eval_cmd = "\$prop_label_text=$uservars[label];";
		@eval($eval_cmd); 
		echo "<TR>";
		switch ($uservars['label']) {
			// May be CORE properties
			case "_NICKNAME":
				echo "<TD>&nbsp;</TD><TD>"._NICKNAME."</TD><TD>";
				if (@$uservars[dtype] == _CORE) {
					echo '<B>'.$userinfo['uname'].'</B>';
				}
				else {
					echo '<INPUT TYPE="text" NAME="nickname" VALUE="'.$userinfo['uname'].'">';
					echo $err_nickname;
				}
				break;

			case "_UNO" : 
				echo '<TD><INPUT TYPE="hidden" NAME="showvars['.@$uservars['id'].']" VALUE="'.@$uservars['id'].'" '.@$sel_show[$uservars['id']].'></TD>';
				echo "<TD>".$prop_label_text ."</TD><TD>";
				if (@$uservars[dtype] == _CORE) {
					echo $userinfo['uno'];
				}
				else {
					echo '<INPUT TYPE="text" NAME="uno" VALUE="'.$userinfo['uno'].'">';
					echo $err_uno;
				}
				 break;

			case "_PASSWORD" : 
				echo '<TD>&nbsp;</TD>';
				echo "<TD>".$prop_label_text ."</TD><TD>";
				echo '<INPUT TYPE="password" NAME="password1" VALUE="" size="15">(*)&nbsp;';
				echo '&nbsp;ยืนยันรหัสผ่าน : <INPUT TYPE="password" NAME="password2" VALUE="" size="15">';break;

			case "_EMAIL" : 
					echo '<TD><INPUT TYPE="checkbox" NAME="showvars['.$uservars['id'].']" VALUE="'.$uservars['id'].'" '.$sel_show[$uservars['id']].'></TD>';
					echo "<TD>".$prop_label_text ."</TD><TD>";
					echo '<INPUT TYPE="text" NAME="email" VALUE="'.$userinfo['email'].'">'; 
					echo $err_email;
					break;
			
			case "_NAME" : 
				echo '<TD><INPUT TYPE="hidden" NAME="showvars['.@$uservars['id'].']" VALUE="'.@$uservars['id'].'" '.@$sel_show[$uservars['id']].'></TD>';
				echo "<TD>".$prop_label_text ."</TD><TD>";
				echo '<INPUT TYPE="text" NAME="name" VALUE="'.$userinfo['name'].'">'; break;
			
			case "_AVATAR" : 
				echo '<TD>&nbsp;</TD>';
				$old_avatar=userVarsGet($uservars['id']);
				echo "<TD valign=top>".$prop_label_text ."</TD><TD>";
				echo '<SELECT NAME="user_avatar" onChange="showimage()">';
				$avatardir = opendir('images/avatar/');
				while ($avatar = readdir($avatardir)) {
					if (preg_match("/.gif|.jpg/i", $avatar)) {
						echo '<OPTION VALUE="'.'images/avatar/'.$avatar.'"';

						if ($old_avatar) {
							if ($avatar == $old_avatar) {
								echo "selected";
							}
							$show_avatar=$old_avatar;
						}
						else {
							if ( $avatar=='blank.gif') {
								echo " selected";
							}
							$show_avatar='blank.gif';
                        }
						echo '>'.$avatar.'</OPTION>';
					}
				}
				
				//use uid to get Path of image

				$img = $userinfo['uid'];
				$name = $userinfo['name'];

				//get image from Path for user

				$avatardir = "images/avatar/userimage/".$img.".jpg";
				if (File_exists($avatardir)) {
					echo '<OPTION selected="selected" VALUE="'.$avatardir.'"';
					echo '>User Logo</OPTION>';
				}
				echo '</SELECT>'; 

				//check image is exist
				if($show_avatar){
					echo '<br><br><img src="images/avatar/blank.gif" name="avatar"  alt="" align="top"><br><br>';
				}else{
					//path image  images/avatar/
					echo '<br><br><img src="'.$show_avatar.'" name="avatar"  alt="" align="top"><br><br>';
				}

				//Link to Upload File
				echo '<b><a href="index.php?mod=User&file=userupload">'._UPLOAD_PICTURE.'</a></b>';

				
				break;
			
			
			case "_PHONE" : 
				echo '<TD><INPUT TYPE="checkbox" NAME="showvars['.$uservars['id'].']" VALUE="'.$uservars['id'].'" '.$sel_show[$uservars['id']].'></TD>';
				echo "<TD>".$prop_label_text ."</TD><TD>";
				echo '<INPUT TYPE="text" NAME="phone" VALUE="'.$userinfo['phone'].'">';
				break;

		case "_NEWS" : 
//				echo '<TD>&nbsp;</TD>';
	//			echo "<TD>&nbsp;</TD><TD>";
		//		echo '<INPUT TYPE="checkbox" NAME="news"  VALUE="1" checked ';
		//if ($userinfo['news']) {
		//	echo "checked";
			//}
		//	echo '>'.$prop_label_text ;
			    break;
			
			case "_EXTRAINFO" : 
				echo '<TD valign=top><INPUT TYPE="hidden" NAME="showvars['.@$uservars['id'].']" VALUE="'.@$uservars['id'].'" '.@$sel_show[$uservars['id']].'></TD>';
				echo "<TD valign=top>".$prop_label_text ."</TD><TD>";
				echo '<TEXTAREA NAME="uservars['.$uservars['id'].']" ROWS="3" COLS="30" style=width:90%>'.userVarsGet($uservars['id']).'</TEXTAREA>';
				break;


			default:
				echo '<TD><INPUT TYPE="hidden" NAME="showvars['.@$uservars['id'].']" VALUE="'.@$uservars['id'].'" '.@$sel_show[$uservars['id']].'></TD>';
				echo "<TD width=100>".$prop_label_text ."</TD><TD>";
				echo '<INPUT TYPE="text" NAME="uservars['.$uservars['id'].']" VALUE="'.userVarsGet($uservars['id']).'">';
		}

		if ($uservars['dtype'] >= 0) {
			echo " &nbsp;";
		}
		else {
			echo "&nbsp;"._REQUIRE;
		}
		echo "<BR>";
   }
   echo "</TD>";
   echo "</TR>";
   $result->MoveNext();
}

echo '<TR><TD>&nbsp;</TD><TD>&nbsp;</TD><TD><BR><INPUT class="button_org" TYPE="submit" VALUE="'._SAVECHANGES.'"><TD></TR>';
echo '</FORM></TABLE>';

CloseTable();

include 'footer.php';

/* - - - - - - - - - - - */

?>