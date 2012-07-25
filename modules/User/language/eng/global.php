<?php
define('_REGISTERDESC','
สำหรับผู้ที่ต้องการพัฒนาตนเองให้มีความรู้ในด้านต่างๆ โดยไม่ต้องเสียค่าใช้จ่ายใดๆ เพียงคุณกรอกรายละเอียดและข้อมูลต่างๆ ตามแบบฟอร์มด้านล่างนี้ โดยกำหนด nickname และ password ด้วยตัวท่านเอง และกรอกข้อมูลอื่นให้ครบถ้วน แล้วกดปุ่ม "CONTINUE" 
<HR>
ชื่อเล่น (nickname) เป็นชื่อที่ใช้แทนตัวคุณซึ่งจะปรากฏในห้องเรียน ข้อมูลส่วนตัวอื่นจะถูกเก็บเป็นความลับ นอกจากที่คุณต้องการจะเปิดเผยให้เพื่อนร่วมห้องทราบ กรุณาป้อนอีเมล์ หรือเบอร์โทรของคุณให้ถูกต้องเพื่อสะดวกในการติดต่อกลับ หรือในกรณีที่คุณลืมรหัสผ่าน (password) 
<P>
<U>หมายเหตุ</U> เครื่องหมาย * หลังข้อมูล แสดงว่าเป็นข้อมูลสำคัญ ต้องกรอกให้ครบ 
');
define('_LOGINERROR','Nickname and Password mismatch');
define('_ALERTNICKNAME','กรุณาป้อนชื่อเล่น (Nickname)');
define('_ALERTPASSWORD1','กรุณาป้อนรหัสผ่าน (Password)');
define('_ALERTPASSWORD2','กรุณาป้อนยืนยันรหัสผ่าน');
define('_ALERTPASSWORD0','รหัสผ่านทั้งสองไม่เหมือนกัน');
define('_ALERTID','กรุณาป้อนเลขประจำตัวด้วยค่ะ');
define('_ALERTEMAIL','ลืมป้อนอีเมล์ค่ะ');
define('_ALERTEMAILFORMAT','รูปแบบของ email ไม่ถูกต้องค่ะ');
define('_ALERTNOALPHA','รับเฉพาะตัวอักษรภาษาอังกฤษเท่านั้นค่ะ');
define('_ALERTNONUM','ป้อนเฉพาะตัวเลขค่ะ');
define('_NICKNAME','ชื่อเล่น');
define('_NICKNAMELIMIT','ใช้อักษรภาษาอังกฤษ');
define('_PASSWORD','รหัสผ่าน');
define('_PASSWORDLIMIT','ใช้ตัวอักษรภาษาอังกฤษ');
define('_CONFIRMPASSWORD','ยืนยันรหัสผ่าน');
define('_UNO','เลขประจำตัว');
define('_UNOHELP','จำนวนตัวเลข');
define('_EMAIL','E-mail');
define('_REGDATE','วันที่สมัคร');
define('_PHONE','โทรศัพท์');
define('_NEWS','รับ email ข่าวสาร และประกาศต่างๆ');
define('_NICKTAKEN','ชื่อเล่นนี้เคยใช้ลงทะเบียนแล้ว');
define('_UNOTAKEN','เลขประจำตัวนี้เคยใช้ลงทะเบียนแล้ว');
define('_EMAILTAKEN','email นี้เคยใช้ลงทะเบียนแล้ว');
define('_EPTYVALUE','กรุณาป้อนข้อมูลทุกตัว');
define('_ADDUSERDONE','&nbsp;<P><CENTER><B>ข้อมูลคุณได้บันทึกเข้าสู่ระบบแล้ว  ท่านจะได้รับ e-Mail ตอบรับการสมัครสมาชิก</B><P><TABLE width=400 cellpadding=5 cellspacing=1 bgcolor=#000000><TR><TD bgcolor=#FFFFFF class=nav1><B>สิ่งที่จะต้องทำต่อไป</B><P><OL><LI>ให้ login เข้าสู่ระบบ  โดยใส่ nickname และ password ที่ทำการสมัครเป็นสมาชิกไว้  แล้วกด &quot;login&quot;<P><LI>เมื่อเข้ามาในระบบได้แล้ว  จะมีเครื่องมือช่วยเรียนปรากฏทางด้านซ้ายมือ และแสดง Nickname ของท่าน<P><LI>เลือกหลักสูตรที่ต้องการเรียนจากกำหนดการลงทะเบียน หรือเลือกจากรายชื่อหลักสูตร  โดยจะลงทะเบียนกี่หลักสูตรก็ได้<P><LI>คลิก &quot;ลงทะเบียน&quot;  หลักสูตรที่ต้องการลงทะเบียนเรียน </OL></TD></TR></TABLE><BR><BR>สำหรับน้องใหม่กรุณาเข้าไปศึกษาวิธีใช้งานและรายละเอียดเพิ่มเติมได้ <A HREF=index.php?action=helpdesk>ที่นี่</A><P>พบ ปัญหา เกี่ยวกับการ ลงทะเบียนเรียน หรือการเข้าเรียน กรุณาติดต่อ <A HREF=index.php?action=contactus>ที่นี่</A></CENTER>');
define('_BACK','Back');
define('_NAME','ชื่อ - นามสกุล');
define('_HOMEPAGE','เว็บไชต์ส่วนตัว');
define('_AVATAR','ตัวการ์ตูนแทนตัวคุณ');
define('_ICQ','ICQ');
define('_AIM','AIM');
define('_YIM','YIM');
define('_MSNM','MSN');
define('_LOCATION','ที่อยู่');
define('_OCCUPATION','อาชีพ');
define('_INTERESTS','ความสนใจ');
define('_SIGNATURE','Signature');
define('_EXTRAINFO','ข้อมูลเพิ่มเติม');
define('_SCHOOLNAME','ชื่อโรงเรียน');
define('_REQUIRE','(*)');


define('_PERSONALINFO','ในหน้านี้คุณสามารถแก้ไขเพิ่มเติมข้อมูลส่วนของคุณ ข้อมูลจะแสดงให้เพื่อนร่วมห้องทราบซึ่งขึ้นอยู่กับความต้องการของคุณว่าจะเปิดเผยข้อมูลส่วนใด สัญญลักษณ์ (*) แสดงถึงข้อมูลที่สำคํญจะต้องกรอกให้ครบ สำหรับข้อมูลบางอย่างทางเราขอสงวนสิทธิ์ในการเปลี่ยนแปลงแก้ไขโดยไม่ต้องแจ้งให้ทราบ<HR size=1 color=#E1E1E1>');
define('_PERSONALIMAGE','<img src="modules/User/images/title_profile.jpg" border="0">');

// User define data type for dynamic properties
define('_TEST','ทดสอบ');


// Admin page 
define('_USERADMIN','User Administration');
define('_USERCONFIG','User Configuration');
define('_USEREDIT','Edit User');
define('_USERMODIFY','Modify User');
define('_USERSEARCH','Search Result');
define('_USERLIST','List User');
define('_USERADD','Add new user');
define('_SUBMITFIND','Submit');
define('_SUBMITADD','Add user');
define('_SAVECHANGES','Save Changes');
define('_USERREGCONFIG','User Configuration');
define('_USERDYCONFIG','Dynamic User Data');

//User Register Configuration
define('_ALLOWREG','Allow registration');
define('_SYES','Yes');
define('_SNO','No');
define('_UNIID','Require unique User ID ');
define('_UNINICKNAME','Require unique Nickname ');
define('_UNIEMAIL','Require unique email addresses ');
define('_LENID','User ID length ');
define('_LENMINNICK','Minimum nickname length');
define('_LENMAXNICK','Maximum nickname length');
define('_LENMINPASS','Minimum password length');
define('_LENMAXPASS','Maximum password length');
define('_DEFAULTGROUP','Default user group');

//Dynamic User data
define('_ACTIVE','Active');
define('_FLABEL','Field Label');
define('_WEIGHT','Weight');
define('_DTYPE','Data Type');
define('_LENGTH','Length');
define('_DELETE','Delete');
define('_FIELD_REQUIRED','Field Required');
define('_FIELD_DEACTIVATE','Deactivate');
define('_FIELD_ACTIVATE','Activate');
define('_ADDFIELD','Add Fields');
define('_FIELDLABEL','Field Label');
define('_ADDINSTRUCTIONS','Example: _MYINT -- You must create a define in language/<BR>(current language)/global.php for this variable');
define('_FIELDTYPE','Data Type');
define('_UDT_STRING','String');
define('_UDT_TEXT','Text');
define('_UDT_FLOAT','Float');
define('_UDT_INTEGER','Integer');
define('_FIELDLENGTH','Length');
define('_STRING_INSTRUCTIONS','STRINGS ONLY: Data Length Range (1,254)');

// Add new user
define('_USERFILE','ไฟล์ข้อมูลสมาชิก');
define('_TEXTDEL','ตัวอักษรขั้นข้อมูล');
define('_UPFILE','Add users');
define('_USERADD_DESC', 'การเพิ่มข้อมูลสมาชิกสามารถทำได้ 2 วิธีโดยวิธีแรกจะเพิ่มได้ที่ละคนซึ่งต้องกรอกข้อมูลที่ต้องการให้ครบ ส่วนวิธีที่สองนั้นจะนำข้อมูลมาจากไฟล์ภายนอกที่เตรียมไว้แล้ว');
define('_USERADD1','เพิ่มสมาชิกเองโดยป้อมข้อมูลลงในช่องด้านล่างนี้');
define('_USERADD2','เพิ่มสมาชิกจากไฟล์ที่ได้เตรียมมาแล้ว');
define('_SAVEDONE','Save done!');
define('_SAVEDONEMSG','รายชื่อสมาชิกที่เพิ่มสำเร็จ');
define('_ERRUPLOAD','พบข้อมูลผิดพลาดเนื่องจากมีข้อมูลซ่ำกับข้อมูลเดิม');

// Edit User
define('_SEARCH','ค้นหา');
define('_NOTFOUND','ไม่พบ');
define('_LISTUSER','รายชื่อสมาชิก');

define('_ALL','All');
define('_TOTALUSERS','จำนวนสมาชิกทั้งหมด');
define('_SHOWPAGE','แสดงจำนวน');

define('_SELECTGROUP','เลือกกลุ่มสมาชิก');
?>