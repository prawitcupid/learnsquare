<?php
/*
* show study report
*/
if (!defined("LOADED_AS_MODULE")) {
	die ("You can't access this file directly...");
}
if (!lnSecAuthAction(0, 'Courses::report', "::", ACCESS_COMMENT)) {
	echo "<CENTER><h1>"._NOAUTHORIZED." to read ".$mod." module!</h1></CENTER>";
	return false;
}

/* - - - - - - - - - - - */
include 'header.php';
OpenTable();

echo '<TABLE  WIDTH=100% HEIGHT=400><TR VALIGN="TOP"><TD>';

//echo lnBlockTitle($mod,'questionaire');
echo '<p class="header"><b>'._QUESTIONAIRE.'</b></p>';

$vars= array_merge($_GET,$_POST);	
/* options */

switch($op) {
	
	case "qtForm":  questionaireForm($vars); 
	
}

echo '</TD></TR></TABLE>';

CloseTable();
include 'footer.php';
/* - - - - - - - - - - - */


function questionaireForm($vars)
{
	//echo $eid;
?>

<body>

<p><b>1. เนื้อหาหลักสูตร</b></p>
<form name="form1" method="post" action="index.php">
<table width="100%"  border="1" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#669900">
    <tr bgcolor="#669900">
      <td width="50%" bgcolor="#669900"><div align="center"><FONT  COLOR="#FFFFFF"><B>หัวข้อประเมิน</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>น้อยมาก</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>น้อย</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>ปานกลาง</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>ดี</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>ดีมาก</B></div></td>
    </tr>
    <tr>
      <td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      ความรู้ของเนื้อหาที่ได้รับตรงตามจุดประสงค์การเรียนรู้</td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_1" type="radio" value="1">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_1" type="radio" value="2">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_1" type="radio" value="3">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_1" type="radio" value="4">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_1" type="radio" value="5">
      </div></td>
    </tr>
    <tr>
      <td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
     ความรู้ที่ได้รับมีประโยชน์และสามารถนำไปใช้ได้จริง</td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_2" type="radio" value="1">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_2" type="radio" value="2">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_2" type="radio" value="3">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_2" type="radio" value="4">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_2" type="radio" value="5">
      </div></td>
    </tr>
    <tr>
      <td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      ลำดับของเนื้อหาหลักสูตรในการนำเสนอ</td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_3" type="radio" value="1">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_3" type="radio" value="2">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_3" type="radio" value="3">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_3" type="radio" value="4">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_3" type="radio" value="5">
      </div></td>
    </tr>
    <tr>
      <td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
    ความเหมาะสมของเนื้อหาหลักสูตร</td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_4" type="radio" value="1">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_4" type="radio" value="2">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_4" type="radio" value="3">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_4" type="radio" value="4">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_4" type="radio" value="5">
      </div></td>
    </tr>
    <tr>
      <td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      ความเหมาะสมของแบบทดสอบ<FONT FACE="Times New Roman, serif">/</FONT>งานที่ได้รับมอบหมาย</td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_5" type="radio" value="1">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_5" type="radio" value="2">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_5" type="radio" value="3">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_5" type="radio" value="4">
      </div></td>
      <td width="10%" bgcolor="#FFFFFF"><div align="center">
        <input name="t1_5" type="radio" value="5">
      </div></td>
    </tr>
  </table>

<br>

<p><b>2. เทคนิคการถ่ายทอดและนำเสนอ</b></p>
<table width="100%"  border="1" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#669900">
  <tr bgcolor="#669900">
      <td width="50%" bgcolor="#669900"><div align="center"><FONT  COLOR="#FFFFFF"><B>หัวข้อประเมิน</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>น้อยมาก</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>น้อย</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>ปานกลาง</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>ดี</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>ดีมาก</B></div></td>
  </tr>
  <tr>
    <td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
   ความเอาใจใส่ต่อผู้เรียนของผู้สอนในแบบออนไลน</td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t2_1" type="radio" value="1">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t2_1" type="radio" value="2">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t2_1" type="radio" value="3">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t2_1" type="radio" value="4">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t2_1" type="radio" value="5">
    </div></td>
  </tr>
  <tr>
    <td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      การถามตอบระหว่างเรียนผ่านกระดานข่าว (Webboard)</td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t2_2" type="radio" value="1">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t2_2" type="radio" value="2">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t2_2" type="radio" value="3">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t2_2" type="radio" value="4">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t2_2" type="radio" value="5">
    </div></td>
  </tr>
</table>



<p><b>3. สื่อ/เครื่องมือสำหรับเรียนออนไลน์</b></p>
<table width="100%"  border="1" cellpadding="0" cellspacing="1" bordercolor="#FFFFFF" bgcolor="#669900">
  <tr bgcolor="#669900">
      <td width="50%" bgcolor="#669900"><div align="center"><FONT  COLOR="#FFFFFF"><B>หัวข้อประเมิน</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>น้อยมาก</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>น้อย</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>ปานกลาง</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>ดี</B></div></td>
      <td width="10%"><div align="center"><FONT  COLOR="#FFFFFF"><B>ดีมาก</B></div></td>
  </tr>
  <tr>
    <td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      ความสวยงาม การออกแบบสื่อบทเรียนออนไลน์ </td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_1" type="radio" value="1">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_1" type="radio" value="2">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_1" type="radio" value="3">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_1" type="radio" value="4">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_1" type="radio" value="5">
    </div></td>
  </tr>
  <tr>
    <td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      ความรวดเร็วในการเข้าถึงระบบ/บทเรียนออนไลน์</td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_2" type="radio" value="1">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_2" type="radio" value="2">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_2" type="radio" value="3">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_2" type="radio" value="4">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_2" type="radio" value="5">
    </div></td>
  </tr>
  <tr>
    <td width="50%" bgcolor="#FFFFFF"><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
      ความยากง่ายในการใช้งานระบบ e-Learning </td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_3" type="radio" value="1">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_3" type="radio" value="2">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_3" type="radio" value="3">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_3" type="radio" value="4">
    </div></td>
    <td width="10%" bgcolor="#FFFFFF"><div align="center">
      <input name="t3_3" type="radio" value="5">
    </div></td>
  </tr>
</table>

<p><b>4. ข้อเสนอแนะอื่นๆ</b></p>
<textarea name="t4" cols="80" rows="5"></textarea>
<br><br>

<INPUT TYPE="hidden" NAME="action" VALUE="savequestionaire">
<INPUT TYPE="hidden" NAME="op" VALUE="show_report">
<INPUT TYPE="hidden" NAME="mod" VALUE="Courses">
<INPUT TYPE="hidden" NAME="file" VALUE="report">

<input type="submit" class="button" name="Submit" value="Submit">

</form>
</body>


<?

}

?>