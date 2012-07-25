<?
/**
 * Logging
 */
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

$vars = array_merge($_POST,$_GET);
include 'header.php';

?>
<script language="javaScript">
		function formSubmit() {
			if(checkFields()) document.forms.Log.submit();
		}
    	function checkFields() {
			var title = document.forms.Log.logdate.value;
			
			if (title  == "" ) {
				alert("ใส่ค่า log ด้วย");
				document.forms.Log.logdate.focus();
				return false;
			}
			return true; 
		}


</script>
<?

if (!empty($op)) {
	// include more functions
	switch($op) {
		case "view_log": viewLog($vars); return;
		case "add_log_form" : addLogForm(); return;
		case "add_log" : addLog($vars); return;
		case "count_time" : countTime($vars); return;
	}
}

/** Navigator **/
$menus= array(_ADMINMENU,_LOGADMIN);
$links=array('index.php?mod=Admin','index.php?mod=Log&amp;file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<BR><TABLE WIDTH=100%>'
.'<TR ALIGN=CENTER>';

if (lnSecAuthAction(0, 'Log::', "view::", ACCESS_READ)) {
	echo '<TD ALIGN=CENTER><a href=index.php?mod=Log&file=admin&op=view_log>'.lnBlockImage('Log','view').'<BR><B>'._VIEWLOG.'</B></a> </TD> ';
}
if (lnSecAuthAction(0, 'Log::', "time::", ACCESS_READ)) {
	echo '<TD ALIGN=CENTER><a href=index.php?mod=Log&file=admin&op=count_time>'.lnBlockImage('Log','time').'<BR><B>'._SHOWTIME.'</B></a> </TD> ';
}
if (lnSecAuthAction(0, 'Log::', "set::", ACCESS_READ)) {
	echo '<TD ALIGN=CENTER><a href=index.php?mod=Log&file=admin&op=add_log_form>'.lnBlockImage('Log','set').'<BR><B>'._LOGCONFIG.'</B></a> </TD> ';
}

echo '</TR></TABLE>';

CloseTable();

///viewlog//////
function viewLog($vars){
	extract($vars);

	/** Navigator **/
	$menus= array(_ADMINMENU,_LOGADMIN,_VIEWLOG);
	$links= array('index.php?mod=Admin','index.php?mod=Log&amp;file=admin','#');
	lnBlockNav($menus,$links);
	/** Navigator **/

	OpenTable();

	//echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Log&amp;file=admin"><B>'._LOGADMIN.'</B></A><BR>&nbsp;';

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$user_logtable = $lntable['user_log'];
	$user_logcolumn = &$lntable['user_log_column'];

	$pagesize = lnConfigGetVar('pagelog');
	$sorting="atime DESC";

	if (!isset($page)) {
		$page = 1;
	}

	$min = $pagesize * ($page - 1); // This is where we start our record set from
	$max = $pagesize; // This is how many rows to select

	if (!empty ($filter)) {
		$where .= " UPPER($user_logcolumn[event]) LIKE UPPER('".lnVarPrepForStore($filter)."%')";
	}

	$myquery = buildSimpleQuery('user_log', array('uid', 'atime', 'event', 'ip'), $where, lnVarPrepForStore($sorting), $max, $min);

	$result = $dbconn->Execute($myquery);

	echo '<table cellpadding =1 cellspacing=0 width=100% border=0>';
	echo '<tr><td colspan=4 height=20><B>Show: </B><A HREF="index.php?mod=Log&amp;file=admin&amp;op=view_log&amp;filter=">All</A> | <A 	HREF="index.php?mod=Log&amp;file=admin&amp;op=view_log&amp;filter=login">Login</A> | <A HREF="index.php?mod=Log&amp;file=admin&amp;op=view_log&amp;filter=logout">Logout</A><HR></td></tr>';
	while(list($uid,$atime,$event,$ip) = $result->fields) {
		$result->MoveNext();
		$stime = date('d-M-Y H:i', $atime);
		$user = lnUserGetVars($uid);
		@$uname = empty($user[uname]) ? '<CENTER>-</CENTER>' : $user[uname];
		$ip = empty($ip) ? '...' : $ip;
		echo "<tr><td width=100 align=center><FONT SIZE=1 COLOR=#999999>$stime</FONT></td><td width=50 align=center>[$ip]</td><td width=30 align=right>$uname</td><td>: $event</td></tr>";
	}
	echo '</table>';

	// Show pages
	if (!empty($where)) {
		$where = " WHERE $where";
	} else {
		$where = '';
	}

	$count = "SELECT COUNT($user_logcolumn[uid]) FROM $user_logtable ";

	$resultcount = $dbconn->Execute($count . $where);
	list ($numrows) = $resultcount->fields;
	$resultcount->Close();

	echo "<HR><center><BR>";

	if ($numrows  > $pagesize) {
		$total_pages = ceil($numrows / $pagesize); // How many pages are we dealing with here ??
		$prev_page = $page - 1;
		if ( $prev_page > 0 ) {
			echo '[&lt;&lt;<A HREF="index.php?mod=Log&amp;file=admin&amp;op=view_log&amp;filter='.$filter.'&amp;page='.$prev_page.'&amp;sorting='.$sorting.'">Back</A>] ';
		}
		for($n=1; $n <= $total_pages; $n++) {
			if ($n == $page) {
				echo "<B>$n</B> ";
			}
			else {
				echo '<A HREF="index.php?mod=Log&amp;file=admin&amp;op=view_log&amp;filter='.$filter.'&amp;page='.$n.'&amp;sorting='.$sorting.'">'.$n.'</A> ';
			}
		}
		$next_page = $page + 1;
		if ( $next_page <= $total_pages ) {
			echo ' [<A HREF="index.php?mod=Log&amp;file=admin&amp;op=view_log&amp;letter='.$filter.'&amp;page='.$next_page.'&amp;sorting='.$sorting.'">Next</A>&gt;&gt;]';
		}
	}

	echo "</center>";
	//echo "<BR>"._TOTALEVENTS.'&nbsp;'.$totalevents."<BR>";
	//echo "<BR><BR>";


	CloseTable();
	include 'footer.php';
}

/////add log form///////
function addLogForm(){

	$menus= array(_ADMINMENU,_LOGADMIN,_LOGCONFIG);
	$links=array('index.php?mod=Admin','index.php?mod=Log&amp;file=admin','#');
	lnBlockNav($menus,$links);

	OpenTable();

	echo '<BR><center><fieldset><legend>Log</legend>'
	.'<TABLE WIDTH="580" CELLPADDING=1 CELLSPACING=0 BORDER=0>'
	.'<FORM NAME="Log" METHOD=POST ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Log">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="add_log">';
	echo '<TR><TD WIDTH=130>'._MESSAGE.' </TD><TD><INPUT TYPE="text" NAME="logdate" SIZE="50" VALUE="30"></TD></TR>';
	echo '<TR><TD WIDTH=130 VALIGN="TOP">&nbsp;<TD><BR><INPUT class="button" TYPE="button"  VALUE="Submit" onclick="formSubmit()"> ';
	echo '<BR><BR></TD></TR></FORM>'
	.'</TABLE>'.'</fieldset>';

	CloseTable();
	include 'footer.php';
}

////////////add log/////////
function addLog($vars) {

	$menus= array(_ADMINMENU,_LOGADMIN,_SHOWTIME);
	$links=array('index.php?mod=Admin','index.php?mod=Log&amp;file=admin','#');
	lnBlockNav($menus,$links);

	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$module_varstable = $lntable['module_vars'];
	$module_varscolumn = &$lntable['module_vars_column'];
	//include 'header.php';
	OpenTable();
	// Get arguments from argument array
	//print_r($vars);
	
	$activetime = time();


	echo '<BR><center><fieldset>'
	.'<TABLE WIDTH="400" CELLPADDING=1 CELLSPACING=1 BORDER=0>';
	echo '<TR><TD WIDTH=130>'._SPECIFY.'&nbsp;&nbsp;<B>'._LOG.'</B></TD><TD><B>'.$logdate.'&nbsp;&nbsp;'._DAY.'</B></TD></TR>';
	echo '<BR></TD></TR></FORM>'
	.'</TABLE>'.'<BR><BR></fieldset>';

	//$lenlogtime = strlen($activetime );

	//$query = "UPDATE $module_varstable
	//                  SET $module_varscolumn[value]='s:'.$lenlogtime.':\"'.$activetime.'\";'
	//                  WHERE $module_varscolumn[id]='38'";
	//       $dbconn->Execute($query);
	lnConfigSetVar('logtime',$activetime);
		
	//$lenlogdate = strlen($logdate);

	//$query = "UPDATE $module_varstable
	//                SET $module_varscolumn[value]='s:'.$lenlogdate.':\"'.$logdate.'\";'
	//              WHERE $module_varscolumn[id]='39'";
	//  $dbconn->Execute($query);
		
	lnConfigSetVar('logdate',$logdate);

	CloseTable();
	//include 'footer.php';
	//echo '<meta http-equiv="refresh" content="0;URL=index.php?mod=Log&file=admin" />';
}

////////counttime//////////////
function countTime($vars) {

	$menus= array(_ADMINMENU,_LOGADMIN,_SHOWTIME);
	$links=array('index.php?mod=Admin','index.php?mod=Log&amp;file=admin','#');
	lnBlockNav($menus,$links);

	//connect userlog
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$user_logtable = $lntable['user_log'];
	$user_logcolumn = &$lntable['user_log_column'];

	//connect modules_vars
	$modulestable = $lntable['module_vars'];
	$module_varscolumn = &$lntable['module_vars_column'];

	OpenTable();
	//find userlog
	$result = $dbconn->Execute("SELECT FROM_UNIXTIME(MAX($user_logcolumn[atime])) FROM $user_logtable ");
	list($atime) = $result->fields;

	//echo $atime;

	//find module_var

	//$result = "SELECT FROM_UNIXTIME(MAX($module_varscolumn[value]))='s:' FROM $modulestable WHERE $module_varscolumn[name] ='logtime' ");
	//list($logtime) = $result->fields;

	//$logtime=lnConfigGetVar('logtime');
	$logtime=date ("Y-m-d H:i:s", lnConfigGetVar('logtime'));

	if($atime == null){
		$atime = $logtime; 
	}
	echo '<center><fieldset><legend>Showlog</legend>'
	.'<TABLE WIDTH="550" CELLPADDING=1 CELLSPACING=1 BORDER=0>';
	echo '<TR><TD WIDTH=300>'._DELLOG.' </TD><TD WIDTH=300><B>'.$atime.'</B></TD></TR>';
	//echo '<TR><TD>'._FIXLOG.' </TD><TD><B>'.$logtime.'</B></TD></TR>';
	echo '<BR></TD></TR></FORM>'
	.'</TABLE>'.'<BR><BR></fieldset><BR>';

	//echo $atime.'<BR>'.$logtime.'<BR>';



	$time =  Date_Calc::dateDiv($logtime,$atime);
	//print_r($time);
	$dtime = $time['D'];
	//echo $dtime;

	//Read logdate.tet
	//$fp = fopen('modules/Log/logdate.txt', 'r');
	//$logdate = fread($fp,999);
	//fclose($fp);

	$logdate =lnConfigGetVar('logdate');

	$totaltime =  $logdate - $dtime;
	//echo $totaltime;
	echo '<center><fieldset><legend>'.Showtime.'</legend><TABLE WIDTH="450" CELLPADDING=1 CELLSPACING=1 BORDER=0>';
	echo '<TR><TD WIDTH=160>'._DAYLOG.' </TD><TD><B>'.$totaltime.'&nbsp;&nbsp;'._DAY.'</B></TD></TR>';
	echo '<BR></TD></TR></FORM>'.'</TABLE>'.'<BR><BR></fieldset>';

	/*if($totaltime<=0){
		$result = $dbconn->Execute("DELETE FROM $user_logtable WHERE $user_logcolumn[atime]");
		$activetime = time();
		$result = $dbconn->Execute("UPDATE $modulestable SET $module_varscolumn[value] = '".lnVarPrepForStore($activetime)."'
		WHERE $module_varscolumn[name] ='logtime'") ;
		}*/
		
	//echo $time['D'];
	//echo $time;
	CloseTable();
	include 'footer.php';
}



include 'footer.php';
/* - - - - - - - - - - - */
?>
