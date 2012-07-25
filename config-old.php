<?php

if (eregi("config.php",$_SERVER['PHP_SELF'])) {
      die ("You can't access this file directly...");
}

// ----------------------------------------------------------------------
// Database & System Config
//
//      dbtype:     type of database, currently only mysql
//      dbhost:     MySQL Database Hostname
//      dbuname:    MySQL Username
//      dbpass:     MySQL Password
//      dbname:     MySQL Database Name
//      system:     0 for Unix/Linux, 1 for Windows
//      encoded:    0 for MySQL information unenccoded
//                  1 for encoded
//$config['dbuname'] = 'cm9vdA==';
//$config['dbpass'] = 'bmVjdGVj';
// ----------------------------------------------------------------------
//
$config['prefix'] = 'ln';
$config['dbtype'] = 'mysql';
$config['dbhost'] = 'localhost';
$config['dbuname'] = 'cm9vdA==';
$config['dbpass'] = '';
$config['dbname'] = '';
$config['system'] = '1';
$config['encoded'] = '1';
$config['defaultlang'] = 'tha';

//define('_CHARSET1', 'tis-620');
define('_CHARSET1', 'UTF-8');
$config['chatserver'] = 'localhost';

//Config LDAP
$config['isUse'] = '0';
$config['ldapserver'] = 'localhost';
$config['ldapsitename'] = 'sis';
$config['sitesuffix'] = 'com';
$config['ou'] = 'people';
?>
