<?php
/**
* Schools module
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

define('IMG_UPLOAD_DIR','modules/Schools/images'); //default image upload location, if upload page is not specified

/* options */
if ($op) {
	$vars= array_merge($_GET,$_POST);	
	switch ($op) {
		case "addschool" : 
				if (!lnSecAuthAction(0, 'Schools::', "::", ACCESS_ADD)) {
						echo "<CENTER><h1>"._NOAUTHORIZED." to ADD".$mod." module!</h1></CENTER>";
						return false;
				}
				addSchool($vars); break;
		case "deleteschool" : 
				if (!lnSecAuthAction(0, 'Schools::', "::", ACCESS_DELETE)) {
						echo "<CENTER><h1>"._NOAUTHORIZED." to ADD".$mod." module!</h1></CENTER>";
						return false;
				}
				deleteSchool($vars); break;
		case "updateschool" : 
				if (!lnSecAuthAction(0, 'Schools::', "::", ACCESS_EDIT)) {
						echo "<CENTER><h1>"._NOAUTHORIZED." to EDIT".$mod." module!</h1></CENTER>";
						return false;
				}
				updateSchool($vars); break;
	}
}

/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_SCHOOLADMIN);
$links=array('index.php?mod=Admin','index.php?mod=Schools&file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

 echo  '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Schools&file=admin"><B>'._SCHOOLADMIN.'</B></A><BR>&nbsp;';
	
 list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$school = $lntable['schools'];
$column = &$lntable['schools_column'];
	
if ($op == "editschool") {
	$query = "SELECT * FROM $school WHERE $column[sid]='$id'";
	$result = $dbconn->Execute($query);
	list($sid,$schoolcode,$schoolname,$schooldesc,$logoimage) = $result->fields;
	$schoolcode = stripslashes($schoolcode);
	$schoolname = stripslashes($schoolname);
	$schooldesc = stripslashes($schooldesc);
}

// SHOW ADD SCHOOL FORM
if ($op == "editschool") {
	$img=$logoimage.".gif";
	$submit = _SAVECHANGES;
}
else {
	$img="blank.gif";
	$submit = _SUBMIT;
}

?>
<script language="javascript">

function preview(){
        if (document.forms[0].logofile.value){
                document.previewPict.src = document.forms[0].logofile.value;
       }
}
setInterval("preview()",100);
</script>
<?

echo '<center><TABLE WIDTH="400" CELLPADING="0" CELLSPACING="0">'
.'<FORM METHOD=POST ACTION="index.php"   enctype="multipart/form-data">'
.'<INPUT TYPE="hidden" NAME="MAX_FILE_SIZE" VALUE="'.MAX_FILESIZE.'">'
.'<INPUT TYPE="hidden" NAME="mod" VALUE="Schools">'
.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">';
if ($op == "editschool") {
	echo '<INPUT TYPE="hidden" NAME="op" VALUE="updateschool">';
	echo '<INPUT TYPE="hidden" NAME="sid" VALUE="'.$id.'">';
}
else {
	echo '<INPUT TYPE="hidden" NAME="op" VALUE="addschool">';
}

echo '<TR><TD WIDTH="70">'._SCHOOLCODE.'</TD><TD><INPUT TYPE="text" NAME="school_code" SIZE="2"  VALUE="'.$schoolcode.'"></TD></TR>'
.'<TR><TD WIDTH="70">'._SCHOOLNAME.'</TD><TD><INPUT TYPE="text" NAME="school_name" SIZE="20" VALUE="'.$schoolname.'"></TD></TR>'
.'<TR><TD WIDTH="70" VALIGN="TOP">'._SCHOOLDESC.'</TD><TD><TEXTAREA NAME="school_desc" ROWS="5" COLS="30">'.$schooldesc.'</TEXTAREA></TD></TR>'
.'<TR HEIGHT=50 WIDTH="70"><TD>'._SCHOOLLOGO.'</TD><TD>'
.'<INPUT TYPE="file" NAME="logofile"><BR>'
.'<img name="previewPict" src="'.IMG_UPLOAD_DIR.'/'.$img.'" border=0></TD></TR>'
.'<TR><TD WIDTH="70">&nbsp;<TD><INPUT  class="button_org" TYPE="submit" VALUE="'. $submit. '"></TD></TR>'
.'</FORM>';
echo '</TABLE><BR><BR>';


// SHOW SCHOOL LIST
    $query = "SELECT *
              FROM $school ORDER BY $column[code]";

	$result = $dbconn->Execute($query);

	echo '<table class="list" width="450" cellpadding="3" cellspacing="1" border="0">';
	echo '<tr><td colspan=3 class="head">'._SCHOOLLIST.'</td></tr>';
	for ($i=1; list($sid,$code,$name,$description,$logo) = $result->fields; $i++) {
		$result->MoveNext();
		$code = stripslashes($code);
		$name = stripslashes($name);
		$description = stripslashes($description);
		$logo_school = lnBlockImage('Schools',$logo);

		echo "<tr valign=top bgcolor=#ffffff><td align=center>$logo_school</td><td> <B>($code) $name  </B><BR>$description</td>";
		echo "<td  align=center>";
		if (lnSecAuthAction(0, 'Schools::', "::", ACCESS_EDIT)) {
			echo '<A HREF="index.php?mod=Schools&amp;file=admin&amp;op=editschool&amp;id='.$sid.'"><IMG SRC="images/global/edit.gif" WIDTH="14" HEIGHT="16" BORDER="0" ALT="'._EDIT.'"></A>';
		}
		echo '&nbsp;';
		if (lnSecAuthAction(0, 'Schools::', "::", ACCESS_DELETE)) {
			echo "<A HREF=\"javascript:if(confirm('Delete?')) window.open('index.php?mod=Schools&amp;file=admin&amp;op=deleteschool&amp;sid=$sid','_self')\"><IMG SRC=\"images/global/delete.gif\" WIDTH=\"14\" HEIGHT=\"16\" BORDER=\"0\" ALT=\""._DELETE."\"></A>";
		}
		echo "</td></tr>";
	}
	echo '</table><BR>&nbsp;</center>';

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */

/**
*	function add new school
*/
function addSchool($var) {
   // Get arguments from argument array
    extract($var);
	
	// upload images to server
	$IMG_ACCEPT =  array("image/gif","image/pjpeg","image/jpg","image/x-png","image/jpeg"); //acceptable types
	list($logofilename,$type) = explode('.',$_FILES['logofile']['name']);
	if (!empty($logofilename)) {
		
		$accept_type = 0;
        foreach ($IMG_ACCEPT as $type) {
                if ($_FILES['logofile']['type'] == $type){
                        $accept_type = 1;
                        break;
                }
        }

        if ($accept_type){
                if (!@copy($_FILES['logofile']['tmp_name'], IMG_UPLOAD_DIR. "/" . $_FILES['logofile']['name'])){
                        $errors =  "Cannot upload " . $_FILES['logofile']['name'];
                }
				unlink($_FILES['logofile']['tmp_name']);
        }else{
                $errors = "Wrong file type";
        }
	
	}

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	$schoolstable = $lntable['schools'];
    $column = &$lntable['schools_column'];
	$query = "INSERT INTO $schoolstable
				  ($column[code],
				   $column[name],
				   $column[description],
				   $column[logo])
					VALUES (
						  '" . lnVarPrepForStore($school_code) . "',
						  '" . lnVarPrepForStore($school_name) . "',
						  '" . lnVarPrepForStore($school_desc) . "',
						  '" . lnVarPrepForStore($logofilename) . "')";

	$dbconn->Execute($query);
	
	 if ($dbconn->ErrorNo() != 0) {
        return false;
    } 
	else {
		return true;
	}
}


/**
*	function delete school
*/
function deleteSchool($var) {
   // Get arguments from argument array
    extract($var);
	
	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
	$schoolstable = $lntable['schools'];
    $column = &$lntable['schools_column'];

	$result = $dbconn->Execute("SELECT $column[logo] FROM $schoolstable  WHERE $column[sid] = '". lnVarPrepForStore($sid) . "'");
	list($logofile) = $result->fields;
	unlink(IMG_UPLOAD_DIR . "/" .$logofile);

	$query = "DELETE FROM $schoolstable 
				  WHERE $column[sid] = '". lnVarPrepForStore($sid) . "'";
	
	$dbconn->Execute($query);
	
	 if ($dbconn->ErrorNo() != 0) {
        return false;
    } 
	else {
		return true;
	}
}

/**
*	function modify school
*/
function updateSchool($var) {
   // Get arguments from argument array
    extract($var);
	
	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();
	$schoolstable = $lntable['schools'];
    $column = &$lntable['schools_column'];

	// upload images to server
	//$IMG_ACCEPT =  array("image/gif","image/pjpeg","image/jpg","image/x-png","image/jpeg"); //acceptable types
	//$logofilename = $_FILES['logofile']['name'];

	$IMG_ACCEPT =  array("image/gif","image/pjpeg","image/jpg","image/x-png","image/jpeg"); //acceptable types  // แก้เอา 2 บรรทัดนี้มาใส่แทนที่ comment ไว้
	list($logofilename,$type) = explode('.',$_FILES['logofile']['name']);

	
	if (!empty($logofilename)) {
		
		$accept_type = 0;
        foreach ($IMG_ACCEPT as $type) {
                if ($_FILES['logofile']['type'] == $type){
                        $accept_type = 1;
                        break;
                }
        }

        if ($accept_type){
                if (!@copy($_FILES['logofile']['tmp_name'], IMG_UPLOAD_DIR. "/" . $_FILES['logofile']['name'])){
                        $errors =  "Cannot upload " . $_FILES['logofile']['name'];
                }
				unlink($_FILES['logofile']['tmp_name']);
				$result = $dbconn->Execute("SELECT $column[logo] FROM $schoolstable  WHERE $column[sid] = '". lnVarPrepForStore($sid) . "'");
				list($logofile) = $result->fields;
				unlink(IMG_UPLOAD_DIR . "/" .$logofile);
		}else{
                $errors = "Wrong file type";
        }
	
	}

	
	$query = "UPDATE $schoolstable SET
				   $column[code] = '" . lnVarPrepForStore($school_code) . "',
				   $column[name] = '" . lnVarPrepForStore($school_name) . "',
				   $column[description] = '" . lnVarPrepForStore($school_desc) . "'";
	if (!empty($logofilename)) {
		$query .= ", $column[logo] = '" . lnVarPrepForStore($logofilename) . "'";
	}

	$query .=  " WHERE $column[sid] = '". lnVarPrepForStore($sid) . "'";


	$dbconn->Execute($query);
	
	 if ($dbconn->ErrorNo() != 0) {
        return false;
    } 
	else {
		return true;
	}
}
?>