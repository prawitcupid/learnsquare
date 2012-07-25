<?php
function wordsResult($wordhamer,$wordarr){
for($freq=count($wordhamer)-2;$freq>=0;$freq--){
//$frequency[$freq]=0;
	foreach($wordarr as $wdar ){
		if($wordhamer[$freq]==$wdar){			
			$frequency[$freq]++;			
		}
				
	}	
	$meaning=$wordhamer[$freq];
	if(translate($meaning)==""){
			//echo "$hamer";
			$compile="<font color='black'>Add <a href='http://lexitron.nectec.or.th/vocabsuggestion/add_vocab.php' target\='_blank'>$meaning</a> to LEXiTRON</font>\n";
	}else{
			$compile=translate($meaning)."<br>";		
	}	
		$tbody.= "<tr><td align=\"left\">".$wordhamer[$freq]."</td><td align=\"center\">".$frequency[$freq]."</td><td align=\"left\">".$compile."</td></tr>";
}

$fhead = " <html><head>
					 <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" >
					 <title></title></head>
					 <body>
					 <center><font color=\"red\"><b>Word Result </b><br><br></font></center>
					 <table border=\"1\" width=\"100%\"><tr><td align=\"center\">Words</td><td align=\"center\">number of words</td><td align=\"center\">meaning</td></tr>";
$shead = "</table></body>
                      </html>";
//$fm  = fopen("word_meaning.html","w");
//fwrite($fm,$fhead);
//fwrite($fm,$tbody);
//fwrite($fm,$shead);
//fclose($fm);
}
?>