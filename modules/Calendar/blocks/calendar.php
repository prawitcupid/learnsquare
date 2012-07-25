<?php

function blocks_calendar_block($row) {
	global $ret;
	if(!isset($GLOBALS['HTTP_GET_VARS'])) $GLOBALS['HTTP_GET_VARS']= array();
	if(!isset($GLOBALS['HTTP_POST_VARS'])) $GLOBALS['HTTP_POST_VARS']= array();
	$vars= array_merge((array)$GLOBALS['HTTP_GET_VARS'],(array)$GLOBALS['HTTP_POST_VARS']);	

	if (empty($row['title'])) {
        $row['title'] = 'Calendar';
    }
	
	ob_start();
	showCalendar($vars);
	$row['content'] = ob_get_contents();
	ob_clean();

	return themesidebox($row);
}


/*- - - Show right small calendar - - -*/
function showCalendar($vars) {
	// Get arguments from argument array
    extract($vars);

	include_once("modules/Calendar/agenda.inc.php");

	if(empty($year) || empty($month)) {
		// get current year, month and day
		$year =date('Y');
		$month = date('m');
		$day = "01";
	}

	// get month structure for generating calendar
	$month_cal = Date_Calc::getCalendarMonth($month,$year,"%E");


?>
<BR>
<center><OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
 codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0"
 WIDTH="90" HEIGHT="90" id="relog" ALIGN="">
  <PARAM NAME=movie VALUE="modules/Calendar/blocks/relog.swf">
  <PARAM NAME=quality VALUE=high>
  <PARAM NAME=bgcolor VALUE=#FFFFFF>
  <param name="wmode" value="transparent">
  <param name="menu" value="false">
  <EMBED src="modules/Calendar/blocks/relog.swf" quality=high bgcolor=#FFFFFF  WIDTH="90" HEIGHT="90" wmode="transparent" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer" menu="false">
  </EMBED></OBJECT></center>
  <BR>

<TABLE width="100%" border="0" cellspacing=1 cellpadding=0 bgcolor="#EFEFEF">
<TR bgcolor="#790000">
	<TD colspan="7">
<!-- Heading -->
	<TABLE width="100%" border="0" cellspacing=0 cellpadding=1>
	<TR>
			<!-- Previous month -->
			<TD align="center" valign="center">
				<? if (lnSessionGetVar('uid')) {  // ��� login ��������ʴ� calendar �ͧ��͹������Ǵ��� ?>
				<A class=white href="<?php echo $PHP_SELF."?mod=Calendar&show=day&".Date_Calc::beginOfPrevMonth($day,$month,$year,"year=%Z&month=%m&day=%d"); ?>">&lt;&lt;</A>
				<? } else { // �������� login ����ͧ�ʴ� calendar?>
				<A class=white href="<?php echo $PHP_SELF."?".Date_Calc::beginOfPrevMonth($day,$month,$year,"year=%Z&month=%m&day=%d"); ?>">&lt;&lt;</A>
				<? } ?>
			</TD>
			<!-- Show date -->
			<TD ALIGN="CENTER">	
			<?php
				echo "<B><FONT COLOR=#FFFFFF>".Date_Calc::dateFormat($day,$month,$year,"%B %Y")."</FONT></B>"; // �ʴ��ѹ���
			?>
			</TD>
			<!-- Next month -->
			<TD align="center" valign="center">
				<? if (lnSessionGetVar('uid')) {?>
				<A class=white href="<?php echo $PHP_SELF."?mod=Calendar&show=day&".Date_Calc::beginOfNextMonth($day,$month,$year,"year=%Z&month=%m&day=%d"); ?>">&gt;&gt;</A>
				<? } else { ?>
				<A class=white href="<?php echo $PHP_SELF."?".Date_Calc::beginOfNextMonth($day,$month,$year,"year=%Z&month=%m&day=%d"); ?>">&gt;&gt;</A>
				<? } ?>
			</TD>
	</TR>
	</TABLE>
<!-- End Heading -->
	</TD>
</TR>

<!-- Week day heading-->
<TR bgcolor="#FE8917"  valign="middle" align="center">
	<TD width=18 class="date">S</TD><TD width=18 class="date">M</TD><TD width=18 class="date">T</TD><TD width=18 class="date">W</TD><TD width=18 class="date">T</TD><TD width=18 class="date">F</TD><TD width=18 class="date">S</TD>
</TR>
<!-- Week day heading-->

<!-- Display day of month -->
<?
	$curr_day = Date_Calc::dateNow("%Z%m%d");

	// loop through each week of the calendar month
	for($row = 0; $row < count($month_cal); $row++) 	{

		echo "<TR height=20>";

		// loop through each day of the current week
		for($col=0; $col < 7; $col++) {
				// background color of day
				$backGroundColor="#5D80A8";

				// set the font color of the day, highlight if it is today
				if(Date_Calc::daysToDate($month_cal[$row][$col],"%m") == $month) {
					if(Date_Calc::daysToDate($month_cal[$row][$col],"%w") == 0) {
						$fontColor="#FF0000";		// �ѹ�ҷԵ����ᴧ
					}
					else {
						$fontColor="#F0F0F0";		// ���ѹ����
					}
				}
				else {
					$fontColor="#777777";			// �բͧ��͹���
				}

				if(Date_Calc::daysToDate($month_cal[$row][$col],"%Z%m%d") == $curr_day) {
					$fontColor="#FFFFFF";			// ���ѹ���
					$backGroundColor="#790000";
				}

				$thisevent=@$event[Date_Calc::daysToDate($month_cal[$row][$col],"%Z-%m-%d")]; 

				if($thisevent) {
					$fontColor="#FF0000";								// ���ѹ��ش
				}

				// print column bg color
				echo "<TD class=whiteate1 height=20 bgcolor=".$backGroundColor." align=center>";
				
				// print day with link (if login)
				if (lnSessionGetVar('uid')) {
					if (!$thisevent) {
						$thisevent=Date_Calc::daysToDate($month_cal[$row][$col],"%d%B %Y");
					}
					echo "<A title='$thisevent'"
					." class=c href=\"index.php?mod=Calendar&show=day&"
					.Date_Calc::daysToDate($month_cal[$row][$col],"year=%Z&month=%m&day=%d")
					."\">"
					."<FONT class=a color=$fontColor size=1>"
					.(int)Date_Calc::daysToDate($month_cal[$row][$col],"%d")
					."</FONT></A>";
				}
				else {
					if ($thisevent) {
						echo "<A title='$thisevent'"
						." class=c href='#'"
						." onclick='javascript:alert(\"$thisevent\");return false;' onmouseover='self.status=this.title;return true;' onmouseout='self.status=this.title;return true;'>"
						."<FONT class=a  color=$fontColor size=1>"
						.(int)Date_Calc::daysToDate($month_cal[$row][$col],"%d")
						."</FONT></A>";
					}
					else  {
						echo "<FONT class=a color=$fontColor size=1>". (int)Date_Calc::daysToDate($month_cal[$row][$col],"%d");
					}
				}
				
				echo "</TD>";
		}
		
		echo "</TR>";
	}

?>

</TABLE>
<!-- End Display day of month -->

<?
}

?>