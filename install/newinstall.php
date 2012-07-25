<?php 
// File: $Id: newinstall.php,v 1.2 2007/02/22 04:03:01 nay Exp $ $Name: HEAD $
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2001 by the Post-Nuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE

// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.

// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Gregor J. Rothfuss
// Purpose of file: Provide functions for a new install.
// ----------------------------------------------------------------------
/**
 * This function creates the DB on new installs
 */
function make_db($dbhost, $dbuname, $dbpass, $dbname, $prefix, $dbtype, $dbmake, $dbtabletype)
{
    global $dbconn;
    echo "<center><br><br>";
    if ($dbmake) {
        mysql_pconnect($dbhost, $dbuname, $dbpass);
		
        //$result = mysql_query("CREATE DATABASE $dbname") or die (_MAKE_DB_1);
		$result = mysql_query("CREATE DATABASE $dbname DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci") ;
		if(!$result)
		{
			unset($result);
			$result = mysql_query("CREATE DATABASE $dbname") or die (_MAKE_DB_1);
			if(!$result)
			{
				$message = "<br><br><font class=\"pn-failed\">$dbname " . _MAKE_DB_2 . "</font>";
			        echo $message;
				die();
			}
		}

		//$result = mysql_query("CREATE DATABASE $dbname DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci") or die (_MAKE_DB_1);
        
    } 
    else 
    {
        echo "<font class=\"pn-failed\">" . _MAKE_DB_3 . "</font>";
    } 

	if ($dbtype=="mysql") {
	    include("install/mysql_tables.php");
	}
	else if ($dbtype == "oci8") {
		include("install/oracle_tables.php");
	}
} 

/**
 * This function inserts the default data on new installs
 */
function input_data($dbhost, $dbuname, $dbpass, $dbname, $prefix, $dbtype, $aid, $name, $pwd, $repeatpwd, $email)
{
    if ($pwd != $repeatpwd) {
        echo _PWBADMATCH;
        exit;
    } else {
        echo "<font class=\"pn-title\">" . _INPUT_DATA_1 . "</font>";

        echo "<center>";
        global $dbconn;
/*
		if ($dbtype == "mysql") {
			mysql_connect($dbhost, $dbuname, $dbpass);
		    mysql_select_db("$dbname") or die ("<br><font class=\"pn-sub\">" . _NOTSELECT . "</font>"); 
		}
		else if ($dbtype == "oci8") {
//			    $db = ADONewConnection($dbtype);
//				$dbconn= $db->Connect($dbhost, $dbuname, $dbpass, $dbname);
		}
*/
        // Put basic information in first
        include("install/newdata.php"); 
        // new installs will use md5 hashing - compatible on windows and *nix variants.
        $pwd = md5($pwd);
        $pwd1 = md5('instructor');
        $pwd2 = md5('student');
        $pwd3 = md5('instructor2');
		$result = $dbconn->Execute("INSERT INTO " . $prefix . "_users VALUES ( '1', '$name', '$aid', '$email', " . time() . ", '$pwd', '', '000000', '1', '', '1','0')") or die ("<b>" . _NOTUPDATED . $prefix . "_users</b>");
        $result = $dbconn->Execute("INSERT INTO " . $prefix . "_users VALUES ( '2', 'instructor', 'instructor', '', " . time() . ", '$pwd1', '', '000001', '1', '', '1','0')") or die ("<b>" . _NOTUPDATED . $prefix . "_users</b>");
        $result = $dbconn->Execute("INSERT INTO " . $prefix . "_users VALUES ( '3', 'student', 'student', '', " . time() . ", '$pwd2', '', '000002', '1', '', '1','0')") or die ("<b>" . _NOTUPDATED . $prefix . "_users</b>");
        $result = $dbconn->Execute("INSERT INTO " . $prefix . "_users VALUES ( '4', 'instructor2', 'instructor2', '', " . time() . ", '$pwd3', '', '000003', '1', '', '1','0')") or die ("<b>" . _NOTUPDATED . $prefix . "_users</b>");
        echo "<br><font class=\"pn-sub\">" . $prefix . "_users" . _UPDATED . "</font>"; 

		// group_membership table
		$table = $prefix."_group_membership";
		$result = $dbconn->Execute("INSERT INTO $table VALUES ('1','1')") or die ("<b>"._NOTUPDATED. " $table</b>");
		$result = $dbconn->Execute("INSERT INTO $table VALUES ('2','2')") or die ("<b>"._NOTUPDATED. " $table</b>");
		$result = $dbconn->Execute("INSERT INTO $table VALUES ('4','3')") or die ("<b>"._NOTUPDATED. " $table</b>");
		$result = $dbconn->Execute("INSERT INTO $table VALUES ('2','4')") or die ("<b>"._NOTUPDATED. " $table</b>");
    } 
} 

?>