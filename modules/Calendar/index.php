<?php
/**
*  Calendar
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Calender::', "::", ACCESS_COMMENT)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}


/* - - - - - - - - - - - */
include 'header.php';

OpenTable();

$vars= array_merge($_GET,$_POST);

//echo lnBlockTitle($mod);
echo '<p class="header"><b>'._CALENDAR_TITLE.'</b></p>';
echo '<BR>'._CALENDAR_DESC;

/* options */
switch ($op) {
	case "editevent":	addEventForm($vars);break;
	case "addevent":	addEventDone($vars);break;
	case "showevent":	showEvent($vars);break;
	case "deleteevent":	deleteEvent($vars);break;
	default :						calendar($vars);
}

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */

/*- - - show main body calendar - - -*/
function calendar($vars) {
	// Get arguments from argument array
    extract($vars);
	
	if(!isset($show)) $show='';
	if ($show=="") {					// if no parameters
	    $day = (int)date('d');
		$month = (int)date('n');
		$year = (int)date('Y');
		$show="day";
	}

	$day=(int)$day;
	$mon=(int)$month;
	$year=(int)$year;

	showCalMenu($show,$day,$month,$year);
	
	if ($show == "day")
		showDay($day,$month,$year);
	else if ($show == "week")
		showWeek($day,$month,$year);
	else if ($show == "month")
		showMonth($day,$month,$year);
	else if ($show == "year")
		showYear($day,$month,$year);

	 echo "<P>";
}

/*- - - Calendar Menu - - -*/
function showCalMenu($show,$d,$m,$y) {

	echo "<TABLE width=100%>";
	echo "<TR><TD align=left>";
	if ($show == "day")
		echo "&nbsp;&nbsp;&nbsp;<IMG SRC='modules/Calendar/images/day.gif' BORDER=0> Day";
	else 
		echo "&nbsp;&nbsp;&nbsp; <A HREF='index.php?mod=Calendar&show=day&day=$d&month=$m&year=$y' class=f><IMG SRC='modules/Calendar/images/day.gif' BORDER=0> Day</A>";
	if ($show == "week")
		echo "&nbsp;&nbsp;&nbsp;<IMG SRC='modules/Calendar/images/week.gif' BORDER=0> Week";
	else 
		echo "&nbsp;&nbsp;&nbsp; <A HREF='index.php?mod=Calendar&show=week&day=$d&month=$m&year=$y' class=f><IMG SRC='modules/Calendar/images/week.gif' BORDER=0> Week</A>";
	if ($show == "month")
		echo "&nbsp;&nbsp;&nbsp;<IMG SRC='modules/Calendar/images/month.gif' BORDER=0> Month";
	else 
		echo "&nbsp;&nbsp;&nbsp; <A HREF='index.php?mod=Calendar&show=month&day=$d&month=$m&year=$y' class=f><IMG SRC='modules/Calendar/images/month.gif' BORDER=0> Month</A>";
	if ($show == "year")
		echo "&nbsp;&nbsp;&nbsp;<IMG SRC='modules/Calendar/images/year.gif' BORDER=0> Year";
	else 
		echo "&nbsp;&nbsp;&nbsp; <A HREF='index.php?mod=Calendar&show=year&day=$d&month=$m&year=$y' class=f><IMG SRC='modules/Calendar/images/year.gif' BORDER=0> Year</A>";
	
	echo "</TD><TD align=right valign=top>";
	echo "<A HREF='index.php?mod=Calendar&amp;op=editevent' class=f><img src=modules/Calendar/images/editdoc.gif border=0></A>";
	echo "</TD></TR></TABLE>";
}


/*- - - แสดงแบบวันที่- - - -*/
function showDay($day, $month, $year){

?>
<P>

<!-- Heading -->

<TABLE width="100%" border="0" cellspacing=0 cellpadding=1 bgcolor="#0066CC">
	<TR>
	<TD align="left" valign="middle">
		<A class=white href="<?php echo $PHP_SELF."?mod=Calendar&show=day&".Date_Calc::prevDay($day,$month,$year,"year=%Z&month=%m&day=%d"); ?>">&lt;&lt;Back</A>
	</TD>
	<TD ALIGN="CENTER">	
<?php
	echo "<B><FONT COLOR=#FFFFFF>".Date_Calc::dateFormat($day,$month,$year,"%e %B %Y")."</FONT></B>";
?></TD>
	<TD align="right" valign="center">
		<A class=white href="<?php echo $PHP_SELF."?mod=Calendar&show=day&".Date_Calc::nextDay($day,$month,$year,"year=%Z&month=%m&day=%d"); ?>">Next&gt;&gt;</A>
	</TD>
	</TR>
</TABLE>

<!-- Heading -->

<!-- SHOW EVENT -->

<TABLE width=100% cellspacing=1 cellpadding=1 border=0 bgcolor="#FFD7AE">
<TR>
	<TD colspan=2 width=60 align=right><B>All Day:</B></TD>
	<TD bgcolor="#EEEEEE">

<?
	//<- - - All Day, TimeType=1
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$calendartable = $lntable['calendar'];
	$calendarcolumn = &$lntable['calendar_column'];

	$query = "SELECT $calendarcolumn[calid], $calendarcolumn[title] FROM $calendartable WHERE $calendarcolumn[uid] ='".lnSessionGetVar('uid')."' AND $calendarcolumn[date]='$year-$month-$day' AND $calendarcolumn[timetype]='1'";

	$result = $dbconn->Execute($query);
	for($j=0; list($eventid,$title)=$result->fields; $j++) {
			$result->MoveNext();
			$title=stripslashes($title);
//			$title=filter($title,1);
			echo "&nbsp;<IMG SRC='modules/Calendar/images/bluedot.gif' > <A HREF=index.php?mod=Calendar&amp;op=showevent&eid=$eventid class=c>$title</A>";
	}

?>

	</TD>
</TR>

<?
	//<- - - Show event, TimeType=0
	//set start and end time is 8.00  to 17.00	
	$starttime=8;
	$endtime=17;
	
	$query="SELECT Min($calendarcolumn[timestart]),Max($calendarcolumn[timestart]) FROM $calendartable WHERE $calendarcolumn[uid]='".lnSessionGetVar('uid')."' and $calendarcolumn[date]='$year-$month-$day' ";
	$result = $dbconn->Execute($query);

	list($start_time,$end_time) = $result->fields;
	@list($shour,$smin,$ssec)=explode(":",$start_time);
	@list($ehour,$emin,$ssec)=explode(":",$end_time);
	if ($shour<$starttime && $shour)
		$starttime=(int)$shour;
	if ($ehour>$endtime &&$ehour)
		$endtime=(int)$ehour;

	for ($i=$starttime;$i<=$endtime;$i++) {
		$is=($i>12) ? $i-12:$i;

		$query = "SELECT $calendarcolumn[title], $calendarcolumn[timestart], $calendarcolumn[calid] FROM $calendartable WHERE $calendarcolumn[uid]='".lnSessionGetVar('uid')."' and $calendarcolumn[date]='$year-$month-$day' and $calendarcolumn[timetype]='0' ";
		$result = $dbconn->Execute($query);
		for($job00="",$job30="",$j=0;list($title,$start,$eventid) = $result->fields;$j++) {
			$result->MoveNext();
			$title=stripslashes($title);
//			$title=filter($title,1);
			list($hour,$min,$sec)=explode(":",$start);
			if ($hour == $i && $min < 30)
				$job00 .= "&nbsp;<IMG SRC='modules/Calendar/images/bluedot.gif' align=absmiddle> <A HREF=index.php?mod=Calendar&amp;op=showevent&eid=$eventid class=c>$title</A>";
			else if ($hour == $i && $min >= 30)
				$job30 .= "&nbsp;<IMG SRC='modules/Calendar/images/bluedot.gif' align=absmiddle> <A HREF=index.php?mod=Calendar&amp;op=showevent&eid=$eventid class=c>$title</A>";
		}

?>

<TR>
	<TD width=40 rowspan=2 align=right bgcolor=#FFFFFF>
		<FONT SIZE=3><B><?=$is?></B></FONT>
	</TD>

<?
		if ($job00)
			echo "<TD width=20 align=center bgcolor=#FFFFFF>00</TD><TD bgcolor=#FFFFFF>".$job00."</TD></TR>";
		else
			echo "<TD width=20 align=center bgcolor=#FFFFFF>00</TD><TD bgcolor=#EFEFEF>".$job00."</TD></TR>";
		echo "<TR>";
		if ($job30)
			echo "<TD width=20 align=center>30</TD><TD bgcolor=#EFEFEF>".$job30."</TD></TR>";
		else 
			echo "<TD width=20 align=center>30</TD><TD bgcolor=#EFEFEF>".$job30."</TD></TR>";
	}
?>

	</TABLE>

	<!-- SHOW EVENT -->

<?
}







/*- - - แสดงแบบสัปดาห์- - - -*/
function showWeek($day, $month, $year) {

?>
<P>

<!-- Heading -->

<TABLE width="100%" border="0" cellspacing=0 cellpadding=1 bgcolor="#0066CC">
	<TR>
	<TD align="left" valign="center">
		<A class=white href="<?php echo $PHP_SELF."?mod=Calendar&show=week&".Date_Calc::beginOfPrevWeek($day,$month,$year,"year=%Z&month=%m&day=%d"); ?>">&lt;&lt;Back</A>
	</TD>
	<TD ALIGN="CENTER">	
<?php
	echo "<B><FONT COLOR=#FFFFFF>".Date_Calc::dateFormat($day,$month,$year,"%B %Y")."</FONT></B>";
?></TD>
	<TD align="right" valign="center">
		<A class=white href="<?php echo $PHP_SELF."?mod=Calendar&show=week&".Date_Calc::beginOfNextWeek($day,$month,$year,"year=%Z&month=%m&day=%d"); ?>">Next&gt;&gt;</A>
	</TD>
	</TR>
</TABLE>

<!-- Heading -->

<!-- SHOW EVENT -->

<TABLE width=100% cellspacing=1 cellpadding=1 border=0 bgcolor="#FFD7AE">

<!-- TITLE -->
<TR>
	<TD colspan=2 width=60 align=center><B>Week</B></TD>

<?
	// get the first weekday 
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$calendartable = $lntable['calendar'];
	$calendarcolumn = &$lntable['calendar_column'];

	$week_cal = Date_Calc::getCalendarWeek($day,$month,$year,"%E");
	$starttime=8;
	$endtime=17;
	for ($i=0;$i<7;$i++) {
		$week_day = Date_Calc::daysToDate(($week_cal[$i]),"%d");			
		$week_month = Date_Calc::daysToDate(($week_cal[$i]),"%m");			
		$week_year = Date_Calc::daysToDate(($week_cal[$i]),"%Z");			
		echo "<TD bgcolor=#FE8917 align=center width=13%>".@$weeks[$i];
		echo "<BR><A HREF='index.php?mod=Calendar&show=day&day=$week_day&month=$week_month&year=$week_year' class=f>"
					.(int)$week_day
					."</A>";
		echo "</TD>";

		$query="SELECT Min($calendarcolumn[timestart]) FROM $calendartable WHERE $calendarcolumn[uid]='".lnSessionGetVar('uid')."' and $calendarcolumn[date]='$week_year-$week_month-$week_day' ";
		$result = $dbconn->Execute($query);

		@list($start_time,$end_time) = $result->fields;
		@list($shour,$smin,$ssec)=explode(":",$start_time);
		@list($ehour,$emin,$ssec)=explode(":",$end_time);
		if ($shour<$starttime && $shour) {
			$newstarttime=(int)$shour;
			$starttime=$newstarttime<$starttime ? $newstarttime:$starttime;
		}
		if ($ehour>$endtime && $ehour) {
			$newendtime=(int)$ehour;
			$endtime=$newendtime>$endtime ? $newendtime:$endtime;
		}
	}

?>
	
	</TR>

<!-- TITLE -->

<!-- SHOW EVENT -->

<TR bgcolor="#EFEFEF">
	<TD width=60 colspan=2 align=center valign=top>
		<FONT SIZE=1><B>All Day:</B></FONT>
	</TD>

<?
		//<- - - All Day
		for ($j=0; $j<7; $j++) {
			$wday = Date_Calc::daysToDate(($week_cal[$j]),"%d");			
			$wmonth = Date_Calc::daysToDate(($week_cal[$j]),"%m");			
			$wyear = Date_Calc::daysToDate(($week_cal[$j]),"%Z");			
			
			$query="SELECT $calendarcolumn[title], $calendarcolumn[calid] FROM $calendartable WHERE $calendarcolumn[uid]='".lnSessionGetVar('uid')."' and $calendarcolumn[date]='$wyear-$wmonth-$wday' and $calendarcolumn[timetype]='1'";
			$result = $dbconn->Execute($query);

			echo "<TD bgcolor=#EFEFEF>";
			for($k=0;list($title,$eventid) = $result->fields;$k++) {
					$result->MoveNext();
					$title=stripslashes($title);
//					$title=filter($title,1);
					echo "&nbsp;<IMG SRC='modules/Calendar/images/bluedot.gif > <A HREF=index.php?mod=Calendar&amp;op=showevent&eid=$eventid class=c>$title</A><BR>";
			}
			echo "&nbsp;</TD>";
		}

?>

</TR>

<?
	//<- - - All EVENTS
	for ($i=$starttime;$i<=$endtime;$i++) {
		$is=($i>12) ? $i-12:$i;
		$line00="";
		$line30="";
		for ($j=0;$j<7;$j++) {
			$wday = Date_Calc::daysToDate(($week_cal[$j]),"%d");			
			$wmonth = Date_Calc::daysToDate(($week_cal[$j]),"%m");			
			$wyear = Date_Calc::daysToDate(($week_cal[$j]),"%Z");
			
			$query="SELECT $calendarcolumn[title], $calendarcolumn[calid] FROM $calendartable WHERE $calendarcolumn[uid]='".lnSessionGetVar('uid')."' and $calendarcolumn[date]='$wyear-$wmonth-$wday' and $calendarcolumn[timetype]='0'";
			$result = $dbconn->Execute($query);

			for($job00="",$job30="",$k=0;list($title,$start,$eventid) = $result->fields;$k++) {
				$result->MoveNext();
				$title=stripslashes($title);
//				$title=filter($title,1);
				list($hour,$min,$sec)=explode(":",$start);
				if ($hour == $i && $min < 30)
					$job00 .= "&nbsp;<IMG SRC='modules/Calendar/images/bluedot.gif> <A HREF=index.php?mod=Calendar&amp;op=showevent&eid=$eventid class=c>$title</A><BR>";
				else if ($hour == $i && $min >= 30)
					$job30 .= "&nbsp;<IMG SRC='modules/Calendar/images/bluedot.gif> <A HREF=index.php?mod=Calendar&amp;op=showevent&eid=$eventid class=c>$title</A><BR>";
			}
			if ($job00) 
				$line00 .= "<TD bgcolor=#FFFFFF>".$job00."&nbsp;</TD>";
			else 
				$line00 .= "<TD bgcolor=#EFEFEF>".$job00."&nbsp;</TD>";
			
			if ($job30)
				$line30 .= "<TD bgcolor=#FFFFFF>".$job30."&nbsp;</TD>";
			else
				$line30 .= "<TD bgcolor=#EFEFEF>".$job30."&nbsp;</TD>";
		}
	
		echo "<TR bgcolor=#FFFFFF>";
		echo "<TD width=40 rowspan=2 align=right><FONT SIZE=3><B>$is</B></FONT></TD>";
		echo "<TD width=20 align=center>00</TD>"; //<- - - Show event 00
		echo $line00;
		echo "</TR>";
		echo "<TR>";
		echo "<TD width=20 align=center>30</TD>"; //<- - - Show event 30
		echo $line30;
		echo "</TR>";
	}

?>

	</TABLE>

<!-- SHOW EVENT -->

<?

}


/*- - - แสดงแบบเดือน - - -*/
function showMonth($day, $month, $year) {

	// get month structure for generating calendar
	$month_cal = Date_Calc::getCalendarMonth($month,$year,"%E");

?>
<P>

<!-- Heading -->

<TABLE width="100%" border="0" cellspacing=0 cellpadding=1 bgcolor="#0066CC">
	<TR>
	<TD align="left" valign="center">
		<A class=white href="<?php echo $PHP_SELF."?mod=Calendar&show=month&".Date_Calc::beginOfPrevMonth($day,$month,$year,"year=%Z&month=%m&day=%d"); ?>">&lt;&lt;Back</A>
	</TD>
	<TD ALIGN="CENTER">	

<?php
	echo "<B><FONT COLOR=#FFFFFF>".Date_Calc::dateFormat($day,$month,$year,"%B %Y")."</FONT></B>";
?>
	
	</TD>
	<TD align="right" valign="center">
		<A class=white href="<?php echo $PHP_SELF."?mod=Calendar&show=month&".Date_Calc::beginOfNextMonth($day,$month,$year,"year=%Z&month=%m&day=%d"); ?>">Next&gt;&gt;</A>
	</TD>
	</TR>
</TABLE>

<!-- Heading -->

<TABLE width="100%" border="0" cellspacing=1 cellpadding=0 bgcolor="#FFD7AE">

<?
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$calendartable = $lntable['calendar'];
	$calendarcolumn = &$lntable['calendar_column'];

	$curr_day = Date_Calc::dateNow("%Z%m%d");

	echo '<TR  height=25 valign="middle" align="center">';
	for($col=1; $col <= 7; $col++) {
		echo "<TD bgcolor=#FE8917 align=center width=14%>". Date_Calc::getWeekdayFullname($col,2,2004)."</TD>";
	}
	echo '</TR>';

	// loop through each week of the calendar month
	for($row = 0; $row < count($month_cal); $row++) 	{


		echo "<TR height=50>";

		// loop through each day of the current week
		for($col=0; $col < 7; $col++) {


				// background color of day
				$backGroundColor="#EFEFEF";

				// set the font color of the day, highlight if it is today
				if(Date_Calc::daysToDate($month_cal[$row][$col],"%m") == $month) {
					if(Date_Calc::daysToDate($month_cal[$row][$col],"%w") == 0) {
						$fontColor="#FF0000";		// วันอาทิตย์สิแดง
					}
					else {
						$fontColor="#000000";		// สีวันปกติ
					}
				}
				else {
					$fontColor="#777777";			// สีของเดือนอื่น
				}

				if(Date_Calc::daysToDate($month_cal[$row][$col],"%Z%m%d") == $curr_day) {
					$fontColor="#FFFFFF";			// สีวันนี้
					$backGroundColor="#790000";
				}

				$thisevent=@$event[Date_Calc::daysToDate($month_cal[$row][$col],"%Z-%m-%d")]; 
				if($thisevent) {
					$fontColor="#FF0000";								// สีวันหยุด
				}

				$mday = Date_Calc::daysToDate(($month_cal[$row][$col]),"%d");			
				$mmonth = Date_Calc::daysToDate(($month_cal[$row][$col]),"%m");			
				$myear = Date_Calc::daysToDate(($month_cal[$row][$col]),"%Z");			

				$query="SELECT $calendarcolumn[title], $calendarcolumn[calid] FROM $calendartable WHERE $calendarcolumn[uid]='".lnSessionGetVar('uid')."' and $calendarcolumn[date]='$myear-$mmonth-$mday' ";
				$result = $dbconn->Execute($query);

				$rows = $result->RecordCount();
				
				// print cell
				if ($rows) {
					$backGroundColor="#FFFFFF";
					$fontColor="#000000";
					echo "<TD height=50 bgcolor=".$backGroundColor." align=left>";
				}
				else {
					echo "<TD height=50 bgcolor=".$backGroundColor." align=center>";
				}


				echo "<A title='$thisevent'"
					." class=a href=\"index.php?mod=Calendar&show=day&"
					.Date_Calc::daysToDate($month_cal[$row][$col],"year=%Z&month=%m&day=%d")
					."\" onmouseover='self.status=this.title;return true;' onmouseout='self.status=this.title;return true;'>"
					."<FONT color=$fontColor size=1><u>". (int)Date_Calc::daysToDate($month_cal[$row][$col],"%d");
				echo "</u></font></a><BR>";
				
				for($k=0;list($title,$eventid) =$result->fields;$k++) {
						$result->MoveNext();
						echo "&nbsp;<IMG SRC=\"modules/Calendar/images/bluedot.gif\" > <A HREF=index.php?mod=Calendar&amp;op=showevent&eid=$eventid class=c>$title</A><BR>";
				}
				
				echo "</TD>"; //<- - - show event

		}
		
		echo "</TR>";
	}

?>

	</TABLE>

<?

}


/*- - - แสดงแบบปี - - -*/
function showYear($day, $month, $year) {
	global $config;
	global $months,$event;

	if(empty($year))
		$year = Date_Calc::dateNow("%Z");
	
	$curr_day = Date_Calc::dateNow("%Z%m%d");

	// get year structure for generating calendar
	$year_cal = Date_Calc::getCalendarYear($year,"%E");
	$view = "year";

?>
<P>

<TABLE border=0 cellspacing=0 width=100% cellpadding=3>

<TR  bgcolor=#0066CC>
<TD align=left>
<A class=white href="<?php echo $PHP_SELF."?mod=Calendar&show=year&year=".($year-1)."&month=".$month."&day=01"; ?>">&lt;&lt; Back</A>
</TD>
<TD align=center>
	<?php
		echo "<B><FONT COLOR=#FFFFFF>".Date_Calc::dateFormat($day,$month,$year,"%Y")."</FONT></B>";
	?>
</TD>
<TD align=right>
<A class=white href="<?php echo $PHP_SELF."?mod=Calendar&show=year&year=".($year+1)."&month=".$month."&day=01"; ?>">Next &gt;&gt;</A>
</TD>
</TR>

<TR align=center valign=top>
<TD>

<?
	// loop through each month
	for($curr_month=0; $curr_month <=11; $curr_month++) {
		?>
		<TABLE cellspacing=0 bgcolor=#FFFFFF width=100% cellpadding=0>
		<TR bgcolor=#e0e0e0>
		<TD align=center colspan=7>
		<A class=b HREF="index.php?mod=Calendar&show=month&<?php echo Date_Calc::daysToDate($year_cal[$curr_month][0][6],"year=%Z&month=%m&day=%d"); ?>">
		<?php
				echo "<b>".Date_Calc::dateFormat(1,$curr_month+1,$year,"%B")."</b>";
		?>
		</A>
		</TD>
		</TR>

		<TR bgcolor="#FE8917"  valign="middle" align="right">
		<TD><font size=1>S</TD><TD><font size=1>M</TD><TD><font size=1>T</TD><TD><font size=1>W</TD><TD><font size=1>T</TD><TD><font size=1>F</TD><TD><font size=1>S</TD></TR>
		
		<?php

		// loop through each week of the calendar month
		// loop through each week of current month
		for($row = 0; $row < count($year_cal[$curr_month]); $row++) {
			echo "<TR  height=15>";
					
			// loop through each week day of current week
			for($col=0; $col < 7; $col++) {

				$backGroundColor="#EEEEEE";

				// set the font color of the day, highlight if it is today
					if(Date_Calc::daysToDate($year_cal[$curr_month][$row][$col],"%w") == 0) {
						$fontColor="#FF0000";		// วันอาทิตย์สิแดง
					}
					else {
						$fontColor="#000000";		// สีวันปกติ
					}

				if(Date_Calc::daysToDate($year_cal[$curr_month][$row][$col],"%Z%m%d") == $curr_day) {
					$fontColor="#FFFFFF";			// สีวันนี้
					$backGroundColor="#790000";
				}

				$thisevent=$event[Date_Calc::daysToDate($year_cal[$curr_month][$row][$col],"%Z-%m-%d")];

				if($thisevent)
					$fontColor="#FF0000";

				// print the day with correct spacing
				if(Date_Calc::daysToDate($year_cal[$curr_month][$row][$col],"%m") == $curr_month + 1){
					$day = (int)Date_Calc::daysToDate($year_cal[$curr_month][$row][$col],"%d");
					echo "<TD align=right bgcolor=".$backGroundColor.">"
					."<A class=a HREF=\"index.php?mod=Calendar&show=day&"
					.Date_Calc::daysToDate(($year_cal[$curr_month][$row][$col]),"year=%Z&month=%m&day=%d")
					."\"><FONT color=$fontColor size=1>"
					.$day
					."</font></A></TD>";
				}
				else
					echo "<TD bgcolor=#FBFBFB>&nbsp;</TD>";
			}

			echo "</TR>\n";
	}

		?>
		
		</TABLE>

		<?php

		// make a new row every third month.
		// to make the year calendar 4x3, instead of 3x4,
		// change % 3 to % 4 and fix the column span on
		// the calendar headers.
		if($curr_month < 11)
		{
 			if(!(($curr_month + 1) % 3) && $curr_month)
			{
				echo "</TD></TR>\n<TR align=center valign=top><TD>\n";
			}
			else
				echo "</TD>\n<TD>\n";
		}
	
	} // end for loop
	?>
	</TD>
	</TR>
</TABLE>

<?

}


/*- - - ฟอร์มเพิ่มข้อมูลตารางนัดหมาย - - -*/
function addEventForm($vars) {
	// Get arguments from argument array
    extract($vars);
//	global $config,$lang,$months;
//	global $day,$month,$year;
//	global $eid;
//	global $jobtitle,$eventtype;
	$config['eventtype'] =  array("งานที่ต้องทำ","เรียน","ประชุม","อบรม","บรรยาย","วันเกิด","วันหยุด","งานแต่งงาน","อาหารเช้า","อาหารกลางวัน","อาหารเย็น","สังสรรค์","อื่นๆ");
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$calendartable = $lntable['calendar'];
	$calendarcolumn = &$lntable['calendar_column'];

	@$jobtitle=str_replace("\"","&quot;",$jobtitle);
	$jobtitle=stripslashes($jobtitle);

	echo "<BR><BR><B>::Add Event</B>";
	if (@$eid) {
		$query = "SELECT $calendarcolumn[type],$calendarcolumn[title], $calendarcolumn[note], $calendarcolumn[date],$calendarcolumn[timestart], $calendarcolumn[timeend], $calendarcolumn[timetype] FROM $calendartable WHERE $calendarcolumn[calid]='$eid' ";
		$result = $dbconn->Execute($query);

		list($eventtype,$jobtitle,$note,$date,$start,$end, $timetype) = $result->fields;
		$jobtitle=str_replace("\"","&quot;",$jobtitle);
		$jobtitle=stripslashes($jobtitle);
		$note=stripslashes($note);
		list($year,$month,$day)=explode("-",$date);
		list($sh,$sm,$ss)=explode(":",$start);
		list($eh,$em,$es)=explode(":",$end);
	}
?>
<script language="JavaScript">

function clearFields() {
	document.forms.eventform.jobtitle.value ="";
	document.forms.eventform.jobmsg.value ="";
}

function checkFields() {
var title    = document.forms.eventform.jobtitle;
var notes = document.forms.eventform.jobmsg;

    if (title.value == ""){
	    alert("ป้อนหัวข้องานด้วยค่ะ.");
        title.focus();
        return false;
    }

	if (title.value.length > 30 ){
		alert("ห้ามเกิน 30 ตัวอักษร เดี๋ยวไม่สวย");
		tile.focus();
		return false;
    }   
    
     if ((document.forms.eventform.start_hour.value == document.forms.eventform.end_hour.value) && 
        (document.forms.eventform.start_minute.value == document.forms.eventform.end_minute.value) && 
        (document.forms.eventform.time[1].checked)) {
        alert("ช่วงเวลานะคะ เอาให้ต่างกันสักหน่อย");
        return false;
     }

    return true;
}
</script>


<TABLE width="100%" border="0" cellspacing=0 cellpadding=1 bgcolor="#F0F0F0">
<FORM NAME="eventform" METHOD="POST" ACTION="index.php" onSubmit="return checkFields()">
<INPUT TYPE="HIDDEN" NAME="mod" VALUE="Calendar">
<INPUT TYPE="HIDDEN" NAME="op" VALUE="addevent">
<INPUT TYPE="HIDDEN" NAME="eid" VALUE="<?=$eid?>">
<TR VALIGN=TOP>
	<TD WIDTH=80 ALIGN=RIGHT>&nbsp;<BR><B>ประเภทงาน:</B></TD>
	<TD>&nbsp;<BR>
<?
	echo "<SELECT NAME='eventtype' class=select1>";
	for($i=0;$i<sizeof($config['eventtype']);$i++) {
		if ($config['eventtype'][$i] == $eventtype) 
			$selectshow="selected";
		else 
			$selectshow="";

		echo "<OPTION $selectshow>".$config['eventtype'][$i]."</OPTION>";
	}
	echo "</SELECT>";
?>		
</TD></TR>
<TR VALIGN=TOP><TD ALIGN=RIGHT><B>หัวข้อ:</B></TD><TD><INPUT TYPE="text" NAME="jobtitle" VALUE="<?=$jobtitle?>" style="width: 90%;"></TD></TR>	
<TR VALIGN=TOP><TD ALIGN=RIGHT><B>รายละเอียด:</B></TD><TD><TEXTAREA NAME="jobmsg" ROWS="6" COLS="30" wrap="soft" style="width: 90%;"><?=@$note?></TEXTAREA></TD></TR>	
<TR VALIGN=TOP><TD ALIGN=RIGHT><B>ณ วันที่:</B></TD><TD>
<?
	echo "<SELECT name='sday'>";
	for($i=1;$i<=31;$i++) 
		if ($i==(int)$day)
			echo "<OPTION selected value=".$i.">".$i."</OPTION>";
		else if ($i==Date_Calc::dateNow("%d"))
			echo "<OPTION selected value=".$i.">".$i."</OPTION>";
		else 
			echo "<OPTION value=".$i.">".$i."</OPTION>";
			
	echo "</SELECT>";
	echo "<SELECT name='smonth'>&nbsp;";
	for($i=1;$i<=12;$i++) {
		if ($i==$month)
			echo "<OPTION selected value=".$i.">".Date_Calc::dateFormat(1,$i,2000,"%B")."</OPTION>";
		else if ($i==Date_Calc::dateNow("%m"))
			echo "<OPTION selected value=".$i.">".Date_Calc::dateFormat(1,$i,2000,"%B")."</OPTION>";
		else 
			echo "<OPTION value=".$i.">".Date_Calc::dateFormat(1,$i,2000,"%B")."</OPTION>";
	}
	echo "</SELECT>&nbsp;";

	echo "<SELECT name='syear'>";
	for($i=date('Y');$i<=date('Y')+10;$i++) {
		$is=$i+543;
		if ($i==$year)
			echo "<OPTION selected value=".$i.">".$is."</OPTION>";
		else
			echo "<OPTION value=".$i.">".$is."</OPTION>";
	}
	echo "</SELECT>";

if (@$timetype) {
	$allday="checked";
	$dday="";
}
else {
	$allday="";
	$dday="checked";
}

?>
</TD></TR>	
<TR VALIGN=TOP><TD ALIGN=RIGHT><B>ช่วงเวลา:</B></TD><TD>
<INPUT TYPE="radio" NAME="stime" <?=$allday?> value=1>ตลอดทั้งวัน</TD></TR>
<TR VALIGN=TOP><TD ALIGN=RIGHT>&nbsp;</TD><TD VALIGN=TOP>
<INPUT TYPE="radio" NAME="stime" <?=$dday?> value=0>ตั้งแต่:
<?
	echo "<SELECT SIZE=4 name=start_hour>";
	for($i=1;$i<=24;$i++) {
		if (($i==9 && !$sh) || $i == (int)$sh)
			echo "<OPTION selected value=".$i.">".$i."</OPTION>";
		else
			echo "<OPTION value=".$i.">".$i."</OPTION>";
	}
	echo "</SELECT>";
	echo "<SELECT SIZE=4 name=start_minute>";
	echo "<OPTION selected value=0>:00</OPTION>";
	echo "<OPTION "; if (@$sm == "00") echo "selected "; echo " value=0>:00</OPTION>";
	echo "<OPTION "; if (@$sm == "15") echo "selected "; echo " value=15>:15</OPTION>";
	echo "<OPTION "; if (@$sm == "30") echo "selected "; echo " value=30>:30</OPTION>";
	echo "<OPTION "; if (@$sm == "45") echo "selected "; echo " value=45>:45</OPTION>";
	echo "</SELECT>";
	echo "&nbsp;&nbsp;ถึง&nbsp;&nbsp;";
	echo "<SELECT SIZE=4 name=end_hour>";
	for($i=1;$i<=24;$i++) {
		if (($i==9 && !$eh) || $i == (int)$eh)
			echo "<OPTION selected value=".$i.">".$i."</OPTION>";
		else
			echo "<OPTION value=".$i.">".$i."</OPTION>";
	}
	echo "</SELECT>";
	echo "<SELECT SIZE=4 name=end_minute>";
	echo "<OPTION selected value=0>:00</OPTION>";
	echo "<OPTION "; if (@$em == "00") echo "selected "; echo " value=0>:00</OPTION>";
	echo "<OPTION "; if (@$em == "15") echo "selected "; echo " value=15>:15</OPTION>";
	echo "<OPTION "; if (@$em == "30") echo "selected "; echo " value=30>:30</OPTION>";
	echo "<OPTION "; if (@$em == "45") echo "selected "; echo " value=45>:45</OPTION>";
	echo "</SELECT>";
?>
</TD></TR>	
<TR VALIGN=TOP><TD>&nbsp;</TD>
	<TD>&nbsp;<BR><input type=image SRC="images/button/tha/save.gif">&nbsp;<a href="javascript:clearFields()"><IMG SRC="images/button/tha/clear.gif" border=0></a></TD></TR>
</FORM>
</TABLE>

<?
}



/*- - - เก็บลง database - - -*/
function addEventDone($vars) {
	// Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$calendartable = $lntable['calendar'];
	$calendarcolumn = &$lntable['calendar_column'];
//	global $config,$usersess;
//	global $eid;
//	global $eventtype,$sday,$smonth,$syear,$start_hour,$start_minute,$end_hour,$end_minute,$jobtitle,$jobmsg,$stime;

	$event_date=$syear."-".$smonth."-".$sday;
	$start_time=$start_hour.":".$start_minute.":00";
	$end_time=$end_hour.":".$end_minute.":00";

	$jobtitle=addslashes($jobtitle);
	$jobmsg=addslashes($jobmsg);

	if ($eid) {
		$sql ="UPDATE $calendartable SET $calendarcolumn[type]= '$eventtype', $calendarcolumn[title]='$jobtitle', $calendarcolumn[uid]='".lnSessionGetVar('uid')."', $calendarcolumn[note]='$jobmsg', ";
		$sql .=" $calendarcolumn[date]='$event_date', $calendarcolumn[timestart]='$start_time', $calendarcolumn[timeend]='$end_time', $calendarcolumn[timetype]='$stime' ";
		$sql .= " WHERE $calendarcolumn[calid]='$eid' ";
	
//		update_event(lnSessionGetVar('uid'),"Update event $jobtitle at $event_date [$start_time-$end_time]");
	}
	else {
		$sql  = "INSERT INTO $calendartable ($calendarcolumn[type], $calendarcolumn[title], $calendarcolumn[uid], $calendarcolumn[note], $calendarcolumn[date], $calendarcolumn[timestart], $calendarcolumn[timeend], $calendarcolumn[timetype]) ";
		$sql .= " VALUES ('$eventtype', '$jobtitle', '".lnSessionGetVar('uid')."', '$jobmsg', '$event_date', '$start_time', '$end_time', '$stime')";	
	

//		update_event(lnSessionGetVar('uid'),"Add event $jobtitle at $event_date [$start_time-$end_time]");
	}
	$dbconn->Execute($sql);
	calendar($vars);
}


/*- - - แสดงตารางนัดหมาย- - -*/
function showEvent($vars) {
//	global $config,$lang,$months;
//	global $day,$month,$year;
//	global $eid;
//	global $jobtitle,$eventtype;
	// Get arguments from argument array
    extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$calendartable = $lntable['calendar'];
	$calendarcolumn = &$lntable['calendar_column'];

//	echo "<BR>$lang[editevent]<HR SIZE=1>";
	if ($eid) {
		$query = "SELECT $calendarcolumn[type], $calendarcolumn[title], $calendarcolumn[note], $calendarcolumn[date], $calendarcolumn[timestart], $calendarcolumn[timeend], $calendarcolumn[timetype] FROM $calendartable WHERE $calendarcolumn[calid]='$eid' ";
		$result = $dbconn->Execute($query);

		list($eventtype,$jobtitle,$note,$date,$start,$end, $timetype) = $result->fields;
		list($y,$m,$d)=explode("-",$date);
		$m = (int)$m;
		$d = (int)$d;
		list($sh,$sm,$ss)=explode(":",$start);
		list($eh,$em,$es)=explode(":",$end);
		$start = $sh.":".$sm;
		$end = $eh.":".$em;
	}
	$jobtitle=stripslashes($jobtitle);
//	$jobtitle=filter($jobtitle,1);
	$note=stripslashes($note);
?>

<script language="javaScript">
	function formSubmit(val) {
		document.forms.Eventform.op.value = val;
		if (val == "deleteevent") {
			if(confirm('Are you sure?')) 
				document.forms.Eventform.submit();
		}
		else {
			document.forms.Eventform.submit();
		}
	}
</script>

<TABLE width="100%" border="0" cellspacing=0 cellpadding=2 bgcolor="#FFFFCC">
<FORM NAME="Eventform" METHOD="POST" ACTION="index.php">
<INPUT TYPE="HIDDEN" NAME="mod" VALUE="Calendar">
<INPUT TYPE="HIDDEN" NAME="op">
<INPUT TYPE="HIDDEN" NAME="eid" value="<?=$eid ?>">
<TR VALIGN=TOP><TD ALIGN=LEFT bgcolor="#CCCCFF" class=nav colspan=2> <B><?=$eventtype?> : <?=$jobtitle?></B></TD></TR>	
<TR VALIGN=TOP><TD ALIGN=RIGHT width=10%>Datetime:</TD><TD><B><? echo Date_Calc::dateFormat($d,$m,$y,"%e %B %Y"); ?>
<?
	if (!$timetype)
		echo "  ($start - $end)";
?>
</B></TD></TR>	
<TR VALIGN=TOP height=100><TD ALIGN=RIGHT width=10%>รายละเอียด:</TD><TD><?=$note?></TD></TR>	
<TR VALIGN=middle height=50>
	<TD  colspan=2 bgcolor="#FFFFFF" align=left><a href="javascript:formSubmit('editevent')"><img SRC="images/button/tha/edit.gif" border=0></a>&nbsp;<a href="javascript:formSubmit('deleteevent')"><IMG SRC="images/button/tha/delete.gif" border=0></a></TD></TR>
</FORM>
</TABLE>

<?
}


/*- - - ลบนัดหมาย - - -*/
function deleteEvent($vars) {
//	global $config,$usersess;
//	global $eid;
    extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$calendartable = $lntable['calendar'];
	$calendarcolumn = &$lntable['calendar_column'];

	$query = "DELETE FROM $calendartable WHERE $calendarcolumn[calid]='$eid' and $calendarcolumn[uid]='".lnSessionGetVar('uid')."'";

	$result = $dbconn->Execute($query);

//	update_event(lnSessionGetVar('uid'),"Delete event $eid");
	calendar($vars);
}


/*- - - ลบนัดหมาย - - -*/
function deleteTodo() {
	global $config,$usersess;
	global $eidlist;

	for($i=0;$i<10;$i++) {
		if ($eidlist[$i]) {
			$sql = "DELETE FROM $config[tableevent] WHERE EventID='$eidlist[$i]' and Nickname='".lnSessionGetVar('uid')."'";

			db_query($sql);
			update_event(lnSessionGetVar('uid'),"Delete event $id");
		}
	}
	calendar();
}


//==============================================================================================
function hasEvent($thatdate) {
	global $config,$usersess;

	$condition="Nickname='".lnSessionGetVar('uid')."' and Date='$thatdate' ";
	$column="Date";
	if (db_getvar($config['tableevent'],$condition,$column))
		return 1;
	else 
		return 0;
}



?>