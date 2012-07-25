<?php
/*- - - send mail function - - - -*/
function mailsock($from,$recipient,$bcc,$subject,$msg) {
	global $config;

//	return true;

	$msg=stripslashes($msg);

	//1//mail by socket

	$server="nnet.nectec.or.th";
	$header="To:$recipient\r\n";
	$header.="From: $from\r\n";
	$header.="Subject: $subject\r\n";
	$header.="Content-Type: text/html; charset=window-874\r\n";
	$header.="X-Priority: 3\r\n";
	$header.="cc: ";

	for ($i=0; $i < sizeof ($bcc) ; $i++) {
		$header .= $bcc[$i];
		if ($i < sizeof($bcc)-1)
				$header .= ",";
	}

	$header.="\r\n";

	$body=$msg."\r\n";

	$socks=fsockopen($server,25);

	if (!$socks) return 0; // mail server is not ready
																						// for debuging			
	fputs($socks, "HELO\r\n");									$reply=fgets($socks,1024); echo "$reply.";
	fputs($socks, "MAIL FROM: $from\r\n");			$reply=fgets($socks,1024); echo "$reply.";
	fputs($socks, "RCPT TO: $recipient\r\n");	//	$reply=fgets($socks,1024); echo "$reply.";
	fputs($socks, "DATA \r\n$header\r\n");		//	$reply=fgets($socks,1024); echo "$reply.";
	fputs($socks, "$body \r\n.\r\n");						//	$reply=fgets($socks,1024); echo "$reply.";
	fputs($socks, "QUIT\r\n");									//	$reply=fgets($socks,1024); echo "$reply.";

	return 1;

/*

//2// mail by php mail function 

	$header.="From: $from\r\n";
	if ($bcc) 
		$header.="Bcc: ".$bcc."\r\n";
	$header.="X-Priority: 3\r\n";
	$header.="Return-Path: <".$config['adminmail'].">\r\n";
	$header.="Content-Type: text/html; charset=window-874\r\n";
	$header.="\r\n";

	if (mail($recipient,$subject,$msg,$header)) {
		return 1;
	}
	else {
		return 0;
	}


//3// mail by libmail.php 

	include "include/libmail.php";
	$m= new Mail; // create the mail
	$m->From( $from );
	$m->To( $recipient );
	$m->Subject( $subject );
	$m->Body( $msg ,"window-874");	// set the body
	if (sizeof($bcc) > 0) $m->Bcc( $bcc );

	$m->Priority(3) ;	// set the priority to Low
	$m->Send();	// send the mail

//	echo $m->Get();
	
	if (eregi("invalid address",$m->Get())) {
		return 0;
	}
	else {
		return 1;
	}

*/
}

?>