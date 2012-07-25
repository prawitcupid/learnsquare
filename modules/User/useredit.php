<?php
/**
* Edit/Find/Delete User
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Admin::', "$file::", ACCESS_ADMIN)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
		return false;
}

include 'header.php';

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$userstable = $lntable['users'];
$userscolumn = &$lntable['users_column'];
$datatable = $lntable['user_data'];
$datacolumn = &$lntable['user_data_column'];
$propertiestable = $lntable['user_property'];
$propcolumn = &$lntable['user_property_column'];
$groupstable = $lntable['groups'];
$groupscolumn = &$lntable['groups_column'];
$group_membershiptable = $lntable['group_membership'];
$group_membershipcolumn = &$lntable['group_membership_column'];


/* options */
// Find User
if ($op=="finduser") {
		/** Navigator **/
		$menus= array(_ADMINMENU,_USERADMIN,_USEREDIT,_USERSEARCH);
		$links=array('index.php?mod=Admin','index.php?mod=User&file=admin','index.php?mod=User&file=useredit','#');
		lnBlockNav($menus,$links);
	  /** Navigator **/

	OpenTable();
	
	 echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <B>'._USERSEARCH.'</B><BR>&nbsp;';
	 

	 list($word,$field) = lnVarCleanFromInput('word','field');
	
	// Search
	$eval_cmd = "\$usersfindcolumn=\$userscolumn[$field];";
	@eval($eval_cmd); 
	$result = $dbconn->Execute("SELECT $userscolumn[uid],$userscolumn[name],$userscolumn[uname],$userscolumn[email],
							$userscolumn[regdate],$userscolumn[phone],$userscolumn[uno],$userscolumn[news] ,$userscolumn[active] 
							 FROM $userstable 
 							 WHERE $usersfindcolumn LIKE '".lnVarPrepForStore($word)."%' ");
		echo "<BR>"._SEARCH." &nbsp;'<B>$word</B>'<BR>";
		if ( $result->PO_RecordCount() == 0) {
			 echo '<BR><BR>'._SEARCH. '&nbsp; <B>'. $word. '</B>&nbsp;'._NOTFOUND;
		}
		else {
			echo '<BR><table width="100%" cellpadding=3 cellspacing=1 bgcolor=#d3d3d3>'
			.'<tr align=center bgcolor=#808080><td class="head">No.</td><td class="head">'._NICKNAME.'</td><td class="head">'._NAME.'</td><td class="head">'._UNO.'</td><td class="head">&nbsp;</td></tr>';
			 for ($i=1; list($uid,$name,$uname,$email,$regdate,$phone,$uno,$news,$active) = $result->fields; $i++) {
					 $result->MoveNext();
					 echo "<tr bgcolor=#FFFFFF><td width=25>$i</td><td width=100>$uname</td><td>$name</td><td width=80>$uno</td><td align=center width=80>";
					 echo "<A HREF=\"index.php?mod=User&amp;file=useredit&amp;op=edituser&amp;uid=$uid\"><IMG SRC=\"images/global/edit.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=0 ALT=\""._EDIT."\"></A> ";
					 echo " &nbsp;";
					 echo "<A HREF=\"javascript:if(confirm('Delete $uname?')) window.open('index.php?mod=User&amp;file=useredit&amp;op=deleteuser&amp;uid=$uid&amp;letter=$letter','_self')\"><IMG SRC=\"images/global/delete.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=0 ALT=\""._DELETE."\"></A></td></tr>";
			}
			echo "</table>";
		}
	
	CloseTable();

	include 'footer.php';

	return;
}

// Delete User
else if ($op == "deleteuser") {
		if (!lnSecAuthAction(0, 'User::', "$file::", ACCESS_DELETE)) {
			echo "<CENTER><h1>"._NOAUTHORIZED." to delete ".$mod." module!</h1></CENTER>";
			return false;
		}
		// delete from user table
		$result = $dbconn->Execute("DELETE FROM $userstable WHERE $userscolumn[uid]='".lnVarPrepForStore($uid)."'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Delete User from user table" . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate User from user table" . $dbconn->ErrorMsg() . "<br>");
            return;
        } 

		// delete from data table
		$result = $dbconn->Execute("DELETE FROM $datatable WHERE $datacolumn[uda_uid]='".lnVarPrepForStore($uid)."'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Delete User from data table " . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate User from data table" . $dbconn->ErrorMsg() . "<br>");
            return;
        } 

		// delete from group membership table
		$result = $dbconn->Execute("DELETE FROM $group_membershiptable WHERE $group_membershipcolumn[uid]='".lnVarPrepForStore($uid)."'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Delete User from group membership table " . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate User from group membership table" . $dbconn->ErrorMsg() . "<br>");
            return;
        } 

		//todo : add module to clear date in table for optimization
		// delete from course_ta table
		$course_tatable=$lntable['course_ta'];
		$course_tacolumn = &$lntable['course_ta_column'];
		$result = $dbconn->Execute("DELETE FROM $course_tatable WHERE $course_tacolumn[uid]='".lnVarPrepForStore($uid)."'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Delete User from course ta table " . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate User from course ta table" . $dbconn->ErrorMsg() . "<br>");
            return;
        }
		
		// delete from userlog table
		$user_logtable=$lntable['user_log'];
		$user_logcolumn = &$lntable['user_log_column'];
		$result = $dbconn->Execute("DELETE FROM $user_logtable WHERE $user_logcolumn[uid]='".lnVarPrepForStore($uid)."'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Delete User from user_log table " . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate User from user_log table" . $dbconn->ErrorMsg() . "<br>");
            return;
        }

		// userperms
		$user_permstable=$lntable['user_perms'];
		$user_permscolumn = &$lntable['user_perms_column'];
		$result = $dbconn->Execute("DELETE FROM $user_permstable WHERE $user_permscolumn[uid]='".lnVarPrepForStore($uid)."'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Delete User from user_perms table " . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate User from user_perms table" . $dbconn->ErrorMsg() . "<br>");
            return;
        }

		//session info
		$session_infotable=$lntable['session_info'];
		$session_infocolumn = &$lntable['session_info_column'];
		$result = $dbconn->Execute("DELETE FROM $session_infotable WHERE $session_infocolumn[uid]='".lnVarPrepForStore($uid)."'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Delete User from sessions_info table " . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate User from session_info table" . $dbconn->ErrorMsg() . "<br>");
            return;
        }
		
        //course_tracking
		
		$course_trackingtable=$lntable['course_tracking'];
		$course_trackingcolumn = &$lntable['course_tracking_column'];
		$list_eid = lnGetEID($uid); //print_r($list_eid);
		for($i=0;$i<count($list_eid);$i++){
			$result = $dbconn->Execute("DELETE FROM $course_trackingtable WHERE $course_trackingcolumn[eid]='".lnVarPrepForStore($list_eid[$i])."'");
        	if ($dbconn->ErrorNo() <> 0) {
            	echo $dbconn->ErrorNo() . "Delete User from scores table " . $dbconn->ErrorMsg() . "<br>";
            	error_log ($dbconn->ErrorNo() . "Deactivate User from scores table" . $dbconn->ErrorMsg() . "<br>");
            	return;
        	}
		}
        
		//course_enrolls
		$course_enrollstable=$lntable['course_enrolls'];
		$course_enrollscolumn = &$lntable['course_enrolls_column'];
		$result = $dbconn->Execute("DELETE FROM $course_enrollstable WHERE $course_enrollscolumn[uid]='".lnVarPrepForStore($uid)."'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Delete User from course_enrolls table " . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate User from course_enroll table" . $dbconn->ErrorMsg() . "<br>");
            return;
        }

		

		// score
		// calendar
		// note
		// privmsgs
		// forum
		
}

// Edit User
if  ($op == "saveuser")  {
	if (!lnSecAuthAction(0, 'User::', "$file::", ACCESS_EDIT)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
		return false;
	}
	list($uid,$nickname,$uno,$name, $password1, $password2, $email, $phone,$news,$uservars,$showvars,$user_avatar,$active,$newLeft) = lnVarCleanFromInput('uid','nickname','uno','name','password1', 'password2','email','phone','news','uservars','showvars','user_avatar','active','newLeft');

		
	// Check duplicate data
	if ($return = lnUserCheck($uid,$nickname,$uno,$email)) { 
		switch ($return) {
			case _UNOTAKEN: $err_uno = "<BR><font color=#FF0000><B>'$uno' $return</B></font>"; break;
			case _NICKTAKEN: $err_nickname = "<BR><font color=#FF0000><B>'$nickname' $return</B></font>"; break;
			case _EMAILTAKEN : $err_email = "<BR><font color=#FF0000><B>'$email' $return</B></font>"; break;
		}
	}
	else {
		$show = join(',',$showvars);
		
	// build query for user table
		// update normal properties
		$query = "UPDATE $userstable SET $userscolumn[name] = '".lnVarPrepForStore($name)."'  ,$userscolumn[phone] = '".lnVarPrepForStore($phone)."' ,$userscolumn[news] = '".lnVarPrepForStore($news)."', $userscolumn[show] = '".lnVarPrepForStore($show)."' WHERE $userscolumn[uid] = '". lnVarPrepForStore($uid)."'";

		$result = $dbconn->Execute($query);
		if ($dbconn->ErrorNo() <> 0) {
			echo "<br>". $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg() . "<br>";
			return;
		} 
	
		// update CORE properties - maybe unique properties
		$query = "UPDATE $userstable SET $userscolumn[uid] = '".lnVarPrepForStore($uid)."'";
		if (isset($uno)) {
			$query .= ", $userscolumn[uno] = '".lnVarPrepForStore($uno)."'";
		}
		if (isset($nickname)) {
			$query .= ", $userscolumn[uname] = '".lnVarPrepForStore($nickname)."'";
		}
		if (isset($email)) {
			$query .= ", $userscolumn[email] = '".lnVarPrepForStore($email)."'";
		}
		$query .= ", $userscolumn[active] = '".lnVarPrepForStore($active)."'";
		
		$query .= " WHERE $userscolumn[uid] = '". lnVarPrepForStore($uid)."'";
		
		
		$result = $dbconn->Execute($query);
		if ($dbconn->ErrorNo() <> 0) {
			echo "<br>". $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg() . "<br>";
			return;
		} 
			
		// update new password
		if ($password1 && $password2 && ($password1 == $password2)) {
			$password = md5($password1);
			$query = "UPDATE $userstable SET $userscolumn[pass] = '".lnVarPrepForStore($password)."' WHERE $userscolumn[uid] = '". lnVarPrepForStore($uid)."'";
		}

		$result = $dbconn->Execute($query);
		if ($dbconn->ErrorNo() <> 0) {
			echo "$query<br>". $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg() . "<br>";
			return;
		} 

	
		// build Query  for dynamic data
		for ($i=0; $i < sizeof($uservars); $i++) {
			$key = key($uservars);
			$value = current($uservars);

			$existingdata = $dbconn->Execute("SELECT $datacolumn[uda_id] FROM $datatable WHERE $datacolumn[uda_propid]='" . lnVarPrepForStore($key) . "' and $datacolumn[uda_uid]='" . lnVarPrepForStore($uid) . "'");
			if (!$existingdata->EOF) {
				$sql[$i] =  "UPDATE $datatable  SET $datacolumn[uda_value]='".lnVarPrepForStore($value)."' WHERE $datacolumn[uda_propid]='" . lnVarPrepForStore($key) . "' and $datacolumn[uda_uid]='" . lnVarPrepForStore($uid) . "'";
			}
			else {
				$id = lnUserDataNextID()+$i;
				$sql[$i] =  "INSERT INTO $datatable  VALUES ('$id','".lnVarPrepForStore($key)."','".lnVarPrepForStore($uid)."','".lnVarPrepForStore($value)."') ";
			}

			$existingdata->Close();

			next($uservars);
		} 
	
		// for Avatars
			$existingdata = $dbconn->Execute("SELECT $datacolumn[uda_id] FROM $datatable WHERE $datacolumn[uda_propid]='" . lnVarPrepForStore(4) . "' and $datacolumn[uda_uid]='" . lnVarPrepForStore($uid) . "'");
			if (!$existingdata->EOF) {
				$sql[]="UPDATE $datatable  SET $datacolumn[uda_value]='".lnVarPrepForStore($user_avatar)."' WHERE $datacolumn[uda_propid]='" . lnVarPrepForStore(4) . "' and $datacolumn[uda_uid]='" . lnVarPrepForStore($uid) . "' ";
			}
			else {
				// fix field at 4'th location of data table
				if (empty($id)) {
					$id = lnUserDataNextID();
				}
				else {
					$id++;
				}
				$sql[]="INSERT INTO $datatable  VALUES ('$id','".lnVarPrepForStore(4)."','". lnVarPrepForStore($uid) . "','".lnVarPrepForStore($user_avatar)."') ";
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

// update group membership
		//1. Delete membership
		$result = $dbconn->Execute("DELETE FROM $group_membershiptable WHERE $group_membershipcolumn[uid]='".lnVarPrepForStore($uid)."'");
        if ($dbconn->ErrorNo() <> 0) {
            echo $dbconn->ErrorNo() . "Delete User from group membership table " . $dbconn->ErrorMsg() . "<br>";
            error_log ($dbconn->ErrorNo() . "Deactivate User from group membership table" . $dbconn->ErrorMsg() . "<br>");
            return;
        }
		
		
		//2. Insert new membership
		$groupList = explode(",",$newLeft);
		for ($i=0; $i < count($groupList); $i++) {
			if (!empty($groupList[$i])) {
				$result = $dbconn->Execute("INSERT INTO $group_membershiptable VALUES ('".lnVarPrepForStore($groupList[$i])."','".lnVarPrepForStore($uid)."')");
			}
		}
	
		$message= _SAVECHANGE;
	}


}

/* - - - - - - - - - - - */
/// show edit user form
if ($op == "edituser" || isset($err_uno) ||  isset($err_nickname) || isset($err_email)) {
	if (!lnSecAuthAction(0, 'User::', "$file::", ACCESS_EDIT)) {
			echo "<CENTER><h1>"._NOAUTHORIZED." to edit ".$mod." module!</h1></CENTER>";
			return false;
	}	
	
/** Navigator **/
$menus= array(_ADMINMENU,_USERADMIN,_USEREDIT,_USERMODIFY);
$links=array('index.php?mod=Admin','index.php?mod=User&file=admin','index.php?mod=User&file=useredit','index.php?mod=User&file=useredit','#');
lnBlockNav($menus,$links);
/** Navigator **/

	OpenTable();

	echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <B>'._USERMODIFY.'</B><BR>&nbsp;';

	// Edit users
	$result = $dbconn->Execute("SELECT $userscolumn[uid],$userscolumn[name],$userscolumn[uname],$userscolumn[email],
							$userscolumn[regdate],$userscolumn[phone],$userscolumn[uno],$userscolumn[news] ,$userscolumn[active], $userscolumn[show] 
							 FROM $userstable 
									WHERE $userscolumn[uid]='".lnVarPrepForStore($uid)."'");
	if ($dbconn->ErrorNo() <> 0) {
		echo $dbconn->ErrorNo() . "Edit User: " . $dbconn->ErrorMsg() . "<br>";
		error_log ($dbconn->ErrorNo() . "Edit User: " . $dbconn->ErrorMsg() . "<br>");
		return;
	} 

	list($uid,$name,$uname,$email,$regdate,$phone,$uno,$news,$active,$show) = $result->fields;
	$shows = explode(',',$show);
	for($i=0; $i<count($shows); $i++) {
		$sel_show[$shows[$i]]="checked";
	}

	$query = "SELECT $propcolumn[prop_id] as id, $propcolumn[prop_label] as label, $propcolumn[prop_dtype] as dtype,
							  $propcolumn[prop_length] as length, $propcolumn[prop_weight] as weight, $propcolumn[prop_validation] as validation
			  FROM $propertiestable WHERE $propcolumn[prop_weight] <> '0' ORDER BY $propcolumn[prop_weight]";

	$result = $dbconn->Execute($query);

?>
<script language="JavaScript">
 function showimage()
   {
      if (!document.images)
         return

	  document.images.avatar.src= 'images/avatar/' + document.Register.user_avatar.options[document.Register.user_avatar.selectedIndex].value
   }
</script>
<?
	echo '<center><table cellpadding=3 cellspacing=0 width=500 border=0>';
	echo '<FORM METHOD=POST NAME="Register" ACTION="index.php">';
	echo '<INPUT TYPE="hidden" NAME="mod" VALUE="User">';
	echo '<INPUT TYPE="hidden" NAME="file" VALUE="useredit">';
	echo '<INPUT TYPE="hidden" NAME="op" VALUE="saveuser">';
	echo '<INPUT TYPE="hidden" NAME="uid" VALUE="'.$uid.'">';
	while (!$result->EOF) {
	   $uservars = $result->GetRowAssoc(false);
		$eval_cmd = "\$prop_label_text=$uservars[label];";
		@eval($eval_cmd); 
	   echo "<TR>";
	   switch ($uservars['label']) {
			// May be CORE properties
			case "_NICKNAME":
				echo '<TD>&nbsp;</TD>';
				echo "<TD>"._NICKNAME."</TD><TD>";
				echo '<INPUT TYPE="text" NAME="nickname" VALUE="'.$uname.'">';
				echo $err_nickname;
				break;

			case "_UNO" : 
				echo '<TD><INPUT TYPE="hidden" NAME="showvars['.$uservars['id'].']" VALUE="'.$uservars['id'].'" '.$sel_show[$uservars['id']].'></TD>';
				echo "<TD>".$prop_label_text ."</TD><TD>";
				echo '<INPUT TYPE="text" NAME="uno" VALUE="'.$uno.'">';
				echo $err_uno;
				 break;

			case "_PASSWORD" : 
				echo '<TD>&nbsp;</TD>';
				echo "<TD>".$prop_label_text ."</TD><TD>";
				echo '<INPUT TYPE="password" NAME="password1" VALUE="">&nbsp;';
				echo '&nbsp;ยืนยันรหัสผ่าน : <INPUT TYPE="password" NAME="password2" VALUE="">';break;

			case "_EMAIL" : 
				echo '<TD><INPUT TYPE="hidden" NAME="showvars['.$uservars['id'].']" VALUE="'.$uservars['id'].'" '.$sel_show[$uservars['id']].'></TD>';
				echo "<TD>".$prop_label_text ."</TD><TD>";
				echo '<INPUT TYPE="text" NAME="email" VALUE="'.$email.'">'; 
				echo $err_email;
				break;
			
			case "_NAME" : 
				echo '<TD><INPUT TYPE="hidden" NAME="showvars['.$uservars['id'].']" VALUE="'.$uservars['id'].'" '.$sel_show[$uservars['id']].'></TD>';
				echo "<TD>".$prop_label_text ."</TD><TD>";
				echo '<INPUT TYPE="text" NAME="name" VALUE="'.$name.'">'; break;
			
			case "_AVATAR" : 
				echo '<TD>&nbsp;</TD>';
				$old_avatar=userVarsGetID($uservars['id'],$uid);
				echo "<TD>".$prop_label_text ."</TD><TD>";
				echo '<SELECT NAME="user_avatar" onChange="showimage()">';
				$avatardir = opendir('images/avatar/');
				while ($avatar = readdir($avatardir)) {
					if (preg_match("/.gif|.jpg/i", $avatar)) {
						echo '<OPTION VALUE="'.$avatar.'"';

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
							$show_avatar="blank.gif";
                        }
						echo '>'.$avatar.'</OPTION>';
					}
				}
				echo '</SELECT>'; 
				echo '&nbsp;&nbsp;<img src="images/avatar/'.$show_avatar.'" name="avatar"  alt="" align="top">';
				break;
			
			
			case "_PHONE" : 
				echo '<TD><INPUT TYPE="hidden" NAME="showvars['.$uservars['id'].']" VALUE="'.$uservars['id'].'" '.$sel_show[$uservars['id']].'></TD>';
				echo "<TD>".$prop_label_text ."</TD><TD>";
				echo '<INPUT TYPE="text" NAME="phone" VALUE="'.$phone.'">';
				break;

			case "_NEWS" : 
			/*	echo '<TD>&nbsp;</TD>';
				echo "<TD>&nbsp;</TD><TD>";
				echo '<INPUT TYPE="checkbox" NAME="news"  VALUE="1" ';
				if ($news) {
					echo "checked";
				}
				echo '>'.$prop_label_text ;*/
			    break;
				
			case "_EXTRAINFO" : 
				echo '<TD valign=top><INPUT TYPE="hidden" NAME="showvars['.$uservars['id'].']" VALUE="'.$uservars['id'].'" '.$sel_show[$uservars['id']].'></TD>';
				echo "<TD valign=top>".$prop_label_text ."</TD><TD>";
				echo '<TEXTAREA NAME="uservars['.$uservars['id'].']" ROWS="3" COLS="30" style=width:90%>'.userVarsGetID($uservars['id'],$uid).'</TEXTAREA>';
				break;

			default:
				echo '<TD><INPUT TYPE="hidden" NAME="showvars['.$uservars['id'].']" VALUE="'.$uservars['id'].'" '.$sel_show[$uservars['id']].'></TD>';
				echo "<TD>".$prop_label_text ."</TD><TD>";
				echo '<INPUT TYPE="text" NAME="uservars['.$uservars['id'].']" VALUE="'.userVarsGetID($uservars['id'],$uid).'">';
		}
		 echo "</TD>";
		 echo "</TR>";
		 $result->MoveNext();
	}
	
	// Select Groups
   	echo "<TR VALIGN=TOP>";
	echo '<TD>&nbsp;</TD>';
	echo '<TD>'._SELECTGROUP.'</TD><TD>';
	echo '
	<INPUT TYPE="hidden" NAME="newLeft">
	<TABLE BORDER=0>
<TR>
	<TD>
	<SELECT CLASS="select" NAME="list1" MULTIPLE SIZE=9 onDblClick="opt.transferRight()"  style="width:150px">';
	$query = "SELECT $group_membershipcolumn[gid], $group_membershipcolumn[uid], $groupscolumn[name],$groupscolumn[type] "
						." FROM $group_membershiptable, $groupstable "
						." WHERE $group_membershipcolumn[gid]=$groupscolumn[gid] AND $group_membershipcolumn[uid] = '$uid' ";
	$result = $dbconn->Execute($query);
	
	$gList=array();
	 for ($i=1;list($gid, $uid,$name,$type) = $result->fields; $i++) {
		    $result->MoveNext();
			echo '<OPTION VALUE="'.$type.'">'.$name.'</OPTION>';
			$gList[]=$type;
	}
	echo '
	</SELECT>';

	echo '
	</TD>
	<TD VALIGN=MIDDLE ALIGN=CENTER>
		<INPUT CLASS="button_white" TYPE="button" NAME="left" VALUE="&lt;&lt;" ONCLICK="opt.transferLeft()"><BR><BR>
		<INPUT CLASS="button_white" TYPE="button" NAME="left" VALUE="All &lt;&lt;" ONCLICK="opt.transferAllLeft()"><BR><BR>
		<INPUT CLASS="button_white" TYPE="button" NAME="right" VALUE="&gt;&gt;" ONCLICK="opt.transferRight()"><BR><BR>
		<INPUT CLASS="button_white" TYPE="button" NAME="right" VALUE="All &gt;&gt;" ONCLICK="opt.transferAllRight()">
		
	</TD>
	<TD>
	<SELECT CLASS="select_gray" NAME="list2" MULTIPLE SIZE=9 onDblClick="opt.transferLeft()" style="width:150px">';
	   $result = $dbconn->Execute("SELECT  $groupscolumn[gid], $groupscolumn[name], $groupscolumn[description],$groupscolumn[type]  FROM $groupstable");
	   for ($i=1;list($gid, $name,$description,$type) = $result->fields; $i++) {
			$result->MoveNext();
			if (!groupExisting($gList,$type)) { 
				echo '<OPTION VALUE="'.$type.'">'.$name.'</OPTION>';
			}
		}
	echo '
	</SELECT>
	</TD>
</TR>
</TABLE>
	';
	echo "</TD></TR>";

	echo "<TR><TD>&nbsp;</TD><TD>&nbsp;</TD><TD>";
	echo '<INPUT TYPE="checkbox" NAME="active"  VALUE="1" ';
	if ($active) {
		echo "checked";
	}
	echo '> Active ?</TD></TR>';
	
	echo '<TR HEIGHT="50"><TD>&nbsp;</TD><TD>&nbsp;</TD><TD VALIGN="MIDDLE"><INPUT class="button_org" TYPE="submit" VALUE="'._SAVECHANGES.'"> <INPUT class="button_org" TYPE="button" VALUE="Cancel" OnClick="javascript:window.open(\'index.php?mod=User&amp;file=useredit\',\'_self\')"><TD></TR>';
	echo '</FORM></table>';

	CloseTable();

	include ("footer.php");
	return;
}


/* Show users */

/** Navigator **/
$menus= array(_ADMINMENU,_USERADMIN,_USEREDIT);
$links=array('index.php?mod=Admin','index.php?mod=User&file=admin','index.php?mod=User&file=useredit');
lnBlockNav($menus,$links);
/** Navigator **/

	OpenTable();

	echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=User&file=useredit"><B>'._USEREDIT.'</B></A>';

	echo '<center><FORM METHOD=POST ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="User">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="useredit">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="finduser">'

	._SEARCH.': <INPUT  class="input" TYPE="text" NAME="word" SIZE="20">&nbsp;'
	.'<SELECT class="select" NAME="field">'
	.'<OPTION VALUE="uname">'._NICKNAME.'</OPTION>'
	.'<OPTION VALUE="name">'._NAME.'</OPTION>'
	.'<OPTION VALUE="uno">'._UNO.'</OPTION>'
	.'<OPTION VALUE="email">'._EMAIL.'</OPTION>'
	.'</SELECT>'
	.' <INPUT class="button_org" TYPE="submit" VALUE="'._SUBMITFIND.'">'
	.'</FORM></center>';


	// Creates the list of letters and makes them a link.
	$alphabet = array (_ALL, "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
	$num = count($alphabet) - 1;
	$counter = 0;
	while(list(, $ltr) = each($alphabet)) {
		$class = ($letter == $ltr) ? "class=line": "";
		$menu[] = "<a $class  href=\"index.php?mod=User&amp;file=useredit&amp;letter=".$ltr."\">".$ltr."</a>";
		$counter++;
	}
	$menus = "<center>[ ".join('&nbsp;&nbsp;|&nbsp;&nbsp;',$menu)." ]</center><BR>";
	echo $menus;

	$pagesize = lnConfigGetVar('pagesize');
	if(empty($sorting)){
	$sorting="uname";
	} 

	if (!isset($letter)) {
	$letter = "A";
	}

	if (!isset($page)) {
	$page = 1;
	}

	$min = $pagesize * ($page - 1); // This is where we start our record set from
	$max = $pagesize; // This is how many rows to select
	$count = "SELECT COUNT($userscolumn[uid]) FROM $userstable ";

	$resultcount = $dbconn->Execute($count);
	list ($totalusers) = $resultcount->fields;
	$resultcount->Close();

	//Security Fix - Cleaning the search input
	$sorting   = lnVarCleanFromInput('sorting');
	if (!empty($sorting)){
	$sort = "$sorting ASC";
	}

	if (($letter != _ALL)) {
	$where .= " UPPER($userscolumn[uname]) LIKE UPPER('".lnVarPrepForStore($letter)."%')";
	}


	//$result = $dbconn->Execute($query);
	$myquery = buildSimpleQuery('users', array('uid', 'name', 'uname', 'email', 'regdate', 'phone','uno','news','active'), $where, lnVarPrepForStore($sort), $max, $min); 

	$result = $dbconn->Execute($myquery);
	if ($result === false) {
			error_log("Error: " . $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg());
	}

	echo '<table width=100% cellpadding=3 cellspacing=1 bgcolor="#d3d3d3">'
	.'<tr bgcolor="#808080" align=center><td class="head">No.</td><td  class="head">'
	.'<A class="white" HREF="index.php?mod=User&amp;file=useredit&amp;letter='.$letter.'&amp;sorting=uname"><B>'._NICKNAME.'</B></A></td><td class="head">'
	.'<A class="white" HREF="index.php?mod=User&amp;file=useredit&amp;letter='.$letter.'&amp;sorting=uno"><B>'._UNO.'</B></A></td><td class="head">'
	.'<A class="white"HREF="index.php?mod=User&amp;file=useredit&amp;letter='.$letter.'&amp;sorting=name"><B>'._NAME.'</B></A></td><td class="head">'
//	.'<A class="white" HREF="index.php?mod=User&amp;file=useredit&amp;letter='.$letter.'&amp;sorting=email"><B>'._EMAIL.'</B></A></td><td class="head">'
	.'<A class="white"HREF="index.php?mod=User&amp;file=useredit&amp;letter='.$letter.'&amp;sorting=regdate"><B>'._REGDATE.'</B></A></td><td class="head">'
	.'&nbsp;</td></tr>';
	for ($i=1; list($uid,$name,$uname,$email,$regdate,$phone,$uno,$news,$active) = $result->fields; $i++) {
	$result->MoveNext();
	if ($active) 
		$activecolor="#000000";
	else 
		$activecolor="#999999";
	$n=$min + $i;
	$link = "<A HREF=\"index.php?mod=User&amp;file=useredit&amp;op=edituser&amp;uid=$uid\">"; 
	echo '<TR  bgcolor=#FFFFFF>'
	.'<TD align=center width="25"><FONT COLOR="'.$activecolor.'">'.$link . $n .'</A></TD>'
	.'<TD width="100"><FONT COLOR="'.$activecolor.'">'.$link . $uname .'</FONT></A></TD>'
	.'<TD align=center width="70"><FONT COLOR="'.$activecolor.'">'.$link . $uno .'</A></TD>'
	.'<TD><FONT COLOR="'.$activecolor.'">'.$link . $name.'</A></TD>'
//	.'<TD><FONT COLOR="'.$activecolor.'">'.$link . $email.'</A></TD>'
	.'<TD align=center width="70"><FONT COLOR="'.$activecolor.'">'.$link . date('d-M-Y',$regdate).'</A></TD>'
	.'<TD align=center width="70">';
	echo "<A HREF=\"index.php?mod=User&amp;file=useredit&amp;op=edituser&amp;uid=$uid\"><IMG SRC=\"images/global/edit.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=0 ALT=\""._EDIT."\"></A>"; 
	echo '&nbsp;';
	echo "<A HREF=\"javascript:if(confirm('Delete $uname?')) window.open('index.php?mod=User&amp;file=useredit&amp;op=deleteuser&amp;uid=$uid&amp;letter=$letter','_self')\"><IMG SRC=\"images/global/delete.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=0 ALT=\""._DELETE."\"></A></TD>";
	echo '</TR>';
	}
	echo '</table>';


	// Show pages
	if (!empty($where)) {
	$where = " WHERE $where";
	} else {
	$where = '';
	}

	$resultcount = $dbconn->Execute($count . $where);
	list ($numrows) = $resultcount->fields;
	$resultcount->Close();

	//echo "<center>";

	if ($numrows  > $pagesize) {
	 $total_pages = ceil($numrows / $pagesize); // How many pages are we dealing with here ??
	 $prev_page = $page - 1;
	 echo '<BR>Page: ';
	  if ( $prev_page > 0 ) {
		echo '[<A HREF="index.php?mod=User&amp;file=useredit&amp;letter='.$letter.'&amp;page='.$prev_page.'&amp;sorting='.$sorting.'">&lt;&lt;</A>] ';
	  }
	  for($n=1; $n <= $total_pages; $n++) {
		if ($n == $page) {
			echo "<B><U>$n</U></B> ";
		}
		else {
			echo '<A HREF="index.php?mod=User&amp;file=useredit&amp;letter='.$letter.'&amp;page='.$n.'&amp;sorting='.$sorting.'">'.$n.'</A> ';
		}
	  } 
	  $next_page = $page + 1;
	  if ( $next_page <= $total_pages ) {
		  echo ' [<A HREF="index.php?mod=User&amp;file=useredit&amp;letter='.$letter.'&amp;page='.$next_page.'&amp;sorting='.$sorting.'">&gt;&gt;</A>]';
	  }
	}
	echo "<BR><B>"._TOTALUSERS.'&nbsp;'.$totalusers."</B><BR>";
	//echo "</center>";

	echo "<BR><BR>";

	CloseTable();

	include 'footer.php';
/*- - - - - - - -*/


// Utilties Functions
function groupExisting($gList,$gid) {
	for ($i=0; $i<count($gList); $i++) {
		if ($gList[$i] == $gid) {
			return true;
		}
	}

	return false;
}
?>