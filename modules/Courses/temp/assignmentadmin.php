<?php
/*
* assignment administration
*/
	if (!defined("LOADED_AS_MODULE")) {
		die ("You can't access this file directly...");
	}


/*
* assignment fuctions
*/
function assignment($vars) {
	global $menus, $links;

	// Get arguments from argument array
    extract($vars);
	
	$courseinfo = lnCourseGetVars($cid);
	$coursecode=$courseinfo['code'];
	$coursename=$courseinfo['title'];
	$url = COURSE_DIR.'/'.$coursecode;

	/** Navigator **/
	$menus[]= $coursecode.' : '.$coursename;
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/

	tabCourseAdmin($cid,4);
	
	echo '<table width= 100% class="main" cellpadding=3 cellspacing=0 border=0>';
	echo '<tr><td valign="top">';
	
	/* options */
	if (!empty($action)) {
		switch($action) {
			case "insert_assignment" :addAssignmentForm($action,$cid,$lid,1,null); return;
			case "add_assignment" : addAssignment($vars); break;
			case "delete_assignment" : deleteAssignment($vars); break;
			case "update_assignment" : updateAssignment($vars); break;
			case "increase_weight": increaseAssignmentWeight($vars); break;
			case "decrease_weight": decreaseAssignmentWeight($vars); break;
		}
	}
	/* options */


	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
	$assignmentstable = $lntable['assignments'];
	$assignmentscolumn = &$lntable['assignments_column'];

	

	// List lessons
	$query = "SELECT $lessonscolumn[lid]
					FROM  $lessonstable
					WHERE $lessonscolumn[cid] =  '" . lnVarPrepForStore($cid) . "'";

	$result = $dbconn->Execute($query);
	$lessonrows = $result->PO_RecordCount();

	// List assignment
	$query = "SELECT $assignmentscolumn[aid],
					$assignmentscolumn[lid],
					$assignmentscolumn[title],
					$assignmentscolumn[question],
					$lessonscolumn[weight],
					$lessonscolumn[title]
					FROM  $assignmentstable, $lessonstable
					WHERE $assignmentscolumn[lid]=$lessonscolumn[lid] AND $lessonscolumn[cid] =  '" . lnVarPrepForStore($cid) . "'
					ORDER BY $lessonscolumn[weight]";

	$result = $dbconn->Execute($query);
	$numrows = $result->PO_RecordCount();

	echo '<table width="100%" cellpadding=3 cellspacing=0 border=0>';
	
	if ($numrows > 0) {

		$rownum = 1;
		$lastpos = '';
		$active_count = 0;

		while(list($aid,$lid,$title,$question,$weight,$lesson_title) = $result->fields) {
			$result->MoveNext();
			$prev_active_count = $active_count;
			$active_count++;
			$rownum++;
			$lastpos = $position;

			if ($action == "edit_assignment" && $active_count == $item) {
				echo "<tr><td valign=top><B><font  color=#800000>"._LESSONNO." ".$weight." ". lnVarPrepForDisplay($lesson_title)."</font></td></tr>"; 
				echo '<tr valign=top valign=top>'
				.'<td BGCOLOR="#EEF3A7">';
				addAssignmentForm($action,$cid,$lid,$item,$aid);
				echo '</td></tr>';
			}
			else {
				echo "<tr valign=top bgcolor=#ffffff valign=top>";
				echo "<td>";
				echo "<table cellpadding=0 cellspacing=0 border=0 width=100%>";
				echo "<tr><td valign=top><B><font  color=#800000>"._LESSONNO." ".$weight." ". lnVarPrepForDisplay($lesson_title)."</font><br><br> - ". lnVarPrepForDisplay($title)."</B></td>";
				echo "<td align=right valign=top>";
				echo "<A HREF=\"index.php?mod=Courses&amp;file=admin&amp;op=assignment&amp;action=edit_assignment&amp;cid=$cid&amp;aid=$aid&amp;item=$active_count&amp;cid=$cid\"><IMG SRC=images/global/edit.gif  BORDER=0 ALT=Edit></A>  &nbsp;";
				
				echo "<A HREF=\"javascript: if(confirm('Delete assignment $weight?')) window.open('index.php?mod=Courses&amp;file=admin&amp;op=assignment&amp;action=delete_assignment&amp;cid=$cid&amp;aid=$aid','_self')\"><IMG SRC=images/global/delete.gif  BORDER=0 ALT=Delete></A>";	
				
				echo "</td></tr></table><br>";
				$question=lnShowContent($question,$url);
				echo  nl2br(stripslashes($question));
				echo "<BR></td></tr>";
				echo '<tr height=10><td bgcolor="#FFFFFF"></td></tr>';
				echo '<tr height=1><td bgcolor="#FFFFFF" background="images/line.gif"></td></tr>';
			}		
		}
	}

	echo '</table>';

	if ($action != "insert_assignment") {
		echo '<CENTER><FORM METHOD=POST ACTION="index.php">'
		.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
		.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
		.'<INPUT TYPE="hidden" NAME="op" VALUE="assignment">'
		.'<INPUT TYPE="hidden" NAME="action" VALUE="insert_assignment">'
		.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
		.'<INPUT TYPE="hidden" NAME="lid" VALUE="'.$lid.'">';
		if ($numrows <= 0) {
			echo '<CENTER><BR><BR>'._NOASSIGNMENT.'</CENTER>';
		}
		// if has lesson show add assignment button
		if (hasLesson($cid)) {
			if ($numrows < $lessonrows) {
				 if ($action != "edit_assignment") {
					echo '<CENTER><BR><BR><INPUT class="button_org" TYPE="SUBMIT" VALUE="'._ADDASSIGNMENT.'"></CENTER>';
				 }
			}
		}
		else {
			echo '<CENTER><BR><BR>'._CREATELESSON.'</CENTER>';
		}
	}

	echo '</td></tr></table>';

}


/*
*	assignment form
*/
function addAssignmentForm($action,$cid,$lid,$weight,$aid) {

	echo '<FORM NAME="assignmentform"METHOD=POST ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="assignment">'
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">';

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$lessonstable = $lntable['lessons'];
	$lessonscolumn = &$lntable['lessons_column'];
	$assignmentstable = $lntable['assignments'];
	$assignmentscolumn = &$lntable['assignments_column'];

	if ($action == "insert_assignment") {
		$weight += 0.5;
	     echo '<INPUT TYPE="hidden" NAME="action" VALUE="add_assignment">';
	}
	else if ($action == "edit_assignment") {
		$result = $dbconn->Execute("SELECT * FROM $assignmentstable WHERE $assignmentscolumn[aid]='". lnVarPrepForStore($aid) ."'");
		list($aid,$lid,$title,$question) = $result->fields;
		echo '<INPUT TYPE="hidden" NAME="action" VALUE="update_assignment">'
		.'<INPUT TYPE="hidden" NAME="aid" VALUE="'.$aid.'">';
	}

	echo '<table cellpadding=3 cellspacing=0 width=100% BGCOLOR="#EEF3A7">';

	// list lessons
	$query = "SELECT $lessonscolumn[lid],$lessonscolumn[weight],$lessonscolumn[title],$assignmentscolumn[title]
					FROM   $lessonstable LEFT JOIN $assignmentstable ON $assignmentscolumn[lid]=$lessonscolumn[lid]
					WHERE  $lessonscolumn[cid] =  '" . lnVarPrepForStore($cid) . "'"; 

	if ($action == "edit_assignment") { 
		$query .= " AND $assignmentscolumn[aid]=$aid";	
	}
	else {
		$query .= " AND $assignmentscolumn[lid] is NULL";	
	}
	
	$query .= " ORDER BY $lessonscolumn[weight] ";
	
	$result = $dbconn->Execute($query);	

	 list($lid,$weight,$lesson_title,$assignment_title) = $result->fields;
	
	 $numrows = $result->PO_RecordCount();
	if ($numrows > 0 || $action == "edit_assignment") {
		if ($action == "edit_assignment") {
			echo '<input type="hidden" name="lid" value="'.$lid.'">';
		}
		else {
			echo '<tr><td width=100 align=right>'._LESSON.':</td>';
			echo '<td><select name="lid">';
			for($i=0; list($lid,$weight,$lesson_title,$assignment_title) = $result->fields; $i++) {
				$result->MoveNext();
				echo '<option value="'.$lid.'">'._LESSONNO.' '.$weight.' '.$lesson_title.'</option>';
			}
			echo '</select></td></tr>';
		}

		echo '<tr><td width=100 align=right>'._ASSTITLE.':</td>';
		echo '<td><INPUT TYPE="text" NAME="ass_title" WIDTH="30" style="width: 90%;" VALUE="'.lnVarPrepForDisplay($assignment_title).'"></td></tr>';

		echo '<tr><td valign=top align=right>'._QUESTION.':</td>';
		echo '<td><TEXTAREA NAME="ass_question" ROWS="5" COLS="30" wrap="soft" style="width: 90%;">'.lnVarPrepForDisplay($question).'</TEXTAREA>';
		
		echo "<BR><INPUT class=button_white TYPE=button VALUE=\" ... \" onclick=\"javascript:popup('index.php?mod=spaw&amp;type=Assignment&amp;aid=$aid&amp;cid=$cid','_blank',750,480)\">";
		echo '</td></tr>';

		if ($action == "edit_assignment") {
			 echo '<tr><td>&nbsp;</td><td><INPUT class=button_org TYPE="submit" VALUE="'._UPDATEASSIGNMENT.'">';
		}
		else {
			 echo '<tr><td>&nbsp;</td><td><INPUT class=button_org TYPE="submit" VALUE="'._ADDASSIGNMENT.'">';
		}
		echo " <INPUT class=button_org TYPE=button VALUE=Cancel OnClick=\"javascript:window.open('index.php?mod=Courses&amp;file=admin&amp;op=assignment&amp;cid=$cid','_self')\"></td></tr>";
	}
	else {
		 echo '<tr><td colspan="2" align="center">'._CANNOTCREATEASS.'</td></tr>';
		echo '<tr height=1><td colspan="2" bgcolor="#FFFFFF" background="images/line.gif"></td></tr>';

	}
	
	echo '</table></FORM>';
}


/*
* add assignment
*/
function addAssignment($vars) {
	// Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$assignmentstable = $lntable['assignments'];
	$assignmentscolumn = &$lntable['assignments_column'];

//	list($lid,$ass_weight) = explode('-',$lesson);

	$aid =  getNextAid();
	$question=stripslashes($question);
	$query = "INSERT INTO $assignmentstable
				  (	$assignmentscolumn[aid],
					$assignmentscolumn[lid],
					$assignmentscolumn[title],
					$assignmentscolumn[question]
					  )
					VALUES ('$aid',
						  '" . lnVarPrepForStore($lid) . "',
						  '" . lnVarPrepForStore($ass_title) . "',
						  '" . lnVarPrepForStore($ass_question) . "')";

	$dbconn->Execute($query);

	 if ($dbconn->ErrorNo() != 0) {
        return false;
    } 
//	else {
//		resequenceAssignments($lid);
//		return true;
//	}
}


/*
* get next Aid
*/
function  getNextAid() {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$assignmentstable = $lntable['assignments'];
	$assignmentscolumn = &$lntable['assignments_column'];
	$query = "SELECT MAX($assignmentscolumn[aid]) FROM $assignmentstable"; 

	$result = $dbconn->Execute($query);

	list($maxaid) =  $result->fields;

	return $maxaid + 1;
}


/*
*	delete assignment
*/
function deleteAssignment($vars) {
 // Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$assignmentstable = $lntable['assignments'];
	$assignmentscolumn = &$lntable['assignments_column'];
	
	$query = "DELETE FROM $assignmentstable 
				  WHERE $assignmentscolumn[aid] = '". lnVarPrepForStore($aid) . "'";
	
	$dbconn->Execute($query);
	
	 if ($dbconn->ErrorNo() != 0) {
        return false;
    } 
//	else {
//		resequenceAssignments($lid);
//		return true;
//	}
}


/*
* update assignment
*/
function updateAssignment($vars) {
	// Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$assignmentstable = $lntable['assignments'];
	$assignmentscolumn = &$lntable['assignments_column'];

//	list($lid,$ass_weight) = explode('-',$lesson);
	$ass_question = stripslashes($ass_question);
	$ass_question = addslashes($ass_question);
	$query = "UPDATE $assignmentstable SET
					$assignmentscolumn[lid] = '$lid',
					$assignmentscolumn[title] =  '" . lnVarPrepForStore($ass_title) . "',
					$assignmentscolumn[question] =   '" . $ass_question . "' 
					WHERE $assignmentscolumn[aid] = '" . lnVarPrepForStore($aid) . "'";

	$dbconn->Execute($query);
	
	 if ($dbconn->ErrorNo() != 0) {
        return false;
    } 
//	else {
//		resequenceAssignments($lid);
//		return true;
//	}
}


/*
* resequence assignment
*/
/*
function resequenceAssignments($lid) {

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
    $assignmentstable = $lntable['assignments'];
    $assignmentscolumn = &$lntable['assignments_column'];
	
    // Get the information
    $query = "SELECT $assignmentscolumn[aid],
                     $assignmentscolumn[weight]
					 FROM $assignmentstable 
					 WHERE $assignmentscolumn[lid]= '". lnVarPrepForStore($lid)."'
               ORDER BY $assignmentscolumn[weight]";
    $result = $dbconn->Execute($query);

    // Fix sequence numbers
    $seq=1;
    while(list($aid, $curseq) = $result->fields) {

        $result->MoveNext();
        if ($curseq != $seq) {
            $query = "UPDATE $assignmentstable
                      SET $assignmentscolumn[weight]='" . lnVarPrepForStore($seq) . "'
                      WHERE $assignmentscolumn[aid]='" . lnVarPrepForStore($aid)."'";
            $dbconn->Execute($query);
        }
        $seq++;
    }
    $result->Close();

    return true;
}
*/

/*
* move down assignment item
*/
/*
function increaseAssignmentWeight($vars) {
   // Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $assignmentstable = $lntable['assignments'];
    $assignmentscolumn = &$lntable['assignments_column'];

    $seq = $weight;

	// Get info on displaced block
    $sql = "SELECT $assignmentscolumn[aid],
                   $assignmentscolumn[weight]
            FROM $assignmentstable
            WHERE $assignmentscolumn[weight] >'" . lnVarPrepForStore($seq) . "'
            AND   $assignmentscolumn[lid]='" . lnVarPrepForStore($lid) . "'
            ORDER BY $assignmentscolumn[weight] ASC";
    $result = $dbconn->SelectLimit($sql, 1);
   
	if ($result->EOF) {
        return false;
    }
    list($altaid, $altseq) = $result->fields;
    $result->Close();
	
    // Swap sequence numbers
    $sql = "UPDATE $assignmentstable
            SET $assignmentscolumn[weight]=$seq
            WHERE $assignmentscolumn[aid]='".lnVarPrepForStore($altaid)."'";
    $dbconn->Execute($sql);
    
	$sql = "UPDATE $assignmentstable
            SET $assignmentscolumn[weight]=$altseq
            WHERE $assignmentscolumn[aid]='".lnVarPrepForStore($aid)."'";
    $dbconn->Execute($sql);

	resequenceAssignments($lid);

    return true;
}
*/
/*
* move up assignment item
*/
/*
function decreaseAssignmentWeight($vars) {
   // Get arguments from argument array
    extract($vars);

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

    $assignmentstable = $lntable['assignments'];
    $assignmentscolumn = &$lntable['assignments_column'];

    $seq = $weight;

	// Get info on displaced block
    $sql = "SELECT $assignmentscolumn[aid],
                   $assignmentscolumn[weight]
            FROM $assignmentstable
            WHERE $assignmentscolumn[weight] < '" . lnVarPrepForStore($seq) . "'
            AND   $assignmentscolumn[lid]='" . lnVarPrepForStore($lid) . "'
            ORDER BY $assignmentscolumn[weight] DESC";
    $result = $dbconn->SelectLimit($sql, 1);
   
	if ($result->EOF) {
        return false;
    }
    list($altaid, $altseq) = $result->fields;
    $result->Close();
	
    // Swap sequence numbers
    $sql = "UPDATE $assignmentstable
            SET $assignmentscolumn[weight]=$seq
            WHERE $assignmentscolumn[aid]='".lnVarPrepForStore($altaid)."'";
    $dbconn->Execute($sql);
    
	$sql = "UPDATE $assignmentstable
            SET $assignmentscolumn[weight]=$altseq
            WHERE $assignmentscolumn[aid]='".lnVarPrepForStore($aid)."'";
    $dbconn->Execute($sql);

	resequenceAssignments($lid);

    return true;
}
*/
?>