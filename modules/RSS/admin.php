<?php
/*
 Module : RSS
 Create on : 29/06/49
 By : Orrawin
 */

if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_ADMIN)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}


/* - - - - MAIN- - - - - */
//$vars= array_merge($GLOBALS['HTTP_GET_VARS'],$GLOBALS['HTTP_POST_VARS']);
$vars = array_merge($_POST,$_GET);
include 'header.php';

/** Navigator **/
$menus = $links = array();
if (lnUserAdmin( lnSessionGetVar('uid'))) {
	$menus[] = _ADMINMENU;
	$links[]='index.php?mod=Admin';
}

$menus[]= _RSSMENU;
$links[]= 'index.php?mod=RSS&amp;file=admin';
/** Navigator **/


if (!empty($op)) {
	// include more functions
	switch($op) {
		case "delete_rss": delRSS($id); break;
		case "add_rss": addRSS($vars); return;
		case "add_rssform" : addRSSform(); return;
		case "add_editrssform" : editRSSform($id); return;
		case "update_rss": updateRSS($vars); return;

	}
}

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$rsstable = $lntable['rss'];
$rsscolumn = &$lntable['rss_column'];
if($dbconn->ErrorNo() != 0) {
	echo "error";
	return;
}

/** Navigator **/
lnBlockNav($menus,$links);
/** Navigator **/


OpenTable();

echo  '<p align=right><IMG SRC="images/global/note.gif"><a href="index.php?mod=RSS&file=admin&amp;op=add_rssform">'. _ADDRSS.'</a></p>';

?>

<table width="100%" cellpadding=2 cellspacing=1 border=0>
	<tr bgcolor='#D2E9FF'>
		<td width='8%'>
		<center><b><font size='2' face='MS Sans Serif'><? echo _HEAD_CODE ?></font></b></center>
		</td>
		<td width='40%'>
		<center><b><font size='2' face='MS Sans Serif'><? echo _RSSMENU ?></font></b></center>
		</td>
		<td width='15%'>
		<center><b><font size='2' face='MS Sans Serif'><? echo _POSTNAME ?></font></b></center>
		</td>
		<td width='16%'>
		<center><b><font size='2' face='MS Sans Serif'><? echo _POSTDATE ?></font></b></center>
		</td>
		<td width='5%'>
		<center><b><font size='2' face='MS Sans Serif'><? echo _DELHEAD ?></font></b></center>
		</td>
	</tr>

	<?
	if ($result->EOF) {
		echo "<br><h3><center>"._NO_TOPIC."</center></h3>";
	}


	//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++


	$pagesize = lnConfigGetVar('pagesize');
	if (!isset($page)) {
		$page = 1;
	}
	$min = $pagesize * ($page - 1); // This is where we start our record set from
	$max = $pagesize; // This is how many rows to select

	$count = "SELECT COUNT($rsscolumn[id]) FROM $rsstable";

	if (!lnUserAdmin(lnSessionGetVar('uid'))) {
		$query = " and $userscolumn[uid]='".lnSessionGetVar('uid')."'";
	}

	if (!empty($where)) {
		$where = "$where";
	} else {
		$where = '';
	}

	$sorting="id DESC";


	$resultcount = $dbconn->Execute($count);

	//	$resultcount = $dbconn->Execute($count . $query . $where);
	list ($numrows) = $resultcount->fields;
	$resultcount->Close();

	//query for show page
	$myquery = buildSimpleQuery('rss', array('id', 'title', 'xml', 'display', 'name', 'date'), $where,lnVarPrepForStore($sorting), $max, $min);

	$result = $dbconn->Execute($myquery);
	$num = 0;
	for ($i=1; list($id,$title,$xml,$display,$name,$date) = $result->fields; $i++) {
		$num++;
		$result->MoveNext();
		$date =  Date_Calc::dateFormat2($date, "%e %b %y");
		$n=$min + $i;


		echo "<tr valign=middle>";
		echo "<td align=center>$num</td>";
		echo "<td><A HREF=\"index.php?mod=RSS&file=admin&amp;op=add_editrssform&amp;id=$id\">$title</A></td>";
		echo "<td align=center>$name</td>";
		echo "<td align=center>$date</td>";
		echo "<td align=center><A HREF=\"javascript: if(confirm('Confirm Delete?')) window.open('index.php?mod=RSS&amp;file=admin&amp;op=delete_rss&amp;id=$id','_self')\"><IMG SRC=\"images/global/delete.gif\"  BORDER=0 ALT="._DELETE."></A></td>";
		echo "</tr>";
	}

	/* show pages */
	//echo '</table>';


	if ($numrows  > $pagesize) {
		$total_pages = ceil($numrows / $pagesize); // How many pages are we dealing with here ??
		$prev_page = $page - 1;
		echo '<BR>Page : ';
		if ( $prev_page > 0 ) {
			echo '<A HREF="index.php?mod=RSS&amp;file=admin&amp;page='.$prev_page.'"><IMG SRC="images/back.gif" WIDTH="19" HEIGHT="9" BORDER="0" ALT=""></A>';
		}
		for($n=1; $n <= $total_pages; $n++) {
			if ($n == $page) {
				echo "<B><U>$n</U></B> ";
			}
			else {
				echo '<A HREF="index.php?mod=RSS&amp;file=admin&amp;page='.$n.'">'.$n.'</A> ';
			}
		}
		$next_page = $page + 1;
		if ( $next_page <= $total_pages ) {
			echo '<A HREF="index.php?mod=RSS&amp;file=admin&amp;page='.$next_page.'"><IMG SRC="images/next.gif" WIDTH="19" HEIGHT="9" BORDER="0" ALT=""></A> ';
		}

		echo '<BR><BR><B> <FONT COLOR="#800000">= '._TOTALCOURSES.'&nbsp;'.$numrows.' ข่าว</B> =</FONT> <BR>';
		echo '</center>';
		echo '</td></tr>';
	}

	echo '</table>';

	//end show page


	CloseTable();

	include 'footer.php';

	/* - - - - END MAIN- - - - - */


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	function addRSSform() {
		global $menus,$links;

		/** Navigator **/
		$menus[]= _ADDRSS;
		$links[]= '#';
		lnBlockNav($menus,$links);
		/** Navigator **/

		?>
	<script language="javaScript">
		function formSubmit() {
			if(checkFields()) document.forms.RSS.submit();
		}
    	function checkFields() {
			var title = document.forms.RSS.rsstitle.value;
			var rssdesc = document.forms.RSS.rssdesc.value;
			var postname = document.forms.RSS.postname.value;
			var postemail = document.forms.RSS.postemail.value;
		
			if (title  == "" ) {
				alert("กรุณากรอกประเภทข่าวค่ะ");
				document.forms.RSS.rsstitle.focus();
				return false;
			}
			if (rssdesc  == "" ) {
				alert("กรุณากรอกลิงค์ของข่าวRSSค่ะ");
				document.forms.RSS.rssdesc.focus();
				return false;
			}
			if (postname  == "" ) {
				alert("กรุณากรอกชื่อผู้เพิ่มข่าวด้วยค่ะ");
				document.forms.RSS.postname.focus();
				return false;
			}
			if (postemail  == "" ) {
				alert("กรุณากรอกอีเมล์ด้วยค่ะ");
				document.forms.RSS.postname.focus();
				return false;
			}
			else if (!((postemail.indexOf(".") > 2) && (postemail.indexOf("@") > 0)))
						{ 		alert("กรุณากรอกอีเมลให้ถูกต้องด้วยค่ะ");
								document.forms.RSS.postname.focus();
								return false;
						}
			return true; 
		}

</script>
<?


list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$rsstable = $lntable['rss'];
$rsscolumn = &$lntable['rss_column'];

$query = "SELECT $rsscolumn[id],
$rsscolumn[title],
$rsscolumn[xml],
$rsscolumn[display],
$rsscolumn[name],
$rsscolumn[date] FROM $rsstable";

$result = $dbconn->Execute($query);

if($dbconn->ErrorNo() != 0) {
	echo "error";
	return;
}

echo '<BR><center><fieldset><legend>'._ADDRSS.'</legend>'
.'<TABLE WIDTH="580" CELLPADDING=1 CELLSPACING=0 BORDER=0>'
.'<FORM NAME="RSS" METHOD=POST ACTION="index.php">'
.'<INPUT TYPE="hidden" NAME="mod" VALUE="RSS">'
.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
.'<INPUT TYPE="hidden" NAME="op" VALUE="add_rss">'
.'<INPUT TYPE="hidden" NAME="course_author" VALUE="'.lnSessionGetVar('uid').'">';

echo '<TR><TD WIDTH=130>'._RSSTITLE.' <B>*</B></TD><TD><INPUT TYPE="text" NAME="rsstitle" SIZE="50" VALUE=""></TD></TR>';
echo '<TR><TD WIDTH=130 VALIGN="TOP">'._RSSDESCRIPTION.' <B>*</B></TD><TD><INPUT TYPE="text" NAME="rssdesc" SIZE="50" VALUE=""></TD></TR>';
echo '<TR><TD WIDTH=130 VALIGN="TOP">'._RSSOPTION.' </TD><TD>
	<INPUT TYPE="radio" NAME="rssop" VALUE="1" CHECKED="true">'._op1.'
	<INPUT TYPE="radio" NAME="rssop" VALUE="2">'._op2.'
	<INPUT TYPE="radio" NAME="rssop" VALUE="3">'._op3.'
	<INPUT TYPE="radio" NAME="rssop" VALUE="4">'._op4.'
	</TD></TR>';
echo '<TR><TD WIDTH=130>'._POSTNAME.' <B>*</B></TD><TD><INPUT TYPE="text" NAME="postname" SIZE="20" VALUE=""></TD></TR>';
echo '<TR><TD WIDTH=130>'._POSTEMAIL.' </TD><TD><INPUT TYPE="text" NAME="postemail" SIZE="50" VALUE=""></TD></TR>';

echo '<TR><TD WIDTH=130 VALIGN="TOP">&nbsp;<TD><BR><INPUT class="button" TYPE="button"  VALUE="'. _ADD. '" onclick="formSubmit()"> ';
echo "<INPUT class=\"button\" TYPE=\"button\" VALUE=\"". _CANCEL. "\" onclick=\"javascript:window.open('index.php?mod=RSS&amp;file=admin','_self')\">";
echo '<BR><BR></TD></TR></FORM>'
.'</TABLE>'
.'</fieldset>';

include 'footer.php';

	}

	//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

	function addRSS($vars) {
	 // Get arguments from argument array
		extract($vars);
		$rss = $rssdesc;
		$time=time();
		if($rssop=='1'){//normal
			$rssop= '';
		}else	if($rssop=='2'){//gotoleft
			$rssop= '<marquee width="100%" onmouseover="this.scrollAmount=0" onmouseout="this.scrollAmount=1" scrollAmount="1" scrollDelay="27" truespeed="true">';
		}else	if($rssop=='3'){//gotoup
			$rssop= '<marquee width="100%" onmouseover="this.scrollAmount=0" onmouseout="this.scrollAmount=1" scrollAmount="1" scrollDelay="27" truespeed="true" direction="up">';
		}else	if($rssop=='4'){//gotodown
			$rssop='<marquee width="100%" onmouseover="this.scrollAmount=0" onmouseout="this.scrollAmount=1" scrollAmount="1" scrollDelay="27" truespeed="true" direction="down">';
		}

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$rsstable = $lntable['rss'];
		$rsscolumn = &$lntable['rss_column'];
		$query = "INSERT INTO $rsstable
		(	$rsscolumn[title],
		$rsscolumn[xml],
		$rsscolumn[display],
		$rsscolumn[name],
		$rsscolumn[email],
		$rsscolumn[date]
		)
		VALUES ('$rsstitle','$rssdesc','$rssop', '$postname','$postemail','$time')";
		//VALUES ('$rsstitle','$rssdesc','$rssop', '$postname','$postemail','$time')";
		//echo $query; exit();
		$dbconn->Execute($query);

	 if ($dbconn->ErrorNo() != 0) {
	 	echo "error";
	 	return false;
	 }
	 else {

	 	global $menus,$links;

	 	/** Navigator **/
	 	$menus[]= _ADDRSS;
	 	$links[]= '#';
	 	lnBlockNav($menus,$links);
	 	/** Navigator **/

	 	echo '<BR><center><fieldset><legend>'._ADDRSS.'</legend>'
	 	.'<TABLE WIDTH="550" HEIGHT="250" CELLPADDING=1 CELLSPACING=0 BORDER=0>'
	 	.'<TR><TD align=center>'._RETURNMESS.'</TD></TR>'
	 	.'<TR><TD></TD></TR>'
	 	.'</TABLE>'
	 	.'</fieldset>';


	 }
	 include 'footer.php';
	 echo '<meta http-equiv="refresh" content="0;URL=index.php?mod=RSS&file=admin" />';
	}

	//***********************************************************************************************************************************************

	//\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

	function delRSS($id)
	{

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$rsstable = $lntable['rss'];
		$rsscolumn = &$lntable['rss_column'];

		// delete rss
		$dbconn->Execute("DELETE FROM $rsstable WHERE $rsscolumn[id] = '$id'");

		//include 'footer.php';

	}



	function editRSSform($id) {

		global $menus, $links;
		/** Navigator **/
		$menus[]= _VIEWRSS;
		$links[]= '#';
		lnBlockNav($menus,$links);
		/** Navigator **/

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$rsstable = $lntable['rss'];
		$rsscolumn = &$lntable['rss_column'];


		$result = $dbconn->Execute("SELECT * from $rsstable WHERE $rsscolumn[id] = $id");

		list($id,$title,$xml,$display,$name,$email,$date) = $result->fields;

		$row['content'] .= '<div><b>'.$title.'</b></div>';
		$row['content'] .= $display;
		$ch = curl_init($xml);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$data = curl_exec($ch);
		curl_close($ch);
		$doc = new SimpleXmlElement($data, LIBXML_NOCDATA);

		if(isset($doc->channel))
		{
			$row['content'] .= parseRSS($doc);
		}
		if(isset($doc->entry))
		{
			$row['content'] .= parseAtom($doc);
		}
		$row['content'] .= '</marquee><hr>';


		$date =  Date_Calc::dateFormat3($date, "%e %b %y");

		echo '<BR><center><fieldset><legend>'._VIEWRSS.'</legend>'
		.'<BR><TABLE WIDTH="550" CELLPADDING=2 CELLSPACING=0 BORDER=1 BGCOLOR=#CCCCCC BORDERCOLOR=#FFFFFF>'
		.'<TR><TD WIDTH=100 VALIGN="TOP">'._RSSTITLE.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$title.'</TD></TR>'
		.'<TR><TD WIDTH=100 VALIGN="TOP">'._RSSDESCRIPTION.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$xml.'</TD></TR>'
		.'<TR><TD WIDTH=100 VALIGN="TOP">'._VIEWRSS.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$row['content'].'</TD></TR>'
		.'<TR><TD WIDTH=100 VALIGN="TOP">'._POSTNAME.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$name.'</TD></TR>'
		.'<TR><TD WIDTH=100 VALIGN="TOP">'._POSTEMAIL.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$email.'</TD></TR>'
		.'<TR><TD WIDTH=100 VALIGN="TOP">'._POSTDATE.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$date.'</TD></TR>'
		.'</TABLE>';

		?>
	<script language="javaScript">
		function formSubmit() {
			if(checkFields()) document.forms.RSS.submit();
		}
    	function checkFields() {
			var title = document.forms.RSS.rsstitle.value;
			var rssdesc = document.forms.RSS.rssdesc.value;
			var postname = document.forms.RSS.postname.value;
			var postemail = document.forms.RSS.postemail.value;
		
			if (title  == "" ) {
				alert("กรุณากรอกประเภทข่าวค่ะ");
				document.forms.RSS.rsstitle.focus();
				return false;
			}
			if (rssdesc  == "" ) {
				alert("กรุณากรอกลิงค์ของข่าวRSSค่ะ");
				document.forms.RSS.rssdesc.focus();
				return false;
			}
			if (postname  == "" ) {
				alert("กรุณากรอกชื่อผู้เพิ่มข่าวด้วยค่ะ");
				document.forms.RSS.postname.focus();
				return false;
			}
			if (postemail  == "" ) {
				alert("กรุณากรอกอีเมล์ด้วยค่ะ");
				document.forms.RSS.postname.focus();
				return false;
			}
			else if (!((postemail.indexOf(".") > 2) && (postemail.indexOf("@") > 0)))
						{ 		alert("กรุณากรอกอีเมลให้ถูกต้องด้วยค่ะ");
								document.forms.RSS.postname.focus();
								return false;
						}
			return true; 
		}

</script>
<?


list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$rsstable = $lntable['rss'];
$rsscolumn = &$lntable['rss_column'];

$query = "SELECT $rsscolumn[id],
$rsscolumn[title],
$rsscolumn[xml],
$rsscolumn[display],
$rsscolumn[name],
$rsscolumn[date] FROM $rsstable";

$result = $dbconn->Execute($query);

if($dbconn->ErrorNo() != 0) {
	echo "error";
	return;
}

echo '<BR><center><fieldset><legend>'._EDITRSS.'</legend>'
.'<TABLE WIDTH="580" CELLPADDING=1 CELLSPACING=0 BORDER=0>'
.'<FORM NAME="RSS" METHOD=POST ACTION="index.php">'
.'<INPUT TYPE="hidden" NAME="mod" VALUE="RSS">'
.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
.'<INPUT TYPE="hidden" NAME="op" VALUE="update_rss">'
.'<INPUT TYPE="hidden" NAME="course_author" VALUE="'.lnSessionGetVar('uid').'">'
.'<INPUT TYPE="hidden" NAME="id" VALUE="'.$id.'">';

echo '<TR><TD WIDTH=130>'._RSSTITLE.' <B>*</B></TD><TD><INPUT TYPE="text" NAME="rsstitle" SIZE="50" VALUE="'.$title.'"></TD></TR>';
echo '<TR><TD WIDTH=130 VALIGN="TOP">'._RSSDESCRIPTION.' <B>*</B></TD><TD><INPUT TYPE="text" NAME="rssdesc" SIZE="50" VALUE="'.$xml.'"></TD></TR>';
echo '<TR><TD WIDTH=130 VALIGN="TOP">'._RSSOPTION.' </TD><TD>
	<INPUT TYPE="radio" NAME="rssop" VALUE="1" CHECKED="true">'._op1.'
	<INPUT TYPE="radio" NAME="rssop" VALUE="2">'._op2.'
	<INPUT TYPE="radio" NAME="rssop" VALUE="3">'._op3.'
	<INPUT TYPE="radio" NAME="rssop" VALUE="4">'._op4.'
	</TD></TR>';
echo '<TR><TD WIDTH=130>'._POSTNAME.' <B>*</B></TD><TD><INPUT TYPE="text" NAME="postname" SIZE="20" VALUE="'.$name.'"></TD></TR>';
echo '<TR><TD WIDTH=130>'._POSTEMAIL.' </TD><TD><INPUT TYPE="text" NAME="postemail" SIZE="50" VALUE="'.$email.'"></TD></TR>';

echo '<TR><TD WIDTH=130 VALIGN="TOP">&nbsp;<TD><BR><INPUT class="button" TYPE="button"  VALUE="'. _EDITRSS. '" onclick="formSubmit()"> ';
echo "<INPUT class=\"button\" TYPE=\"button\" VALUE=\"". _CANCEL. "\" onclick=\"javascript:window.open('index.php?mod=RSS&amp;file=admin','_self')\">";
echo '<BR><BR></TD></TR></FORM>'
.'</TABLE>'
.'</fieldset>';


include 'footer.php';
	}

	//****************************
	function updateRSS($vars) {
	 // Get arguments from argument array
		extract($vars);
		$rss = $rssdesc;
		$time=time();
		if($rssop=='1'){//normal
			$rssop= '';
		}else	if($rssop=='2'){//gotoleft
			$rssop= '<marquee width="100%" onmouseover="this.scrollAmount=0" onmouseout="this.scrollAmount=1" scrollAmount="1" scrollDelay="27" truespeed="true">';
		}else	if($rssop=='3'){//gotoup
			$rssop= '<marquee width="100%" onmouseover="this.scrollAmount=0" onmouseout="this.scrollAmount=1" scrollAmount="1" scrollDelay="27" truespeed="true" direction="up">';
		}else	if($rssop=='4'){//gotodown
			$rssop='<marquee width="100%" onmouseover="this.scrollAmount=0" onmouseout="this.scrollAmount=1" scrollAmount="1" scrollDelay="27" truespeed="true" direction="down">';
		}

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$rsstable = $lntable['rss'];
		$rsscolumn = &$lntable['rss_column'];
		$query = "UPDATE $rsstable SET
		$rsscolumn[title] = '$rsstitle',
		$rsscolumn[xml] = '$rssdesc',
		$rsscolumn[display] = '$rssop',
		$rsscolumn[name] = '$postname',
		$rsscolumn[email] = '$postemail',
		$rsscolumn[date] = '$time'
		WHERE $rsscolumn[id] = '$id'";
		$dbconn->Execute($query);

	 if ($dbconn->ErrorNo() != 0) {
	 	echo "error";
	 	return false;
	 }
	 else {

	 	global $menus,$links;

	 	/** Navigator **/
	 	$menus[]= _ADDRSS;
	 	$links[]= '#';
	 	lnBlockNav($menus,$links);
	 	/** Navigator **/

	 	echo '<BR><center><fieldset><legend>'._ADDRSS.'</legend>'
	 	.'<TABLE WIDTH="550" HEIGHT="250" CELLPADDING=1 CELLSPACING=0 BORDER=0>'
	 	.'<TR><TD align=center>'._RETURNEDITMESS.'</TD></TR>'
	 	.'<TR><TD></TD></TR>'
	 	.'</TABLE>'
	 	.'</fieldset>';


	 }
	 include 'footer.php';

	 echo '<meta http-equiv="refresh" content="0;URL=index.php?mod=RSS&file=admin" />';

	}


	function parseRSS($xml)
	{
		$feed = "<center><table border = '0'><tr><td><strong>".$xml->channel->title."</strong></td></tr>";
		$cnt = count($xml->channel->item);
		$cnt = $cnt/2;
		$k = 0;
		for($i=0; $i < $cnt; $i++)
		{
			$feed .= '<tr>';
			for($j = 0;$j < 2;$j++){

				$url = $xml->channel->item[$k]->link;
				$title 	= $xml->channel->item[$k]->title;
				$desc = $xml->channel->item[$k]->description;

				$feed .= '<td><b><a href="'.$url.'">'.$title.'</a></b><br>'.$desc.'</td>';
				$k++;
			}
			$feed .= '</tr>';

		}
		$feed .= '</table></center>';
		return $feed;
	}
	
	function parseAtom($xml)
	{
		$feed = "<div style = 'width:450px;'><strong>".$xml->author->name."</strong>";
		$cnt = count($xml->entry);
		for($i=0; $i < $cnt; $i++)
		{
			//print_r($xml->entry[$i]);
			//exit();
			//$urlAtt = $xml->entry->link[$i];
			//$url	= $urlAtt['@attributes']['href'];
			//$title 	= $xml->entry->title;
			$desc = $xml->entry[$i]->content;
			$feed .= $desc;
			//$feed .= '<tr><td><a href="'.$url.'"><b>'.$title.'</b></a>'.$desc.'<hr></td></tr>';
		}
		$feed .= '</div>';
		return $feed;
	}

	?>