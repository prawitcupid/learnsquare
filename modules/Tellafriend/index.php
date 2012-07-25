<?php
/**
* Site promotion module
*/
if (!defined("LOADED_AS_MODULE")) {
    die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Tellafriend::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */
include 'header.php';

OpenTable();

switch ($op) {
	case "send" :  sendTellafriend($firstname,$email,$email1,$email2,$email3,$email4,$message); break;
	default :				 show();
}

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */


/**
* show tell a friend form
*/
function show() {

	echo _TELLDESC;
	tellafriendForm();
}


/**
* Input form
*/
function tellafriendForm() {

?>
<script language="javaScript">
	function formSubmit(val) {
		document.forms.contact.op.value = val;
		if(checkFields()) document.forms.contact.submit();
	}
	
	function checkFields() {
		var name = document.forms.contact.firstname.value;
		var email = document.forms.contact.email.value;
		var email1 = document.forms.contact.email1.value;
		var email2 = document.forms.contact.email2.value;
		var email3 = document.forms.contact.email3.value;
		var email4 = document.forms.contact.email4.value;
		var message = document.forms.contact.message.value;
	
		if (name == "" ) {
			alert("<?=_ALERTNAME?>");
			document.forms.contact.firstname.focus();
			return false;
		}
		if (email == "" ) {
			alert("<?=_ALERTEMAIL?>");
			document.forms.contact.email.focus();
			return false;
		}
		if (email1 == "" && email2 == "" && email3 == "" && email4 == "") {
			alert("<?=_ALERTTOEMAIL?>");
			document.forms.contact.email1.focus();
			return false;
		}

		return true;
	}
</script>
<P><center>
<TABLE width="500"  border="0" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
<FORM name="contact" METHOD=POST ACTION="index.php">
<INPUT TYPE="hidden" name="mod" value="Tellafriend">
<INPUT TYPE="hidden" name="op" value="send">

<TR>
<TD  width="70" bgcolor="#FFFFFF" valign="top">
<?=_FLNAME?>
</TD>

<TD  bgcolor="#FFFFFF" valign="top" align="left">
<INPUT  TYPE="text" NAME="firstname">
</TD>
</TR>

<TR>
<TD  width="70" bgcolor="#FFFFFF" valign="top">
<?=_EMAIL?>
</TD>

<TD  bgcolor="#FFFFFF" valign="top" align="left">
<INPUT  TYPE="text" NAME="email">
</TD>
</TR>

<TD  bgcolor="#FFFFFF" valign="top" align="left">
&nbsp;</TD>
</TR>

<TR>
<TD  width="70" bgcolor="#FFFFFF" valign="top">
<?=_EMAILFRIEND?>
</TD>

<TD  bgcolor="#FFFFFF" valign="top" align="left">
<INPUT  TYPE="text" NAME="email1"><BR>
<INPUT  TYPE="text" NAME="email2"><BR>
<INPUT  TYPE="text" NAME="email3"><BR>
<INPUT  TYPE="text" NAME="email4"><BR>
</TD>
</TR>

<TR>
<TD  width="100" bgcolor="#FFFFFF" valign="top">
<?=_MOREMSG?>
</TD>

<TD  bgcolor="#FFFFFF" valign="top" align="left">
<TEXTAREA NAME="message" ROWS="5" COLS="40"></TEXTAREA>
</TD>
</TR>

<TR>
<TD  width="100" bgcolor="#FFFFFF" valign="top" align="right">&nbsp;</TD>
<TD  bgcolor="#FFFFFF" valign="top" align="left">
	<BR><a href="javascript:formSubmit('send')"><? lnBlockButton('send'); ?></a>
</TD>
</TR>
</FORM>
</TABLE>
<!-- End  tell a friendTable -->

<?
}


/*- - -  Send Tell a Friend  - - -*/
function sendTellafriend($firstname,$email,$email1,$email2,$email3,$email4,$message) {
	if (!lnSecAuthAction(0, 'Tellafriend::', "::", ACCESS_READ)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." ".$mod." module!</h1></CENTER>";
		return false;
	}
	$subject = _MAILSUBJECT ." จากคุณ ". $firstname;
	$msg.=_MAILCONTENT;
	$msg.=str_replace("\n","<BR>\r\n",$message);
	$bcc= array ();
	if ($email2) $bcc[]=$email2;
	if ($email3) $bcc[]=$email3;
	if ($email4) $bcc[]=$email4;

	mailsock($email,$email1,$bcc,$subject,$msg);
	echo "<CENTER>&nbsp;<P><B>"._MAILTHANKS. "<P> ถึง<P> $email1 สำเนา: ";
	for ($i=0;$i<sizeof($bcc); $i++)
		echo $bcc[$i] . ",";
	echo "<P> ได้ถูกส่งไปแล้ว ขอบคุณค่ะ.. </B></CENTER>";
}

?>