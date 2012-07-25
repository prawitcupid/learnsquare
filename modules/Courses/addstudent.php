<?php

if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

$vars= array_merge($_GET,$_POST);	

//print_r($vars);
/* options */
switch ($op) {
	case "finduser" :					finduser($vars);break;
	case "adduser" :					adduser($vars);break;
	case "deluser" :					deluser($vars);break;

	default :	userlist($vars);
}




function userlist($vars) 
{	
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();	
	 extract($vars);
	
	//echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Courses&file=addstudent"><B>'._USERADMIN.'</B></A>';
	echo "<table width=\"80%\" border=\"0\"><tr><td>&nbsp;</td></tr><tr align=\"center\"><td>";
	echo '<FORM METHOD="POST" ACTION="index.php">'."\n"
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'."\n"
	.'<INPUT TYPE="hidden" NAME="file" VALUE="addstudent">'."\n"
	.'<INPUT TYPE="hidden" NAME="op" VALUE="finduser">'."\n"
	.'<INPUT TYPE="hidden" NAME="instructor" VALUE="'.$instructor.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="start" VALUE="'.$start.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="enroll" VALUE="'.$enroll.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="sid" VALUE="'.$sid.'">'."\n"
	
	._SEARCH.': <INPUT  class="input" TYPE="text" NAME="word" SIZE="20">&nbsp;'."\n"
	.'<SELECT class="select" NAME="field">'."\n"
	.'<OPTION VALUE="uname">'._NICKNAME.'</OPTION>'."\n"
	.'<OPTION VALUE="name">'._NAME.'</OPTION>'."\n"
	.'<OPTION VALUE="uno">'._UNO.'</OPTION>'."\n"
	.'<OPTION VALUE="email">'._EMAIL.'</OPTION>'."\n"
	.'</SELECT>'."\n"
	.' <INPUT class="button_org" TYPE="submit" VALUE="'._SUBMITFIND.'">'."\n"
	.'</FORM></td></tr></table>';

	
}
?>
<script language="javascript">
function checkAllBox(obj)
{
  var theForm = obj.form;
  var i;
  if(obj.checked){
   for(i=1;i<theForm.length; i++)
   {
     theForm[i].checked = true;
   }
  }else if(!obj.checked){
   for(i=1;i<theForm.length; i++)
   {
     theForm[i].checked = false;
   }
  }
}
</script>
<?php
function finduser($vars)
{
	include 'header.php';
	 extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();	
	$userstable = $lntable['users'];
	$userscolumn = &$lntable['users_column'];
	
	$membershiptable = $lntable['group_membership'];
	$membershipcolumn = &$lntable['group_membership_column'];
	
	$coursestable = $lntable['courses'];
	$coursescolumn = &$lntable['courses_column'];
	
	$submissionstable = $lntable['course_submissions'];
	$submissionscolumn = &$lntable['course_submissions_column'];
	
	/** Navigator **/
	$menus = $links = array();
	if (lnUserAdmin( lnSessionGetVar('uid'))) {
		$menus[] = _ADMINMENU;
		$links[]='index.php?mod=Admin';
	}
	
	$menus[]= _ADDCOURSE;
	$links[]= 'index.php?mod=Courses&file=admin';
	/** Navigator **/
	lnBlockNav($menus,$links);
	OpenTable();
	 echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <B>'._USERSEARCH.'</B><BR>&nbsp;';
	 echo '<center><FORM METHOD=POST ACTION="index.php">' ."\n"
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'."\n"
	.'<INPUT TYPE="hidden" NAME="file" VALUE="addstudent">'."\n"
	.'<INPUT TYPE="hidden" NAME="op" VALUE="finduser">'."\n"
	.'<INPUT TYPE="hidden" NAME="instructor" VALUE="'.$instructor.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="start" VALUE="'.$start.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="enroll" VALUE="'.$enroll.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="sid" VALUE="'.$sid.'">'."\n"
	._SEARCH.': <INPUT  class="input" TYPE="text" NAME="word" SIZE="20">&nbsp;'."\n"
	.'<SELECT class="select" NAME="field">'."\n"
	.'<OPTION VALUE="uname">'._NICKNAME.'</OPTION>'."\n"
	.'<OPTION VALUE="name">'._NAME.'</OPTION>'."\n"
	.'<OPTION VALUE="uno">'._UNO.'</OPTION>'."\n"
	.'<OPTION VALUE="email">'._EMAIL.'</OPTION>'."\n"
	.'</SELECT>'."\n"
	.' <INPUT class="button_org" TYPE="submit" VALUE="'._SUBMITFIND.'">'."\n"
	.'</FORM></center>';

	 list($word,$field) = lnVarCleanFromInput('word','field');
	
	// Search
	$eval_cmd = "\$usersfindcolumn=\$userscolumn[$field];";
	@eval($eval_cmd); 
	$sql = "SELECT $userscolumn[uid],$userscolumn[name],$userscolumn[uname],$userscolumn[email],
		$userscolumn[regdate],$userscolumn[phone],$userscolumn[uno],$userscolumn[news] ,$userscolumn[active] 
		 FROM $userstable 
		 WHERE $usersfindcolumn LIKE '%".lnVarPrepForStore($word)."%'  
		 and $userscolumn[uid] in (select $membershipcolumn[uid] from $membershiptable 
		 where $membershipcolumn[gid] = '4')";
	$result = $dbconn->Execute($sql);
		//echo "<BR>"._SEARCH." &nbsp;'<B>$word</B>'<BR>";
		if ( $result->PO_RecordCount() == 0) {
			 echo '<BR><BR>'._SEARCH. '&nbsp; <B>'. $word. '</B>&nbsp;'._NOTFOUND;
		}
		else {
			echo '<FORM NAME="theform" METHOD="POST" ACTION="index.php">'."\n"
				.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'."\n"
				.'<INPUT TYPE="hidden" NAME="file" VALUE="addstudent">'."\n"
				.'<INPUT TYPE="hidden" NAME="instructor" VALUE="'.$instructor.'">'."\n"
				.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'."\n"
				.'<INPUT TYPE="hidden" NAME="start" VALUE="'.$start.'">'."\n"
				.'<INPUT TYPE="hidden" NAME="enroll" VALUE="'.$enroll.'">'."\n"
				.'<INPUT TYPE="hidden" NAME="sid" VALUE="'.$sid.'">'."\n"
				.'<INPUT TYPE="hidden" NAME="op" VALUE="adduser">'."\n";
			echo "<br><B>"._USERLIST."</B><br>";
			echo '<BR><table width="100%" cellpadding=3 cellspacing=1 bgcolor=#d3d3d3>'."\n"
			.'<tr align=center bgcolor=#808080><td class="head">No.</td><td class="head">'._NICKNAME.'</td><td class="head">'._NAME.'</td><td class="head">'._UNO.'</td><td class="head">'._USERSELECT.'<input type="checkbox" name="checkall" onclick="checkAllBox(this)" /></td></tr>'."\n";
			 for ($i=1; list($uid,$name,$uname,$email,$regdate,$phone,$uno,$news,$active) = $result->fields; $i++) 
			 {
					 $result->MoveNext();
					 echo "<tr bgcolor=#FFFFFF><td width=25>$i</td><td width=100>$uname</td><td>$name</td><td width=80>$uno</td><td align=center width=80>"."\n";
					 echo '<INPUT TYPE="checkbox" NAME="uid[]" value="'.$uid.'" >'."\n";
					 echo "</td></tr>\n";
			}
			echo "</table>\n";
			echo '<INPUT class="button_org" TYPE="submit" VALUE="'._SUBMITFIND.'"></FORM>'."\n";
			
		}
		echo "<hr>";
		echo "<br><B>"._USERSUBMISSION."</B><br>";
		$course_enrollsstable = $lntable['course_enrolls'];
		$course_enrollscolumn = &$lntable['course_enrolls_column'];
		$sql = "SELECT $course_enrollscolumn[eid],$userscolumn[uid],$userscolumn[name],$userscolumn[uname],$userscolumn[email],
		$userscolumn[regdate],$userscolumn[phone],$userscolumn[uno],$userscolumn[news] ,$userscolumn[active] 
		 FROM $userstable inner join $course_enrollsstable on  $userscolumn[uid] = $course_enrollscolumn[uid] where $course_enrollscolumn[sid] = '" .$sid . "'"  ;
		 
		$result = $dbconn->Execute($sql);
		
		echo '<BR><table width="100%" cellpadding=3 cellspacing=1 bgcolor=#d3d3d3>'."\n"
		.'<tr align=center bgcolor=#808080><td class="head">No.</td><td class="head">'._NICKNAME.'</td><td class="head">'._NAME.'</td><td class="head">'._UNO.'</td><td class="head">&nbsp;</td></tr>'."\n";
		 for ($i=1; list($eid,$uid,$name,$uname,$email,$regdate,$phone,$uno,$news,$active) = $result->fields; $i++) 
		 {
				 $result->MoveNext();
				 echo "<tr bgcolor=#FFFFFF><td width=25>$i</td><td width=100>$uname</td><td>$name</td><td width=80>$uno</td><td align=center width=80>"."\n";
				 echo '<a href="javascript:if(confirm(\'Delete Student?\')) document.forms.delusers'.$i.'.submit();"><img width="14" height="16" border="0" alt="ลบ" src="images/global/delete.gif"/></a>'."\n" ;
				 echo '<FORM NAME="delusers'.$i.'" METHOD="GET" ACTION="index.php">'.""
				.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'."\n"
				.'<INPUT TYPE="hidden" NAME="file" VALUE="addstudent">'."\n"
				.'<INPUT TYPE="hidden" NAME="instructor" VALUE="'.$instructor.'">'."\n"
				.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'."\n"
				.'<INPUT TYPE="hidden" NAME="start" VALUE="'.$start.'">'."\n"
				.'<INPUT TYPE="hidden" NAME="enroll" VALUE="'.$enroll.'">'."\n"
				.'<INPUT TYPE="hidden" NAME="sid" VALUE="'.$sid.'">'."\n"
				.'<INPUT TYPE="hidden" NAME="op" VALUE="deluser">'."\n"
				 .'<INPUT TYPE="hidden" NAME="eid" VALUE="'.$eid.'">'."\n"
				 .'<INPUT TYPE="hidden" NAME="uid" VALUE="'.$uid.'">'."\n</form>";
				 echo "</td></tr>\n";
		}
		echo "</table>\n";
		
			echo '<FORM METHOD=POST ACTION="index.php">'."\n"
			.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'."\n"
			.'<INPUT TYPE="hidden" NAME="op" VALUE="schedule">'."\n"
			.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'."\n"
			.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'."\n";
			echo '<INPUT class="button_org" TYPE="submit" VALUE="'._RETURNMAIN.'"></FORM>'."\n";
			
	CloseTable();
	
	return;

}
function adduser($vars)
{
	include 'header.php';
	/** Navigator **/
	$menus = $links = array();
	if (lnUserAdmin( lnSessionGetVar('uid'))) {
		$menus[] = _ADMINMENU;
		$links[]='index.php?mod=Admin';
	}
	
	$menus[]= _ADDCOURSE;
	$links[]= 'index.php?mod=Courses&file=admin';
	/** Navigator **/
	lnBlockNav($menus,$links);
	extract($vars);
	if (isset($uid)) 
	{

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$course_enrollsstable = $lntable['course_enrolls'];
		$course_enrollscolumn = &$lntable['course_enrolls_column'];

		$course_submissionstable = $lntable['course_submissions'];
		$course_submissionscolumn = &$lntable['course_submissions_column'];
		
		$enroll = $enroll =='' ? 0:$enroll;
		list($d,$m,$y) = explode('-',$start);
		$start = "$y-$m-$d";
		
		for($i=0;$i<= count($uid) - 1;$i++ )
		{
			$sql = "INSERT INTO ". $course_enrollsstable ." ( ".$course_enrollscolumn['eid']." , ".$course_enrollscolumn['sid']." , ".$course_enrollscolumn['gid']." , ".$course_enrollscolumn['uid']." , ".$course_enrollscolumn['options']." , ".$course_enrollscolumn['status']." , ".$course_enrollscolumn['mentor']." , ".$course_enrollscolumn['start']." )
				VALUES (
				NULL , '".$sid."', '0', '".$uid[$i]."', '1', '1', '".$instructor."', '".$start."'
				)";
				//echo $sql;
			$dbconn->Execute($sql);
		}
		$sql= "SELECT COUNT($course_enrollscolumn[eid]) FROM $course_enrollsstable WHERE $course_enrollscolumn[sid]=$sid";
		$result = $dbconn->Execute($sql);
		list($amountstd) = $result->fields;
		
		$sql= "UPDATE $course_submissionstable SET $course_submissionscolumn[amountstd] = $amountstd WHERE $course_submissionscolumn[sid]=$sid";
		$dbconn->Execute($sql);
		
		
	}
	echo '<FORM NAME="tmp" METHOD="POST" ACTION="index.php">'."\n"
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'."\n"
	.'<INPUT TYPE="hidden" NAME="file" VALUE="addstudent">'."\n"
	.'<INPUT TYPE="hidden" NAME="instructor" VALUE="'.$instructor.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="start" VALUE="'.$start.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="enroll" VALUE="'.$enroll.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="sid" VALUE="'.$sid.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="op" VALUE="finduser">'."\n"
	.'<INPUT TYPE="hidden" NAME="field" VALUE="uname">'."\n"
	.'<INPUT TYPE="hidden" NAME="word" VALUE=""></FORM>'."\n";
	echo "<script>  document.forms.tmp.submit(); </script>";
	/*$host  = $_SERVER['HTTP_HOST'];
	$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	$extra = "index.php?mod=Courses&file=addstudent&op=finduser&cid=".$cid."&sid=".$sid;
	header("Location: http://$host$uri/$extra");*/
	exit;
}

function deluser($vars)
{
	//include 'header.php';
	/** Navigator **/
	$menus = $links = array();
	if (lnUserAdmin( lnSessionGetVar('uid'))) {
		$menus[] = _ADMINMENU;
		$links[]='index.php?mod=Admin';
	}
	
	$menus[]= _ADDCOURSE;
	$links[]= 'index.php?mod=Courses&file=admin';
	/** Navigator **/
	lnBlockNav($menus,$links);
	extract($vars);

	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$course_enrollsstable = $lntable['course_enrolls'];
	$course_enrollscolumn = &$lntable['course_enrolls_column'];
	$sql = "delete from ". $course_enrollsstable ." where $course_enrollscolumn[eid] = '" .$eid . "' and $course_enrollscolumn[sid] = '" . $sid ."'" ;
	$dbconn->Execute($sql);

	echo '<FORM NAME="tmp" METHOD="POST" ACTION="index.php">'."\n"
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'."\n"
	.'<INPUT TYPE="hidden" NAME="file" VALUE="addstudent">'."\n"
	.'<INPUT TYPE="hidden" NAME="instructor" VALUE="'.$instructor.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="start" VALUE="'.$start.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="enroll" VALUE="'.$enroll.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="sid" VALUE="'.$sid.'">'."\n"
	.'<INPUT TYPE="hidden" NAME="op" VALUE="finduser">'."\n"
	.'<INPUT TYPE="hidden" NAME="field" VALUE="uname">'."\n"
	.'<INPUT TYPE="hidden" NAME="word" VALUE=""></FORM>'."\n";
	echo "<script>  document.forms.tmp.submit(); </script>";
	//$host  = $_SERVER['HTTP_HOST'];
	//$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
	//$extra = "index.php?mod=Courses&file=addstudent&op=finduser&cid=".$cid."&sid=".$sid . "&word=";
	//header("Location: javascript:document.forms.tmp.submit()");
	exit;
}
?>
