<?php
/************************************************************/
/* Function themeheader()													 */
/************************************************************/
function themeheader() {
	global $index,$expand;
	$thistheme = lnConfigGetVar('Default_Theme');
	
	echo '<div class="container clearfix">';
	echo '<div class="grid_12">'; // start flash
	echo '<object width="780" height="227"';
	echo 'classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" ';
	echo 'codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0">';
	echo '<param name="SRC" value="51.swf">';
	echo '<embed src="themes/pea/images/51.swf" width="780" height="227"></embed>';
	echo '</object>';
	echo '</div>'; // end flash
	
	echo '<div class="grid_12">'; // start menu
	echo '<div class="mainmenu">';
	mainmenu();
	echo '</div>';
	echo '</div>'; // end menu
		
	echo '<div class="grid_3">'; // start side left
	if ($GLOBALS['expand']!=1) { 
		blocks('left');
	}
	echo '</div>'; //end side left
	
	if ($GLOBALS['index'] == 1) {
		echo '<div class="grid_6">'; // start content
		blocks('center');
	} elseif ($GLOBALS['expand'] == 1 && $GLOBALS['index'] != 1) {
		echo '<div class="grid_12">';
		echo '<div class="content-main">';
	} else {
		echo '<div class="grid_9">';
		echo '<div class="content-main">';
	}
}

/************************************************************/
/* Function themefooter()*/
/************************************************************/
function themefooter() {
$thistheme = lnConfigGetVar('Default_Theme');
	echo '</div>'; // end content
	
	if ($GLOBALS['index'] == 1) {
		echo '<div class="grid_3">'; // start side right
		blocks('right');
		echo '</div>';  // end side right
	}else{
		echo '</div>';
	}
	
	echo '<div class="grid_12">';
	echo '<div class="footer">';
	footmsg();
	$filename = "version.txt";
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle); 
	echo 'Version: '.$contents;
	echo '</div>'; // end footer
	echo '</div>'; // end grid footer
	
	echo '</div>'; // end container

} //*** end Function themefooter()

/************************************************************/
/* Function themesidebox()												*/
/************************************************************/
function themesidebox($block) {
	$thistheme = lnConfigGetVar('Default_Theme');
	$title = $block['title'];
	$content = $block['content'];
	$module = strtolower($block['bkey']);

	if($block['position'] == 'c') {  // center block
		echo '<div class="content-main">';
		echo $content;
		echo '</div>';
	}
	
	if($block['position'] == 'l') {
		echo '<div class="content-side">';
		echo '<h4>'.$title.'</h4>';
		echo '<div>'.$content.'</div>';
		echo '</div>';
	} elseif ($block['position'] == 'r') {
		echo '<div class="content-side">';
		echo '<h4>'.$title.'</h4>';
		echo '<div>'.$content.'</div>';
		echo '</div>';
	}
}

/************************************************************/
/* Function opentable()														*/
/************************************************************/
function OpenTable() {
}

function CloseTable() {
}

function OpenTable2() {
}

function CloseTable2() {
}

?>