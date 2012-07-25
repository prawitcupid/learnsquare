<?php
// define default 
$welcome_msg = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="100%" height="200">
  <param name="movie" value="flash_welcome.swf">
  <param name="quality" value="high">
  <embed src="flash_welcome.swf" quality="high" pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash" type="application/x-shockwave-flash" width="100%" height="200"></embed>
</object>';

$links = '
<div style="text-align: center; "><a target="_blank" title="eDLTV :: e-Learning ของการศึกษาทางไกลผ่านดาวเทียม:: LearnSquare " href="http://edltv.thai.net/"><img border="0" alt="" src="edltv-s.jpg" /></a>
</div>
<div> 
  <div style="text-align: center; "><a target="_blank" title="eDLTV เพื่อพัฒนาอาชีพ" href="http://edltv.vec.go.th/"><img border="0" alt="" src="edltv-v.jpg" /></a>
  </div> 
  <div style="text-align: center; "><a target="_blank" title="Malayu e-Learning ภาษามลายูถิ่น" href="http://malayu.nectec.or.th/"><img border="0" alt="" src="malayu.jpg" /></a>
  </div> 
  <div style="text-align: center; "><a target="_blank" title="Karen e-Learning   ภาษากระเหรี่ยง" href="http://karen.nectec.or.th/"><img border="0" alt="" src="karen.jpg" /></a><br /> 
  </div> 
</div>';
//chang here
$thanks_msg = '<center><a href="http://www.egat.co.th/"><img border="0" alt="" src="egat.gif" /></a> <a href="http://www.nectec.or.th/"><img border="0" alt="" src="nectec_logo.gif" /></a> <a href="http://www.nstda.or.th/"><img border="0" alt="" src="nstda.gif" /></a> </center>';

$download = "<LI type=circle><A href=\"\"><B>คู่มือการใช้งาน</B></A> \r\n<LI type=circle><A href=\"\"><B>ตัวอย่างหลักสูตร</B></A> \r\n<LI type=circle><A href=\"\"><B>ขั้นตอนการสมัคร</B></A> \r\n<LI type=circle><A href=\"\"><B>การลงทะเบียนเรียน</B></A> \r\n<LI type=circle><A href=\"\"><B>ขั้นตอนการเข้าเรียน</B></A> </LI>";

$help = "<li type=\"circle\"><a title=\"manual\" target=\"_blank\" href=\"info/manual\"><b>คู่มือการใช้งาน</b></a></li>
<li type=\"circle\"><a title=\"installation file\" target=\"_blank\" href=\"info/installation\"><b>ไฟล์สำหรับการติดตั้ง</b></a></li>
<li type=\"circle\"><b><a target=\"_blank\" href=\"info/scorm_content\">ตัวอย่างบทเรียน (SCORM)</a>
    <br /></b></li>
<li type=\"circle\"><b><b><a target=\"_blank\" href=\"info/quickguide/index.html\"><b>Quickguide</b></a></b></b></li>";

// blocks table

$table = $prefix."_blocks";
$dbconn->Execute("SET character_set_results=utf8");
$dbconn->Execute("SET collation_connection = utf8_general_ci");
$dbconn->Execute("SET NAMES 'utf8'");

$result = $dbconn->Execute("INSERT INTO $table VALUES ('1', 'user', 'User Organizer', '', '', '10', 'l', '1.0', '1', '0', '".time()."', '')") or die ("<b>"._NOTUPDATED." $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('2', 'online', 'User Online', '', '', '15', 'l', '2.0', '1', '0', ".time().", '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('3', '', 'Welcome Message', '".$welcome_msg."', '', '0', 'c', '1.0', '0', '0', ".time().", '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('10', '', 'เพื่อนบ้าน', '".$links."', '', '0', 'r', '1.0', '0', '0', ".time().", '')") or die ("<b>"._NOTUPDATED. " $table</b>");

//$result = $dbconn->Execute("INSERT INTO $table VALUES ('3', '', 'Welcome Message', '".$welcome_msg."', '', '0', 'c', '1.0', '1', '0', ".time().", '')") or die ("<b>"._NOTUPDATED. " $table</b>");



$result = $dbconn->Execute("INSERT INTO $table VALUES ('4', 'school', 'สาระการเรียนรู้', '', '', '14', 'c', '3.0', '1', '0', ".time().", '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('5', '', 'Thanks', '".$thanks_msg."', '', '0', 'c', '3.0', '1', '0', ".time().", '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('6', '', 'Admin Message', '<center>ระบบ e-Learning เป็นรูปแบบการเรียนที่ผู้เรียนสามารถเรียนรู้ผ่านเครือข่าย คอมพิวเตอร์ด้วยตนเอง โดยสามารถเรียนรู้ได้ตามอัธยาศัยทุกที่ ทุกเวลา และยังสามารถเลือกเรียนได้ตามความสามารถและความสนใจของตนเองอีกด้วย </center><center><span style=\"COLOR: #0f68d6\"><strong>LearnSquare </strong></span>เป็นระบบจัดการการเรียนการสอนบนเครือข่ายอินเทอร์เน็ตที่มีความสามารถในการสร้างและจัดการหลักสูตร รวมถึงทำกิจกรรมในการเรียนการสอนเสมือนในห้องเรียนจริง</center>', '', '0', 'r', '1.0', '1', '0', ".time().", '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('7', '', 'Download', '".$download."', '', '0', 'r', '1.0', '0', '0', ".time().", '')") or die ("<b>"._NOTUPDATED. " $table</b>");

/*
$result = $dbconn->Execute("INSERT INTO $table VALUES ('8', '', 'Help', '".$help."', '', '0', 'r', '1.0', '1', '0', ".time().", '')") or die ("<b>"._NOTUPDATED. " $table</b>");
echo "<br><font class=\"pn-sub\">$table "._UPDATED."</font>";
*/

$result = $dbconn->Execute("INSERT INTO $table VALUES ('9', 'schedule', 'วิชาเปิดลงทะเบียน', '', '', '17', 'c', '2.0', '1', '0', ".time().", '')") or die ("<b>"._NOTUPDATED. " $table</b>");



// groups table

$table = $prefix."_groups";
$result = $dbconn->Execute("INSERT INTO $table VALUES ('1','Admin','Administration','1')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('2','Instructor','Instructor','2')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('3','TA','Teaching Assistant','3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('4','Student','Student','4')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('0','Guest','Guest','0')") or die ("<b>"._NOTUPDATED. " $table</b>");
echo "<br><font class=\"pn-sub\">$table "._UPDATED."</font>";

// modules table
$table = $prefix."_modules";

$result = $dbconn->Execute("INSERT INTO $table VALUES ('1', 'Admin', '0', 'Admin', 'Administration Menu', 'Admin', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('2', 'Blocks', '0', 'Blocks', 'Blocks', 'Blocks', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('3', 'Contactus', '1', 'Contact Us', 'Contact Us', 'Contactus', '1.0', '0', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('4', 'Group', '0', 'Group', 'Group','Group', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('5', 'Main', '0', 'Main', 'Main Module', 'Main', '1.0', '0', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('6', 'Message', '1', 'Message', 'Message', 'Message', '1.0', '0', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('7', 'Modules', '0', 'Modules', 'Modules',  'Modules', '1', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('8', 'Settings', '0', 'Site Setting', 'Site Settings', 'Settings', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('9', 'Tellafriend', '1', 'Contact Us', 'Tellafriend',  'Tellafriend', '1.0', '0', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('10', 'User', '0', 'User Administration', 'User',  'User', '1.0', '1', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('11', 'Permissions', '0', 'Permissions', 'Permissions', 'Permissions', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('12', 'Courses', '0', 'Courses', 'Add Courses!', 'Courses', '1.0', '1', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
//$result = $dbconn->Execute("INSERT INTO $table VALUES ('13', 'Richedit', '1', 'Richtext Editor', 'Richedit',  'Richedit', '1.0', '0', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('13', 'spaw', '0', 'WYSIWYG HTML editor', 'spaw',  'spaw', '2.0.5', '0', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('14', 'Schools', '0', 'Schools', 'School List','Schools', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('15', 'UserOnline', '1', 'User Online', 'User Online', 'UserOnline', '1.0', '0', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('16', 'Log', '0', 'Logging', 'Event Logging', 'Log', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('17', 'Submissions', '0', 'Course Submissions', 'Course Submission','Submissions', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
//$result = $dbconn->Execute("INSERT INTO $table VALUES ('18', 'Chat', '1', 'Chat', 'Chat Room',  'Chat', '1', '1', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('19', 'Forums', '1', 'Forums', 'Discussion forums', 'Forums', '1', '1', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('20', 'Private_Messages', '1', 'Private Messages', 'Private Messages', 'Private_Messages', '1', '1', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('21', 'SCORM', '1', 'SCORM', 'SCORM', 'SCORM', '1', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('22', 'Calendar', '1', 'Calendar', 'Calendar', 'Calendar', '1', '0', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('23', 'Note', '1', 'Note', 'Note', 'Note', '1', '0', '1', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
echo "<br><font class=\"pn-sub\">$table "._UPDATED."</font>";
$result = $dbconn->Execute("INSERT INTO $table VALUES ('24', 'Report', '0', 'Report', 'Study Summery Report', 'Report', '2.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('25', 'Upgrade', '0', 'Backup/Update', 'Backup/Update', 'Upgrade', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('26', 'News', '0', 'News', 'News and Webboard', 'News', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('27', 'Vaja', '0', 'Vaja', 'Vaja Webservice', 'Vaja', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('28', 'RSS', '0', 'RSS', 'RSS News', 'RSS', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('29', 'Statistic', '0', 'Statistic', 'Web Statistic', 'Statistic', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('30', 'Quiz', '0', 'Quiz', 'Quiz', 'Quiz', '1.0', '0', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('31', 'SMT', '0', 'SMT', 'Statistic Machine Translation Webservice', 'SMT', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('32', 'JoeJae', '0', 'JoeJae', 'JoeJae ChatRoom', 'JoeJae', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('33', 'Repository', '0', 'Repository', 'Repository Content Sharing', 'Repository', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('34', 'Mining', '0', 'Mining', 'Mining tracking learning', 'Mining', '1.0', '1', '0', '3')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('35', 'Mobile', '0', 'Mobile', 'Learnsquare Mobile version', 'Mobile', '1.0', '1', '0', '2')") or die ("<b>"._NOTUPDATED. " $table</b>");

// modules_vars
$table = $prefix."_module_vars";

$result = $dbconn->Execute("INSERT INTO $table VALUES ('1', 'startpage', 's:4:\"Main\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('2', 'language', 's:3:\"tha\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('3', 'seclevel', 's:4:\"High\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('4', 'secmeddays', 'i:7;')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('5', 'intranet', 'i:0;')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('6', 'secinactivemins', 's:2:\"15\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('7', 'theme_change', 's:1:\"0\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('8', 'Default_Theme', 's:8:\"BlueSea2\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('9', 'slogan', 's:20:\"Thai Open Source LMS\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('10', 'sitename', 's:27:\"e-Learning : LearnSquare V5\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('11', 'banner', 'i:0;')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('12', 'foot', 's:144:\"<font color=\"#888888\"><B><BR>Copyright &copy; 2009 <a href=\"http://www.learnsquare.com\">www.learnsquare.com</a>, All rights reserved.</B></font>\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('13', 'adminmail', 's:0:\"\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('14', 'reg_allowreg', 's:1:\"0\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('15', 'reg_min_nickname', 's:1:\"4\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('16', 'reg_max_nickname', 's:2:\"20\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('17', 'reg_min_password', 's:1:\"6\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('18', 'reg_max_password', 's:2:\"32\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('19', 'reg_id_len', 's:2:\"13\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('20', 'reg_uniuname', 's:1:\"1\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('21', 'reg_uniid', 's:1:\"1\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('22', 'reg_uniemail', 's:1:\"1\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('23', 'reg_allowfile', 's:1:\"1\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('24', 'admingraphic', 's:1:\"1\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('25', 'pagesize', 's:2:\"20\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('26', 'default_group', 's:1:\"4\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('27', 'pagelog', 's:2:\"50\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('28', 'htmleditor', 's:1:\"1\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('29', 'reg_allowfile', 's:1:\"1\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('30', 'chatserver', 's:9:\"localhost\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('31', 'showsmiley', 's:1:\"1\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('32', 'uploadpic', 's:1:\"1\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('33', 'inboxsize', 's:1:\"1\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('34', 'VajaStatus', 's:1:\"0\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('35', 'VajaServiceAddr', 's:50:\"http://203.185.132.236:8083/axis/ServerWS.jws?wsdl\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('36', 'ln_version', 's:3:\"5.0\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('37', 'patch_version', 's:1:\"0\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('38', 'logtime', 's:10:\"".time()."\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('39', 'logdate', 's:2:\"30\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('40', 'VajaServiceWav', 's:27:\"http://203.185.132.236/wav/\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('41', 'SMTServiceAddr', 's:66:\"http://203.185.132.252:8080/ws1/services/ServiceCenterService?wsdl\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('42', 'SMTStatus', 's:1:\"0\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('43', 'SUPARSITStatus', 's:1:\"0\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('44', 'LEXITRONStatus', 's:1:\"0\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('45', 'RepositoryAddr', 's:28:\"http://store.learnsquare.com\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('46', 'RepositoryStatus', 's:1:\"0\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('47', 'MiningStatus', 's:1:\"0\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('48', 'MobileStatus', 's:1:\"0\";')") or die ("<b>"._NOTUPDATED. " $table</b>");
echo "<br><font class=\"pn-sub\">$table "._UPDATED."</font>";


// group perms  table
$table = $prefix."_group_perms";

$result = $dbconn->Execute("INSERT INTO $table VALUES ('1', '1', '1', '0', 'Courses::Student', '.*', '0', '0')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('2', '1', '2', '0', '.*', '.*', '800', '0')") or die ("<b>"._NOTUPDATED. " $table</b>");
//$result = $dbconn->Execute("INSERT INTO $table VALUES ('3', '-1', '4', '0', 'Admin::', '.*', '0', '0')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('3', '-1', '6', '0', '.*', '.*', '200', '0')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('4', '2', '3', '0', '.*', '.*', '400', '0')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('5', '3', '4', '0', '.*', '.*', '400', '0')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('6', '4', '5', '0', '.*', '.*', '300', '0')") or die ("<b>"._NOTUPDATED. " $table</b>");
echo "<br><font class=\"pn-sub\">$table "._UPDATED."</font>";


// schools table
$table = $prefix."_schools";
$result = $dbconn->Execute("INSERT INTO $table VALUES ('1', 'ท.', 'กลุ่มภาษาไทย', 'การฟัง พูด อ่านและเขียนภาษาไทย หลักการใช้ภาษา วรรณกรรมและวรรณคดี', 'thai')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('2', 'ค.', 'กลุ่มคณิตศาสตร์', 'ทักษะขบวนการทางคณิตศาสตร์ จำนวนและการดำเนินการ การวัด เรขาคณิต พีชคณิต การวิเคราะห์ข้อมูลและความน่าจะเป็น', 'math')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('3', 'ว.', 'กลุ่มวิทยาศาสตร์', 'สิ่งมีชีวิตกับกระบวนการดำรงชีวิต ชีวิตกับสิ่งแวดล้อม สารและสมบัติของสาร แรงและการเคลื่อนที่ พลังงาน กระบวนการเปลี่ยนแปลงของโลก ดาราศาสตร์และอวกาศ ธรรมชาติของวิทยาศาสตร์และเทคโนโลยี ', 'science')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('4', 'ส.', 'กลุ่มสังคมศึกษา ศาสนา และวัฒนธรรม', 'ศาสนา ศีลธรรม จริยธรรม หน้าที่พลเมือง วัฒนธรรม และการดำเนินชีวิตในสังคม เศรษฐศาสตร์ ประวัติศาสตร์ ภูมิศาสตร์', 'social')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('5', 'พ.', 'กลุ่มสุขศึกษาและพลศึกษา', 'การเจริญเติบโตและพัฒนาการของมนุษย์ ชีวิตและครอบครัว การเคลื่อนไหว การออกกำลังกาย กการเล่นกีฬา การสร้างเสริมสุขภาพ สมรรถภาพ และการป้องกันโรค ความปลอดภัยในชีวิต', 'health')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('6', 'ศ.', 'กลุ่มวิชาศิลปะ', 'ทัศนศิลป์ ดนตรี นาฏศิลป์', 'art')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('7', 'อ.', 'กลุ่มการงานอาชีพและเทคโนโลยี', 'เทคโนโลยีสารสนเทศ การดำรงชีวิตและครอบครัว การอาชีพ การออกแบบและเทคโนโลยี เทคโนโลยีเพื่อการทำงานและอาชีพ', 'com')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES ('8', 'ภ.', 'กลุ่มภาษาต่างประเทศ', 'ภาษาเพื่อการสื่อสาร ภาษาและวัฒนธรรม', 'english')") or die ("<b>"._NOTUPDATED. " $table</b>");
//$result = $dbconn->Execute("INSERT INTO $table VALUES ('1', 'CT', 'Computer & Technology', 'หลักสูตรทางด้านคอมพิวเตอร์ และเทคโนโลยีสารสนเทศ', 'ct')") or die ("<b>"._NOTUPDATED. " $table</b>");
//$result = $dbconn->Execute("INSERT INTO $table VALUES ('2', 'TC', 'Technical School', 'หลักสูตรทางด้านเทคนิค และวิชาการต่างๆ', 'tc')") or die ("<b>"._NOTUPDATED. " $table</b>");
//$result = $dbconn->Execute("INSERT INTO $table VALUES ('3', 'VC', 'Variety School', 'หลักสูตรทางด้านการบริหาร และการจัดการ', 'vc')") or die ("<b>"._NOTUPDATED. " $table</b>");
//$result = $dbconn->Execute("INSERT INTO $table VALUES ('4', 'LC', 'Language Center', 'หลักสูตรทางด้านภาษา', 'lc')") or die ("<b>"._NOTUPDATED. " $table</b>");
echo "<br><font class=\"pn-sub\">$table "._UPDATED."</font>";


// user properties table
// schools table
$table = $prefix."_user_property";
$result = $dbconn->Execute("INSERT INTO $table VALUES('1', '_NAME', '0', '255', '4', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('2', '_EMAIL', '0', '255', '0', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('3', '_HOMEPAGE', '1', '255', '0', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('4', '_AVATAR', '1', '255', '5', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('5', '_ICQ', '1', '255', '5', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('6', '_AIM', '1', '255', '0', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('7', '_YIM', '1', '255', '0', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('8', '_MSNM', '1', '255', '0', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('9', '_LOCATION', '1', '255', '0', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('10', '_OCCUPATION', '1', '255', '0', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('11', '_INTERESTS', '1', '255', '0', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('12', '_SIGNATURE', '1', '255', '0', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('13', '_EXTRAINFO', '1', '255', '6', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('14', '_PASSWORD', '-1', '255', '3', '')") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('18', '_UNO', '-1', '255', '1', NULL)") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('19', '_PHONE', '0', '255', '0', NULL)") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('20', '_NEWS', '0', '255', '7', NULL)") or die ("<b>"._NOTUPDATED. " $table</b>");
$result = $dbconn->Execute("INSERT INTO $table VALUES('21', '_NICKNAME', '-1', '255', '2', NULL)") or die ("<b>"._NOTUPDATED. " $table</b>");
echo "<br><font class=\"pn-sub\">$table "._UPDATED."</font>";

//database lexitron
$sqlFile = 'install/et_data.sql';
$fp=fopen($sqlFile,"r");
while(!feof($fp)){	// read	data in	the	webpage	until	the	end	of file
	$query	=	fgets($fp, 4096);
	$result = $dbconn->Execute($query) or die("Error insert data");
}
fclose($fp);
$table = $prefix."_et";
echo "<br><font class=\"pn-sub\">$table "._UPDATED."</font>\n";
?>
