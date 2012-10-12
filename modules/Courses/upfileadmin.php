<?php
/**
* file administration
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}


// fixed
// max file size 
// by 	pukkapol.tan@nectec.or.th
// date	2012.10.12
$php_ini = ini_get_all();
define('MAX_FILESIZE',$php_ini['upload_max_filesize']);

/**
* upload files
*/
function upfile($vars) {
	global $menus, $links;
	
	// Get arguments from argument array
    extract($vars);
	
	if (preg_match("/\.\./i",@$coursepath)) {
		echo '<BR><BR><CENTER><B>Try to hack upper directory?</B></CENTER>';
		return;
	}
	
	switch (@$action) {
		case "upload_file" : uploadFiles($vars); break;
		case "delete_file" : deleteFiles($vars); break;
		case "create_folder" : createFolder($vars); break;
	}


	$temp_coursepath=@$coursepath;
	if (@$action  != "create_folder" && @$action  != "upload_file") {
		if (!empty($coursepath)) {
			$coursepath= COURSE_DIR . "/" .$cid."/".$coursepath;	
		}
		else {
			$coursepath= COURSE_DIR . "/" .$cid;
		}
	}

	$coursepath=str_replace('%20',' ',$coursepath);

	if (file_exists($coursepath)) {
		$d = dir($coursepath);
		$files = array();
		while (false !== ($entry = $d->read())) {
			if(substr($entry,0,1) != '.')  {
				$files[] = $entry;
			}
		}
		$d->close();
		sort($files);

		$paths=explode('/',$coursepath);
		$rpaths=$rpathtemp=array();
		for ($j=2;$j<count($paths);$j++) {
			$paths[$j]=str_replace(' ','%20',$paths[$j]);
			$rpaths[] =$paths[$j]; 
		}

		$rpathstemp = $rpaths;
		$rpath=join('/',$rpaths);
		array_pop($rpathstemp);
		$uppath=join('/',$rpathstemp);
	}

	/** Navigator **/
	$courseinfo = lnCourseGetVars($cid);
	$menus[]= $courseinfo['title'];
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/
	
	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';
	
	tabCourseAdmin($cid,5);

	echo '</TD></TR><TR><TD>';
		
	echo '<FORM method="post" ACTION="index.php">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="upfile">'
	.'<INPUT TYPE="hidden" NAME="action" VALUE="create_folder">'
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<input type="hidden" name="coursepath" value="'.$coursepath.'">';

	echo '<table width= 100% class="main" cellpadding=0 cellspacing=0  border=0>';

	echo '<tr><td align="center" valign="top">';

	echo '<BR>'
	.'<TABLE width=97% cellpadding=0 cellspacing=1 border=0>'
	.'<TR VALIGN="TOP">'
	.'<TD ALIGN="LEFT" VALIGN="TOP">'
	.'New Folder : <INPUT TYPE="text" NAME="folder_name"> <INPUT class="button" TYPE="submit" VALUE="Create"><BR>&nbsp;'
	.'</TD></TR></FORM>'
	.'</TABLE>';

	echo '<fieldset><legend>Upload file(s)</legend>'
	.'<TABLE WIDTH=97% cellpadding=0 cellspacing=0 cellspacing=0  border=0>'

	.'<FORM method="post" ACTION="index.php" enctype="multipart/form-data">'
	.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
	.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
	.'<INPUT TYPE="hidden" NAME="op" VALUE="upfile">'
	.'<INPUT TYPE="hidden" NAME="action" VALUE="upload_file">'
	.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
	.'<input type="hidden" name="coursepath" value="'.$coursepath.'">'
	.'<input type="hidden" name="MAX_FILE_SIZE" value="'.MAX_FILESIZE.'">'

	.'<TR VALIGN="TOP">'
	.'<TD ALIGN="CENTER" VALIGN="TOP"><BR>'
	.'1.<input type="file" name="upfile[0]"><BR>'
	.'2.<input type="file" name="upfile[1]"><BR>'
	.'3.<input type="file" name="upfile[2]"><BR>'
	.'4.<input type="file" name="upfile[3]"><BR>'
	.'5.<input type="file" name="upfile[4]"><BR><BR>'
	.'</TD>'
	.'<TD ALIGN="CENTER" VALIGN="TOP"><BR>'
	.'6.<input type="file" name="upfile[5]"><BR>'
	.'7.<input type="file" name="upfile[6]"><BR>'
	.'8.<input type="file" name="upfile[7]"><BR>'
	.'9.<input type="file" name="upfile[8]"><BR>'
	.'10.<input type="file" name="upfile[9]"><BR><BR>'
	.'</TD></TR>'
	.'<TR><TD HEIGHT="28" F COLSPAN=2 ALIGN="CENTER" VALIGN="MIDDLE">'
	.'<input style=" cursor: hand" type="submit" value="'._UPLOAD.'"></TD>'
	.'</TR></FORM>'
	.'</TABLE></fieldset><BR>';


	// List file(s)
	?>
	<script language="Javascript" type="text/javascript">
	function select_switch()
	{
		for (i = 0; i < document.file_list.length; i++)
		{
			if (document.file_list.selectall.checked == false) {
				document.file_list.elements[i].checked = false;
			}
			else {
				document.file_list.elements[i].checked = true;
			}
		}
	}
	</script>
	<?
		echo '<BR><table class="list" cellpadding=0 cellspacing=1 border=0 width=97%>';
		echo '<tr height=20 valign=middle><td width=20 bgcolor=#CCCCCC align=center><IMG SRC="images/ext/folder_small.gif" WIDTH="16" HEIGHT="16" BORDER=0 ALT=""></td><td  class="head">&nbsp;<B>/'.str_replace('%20',' ',@$rpath);
		echo "</B></td></tr></table>";

		echo '<table cellpadding=0 cellspacing=1 border=0 bgcolor=#CCCCCC width=97%>';
		if (count(@$files)==0) {
			echo '<tr>';
			echo '<td width=100% align=left height=25 bgcolor="#FFFFFF">';
			echo "&nbsp;<A HREF=\"index.php?mod=Courses&file=admin&op=upfile&cid=$cid&coursepath=".@$uppath."\"><IMG SRC=images/ext/up.gif WIDTH=16 HEIGHT=18 BORDER=0 ALT=UP align=absmiddle> ..</A>";	
			echo "</td></tr>";	
		}
		$q=0;
		if(count(@$paths)<= 2)
			$r = 4;
		else
			$r=3;
		$newrow = 1;
		for ($head=1,$i=0; $i < count(@$files); $i++) {
				$q++;
				$entry=$files[$i];
				echo '<FORM NAME="file_list" method="post" action="index.php">'
				.'<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">'
				.'<INPUT TYPE="hidden" NAME="file" VALUE="admin">'
				.'<INPUT TYPE="hidden" NAME="op" VALUE="upfile">'
				.'<INPUT TYPE="hidden" NAME="action" VALUE="delete_file">'
				.'<INPUT TYPE="hidden" NAME="cid" VALUE="'.$cid.'">'
				.'<INPUT TYPE="hidden" NAME="coursepath" VALUE="'.$temp_coursepath.'">';

				if ($newrow == 1 ){ 
					echo "<tr valign=middle bgcolor=#FFFFFF height=20>";
					$newrow = 0;
				}
				
				if (count($paths) > 2 && $head==1) {
					echo '<td width=10% height=25 bgcolor="#FFFFFF" colspan=2>';
					echo "&nbsp;<A HREF=\"index.php?mod=Courses&file=admin&op=upfile&cid=$cid&coursepath=".$uppath."\"><IMG SRC=images/ext/up.gif WIDTH=16 HEIGHT=18 BORDER=0 ALT=UP align=absmiddle> ..</A>";
					echo "</td>";
					$head = 0;
				}
				
				
				if (is_dir($coursepath.'/'.$entry)) {
						echo '<td width=1><INPUT TYPE="checkbox" NAME="delfiles['.$i.']" VALUE="'.$entry.'"></td>';
						if (count($rpaths)) {
							$nextdir = $rpath.'/'.$entry;
						}
						else {
							$nextdir = $entry;
						}
						$nextdir=str_replace(' ','%20',$nextdir);

					echo "<td width=25%>&nbsp;<A HREF=index.php?mod=Courses&file=admin&op=upfile&cid=$cid&coursepath=$nextdir><img src=images/ext/folder_small.gif border=0 align=absmiddle> $entry</A></td>";
				}
				else 
				{
					echo '<td width=1><INPUT TYPE="checkbox" NAME="delfiles['.$i.']" VALUE="'.$entry.'"></td>';
					if (strlen($entry) > 17) {
						$entry_show=substr($entry,0,17).'...';
					}
					else {
						$entry_show=$entry;
					}
					echo "<td width=25%> &nbsp;<A HREF=$coursepath/$entry target=_blank>".$entry_show."</A></td>";
				}
				
				
				
				if ($q == $r)
				{
					$newrow =1; 
					$q=0;
					$r = 4;
					echo "</tr>";
				}		
		}
		
		if ($i > 0) {
			// fill <td>
			if ($i%4 != 0) {
				$span = 2*(4 - ($i%4));
				$pspan=$span/2*25;
				echo "<td bgcolor=#FFFFFF colspan=$span width=$pspan%></td>";
				echo '</tr>';
			}
			echo '<tr><td colspan=8 height=25 align=left bgcolor=#DDDDDD>';
			echo "<INPUT TYPE=checkbox NAME=selectall Onclick=\"javascript:select_switch()\"  title='Select or de-select all messages'>&nbsp;";
			echo '<INPUT CLASS="button" TYPE="submit" VALUE="Delete">';
			echo '</td></tr>';
		}

	echo '</table>';
	echo '</FORM>';
	
	echo '</td></tr></table>';

	echo '</TD></TR></TABLE>';

	include 'footer.php';
}


/**
* upload file
*/
function uploadFiles($vars) {
	// Get arguments from argument array
    extract($vars);
	
	if (empty($coursepath)) {
		$coursepath= COURSE_DIR . "/" .$cid;
	}

	for ($i=0; $i < 10; $i++) {
		
		if ($_FILES['upfile']['name'][$i]) {
			 if (!file_exists($coursepath."/".$_FILES['upfile']['name'][$i])) 
			 {
				// if (copy($_FILES['upfile']['tmp_name'][$i], $coursepath."/" . $_FILES['upfile']['name'][$i])) 
				
				if(move_uploaded_file($_FILES['upfile']['tmp_name'][$i], $coursepath."/" . $_FILES['upfile']['name'][$i]))
				{
					$path_parts = pathinfo($_FILES['upfile']['name'][$i]);
					if($path_parts['extension'] == 'php')
					{
						echo "can't upload file php";
						continue;
					}
					if($path_parts['extension'] == 'zip')
					{
						include_once('modules/SCORM/classes/pclzip.lib.php');			
							//Deletes file in TMP directory
						
						$archive = new PclZip($coursepath."/" . $_FILES['upfile']['name'][$i]);

						if ($archive->extract(PCLZIP_OPT_PATH,	$coursepath,PCLZIP_CB_PRE_EXTRACT,'preImportCallBack') == 0) 
						{
							//echo 'Cannot extract to $import_path';
							//clr_dir($import_path);
							//exit;
						}
						unset($archive);
						unlink($coursepath."/" . $_FILES['upfile']['name'][$i]);


					}
					unlink($_FILES['upfile']['tmp_name'][$i]);
						 
				 }
				else 
				{
						echo "Your file could not be copied.\n";
				}
			}
		}
	}

}


/**
* delete files
*/
function deleteFiles($vars) {
	// Get arguments from argument array
    extract($vars);
	
	foreach ($delfiles as $val) {
		if(trim($coursepath) != '')
			$deletefile= COURSE_DIR . '/' .$cid.'/'.$coursepath.'/'.$val;
		else
			$deletefile= COURSE_DIR . '/' .$cid.'/'.$val;
		
		if (is_dir($deletefile)) {
			//rmdir($deletefile);			
			remove_directory($deletefile .'/');
		}
		else 
		{
			@unlink($deletefile);
		}
	}
}

function remove_directory($dir) {
       $dir_contents = scandir($dir);
       foreach ($dir_contents as $item) {
           if (is_dir($dir.$item) && $item != '.' && $item != '..') {
               remove_directory($dir.$item.'/');
           }
           elseif (file_exists($dir.$item) && $item != '.' && $item != '..') {
               unlink($dir.$item);
           }
       }
       rmdir($dir);
   } 

/**
* create new folder
*/
function createFolder($vars) {
	// Get arguments from argument array
    extract($vars);
	
	if (!empty($folder_name)) {
		$newFolder = $coursepath.'/'.$folder_name;
		@mkdir($newFolder,0755);
	}
}
?>
