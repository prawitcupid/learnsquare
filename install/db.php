<?php 
// File: $Id: db.php,v 1.1.1.1 2007/02/22 03:17:41 mrmeaw Exp $ $Name: HEAD $
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
// Original Author of file:  Gregor J. Rothfuss
// Purpose of file: Provide common db functions for the installer.
// ----------------------------------------------------------------------
/**
 * Connect to Database
 */
function dbconnect($dbhost, $dbuname, $dbpass, $dbname, $dbtype = 'mysql')
{
    $connectString = "$dbtype://$dbuname:$dbpass@$dbhost/$dbname";

    GLOBAL $ADODB_FETCH_MODE;
    $dbconn = ADONewConnection($dbtype);
    $dbh = $dbconn->Connect($dbhost, $dbuname, $dbpass, $dbname);
    $ADODB_FETCH_MODE = ADODB_FETCH_NUM; 
    // if we get an error, log it and die
    if ($dbh === false) {
        error_log ("connect string: $connectString");
        error_log ("error: " . $dbconn->ErrorMsg()); 
        // show error and die
        PN_DBMsgError($dbconn, __FILE__ , __LINE__, "Error connecting to db");
    } else {
        return $dbconn;
    } 
} 

/**
 * Error message due a ADODB SQL error and die (copied from mainfile.php because it is not included
 */
function PN_DBMsgError($db = '', $prg = '', $line = 0, $message = 'Error accessing the database')
{
    $lcmessage = $message . "<br>" . "Program: " . $prg . " - " . "Line N.: " . $line . "<br>" . "Database: " . $db->database . "<br> ";

    if ($db->ErrorNo() <> 0) {
        $lcmessage .= "Error (" . $db->ErrorNo() . ") : " . $db->ErrorMsg() . "<br>";
    } 
    die($lcmessage);
} 

?>