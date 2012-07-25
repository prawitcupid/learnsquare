<?php
/**
*  registration 
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}

if (!lnSecAuthAction(0, 'User::Register', "::", ACCESS_READ)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

include 'header.php';

// Add new user after submit registration
//-------------------------------------------------------
if ($op ==  "adduser") {
	// if Allow to register
    if (lnConfigGetVar('reg_allowreg')) {
		
		// Clean variables
		list($nickname, $password1, $password2, $id, $email, $phone,$news) = lnVarCleanFromInput('nickname', 'password1', 'password2','id','email','phone','news');
		
		// Check duplicate nickname, id, email
		if ($return = lnUserCheck('',$nickname,$id,$email)) { 
			switch ($return) {
				case _NICKTAKEN : $err_nickname = "<font color=#FF0000><B>'$nickname' $return</B></font>"; break;
				case _UNOTAKEN : $err_uno = "<font color=#FF0000><B>$return</B></font>"; break;
				case _EMAILTAKEN : $err_email = "<font color=#FF0000><B>$return</B></font>"; break;
			}
		}
		else {
			// Compare password 
			if ($password1 == $password2) {
				$add_pass = md5($password1);
			}
			else {
				return false;
			}
			
			// Insert new user to user table
			list($dbconn) = lnDBGetConn();
			$lntable = lnDBGetTables();
			
			$userstable = $lntable['users'];
			$column = &$lntable['users_column'];
			$group_membershiptable = $lntable['group_membership'];
			$group_membershipcolumn = &$lntable['group_membership_column'];

			$user_regdate = time();
			$uid = lnUserNextID();

			 $query = "INSERT INTO $userstable ($column[uid],
							$column[uname], $column[email], $column[regdate],
							$column[pass], $column[phone], $column[news], $column[uno])
							 values ($uid,
								'" . lnVarPrepForStore($nickname) . "','" . lnVarPrepForStore($email) . "', '" . lnVarPrepForStore($user_regdate) . "',
								 '" . lnVarPrepForStore($add_pass) . "','" . lnVarPrepForStore($phone) . "','" . lnVarPrepForStore($news) . "','" . lnVarPrepForStore($id) . "')";

			   $result = $dbconn->Execute($query);


			if ($dbconn->ErrorNo() <> 0) {
				echo "<br>". $dbconn->ErrorNo() . ": " . $dbconn->ErrorMsg() . "<br>";
				return;
			} 
			else {
				$registerdone = "<center>". _ADDUSERDONE."</center>";
				$userinfo = lnUserGetVars(0);

				lnMail($userinfo['email'],$email,_MAILSUBJECT,_MAILMESSAGE);

				$gid =  lnConfigGetVar('default_group');
				$query = "INSERT INTO $group_membershiptable ($group_membershipcolumn[gid],
							$group_membershipcolumn[uid])
							 values ($gid,$uid)";
			   $result = $dbconn->Execute($query);

			}
		}
	}
	else {
		error('Not allow to register!');
	}
}


// Show Registration Form
//----------------------------
if (empty($registerdone)) {

?>
 
    <script language="javaScript">
		
		function formSubmit(val) {
			document.forms.Userprofile.op.value = val;
			if(val == "Clear") document.forms.Userprofile.submit();
			else if(checkFields()) document.forms.Userprofile.submit();
		}
		
		function clearFields() {
			document.forms.Userprofile.reset();
		}

    	function checkFields() {
			var nickname = document.forms.Userprofile.nickname.value;
			var password1 = document.forms.Userprofile.password1.value;
			var password2 = document.forms.Userprofile.password2.value;

			<? if (lnUserReqProp('_UNO')) { ?>
						var userId = document.forms.Userprofile.id.value;
			<? } ?>
			<? if (lnUserReqProp('_EMAIL')) { ?>
						var emailAddress = document.forms.Userprofile.email.value;
			<? } ?>
			<? if (lnUserReqProp('_PHONE')) { ?>
						var phone = document.forms.Userprofile.phone.value;
			<? } ?>
	
			if (nickname  == "" ) {
				alert("<?=_ALERTNICKNAME?>");
				document.forms.Userprofile.nickname.focus();
				return false;
			} else if (nickname.length  < <? echo lnConfigGetVar('reg_min_nickname'); ?>) {
				alert("<? echo _NICKNAME ." >= ".  lnConfigGetVar('reg_min_nickname'); ?>");
				document.forms.Userprofile.nickname.focus();
				return false;
			} else if (nickname.length  >= <? echo lnConfigGetVar('reg_max_nickname'); ?>) {
				alert("<? echo _NICKNAME ." <= ".  lnConfigGetVar('reg_max_nickname'); ?>");
				document.forms.Userprofile.nickname.focus();
				return false;
			}
			
			if (password1  == "") {
				alert("<?=_ALERTPASSWORD1?>");
				document.forms.Userprofile.password1.focus();
				return false;
			}

			if (password1.length  < <? echo lnConfigGetVar('reg_min_password'); ?>) {
				alert("<? echo _PASSWORD ." >= ".  lnConfigGetVar('reg_min_password'); ?>");
				document.forms.Userprofile.password1.focus();
				return false;
			} 
			else if (password1.length  >= <? echo lnConfigGetVar('reg_max_password'); ?>) {
				alert("<? echo _PASSWORD ." >= ".  lnConfigGetVar('reg_max_password'); ?>");
				document.forms.Userprofile.password1.focus();
				return false;
			}

			if (password2  == "") {
				alert("<?=_ALERTPASSWORD2?>");
				document.forms.Userprofile.password2.focus();
				return false;
			}

			if (password2 != password1) {
				alert("<?=_ALERTPASSWORD0?>");
				document.forms.Userprofile.password2.focus();
				return false;
			}

<? if (lnUserReqProp('_UNO')) { ?>
			if (userId  == "") {
				alert("<?=_ALERTID?>");
				document.forms.Userprofile.id.focus();
				return false;
			}

			if (userId.length != <? echo lnConfigGetVar('reg_id_len'); ?>) {
				alert("<?=_SID?> = <? echo lnConfigGetVar('reg_id_len'); ?>");
				document.forms.Userprofile.id.focus();
				return false;
			}

			if (isComposedOfChars("0123456789",userId)){
				alert("<?=_SID?> <?=_ALERNONUM?>");
				document.forms.Userprofile.id.focus();
				return false;
			}
<? } ?>

<? if (lnUserReqProp('_EMAIL')) { ?>
			if (emailAddress  == "") {
				alert("<?=_ALERTEMAIL?>");
				document.forms.Userprofile.email.focus();
				return false;
			}

			if (emailAddress != "" && (emailAddress.indexOf(" ") > -1 || emailAddress.indexOf("@") == -1 || emailAddress.indexOf(",") > -1 )) {
				alert("<?=_ALERTEMAILFORMAT?>");
				document.forms.Userprofile.email.focus();
				return false;
			}
<? } ?>

<? if (lnUserReqProp('_EMAIL')) { ?>
			if (isComposedOfChars("0123456789-",phone)){
				alert("<?=_PHONE?> <?=_ALERTNONUM?>");
				document.forms.Userprofile.phone.focus();
				return false;
			}
<? } ?>

			if (isComposedOfChars("_ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",password1)){
				alert("<?=_PASSWORD?> <?=_ALERTNOALPHA?>");
				document.forms.Userprofile.password1.focus();
				return false;
			}

			if (isComposedOfChars("_ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",password2)){
				alert("<?=_PASSWORD?> <?=_ALERTNOALPHA?>");
				document.forms.Userprofile.password2.focus();
				return false;
			}

			if (isComposedOfChars("_ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789",nickname)){
				alert("<?=_NICKNAME?> <?=_ALERTNOALPHA?>");
				document.forms.Userprofile.nickname.focus();
				return false;
			}


		return true; 
	}

	function isComposedOfChars(testSet, input) {
		for (var j=0; j<input.length; j++) {
			if (testSet.indexOf(input.charAt(j), 0) == -1) {
				return true;
			}
		}
		return false;
	}

	</script>

<BR>
<?
	OpenTable();

	//echo lnBlockTitle($mod,'register');
	echo '<p class="header"><b>'._REGISTER_TITLE.'</b></p>';
	echo '<BR>'._REGISTERDESC;
?>
 <FORM name="Userprofile" method="post" action="index.php" onSubmit="return checkFields();">
 <input type="hidden" name="mod" value="User">
 <input type="hidden" name="file" value="register">
 <input type="hidden" name="op">

<CENTER>
 <TABLE WIDTH="500">
<TR><TD colspan=3>
</TD></TR>
<TR><TD><?=_NICKNAME?>:</TD>
	<TD>
		<INPUT TYPE="text" NAME="nickname" value="<?=$nickname?>"> * <?=_NICKNAMELIMIT?> <? echo lnConfigGetVar('reg_min_nickname'); ?> - <? echo lnConfigGetVar('reg_max_nickname'); ?><BR><?=$err_nickname?>
	</TD>
</TR>

<TR><TD><?=_PASSWORD?>:</TD>
	<TD><INPUT TYPE="password" NAME="password1" value="<?=$password1?>"> * <?=_PASSWORDLIMIT?>  <? echo lnConfigGetVar('reg_min_password'); ?> - <? echo lnConfigGetVar('reg_max_password'); ?></TD></TR>

<TR><TD><?=_CONFIRMPASSWORD?>:</TD>
	<TD><INPUT TYPE="password" NAME="password2" value="<?=$password2?>"> *</TD></TR>

<? if (lnUserReqProp('_UNO')) { ?>
<TR valign="top"><TD><?=_UNO?>:</TD>
	<TD>
		<INPUT TYPE="text" NAME="id" value="<?=$id?>"> * &nbsp;<?=_UNOHELP?> <? echo lnConfigGetVar('reg_id_len'); ?><BR><?=$err_uno?>
	</TD>
</TR>
<? } ?>

<? if (lnUserReqProp('_EMAIL')) { ?>
<TR valign="top"><TD><?=_EMAIL?>:</TD>
	<TD>
		<INPUT TYPE="text" NAME="email" value="<?=$email?>"> *<BR><?=$err_email?>
	</TD>
</TR>
<? } ?>

<? if (lnUserReqProp('_PHONE')) { ?>
<TR valign="top"><TD><?=_PHONE?>:</TD>
	<TD>
		<INPUT TYPE="text" NAME="phone" value="<?=$phone?>"> 
	</TD>
</TR>
<? } ?>

<? if (lnUserReqProp('_NEWS')) { ?>
<TR valign="top"><TD></TD>
	<TD>
<!--<INPUT TYPE="checkbox" NAME="news"  VALUE="1" <? if (isset($news) || $news == '1' || !isset($op)) echo checked; else echo ""; ?>> <?=_NEWS?>  -->	
	</TD>
</TR>
<? } ?>

<TR>
<TD></TD>
<TD>
<BR><a href="javascript:formSubmit('adduser')"><?lnBlockButton('register')?></a>&nbsp;
</TD>
</TR>

</TABLE>
</CENTER>
</FORM>	

<?
}
else {
	OpenTable();
	echo $registerdone;

}

CloseTable();

include 'footer.php';

?>