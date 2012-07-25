<?php
class smt_html {
	protected $_rawHTML;
	protected $_nobody = false;
	protected $_file;
	protected $_pBody = '/<body[^\b]*body>/';
	
	protected function File($str) {
		$pattern = '/.htm[l]?/';
		$this->_file['name'] = preg_replace($pattern, '', $str);
	}
	
	protected function _readHTML($filename) {
		$h = @fopen($filename,'r') or die ("Can't open file ". $filename);
		$text = fread($h, filesize ( $filename ));
		fclose($h);
		return $this->_fixSpaw($text);
	}
	
	protected function _fixSpaw($string) {
		$string = str_replace('<span 13px;="" \"="" apple-style-span\""="" class="\">','',$string);
		$string = str_replace('</span><!--RICHEDIT-->','',$string);
		$string = str_replace('<!--RICHEDIT-->','',$string);
		return $string;
	}
	
	protected function _getBody() {
		if ($this->_rawHTML == "") {
			$this->_rawHTML = $this->_readHTML($this->_file['path']);
		}
		preg_match ( $this->_pBody, $this->_rawHTML, $result );
		if(!$result[0]) {
			$this->_nobody = true;
			$out = $this->_rawHTML;
		}else{
			$out = $result[0];
		}
		return $out;
	}
	
	protected function _splitText($string) {
		$pattern = '/(\.)|(\?)/';
		return preg_split ( $pattern, $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	}
	
	protected function _splitWord($string) {
		$pattern = '/\W/';
		return preg_split ( $pattern, $string, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	}
	
	protected function _unTabSpace($string) {
		$string = str_replace ( "\n", "", $string );
		$string = str_replace ( "\t", " ", $string );
		$pattern = '/[ |]+/';
		return preg_replace ( $pattern, ' ', $string );
	}
	
	protected function _buildSupaHTML($noTag,$withTag) {
		if(count($noTag) != count($withTag)) {
			for ($i = 0; $i < count($noTag); $i++) {
				while ($noTag[$i] != strip_html_tags($withTag[$i])) {
					$tmp = $withTag[$i] . $withTag[$i+1];
					array_splice($withTag, $i, 2, $tmp);
				}
				if( ($withTag[$i] == ".") || ($withTag[$i] == "?") ) $withTag[$i] .= "<img class='supa' id='$i'>";
			}
		}		
		$filename = $this->_file['dir'] . 'suparsit_' . $this->_file['name'] . '.html';
		$handle = fopen($filename, 'w');
		$body = implode("",$withTag);
		$newhtml = preg_replace($this->_pBody, $body, $this->_rawHTML);
		$script = '<script src="../../modules/SMT/javascript/jquery-1.3.2.min.js"></script>
<script src="../../modules/SMT/javascript/wtooltip.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		var xml = $.ajax({
  			url: "suparsit_'.$this->_file['name'].'.xml'.'",
  			async: false
 		}).responseXML;
		$(".supa").each(function(){this.src = "../../images/translator.png";});
		$(".supa").wTooltip({
     			content: true,
     			offsetY: -30,
				callBefore: function(tooltip,node) {					
        			$(tooltip).html($("#"+node.id ,xml).text());
				}
		});		
	});
</script>';
		if($this->_nobody){
			$newhtml = $script . $body;
		}else{
			$newhtml = str_replace("</head>",$script . "\n</head>",$newhtml);
		}
		fwrite($handle, $newhtml);
		fclose($handle);
	}
		
	protected function _buildLexHTML($words) {
		$htmlNEW = replaceWord($this->_rawHTML,$words);		
		$filename = $this->_file['dir'] . 'lexitron_' . $this->_file['name'] . '.html';
		$handle = fopen($filename, 'w');
		$script = '<script src="../../modules/SMT/javascript/jquery-1.3.2.min.js"></script>
<script src="../../modules/SMT/javascript/wtooltip.js"></script>';
		
		$script_lex = '<script src="../../modules/SMT/javascript/x3.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		var xml = $.ajax({
  			url: "lexitron_'.$this->_file['name'].'.xml'.'",
  			async: false
 		}).responseXML;		
		$("span.lex").wTooltip({
     			content: true,
     			offsetY: -30,
				callBefore: function(tooltip,node) {					
        			$(tooltip).html(makeContents(node.id,xml).html());
				}
		});		
	});
</script>';
		$script = $script . $script_lex;
		if($this->_nobody){
			$htmlNEW = $script . $htmlNEW;
		}else{
			$htmlNEW = str_replace("</head>",$script . "\n</head>\n",$htmlNEW);
		}
		fwrite($handle, $htmlNEW);
		fclose($handle);
		
		// Build SMT file
		$supa_file = $this->_file['dir'] . 'suparsit_' . $this->_file['name'] . '.html';
		if (file_exists($supa_file)){
			$html = $this->_readHTML($supa_file);
			$html = replaceWord($html,$words);
			$filename = $this->_file['dir'] . 'smt_' . $this->_file['name'] . '.html';
			$handle = fopen($filename, 'w');
			$html = str_replace('<script src="../../modules/SMT/javascript/wtooltip.js"></script>','<script src="../../modules/SMT/javascript/wtooltip.js"></script>' . $script_lex . "\n",$html);
			fwrite($handle, $html);
			fclose($handle);
		}
	}
	
	public function __construct($string) {
		$this->setPath($string);
	}
	
	public function setPath($str) {
		$this->_file['path'] = $str;
		if (!empty ( $this->_file['path'] )) {
			$pattern = '/[^\/]+.htm[l]?$/';
			preg_match ( $pattern, $this->_file['path'], $result );
			$this->File($result[0]);
			$this->_file['dir'] = str_replace ( $result[0], "", $this->_file['path'] );
		} else {
			die("PATH is Empty");			
		}
		return $this->_file;
	}
	public function getFile() {
		return $this->_file;
	}
	public function getSentence() {
		$html = $this->_unTabSpace($this->_getBody());
		$noTag = $this->_splitText(strip_html_tags($html));
		$withTag = $this->_splitText($html);
		$this->_buildSupaHTML($noTag,$withTag);
		return $noTag;
	}
	public function getWord() {
		$html = $this->_unTabSpace($this->_getBody());
		$noTag = array_values(array_filter(array_unique($this->_splitWord(strip_html_tags($html))),"Nanny"));
		$this->_buildLexHTML($noTag);
		return $noTag;
	}
}

class smt_translator {
	protected $_client;
	protected $_name;
	protected $_timeout;

	public function __construct($timeout, $url = "http://203.185.132.252:8080/ws1/services/ServiceCenterService?wsdl", $name = 'translateEngThaiMosesSocket') {
		$this->_timeout = $timeout;
		$this->_name = $name;
		$this->_client = new nusoap_client ( $url, 'wsdl', '', '', '', '' );
		$this->_client->soap_defencoding = "UTF-8";
		$this->_client->decode_utf8 = false;
		$err = $this->_client->getError ();
		if ($err) {
			echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
		}
	}
	/*
	 * Input String Eng
	 * Output String Thai
	 */
	public function suparsitTranslate($str,&$stat) {
		$param = array ('srcEn' => $str, 'timeout' => $this->_timeout );
		$result = $this->_client->call ( $this->_name, array ('parameters' => $param ), '', '', false, true );
		// Check for a fault
	
		if ($this->_client->fault) {
			$stat[0] = false;
		} else {
			// Check for errors
			$err = $this->_client->getError ();
			if ($err) {
				// Display the error
				$stat[0] = false;
			} else {
				return $result ['return'];
			}
		}
		/*
		if ($this->_client->fault) {
			echo '<h2>Fault</h2><pre>';
			print_r ( $result );
			echo '</pre>';
		} else {
			// Check for errors
			$err = $this->_client->getError ();
			if ($err) {
				// Display the error
				echo '<h2>Error</h2><pre>' . $err . '</pre>';
			} else {
				return $result ['return'];
			}
		}
		*/
	}
	public function lexitronTranslate($word){
		// --	connect	database
		// Configuration for LEXiTRON database
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
	
		$ettable = $lntable['et'];
		$etcolumn = &$lntable['et_column'];
		if($dbconn->ErrorNo() != 0) {
			echo "error cannot connect et";
			return;
		}
		$word = trim($word);
		$word = trim($word,'');
		$condition = "$etcolumn[esearch] = '$word'";
	
		$query = "SELECT $etcolumn[esearch], $etcolumn[ecat], $etcolumn[tentry], 
		$etcolumn[esyn], $etcolumn[eant], $etcolumn[esample]
		FROM $ettable
		WHERE $condition";
		$result = $dbconn->Execute($query) or die ("<b>Error Query ET</b>");
		// Name array
		$resultName = array("word","pos","meaning","syn","ant","ex");
		//Set of Meaning
		$meanings = array();
		while($lexOuts = $result->fields){			
			$result->MoveNext();
			array_push($meanings,array_combine($resultName,array_slice($lexOuts,0,6)));	
		}		
		return $meanings;
	}
}

class SMTXML extends XMLWriter {
	/**
	 * @param array $file = data from smt_html::getFile()
	 * @param int $type 0 = suparsit , 1 = lexitron
	 */
	public function __construct($file,$type = 0) {
		$prefix = array("suparsit_","lexitron_");
		$this->openURI($file['dir'] . $prefix[$type] . $file['name'] . '.xml');
		$this->startDocument('1.0','utf-8');
		$this->setIndent(4);
		//--SMT--
		$this->startElement("smt");
		$this->writeAttribute("filename",$file['name'] . ".html");
		$this->writeAttribute("hash", hash_file('md5', $file['path']));
	}
	
	public function endXML() {
		$this->endElement(); //--SMT--
		$this->endDocument();
		$this->flush();
	}	
}

class SuparsitXML extends SMTXML {
	public function addMeaning($id,$mean) {
		//--Mean--
		$this->startElement("meaning");
		$this->writeAttribute("id", $id);
		$this->writeRaw(htmlspecialchars($mean));
		$this->endElement();//--Mean--
	}
}

class LexitronXML extends SMTXML {
	public function addMeaning($id,$mean) {
		//--Mean--
		$this->startElement("meaning");
		$this->writeAttribute("id", $id);
		foreach ($mean as $var) {
			$this->startElement("mirror");
			$this->writeElement("word",htmlspecialchars($var['word']));
			$this->writeElement("pos",htmlspecialchars($var['pos']));
			$this->writeElement("mean",htmlspecialchars($var['meaning']));
			$this->writeElement("syn",htmlspecialchars($var['syn']));
			$this->writeElement("ant",htmlspecialchars($var['ant']));
			$this->writeElement("ex",htmlspecialchars($var['ex']));			
			$this->endElement();//--Mirror--
		}		
		$this->endElement();//--Mean--
	}
}


class smtChecker {
	protected $_file;
	
	public function __construct($file) {
		$this->_file = $file;
	}
	
	public function suparsitCheck() {
		$xml = new XMLReader();
		if(!$xml->open($this->_file['dir'] . "suparsit_" . $this->_file['name'] . '.xml')) {
			// Can't open file
			return false;
		}
		$xml->read();
		if(hash_file('md5', $this->_file['path']) != $xml->getAttribute('hash')){
			// Non match hash
			return false;
		}
		return true;
	}
	
	public function lexitronCheck() {
		$xml = new XMLReader();
		if(!$xml->open($this->_file['dir'] . "lexitron_" . $this->_file['name'] . '.xml')) {
			// Can't open file
			return false;
		}
		$xml->read();
		if(hash_file('md5', $this->_file['path']) != $xml->getAttribute('hash')){
			// Non match hash
			return false;
		}
		return true;
	}
}

function SMT_Run($path = null, $timeout = 6000 ) {
	$html = new smt_html($path);
	$smt = new smt_translator($timeout);
	$file = $html->getFile();
	$chk = new smtChecker($file);
	
	$SUPARSITStatus =lnConfigGetVar('SUPARSITStatus');
	$LEXITRONStatus =lnConfigGetVar('LEXITRONStatus');
	if($SUPARSITStatus&&!$chk->suparsitCheck()){
		$sXml = new SuparsitXML($file,0);
		$sentences = $html->getSentence();
		$errStack = array(true);
		for ($i = 0;$i < count($sentences); $i++) {
			if(($sentences[$i] == ".") || ($sentences[$i] == "?")) continue;
			$sXml->addMeaning($i+1, $smt->suparsitTranslate($sentences[$i]),$errStack);
		}
		//Show Error
		if(!$errStack[0])echo "<center><p>Suparsit Transalation has Error<br />การแปลภาษาด้วยระบบสุภาษิตเกิดข้อผิดพลาด</p></center>";
		$sXml->endXML();
		if($chk->lexitronCheck()&&!file_exists($file['dir'] . 'smt_' . $file['name'] . '.html')){
			$html->getWord();
		}
	}
	if($LEXITRONStatus&&!$chk->lexitronCheck()){
		$lXml = new LexitronXML($file,1);
		$words = $html->getWord();
		for ($i = 0;$i < count($words); $i++) {
			if(($words[$i] == ".") || ($words[$i] == "?")) continue;
			$lXml->addMeaning($i, $smt->lexitronTranslate($words[$i]));			
		}
		$lXml->endXML();
	}	
}
/**
 * Remove HTML tags, including invisible text such as style and
 * script code, and embedded objects.  Add line breaks around
 * block-level tags to prevent word joining after tag removal.
 */
function strip_html_tags( $text )
{
    $text = preg_replace(
        array(
          // Remove invisible content
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
          // Add line breaks before and after blocks
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text );
    return strip_tags( $text );
}  

function print_rx(&$arr) {
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}

function makeSpan($input,$words) {
	foreach ($words as $i => $var) {
		if(preg_match("/^$var$/si",$input)){
			$input = preg_replace("/^($var)$/si",'<span class="lex" id="' . $i . '" >$1</span>',$input);
			return $input;
		}		
	}
	return $input;
}

function replaceWord($input,$words) {
	$patt = '@(.*?)(<([\w]+)[^>]*>)(.*?)</\3>@si';
	if(!preg_match_all($patt,$input,$m,PREG_SET_ORDER)) {
		return replaceUnTag($input,$words);
	}else{
		$endtxt = $input;
		foreach ($m as &$set){
			$endtxt = str_replace($set[0],'',$endtxt);
			if(preg_match('@(head)|(style)|(script)|(object)|(embed)|(applet)|(noframes)|(noscript)|(noembed)@si',$set[3])){
				$set = replaceUnTag($set[1],$words) . $set[2] . $set[4] . "</$set[3]>";
			}else{
				$set = replaceUnTag($set[1],$words) . $set[2] . replaceWord($set[4],$words) . "</$set[3]>";
			}
		}
		return implode('',$m) . replaceUnTag($endtxt,$words);
	}	
}

function replaceUnTag($input,$words) {
	$patt = '@(<[^\s]+[^>]*>)@si';
	$piece = preg_split($patt,$input,-1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	print_r($arr);
	foreach ($piece as &$var) {
		if(!preg_match($patt,$var)){
			$sp = preg_split ( '/(\W)/', $var, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			foreach ($sp as &$spx) {
				$spx = makeSpan($spx,$words);
			}
			$var = implode('',$sp);
		}
	}
	return implode('',$piece);
}

function Nanny($str) {
	if(preg_match('/[0-9]+/si',$str)) return false;
	else return true;	
}
?>