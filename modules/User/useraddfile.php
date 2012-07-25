<?php
/**
* Add user using data file
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Admin::', "$file::", ACCESS_ADMIN)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." to read '".$mod." module' </h1></CENTER>";
		return false;
}


/* Add users options */
if ($op == "savefromfile") {
		if (!lnSecAuthAction(0, 'User::', "$file::", ACCESS_ADD)) {
			echo "<CENTER><h1>"._NOAUTHORIZED." to add '".$mod." module'</h1></CENTER>";
			return false;
		}

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$table = $lntable['users'];
		$column = &$lntable['users_column'];
		$group_membershiptable = $lntable['group_membership'];
		$group_membershipcolumn = &$lntable['group_membership_column'];

		list($upfile,$textdel,$newLeft2) = lnVarCleanFromInput('upfile','textdel','newLeft2');
		
		// upload data file
		if ($_FILES['upfile']['name']) {
			$line=file($_FILES['upfile']['tmp_name']);
			if(count($line)==1){
				$msgupfile .= "<LI> Error, Invalid file format";
			}
			$fname = explode("$textdel",$line[0]); // extract data with text delimiter
			$uids=array();
			//echo "===".$_FILES['upfile']['tmp_name']."<br>line=".count($line)."<br>";exit();
			for ($i=1;$i<count($line);$i++) {
				// check exists user
				//echo ($i+1).">".$line[0].">>".$line[$i].">>>".$textdel."<br>";
				if (!($error = lnUserCheckDup($i+1,$line[0],$line[$i],$textdel))) {
					$next_uid=getNextUserID();
					$uids[]=$next_uid;
					
					// insert user
					$sql1 = "INSERT INTO $table ($column[uid]";
					$data = explode("$textdel",$line[$i]);
					$sql2 = "VALUES ('".lnVarPrepForStore($next_uid)."'";
					for ($j=0;$j<count($data);$j++) {
						//echo "name=".$fname[$j]."data=".$data[$j]."<br>";
						$sql1 .= ", ". $fname[$j];           // 'ln_' may change for support prefix database
						if ($fname[$j] == 'ln_pass') {
							$data[$j] = md5($data[$j]);
						}
						$sql2 .= ", '". $data[$j]. "'";
					}

					$regdate = time();
					$sql1 .= ", $column[regdate])  ";
					$sql2 .= ", '$regdate') ";
					$query = $sql1 . $sql2;
					//echo $query."<br>";
					$result = $dbconn->Execute($query);
					if ($dbconn->ErrorNo() <> 0) {
						echo "$query<br>". $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg() . "<br>";
						return;
					} 
					else {
						$msgupfile_done .= "<LI>".$data[0]. ' '. $data[1];
					}
				}
				else {
					$msgupfile .= "<LI>". $error;
				}
			}//exit();

			unlink($_FILES['upfile']['tmp_name']);

			lnUpdateUserEvent("Add user from file ".$_FILES['upfile']['name']);

			// Insert to group memship
			$groupList=explode(",",$newLeft2);
			for ($n=0; $n < count($uids); $n++) {
				for($m=0; $m < count($groupList); $m++) {
					$sql = "INSERT INTO $group_membershiptable VALUES ";
					$sql .=" (".$groupList[$m]. ",".$uids[$n].")";
					$result = $dbconn->Execute($sql);
					if ($dbconn->ErrorNo() <> 0) {
						echo "$query<br>". $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg() . "<br>";
						return;
					} 
				}
			}
		}
}


/* - - - - - - - - - - - */
include 'header.php';

/** Navigator **/
$menus= array(_ADMINMENU,_USERADMIN,_USERADDFILE);
$links=array('index.php?mod=Admin','index.php?mod=User&file=admin','index.php?mod=User&file=useraddfile');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=User&file=useraddfile"><B>'._USERADDFILE.'</B></A><BR><BR>'._USERADD_DESC2.'<BR>&nbsp;';

// Type 2
// Enable to add user from file
if (lnConfigGetVar('reg_allowfile')) {

// Select Groups
   list($dbconn) = lnDBGetConn();
   $lntable = lnDBGetTables();

   $groupstable = $lntable['groups'];
   $groupscolumn = &$lntable['groups_column'];

   $default_group = lnConfigGetVar('default_group');
   $default_group_name = findGroupName($default_group);

	echo '<FORM METHOD=POST NAME="Register2" ACTION="index.php" enctype="multipart/form-data">'
	 .'<INPUT TYPE="hidden" NAME="mod" VALUE="User">'
	 .'<INPUT TYPE="hidden" NAME="file" VALUE="useraddfile">'
	 .'<INPUT TYPE="hidden" NAME="op" VALUE="savefromfile">'
	 .'<P><table width=500 cellpadding=2 cellspacing=0 border=0>'
	 .'<tr><td align="left" width="100">'._USERFILE.'</td>'
	 .'<td><INPUT TYPE="file" NAME="upfile"> (<A HREF="modules/User/sample/users.html" TARGET="_BLANK"><font color="red"><b>'._SAMPLE.'</b></font></A>)</td></tr>'
	 .'<tr><td align="left" width="100">'._TEXTDEL.'</td>'
	 .'<td><INPUT CLASS="input" TYPE="text" NAME="textdel" SIZE="2" VALUE=","></td></tr>';
 
		echo "<TR VALIGN=TOP>";
		echo '<TD ALIGN="left" width="100">'._SELECTGROUP.'</TD><TD>';
		echo '
		<INPUT TYPE="hidden" NAME="newLeft2">
		<TABLE BORDER=0>
		<TR>
			<TD>
			<SELECT CLASS="select" NAME="list1" MULTIPLE SIZE=9 onDblClick="opt2.transferRight()" style="width:150px">
			<OPTION VALUE="'.$default_group.'">'.$default_group_name.'</OPTION>
			</SELECT>
		</TD>
		<TD VALIGN=MIDDLE ALIGN=CENTER>
			<INPUT CLASS="button_white" TYPE="button" NAME="right" VALUE="&gt;&gt;" ONCLICK="opt2.transferRight()"><BR><BR>
			<INPUT CLASS="button_white" TYPE="button" NAME="right" VALUE="All &gt;&gt;" ONCLICK="opt2.transferAllRight()"><BR><BR>
			<INPUT CLASS="button_white" TYPE="button" NAME="left" VALUE="&lt;&lt;" ONCLICK="opt2.transferLeft()"><BR><BR>
			<INPUT CLASS="button_white" TYPE="button" NAME="left" VALUE="All &lt;&lt;" ONCLICK="opt2.transferAllLeft()">
		</TD>
		<TD>
				<SELECT CLASS="select_gray" NAME="list2" MULTIPLE SIZE=9 onDblClick="opt2.transferLeft()" style="width:150px">';
					  $result = $dbconn->Execute("SELECT  $groupscolumn[gid], $groupscolumn[name], $groupscolumn[description]  FROM $groupstable");
					   for ($i=1;list($gid, $name,$description) = $result->fields; $i++) {
							$result->MoveNext();
							if ($gid != $default_group) { 
								echo '<OPTION VALUE="'.$gid.'">'.$name.'</OPTION>';
							}
						}
		echo '
				</SELECT>
				</TD>
			</TR>
			</TABLE>

			</TD></TR>
			';

		  echo '<TR><TD>&nbsp;</TD>'
		 .'<TD><INPUT CLASS="button_org" TYPE="submit" VALUE="'._UPFILE.'"></TD></TR>'
		 .'</FORM></table><BR></center>';
		 if (!empty($msgupfile_done)) {
			echo "<HR><B>"._SAVEDONEMSG."</B>";
			 echo "<UL>";
			 echo $msgupfile_done;
			 echo "</UL></center>";
		 }
		 if (!empty($msgupfile)) {
			 echo "<HR>* * * <B><FONT COLOR=#FF0000>"._ERRUPLOAD."</FONT></B> * * *";
			 echo "<UL>";
			 echo $msgupfile;
			 echo "</UL>";
		}
}


CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */
?>