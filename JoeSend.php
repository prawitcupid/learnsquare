<?php
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
	header("Cache-Control: no-cache");
	header("Cache-Control: post-check=0,pre-check=0");
	header("Cache-Control: max-age=0");
	header("Pragma: no-cache");
	header('Content-type: text/html; charset=utf-8');
	echo '<tr><td>'.$_POST['joe_text'] .'</td></tr>';
?>