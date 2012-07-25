<?php
//edit by Watee wichiennit 03/01/2008
//add some code to make it work with ajax dialog engine
session_start();
$_SESSION['_cid'] = $_GET['_cid'];

include("mathpublisher.php") ;

function mathimageurl($text,$size,$pathtoimg)
{
/*
Creates the formula image (if the image is not in the cache) and returns the <img src=...></img> html code.
*/
global $dirimg;
$nameimg = md5(trim($text).$size).'.png';
$v=detectimg($nameimg);
if ($v==0)
	{
	//the image doesn't exist in the cache directory. we create it.
	$formula=new expression_math(tableau_expression(trim($text)));
	$formula->dessine($size);
	$v=1000-imagesy($formula->image)+$formula->base_verticale+3;
	//1000+baseline ($v) is recorded in the name of the image
	ImagePNG($formula->image,$dirimg."/math_".$v."_".$nameimg);
	}
$valign=$v-1000;
return $pathtoimg."math_".$v."_".$nameimg;
}


function mathfilterurl($text,$size,$pathtoimg) 
{
/* THE MAIN FUNCTION
1) the content of the math tags (<m></m>) are extracted in the $t variable (you can replace <m></m> by your own tag).
2) the "mathimage" function replaces the $t code by <img src=...></img> according to this method :
- if the image corresponding to the formula doesn't exist in the $dirimg cache directory (detectimg($nameimg)=0), the script creates the image and returns the "<img src=...></img>" code.
- otherwise, the script returns only the <img src=...></img>" code.
To align correctly the formula image with the text, the "valign" parameter of the image is required.
That's why a parameter (1000+valign) is recorded in the name of the image file (the "detectimg" function returns this parameter if the image exists in the cache directory)
To be sure that the name of the image file is unique and to allow the script to retrieve the valign parameter without re-creating the image, the syntax of the image filename is :
math_(1000+valign)_md5(formulatext.size).png.
(1000+valign is used instead of valign directly to avoid a negative number)
*/
$text=stripslashes($text);
$size=max($size,10);
$size=min($size,24);
preg_match_all("|<m>(.*?)</m>|", $text, $regs, PREG_SET_ORDER);
foreach ($regs as $math) 
	{
	$t=str_replace('<m>','',$math[0]);
	$t=str_replace('</m>','',$t);
	$code=mathimageurl(trim($t),$size,$pathtoimg);
	$text = str_replace($math[0], $code, $text);
	}	
return $text;
}
$_val = $_REQUEST["formula"];
$_val = str_replace("&plus;","+",$_val);
$_val = str_replace("\\","",$_val);

echo mathfilter("<m>".$_val."</m>",$_dffonsize,$dirimgrel);

?>