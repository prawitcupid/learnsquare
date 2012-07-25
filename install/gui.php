<?php
/*
 function print_header()
 {
 $bn_num = mt_rand (1, 5);
 echo "
 <!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">
 <html><head><title>" . _INSTALLATION . "</title>
 <META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=" . _CHARSET . "\">
 <META NAME=\"AUTHOR\" CONTENT=\"PostNuke Crew\">
 <META NAME=\"GENERATOR\" CONTENT=\"PostNuke -- http://www.postnuke.com\">
 <link rel=\"StyleSheet\" href=\"install/style/styleNN.css\" type=\"text/css\">
 <style type=\"text/css\">@import url(\"install/style/style.css\");</style>
 </head><body leftmargin=\"0\" rightmargin=\"0\" topmargin=\"0\" bottommargin=\"0\"><table width=\"100%\" height=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td valign=\"top\"><table cellspacing=\"0\" cellpadding=\"0\" width=\"100%\" bgcolor=\"#264CB7\" background=\"install/style/bg.gif\"><tr><td align=\"left\"><img src='install/style/top1.jpg'  alt='' border='0' align='middle'></td><td align=\"right\"></td></tr><tr bgcolor=\"#000000\" height=\"3\"><td colspan=\"2\"></td></tr></table><br>
 <table bgcolor=\"#000000\" cellspacing=\"0\" align=center><tr bgcolor=\"#ffffff\"><td><img src='install/banners/banner.$bn_num.jpg' width='468' height='60' alt='' border='0' align='middle'></td></tr></table><br>
 <table width=\"80%\" align=\"center\"><tr><td>";
 }
 */
function print_header()
{
	echo '      <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
				<html><head>
				<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
				<title>' . _INSTALLATION . '</title>
				<META NAME="GENERATOR" CONTENT="LearnSquare">
				<link rel="StyleSheet" href="install/style/styleNN.css" type="text/css">
				<style type="text/css">@import url("install/style/style.css");</style>
				
				<script language="JavaScript" src="javascript/jquery.min.js" type="text/javascript"></script>
				<script language="JavaScript" src="javascript/jquery.blockUI.js" type="text/javascript"></script>
                
				</head>
				<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
				<table width="100%" height="100%" cellspacing="0" cellpadding="0"><tr><td valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" align="center"  background="install/style/bgg.gif">
					<tr>
				   <td><div align="center"><img name="index_r1_c1" src="install/style/lnlogo.gif" width="400" height="120" border="0"></div></td>
				   </tr>
					<tr bgcolor="#FFFFFF" height="3"><td></td></tr>
					</table>
					
					<table bgcolor="#000000" cellspacing="0" align=center><tr bgcolor="#ffffff"><td><img src="install/style/banner.5.jpg" width="468" height="60" border="0" align="middle"></td></tr></table>

					<table width="80%" align="center" border="0"><tr><td>
	';
	
	echo "<script language='JavaScript'>$(document).ready(function() {
    $('#submit').click(function() { 
        $.blockUI({
           message: $('#pleaseWait'),
           css: {
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#000', 
            '-webkit-border-radius': '10px', 
            '-moz-border-radius': '10px', 
            opacity: .5, 
            color: '#fff'
           } 
        }); 
        
 
        setTimeout($.unblockUI, 60000); 
    }); 
    });</script>";
	
	echo '<div id="pleaseWait" style="display: none;">
	<img src="images/ajax-loader.gif" /?<br><h1>'._WAIT.'<h1>
    </div>';
    
}

function print_footer()
{
	echo '
		</td></tr></table>
	</td></tr>
	<tr><td valign=bottom>
		
		<table width="100%" cellpadding=0 cellspacing=0 border=0>
		<tr>
		<td align="center" width="100%"  height="90"  background="install/style/bgg2.gif"><img src="install/style/lnfooter2.gif" width="400" height="90" /></td>

		</tr>		
		<tr bgcolor="#FFFFFF">
		<td align="center" width="100%">
		<FONT SIZE="1" COLOR="#000000">' . _FOOTER_1 . '</FONT>
		</td>
		</tr>

		</table>
		
		
		
		
	</td></tr>
	</table>
	</body></html>';
}

function print_select_language()
{
	progress(10);
	echo "<br><center>
<font class=\"pn-title\">" . _SELECT_LANGUAGE_1 . "</font></center>
<form action=\"install.php\" method=\"post\"><center><table width=\"50%\" align=center><tr>
<td align=center><font class=\"pn-normal\">" . _SELECT_LANGUAGE_2;

	lang_dropdown();

	echo "<input type=\"hidden\" name=\"op\" value=\"Set Language\">
<input type=\"submit\" id=\"submit\" value=\"" . _BTN_SET_LANGUAGE . "\"></font></td></tr>
</table></center></form>";
}

function print_default()
{
	progress(20);
	echo "<br>
<font class=\"pn-normal\">" . _DEFAULT_1 . "</font><br><br>
<font class=\"pn-title\">" . _DEFAULT_2 . "</font>
<font class=\"pn-normal\">" . _DEFAULT_3 . "<br><br>
<form action=\"install.php\" method=\"post\"><center>
<textarea name=\"license\" cols=80 rows=10>";

	include("docs/copying.txt");

	echo "</textarea><br><br>";

	print_form_hidden();

	echo "
<input type=\"hidden\" name=\"op\" value=\"Check\">
<input type=\"submit\" id=\"submit\" value=\"" . _BTN_NEXT . "\"></center>
</form></font><br><br>";
}

function print_form_hidden()
{
	global $currentlang;
	global $dbhost, $dbuname, $dbpass, $dbname, $prefix, $dbtype, $intranet, $dbtabletype;

	if (empty($intranet)) {
		$intranet = 0;
	}

	echo "
<input type=\"hidden\" NAME=\"currentlang\" value=\"$currentlang\">
<input type=\"hidden\" NAME=\"dbhost\" value=\"$dbhost\">
<input type=\"hidden\" NAME=\"dbuname\" value=\"$dbuname\">
<input type=\"hidden\" NAME=\"dbpass\" value=\"$dbpass\">
<input type=\"hidden\" NAME=\"dbname\" value=\"$dbname\">
<input type=\"hidden\" NAME=\"prefix\" value=\"$prefix\">
<input type=\"hidden\" NAME=\"dbtype\" value=\"$dbtype\">
<input type=\"hidden\" NAME=\"dbtabletype\" value=\"$dbtabletype\">
<input type=\"hidden\" NAME=\"intranet\" value=\"$intranet\">";
}

function print_CHM_check()
{
	global $currentlang;
	progress(40);
	echo "<BR>
<font class=\"pn-title\">" . _DBINFO . "</font><font class=\"pn-normal\"> " . _CHM_CHECK_1 . "<br>
<form action=\"install.php\" method=\"post\"><center>";

	print_form_editabletext(0);

	echo "<BR><input type=\"hidden\" NAME=\"currentlang\" value=\"$currentlang\">
<input type=\"hidden\" name=\"op\" value=\"Submit\">
<input type=\"submit\" id=\"submit\" value=\"" . _BTN_SUBMIT . "\"></center></form></font>";
}

function print_form_editabletext($border = 0)
{
	global $dbhost, $dbuname, $dbpass, $dbname, $intranet, $prefix;

	echo "<br>
<table border=$border>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _DBHOST . "</font></td>
<td><input type=\"text\" NAME=\"dbhost\" SIZE=30 maxlength=80 value=\"$dbhost\"></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _DBUNAME . "</font></td>
<td><input type=\"text\" NAME=\"dbuname\" SIZE=30 maxlength=80 value=\"$dbuname\"></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _DBPASS . "</font></td>
<td><input type=\"password\" NAME=\"dbpass\" SIZE=30 maxlength=80 value=\"$dbpass\"></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _DBNAME . "</font></td>
<td><input type=\"text\" NAME=\"dbname\" SIZE=30 maxlength=80 value=\"$dbname\"></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _DBPREFIX . "</font></td>
<td><input type=\"text\" NAME=\"prefix\" SIZE=30 maxlength=80 value=\"$prefix\"></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _DBTYPE . "</font></td>
<td><select name=\"dbtype\"><option value=\"mysql\" selected>&nbsp;MySQL&nbsp;</option>
	<option value=\"oci8\">&nbsp;Oracle&nbsp;</option>
</select></td></tr>
</table>";
}

function print_submit()
{
	progress(50);
	echo "<br><center>
<font class=\"pn-title\">" . _DBINFO . "</font>: <font class=\"pn-normal\"> " . _SUBMIT_1 . "</font> <font class=\"pn-normal\">" . _SUBMIT_3 . "</font> <BR><BR>
<font class=\"pn-normal\">" . _SUBMIT_2 . "</font><br>";

	print_form_text(1);

	echo "
</font>
	<BR><BR>
<form action=\"install.php\" method=\"post\">
<input type=\"submit\" id=\"submit\" name=\"op\" value=\"" . _BTN_CHANGEINFO . "\">
";

	print_form_hidden();

	echo "<input type=\"submit\" id=\"submit\" name=\"op\" value=\"" . _BTN_NEWINSTALL . "\"></form></center>";

}

function print_form_text($border = 0)
{
	global $dbhost, $dbuname, $dbpass, $dbname, $prefix, $dbtype, $intranet, $dbtabletype;

	if ($dbtype=="oci8") {
		$dbtype_show = "oracle";
	}
	else {
		$dbtype_show = $dbtype;
	}
	echo "
<table border=$border cellpaddng=3 width=350>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _DBHOST . "</font></td>
<td><font class=\"pn-normal\">$dbhost</font></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _DBUNAME . "</font></td>
<td><font class=\"pn-normal\">$dbuname</font></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _DBPASS . "</font></td>
<td><font class=\"pn-normal\">&middot;&middot;&middot;&middot;&middot;</font></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _DBNAME . "</font></td>
<td><font class=\"pn-normal\">$dbname</font></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _DBPREFIX . "</font></td>
<td><font class=\"pn-normal\">$prefix</font></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _DBTYPE . "</font></td>
<td><font class=\"pn-normal\">$dbtype_show</font></td></tr>
</table>";
}

function print_change_info()
{
	echo "<center>
<font class=\"pn-title\">Change Info</font><font class=\"pn-normal\">" . _CHANGE_INFO_1 . "<br><br>
<form action=\"install.php\" method=\"post\">";

	print_form_editabletext(0);
	print_form_hidden();

	echo "
	<BR>
<input type=\"hidden\" name=\"op\" value=\"Submit\">
<input type=\"submit\" id=\"submit\" value=\"" . _BTN_SUBMIT . "\"></center></form></font>";
}

function print_new_install()
{
	progress(60);
	echo "<center><br><font class=\"pn-title\">" . _NEWINSTALL . "</font><br><font class=\"pn-normal\"> " . _NEW_INSTALL_1 . "</font><br><br><center>";

	print_form_text(0);

	echo "
<br><br><font class=\"pn-normal\">" . _NEW_INSTALL_2 . "</font>
<form action=\"install.php\" method=\"post\"><table width=\"50%\"><tr>
<td align=center><font class=\"pn-normal\">" . _NEW_INSTALL_3 . "</font><br><input type=checkbox name=\"dbmake\"><br><BR>";

	print_form_hidden();

	echo "
<input type=\"hidden\" name=\"op\" value=\"Start\">
<input type=\"submit\" id=\"submit\" value=\"" . _BTN_START . "\"></td></tr></table></form></font></center>";
}

function print_start()
{
	echo "<br>
<form action=\"install.php\" method=\"post\"><center><table width=\"50%\" align=center>
<tr><td align=center>";

	print_form_hidden();

	echo "
<input type=\"hidden\" name=\"op\" value=\"Continue\">
<input type=\"submit\" id=\"submit\" value=\"" . _BTN_CONTINUE . "\"></td></tr></table></center></form>";
}

function print_continue()
{
	progress(80);
	echo "<br>
<font class=\"pn-title\">" . _CONTINUE_1 . "</font>
<font class=\"pn-normal\">" . _CONTINUE_2 . "</font><br><br>
<center><form action=\"install.php\" method=\"post\"><table width=\"50%\" border=0>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _ADMIN_LOGIN . "</font></td>
<td><input type=\"text\" NAME=\"aid\" SIZE=30 maxlength=80 value=\"admin\"></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _ADMIN_PASS . "</font></td>
<td><input type=\"password\" NAME=\"pwd\" SIZE=30 maxlength=80 value=\"\"></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _ADMIN_REPEATPASS . "</font></td>
<td><input type=\"password\" NAME=\"repeatpwd\" SIZE=30 maxlength=80 value=\"\"></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _ADMIN_NAME . "</font></td>
<td><input type=\"text\" NAME=\"name\" SIZE=30 maxlength=80 value=\"admin\"></td></tr>
<tr><td align=\"left\"><font class=\"pn-normal\">" . _ADMIN_EMAIL . "</font></td>
<td><input type=\"text\" NAME=\"email\" SIZE=30 maxlength=80 value=\"none@none.com\"></td></tr>
";

	print_form_hidden();

	echo "
</td></tr></table><br><input type=\"hidden\" name=\"op\" value=\"Set Login\">
<input type=\"submit\" id=\"submit\" value=\"" . _BTN_SET_LOGIN . "\"></form></font></center>";
	//wait install lexitron
	echo "<center>"._WAIT_LEXITRON."</center>";
}

function print_set_login()
{
	echo "
<form action=\"install.php\" method=\"post\"><center><table width=\"50%\">";

	print_form_hidden();

	echo "
<tr><td align=center><input type=\"hidden\" name=\"op\" value=\"Finish\">
<input type=\"submit\" id=\"submit\" value=\"" . _BTN_FINISH . "\"></td></tr></table></center></form>";
}

function print_finish()
{
	progress(100);
	echo "
<font class=\"pn-title\">" . _FINISH_1 . "</font>
<font class=\"pn-normal\">" . _FINISH_2 . "<br><br><form action=\"install.php\" method=\"post\">
<center><textarea name=\"license\" cols=80 rows=10>";

	include("docs/credits.txt");

	echo "
</textarea><br><br>" . _FINISH_3 . "</center></form></font>
<br><br><center><b><a href=\"index.php\">" . _FINISH_4 . "</a></b></center><br><br>";
}

?>
