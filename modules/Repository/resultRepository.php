<?php
include_once 'language/tha/global.php';

$keyword = $_POST['keyword'];
$selectResult = $_POST['selectResult'];
$selectCollection = $_POST['selectCollection'];
$item = $_POST['item'];
$weight = $_POST['weight'];
$cid = $_POST['cid'];
$lid = $_POST['lid'];
	
$repositoryAddr = $_POST['RepositoryAddr'];
//echo "repositoryAddr =".$repositoryAddr."<br>";

$url = $repositoryAddr."/list/?cat=quick_filter";
//echo $url."<br>";
if($keyword!=''){
	$searchkeyword = "&search_keys[0]=".$keyword;
}
if($selectCollection!=''){
	$collection = "&search_keys[core_8][]=".$selectCollection;
}
if($selectResult!=''){
	$totalresult = "&rows=".$selectResult;
}else{
	$totalresult = "&rows=500";
}

//$totalresult = "rows=5&pager_row=".$pageselect;

$objecttype = "&search_keys[core_10][]=3";
$typedisplay = "&tpl=3";

$link = $url.$searchkeyword.$collection.$totalresult.$objecttype.$typedisplay;

$dom= new DOMDocument();
$dom->prevservWhiteSpace = false;

if (!@$dom->load($link)) {
	echo "xml doesn't exist!\n";
	return;
}

//print_r($dom);
//echo $dom->getElementsByTagName("to")->item(0)->nodeValue;

$domItem = $dom->getElementsByTagName("item");
//echo "Link=".$link."<br>";
$total_result = $domItem->length;
//echo "<hr>Keyword:: ".$keyword."<br>";
//echo "Total Result:: ".$total_result."<br>";
$no=0;
$accordion=0;
//$tab=0;
//print_r($domItem);

$pagelength = ceil(($total_result/5));
//echo 'Page='.$pagelength.'<br>';

echo '<div id="tabs">';
echo '<ul>';
for($i=0;$i<$pagelength;$i++){
	echo '<li><a href="#'.$i.'">'.($i+1).'</a></li>';
}
echo '</ul>';

echo '<div id="'.$accordion.'">';//tab id
echo '<div id="accordion'.$accordion.'">';

foreach ($domItem as $item)
{
	$title = $item->getElementsByTagName("title")->item(0)->nodeValue;
	$pid = $item->getElementsByTagName("pid")->item(0)->nodeValue;
	$genre = $item->getElementsByTagName("genre")->item(0)->nodeValue;
	$datastream_links = $item->getElementsByTagName("datastreams");
	$link_view = $repositoryAddr."/view/".$pid;
	
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
				$preview_image = '<image src="'.$repositoryAddr.'/eserv/'.$pid.'/'.$data.'" width="250px" border="0" title="'._VIEWREALSOURCE.'">';
			}
		}
		if($preview_image==''){
			$preview_image = '<image src="images/ln2_logo.jpg" width="150px" border="0" title="'._VIEWREALSOURCE.'">';
		}

		echo '<h3><a href="#">'.$title.'</a></h3>';
		echo '<div>';
		echo '<table border="0" cellpadding="1" cellspacing="1" width="80%">';
		//echo '<tr><td align="center" colspan="5"><a href="'.$link_view.'" target="_blank"><u>View Original Source</u></a></td></tr>';
		echo '<tr><td align="center" rowspan="'.($total+1).'"><a href="'.$link_view.'" target="_blank">'.$preview_image.'</a></td></tr>';
		for($i=0; $i<$total ;$i++){
			$data = $link->item($i)->nodeValue;
			$datatypes = explode(".", $data);
			$index = count($datatypes)-1;
			$datatype = $datatypes[$index];
			$check1 = strpos($data, "preview_");
			$check2 = strpos($data, "thumbnail_");
			$check3 = strpos($data, "web_");
			
			
			if($datatype!='xml'&&$check1===false&&$check2===false&&$check3===false){
				if($datatype=='jpg'){
					$data="web_".$data;
				}
				$link_localtion = $repositoryAddr."/eserv/".$pid."/".$data;
				
				echo '<tr>';
				echo "<td><a href='".$link_localtion."' title='"._VIEWSOURCEFILE."'>".$data."</a></td>";
				//echo "<td align='center'><a href='".$link_localtion."' target='_blank'><image src='images/view.gif' border='0'></a></td>";
				echo "<td align='center'><a href='#' onClick=\"upload('".$link_localtion."','".$data."','".$pid."','".$title."')\"><image src='modules/Repository/images/download.png' border='0' title='"._DOWNLOADCONTENT."'></a></td>";
				echo '</tr>';
			}
		}
		echo '</table>';
		echo '</div>';
		
	}
	if($no%5==0){
		echo '</div>';
		echo '</div>';
		$accordion++;
		echo '<div id="'.$accordion.'">';
		echo '<div id="accordion'.$accordion.'">';
	}
	if($no==$total_result){
		echo '</div>';
		echo '</div>';
	}
}
//echo '</table>';
//echo '</div>';
?>
<script language="JavaScript" type="text/javascript">
	$(function() {
		$( "#tabs" ).tabs();
<?php 
	for($i=0;$i<=$accordion;$i++){
		echo '$( "#accordion'.$i.'" ).accordion({
			autoHeight: false,
			navigation: true
			});';
	}
?>
	});
</script>
<?php
/*echo '<ul>';
for($i=0;$i<$pagelength;$i++){
	echo '<li><a href="#'.$i.'">'.($i+1).'</a></li>';
}
echo '</ul>';*/

echo '</div>';//end tabs
exit();
?>
