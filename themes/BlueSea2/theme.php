<?php
/************************************************************/
/* Function themeheader()													 */
/************************************************************/
function themeheader() {
	global $index,$expand;
	$thistheme = lnConfigGetVar('Default_Theme');
	
	echo '<div class="wrapper_header">';
	echo '<div class="container_24">';
	echo '<div class="grid_24">';
	echo '<div class="grid_10">';
	echo '<image src="themes/'.$thistheme.'/images/logo_blue.png">';
	echo '</div>';
	echo '<div class="clear"></div>';
	echo '<div class="mainmenu">';
	mainmenu();
	echo '</div>';
	echo '</div>';
	echo '<div class="clear"></div>';
		
	echo '<div class="grid_5">';
	if ($GLOBALS['expand']!=1) { 
		blocks('left');
	}
	
	echo '</div>';
	if ($GLOBALS['index'] == 1) {
		echo '<div class="grid_14">';
		blocks('center');
	}else{
		echo '<div class="grid_19">';
		echo '<div class="content-main">';
	}
}

/************************************************************/
/* Function themefooter()*/
/************************************************************/
function themefooter() {
$thistheme = lnConfigGetVar('Default_Theme');
	echo '</div>';
	
	if ($GLOBALS['index'] == 1) {
		echo '<div class="grid_5">'; 
		blocks('right');
		echo '</div>';
	}else{
		echo '</div>';
	}
		
	echo '<div class="clear"></div>';
	
	echo '<div class="grid_24">';
	echo '<div class="footer">';
	footmsg();
	$filename = "version.txt";
	$handle = fopen($filename, "r");
	$contents = fread($handle, filesize($filename));
	fclose($handle); 
	echo 'Version: '.$contents;
	echo '</div>';
	echo '</div>';
	echo '</div>'; // end container_24 second
	echo '</div>'; // end wrapper_footer

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