<?php
/**
*  Forums module
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Webboard::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

define ('_UPLOADIMAGESDIR','modules/Forums/images/upload');
$IMG_ACCEPT = array("image/gif","image/pjpeg","image/jpg","image/x-png","image/jpeg",); //acceptable types

/* - - - - - - - - - - - */
include 'header.php';

$vars= array_merge($_GET,$_POST);	

echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

tabMenu($vars,3);

echo '</TD></TR><TR><TD>';

echo '<table class="main" width= 100% cellpadding=3 cellspacing=1 border=0>';

echo '<tr><td height=350 bgcolor=#FFFFFF valign=top>';

/* options */
switch ($op) {
	case "mod_reply":
					modReply($vars);
					showQuestion($vars);
					replyForm($vars);
					break;
	case "delete_reply":
					deleteReply($vars);
					showQuestion($vars);
					replyForm($vars);
					break;
	case "mod_question_preview":
					postPreview($vars);
					showQuestion($vars);
					break;
	case "mod_question":
					modQuestion($vars);
					showQuestion($vars);
					replyForm($vars);
					break;
	case "post_question":
					postQuestion($vars);
					showForums($vars);
					postForm($vars);
					break;
	case "post_preview":
					showForums($vars);
					postPreview($vars);
					postForm($vars);
					break;
	case "delete_question": 
					deleteQuestion($vars);
					showForums($vars);
					postForm($vars);
					break;
	case "edit_question": 
					showQuestion($vars);
					break;
	case "edit_reply":
					showQuestion($vars);
					break;
	case "show_question":
					showQuestion($vars);
					replyForm($vars);
					break;
	case "reply_preview":
					showQuestion($vars);
					replyPreview($vars);
					replyForm($vars);
					break;
	case "post_reply":
					postQuestion($vars);
					showQuestion($vars);
					replyForm($vars);
					break;
	case "forum_list": 
	default:
					showForums($vars);
					postForm($vars);
}

echo '</td></tr>';
echo '</table>';

echo '</TD></TR></TABLE>';


include 'footer.php';
/* - - - - - - - - - - - */


function modReply($vars) {
	global $IMG_ACCEPT;
	// Get arguments from argument array
    extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$forumstable = $lntable['forums'];
	$forumscolumn = &$lntable['forums_column'];
	$post_time = time();
	$ip = getenv("REMOTE_ADDR");
	$query = "UPDATE  $forumstable SET 	
						$forumscolumn[subject] = '". lnVarPrepForStore($subject) ."',
						$forumscolumn[post_text] = '". lnVarPrepForStore($message) ."',
						$forumscolumn[icon] = '". lnVarPrepForStore($icon) ."',
						$forumscolumn[ip] = '". lnVarPrepForStore($ip) ."',
						$forumscolumn[post_time] = '". lnVarPrepForStore($post_time) ."'
						WHERE $forumscolumn[fid]='$fid'
						";
	$result = $dbconn->Execute($query);

	if (!empty ($delpic)) {
		unlink(_UPLOADIMAGESDIR . '/'. $delpic);
	}

	// upload attached images
	if ($_FILES['filename']['name']){
			$accept_type = 0;											
			foreach ($IMG_ACCEPT as $type) {	//check upload file type
					if ($_FILES['filename']['type'] == $type){
							$accept_type = 1;
							break;
					}
			}

			if ($accept_type){
				$new_images = getNewImageName($fid, $_FILES['filename']['name']);
				if (!@copy($_FILES['filename']['tmp_name'], _UPLOADIMAGESDIR . '/'. $new_images)) {
						$errors =  'Can not upload '. _UPLOADIMAGESDIR . '/' .  $_FILES['filename']['name'];
				}
				unlink($_FILES['filename']['tmp_name']);
			}
			else{
					$errors = "wrong type";
			}
	}
	
	echo $errors;

}

function deleteReply($vars) {
	// Get arguments from argument array
    extract($vars);

	// delete attached image
	 if (file_exists(_UPLOADIMAGESDIR . '/'. $fid.'.gif')) {
		 unlink(_UPLOADIMAGESDIR . '/'. $fid . '.gif');
	 }
	 else if (file_exists(_UPLOADIMAGESDIR . '/'. $fid.'.jpg')) {
		 unlink(_UPLOADIMAGESDIR . '/'. $fid . '.jpg');
	 }
	 else if (file_exists(_UPLOADIMAGESDIR . '/'. $fid.'.png')) {
		 unlink(_UPLOADIMAGESDIR . '/'. $fid . '.png');
	 }

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$forumstable = $lntable['forums'];
	$forumscolumn = &$lntable['forums_column'];
	$query = "DELETE FROM $forumstable WHERE $forumscolumn[fid]='$fid'";
	$result = $dbconn->Execute($query);
}

function modQuestion($vars) {
	global $IMG_ACCEPT;
	// Get arguments from argument array
    extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$forumstable = $lntable['forums'];
	$forumscolumn = &$lntable['forums_column'];
	$post_time = time();
	$ip = getenv("REMOTE_ADDR");
	$query = "UPDATE  $forumstable SET 	
						$forumscolumn[subject] = '". lnVarPrepForStore($subject) ."',
						$forumscolumn[post_text] = '". lnVarPrepForStore($message) ."',
						$forumscolumn[icon] = '". lnVarPrepForStore($icon) ."',
						$forumscolumn[ip] = '". lnVarPrepForStore($ip) ."',
						$forumscolumn[post_time] = '". lnVarPrepForStore($post_time) ."'
						WHERE $forumscolumn[fid]='$fid'
						";
	$result = $dbconn->Execute($query);

	if (!empty ($delpic)) {
		unlink(_UPLOADIMAGESDIR . '/'. $delpic);
	}

	// upload attached images
	if ($_FILES['filename']['name']){
			$accept_type = 0;											
			foreach ($IMG_ACCEPT as $type) {	//check upload file type
					if ($_FILES['filename']['type'] == $type){
							$accept_type = 1;
							break;
					}
			}

			if ($accept_type){
				$new_images = getNewImageName($fid, $_FILES['filename']['name']);
				if (!@copy($_FILES['filename']['tmp_name'], _UPLOADIMAGESDIR . '/'. $new_images)) {
						$errors =  'Can not upload '. _UPLOADIMAGESDIR . '/' .  $_FILES['filename']['name'];
				}
				unlink($_FILES['filename']['tmp_name']);
			}
			else{
					$errors = "wrong type";
			}
	}
	
	echo $errors;

}

function deleteQuestion($vars) {
    extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$forumstable = $lntable['forums'];
	$forumscolumn = &$lntable['forums_column'];
		
	$query = "SELECT $forumscolumn[fid] FROM $forumstable WHERE $forumscolumn[tid]='$tid' ";
	$result = $dbconn->Execute($query);
	for($i=0; list($fid) = $result->fields; $i++) {
		$result->MoveNext();
		 if (file_exists(_UPLOADIMAGESDIR . '/'. $fid.'.gif')) {
			 unlink(_UPLOADIMAGESDIR . '/'. $fid . '.gif');
		 }
		 else if (file_exists(_UPLOADIMAGESDIR . '/'. $fid.'.jpg')) {
			 unlink(_UPLOADIMAGESDIR . '/'. $fid . '.jpg');
		 }
		 else if (file_exists(_UPLOADIMAGESDIR . '/'. $fid.'.png')) {
			 unlink(_UPLOADIMAGESDIR . '/'. $fid . '.png');
		 }
	}

	$query = "DELETE FROM $forumstable WHERE $forumscolumn[tid]='$tid' ";
	$result = $dbconn->Execute($query);
}

function replyPreview($vars) {
    extract($vars);
	$message = filter($message,1);
	$message = stripslashes($message);
	
	echo '<center><BR>';
	echo '<table border=0 width=539  cellspacing=1 cellpadding=3 bgcolor=#999999>';
	echo '<tr><td bgcolor=#996633><FONT COLOR="#FFFFFF"><B>Preview : ข้อความ</B></FONT></td></tr>';
	echo '<tr><td bgcolor=#FFFFFF>'.$message.'</td></tr>';
	echo '</table>';
	echo '</center>';
}

function showQuestion($vars) {
// Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$forumstable = $lntable['forums'];
	$forumscolumn = &$lntable['forums_column'];
	
	$sid = lnGetSubmission($cid);
	$query = "SELECT $forumscolumn[fid],
									$forumscolumn[sid],
									$forumscolumn[tid],
									$forumscolumn[tix],
									$forumscolumn[uid],
									$forumscolumn[subject],
									$forumscolumn[post_text],
									$forumscolumn[icon],
									$forumscolumn[ip],
									$forumscolumn[post_time],
									$forumscolumn[options]
										FROM $forumstable
										WHERE $forumscolumn[tid] = '". lnVarPrepForStore($tid) ."' and $forumscolumn[options] = '1'
										ORDER BY $forumscolumn[tix]
						";
	$result = $dbconn->Execute($query);
	echo '<center>';
	echo '<table border=0  width=98%  cellspacing=1 cellpadding=2 bgcolor=#FFFFFF>';
	echo '<tr><td bgcolor=#FFFFFF align=right><A HREF="index.php?mod=Forums&op=forum_list&cid='.$cid.'&sid='.$sid.'&no=3#PF"><IMG SRC="modules/Forums/images/post.gif" BORDER="0" ALT="ตั้งคำถาม "></A>&nbsp;<A HREF="index.php?mod=Forums&op=show_question&cid='.$cid.'&tid='.$tid.'&sid='.$sid.'&no=3#RF"><IMG SRC="modules/Forums/images/reply.gif"  BORDER=0 ALT=""></td>';
	echo '</table>';

	// show question
	if ($op == "edit_question" || $op == "mod_question_preview") {
		postForm($vars);
	}
	else {
		list($fid_d,$sid,$tid,$tix,$uid,$subject,$post_text,$icon,$ip,$post_time,$options) = $result->fields;
		$userinfo = lnUserGetVars($uid);
		//Path images/avatar/ -- Narasak Tai 25/06/2008 --
		//$avatar = 'images/avatar/' . $userinfo[_AVATAR];
		$avatar = $userinfo[_AVATAR];
		$date = date('Y-M-d', $post_time);
		$time = date('h:i', $post_time);
		$str = Date_Calc::dateFormat2($date,"%d %b %Y") . '-' . $time;
		$poster_at = ' <FONT  COLOR="#888888">'. $str .' </FONT>';
		$subject = filter($subject,1);
		$post_text = filter($post_text,1);
		$subject = stripslashes($subject);
		$post_text = stripslashes($post_text);
		$image = getImageName($fid_d);
		echo '<table border=0 width=98%  cellspacing=0 cellpadding=0>';
		echo '<tr><td height=22 colspan=2 bgcolor=#800000 align=left valign=middle><img src="modules/Forums/images/icons/'.$icon.'.gif">&nbsp;';
		echo '<B><FONT  COLOR="#FFFFFF">'.$subject.'</FONT></B></td></tr>';
		echo "<tr bgcolor=#CFED8A>";
		echo '<td  width=120 align=center valign=top><BR><img src='.$avatar.'><BR><BR><B>'.$userinfo[uname].'</B><BR>'.$poster_at .'<BR></td>';
		echo '<td width=500  bgcolor=#CFED8A valign=top>'.$post_text.'<BR>';

		if (!empty($image)) {
			echo ' <BR><CENTER><IMG SRC="modules/Forums/images/upload/'.$image.'"  BORDER=1 ALT=""></CENTER><BR>';
		}

		echo '</td></tr>';
		echo '<tr height=8>';
		echo '<td colspan=2 bgcolor=#ADD556>';
		echo '<table border=0 width=100%  cellspacing=0 cellpadding=0>';
		//echo '<tr height=22><td><IMG SRC="modules/Forums/images/icon_email.gif"  BORDER=0 ALT=""></td>';
		echo '<td align=right>';
		if (lnSessionGetVar('uid') == $uid) { // pending find instructor also
			echo '<IMG SRC="modules/Forums/images/icon_email.gif"  BORDER=0 ALT="">';
			echo '<A HREF="index.php?mod=Forums&op=edit_question&amp;cid='.$cid.'&amp;tid='.$tid.'&amp;fid='.$fid_d.'"><IMG SRC="modules/Forums/images/edit.gif" BORDER="0" ALT="edit"></A>';
			echo "<A HREF=\"javascript:if(confirm('Delete ?')) window.open('index.php?mod=Forums&op=delete_question&amp;cid=$cid&amp;tid=$tid&amp;fid=$fid_d','_self')\"><IMG SRC=\"modules/Forums/images/delete.gif\"  BORDER=\"0\" ALT=\"edit\"></A>";
		}

		echo '</td>';
		echo '</tr>';

		echo '</table>';
		echo '</td></tr>';
		echo '</table><BR>';
	}
	
/*	echo '<table border=0 width=98%  cellspacing=1 cellpadding=2 bgcolor=#444444>';
	echo '<tr><td><FONT  COLOR="#FFFFFF"><B>ตอบคำถาม</B></FONT></td></tr>';
	echo '</table>';
*/
	// show replies
	$bgcolor = array('#F5F5F5','#F8F8F8');
	
	$result->MoveNext();
	for($i=0; list($fid_d,$sid,$tid,$tix,$uid,$subject,$post_text,$icon,$ip,$post_time,$options) = $result->fields; $i++) {
			$result->MoveNext();
			$userinfo = lnUserGetVars($uid);
			//$avatar = 'images/avatar/' . $userinfo[_AVATAR];
			$avatar = $userinfo[_AVATAR];
			$date = date('Y-M-d', $post_time);
			$time = date('h:i', $post_time);
			$str = Date_Calc::dateFormat2($date,"%d %b %Y") . '  ' . $time;
			$poster_at = ' <FONT  COLOR="#888888">'. $str .' </FONT>';
			$reply_no = getReplyNo($tid);
			$post_text = filter($post_text,1);
			$post_text = stripslashes($post_text);
			$image = getImageName($fid_d);
			echo '<table border=0 width=98%  cellspacing=1 cellpadding=0 bgcolor=#CCCC99>';
			echo '<tr bgcolor='.$bgcolor[$i%2].'>';
			echo '<td valign=top>';
			if ($op == "edit_reply" && $fid_d == $fid ) {
					replyForm($vars);
			}
			else {
				echo '<table border=0 width=100% cellspacing=0 cellpadding=0 bgcolor=#FFFFFF>';
				echo '<tr  bgcolor='.$bgcolor[$i%2].' >';
				echo '<td height=60 width=120 align=center valign=top><BR><img src='.$avatar.'><BR><BR><B>'.$userinfo[uname].'</B><BR>'.$poster_at.'</td>';
				echo '<td width=500 valign=top>'.$post_text.'<BR>';
				if (!empty($image)) {
					echo ' <BR><CENTER><IMG SRC="modules/Forums/images/upload/'.$image.'"  BORDER=1 ALT=""></CENTER><BR>';
				}
				echo '</td>';
				echo '</tr>';
				echo '<tr height=20 bgcolor='.$bgcolor[$i%2].' >';
				echo '<td colspan=2 bgcolor=#E3E1C6 align=right><A HREF="index.php?mod=User&op=profile&amp;uid='.$uid.'"><IMG SRC="modules/Forums/images/icon_profile.gif"  BORDER=0 ALT=""></A><A HREF="index.php?mod=Private_Messages&op=post&amp;to='.$uid.'"><IMG SRC="modules/Forums/images/icon_email.gif"  BORDER=0 ALT=""></A>';
				if (lnSessionGetVar('uid') == $uid) { // pending find instructor also
					echo '<A HREF="index.php?mod=Forums&op=edit_reply&amp;cid='.$cid.'&amp;tid='.$tid.'&amp;fid='.$fid_d.'"><IMG SRC="modules/Forums/images/edit.gif"  BORDER="0" ALT="edit"></A>';
					echo "<A HREF=\"javascript:if(confirm('Delete ?')) window.open('index.php?mod=Forums&op=delete_reply&amp;cid=$cid&amp;tid=$tid&amp;fid=$fid_d','_self')\"><IMG SRC=\"modules/Forums/images/delete.gif\" BORDER=0  ALT=\"edit\"></A>";
				}
				echo '</td>';
				echo '</tr>';
				

				echo '</table>';
			}
			echo '</td>';
			echo '</tr>';
			echo '</table>';
			echo '<br>';

	}
	
	echo '</center>';

}

function postPreview($vars) {
	// Get arguments from argument array
    extract($vars);
	$subject = filter($subject,1);
	$message = filter($message,1);
	$subject = stripslashes($subject);
	$message = stripslashes($message);
	
	echo '<center><BR>';
	echo '<table border=0 width=539  cellspacing=1 cellpadding=3 bgcolor=#999999>';
	echo '<tr><td colspan=2 bgcolor=#996633><FONT COLOR="#FFFFFF"><B>Preview</B></FONT></td></tr>';
	echo '<tr><td width=80 bgcolor=#FFFFFF><B>หัวข้อคำถาม</B>:</td><td bgcolor=#FFFFFF>'.$subject.'</td></tr>';
	echo '<tr><td bgcolor=#FFFFFF><B>รายละเอียด</B>:</td><td bgcolor=#FFFFFF>'.$message.'</td></tr>';
	echo '</table>';
	echo '</center>';
}

function postQuestion($vars) {
	global $IMG_ACCEPT;
	// Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$forumstable = $lntable['forums'];
	$forumscolumn = &$lntable['forums_column'];

//	$sid = lnGetSubmission($cid);
	$uid = lnSessionGetVar('uid');
	$post_time = time();
	$fid = getMaxFID();
	if (empty($tid)) {
		$tid = getMaxTID();
		$tix = 0;
	}
	else {
		$tix = getMaxTIX($tid);
	}
	$ip = getenv("REMOTE_ADDR");
	$subject = stripslashes($subject);
	$message = stripslashes($message);
	$query = "INSERT INTO $forumstable ($forumscolumn[fid],
									$forumscolumn[sid],
									$forumscolumn[tid],
									$forumscolumn[tix],
									$forumscolumn[uid],
									$forumscolumn[subject],
									$forumscolumn[post_text],
									$forumscolumn[icon],
									$forumscolumn[ip],
									$forumscolumn[post_time],
									$forumscolumn[options])
									VALUES ('". lnVarPrepForStore($fid) ."',
									'". lnVarPrepForStore($sid) ."',
									'". lnVarPrepForStore($tid) ."',
									'". lnVarPrepForStore($tix) ."',
									'". lnVarPrepForStore($uid) ."',
									'". lnVarPrepForStore($subject) ."',
									'". lnVarPrepForStore($message) ."',
									'". lnVarPrepForStore($icon) ."',
									'". lnVarPrepForStore($ip) ."',
									'". lnVarPrepForStore($post_time) ."',
									'". lnVarPrepForStore(1) ."')
									";

	$result = $dbconn->Execute($query);
	// upload attached images
	if ($_FILES['filename']['name']){
			$accept_type = 0;											
			foreach ($IMG_ACCEPT as $type) {	//check upload file type
					if ($_FILES['filename']['type'] == $type){
							$accept_type = 1;
							break;
					}
			}

			if ($accept_type){
				$new_images = getNewImageName($fid, $_FILES['filename']['name']);
				if (!@copy($_FILES['filename']['tmp_name'], _UPLOADIMAGESDIR . '/'. $new_images)) {
						$errors =  'Can not upload '. _UPLOADIMAGESDIR . '/' .  $_FILES['filename']['name'];
				}
				unlink($_FILES['filename']['tmp_name']);
			}
			else{
					$errors = "wrong type";
			}
	}
	
	echo $errors;
}

function getNewImageName ($fid, $entry) {
	 if (strpos($entry,".gif")) {
		 $new_images = $fid . '.gif';
	 }
	 else if (strpos($entry,".jpg")) {
		 $new_images = $fid . '.jpg';
	 }
	 else if (strpos($entry,".png")) {
		 $new_images = $fid . '.png';
	 }

	 return $new_images;
}

function getImageName ($fid) {
	 if (file_exists(_UPLOADIMAGESDIR.'/'.$fid.'.gif')) {
		 $new_images = $fid . '.gif';
	 }
	 else if (file_exists(_UPLOADIMAGESDIR.'/'.$fid.'.jpg')) {
		 $new_images = $fid . '.jpg';
	 }
	 if (file_exists(_UPLOADIMAGESDIR.'/'.$fid.'.png')) {
		 $new_images = $fid . '.png';
	 }

	 return $new_images;
}

function getMaxFID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	
	$forumstable = $lntable['forums'];
	$forumscolumn = &$lntable['forums_column'];
	
	$query = "SELECT MAX($forumscolumn[fid]) FROM $forumstable";
	$result = $dbconn->Execute($query);
	list ($maxfid) = $result->fields;
	
	return $maxfid + 1;
}

function getMaxTID() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	
	$forumstable = $lntable['forums'];
	$forumscolumn = &$lntable['forums_column'];
	
	$query = "SELECT MAX($forumscolumn[tid]) FROM $forumstable";
	$result = $dbconn->Execute($query);
	list ($maxtid) = $result->fields;
	
	return $maxtid + 1;
}

function getMaxTIX($tid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	
	$forumstable = $lntable['forums'];
	$forumscolumn = &$lntable['forums_column'];
	
	$query = "SELECT MAX($forumscolumn[tix]) FROM $forumstable WHERE $forumscolumn[tid]='$tid'";
	$result = $dbconn->Execute($query);
	list ($maxtix) = $result->fields;
	
	return $maxtix + 1;
}

function showForums($vars) {
// Get arguments from argument array
    extract($vars);

	if (!empty($sid)) {
		$cid = lnGetCourseID($sid);
	}

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$forumstable = $lntable['forums'];
	$forumscolumn = &$lntable['forums_column'];
	
//	$sid = lnGetSubmission($cid);
	$query = "SELECT $forumscolumn[fid],
									$forumscolumn[sid],
									$forumscolumn[tid],
									$forumscolumn[tix],
									$forumscolumn[uid],
									$forumscolumn[subject],
									$forumscolumn[post_text],
									$forumscolumn[icon],
									$forumscolumn[ip],
									$forumscolumn[post_time],
									$forumscolumn[options]
										FROM $forumstable
										WHERE $forumscolumn[sid] = '". lnVarPrepForStore($sid) ."'  and $forumscolumn[tix] = '0' and $forumscolumn[options] = '1'
										ORDER BY $forumscolumn[post_time] DESC
						";
								
	$result = $dbconn->Execute($query);
//	$bgcolor = array('#F5F5F5','#F8F8F8');
	echo '<center>';
/*
	echo '<table border=0  width=98%  cellspacing=1 cellpadding=2 bgcolor=#FFFFFF>';
	echo '<tr><td width=200><IMG SRC="modules/Forums/images/wb_bullet.gif" WIDTH="15" HEIGHT="15" BORDER="0" ALT="" ALIGN="absmiddle"> <B>กระดานข่าว</B></td><td bgcolor=#FFFFFF align=right><A HREF="index.php?mod=Forums&op=forum_list&cid='.$cid.'&no=2#PF"><IMG SRC="modules/Forums/images/post.gif" BORDER="0" ALT="ตั้งคำถาม "></A>&nbsp;</td></tr>';
	echo '<tr><td colspan="2" height="1" bgcolor="#000000"></td></tr>';
	echo '</table>';
*/
	echo '<table border=0  width=98%  cellspacing=1 cellpadding=2 bgcolor=#FFFFFF>';
	echo '<tr><td bgcolor=#FFFFFF align=right><A HREF="index.php?mod=Forums&op=forum_list&cid='.$cid.'&sid='.$sid.'&no=3#PF"><IMG SRC="modules/Forums/images/post.gif" BORDER="0" ALT="ตั้งคำถาม "></A>&nbsp;</td></tr>';
	echo '<tr><td colspan="2" height="1" bgcolor="#000000"></td></tr>';
	echo '</table>';

	echo '<table border=0  width=98%  cellspacing=0 cellpadding=1 bgcolor=#FFFFFF>';
	$color = array("#FFFFFF","#EEF3A7");
	
	for($i=0; list($fid,$_,$tid,$tix,$uid,$subject,$post_text,$icon,$ip,$post_time,$options) = $result->fields; $i++) {
			$result->MoveNext();
			$userinfo = lnUserGetVars($uid);
			$stime =  Date_Calc::dateFormat3($post_time, "%d %b %Y");
			$reply_no = getReplyNo($tid);
			$poster_at = '<FONT  COLOR="#646400">'.$userinfo[uname].'</FONT><FONT  COLOR="#7F7F7F">['.$reply_no.' - ' . $stime .']</FONT>';
			echo '<tr height=20 bgcolor='.$color[$i%2].'>';
			echo "<td width=10 align=center><img src='modules/Forums/images/icons/$icon.gif'></td>";
			echo '<td><A HREF="index.php?mod=Forums&amp;op=show_question&amp;cid='.$cid.'&amp;tid='.$tid.'&amp;sid='.$sid.'">'.$subject.' </A></td>';
			echo '<td width=180>'. $poster_at. '</td>';
			echo '</td></tr>';
			//echo '<tr><td colspan="3" height="1" bgcolor="#CCCCCC"></td></tr>';
	}
	echo '</table>';
	echo '</center>';

}	

function getReplyNo($tid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$forumstable = $lntable['forums'];
	$forumscolumn = &$lntable['forums_column'];
	
	$sid = lnGetSubmission($cid);
	$query = "SELECT COUNT($forumscolumn[fid])
							FROM $forumstable
							WHERE $forumscolumn[tid] = '". lnVarPrepForStore($tid) ."' and $forumscolumn[tix] <> '0' and $forumscolumn[options] = '1'";

	$result = $dbconn->Execute($query);
	$count = $result->fields[0];
	
	return $count;
}

function postForm($vars) {
	// Get arguments from argument array
    extract($vars);

	$userinfo=lnUserGetVars( lnSessionGetVar('uid'));

	if ($op == "forum_list" || $op == "post_question") {
		$subject = '';
		$message ='';
		$icon = '';
	}
	$subject = stripslashes($subject);
	$message = stripslashes($message);

	if ($op == "edit_question") {
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$forumstable = $lntable['forums'];
		$forumscolumn = &$lntable['forums_column'];
		
//		$sid = lnGetSubmission($cid);
		$query = "SELECT $forumscolumn[fid],
									$forumscolumn[sid],
									$forumscolumn[tid],
									$forumscolumn[tix],
									$forumscolumn[uid],
									$forumscolumn[subject],
									$forumscolumn[post_text],
									$forumscolumn[icon],
									$forumscolumn[ip],
									$forumscolumn[post_time],
									$forumscolumn[options]
										FROM $forumstable
										WHERE $forumscolumn[fid] = '". lnVarPrepForStore($fid) ."' and $forumscolumn[options] = '1'
						";
		$result = $dbconn->Execute($query);
		list($fid,$sid,$tid,$tix,$uid,$subject,$post_text,$icon,$ip,$post_time,$options) = $result->fields;
		$userinfo = lnUserGetVars($uid);
		//$avatar = 'images/avatar/' . $userinfo[_AVATAR];
		$avatar = $userinfo[_AVATAR];
		$subject = stripslashes($subject);
		$post_text = stripslashes($post_text);	
		$subject = stripslashes($subject);
		$message = stripslashes($post_text);
	}

	if (empty($icon)) $icon='xx';
	$iconlist[$icon] = "selected";
	$iconshow = "<img src='modules/Forums/images/icons/$icon.gif' name='icons' border=0 hspace=15>";

	echo "
<P><A NAME=#PF>
 <FORM action='index.php' method='post' name='postquestion' onSubmit='submitonce(this);'  ENCTYPE='multipart/form-data'>
<INPUT TYPE='hidden' NAME='mod' VALUE='Forums'>
<INPUT TYPE='hidden' NAME='op'>
<INPUT TYPE='hidden' NAME='cid' VALUE='$cid'>
<INPUT TYPE='hidden' NAME='sid' VALUE='$sid'>


<script language='JavaScript1.2' src='modules/Forums/javascript/ubbc.js' type='text/javascript'></script>
<script language='JavaScript1.2' type='text/javascript'>
<!--
function showimage()
{
	document.images.icons.src='modules/Forums/images/icons/'+document.postquestion.icon.options[document.postquestion.icon.selectedIndex].value+'.gif';
}

function preview() {
        if (document.forms[0].filename.value){
			document.previewPict.src = document.forms[0].filename.value;
			document.previewPict.height = 100;
        }
}

setInterval('preview()',100);

//-->
</script>

<center>
<a name='#PF'>
<table border=0  width='539'  cellspacing=1 cellpadding=3 bgcolor='#800000'>
  <tr>
    <td bgcolor='#800000'><IMG SRC=modules/Forums/images/post1.jpg BORDER=0></td>
  </tr>
  <tr>
    <td bgcolor='#FFD9EC'>
    <table border=0 cellpadding='1' width=100%>
   <tr>
        <td align='right'><font size=2 color=#000000><b>หัวข้อคำถาม:</b></font></td>
        <td><font size=2><input type=text name='subject' value=\"$subject\" size=50 maxlength=80></font></td>
      </tr><tr>
	<td align='right'><font size=2 color=#000000><b>ไอคอนข้อความ:</b></font></td>
	<td>
	<select name='icon' onChange='showimage()'>
		<option value='xx' ".$iconlist[xx].">มาตราฐาน
		<option value='thumbup' ".$iconlist[thumbup].">รูปยกหัวแม่มือ
		<option value='thumbdown' ".$iconlist[thumbdown].">รูปยกหัวแม่มือลง
		<option value='exclamation' ".$iconlist[exclamation].">ป้ายเตือน
		<option value='question' ".$iconlist[question].">เครื่องหมายคำถาม
		<option value='lamp' ".$iconlist[lamp].">รูปดวงไฟ
		<option value='smiley' ".$iconlist[smiley].">ยิ้ม
		<option value='angry' ".$iconlist[angry].">โกรธ
		<option value='cheesy' ".$iconlist[cheesy].">ดีใจมาก
		<option value='laugh' ".$iconlist[langh].">หัวเราะ
		<option value='sad' ".$iconlist[sad]. ">เศร้า
		<option value='wink' ".$iconlist[wink].">ยิ้มแบบขยิบตา
	</select>
	".$iconshow."
	</td>
	</tr>

	<tr>
	<td valign=top align=right><font size=2 color=#000000><b>รายละเอียด:</b></font></td>
	<td><textarea name='message' rows=12 cols=30 style='width:95%' wrap=virtual ONSELECT='javascript:storeCaret(this);' ONCLICK='javascript:storeCaret(this);' ONKEYUP='javascript:storeCaret(this);' ONCHANGE='javascript:storeCaret(this);'>$message</textarea></td>
	</tr>";

  if (lnConfigGetVar('showsmiley')) { 
	  echo "
	
	<tr>
	<td align=right><font size=2 color=#000000><b>ยิ้มกว้าง ๆ:</b></font></td>
	<td valign=middle>
	<script language='JavaScript1.2' type='text/javascript'>
	<!--
	if((navigator.appName == 'Netscape' && navigator.appVersion.charAt(0) >= 4) || (navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.charAt(0) >= 4) || (navigator.appName == 'Opera' && navigator.appVersion.charAt(0) >= 4)) {
		document.write(\"<a href=javascript:smiley()><img src='modules/Forums/images/icons/smiley.gif' align=bottom alt='ยิ้ม' border='0'></a> \");
		document.write(\"<a href=javascript:wink()><img src='modules/Forums/images/icons/wink.gif' align=bottom alt='ยิ้มแบบขยิบตา' border='0'></a> \");
		document.write(\"<a href=javascript:cheesy()><img src='modules/Forums/images/icons/cheesy.gif' align=bottom alt='ดีใจมาก' border='0'></a> \");
		document.write(\"<a href=javascript:grin()><img src='modules/Forums/images/icons/grin.gif' align=bottom alt='ยิ้มยิงฟัน' border='0'></a> \");
		document.write(\"<a href=javascript:angry()><img src='modules/Forums/images/icons/angry.gif' align=bottom alt='โกรธ' border='0'></a> \");
		document.write(\"<a href=javascript:sad()><img src='modules/Forums/images/icons/sad.gif' align=bottom alt='เศร้า' border='0'></a> \");
		document.write(\"<a href=javascript:shocked()><img src='modules/Forums/images/icons/shocked.gif' align=bottom alt='ช็อค' border='0'></a> \");
		document.write(\"<a href=javascript:cool()><img src='modules/Forums/images/icons/cool.gif' align=bottom alt='เจ๋ง' border='0'></a> \");
		document.write(\"<a href=javascript:huh()><img src='modules/Forums/images/icons/huh.gif' align=bottom alt='อืม' border='0'></a> \");
		document.write(\"<a href=javascript:rolleyes()><img src='modules/Forums/images/icons/rolleyes.gif' align=bottom alt='ขยิบตา' border='0'></a> \");
		document.write(\"<a href=javascript:tongue()><img src='modules/Forums/images/icons/tongue.gif' align=bottom alt='แลบลิ้น' border='0'></a> \");
		document.write(\"<a href=javascript:embarassed()><img src='modules/Forums/images/icons/embarassed.gif' align=bottom alt='อายหน้าแดง' border='0'></a> \");
		document.write(\"<a href=javascript:lipsrsealed()><img src='modules/Forums/images/icons/lipsrsealed.gif' align=bottom alt='ปิดปากไม่พูด' border='0'></a> \");
		document.write(\"<a href=javascript:undecided()><img src='modules/Forums/images/icons/undecided.gif' align=bottom alt='ลังเล' border='0'></a> \");
		document.write(\"<a href=javascript:kiss()><img src='modules/Forums/images/icons/kiss.gif' align=bottom alt='จูบ' border='0'></a> \");
		document.write(\"<a href=javascript:cry()><img src='modules/Forums/images/icons/cry.gif' align=bottom alt='ร้องไห้' border='0'></a> \");
	}
	else { document.write(\"<font size=1>บราวเซอร์ไม่คอมแพตเทเบิ้ลกับปุ่มนี้</font>\"); }
	//-->
	</script>
	<noscript>
	<font size=1>บราวเซอร์ไม่คอมแพตเทเบิ้ลกับปุ่มนี้</font>
	</noscript>
	</td>
	</tr>";

 } 


if (lnConfigGetVar('uploadpic')) { 
echo "
	<tr>
        <td valign=top align='right'><font size=2 color=#000000><b>ภาพประกอบ:</b></font></td>
        <td>
			<input type=file name='filename' value='' size='20' maxlength='50'><font size=2 color=#800000>(ขนาดไม่เกิน 50KB)</font><BR>
			<BR>";
	if ($image= getImageName($fid)) {				
		echo "<img name=previewPict src='modules/Forums/images/upload/$image' border=0>";
		echo '<INPUT TYPE="checkbox" NAME="delpic" value='.$image.'> ลบภาพนี้';
	}
	else {
		echo "<img name=previewPict src='modules/Forums/images/blank.gif' border=0>";
	}

	echo "</td>
	</tr>";
}

echo "	
	<tr>
	<td>&nbsp;</td><td>";
if ($op == "edit_question" || $op == "mod_question_preview" ) {
	echo "<BR>
		<input type='hidden' name='tid' value='$tid'>
		<input type='hidden' name='fid' value='$fid'>
		<input type='submit' value='แก้คำถาม' onClick=\"WhichClicked('mod_question');\">
		<input type='submit' value='ตรวจสอบก่อนส่ง' onClick=\"WhichClicked('mod_question_preview');\">
		<input type='submit' value='ยกเลิกนะ' onClick=\"WhichClicked('show_question');\">
	";
}
else {
	echo "
		<input type='submit' value='ส่งคำถาม' onClick=\"WhichClicked('post_question');\">
		<input type='submit' value='ตรวจสอบก่อนส่ง' onClick=\"WhichClicked('post_preview');\">
		<input type='submit' value='ยกเลิก' onClick=\"WhichClicked('forum_list');\">
	";
}
echo "	
	</td>
	</tr>
	<tr>
	<td colspan=2></td>
	</tr>
	</table>
</td>
</tr>
</table>
</center>
</FORM>
	";
}


function replyForm($vars) {
	// Get arguments from argument array
    extract($vars);

	$userinfo=lnUserGetVars( lnSessionGetVar('uid'));

	if ($op == "show_question" || $op == "post_reply" || $op == "mod_question" || $op == "mod_reply") {
		$fid = 0;
		$message ='';
	}
	$message = stripslashes($message);

	if ($op == "edit_reply") {
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$forumstable = $lntable['forums'];
		$forumscolumn = &$lntable['forums_column'];
		
//		$sid = lnGetSubmission($cid);
		$query = "SELECT $forumscolumn[fid],
									$forumscolumn[sid],
									$forumscolumn[tid],
									$forumscolumn[tix],
									$forumscolumn[uid],
									$forumscolumn[subject],
									$forumscolumn[post_text],
									$forumscolumn[icon],
									$forumscolumn[ip],
									$forumscolumn[post_time],
									$forumscolumn[options]
										FROM $forumstable
										WHERE $forumscolumn[fid] = '". lnVarPrepForStore($fid) ."' and $forumscolumn[options] = '1'
						";
		$result = $dbconn->Execute($query);
		list($fid,$sid,$tid,$tix,$uid,$subject,$post_text,$icon,$ip,$post_time,$options) = $result->fields;
		$userinfo = lnUserGetVars($uid);
		//$avatar = 'images/avatar/' . $userinfo[_AVATAR];
		$avatar = $userinfo[_AVATAR];
		$subject = stripslashes($subject);
		$post_text = stripslashes($post_text);	
		$subject = stripslashes($subject);
		$message = stripslashes($post_text);
	}

	echo "
<P>
 <FORM action='index.php' method='post' name='postquestion' onSubmit='submitonce(this);'  ENCTYPE='multipart/form-data'>
<INPUT TYPE='hidden' NAME='mod' VALUE='Forums'>
<INPUT TYPE='hidden' NAME='op'>
<INPUT TYPE='hidden' NAME='cid' VALUE='$cid'>
<INPUT TYPE='hidden' NAME='tid' VALUE='$tid'>
<INPUT TYPE='hidden' NAME='sid' VALUE='$sid'>


<script language='JavaScript1.2' src='modules/Forums/javascript/ubbc.js' type='text/javascript'></script>
<script language='JavaScript1.2' type='text/javascript'>
<!--
function preview() {
	if (document.forms[0].filename.value){
		document.previewPict.src = document.forms[0].filename.value;
		document.previewPict.height = 100;
	}
}

setInterval('preview()',100);

//-->
</script>

<center>
<a name='#RF'>
<table border=0  width='539'  cellspacing=1 cellpadding=3 bgcolor='#800000'>
  <tr>
    <td bgcolor='#800000'><IMG SRC=modules/Forums/images/post1.jpg BORDER=0></td>
  </tr>
  <tr>
    <td bgcolor='#FFD9EC'>
    <table border=0 cellpadding='1' width=100%>
	<tr>
	<td width=15% valign=top align=right><font size=2 color=#000000><b>ข้อความ:</b></font></td>
	<td><textarea name='message' rows=10 cols=30 style='width:95%' wrap=virtual ONSELECT='javascript:storeCaret(this);' ONCLICK='javascript:storeCaret(this);' ONKEYUP='javascript:storeCaret(this);' ONCHANGE='javascript:storeCaret(this);'>$message</textarea></td>
	</tr>
 
	<tr>
	<td align=right><font size=2 color=#000000><b>ยิ้มกว้าง ๆ:</b></font></td>
	<td valign=middle>
	<script language='JavaScript1.2' type='text/javascript'>
	<!--
	if((navigator.appName == 'Netscape' && navigator.appVersion.charAt(0) >= 4) || (navigator.appName == 'Microsoft Internet Explorer' && navigator.appVersion.charAt(0) >= 4) || (navigator.appName == 'Opera' && navigator.appVersion.charAt(0) >= 4)) {
		document.write(\"<a href=javascript:smiley()><img src='modules/Forums/images/icons/smiley.gif' align=bottom alt='ยิ้ม' border='0'></a> \");
		document.write(\"<a href=javascript:wink()><img src='modules/Forums/images/icons/wink.gif' align=bottom alt='ยิ้มแบบขยิบตา' border='0'></a> \");
		document.write(\"<a href=javascript:cheesy()><img src='modules/Forums/images/icons/cheesy.gif' align=bottom alt='ดีใจมาก' border='0'></a> \");
		document.write(\"<a href=javascript:grin()><img src='modules/Forums/images/icons/grin.gif' align=bottom alt='ยิ้มยิงฟัน' border='0'></a> \");
		document.write(\"<a href=javascript:angry()><img src='modules/Forums/images/icons/angry.gif' align=bottom alt='โกรธ' border='0'></a> \");
		document.write(\"<a href=javascript:sad()><img src='modules/Forums/images/icons/sad.gif' align=bottom alt='เศร้า' border='0'></a> \");
		document.write(\"<a href=javascript:shocked()><img src='modules/Forums/images/icons/shocked.gif' align=bottom alt='ช็อค' border='0'></a> \");
		document.write(\"<a href=javascript:cool()><img src='modules/Forums/images/icons/cool.gif' align=bottom alt='เจ๋ง' border='0'></a> \");
		document.write(\"<a href=javascript:huh()><img src='modules/Forums/images/icons/huh.gif' align=bottom alt='อืม' border='0'></a> \");
		document.write(\"<a href=javascript:rolleyes()><img src='modules/Forums/images/icons/rolleyes.gif' align=bottom alt='ขยิบตา' border='0'></a> \");
		document.write(\"<a href=javascript:tongue()><img src='modules/Forums/images/icons/tongue.gif' align=bottom alt='แลบลิ้น' border='0'></a> \");
		document.write(\"<a href=javascript:embarassed()><img src='modules/Forums/images/icons/embarassed.gif' align=bottom alt='อายหน้าแดง' border='0'></a> \");
		document.write(\"<a href=javascript:lipsrsealed()><img src='modules/Forums/images/icons/lipsrsealed.gif' align=bottom alt='ปิดปากไม่พูด' border='0'></a> \");
		document.write(\"<a href=javascript:undecided()><img src='modules/Forums/images/icons/undecided.gif' align=bottom alt='ลังเล' border='0'></a> \");
		document.write(\"<a href=javascript:kiss()><img src='modules/Forums/images/icons/kiss.gif' align=bottom alt='จูบ' border='0'></a> \");
		document.write(\"<a href=javascript:cry()><img src='modules/Forums/images/icons/cry.gif' align=bottom alt='ร้องไห้' border='0'></a> \");
	}
	else { document.write(\"<font size=1>บราวเซอร์ไม่คอมแพตเทเบิ้ลกับปุ่มนี้</font>\"); }
	//-->
	</script>
	<noscript>
	<font size=1>บราวเซอร์ไม่คอมแพตเทเบิ้ลกับปุ่มนี้</font>
	</noscript>
	</td>
	</tr>

	<tr>
        <td align='right' valign=top><font size=2 color=#000000><b>ภาพประกอบ:</b></font></td>
        <td><input type=file name='filename' value='' size='30' maxlength='50'> <font size=2 color=#800000>(ขนาดไม่เกิน 50KB)</font><BR>";
		if ($image= getImageName($fid)) {				
			echo "<img name=previewPict src='modules/Forums/images/upload/$image' border=0>";
			echo '<INPUT TYPE="checkbox" NAME="delpic" value='.$image.'> ลบภาพนี้';
		}
		else {
			echo "<img name=previewPict src='modules/Forums/images/blank.gif' border=0>";
		}
		echo "</td>
	</tr>
    <!-- <tr>
        <td align='right'><font size=2 color=#000000><b>ชื่อผู้ส่ง:</b></font></td>
        <td><B>".$userinfo[uname]."</B></td>
    </tr> -->
	<tr>
	<td>&nbsp;</td><td>";
	if ($op == "edit_reply") {
	echo "<BR>
		<input type='hidden' name='tid' value='$tid'>
		<input type='hidden' name='fid' value='$fid'>
		<input type='submit' value='แก้คำถาม' onClick=\"WhichClicked('mod_reply');\">
		<input type='submit' value='ตรวจสอบก่อนส่ง' onClick=\"WhichClicked('mod_reply_preview');\">
		<input type='submit' value='ยกเลิกนะ' onClick=\"WhichClicked('show_question');\">
		";
	}
	else {
		echo "<BR>
		<input type='submit' value='ส่งคำถาม' onClick=\"WhichClicked('post_reply');\">
		<input type='submit' value='ตรวจสอบก่อนส่ง' onClick=\"WhichClicked('reply_preview');\">
		<input type='submit' value='ยกเลิก' onClick=\"WhichClicked('show_question');\">
		";
	}	

	echo "
	</td>
	</tr>
	<tr>
	<td colspan=2></td>
	</tr>
	</table>
</td>
</tr>
</table>
</center>
</FORM>
	";
}


/*- - - Filter  message and emotion code - - -*/
function filter($data,$efcode)
{
	global $config;
	$config['smileysdir'] = 'modules/Forums/images/icons';

	$data = str_replace("<","&lt;",$data);
	$data = str_replace(">","&gt;",$data);
	$data = nl2br($data);
	
	if ($efcode) {
		$data = " ".$data;
		$data = preg_replace("#([\n ])([a-z]+?)://([^, \n\r]+)#i", "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>", $data);
		$data = preg_replace("#([\n ])www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:/[^, \n\r]*)?)#i", "\\1<a href=\"http://www.\\2.\\3\\4\" target=\"_blank\">www.\\2.\\3\\4</a>", $data);
		$data = preg_replace("#([\n ])([a-z0-9\-_.]+?)@([^, \n\r]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $data);

		/* Remove space */
		$data = substr($data, 1);
		$data = str_replace("\n","",$data);
		$data = str_replace("\r","",$data);
		
		$data = str_replace("::)","<IMG src=\"$config[smileysdir]/rolleyes.gif\" border=0>",$data);
		$data = str_replace(":)","<IMG src=\"$config[smileysdir]/smiley.gif\" border=0>",$data);
		$data = str_replace(";)","<IMG src=\"$config[smileysdir]/wink.gif\" border=0>",$data);
		$data = str_replace(":D","<IMG src=\"$config[smileysdir]/cheesy.gif\" border=0>",$data);
		$data = str_replace(";D","<IMG src=\"$config[smileysdir]/grin.gif\" border=0>",$data);
		$data = str_replace("&gt;:(","<IMG src=\"$config[smileysdir]/angry.gif\" border=0>",$data);
		$data = str_replace(":(","<IMG src=\"$config[smileysdir]/sad.gif\" border=0>",$data);
		$data = str_replace(":o","<IMG src=\"$config[smileysdir]/shocked.gif\" border=0>",$data);
		$data = str_replace("8)","<IMG src=\"$config[smileysdir]/cool.gif\" border=0>",$data);
		$data = str_replace("???","<IMG src=\"$config[smileysdir]/huh.gif\" border=0>",$data);
		$data = str_replace(":P","<IMG src=\"$config[smileysdir]/tongue.gif\" border=0>",$data);
		$data = str_replace(":-[","<IMG src=\"$config[smileysdir]/embarassed.gif\" border=0>",$data);
		$data = str_replace(":-X","<IMG src=\"$config[smileysdir]/lipsrsealed.gif\" border=0>",$data);
		$data = str_replace(":-/","<IMG src=\"$config[smileysdir]/undecided.gif\" border=0>",$data);
		$data = str_replace(":-*","<IMG src=\"$config[smileysdir]/kiss.gif\" border=0>",$data);
		$data = str_replace(":*(","<IMG src=\"$config[smileysdir]/cry.gif\" border=0>",$data);

		$data = preg_replace("/\[b\](.*?)\[\/b\]/si", "<B>\\1</B>", $data);
		$data = preg_replace("/\[i\](.*?)\[\/i\]/si", "<I>\\1</I>", $data);
		$data = preg_replace("/\[u\](.*?)\[\/u\]/si", "<U>\\1</U>", $data);
		$data = preg_replace("/\[url\](http:\/\/)?(.*?)\[\/url\]/si", "<A HREF=\"http://\\2\" TARGET=\"_blank\">\\2</A>", $data);
		$data = preg_replace("/\[url=(http:\/\/)?(.*?)\](.*?)\[\/url\]/si", "<A HREF=\"http://\\2\" TARGET=\"_blank\">\\3</A>", $data);
		$data = preg_replace("/\[email\](.*?)\[\/email\]/si", "<A HREF=\"mailto:\\1\">\\1</A>", $data);
		$data = preg_replace("/\[img\](.*?)\[\/img\]/si", "<IMG SRC=\"\\1\">", $data);
		$data = preg_replace("/\[code\](.*?)\[\/code\]/si", "<p><blockquote><font face='ms sans serif'  size=1>code:</font><HR noshade size=1><pre>\\1<br></pre><HR noshade size=1></blockquote><p>", $data);	
	}

	// Bad word filter
	$repchar = '.';
	
	for($i=0;$i<sizeof($lang['badwords']);$i++){
		$rep = '';
		$ltrs = strlen($lang['badwords'][$i])-1;
		for ($n=0;$n<$ltrs;$n++){
			$rep .= $repchar;
		}
		$replacement = substr($lang['badwords'][$i],0,1).$rep;
		$data = preg_replace('/'.$lang['badwords'][$i].'/i',$replacement,$data);
	}
	
	return $data;
}

?>
