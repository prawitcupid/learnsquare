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

/////////////////////////////////////////////////////////////////////////////////////
	
//$path =  $_SERVER["PATH_TRANSLATED"];
$path = $_SERVER["SCRIPT_FILENAME"];
$path = substr($path,0,strlen($path)-10);

	
$copy_from_dir = $path."/courses";
$copy_to_dir = $path."/modules/Upgrade/backup/content";

copydirrContent($copy_from_dir,$copy_to_dir,0777,true);








/////////////////////////////////////////////////////////////////////////////////////

function copydirrContent($fromDir,$toDir,$chmod=0757,$verbose=false)
{

	$errors=array();
	$messages=array();


	if (!is_writable($toDir))
	   $errors[]='target '.$toDir.' is not writable';
	if (!is_dir($toDir))
	   $errors[]='target '.$toDir.' is not a directory';
	if (!is_dir($fromDir))
	   $errors[]='source '.$fromDir.' is not a directory';
	if (!empty($errors))
	{
	   if ($verbose)
	       foreach($errors as $err)
	           echo '<strong>Error</strong>: '.$err.'<br />';
	   return false;
	}
	//*/

	$exceptions=array('.','..');

	$handle=opendir($fromDir);
	while (false!==($item=readdir($handle)))
	   if (!in_array($item,$exceptions))
	   {
		   //* cleanup for trailing slashes in directories destinations
		   $from=str_replace('//','/',$fromDir.'/'.$item);
		   $to=str_replace('//','/',$toDir.'/'.$item);
		   //*/
		   if (is_file($from))
		   {
			   if (@copy($from,$to))
               {
                   chmod($to,$chmod);
				   touch($to,filemtime($from)); // to track last modified time
                   $messages[]='File copied from '.$from.' to '.$to;
               }
               else
                   $errors[]='cannot copy file from '.$from.' to '.$to;
		   }
           if (is_dir($from))
           {
               if (@mkdir($to))
               {
                   chmod($to,$chmod);
                   $messages[]='Directory created: '.$to;
               }
               else
                   $errors[]='cannot create directory '.$to;
               copydirrContent($from,$to,$chmod,$verbose);
           }
       }
       closedir($handle);
	//*/
	//* Output
	if ($verbose)
	{
		foreach($errors as $err)
			echo '<strong>Error</strong>: '.$err.'<br />';
		foreach($messages as $msg)
			echo $msg.'<br />';
	}
//*/
	return true;
}

?>