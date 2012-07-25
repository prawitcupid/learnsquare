<?php
///////////////////////////////////////////////////////////////////////////////////////////////
// LEARNING-NUKE: ADVANCED LEARNING MANAGEMENT SYSTEM //===============================================================================
// Original Author of this file: S.Kongdej 
// Purpose of this file: Directs to the start page as defined in config.php
// Copyright (C) 2004 by the Learning-Nuke Development Team.
// http://www.learningnuke.com/
// -----------------------------------------------------------------------------------------------------------------------------------------
// Based on:
// POST-NUKE - http://postnuke.org
// and PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
//
// This program is free software. You can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation; either version 2 of the License.
///////////////////////////////////////////////////////////////////////////////////////////////

// Include base api
include ("includes/lnAPI.php");
date_default_timezone_set ("Asia/Bangkok");

// Start  LN
lnInit();

//Mobile Redirector
if(lnConfigGetVar('MobileStatus')){
	include 'mobileRedirector.php';
}

//Tracking
$trackname = lnSessionGetVar('uid');
//echo "<B>************".$trackname."*************</B><BR>";
$track = lnSessionGetVar($trackname);
//echo "<B>************".$track['eid']."*************</B><BR>";
//echo "<B>************".$track['lid']."*************</B><BR>";

if($track['eid']!=""&&$track['lid']!=""){
	//echo "<B>************GET ".$track['eid']."*************</B><BR>";
	//echo "<B>************GET ".$track['lid']."*************</B><BR>";
	//record timeout
	//function outtime($lid,$eid){
		//$lidthis = $lid;
		$eid = $track['eid'];
		$lid = $track['lid'];
		
		$timethis = time();

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$course_trackingtable = $lntable['course_tracking'];
		$course_trackingcolumn = &$lntable['course_tracking_column'];

		$query = "SELECT  MAX($course_trackingcolumn[atime])
		FROM $course_trackingtable
		WHERE $course_trackingcolumn[eid]='$eid' AND $course_trackingcolumn[lid]='$lid'";
		$result = $dbconn->Execute($query);
		//echo "<hr>".$query."<hr>";
		while(list($atime) = $result->fields) {
			$result->MoveNext();
			$atimes =$atime;
		}
		$query = "SELECT  MAX($course_trackingcolumn[outime])
		FROM $course_trackingtable
		WHERE $course_trackingcolumn[eid]='$eid' AND $course_trackingcolumn[lid]='$lid' AND $course_trackingcolumn[atime] = '$atimes'";
		$result = $dbconn->Execute($query);
		//echo "<hr>".$query."<hr>";
		while(list($outime) = $result->fields) {
			$result->MoveNext();
			$outimes =$outime;
		}
		if($outimes ==""){
			$query1 = "UPDATE $course_trackingtable  SET  $course_trackingcolumn[outime]  = '$timethis'
			WHERE $course_trackingcolumn[eid]='$eid' AND $course_trackingcolumn[lid]='$lid' AND $course_trackingcolumn[atime] = '$atimes'";
			$result = $dbconn->Execute($query1);
			//echo "<hr>".$query1."<hr>";
		}
	//}
	//Set Outtime form lesson
	//outtime($track['lid'],$track['eid']);
	lnSessionDelVar($trackname);
	//echo "<B>************DELETE*************</B><BR>";
}//else{echo "<B>@@@@@@@@@@@@ NO Token @@@@@@@@@@@@@</B><BR>";}

// Get variables
list($mod, $file, $op) = lnVarCleanFromInput('mod','file','op');

// Check requested module and set to start module if not present
if (empty($mod)) {
    $module = lnConfigGetVar('startpage');
}
else {
	$module = $mod;
}

if (empty($file)) {
	$file="index";
}

if (lnModAvailable($module)) {
	if ($modfile = lnModLoad($module,$file)) {
		define("LOADED_AS_MODULE","1");
		include $modfile;
	}
	else {
		error('Module "' . lnVarPrepForOS($module) . '" file does not exist.');
	}
}
else {
	error('Module "' . lnVarPrepForOS($module) . '" is not available.');
}

?>