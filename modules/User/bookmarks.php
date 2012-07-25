<?php
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'User::Profile', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

$vars = array_merge($_GET,$_POST);
extract($vars);
//echo 'bookmarks';
/* options */
switch ($op) {
	case "add":		bookmarksAdd($vars);	break;
	case "delete":	bookmarksDelete($vars); break;
	case "bookmarks_delete":	bookmarksDelete($vars); bookmarksShow($vars); break;
	case "bookmarks_show":		bookmarksShow($vars);
	default :					
}

/* - - - - - - - - - - - */

function bookmarksShow($vars) {
	
	include 'header.php';

	OpenTable();
	$lang = lnConfigGetVar('language');
	$imgfile = "modules/User/images/$lang/bookmark.jpg";
		
	//echo '<img src='.$imgfile.' border=0>';
	echo '<p class="header"><b>'._BOOKMARK_TITLE.'</b></p>';

	extract($vars);
	
	$uid = lnSessionGetVar('uid');
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$bookmarkstable = $lntable['bookmarks'];
	$bookmarkscolumn = &$lntable['bookmarks_column'];
	
	$query = "SELECT $bookmarkscolumn[bid],$bookmarkscolumn[uid],$bookmarkscolumn[sid],$bookmarkscolumn[cid],$bookmarkscolumn[lid],$bookmarkscolumn[page],$bookmarkscolumn[date] 
	FROM  $bookmarkstable WHERE $bookmarkscolumn[uid]=$uid";
	//echo $query;
	$result = $dbconn->Execute($query);

	echo '<br>';	
	echo '<div id="tabs">';
	echo '<table border="0" cellpadding="1" cellspacing="1" width="100%">';
	echo '<tr bgcolor="#D2E9FF" align="center"><td>No.</td><td>Lesson</td><td>Course</td><td>Date</td><td>Delete</td></tr>';
	$i=1;
	while(list($bid,$uid,$sid,$cid,$lid,$page,$date) = $result->fields) {
		$result->MoveNext();
		$lesson = lnLessonGetVars($lid);
		$course = lnCourseGetVars($cid);
		//$school = lnSchoolGetVars($sid);
		$title = $lesson['title'];
		//$len = mb_strlen($title);
		//$title = mb_substr($title,-$len,$len);
		echo '<tr><td>'.$i.'</td><td><a href="index.php?mod=Courses&op=lesson_show&uid='.$uid.'&cid='.$cid.'&sid='.$sid.'&lid='.$lid.'&page='.$page.'"> '.$title.' </a></td>'; 
		echo'<td><a href="index.php?mod=Courses&op=course_lesson&cid='.$cid.'">'.$course['title'].'</a></td><td>'.$date.'</td>';
		echo'<td><a href="index.php?mod=User&file=bookmarks&op=bookmarks_delete&lid='.$lid.'"><image src="images/global/delete1.jpg" border="0"></a></td></tr>';
		$i++;
	}
	echo '</table>';
	echo '</div>';
		
	CloseTable();
	include 'footer.php';
}

function bookmarksAdd($vars) {
	extract($vars);
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$bookmarkstable = $lntable['bookmarks'];
	$bookmarkscolumn = &$lntable['bookmarks_column'];

	$query = "INSERT INTO $bookmarkstable ($bookmarkscolumn[uid],$bookmarkscolumn[sid],$bookmarkscolumn[cid],$bookmarkscolumn[lid],$bookmarkscolumn[page],$bookmarkscolumn[date]) 
	VALUES ($uid,$sid,$cid,$lid,$page,NOW())";

	$result = $dbconn->Execute($query);
	echo $query;
}

function bookmarksDelete($vars) {
    extract($vars);
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	
	if(!$uid) $uid = lnSessionGetVar('uid');
	
	$bookmarkstable = $lntable['bookmarks'];
	$bookmarkscolumn = &$lntable['bookmarks_column'];
	
	$query = "DELETE FROM $bookmarkstable WHERE $bookmarkscolumn[uid]='$uid' AND $bookmarkscolumn[lid]='$lid' ";
	$result = $dbconn->Execute($query);
	echo $query;
}

function bookmarksCheck($uid,$lid) {
	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$bookmarkstable = $lntable['bookmarks'];
	$bookmarkscolumn = &$lntable['bookmarks_column'];

	$query = "SELECT COUNT(*) FROM $bookmarkstable
	WHERE $bookmarkscolumn[uid]=$uid AND $bookmarkscolumn[lid]=$lid ";

	$result = $dbconn->Execute($query);
	list($count) = $result->fields[0];
	return $count;
}

?>