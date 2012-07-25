<?php
//include 'aa.php';
require("modules/SMT/lexitran/config.inc.php");
require("modules/SMT/lexitran/function_isword.php");
require("modules/SMT/lexitran/function_splitspecialchars.php");
require("modules/SMT/lexitran/function_htmltoarray.php");
require("modules/SMT/lexitran/function_translate.php");
require("modules/SMT/lexitran/function_wordsResult.php");
require("modules/SMT/lexitran/function_addArray.php");
require("modules/SMT/lexitran/function_array_srch.php");

//========================== START PROGRAM =====================================

function getcod_en($file,$dir){
	$SUPARSITStatus =lnConfigGetVar('SUPARSITStatus');
	$LEXITRONStatus =lnConfigGetVar('LEXITRONStatus');

	if($SUPARSITStatus){
		//input file
		$url=$dir."suparsit_".$file.".html";
		// result file
		$filename= $dir."smt_".$file.".html";
	}else if($LEXITRONStatus){
		//input file
		$url=$dir.$file.".html";
		// result file
		$filename= $dir."lexitran_".$file.".html";
	}



	echo '<br><b>FileTarget=</b>'.$url;
	echo '<br><b>FileResult=</b>'.$filename;
	//echo '<br>Suparsit='.$SUPARSITStatus =lnConfigGetVar('SUPARSITStatus');
	//echo '<br>Lexitron='.$LEXITRONStatus =lnConfigGetVar('LEXITRONStatus');
	//exit();

	// read data from url to array
	$targetfile=fopen($url,"r") or die("URL:$url not found");
	$file_contents=htmltoarray($targetfile);
	$file_contents_index=count($file_contents);
	fclose($targetfile);


	$headflag=0;
	$scriptflag=0;
	$styleflag=0;
	$bodyflag=0;
	$headtag="";
	$scripttag="";
	$bodytag="";
	$word_list=array();
	addArray($word_list,'000','name');
	$indexarr=0;
	for($i=0;$i<$file_contents_index;$i++){
		$$file_contents[$i] = trim($file_contents[$i]);
		// check tag script
		if(substr(strtolower($file_contents[$i]),0,5)=="<html"){
			$headflag=1;
		}
		elseif(substr(strtolower($file_contents[$i]),0,6)=="</head"){
			$headflag=0;
		}
		elseif(substr(strtolower($file_contents[$i]),0,6)=="<style"){
			$styleflag=1;
		}
		elseif(substr(strtolower($file_contents[$i]),0,7)=="</style"){
			$styleflag=0;
		}
		elseif(substr(strtolower($file_contents[$i]),0,7)=="<script"){
			$scriptflag=1;
		}
		elseif(substr(strtolower($file_contents[$i]),0,8)=="</script"){
			$scriptflag=0;
		}
		elseif(substr(strtolower($file_contents[$i]),0,5)=="<body"){
			$bodyflag=1;
		}
		elseif(substr(strtolower($file_contents[$i]),0,6)=="</body"){
			$bodyflag=0;
		}

		// string for each tag
		if($headflag==1){
			$headtag=$headtag.$file_contents[$i];
		}
		elseif($styleflag==1){
			$styletag=$styletag.$file_contents[$i];
		}
		elseif($scriptflag==1){
			$scripttag=$scripttag.$file_contents[$i];
		}
		elseif($bodyflag==1){
			if(substr($file_contents[$i],0,1)!="<"){
				if($file_contents[$i]!=""){
					$splitedwords=splitspecialchars($file_contents[$i]);
					$numsplit=count($splitedwords);
					for($x=0;$x<$numsplit;$x++){
						$lenword=strlen($splitedwords[$x]);
						$line="";
						$substrword=substr($splitedwords[$x],0,1);
						if(substr($splitedwords[$x],0,1)!='&'){	//-	-	-	-	->a
							$line = $splitedwords[$x];
							$word=preg_split("/ /",$line);
							$numline=count($word);
							$z = 0;
							foreach($word as $wd){

								if(substr($wd,0,1)!=' '){

									//$sword = $word[$z];
									$sword = $wd;
									$tfword=isword(trim($sword));
									if($tfword){
										$keyarry="";
										$keyarry=$i.$x.$z;
										//$words=$word[$z];
										//echo $wd."<br>";
										$ch = isword2($wd);
										$ch_ex = explode(" ",$ch);
										//echo $ch."<br>";
										$ch_ex[0] = preg_replace("/\/i"","",$ch_ex[0]);
										$words = $ch_ex[0];
										#######get all word to wordarr use in check frequency word###########
										$wordarr[$indexarr]=$words;
										$indexarr++;
										#############################################################
										$key="";
										reset($word_list);
										$srchw = array_srch($keyarry,$words,$word_list);
										if($srchw!=""){
											$bodytag=$bodytag."<SPAN class='lex'id='$srchw'>$wd </SPAN>";
										}else{
											//echo "!!!!!TRUE<br>";
											//$bodytag=$bodytag."<SPAN onMouseOver='popLayer($keyarry)'>$word[$z] </SPAN>";
											$bodytag=$bodytag."<SPAN class='lex'id='$keyarry'>$wd </SPAN>";
											addArray($word_list,$keyarry,$words);
										}
									}
									else{
										// Collection	the	body tag
										//$bodytag=$bodytag.$word[$z];
										$bodytag=$bodytag.$wd;
									}
								}else{
									// Collection	the	body tag
									//$bodytag=$bodytag.$word[$z];
									$bodytag=$bodytag.$wd;
								}
								$z++;
							}
						}
						else{				 //- - - ->a
							// Collection	the	body tag
							$bodytag=$bodytag.$splitedwords[$x];
						}
					}
				}else{ //file_contents array is not null value
					$bodytag=$bodytag.$file_contents[$i];
				}
			}else{	//if(substr($file_contents[$i],0,1)!="<"
				$bodytag=$bodytag.$file_contents[$i]."\n";
			}
		}else{

			if($headflag==1){
				$headtag=$headtag.$file_contents[$i];
			}else{
				$bodytag=$bodytag.$file_contents[$i];
			}

		}// end if tag body
	}

	//========================== Send to Dictionary ===================================
	arsort($word_list);

	// --	connect	database
	// Configuration for LEXiTRON database
	/*
	//echo "<br>host=$dbhost user=$dbuser pass=$dbpass db=$dbname -->aa=$aa";
	$lnk = mysql_connect($dbhost,$dbuser,$dbpass)	or die ("Error mysql_connect");
	mysql_select_db($dbname, $lnk) or die ("Error mysql_select_db ".mysql_error());

	//set utf-8 all mysql
	mysql_query("SET character_set_results=utf8");
	mysql_query("SET collation_connection = utf8_general_ci");
	mysql_query("SET NAMES 'utf8'");
	*/

	$indexwordhamer=0;
	foreach	(array_keys($word_list)	as $fields)
	{
		$hamer="";
		$wordchar=$word_list[$fields];
		$hamer="$fields";
		$hamer=ltrim($hamer);
		$hamer=rtrim($hamer);

		################get pefect word to worhamer use in check frequency word#########################
		$wordhamer[$indexwordhamer]=$hamer;
		$indexwordhamer++;
		##########################################################################################
		if($hamer==""){
			$translate="";
		}else{
			//$translate="ทดสอบภาษาไทย";
			//echo $hamer.'<br>';
			$translate=translate($hamer);
			//echo $translate.'<hr>';
		}
		if($translate==""){
			$javatag = $javatag."descarray[$wordchar]=\"<font color='red'> ไม่มีในฐานข้อมูลจ๊ะ</font><br>\";\n";
			$javatag = preg_replace("/\n/i"," ",$javatag);
			$javatag = $javatag."\n";
		}else{
			$translate = preg_replace('/"/i','\"',$translate);
			$javatag=$javatag."descarray[$wordchar]=\"<font color='black'>".$translate."</font><br>\";\n";
		}
	}
	// --	close	connection to	database
	wordsResult($wordhamer,$wordarr);
	mysql_close($lnk);
	//fclose($fvocab);//ปิดไฟล์ที่เขียนคำศัพท์
	#########################################################################################
	$linefile=fopen($filename,"w");
	fwrite($linefile,"$headtag\n");

	//$SUPARSITStatus =lnConfigGetVar('SUPARSITStatus');
	//$LEXITRONStatus =lnConfigGetVar('LEXITRONStatus');

	if(($SUPARSITStatus==0)&&($LEXITRONStatus==1)){
		//echo '<br>Lexitran Only<br>';
		$fp=fopen("modules/SMT/lexitran/func_lex_only.txt","r");
		while(!feof($fp)){	// read	data in	the	webpage	until	the	end	of file
			$somecontents="";
			$somecontents	=	fread($fp,20);
			fwrite($linefile,"$somecontents");
		}
		fclose($fp);
		//fwrite($linefile,"$javatag \n");
	}

	$fp=fopen("modules/SMT/lexitran/func.txt","r");
	while(!feof($fp)){	// read	data in	the	webpage	until	the	end	of file
		$somecontents="";
		$somecontents	=	fread($fp,20);
		fwrite($linefile,"$somecontents");
	}
	fclose($fp);
	fwrite($linefile,"$javatag \n");
	$javacltag="</script>";
	$divtag="<div id=\"object1\" style=\"position:absolute; background-color:FFFFDD;color:black;border-color:black;border-width:20; visibility:show;	left:25px; top:-200px; z-index:+1\"	onmouseover=\"overdiv=1;\" onclick=\"overdiv=0;\"	>
popup description layer
</div>";
	fwrite($linefile,"$javacltag \n");
	fwrite($linefile,"$bodytag\n");
	fwrite($linefile,"</body>\n");
	fwrite($linefile,"$divtag \n");
	fwrite($linefile,"</html>\n");
	fclose($linefile);
	//header("Location: translated_en.html");
	echo '<br><b>Translate Success!</b>';
	return true;
}

?>
