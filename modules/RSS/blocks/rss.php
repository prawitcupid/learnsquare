<?php

function blocks_rss_block($row) {
	   
	   if (empty($row['title'])) {
			$row['title'] = _TITLE_NEWS;
		}

		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
	
		$rsstable = $lntable['rss'];
		$rsscolumn = &$lntable['rss_column'];
		$result = $dbconn->Execute("SELECT $rsscolumn[id], $rsscolumn[title], $rsscolumn[xml], $rsscolumn[display], $rsscolumn[name], $rsscolumn[date] FROM $rsstable ORDER BY $rsscolumn[id] DESC");

		
	if ($result ->RecordCount()) {
		$countrow=0;


		while((list($id,$title,$xml,$display,$name,$date) = $result->fields))
		{
			//$result->MoveNext();

			$row['content'] .= '<div><b>'.$title.'</b></div>';
			
			$row['content'] .= $display;
	
			$ch = curl_init($xml);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$data = curl_exec($ch);
			curl_close($ch);
			$doc = new SimpleXmlElement($data, LIBXML_NOCDATA);
	
			//print_r($doc);
			//exit();
			if(isset($doc->channel))
			{
			   $row['content'] .= parseRSS($doc);
			}
			if(isset($doc->entry))
			{
			   $row['content'] .= parseAtom($doc);
			}
			$row['content'] .= '</marquee><hr>';
			$result->MoveNext();

		}

			//$row['content'] .= '</marquee>';
	                return themesidebox($row);
		
	}

}



function parseRSS($xml)
{
    $feed = "<center><table border = '0'><tr><td><strong>".$xml->channel->title."</strong></td></tr>";
    $cnt = count($xml->channel->item); 
    $cnt = $cnt/2;
    $k = 0;
    for($i=0; $i < $cnt; $i++)
    {
	$feed .= '<tr>';
	for($j = 0;$j < 2;$j++){
		
		$url = $xml->channel->item[$k]->link;
		$title 	= $xml->channel->item[$k]->title;
		$desc = $xml->channel->item[$k]->description;

		$feed .= '<td><b><a href="'.$url.'">'.$title.'</a></b><br>'.$desc.'</td>';
		$k++;
	}
	$feed .= '</tr>';

    }
	$feed .= '</table></center>';
	return $feed;
}
function parseAtom($xml)
{
    $feed = "<div style = 'width:450px;'><strong>".$xml->author->name."</strong>";
    $cnt = count($xml->entry);
    for($i=0; $i < $cnt; $i++)
    {
	//print_r($xml->entry[$i]);
	//exit();
	//$urlAtt = $xml->entry->link[$i];
	//$url	= $urlAtt['@attributes']['href'];
	//$title 	= $xml->entry->title;
	$desc = $xml->entry[$i]->content;
	$feed .= $desc;
	//$feed .= '<tr><td><a href="'.$url.'"><b>'.$title.'</b></a>'.$desc.'<hr></td></tr>';
    }
	$feed .= '</div>';
	return $feed;
}
?>
