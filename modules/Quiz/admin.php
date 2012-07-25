<?php
/**
 *  Quiz administration
 */
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

$vars= array_merge($_GET,$_POST);
//include 'header.php';

/**
 * quiz functions
 */
function quiz($vars) {
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
	tabCourseAdmin($cid,3);
	echo '</TD></TR><TR><TD>';

	echo '<table width= 100% class="main" cellpadding=3 cellspacing=1 border="0">';
	echo '<tr><td valign="top">';


if (!empty($action)) {

	switch($action) {

		case "createquiz" :
		  	include("createQuiz.php");
			quizIssue($vars); return;

		case "createtest" :
			include("createTest.php");
			/*quiz($vars);*/ return;

		case "importtest" :
			include("importTest.php");
			return;

	}
}

	echo '<BR><TABLE WIDTH=100% cellspacing=0 cellpading=0>'.'<TR ALIGN=CENTER>';

	echo '<TD ALIGN=CENTER><a href="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createquiz&amp;cid='.$cid.'">'.lnBlockImage('Quiz','quizCreate').'<BR><B>'._CREATEQUIZ.'</B></a> </TD> ';


	echo '<TD ALIGN=CENTER><a href="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=createtest&amp;cid='.$cid.'">'.lnBlockImage('Quiz','quizManage').'<BR><B>'._CREATETEST.'</B></a> </TD> ';


	echo '<TD ALIGN=CENTER><a href="index.php?mod=Courses&amp;file=admin&amp;op=quiz&amp;action=importtest&amp;cid='.$cid.'">'.lnBlockImage('Quiz','quizImport').'<BR><B>'._IMPORTTEST.'</B></a> </TD> ';


	echo '</TR></TABLE>';

	echo '</TD></TR></TABLE>';

//include 'footer.php';
	
}




?>