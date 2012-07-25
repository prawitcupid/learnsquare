<?php
/** function isword by neab */
function isword($word){
	$found=1;
	if($word==""){
		$found=0;
	}else{
		$testset="#+_*%^&|=[]{}@-\;\'<>?()กขฃคฅฆงจฉชซฌญฎฏฐฑฒณดตถทธนบปผฝพฟภมยรลวศษสหฬอฮะาเแโใไๆ ่ ้ ๊ ๋";
		$s=$word;
		$si=0;
		$s_len=strlen($s);
		for	($si=0;$si<$s_len;$si++){
			$c=$s[$si];
			for	($j=0;$j<strlen($testset);$j++){
				if($c==$testset[$j]){
					$found=0;
					break;
				}
			}
		}
	}
	return $found;
}
function isword2($word){
	$found=1;
	//echo $word."<br>";
	if($word==""){
		$found=0;
	}else{
		$testset="#!+_*%^&.|=[]{}@,-\:\;\"\'/\<>?\(\)กขฃคฅฆงจฉชซฌญฎฏฐฑฒณดตถทธนบปผฝพฟภมยรลวศษสหฬอฮะาเแโใไๆ ่ ้ ๊ ๋";
		$s=$word;
		$si=0;
		$s_len=strlen($s);
		for	($si=0;$si<$s_len;$si++){
			$c=$s[$si];
			for	($j=0;$j<strlen($testset);$j++){
				if($c==$testset[$j]){
					$found=0;
					break;
				}
			}
		}
	}
	//return $found;
	if($found==0){
		return substr($word,0,($s_len-1))." ".$testset[$j];
	}else{
		return $word;
	}
}
?>