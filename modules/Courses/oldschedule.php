<?php
/**
* Schedule administration
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}


/**
* schedule functions
*/
function schedule($vars) {
	global $menus, $links;

	// Get arguments from argument array
    extract($vars);

	
	/** Navigator **/
	$courseinfo = lnCourseGetVars($cid);
	$menus[]= $courseinfo['title'];
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
	
	tabCourseAdmin($cid,4);
	
	echo '</TD></TR><TR><TD>';

	echo '<table class="main" width= 100% cellpadding=0 cellspacing=0  border=0>';
	echo '<tr><td align=center valign=top>';
	
	switch ($action) {
		case "add_schedule_form" :	addScheduleForm($vars); break;
		case "add_schedule" :				addSchedule($vars); break;
		case "delete_schedule" :			deleteSchedule($vars); break;
		case "update_schedule" :			updateSchedule($vars); break;
		default :												showScheduleList($vars); break;
	}

	echo '</td></tr></table>';
	
	echo '</TD></TR></TABLE>';

	include 'footer.php';
}


/**
* show submissions list 
*/
function showScheduleList($vars) {
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
		if ($order == "start" || $order == "enroll" || $order == "amountstd" || $order == "limitstd") {
			$orderby = "$course_submissionscolumn[$order]";
		}
		else if ($order == "uname" || $order == "name") {
			$orderby = $userscolumn[$order];		
		}
	}
	else {
		$orderby = $course_submissionscolumn[start];
	}

	if (!empty($sort)) {
		if ($sort == "up") {
			$orderby .= " ASC";
			$sortdir = "down";
			$arrow="<IMG SRC='images/global/arrowup.gif' WIDTH=10 HEIGHT=9 BORDER=0>";
		}
		else {
			$orderby .= " DESC";
			$sortdir = "up";
			$arrow="<IMG SRC='images/global/arrowdown.gif' WIDTH=10 HEIGHT=9 BORDER=0>";
		}
	}
	else {
		$sortdir='down';
	}

	$pagesize = lnConfigGetVar('pagesize');
	if (!isset($page)) {
		$page = 1;
	}
	$min = $pagesize * ($page - 1); // This is where we start our record set from
	$max = $pagesize; // This is how many rows to select

	$count = "SELECT COUNT($course_submissionscolumn[sid]) FROM $coursestable, $userstable, $course_submissionstable WHERE $course_submissionscolumn[cid]=$coursescolumn[cid] and $course_submissionscolumn[instructor]=$userscolumn[uid]";

	if (!lnUserAdmin(lnSessionGetVar('uid'))) {
		$query = " and $userscolumn[uid]='".lnSessionGetVar('uid')."'";
	}

	if (!empty($where)) {
		$where = "$where";
	} else {
		$where = '';
	}

	$where .= " AND $course_submissionscolumn[cid]=$cid ";

	$resultcount = $dbconn->Execute($count . $query . $where);
	list ($numrows) = $resultcount->fields;
	$resultcount->Close();


	$myquery = buildQuery (array ('users', 'courses','course_submissions'),
                        array ($course_submissionscolumn['sid'], 
						$course_submissionscolumn['cid'],
						$course_submissionscolumn['start'],
						$course_submissionscolumn['instructor'],
						$course_submissionscolumn['enroll'],
						$course_submissionscolumn['active'],
						$course_submissionscolumn['amountstd'],
						$course_submissionscolumn['limitstd'],
						$coursescolumn['code'],
						$coursescolumn['title'],
						$userscolumn['uname'],
						$userscolumn['name']),
                         "$course_submissionscolumn[cid]=$coursescolumn[cid] and $course_submissionscolumn[instructor]=$userscolumn[uid] " . $query . $where,
                         array($orderby),
                         $max, $min);

	$result = $dbconn->Execute($myquery);

	echo '<table width= 98% cellpadding=0 cellspacing=0 border=0>';
	echo '<tr>';
	echo '<td align=right><A HREF="index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;action=add_schedule_form&amp;cid='.$cid.'"><IMG SRC="modules/Courses/images/create.gif" BORDER=0 ALT=""></A>&nbsp;&nbsp;&nbsp;</td></tr></table>';

	if ($numrows > 0) {	
		echo '<table width=98% cellpadding=2 cellspacing=1 border=0 class="list">';
		echo '<TR>';
		echo '<TD class="head" WIDTH=100 align=center><A class="invert" HREF="index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;cid='.$cid.'&amp;order=start&amp;sort='.$sortdir.'">'._STARTDATE;
			if ($order=="start") echo ' ' . $arrow;
		echo '</A></TD>';
		echo '<TD class="head" WIDTH=80 align=center><A  CLASS="invert" HREF="index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;cid='.$cid.'&amp;order=uname&amp;sort='.$sortdir.'">'._INSTRUCTOR;
			if ($order=="uname") echo ' ' . $arrow;
		echo '</A></TD>';
		echo '<TD class="head" align=center><A  CLASS="invert" HREF="index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;cid='.$cid.'&amp;order=name&amp;sort='.$sortdir.'">'._NAME;
			if ($order=="name") echo ' ' . $arrow;
		echo '</A></TD>';
		echo '<TD class="head" WIDTH=100 align=center><A  CLASS="invert" HREF="index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;cid='.$cid.'&amp;order=enroll&amp;sort='.$sortdir.'">'._ENROLL;
			if ($order=="enroll") echo ' ' . $arrow;
		echo '</A></TD>';
		echo '<TD class="head" WIDTH=100 align=center><A class="invert" HREF="index.php?mod=Courses&file=admin&op=schedule&cid='.$cid.'&order=amountstd&sort='.$sortdir.'">'._AMOUNTSTD;
 			if ($order=="amountstd") echo ' ' . $arrow;
 		echo '</A></TD>';
 		echo '<TD class="head" WIDTH=100 align=center><A class="invert" HREF="index.php?mod=Courses&file=admin&op=schedule&cid='.$cid.'&order=limitstd&sort='.$sortdir.'">'._LIMITSTD;
 			if ($order=="limitstd") echo ' ' . $arrow;
 		echo '</A></TD>';
		echo '<TD class="head" WIDTH=40>&nbsp;</TD></TR>';
		for ($i=1; list($sid,$cid_l,$start,$instructor,$enroll,$active,$amount,$limit,$coursecode,$coursename,$uname,$name) = $result->fields; $i++) {
			$result->MoveNext();
			if (Date_Calc::isValidDate2($start)) {
				$sstart = Date_Calc::dateFormat2($start,"%e %B %Y");
			}
			else {
				$sstart = '-';
			}
			if ($enroll == 1) {
				$enroll = '<IMG SRC="modules/Courses/images/pro_dot.gif" WIDTH="14" HEIGHT="12" BORDER="0" ALT="/">';
			}
			else {
				$enroll = '-';
			}

			if ($active == 1) {
				$link = '<A HREF="index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;action=add_schedule_form&amp;cid='.$cid_l.'&amp;sid='.$sid.'">'; 
			}
			else {
				$link = '<A class=gray HREF="index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;action=add_schedule_form&amp;cid='.$cid_l.'&amp;sid='.$sid.'">'; 
			}

			echo '<TR class="list"><TD align=center>'.$link.$sstart.'</A></TD><TD align=left>'.$link.$uname.'</A></TD><TD align=left>'.$link.$name.'</A></TD><TD align=center>'.$enroll.'</TD>';
			echo '<TD align=center>'.$amount.'</A></TD><TD align=center>'.$limit.'</A></TD>';
			echo '<TD ALIGN="CENTER">';
			echo "<A HREF=\"index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;action=add_schedule_form&amp;cid=$cid&amp;sid=$sid\"><IMG SRC=images/global/edit.gif BORDER=0 ALT='edit'></A>";
			echo "&nbsp;<A HREF=\"javascript:if(confirm('Delete ?')) window.open('index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;action=delete_schedule&amp;cid=$cid&amp;sid=$sid','_self')\"><IMG SRC=images/global/delete.gif BORDER=0 ALT='delete'></A></TD></TR>";
		}
		echo '</table>';

	
	/* show pages */

		echo "<center>";
		 
		 if ($numrows  > $pagesize) {
			 $total_pages = ceil($numrows / $pagesize); // How many pages are we dealing with here ??
			 $prev_page = $page - 1;
			 echo '<BR>';
			  if ( $prev_page > 0 ) {
				echo '<A HREF="index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;cid='.$cid.'&amp;order='.$order.'&amp;page='.$prev_page.'&amp;sort='.$sort.'&amp;keyword='.$keyword.'"><IMG SRC="images/back.gif" WIDTH="19" HEIGHT="9" BORDER="0" ALT=""></A>';
			  }
			  for($n=1; $n <= $total_pages; $n++) {
				if ($n == $page) {
					echo "<B><U>$n</U></B> ";
				}
				else {
					echo '<A HREF="index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;cid='.$cid.'&amp;order='.$order.'&amp;page='.$n.'&amp;sort='.$sort.'&amp;keyword='.$keyword.'">'.$n.'</A> ';
				}
			  } 
			  $next_page = $page + 1;
			  if ( $next_page <= $total_pages ) {
				  echo '<A HREF="index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;cid='.$cid.'&amp;order='.$order.'&amp;page='.$next_page.'&amp;sort='.$sort.'&amp;keyword='.$keyword.'"><IMG SRC="images/next.gif" WIDTH="19" HEIGHT="9" BORDER="0" ALT=""></A> ';
			  }
		 }

		echo '<BR><BR><B>= '._TOTALCOURSES.'&nbsp;'.$numrows.'</B> =<BR>';
		echo '</center>';
		echo '</td></tr>';
		echo '</table>';

		}	
		else {
			echo '<BR><BR><B>'._NOSCHEDULE.'</B>';
		}

}


/** 
* add Schedule Form
*/
function addScheduleForm($vars) {
	// Get arguments from argument array
    extract($vars);

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
			var start = document.forms.submission.start.value;
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
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="schedule">';

	//edit submission
	if (!empty($sid)) {
		$result = $dbconn->Execute("SELECT * FROM $course_submissionstable WHERE $course_submissionscolumn[sid]='". lnVarPrepForStore($sid) ."'");
		list($e_sid, $e_cid, $e_start, $e_instructor, $e_enroll, $e_active) = $result->fields;
		echo '<INPUT TYPE="hidden" NAME="action" VALUE="update_schedule">';
	    echo '<INPUT TYPE="hidden" NAME="sid" VALUE="'.$e_sid.'">';
	    echo '<INPUT TYPE="hidden" NAME="cid" VALUE="'.$e_cid.'">';
		list($y,$m,$d) = explode('-',$e_start);
		$e_start = "$d-$m-$y";
	}
	
	// add submission
	else {
		 echo '<INPUT TYPE="hidden" NAME="action" VALUE="add_schedule">';
		 echo '<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">';
   }
	
	// find instructor after select course
	if (!empty($cid) &&empty($id) && empty($sid) && $action="add_schuedule_form") {
		$result = $dbconn->Execute("SELECT $coursescolumn[author] FROM $coursestable WHERE $coursescolumn[cid]='$cid'");
		list($e_instructor) = $result->fields;	// default instructor or course author
	}

	echo '<center>';
	echo '<BR><fieldset ><legend>'._CREATESCHEDULE.'</legend>';
	echo '<table width=100% border=0 cellpadding=3 cellspacing=0>';


// 1 <======== Instructor selection 
	echo '<tr><td bgcolor=#FFFFFF width=15% align=right><B>'._INSTRUCTOR.'</B> :</td>';

	$query = "SELECT $group_membershipcolumn[uid],$userscolumn[uname],$userscolumn[name] 
						FROM $groupstable,$group_membershiptable, $userstable
						WHERE $group_membershipcolumn[gid]=$groupscolumn[gid] and $group_membershipcolumn[uid]=$userscolumn[uid] and $groupscolumn[type]="._LNGROUP_INSTRUCTOR."
						ORDER BY $userscolumn[uname]";

	$result = $dbconn->Execute($query);

	echo '<td>';

	echo '<SELECT  NAME="instructor" >';
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
		var MONTH_NAMES=new Array('เธกเธเธฃเธฒเธเธก','เธเธธเธกเธ เธฒเธเธฑเธเธเน','เธกเธตเธเธฒเธเธก','เน€เธกเธฉเธฒเธขเธ','เธเธคเธฉเธ เธฒเธเธก','เธกเธดเธ–เธธเธเธฒเธขเธ','เธเธฃเธเธเธฒเธเธก','เธชเธดเธเธซเธฒเธเธก','เธเธฑเธเธขเธฒเธขเธ','เธ•เธธเธฅเธฒเธเธก','เธเธคเธจเธเธดเธเธฒเธขเธ','เธเธฑเธเธงเธฒเธเธก','เธก.เธ.','เธ.เธ.','เธกเธต.เธ.','เน€เธก.เธข.','เธ.เธ.','เธกเธด.เธข.','เธ.เธ.','เธช.เธ.','เธ.เธข.','เธ•.เธ.','เธ.เธข.','เธ.เธ.');
		var DAY_NAMES=new Array('เธญเธฒเธ—เธดเธ•เธขเน','เธเธฑเธเธ—เธฃเน','เธญเธฑเธเธเธฒเธฃ','เธเธธเธ','เธเธคเธซเธฑเธชเธเธ”เธต','เธจเธธเธเธฃเน','เน€เธชเธฒเธฃเน','เธญ.','เธ.','เธญ.','เธ.','เธ.','เธจ.','เธช.');
		function CalendarPopup(){var c;if(arguments.length>0){c = new PopupWindow(arguments[0]);}else{c = new PopupWindow();c.setSize(150,175);}c.offsetX = -152;c.offsetY = 25;c.autoHide();c.monthNames = new Array('เธกเธเธฃเธฒเธเธก','เธเธธเธกเธ เธฒเธเธฑเธเธเน','เธกเธตเธเธฒเธเธก','เน€เธกเธฉเธฒเธขเธ','เธเธคเธฉเธ เธฒเธเธก','เธกเธดเธ–เธธเธเธฒเธขเธ','เธเธฃเธเธเธฒเธเธก','เธชเธดเธเธซเธฒเธเธก','เธเธฑเธเธขเธฒเธขเธ','เธ•เธธเธฅเธฒเธเธก','เธเธคเธจเธเธดเธเธฒเธขเธ','เธเธฑเธเธงเธฒเธเธก');c.monthAbbreviations = new Array('เธก.เธ.','เธ.เธ.','เธกเธต.เธ.','เน€เธก.เธข.','เธ.เธ.','เธกเธด.เธข.','เธ.เธ.','เธช.เธ.','เธ.เธข.','เธ•.เธ.','เธ.เธข.','เธ.เธ.');c.dayHeaders = new Array("เธญ","เธ","เธญ","เธ","เธ","เธจ","เธช");c.returnFunction = "CP_tmpReturnFunction";c.returnMonthFunction = "CP_tmpReturnMonthFunction";c.returnQuarterFunction = "CP_tmpReturnQuarterFunction";c.returnYearFunction = "CP_tmpReturnYearFunction";c.weekStartDay = 0;c.isShowYearNavigation = false;c.displayType = "date";c.disabledWeekDays = new Object();c.disabledDatesExpression = "";c.yearSelectStartOffset = 2;c.currentDate = null;c.todayText="Today";c.cssPrefix="";c.isShowYearNavigationInput=false;window.CP_targetInput = null;window.CP_dateFormat = "MM/dd/yyyy";c.setReturnFunction = CP_setReturnFunction;c.setReturnMonthFunction = CP_setReturnMonthFunction;c.setReturnQuarterFunction = CP_setReturnQuarterFunction;c.setReturnYearFunction = CP_setReturnYearFunction;c.setMonthNames = CP_setMonthNames;c.setMonthAbbreviations = CP_setMonthAbbreviations;c.setDayHeaders = CP_setDayHeaders;c.setWeekStartDay = CP_setWeekStartDay;c.setDisplayType = CP_setDisplayType;c.setDisabledWeekDays = CP_setDisabledWeekDays;c.addDisabledDates = CP_addDisabledDates;c.setYearSelectStartOffset = CP_setYearSelectStartOffset;c.setTodayText = CP_setTodayText;c.showYearNavigation = CP_showYearNavigation;c.showCalendar = CP_showCalendar;c.hideCalendar = CP_hideCalendar;c.getStyles = getCalendarStyles;c.refreshCalendar = CP_refreshCalendar;c.getCalendar = CP_getCalendar;c.select = CP_select;c.setCssPrefix = CP_setCssPrefix;c.showYearNavigationInput = CP_showYearNavigationInput
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
	<tr><td bgcolor=#FFFFFF align=right ><B><?=_STARTDATE?></B> :</td>
	<td>
	
	<INPUT TYPE="text" NAME="start" size=10 VALUE="<?=$e_start?>">
	<A id=anchor title="" onclick="javascript: cal.select(document.forms[0].start,'anchor','dd-MM-yyyy'); return false; " href="#" name=anchor><IMG align=absmiddle SRC="modules/Submissions/images/calendar.gif" WIDTH="24" HEIGHT="24" BORDER=0 ALT="Select Date"></A> (dd-mm-yyyy)<BR>

	</td>
	<tr><td bgcolor=#FFFFFF align=center ><B><?=_LIMITSTD?></B> :</td>
	<td> 
	<INPUT TYPE="text" NAME="limit" size=10 VALUE="<?=$e_limit?>">
	<?echo '* 0 = '._UNLIMITSTD;?>
	</td>
<?

	echo '<tr><td>&nbsp;</td><td width=97% align=left valign="top">';

	$enrollcheck[$e_enroll] = "checked";
	if (empty($sid)) { // default setting
		$enrollcheck[_LNSTUDENT_ENROLL]="checked";	
	}
	echo '<INPUT TYPE="checkbox" NAME="enroll" '.$enrollcheck[_LNSTUDENT_ENROLL].' VALUE="'._LNSTUDENT_ENROLL.'">'._MUSTENROLL;
	echo '</td></tr>';

	// 2 <=========
	$activechecked[$e_active]="checked";

	echo '<tr><td bgcolor=#FFFFFF>&nbsp;</td><td bgcolor=#FFFFFF align=left>';
	if (empty($sid)) {
		echo '<BR><INPUT class="button" TYPE="button" value="'._ADDSCHEDULE.'" class="button_org" onclick="formSubmit()">';
	}
	else {
		echo '<INPUT TYPE="checkbox" NAME="active" VALUE="1" '.$activechecked[1].'> '._SCHEDULEACTIVATE;
		echo '<BR><BR><INPUT class="button" TYPE="submit" value="'._EDITSCHEDULE.'" onclick="formSubmit()">';
	}
	echo " <INPUT class=\"button\" TYPE=button value=\""._CANCEL."\" Onclick=\"javascript: window.open('index.php?mod=Courses&amp;file=admin&amp;op=schedule&amp;cid=$cid','_self')\">";
	echo '</td></tr>';
	echo '</table>';

	echo '</fieldset>';

	echo '</center>';
	echo '</FORM>';

}


/**
* Insert Schedule 
*/
function addSchedule($vars) {
	// Get arguments from argument array
    extract($vars);

	if (!empty($cid)) {

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$course_submissionstable = $lntable['course_submissions'];
		$course_submissionscolumn = &$lntable['course_submissions_column'];
		
		$enroll = $enroll =='' ? 0:$enroll;
		list($d,$m,$y) = explode('-',$start);
		$start = "$y-$m-$d";
		$maxsid = getMaxSID();

		// insert into submissions table
		$query = "INSERT INTO $course_submissionstable 
							(	$course_submissionscolumn[sid],
								$course_submissionscolumn[cid],
								$course_submissionscolumn[start],
								$course_submissionscolumn[instructor],
								$course_submissionscolumn[enroll],
								$course_submissionscolumn[active],
								$course_submissionscolumn[amountstd],
								$course_submissionscolumn[limitstd]
							) 
							VALUES ( '".$maxsid."',
							'". lnVarPrepForStore($cid) ."',
							'". lnVarPrepForStore($start) ."',
							'". lnVarPrepForStore($instructor) ."',
							'". lnVarPrepForStore($enroll) ."',
							'1',
							'". lnVarPrepForStore($amount) ."',
							'". lnVarPrepForStore($limit) ."'
							)";	
							//by Xeonkung

		$result = $dbconn->Execute($query);
	}

	showScheduleList($vars);
}


/**
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


/**
* Delete schedule
*/
function deleteSchedule($vars) {
	// Get arguments from argument array
    extract($vars);

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

	showScheduleList($vars);
}


/**
* update Schedule
*/
function updateSchedule($vars) {
	// Get arguments from argument array
    extract($vars);

	list($d,$m,$y) = explode('-',$start);
	$start = "$y-$m-$d";

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];

	//1. update submissions table
	if (empty($active)) $active='0';
	if (empty($enroll)) $enroll='0';

	$query = "UPDATE $course_submissionstable SET
							$course_submissionscolumn[cid] = '". lnVarPrepForStore($cid) ."',
							$course_submissionscolumn[start] = '". lnVarPrepForStore($start) ."',
							$course_submissionscolumn[instructor] = '". lnVarPrepForStore($instructor) ."',
							$course_submissionscolumn[enroll] = '". lnVarPrepForStore($enroll) ."',
							$course_submissionscolumn[active] = '". lnVarPrepForStore($active) ."',
							$course_submissionscolumn[limitstd] = '". lnVarPrepForStore($limit) ."'			//by Xeonkung
							WHERE $course_submissionscolumn[sid] = '". lnVarPrepForStore($sid) ."'";
	$result = $dbconn->Execute($query);
	
	showScheduleList($vars);
}
?>