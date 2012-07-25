<?php
/*
Module : News
Create on : 29/06/49
By : Orrawin
*/

if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_MODERATE)) {
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

$menus[]= _NEWSMENU;
$links[]= 'index.php?mod=News&amp;file=admin';
/** Navigator **/


if (!empty($op)) {
	// include more functions
	switch($op) {
		case "delete_ques": delQues($idq); break;
		case "add_ques": addQues($vars); return;
		case "add_qform" : addQform(); return;
		case "delete_ans" : delAns($idq,$ida); return;
		case "add_ans" : addAns($vars); return;
		case "add_aform" : addAform($idq); return;
		
	}
}

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$newstable = $lntable['news'];
$newscolumn = &$lntable['news_column'];
if($dbconn->ErrorNo() != 0) {
echo "error";
	return;
}

/** Navigator **/
lnBlockNav($menus,$links);
/** Navigator **/


OpenTable();

echo  '<p align=right><IMG SRC="images/global/note.gif"><a href="index.php?mod=News&file=admin&amp;op=add_qform">'. _ADDNEWS.'</a></p>';

?>

	<table width= "100%" cellpadding=2 cellspacing=1 border=0>
		<tr bgcolor='#D2E9FF'>
			<td width='8%'><b><font size='2' face='MS Sans Serif'><? echo _HEAD_CODE ?></font></b></td>
			<td width='40%'><b><font size='2' face='MS Sans Serif'><? echo _NEWSMENU ?></font></b></td>
			<td width='15%'><b><font size='2' face='MS Sans Serif'><? echo _POSTNAME ?></font></b></td>
			<td width='16%'><b><font size='2' face='MS Sans Serif'><? echo _POSTDATE ?></font></b></td>
			<td width='5%'><b><font size='2' face='MS Sans Serif'><? echo _DELHEAD ?></font></b></td>
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

	$count = "SELECT COUNT($newscolumn[idq]) FROM $newstable";

	if (!lnUserAdmin(lnSessionGetVar('uid'))) {
		$query = " and $userscolumn[uid]='".lnSessionGetVar('uid')."'";
	}

	if (!empty($where)) {
		$where = "$where";
	} else {
		$where = '';
	}

	$sorting="idq DESC";


	$resultcount = $dbconn->Execute($count);

//	$resultcount = $dbconn->Execute($count . $query . $where);
	list ($numrows) = $resultcount->fields;
	$resultcount->Close();

//query for show page
	$myquery = buildSimpleQuery('news', array('idq', 'titleq', 'detailq', 'nameq', 'dateq'), $where,lnVarPrepForStore($sorting), $max, $min); 

	$result = $dbconn->Execute($myquery);

for ($i=1; list($idq,$titleq,$detailq,$nameq,$dateq) = $result->fields; $i++) {

	$result->MoveNext();
	$date  = date('d-M-Y H:i', $dateq);
	//$date =  Date_Calc::dateFormat2($dateq, "%e %b %y");
	$n=$min + $i;


	echo "<tr >";
	echo "<td align=center>$idq</td>";
	echo "<td><A HREF=\"index.php?mod=News&file=admin&amp;op=add_aform&amp;idq=$idq\">$titleq</A></td>";
	echo "<td >$nameq</td>";
	echo "<td >$date</td>";
	echo "<td ><A HREF=\"javascript: if(confirm('Confirm Delete?')) window.open('index.php?mod=News&amp;file=admin&amp;op=delete_ques&amp;idq=$idq','_self')\"><IMG SRC=\"images/global/delete.gif\"  BORDER=0 ALT="._DELETE."></A></td>";
	echo "</tr>";
}

/* show pages */
echo '</table>';

		 
		 if ($numrows  > $pagesize) {
			 $total_pages = ceil($numrows / $pagesize); // How many pages are we dealing with here ??
			 $prev_page = $page - 1;
			 echo '<BR>Page : ';
			  if ( $prev_page > 0 ) {
				echo '<A HREF="index.php?mod=News&amp;file=admin&amp;page='.$prev_page.'"><IMG SRC="images/back.gif" WIDTH="19" HEIGHT="9" BORDER="0" ALT=""></A>';
			  }
			  for($n=1; $n <= $total_pages; $n++) {
				if ($n == $page) {
					echo "<B><U>$n</U></B> ";
				}
				else {
					echo '<A HREF="index.php?mod=News&amp;file=admin&amp;page='.$n.'">'.$n.'</A> ';
				}
			  } 
			  $next_page = $page + 1;
			  if ( $next_page <= $total_pages ) {
				  echo '<A HREF="index.php?mod=News&amp;file=admin&amp;page='.$next_page.'"><IMG SRC="images/next.gif" WIDTH="19" HEIGHT="9" BORDER="0" ALT=""></A> ';
			  }

		echo '<BR><BR><B> <FONT COLOR="#800000">= '._TOTALCOURSES.'&nbsp;'.$numrows.' ข่าว</B> =</FONT> <BR>';
		echo '';
		echo '</td></tr>';
		}	

echo '</table>';

//end show page


CloseTable();

include 'footer.php';

/* - - - - END MAIN- - - - - */
			

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function addQform() {
	global $menus,$links;

	/** Navigator **/
	$menus[]= _ADDNEWS;
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/

?>
<script language="javaScript">
		function formSubmit() {
			if(checkFields()) document.forms.News.submit();
		}
    	function checkFields() {
			var title = document.forms.News.newstitle.value;
			var postname = document.forms.News.postname.value;
			var postemail = document.forms.News.postemail.value;
		
			if (title  == "" ) {
				alert("กรุณากรอกหัวข้อข่าวค่ะ");
				document.forms.News.newstitle.focus();
				return false;
			}
			if (postname  == "" ) {
				alert("กรุณากรอกชื่อผู้ประกาศด้วยค่ะ");
				document.forms.News.postname.focus();
				return false;
			}
			if (postemail  == "" ) {
				alert("กรุณากรอกอีเมล์ด้วยค่ะ");
				document.forms.News.postname.focus();
				return false;
			}
			else if (!((postemail.indexOf(".") > 2) && (postemail.indexOf("@") > 0)))
						{ 		alert("กรุณากรอกอีเมลให้ถูกต้องด้วยค่ะ");
								document.forms.News.postname.focus();
								return false;
						}
			return true; 
		}

</script>
<?	


list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$newstable = $lntable['news'];
$newscolumn = &$lntable['news_column'];

$query = "SELECT $newscolumn[idq],
									$newscolumn[titleq],
									$newscolumn[detailq],
									$newscolumn[nameq],
									$newscolumn[dateq] FROM $newstable";

$result = $dbconn->Execute($query);

if($dbconn->ErrorNo() != 0) {
echo "error";
	return;
}

	echo '<BR><fieldset><legend>'._ADDNEWS.'</legend>'
	.'<TABLE WIDTH="550" CELLPADDING=1 CELLSPACING=0 BORDER=0>'
	.'<FORM NAME="News" METHOD=POST ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="News">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="add_ques">'
	.'<INPUT TYPE="hidden" NAME="course_author" VALUE="'.lnSessionGetVar('uid').'">';

	echo '<TR><TD WIDTH=100>'._NEWSTITLE.' <B>*</B></TD><TD><INPUT TYPE="text" NAME="newstitle" SIZE="50" VALUE=""></TD></TR>';
	echo '<TR><TD WIDTH=100 VALIGN="TOP">'._NEWSDESCRIPTION.' </TD><TD><TEXTAREA " NAME="newsdesc" ROWS="5" COLS="30" wrap="soft" style="width: 90%;"></TEXTAREA></TD></TR>';
	echo '<TR><TD WIDTH=100>'._POSTNAME.' <B>*</B></TD><TD><INPUT TYPE="text" NAME="postname" SIZE="20" VALUE=""></TD></TR>';
	echo '<TR><TD WIDTH=100>'._POSTEMAIL.' </TD><TD><INPUT TYPE="text" NAME="postemail" SIZE="50" VALUE=""></TD></TR>';

	echo '<TR><TD WIDTH=100 VALIGN="TOP">&nbsp;<TD><BR><INPUT class="button" TYPE="button"  VALUE="'. _ADD. '" onclick="formSubmit()"> ';
	echo "<INPUT class=\"button\" TYPE=\"button\" VALUE=\"". _CANCEL. "\" onclick=\"javascript:window.open('index.php?mod=News&amp;file=admin','_self')\">";
	echo '<BR><BR></TD></TR></FORM>'
	.'</TABLE>'
	.'</fieldset>';

	include 'footer.php';
}

//---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

function addQues($vars) {
	 // Get arguments from argument array
    extract($vars);

	$time=time();

list($dbconn) = lnDBGetConn();
$lntable = lnDBGetTables();

$newstable = $lntable['news'];
$newscolumn = &$lntable['news_column'];
$query = "INSERT INTO $newstable
(	$newscolumn[titleq],
	$newscolumn[detailq],
	$newscolumn[nameq],
	$newscolumn[emailq],
	$newscolumn[dateq]
  )
	VALUES ('$newstitle','$newsdesc', '$postname','$postemail','$time')";

	$dbconn->Execute($query);
	
	 if ($dbconn->ErrorNo() != 0) {
	    echo "error";
        return false;
    } 
	else {

	global $menus,$links;

	/** Navigator **/
	$menus[]= _ADDNEWS;
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/

	echo '<BR><fieldset><legend>'._ADDNEWS.'</legend>'
	.'<TABLE WIDTH="550" HEIGHT="250" CELLPADDING=1 CELLSPACING=0 BORDER=0>'
	.'<TR><TD >'._RETURNMESS.'</TD></TR>'
	.'<TR><TD></TD></TR>'
	.'</TABLE>'
	.'</fieldset>';		


	}
	include 'footer.php';
}

//***********************************************************************************************************************************************

function addAform($idq) {

?>
<script language="javaScript">
		function formSubmit() {
			if(checkFields()) document.forms.News.submit();
		}
    	function checkFields() {
			var postname = document.forms.News.postname.value;
			var postemail = document.forms.News.postemail.value;
		
			if (postname  == "" ) {
				alert("กรุณากรอกชื่อผู้ประกาศด้วยค่ะ");
				document.forms.News.postname.focus();
				return false;
			}

			if (postemail  == "" ) {
				alert("กรุณากรอกอีเมล์ด้วยค่ะ");
				document.forms.News.postname.focus();
				return false;
			}
			else if (!((postemail.indexOf(".") > 2) && (postemail.indexOf("@") > 0)))
						{ 		alert("กรุณากรอกอีเมลให้ถูกต้องด้วยค่ะ");
								document.forms.News.postname.focus();
								return false;
						}
			return true; 
		}

</script>

<?	

	global $menus, $links;
	/** Navigator **/
	$menus[]= _VIEWNEWS;
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	$newstable = $lntable['news'];
	$newscolumn = &$lntable['news_column'];


	$resultq = $dbconn->Execute("SELECT * from $newstable WHERE $newscolumn[idq] = $idq");

	list($idq,$titleq,$detailq,$nameq,$emailq,$dateq) = $resultq->fields;

	$date  = date('d-M-Y H:i', $dateq);
	//$date =  Date_Calc::dateFormat3($dateq, "%e %b %y");

	echo '<BR><fieldset><legend>'._VIEWNEWS.'</legend>'
	.'<BR><TABLE WIDTH="550" CELLPADDING=2 CELLSPACING=0 BORDER=1 BGCOLOR=#CCCCCC BORDERCOLOR=#FFFFFF>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">'._NEWSTITLE.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$titleq.'</TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">'._NEWSDESCRIPTION.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$detailq.'</TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">'._POSTNAME.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$nameq.'</TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">'._POSTEMAIL.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$emailq.'</TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">'._POSTDATE.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$date.'</TD></TR>'
	.'</TABLE>';

	$newsanstable = $lntable['news_ans'];
	$newsanscolumn = &$lntable['news_ans_column'];

	$result = $dbconn->Execute("SELECT $newsanscolumn[ida], $newsanscolumn[detailans], $newsanscolumn[nameans], $newsanscolumn[emailans], $newsanscolumn[dateans]
														FROM $newsanstable WHERE $newsanscolumn[idq] = $idq  ORDER BY $newsanscolumn[ida]");

	for ($i=1; list($ida,$detailans,$nameans,$emailans,$dateans) = $result->fields; $i++) {

	$result->MoveNext();
	
	$date2  = date('d-M-Y H:i', $dateans);
	//$date2 =  Date_Calc::dateFormat3($dateans, "%e %b %y");

	echo '<BR><TABLE WIDTH="550" CELLPADDING=2 CELLSPACING=0 BORDER=1 BGCOLOR=#CCCCCC BORDERCOLOR=#FFFFFF>'
	.'<TR><TD colspan="2">'._COMMENT.' '.$i.'</TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">'._NEWSDESCRIPTION.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$detailans.'</TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">'._POSTNAME.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$nameans.'</TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">'._POSTEMAIL.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$emailans.'</TD></TR>'
	.'<TR><TD WIDTH=100 VALIGN="TOP">'._POSTDATE.'</TD><TD BGCOLOR=#EEEEEE VALIGN="TOP">'.$date2.'</TD></TR>'
	.'<TR><TD colspan="2" align=right>'._DELCOMMENT.' <A HREF="javascript: if(confirm(\'ต้องการลบความคิดเห็น?\')) window.open(\'index.php?mod=News&amp;file=admin&amp;op=delete_ans&amp;idq='.$idq.'&amp;ida='.$ida.'\',\'_self\')"> <IMG SRC="images/global/delete.gif"  BORDER=0 ALT='._DELETE.'></A></TD></TR>'
	.'</TABLE>';
}

	echo '</fieldset>';	

	echo '<BR><fieldset><legend>'._ADDCOMMENT.'</legend>'
	.'<TABLE WIDTH="550" CELLPADDING=1 CELLSPACING=0 BORDER=0>'
	.'<FORM NAME="News" METHOD=POST ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="News">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="add_ans">'
	.'<INPUT TYPE="hidden" NAME="idq" VALUE="'.$idq.'">';

	echo '<TR><TD WIDTH=100 VALIGN="TOP">'._NEWSDESCRIPTION.' </TD><TD><TEXTAREA " NAME="newsdesc" ROWS="5" COLS="30" wrap="soft" style="width: 90%;"></TEXTAREA></TD></TR>';
	echo '<TR><TD WIDTH=100>'._POSTNAME.' <B>*</B></TD><TD><INPUT TYPE="text" NAME="postname" SIZE="20" VALUE=""></TD></TR>';
	echo '<TR><TD WIDTH=100>'._POSTEMAIL.' </TD><TD><INPUT TYPE="text" NAME="postemail" SIZE="50" VALUE=""></TD></TR>';

	echo '<TR><TD WIDTH=100 VALIGN="TOP">&nbsp;<TD><BR><INPUT class="button" TYPE="button"  VALUE="'. _ADD. '" onclick="formSubmit()"> ';
	echo "<INPUT class=\"button\" TYPE=\"button\" VALUE=\"". _CANCEL. "\" onclick=\"javascript:window.open('index.php?mod=News&amp;file=admin','_self')\">";
	echo '</TABLE>'
	.'</fieldset>';
	
	include 'footer.php';	
}

//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

function addAns($vars)  {

	 // Get arguments from argument array
    	extract($vars);

	$time=time();

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	
	$newsanstable = $lntable['news_ans'];
	$newsanscolumn = &$lntable['news_ans_column'];
	$query = "INSERT INTO $newsanstable
				  (	$newsanscolumn[idq],
					$newsanscolumn[detailans],
					$newsanscolumn[nameans],
					$newsanscolumn[emailans],
					$newsanscolumn[dateans]
				  )
					VALUES ('$idq',
						  '$newsdesc',
						  '$postname',
  						  '$postemail',
						  '$time'
					)";
	$dbconn->Execute($query);
	
	 if ($dbconn->ErrorNo() != 0) {
	    echo "error";
        return false;
    } 
	else {

	global $menus,$links;

	/** Navigator **/
	$menus[]= _ADDNEWS;
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/


	echo '<BR><fieldset><legend>'._ADDNEWS.'</legend>'
	.'<TABLE WIDTH="550" HEIGHT="250" CELLPADDING=1 CELLSPACING=0 BORDER=0>'
	.'<TR><TD align=center>'._RETURNMESS.'</TD></TR>'
	.'</TABLE>'
	.'</fieldset>';		


	}
	include 'footer.php';
}

//\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\

function delQues($idq)
{

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	$newstable = $lntable['news'];
	$newscolumn = &$lntable['news_column'];
	$newsanstable = $lntable['news_ans'];
	$newsanscolumn = &$lntable['news_ans_column'];

	// delete news
	$dbconn->Execute("DELETE FROM $newstable WHERE $newscolumn[idq] = '$idq'");

	$dbconn->Execute("DELETE FROM $newsanstable WHERE  $newsanscolumn[idq] = '$idq'");

}

//-+-+-+-++--++-+-+-+-+-+-+-+-+-+-+-++--++-+-+-+-+-+-+-+-+-+-+-++--++-+-+-+-+-+-+-+-+-+-+-++--++-+-+-+-+-+-+-+-+-

function delAns($idq,$ida)
{

	list($dbconn) = lnDBGetConn();
    $lntable = lnDBGetTables();

	$newstable = $lntable['news'];
	$newscolumn = &$lntable['news_column'];
	$newsanstable = $lntable['news_ans'];
	$newsanscolumn = &$lntable['news_ans_column'];

	$dbconn->Execute("DELETE FROM $newsanstable WHERE  $newsanscolumn[ida] = '$ida' and $newsanscolumn[idq] = '$idq'");

	 if ($dbconn->ErrorNo() != 0) {
	    echo "error";
        return false;
    } 
	else {

	global $menus,$links;

	/** Navigator **/
	$menus[]= _DELHEAD;
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/


	echo '<BR><fieldset><legend>'._DELHEAD.'</legend>'
	.'<TABLE WIDTH="550" HEIGHT="250" CELLPADDING=1 CELLSPACING=0 BORDER=0>'
	.'<TR><TD align=center>'._DELMESS.' <BR><BR><A HREF="index.php?mod=News&file=admin&op=add_aform&idq='.$idq.'"> คลิกที่นี่ </A> เพื่อกลับไปดูข้อความอื่น</TD></TR>'
	.'</TABLE>'
	.'</fieldset>';		


	}
	include 'footer.php';


}

?>
