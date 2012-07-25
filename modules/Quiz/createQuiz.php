<?
//if (!defined("LOADED_AS_MODULE")) {
//	die ("You can't access this file directly...");
//}
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::Admin', "::", ACCESS_MODERATE)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

function quizIssue($vars){
	extract($vars);
	if(!isset($issue)) $issue='';
	switch ($issue){
		case "quizForm": quizForm($vars);break;
		case "makeSingleQuizSave": makeSingleQuizSave($vars);mainQuiz($vars);break;
		case "makeClozeQuizSave": makeClozeQuizSave($vars);mainQuiz($vars);break;
		case "makeMultiQuizSave": makeMultiQuizSave($vars);mainQuiz($vars);break;
		case "del": quizDel($vars);header("Location: index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=$cid");
		default: mainQuiz($vars);break;
	}
}
function mainQuiz($vars){
	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];
	$uid = lnSessionGetVar('uid');
	$sql = "SELECT $multichoiceColumn[mcid],$multichoiceColumn[uid],$multichoiceColumn[question],$multichoiceColumn[answer],$multichoiceColumn[difficulty],$multichoiceColumn[type],$multichoiceColumn[share] FROM $multichoice WHERE $multichoiceColumn[uid]='$uid' ORDER BY $multichoiceColumn[mcid] ASC";
	$result = $dbconn->Execute($sql);
	while (list($mcid,$uid,$ques,$ans,$diff,$type,$key,$share) = $result->fields){
		$result->MoveNext();
		if($type == 1){
			$quiz[] = array($mcid,$ques,$ans,$diff,$type,$key,$share);
			$quizSection[] = $quiz;
			unset($quiz);
		}else{
			$quiz[] = array($mcid,$ques,$ans,$diff,$type,$key,$share);
		}
		if($ans == 0 && $diff == 0){
			$quizSection[] = $quiz;
			unset($quiz);
		}
	}
	rmslashesextended($quizSection);
	//--html
	?>
<style>
<!--
a img {
	border: 0;
	margin: 2px;
}
-->
</style>
<div
	id="mainQuiz" style="width: 100%; margin: 0;"><!-- if empty quiz create will always show -->
<div id="dForm">
<form
	action="index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>"
	method="post" onsubmit="return validateKey()"><!-- Issue to --> <input
	type="hidden" name="issue" value="quizForm" />
<fieldset><legend><?php echo _CREATEQUIZ?></legend>
<table>
	<tr>
		<td><?echo _QUIZ_KEYWORD;?></td>
		<td><input type="text" name="quizKey" id="quizKey" /></td>
	</tr>
	<tr>
		<td><?echo _QUIZ_TYPE;?></td>
		<td><select name="quizType">
			<option value="1"><?echo _QUIZ_TYPE_1;?></option>
			<option value="2"><?echo _QUIZ_TYPE_2;?></option>
			<option value="3"><?echo _QUIZ_TYPE_3;?></option>
		</select></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="submit" class="button"
			value="<?echo _NEXT;?>" /></td>
	</tr>
</table>
</fieldset>
</form>
</div>
<script type="text/javascript">
	function validateKey() {
		if(document.getElementById('quizKey').value != ''){
			return true;
		}else{
			alert('<?php echo _ALT_KEYWORD?>');
			return false;
		}		
	}
	</script> <!--<div id="dNew"><?echo _ADD_NEW_QUIZ;?></div>-->
<div id="dQList">
<table border="1px" cellspacing="0" width="100%" cellpadding="0">
<?
$quizNumber = 1;

$host = $_SERVER["HTTP_HOST"];
$path= str_replace('/index.php','',$_SERVER["SCRIPT_NAME"]);
$coursepath= COURSE_DIR . "/" .$cid;
$url= 'http://'.$host.$path.'/'.$coursepath ;
//echo "URL=".$url."<br>";
	
foreach ($quizSection as $qs) {
	echo "<tr><td>\n";
	echo '<div align="right"><a href="index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid='.$cid.'&issue=quizForm&qe=1&mcid='.$qs[0][0].'"><img src="images/global/view1.gif" title="'._EDIT.'"/></a><a href="index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid='.$cid.'&issue=del&mcid='.$qs[0][0].'"><img src="images/global/delete.gif" title="'._DEL.'"/></a></div>';
	$qsCH = "";
	$qsText = "";
	foreach ($qs as $sq) {
		switch ($sq[4]) {
			case 1:
				$sq[1]=str_replace('\"','"',$sq[1]);
				$sq[1] = lnShowContent($sq[1],$url);
				
				$qsCH .= "<fieldset><legend>"._QUESTION_NUMB." ".$quizNumber."</legend>";
				$qsCH .= "<div><strong>"._QUESTION." :: </strong>".$sq[1]."</div>";//question
				$qsCH .= "<div>";
				$sql = "SELECT $quizChoiceColumn[answer],$quizChoiceColumn[feedback] FROM $quizChoice WHERE $quizChoiceColumn[mcid]='$sq[0]' ORDER BY $quizChoiceColumn[weight]";
				$result = $dbconn->Execute($sql);
				$j = 0;
				while (list($answer,$feedback) = rmslashesextended($result->fields)) {
					$result->MoveNext();
					
					$answer = str_replace('\"','"',$answer);
					$feedback = str_replace('\"','"',$feedback);
					$answer = lnShowContent($answer,$url);
					$feedback = lnShowContent($feedback,$url);
					
					if(choiceTypeChecker($sq[2]) == 1){
						$qsCH .= '<input ';
						if($sq[2] & pow(2,$j))$qsCH .= 'checked ';
						$qsCH .= 'type="checkbox" name="answerMCID'.$sq[0].'['.$j.']" value="'.$j.'"/> ';
						$qsCH .= $answer." - <font color=#009900>"._FEEDBACKCHOICE." :: ".$feedback."</font><br/>";
						$j++;
					}else{
						$qsCH .= '<input ';
						if($sq[2] & pow(2,$j))$qsCH .= 'checked ';
						$qsCH .= 'type="radio" name="answerMCID'.$sq[0].'" value="'.$j.'"/> ';
						$qsCH .= $answer." - <font color=#009900>"._FEEDBACKCHOICE." :: ".$feedback."</font><br/>";
						$j++;
					}
				}
				$qsCH .= "</div></fieldset>";
				break;
					
			case 2:
				$sq[1] = str_replace('\"','"',$sq[1]);
				$sq[1] = lnShowContent($sq[1],$url);
				
				if($sq[2] == 0 && $sq[3] == 0){
					$qsText .= $sq[1];
					$quizNumber--;
				}else{
					$qsText .= $sq[1] . "<u> (ข้อที่ $quizNumber) </u>";
					$qsCH .= "<fieldset><legend>"._QUESTION_NUMB." ".$quizNumber."</legend>";
					$qsCH .= "<div>";
					$sql = "SELECT $quizChoiceColumn[answer],$quizChoiceColumn[feedback] FROM $quizChoice WHERE $quizChoiceColumn[mcid]='$sq[0]' ORDER BY $quizChoiceColumn[weight]";
					$result = $dbconn->Execute($sql);
					$j = 0;
					while (list($answer,$feedback) = rmslashesextended($result->fields)) {
						$result->MoveNext();
						$answer = str_replace('\"','"',$answer);
						$feedback = str_replace('\"','"',$feedback);
						$answer = lnShowContent($answer,$url);
						$feedback = lnShowContent($feedback,$url);
						$qsCH .= '<input ';
						if($sq[2] & pow(2,$j))$qsCH .= 'checked ';
						$qsCH .= 'type="radio" name="answerMCID'.$sq[0].'" value="'.$j.'"/> ';
						$qsCH .= $answer." - <font color=#009900>"._FEEDBACKCHOICE." :: ".$feedback."</font><br/>";
						$j++;
					}
					$qsCH .= "</div></fieldset>";
				}
				//echo "<fieldset><legend>"._QUESTION."</legend>$qsText</fieldset>\n$qsCH";
				break;
			case 3:
				$sq[1] = str_replace('\"','"',$sq[1]);
				$sq[1] = lnShowContent($sq[1],$url);
				
				if($sq[2] == 0 && $sq[3] == 0){
					$qsText = $sq[1];
					$quizNumber--;
				}else{
					$qsCH .= "<fieldset><legend>"._QUESTION_NUMB." ".$quizNumber."</legend>";
					$qsCH .= "<div><strong>"._QUESTION." :: </strong>".$sq[1]."</div>";//question
					$qsCH .= "<div>";
					$sql = "SELECT $quizChoiceColumn[answer],$quizChoiceColumn[feedback] FROM $quizChoice WHERE $quizChoiceColumn[mcid]='$sq[0]' ORDER BY $quizChoiceColumn[weight]";
					$result = $dbconn->Execute($sql);
					$j = 0;
					while (list($answer,$feedback) = ($result->fields)) {
						$result->MoveNext();
						
						$answer = str_replace('\\\"','"',$answer);
						$feedback = str_replace('\\\"','"',$feedback);
						$answer = str_replace('\"','"',$answer);
						$feedback = str_replace('\"','"',$feedback);
						$answer = lnShowContent($answer,$url);
						$feedback = lnShowContent($feedback,$url);
							
						if(choiceTypeChecker($sq[2]) == 1){
							$qsCH .= '<input ';
							if($sq[2] & pow(2,$j))$qsCH .= 'checked ';
							$qsCH .= 'type="checkbox" name="answerMCID'.$sq[0].'['.$j.']" value="'.$j.'"/> ';
							$qsCH .= $answer." - <font color=#009900>"._FEEDBACKCHOICE." :: ".$feedback."</font><br/>";
							$j++;
						}else{
							$qsCH .= '<input ';
							if($sq[2] & pow(2,$j))$qsCH .= 'checked ';
							$qsCH .= 'type="radio" name="answerMCID'.$sq[0].'" value="'.$j.'"/> ';
							$qsCH .= $answer." - <font color=#009900>"._FEEDBACKCHOICE." :: ".$feedback."</font><br/>";
							$j++;
						}
					}
					$qsCH .= "</div></fieldset>";
				}
				//echo "<fieldset><legend>"._QUESTION."</legend>$qsText</fieldset>\n$qsCH";
				break;
		}
		$quizNumber++;
	}
	if($qsText != "")echo "<fieldset><legend>"._QUESTION."</legend>".stripslashes($qsText)."</fieldset>\n";

	echo stripslashes($qsCH);
	echo "<tr><td>\n";
}


?>
</table>
</div>
</div>

<?
//--end html
}
function quizDel($vars) {
	list($dbconn) = lnDBGetConn();
	extract($vars);
	$lntable = lnDBGetTables();
	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];
	$sql = "SELECT $multichoiceColumn[type] FROM $multichoice WHERE $multichoiceColumn[mcid] = '$mcid'";
	$result = $dbconn->Execute($sql);
	list($type) = $result->fields;
	if($type == 1){
		quizChoiceDel($mcid);
		return;
	}
	$sql = "SELECT MIN($multichoiceColumn[mcid]) FROM $multichoice WHERE $multichoiceColumn[mcid] > '$mcid' AND $multichoiceColumn[difficulty] = '0' AND $multichoiceColumn[answer] = '0'";
	$result = $dbconn->Execute($sql);
	list($lastmcid) = $result->fields;
	for ($i = $mcid; $i <= $lastmcid; $i++) {
		quizChoiceDel($i);
	}
}
function quizChoiceDel($mcid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];
	$sql = "DELETE FROM $multichoice WHERE $multichoiceColumn[mcid] = '$mcid'";
	$dbconn->Execute($sql);
	$sql = "DELETE FROM $quizChoice WHERE $quizChoiceColumn[mcid] = '$mcid'";
	$dbconn->Execute($sql);
}
function quizForm($vars){
	extract($vars);
	list($dbconn) = lnDBGetConn();
	//check edit from $qe
	if(@$qe){
		quizEditor($vars);
		return;
	}

	//check type from qt

	switch ($quizType){
		case "1": makeSingleQuiz($vars);break;
		case "2": makeClozeQuiz($vars);break;
		case "3": makeMultiQuiz($vars);break;
	}
}
function makeSingleQuiz($vars){
	extract($vars);
	//---html
	?>
<div id="makeChoice">
<div>
<form name="quizform"
	action="index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>"
	method="post" onsubmit="return validation()"><input type="hidden"
	name="quizKey" value="<?echo $quizKey;?>" /> <input type="hidden"
	name="issue" value="makeSingleQuizSave" /> <?if(@$mcid != 0) :?> <input
	type="hidden" name="mcid" value="<?echo $mcid;?>" /> <?endif;?>
<fieldset><legend><?php echo _QUESTION?></legend>
<table>
	<tr>
		<td><?echo _QUESTION . " :";?></td>
		<td><textarea name="quizContent" style="width: 400px"></textarea>
		<button
			onclick="popup('index.php?mod=spaw&amp;type=Question&amp;cid=<?php echo $cid;?>&amp;txt='+getText(this),'_blank',750,480)"
			type="button" class="button">...</button>
		</td>
	</tr>
	<tr>
		<td valign="top"><?echo _QUIZ_ANSWER?> :</td>
		<td>
		<table id="choice">
			<tr>
				<td align="center"><?echo _CORRECTANS?></td>
				<td align="center"><?echo _QUIZ_CHOICE;?></td>
				<td align="center">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _DESCRIPTION;?></td>
			</tr>
		</table>
		<div align="right">
		<button onclick="pChoice()" type="button" class="button">+</button>
		<button onclick="mChoice()" type="button" class="button">-</button>
		</div>
		</td>
	</tr>
	<tr>
		<td><?echo _DIFFICUTY?> :</td>
		<td><label><input type="radio" name="quizDiff" value="1"
			checked="checked">1</label><label><input type="radio" name="quizDiff"
			value="2">2</label><label><input type="radio" name="quizDiff"
			value="3">3</label><label><input type="radio" name="quizDiff"
			value="4">4</label><label><input type="radio" name="quizDiff"
			value="5">5</label></td>
	</tr>
	<tr>
		<td><?echo _PERMISSION?> :</td>
		<td><label><input type="radio" name="permission" value="0"
			checked="checked"><?echo _DENY?></label><label><input type="radio"
			name="permission" value="1"><?echo _AGREE?></label></td>
	</tr>
	<tr>
		<td colspan="2" align="center"><input type="button" class="button"
			value="<?echo _CANCEL;?>"
			onclick="window.open('index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>','_self')" />
		<input type="submit" class="button" value="<?echo _CREATE?>" /></td>
	</tr>
</table>
</fieldset>
</form>
</div>
</div>
<script
	type="text/javascript" src="javascript/jquery.min.js"></script>
<script type="text/javascript">
	<!--
	var nCh = 0;
	function pChoice() {
		$("table#choice").append('<tr class="nChoice"><td><input type="checkbox" name="answerCh['+nCh+']"/></td><td>'+(nCh+1)+'.'+'<textarea name="textCh[]"></textarea><button onclick="popup(\'index.php?mod=spaw&amp;type=Choice&amp;cid=<?php echo $cid;?>&amp;chid=&amp;y='+nCh+'&amp;txt=\'+getText(this),\'_blank\',750,480)" type="button" class="button">...</button></td><td valign="top" align="left"><TEXTAREA NAME="reasonCh[]" ></TEXTAREA></td></tr>');
		nCh++;	
	}
	function mChoice() {
		$("table#choice tr.nChoice:last").remove();
		nCh--;
	}
	function test(){
		return false;
	}
	$(function() {
		for ( var i = 0; i < 4; i++) {
			pChoice();
		}
	});
	function validation() {
		var n = $('input[name^="answerCh"]:checked').length;
		var err = "";
		if(!questionVali()){
			err += "- <?php echo _ALT_QUESION?>\n";
		}
		if(!choiceVali()){
			err += "- <?php echo _ALT_CHTEXT?>\n";
		}		
		if(n == 0){
			err += "- <?php echo _ALT_CHOICE?>\n";
		}
		if(err.length == 0)
			return true;
		alert("<?php echo _ALT_ERROR?>\n"+err);
		return false;
	}
	function choiceVali() {
		var ret = true;
		$('textarea[name="textCh[]"]').each(function(i){
			if($(this).val().length == 0)
				ret = false;
		});
		return ret;
	}
	function questionVali() {
		if($('textarea[name="quizContent"]').val().length == 0)
			return false;
		else
			return true;
	}
	function getText(dom){
		//var textbox = $('[name="'+name+'"]').eq(index);
		var textbox = $(dom).prev();
		//var textbox = $('textarea').eq(dom);
		return encodeURIComponent(textbox.val());
	}
	//-->
	
	</script>
	<?
	//---end html
	//
}
function makeSingleQuizSave($vars) {
	//---------
	addslashesextended($vars);
	//---------
	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];
	if(isset($mcid)){
		$sumAnswer = 0;
		$answerCh = "answerCh".$mcid;
		$answerCh = $$answerCh;
		$textCh = "textCh".$mcid;
		$textCh = $$textCh;
		$reasonCh = "reasonCh".$mcid;
		$reasonCh = $$reasonCh;
		$quizDiff = "quizDiff".$mcid;
		$quizDiff = $$quizDiff;
		foreach ($answerCh as $choi) {
			$sumAnswer += pow(2,$choi);
		}

		//get keyword
		$sqlkeyword = "SELECT $multichoiceColumn[keyword]
		FROM $multichoice WHERE $multichoiceColumn[mcid]='$mcid'";
		$resultkeyword = $dbconn->Execute($sqlkeyword);
		$quizKey = $resultkeyword->fields[0];

		//get guid
		$guid = getGuid();
		$sql = "UPDATE $multichoice SET $multichoiceColumn[question] = '$quizContent',
		$multichoiceColumn[answer] = '$sumAnswer',
		$multichoiceColumn[difficulty] = '$quizDiff',
		$multichoiceColumn[keyword] = '$quizKey',
		$multichoiceColumn[share] = '$permission',
		$multichoiceColumn[guid] = '$guid'
				WHERE $multichoiceColumn[mcid] = '$mcid'";
		$dbconn->Execute($sql);
		choiceManager($mcid,$textCh,$reasonCh);
	}else{
		// Insert
		$sumAnswer = 0;

		foreach ($answerCh as $key => $choi) {
			if($choi == 'on'){
				$sumAnswer += pow(2,$key);
			}
		}
		$uid = lnSessionGetVar('uid');
		//get guid
		$guid = getGuid();
		$sql = "INSERT INTO $multichoice ($multichoiceColumn[question],$multichoiceColumn[answer],$multichoiceColumn[difficulty],$multichoiceColumn[keyword],$multichoiceColumn[type],$multichoiceColumn[share],$multichoiceColumn[uid],$multichoiceColumn[cid],$multichoiceColumn[guid])
				VALUES ('$quizContent','$sumAnswer','$quizDiff','$quizKey','1','$permission','$uid','$cid','$guid')";
		$dbconn->Execute($sql);
		$sql = "SELECT MAX($multichoiceColumn[mcid]) FROM $multichoice";
		$result = $dbconn->Execute($sql);
		list($mcid) = $result->fields;
		for ($i = 0; $i < count($textCh); $i++) {
			insertChoice($mcid,$i+1,$textCh[$i],$reasonCh[$i]);
		}
	}
}
function makeClozeQuiz($vars){
	extract($vars);
	//---html
	?>
<div id="makeChoice">
<div id="cqState1">
<fieldset><legend><?echo _NUM_OF_QUESTION;?></legend> <?echo _NUM_OF_QUESTION;?>
<select id="numOfQuestion"></select><br />
<input class="button" type="button" value="<?echo _CANCEL;?>"
	onclick="window.open('index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>','_self')" />
</fieldset>
</div>
<form name="quizform"
	action="index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>"
	method="post" onsubmit="return validation()"><input type="hidden"
	name="cqNumQuiz" /> <input type="hidden" name="quizKey"
	value="<?echo $quizKey;?>" /> <input type="hidden" name="issue"
	value="makeClozeQuizSave" /> <?if($mcid != 0) :?> <input type="hidden"
	name="mcid" value="<?echo $mcid;?>" /> <?endif;?>
<div id="cqState2" style="display: none;">
<fieldset><legend><?php echo _QUESTION?></legend>
<table id="cqQuestion">
</table>
<p align="center">
<button type="button" class="button" onclick="goState1()"><?echo _BACK;?></button>
<button type="button" class="button" onclick="goState3()"><?echo _NEXT;?></button>
</p>
</fieldset>
</div>
<div id="cqState3" style="display: none;">
<fieldset id="cqText"><legend><?echo _QUESTION?></legend></fieldset>
<div></div>
	<?echo _PERMISSION?> :<label><input type="radio" name="permission"
	value="0" checked="checked"><?echo _DENY?></label><label><input
	type="radio" name="permission" value="1"><?echo _AGREE?></label><br />
<center>
<button type="button" onclick="goState2()" class="button"><?echo _BACK;?></button>
<input type="submit" class="button" value="<?echo _NEXT;?>" /></center>
</div>
</form>
</div>

<script
	type="text/javascript" src="javascript/jquery.min.js"></script>
<script type="text/javascript">
	function buildQuestion() {
		var num = $("select#numOfQuestion option:selected").val();
		var table = $("table#cqQuestion");
		table.empty();
		for ( var i = 0; i < num; i++) {
			table.append('<tr><td><?php echo _ClozeTestQuiz?> ' + (i + 1) + '<br/><input type="text" name="cqQuestion[]" style="width: 300px"/></td><td><u> (ช่องว่างของข้อที่' + (i + 1) + ') </u></td></tr>');
		}
		table.append('<tr><td><br/><input type="text" name="cqQuestionEnd" style="width: 300px"/></td><td>*ประโยคปิดท้าย(ไม่มีโปรดเว้นว่าง)</td></tr>');
	}
	function goState1() {
		$("#cqState2").hide();	
		$("#cqState1").show();
	}
	function goState2() {
		$("#cqState3").hide();	
		$("#cqState2").show();
	}
	function goState3() {
		var inputbox = $('input[name="cqQuestion[]"]');
		var field = $('#cqText');
		var cq3 = $("#cqState3 div");
		var i;
		if(questionVali()){
		field.empty();
		cq3.empty();
		i = 0;
		inputbox.each(function(){
			field.append($(this).val());
			field.append("<u> (ช่องว่างของข้อที่"+(i+1)+") </u>");
			i++;
		});
		field.append($('input[name="cqQuestionEnd"]').val());		
		$('input[name="cqNumQuiz"]').val(i);
		for ( var j = 0; j < i; j++) {
			cq3.append('<fieldset><legend>ข้อที่ '+(j+1)+'</legend><table><tr><td  valign="top"><?echo _QUIZ_ANSWER?> :</td><td><table id="cqChoies'+j+'"><tr><td><?echo _CORRECTANS?></td><td align="center"><?echo _QUIZ_CHOICE?></td><td align="center">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _DESCRIPTION;?></td></tr></table><div align="right"><button  class="button" onclick="pChoice('+j+')" type="button">+</button><button  class="button" onclick="mChoice('+j+')" type="button">-</button></div></td></tr><tr><td><?echo _DIFFICUTY?><label> :</td><td><input type="radio" name="quizDiff'+j+'" value="1" checked="checked" >1</label><label><input type="radio" name="quizDiff'+j+'" value="2">2</label><label><input type="radio" name="quizDiff'+j+'" value="3">3</label><label><input type="radio" name="quizDiff'+j+'" value="4">4</label><label><input type="radio" name="quizDiff'+j+'" value="5">5</label></td></tr></table></fieldset>');
			choiceCount[j] = 0;
			for ( var k = 0; k < 4; k++) {
				pChoice(j);
			}
		}
		
			$("#cqState2").hide();		
			$("#cqState3").show();
		}else{
			alert("<?php echo _ALT_ERROR?>\n"+"- <?php echo _ALT_QUESION?>\n");
		}
	}
	function pChoice(n) {
		$("table#cqChoies"+n).append('<tr class="nChoice"><td><input type="radio" name="answerCh'+n+'" value="'+choiceCount[n]+'"/></td><td>'+(choiceCount[n]+1)+'.<textarea name="textCh'+n+'[]"></textarea><button onclick="popup(\'index.php?mod=spaw&amp;type=Choice&amp;cid=<?php echo $cid;?>&amp;chid=&amp;x='+n+'&amp;y='+choiceCount[n]+'&amp;txt=\'+getText(this),\'_blank\',750,480)" type="button" class="button">...</button></td><td valign="top" align="left"><TEXTAREA NAME="reasonCh'+n+'[]" ></TEXTAREA></td></tr>');
		choiceCount[n]++;
	}
	function mChoice(n) {
		$("table#cqChoies"+n+" tr.nChoice:last").remove();
		choiceCount[n]--;	
	}
	$(function() {
		var numQuiz = $("select#numOfQuestion");
		for ( var i = 0; i < 100; i++) {
			numQuiz.append('<option value="' + i + '">' + i + '</option>');
		}
		numQuiz.change(function() {
			$("#cqState1").hide();
			buildQuestion();
			$("#cqState2").show();
		});
	});
	var choiceCount = new Array();
	function validation() {
		var n = $('input[name^="answerCh"]:checked').length;
		var err = "";		
		if(!choiceVali()){
			err += "- <?php echo _ALT_CHTEXT?>\n";
		}		
		if(n != choiceCount.length){
			err += "- <?php echo _ALT_CHOICE?>\n";
		}
		if(err.length == 0)
			return true;
		alert("<?php echo _ALT_ERROR?>\n"+err);
		return false;
	}
	function choiceVali() {
		var ret = true;
		$('textarea[name^="textCh"]').each(function(i){
			if($(this).val().length == 0)
				ret = false;
		});
		return ret;
	}
	function questionVali() {
		var ret = true;
		$('input[name="cqQuestion[]"]').each(function(i){
			if($(this).val().length == 0)
				ret = false;
		});
		return ret;
	}
	function getText(dom){
		//var textbox = $('[name="'+name+'"]').eq(index);
		var textbox = $(dom).prev();
		//var textbox = $('textarea').eq(dom);
		return encodeURIComponent(textbox.val());
	}
	</script>
	<?
	//---end html
}
function makeClozeQuizSave($vars) {
	//---------
	addslashesextended($vars);
	//---------
	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];
	if(isset($mcid)){
		$arr_mcid = getQuizMember($mcid,2);
		for ($i = 0; $i < count($arr_mcid); $i++) {
			$answerCh = "answerCh".$arr_mcid[$i][mcid];
			$sumAnswer = pow(2,$$answerCh);
			$quizDiff = "quizDiff".$arr_mcid[$i][mcid];
			$quizDiff = $$quizDiff;
			$textCh = "textCh".$arr_mcid[$i][mcid];
			$textCh = $$textCh;
			$reasonCh = "reasonCh".$arr_mcid[$i][mcid];
			$reasonCh = $$reasonCh;
				
			//get keyword
			$sqlkeyword = "SELECT $multichoiceColumn[keyword]
			FROM $multichoice WHERE $multichoiceColumn[mcid]='$mcid'";
			$resultkeyword = $dbconn->Execute($sqlkeyword);
			$quizKey = $resultkeyword->fields[0];

			//get guid
			$guid = getGuid();
				
			$sql = "UPDATE $multichoice SET $multichoiceColumn[question] = '$cqQuestion[$i]',
			$multichoiceColumn[answer] = '$sumAnswer',
			$multichoiceColumn[difficulty] = '$quizDiff',
			$multichoiceColumn[keyword] = '$quizKey',
			$multichoiceColumn[share] = '$permission',
			$multichoiceColumn[guid] = '$guid'
			WHERE $multichoiceColumn[mcid] = '".$arr_mcid[$i][mcid]."'";
			$dbconn->Execute($sql);
			choiceManager($arr_mcid[$i][mcid],$textCh,$reasonCh);
		}
		//get guid
		$guid = getGuid();

		$sql = "UPDATE $multichoice SET $multichoiceColumn[question] = '$cqQuestionEnd',
		$multichoiceColumn[keyword] = '$quizKey',
		$multichoiceColumn[share] = '$permission',
		$multichoiceColumn[guid] = '$guid'
			WHERE $multichoiceColumn[mcid] = '".$arr_mcid[0][foot]."'";
		$dbconn->Execute($sql);
	}else{
		//Insert
		$uid = lnSessionGetVar('uid');
		for ($i = 0; $i < count($cqQuestion); $i++) {
			$answerCh = "answerCh".$i;
			$sumAnswer = pow(2,$$answerCh);
			$quizDiff = "quizDiff".$i;
			$quizDiff = $$quizDiff;
			$textCh = "textCh".$i;
			$textCh = $$textCh;
			$reasonCh = "reasonCh".$i;
			$reasonCh = $$reasonCh;
				
			//get guid
			$guid = getGuid();

			$sql = "INSERT INTO $multichoice ($multichoiceColumn[question],$multichoiceColumn[answer],$multichoiceColumn[difficulty],$multichoiceColumn[keyword],$multichoiceColumn[type],$multichoiceColumn[share],$multichoiceColumn[uid],$multichoiceColumn[cid],$multichoiceColumn[guid])
				VALUES ('$cqQuestion[$i]','$sumAnswer','$quizDiff','$quizKey','2','$permission','$uid','$cid','$guid')";
			$dbconn->Execute($sql);
			$sql = "SELECT MAX($multichoiceColumn[mcid]) FROM $multichoice";
			$result = $dbconn->Execute($sql);
			list($mcid) = $result->fields;
			for ($j = 0; $j < count($textCh); $j++) {
				insertChoice($mcid,$j+1,$textCh[$j],$reasonCh[$j]);
			}
		}
		//get guid
		$guid = getGuid();

		$sql = "INSERT INTO $multichoice ($multichoiceColumn[question],$multichoiceColumn[answer],$multichoiceColumn[difficulty],$multichoiceColumn[keyword],$multichoiceColumn[type],$multichoiceColumn[share],$multichoiceColumn[uid],$multichoiceColumn[cid],$multichoiceColumn[guid])
				VALUES ('$cqQuestionEnd','0','0','$quizKey','2','$permission','$uid','$cid','$guid')";
		$dbconn->Execute($sql);
		// Insert
	}
}
function makeMultiQuiz($vars){
	extract($vars);
	//---html
	?>
<div id="makeChoice">
<div id="mqState1">
<fieldset><legend><?php echo _NUMQUESTIONS?></legend> <?echo _NUM_OF_QUESTION;?>
<select id="numOfQuestion"></select><br />
<input type="button" class="button" value="<?echo _CANCEL;?>"
	onclick="window.open('index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>','_self')" />
</fieldset>
</div>
<form name="quizform"
	action="index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>"
	method="post" onsubmit="return validation()"><input type="hidden"
	name="mqNumQuiz" /> <input type="hidden" name="quizKey"
	value="<?echo $quizKey;?>" /> <input type="hidden" name="issue"
	value="makeMultiQuizSave" /> <?if($mcid != 0) :?> <input type="hidden"
	name="mcid" value="<?echo $mcid;?>" /> <?endif;?>
<div id="mqState2" style="display: none;">
<fieldset id="mqQuestion"><legend><?echo _QUESTION;?></legend> <textarea
	name="mqText" style="width: 400px;"></textarea>
<button
	onclick="popup('index.php?mod=spaw&amp;type=mqText&amp;cid=<?php echo $cid;?>&amp;txt='+getText(this),'_blank',750,480)"
	type="button" class="button">...</button>
</fieldset>
<center>
<button type="button" class="button" onclick="goState1()"><?echo _BACK;?></button>
<button class="button" type="button" onclick="goState3()"><?echo _NEXT;?></button>
</center>
</div>
<div id="mqState3" style="display: none;">
<fieldset id="mqTextShow"><legend><?echo _QUESTION?></legend></fieldset>
<div></div>
	<?echo _PERMISSION?> :<label><input type="radio" name="permission"
	value="0" checked="checked"><?echo _DENY?></label><label><input
	type="radio" name="permission" value="1"><?echo _AGREE?></label><br />
<center>
<button type="button" class="button" onclick="goState2()"><?echo _BACK;?></button>
<input class="button" type="submit" value="<?echo _NEXT;?>" /></center>
</div>

</form>
</div>

<script
	type="text/javascript" src="javascript/jquery.min.js"></script>
<script type="text/javascript">	
	function goState1() {
		$("#mqState2").hide();	
		$("#mqState1").show();
	}
	function goState2() {
		$("#mqState3").hide();	
		$("#mqState2").show();
	}
	function goState3() {
		var num = $("select#numOfQuestion option:selected").val();		
		var mq3 = $("#mqState3 div");
		if(mqVali()){
		$('#mqText').empty();
		mq3.empty();		

		$('#mqTextShow').append($('textarea[name="mqText"]').val());
		$('input[name="mqNumQuiz"]').val(num);
		for ( var j = 0; j < num; j++) {
			mq3.append('<fieldset><legend>ข้อที่ '+(j+1)+'</legend><table><tr><td><?echo _QUESTION?> :</td><td><textarea name="mqQuestion[]" style="width: 400px"></textarea><button onclick="popup(\'index.php?mod=spaw&amp;type=mqQuestion&amp;cid=<?php echo $cid;?>&amp;x='+j+'&amp;txt=\'+getText(this),\'_blank\',750,480)" type="button" class="button">...</button></td></tr><tr><td  valign="top"><?echo _QUIZ_ANSWER?> :</td><td><table id="mqChoies'+j+'"><tr><td><?echo _CORRECTANS?></td><td align="center"><?echo _QUIZ_CHOICE?></td><td align="center">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo _DESCRIPTION;?></td></tr></table><div align="right"><button  class="button" onclick="pChoice('+j+')" type="button">+</button><button  class="button" onclick="mChoice('+j+')" type="button">-</button></div></td></tr><tr><td><?echo _DIFFICUTY?> :</td><td><label><input type="radio" name="quizDiff'+j+'" value="1" checked="checked">1</label><label><input type="radio" name="quizDiff'+j+'" value="2">2</label><label><input type="radio" name="quizDiff'+j+'" value="3">3</label><label><input type="radio" name="quizDiff'+j+'" value="4">4</label><label><input type="radio" name="quizDiff'+j+'" value="5">5</label></td></tr></table></fieldset>');
			choiceCount[j] = 0;
			for ( var k = 0; k < 4; k++) {
				pChoice(j);
			}			
		}				
		$("#mqState2").hide();		
		$("#mqState3").show();
		}else{
			alert("<?php echo _ALT_ERROR?>\n"+"- <?php echo _ALT_QUESION?>\n");
		}
	}
	function pChoice(n) {
		$("table#mqChoies"+n).append('<tr class="nChoice"><td><input type="checkbox" name="answerCh'+n+'['+choiceCount[n]+']"/></td><td>'+(choiceCount[n]+1)+'.<textarea name="textCh'+n+'[]"></textarea><button onclick="popup(\'index.php?mod=spaw&amp;type=Choice&amp;cid=<?php echo $cid;?>&amp;chid=&amp;x='+n+'&amp;y='+choiceCount[n]+'&amp;txt=\'+getText(this),\'_blank\',750,480)" type="button" class="button">...</button></td><td valign="top" align="left"><TEXTAREA NAME="reasonCh'+n+'[]" ></TEXTAREA></td></tr>');
		choiceCount[n]++;			
	}
	function mChoice(n) {
		$("table#mqChoies"+n+" tr.nChoice:last").remove();
		choiceCount[n]--;
	}
	$(function() {
		var numQuiz = $("select#numOfQuestion");
		for ( var i = 0; i < 100; i++) {
			numQuiz.append('<option value="' + i + '">' + i + '</option>');
		}
		numQuiz.change(function() {
			$("#mqState1").hide();			
			$("#mqState2").show();
		});
	});
	var choiceCount = new Array();	
	function getText(dom){
		//var textbox = $('[name="'+name+'"]').eq(index);
		var textbox = $(dom).prev();
		//var textbox = $('textarea').eq(dom);
		return encodeURIComponent(textbox.val());
	}
	function validation() {		
		var err = "";
		if(!questionVali()){
			err += "- <?php echo _ALT_QUESION?>\n";
		}
		if(!choiceVali()){
			err += "- <?php echo _ALT_CHTEXT?>\n";
		}		
		if(!chkChoice()){
			err += "- <?php echo _ALT_CHOICE?>\n";
		}
		if(err.length == 0)
			return true;
		alert("<?php echo _ALT_ERROR?>\n"+err);
		return false;
	}
	function chkChoice() {
		var n;
		for (var i = 0; i < choiceCount.length; i++) {
			n = $('input[name^="answerCh'+i+'"]:checked').length;
			if(n == 0){
				return false;
			}
		}		
		return true;		
	}
	function choiceVali() {
		var ret = true;
		$('textarea[name^="textCh"]').each(function(i){
			if($(this).val().length == 0)
				ret = false;
		});
		return ret;
	}
	function questionVali() {
		var ret = true;
		$('textarea[name="mqQuestion[]"]').each(function(i){
			if($(this).val().length == 0)
				ret = false;
		});
		return ret;
	}
	function mqVali() {
		if($('textarea[name="mqText"]').val().length == 0)
			return false;
		else
			return true;
	}
	</script>
	<?
	//---end html
}
function makeMultiQuizSave($vars) {
	//---------
	addslashesextended($vars);
	//---------
	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];
	if(isset($mcid)){
		$arr_mcid = getQuizMember($mcid,3);
		for ($i = 0; $i < count($arr_mcid); $i++) {
			$answerCh = "answerCh".$arr_mcid[$i][mcid];
			$answerCh = $$answerCh;
			$sumAnswer = 0;
			foreach ($answerCh as $choi) {
				$sumAnswer += pow(2,$choi);
			}
			$quizDiff = "quizDiff".$arr_mcid[$i][mcid];
			$quizDiff = $$quizDiff;
			$textCh = "textCh".$arr_mcid[$i][mcid];
			$textCh = $$textCh;
			$reasonCh = "reasonCh".$arr_mcid[$i][mcid];
			$reasonCh = $$reasonCh;
				
			//get keyword
			$sqlkeyword = "SELECT $multichoiceColumn[keyword] 
			FROM $multichoice WHERE $multichoiceColumn[mcid]='$mcid'";
			$resultkeyword = $dbconn->Execute($sqlkeyword);
			$quizKey = $resultkeyword->fields[0];
		
			//get guid
			$guid = getGuid();
			$sql = "UPDATE $multichoice SET $multichoiceColumn[question] = '$mqQuestion[$i]',
			$multichoiceColumn[answer] = '$sumAnswer',
			$multichoiceColumn[difficulty] = '$quizDiff',
			$multichoiceColumn[keyword] = '$quizKey',
			$multichoiceColumn[share] = '$permission',
			$multichoiceColumn[guid] = '$guid'
			WHERE $multichoiceColumn[mcid] = '".$arr_mcid[$i][mcid]."'";
			$dbconn->Execute($sql);
			choiceManager($arr_mcid[$i][mcid],$textCh,$reasonCh);
		}
		//get guid
		$guid = getGuid();
		$sql = "UPDATE $multichoice SET $multichoiceColumn[question] = '$mqText',
		$multichoiceColumn[keyword] = '$quizKey',
		$multichoiceColumn[share] = '$permission',
		$multichoiceColumn[guid] = '$guid'
			WHERE $multichoiceColumn[mcid] = '".$arr_mcid[0][foot]."'";
		$dbconn->Execute($sql);
	}else{
		$uid = lnSessionGetVar('uid');
		//Insert
		for ($i = 0; $i < count($mqQuestion); $i++) {
			$answerCh = "answerCh".$i;
			$answerCh = $$answerCh;
			$sumAnswer = 0;
			foreach ($answerCh as $key => $choi) {
				if($choi == 'on'){
					$sumAnswer += pow(2,$key);
				}
			}
			$quizDiff = "quizDiff".$i;
			$quizDiff = $$quizDiff;
			$textCh = "textCh".$i;
			$textCh = $$textCh;
			$reasonCh = "reasonCh".$i;
			$reasonCh = $$reasonCh;
				
			//get guid
			$guid = getGuid();
			$sql = "INSERT INTO $multichoice ($multichoiceColumn[question],$multichoiceColumn[answer],$multichoiceColumn[difficulty],$multichoiceColumn[keyword],$multichoiceColumn[type],$multichoiceColumn[share],$multichoiceColumn[uid],$multichoiceColumn[cid],$multichoiceColumn[guid])
				VALUES ('$mqQuestion[$i]','$sumAnswer','$quizDiff','$quizKey','3','$permission','$uid','$cid','$guid')";
			$dbconn->Execute($sql);
			$sql = "SELECT MAX($multichoiceColumn[mcid]) FROM $multichoice";
			$result = $dbconn->Execute($sql);
			list($mcid) = $result->fields;
			for ($j = 0; $j < count($textCh); $j++) {
				insertChoice($mcid,$j+1,$textCh[$j],$reasonCh[$j]);
			}
		}
		//get guid
		$guid = getGuid();
		$sql = "INSERT INTO $multichoice ($multichoiceColumn[question],$multichoiceColumn[answer],$multichoiceColumn[difficulty],$multichoiceColumn[keyword],$multichoiceColumn[type],$multichoiceColumn[share],$multichoiceColumn[uid],$multichoiceColumn[cid],$multichoiceColumn[guid])
				VALUES ('$mqText','0','0','$quizKey','3','$permission','$uid','$cid','$guid')";
		$dbconn->Execute($sql);
		// Insert
	}
}
function quizEditor($vars) {
	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];

	$sql = "SELECT $multichoiceColumn[question],$multichoiceColumn[answer],$multichoiceColumn[difficulty],$multichoiceColumn[type],$multichoiceColumn[keyword],$multichoiceColumn[share] FROM
	$multichoice WHERE
	$multichoiceColumn[mcid] = '$mcid'";
	$result = $dbconn->Execute($sql);
	list($question1,$answer1,$diff1,$type,$key,$share) = rmslashesextended($result->fields);
	if($share == 0){
		$permission = '<label><input type="radio" name="permission" value="0" checked >'._DENY.'</label><label><input type="radio" name="permission" value="1">'._AGREE.'</label>';
	}else{
		$permission = '<label><input type="radio" name="permission" value="0">'._DENY.'</label><label><input type="radio" name="permission" value="1" checked >'._AGREE.'</label>';
	}
	$permission = '<div>'._PERMISSION.' : '.$permission.'</div>';
	switch ($type){
		case 1:
			//---html
			?>
<div id="makeChoice">
<div>
<form name="quizform"
	action="index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>"
	method="post" onsubmit="return validation()"><input type="hidden"
	name="issue" value="makeSingleQuizSave" /> <input type="hidden"
	name="mcid" value="<?echo $mcid;?>" />
<table>
	<!--<tr>
		<td><?echo _KEYWORD;?></td>
		<td><input type="text" name="quizKey" value="<?echo $key?>"/></td>
	</tr>
	-->
	<tr>
		<td><?echo _QUESTION;?> :</td>
		<td><textarea name="quizContent" style="width: 400px;"><?echo $question1?></textarea>
		<button class="button"
			onclick="popup('index.php?mod=spaw&amp;type=Question&amp;cid=<?php echo $cid;?>&amp;mcid=<?php echo $mcid?>&amp;txt='+getText(this),'_blank',750,480)"
			type="button" class="button">...</button>
		</td>
	</tr>
	<tr>
		<td valign="top"><?echo _QUIZ_ANSWER;?> :</td>
		<td><?echo makeCheckChoice($cid,$mcid)?></td>
	</tr>
</table>
			<?echo $permission;?> <br />
<center>
<button class="button" type="button"
	onclick="window.open('index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>','_self')"><?echo _BACK?></button>
<input class="button" type="submit" value="<?echo _SAVE?>" /></center>
</form>
</div>
</div>
<script
	type="text/javascript" src="javascript/jquery.min.js"></script>
<script type="text/javascript">
	<!--	
	function pChoice() {
		var nCh = $('table#choice<?php echo $mcid?> tr.nChoice:last input[type="checkbox"]').val();
		if(nCh){
			nCh++;
		}else{
			nCh = 0;
		}
		$("table#choice<?php echo $mcid?>").append('<tr class="nChoice"><td><input type="checkbox" name="answerCh<?echo $mcid?>[]" value="'+nCh+'"/></td><td>'+(nCh+1)+'.<textarea name="textCh<?echo $mcid?>[]"></textarea><button onclick="popup(\'index.php?mod=spaw&amp;type=Choice&amp;cid=<?php echo $cid?>&amp;chid=&amp;x=<?php echo $mcid?>&amp;y='+nCh+'&amp;txt=\'+getText(this),\'_blank\',750,480)" class="button" type="button">...</button></td></tr>');		
	}
	function mChoice() {
		$("table#choice<?php echo $mcid?> tr.nChoice:last").remove();		
	}
	function validation() {
		var n = $('input[name^="answerCh"]:checked').length;
		var err = "";
		if(!questionVali()){
			err += "- <?php echo _ALT_QUESION?>\n";
		}
		if(!choiceVali()){
			err += "- <?php echo _ALT_CHTEXT?>\n";
		}		
		if(n == 0){
			err += "- <?php echo _ALT_CHOICE?>\n";
		}
		if(err.length == 0)
			return true;
		alert("<?php echo _ALT_ERROR?>\n"+err);
		return false;
	}
	function choiceVali() {
		var ret = true;
		$('textarea[name^="textCh"]').each(function(i){
			if($(this).val().length == 0)
				ret = false;
		});
		return ret;
	}
	function questionVali() {
		if($('textarea[name="quizContent"]').val().length == 0)
			return false;
		else
			return true;
	}
	function getText(dom){
		//var textbox = $('[name="'+name+'"]').eq(index);
		var textbox = $(dom).prev();
		//var textbox = $('textarea').eq(dom);
		return encodeURIComponent(textbox.val());
	}
	//-->
	</script>
			<?
			//---end html
			break;
case 2:
	$inputQues = "";
	$field = "";
	$arr_mcid = getQuizMember($mcid,$type);
	for ($i = 0; $i < count($arr_mcid); $i++) {
		if($arr_mcid[$i][mcid] != $arr_mcid[$i][foot]){
			$inputQues .= '<tr><td>'._ClozeTestQuiz.($i+1).'<br/><input type="text" name="cqQuestion[]" style="width: 400px" value="'.getInterrogativeSentence($arr_mcid[$i][mcid]).'"/></td><td><u> (ช่องว่างของข้อที่'.($i + 1).') </u></td></tr>';
			$field .= '<fieldset><legend>'._ClozeTestQuiz.($i+1).'</legend><table id="cqChoies'.$i.'"><tr><td valign="top">'._QUIZ_ANSWER.' :</td><td>';
			$field .= makeRadioChoice($cid,$arr_mcid[$i][mcid]);
			$field .= '</td></table></fieldset>';
		}
	}
	$inputQues .= '<tr><td><br/><input type="text" name="cqQuestionEnd" style="width: 400px" value="'.getInterrogativeSentence($arr_mcid[0][foot]).'"/></td><td>*ประโยคปิดท้าย(ไม่มีโปรดเว้นว่าง)</td></tr>';
	//html
	?>
<div id="makeChoice">
<form name="quizform"
	action="index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>"
	method="post" onsubmit="return validation()"><input type="hidden"
	name="mqNumQuiz" /> <input type="hidden" name="issue"
	value="makeClozeQuizSave" /> <input type="hidden" name="mcid"
	value="<?echo $mcid;?>" />

<div id="cqState1"><b>Cloze Test</b>
<fieldset><legend><?php echo _QUESTION?></legend>
<table>
<?echo $inputQues;?>
</table>
</fieldset>
<p align="center">
<button class="button" type="button"
	onclick="window.open('index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>','_self')"><?echo _BACK?></button>
<button class="button" type="button" onclick="goState2()"><?echo _NEXT?></button>
</p>
</div>
<div id="cqState2" style="display: none;">
<fieldset id="cqText"><legend><?echo _QUESTION?></legend></fieldset>
<?echo $field.$permission;?> <br />
<center>
<button class="button" type="button" onclick="goState1()"><?echo _BACK?></button>
<input class="button" type="submit" value="<?echo _SAVE?>" /></center>
</div>
</form>
</div>

<script
	type="text/javascript" src="javascript/jquery.min.js"></script>
<script type="text/javascript">
	function goState1() {
			$("#cqState2").hide();	
			$("#cqState1").show();
	}	
	function goState2() {
		var inputbox = $('input[name="cqQuestion[]"]');
		var field = $('#cqText');
		var cq3 = $("#cqState3 div");
		var i = 1;
		field.empty();
		cq3.empty();
		if(questionVali()){
		inputbox.each(function(){
			field.append($(this).val());
			field.append("<u> (ช่องว่างของข้อที่"+i+") </u>");
			i++;
		});
		field.append($('input[name="cqQuestionEnd"]').val());
		var numQuiz = i-1;
		$("#cqState1").hide();
		$("#cqState2").show();
		}else{
			alert("<?php echo _ALT_ERROR?>\n"+"- <?php echo _ALT_QUESION?>\n");
		}
	}
	function pChoice(n) {
		var nCh = $('table#choice'+n+' tr.nChoice:last input[type="radio"]').val();
		if(nCh){
			nCh++;
		}else{
			nCh = 0;
		}
		$("table#choice"+n).append('<tr class="nChoice"><td><input type="radio" name="answerCh'+n+'" value="'+nCh+'"/></td><td>'+(nCh+1)+'.<textarea name="textCh'+n+'[]"></textarea><button onclick="popup(\'index.php?mod=spaw&amp;type=Choice&amp;cid=<?php echo $cid?>&amp;chid=&amp;x='+n+'&amp;y='+nCh+'&amp;txt=\'+getText(this),\'_blank\',750,480)" class="button" type="button">...</button></td></tr>');
		
	}
	function mChoice(n) {
		$("table#choice"+n+" tr.nChoice:last").remove();
	}	
	function getText(dom){
		//var textbox = $('[name="'+name+'"]').eq(index);
		var textbox = $(dom).prev();
		//var textbox = $('textarea').eq(dom);
		return encodeURIComponent(textbox.val());
	}
	function validation() {
		var n = $('input[name^="answerCh"]:checked').length;
		var err = "";		
		if(!choiceVali()){
			err += "- <?php echo _ALT_CHTEXT?>\n";
		}		
		//if(n != choiceCount.length){
		//	err += "- <?php echo _ALT_CHOICE?>\n";
		//}
		if(err.length == 0)
			return true;
		alert("<?php echo _ALT_ERROR?>\n"+err);
		return false;
	}
	function choiceVali() {
		var ret = true;
		$('textarea[name^="textCh"]').each(function(i){
			if($(this).val().length == 0)
				ret = false;
		});
		return ret;
	}
	function questionVali() {
		var ret = true;
		$('input[name="cqQuestion[]"]').each(function(i){
			if($(this).val().length == 0)
				ret = false;
		});
		return ret;
	}
	</script>
<?
break;
case 3:
	$field = "<script>var mcids = new Array();</script>";
	$arr_mcid = getQuizMember($mcid,$type);
	for ($i = 0; $i < count($arr_mcid); $i++) {
		if($arr_mcid[$i][mcid] != $arr_mcid[$i][foot]){
			$field .= '<fieldset><legend>ข้อที่ '.($i+1).'</legend><table id="cqChoies'.$i.'">';
			$field .= '<tr><td>'._QUESTION.' :</td><td><textarea name="mqQuestion[]" style="width: 400px;">'.getInterrogativeSentence($arr_mcid[$i][mcid]).'</textarea><button onclick="popup(\'index.php?mod=spaw&amp;type=mqQuestion&amp;cid='.$cid.'&amp;x='.$i.'&amp;mcid='.$arr_mcid[$i][mcid].'&amp;txt=\'+getText(this),\'_blank\',750,480)" type="button" class="button">...</button></td></tr>';
			$field .= '<tr><td valign="top">'._QUIZ_ANSWER.' :</td><td>';
			$field .= makeCheckChoice($cid,$arr_mcid[$i][mcid]);
			$field .= '</td></table></fieldset>';
			$field .= '<script>mcids['.$i.'] = '.$arr_mcid[$i][mcid].';</script>';
		}
	}
	$question = getInterrogativeSentence($arr_mcid[0][foot]);
	$question=stripslashes($question);
	$question=nl2br($question);
	$question=str_replace('\\"','"',$question);
	//echo ">>>>>>".$question;
	
	//---html
	?>
<div id="makeChoice">
<form name="quizform"
	action="index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>"
	method="post" onsubmit="return validation()"><input type="hidden"
	name="mqNumQuiz" /> <input type="hidden" name="issue"
	value="makeMultiQuizSave" /> <input type="hidden" name="mcid"
	value="<?echo $mcid;?>" />
<div id="mqState2">
<fieldset id="mqQuestion"><legend><?echo _QUESTION;?></legend> <textarea
	name="mqText" style="width: 400px;"><?echo $question;//getInterrogativeSentence($arr_mcid[0][foot])?></textarea>
<button
	onclick="popup('index.php?mod=spaw&amp;type=mqText&amp;cid=<?php echo $cid;?>&amp;mcid=<?php echo $arr_mcid[0][foot];?>&amp;txt='+getText(this),'_blank',750,480)"
	type="button" class="button">...</button>
</fieldset>
<center>
<button class="button" type="button"
	onclick="window.open('index.php?mod=Courses&file=admin&op=quiz&action=createquiz&cid=<?echo $cid?>','_self')"><?echo _BACK;?></button>
<button class="button" type="button" onclick="goState3()"><?echo _NEXT;?></button>
</center>
</div>
<div id="mqState3" style="display: none;">
<fieldset id="mqTextShow"><legend><?echo _QUESTION?></legend></fieldset>
	<?echo $field.$permission;?> <br />
<center>
<button class="button" type="button" onclick="goState2()"><?echo _BACK;?></button>
<input class="button" type="submit" value="<?echo _SAVE;?>" /></center>
</div>

</form>
</div>

<script
	type="text/javascript" src="javascript/jquery.min.js"></script>
<script type="text/javascript">	
	function goState1() {
		$("#mqState2").hide();	
		$("#mqState1").show();
	}
	function goState2() {
		$("#mqState3").hide();	
		$("#mqState2").show();
	}
	function goState3() {
		var mq3 = $("#mqState3 div");
		if(mqVali()){		
		$('#mqTextShow').append($('textarea[name="mqText"]').val());				
		$("#mqState2").hide();		
		$("#mqState3").show();
		}else{
			alert("<?php echo _ALT_ERROR?>\n"+"- <?php echo _ALT_QUESION?>\n");
		}
	}	
	function pChoice(n) {
		var nCh = $('table#choice'+n+' tr.nChoice:last input[type="checkbox"]').val();
		if(nCh){
			nCh++;
		}else{
			nCh = 0;
		}
		$("table#choice"+n).append('<tr class="nChoice"><td><input type="checkbox" name="answerCh'+n+'[]" value="'+nCh+'"/></td><td>'+(nCh+1)+'.<textarea name="textCh'+n+'[]"></textarea><button onclick="popup(\'index.php?mod=spaw&amp;type=Choice&amp;cid=<?php echo $cid?>&amp;chid=&amp;x='+n+'&amp;y='+nCh+'&amp;txt=\'+getText(this),\'_blank\',750,480)" class="button" type="button">...</button></td></tr>');
		
	}
	function mChoice(n) {
		$("table#choice"+n+" tr.nChoice:last").remove();
	}	
	$(function() {
		var numQuiz = $("select#numOfQuestion");
		for ( var i = 0; i < 100; i++) {
			numQuiz.append('<option value="' + i + '">' + i + '</option>');
		}
		numQuiz.change(function() {
			$("#mqState1").hide();			
			$("#mqState2").show();
		});
	});
	var choiceCount = new Array();	
	function validation() {		
		var err = "";
		if(!questionVali()){
			err += "- <?php echo _ALT_QUESION?>\n";
		}
		if(!choiceVali()){
			err += "- <?php echo _ALT_CHTEXT?>\n";
		}		
		if(!chkChoice()){
			err += "- <?php echo _ALT_CHOICE?>\n";
		}
		if(err.length == 0)
			return true;
		alert("<?php echo _ALT_ERROR?>\n"+err);
		return false;
	}
	function chkChoice() {
		var n;
		for (var i = 0; i < mcids.length; i++) {
			n = $('input[name^="answerCh'+mcids[i]+'"]:checked').length;
			if(n == 0){				
				return false;
			}
		}		
		return true;	
	}
	function choiceVali() {
		var ret = true;
		$('textarea[name^="textCh"]').each(function(i){
			if($(this).val().length == 0)
				ret = false;
		});
		return ret;
	}
	function questionVali() {
		var ret = true;
		$('textarea[name="mqQuestion[]"]').each(function(i){
			if($(this).val().length == 0)
				ret = false;
		});
		return ret;
	}
	function mqVali() {
		if($('textarea[name="mqText"]').val().length == 0)
			return false;
		else
			return true;
	}
	function getText(dom){
		//var textbox = $('[name="'+name+'"]').eq(index);
		var textbox = $(dom).prev();
		//var textbox = $('textarea').eq(dom);
		return encodeURIComponent(textbox.val());
	}
	</script>
	<?
	//---end html
	}
}
function makeRadioChoice($cid,$mcid){
	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];

	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];
	$sql = "SELECT $multichoiceColumn[answer],$multichoiceColumn[difficulty] FROM
	$multichoice WHERE
	$multichoiceColumn[mcid] = '$mcid'";
	$result = $dbconn->Execute($sql);
	list($ans,$diff) = $result->fields;


	$sql = "SELECT $quizChoiceColumn[chid],$quizChoiceColumn[answer],$quizChoiceColumn[feedback] FROM $quizChoice WHERE $quizChoiceColumn[mcid] = '$mcid' ORDER BY $quizChoiceColumn[weight]";
	$result = $dbconn->Execute($sql);
	//--html
	$html = '<table id="choice'.$mcid.'"><tr><td>'._CORRECTANS.'</td><td align="center">'._QUIZ_CHOICE.'</td><td align="center">&nbsp;&nbsp;&nbsp;&nbsp;'._DESCRIPTION.'</td></tr>';
	for($i = 0;list($chid,$answer,$feedback) = rmslashesextended($result->fields);$i++){
		$result->MoveNext();
		$html .= '<tr class="nChoice"><td><input ';
		if(pow(2,$i) == $ans)$html .= "checked";
		//$html .= ' type="radio" name="answerCh'.$mcid.'" value="'.$i.'"/></td><td>'.($i+1).'.<textarea name="textCh'.$mcid.'[]">'.$answer.'</textarea><button class="button" onclick="popup(\'index.php?mod=spaw&amp;type=Choice&amp;cid='.$cid.'&amp;chid=&amp;x='.$mcid.'&amp;y='.$i.'&amp;txt=\'+getText(this),\'_blank\',750,480)" type="button">...</button></td></tr>';
		$html .= ' type="radio" name="answerCh'.$mcid.'" value="'.$i.'"/></td><td>'.($i+1).'.<textarea name="textCh'.$mcid.'[]">'.$answer.'</textarea><button class="button" onclick="popup(\'index.php?mod=spaw&amp;type=Choice&amp;cid='.$cid.'&amp;chid='.$chid.'&amp;x='.$mcid.'&amp;y='.$i.'&amp;txt=\'+getText(this),\'_blank\',750,480)" type="button">...</button></td>';
		$html .= '<td valign="top" align="left"><TEXTAREA NAME="reasonCh'.$mcid.'[]" >'.$feedback.'</TEXTAREA></td>';
		$html .= '</tr>';
	}
	$html .= '</table>';
	$html .= '<div align="right"><button onclick="pChoice('.$mcid.')" type="button" class="button">+</button><button onclick="mChoice('.$mcid.')" type="button" class="button">-</button></div>';
	$html .= '<tr><td>'._DIFFICUTY.' :</td><td>';
	for ($i = 1; $i <= 5; $i++) {
		$html .= '<label><input ';
		if($i == $diff){
			$html .= 'checked ';
		}
		$html .= 'type="radio" name="quizDiff'.$mcid.'" value="'.$i.'">'.$i.'</label>';
	}
	$html .= '</td></tr>';
	//--html
	return $html;

}
function makeCheckChoice($cid,$mcid) {
	extract($vars);
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];
	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];

	$sql = "SELECT $multichoiceColumn[answer],$multichoiceColumn[difficulty] FROM
	$multichoice WHERE
	$multichoiceColumn[mcid] = '$mcid'";
	$result = $dbconn->Execute($sql);
	list($ans,$diff) = $result->fields;

	$sql = "SELECT $quizChoiceColumn[chid],$quizChoiceColumn[answer],$quizChoiceColumn[feedback] FROM $quizChoice WHERE $quizChoiceColumn[mcid] = '$mcid' ORDER BY $quizChoiceColumn[weight]";
	$result = $dbconn->Execute($sql);

	//--html
	$html = '<table id="choice'.$mcid.'"><tr><td>'._CORRECTANS.'</td><td align="center">'._QUIZ_CHOICE.'</td><td align="center">&nbsp;&nbsp;&nbsp;&nbsp;'._DESCRIPTION.'</td></tr>';
	for($i = 0;list($chid,$answer,$feedback) = rmslashesextended($result->fields);$i++){
		$result->MoveNext();
		$html .= '<tr class="nChoice"><td><input ';
		if(pow(2,$i) & $ans)$html .= "checked";
		//$html .= ' type="checkbox" name="answerCh'.$mcid.'[]" value="'.$i.'"/></td><td>'.($i+1).'.<textarea name="textCh'.$mcid.'[]">'.$answer.'</textarea><button class="button" onclick="popup(\'index.php?mod=spaw&amp;type=Choice&amp;cid='.$cid.'&amp;chid=&amp;x='.$mcid.'&amp;y='.$i.'&amp;txt=\'+getText(this),\'_blank\',750,480)" type="button">...</button></td></tr>';
		$html .= ' type="checkbox" name="answerCh'.$mcid.'[]" value="'.$i.'"/></td><td>'.($i+1).'.<textarea name="textCh'.$mcid.'[]">'.$answer.'</textarea><button class="button" onclick="popup(\'index.php?mod=spaw&amp;type=Choice&amp;cid='.$cid.'&amp;chid='.$chid.'&amp;x='.$mcid.'&amp;y='.$i.'&amp;txt=\'+getText(this),\'_blank\',750,480)" type="button">...</button></td>';
		$html .= '<td valign="top" align="left"><TEXTAREA NAME="reasonCh'.$mcid.'[]" >'.$feedback.'</TEXTAREA></td>';
		$html .= '</tr>';
	}
	$html .= '</table>';
	$html .= '<div align="right"><button onclick="pChoice('.$mcid.')" class="button" type="button">+</button><button onclick="mChoice('.$mcid.')" class="button" type="button">-</button></div>';
	$html .= '<tr><td>'._DIFFICUTY.' :</td><td>';
	for ($i = 1; $i <= 5; $i++) {
		$html .= '<label><input ';
		if($i == $diff){
			$html .= 'checked ';
		}
		$html .= 'type="radio" name="quizDiff'.$mcid.'" value="'.$i.'">'.$i.'</label>';
	}
	$html .= '</td></tr>';
	//--html
	return $html;
}
function getQuizMember($mcid,$type) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$quiztest_TB = $lntable['quiz_test'];
	$quiztest_COL = &$lntable['quiz_test_column'];
	$quizmulti_TB = $lntable['quiz_multichoice'];
	$quizmulti_COL = &$lntable['quiz_multichoice_column'];
	//=== Query Member's Header
	$sql = "SELECT $quiztest_COL[mcid],$quizmulti_COL[type] FROM
	$quiztest_TB,
	$quizmulti_TB WHERE
	$quiztest_COL[mcid] = $quizmulti_COL[mcid] AND
	$quiztest_COL[qid] = '$qid' ORDER BY
	$quiztest_COL[weight] ASC";
	$result = $dbconn->Execute($sql);
	//== array mcid
	$arr_mcid;
	if($type == 1){
		$arr_mcid[] = array("mcid"=>$mcid,"type"=>$type);
		continue;
	}
	//=== Query Tail
	$sql = "SELECT MIN($quizmulti_COL[mcid]) FROM
	$quizmulti_TB WHERE
	$quizmulti_COL[mcid] > '$mcid' AND
	$quizmulti_COL[answer] = '0' AND
	$quizmulti_COL[difficulty] = '0' ORDER BY
	$quizmulti_COL[mcid] ASC";
	$stopMcid = $dbconn->Execute($sql);
	list($stopMcid) = $stopMcid->fields;
	$sql = "SELECT $quizmulti_COL[mcid] FROM
	$quizmulti_TB WHERE
	$quizmulti_COL[mcid] >= '$mcid' AND
	$quizmulti_COL[mcid] < '$stopMcid' ORDER BY
	$quizmulti_COL[mcid] ASC";
	$mcidS = $dbconn->Execute($sql);
	while(list($mcidT) = $mcidS->fields){
		$mcidS->MoveNext();
		$arr_mcid[] = array("mcid"=>$mcidT,"type"=>$type,"head"=>$mcid,"foot"=>$stopMcid);
	}
	return $arr_mcid;
}
function getInterrogativeSentence($mcid) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$quizmulti_TB = $lntable['quiz_multichoice'];
	$quizmulti_COL = &$lntable['quiz_multichoice_column'];

	$sql = "SELECT $quizmulti_COL[question] FROM
	$quizmulti_TB WHERE
	$quizmulti_COL[mcid] = '$mcid'";
	$return = $dbconn->Execute($sql);
	list($return) = rmslashesextended($return->fields);
	return $return;
}
function addslashesextended(&$arr_r)
{
	if(is_array($arr_r))
	{
		foreach ($arr_r as &$val)
		is_array($val) ? addslashesextended($val):$val=addslashes($val);
		unset($val);
	}
	else
	$arr_r=addslashes($arr_r);
}
function rmslashesextended(&$arr_r)
{
	if(is_array($arr_r))
	{
		foreach ($arr_r as &$val)
		is_array($val) ? rmslashesextended($val):$val=stripslashes($val);
		unset($val);
	}
	else
	$arr_r=stripslashes($arr_r);
	return $arr_r;
}
function xeonHtml($tag,$text = "",$attr = array()) {
	$str_attr = "";
	foreach ($attr as $key => $val) {
		$str_attr .= "$key=\"$val\" ";
	}
	if($text = ""){
		$ret = "<$tag $str_attr/>";
	}else{
		$ret .= "<$tag $str_attr>$text</$tag>\n";
	}
	return $ret;
}
function choiceTypeChecker($answer){
	$count = 0;
	for ($i=0; $i<10; $i++) {
		if ($answer & pow(2,$i)) {
			$count++;
		}
	}
	if ($count == 1) {
		return 0;
	}
	else {
		return 1;
	}
}
function saveChoice($mcid,$w,$text,$reason) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];
	$sql = "UPDATE $quizChoice SET
	$quizChoiceColumn[answer] = '$text',
	$quizChoiceColumn[feedback] = '$reason'
			WHERE $quizChoiceColumn[weight] = '$w'
			AND $quizChoiceColumn[mcid] = '$mcid'";
	//echo '>>>'.$sql;exit();
	$dbconn->Execute($sql);
}
function insertChoice($mcid,$w,$text,$reason) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];
	$sql = "INSERT INTO $quizChoice ($quizChoiceColumn[mcid],$quizChoiceColumn[answer],$quizChoiceColumn[feedback],$quizChoiceColumn[weight]) VALUES ('$mcid','$text','$reason','$w')";
	$dbconn->Execute($sql);
}
function delChoice($mcid,$w,$type) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$multichoice = $lntable['quiz_multichoice'];
	$multichoiceColumn = &$lntable['quiz_multichoice_column'];
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];
	if($type == 0){
		$sql = "DELETE FROM $quizChoice WHERE $quizChoiceColumn[mcid] = '$mcid' AND $quizChoiceColumn[weight] = '$w'";
	}else{
		$sql = "DELETE FROM $quizChoice WHERE $quizChoiceColumn[mcid] = '$mcid' AND $quizChoiceColumn[weight] >= '$w'";
	}
	$dbconn->Execute($sql);
}
function choiceManager($mcid,$texts,$reasons) {
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();
	$quizChoice = $lntable['quiz_choice'];
	$quizChoiceColumn = &$lntable['quiz_choice_column'];
	$sql = "SELECT COUNT(*) FROM $quizChoice WHERE $quizChoiceColumn[mcid] = '$mcid'";
	$dbconn->Execute($sql);
	$result = $dbconn->Execute($sql);
	list($DBlength) = $result->fields;
	$i = 0;
	$min = min($DBlength,count($texts));
	while ($i < $min){
		saveChoice($mcid,$i+1,$texts[$i],$reasons[$i]);
		$i++;
	}
	if($min == $DBlength){
		while ($i < count($texts)){
			insertChoice($mcid,$i+1,$texts[$i],$reasons[$i]);
			$i++;
		}
	}else{
		delChoice($mcid,$i+1,1);
	}
}
?>
