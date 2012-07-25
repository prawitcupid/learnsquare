<?php

function getCollection(){
	$html="";
	$repositoryAddr = "http://203.185.132.234";
	$url = $repositoryAddr."/list/?cat=quick_filter";

	if(isset($selectResult)){
		$totalresult = "&rows=".$selectResult;
	}
	$objecttype = "&search_keys[core_10][]=2";//collection
	$typedisplay = "&tpl=3";

	$link = $url.$totalresult.$objecttype.$typedisplay;

	//echo $link."<br>";
	$dom= new DOMDocument();
	$dom->prevservWhiteSpace = false;

	if (!@$dom->load($link)) {
		echo "xml doesn't exist!\n";
		return;
	}

	$domItem = $dom->getElementsByTagName("item");
	//$no=1;
	//echo '<table border="0" cellpadding="1" cellspacing="1" bgcolor="#ffffff" width="100%">';
	foreach ($domItem as $item)
	{
		$title = $item->getElementsByTagName("title")->item(0)->nodeValue;
		$pid = $item->getElementsByTagName("pid")->item(0)->nodeValue;
		$genre = $item->getElementsByTagName("genre")->item(0)->nodeValue;
		$datastream_links = $item->getElementsByTagName("datastreams");
		$link_view = $repositoryAddr."/view/".$pid;
		//echo '<tr bgcolor="#808080" align="center"><td>Preview image</td><td colspan="3" width="70%">Title</td></tr>';
		//echo "<tr><td align='center'><image src='images/no_image.gif' heigh='50%' width='50%'></td><td colspan='3'>".$title."</td></tr>";
		$no++;

		foreach ($datastream_links as $datastream_link){
			$total = $datastream_link->getElementsByTagName("datastream")->length;
			$link = $datastream_link->getElementsByTagName("datastream");
			//echo '<tr colspan="3"><td>';

			//check preview image
			for($i=0; $i<$total ;$i++){
				$data = $link->item($i)->nodeValue;
				$datatypes = explode(".", $data);
				$index = count($datatypes)-1;
				$datatype = $datatypes[$index];
				$preview_image='';
				if($datatype=='jpg'){
					//echo $data;
					$preview_image = '<image src="'.$repositoryAddr.'/eserv/'.$pid.'/'.$data.'" heigh="50%" width="50%">';
				}
			}
			if($preview_image==''){
				$preview_image = '<image src="images/no_image.gif" heigh="50%" width="50%">';
			}
			
			$html .= '<option value="'.$pid.'">'.$title.'</option>';
			//echo '<tr bgcolor="#808080" align="center"><td>Preview image</td><td colspan="3" width="70%">Title</td></tr>';
			//echo '<tr><td align="center">'.$pid.'</td>';
			//echo "<td colspan='3'><a href='".$link_view."' target='_blank'>".$title."</a></td></tr>";
		}
	}
	//echo '</table>';
	return $html;
}
?>