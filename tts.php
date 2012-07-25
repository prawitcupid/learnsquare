<?
	
	//tts.php
	require ('function_htmltoarray.php');
	//sleep(3);
	
	$cid = $_GET['CID'];
	$file = $_GET['FILE'];
	$vaja_addr = $_GET['VAJA'];
	$vaja_wav = $_GET['VAJAWAV'];
	
	$referer = $_SERVER["SERVER_ADDR"].$_SERVER["SCRIPT_NAME"];
	//echo $referer;
	$ref = explode("tts.php", $referer);
	$urladdr = $ref[0];
	//echo "<br>".$urladdr;
	
	$wavepath = "courses/".$cid."/"."wav";
	$coursepath= "courses/" .$cid;	
	// create course directory
	if (!file_exists($coursepath)) {
		mkdir($coursepath);
	}
		// create course/wav directory
	if (!file_exists($wavepath)) {
		mkdir($wavepath);
	}
	
	// Copy and Create speech file
	if(!empty($file))
	{
		$url= $coursepath."/".$file;
		//New cut
//**************************************** load url and strip*****************************
		$pc_file = fopen($url,'r');
		$text_read = fread($pc_file,filesize($url));
		$text = strip_tags($text_read);
		fclose($pc_file);
		$text = preg_replace('/&nbsp;/',null,$text);
//*************************************** cut non alphabet ******************************
		$slot =0;
		$token="";
	
		for($i=0;$i< strlen($text);$i++){
			if(ord($text[$i])<33){
				if($token[$slot] != ""){
					$slot++;
				}
			}
		
			else $token[$slot] .= $text[$i];
		}
//*************************************** concat to less than 200 char*******************************
		$no = 0;
		$fintext ="";			//Array of Word = fintext
		for($i=0;$i<=$slot  ;$i++){
			if(strlen($fintext[$no].$token[$i])>100){
				$no++;
			} 
			$fintext[$no] .= $token[$i];
			$fintext[$no].=' ';
		}

		// result file
		// read data from url to array		
		for ($i=0; $i<strlen($file); $i++)
		{
				if ($file[$i]=='.')break;
			$playlist_name .=$file[$i];
		}
		$playlist_name .= '.m3u';	
		$handle = fopen($coursepath."/".$playlist_name, 'w');			
		require_once('lib/nusoap.php');
		echo $no+1;
		flush();
		for($i=0;$i<= $no ;$i++)
		{
			$client= new nusoapClient($vaja_addr,true,false,false,false,false,120,120);
			$result2 = $client->call('vaja',array('speed' => '0', 'sex' => 'Female', 'text' =>$fintext[$i]));
			if ($client->fault) 
			{
 		  		echo '<h2>Fault</h2><pre>';
   		 		print_r($result2);
 		   		echo '</pre>';
			}
			 else
			{
  			  	// Check for errors
    				$err = $client->getError();
    				if ($err) 
    				{
        					// Display the error
        					echo '<h2>Error</h2><pre>' . $err . '</pre>';
    				} 
    				else 
    				{
        					// Display the result
        					echo '<h2>Result '.$i.'</h2><pre>';
      					print_r($result2);
      					echo $fintext[$i]."\n";
      					$result1 = $wavepath."/".$result2;
      					$result2 = $vaja_wav.$result2;
      					echo $result1;	
	 				echo $result2;
   					echo '</pre>';
    				}
			}
			// Code Copy File				
			if (!copy($result2, $result1)) 
    				echo "failed to copy $result2...\n";
			//$result1 = "http://localhost/ln2/learnsquareV2_5_org/".$result1."\n";			// URL ต้องแก้
			$result1 = "http://".$urladdr.$result1."\n";
			fwrite($handle, $result1);
			
		}
		fclose($handle);
		$handle = fopen($url, 'r');
		$s_arr = htmltoarray($handle);
		$file_index=count($s_arr);
		fclose($handle);

		for($i=0;$i<$file_index;$i++){
			if(substr($s_arr[$i], 0, 110)=='<embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" name="speech1'){
				$s_arr[$i] = null;				
			}
		}

		$handle = fopen($url, 'w+');
		for($i=0;$i<$file_index;$i++){
			fwrite($handle,$s_arr[$i]);
		}
		fseek($handle,0,SEEK_END);
		fwrite($handle,"\n".'<embed type="application/x-mplayer2" pluginspage="http://www.microsoft.com/Windows/MediaPlayer/" name="speech1" ShowStatusBar="false" EnableContextMenu="false" autostart="false" width="320" height="40" loop="false" src="'.$playlist_name.'" />');
		fclose($handle);
		//echo '<a href="http://localhost/ln2/learnsquareV2_5_org/'.$url.'">[Link]</a>';
		echo '<a href="http://'.$urladdr.$url.'">[Link]</a>';
	}
?>