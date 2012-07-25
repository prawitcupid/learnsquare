<?php

global $PHP_SELF;

if (preg_match("/footer.php/i", $PHP_SELF)) {
	die ("You can't access this file directly...");
}

function footmsg()
{
	
   echo lnConfigGetVar('foot');
   
}

function foot()
{
    global $index;

    themefooter();

	echo "</body>\n</html>";
}

foot();

?>