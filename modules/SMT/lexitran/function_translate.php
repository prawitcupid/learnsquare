<?php
/** function translate by neab */
function translate($word){

	// --	connect	database
	// Configuration for LEXiTRON database
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$ettable = $lntable['et'];
	$etcolumn = &$lntable['et_column'];
	if($dbconn->ErrorNo() != 0) {
		echo "error cannot connect et";
		return;
	}else{
		//echo '<br>Connect Success!!';
	}

	//exit();
	$word = trim($word);
	$word = trim($word,'');
	//$table_name	= "ET";
	$condition = "$etcolumn[esearch] = '$word'";

	$query = "SELECT $etcolumn[esearch], $etcolumn[ecat], $etcolumn[tentry], 
	$etcolumn[esyn], $etcolumn[eant], $etcolumn[esample]
	FROM $ettable
	WHERE $condition";
	//echo '<hr><pre>'.$query.'</pre>';
	$result = $dbconn->Execute($query) or die ("<b>Error Query ET</b>");
	while((list($esearch,$ecat,$tentry,$esyn,$eant,$esample) = $result->fields)){
		$result->MoveNext();
		$word = $esearch;
		$pos = $ecat;
		$meaning = $tentry;
		$syn = $esyn;
		$ant = $eant;
		$ex	= $esample;

		if($num_rows==1){
			$str_word.=	"<b><font	color='#2046B8'>".$word."</font></b> - ".$pos.". - ".$meaning."<br>";
		}else{
			$str_word.=	"<b><font	color='#2046B8'>".$word."</font></b> (".($i+1).")	-	".$pos.".	-	".$meaning."<br>";
		}if($syn){
			$str_word.=	"&nbsp;&nbsp;	<i><font color='#95A3D2'>Syn.	:: </font></i>".$syn."<br>";
		}if($ant){
			$str_word.="&nbsp;&nbsp; <i><font	color='#95A3D2'>Syn. ::	</font></i>".$ant."<br>";
		}if($ex){
			$str_word.="&nbsp;&nbsp; <i><font	color='#95A3D2'>Example	:: </font></i>".$ex."<br>";
		}
		$word_result	=	$word_result.$str_word;
		$str = $word."-".$pos."-".$meaning."\n	---------------------------------\n";

	}
	//echo '<br><pre>'.$word_result.'</pre><br>';
	//exit();
	/*
	$sql = "SELECT * FROM $table_name
			WHERE $condition";
	//echo $sql.'<hr>'; //exit();
	$query = mysql_query($sql);
	if (!$query) {
		die('Invalid query: ' . mysql_error());
	}
	//echo '<pre>';
	//print_r($query);
	//echo '</pre>';
	//$fp	= fopen("test_sql.txt","a+") or	die	("can't	open file");
	if($query){
		$num_rows = mysql_num_rows($query);
		if($num_rows != 0){
			for($i=0; $i<$num_rows; $i++){
				$str_word = "";
				$result	= mysql_fetch_array($query);

				$word = $result['ESEARCH'];
				$pos = $result['TCAT'];
				$meaning = $result['TENTRY'];
				$syn = $result['ESYN'];
				$ant = $result['EANT'];
				$ex	= $result['ESAMPLE'];

				if($num_rows==1){
					$str_word.=	"<b><font	color='#2046B8'>".$word."</font></b> - ".$pos.". - ".$meaning."<br>";
				}else{
					$str_word.=	"<b><font	color='#2046B8'>".$word."</font></b> (".($i+1).")	-	".$pos.".	-	".$meaning."<br>";
				}if($syn){
					$str_word.=	"&nbsp;&nbsp;	<i><font color='#95A3D2'>Syn.	:: </font></i>".$syn."<br>";
				}if($ant){
					$str_word.="&nbsp;&nbsp; <i><font	color='#95A3D2'>Syn. ::	</font></i>".$ant."<br>";
				}if($ex){
					$str_word.="&nbsp;&nbsp; <i><font	color='#95A3D2'>Example	:: </font></i>".$ex."<br>";
				}
				$word_result	=	$word_result.$str_word;
				$str = $word."-".$pos."-".$meaning."\n	---------------------------------\n";
				//fwrite($fp,$str);

			}	// end for loop	(	get	vocab	from database)
		}
	}
	//fclose($fp);
	//echo $word_result.'<br>';
	
	*/
	return $word_result;
}
?>