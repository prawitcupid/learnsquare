<?php

function blocks_schedule_block($row) {
	   if (empty($row['title'])) {
			$row['title'] = 'Enroll Course';
		}

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
	
		$course_submissionstable = $lntable['course_submissions'];
		$course_submissionscolumn = &$lntable['course_submissions_column'];
		
		$query = "SELECT $course_submissionscolumn[cid], $course_submissionscolumn[start] FROM $course_submissionstable WHERE $course_submissionscolumn[active]=1 AND $course_submissionscolumn[enroll] = '"._LNSTUDENT_ENROLL."' ORDER BY  $course_submissionscolumn[start]";
		
		$result = $dbconn->Execute($query);

		if ($result ->RecordCount()) {
				$row['content'] .= '<IMG SRC="images/global/bl_red.gif" ALIGN="absmiddle"> <B>'.$row['title'].'</B><BR>&nbsp;';
				$row['content'] .= '<table width="100%" cellpadding="2" cellspacing="0">';
				$row['content'] .= '<tr height=20><td  bgcolor="#bbbbbb" align="center"><B>'._BROWSECOURSE.'</B></td><td  bgcolor="#cccccc"  align="center"><B>'._ENROLLSCHEDULE.'</B></td>';

				while(list($cid,$start) = $result->fields) {
					$result->MoveNext();
					$now=date('Y-m-d');
					if ( Date_Calc::isValidDate2($start) &&  Date_Calc::dateDiff3($start,$now) > 0 && Date_Calc::dateDiff3($start,$now) <= _LNSCHEDULE_LIMIT) {
						$courseinfo = lnCourseGetVars($cid);
						$course_length = lnCourseLength($cid) - 1;
						$from = Date_Calc::dateFormat2($start, "%e %b");
						$to = Date_Calc::daysAddtoDate2($start, $course_length, "%e %b %y");
						$start = $from . ' - ' . $to.'<BR>';
						$row['content'] .= '<tr height=20 ><td bgcolor="#EEF3A7"><A HREF="index.php?mod=Courses&op=course_enroll&cid='.$cid.'">'.stripslashes($courseinfo[title]).'</A>';
						$date1=date('Y-m-d');
						$date2=date('Y-m-d',$courseinfo['createon']);
						if (Date_Calc::dateDiff2($date1,$date2) < 30) {;
							$row['content'] .=  '<IMG SRC="images/new.gif" WIDTH="28" HEIGHT="11" BORDER=0 ALT="">';
						}
						$row['content'] .= "</td>";
						$row['content'] .= '<td bgcolor="#CFED8A" align="center">'.$start.'</td></tr>';
						$row['content'] .= '<tr height=1 bgcolor="#FFFFFF"><td colspan="2"></td></tr>';
					}
				}
			$row['content'] .= '</table>';
			$row['mod'] = 'Submissions';
		
			return themesidebox($row);
		}
	}
?>