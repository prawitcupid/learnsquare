<?php
global $PHP_SELF;
if (preg_match("/header.php/i", $PHP_SELF)) {
    die ("You can't access this file directly...");
}

function head() {
	
	$thistheme = lnConfigGetVar('Default_Theme');
	
	lnThemeLoad($thistheme);

	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
    echo "<html>\n<head>\n";
    echo '<meta http-equiv="X-UA-Compatible" content="IE=9, IE=8, IE=7.5, IE=7">'; 
	echo '<title>'.lnConfigGetVar('sitename').' :: '.lnConfigGetVar('slogan')."</title>\n";
	if (defined("_CHARSET1") && trim(_CHARSET1) != "") {
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset="._CHARSET1."\">\n";
	}
	if (defined("_CHARSET2") && trim(_CHARSET2) != "") {
		echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset="._CHARSET2."\">\n";
	}
    //echo '<meta name="KEYWORDS" content="'.lnConfigGetVar('metakeywords')."\">\n";
    echo '<meta name="KEYWORDS" content="'.lnConfigGetVar('sitename')."\">\n";
	echo '<meta name="DESCRIPTION" content="'.lnConfigGetVar('slogan')."\">\n";
    echo "<meta name=\"ROBOTS\" content=\"INDEX,FOLLOW\">\n";
    echo "<meta name=\"resource-type\" content=\"document\">\n";
    echo "<meta http-equiv=\"expires\" content=\"0\">\n";
    echo '<meta name="author" content="'.lnConfigGetVar('sitename')."\">\n";
    echo '<meta name="copyright" content="Copyright (c) 2004 by '.lnConfigGetVar('sitename')."\">\n";
    echo "<meta name=\"revisit-after\" content=\"1 days\">\n";
    echo "<meta name=\"distribution\" content=\"Global\">\n";
    echo '<meta name="generator" content="Learnsquare '._LN_VERSION_NUM."\">\n";
    echo "<meta name=\"rating\" content=\"General\">\n";
    echo '<link rel="shortcut icon" href="favicon.ico" >';
	echo "<link rel=\"StyleSheet\" href=\"themes/".$thistheme."/style/style.css\" type=\"text/css\">\n";
	
	if(lnConfigGetVar('MiningStatus')){
		echo '<script language="javascript" src="xetrack.js" type="text/javascript"></script>';
	}
	
	echo "<script language=\"JavaScript\" src=\"javascript/JSCookMenu.js\" type=\"text/javascript\"></script>\n";
	echo "<script language=\"JavaScript\" src=\"javascript/prototype.js\" type=\"text/javascript\"></script>\n";
	echo "<link rel=\"stylesheet\" href=\"javascript/ThemeOffice/theme.css\" type=\"text/css\">\n";
	echo "<script language=\"JavaScript\" src=\"javascript/ThemeOffice/theme.js\" type=\"text/javascript\"></script>\n";
	echo "<script language=\"JavaScript\" src=\"javascript/treemenu.js\"></script>\n";
	
	echo "<script language=\"JavaScript\" src=\"javascript/popup.js\"></script>\n";
	echo "<script language=\"JavaScript\" src=\"javascript/plugins.js\"></script>\n";
	// Javascripts for groups select box 
	if ($GLOBALS['mod']=="User" && $GLOBALS['file']=="useredit" && $GLOBALS['op']=="edituser") {
			echo "<script language=\"JavaScript\" src=\"javascript/OptionTransfer.js\"></SCRIPT>\n";
			echo '
			<SCRIPT language=JavaScript>
			var opt = new OptionTransfer("list1","list2");
			opt.setAutoSort(true);
			opt.setDelimiter(",");
			opt.saveRemovedLeftOptions("removedLeft");
			opt.saveRemovedRightOptions("removedRight");
			opt.saveAddedLeftOptions("addedLeft");
			opt.saveAddedRightOptions("addedRight");
			opt.saveNewLeftOptions("newLeft");
			opt.saveNewRightOptions("newRight");
			</SCRIPT>';
	}
	else if ($GLOBALS['mod']=="User" && $GLOBALS['file']=="useradd") {
			echo "<script language=\"JavaScript\" src=\"javascript/OptionTransfer.js\"></SCRIPT>\n";
			echo '
			<SCRIPT language=JavaScript>
			var opt = new OptionTransfer("list1","list2");
			opt.setAutoSort(true);
			opt.setDelimiter(",");
			opt.saveRemovedLeftOptions("removedLeft");
			opt.saveRemovedRightOptions("removedRight");
			opt.saveAddedLeftOptions("addedLeft");
			opt.saveAddedRightOptions("addedRight");
			opt.saveNewLeftOptions("newLeft");
			opt.saveNewRightOptions("newRight");
			</SCRIPT>';	
	}
	else if ($GLOBALS['mod']=="User" && $GLOBALS['file']=="useraddfile") {
			echo "<script language=\"JavaScript\" src=\"javascript/OptionTransfer.js\"></SCRIPT>\n";
			echo '
			<SCRIPT language=JavaScript>
			var opt2 = new OptionTransfer("list1","list2");
			opt2.setAutoSort(true);
			opt2.setDelimiter(",");
			opt2.saveRemovedLeftOptions("removedLeft2");
			opt2.saveRemovedRightOptions("removedRight2");
			opt2.saveAddedLeftOptions("addedLeft2");
			opt2.saveAddedRightOptions("addedRight2");
			opt2.saveNewLeftOptions("newLeft2");
			opt2.saveNewRightOptions("newRight2");
			</SCRIPT>';		
	}

   echo "\n</head>\n\n";

// body start	
    echo "<body topmargin=0 leftmargin=0 marginheight=0 marginwidth=0 ";
	// for groups select box
	if ($GLOBALS['mod']=="User" && $GLOBALS['file']=="useredit" && $GLOBALS['op']=="edituser") {
			echo " onLoad=\"opt.init(document.forms['Register']);\"";
	}
	else if ($GLOBALS['mod']=="User"  && $GLOBALS['file'] =="useradd") {
			echo " onLoad=\"opt.init(document.forms['Register']);\"";
	}
	else if ($GLOBALS['mod']=="User"  && $GLOBALS['file'] =="useraddfile") {
			echo " onLoad=\"opt2.init(document.forms['Register2'])\"";
	}
	
	echo ">";
// body end

	themeheader();
}


head();

?>
