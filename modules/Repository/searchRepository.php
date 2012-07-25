<?php
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/**
* upload files
*/
function search($vars) {
	global $menus, $links;
	
	// Get arguments from argument array
    extract($vars);
    //print_r($vars);
    
    /** Navigator **/
	$courseinfo = lnCourseGetVars($cid);
	$menus[]= $courseinfo['title'];
	$links[]= '#';
	lnBlockNav($menus,$links);
	/** Navigator **/

	echo '<TABLE width=100% CELLPADDING="0" CELLSPACING="0" BORDER="0"><TR><TD>';

	tabCourseAdmin($cid,2);

	echo '</TD></TR><TR><TD>';
	echo '<table class="main" width= 100% cellpadding=0 cellspacing=0  border=0>';
	echo '<tr><td valign=top><BR>';

?>
<script language="JavaScript" src="javascript/jquery.min.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/jquery-ui.min.js" type="text/javascript"></script>
<script language="JavaScript" src="javascript/jquery.blockUI.js" type="text/javascript"></script>
<link type="text/css" href="css/ui-lightness/jquery-ui.css" rel="stylesheet" />

<script language="JavaScript" type="text/javascript">
	$(function(){
		$("input#Search").click(function(){
	    	var url="modules/Repository/resultRepository.php";
	    	//var url="mod=Repository&file=resultRepository";
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

<script language="JavaScript" type="text/javascript">
	function upload(link,name,pid,title){
		$(function(){
	    	var url="modules/Repository/getContentRepository.php";
	    	var dataSet={link: link, filename: name, pid: pid, cid: <?php echo $cid;?>};
			//alert(link+"\n"+name+"\n"+pid+"\n"+title);
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
			    //$('div#result').html(data);
			    var url_repository = 'index.php?mod=Courses&file=admin&op=lesson&action=insert_lesson&item='+<?php echo $item; ?>+'&weight='+<?php echo $weight; ?>+'&cid='+<?php echo $cid; ?>+'&lid='+<?php echo $lid; ?>+'&parent_lid='+<?php echo $parent_lid; ?>+'&titlerepository='+title+'_'+data+'&filerepository='+data;
				//alert(url_repository);
			    window.location.href = url_repository;      
	    	});
		});
	}
</script>

<form id='searchform' method='post' action=''>
	<input name='item' id='item' type="hidden" value="<?php echo $item; ?>">
	<input name='weight' id='weight' type="hidden" value="<?php echo $weight; ?>">
	<input name='cid' id='cid' type="hidden" value="<?php echo $cid; ?>">
	<input name='lid' id='lid' type="hidden" value="<?php echo $lid; ?>">
	<input name='parent_lid' id='parent_lid' type="hidden" value="<?php echo $parent_lid; ?>">
	
	<p> <?php echo _KEYWORDS; ?> 
	<input type='text' name='keyword' id='keyword'><br>
	<?php echo _COLLECTION; ?>	
	<select id='selectCollection'>
		<option value="">All</option>
  		<?php 
  			include_once 'getCollectionRepository.php';
  			echo getCollection();
  		?>
	</select>
	<br>
	<?php echo _TOTALRESULT; ?>
	<select id='selectResult'>
		<option value="">All</option>
  		<option value="5">5</option>
  		<option value="15">15</option>
  		<option value="30">30</option>
  		<option value="50">50</option>
	</select>
	<br>
	</p>
	<input type='button' name='Search' id='Search' value='<?php echo _SEARCH; ?>'>
	
	<div id='result' name='result'></div>
</form>

<hr>
<div id="pleaseWait" style="display: none;">
	<img src="images/ajax-loader.gif"/><br><h1>Downloading..</h1>
</div>

<?php
	echo '</td></tr></table>';
	echo '</TD></TR></TABLE>';

	include 'footer.php'; 
}

?>