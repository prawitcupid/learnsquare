<?php
function array_srch($indexarr, $wordarr, $word_list) {
	$reword="";
	reset($word_list);		//???	pionter	????????????????
	foreach	(array_keys($word_list)	as $fields){
		if($fields===$wordarr){
			$reword=$word_list[$fields];
		}
	}
	return($reword);
}
?>