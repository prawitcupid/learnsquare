<?php
/**
* Show message from file: about us, helpdesk 
*/
if (!defined("LOADED_AS_MODULE")) {
    die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Message::', "$op::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */
include 'header.php';

OpenTable();

$file= $op;//$GLOBALS[op];

$defaultlang = lnConfigGetVar('language');
if (empty($defaultlang)) {
	$defaultlang = 'tha';
}

$currentlang = lnUserGetLang();
if (file_exists("modules/Message/language/$currentlang/$file.html")) {
	include "modules/Message/language/" . lnVarPrepForOS($currentlang) . "/$file.html";
} 
elseif (file_exists("modules/Message/language/$defaultlang/$file.html")) {
	include "modules/Message/language/" . lnVarPrepForOS($defaultlang) . "/$file.html";
}

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */
?>