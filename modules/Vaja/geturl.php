<?php

include 'header.php';
/** Navigator **/
$menus= array(_ADMINMENU,_Vaja);
$links=array('index.php?mod=Admin','index.php?mod=Vaja&amp;file=admin');
lnBlockNav($menus,$links);
/** Navigator **/

OpenTable();

function getVajaServiceWav(){
	return lnConfigGetVar('VajaServiceWav');
}            
CloseTable();
include 'footer.php';

?>