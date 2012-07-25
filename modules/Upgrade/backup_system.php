<?php
/**
*  Upgrade backup system
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Admin::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

set_time_limit(180);

//$path =  $_SERVER["PATH_TRANSLATED"];
$path = $_SERVER["SCRIPT_FILENAME"];
$path2 = $path;
$path = substr($path,0,strlen($path)-10);

$path3 = explode("/",$path2);
$learn_name = $path3[count($path3)-2];
	
$copy_from_dir = $path;

$copy_to_dir = $path."/modules/Upgrade/backup/system/$learn_name";


mkdir($copy_to_dir, 0777);

echo $copy_from_dir."<br>";
echo $copy_to_dir."<br>";

copydirrLearn($copy_from_dir,$copy_to_dir,0777,true);


function copydirrLearn($fromDir,$toDir,$chmod=0757,$verbose=false)
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
		   //echo "\n item= $item \n";
		   
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
           if (is_dir($from)&&"$item"!="lite"&&"$item"!=".svn")
           {
			   $parts = pathinfo($from);
			   // mai aow  "Upgrade" and "courses"
			   if($parts["basename"]!="Upgrade" && $parts["basename"]!="courses" && $parts["basename"]!="backuppure")   
			   {
					if (@mkdir($to))
					{
						chmod($to,$chmod);
						$messages[]='Directory created: '.$to;
					}
					else
						$errors[]='cannot create directory '.$to;
						copydirrLearn($from,$to,$chmod,$verbose);
			   }
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