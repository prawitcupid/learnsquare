<?
/*
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Blocls::', "::$bid", ACCESS_READ)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
		return false;
}
*/
/*
 Module : CHATROOM
 Create on : 10/01/5
 By : 
 */

/*
 Module : RSS
 Create on : 29/06/49
 By : Orrawin
 */

if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - MAIN- - - - - */
//$vars= array_merge($GLOBALS['HTTP_GET_VARS'],$GLOBALS['HTTP_POST_VARS']);
$vars = array_merge($_POST,$_GET);
include 'header.php';

/** Navigator **/
$menus = $links = array();
if (lnUserAdmin( lnSessionGetVar('uid'))) {
	$menus[] = _ADMINMENU;
	$links[]='index.php?mod=Admin';
}

$menus[]= _CHATROOMMENU;
$links[]= 'index.php?mod=JoeJae&amp;file=admin';
/** Navigator **/

/** Navigator **/
lnBlockNav($menus,$links);
/** Navigator **/


if (!empty($op)) {
	// include more functions
	switch($op) {
		case "jor_config": 
			 JoeTakeConfig($vars);
			 return;
		default: JoeShowConfig($vars);
	}
}
else
{
	JoeShowConfig($vars);
}
include 'footer.php';

function JoeTakeConfig($vars) {
	extract($vars);
	
	$error = false;

	if(isset($vars['joe_refresh_delay']))
	{
		if(!ctype_digit($vars['joe_refresh_delay']))
		{
			$error = true;
		}
	}
	else
	{
		$error = true;
	}
	if(isset($vars['joe_disconnect_delay']))
	{
		if(!ctype_digit($vars['joe_disconnect_delay']))
		{
			$error = true;
		}
	}
	else
	{
		$error = true;
	}
	if(isset($vars['joe_userlist_delay']))
	{
		if(!ctype_digit($vars['joe_userlist_delay']))
		{
			$error = true;
		}
	}
	else
	{
		$error = true;
	}
	if($error)
	{
		echo '<br /><center><font color="red">Error your values must be only Number.</font></center>';
	}
	else
	{
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
		$JoeJae_configtable = $lntable['JoeJae_configtable'];
		$JoeJae_config_column = &$lntable['JoeJae_config_column'];
		
		JoeGetSQLUpdate('refresh_delay', $vars['joe_refresh_delay']);
		JoeGetSQLUpdate('disconnect_delay', $vars['joe_disconnect_delay']);
		JoeGetSQLUpdate('userlist_delay', $vars['joe_userlist_delay']);

		echo '<br /><center><font color="red">Update successfully.</font></center>';
	}
	JoeShowConfig($vars);
}

function JoeGetSQLUpdate($title, $value)
{
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$JoeJae_configtable = $lntable['JoeJae_configtable'];
	$JoeJae_config_column = &$lntable['JoeJae_config_column'];

	$query = mysql_query('UPDATE '. $JoeJae_configtable .' SET '. $JoeJae_config_column['value'] .' = "'. $value .'" WHERE '. $JoeJae_config_column['title'] .' = "'. $title .'" LIMIT 1');
}

function JoeShowConfig($vars) {
?>
<form id="form1" name="form1" method="post" action="index.php?mod=JoeJae&file=admin&op=jor_config">
  <table width="100%" height="350" border="0" cellspacing="0" cellpadding="1">
    <tr>
      <td bgcolor="#000000"><table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
        <tr>
          <td width="2%" height="31">&nbsp;</td>
          <td colspan="1">
          <?php
          echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=JoeJae&file=admin"><B>'._CHATROOMCONFIG.'</B></A><BR>';
          ?>
          </td>
        </tr>
        <tr>
          <td >&nbsp;</td>
          <td width="31%" align="right">Chat Box refresh Delay (ms): </td>
          <td width="1%">&nbsp;</td>
          <td width="66%"><input name="joe_refresh_delay" type="text" id="joe_refresh_delay" value="<?php echo JoeGetSQLConfig('refresh_delay'); ?>" /></td>
        </tr>
        <tr>
          <td >&nbsp;</td>
          <td align="right">Disconnect Delay (sec): </td>
          <td>&nbsp;</td>
          <td><input name="joe_disconnect_delay" type="text" id="joe_disconnect_delay" value="<?php echo JoeGetSQLConfig('disconnect_delay'); ?>" /></td>
        </tr>
        <tr>
          <td >&nbsp;</td>
          <td align="right">Userlist Update Delay (ms): </td>
          <td>&nbsp;</td>
          <td><input name="joe_userlist_delay" type="text" id="joe_userlist_delay" value="<?php echo JoeGetSQLConfig('userlist_delay'); ?>"  /></td>
        </tr>
        <tr>
          <td >&nbsp;</td>
          <td align="right">&nbsp;</td>
          <td>&nbsp;</td>
          <td><input type="submit" name="Submit" value="Update" class="button" /></td>
        </tr>
      </table></td>
    </tr>
  </table>
</form>
<?
}
?>