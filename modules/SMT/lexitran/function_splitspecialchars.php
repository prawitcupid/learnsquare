<?php
/** function splitspecialchars by neab */
function splitspecialchars($str){
	$i=0;
	$j=0;
	$len=strlen($str);
	while($i<$len){
		if($str[$i]!='&'){
			while($str[$i]!='&'	&& $i<$len){
				$temp[$j].=$str[$i];
				//echo "str	i	$i = ".$str[$i]."	tempj	$j = ".$temp[$j]."<br>\n";
				$i++;
			}
			$i--;
		}else{
			while($str[$i]!=';'	&& $i<$len){
				$temp[$j].=$str[$i];
				//echo "str	i	$i = ".$str[$i]."	tempj	$j = ".$temp[$j]."<br>\n";
				$i++;
			}
			$temp[$j].=";";
		}
		$j	++;
		$i++;
	}
	return	$temp;
}
?>