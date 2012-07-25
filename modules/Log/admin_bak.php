<?php
/**
* Logging
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Log::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}


/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_LOGADMIN);
$links=array('index.php?mod=Admin','index.php?mod=Log&amp;file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Log&amp;file=admin"><B>'._LOGADMIN.'</B></A><BR>&nbsp;';

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
echo '<tr><td colspan=4 height=20><B>Show: </B><A HREF="index.php?mod=Log&amp;file=admin&amp;filter=">All</A> | <A HREF="index.php?mod=Log&amp;file=admin&amp;filter=login">Login</A> | <A HREF="index.php?mod=Log&amp;file=admin&amp;filter=logout">Logout</A><HR></td></tr>';
while(list($uid,$atime,$event,$ip) = $result->fields) {
	$result->MoveNext();
	$stime = date('d-M-Y H:i', $atime);
	$user = lnUserGetVars($uid);
	$uname = empty($user[uname]) ? '<CENTER>-</CENTER>' : $user[uname];
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
		echo '[&lt;&lt;<A HREF="index.php?mod=Log&amp;file=admin&amp;filter='.$filter.'&amp;page='.$prev_page.'&amp;sorting='.$sorting.'">Back</A>] ';
	  }
      for($n=1; $n <= $total_pages; $n++) {
		if ($n == $page) {
			echo "<B>$n</B> ";
		}
		else {
			echo '<A HREF="index.php?mod=Log&amp;file=admin&amp;filter='.$filter.'&amp;page='.$n.'&amp;sorting='.$sorting.'">'.$n.'</A> ';
		}
      } 
	  $next_page = $page + 1;
      if ( $next_page <= $total_pages ) {
		  echo ' [<A HREF="index.php?mod=Log&amp;file=admin&amp;letter='.$filter.'&amp;page='.$next_page.'&amp;sorting='.$sorting.'">Next</A>&gt;&gt;]';
	  }
 }
echo "</center>";
//echo "<BR>"._TOTALEVENTS.'&nbsp;'.$totalevents."<BR>";
//echo "<BR><BR>";


CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */
?>