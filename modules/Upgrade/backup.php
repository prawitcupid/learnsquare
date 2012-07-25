<?php
/**
 *  Upgrade
 create by : pure

 last edit :-----
 programmer : bas
 date : 23-06-49
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
$menus= array(_ADMINMENU,_UPGRADEADMIN,_BACKUP_RESTORE_ADMIN);
$links=array('index.php?mod=Admin','index.php?mod=Upgrade&amp;file=admin','index.php?mod=Upgrade&amp;file=backup');
lnBlockNav($menus,$links);
/** Navigator **/
set_time_limit(0);
require_once('modules/SCORM/classes/pclzip.lib.php');

$backup_completed = "";
$restore_completed ="";


OpenTable();

echo '<IMG SRC="images/global/linkicon.gif" WIDTH="9" HEIGHT="9" BORDER=0 ALT=""> <A class=dot HREF="index.php?mod=Upgrade&amp;file=backup"><B>'._BACKUP_RESTORE_ADMIN.'</B></A><BR>';


echo //'<tr>'
//.'<td>'
	'<form action="index.php?mod=Upgrade&amp;file=backup" method="post">'
	.'<b>Backup</b><br>'
	//.'</td>'
	//.'<td>'

	.'<input type="checkbox" name="chkbox_c" value="yes" checked="checked">  Content  <br>'
	.'<input type="checkbox" name="chkbox_d" value="yes" checked="checked">  Database <br>'
	.'<input type="checkbox" name="chkbox_l" value="yes" checked="checked">  System   <br>'
	.'<input type="hidden"   name="op"       value="backup">'
	.'<input type="submit"   name="submit"   value="   Submit   "></form>';
	//.'</td>'
	//.'</tr>';

	echo '<br><br>';

	echo '<form enctype="multipart/form-data" action="index.php?mod=Upgrade&amp;file=backup" method="post">'
	//.'<tr>'
	//.'<td>'
	.'<b>Restore</b><br>'
	//.'</td>'
	//.'<td>'
	.'<input type="file" name="fileimport" id = "fileimport">'
	.'<input type="hidden" name="op" value="restore"><br>'
	.'<input type="submit" name="submit2" value="   Submit   "></form>';
	//.'</td>'
	//.'</tr>';

	echo '<br><b>Result<b><br><Textarea rows="6" cols="85" name="txtarea_log">';



	if($_POST['op'] == "backup")
	{
		$zipname = "";
		$val = $dbconn->GetOne("SELECT current_time( )");
		$t = explode(":",$val);
		if($chkbox_c=="yes")
		{
			include('backup_content.php');
			$zipname .="C";
		}
		if($chkbox_d=="yes")
		{
			include('backup_db.php');
			$zipname .="D";
		}
		if($chkbox_l=="yes")
		{
			include('backup_system.php');
			$zipname .="S";
		}

		$zipname = date("y-m-d")."_".$t[0]."-".$t[1]."-".$t[2]."_".$zipname.".zip";
		//$path =  $_SERVER["PATH_TRANSLATED"];
		$path = $_SERVER["SCRIPT_FILENAME"];
		$path = substr($path,0,strlen($path)-10); //path = C:\AppServ\www\pure\LearnsquareV1
		/*$path_upgrade_backup = $path.'/modules/Upgrade/backup';
		 $path_upgrade= $path.'/modules/Upgrade';*/
		$path_upgrade_backup = './modules/Upgrade/backup';
		$path_upgrade= './modules/Upgrade';
		$zipfilepath = $path.'/backup';

		if(!is_dir($zipfilepath))
		{
			@mkdir($zipfilepath."/",777);
		}

		/*
		 $archive = new PclZip($zipfilepath.'/'.$zipname);
		 $v_list = $archive->create($path_upgrade_backup,PCLZIP_OPT_REMOVE_PATH, $path_upgrade);
		 if ($v_list == 0)
		 {
			die("Error : ".$archive->errorInfo(true));
		 }
		 */
		//ZipArchive
		$archive_name = $zipname;
		$archive_folder = $path_upgrade_backup;
		$zip = new ZipArchive;
		if ($zip -> open($zipfilepath.'/'.$archive_name, ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE) === TRUE)
		{
			$dir = preg_replace('/[\/]{2,}/', '/', $archive_folder."/");
			$dirs = array($dir);
			while (count($dirs))
			{
				//echo "count=".count($dirs)."<br>";
				$dir = current($dirs);
				$zip -> addEmptyDir($dir);
				$dh = opendir($dir);
				$dir_target = str_replace("./modules/Upgrade/","",$dir);
				while($file = readdir($dh))
				{
					if ($file != '.' && $file != '..')
					{ 	//echo "\n$dir.$file\n";
					if (is_file($dir.$file)){
						$zip -> addFile($dir.$file, $dir_target.$file);
					}elseif (is_dir($dir.$file)&&"$dir.$file"!="modules/.Upgrade"&&"$file"!=".svn"&&"$file"!=".lite"){
						$dirs[] = $dir.$file."/";
					}
					}
				}
				closedir($dh);
				array_shift($dirs);
			}
			$zip -> close();
			echo "\n\n Archiving is sucessful! \n";
		}
		else
		{
			echo 'Error, can\'t create a zip file!';
		}

		//	$path =  $_SERVER["PATH_TRANSLATED"];
		$path = $_SERVER["SCRIPT_FILENAME"];
		$path = substr($path,0,strlen($path)-10);
		$source = $path;
		/*if(!file_exists($path."/backup"))
		 mkdir($path."/backup/");
		 $dest = $path."/backup";
		 $backup_completed = $dest.'/'.$zipname;
		 copy($source.'/'.$zipname,$dest.'/'.$zipname);
		 unlink($source.'/'.$zipname);*/

		$content = $source."/modules/Upgrade/backup/content";
		$db = $source."/modules/Upgrade/backup/db";
		$learn = $source."/modules/Upgrade/backup/system";
		$temp = $source . "/modules/Upgrade/temp";

		RemoveFiles($learn);
		RemoveFiles($content);
		RemoveFiles($db);
		deleteDir($temp);

	}

	if($_POST['op'] == "restore")
	{
		$path = $_SERVER["SCRIPT_FILENAME"];
		$path = substr($path,0,strlen($path)-10);

		$path .= "/modules/Upgrade/temp";
		if(!is_dir($path))

		{

			@mkdir($path."/",777);


		}

		@move_uploaded_file($_FILES['fileimport']['tmp_name'], $path . "/".$_FILES['fileimport']['name']);

		$path_parts = pathinfo($path . "/".$_FILES['fileimport']['name']);

		$namezip = explode(".".$path_parts["extension"],$path_parts["basename"]);

		$archive = new PclZip($path . "/". $_FILES['fileimport']['name']);

		$lnpath = $_SERVER['SCRIPT_FILENAME'];
		$lnpath = substr($lnpath,0,strlen($lnpath)-10);
		$copy_from_dir = $lnpath."/backup";
		
		if ($archive->extract(PCLZIP_OPT_PATH,$copy_from_dir,PCLZIP_CB_PRE_EXTRACT,'preImportCallBack') == 0)
		{
			die("Error : ".$archive->errorInfo(true));
		}
		
		closedir($handle);
		$path = $_SERVER["SCRIPT_FILENAME"];
		$path = substr($path,0,strlen($path)-10);
		$path .= "/modules/Upgrade/";

		$path_parts = pathinfo($path . "/". $_FILES['fileimport']['name']);

		$namezip = explode(".".$path_parts["extension"],$path_parts["basename"]);

		if(strpos($namezip[0],'C')!==false)
		{
			include $path . 'restore_content.php';
		}
		if(strpos($namezip[0],'D')!==false)
		{
			include $path . 'restore_db.php';
		}
		if(strpos($namezip[0],'S')!==false)
		{
			include $path . 'restore_system.php';
		}
		$path = $_SERVER["SCRIPT_FILENAME"];
		$path = substr($path,0,strlen($path)-10);
		$path .= "/modules/Upgrade/";
		deleteDir($path ."temp");
		//	$path =  $_SERVER["PATH_TRANSLATED"];
		$path = $_SERVER["SCRIPT_FILENAME"];

		$path = substr($path,0,strlen($path)-10);
		$source = $path."/backup/backup";
		RemoveFiles($source,1);

	}

	echo '</Textarea>';

	if($zp=@fopen("backup_log.txt","w"))
	{
		@fwrite($zp,$txtarea_log);
		@fclose($zp);
	}


	if($op=="backup")
	echo '<br><br><b>Bckup Completed<br>'
	.$backup_completed.'</b><br><br><br>';
	if($op=="restore")
	echo '<br><br><b>Restore Completed<br>';
	CloseTable();



	function RemoveFiles($source,$all=0)
	{
		$folder = opendir($source);
		while($file = readdir($folder))
		{	//echo "\n =>> $file";
			if ($file == '.' || $file == '..') {
				continue;
			}
			 
			if(is_dir($source.'/'.$file))
			{

				RemoveFiles($source.'/'.$file);
			}
			else
			{
				unlink($source.'/'.$file);
			}
		}
		closedir($folder);
		$pure = pathinfo($source);
		if($all == 0)
		{
	 	if($pure["basename"]!="content"&&$pure["basename"]!="system"&&$pure["basename"]!="db")
	 	rmdir($source);
		}
		else
		@rmdir($source);
		return 1;
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

			/*if (!@rmdir($dir))
			 return false;*/
			return true;
		}
		return false;
	}



	function unzip($dir, $file, $verbose = 0) {

		$dir_path = "$dir$file";
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
								echo "extracting: $name<br>";
								 
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
		 
		return $ERROR_MSGS[$ERROR];
		 
	}


	function Unzipp($dir, $file, $destiny="")
	{
		$dir .= DIRECTORY_SEPARATOR;
		$path_file = $dir . $file;
		$zip = zip_open($path_file);
		$_tmp = array();
		$count=0;
		if ($zip)
		{
			while ($zip_entry = zip_read($zip))
			{
				$_tmp[$count]["filename"] = zip_entry_name($zip_entry);
				$_tmp[$count]["stored_filename"] = zip_entry_name($zip_entry);
				$_tmp[$count]["size"] = zip_entry_filesize($zip_entry);
				$_tmp[$count]["compressed_size"] = zip_entry_compressedsize($zip_entry);
				$_tmp[$count]["mtime"] = "";
				$_tmp[$count]["comment"] = "";
				$_tmp[$count]["folder"] = dirname(zip_entry_name($zip_entry));
				$_tmp[$count]["index"] = $count;
				$_tmp[$count]["status"] = "ok";
				$_tmp[$count]["method"] = zip_entry_compressionmethod($zip_entry);
				 
				if (zip_entry_open($zip, $zip_entry, "r"))
				{
					$buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
					if($destiny)
					{
						$path_file = str_replace("/",DIRECTORY_SEPARATOR, $destiny . zip_entry_name($zip_entry));
					}
					else
					{
						$path_file = str_replace("/",DIRECTORY_SEPARATOR, $dir . zip_entry_name($zip_entry));
					}
					$new_dir = dirname($path_file);
					 
					// Create Recursive Directory
					mkdir($new_dir);
					 

					$fp = fopen($dir . zip_entry_name($zip_entry), "w");
					fwrite($fp, $buf);
					fclose($fp);

					zip_entry_close($zip_entry);
				}
				echo "\n</pre>";
				$count++;
			}

			zip_close($zip);
		}
	}


	include 'footer.php';
	?>