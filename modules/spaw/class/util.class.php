<?php
/**
 * SPAW Editor v.2 Utility classes
 * 
 * @package spaw2
 * @subpackage Util  
 * @author Alan Mendelevich <alan@solmetra.lt> 
 * @copyright UAB Solmetra
 */ 

/**
 * Variable access class
 * 
 * Returns values of variable from global arrays independent of PHP version and settings
 * @package spaw2
 * @subpackage Util
 */
class SpawVars
{
  /**
   * Returns GET variable value
   * @param string $var_name variable name
   * @param string $empty_value value to return if variable is empty
   * @returns string
   * @static   
   */              
  function getGetVar($var_name, $empty_value='')
  {
    global $HTTP_GET_VARS;
    if (!empty($_GET[$var_name]))
      return $_GET[$var_name];
    elseif (!empty($HTTP_GET_VARS[$var_name]))
      return $HTTP_GET_VARS[$var_name];
    else
      return $empty_value;
  }

  /**
   * Returns POST variable value
   * @param string $var_name variable name
   * @param string $empty_value value to return if variable is empty
   * @returns string
   * @static
   */      
  function getPostVar($var_name, $empty_value='')
  {
    global $HTTP_POST_VARS;
    if (!empty($_POST[$var_name]))
      return $_POST[$var_name];
    else if (!empty($HTTP_POST_VARS[$var_name]))
      return $HTTP_POST_VARS[$var_name];
    else
      return $empty_value;
  }
  
  /**
   * Returns FILES variable value
   * @param string $var_name variable name
   * @param string $empty_value value to return if variable is empty
   * @returns mixed
   * @static
   */      
  function getFilesVar($var_name, $empty_value='')
  {
    global $HTTP_POST_FILES;
    if (!empty($_FILES[$var_name]))
      return $_FILES[$var_name];
    else if (!empty($HTTP_POST_FILES[$var_name]))
      return $HTTP_POST_FILES[$var_name];
    else
      return $empty_value;
  }
  
  /**
   * Returns SERVER variable value
   * @param string $var_name variable name
   * @param string $empty_value value to return if variable is empty
   * @returns string
   * @static
   */      
  function getServerVar($var_name, $empty_value='')
  {
    global $HTTP_SERVER_VARS;
    if (!empty($_SERVER[$var_name]))
      return $_SERVER[$var_name];
    else if (!empty($HTTP_SERVER_VARS[$var_name]))
      return $HTTP_SERVER_VARS[$var_name];
    else
      return $empty_value;
  }

  /**
   * Returns SESSION variable value
   * @param string $var_name variable name
   * @param string $empty_value value to return if variable is empty
   * @returns string
   * @static
   */      
  function getSessionVar($var_name, $empty_value='')
  {
    global $HTTP_SESSION_VARS;
    if (!empty($_SESSION[$var_name]))
      return $_SESSION[$var_name];
    else if (!empty($HTTP_SESSION_VARS[$var_name]))
      return $HTTP_SESSION_VARS[$var_name];
    else
      return $empty_value;
  }

  /**
   * Sets SESSION variable value
   * @param string $var_name variable name
   * @param string $value value to set
   * @static
   */      
  function setSessionVar($var_name, $value='')
  {
    global $HTTP_SESSION_VARS;
    if (isset($_SESSION))
      $_SESSION[$var_name] = $value;
    else if (isset($HTTP_SESSION_VARS))
      $HTTP_SESSION_VARS[$var_name] = $value;
  }
  
  /**
   * Strips slashes from variable if magic_quotes is on
   * @param string $var variable
   * @returns string
   * @static   
   */              
  function stripSlashes($var)
  {
    if (get_magic_quotes_gpc()) {
      return stripslashes($var);
    }
    return $var;
  }

}     

/**
 * Usupported browser
 */ 
define("SPAW_AGENT_UNSUPPORTED", 0);
/**
 * Microsoft Internet Explorer for Windows version 5.5 or higher
 */ 
define("SPAW_AGENT_IE", 15);
/**
 * Gecko based browser with engine built on 2003-03-12 or later
 */ 
define("SPAW_AGENT_GECKO", 240);
/**
 * Opera 9 or higher
 */
define("SPAW_AGENT_OPERA", 3840); 
/**
 * Safari 3 or higher
 */ 
define("SPAW_AGENT_SAFARI", 61440);
/**
 * All supported browsers
 */ 
define("SPAW_AGENT_ALL", 65535);

/**
 * Provides itformation about current user agent (browser)
 * @package spaw2
 * @subpackage Util
 */   
class SpawAgent
{
  /**
   * Returns constant representing user agent (browser) in SPAW terms
   * @returns integer
   * @static
   * @see SPAW_AGENT_UNSUPPORTED, SPAW_AGENT_IE, SPAW_AGENT_GECKO          
   */     
  function getAgent()
  {
  	$spawvar = new SpawVars();
    $result = SPAW_AGENT_UNSUPPORTED;
    $browser = $spawvar->GetServerVar('HTTP_USER_AGENT');
    //echo $browser."<br>";
    // check if msie
    if (preg_match("/MSIE[^;]*/i",$browser,$msie))
    {
      // get version 
      if (preg_match("/[0-9]+\.[0-9]+/i",$msie[0],$version))
      {
        // check version
        if ((float)$version[0]>=5.5)
        {
          // finally check if it's not opera impersonating ie
          if (!preg_match("/opera/i",$browser))
          {
            $result = SPAW_AGENT_IE;
          }
        }
      }
    }
    elseif (preg_match("/Gecko\/[0-9]*/i",$browser,$build))
    { 	//echo "Gecko";
    	$build = substr($build[0],6);
      // build date of Mozilla version 1.3 is 20030312
      if ($build > "20030312")
        $result = SPAW_AGENT_GECKO;
    }
  	elseif (preg_match("/Firefox\/[0-9]*/i",$browser,$firefox))
    { 	//echo "Firefox";
    	$firefox = substr($firefox[0],8);
      // build date of Mozilla version 2
      if ($firefox > "2")
        $result = SPAW_AGENT_GECKO;
    }
    elseif (preg_match("/Opera\/[0-9]*/i", $browser, $opera))
    {
      if ((float)$opera[1] >= 9)
        $result = SPAW_AGENT_OPERA;
    }
   elseif (preg_match("/Chrome\/[0-9]*/i", $browser, $chrome))
    {
      // safari build 500 or higher (safari 3 or newer)
      $chrome = substr($chrome[0],7);
      if ((float)$chrome >= 5)
        $result = SPAW_AGENT_GECKO;
    }
    elseif (preg_match("/Safari\/[0-9]*/i", $browser, $safari))
    {
      // safari build 500 or higher (safari 3 or newer)
      $safari = substr($safari[0],7);
      if ((float)$safari >= 500)
        $result = SPAW_AGENT_SAFARI;
    }
  	//echo $result;
    return $result;
  }
  
  /**
   * Returns string representation of current user agent to be used as part of file extension or dir name
   * @returns string   
   * @static
   */        
  function getAgentName()
  {
    $result = '';
    $spawagent = new SpawAgent();
    switch($spawagent->getAgent())
    {
      case SPAW_AGENT_IE:
        $result = 'ie';
        break;
      case SPAW_AGENT_GECKO:
        $result = 'gecko';
        break;
      case SPAW_AGENT_OPERA:
        $result = 'opera';
        break;
      case SPAW_AGENT_SAFARI:
        $result = 'safari';
        break;
      default:
        $result = '';
        break;
    }
    return $result;
  }
} 

?>
