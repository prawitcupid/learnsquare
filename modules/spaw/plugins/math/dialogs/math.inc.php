<?php
//edit by Watee wichiennit 03/01/2008
//

$lang->setModule("math");
$lang->setBlock("math");
$spaw_a_targets = $config->getConfigValue("a_targets");
?>
<script type="text/javascript" src="<?php echo SpawConfig::getStaticConfigValue('SPAW_DIR') ?>plugins/math/dialogs/math.js"></script>
<script type="text/javascript">
<!--
<?php
//add cid for learnsquare
echo "var cid='" . $_GET['paths']."';\n";
//end add cid for learnsquare
?>
//-->
</script>
<?php
define('EQ_SIZE', 11);
define('INLINE_EQ_SIZE', 10);
define('LATEXRENDER','/latexrender/latex.php');
$target="self";
if(isset($_GET['target']))
$target=addslashes($_GET['target']);
// This renders the equation for the iframe and skips over the rest of this page.
// could also be used independently on any other page.
if(isset($_GET['render']))
{ 
	if(strlen($_GET['render'])) 
	{
		$text = "";
		$text = urldecode($_GET['render']);
				
		require($_SERVER['DOCUMENT_ROOT'].LATEXRENDER);
		echo nl2br(latexEqn($text, $fsize, '/pictures'));;
	}
}
else
{
?>

<script type="text/javascript">
var changed=false;

// Clears the main editor window
function cleartext()
{ 
 var id=document.getElementById('latex_formula');
 var okbtn = document.getElementById('okbtn');
 id.value = "";
 id.focus(); 
 okbtn.disabled = true;
 changed=false;
}

// Tries to inserts text at the cursor position of text area
//  wind = document                <- when inserting text into the current equation editor box  
//  wind = window.opener.document  <- when inserting text into a parent window box
function addText(wind, textbox, txt) 
{
	myField = wind.getElementById(textbox);
  // IE 
  if (wind.selection) 
  {
    myField.focus();
    sel = wind.selection.createRange();
    sel.text = txt;
  }
  // MOZILLA
  else 
  {
	  if (myField.selectionStart || myField.selectionStart == '0') 
    {
      var startPos = myField.selectionStart;
      var endPos = myField.selectionEnd;
			var cursorPos = startPos + txt.length;
      myField.value = myField.value.substring(0, startPos) + txt 
					+ myField.value.substring(endPos, myField.value.length);
			myField.selectionStart = cursorPos;
			myField.selectionEnd = cursorPos;
    } 
    else 
      myField.value += txt;
  }
}

function insertText( txt, pos )
{
	// pos = optional parameter defining where in inserted text to put the caret
	// if undefined put at end of inserted text
	// if pos=1000 then using style options and move to just before final }
	// startPos = final position of caret in complete text
	if (pos==1000)(pos=txt.length-1);
	if (pos==undefined)(pos=txt.length);
	
	// my textarea is called latex_formula
	myField = document.getElementById('latex_formula');
	if (document.selection) 	{
		// IE
		myField.focus();
		var sel = document.selection.createRange();
		// find current caret position
		var i = myField.value.length+1; 
		theCaret = sel.duplicate(); 
		while (theCaret.parentElement()==myField 
		&& theCaret.move("character",1)==1) --i; 
	
		// take account of line feeds
		var startPos = i - myField.value.split('\n').length + 1 ; 
	
		if (txt.substring(1,5) == "left" && sel.text.length)	{ 
			// allow highlighted text to be bracketed
			pos = txt.length + sel.text.length + 1;
			sel.text = txt.substring(0,7) + sel.text + txt.substr(6);	     
		} else {
			sel.text = txt;
		}
		// put caret in correct position to start editing
		var range = myField.createTextRange();
		range.collapse(true);
		range.moveEnd('character', startPos + pos);
		range.moveStart('character', startPos + pos);
		range.select();
	}
	else
	{
		// MOZILLA
		if (myField.selectionStart || myField.selectionStart == '0')	{
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;
			var cursorPos = startPos + txt.length;
			if (txt.substring(1,5) == "left" && endPos > startPos)	{ 
				// allow highlighted text to be bracketed
				pos = txt.length + endPos - startPos + 1;
				txt = txt.substring(0,7) + myField.value.substring(startPos, endPos) + txt.substr(6);	     
			}
			myField.value = myField.value.substring(0, startPos) + txt 
							+ myField.value.substring(endPos, myField.value.length);
		
			myField.selectionStart = cursorPos;
			myField.selectionEnd = cursorPos;
					
						// put caret in correct position to start editing
			myField.focus();
			myField.setSelectionRange(startPos + pos,startPos + pos);	      
		}
		else	
			myField.value += txt;
	}
	myField.focus();
}

// --- FCKEditor Integration ----
var oEditor = window.opener;
var FCKequation=null;
var eSelected=null;

// Loads the equations from the fckeditor, or create a default equations for an example
function LoadSelected()
{
  // Look for fckeditor
	if(oEditor)
    FCKequation = oEditor.FCKequation;
  if(FCKequation) 
	  eSelected = oEditor.FCKSelection.GetSelectedElement();

	if ( eSelected && eSelected.tagName == 'IMG' && eSelected._fckequation )	{
	  var comm = unescape( eSelected._fckequation );
	  var parts = comm.match( /\\f([\[\$])(.*?)\\f[\]\$]/ );
		
		document.getElementById('latex_formula').value = parts[2];

		if(parts[1]=='[')
		  document.getElementById('eqstyle2').checked=true;
		else
		  document.getElementById('eqstyle1').checked=true;		
	}
	else	{
	  document.getElementById('latex_formula').value = 'f(x)=\\int_{-\\infty}^x e^{-t^2}dt';
		eSelected == null ;
	}	
	renderEqn(null);
}


// Send the equation to the opening window.
function updateOpener()
{
  var text=document.getElementById('latex_formula').value;

<?php if($target) { 
// ---- Traditional mode with plane HTML text box -----
?>
  // Configure for insertion into phpBB fourm	
<?php if(isset($_GET['forum'])) { ?>
	text = '[tex]' + text + '[/tex]';
<?php } else	{ ?>
	if(document.getElementById('eqstyle').checked) 
	{ text = ' \\f$' + text + '\\f$ '; }
	else
	{ text = ' \\f[' + text + '\\f] '; }
<?php } ?>
	
	addText(window.opener.document,'<?php echo $target ?>',text);
<?php } else { 
// ---- Advanced mode with FCKEditor ----
?> 
	if (text.length == 0) 	{
		alert( FCKLang.EquationErrNoEqn ) ;
		return false ;
	}	
	
	if(document.getElementById('eqstyle').checked) 
	{ text = '\\f$' + text + '\\f$'; }
	else
	{ text = '\\f[' + text + '\\f]'; }

	if ( eSelected && eSelected._fckequation == text )
		return true ;

	FCKequation.Add( text ) ;
<?php } ?>	
	return true ;
}
</script>
<script src="ajax.js" type="text/javascript"></script>
<script>
var updateparent=null;
function processEquationChange() 
{
  // only if req shows "loaded"
  if (req.readyState == 4) {
		// only if "OK"
		if (req.status == 200) {
		  div = document.getElementById('equationview');
			div.innerHTML = "";
			div.innerHTML = req.responseText;
			
			if(updateparent!=null) { updateparent(); }
			updateparent=null;
		} else {
				alert("There was a problem retrieving the XML data:\n" +
						req.statusText);
	  }
	}
}

// Triggers the rendering of the equations within the iframe
function renderEqn(callback)
{
  var val='<?php echo  SpawConfig::getStaticConfigValue('SPAW_DIR').'plugins/math/phpmathpublisher/' ;?>derivemath.php?_cid=<?php echo $_GET['paths'];?>&formula=' + escape(document.getElementById('latex_formula').value.replace(/\+/g,"&plus;")); 
	//var val='<?php echo  SpawConfig::getStaticConfigValue('SPAW_DIR').'plugins/math/phpmathpublisher/' ;?>derivemath.php?formula=' + document.getElementById('latex_formula').value; 
 var okbtn = document.getElementById('okbtn');
 okbtn.disabled = false;
  if(document.getElementById('eqstyle').checked) 
   { val+='&style=inline'; }
	updateparent=callback;
	 
	// Update the  
	changed=false;
	
	div = document.getElementById('equationview');
	
	div.innerHTML = "Rendering Equation <img src='<?php echo  SpawConfig::getStaticConfigValue('SPAW_DIR').'plugins/math/lib/theme/spaw2/img/'; ?>wait.gif' width=\"13\" height =\"13\" border=\"0\" />";
	    
	if (window.XMLHttpRequest) {
		
			req = new XMLHttpRequest();
			req.onreadystatechange = processEquationChange;
			req.open("GET", val, true);
			
			req.send(null);
	// branch for IE/Windows ActiveX version
	} else if (window.ActiveXObject) {
		
			isIE = true;
			req = new ActiveXObject("Microsoft.XMLHTTP");
			if (req) {
					req.onreadystatechange = processEquationChange;
					req.open("GET", val, true);
					req.send();
			}
	}
	
	//loadXMLDoc(val, processEquationChange);
}
LoadSelected();
</script>
<style>
  td { font-size:12px; font-family:Arial, Helvetica, sans-serif; color:#666666 }
	body { padding:0; margin:0; }
</style>
<form name="a_prop" onsubmit="return false;">
  <table width="100%" height="450px" border="0" cellpadding="2" cellspacing="0" id="prop_table">
    <tr>
      <td width="100%" height="10" align="center" bgcolor="#efefde">
	  
	  <!-- <select name="spaces" id="spaces" style="width:100" onChange="insertText(this.options[this.selectedIndex].value); this.selectedIndex=0;">
          <option selected value="" style="color:#8080ff">Spaces...</option>
          <option value="\,">thin</option>
          <option value="\:">medium</option>
          <option value="\;">thick</option>
          <option value="\!">negative</option>
        </select>
          </select>
          <select name="style" id="style" style="width:100" onChange="insertText(this.options[this.selectedIndex].value, 1000); this.selectedIndex=0;">
            <option selected value="" style="color:#8080ff">Style...</option>
            <option value="\textrm{}">Roman</option>            
            <option value="\textbf{}">Bold</option>
            <option value="\textit{}">Italics</option>
          </select>
          </select>
          <select name="style" id="style" style="width:100" onChange="insertText(this.options[this.selectedIndex].value); this.selectedIndex=0;">
            <option selected value="" style="color:#8080ff">Foreign...</option>
            <option value="\oe">oe</option>
            <option value="\OE">OE</option>
            <option value="\ae">ae</option>
            <option value="\AE">AE</option>
            <option value="\aa">aa</option>
            <option value="\AA">AA</option>
            <option value="\o">o</option>
            <option value="\O">O</option>
            <option value="\l">l</option>
            <option value="\L">L</option>
            <option value="\ss">ss</option>
            <option value="\dag">dag</option>
            <option value="\ddag">ddag</option>
            <option value="\S">S</option>
            <option value="\P">P</option>
            
          </select>
          </select> -->
         
        &nbsp;&nbsp;&nbsp;&nbsp;<a href="../plugins/math/dialogs/doc/help.html" target="_blank"><img src="<?php echo  SpawConfig::getStaticConfigValue('SPAW_DIR').'plugins/math/lib/theme/spaw2/img/'; ?>i.gif" align="absmiddle" width="13" height="13" border="0">&nbsp;Help</a> </td>
    </tr>
    <tr>
      <td align="center" height="79" bgcolor="#efefde"><img src="<?php echo  SpawConfig::getStaticConfigValue('SPAW_DIR').'plugins/math/lib/theme/spaw2/img/'; ?>equation_editor2.gif" width="550" height="79" border="0" usemap="#equationeditormap"></td>
    </tr>
    <tr>
      <td align="center" height="80" bgcolor="#efefde"><textarea name="latex_formula" id="latex_formula" rows="6" cols="72" onChange="changed=true"></textarea>
          <br></td>
    </tr>
    <tr>
      <td colspan="2" align="center" height="15" valign="middle" bgcolor="#efefde"> <input type="radio" id="eqstyle" name="eqstyle" value="inline" style="visibility:hidden;>
        
        <input type="radio" id="eqstyle2" name="eqstyle" value="display" checked style="visibility:hidden; ">
       
        <input name="button" type="button" style="font-size:11px" onClick="cleartext()" value="Clear Expression">
        &nbsp; &nbsp;
        <input name="button" type="button"  style="font-size:11px" onClick="renderEqn(null)" value="Render Expression">
        &nbsp; &nbsp;
       </td>
    </tr>
    <tr>
      <td align="center" height="25" valign="top"><b>Click Render Expression to see your equation rendered below...</b></td>
    </tr>
    <tr>
      <td align="center" valign="middle"><div id="equationview"></div></td>
    </tr>
  </table>
  <map name="equationeditormap">
    <area shape="rect" alt="bigcap{a}{b}{x}" title="bigcap{a}{b}{x}" coords="6,4,30,27" href="javascript:insertText(' bigcap{ }{ }{ }',9)">
    <area shape="rect" alt="bigcup{a}{b}{x}" title="bigcup{a}{b}{x}" coords="35,4,59,27" href="javascript:insertText(' bigcup{ }{ }{ }',9)">
    <area shape="rect" alt="prod{a}{b}{x}" title="prod{a}{b}{x}" coords="68,4,92,27" href="javascript:insertText(' prod{ }{ }{ }',7)">
    <area shape="rect" alt="coprod{a}{b}{x}" title="coprod{a}{b}{x}" coords="97,4,121,27" href="javascript:insertText(' coprod{ }{ }{ }',9)">
    <area shape="rect" alt="int{a}{b}{x}" title="int{a}{b}{x}" coords="130,4,154,27" href="javascript:insertText(' int{ }{ }{ }',6)">
    <area shape="rect" alt="oint{a}{b}{x}" title="oint{a}{b}{x}" coords="159,4,183,27" href="javascript:insertText(' oint{ }{ }{ }',7)">
    <area shape="rect" alt="sum{a}{b}{x}>" title="sum{a}{b}{x}>" coords="188,4,212,27" href="javascript:insertText(' sum{ }{ }{ }',6)">
    <area shape="rect" alt="a_b" title="a_b" coords="221,4,245,27" href="javascript:insertText('{}_{}',1)">
    <area shape="rect" alt="a^b" title="a^b" coords="250,4,274,27" href="javascript:insertText('{}^{}',1)">
    <area shape="rect" alt="root{n}{a}" title="root{n}{a}" coords="279,4,303,27" href="javascript:insertText(' root{ }{ }',6)">
    <area shape="rect" alt="lim {x right 0} { }" title="lim {x right 0} { }" coords="308,4,332,27" href="javascript:insertText(' lim {x right 0} { }',6)">
    <area shape="rect" alt="delim{[}{ }{]} " title="delim{[}{ }{]} " coords="341,4,365,27" href="javascript:insertText(' delim{[}{ }{]} ',6)">
    <area shape="rect" alt="( ) " title="( )" coords="370,4,394,27" href="javascript:insertText(' ( )',6)">
    <area shape="rect" alt="delim{|}{ }{|}" title="delim{|}{ }{|}" coords="399,4,423,27" href="javascript:insertText(' delim{|}{ }{|}',6)">
    <area shape="rect" alt="a/b" title="a/b" coords="428,4,452,27" href="javascript:insertText(' {}/{}',6)">
    <area shape="rect" alt="bracelet" title="bracelet" coords="458,4,482,27" href="javascript:insertText(' lbrace  rbrace',19)">
    <area shape="rect" alt="matrix" title="matrix" coords="490,4,514,27" href="javascript:insertText(' matrix{num of lines}{num of columns}{first_element ... last_element}',20)">
    <area shape="rect" alt="table" title="table" coords="519,4,543,27" href="javascript:insertText(' tabular{lines description}{columns description}{first_element ... last_element}
',20)">
    <area shape="rect" alt="alpha" title="alpha" coords="6,34,17,45" href="javascript:insertText(' alpha')">
    <area shape="rect" alt="beta" title="beta" coords="22,34,33,45" href="javascript:insertText(' beta')">
    <area shape="rect" alt="gamma" title="gamma" coords="38,34,49,45" href="javascript:insertText(' gamma')">
    <area shape="rect" alt="delta" title="delta" coords="54,34,65,45" href="javascript:insertText(' delta')">
    <area shape="rect" alt="epsilon" title="epsilon" coords="70,34,81,45" href="javascript:insertText(' epsilon')">
    <area shape="rect" alt="varepsilon" title="varepsilon" coords="86,34,97,45" href="javascript:insertText(' varepsilon')">
    <area shape="rect" alt="zeta" title="zeta" coords="102,34,113,45" href="javascript:insertText(' zeta')">
    <area shape="rect" alt="eta" title="eta" coords="118,34,129,45" href="javascript:insertText(' eta')">
    <area shape="rect" alt="theta" title="theta" coords="134,34,145,45" href="javascript:insertText(' theta')">
    <area shape="rect" alt="vartheta" title="vartheta" coords="150,34,161,45" href="javascript:insertText(' vartheta')">
    <area shape="rect" alt="iota" title="iota" coords="166,34,177,45" href="javascript:insertText(' iota')">
    <area shape="rect" alt="kappa" title="kappa" coords="182,34,193,45" href="javascript:insertText(' kappa')">
    <area shape="rect" alt="lambda" title="lambda" coords="198,34,209,45" href="javascript:insertText(' lambda')">
    <area shape="rect" alt="mu" title="mu" coords="214,34,225,45" href="javascript:insertText(' mu')">
    <area shape="rect" alt="nu" title="nu" coords="230,34,241,45" href="javascript:insertText(' nu')">
    <area shape="rect" alt="<=" title="<=" coords="300,34,311,45" href="javascript:insertText(' <=')">
    <area shape="rect" alt="<<" title="<<" coords="316,34,327,45" href="javascript:insertText(' {<}{<}')">
    <area shape="rect" alt=">=" title=">=" coords="332,34,343,45" href="javascript:insertText(' >=')">
    <area shape="rect" alt=">>" title=">>" coords="348,34,359,45" href="javascript:insertText(' {>}{>}')">
    <area shape="rect" alt="approx" title="approx" coords="365,34,376,45" href="javascript:insertText(' approx')">
    <area shape="rect" alt="<>" title="<>" coords="381,34,392,45" href="javascript:insertText(' <>')">
    <!-- <area shape="rect" alt="succeq" title="succeq" coords="400,34,411,45" href="javascript:insertText(' succeq')"> -->
    <area shape="rect" alt="wedge" title="wedge" coords="421,34,432,45" href="javascript:insertText(' wedge')">
    <area shape="rect" alt="pm" title="pm" coords="436,34,447,45" href="javascript:insertText(' pm')">
    <area shape="rect" alt="ortho" title="ortho" coords="452,34,463,45" href="javascript:insertText(' ortho')">
    <area shape="rect" alt="backslash" title="backslash" coords="468,34,479,45" href="javascript:insertText(' backslash')">
    <area shape="rect" alt="prime" title="prime" coords="484,34,495,45" href="javascript:insertText(' prime')">
    <area shape="rect" alt="cdots" title="cdots" coords="500,34,511,45" href="javascript:insertText(' cdots')">
    <area shape="rect" alt="vdots" title="vdots" coords="511,34,524,43" href="javascript:insertText(' vdots')"> 
    <area shape="rect" alt="ddots" title="ddots" coords="532,34,543,45" href="javascript:insertText(' ddots')">
    <area shape="rect" alt="xi" title="xi" coords="6,48,17,59" href="javascript:insertText(' xi')">
    <area shape="rect" alt="pi" title="pi" coords="22,48,33,59" href="javascript:insertText(' pi')">
    <area shape="rect" alt="varpi" title="varpi" coords="38,48,49,59" href="javascript:insertText(' varpi')">
    <area shape="rect" alt="rho" title="rho" coords="54,48,65,59" href="javascript:insertText(' rho')">
    <area shape="rect" alt="varrho" title="varrho" coords="70,48,81,59" href="javascript:insertText(' varrho')">
    <area shape="rect" alt="sigma" title="sigma" coords="86,48,97,59" href="javascript:insertText(' sigma')">
    <area shape="rect" alt="varsigma" title="varsigma" coords="102,48,113,59" href="javascript:insertText(' varsigma')">
    <area shape="rect" alt="tau" title="tau" coords="118,48,129,59" href="javascript:insertText(' tau')">
    <area shape="rect" alt="upsilon" title="upsilon" coords="134,48,145,59" href="javascript:insertText(' upsilon')">
    <area shape="rect" alt="phi" title="phi" coords="150,48,161,59" href="javascript:insertText(' phi')">
    <area shape="rect" alt="varphi" title="varphi" coords="166,48,177,59" href="javascript:insertText(' varphi')">
    <area shape="rect" alt="chi" title="chi" coords="182,48,193,59" href="javascript:insertText(' chi')">
    <area shape="rect" alt="psi" title="psi" coords="198,48,209,59" href="javascript:insertText(' psi')">
    <area shape="rect" alt="omega " title="omega " coords="214,48,225,59" href="javascript:insertText(' omega ')">
    <area shape="rect" alt="subset" title="subset" coords="252,48,263,59" href="javascript:insertText(' subset')">
    <area shape="rect" alt="notsubset" title="notsubset" coords="268,48,279,59" href="javascript:insertText(' notsubset')">
    <area shape="rect" alt="in" title="in" coords="284,48,295,59" href="javascript:insertText(' in')">
    <area shape="rect" alt="notin" title="notin" coords="300,48,311,59" href="javascript:insertText(' notin')">
    <area shape="rect" alt="inter" title="inter" coords="316,48,327,59" href="javascript:insertText(' inter')">
    <area shape="rect" alt="union" title="union" coords="332,48,343,59" href="javascript:insertText(' union')">
    <area shape="rect" alt="left" title="left" coords="374,48,398,59" href="javascript:insertText(' left')">
    <area shape="rect" alt="doubleleft" title="doubleleft" coords="403,48,427,59" href="javascript:insertText(' doubleleft')">
    <area shape="rect" alt="right" title="right" coords="432,48,456,59" href="javascript:insertText(' right')">
    <area shape="rect" alt="doubleright" title="doubleright" coords="461,48,485,59" href="javascript:insertText(' doubleright')">
    <area shape="rect" alt="leftright" title="leftright" coords="490,48,514,59" href="javascript:insertText(' leftright')">
    <area shape="rect" alt="doubleleftright " title="doubleleftright " coords="519,48,543,59" href="javascript:insertText(' doubleleftright')">
    <area shape="rect" alt="Gamma" title="Gamma" coords="6,62,17,73" href="javascript:insertText(' Gamma')">
    <area shape="rect" alt="Delta" title="Delta" coords="22,62,33,73" href="javascript:insertText(' Delta')">
    <area shape="rect" alt="Theta" title="Theta" coords="38,62,49,73" href="javascript:insertText(' Theta')">
    <area shape="rect" alt="Lambda" title="Lambda" coords="54,62,65,73" href="javascript:insertText(' Lambda')">
    <area shape="rect" alt="Xi" title="Xi" coords="70,62,81,73" href="javascript:insertText(' Xi')">
    <area shape="rect" alt="Pi" title="Pi" coords="86,62,97,73" href="javascript:insertText(' Pi')">
    <area shape="rect" alt="Sigma" title="Sigma" coords="102,62,113,73" href="javascript:insertText(' Sigma')">
    <area shape="rect" alt="Upsilon" title="Upsilon" coords="118,62,129,73" href="javascript:insertText(' Upsilon')">
    <area shape="rect" alt="Phi" title="Phi" coords="134,62,145,73" href="javascript:insertText(' Phi')">
    <area shape="rect" alt="Psi" title="Psi" coords="150,62,161,73" href="javascript:insertText(' Psi')">
    <area shape="rect" alt="Omega" title="Omega" coords="166,62,177,73" href="javascript:insertText(' Omega')">
    <area shape="rect" alt="circ" title="circ" coords="252,62,263,73" href="javascript:insertText(' circ')">
    <area shape="rect" alt="notexists" title="notexists" coords="268,62,279,73" href="javascript:insertText(' notexists')">
    <area shape="rect" alt="varnothing" title="varnothing" coords="284,62,295,73" href="javascript:insertText(' varnothing')">
    <area shape="rect" alt="forall" title="forall" coords="300,62,311,73" href="javascript:insertText(' forall')">
    <area shape="rect" alt="exists" title="exists" coords="316,62,327,73" href="javascript:insertText(' exists')">
    <area shape="rect" alt="partial" title="partial" coords="332,62,343,73" href="javascript:insertText(' partial')">
    <area shape="rect" alt="infty" title="infty" coords="348,62,363,73" href="javascript:insertText(' infty')">
    <!-- <area shape="rect" alt="aleph" title="aleph" coords="404,62,415,73" href="javascript:insertText(' aleph')">
    <area shape="rect" alt="hbar" title="hbar" coords="420,62,431,73" href="javascript:insertText(' hbar')">
    <area shape="rect" alt="imath" title="imath" coords="436,62,447,73" href="javascript:insertText(' imath')">
    <area shape="rect" alt="jmath" title="jmath" coords="452,62,463,73" href="javascript:insertText(' jmath')">
    <area shape="rect" alt="ell" title="ell" coords="468,62,479,73" href="javascript:insertText(' ell')"> -->
    <area shape="rect" alt="bbR" title="bbR" coords="484,62,495,73" href="javascript:insertText(' bbR')">
    <area shape="rect" alt="bbN" title="bbN" coords="500,62,511,73" href="javascript:insertText(' bbN')">
    <area shape="rect" alt="bbZ" title="bbZ" coords="516,62,527,73" href="javascript:insertText(' bbZ')">
    <area shape="rect" alt="bbC" title="bbC" coords="532,62,543,73" href="javascript:insertText(' bbC')">
  </map>
  <table border="0" cellpadding="2" cellspacing="0" width="100%">
<tr>
<td nowrap>
<hr width="100%">
</td>
</tr>
<tr>
<td align="right" valign="bottom" nowrap>

<input type="submit" id = 'okbtn' value="<?php echo $lang->m('ok')?>" onClick="SpawMathDialog.okClick()" class="bt" disabled alt="Please click 'Render Expression' button before add equation." >
<input type="button" value="<?php echo $lang->m('cancel')?>" onClick="SpawMathDialog.cancelClick()" class="bt">
</td>
</tr>
</table>

</form>
<?php } ?>