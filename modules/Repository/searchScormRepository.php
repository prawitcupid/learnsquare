<?php
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

include 'header.php';
													
/** Navigator **/
$menus= array(_ADMINMENU,_SCORMADMIN,_SCORMIMPORTREPOSITORY);
$links=array('index.php?mod=Admin','index.php?mod=SCORM&amp;file=admin','index.php?mod=SCORM&amp;file=selectimport');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();
?>
<script language="JavaScript" src="javascript/jquery.min.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/jquery-ui.min.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/jquery.blockUI.js" type="text/javascript"></script>
<link type="text/css" href="css/ui-lightness/jquery-ui.css" rel="stylesheet" />

<script language="JavaScript" type="text/javascript">
	$(function(){
		$("input#Search").click(function(){
	    	var url="modules/Repository/resultScormRepository.php";
	    	var dataSet={keyword: $("#keyword").val(), selectCollection: $("#selectCollection").val(), selectResult: $("#selectResult").val(), RepositoryAddr: "<?php echo lnConfigGetVar('RepositoryAddr'); ?>" };
			//alert("keyword: "+$("#keyword").val()+", selectResult: "+$("#selectResult").val());
			$.blockUI({
	            message: $('#pleaseWait'),
	            css: {
	             border: 'none', 
	             padding: '15px', 
	             backgroundColor: '#000', 
	             '-webkit-border-radius': '10px', 
	             '-moz-border-radius': '10px', 
	             opacity: .5, 
	             color: '#fff'
	            } 
	         });
	    	$.post(url, dataSet, function(data){
	    		$.unblockUI();
			    $('div#result').html(data);      
	    	});
	    });
	});
</script>


<form id='searchform' method='post' action=''>

<p><?php echo _SEARCHANDSELECTSCORM; ?></p>
<p><?php echo _KEYWORD; ?> <input type='text' size="50" name='keyword' id='keyword'><br>
<?php echo _COLLECTION; ?>
	<select id='selectCollection'>
		<option value="">All</option>
  		<?php 
  			include_once 'getScormCollectionRepository.php';
  			echo getScormCollection();
  		?>
	</select>
	<br>
<?php echo _TOTALRESULT; ?> <select id='selectResult'>
	<option value="">All</option>
	<option value="5">5</option>
	<option value="15">15</option>
	<option value="30">30</option>
	<option value="50">50</option>
</select> <br>
</p>
<input type='button' name='Search' id='Search' value='<?php echo _SEARCH; ?>'>

<div id='result' name='result'></div>
</form>

<hr>
<div id="pleaseWait" style="display: none;"><img
	src="images/ajax-loader.gif" /><br>
<h1>Waiting...</h1>
</div>

<?php
	CloseTable();
	include 'footer.php';
?>