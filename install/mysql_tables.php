<?
function dosql($table,$sql) {
   GLOBAL $dbconn;
   $result = $dbconn->Execute($sql);
   if ($result === false) {
      echo "<font class=\"pn-failed\">"._NOTMADE." ".$table."</font>";
      exit;
   }
   if (substr($sql,0,3)=="DRO") {
		echo "<br><font class=\"pn-sub\"> Clear table '".$table."'.</font>";
   }
   else {
		echo "<br><font class=\"pn-sub\"> Create table '".$table."'. </font>";
   }
}

$dbconn = dbconnect($dbhost, $dbuname, $dbpass, $dbname, $dbtype);



$table= $prefix.'_assignment';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_assignment (
  ln_eid int(10) unsigned NOT NULL,
  ln_lid int(10) unsigned NOT NULL,
  ln_file varchar(100) default NULL,
  ln_status char(1) default NULL,
  ln_score  int(3),
  ln_date_sent varchar(14),
  ln_date_check varchar(14),
  PRIMARY KEY  (ln_eid,ln_lid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_blocks';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_blocks (
  ln_bid int(11) unsigned NOT NULL auto_increment,
  ln_bkey varchar(255) NOT NULL default '',
  ln_title varchar(255) NOT NULL default '',
  ln_content text NOT NULL,
  ln_url varchar(254) NOT NULL default '',
  ln_mid int(11) unsigned NOT NULL default '0',
  ln_position char(1) NOT NULL default 'l',
  ln_weight decimal(10,1) NOT NULL default '0.0',
  ln_active tinyint(3) unsigned NOT NULL default '1',
  ln_refresh int(11) unsigned NOT NULL default '0',
  ln_last_update varchar(14) NOT NULL,
  ln_language varchar(30) NOT NULL default '',
  PRIMARY KEY  (ln_bid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);

$table= $prefix.'_calendar';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_calendar (
  ln_calid int(10) unsigned NOT NULL auto_increment,
  ln_type varchar(20) default '0',
  ln_title varchar(50) default NULL,
  ln_uid varchar(20) default NULL,
  ln_note text,
  ln_date date default NULL,
  ln_timestart time default NULL,
  ln_timeend time default NULL,
  ln_timetype char(1) NOT NULL default '0',
  PRIMARY KEY  (ln_calid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);

$table= $prefix.'_courses';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_courses (
  ln_cid int(10) unsigned NOT NULL auto_increment,
  ln_code varchar(10) default NULL,
  ln_sid int(10) unsigned default NULL,
  ln_title varchar(255) NOT NULL default '',
  ln_author varchar(100) default NULL,
  ln_description text,
  ln_prerequisite text,
  ln_purpose text,
  ln_credit float default NULL,
  ln_reference text,
  ln_active char(1) default NULL,
  ln_createon varchar(14),
  ln_sequence char(2),
  PRIMARY KEY  (ln_cid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci

";
dosql($table,$sql);


$table= $prefix.'_course_enrolls';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_course_enrolls (
  ln_eid int(10) unsigned NOT NULL auto_increment,
  ln_sid int(10) unsigned NOT NULL default '0',
  ln_gid bigint(20) unsigned NOT NULL default '0',
  ln_uid int(10) unsigned NOT NULL default '0',
  ln_options char(1) default NULL,
  ln_status char(1) default NULL,
  ln_mentor int(10) unsigned NOT NULL default '0',
  ln_start date ,
  PRIMARY KEY  (ln_sid,ln_uid),
  KEY eid (ln_eid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_course_submissions';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_course_submissions (
  ln_sid int(10) unsigned NOT NULL default '0',
  ln_cid int(11) NOT NULL default '0',
  ln_start date NOT NULL default '0000-00-00',
  ln_instructor int(10) unsigned NOT NULL default '0',
  ln_enroll int(1) NOT NULL default '1',
  ln_active char(1) NOT NULL default '1',
  ln_amountstd int(7) unsigned NOT NULL default '0',
  ln_limitstd int(7) unsigned NOT NULL default '0',
  PRIMARY KEY  (ln_cid,ln_start,ln_instructor)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_course_ta';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_course_ta (
  ln_sid int(10) unsigned NOT NULL default '0',
  ln_uid int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (ln_sid,ln_uid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_course_tracking';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_course_tracking (
  ln_eid int(10) unsigned default NULL,
  ln_lid int(10) unsigned default NULL,
  ln_page int(10) unsigned default NULL,
  ln_atime varchar(14) default NULL,
  ln_ip varchar(15) default NULL,
  ln_weight  int(10) unsigned default NULL,
  ln_outime varchar(14) default NULL
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_forums';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_forums (
  ln_fid int(10) unsigned NOT NULL auto_increment,
  ln_sid int(10) unsigned default NULL,
  ln_tid int(10) unsigned default NULL,
  ln_tix int(10) unsigned default NULL,
  ln_uid int(11) default NULL,
  ln_subject text,
  ln_post_text text,
  ln_icon varchar(50) default NULL,
  ln_ip varchar(15) default NULL,
  ln_post_time varchar(14) default NULL,
  ln_options char(1) default '1',
  PRIMARY KEY  (ln_fid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_group_membership';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_group_membership (
  ln_gid int(11) NOT NULL default '0',
  ln_uid int(11) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_group_perms';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_group_perms (
  ln_pid int(11) NOT NULL auto_increment,
  ln_gid int(11) NOT NULL default '0',
  ln_sequence float NOT NULL default '0',
  ln_realm smallint(4) NOT NULL default '0',
  ln_component varchar(255) NOT NULL default '',
  ln_instance varchar(255) NOT NULL default '',
  ln_level smallint(4) NOT NULL default '0',
  ln_bond int(2) NOT NULL default '0',
  PRIMARY KEY  (ln_pid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_groups';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_groups (
  ln_gid int(11) NOT NULL auto_increment,
  ln_name varchar(255) NOT NULL default '',
  ln_description varchar(255) default NULL,
  ln_type char(3),
  PRIMARY KEY  (ln_gid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_lessons';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_lessons (
  ln_lid int(10) unsigned NOT NULL auto_increment,
  ln_cid int(10) unsigned NOT NULL default '0',
  ln_title varchar(255) NOT NULL default '',
  ln_description text,
  ln_file varchar(100) default NULL,
  ln_duration float default NULL,
  ln_weight float NOT NULL default '0',
  ln_lid_parent INT(10)  UNSIGNED NOT NULL DEFAULT '0' ,
  ln_type int(3) unsigned NOT NULL default '0',
  ln_smt int(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (ln_lid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_module_vars';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_module_vars (
  ln_id int(11) unsigned NOT NULL auto_increment,
  ln_name varchar(64) NOT NULL default '',
  ln_value longtext,
  PRIMARY KEY  (ln_id),
  KEY pn_name (ln_name)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_modules';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_modules (
  ln_id int(11) unsigned NOT NULL auto_increment,
  ln_name varchar(64) NOT NULL default '',
  ln_type int(6) NOT NULL default '0',
  ln_displayname varchar(64) NOT NULL default '',
  ln_description varchar(255) NOT NULL default '',
  ln_directory varchar(64) NOT NULL default '',
  ln_version varchar(10) NOT NULL default '0',
  ln_admin_capable tinyint(1) NOT NULL default '0',
  ln_user_capable tinyint(1) NOT NULL default '0',
  ln_state tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (ln_id)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);



$table= $prefix.'_news';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_news (
  ln_idq int(11) unsigned NOT NULL auto_increment,
  ln_titleq varchar(50) NOT NULL default '',
  ln_detailq text NOT NULL,
  ln_nameq varchar(30) NOT NULL default '',
  ln_emailq varchar(30) NOT NULL default '',
  ln_dateq varchar(14) default NULL,
  PRIMARY KEY  (ln_idq)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);



$table= $prefix.'_news_ans';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_news_ans (
  ln_ida int(11) unsigned NOT NULL auto_increment,
  ln_idq int(11) unsigned NOT NULL,
  ln_detailans text NOT NULL,
  ln_nameans varchar(30) NOT NULL default '',
  ln_emailans varchar(30) NOT NULL default '',
  ln_dateans varchar(14) default NULL,
  PRIMARY KEY  (ln_ida)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);



$table= $prefix.'_note';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_note (
  ln_folder_id int(10) unsigned NOT NULL auto_increment,
  ln_uid int(10) unsigned NOT NULL default '0',
  ln_subject varchar(255) default NULL,
  ln_type char(1) default NULL,
  ln_note text,
  ln_notetime varchar(14) NOT NULL default '',
  ln_parent int(10) unsigned default NULL,
  PRIMARY KEY  (ln_folder_id)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);

$table= $prefix.'_privmsgs';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_privmsgs (
  ln_privmsgs_id int(10) unsigned NOT NULL default '0',
  ln_privmsgs_type tinyint(4) NOT NULL default '0',
  ln_privmsgs_priority tinyint(1) unsigned NOT NULL default '0',
  ln_privmsgs_subject varchar(255) NOT NULL default '0',
  ln_privmsgs_message text,
  ln_privmsgs_from_uid int(11) unsigned NOT NULL default '0',
  ln_privmsgs_to_uid int(10) unsigned NOT NULL default '0',
  ln_privmsgs_date int(11) unsigned NOT NULL default '0',
  ln_privmsgs_ip varchar(15) NOT NULL default '',
  ln_privmsgs_enable tinyint(1) NOT NULL default '1'
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_questionaire';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "

CREATE TABLE ".$prefix."_questionaire (
  ln_eid int(10) unsigned default NULL,
  ln_sid int(10) unsigned default NULL,
  ln_t1_1 int(1) unsigned default NULL,
  ln_t1_2 int(1) unsigned default NULL,
  ln_t1_3 int(1) unsigned default NULL,
  ln_t1_4 int(1) unsigned default NULL,
  ln_t1_5 int(1) unsigned default NULL,
  ln_t2_1 int(1) unsigned default NULL,
  ln_t2_2 int(1) unsigned default NULL,
  ln_t3_1 int(1) unsigned default NULL,
  ln_t3_2 int(1) unsigned default NULL,
  ln_t3_3 int(1) unsigned default NULL,
  ln_t4 text,
  PRIMARY KEY  (ln_eid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);




$table= $prefix.'_quiz';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "

CREATE TABLE ".$prefix."_quiz (
  ln_qid int(10) unsigned NOT NULL auto_increment,
  ln_cid int(10) unsigned default NULL,
  ln_name varchar(255),
  ln_intro text,
  ln_attempts  int(6),
  ln_feedback int(4),
  ln_correctanswers int(4),
  ln_grademethod int(4),
  ln_shufflequestions int(4),
  ln_testtime int(10),
  ln_grade int(10),
  ln_assessment int(3),
  ln_correctscore int(3),
  ln_wrongscore int(3),
  ln_noans int(3),
  ln_difficulty int(1),
  ln_difficultypriority int(1),
  PRIMARY KEY  (ln_qid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_quiz_answer';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_quiz_answer (
  ln_qaid int(10) unsigned NOT NULL auto_increment,
  ln_eid int(10) unsigned NOT NULL,
  ln_mcid int(10) unsigned NOT NULL,
  ln_useranswer tinyint(2) unsigned NOT NULL,
  ln_attempts int(10) unsigned NOT NULL,
  ln_qid int(10) unsigned NOT NULL,
  ln_lid int(10) unsigned NOT NULL,
  KEY qaid (ln_qaid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);

//by Orrawin 15/10/09
$table= $prefix.'_quiz_test';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_quiz_test (
  ln_qid int(10) unsigned NOT NULL,
  ln_mcid int(10) unsigned NOT NULL,  
  ln_weight int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_quiz_choice';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_quiz_choice (
  ln_chid int(10) unsigned NOT NULL auto_increment,
  ln_mcid int(10) unsigned NOT NULL default '0',
  ln_answer text NOT NULL,
  ln_feedback text NOT NULL,
  ln_weight int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (ln_chid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_quiz_multichoice';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_quiz_multichoice (
  ln_mcid int(10) unsigned NOT NULL auto_increment,
  ln_cid int(10) unsigned,
  ln_uid int(10) unsigned NOT NULL,
  ln_question text NOT NULL,
  ln_answer text NOT NULL,
  ln_difficulty int(5) unsigned NOT NULL default '0',
  ln_type int(1) NOT NULL default '1',
  ln_keyword varchar(100) NOT NULL default '',
  ln_share int(1) unsigned NOT NULL default '0',
  ln_guid varchar(100) NOT NULL default '',
  PRIMARY KEY  (ln_mcid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_schools';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_schools (
  ln_sid int(10) unsigned NOT NULL auto_increment,
  ln_code char(2) NOT NULL default '',
  ln_name varchar(50) default NULL,
  ln_description text,
  ln_logo varchar(255) default NULL,
  PRIMARY KEY  (ln_sid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_scores';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_scores (
  ln_eid int(10) unsigned NOT NULL default '0',
  ln_lid int(10) unsigned NOT NULL default '0',
  ln_score float default NULL,
  ln_quiz_time timestamp NOT NULL,
  ln_attempts int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_session_info';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_session_info (
  ln_sessid varchar(32) NOT NULL default '',
  ln_ipaddr varchar(20) NOT NULL default '',
  ln_firstused varchar(14) NOT NULL default '0',
  ln_lastused varchar(14) NOT NULL default '0',
  ln_uid int(11) NOT NULL default '0',
  ln_vars blob,
  PRIMARY KEY  (ln_sessid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);

/*$table= $prefix.'_event_user';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_event_user (
  ln_uid varchar(10) NOT NULL default '',
  ln_ipaddr varchar(20) NOT NULL default '',
  ln_ippro varchar(20) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);*/

$table= $prefix.'_user_data';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_user_data (
  ln_uda_id int(11) NOT NULL auto_increment,
  ln_uda_propid int(11) NOT NULL default '0',
  ln_uda_uid int(11) NOT NULL default '0',
  ln_uda_value mediumblob NOT NULL,
  PRIMARY KEY  (ln_uda_id)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_user_log';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_user_log (
  ln_uid int(11) default NULL,
  ln_atime varchar(14) default NULL,
  ln_event varchar(255) default NULL,
  ln_ip varchar(15) default NULL,
  ln_cid int(14) default NULL
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_user_perms';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_user_perms (
  ln_pid int(11) NOT NULL auto_increment,
  ln_uid int(11) NOT NULL default '0',
  ln_sequence float NOT NULL default '0',
  ln_realm int(4) NOT NULL default '0',
  ln_component varchar(255) NOT NULL default '',
  ln_instance varchar(255) NOT NULL default '',
  ln_level int(4) NOT NULL default '0',
  ln_bond int(2) NOT NULL default '0',
  PRIMARY KEY  (ln_pid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_user_property';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_user_property (
  ln_prop_id int(11) NOT NULL auto_increment,
  ln_prop_label varchar(255) NOT NULL default '',
  ln_prop_dtype int(11) NOT NULL default '0',
  ln_prop_length int(11) NOT NULL default '255',
  ln_prop_weight decimal(10,1) NOT NULL default '0.0',
  ln_prop_validation varchar(255) default NULL,
  PRIMARY KEY  (ln_prop_id),
  UNIQUE KEY pn_prop_label (ln_prop_label)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_users';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_users (
  ln_uid int(11) NOT NULL auto_increment,
  ln_name varchar(60) default NULL,
  ln_uname varchar(25) default NULL,
  ln_email varchar(60) default NULL,
  ln_regdate varchar(20) default NULL,
  ln_pass varchar(40) default NULL,
  ln_phone varchar(50) default NULL,
  ln_uno varchar(25) default NULL,
  ln_news char(1) default NULL,
  ln_theme varchar(25) default NULL,
  ln_active char(1) default '1',
  ln_show int(11) NOT NULL default '0',
  PRIMARY KEY  (ln_uid)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);


$table= $prefix.'_rss';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_rss (
  ln_id int(11) unsigned NOT NULL auto_increment,
  ln_title varchar(50) NOT NULL default '',
  ln_xml varchar(100) NOT NULL default '',
  ln_display text NOT NULL,
  ln_name varchar(30) NOT NULL default '',
  ln_email varchar(30) NOT NULL default '',
  ln_date varchar(14) default NULL,
  PRIMARY KEY  (ln_id)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);

//==JoeJoy Chat Room==
$table= $prefix.'_chat_data';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_chat_data (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '-2',
  `uname` varchar(25) NOT NULL,
  `text` text NOT NULL,
  `wuid` varchar(25) NOT NULL,
  `cid` int(10) NOT NULL,
  `time` int(10) NOT NULL,
  `ip` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
";
dosql($table,$sql);

$table= $prefix.'_chat_useronline';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_chat_useronline (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `uname` varchar(25) NOT NULL,
  `cid` int(10) NOT NULL,
  `lastseen` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);

$table= $prefix.'_chat_config';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_chat_config (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
";
dosql($table,$sql);

$table= $prefix.'_chat_active';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_chat_active (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `cid` int(10) NOT NULL,
  `ln_allow_chat` tinyint(1) NOT NULL,
  `ln_allow_member` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
";
dosql($table,$sql);

//Lexitron Database ET
$table= $prefix.'_et';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_et (
  `ln_id` int(15) NOT NULL auto_increment,
  `ln_EENTRY` varchar(255) NOT NULL default '',
  `ln_TENTRY` varchar(255) NOT NULL default '',
  `ln_ESEARCH` varchar(255) NOT NULL default '',
  `ln_ECAT` varchar(255) default NULL,
  `ln_ETHAI` varchar(255) default NULL,
  `ln_ESYN` varchar(255) default NULL,
  `ln_EANT` varchar(255) default NULL,
  `ln_ESAMPLE` varchar(255) default NULL,
  `ln_freq_use` int(20) NOT NULL default '0',
  `ln_vocab_by` varchar(255) NOT NULL default 'lexitron',
  `ln_owner` varchar(30) NOT NULL default 'Lexitron',
  PRIMARY KEY  (`ln_id`),
  KEY `EENTRY` (`ln_EENTRY`),
  KEY `ESEARCH` (`ln_ESEARCH`)
) ENGINE=MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci
";
dosql($table,$sql);

//Mining Module
$table= $prefix.'_xestat';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE IF NOT EXISTS `".$prefix."_xestat` (
  `ip` int(11) NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `uid` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `refUrl` varchar(255) NOT NULL,
  `dura` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY  (`ip`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
dosql($table,$sql);

//Bookmarks
$table= $prefix.'_bookmarks';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_bookmarks (
  ln_bid int(10) unsigned NOT NULL auto_increment,
  ln_uid int(10) unsigned NOT NULL default '0',
  ln_sid int(10) unsigned NOT NULL default '0',
  ln_cid int(10) unsigned NOT NULL default '0',
  ln_lid int(10) unsigned NOT NULL default '0',
  ln_page int(10) unsigned NOT NULL default '0',
  ln_date date default NULL,
  PRIMARY KEY  (ln_bid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
";
dosql($table,$sql);

//File Download Counter
$table= $prefix.'_counters';
$sql ="DROP TABLE IF EXISTS $table";
dosql($table,$sql);

$sql = "
CREATE TABLE ".$prefix."_counters (
  ln_id int(10) unsigned NOT NULL AUTO_INCREMENT,
  ln_file varchar(255) NOT NULL,
  ln_server_addr varchar(255) NOT NULL,
  ln_remote_addr varchar(255) NOT NULL,
  ln_date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (ln_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";
dosql($table,$sql);
?>