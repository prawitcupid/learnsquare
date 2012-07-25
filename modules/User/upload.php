<?php
include 'header.php';

OpenTable();

//echo lnBlockTitle($mod);
echo '<p class="header"><b>'._USER_TITLE.'</b></p>';
echo '<BR>'._PERSONALINFO;

//Upload Image Page
//---Programmer Narasak Tai 10/09/2007----

//access data
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'User::Profile', "::", ACCESS_COMMENT)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}
//get uid
$userinfo= lnSessionGetVar('uid');//lnUserGetVars(lnSessionGetVar('uid'));

//get file from cache server
$fileupload = $_FILES['fileupload']['tmp_name'];
//echo '<script> alert("'.$fileupload.'");</script>';

if(!empty($fileupload)){

	$img = $userinfo.".jpg";
	$file = $_FILES['fileupload']['name'];
	$uploaddir = "images/avatar/userimage/".$img; //path+filename
	$typefile = $_FILES['fileupload']['type'];

	//check type image
	if(($typefile=='image/gif')or($typefile=='image/pjpeg')or($typefile=='image/jpeg')or($typefile=='image/png')){
		////Resize

		$images = $fileupload;
		//fix width = 120 px but height calculate form size & width
		$width=120;
		$size=GetimageSize($images);
		$height=round($width*$size[1]/$size[0]);

		if($typefile=='image/gif'){
		 $images_orig = ImageCreateFromGIF($images);
		 $photoX = ImagesX($images_orig);
		 $photoY = ImagesY($images_orig);
		 $images_fin = ImageCreateTrueColor($width, $height);
		 ImageCopyResampled($images_fin, $images_orig, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);
		 ImageGIF($images_fin,$uploaddir); // sizeImage+px,Path+name
		 ImageDestroy($images_orig);
		 ImageDestroy($images_fin);
		}else if($typefile=='image/png'){
		 $images_orig = ImageCreateFromPNG($images);
		 $photoX = ImagesX($images_orig);
		 $photoY = ImagesY($images_orig);
		 $images_fin = ImageCreateTrueColor($width, $height);
		 ImageCopyResampled($images_fin, $images_orig, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);
		 ImagePNG($images_fin,$uploaddir); // sizeImage+px,Path+name่
		 ImageDestroy($images_orig);
		 ImageDestroy($images_fin);
		}else{//image/pjpeg or image/jpeg
		 $images_orig = ImageCreateFromJPEG($images);
		 $photoX = ImagesX($images_orig);
		 $photoY = ImagesY($images_orig);
		 $images_fin = ImageCreateTrueColor($width, $height);
		 ImageCopyResampled($images_fin, $images_orig, 0, 0, 0, 0, $width+1, $height+1, $photoX, $photoY);
		 ImageJPEG($images_fin,$uploaddir); // sizeImage+px,Path+name
		 ImageDestroy($images_orig);
		 ImageDestroy($images_fin);
		}

		//change Permission file
		chmod($uploaddir,0777);
		echo _UPLOADING;

	}else{
		echo _ERROR_TYPE_UPLOAD;
	}

}else{
	echo _ERROR_EMPTY_UPLOAD;
}
echo '<br><b><a href=index.php?mod=User&file=profile>'._BackFromUpload.'</a></b>';

CloseTable();

include 'footer.php';

?>
