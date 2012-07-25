<?php
/*
programmer : Neetiwit B.
date : 28-10-2549
Description :
 1. แก้ไขให้ login เข้า ldap ได้

*/
include 'lntables.php';
function lnUserGetlang() {
    $lang = lnSessionGetVar('lang');
    if (!empty($lang)) {
        return $lang;
    } else {
        return lnConfigGetVar('language');
    }
}


function themes_get_language() {
	return true;
}


/**
 * Log the user in
 * @param uname the name of the user logging in
 * @param pass the password of the user logging in
 * @param whether or not to remember this login
 * @returns bool
 * @return true if the user successfully logged in, false otherwise
 */
function lnUserLogIn($uname, $pass, $rememberme)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	$userIP =	getenv("REMOTE_ADDR");
	
/*	$event_usertable = $lntable['event_user'];
	$event_usercolumn = &$lntable['event_user_column'];

$query3 = "DELETE from $event_usertable
              WHERE $event_usercolumn[ipaddr] = '$bar' AND  $event_usercolumn[ippro] = '$userIP'";
    $dbconn->Execute($query3);*/
//AND  $event_usercolumn[ippro] = $userIP

    if (!lnUserLoggedIn()) {
		include('config.php');
		if($config['isUse'] == '1') // เช็คว่าจะใช้ ldap หรือเปล่า
		{
			$ldapinfo = checkLdapUser($uname,$pass); // Check Ldap User
			if($ldapinfo == true) // เจอ user หรือไม่
			{
				$userscolumn = &$lntable['users_column'];
				$userstable = $lntable['users'];
				$group_membershiptable = $lntable['group_membership'];
				$group_membershipcolumn = &$lntable['group_membership_column'];

				$query = "SELECT $userscolumn[uid],
							 $userscolumn[pass]
					  FROM $userstable
					  WHERE $userscolumn[uname] = '" . lnVarPrepForStore($uname) ."'";

				$result = $dbconn->Execute($query);
				if ($result->EOF) {
					$uid = lnUserNextID();
					$query = "INSERT INTO $userstable ($userscolumn[uid],$userscolumn[name],
							$userscolumn[uname], $userscolumn[email], $userscolumn[regdate],
							$userscolumn[pass], $userscolumn[phone], $userscolumn[news], $userscolumn[uno])
							 values ($uid,'".$uname . "','".$uname."',null, '" . gmmktime() . "', '" . md5($pass) . "',null,null,null)";

					 $dbconn->Execute($query);
					$gid1 =  lnConfigGetVar('default_group');
					$query = "INSERT INTO $group_membershiptable ($group_membershipcolumn[gid],
							$group_membershipcolumn[uid])
							 values ($gid1,$uid)";
					//echo $query;
					$result = $dbconn->Execute($query);
					 lnUpdateUserEvent("First login by LDAP and insert user in db Complete");
				}
				unset($result);
				$query = "SELECT $userscolumn[uid],
							 $userscolumn[pass]
					  FROM $userstable
					  WHERE $userscolumn[uname] = '" . lnVarPrepForStore($uname) ."'";
  				//echo $query;
				$result = $dbconn->Execute($query);
				list($uid, $realpass) = $result->fields;
				$result->Close();

				// Confirm that passwords match
				if (!comparePasswords($pass, $realpass, $uname, substr($realpass, 0, 2))) {
					// update event
					lnUpdateUserEvent("'$uname' type wrong password '$pass' .");
					return false;
				}

				// Set user session information (new table)
				$sessioninfocolumn = &$lntable['session_info_column'];
				$sessioninfotable = $lntable['session_info'];


				$query = "UPDATE $sessioninfotable
						  SET $sessioninfocolumn[uid] = " . lnVarPrepForStore($uid) . "
						  WHERE $sessioninfocolumn[sessid] = '" . lnVarPrepForStore(session_id()) . "'";
				$dbconn->Execute($query);

		 
		//lnVarPrepForStore($uname)
				if (!empty($rememberme)) {
					lnSessionSetVar('rememberme', 1);
				}
			}
			else
			{
				// Get user information
				$userscolumn = &$lntable['users_column'];
				$userstable = $lntable['users'];

				$query = "SELECT $userscolumn[uid],
								 $userscolumn[pass]
						  FROM $userstable
						  WHERE $userscolumn[uname] = '" . lnVarPrepForStore($uname) ."'";
				$result = $dbconn->Execute($query);

				if ($result->EOF) {
					// update event
					lnUpdateUserEvent("'$uname' try to login with '$pass' .");
					return false;
				}
				list($uid, $realpass) = $result->fields;
				$result->Close();

				// Confirm that passwords match
				if (!comparePasswords($pass, $realpass, $uname, substr($realpass, 0, 2))) {
					// update event
					lnUpdateUserEvent("'$uname' type wrong password '$pass' .");
					return false;
				}

				// Set user session information (new table)
				$sessioninfocolumn = &$lntable['session_info_column'];
				$sessioninfotable = $lntable['session_info'];


				$query = "UPDATE $sessioninfotable
						  SET $sessioninfocolumn[uid] = " . lnVarPrepForStore($uid) . "
						  WHERE $sessioninfocolumn[sessid] = '" . lnVarPrepForStore(session_id()) . "'";
				$dbconn->Execute($query);

		 
				//lnVarPrepForStore($uname)
				if (!empty($rememberme)) {
					lnSessionSetVar('rememberme', 1);
				}
			}
		}
		else
		{
			// Get user information
			$userscolumn = &$lntable['users_column'];
			$userstable = $lntable['users'];

			$query = "SELECT $userscolumn[uid],
							 $userscolumn[pass]
					  FROM $userstable
					  WHERE $userscolumn[uname] = '" . lnVarPrepForStore($uname) ."'";
			$result = $dbconn->Execute($query);

			if ($result->EOF) {
				// update event
				lnUpdateUserEvent("'$uname' try to login with '$pass' .");
				return false;
			}
			list($uid, $realpass) = $result->fields;
			$result->Close();

			// Confirm that passwords match
			if (!comparePasswords($pass, $realpass, $uname, substr($realpass, 0, 2))) {
				// update event
				lnUpdateUserEvent("'$uname' type wrong password '$pass' .");
				return false;
			}

			// Set user session information (new table)
			$sessioninfocolumn = &$lntable['session_info_column'];
			$sessioninfotable = $lntable['session_info'];


			$query = "UPDATE $sessioninfotable
					  SET $sessioninfocolumn[uid] = " . lnVarPrepForStore($uid) . "
					  WHERE $sessioninfocolumn[sessid] = '" . lnVarPrepForStore(session_id()) . "'";
			$dbconn->Execute($query);

	 
	//lnVarPrepForStore($uname)
			if (!empty($rememberme)) {
				lnSessionSetVar('rememberme', 1);
			}
		 }
	}
	$userIP = getenv("REMOTE_ADDR");
	if(!isset($uid)) $uid=0;
	lnSessionSetVar('uid', $uid);
	lnSessionSetVar('uname', lnVarPrepForStore($uname));
	// update event
	lnUpdateUserEvent("Login");

	$userscolumn = &$lntable['users_column'];
	$userstable = $lntable['users'];
	

$query = "SELECT $userscolumn[uid]
                  FROM $userstable
                  WHERE $userscolumn[uname] = '$uname'  AND  $userscolumn[pass] = '$pass'";

	
$result1 = $dbconn->Execute($query);

   $uidnow =  lnSessionGetVar('uid');

	//$event_usertable = $lntable['event_user'];
	//$event_usercolumn = &$lntable['event_user_column'];

	 /*$query = "INSERT INTO $event_usertable
	VALUES ('".$uidnow."', '".$bar."', '".$userIP."')";

$result = $dbconn->Execute($query);*/


/*$filenow = $uname.'_'.'IpAddress.txt';
	$lessondir =  'modules' . '/' . 'Courses' . '/' . $filenow;	
	$file =  fopen($lessondir,"w");
					fwrite($file,$bar);
					fwrite($file,',');
					fwrite($file,$userIP);
					fwrite($file,','.$query3);
	               fclose($file);*/

     return true;
}

/**
 * Compare Passwords
  */
function comparePasswords($givenpass, $realpass, $username, $cryptSalt='')
{

    $compare2crypt = true;
    $compare2text = true;
    
    $system = lnConfigGetVar('system');
    
    $md5pass = md5($givenpass);

    if (strcmp($md5pass, $realpass) == 0)
        return $md5pass;
    elseif ($compare2crypt && $system != "1" ){
        $crypted = false;
        if ($cryptSalt != '') {
            if (strcmp(crypt($givenpass, $cryptSalt), $realpass) == 0)
                $crypted = true;
        } else {
            if (strcmp(crypt($givenpass, $cryptSalt), $realpass) == 0)
                $crypted = true;
        }
        if ($crypted){
            updateUserPass($username, $md5pass);
            return $md5pass;
        }
    } elseif ($compare2text && strcmp($givenpass, $realpass) == 0) {
             updateUserPass($username, $md5pass);
             return $md5pass;
    }
    
    return false;
}

/**
 * Log the user out
 * @public
 * @returns bool
 * @return true if the user successfully logged out, false otherwise
 */
function lnUserLogOut()
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    if (lnUserLoggedIn()) {
        // Reset user session information (new table)

		$sessioninfocolumn = &$lntable['session_info_column'];
        $sessioninfotable = $lntable['session_info'];
        $query = "UPDATE $sessioninfotable
                  SET $sessioninfocolumn[uid] = 0
                  WHERE $sessioninfocolumn[sessid] = '" . lnVarPrepForStore(session_id()) . "'";
        $dbconn->Execute($query);

 $uidnow =  lnSessionGetVar('uid');
$userIP = getenv("REMOTE_ADDR");

	/*$event_usertable = $lntable['event_user'];
	$event_usercolumn = &$lntable['event_user_column'];

$query = "DELETE from $event_usertable
              WHERE $event_usercolumn[uid] = $uidnow";
    $dbconn->Execute($query);

        $dbconn->Execute($query);*/

		$uids =  lnSessionGetVar('uid');
		$unames =  lnSessionGetVar('uname');
		$filename = $unames.'_'.$uids.'.txt';
		$lessondir =  'modules' . '/' . 'Courses' . '/' .$filename;	
	

		$unames =  lnSessionGetVar('uname');
		$filename1 = $unames.'_'.'IpAddress.txt';
		$lessondir1 =  'modules' . '/' . 'Courses' . '/' .$filename1;	

		
	   if(is_file($lessondir)) unlink($lessondir);
       if(is_file($lessondir1)) unlink($lessondir1);


		lnUpdateUserEvent("Logout");
        lnSessionDelVar('rememberme');
        lnSessionDelVar('uid');
		 lnSessionDelVar('uname');
		
    }
}

/**
 * is the user logged in?
 * @public
 * @returns bool
 * @returns true if the user is logged in, false if they are not
 */
function lnUserLoggedIn()
{
    if(lnSessionGetVar('uid')) {
        return true;
    } else {
        return false;
    }
}

function lnUserGetUid($uname)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	$userstable = $lntable['users'];
	$userscolumn = $lntable['users_column'];
    $query = "SELECT $userscolumn[uid]
				  FROM $userstable
				  WHERE $userscolumn[uname] = '" . lnVarPrepForStore($uname) ."' ";

	$result = $dbconn->Execute($query);
	list ($uid) = $result->fields;
	
	return $uid;
}


/**
 * get all user variables
 * @access public
 * @author Gregor J. Rothfuss
 * @since 1.33 - 2002/02/07
 * @param uid the user id of the user
 * @returns array
 * @return an associative array with all variables for a user
 */
function lnUserGetVars($uid)
{
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$vars = array();

    $propertiestable = $lntable['user_property'];
    $userstable = $lntable['users'];
    $datatable = $lntable['user_data'];
    $userscolumn = &$lntable['users_column'];
    $datacolumn = &$lntable['user_data_column'];
    $propcolumn = &$lntable['user_property_column'];
						
	$query = "SELECT $propcolumn[prop_label] as label, $datacolumn[uda_value] as value
              FROM $datatable, $propertiestable 
              WHERE ".$datacolumn['uda_uid']." = '" . lnVarPrepForStore($uid) ."' "
              ."AND $datacolumn[uda_propid] = $propcolumn[prop_id]";


    $result = $dbconn->Execute($query);

	while (!$result->EOF) {
       $uservars = &$result->GetRowAssoc(false);
       $vars[$uservars['label']] = $uservars['value'];
       $result->MoveNext();
    }

    $result->Close();

    $query = "SELECT *
              FROM $userstable
              WHERE $userscolumn[uid] = " . lnVarPrepForStore($uid);
    $result = $dbconn->Execute($query);

	if ($result->EOF) {
        return false;
    }

    $corevars = $result->GetRowAssoc(false);
    $result->Close();

    $vars = array_merge ($vars, $corevars);

	// Aliasing if required
    if (empty($vars['uid'])) {
        $vars['uid'] = $vars['ln_uid'];
        $vars['email'] = $vars['ln_email'];
        $vars['name'] = $vars['ln_name'];
        $vars['uname'] = $vars['ln_uname'];
        $vars['phone'] = $vars['ln_phone'];
        $vars['uno'] = $vars['ln_uno'];
        $vars['news'] = $vars['ln_news'];
        $vars['show'] = $vars['ln_show'];
    }

return($vars);
}

/**
 * get a user variable
 * @public
 * @author Jim McDonald
 * @param name the name of the variable
 * @param uid the user to get the variable for
 * @returns string
 * @return the value of the user variable if successful, false otherwise
 */
function lnUserGetVar($name, $uid=-1)
{

    static $vars = array();

    if (empty($name)) {
        return;
    }

    if ($uid == -1) {
        $uid = lnSessionGetVar('uid');
    }
    if (empty($uid)) {
        return;
    }

    // Get this user's variables if not already obtained
    if (!isset($vars[$uid])) {
		$vars[$uid] = lnUserGetVars($uid);
    }

    // Return the variable
    if (isset($vars[$uid][$name])) {
        return $vars[$uid][$name];
    } else {
        return;
    }
}

function userVarsGet($id) {
	$userinfo=lnUserGetVars(lnSessionGetVar('uid'));
	 $ret = userVarsGetID($id,$userinfo['uid']);

/*
	 list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	$datatable = $lntable['user_data'];
	$datacolumn = &$lntable['user_data_column'];

	$query = "SELECT $datacolumn[uda_value] value FROM $datatable WHERE $datacolumn[uda_propid]='" . lnVarPrepForStore($id) . "' and $datacolumn[uda_uid]='" . lnVarPrepForStore($userinfo['uid']) . "'";

	$result = $dbconn->Execute($query);
	$data = $result->GetRowAssoc(false);
*/

	return $ret;
}

function userVarsGetID($id,$uid) {

	 list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	$datatable = $lntable['user_data'];
	$datacolumn = &$lntable['user_data_column'];

	$query = "SELECT $datacolumn[uda_value] value FROM $datatable WHERE $datacolumn[uda_propid]='" . lnVarPrepForStore($id) . "' and $datacolumn[uda_uid]='" . lnVarPrepForStore($uid) . "'";

	$result = $dbconn->Execute($query);
	$data = $result->GetRowAssoc(false);

	return $data['value'];
}


function updateUserPass($username, $md5pass)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $column = &$lntable['users_column'];
    $result = $dbconn->Execute("UPDATE $pntable[users]
                              SET $column[pass] = '" . lnVarPrepForStore($md5pass) . "'
                              WHERE $column[uname]='" . lnVarPrepForStore($username) . "'");
} 


/**
 * delete the contents of a user variable
 * @access public
 * @author Gregor J. Rothfuss
 * @since 1.23 - 2002/02/01
 * @param name the name of the variable
 * @returns bool
 * @return true on success, false on failure
 */
function lnUserDelVar($name)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $propertiestable = $lntable['user_property'];
    $datatable = $lntable['user_data'];
    $propcolumns = &$lntable['user_property_column'];
    $datacolumns = &$lntable['user_data_column'];

    // Prevent deletion of core fields (duh)
    if (empty($name) || ($name == 'uid') || ($name == 'email') ||
    ($name == 'password') || ($name == 'uname')) {
        return false;
    }

    $uid = lnSessionGetVar('uid');
    if (empty($uid)) {
        return false;
    }

    // get property id for cascading delete later
    $query = "SELECT $propcolumns[prop_id] from $propertiestable
              WHERE $propcolumns[prop_label] = '" . lnVarPrepForStore($name) ."'";
    $result = $dbconn->Execute($query);

    if ($result->EOF) {
        return false;
    }

    list ($id) = $result->fields;

    $query = "DELETE from $propertiestable
              WHERE $propcolumns[prop_id] = '" . lnVarPrepForStore($id) ."'";
    $result = $dbconn->Execute($query);

    if($dbconn->ErrorNo() != 0) {
      return false;
    }

    // delete variable from user data for all users
    $query = "DELETE from $datatable
              WHERE $datacolumns[uda_propid] = '" . pnVarPrepForStore($id) ."'";
    $dbconn->Execute($query);

    if($dbconn->ErrorNo() != 0) {
      return false;
    }

    return true;
}



function lnUserCheck($uid='',$nickname,$id,$email) {

	if (lnUserLoggedIn() && empty($uid)) {
		$userinfo=lnUserGetVars(lnSessionGetVar('uid'));
	}
	else if (!empty($uid)){
		$userinfo=lnUserGetVars($uid);	
	}


	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$userstable = $lntable['users'];
	$column = &$lntable['users_column'];

	 if (lnConfigGetVar('reg_uniuname')) {
		
		if ($userinfo['uname'] == $nickname || empty($nickname)) {
		}
		else {
			$existinguser = $dbconn->Execute("SELECT $column[uname] FROM $userstable WHERE $column[uname]='" . lnVarPrepForStore($nickname) . "'");
			if (!$existinguser->EOF) {
				$existinguser->Close();
				$stop =  _NICKTAKEN;
				return $stop;
			} 
			$existinguser->Close();
		}
	 }
	 
	
	if (lnConfigGetVar('reg_uniid')) {
		if ($userinfo['uno'] == $id || empty($id)) {
		}
		else {
			$existinguser = $dbconn->Execute("SELECT $column[uname] FROM $userstable WHERE $column[uno]='" . lnVarPrepForStore($id) . "'");
			if (!$existinguser->EOF) {
				$existinguser->Close();
				$stop =  _UNOTAKEN;
				return $stop;
			} 
			$existinguser->Close();
		}
	 }

	 if (lnConfigGetVar('reg_uniemail')) {	
		if ($userinfo['email'] == $email || empty($email)) {
		}
		else {
			$existinguser = $dbconn->Execute("SELECT $column[uname] FROM $userstable WHERE $column[email]='" . lnVarPrepForStore($email) . "'");
			if (!$existinguser->EOF) {
				$existinguser->Close();
				$stop = _EMAILTAKEN;
				return $stop;
			} 
			$existinguser->Close();
		}
	 }

	 return false;

}


function lnUserReqProp($proplabel) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$propertiestable = $lntable['user_property'];
	$propcolumn = &$lntable['user_property_column'];
	$existingdata = $dbconn->Execute("SELECT $propcolumn[prop_id] FROM $propertiestable WHERE $propcolumn[prop_label]='" . lnVarPrepForStore($proplabel) . "' and $propcolumn[prop_weight] <> '0' ");
	if ($existingdata->EOF) {
		return false;		
	}
	else {
		return true;
	}
}

function lnUserCheckDup($line,$head,$data,$td) {

	$heads = explode("$td",$head);
	$datas = explode("$td",$data);
	 
	if (count($heads) != count($datas)) {
		return false;
	}

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$table = $lntable['users'];
	$column = &$lntable['users_column'];

	for ($i=0; $i < count($heads) ;$i++) {
		$existingdata = $dbconn->Execute("SELECT $column[uid] FROM $table WHERE ".trim($heads[$i])." ='" . trim(lnVarPrepForStore($datas[$i])) . "'");
		if(!$existingdata){
			$error =  "Error, ".$dbconn->ErrorMsg().", <b><font color='red'>column header error</font></b>";
			return $error;
		}
		//echo "SELECT $column[uid] FROM $table WHERE $heads[$i] ='" . lnVarPrepForStore($datas[$i]) . "'<br>";
		if (!$existingdata->EOF) {
			$existingdata->Close();
			if (lnConfigGetVar('reg_uniuname') && preg_match("/$heads[$i]/i", $column[uname])) {
				$error = "Duplicate nickname '$datas[$i]' in line #$line";
				return $error;
			}
			if (lnConfigGetVar('reg_uniid') && preg_match("/$heads[$i]/i", $column[uno])) {
				$error = "Duplicate user id  '$datas[$i]'  in line #$line";
				return $error;
			}
			if (lnConfigGetVar('reg_uniemail') && preg_match("/$heads[$i]/i", $column[email])) {
				$error = "Duplicate email '$datas[$i]' in line #$line";
				return $error;
			}
			
		} 
		$existingdata->Close();
	}

	return false;
}

// Utilities Function ================================
function findGroupName($default_group) {

	if ($default_group == -1) {
		return _ALLGROUPS;
	}
	else if ($default_group == 0) {
		return _UNREGUSERS;
	}

   list($dbconn) = lnDBGetConn();
   $lntable = lnDBGetTables();

   $groupstable = $lntable['groups'];
   $groupscolumn = &$lntable['groups_column'];
   $result = $dbconn->Execute("SELECT  $groupscolumn[name]  FROM $groupstable WHERE $groupscolumn[gid] = '".$default_group."'");
   list($name) = $result->fields;

   return $name;
}

// Get Next User ID instead Auto Increment
function getNextUserID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$table = $lntable['users'];
	$column = &$lntable['users_column'];
	 $result = $dbconn->Execute("SELECT  Max($column[uid])  FROM $table");
	 list($maxid) = $result->fields;
	  
	 return $maxid+1;
}


/**
* User Online
*/
function lnGetUserNumber($get) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$sessioninfotable = $lntable['session_info'];
	$sessioninfocolumn = &$lntable['session_info_column'];
	$activetime = time() - (lnConfigGetVar('secinactivemins') * 60);

	// Get list of users on-line
	$sql = "SELECT DISTINCT $sessioninfocolumn[uid]
                    FROM $sessioninfotable
                    WHERE $sessioninfocolumn[uid] != 0
                    AND $sessioninfocolumn[lastused] > $activetime
                   GROUP BY $sessioninfocolumn[uid]";

	$result3 = $dbconn->Execute($sql);
	$numusers = $result3->RecordCount();

	$unames = array();
	for (; !$result3->EOF; $result3->MoveNext()) {
		$unames[] = lnUserGetVar('uname', $result3->fields[0]);
	}
	$result3->Close();

	// Get list of guest users on-line
	$query = "SELECT DISTINCT $sessioninfocolumn[ipaddr]
              FROM $sessioninfotable
              WHERE $sessioninfocolumn[lastused] > $activetime 
              AND $sessioninfocolumn[uid] = '0'
			  GROUP BY $sessioninfocolumn[sessid]";
	
    $result = $dbconn->Execute($query);
	$numguests = $result->RecordCount();
/*
    for (; !$result->EOF; $result->MoveNext()) {
        list($type, $num) = $result->fields;
        if ($type == 0) {
            $numguests = $num;
        } else {
            $numusers++;
        }
    }
*/
	$result->Close();

	$query = "DELETE  
              FROM $sessioninfotable
              WHERE $sessioninfocolumn[lastused] <= $activetime";
	
    $result = $dbconn->Execute($query);
	
	$guests = _GUEST;
	$users = _MEMBER;

	// Sort by username
	sort($unames);
	reset($unames);
	$numregusers = count($unames);

// Get last visit
	$sql = "SELECT $sessioninfocolumn[lastused]
                    FROM $sessioninfotable
                    WHERE $sessioninfocolumn[uid] = '".lnSessionGetVar('uid')."'
					ORDER BY $sessioninfocolumn[lastused] DESC";

	$result4 = $dbconn->Execute($sql);
	$result4->MoveNext();
	$lastvisit = $result4->fields[0];
	$ret='';
	if ($get=='number') {
		$ret = "<table>";
		$ret .= "<tr><td rowspan=3 valign=middle><IMG SRC=modules/UserOnline/images/icon_connect.gif WIDTH=16 HEIGHT=16 BORDER=0><td colspan=3></td></tr>";
		$ret .= "<tr><td><IMG SRC=modules/UserOnline/images/icon_users.gif WIDTH=15 HEIGHT=15 BORDER=0></td><td>".lnVarPrepForDisplay($guests)."</td><td><B>".lnVarPrepForDisplay($numguests)."</B>&nbsp; "._UNIT."</td></tr>";
		$ret .="<tr><td><IMG SRC=modules/UserOnline/images/icon_registered.gif WIDTH=15 HEIGHT=15 BORDER=0></td><td>".lnVarPrepForDisplay($users)."</td><td><B>".lnVarPrepForDisplay($numregusers)."</B>&nbsp; "._UNIT."</td></tr>";
		$ret .="</table>";
	}

	if ($get=='online') {
		$ret = "<a name=online><br><table align=\"center\" width=\"95%\" cellspacing=\"1\" cellpadding=\"5\"><tr><td>";
		foreach ($unames as $uname) {
			$ret .= "<b>+ ".lnVarPrepForDisplay($uname)."&nbsp;</font></b> ";
		}
		echo"</td></tr></table>";
	}
	
	if($get=='countnum') {
		$ret = $numusers;
	}

	if ($get=='lastvisit' && !empty($lastvisit)) {
		$ret = _LASTVISIT .'<BR>&nbsp;'. date('d-M-Y h:i',$lastvisit).'&nbsp;';
	}

	return $ret;
}


/*
* update user event
*/
function lnUpdateUserEvent($event) {

	if($event == "Logout"){
//SetCookie("uidnow","",time()+3600); 
		//if(session_is_registered("uidnow"))
		if(isset($_SESSION['uidnow']))
		{
			//Session_Start();
			lnSessionDelVar("uidnow");
			//session_unregister("uidnow");
		}

	}
	$uid = lnSessionGetVar('uid');
	if(trim($uid) == '')
		$uid = 'NULL';
//	$cid = lnSessionGetVar('cid');
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$userlogtable = $lntable['user_log'];
	$userlogcolumn = &$lntable['user_log_column'];

	$activetime = time();
	$userIP = getenv("REMOTE_ADDR");
	//$cid = crc32(substr($event,17,1));
  //$cid = strlen($event);
    $cids = lnVarPrepForStore($event);
	
$cid = substr($cids,19,1);
if(trim($cid) == '')
	$cid = 'NULL';
  /* $handle =  fopen("test.txt",'w');
    fwrite($handle,$cids);
	fclose($handle);*/

	$query = "INSERT INTO $userlogtable 
						($userlogcolumn[uid], $userlogcolumn[cid], $userlogcolumn[atime], $userlogcolumn[event], $userlogcolumn[ip])
						VALUES
						( '".lnVarPrepForStore($uid)."',  ".$cid." ,'".lnVarPrepForStore($activetime)."','".lnVarPrepForStore($event)."', '".lnVarPrepForStore($userIP)."')";


	$result = $dbconn->Execute($query);
	if ($result->EOF) {
		return false;		
	}
	else {
		return true;
	}
}

function lnUserStudent($uid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$groupstable = $lntable['groups'];
	$groupscolumn = &$lntable['groups_column'];
	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];
	
	$result = $dbconn->Execute("SELECT  $groupscolumn[gid]  FROM $groupstable,$group_membershiptable WHERE $groupscolumn[gid]=$group_membershipcolumn[gid] AND $group_membershipcolumn[uid]='$uid' AND $groupscolumn[type]='"._LNGROUP_STUDENT."'");
		
	list($gid) = $result->fields;
	  
	 if (empty($gid)) {
		return FALSE;
	 }
	 else {
		return TRUE;
	 }
}

function lnUserInstructor($uid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$groupstable = $lntable['groups'];
	$groupscolumn = &$lntable['groups_column'];
	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];
	
	$result = $dbconn->Execute("SELECT  $groupscolumn[gid]  FROM $groupstable,$group_membershiptable WHERE $groupscolumn[gid]=$group_membershipcolumn[gid] AND $group_membershipcolumn[uid]='$uid' AND $groupscolumn[type]='"._LNGROUP_INSTRUCTOR."'");
		
	list($gid) = $result->fields;
	  
	 if (empty($gid)) {
		return FALSE;
	 }
	 else {
		return TRUE;
	 }
}

function lnUserTA($uid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$groupstable = $lntable['groups'];
	$groupscolumn = &$lntable['groups_column'];
	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];
	
	$result = $dbconn->Execute("SELECT  $groupscolumn[gid]  FROM $groupstable,$group_membershiptable WHERE $groupscolumn[gid]=$group_membershipcolumn[gid] AND $group_membershipcolumn[uid]='$uid' AND $groupscolumn[type]='"._LNGROUP_TA."'");
		
	list($gid) = $result->fields;
	  
	 if (empty($gid)) {
		return FALSE;
	 }
	 else {
		return TRUE;
	 }
}

function lnUserAdmin($uid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$groupstable = $lntable['groups'];
	$groupscolumn = &$lntable['groups_column'];
	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];
	
	$result = $dbconn->Execute("SELECT  $groupscolumn[gid]  FROM $groupstable,$group_membershiptable WHERE $groupscolumn[gid]=$group_membershipcolumn[gid] AND $group_membershipcolumn[uid]='$uid' AND $groupscolumn[type]='"._LNGROUP_ADMIN."'");
		
	list($gid) = $result->fields;
	  
	 if (empty($gid)) {
		return FALSE;
	 }
	 else {
		return TRUE;
	 }
}

function lnMail($from,$to,$subject,$msg) {

	$header.="From: $from\r\n";
	$header.="X-Priority: 3\r\n";
	$header.="Content-Type: text/html; charset=tis-620\r\n";
	$header.="\r\n";

	if (mail($to,$subject,$msg,$header)) {
		return 1;
	}
	else {
		return 0;
	}
}

function lnUserNextID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	
	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];
	$query = "SELECT MAX($userscolumn[uid]) FROM $userstable";
	
	$result = $dbconn->Execute($query);

	return $result->fields[0] + 1;
}

function lnUserDataNextID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	
	$user_datatable = $lntable['user_data'];
	$user_datacolumn = &$lntable['user_data_column'];
	$query = "SELECT MAX($user_datacolumn[uda_id]) FROM $user_datatable";
	
	$result = $dbconn->Execute($query);

	return $result->fields[0] + 1;
}
function checkLdapUser($username,$password)
{
	include('config.php');

   $ldap_server = $config['ldapserver'];
   $sitename = $config['ldapsitename'];
   $sitesuffix = $config['sitesuffix'];
   $ou = $config['ou'];
  if($connect=@ldap_connect($ldap_server)){ // if connected to ldap server

   /*if (ldap_set_option($connect, LDAP_OPT_PROTOCOL_VERSION, 3)) {
     echo "version 3<br>\n";
   } else {
     echo "version 2<br>\n";
   }
   echo "verification on '$ldap_server': ";*/

   // bind to ldap connection
   if(($bind=@ldap_bind($connect)) == false){
      lnUpdateUserEvent( "bind:__FAILED__");
     return false;
   }

   // search for user
   if (($res_id = ldap_search( $connect,
                               "ou=$ou,dc= $sitename,dc=$sitesuffix",
                               "uid=$username")) == false) {
     lnUpdateUserEvent( "failure: search in LDAP-tree failed");
     return false;
   }

   if (ldap_count_entries($connect, $res_id) != 1) {
     lnUpdateUserEvent( "failure: username $username found more than once");
     return false;
   }

   if (( $entry_id = ldap_first_entry($connect, $res_id))== false) {
     lnUpdateUserEvent( "failur: entry of searchresult couln't be fetched");
     return false;
   }

   if (( $user_dn = ldap_get_dn($connect, $entry_id)) == false) {
     lnUpdateUserEvent("failure: user-dn coulnd't be fetched");
     return false;
   }

   /* Authentifizierung des User */
   if (($link_id = ldap_bind($connect, $user_dn, $password)) == false) {
     lnUpdateUserEvent("failure: username, password didn't match: $user_dn");
     return false;
   }

   return true;
   @ldap_close($connect);
  } else {                                  // no conection to ldap server
   lnUpdateUserEvent("no connection to '$ldap_server'");
  }

  //echo "failed: ".ldap_error($connect)."<BR>\n";

  @ldap_close($connect);
  return(false);

}//end function checkldapuser
/*function checkLdapUser ($username,$password) {
	include('config.php');

   $ldapserver = $config['ldapserver'];
   $sitename = $config['sitename'];
   $sitesuffix = $config['sitesuffix'];
   $cn = $config['cn'];
	if($config['isUse'] == '1')
	{
	   $ds=ldap_connect($ldapserver);
	   if ($ds) {
		   $dn="uid=$username,cn=$cn, DC=$sitename, DC=$sitesuffix";
		   $r=@ldap_bind($ds,$dn,$password); 
   			echo $dn;
		   if ($r) 
			{ 
				$sr=ldap_search($ds, "cn=$cn, DC=$sitename, DC=$sitesuffix", "uid=$username"); 
   				$info = ldap_get_entries($ds, $sr);
				print_r($info);
				return $info;
		   } else {
			   return ldap_error($ds);
		   }
	   }
	}
	else
	{
		return false;
	}
}*/

//function Check Status
function lnUserStatus($uid)
{	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$session_infotable = $lntable['session_info'];
	$session_infocolumn = $lntable['session_info_column'];

	//check online

	$sql="SELECT '1' FROM $session_infotable WHERE $session_infocolumn[uid] = '".$uid."'";
	
	$result = $dbconn->Execute($sql);

	if($result->RecordCount() > 0)
	{
		echo "<img src='images/status/online.JPG'>";
	}else{
		echo "<img src='images/status/offline.JPG'>";
	}


}
?>
