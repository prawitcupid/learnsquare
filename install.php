<?php

/*	Allows Postnuke to work with register_globals set to off 
 *	Patch for php 4.2.x or greater
 */

if (phpversion() >= "4.3.0") {
    if ( ini_get('register_globals') != 1 ) {
        $supers = array('_REQUEST',
                        '_ENV', 
                        '_SERVER', 
                        '_POST', 
                        '_GET', 
                        '_COOKIE', 
                        '_SESSION', 
                        '_FILES', 
                        '_GLOBALS' );
        				
        foreach( $supers as $__s) {
            if ( ( isset( $$__s ) ) && ( is_array( $$__s ) == true )) {
                extract( $$__s, EXTR_OVERWRITE );
            }
        }
        unset($supers);
    }
} else {
    if ( ini_get('register_globals') != 1 ) {
        $supers = array('HTTP_POST_VARS', 
                        'HTTP_GET_VARS', 
                        'HTTP_COOKIE_VARS', 
                        'GLOBALS', 
                        'HTTP_SESSION_VARS', 
                        'HTTP_REQUEST_VARS', 
                        'HTTP_SERVER_VARS', 
                        'HTTP_ENV_VARS' );
        
        foreach( $supers as $__s) {
            if ( is_array( $$__s ) == true ) {
                extract( $$__s, EXTR_OVERWRITE );
            }
        }
        unset($supers);
    }
}


@set_time_limit(0);

// ADODB configuration
include "adodb/adodb.inc.php";

//ini_set('register_globals', 'On');

if (isset($alanguage)) {
    $currentlang = $alanguage;
}

if(!isset($prefix)) {
    include_once 'config.php';
    $prefix = $config['prefix'];
    $dbtype = $config['dbtype'];
    //$dbtabletype = $config['dbtabletype'];
    $dbhost = $config['dbhost'];
    $dbuname = $config['dbuname'];
    $dbpass = $config['dbpass'];
    $dbname = $config['dbname'];
    $system = $config['system'];
    $encoded = $config['encoded'];   
}

if (!empty($encoded)) {
    // Decode username and password
    $dbuname = base64_decode($dbuname);
    $dbpass = base64_decode($dbpass);
}

$config['prefix'] = $prefix;
include_once 'lntables.php';
include_once 'install/language.php'; // functions for multilanguage support

installer_get_language();

include_once 'install/newinstall.php'; // functions for new installs
include_once 'install/modify_config.php'; // functions to modify config.php
include_once 'install/check.php'; // functions for various checks
include_once 'install/gui.php'; // functions for rendering the gui
include_once 'install/db.php'; // functions for accessing the db

print_header();

switch(@$op) {
  
  case "Finish":
         print_finish();
         break;
		
    case "Set Login":
         $dbconn = dbconnect($dbhost, $dbuname, $dbpass, $dbname, $dbtype);
         input_data($dbhost, $dbuname, $dbpass, $dbname, $prefix, $dbtype, $aid, $name, $pwd, $repeatpwd, $email);
         update_config_php(true); // Scott - added
         print_set_login();
         break;
	
	case "Continue":
         print_continue();
         break;
   
   case "Start":
         if(!isset($dbmake)) {
            $dbmake = false;
         }
	 	 make_db($dbhost, $dbuname, $dbpass, $dbname, $prefix, $dbtype, $dbmake, $dbtabletype);
         print_start();
         break;

	case _BTN_NEWINSTALL:
         print_new_install();
         break;
	 
	 case _BTN_CHANGEINFO:
         print_change_info();
         break;
	
	case "Submit":
         print_submit();
         break;
	
	case "CHM_check":
         print_CHM_check();
         break;

    case "Check":
        do_check_php();       
	do_check_chmod();
        break;

    case "Select Language":
         print_select_language();
         break;

    case "Set Language":
         $currentlang = $alanguage;
         print_default();
         break;

    default:
        print_select_language();
         break;
}

print_footer();
?>
