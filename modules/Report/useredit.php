<?php
/**
* Edit/Find/Delete User
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'ADMIN::', "$file::", ACCESS_ADMIN)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
		return false;
}

include 'header.php';

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$userstable = $lntable['users'];
$userscolumn = &$lntable['users_column'];
$datatable = $lntable['user_data'];
$datacolumn = &$lntable['user_data_column'];
$propertiestable = $lntable['user_property'];
$propcolumn = &$lntable['user_property_column'];
$groupstable = $lntable['groups'];
$groupscolumn = &$lntable['groups_column'];
$group_membershiptable = $lntable['group_membership'];
$group_membershipcolumn = &$lntable['group_membership_column'];

/* Show users */
if ($op=="finduser") {
		/** Navigator **/
		$menus= array(_ADMINMENU,Report);
		$links=array('index.php?mod=Admin','index.php?mod=Report&file=admin','index.php?mod=Report&file=usershow','#');
		lnBlockNav($menus,$links);
	  /** Navigator **/

	OpenTable();
	
	 echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <B>'._USERSEARCH.'</B><BR>&nbsp;';
	 

	 list($word,$field) = lnVarCleanFromInput('word','field');
	
	// Search
	$eval_cmd = "\$usersfindcolumn=\$userscolumn[$field];";
	@eval($eval_cmd); 
	$result = $dbconn->Execute("SELECT $userscolumn[uid],$userscolumn[name],$userscolumn[uname],$userscolumn[email],
							$userscolumn[regdate],$userscolumn[phone],$userscolumn[uno],$userscolumn[news] ,$userscolumn[active] 
							 FROM $userstable 
 							 WHERE $usersfindcolumn LIKE '".lnVarPrepForStore($word)."%' ");
		echo "<BR>"._SEARCH." &nbsp;'<B>$word</B>'<BR>";
		if ( $result->PO_RecordCount() == 0) {
			 echo '<BR><BR>'._SEARCH. '&nbsp; <B>'. $word. '</B>&nbsp;'._NOTFOUND;
		}
		else {
						echo '<BR><table width="100%" cellpadding=3 cellspacing=1 bgcolor=#d3d3d3>'
			.'<tr align=center bgcolor=#808080><td class="head">No.</td><td class="head"  align=center>'._NICKNAME.'</td><td class="head"  align=center>'._UNO.'</td><td class="head" >'._NAME.'</td><td class="head"  align=center>'.State.'</td>';
			 for ($i=1; list($uid,$name,$uname,$email,$regdate,$phone,$uno,$news,$active) = $result->fields; $i++) {
					 $result->MoveNext();
			$group = check($uid);  
		//			 echo '<tr bgcolor=#FFFFFF><td width=25>'.$i.'</td><td width=100><A  HREF="index.php?mod=StudentReport&file=useredit&op=show&amp;uid='.$uid[$b].'>'.$uname.'</td><td>'.$name.'</td><td width=80>'.$uno.'</td>';
//	index.php?mod=User&amp;file=useredit&amp;op=edituser&amp;uid=$uid
                   echo "<tr bgcolor=#FFFFFF><td width=25>$i</td> <td width=100 align=center><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=show&amp;uid=$uid\">$uname</a></td><td width=80 align=center><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=show&amp;uid=$uid\">$uno</a></td><td><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=show&amp;uid=$uid\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;$name</a></td ><td width=80 align=center><A HREF=\"index.php?mod=Report&amp;file=usershow&amp;op=show&amp;uid=$uid\">$group</a></td>";
				 echo "</tr>";
			}
			echo "</table>";
		}
	
	CloseTable();

	include 'footer.php';

	return;
}

/** Navigator **/
$menus= array(_ADMINMENU,Report);
	$links=array('index.php?mod=Admin','index.php?mod=Report&file=admin','index.php?mod=Report&file=useredit','#');
lnBlockNav($menus,$links);
/** Navigator **/
OpenTable();
	$bgcolor1 = "#ffffff";
	$bgcolor2 = "#000000";
	echo "<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"$bgcolor2\"><tr><td>\n";
    echo "<table width=\"100%\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\" bgcolor=\"$bgcolor1\" height=\"350\"><tr><td valign=\"top\">\n";

	echo '<br><IMG SRC="images/global/bl_red.gif"><B>เลือกบุคคลที่ต้องการแสดงรายงาน</B><br><br>';


/*
	echo '<center><FORM METHOD=POST ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Report">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="useredit">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="finduser"><br><br>'

	._SEARCH.': <INPUT  class="input" TYPE="text" NAME="word" SIZE="20">&nbsp;'
	.'<SELECT class="select" NAME="field">'
	.'<OPTION VALUE="uname">'._NICKNAME.'</OPTION>'
	.'<OPTION VALUE="name">'._NAME.'</OPTION>'
	.'<OPTION VALUE="uno">'.State.'</OPTION>'
	.'<OPTION VALUE="uno">'._UNO.'</OPTION>'
	.'<OPTION VALUE="email">'._EMAIL.'</OPTION>'
	.'</SELECT>'
	.' <INPUT class="button_org" TYPE="submit" VALUE="'._SUBMITFIND.'">'
	.'</FORM></center><br><br>';
*/

	// Creates the list of letters and makes them a link.
	$alphabet = array (_ALL, "A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
	$num = count($alphabet) - 1;
	$counter = 0;
	while(list(, $ltr) = each($alphabet)) {
		$class = ($letter == $ltr) ? "class=line": "";
		$menu[] = "<a $class  href=\"index.php?mod=Report&amp;file=useredit&amp;letter=".$ltr."\">".$ltr."</a>";
		$counter++;
	}
	$menus = "<center>[ ".join('&nbsp;&nbsp;|&nbsp;&nbsp;',$menu)." ]</center><BR>";
	echo $menus;

	$pagesize = lnConfigGetVar('pagesize');
	if(empty($sorting)){
	$sorting="uname";
	} 

	if (!isset($letter)) {
	$letter = "A";
	}

	if (!isset($page)) {
	$page = 1;
	}

	$min = $pagesize * ($page - 1); // This is where we start our record set from
	$max = $pagesize; // This is how many rows to select
	$count = "SELECT COUNT($userscolumn[uid]) FROM $userstable ";

	$resultcount = $dbconn->Execute($count);
	list ($totalusers) = $resultcount->fields;
	$resultcount->Close();

	//Security Fix - Cleaning the search input
	$sorting   = lnVarCleanFromInput('sorting');
	if (!empty($sorting)){
	$sort = "$sorting ASC";
	}

	if (($letter != _ALL)) {
	$where .= " UPPER($userscolumn[uname]) LIKE UPPER('".lnVarPrepForStore($letter)."%')";
	}


	//$result = $dbconn->Execute($query);
	$myquery = buildSimpleQuery('users', array('uid', 'name', 'uname', 'email', 'regdate', 'phone','uno','news','active'), $where, lnVarPrepForStore($sort), $max, $min); 

	$result = $dbconn->Execute($myquery);
	if ($result === false) {
			error_log("Error: " . $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg());
	}

	echo '<table width=100% cellpadding=3 cellspacing=1 bgcolor="#999999">'
	.'<tr bgcolor="#D0D0D0" align=center><td><B>No.</B></td><td>'
	.'<A class="white"><B>'._NICKNAME.'</B></A></td><td>'
	.'<A class="white"><B>'._UNO.'</B></A></td><td>'
	.'<A class="white"><B>'._NAME.'</B></A></td><td>'
	.'<A class="white"><B>'._STATE.'</B></A></td><td>'
	.'<A class="white"><B>'._REGDATE.'</B></A></td></tr>';
	for ($i=1; list($uid,$name,$uname,$email,$regdate,$phone,$uno,$news,$active) = $result->fields; $i++) {
	$result->MoveNext();
	if ($active) 
		$activecolor="#000000";
	else 
		$activecolor="#999999";
	$n=$min + $i;
	$group = check($uid);    
		$link = "<A HREF=\"index.php?mod=Report&amp;file=show&amp;op=show&amp;uid=$uid\">"; 
	echo '<TR  bgcolor=#FFFFFF align=center>'
	.'<TD width="5%"><FONT COLOR="'.$activecolor.'">'.$link . $n .'</A></TD>'
	.'<TD width="15%"><FONT COLOR="'.$activecolor.'">'.$link . $uname .'</FONT></A></TD>'
	.'<TD width="15%"><FONT COLOR="'.$activecolor.'">'.$link . $uno .'</A></TD>'
	.'<TD width="35%"><FONT COLOR="'.$activecolor.'">'.$link . $name.'</A></TD>'
	.'<TD width="15%"><FONT COLOR="'.$activecolor.'">'.$link . $group.'</A></TD>'
	.'<TD width="15%"><FONT COLOR="'.$activecolor.'">'.$link . date('d-M-Y',$regdate).'</A></TD>';
	echo '</TR>';
	}
	echo '</table>';
	echo '</table>';
	echo '</table>';

	// Show pages
	if (!empty($where)) {
	$where = " WHERE $where";
	} else {
	$where = '';
	}

	$resultcount = $dbconn->Execute($count . $where);
	list ($numrows) = $resultcount->fields;
	$resultcount->Close();

	//echo "<center>";

	if ($numrows  > $pagesize) {
	 $total_pages = ceil($numrows / $pagesize); // How many pages are we dealing with here ??
	 $prev_page = $page - 1;
	 echo '<BR>Page: ';
	  if ( $prev_page > 0 ) {
		echo '[<A HREF="index.php?mod=Report&amp;file=useredit&amp;letter='.$letter.'&amp;page='.$prev_page.'&amp;sorting='.$sorting.'">&lt;&lt;</A>] ';
	  }
	  for($n=1; $n <= $total_pages; $n++) {
		if ($n == $page) {
			echo "<B><U>$n</U></B> ";
		}
		else {
			echo '<A HREF="index.php?mod=Report&amp;file=useredit&amp;letter='.$letter.'&amp;page='.$n.'&amp;sorting='.$sorting.'">'.$n.'</A> ';
		}
	  } 
	  $next_page = $page + 1;
	  if ( $next_page <= $total_pages ) {
		  echo ' [<A HREF="index.php?mod=Report&amp;file=useredit&amp;letter='.$letter.'&amp;page='.$next_page.'&amp;sorting='.$sorting.'">&gt;&gt;</A>]';
	  }
	}
	echo "<BR><B>"._TOTALUSERS.'&nbsp;'.$totalusers."</B><BR>";
	//echo "</center>";

	echo "<BR><BR>";

	CloseTable();

	include 'footer.php';
/*- - - - - - - -*/
function check($uid){

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];

    $query2 = "SELECT * 
	FROM $group_membershiptable 
		WHERE  $group_membershipcolumn[uid] = $uid ";

    $result2 = mysql_query($query2);

while($rets = mysql_fetch_row($result2)) {
  $gid= $rets[0];
	}	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$groupstable = $lntable['groups'];
	$groupscolumn = &$lntable['groups_column'];

    $query3 = "SELECT * 
	FROM $groupstable 
		WHERE  $groupscolumn[gid] = $gid ";

    $result3 = mysql_query($query3);

while($rets = mysql_fetch_row($result3)) {
  $group= $rets[1];
	}
	
				 return $group;
		  
}

// Utilties Functions
function groupExisting($gList,$gid) {
	for ($i=0; $i<count($gList); $i++) {
		if ($gList[$i] == $gid) {
			return true;
		}
	}

	return false;
}
?>