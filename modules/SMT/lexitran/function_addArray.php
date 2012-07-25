<?php
function addArray(&$array, $id,	$var)
{
	 $tempArray	=	array($var =>	$id);
	 $array	=	array_merge	($array, $tempArray);
}
?>