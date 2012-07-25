<?php
/**
*  Course submission
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */
include 'header.php';

$vars= array_merge($_GET,$_POST);

/** Navigator **/
$menus= array(_ADMINMENU,_UPGRADEADMIN,_UPDATE_ADMIN);
$links=array('index.php?mod=Admin','index.php?mod=Upgrade&amp;file=admin','index.php?mod=Upgrade&amp;file=update');
lnBlockNav($menus,$links);
/** Navigator **/


OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Upgrade&amp;file=update"><B>'._UPDATE_ADMIN.'</B></A><BR>';

echo '<form enctype="multipart/form-data"  action="index.php?mod=Upgrade&file=update" method="post">'
	.'<br>choose file script : <INPUT TYPE="file" NAME="fileimport"><br>'
	.'<BR><INPUT TYPE="submit" VALUE="   Submit   " class="button_org">'
	.'<input type="hidden" name="op" value="Script"><br></form>';

echo '<br><b>Result<b><br><Textarea rows="8" cols="80">';

if($_POST['op'] == "Script")
{
	$lnpath =  substr( $_SERVER['SCRIPT_FILENAME'],0,strlen($_SERVER['SCRIPT_FILENAME'])-10)."/modules/Upgrade/temp";

	if(!is_dir($lnpath.'/'))
	    
	{
		@mkdir($lnpath.'/');
	
	}
	else
	{
		deleteDirectory($lnpath.'/',false);
		@mkdir($lnpath.'/');
	}
	@move_uploaded_file($_FILES['fileimport']['tmp_name'],$lnpath.'/'.$_FILES['fileimport']['name']);
	$error = unzip($lnpath.'/',basename($_FILES['fileimport']['name'],'.zip'),1,$lnpath.'/');
	echo $error;
	if(basename($_FILES['fileimport']['name'],'.zip')=="update")
		run_script("update",$lnpath.'/'.basename($_FILES['fileimport']['name'],'.zip'));
	else
		echo "Error File Update";

	deleteDirectory($lnpath.'/',false);
}
echo '</Textarea></td></td></tr>';
CloseTable();
if($_POST['op'] == "Script")
{
	echo '<br><br><b>Update Completed<br></b>';
}

/**
 * Removes the directory and all its contents.
 * 
 * @param string the directory name to remove
 * @param boolean whether to just empty the given directory, without deleting the given directory.
 * @return boolean True/False whether the directory was deleted.
 */
function deleteDirectory($dirname,$only_empty=false) {
    if (!is_dir($dirname))
        return false;
    $dscan = array(realpath($dirname));
    $darr = array();
    while (!empty($dscan)) {
        $dcur = array_pop($dscan);
        $darr[] = $dcur;
        if ($d=opendir($dcur)) {
            while ($f=readdir($d)) {
                if ($f=='.' || $f=='..')
                    continue;
                $f=$dcur.'/'.$f;
                if (is_dir($f))
                    $dscan[] = $f;
                else
                    unlink($f);
            }
            closedir($d);
        }
    }
    $i_until = ($only_empty)? 1 : 0;
    for ($i=count($darr)-1; $i>=$i_until; $i--) {
        echo "\nClear Temp directory '".$darr[$i]."' ... ";
        if (@rmdir($darr[$i]))
            echo "ok";
        else
            echo "FAIL";
    }
    return (($only_empty)? (count(scandir)<=2) : (!is_dir($dirname)));
}


function unzip($dir, $file, $verbose = 0,$dir2) 
{
	$dir_path = "$dir2$file";
	$zip_path = "$dir$file.zip";
	if(!is_dir($dir_path.'/'))
	    
	{
		@rmdir($dir_path.'/');
	
	}
	$ERROR_MSGS[0] = "Extract OK";
	$ERROR_MSGS[1] = "Zip path $zip_path doesn't exists.";
	$ERROR_MSGS[2] = "Directory $dir_path for unzip the pack already exists, impossible continue.";
	$ERROR_MSGS[3] = "Error while opening the $zip_path file.";
   
	$ERROR = 0;
	if(file_exists($zip_path)) 
	{	
		if(!is_dir($dir_path."/")) 
		{  
			@mkdir($dir_path."/");
			if ($link = zip_open($zip_path)) 
			{   
				
				while (($zip_entry = zip_read($link)) && (!$ERROR)) 
				{
					if (zip_entry_open($link, $zip_entry, "r")) 
					{        
						$dir_name = dirname(zip_entry_name($zip_entry));
						 $name = zip_entry_name($zip_entry);                
						 if ($name[strlen($name)-1] == '/') 
						 {                       
							$base = "$dir_path/";
							foreach ( explode("/", $name) as $k) 
							{                         
								$base .= "$k/";                           
								echo $base;
                       
								if (!file_exists($base))
									@mkdir($base);                           
							}                           
						}
						else 
						{                  
							$data = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
							$name = $dir_path."/".$name;                      
							if ($verbose)
								"extracting: $name<br>";                       
							$stream = fopen($name, "w");
							fwrite($stream, $data);                  
						}                 
						zip_entry_close($zip_entry);                
					}		
					else
						$ERROR = 4;     
				}         
				zip_close($link);             
			}		
			else
				$ERROR = 3;
		}
	       else 
		 $ERROR = 2;
	}
	else 
		$ERROR = 1;
	rmdir($dir_path);
	
	return $ERROR_MSGS[$ERROR];        
	
}    

function run_script($script,$pathscript)
{
	if($script=="update")
		include($pathscript.'/script.php');
}

include 'footer.php';
?>