<?php
//require_once('modules/SCORM/classes/XML/XML_HTMLSax/XML_HTMLSax.php');	/* for XML_HTMLSax */

//define ('LN_BLOCK_IMAGE_DIR','modules/Blocks/images/upload');

$my_files=array();
class MyHandler {
	function MyHandler(){}
	function openHandler(& $parser,$name,$attrs) {
		global $my_files;
		$elements = array(	'img'		=> 'src', 'IMG'	=> 'SRC', 'IMG'	=> 'src', 'img'	=> 'SRC',
						'a'			=> 'href', 'A'	=> 'href', 'A'	=> 'HREF', 'a'	=> 'HREF',
						'object'	=> 'data', 'OBJECT'	=> 'data',
						'applet'	=> 'classid',
						'link'		=> 'href',
						'script'	=> 'src',
						'form'		=> 'action',
						'input'		=> 'src',
						'iframe'	=> 'src',
						'embed'	=> 'src',
						'param'		=> 'value'
						
				);
		//echo $attrs[$elements[strtolower($name)]].'<BR>';
		////10/10/2550 edit by nay 
		if(isset($attrs['name']) && isset($attrs['value']))
		{
			
			if(strtolower($attrs['name']) == 'filename')
			{		
				$my_files[] = $attrs[$elements[strtolower($name)]];
			}
		}//end edit 
		elseif (isset($elements[strtolower($name)])) 
		{					// set name to lower
			if (@$attrs[$elements[strtolower($name)]] != '') 
			{		//lowercase attribute
				
				$my_files[] = $attrs[$elements[strtolower($name)]];
				
			}
			else if (@$attrs[strtoupper($elements[strtolower($name)])] != '')
			{	//uppercase attribute
				$my_files[] = $attrs[strtoupper($elements[strtolower($name)])];
			}
		}
		
	}

	function closeHandler(& $parser,$name) { }
}

// convert relative path to full url path
function lnShowContent($html,$url) {
	global $my_files;

	$handler=new MyHandler();
	/*
	$parser = new XML_HTMLSax();
	$parser->set_object($handler);
	$parser->set_element_handler('openHandler','closeHandler');
	$parser->parse($html);
	*/
	/*
	//php xml
	$parser = xml_parser_create();
	xml_set_object($parser,$handler);
	xml_set_element_handler($parser,'openHandler','closeHandler');
	xml_parse($parser,$html);
	*/
	//extract tags html
	//http://stackoverflow.com/questions/138313/how-to-extract-img-src-title-and-alt-from-html-using-php
	preg_match_all('/<([A-Za-z])[^>]+>/i',$html, $tags); 
	//print_r($tags);
	for($i=0;$i<count($tags[0]);$i++){
		//echo "<Pre>Tag =". $tags[0][$i]."</Pre>";
		//php xml
		$parser = xml_parser_create();
		xml_set_object($parser,$handler);
		xml_set_element_handler($parser,'openHandler','closeHandler');
		xml_parse($parser,$tags[0][$i]);
	}

	$files=array();
	$my_files = @array_unique($my_files);
	if (count($my_files) > 0) {
		foreach ($my_files as $file) {
			$url_parts = @parse_url($file);
			if (isset($url_parts['scheme'])) {
				continue;
			}
			$scourl = $url_parts['path'];
			$newscourl = $url.'/'.$scourl;
			//echo 'scourl='.$scourl." - newscourl=".$newscourl.'<BR>';
			if ($scourl != "index.php") {
				//09/10/2550 edit by nay 
				if(substr($scourl,0,3) != "www")
				{
					$html = str_replace($scourl,$newscourl,$html);
				}
/*				$ext = @explode(".",$scourl);
//				print_r($ext);
				if($ext[1] == 'wmv' )
				{
					$html = str_replace('application/x-shockwave-flash','video/x-ms-wmv',$html);
				}
				if($ext[1] == 'avi' )
				{
					$html = str_replace('application/x-shockwave-flash','video/avi',$html);
				}
				if($ext[1] == 'mpg' )
				{
					$html = str_replace('application/x-shockwave-flash','video/mpeg',$html);
				}
				if($ext[1] == 'mov' )
				{
					$html = str_replace('application/x-shockwave-flash','video/quicktime',$html);
				}
				
				if($ext[1] == 'wav' )
				{
					$html = str_replace('application/x-shockwave-flash','audio/x-wav',$html);
				}
				if($ext[1] == 'mp3' )
				{
					$html = str_replace('application/x-shockwave-flash','audio/mpeg',$html);
				}
				if($ext[1] == 'ogg' )
				{
					$html = str_replace('application/x-shockwave-flash','application/ogg',$html);
				}
				if($ext[1] == 'mid' )
				{
					$html = str_replace('application/x-shockwave-flash','audio/x-midi',$html);
				}
				if($ext[1] == 'mov' )
				{
					$html = str_replace('application/x-shockwave-flash','video/quicktime',$html);
				}
				//end edit*/
			}
		}
	}
	$my_files=array();

	return $html;
}
?>