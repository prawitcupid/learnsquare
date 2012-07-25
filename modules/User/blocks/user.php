<?php
/**
* Blocks user organizer
*/
function blocks_user_block($row) {
	global $ret;

	if (!lnSecAuthAction(0, 'Menublock::', "::", ACCESS_READ)) {
		echo "<CENTER><b>"._NOAUTHORIZED." ".$mod." module!</b><P></CENTER>";
		return false;
	}

    if (empty($row['title'])) {
        $row['title'] = 'Login';
    }

	// user logedin menu
	if (lnUserLoggedIn()) {
				$userinfo = lnUserGetVars(lnSessionGetVar('uid'));
				$content = '';
				$content .= '<table cellpadding=0 cellspacing=0 border=0 width=100%>';
				$content .= '<tr><td>';
				
				if (empty($userinfo['_AVATAR']) || $userinfo['_AVATAR'] == "blank.gif") {
					$content .= '<IMG SRC="images/global/user_student.gif" WIDTH="14" HEIGHT="18" BORDER=0 ALT="" align=absmiddle><B>' . $userinfo['uname']."</B>"; //////////////////////////////////////à¸•à¸£à¸‡à¸—à¸µà¹ˆà¹�à¸ªà¸”à¸‡à¸§à¹ˆà¸²à¹ƒà¸„à¸£à¹€à¸›à¹‡à¸™à¸„à¸™à¸—à¸µà¹ˆlogin à¹€à¸‚à¹‰à¸²à¸¡à¸² /////////////////////////////////////////////////////
				}
				else { /// $userinfo['_AVATAR'] save path Image user
					$content .= '<center><IMG SRC="'.$userinfo['_AVATAR'].'"  BORDER=0 ALT="" align=absmiddle>&nbsp;<BR><B>' . $userinfo['uname']."</B></center>"; 
				}
				
				$content .= '<BR><CENTER><FONT SIZE="1" COLOR="#999999">'.lnGetUserNumber('lastvisit').'</FONT></CENTER>';
				$content .= newMessage();

				$content .= '</td></tr>';
				$content .= '</table>';

				$content .= '<BR><table cellpadding=0 cellspacing=0 border=0 width=100%>';
				
				// teacher menu
				if (lnUserTA(lnSessionGetVar('uid')) || lnUserInstructor(lnSessionGetVar('uid'))) {
					if (lnSecAuthAction(0, "Courses::instructor", "::", ACCESS_READ)) {
						if (lnUserTA(lnSessionGetVar('uid'))) {
							$content .= '<tr height=22><td align=center>'.lnBlockImage('user','report').'</td><td><B>'._COACHCOURSE.'</B></td></tr>';
						}
						else {
							$content .= '<tr height=22><td align=center>'.lnBlockImage('user','report').'</td><td><A class=b HREF="index.php?mod=Submissions&amp;file=admin"><B>'._COACHCOURSE.'</B></A></td></tr>';
						}
						$content .= listSubmissions();
						$content .= '<tr><td colspan=2 background="<IMG SRC="themes/Simple/images/line.gif" WIDTH="39" HEIGHT="1" BORDER="0" ALT=""></td></tr>';
					}
					if (lnUserInstructor(lnSessionGetVar('uid'))) {
						if (lnSecAuthAction(0, "Courses::Admin", "::", ACCESS_READ)) {
							$content .= '<tr height=22><td align=center>'.lnBlockImage('user','report').'</td><td>&nbsp;<A class=b HREF="index.php?mod=Courses&amp;file=admin"><B>'._MAINTAINCOURSE.'</B></A></td></tr>';
						}
						
						if (lnSecAuthAction(0, "SCORM::", "::", ACCESS_READ)) {
							$content .= '<tr height=22><td align=center>'.lnBlockImage('user','report').'</td><td>&nbsp;<A class=b HREF="index.php?mod=SCORM&amp;file=scormmenu"><B>'._SCORM_IMPORTEXPORT.'</B></A></td></tr>';
						}
						/*
						if (lnSecAuthAction(0, "SCORM::", "::", ACCESS_READ)) {
							$content .= '<tr height=22><td align=center>'.lnBlockImage('user','report').'</td><td>&nbsp;<A class=b HREF="index.php?mod=SCORM&amp;file=import"><B>'._SCORM_IMPORT.'</B></A></td></tr>';
						}
						//import content from repository
						if (lnSecAuthAction(0, "SCORM::", "::", ACCESS_READ)) {
							$content .= '<tr height=22><td align=center>'.lnBlockImage('user','report').'</td><td>&nbsp;<A class=b HREF="index.php?mod=Repository&file=searchScormRepository"><B>'._SCORM_REPOSITORY_IMPORT.'</B></A></td></tr>';
						}
						if (lnSecAuthAction(0, "SCORM::", "::", ACCESS_READ)) {
							$content .= '<tr height=22><td align=center>'.lnBlockImage('user','report').'</td><td>&nbsp;<A class=b HREF="index.php?mod=SCORM&amp;file=export"><B>'._SCORM_EXPORT.'</B></A></td></tr>';
						}
						*/
					}
				}
				
				// student menu
				 if (lnUserStudent(lnSessionGetVar('uid'))) {
					if (lnSecAuthAction(0, "Courses::Student", "::", ACCESS_READ)) {
						$content .= '<tr height=22><td align=center>'.lnBlockImage('user','report').'</td><td>&nbsp;<A class=b HREF="index.php?mod=Courses&amp;file=report"><B>'._REPORT.'</B></A></td></tr>';     /////////////////////////////////à¸ªà¸¡à¸¸à¸”à¸£à¸²à¸¢à¸‡à¸²à¸™
				  
						$content .= listEnroll(); 

						//menu bookmarks
						$content .= '<tr height=22><td align=center>'.lnBlockImage('user','bookmarks').'</td><td>&nbsp;<A class=b HREF="index.php?mod=User&amp;file=bookmarks&amp;op=bookmarks_show"><B>'._BOOKMARKSMENU.'</B></A></td></tr>';/////////////////////////////à¸§à¸´à¸Šà¸²à¸—à¸µà¹ˆà¹€à¸£à¸µà¸¢à¸™
					}
				}
				
				// all user menus
				if (lnSecAuthAction(0, "Private_Messages::", "::", ACCESS_READ)) {
					$content .= '<tr height=22><td align=center>'.lnBlockImage('user','mail').'</td><td>&nbsp;<A class=b HREF="index.php?mod=Private_Messages&amp;op=post"><B>'._SENDMESSAGE.'</B></A></td></tr>';
				}
				if (lnSecAuthAction(0, "Calendar::", "::", ACCESS_READ) && lnModAvailable("Calendar")) {
					$content .= '<tr height=22><td align=center>'.lnBlockImage('user','calendar').'</td><td>&nbsp;<A class=b HREF="index.php?mod=Calendar"><B>'._CALENDAR.'</B></A></td></tr>';
				}
				if (lnSecAuthAction(0, "Note::", "::", ACCESS_READ) && lnModAvailable("Note")) {
					$content .= '<tr height=22><td align=center>'.lnBlockImage('user','note').'</td><td>&nbsp;<A class=b HREF="index.php?mod=Note"><B>'._NOTE.'</B></A></td></tr>';
				}
				if (lnSecAuthAction(0, "Admin::", "::", ACCESS_ADMIN)) {
					$content .= '<tr height=22><td align=center>'.lnBlockImage('user','config').'</td><td>&nbsp;<A class=b HREF="index.php?mod=Admin"><B>'._ADMINSETTING.'</B></A></td></tr>';
				}
				if (lnSecAuthAction(0, "User::Profile", "Admin:", ACCESS_READ)) {
					$content .= '<tr height=22><td  align=center>'.lnBlockImage('user','profile').'</td><td>&nbsp;<A class=b HREF="index.php?mod=User&file=profile"><B>'._CHANGEPROFILE.'</B></A></td></tr>';
				}

				$content .= '<tr height=22><td align=center>'.lnBlockImage('user','logout').'</td><td>&nbsp;';
				$content .= '<A class=b HREF="index.php?mod=User&op=logout"><B>'._LOGOUT.'</B></A></td></tr>';
			
			
				$content .='</table>';
			
	}

	// show user login 
	else {
				$content ="
				<script  language=javaScript>
						function checkloginFields() {
							var userLogin       = document.login.nickname.value;
							var userPass        = document.login.password.value;
							if (userLogin == \"\") {
								alert('"._ALERT_LOGIN_NICKNAME."');
								document.login.nickname.focus();
								return false;
								}
							if (userPass == \"\") {
								alert('"._ALERT_LOGIN_PASSWORD."');
								document.login.password.focus();
								return false;
								}
				
							return true;
						}
				</script>
				";

				$content .= '<b><font color=#FF000>'. $ret .'</font></b>';
				$content .= '

				<CENTER>
			
				<FORM name="login" method="post" action="index.php" onSubmit="return checkloginFields(); " >	
             
				<INPUT TYPE="hidden" NAME="mod" VALUE="User">
				<INPUT TYPE="hidden" NAME="op" VALUE="login">
				<TABLE  BORDER=0 CELLPADDING=2 CELLSPACING=0>
			
				<TR>
				<TD valign="top" align="center">
							<B>'._BLOCK_LOGIN_NICKNAME.'</B>
				</TD>
				</TR>
				
				<TR height="30">
				<TD valign="top" align="center">
							<INPUT TYPE="text" NAME="nickname" VALUE="" SIZE="10" class="input"> 
				</TD>
				</TR>
				
				<TR>
				<TD valign="top" align="center">
							<B>'._BLOCK_LOGIN_PASSWORD.'</B>
				</TD>
				</TR>
				
				<TR height="30">
				<TD valign="top" align="center">
							<INPUT  TYPE="password" NAME="password" SIZE="10" class="input">
				</TD>
				</TR>
	
				';
	
				if (lnConfigGetVar('seclevel') != 'High') {
						$content .= '
						<TR height="30">
						<TD valign="top" align="center">
								<INPUT TYPE="checkbox" NAME="rememberme" VALUE="1"> '._BLOCK_LOGIN_REMEMBER.'
						</TD>
						</TR>
						';
				}

				$content .= '
				<TR>
				<TD valign="middle" align="center">
					<input type=submit value=Login name="Submit" class="input">
				</TD>
				</TR>
            <TR>
				<TD valign="middle" align="center">
		
				</TD>
				</TR>
					<TR>
				<TD valign="middle" align="center" >';

				 if (lnUserReqProp('_EMAIL')) {
					$content .= '<BR><A HREF="index.php?action=forgetpass" >'._BLOCK_LOGIN_FORGOT.'</A>';
				 }

				$content .= '<BR><BR>';

				if (lnConfigGetVar('reg_allowreg') && lnSecAuthAction(0, 'User::Register', "::", ACCESS_READ)) {
					if (file_exists(_IMAGES.'/button_register.jpg')) {

						$content .= '<A HREF="index.php?mod=User&file=register"><IMG SRC="'._IMAGES.'/button_register.jpg"  BORDER=0 ALT=""></A>';
					}
					else {
						$content .= '<A HREF="index.php?mod=User&file=register">'._BLOCK_LOGIN_REGISTER.'</A>';
					}
				}

				$content .= '
				</TD>
				</TR>
				</TABLE>

				</FORM></CENTER>
			';
	}

	$row['content'] = $content;
	$row['mod'] = 'User';

	return themesidebox($row);
}
function setFocus()
{
document.getElementById("ip");
}
//<script type="text/javascript">
//bar = document.bar.focus();
//</script> 
// function show user study coureses
function listEnroll() {
		
	/* lnSessionDelVar('eidnow');
	 lnSessionDelVar('cidnow');
	 lnSessionDelVar('lidnow');*/
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];

	$uid = lnSessionGetVar('uid');
	
	$query = "SELECT $coursescolumn[code],$coursescolumn[title],$coursescolumn[cid],$course_enrollscolumn[sid], $course_enrollscolumn[eid],$course_submissionscolumn[start],$course_submissionscolumn[enroll]  FROM $course_enrollstable,$course_submissionstable,$coursestable WHERE $course_enrollscolumn[sid]=$course_submissionscolumn[sid] and $course_submissionscolumn[cid]=$coursescolumn[cid] and  $course_enrollscolumn[uid] ='". lnVarPrepForStore($uid) ."' and $course_enrollscolumn[status]='"._LNSTATUS_STUDY."' ORDER BY $course_submissionscolumn[start]";
	$result = $dbconn->Execute($query);
	for($i=0,$content=''; list($code,$title,$cid,$sid,$eid,$start,$study) = $result->fields; $i++) {
		$result->MoveNext();
	//////////	define('_LNSTUDENT_ENROLL',1);
	
		if ($study == _LNSTUDENT_ENROLL) {
			
			$courseinfo = lnCourseGetVars($cid);
			//////////////////////////////////////////////////////////////////////////////////////à¸”à¸¶à¸‡ uid à¸ˆà¸²à¸�à¸«à¸™à¹‰à¸² student à¸•à¸£à¸‡à¸™à¸µà¹‰à¸™à¸°		//////////////////////////////////////////////////////////////
			$content .= '<tr valign=top><td align=center> - </td><td><A HREF="index.php?mod=Courses&amp;op=course_lesson&amp;cid='.$cid.'&uid='.$uid.'&eid='.$eid.'">';
			/////////////////////////////////////////////////////////////////////////////à¸•à¸£à¸‡à¸™à¸µà¹‰à¹€à¸�à¹‡à¸šà¸„à¹ˆà¸²à¸—à¸µà¹ˆà¹„à¸§à¹‰à¹ƒà¸ªà¹ˆà¹ƒà¸™à¸•à¸²à¸£à¸²à¸‡ scores/////////////////////////////////////////////////////////////////////////////////////

		/*	$lessondir =  courses . '/' . $cid . '/' . "uid.txt";	
			$file =  fopen($lessondir,"w");
			fwrite($file,$uid);
			fwrite($file,",");
			fwrite($file,$eid);
			fclose($file);
			$lessondir =  "modules" . '/' . "Courses" . '/' . "uid.txt";	
			$file =  fopen($lessondir,"w");
			fwrite($file,$uid);
			fwrite($file,",");
			fwrite($file,$eid);
			fwrite($file,",");
			Session_Start();
			fwrite($file,"$uidnow ");
			fclose($file);*/

			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		//				  include "modules/Courses/middles.php";                  //////////////////////////////////////à¸—à¸³à¹ƒà¸«à¹‰à¸•à¸­à¸™à¹€à¸›à¸´à¸”à¸‚à¹‰à¸­à¸ªà¸­à¸š middle active
			//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	 //////////////////////////////////////////////////////////////////// //////////////////////////////////////////////////////////////////// ////////////////////////////////////////////////////////////////////
			$course_length = lnCourseLength($cid) - 1;
			$from = Date_Calc::dateFormat2($start, "%e %b");
			$to = Date_Calc::daysAddtoDate2($start, $course_length, "%e %b %y");
			$start = $from . ' - ' . $to.'<BR>';
			$content .='<FONT COLOR="#0000CC"><B>'.$start.'</B></FONT>&nbsp;';
			$content .= $courseinfo[title].' </A></td></tr>';
			$content .= '<tr height=10><td>&nbsp;</td><td>&nbsp;</td></tr>';
		}
	}		

	return $content;
}

// list courses for ta , 30 days
function listSubmissions() {
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$course_submissionstable = $lntable['course_submissions'];
	$course_submissionscolumn = &$lntable['course_submissions_column'];
	$course_tatable = $lntable['course_ta'];
	$course_tacolumn = &$lntable['course_ta_column'];
	$course_enrollstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];

	$uid = lnSessionGetVar('uid');

	//$query = "SELECT $course_submissionscolumn[sid],$course_submissionscolumn[cid],$course_submissionscolumn[start] FROM $course_submissionstable LEFT JOIN  $course_tatable ON $course_submissionscolumn[sid]=$course_tacolumn[sid] WHERE $course_submissionscolumn[active]='1' AND ($course_submissionscolumn[instructor] ='". lnVarPrepForStore($uid) ."' OR $course_tacolumn[uid]='". lnVarPrepForStore($uid) ."') GROUP BY $course_submissionscolumn[sid] ORDER BY $course_submissionscolumn[start]";

	$query = "SELECT $course_submissionscolumn[sid],$course_submissionscolumn[cid],$course_submissionscolumn[start] FROM $course_submissionstable LEFT JOIN  $course_enrollstable ON $course_submissionscolumn[sid]=$course_enrollscolumn[sid] WHERE $course_submissionscolumn[active]='1' AND ($course_submissionscolumn[instructor] ='". lnVarPrepForStore($uid) ."' OR $course_enrollscolumn[mentor]='". lnVarPrepForStore($uid) ."') GROUP BY $course_submissionscolumn[sid] ORDER BY $course_submissionscolumn[start]";

	$result = $dbconn->Execute($query);
	for($i=0,$content=''; list($sid,$cid,$start) = $result->fields; $i++) {
		$result->MoveNext();
		$now=date('Y-m-d');
		if ( Date_Calc::isValidDate2($start) &&  Date_Calc::dateDiff2($now,$start) <= 30) {
			$courseinfo = lnCourseGetVars($cid);
			$course_length = lnCourseLength($cid) - 1;
			$from = Date_Calc::dateFormat2($start, "%e %b");
			$to = Date_Calc::daysAddtoDate2($start, $course_length, "%e %b %y");
			$start = $from . ' - ' . $to.'<BR>';
			$content .= '<tr valign=top><td align=center> - </td><td><A HREF="index.php?mod=Courses&amp;op=course_lesson&amp;cid='.$cid.'&amp;sid='.$sid.'">'.$start.'&nbsp;' .$courseinfo[title].' </A></td></tr>';
			$content .= '<tr height=10><td>&nbsp;</td><td>&nbsp;</td></tr>';

				
		}
	}		

	return $content;
}

// show new incoming messages
function newMessage() {
  
   
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$privmsgstablefile = 'modules/Private_Messages/lntables.php';
   @include_once $privmsgstablefile;
    $lntable = array_merge($lntable, Private_Messages_lntables());

	$privmsgstable = $lntable['privmsgs'];
	$privmsgscolumn = &$lntable['privmsgs_column'];

	$query = "SELECT COUNT($privmsgscolumn[id])
						FROM $privmsgstable
						WHERE $privmsgscolumn[to_uid]='". lnSessionGetVar('uid') ."' AND ($privmsgscolumn[type]='"._MESSAGESEND."' OR $privmsgscolumn[type]='"._MESSAGEVIEW."')";

	$result = $dbconn->Execute($query);	

	list($count) = $result->fields;
	$ret = '';
	if ($count > 0) {
		$ret = '<BR><BR>&nbsp;<IMG SRC="images/global/mail1.gif"  BORDER="0" ALT="">&nbsp;<A class="blue" HREF="index.php?mod=Private_Messages">';
		$ret .= '<B>'.$count.' New message</B>';
		$ret .= '</A>';
	}

	return $ret;
}
?>