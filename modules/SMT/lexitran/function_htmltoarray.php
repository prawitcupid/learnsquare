<?php
/** function htmltoarray by neab */
function htmltoarray($targetfile){
	$file_contents_index=0;
	$file_contents[$file_contents_index]="";

	while(!feof($targetfile)){	
		$ch_bucket=fread($targetfile,"1");	
		if($ch_bucket=='<'){	

			if($file_contents[$file_contents_index]!=""){
				$file_contents_index++;
			}
			$file_contents[$file_contents_index].="<";
			while($ch_bucket!='>'){
				$ch_bucket=fread($targetfile,1);
				//$file_contents[$file_contents_index].=strtolower($ch_bucket);
				$file_contents[$file_contents_index].=$ch_bucket;
				//fwrite($fp1,$ch_bucket);
			}
			$file_contents_index++;
		}else{	
			$file_contents[$file_contents_index].=$ch_bucket;
			
		}	//if($ch_bucket=='<'){
	}	// while loop
	//fwrite($fp1,"\n\n---------------------------\n\n");
	return $file_contents;
}
?>