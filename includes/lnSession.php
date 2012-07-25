<?php
/**
 * Set up session handling
 *
 * Set all PHP options for LearningNuke session handling
 */
function lnSessionSetup()
{
    //global $_SERVER;

	list($rememberme) = lnVarCleanFromInput('rememberme');

    $path = lnGetBaseURI();
    if (empty($path)) {
        $path = '/';
    }
    $host = $_SERVER['HTTP_HOST'];
    if (empty($host)) {
        $host = getenv('HTTP_HOST');
    }
    $host = preg_replace('/:.*/', '', $host);

	// PHP configuration variables

    // Stop adding SID to URLs
    ini_set('session.use_trans_sid', 0);

    // User-defined save handler
//    ini_set('session.save_handler', 'user');

    // How to store data
    ini_set('session.serialize_handler', 'php');

    // Use cookie to store the session ID
//	if ($rememberme) {
	    ini_set('session.use_cookies', 1);
//	}
//	else { 
//	    ini_set('session.use_cookies', 0);
//	}
    // Name of our cookie
    ini_set('session.name', 'LNSESSID');

    // Lifetime of our cookie
    $seclevel = lnConfigGetVar('seclevel');

    switch ($seclevel) {
        case 'High':
            // Session lasts duration of browser
            $lifetime = 0;
            // Referer check
            //ini_set('session.referer_check', "$host$path");
            ini_set('session.referer_check', "$host");
            break;
        case 'Medium':
            // Session lasts set number of days
            $lifetime = lnConfigGetVar('secmeddays') * 86400;
            break;
        case 'Low':
            // Session lasts unlimited number of days (well, lots, anyway)
            // (Currently set to 25 years)
            $lifetime = 788940000;
            break;
    }
    ini_set('session.cookie_lifetime', $lifetime);
    
    if (lnConfigGetVar('intranet') == false) {
        // Cookie path
        ini_set('session.cookie_path', $path);

        // Cookie domain
        // only needed for multi-server multisites - adapt as needed
        //$domain = preg_replace('/^[^.]+/','',$host);
        //ini_set('session.cookie_domain', $domain);
    }

    // Garbage collection
    ini_set('session.gc_probability', 1);

    // Inactivity timeout for user sessions
    ini_set('session.gc_maxlifetime', lnConfigGetVar('secinactivemins') * 60);

    // Auto-start session
    ini_set('session.auto_start', 0);///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // Session handlers
	/*
    session_set_save_handler("pnSessionOpen",
                             "pnSessionClose",
                             "pnSessionRead",
                             "pnSessionWrite",
                             "pnSessionDestroy",
                             "pnSessionGC");
	*/

    return true;
}

/**
 * Initialise session
 */
function lnSessionInit()
{
    //global $_SERVER;

    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    // First thing we do is ensure that there is no attempted pollution
    // of the session namespace
	//echo "<pre>";
  /*  foreach($GLOBALS as $k=>$v) {
		//print_r($GLOBALS);
        if (preg_match('/^PNSV/', $k)) {
            return false;
        }
    }*/
	//	echo "</pre>";

    // Kick it
	//if(!session_is_registered("PNSVuid"))
	    session_start();

    // Have to re-write the cache control header to remove no-save, this
    // allows downloading of files to disk for application handlers
    // adam_baum - no-cache was stopping modules (andromeda) from caching the playlists, et al.
    // any strange behaviour encountered, revert to commented out code.
    Header('Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0');
    //Header('Cache-Control: cache');

    $sessid = session_id();

    // Get (actual) client IP addr
    $ipaddr = $_SERVER['REMOTE_ADDR'];
    if (empty($ipaddr)) {
        $ipaddr = getenv('REMOTE_ADDR');
    }
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddr = $_SERVER['HTTP_CLIENT_IP'];
    }
    $tmpipaddr = getenv('HTTP_CLIENT_IP');
    if (!empty($tmpipaddr)) {
        $ipaddr = $tmpipaddr;
    }
    if  (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddr = preg_replace('/,.*/', '', $_SERVER['HTTP_X_FORWARDED_FOR']);
    }
    $tmpipaddr = getenv('HTTP_X_FORWARDED_FOR');
    if  (!empty($tmpipaddr)) {
        $ipaddr = preg_replace('/,.*/', '', $tmpipaddr);
    }


    $sessioninfocolumn = &$lntable['session_info_column'];
    $sessioninfotable = $lntable['session_info'];

    $query = "SELECT $sessioninfocolumn[ipaddr]
              FROM $sessioninfotable
              WHERE $sessioninfocolumn[sessid] = '" . lnVarPrepForStore($sessid) . "'";

    $result = $dbconn->Execute($query);

    if($dbconn->ErrorNo() != 0) {
        return false;
    } 

    if (!$result->EOF) {
        $result->Close();
		lnSessionCurrent($sessid);
    } else {
        lnSessionNew($sessid, $ipaddr);
        
        // Generate a random number, used for
        // some authentication
        srand((double)microtime()*1000000);
        lnSessionSetVar('rand', rand());
    }

    return true;
}

/**
 * Continue a current session
 * @private
 * @param sessid the session ID
 */
function lnSessionCurrent($sessid)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $sessioninfocolumn = &$lntable['session_info_column'];
    $sessioninfotable = $lntable['session_info'];

    // Touch the last used time
    $query = "UPDATE $sessioninfotable
              SET $sessioninfocolumn[lastused] = " . time() . "
              WHERE $sessioninfocolumn[sessid] = '" . lnVarPrepForStore($sessid) . "'";

    $result = $dbconn->Execute($query);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    return true;
}

/**
 * Create a new session
 * @private
 * @param sessid the session ID
 * @param ipaddr the IP address of the host with this session
 */
function lnSessionNew($sessid, $ipaddr)
{
    list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $sessioninfocolumn = &$lntable['session_info_column'];
    $sessioninfotable = $lntable['session_info'];

    $query = "INSERT INTO $sessioninfotable
                 ($sessioninfocolumn[sessid],
                  $sessioninfocolumn[ipaddr],
                  $sessioninfocolumn[uid],
                  $sessioninfocolumn[firstused],
                  $sessioninfocolumn[lastused])
              VALUES
                 ('" . lnVarPrepForStore($sessid) . "',
                  '" . lnVarPrepForStore($ipaddr) . "',
                  0,
                  " . time() . ",
                  " . time() . ")";
                  
    $dbconn->Execute($query);


    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    return true;
}

/*
 * Session variables here are a bit 'different'.  Because they sit in the
 * global namespace we use a couple of helper functions to give them their
 * own prefix, and also to force users to set new values for them if they
 * require.  This avoids blatant or accidental over-writing of session
 * variables.
 *
*/

/**
 * Get a session variable
 *
 * @param name name of the session variable to get
 */
function lnSessionGetVar($name)
{
    /*global $HTTP_SESSION_VARS;

    $var = "PNSV$name";
	echo "bbbbb ::".$HTTP_SESSION_VARS[$var];
    global $$var;
    if (!empty($HTTP_SESSION_VARS[$var])) {
        return $HTTP_SESSION_VARS[$var];
    }*/
	
	$var = "PNSV".$name;
	if($name == 'uid')
	    //global $$var;
		if (!empty($_SESSION[$var])) 
		{
			return $_SESSION[$var];
		}
	if(isset($_SESSION["$var"]))	
		return $_SESSION["$var"];
}

/** 
 * Set a session variable
 * @param name name of the session variable to set
 * @param value value to set the named session variable
 */
function lnSessionSetVar($name, $value)
{
	/*global $HTTP_SESSION_VARS;
    	$var = "PNSV$name";

    	global $$var;
	$$var = $value;
	$HTTP_SESSION_VARS[$var] = $value;*/
	$var = "PNSV".$name;
	//if($name == 'uid')

    	//global $$var;
	//$$var = $value;
	//session_register($var);
	$_SESSION[$var] = "";
	if(isset($_SESSION[$var])) 
	{
		$_SESSION[$var] = $value;
    	return true;
	}
	else
		return false;
}

/**
 * Delete a session variable
 * @param name name of the session variable to delete
 */
function lnSessionDelVar($name)
{
    $var = "PNSV".$name;

   //global $$var;
	// Fix for PHP >4.0.6 By John Barnett (johnpb)
    //unset($$var);	
	//if($name == 'uid')
	unset($_SESSION[$var]); 
	//else
	//	unset($_SESSION[$var]);
   // session_unregister($var);
	  	
    return true;
}

?>