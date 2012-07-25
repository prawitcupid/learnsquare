<?php 
// File: $Id: modify_config.php,v 1.1.1.1 2007/02/22 03:17:41 mrmeaw Exp $ $Name: HEAD $
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
// Original Author of file: Scott Kirkwood (scott_kirkwood@bigfoot.com)
// Purpose of file: Routines to modify the config.php file.
// General routine modify_file() is useful in it's own right.
// ----------------------------------------------------------------------
// This is the last update to this script before the new version is finished.
// mod_file is general, give it a source file a destination.
// an array of search patterns (Perl style) and replacement patterns
// Returns a string which starts with "Err" if there's an error
function modify_file($src, $dest, $reg_src, $reg_rep)
{
    $in = @fopen($src, "r");
    if (! $in) {
        return _MODIFY_FILE_1 . " $src";
    } 
    $i = 0;
    while (!feof($in)) {
        $file_buff1[$i++] = fgets($in, 4096);
    } 
    fclose($in);

    $lines = 0; // Keep track of the number of lines changed
    
    while (list ($bline_num, $buffer) = each ($file_buff1)) {
        $new = preg_replace($reg_src, $reg_rep, $buffer);
        if ($new != $buffer) {
            $lines++;
        } 
        $file_buff2[$bline_num] = $new;
    } 

    if ($lines == 0) {
        // Skip the rest - no lines changed
        return _MODIFY_FILE_3;
    } 

    reset($file_buff1);
    $out_backup = @fopen($dest, "w");

    if (! $out_backup) {
        return _MODIFY_FILE_2 . " $dest";
    } while (list ($bline_num, $buffer) = each ($file_buff1)) {
        fputs($out_backup, $buffer);
    } 

    fclose($out_backup);

    reset($file_buff2);
    $out_original = fopen($src, "w");
    if (! $out_original) {
        return _MODIFY_FILE_2 . " $src";
    } while (list ($bline_num, $buffer) = each ($file_buff2)) {
        fputs($out_original, $buffer);
    } 

    fclose($out_original); 
    // Success!
    return "$src updated with $lines lines of changes, backup is called $dest";
} 
// Two global arrays
$reg_src = array();
$reg_rep = array();
// Setup various searches and replaces
// Scott Kirkwood
function add_src_rep($key, $rep)
{
    global $reg_src, $reg_rep; 
    // Note: /x is to permit spaces in regular expressions
    // Great for making the reg expressions easier to read
    // Ex: $pnconfig['sitename'] = stripslashes("Your Site Name");
    $reg_src[] = "/ \['$key'\] \s* = \s* stripslashes\( (\' | \") (.*) \\1 \); /x";
    $reg_rep[] = "['$key'] = stripslashes(\\1$rep\\1);"; 
    // Ex. $pnconfig['site_logo'] = "logo.gif";
    $reg_src[] = "/ \['$key'\] \s* = \s* (\' | \") (.*) \\1 ; /x";
    $reg_rep[] = "['$key'] = '$rep';"; 
    // Ex. $pnconfig['pollcomm'] = 1;
    $reg_src[] = "/ \['$key'\] \s* = \s* (\d*\.?\d*) ; /x";
    $reg_rep[] = "['$key'] = $rep;";
} 

function show_error_info()
{
    global $dbhost, $dbuname, $dbpass, $dbname, $prefix, $dbtype;

    echo "<br/><br/><b>" . _SHOW_ERROR_INFO_1 . "<br/>";
	echo <<< EOT
        <tt>
        \$config['dbtype'] = '$dbtype';<br/>
        \$config['dbtabletype'] = '$dbtabletype';<br/>
        \$config['dbhost']  = '$dbhost';<br/>
        \$config['dbuname'] = '$dbuname';<br/>
        \$config['dbpass'] = '$dbpass';<br/>
        \$config['dbname'] = '$dbname';<br/>
        \$config['prefix'] = '$prefix';<br/>
        </tt>
EOT;
} 
// Update the config.php file with the database information.
function update_config_php($db_prefs = false)
{
    global $reg_src, $reg_rep;
    global $dbhost, $dbuname, $dbpass, $dbname, $prefix, $dbtype, $dbtabletype;
    global $email, $url ;

    add_src_rep("dbhost", $dbhost);
    add_src_rep("dbuname", base64_encode($dbuname));
    add_src_rep("dbpass", base64_encode($dbpass));
    add_src_rep("dbname", $dbname);
    add_src_rep("prefix", $prefix);
    add_src_rep("dbtype", $dbtype);
    add_src_rep("dbtabletype", $dbtabletype);
    if (@strstr($_ENV["OS"], "Win")) {
        add_src_rep("system" , '1');
    } else {
        add_src_rep("system", '0');
    } 
    add_src_rep("encoded", '1');

    if ($email) {
        add_src_rep("adminmail", $email);
    } 
	
	/*add_src_rep("isUse", '0');
	add_src_rep("ldapserver", '');
	add_src_rep("sitename", '');
	add_src_rep("sitesuffix", '');
	add_src_rep("ou", '');*/
    
    $ret = modify_file("config.php", "config-old.php", $reg_src, $reg_rep);

    if (preg_match("/Error/", $ret)) {
        show_error_info();
    } 
} 

?>