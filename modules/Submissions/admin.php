<?php
/**
*  Course submission
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */
include 'header.php';

$vars= array_merge($_GET,$_POST);

/* options */
switch ($op) {
	case "add_form" :	submissionsForm(null,$cid); break; // ($sid,$cid)
	case "add" :				addSubmission($vars); break;
	case "delete" :			deleteSubmission($sid,$cid); break;
	case "edit" :				editSubmission($sid,$cid); break;
	case "update" :			updateSubmission($vars); break;
	case "search" :			
	default :							showList($vars); break;
}

include 'footer.php';
/* - - - - - - - - - - - */


/**
* show submissions list 
*/
function showList($vars) {
	// Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];

	$orderby = 'null';
	if (!empty($order)) {
		if ($order == "code" || $order == "title") {
			$orderby = $coursescolumn[$order];
		}
		else if ($order == "duration" || $order == "start") {
			$orderby = "$course_submissionscolumn[$order]";
		}
		else if ($order == "uid") {
			$orderby = $userscolumn[$order];		
		}
	}
	else {
		$orderby = $course_submissionscolumn[start];
	}

	if (!empty($sort) || $sort == "up") {
		$orderby .= " ASC";
		$sortdir = "down";
		$arrow="<IMG SRC='images/global/arrowup.gif' WIDTH=10 HEIGHT=9 BORDER=0>";
	}
	else {
		$orderby .= " DESC";
		$sortdir = "up";
		$arrow="<IMG SRC='images/global/arrowdown.gif' WIDTH=10 HEIGHT=9 BORDER=0>";
	}

	$pagesize = lnConfigGetVar('pagesize');
	if (!isset($page)) {
		$page = 1;
	}
	$min = $pagesize * ($page - 1); // This is where we start our record set from
	$max = $pagesize; // This is how many rows to select

	$count = "SELECT COUNT($course_submissionscolumn[sid]) FROM $coursestable, $userstable, $course_submissionstable WHERE $course_submissionscolumn[cid]=$coursescolumn[cid] and $course_submissionscolumn[instructor]=$userscolumn[uid]";

	if (!empty($keyword)) {
		$where = " and ($coursescolumn[title] like '%". lnVarPrepForStore($keyword) ."%' or $coursescolumn[code] like '%". lnVarPrepForStore($keyword) ."%' or $userscolumn[uname] like '%". lnVarPrepForStore($keyword) ."%')";		
	}
	if (!lnUserAdmin(lnSessionGetVar('uid'))) {
		$query = " and $userscolumn[uid]='".lnSessionGetVar('uid')."'";
	}

	if (!empty($where)) {
		$where = "$where";
	} else {
		$where = '';
	}

	$resultcount = $dbconn->Execute($count . $query . $where);
	list ($numrows) = $resultcount->fields;
	$resultcount->Close();


	/********** query for find all of course that this instructor teaching ********************/
	$userinfo = lnUserGetVars(lnSessionGetVar('uid'));
	//$user = $userinfo['uname'];
	$user = $userinfo['uid'];
	
	$user2 = $userinfo['uid'];
	
	//echo $user2;
	

	$query = " SELECT $coursescolumn[cid], $coursescolumn[code], $coursescolumn[title], $userscolumn[uname], $course_submissionscolumn[start]  
		FROM $userstable,$coursestable LEFT JOIN $course_submissionstable ON $coursescolumn[cid] = $course_submissionscolumn[cid] WHERE $course_submissionscolumn[instructor] =  '$user2' AND $userscolumn[uid]='$user'";
	/********** end query for find all of course that this instructor teaching ********************/
	

$result = $dbconn->Execute($query);

if($dbconn->ErrorNo() != 0) {
	echo "ERROR";
	return;
}

/********************************************************************************************/


/*REAL
	$myquery = buildQuery (array ('users', 'courses','course_submissions'),
                        array ($course_submissionscolumn['sid'], 
						$course_submissionscolumn['cid'],
						$course_submissionscolumn['start'],
						$course_submissionscolumn['instructor'],
						$course_submissionscolumn['study'],
						$course_submissionscolumn['active'],
						$coursescolumn['code'],
						$coursescolumn['title'],
						$userscolumn['uname']),
                         "$course_submissionscolumn[cid]=$coursescolumn[cid] and $course_submissionscolumn[instructor]=$userscolumn[uid] " . $query . $where,
                         array($orderby),
                         $max, $min);
	$result = $dbconn->Execute($myquery);
*/


	/** Navigator **/
		$menus = $links = array();
		if (lnUserAdmin( lnSessionGetVar('uid'))) {
			$menus[] = _ADMINMENU;
			$links[]='index.php?mod=Admin';
		}

	$menus[]= _SUBMISSIONADMIN;
	$links[]= 'index.php?mod=Submissions&file=admin';
	lnBlockNav($menus,$links);
	/** Navigator **/

	OpenTable();

	echo '<table width= 100% cellpadding=0 cellspacing=0 border=0>';
	echo '<tr><td bgcolor=#FFFFFF algin=left valign=middle>';

	/* == search form == */
/*
	echo '
	<TABLE   CELLSPACING="0" BORDER="0" CELLPADDING="3">
	<FORM ACTION="index.php" METHOD="post">
	<INPUT VALUE="Submissions" NAME="mod" TYPE="hidden">
	<INPUT VALUE="admin" NAME="file" TYPE="hidden">
	<INPUT VALUE="search" NAME="op" TYPE="hidden">
	<TR>
	<TD ALIGN="left">
	<FONT SIZE="2" FACE="Arial, Helvetica, sans-serif"><B>'._SEARCHFOR.':</B></FONT></TD>
	<TD ALIGN="left">
	<INPUT SIZE="20" NAME="keyword" TYPE="text" VALUE="'.$keyword.'"></TD>
	<TD>
	<INPUT BORDER="0" SRC="modules/Submissions/images/go.gif" ALT="Go" TYPE="image">
	</TD></TR>
	</FORM></TABLE>';
	// == search form == 
*/
	echo '</td>';

/*
	echo '<td align=right><A HREF="index.php?mod=Submissions&amp;file=admin&amp;op=add_form"><IMG SRC="modules/Submissions/images/create.gif" BORDER=0 ALT=""></A>&nbsp;&nbsp;&nbsp;</td></tr>';
*/
	if ($numrows > 0) {
		echo '<tr><td colspan=2 height=350 bgcolor=#FFFFFF valign=top>';
		
		echo '<table width= 100% cellpadding=2 cellspacing=1 border=0 bgcolor="#d3d3d3">';
		echo '<TR BGCOLOR=#808080>';
		echo '<TD WIDTH=80 align=center><A  CLASS="head" HREF="index.php?mod=Submissions&amp;file=admin&amp;order=code&amp;sort='.$sortdir.'">'._CODE;
		if ($order=="code") echo ' ' . $arrow;
		echo '</A></TD>';
		echo '<TD align=center><A  CLASS="head" HREF="index.php?mod=Submissions&amp;file=admin&amp;order=title&amp;sort='.$sortdir.'">'._COURSENAME;
			if ($order=="title") echo ' ' . $arrow;
		echo '</A></TD>';
		echo '<TD  WIDTH=80 align=center><A  CLASS="head" HREF="index.php?mod=Submissions&amp;file=admin&amp;order=uid&amp;sort='.$sortdir.'">'._INSTRUCTOR;
			if ($order=="uid") echo ' ' . $arrow;
		echo '</A></TD>';
		
		echo '<TD  WIDTH=100 align=center><A  CLASS="head" HREF="index.php?mod=Submissions&amp;file=admin&amp;order=start&amp;sort='.$sortdir.'">'._STARTDATE;
			if ($order=="start") echo ' ' . $arrow;
		echo '</A></TD>';
		echo '<TD  WIDTH=40>&nbsp;</TD></TR>';


	//for ($i=1; list($name) = $result->fields; $i++)  {


	for ($i=1; list($cid,$code,$title,$uname,$start) = $result->fields; $i++)  {

//	for ($i=1; list($sid,$cid,$start,$instructor,$study,$active,$coursecode,$coursename,$instructorname) = $result->fields; $i++) {
			$result->MoveNext();
			if (Date_Calc::isValidDate2($start)) {
				$sstart = Date_Calc::dateFormat2($start,"%e %b %y");
			}
			else {
				$sstart = '-';
			}
			if ($study & 1) {
				$duration = '-';
			}
			if ($active == 1) {
				$link = '<A HREF="index.php?mod=Submissions&file=admin&op=edit_course&cid='.$cid.'">'; 
//				$link = '<A HREF="index.php?mod=Submissions&amp;file=admin&amp;op=edit&amp;sid='.$sid.'">'; 
			}
			else {
				$link = '<A class=gray HREF="index.php?mod=Courses&op=course_lesson&cid='.$cid.'&sid='.$sid.'">'; 
			}

/**************งแก้ตรงนี้นะ เกี่ยวกับ link งที่ delete ให้มัน delete เฉพาะที่เป็น submission *********************************/

			echo '<TR  bgcolor="#FFFFFF"><TD ALIGN="CENTER">'.$link.$code.'</A></TD><TD>'.$link.$title.'</A></TD><TD align=center>'.$uname.'</TD><TD align=center>'.$sstart.'</TD>';
			echo '<TD ALIGN="CENTER">';
			echo "<A HREF=\"index.php?mod=Courses&file=admin&op=edit_course&cid=$cid\"><IMG SRC=images/global/edit.gif BORDER=0 ALT='edit'></A>";
			echo "&nbsp;<A HREF=\"javascript:if(confirm('Delete ?')) window.open('index.php?mod=Courses&file=admin&op=delete_course&cid=$cid','_self')\"><IMG SRC=images/global/delete.gif BORDER=0 ALT='delete'></A></TD></TR>";

/*
			echo '<TR  bgcolor="#FFFFFF"><TD ALIGN="CENTER">'.$link.$coursecode.'</A></TD><TD>'.$link.$coursename.'</A></TD><TD align=center>'.$instructorname.'</TD><TD align=center>'.$sstart.'</TD>';
			echo '<TD ALIGN="CENTER">';
			echo "<A HREF=\"index.php?mod=Submissions&amp;file=admin&amp;op=edit&amp;sid=$sid\"><IMG SRC=images/global/edit.gif BORDER=0 ALT='edit'></A>";
			echo "&nbsp;<A HREF=\"javascript:if(confirm('Delete ?')) window.open('index.php?mod=Submissions&amp;file=admin&amp;op=delete&amp;sid=$sid','_self')\"><IMG SRC=images/global/delete.gif BORDER=0 ALT='delete'></A></TD></TR>";
*/
		}

		echo '</table>';

	
	/* show pages */

		echo "<center>";
		 
		 if ($numrows  > $pagesize) {
			 $total_pages = ceil($numrows / $pagesize); // How many pages are we dealing with here ??
			 $prev_page = $page - 1;
			 echo '<BR>';
			  if ( $prev_page > 0 ) {
				echo '<A HREF="index.php?mod=Submissions&amp;file=admin&amp;order='.$order.'&amp;page='.$prev_page.'&amp;sort='.$sort.'&amp;keyword='.$keyword.'"><IMG SRC="images/back.gif" WIDTH="19" HEIGHT="9" BORDER="0" ALT=""></A>';
			  }
			  for($n=1; $n <= $total_pages; $n++) {
				if ($n == $page) {
					echo "<B><U>$n</U></B> ";
				}
				else {
					echo '<A HREF="index.php?mod=Submissions&amp;file=admin&amp;order='.$order.'&amp;page='.$n.'&amp;sort='.$sort.'&amp;keyword='.$keyword.'">'.$n.'</A> ';
				}
			  } 
			  $next_page = $page + 1;
			  if ( $next_page <= $total_pages ) {
				  echo '<A HREF="index.php?mod=Submissions&amp;file=admin&amp;order='.$order.'&amp;page='.$next_page.'&amp;sort='.$sort.'&amp;keyword='.$keyword.'"><IMG SRC="images/next.gif" WIDTH="19" HEIGHT="9" BORDER="0" ALT=""></A> ';
			  }
		 }

		echo '<BR><BR><B> <FONT COLOR="#800000">= '._TOTALCOURSES.'&nbsp;'.$numrows.'</B> =</FONT> <BR>';
		echo '</center>';
		echo '</td></tr>';

		}	
		else {
			echo '<tr><td colspan="2" align="center" height="50"><B>* * *  No Schedule * * *</B></td></tr>';
		}
		echo '</table>';
	
		CloseTable();
}


/*
* Delete 
*/
function deleteSubmission($sid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_tatable = $lntable['course_ta'];
	$course_tacolumn = &$lntable['course_ta_column'];

	// delete from submission table
	$query = "DELETE FROM $course_submissionstable WHERE $course_submissionscolumn[sid] = '". lnVarPrepForStore($sid) ."'";
	$result = $dbconn->Execute($query);

	// delete from enroll table ;
	$query = "DELETE FROM $course_enrollstable WHERE $course_enrollscolumn[sid] = '". lnVarPrepForStore($sid) ."'";
	$result = $dbconn->Execute($query);

	// delete from enroll table ;
	$query = "DELETE FROM $course_tatable WHERE $course_tacolumn[sid] = '". lnVarPrepForStore($sid) ."'";
	$result = $dbconn->Execute($query);
	showList($vars);
}


/*
* Modify
*/
function updateSubmission($vars) {
	// Get arguments from argument array
    extract($vars);

	list($d,$m,$y) = explode('-',$start);
	$start = "$y-$m-$d";
	$study = $study[0] + $study[1];

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_tatable = $lntable['course_ta'];
	$course_tacolumn = &$lntable['course_ta_column'];
	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];

	//1. update submissions table
	if (empty($active)) $active='0';
	if (empty($duration)) $duration='0';
	if (empty($pass)) $pass='0';

	$query = "UPDATE $course_submissionstable SET
							$course_submissionscolumn[cid] = '". lnVarPrepForStore($cid) ."',
							$course_submissionscolumn[start] = '". lnVarPrepForStore($start) ."',
							$course_submissionscolumn[study] =  '". lnVarPrepForStore($study) ."',
							$course_submissionscolumn[student] = '". lnVarPrepForStore($student) ."',
							$course_submissionscolumn[instructor] = '". lnVarPrepForStore($instructor) ."',
							$course_submissionscolumn[active] = '". lnVarPrepForStore($active) ."'
							WHERE $course_submissionscolumn[sid] = '". lnVarPrepForStore($sid) ."'";
	$result = $dbconn->Execute($query);
	
	// 2.update enroll table
	// find old users
	$query = "SELECT $course_enrollscolumn[uid] FROM $course_enrollstable WHERE $course_enrollscolumn[sid]='$sid'";
	$result = $dbconn->Execute($query);
	$olduserlist = array();
	 for ($i=0; list($uid) = $result->fields; $i++) {
		$result->MoveNext();
		$olduserlist[]=$uid;
	 }

	// add user enroll
	$userlist = explode(',',$newLeft3);
	foreach ($userlist as $uid) { 
		if (!empty($uid)) {
			if (!in_array ($uid,$ulist)) {
				$ulist[]=$uid;
			}
		}
	}
	
	$ulist = array_unique($ulist);

	$result	= array_diff($olduserlist, $ulist);
	foreach ($result as $uid) { 
		$query = "DELETE FROM $course_enrollstable WHERE $course_enrollscolumn[sid] =  '". lnVarPrepForStore($sid) ."' AND $course_enrollscolumn[uid] =  '". lnVarPrepForStore($uid) ."'";
		$dbconn->Execute($query);	
	}

	$result	= array_diff($ulist, $olduserlist);
	foreach ($result as $uid) { 
		if ($uid != 0) {
			$query = "INSERT INTO $course_enrollstable 
							(	$course_enrollscolumn[eid],
								$course_enrollscolumn[sid],
								$course_enrollscolumn[gid],
								$course_enrollscolumn[uid],
								$course_enrollscolumn[options],
								$course_enrollscolumn[status],
								$course_enrollscolumn[mentor]
							) 
							VALUES ( '',
							'". lnVarPrepForStore($sid) ."',
							'0',
							'". lnVarPrepForStore($uid) ."',
							'3',
							'0',
							'0'
							)";
			$result = $dbconn->Execute($query);
		}
	}

	// 3. insert into course_ta table
	// delete old TA
	$query = "DELETE FROM $course_tatable WHERE $course_tacolumn[sid] =  '". lnVarPrepForStore($sid) ."'";
	$dbconn->Execute($query);	
	
	// find old TA
/*	$query = "SELECT $course_tacolumn[uid] FROM $course_tatable WHERE $course_tacolumn[sid]='$sid'";
	$result = $dbconn->Execute($query);
	$oldtalist = array();
	 for ($i=0; list($uid) = $result->fields; $i++) {
		$result->MoveNext();
		$oldtalist[]=$uid;
	 }

	$talist = explode(',',$newLeft);
	$tlist = array();
	foreach ($talist as $uid) { 
		if (!empty($uid)) {
			if (!in_array ($uid,$tlist)) {
				$tlist[]=$uid;
			}
		}
	}
	
	$tlist = array_unique($tlist);

	$result	= array_diff($oldtalist, $tlist);
*/

	$talist = explode(',',$newLeft);
	foreach ($talist as $uid) { 
		if ($uid != 0) {
			$query = "INSERT INTO $course_tatable 
							(	$course_tacolumn[sid],
								$course_tacolumn[uid]
							) 
							VALUES ( 
							'". lnVarPrepForStore($sid) ."',
							'". lnVarPrepForStore($uid) ."'
							)";

			$result = $dbconn->Execute($query);
		}
	}

	submissionsForm($sid,$cid);
}


/*
* Insert submission 
*/
function addSubmission($vars) {
	// Get arguments from argument array
    extract($vars);


//	if (!empty($cid) && (($study==_LNSCHEDULE_BASED && Date_Calc::isValidDate2($start)) || $study==_LNSELFPACE_BASED || $study==_LNALLTIME_BASED) ) {
//		$pretest = $pretest + $pretest1 + $pretest2 + $showanswer1 + $timelimit1;
//		$posttest = $posttest + $posttest1 + $posttest2 + $showanswer2 + $timelimit2;
//		if (empty($duration)) $duration=0;
//		if (empty($pass)) $pass=0;
	
	if ($student=='') {
		$student=_LNSTUDENT_USER;
	}

	list($d,$m,$y) = explode('-',$start);
	$start = "$y-$m-$d";

	if (!empty($cid)) {

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$course_submissionstable = $lntable['course_submissions'];
		$course_submissionscolumn = &$lntable['course_submissions_column'];
		$course_enrollstable = $lntable['course_enrolls'];
		$course_enrollscolumn = &$lntable['course_enrolls_column'];
		$course_tatable = $lntable['course_ta'];
		$course_tacolumn = &$lntable['course_ta_column'];
		$group_membershiptable = $lntable['group_membership'];
		$group_membershipcolumn = &$lntable['group_membership_column'];
		
		// insert into submissions table
		$study = $study[0] + $study[1];
		$maxsid = getMaxSID();
		$query = "INSERT INTO $course_submissionstable 
							(	$course_submissionscolumn[sid],
								$course_submissionscolumn[cid],
								$course_submissionscolumn[start],
								$course_submissionscolumn[study],
								$course_submissionscolumn[student],
								$course_submissionscolumn[instructor],
								$course_submissionscolumn[active]
							) 
							VALUES ( '".$maxsid."',
							'". lnVarPrepForStore($cid) ."',
							'". lnVarPrepForStore($start) ."',
							'". lnVarPrepForStore($study) ."',
							'". lnVarPrepForStore($student) ."',
							'". lnVarPrepForStore($instructor) ."',
							'1'
							)";
	
		$result = $dbconn->Execute($query);

/*
		// add user enroll
		$userlist = explode(',',$newLeft3);

		foreach ($userlist as $uid) { 
			if (!empty($uid)) {
				if (!in_array ($uid,$ulist)) {
					$ulist[]=$uid;
				}
			}
		}

		$ulist = array_unique($ulist);

		// insert userlist
		foreach ($ulist as $uid) {
				$maxeid = getMaxEID();
				$query = "INSERT INTO $course_enrollstable 
								(	$course_enrollscolumn[eid],
									$course_enrollscolumn[sid],
									$course_enrollscolumn[gid],
									$course_enrollscolumn[uid],
									$course_enrollscolumn[options],
									$course_enrollscolumn[status],
									$course_enrollscolumn[mentor]
								) 
								VALUES ( 
									'$maxeid',
									'$maxsid',
									'0',
									'". lnVarPrepForStore($uid) ."',
									'3',
									'0',
									'0'
								)";
				$result = $dbconn->Execute($query);
//				echo $query.'<BR>';
				// sendmessage
				$info = lnCourseGetVars(lnGetCourseID($maxsid)); 
				$start  = Date_Calc::dateFormat2(lnGetStartDateSubmission($maxsid), "%e %b %Y");
				$subject = _WELCOMECOURSESUBJECT.' '.$info['title'];
				$message = _WELCOMECOURSEMSG.' '.$start;
				$variables =  array ('priority'=>'1','subject'=>"$subject",'message'=>"$message",'from_uid'=>'1','to_uid'=>"$uid");
				$vars= array_merge($vars,$variables);
				sendMessage($vars);				
		}

		// insert into course_ta table
		$talist = explode(',',$newLeft);
		foreach ($talist as $uid) { 
			if ($uid != 0) {
				$query = "INSERT INTO $course_tatable 
								(	$course_tacolumn[sid],
									$course_tacolumn[uid]
								) 
								VALUES ( '".$maxsid."',
								'". lnVarPrepForStore($uid) ."'
								)";

				$result = $dbconn->Execute($query);
			}
		}
*/
	}

	showList($vars);
}



/*
* Edit  form
*/
function editSubmission($sid,$cid) {
	submissionsForm($sid,$cid); 
}


/**
* Submission  Form
*/
function submissionsForm($id,$cid) {

	/** Navigator **/
	$menus = $links = array();
	if (lnUserAdmin( lnSessionGetVar('uid'))) {
		$menus[] = _ADMINMENU;
		$links[]='index.php?mod=Admin';
	}

	$menus[]= _SUBMISSIONADMIN;
	$links[]= 'index.php?mod=Submissions&file=admin';
	$menus[]= _CREATESUBMISSION;
	$links[]= 'index.php?mod=Submissions&amp;file=admin&amp;op=add_form';

	lnBlockNav($menus,$links);
	/** Navigator **/

	OpenTable();
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];
	$groupstable = $lntable['groups'];
	$groupscolumn = &$lntable['groups_column'];
	$group_membershiptable = $lntable['group_membership'];
	$group_membershipcolumn = &$lntable['group_membership_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];

?>
<script language="javaScript">
		function formSubmit() {
			if(checkFields()) document.forms.submission.submit();
		}
    	function checkFields() {
			var cid = document.forms.submission.cid.value;
			var start = document.forms.submission.start.value;
			if (cid == "") {
				alert("<?=_SELECTACOURSE?>");
				document.forms.submission.cidselect.focus();
				return false;
			}
			if (start == "" ) {
				alert("<?=_EMPTYSTARTDATE?>");
				document.forms.submission.start.focus();
				return false;
			}

			return true; 
		}
</script>
<?

	echo '<FORM NAME="submission" METHOD=POST ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Submissions">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">';

	//edit submission
	if (!empty($id)) {
		$result = $dbconn->Execute("SELECT * FROM $course_submissionstable WHERE $course_submissionscolumn[sid]='". lnVarPrepForStore($id) ."'");
		list($e_sid, $e_cid, $e_start,$e_instructor,$e_active) = $result->fields;
		$data = lnUserGetVars($e_instructor);
		$e_instructor_name = $data['name'];
		$courseinfo = lnCourseGetVars($e_cid);
		$e_coursename = $courseinfo['title'];
		echo '<INPUT TYPE="hidden" NAME="op" VALUE="update">';
	    echo '<INPUT TYPE="hidden" NAME="sid" VALUE="'.$e_sid.'">';
	    echo '<INPUT TYPE="hidden" NAME="cid" VALUE="'.$e_cid.'">';
		list($y,$m,$d) = explode('-',$e_start);
		$e_start = "$d-$m-$y";
	}
	// add submission
	else {
		 echo '<INPUT TYPE="hidden" NAME="op" VALUE="add">';
		 echo '<INPUT TYPE="hidden" NAME="sid" VALUE="'.$id.'">';
		 echo '<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">';
   }
	
	// find instructor after select course
	if (!empty($cid) &&empty($id) && empty($sid) && $op="add_form") {
		$result = $dbconn->Execute("SELECT $coursescolumn[author],$coursescolumn[sequence] FROM $coursestable WHERE $coursescolumn[cid]='$cid'");
		list($e_instructor, $sequence) = $result->fields;	// default instructor or course author
	}



	// 0 <======= show instruction
	echo '<center>';
	echo '<table width="100%" cellpadding="3" cellspacing="0" bgcolor="#FFFFFF" border="0">';
	echo '<tr><td><FONT COLOR="#800000"><B>'._CREATESCHEDULE.'</B></FONT></td></tr>';
	echo '<tr><td>'._INSTRUCTION.'</td></tr>';
	echo '</table><BR>';

	echo '<table width=90% border=0 cellpadding=3 cellspacing=0>';

	if (empty($id)) {
		
		// if admin list all courses
		if (lnUserAdmin(lnSessionGetVar('uid'))) {
			$result = $dbconn->Execute("SELECT $coursescolumn[cid],$coursescolumn[code],$coursescolumn[title] FROM $coursestable WHERE $coursescolumn[active]='1' AND $coursescolumn[enroll]='"._LNSTUDENT_ENROLL."'  ORDER BY $coursescolumn[code]");
		}
		// list only own course
		else {
			$result = $dbconn->Execute("SELECT $coursescolumn[cid],$coursescolumn[code],$coursescolumn[title] FROM $coursestable WHERE $coursescolumn[author]=".lnSessionGetVar('uid')." AND $coursescolumn[active]='1'  AND $coursescolumn[enroll]='"._LNSTUDENT_ENROLL."' ORDER BY  $coursescolumn[code]");
		}
		if (!empty($cid)) { // select course
			$cidselect[$cid] = "selected";
		}
		

		// 1 <=======
		echo '<tr><td width=15% align=right><B>'._SELECTCOURSE.'</B>:</td> ';
		echo "<td><SELECT CLASS=\"select\" NAME=\"cidselect\" onchange=\"window.open(this.options[this.selectedIndex].value,'_self');\">";
		echo "<OPTION>"._SELECTACOURSE."</OPTION>";

		while(list($cid, $coursecode, $coursename) = $result->fields) {
			$result->MoveNext();
			echo '<OPTION VALUE="index.php?mod=Submissions&amp;file=admin&amp;op=add_form&amp;cid='.$cid.'" '.$cidselect[$cid].'>'.$coursecode.':'.$coursename.'</OPTION>';
		}
		echo '</SELECT></td></tr>';
	}
	else {
		$result = $dbconn->Execute("SELECT $coursescolumn[sequence] FROM $coursestable WHERE $coursescolumn[cid]='$e_cid'");
		list($sequence) = $result->fields;	// default instructor or course author
		echo '<tr><td width=15% align=right><B>'._SELECTCOURSE.'</B>:</td> ';
		echo "<td><B>&quot;$e_coursename&quot;</B></td></tr>";
	}


// 4 <======== Instructor selection 
	echo '<tr><td bgcolor=#FFFFFF width=15% align=right><B>'._INSTRUCTOR.'</B> :</td>';

	$query = "SELECT $group_membershipcolumn[uid],$userscolumn[uname],$userscolumn[name] 
						FROM $groupstable,$group_membershiptable, $userstable
						WHERE $group_membershipcolumn[gid]=$groupscolumn[gid] and $group_membershipcolumn[uid]=$userscolumn[uid] and $groupscolumn[type]="._LNGROUP_INSTRUCTOR."
						ORDER BY $userscolumn[uname]";

	$result = $dbconn->Execute($query);

	echo '<td>';

	echo '<SELECT class="select" NAME="instructor" >';
	$select_inst[$e_instructor] = "selected";
	while(list($uid,$uname,$name) = $result->fields) {
		$result->MoveNext();
		echo '<OPTION VALUE="'.$uid.'" '.$select_inst[$uid].'>'.$uname.'</OPTION>';
	}
	echo '</SELECT>';



	// show javascript calendar popup
	if (lnConfigGetVar('language') == 'tha' ) {
?>
	<SCRIPT LANGUAGE="JavaScript">
		var MONTH_NAMES=new Array('มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.');
		var DAY_NAMES=new Array('อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์','อ.','จ.','อ.','พ.','พ.','ศ.','ส.');
		function CalendarPopup(){var c;if(arguments.length>0){c = new PopupWindow(arguments[0]);}else{c = new PopupWindow();c.setSize(150,175);}c.offsetX = -152;c.offsetY = 25;c.autoHide();c.monthNames = new Array('มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม');c.monthAbbreviations = new Array('ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.');c.dayHeaders = new Array("อ","จ","อ","พ","พ","ศ","ส");c.returnFunction = "CP_tmpReturnFunction";c.returnMonthFunction = "CP_tmpReturnMonthFunction";c.returnQuarterFunction = "CP_tmpReturnQuarterFunction";c.returnYearFunction = "CP_tmpReturnYearFunction";c.weekStartDay = 0;c.isShowYearNavigation = false;c.displayType = "date";c.disabledWeekDays = new Object();c.disabledDatesExpression = "";c.yearSelectStartOffset = 2;c.currentDate = null;c.todayText="Today";c.cssPrefix="";c.isShowYearNavigationInput=false;window.CP_targetInput = null;window.CP_dateFormat = "MM/dd/yyyy";c.setReturnFunction = CP_setReturnFunction;c.setReturnMonthFunction = CP_setReturnMonthFunction;c.setReturnQuarterFunction = CP_setReturnQuarterFunction;c.setReturnYearFunction = CP_setReturnYearFunction;c.setMonthNames = CP_setMonthNames;c.setMonthAbbreviations = CP_setMonthAbbreviations;c.setDayHeaders = CP_setDayHeaders;c.setWeekStartDay = CP_setWeekStartDay;c.setDisplayType = CP_setDisplayType;c.setDisabledWeekDays = CP_setDisabledWeekDays;c.addDisabledDates = CP_addDisabledDates;c.setYearSelectStartOffset = CP_setYearSelectStartOffset;c.setTodayText = CP_setTodayText;c.showYearNavigation = CP_showYearNavigation;c.showCalendar = CP_showCalendar;c.hideCalendar = CP_hideCalendar;c.getStyles = getCalendarStyles;c.refreshCalendar = CP_refreshCalendar;c.getCalendar = CP_getCalendar;c.select = CP_select;c.setCssPrefix = CP_setCssPrefix;c.showYearNavigationInput = CP_showYearNavigationInput
return c;}
</SCRIPT>
<?	
	}
	else {
?>
	<SCRIPT LANGUAGE="JavaScript">
		var MONTH_NAMES=new Array('January','February','March','April','May','June','July','August','September','October','November','December','Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');
		var DAY_NAMES=new Array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','S','M','T','W','T','F','S');
		function CalendarPopup(){var c;if(arguments.length>0){c = new PopupWindow(arguments[0]);}else{c = new PopupWindow();c.setSize(150,175);}c.offsetX = -152;c.offsetY = 25;c.autoHide();c.monthNames = new Array('January','February','March','April','May','June','July','August','September','October','November','December');c.monthAbbreviations = new Array('Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec');c.dayHeaders = new Array('S','M','T','W','T','F','S');c.returnFunction = "CP_tmpReturnFunction";c.returnMonthFunction = "CP_tmpReturnMonthFunction";c.returnQuarterFunction = "CP_tmpReturnQuarterFunction";c.returnYearFunction = "CP_tmpReturnYearFunction";c.weekStartDay = 0;c.isShowYearNavigation = false;c.displayType = "date";c.disabledWeekDays = new Object();c.disabledDatesExpression = "";c.yearSelectStartOffset = 2;c.currentDate = null;c.todayText="Today";c.cssPrefix="";c.isShowYearNavigationInput=false;window.CP_targetInput = null;window.CP_dateFormat = "MM/dd/yyyy";c.setReturnFunction = CP_setReturnFunction;c.setReturnMonthFunction = CP_setReturnMonthFunction;c.setReturnQuarterFunction = CP_setReturnQuarterFunction;c.setReturnYearFunction = CP_setReturnYearFunction;c.setMonthNames = CP_setMonthNames;c.setMonthAbbreviations = CP_setMonthAbbreviations;c.setDayHeaders = CP_setDayHeaders;c.setWeekStartDay = CP_setWeekStartDay;c.setDisplayType = CP_setDisplayType;c.setDisabledWeekDays = CP_setDisabledWeekDays;c.addDisabledDates = CP_addDisabledDates;c.setYearSelectStartOffset = CP_setYearSelectStartOffset;c.setTodayText = CP_setTodayText;c.showYearNavigation = CP_showYearNavigation;c.showCalendar = CP_showCalendar;c.hideCalendar = CP_hideCalendar;c.getStyles = getCalendarStyles;c.refreshCalendar = CP_refreshCalendar;c.getCalendar = CP_getCalendar;c.select = CP_select;c.setCssPrefix = CP_setCssPrefix;c.showYearNavigationInput = CP_showYearNavigationInput
return c;}
	</SCRIPT>
<?
	}
?>

	<SCRIPT language=JavaScript src="javascript/CalendarPopup.js"></SCRIPT>
	<SCRIPT language=JavaScript>document.write(getCalendarStyles());</SCRIPT>
	<SCRIPT language=JavaScript>
	var now = new Date();
	var cal = new CalendarPopup("div");
	cal.addDisabledDates(null,formatDate(now,"yyyy-MM-dd"));
	</SCRIPT>
	<DIV id=div 
	style="VISIBILITY: hidden; POSITION: absolute; BACKGROUND-COLOR: #78BCBC; layer-background-color: #FFFFFF"></DIV>
	
	</td></tr>
	<tr><td bgcolor=#FFFFFF width=20%><B><?=_STARTDATE?></B> :</td>
	<td>
	
	<INPUT class=input TYPE="text" NAME="start" size=10 VALUE="<?=$e_start?>">
	<A id=anchor title="" onclick="javascript: cal.select(document.forms[0].start,'anchor','dd-MM-yyyy'); return false; " href="#" name=anchor><IMG align=absmiddle SRC="modules/Submissions/images/calendar.gif" WIDTH="24" HEIGHT="24" BORDER=0 ALT="Select Date"></A> (dd-mm-yyyy)<BR>

	</td>
<?

	echo '<tr><td bgcolor=#FFFFFF>&nbsp;</td><td bgcolor=#FFFFFF width=97% align=left valign="top">';

	$studentcheck[$e_student] = "checked";
	if (empty($id)) { // default setting
		$studentcheck[_LNSTUDENT_ENROLL]="checked";	
	}
//	echo '<INPUT TYPE="radio" NAME="student" '.$studentcheck[1].' VALUE="'._LNSTUDENT_ENROLL.'">'._ALLOWENROLL.'<BR>';
//	echo '<INPUT TYPE="radio" NAME="student" '.$studentcheck[2].' VALUE="'._LNSTUDENT_USER.'">'._ALLOWALLUSERS.'<BR>';
//	echo '<INPUT TYPE="radio" NAME="student" '.$studentcheck[3].' VALUE="'._LNSTUDENT_GUEST.'">'._ALLOWGUEST.'<BR>';
//	echo '<INPUT TYPE="checkbox" NAME="student" '.$studentcheck[1].' VALUE="'._LNSTUDENT_ENROLL.'">'._ALLOWENROLL.'<BR>';
	echo '</td></tr>';

	// 5 <=========
	$activechecked[$e_active]="checked";

	echo '<tr><td bgcolor=#FFFFFF height=20>&nbsp;</td><td bgcolor=#FFFFFF align=left>';
	if (empty($id)) {
		echo '<INPUT TYPE="button" value="Submit" class="button_org" onclick="formSubmit()">';
	}
	else {
		echo '<BR><INPUT TYPE="checkbox" NAME="active" VALUE="1" '.$activechecked[1].'> Activate ?<BR><BR>';
		echo '<INPUT TYPE="submit" value="Save" class="button_org">';
	}
	echo " <INPUT class=\"button_org\" TYPE=button value=\"Cancel\" Onclick=\"javascript: window.open('index.php?mod=Submissions&amp;file=admin','_self')\">";
	echo '</td></tr>';
	echo '</table>';

	echo '</center>';
	echo '</FORM>';
	CloseTable();
}



/*
* get next sid
*/
function getMaxSID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	$query = "SELECT MAX($course_submissionscolumn[sid]) FROM $course_submissionstable";
	$result = $dbconn->Execute($query);
	list ($maxsid) = $result->fields;
	
	return $maxsid + 1;
}

/*
* get next eid
*/
function getMaxEID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$query = "SELECT MAX($course_enrollscolumn[eid]) FROM $course_enrollstable";
	$result = $dbconn->Execute($query);
	list ($maxeid) = $result->fields;
	
	return $maxeid + 1;
}

?>