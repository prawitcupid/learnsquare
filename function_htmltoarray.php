<?php
/** function htmltoarray by neab */
function htmltoarray($targetfile){
	$file_contents_index=0;
	$file_contents[$file_contents_index]="";

	//$fp1 = fopen("test_2.txt","a+")	or die("can't	open file");
	//fwrite($fp1,"-----------$targetfile-----------------\n\n");


	while(!feof($targetfile)){		
		$ch_bucket=fread($targetfile,"1");
		if($ch_bucket=='<'){		//tag html

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
		}else{	//tag
			//$file_contents[$file_contents_index].=strtolower($ch_bucket);
			$file_contents[$file_contents_index].=$ch_bucket;
			//fwrite($fp1,$ch_bucket);
		}	//if($ch_bucket=='<'){
	}	// while loop
	//fwrite($fp1,"\n\n---------------------------\n\n");
	return $file_contents;
}
?>