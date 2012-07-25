<?php
$link = $_POST['link'];
$filename = $_POST['filename'];
$pid = $_POST['pid'];
$cid = $_POST['cid'];

//echo "cid=".$cid."<br>";
//echo "File Name : ".$filename."<br>";
//echo "File Link Location : ".$link."<br>";
$pid = explode(":",$pid);
$prefix_name = $pid[1];
echo $prefix_name."_".basename($link);

if($link){
	$file = fopen($link,"rb");
	if($file){
		$ext = end(explode(".",strtolower(basename($link))));
		$newfile = fopen("../../courses/".$cid."/". $prefix_name."_".basename($link), "wb"); // replace "downloads" with whatever directory you wish.
		//$newfile = fopen("uploads/". basename($link), "wb");
		if($newfile){
			while(!feof($file)){
				// Write the url file to the directory.
				fwrite($newfile,fread($file,1024 * 8),1024 * 8);
				// write the file to the new directory at a rate of 8kb/sec. until we reach the end.
			}
		}
	}
}
//echo "Sucesses!!";

?>