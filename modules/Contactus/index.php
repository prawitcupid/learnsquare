<?php
/**
* Contact us 
*/
if (!defined("LOADED_AS_MODULE")) {
    die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'Contact Us::', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */
include 'header.php';

OpenTable();

switch ($op) {
	case "send" :  sendContactus($firstname,$subject, $email,$message); break;
	default :				 contactusForm();
}

CloseTable();

include 'footer.php';
/* - - - - - - - - - - - */

/**
* show contactus form
*/
function contactusForm() {
	echo _CONTACTDESC;

?>
    <script language="javaScript">
		function formSubmit(val) {
			document.forms.contact.op.value = val;
			if(checkFields()) document.forms.contact.submit();
		}
		
		function checkFields() {
			var name = document.forms.contact.firstname.value;
			var email = document.forms.contact.email.value;
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
			if (message  == "" ) {
				alert("<?=_ALERTMSG?>");
				document.forms.contact.message.focus();
				return false;
			}

			return true;
		}
</script>
<BR>
<TABLE width="500"  border="0" cellpadding="3" cellspacing="0">
<FORM NAME="contact" METHOD=POST ACTION="index.php">
<INPUT TYPE="hidden" name="mod" value="Contactus">
<INPUT TYPE="hidden" name="op">

<TR>
<TD  width="100">
<?=_FLNAME?>
</TD>

<TD>
<INPUT class=input1 TYPE="text" NAME="firstname">
</TD>
</TR>


<TR>
<TD  width="100">
<?=_EMAIL?> 
</TD>

<TD align="left">
<INPUT class=input1 TYPE="text" NAME="email">
</TD>
</TR>

<TR>
<TD  width="100" >
<?=_SUBJECT?> 
</TD>

<TD align="left">
<select name="subject" class="input1">
<option value = "-"><?=_CHOICE?></option>
<option value = "<?=_CHOICE1?>"><?=_CHOICE1?></option>
<option value = "<?=_CHOICE2?>"><?=_CHOICE2?></option>
<option value = "<?=_CHOICE3?>"><?=_CHOICE3?></option>
</select>
</TD>
</TR>


<TR>
<TD  width="100" valign="top">
<?=_MESSAGE?>
</TD>

<TD valign="top" align="left">
<TEXTAREA NAME="message" ROWS="8" COLS="40" style='width=90%'></TEXTAREA>
</TD>
</TR>

<TR>
<TD  width="100" valign="top" align="right">&nbsp;</TD>
<TD valign="top" align="left">

<a href="javascript:formSubmit('send')"><? lnBlockButton('send'); ?></a>
</TD>
</TR>
</FORM>
</TABLE>

<?
}

/*- - - Contact us - - -*/
function sendContactUs($firstname,$subject, $email,$message) {
	if (!lnSecAuthAction(0, 'Contact Us::', "::", ACCESS_READ)) {
		echo "<CENTER><h1>"._NOAUTHORIZED." ".$mod." module!</h1></CENTER>";
		return false;
	}

	$recipient=lnConfigGetVar('adminmail');  //เรียก mail  ตรงนี้
	//$userinfo=lnUserGetVars(0);	
	//$recipient=$userinfo['email'];
	$subject=_MAILPRETITLE .': '.$subject;
	$msg=str_replace("\n","<BR>\r\n",$message);
	$message="<font face='ms sans serif' size=2>"._MAILFROM." <B>$firstname ($email)</B><BR>\r\n "._MAILSUBJECT." $subject<BR><BR>\r\n\r\n".$msg;
	if (lnMail($email,$recipient,$subject,$message)) {
		echo "<CENTER>&nbsp;<P><B>"._MAILTHANKS."</B></CENTER>";
		
	}
	else {
		echo "&nbsp;<P><CENTER><B>"._MAILERROR."</B></CENTER>";
	}
}
?>