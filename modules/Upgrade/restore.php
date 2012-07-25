<?php
/**
*  Upgrade
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
$menus= array(_ADMINMENU,_UPGRADEADMIN);
$links=array('index.php?mod=Admin','index.php?mod=Upgrade&file=admin');
lnBlockNav($menus,$links);
/** Navigator **/


OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot  HREF="index.php?mod=Upgrade&amp;file=admin"><B>'._UPGRADEADMIN.'</B></A><BR>';

echo '<form enctype="multipart/form-data" action="index.php?mod=Upgrade&file=admin" method="post">'
	.'<br>choose file script : <INPUT TYPE="file" NAME="fileimport"><br>'
	.'<BR><INPUT TYPE="submit" VALUE="   Submit   " class="button_org">'
	.'<input type="hidden" name="op" value="Script"><br>';

echo '<input type="checkbox" name="chkbox_d" value="checked">content<br>'
	.'<input type="checkbox" name="chkbox_c" value="checked">database<br>'
	.'<input type="checkbox" name="chkbox_l" value="checked">learnsquare<br></form>';
	
CloseTable();

if($_POST['op'] == "Script")
{
	$path = $_SERVER["SCRIPT_FILENAME"];
	
	$path = substr($path,0,strlen($path)-10) ;
	if(!is_dir($path."/backup"))
	    
		@mkdir($path."/backup".'/');
	
	@move_uploaded_file($_FILES['fileimport']['tmp_name'],$path."/backup".'/'.$_FILES['fileimport']['name']);
//	$path_parts = pathinfo($path."/backup".'/'.$_FILES['fileimport']['name']);
//	$temp = explode(".".$path_parts["extension"],$path_parts["basename"]);

	//$error = unzip($path, "/"."$temp[0]", 1,'modules/Upgrade');
	$error = unzip($path.'/',basename($_FILES['fileimport']['name'],'.zip'),1,$path.'/');
	echo $error;
	
	if($temp[0]=="backup")
		run_script("backup",$path.'/');
	else if($temp[0]=="restore")
		run_script("restore",$path.'/');
	else if($temp[0]=="update")
		run_script("update",$path.'/');
	else if($temp[0]=="full_backup")
		run_script("full_backup",$lnpath.'/');

	deleteDir("modules/Upgrade/$temp[0]");
}



function RemoveDirectory($path){
   if(ClearDirectory($path)){
       if(rmdir($path)){
           return true;
// directory removed
       }else{
           return false;
// directory couldn?t removed
       }
   }else{
       return false;
// no empty directory
   }
}

function deleteDir($dir)
{
   if (substr($dir, strlen($dir)-1, 1) != '/')
       $dir .= '/';

   if ($handle = opendir($dir))
   {
       while ($obj = readdir($handle))
       {
           if ($obj != '.' && $obj != '..')
           {
               if (is_dir($dir.$obj))
               {
                   if (!deleteDir($dir.$obj))
                       return false;
               }
               elseif (is_file($dir.$obj))
               {
                   if (!unlink($dir.$obj))
                       return false;
               }
           }
       }

       closedir($handle);

       if (!@rmdir($dir))
           return false;
       return true;
   }
   return false;
} 


function unzip($dir, $file, $verbose = 0,$dir2) {

   $dir_path = "$dir2$file";
   $zip_path = "$dir$file.zip";

   
   $ERROR_MSGS[0] = "OK";
   $ERROR_MSGS[1] = "Zip path $zip_path doesn't exists.";
   $ERROR_MSGS[2] = "Directory $dir_path for unzip the pack already exists, impossible continue.";
   $ERROR_MSGS[3] = "Error while opening the $zip_path file.";
   
   $ERROR = 0;
   
   if (file_exists($zip_path)) {   
         if (!file_exists($dir_path)) {  
           mkdir($dir_path);            
         if (($link = zip_open($zip_path))) {  
			 
           while (($zip_entry = zip_read($link)) && (!$ERROR)) {
               if (zip_entry_open($link, $zip_entry, "r")) {          
                 $data = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                 $dir_name = dirname(zip_entry_name($zip_entry));
                 $name = zip_entry_name($zip_entry);                
                 if ($name[strlen($name)-1] == '/') {                       
                       $base = "$dir_path/";
                     foreach ( explode("/", $name) as $k) {                         
                       $base .= "$k/";                           
                       if (!file_exists($base))
                           mkdir($base);                           
                     }                           
                 }
                 else {                  
                     $name = "$dir_path/$name";                      
                     if ($verbose)
                       echo "";//"extracting: $name<br>";                       
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
             $ERROR = "3";
       }
       else 
         $ERROR = 2;
   }
   else 
       $ERROR = 1;
   //return $ERROR_MSGS[$ERROR];        
}    


function run_script($script,$pathscript)
{
	if($script=="backup")
		include('backup/script.php');
	else if($script=="restore")
		include('restore/script.php');
	else if($script=="update")
		include('update/script.php');
	else if($script=="full_backup")
		include('full_backup/script.php');
}

include 'footer.php';
?>