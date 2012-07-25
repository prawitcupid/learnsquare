<?
function dosql($table,$sql) {
   GLOBAL $dbconn;

   $result = $dbconn->Execute($sql);
   if ($result === false) {
      echo "<font class=\"pn-failed\">"._NOTMADE." ".$table."</font>";
      exit;
   }
   if (substr($sql,0,3)=="DRO") {
		echo "<br><font class=\"pn-sub\"> Drop table '".$table."'.</font>";
   }
   else {
		echo "<br><font class=\"pn-sub\"> Create table '".$table."'. </font>";
   }
}

$dbconn = dbconnect($dbhost, $dbuname, $dbpass, $dbname, $dbtype);


//assignments table
$table= $prefix.'_assignments';
$sql ="DROP TABLE $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_assignments (
  ln_aid NUMBER(10,0),
  ln_lid NUMBER(10,0),
  ln_title VARCHAR2(255),
  ln_question CLOB,
  ln_weight NUMBER(4,1)
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_assignments ADD PRIMARY KEY (ln_aid)";
dosql($table,$sql);


// blocks table
$table= $prefix.'_blocks';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_blocks (
  ln_bid NUMBER(10,0),
  ln_bkey VARCHAR2(255),
  ln_title VARCHAR2(255),
  ln_content CLOB,
  ln_url VARCHAR2(255),
  ln_mid NUMBER(10,0),
  ln_position CHAR(1),
  ln_weight NUMBER(4,1),
  ln_active NUMBER(3,0),
  ln_refresh NUMBER(10,0),
  ln_last_update VARCHAR2(14),
  ln_language VARCHAR2(30)
) 
";
dosql($table,$sql);
$sql="ALTER TABLE ln_blocks ADD PRIMARY KEY (ln_bid)";
dosql($table,$sql);


// course_enrolls
$table= $prefix.'_course_enrolls';
$sql ="DROP TABLE  $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_course_enrolls (
  ln_eid NUMBER(10,0),
  ln_sid NUMBER(10,0) NOT NULL,
  ln_gid NUMBER(10,0) NOT NULL,
  ln_uid NUMBER(10,0) NOT NULL,
  ln_options CHAR(1),
  ln_status CHAR(1) 
) 
";
dosql($table,$sql);
$sql="ALTER TABLE ln_course_enrolls ADD PRIMARY KEY (ln_sid, ln_uid)";
dosql($table,$sql);


// course_submissions table
$table= $prefix.'_course_submissions';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_course_submissions (
  ln_sid NUMBER(10,0) NOT NULL,
  ln_cid NUMBER(10,0) NOT NULL,
  ln_start DATE NOT NULL,
  ln_duration CHAR(3),
  ln_study CHAR(1),
  ln_student CHAR(1),
  ln_pretest CHAR(1),
  ln_pretest_no NUMBER(10,0),
  ln_posttest CHAR(1),
  ln_posttest_no NUMBER(10,0),
  ln_instructor NUMBER(10,0) NOT NULL,
  ln_lesson_active CHAR(32) 
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_course_submissions ADD PRIMARY KEY (ln_cid, ln_start, ln_instructor)";
dosql($table,$sql);


// course_ta table
$table= $prefix.'_course_ta';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_course_ta (
  ln_sid NUMBER(10,0) NOT NULL,
  ln_uid NUMBER(10,0)  NOT NULL
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_course_ta ADD PRIMARY KEY (ln_sid, ln_uid)";
dosql($table,$sql);

$table= $prefix.'_course_tracking';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_course_tracking (
  ln_eid NUMBER(10,0) NOT NULL,
  ln_lid NUMBER(10,0) NOT NULL,
  ln_weight  NUMBER(10,0) NOT NULL,
  ln_page NUMBER(10,0)  NOT NULL,
  ln_atime VARCHAR2(100) NOT NULL,
  ln_outime VARCHAR2(100) NOT NULL,
  ln_ip VARCHAR2(100) NOT NULL
) TYPE=MyISAM
";
dosql($table,$sql);

// courses table
$table= $prefix.'_courses';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_courses (
  ln_cid NUMBER(10,0),
  ln_code VARCHAR2(10),
  ln_sid NUMBER(10,0),
  ln_title VARCHAR2(255) NOT NULL,
  ln_author VARCHAR2(100),
  ln_description CLOB,
  ln_prerequisite CLOB,
  ln_purpose CLOB,
  ln_credit NUMBER(10,1),
  ln_reference CLOB,
  ln_active CHAR(1),
  ln_instructor NUMBER(10,0)  NOT NULL
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_courses ADD PRIMARY KEY (ln_cid)";
dosql($table,$sql);


// forums table
$table= $prefix.'_forums';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_forums (
  ln_fid NUMBER(10,0),
  ln_sid NUMBER(10,0),
  ln_tid NUMBER(10,0),
  ln_tix NUMBER(10,0),
  ln_uid NUMBER(10,0),
  ln_subject CLOB,
  ln_post_text CLOB,
  ln_icon VARCHAR2(50),
  ln_ip VARCHAR2(15),
  ln_post_time VARCHAR2(14),
  ln_options CHAR(1) 
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_forums ADD PRIMARY KEY (ln_fid)";
dosql($table,$sql);


// group_membership table
$table= $prefix.'_group_membership';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_group_membership (
  ln_gid NUMBER(11,0) NOT NULL,
  ln_uid NUMBER(11,0)  NOT NULL
)
";
dosql($table,$sql);


// group_perms table
$table= $prefix.'_group_perms';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_group_perms (
  ln_pid NUMBER(10,0),
  ln_gid NUMBER(10,0) NOT NULL,
  ln_sequence NUMBER(4,1) NOT NULL,
  ln_realm NUMBER(4,0) NOT NULL,
  ln_component VARCHAR2(255) NOT NULL,
  ln_instance VARCHAR2(255) NOT NULL,
  ln_level NUMBER(4,0) NOT NULL,
  ln_bond NUMBER(2,0)  NOT NULL
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_group_perms ADD PRIMARY KEY (ln_pid)";
dosql($table,$sql);


// groups table
$table= $prefix.'_groups';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_groups (
  ln_gid NUMBER(10,0),
  ln_name VARCHAR2(255) NOT NULL,
  ln_description VARCHAR2(255) 
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_groups ADD PRIMARY KEY (ln_gid)";
dosql($table,$sql);


// lesson table
$table= $prefix.'_lessons';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_lessons (
  ln_lid NUMBER(10,0),
  ln_cid NUMBER(10,0) NOT NULL,
  ln_title VARCHAR2(255) NOT NULL,
  ln_description CLOB,
  ln_file VARCHAR2(100),
  ln_duration NUMBER(12,1),
  ln_weight NUMBER(4,1)  NOT NULL,
  ln_lid_parent NUMBER(10,0) NOT NULL
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_lessons ADD PRIMARY KEY (ln_lid)";
dosql($table,$sql);


// module_vars table
$table= $prefix.'_module_vars';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_module_vars (
  ln_id NUMBER(10,0),
  ln_name VARCHAR2(64) NOT NULL,
  ln_value CLOB 
) 
";
dosql($table,$sql);
$sql="ALTER TABLE ln_module_vars ADD PRIMARY KEY (ln_id)";
dosql($table,$sql);


// modules table
$table= $prefix.'_modules';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_modules (
  ln_id NUMBER(11,0),
  ln_name VARCHAR2(64) NOT NULL,
  ln_type NUMBER(6,0) NOT NULL,
  ln_displayname VARCHAR2(64) NOT NULL,
  ln_description VARCHAR2(255) NOT NULL,
  ln_regid NUMBER(11,0) NOT NULL,
  ln_directory VARCHAR2(64) NOT NULL,
  ln_version VARCHAR2(10) NOT NULL,
  ln_admin_capable NUMBER(1,0) NOT NULL,
  ln_user_capable NUMBER(1,0) NOT NULL,
  ln_state NUMBER(1,0)  NOT NULL
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_modules ADD PRIMARY KEY (ln_id)";
dosql($table,$sql);


// privmsg table
$table= $prefix.'_privmsgs';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_privmsgs (
  ln_privmsgs_id NUMBER(10,0) NOT NULL,
  ln_privmsgs_type NUMBER(4,0) NOT NULL,
  ln_privmsgs_priority NUMBER(1,0) NOT NULL,
  ln_privmsgs_subject VARCHAR2(255) NOT NULL,
  ln_privmsgs_message CLOB,
  ln_privmsgs_from_uid NUMBER(11,0) NOT NULL,
  ln_privmsgs_to_uid NUMBER(10,0) NOT NULL,
  ln_privmsgs_date NUMBER(11,0) NOT NULL,
  ln_privmsgs_ip VARCHAR2(15) NOT NULL,
  ln_privmsgs_enable NUMBER(1,0)  NOT NULL
)
";
dosql($table,$sql);


// quiz table
$table= $prefix.'_quiz';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_quiz (
  ln_qid NUMBER(10,0),
  ln_lid NUMBER(10,0),
  ln_description CLOB,
  ln_type CHAR(2)  NOT NULL
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_quiz ADD PRIMARY KEY (ln_qid)";
dosql($table,$sql);


// quiz_choice table
$table= $prefix.'_quiz_choice';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_quiz_choice (
  ln_chid NUMBER(10,0),
  ln_quid NUMBER(10,0) NOT NULL,
  ln_choice CLOB NOT NULL,
  ln_description CLOB NOT NULL,
  ln_weight NUMBER(4,1)  NOT NULL
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_quiz_choice ADD PRIMARY KEY (ln_chid)";
dosql($table,$sql);


// quiz_question table
$table= $prefix.'_quiz_question';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_quiz_question (
  ln_quid NUMBER(10,0),
  ln_qid NUMBER(10,0) NOT NULL,
  ln_question CLOB NOT NULL,
  ln_answer CLOB NOT NULL,
  ln_score NUMBER(10,2) NOT NULL,
  ln_weight NUMBER(4,1)  NOT NULL
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_quiz_question ADD PRIMARY KEY (ln_quid)";
dosql($table,$sql);


// schools table
$table= $prefix.'_schools';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_schools (
  ln_sid NUMBER(10,0),
  ln_code CHAR(2) NOT NULL,
  ln_name VARCHAR2(50),
  ln_description CLOB,
  ln_logo VARCHAR2(255) 
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_schools ADD PRIMARY KEY (ln_sid)";
dosql($table,$sql);


// scores table
$table= $prefix.'_scores';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_scores (
  ln_eid NUMBER(10,0) NOT NULL,
  ln_qid NUMBER(10,0) NOT NULL,
  ln_score NUMBER(3,2) default NULL,
  ln_quiz_time VARCHAR2(14) NOT NULL
)
";
dosql($table,$sql);


// session_info table
$table= $prefix.'_session_info';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_session_info (
  ln_sessid VARCHAR2(32) NOT NULL,
  ln_ipaddr VARCHAR2(20) NOT NULL,
  ln_firstused VARCHAR2(14) NOT NULL,
  ln_lastused VARCHAR2(14) NOT NULL,
  ln_uid NUMBER(10,0) NOT NULL,
  ln_vars BLOB 
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_session_info ADD PRIMARY KEY (ln_sessid)";
dosql($table,$sql);

// _event_user table
/*$table= $prefix.'_event_user';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_event_user (
  ln_uid VARCHAR2(10) NOT NULL,
  ln_ipaddr VARCHAR2(20) NOT NULL,
  ln_ippro varchar(20) NOT NULL NULL '')
";
dosql($table,$sql);
$sql="ALTER TABLE ln_event_user ";
dosql($table,$sql);
*/
// user_data table
$table= $prefix.'_user_data';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_user_data (
  ln_uda_id NUMBER(10,0) NOT NULL,
  ln_uda_propid NUMBER(10,0) NOT NULL,
  ln_uda_uid NUMBER(10,0) NOT NULL,
  ln_uda_value BLOB  NOT NULL
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_user_data ADD PRIMARY KEY (ln_uda_id)";
dosql($table,$sql);


// user_log table
$table= $prefix.'_user_log';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_user_log (
  ln_log_id NUMBER(10,0) NOT NULL,
  ln_cid NUMBER(10,0) NOT  NULL,
  ln_uid NUMBER(10,0),
  ln_atime VARCHAR2(14),
  ln_event VARCHAR2(255),
  ln_ip VARCHAR2(15) 
)
";
dosql($table,$sql);


// user_perms table
$table= $prefix.'_user_perms';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_user_perms (
  ln_pid NUMBER(10,0),
  ln_uid NUMBER(10,0) NOT NULL,
  ln_sequence NUMBER(4,1) NOT NULL,
  ln_realm NUMBER(4,0) NOT NULL,
  ln_component VARCHAR2(255) NOT NULL,
  ln_instance VARCHAR2(255) NOT NULL,
  ln_level NUMBER(4,0) NOT NULL,
  ln_bond NUMBER(2,0)  NOT NULL
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_user_perms ADD PRIMARY KEY (ln_pid)";
dosql($table,$sql);


// user_property table
$table= $prefix.'_user_property';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_user_property (
  ln_prop_id NUMBER(10,0),
  ln_prop_label VARCHAR2(255) NOT NULL,
  ln_prop_dtype NUMBER(10,0) NOT NULL,
  ln_prop_length NUMBER(10,0) NOT NULL,
  ln_prop_weight NUMBER(10,0) NOT NULL,
  ln_prop_validation VARCHAR2(255) 
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_user_property ADD PRIMARY KEY (ln_prop_id)";
dosql($table,$sql);


// users table
$table= $prefix.'_users';
$sql ="DROP TABLE  $table";
dosql($table,$sql);
$sql = "
CREATE TABLE ".$prefix."_users (
 ln_uid NUMBER(10,0),
  ln_name VARCHAR2(60),
  ln_uname VARCHAR2(25),
  ln_email VARCHAR2(60),
  ln_regdate VARCHAR2(20),
  ln_pass VARCHAR2(40),
  ln_phone VARCHAR2(50),
  ln_uno VARCHAR2(25),
  ln_news CHAR(1),
  ln_theme VARCHAR2(25),
  ln_active CHAR(1) 
)
";
dosql($table,$sql);
$sql="ALTER TABLE ln_users ADD PRIMARY KEY (ln_uid)";
dosql($table,$sql);
?>