<?php
include 'header.php';

OpenTable();

if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'User::Profile', "::", ACCESS_COMMENT)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

//echo lnBlockTitle($mod);
echo '<p class="header"><b>'._USER_TITLE.'</b></p>';
echo '<BR>'._PERSONALINFO;

//Upload Image Page
//---Programmer Narasak Tai 10/09/2007----

echo _RULEUPLOAD;
echo '<form id="frmUpload" action="index.php?mod=User&file=upload" 
	method="post" enctype="multipart/form-data">'._SELECTPIC.'<br>';
echo '<input id="fileupload" name="fileupload" type="file" >&nbsp;';
echo '<input id="btnUpload" type="submit" value="'._UPLOAD.'">';
echo '</form>';

CloseTable();

include 'footer.php';

?>