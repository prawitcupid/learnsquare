<?php

if (phpversion() >= "5.2.0") {
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
			if ( (isset($$__s) == true) && (is_array( $$__s ) == true) ) extract( $$__s, EXTR_OVERWRITE );
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
                                'HTTP_SERVER_VARS',
                                'HTTP_ENV_VARS'
                                 );

		foreach( $supers as $__s) {
			if ( (isset($$__s) == true) && (is_array( $$__s ) == true) ) extract( $$__s, EXTR_OVERWRITE );
		}
		unset($supers);
	}
}

if (preg_match("/lnAPI.php/i",$_SERVER['PHP_SELF'])) {
   die ("You can't access this file directly...");
}

/* version */
define('_LN_VERSION_NUM', '5.0');

/*
 * State of modules
 */
define('_LNMODULE_STATE_UNINITIALISED', 1);
define('_LNMODULE_STATE_INACTIVE', 2);
define('_LNMODULE_STATE_ACTIVE', 3);
define('_LNMODULE_STATE_MISSING', 4);
define('_LNMODULE_STATE_UPGRADED', 5);

/*
 * Type of modules
 */
define('_LNMODULE_TYPE_CORE', 0);
define('_LNMODULE_TYPE_REQUIRE', 1);
define('_LNMODULE_TYPE_USER', 2);


define('_YES',1);
define('_NO',0);
define('_CORE',-1);

define('COURSE_DIR','courses');
define('_LESSONPREFIX','lesson');
define('_QUESTIONPREFIX','quiz');
define('ASSIGNMENT_DIR','assignments');

/*
* Core Groups
*/
define('_LNGROUP_NONE',0);
define('_LNGROUP_ADMIN',1);
define('_LNGROUP_INSTRUCTOR',2);
define('_LNGROUP_TA',3);
define('_LNGROUP_STUDENT',4);


/*
* Study Types
*/
// value of study column in submission table
define('_LNSCHEDULE_BASED',1);
define ('_LNAUTOPASS',2);

// value of student  column in submission table
define('_LNSTUDENT_ENROLL',1);
define('_LNSTUDENT_USER',2);

// value of pre-test&post-test  column in submission table
define('_LNTEST_SHOWANS',1);
define('_LNTEST_REQUIRED',2);

// question
define('_LNSCORE',1);
define('_LNCHOICE',4);
define('_LNPASS_SCORE',60);					// % score pass
define('_LNSCHEDULE_LIMIT',30);			// show Enroll Course schedule limit in 30 days
define('_LNDEFUALT_RANDOM',5);		// shuffle pre/post-test
define('_LNDEFUALT_TIMELIMIT',30);	// quiz time limit

// break page
define('_LNBREAKPAGE1','{PAGE}');
define('_LNBREAKPAGE2','<!-- BREAK -->');
define('_LNBREAKPAGE3','<!--BREAK-->');
define('_LNBREAKPAGE4','{page}');

/*
* Study Status
*/
define('_LNSTATUS_STUDY',1);
define('_LNSTATUS_COMPLETE',2);
define('_LNSTATUS_DROP',3);
define('_LNSTATUS_FAIL',4);

/* Enroll Status*/
define('_LNSHOWNICKNAME',1);
define('_LNNOTIFY',2);

/*message type*/
define ('_MESSAGESEND',1);
define ('_MESSAGEVIEW',5);
define ('_MESSAGEREAD',0);
define ('_MESSAGESAVE',3);
define ('_MESSAGESENT',2);

/* quiz */
define ('_LNQUIZ_GRADE_MAX',1);
define ('_LNQUIZ_GRADE_AVG',2);
define ('_LNQUIZ_GRADE_LAST',3);

define ('LN_BLOCK_IMAGE_DIR','modules/Blocks/images/upload');

/**
 * Initialise LearningNuke
 *
 * Carries out a number of initialisation tasks to get LearningNuke up and running.
 * @returns void
 */
function lnInit()
{
    // proper error_repoting
    // e_all for development
    // error_reporting(E_ALL);
    // without warnings and notices for release
    error_reporting(E_ALL & ~E_DEPRECATED);

    // Hack for some weird PHP systems that should have the
    // LC_* constants defined, but don't
    if (!defined('LC_TIME')) {
        define('LC_TIME', 'LC_TIME');
    }

    // ADODB configuration
    include_once("adodb/adodb.inc.php");

	// Initialise and load configuration
    global $config;
    $config = array();
    include "config.php";
    	

  // Initialise and load lntables
    global $lntable;
    $lntable = array();
	if (file_exists('lntables.php')) {
        include 'lntables.php';
	}

    // Connect to database
    if (!lnDBInit()) {
        die('Database initialisation failed');
    }

 	// Build up $config array
    lnConfigInit();

	// Other includes
    include_once("includes/lnSession.php");
    include_once("includes/lnUser.php");

   // Start session
   if(function_exists("lnSessionSetup"))
	{
	    if (!lnSessionSetup()) {
			die('Session setup failed');
		}
	    if (!lnSessionInit()) {
		    die('Session initialisation failed');
		}
	}
    // Load global language defines
    $lang = lnConfigGetVar('language');    
    if (isset ($lang) && file_exists('language/' . lnVarPrepForOS($lang) . '/global.php')) {
        $currentlang = $lang;
	}
	else {
		$currentlang = $config['defaultlang'];
	}
	include 'language/' . lnVarPrepForOS($currentlang) . '/global.php';
   // Other other includes
    include 'includes/lnMod.php';
 
    include 'includes/lnBlocks.php';
	include 'includes/lnMail.php';
	include 'includes/lnTheme.php';
    include 'includes/queryutil.php';
    include 'includes/security.php';
	include 'includes/calc.php';
	include 'includes/lnCourses.php';
	include 'includes/filemanager.inc.php';
	include 'includes/lnXMLutil.php';
	include 'includes/lnPrvMsg.php';
	include 'includes/lnLog.php';
	include 'includes/lnExportExcel.php';
}

/**
 * Initialise Database
 *
 * @param none
 * @returns true|false
 * @return none
 */
function lnDBInit()
{
    // Get database parameters
    global $config;

	// Decode encoded DB parameters
    if ($config['encoded']) {
        $config['dbuname'] = base64_decode($config['dbuname']);
        $config['dbpass'] = base64_decode($config['dbpass']);
        $config['encoded'] = 0;
    }

	$dbtype = $config['dbtype'];
    $dbhost = $config['dbhost'];
    $dbname = $config['dbname'];
    $dbuname = $config['dbuname'];
    $dbpass = $config['dbpass'];

    // Database connection is a global (for now)
    global $dbconn;

    // Start connection
    $dbconn = ADONewConnection($dbtype);
	$dbconn->debug = false;
    $dbh = $dbconn->Connect($dbhost, $dbuname, $dbpass, $dbname);
    //echo $dbh;
    if (!$dbh || trim($dbname)=="") {
        //die("$dbtype://$dbuname:$dbpass@$dbhost/$dbname failed to connect" . $dbconn->ErrorMsg());
		//die("<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n<html>\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\">\n<title>LearningNuke powered Website</title>\n</head>\n<body>\n<center>\n<h1>Problem in Database Connection</h1>\n<br /><br />\n<h5>This Website is powered by LearningNuke</h5>\n<a href=\"http://php.weblogs.com/ADODB\" target=\"_blank\"><img src=\"images/powered/adodb2.gif\" alt=\"ADODB database library\" border=\"0\" hspace=\"10\" /></a><a href=\"http://www.php.net\" target=\"_blank\"><img src=\"images/powered/php2.gif\" alt=\"PHP Scripting Language\" border=\"0\" hspace=\"10\" /></a><br />\n<h5>Although this site is running the LearningNuke software<br />it has no other connection to the LearningNuke Developers.<br />Please refrain from sending messages about this site or its content<br />to the LearningNuke team, the end will result in an ignored e-mail.</h5>\n</center>\n</body>\n</html>");
		echo("<meta http-equiv='refresh' content='0;URL=install.php'>");
    }
	else
	{
		$dbconn->Execute("SET character_set_results=utf8");
		$dbconn->Execute("SET collation_connection = utf8_general_ci");
		$dbconn->Execute("SET NAMES 'utf8'");
	}
    global $ADODB_FETCH_MODE;
    $ADODB_FETCH_MODE = ADODB_FETCH_NUM;

    // force oracle to a consistent date format for comparison methods later on
    if (strcmp($dbtype, 'oci8') == 0) {
        $dbconn->Execute("alter session set NLS_DATE_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
    }

    return true;
}

/**
 * ready operating system output
 * <br>
 * Gets a variable, cleaning it up such that any attempts
 * to access files outside of the scope of the LearningNuke
 * system is not allowed
 * @param var variable to prepare
 * @param ...
 * @returns string/array
 * @return prepared variable if only one variable passed
 * in, otherwise an array of prepared variables
 */
function lnVarPrepForOS()
{
    static $search = array('!\.\./!si', // .. (directory traversal)
                           '!^.*://!si', // .*:// (start of URL)
                           '!/!si',     // Forward slash (directory traversal)
                           '!\\\\!si'); // Backslash (directory traversal)

    static $replace = array('',
                            '',
                            '_',
                            '_');

    $resarray = array();
    foreach (func_get_args() as $ourvar) {

        // Parse out bad things
        $ourvar = preg_replace($search, $replace, $ourvar);

        // Prepare var
        if (!get_magic_quotes_runtime()) {
            $ourvar = addslashes($ourvar);
        }

        // Add to array
        array_push($resarray, $ourvar);
    }

    // Return vars
    if (func_num_args() == 1) {
        return $resarray[0];
    } else {
        return $resarray;
    }
}


/**
 * ready databse output
 * <br>
 * Gets a variable, cleaning it up such that the text is
 * stored in a database exactly as expected
 * @param var variable to prepare
 * @param ...
 * @returns string/array
 * @return prepared variable if only one variable passed
 * in, otherwise an array of prepared variables
 */
function lnVarPrepForStore()
{
    $resarray = array();
    foreach (func_get_args() as $ourvar) {

        // Prepare var
        if (!get_magic_quotes_runtime()) {
            $ourvar = addslashes($ourvar);
        }

        // Add to array
        array_push($resarray, $ourvar);
    }

    // Return vars
    if (func_num_args() == 1) {
        return $resarray[0];
    } else {
        return $resarray;
    }
}

/**
 * get a configuration variable
 * @param name the name of the variable
 * @returns data
 * @return value of the variable, or false on failure
 */
function lnConfigGetVar($name)
{
    //global $config;

    if (isset($config[$name])) {
        $result = $config[$name];
    } else {
        /*
         * Fetch base data
         */
        list($dbconn) = lnDBGetConn();
        $lntable = lnDBGetTables();

        $table = $lntable['module_vars'];
        $columns = &$lntable['module_vars_column'];

        /*
         * Make query and go
         */
        $query = "SELECT $columns[value]
                  FROM $table
                  WHERE $columns[name]='" . lnVarPrepForStore($name) . "'";
        $dbresult = $dbconn->Execute($query);

        /*
         * In any case of error return false
         */
        if($dbconn->ErrorNo() != 0) {
            return false;
        }
        if ($dbresult->EOF) {
            $dbresult->Close();
            return false;
        }

        /*
         * Get data
         */
        list ($result) = $dbresult->fields;
        $result = unserialize(stripslashes($result));

        /*
         * Some caching
         */
        $config[$name] = $result;
        /*
         * That's all folks
         */
        $dbresult->Close();
    }
    return $result;
}

/**
 * set a configuration variable
 * @param name the name of the variable
 * @param value the value of the variable
 * @returns bool
 * @return true on success, false on failure
 */
function lnConfigSetVar($name, $value)
{
    /*
     * The database parameter are not allowed to change
     */
    if (empty($name) || ($name == 'dbtype') || ($name == 'dbhost') || ($name == 'dbuname') || ($name == 'dbpass')
            || ($name == 'dbname') || ($name == 'system') || ($name == 'prefix') || ($name == 'encoded')) {
        return false;
    }

    global $config;
    foreach($config as $k => $v) {
        if ($k == $name && $v == $value) {
            return true;
        }
    }

    /*
     * Fetch base data
     */
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
    $table = $lntable['module_vars'];
    $columns = &$lntable['module_vars_column'];

	/*
	 * Update
	 */
	 $query = "UPDATE $table
			   SET $columns[value]='" . lnVarPrepForStore(serialize($value)) . "'
			   WHERE $columns[name]='" . lnVarPrepForStore($name) . "'";

    $dbconn->Execute($query);
    if($dbconn->ErrorNo() != 0) {
        return false;
    }

    /*
     * Update my vars
     */
    $lnconfig[$name] = $value;

    return true;
}

/**
 * get all configuration variable into $config
 * @param none
 * @returns true|false
 * @return none
 */
function lnConfigInit() {
    global $config;

    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $table = $lntable['module_vars'];
    $columns = &$lntable['module_vars_column'];

    /*
     * Make query and go
     */
    $query = "SELECT $columns[name],
              $columns[value]
              FROM $table";

    $dbresult = $dbconn->Execute($query);
    if($dbconn->ErrorNo() != 0) {
        return false;
    }
    if ($dbresult->EOF) {
        $dbresult->Close();
        return false;
    }
    while(!$dbresult->EOF) {
        list($k, $v) = $dbresult->fields;
        $dbresult->MoveNext();
        if (($k != 'dbtype') && ($k != 'dbhost') && ($k != 'dbuname') && ($k != 'dbpass')
                && ($k != 'dbname')  && ($k != 'prefix') && ($k != 'defaultlang') && ($k != 'encoded')) {
            $v =@unserialize(stripslashes($v));
            $config[$k] = $v;
        }
    }
    $dbresult->Close();
    return true;
}

function lnDBGetConn()
{
    global $dbconn;

    return array($dbconn);
}

function lnDBGetTables()
{
    global $lntable;
    return $lntable;
}


/**
 * clean user input
 * 
 * Gets a global variable, cleaning it up to try to ensure that hack attacks don't work
 * @param var name of variable to get
 * @param ...
 * @returns string/array
 * @return prepared variable if only one variable passed
 * in, otherwise an array of prepared variables
 */
function lnVarCleanFromInput()
{
    $search = array('|</?\s*SCRIPT.*?>|si',
                    '|</?\s*FRAME.*?>|si',
                    '|</?\s*OBJECT.*?>|si',
                    '|</?\s*META.*?>|si',
                    '|</?\s*APPLET.*?>|si',
                    '|</?\s*LINK.*?>|si',
                    '|</?\s*IFRAME.*?>|si',
                    '|STYLE\s*=\s*"[^"]*"|si');

    $replace = array('');

    $resarray = array();
    foreach (func_get_args() as $var) {
        // Get var
        global $$var;
        if (empty($var)) {
            return;
        }
        $ourvar = $$var;

        if (!isset($ourvar)) {
            array_push($resarray, NULL);
            continue;
        }
        if (empty($ourvar)) {
            array_push($resarray, $ourvar);
            continue;
        }

        // Clean var
        if (get_magic_quotes_gpc()) {
            lnStripslashes($ourvar);
        }

		// Add to result array
        array_push($resarray, $ourvar);
    }

    // Return vars
    if (func_num_args() == 1) {
        return $resarray[0];
    } else {
        return $resarray;
    }
}

/**
 * strip slashes
 *
 * stripslashes on multidimensional arrays.
 * Used in conjunction with pnVarCleanFromInput
 * @access private
 * @param any variables or arrays to be stripslashed
 */
function lnStripslashes (&$value) {
    if(!is_array($value)) {
        $value = stripslashes($value);
    } else {
        array_walk($value,'pnStripslashes');
    }
}


function lnThemeLoad($thistheme) {
    static $loaded = 0;

    if ($loaded) {
        return true;
    }

    if (file_exists("themes/$thistheme/theme.php")) {
		include "themes/$thistheme/theme.php";
		$loaded = 1;
		return true;
	}
	else {
		$loaded = 0;
		return false;
	}
}


function error($msg) {
die("<!DOCTYPE html PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n<html>\n<head>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=ISO-8859-1\">\n<title>Error</title>\n</head>\n<body>\n<center>\n<h1>$msg</h1>\n<br /><br />\n<h5>This Website is powered by Learning Nuke</h5>\n <a href=\"http://php.weblogs.com/ADODB\" target=\"_blank\"><img src=\"images/powered/adodb2.gif\" alt=\"ADODB database library\" border=\"0\" hspace=\"10\" /></a><a href=\"http://www.php.net\" target=\"_blank\"><img src=\"images/powered/php2.gif\" alt=\"PHP Scripting Language\" border=\"0\" hspace=\"10\" /></a><br />\n<h5>Although this site is running the LearningNuke software<br />it has no other connection to the LearningNuke Developers.<br />Please refrain from sending messages about this site or its content<br />to the LearningNuke team, the end will result in an ignored e-mail.</h5>\n</center>\n</body>\n</html>");
}

/**
 * get base URI for LearningNuke
 * @returns string
 * @return base URI for LearningNuke
 */
function lnGetBaseURI()
{
    global $HTTP_SERVER_VARS;

    // Get the name of this URI

    // Start of with REQUEST_URI
    if (isset($HTTP_SERVER_VARS['REQUEST_URI'])) {
        $path = $HTTP_SERVER_VARS['REQUEST_URI'];
    } else {
        $path = getenv('REQUEST_URI');
    }
    if ((empty($path)) ||
        (substr($path, -1, 1) == '/')) {
        // REQUEST_URI was empty or pointed to a path
        // Try looking at PATH_INFO
        $path = getenv('PATH_INFO');
        if (empty($path)) {
            // No luck there either
            // Try SCRIPT_NAME
            if (isset($HTTP_SERVER_VARS['SCRIPT_NAME'])) {
                $path = $HTTP_SERVER_VARS['SCRIPT_NAME'];
            } else {
                $path = getenv('SCRIPT_NAME');
            }
        }
    }

    $path = preg_replace('/[#\?].*/', '', $path);
    $path = dirname($path);

    if (preg_match('!^[/\\\]*$!', $path)) {
        $path = '';
    }

    return $path;
}

/**
 * Carry out a redirect
 * @param the URL to redirect to
 * @returns void
 */
function lnRedirect($redirecturl)
{
    // Always close session before redirect
    if (function_exists('session_write_close')) {
        session_write_close();
    }

    if (preg_match('!^http!', $redirecturl)) {
        // Absolute URL - simple redirect
        Header("Location: $redirecturl");
        return;
    } else {
        // Removing leading slashes from redirect url
        $redirecturl = preg_replace('!^/*!', '', $redirecturl);

        // Get base URL
        $baseurl = lnGetBaseURL();

        Header("Location: $baseurl$redirecturl");
    }

}


/**
 * get base URL for LearningNuke
 * @returns string
 * @return base URL for LearningNuke
 */
function lnGetBaseURL()
{
    global $HTTP_SERVER_VARS;

    if (empty($HTTP_SERVER_VARS['HTTP_HOST'])) {
        $server = getenv('HTTP_HOST');
    } else {
        $server = $HTTP_SERVER_VARS['HTTP_HOST'];
    }
    // IIS sets HTTPS=off
    if (isset($HTTP_SERVER_VARS['HTTPS']) && $HTTP_SERVER_VARS['HTTPS'] != 'off') {
        $proto = 'https://';
    } else {
        $proto = 'http://';
    }

    $path = lnGetBaseURI();

    return "$proto$server$path/";
}

/**
 * ready user output
 * <br>
 * Gets a variable, cleaning it up such that the text is
 * shown exactly as expected
 * @param var variable to prepare
 * @param ...
 * @returns string/array
 * @return prepared variable if only one variable passed
 * in, otherwise an array of prepared variables
 */
function lnVarPrepForDisplay()
{
    // This search and replace finds the text 'x@y' and replaces
    // it with HTML entities, this provides protection against
    // email harvesters
    static $search = array('/(.)@(.)/se');

    static $replace = array('"&#" .
                            sprintf("%03d", ord("\\1")) .
                            ";&#064;&#" .
                            sprintf("%03d", ord("\\2")) . ";";');

    $resarray = array();
    foreach (func_get_args() as $ourvar) {

        // Prepare var
        $ourvar = htmlspecialchars($ourvar);
		$ourvar = stripslashes($ourvar);
        $ourvar = preg_replace($search, $replace, $ourvar);

        // Add to array
        array_push($resarray, $ourvar);
    }

    // Return vars
    if (func_num_args() == 1) {
        return $resarray[0];
    } else {
        return $resarray;
    }
}

// Languages List
function languagelist()  {
	$lang = array("eng"=>"English",
							"tha"=>"Thai");
	
	return $lang;
}

//JoeJae Config
function JoeGetSQLConfig($title)
{
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$JoeJae_configtable = $lntable['JoeJae_configtable'];
	$JoeJae_config_column = &$lntable['JoeJae_config_column'];

	$query = mysql_query('SELECT '. $JoeJae_config_column['value'] .' FROM '. $JoeJae_configtable .' WHERE '. $JoeJae_config_column['title'] .' = "'. $title .'" LIMIT 1');
	if(mysql_num_rows($query) <= 0)
	{
		return 2000;
	}
	else
	{
		list($value) = mysql_fetch_row($query);
		return $value;
	}
}
?>
