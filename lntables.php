<?php

if (preg_match("/lntables.php/i",$_SERVER['PHP_SELF'])) {
    Header("Location: index.php");
    die();
}



$prefix = $config['prefix'];

$lntable = array();


$assignment = $prefix . '_assignment';
$lntable['assignment'] = $assignment;
$lntable['assignment_column'] = array ('eid'        => $assignment . '.ln_eid',
                                   'lid'       => $assignment . '.ln_lid',
                                   'file'     => $assignment . '.ln_file',
                                   'status'         => $assignment . '.ln_status',
								   'score'      => $assignment . '.ln_score',
								   'date_sent'      => $assignment . '.ln_date_sent',
								   'date_check'      => $assignment . '.ln_date_check');



$blocks = $prefix . '_blocks';
$lntable['blocks'] = $blocks;
$lntable['blocks_column'] = array ('bid'         => $blocks . '.ln_bid',
                                   'bkey'        => $blocks . '.ln_bkey',
                                   'title'       => $blocks . '.ln_title',
                                   'content'     => $blocks . '.ln_content',
                                   'mid'         => $blocks . '.ln_mid',
                                   'position'    => $blocks . '.ln_position',
                                   'weight'      => $blocks . '.ln_weight',
                                   'active'      => $blocks . '.ln_active',
								   'refresh'      => $blocks . '.ln_refresh',
								   'last_update'      => $blocks . '.ln_last_update',
								   'language'      => $blocks . '.ln_language');

$courses = $prefix . '_courses';
$lntable['courses'] = $courses;
$lntable['courses_column'] = array ('cid'         => $courses . '.ln_cid',
                                   'code'        => $courses . '.ln_code',
                                   'sid'       => $courses . '.ln_sid',
                                   'title'     => $courses . '.ln_title',
                                   'author'         => $courses . '.ln_author',
                                   'description'         => $courses . '.ln_description',
                                   'prerequisite'    => $courses . '.ln_prerequisite',
                                   'purpose'      => $courses . '.ln_purpose',
                                   'credit'     => $courses . '.ln_credit',
                                   'reference' => $courses . '.ln_reference',
                                   'active'   => $courses . '.ln_active',
								   'createon'   => $courses . '.ln_createon',
								   'sequence'   => $courses . '.ln_sequence'
								   );

$course_enrolls = $prefix . '_course_enrolls';
$lntable['course_enrolls'] = $course_enrolls;
$lntable['course_enrolls_column'] = array ('eid'         => $course_enrolls . '.ln_eid',
                                   'sid'        => $course_enrolls . '.ln_sid',
                                   'gid'       => $course_enrolls . '.ln_gid',
                                   'uid'     => $course_enrolls . '.ln_uid',
                                   'options'         => $course_enrolls . '.ln_options',
                                   'status'         => $course_enrolls . '.ln_status',
								   'mentor'         => $course_enrolls . '.ln_mentor',
								   'start'         => $course_enrolls . '.ln_start'
								   );

$course_submissions = $prefix . '_course_submissions';
$lntable['course_submissions'] = $course_submissions;
$lntable['course_submissions_column'] = array ('sid'         => $course_submissions . '.ln_sid',
                                   'cid'        => $course_submissions . '.ln_cid',
                                   'start'       => $course_submissions . '.ln_start',
                                   'instructor'         => $course_submissions . '.ln_instructor',
                                   'enroll'         => $course_submissions . '.ln_enroll',
                                   'active'         => $course_submissions . '.ln_active',
                                   'amountstd' => $course_submissions . '.ln_amountstd',
                                   'limitstd' => $course_submissions . '.ln_limitstd');

$course_ta = $prefix . '_course_ta';
$lntable['course_ta'] = $course_ta;
$lntable['course_ta_column'] = array ('sid'         => $course_ta . '.ln_sid',
                                   'uid'        => $course_ta . '.ln_uid');

$course_tracking = $prefix . '_course_tracking';
$lntable['course_tracking'] = $course_tracking;
$lntable['course_tracking_column'] = array ('eid'        => $course_tracking . '.ln_eid',
								   'lid'			=> $course_tracking.'.ln_lid',
								   'weight' => $course_tracking.'.ln_weight',
								   'page'		=> $course_tracking.'.ln_page',
								   'atime'	=> $course_tracking.'.ln_atime',
								   'outime'	=> $course_tracking.'.ln_outime',
								   'ip'			=> $course_tracking.'.ln_ip');

$group_membership = $prefix . '_group_membership';
$lntable['group_membership'] = $group_membership;
$lntable['group_membership_column'] = array ('gid' => $group_membership . '.ln_gid',
                                             'uid' => $group_membership . '.ln_uid');

$group_perms = $prefix . '_group_perms';
$lntable['group_perms'] = $group_perms;
$lntable['group_perms_column'] = array ('pid'       => $group_perms . '.ln_pid',
                                        'gid'       => $group_perms . '.ln_gid',
                                        'sequence'  => $group_perms . '.ln_sequence',
                                        'realm'     => $group_perms . '.ln_realm',
                                        'component' => $group_perms . '.ln_component',
                                        'instance'  => $group_perms . '.ln_instance',
                                        'level'     => $group_perms . '.ln_level',
                                        'bond'      => $group_perms . '.ln_bond');

$groups = $prefix . '_groups';
$lntable['groups'] = $groups;
$lntable['groups_column'] = array ('gid'  => $groups . '.ln_gid',
                                   'name' => $groups . '.ln_name',
								   'description' => $groups . '.ln_description',
								   'type' => $groups . '.ln_type');

$lessons = $prefix . '_lessons';
$lntable['lessons'] = $lessons;
$lntable['lessons_column'] = array ('lid'  => $lessons . '.ln_lid',
                                   'cid' => $lessons . '.ln_cid',
                                   'title' => $lessons . '.ln_title',
                                   'description' => $lessons . '.ln_description',
                                   'file' => $lessons . '.ln_file',
                                   'duration' => $lessons . '.ln_duration',
								   'weight' => $lessons . '.ln_weight',
								   'lid_parent' => $lessons . '.ln_lid_parent',
								   'type' => $lessons . '.ln_type',
								   'smt' => $lessons . '.ln_smt');


$module_vars = $prefix . '_module_vars';
$lntable['module_vars'] = $module_vars;
$lntable['module_vars_column'] = array ('id'      => $module_vars . '.ln_id',
                                        'name'    => $module_vars . '.ln_name',
                                        'value'   => $module_vars . '.ln_value');

$modules = $prefix . '_modules';
$lntable['modules'] = $modules;
$lntable['modules_column'] = array ('id'            => $modules . '.ln_id',
                                    'name'          => $modules . '.ln_name',
                                    'type'          => $modules . '.ln_type',
                                    'displayname'   => $modules . '.ln_displayname',
                                    'description'   => $modules . '.ln_description',
                                    'directory'     => $modules . '.ln_directory',
                                    'version'       => $modules . '.ln_version',
                                    'admin_capable' => $modules . '.ln_admin_capable',
                                    'user_capable'  => $modules . '.ln_user_capable',
                                    'state'         => $modules . '.ln_state');

$news = $prefix . '_news';
$lntable['news'] = $news;
$lntable['news_column'] = array ('idq'         => $news . '.ln_idq',
                                   'titleq'        => $news . '.ln_titleq',
                                   'detailq'       => $news . '.ln_detailq',
                                   'nameq'         => $news . '.ln_nameq',
                                   'emailq'         => $news . '.ln_emailq',
                                   'dateq'         => $news . '.ln_dateq');


$news_ans = $prefix . '_news_ans';
$lntable['news_ans'] = $news_ans;
$lntable['news_ans_column'] = array ('ida'         => $news_ans . '.ln_ida',
								   'idq'		=>	$news_ans. '.ln_idq',
                                   'detailans'       => $news_ans . '.ln_detailans',
                                   'nameans'         => $news_ans . '.ln_nameans',
                                   'emailans'         => $news_ans . '.ln_emailans',
                                   'dateans'         => $news_ans . '.ln_dateans');




$privmsgs = $prefix . '_privmsgs';
$lntable['privmsgs'] = $privmsgs;
$lntable['privmsgs_column'] = array ('id'  => $privmsgs . '.ln_privmsgs_id',
										'type'  => $privmsgs . '.ln_privmsgs_type',
										'priority'  => $privmsgs . '.ln_privmsgs_priority',
										'subject'  => $privmsgs . '.ln_privmsgs_subject',
										'message'  => $privmsgs . '.ln_privmsgs_message',
										'from_uid'  => $privmsgs . '.ln_privmsgs_from_uid',
										'to_uid'  => $privmsgs . '.ln_privmsgs_to_uid',
										'date'  => $privmsgs . '.ln_privmsgs_date',
										'ip'  => $privmsgs . '.ln_privmsgs_ip',
										'enable'  => $privmsgs . '.ln_privmsgs_enable'
										);


$questionaire = $prefix . '_questionaire';
$lntable['questionaire'] = $questionaire;
$lntable['questionaire_column'] = array ('eid'         => $questionaire . '.ln_eid',
                                      'sid'        => $questionaire . '.ln_sid',
                                   't1_1'        => $questionaire . '.ln_t1_1',
                                   't1_2'       => $questionaire . '.ln_t1_2',
                                   't1_3'     => $questionaire . '.ln_t1_3',
                                   't1_4'         => $questionaire . '.ln_t1_4',
                                   't1_5'         => $questionaire . '.ln_t1_5',
	              't2_1'         => $questionaire . '.ln_t2_1',
	              't2_2'         => $questionaire . '.ln_t2_2',
	              't3_1'         => $questionaire . '.ln_t3_1',
	              't3_2'         => $questionaire . '.ln_t3_2',
	              't3_3'         => $questionaire . '.ln_t3_3',
	              't4'         => $questionaire . '.ln_t4'
			   );



$quiz = $prefix . '_quiz';
$lntable['quiz'] = $quiz;
$lntable['quiz_column'] = array ('qid'         => $quiz . '.ln_qid',
                                   'cid'        => $quiz . '.ln_cid',
                                   'name'        => $quiz . '.ln_name',
                                   'intro'        => $quiz . '.ln_intro',
                                   'attempts'        => $quiz . '.ln_attempts',
                                   'feedback'        => $quiz . '.ln_feedback',
                                   'correctanswers'        => $quiz . '.ln_correctanswers',
                                   'grademethod'        => $quiz . '.ln_grademethod',
                                   'shufflequestions'        => $quiz . '.ln_shufflequestions',
                                   'testtime'        => $quiz . '.ln_testtime',
                                   'grade'    => $quiz . '.ln_grade',
                                   'assessment'    => $quiz . '.ln_assessment',
									'correctscore'    => $quiz . '.ln_correctscore',
								   'wrongscore'    => $quiz . '.ln_wrongscore',
								   'noans'    => $quiz . '.ln_noans',
								   'difficulty'    => $quiz . '.ln_difficulty',
								   'difficultypriority'    => $quiz . '.ln_difficultypriority'
								   );


$quiz_answer = $prefix . '_quiz_answer';
$lntable['quiz_answer'] = $quiz_answer;
$lntable['quiz_answer_column'] = array ('qaid'         => $quiz_answer . '.ln_qaid',
                                   'eid'        => $quiz_answer . '.ln_eid',
                                   'mcid'        => $quiz_answer . '.ln_mcid',
                                   'useranswer'        => $quiz_answer . '.ln_useranswer',
									'attempts'        => $quiz_answer . '.ln_attempts',
									'qid'         => $quiz_answer . '.ln_qid',
									'lid'         => $quiz_answer . '.ln_lid');

//by Orrawin
$quiz_test = $prefix . '_quiz_test';
$lntable['quiz_test'] = $quiz_test;
$lntable['quiz_test_column'] = array ('qid'         => $quiz_test . '.ln_qid',
                                   'mcid'         => $quiz_test . '.ln_mcid',
								   'weight'        => $quiz_test . '.ln_weight');


$quiz_multichoice = $prefix . '_quiz_multichoice';
$lntable['quiz_multichoice'] = $quiz_multichoice;
$lntable['quiz_multichoice_column'] = array ('mcid'         => $quiz_multichoice . '.ln_mcid',
									'cid'        => $quiz_multichoice . '.ln_cid',
                                   'uid'        => $quiz_multichoice . '.ln_uid',
                                   'question'        => $quiz_multichoice . '.ln_question',
                                   'answer'        => $quiz_multichoice . '.ln_answer',
                                   'difficulty'        => $quiz_multichoice . '.ln_difficulty',
									'type'    => $quiz_multichoice . '.ln_type',
									'keyword'       => $quiz_multichoice . '.ln_keyword',
									'share'	=> $quiz_multichoice . '.ln_share',
									'guid'	=> $quiz_multichoice . '.ln_guid');

$quiz_choice = $prefix . '_quiz_choice';
$lntable['quiz_choice'] = $quiz_choice;
$lntable['quiz_choice_column'] = array ('chid'         => $quiz_choice . '.ln_chid',
                                   'mcid'        => $quiz_choice . '.ln_mcid',
                                   'answer'        => $quiz_choice . '.ln_answer',
                                   'feedback'        => $quiz_choice . '.ln_feedback',
								   'weight'    => $quiz_choice . '.ln_weight');

$scores = $prefix . '_scores';
$lntable['scores'] = $scores;
$lntable['scores_column'] = array ('eid'      => $scores . '.ln_eid',
										'lid'      => $scores . '.ln_lid',
                                        'score'    => $scores . '.ln_score',
	                                    'quiz_time'    => $scores . '.ln_quiz_time',
										'attempts'    => $scores . '.ln_attempts');

$schools = $prefix . '_schools';
$lntable['schools'] = $schools;
$lntable['schools_column'] = array ('sid'      => $schools . '.ln_sid',
										'code'      => $schools . '.ln_code',
                                        'name'    => $schools . '.ln_name',
                                        'description'   => $schools . '.ln_description',
										'logo' => $schools. '.ln_logo');

$session_info = $prefix . '_session_info';
$lntable['session_info'] = $session_info;
$lntable['session_info_column'] = array ('sessid'    => $session_info . '.ln_sessid',
                                         'ipaddr'    => $session_info . '.ln_ipaddr',
                                         'firstused' => $session_info . '.ln_firstused',
                                         'lastused'  => $session_info . '.ln_lastused',
                                         'uid'       => $session_info . '.ln_uid',
                                         'vars'      => $session_info . '.ln_vars');

/*$session_info = $prefix . '_event_user';
$lntable['event_user'] = $session_info;
$lntable['event_user_column'] = array ('uid'    => $session_info . '.ln_uid',
                                         'ipaddr'    => $session_info . '.ln_ipaddr',
                                         'ippro' => $session_info . '.ln_ippro');*/

$user_data = $prefix . '_user_data';
$lntable['user_data'] = $user_data;
$lntable['user_data_column'] = array ('uda_id'       => $user_data . '.ln_uda_id',
                                       'uda_propid'  => $user_data . '.ln_uda_propid',
                                       'uda_uid'     => $user_data . '.ln_uda_uid',
                                       'uda_value'   => $user_data . '.ln_uda_value');

$user_property = $prefix . '_user_property';
$lntable['user_property'] = $user_property;
$lntable['user_property_column'] = array ('prop_id' => $user_property . '.ln_prop_id',
                                  'prop_label'      => $user_property . '.ln_prop_label',
                                  'prop_dtype'      => $user_property . '.ln_prop_dtype',
                                  'prop_length'     => $user_property . '.ln_prop_length',
                                  'prop_weight'     => $user_property . '.ln_prop_weight',
                                  'prop_validation' => $user_property . '.ln_prop_validation'
                                  );


$users = $prefix . '_users';
$lntable['users'] = $users;
$lntable['users_column'] = array ('uid'             => $users . '.ln_uid',
                                  'name'            => $users . '.ln_name',
                                  'uname'           => $users . '.ln_uname',
                                  'email'           => $users . '.ln_email',
                                  'regdate'    => $users . '.ln_regdate',
                                  'pass'            => $users . '.ln_pass',
                                  'phone'         => $users . '.ln_phone',
                                  'uno' => $users . '.ln_uno',
								  'news' => $users . '.ln_news',
								  'theme' => $users . '.ln_theme',
								  'active' => $users . '.ln_active',
								  'show' => $users . '.ln_show');
//////////////////////////////////////////////////////////////////////////////////////////////เน€เธ�เธดเน�เธกเธ•เธฒเธฃเธฒเธ�///////////////////////////////////////////////////////////////////////////////////////////////////////
$user_log = $prefix . '_user_log';
$lntable['user_log'] = $user_log;
$lntable['user_log_column'] = array ( 'uid'       => $user_log . '.ln_uid',
                                       'cid'  => $user_log . '.ln_cid',
                                       'atime'  => $user_log . '.ln_atime',
                                       'event'     => $user_log . '.ln_event',
                                       'ip' => $user_log . '.ln_ip');
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$user_perms = $prefix . '_user_perms';
$lntable['user_perms'] = $user_perms;
$lntable['user_perms_column'] = array ('pid'       => $user_perms . '.ln_pid',
                                       'uid'       => $user_perms . '.ln_uid',
                                       'sequence'  => $user_perms . '.ln_sequence',
                                       'realm'     => $user_perms . '.ln_realm',
                                       'component' => $user_perms . '.ln_component',
                                       'instance'  => $user_perms . '.ln_instance',
                                       'level'     => $user_perms . '.ln_level',
                                       'bond'      => $user_perms . '.ln_bond');

$calendar = $prefix . '_calendar';
$lntable['calendar'] = $calendar;
$lntable['calendar_column'] = array ('calid'       => $calendar . '.ln_calid',
                                       'type'       => $calendar . '.ln_type',
                                       'title'  => $calendar . '.ln_title',
                                       'uid'     => $calendar . '.ln_uid',
                                       'note' => $calendar . '.ln_note',
                                       'date'  => $calendar . '.ln_date',
                                       'timestart'     => $calendar . '.ln_timestart',
                                       'timeend'      => $calendar . '.ln_timeend',
									   'timetype'      => $calendar . '.ln_timetype');

$note = $prefix . '_note';
$lntable['note'] = $note;
$lntable['note_column'] = array ('folder_id'       => $note . '.ln_folder_id',
                                       'uid'       => $note. '.ln_uid',
                                       'title'  => $note . '.ln_title',
                                       'subject'     => $note . '.ln_subject',
                                       'type' => $note . '.ln_type',
                                       'note'  => $note . '.ln_note',
                                       'notetime'     => $note. '.ln_notetime',
                                       'parent'      => $note . '.ln_parent');

$forums = $prefix . '_forums';
$lntable['forums'] = $forums;
$lntable['forums_column'] = array ('fid'       => $forums . '.ln_fid',
                                       'sid'       => $forums. '.ln_sid',
                                       'tid'  => $forums . '.ln_tid',
                                       'tix'     => $forums . '.ln_tix',
                                       'uid' => $forums . '.ln_uid',
                                       'subject'  => $forums . '.ln_subject',
                                       'post_text'     => $forums. '.ln_post_text',
                                       'icon'     => $forums. '.ln_icon',
                                       'ip'     => $forums. '.ln_ip',
                                       'post_time'     => $forums. '.ln_post_time',
                                       'options'      => $forums . '.ln_options');

//////////////////////////////////////////////////////////////////////////////////////////////เน€เธ�เธดเน�เธกเธ•เธฒเธฃเธฒเธ�///////////////////////////////////////////////////////////////////////////////////////////////////////

$rss = $prefix . '_rss';
$lntable['rss'] = $rss;
$lntable['rss_column'] = array ('id'         => $rss . '.ln_id',
                                   'title'        => $rss . '.ln_title',
                                   'xml'       => $rss . '.ln_xml',
								   'display'       => $rss . '.ln_display',
                                   'name'         => $rss . '.ln_name',
                                   'email'         => $rss . '.ln_email',
                                   'date'         => $rss . '.ln_date');

//JoeJae Chat Room
$JoeJae_enrolls = $prefix . '_chat_data';
$lntable['JoeJae_enrolls'] = $JoeJae_enrolls;
$lntable['JoeJae_enrolls_column'] = array ('id'         => $JoeJae_enrolls . '.id',
                                   'uid'        => $JoeJae_enrolls . '.uid',
								   'uname'	=> $JoeJae_enrolls . '.uname',
                                   'text'       => $JoeJae_enrolls . '.text',
								   'wuid'       => $JoeJae_enrolls . '.wuid',
								   'cid'       => $JoeJae_enrolls . '.cid',
                                   'time'         => $JoeJae_enrolls . '.time',
                                   'ip'         => $JoeJae_enrolls . '.ip');

$JoeJae_onlinetable = $prefix . '_chat_useronline';
$lntable['JoeJae_onlinetable'] = $JoeJae_onlinetable;
$lntable['JoeJae_online_column'] = array ('id'         => $JoeJae_onlinetable . '.id',
                                   'uid'        => $JoeJae_onlinetable . '.uid',
								   'uname'	=> $JoeJae_onlinetable . '.uname',
								   'cid'	=> $JoeJae_onlinetable . '.cid',
                                   'lastseen'       => $JoeJae_onlinetable . '.lastseen');

$JoeJae_configtable = $prefix . '_chat_config';
$lntable['JoeJae_configtable'] = $JoeJae_configtable;
$lntable['JoeJae_config_column'] = array ('id'         => $JoeJae_configtable . '.id',
                                   'title'        => $JoeJae_configtable . '.title',
								   'value'	=> $JoeJae_configtable . '.value');

$JoeJae_activetable = $prefix . '_chat_active';
$lntable['JoeJae_activetable'] = $JoeJae_activetable;
$lntable['JoeJae_active_column'] = array ('id'         => $JoeJae_activetable . '.id',
                                   'cid'        => $JoeJae_activetable . '.cid',
								   'allow_chat'		=> $JoeJae_activetable . '.ln_allow_chat',
								   'allow_member'        => $JoeJae_activetable . '.ln_allow_member');

//DB Lexitron
$et = $prefix . '_et';
$lntable['et'] = $et;
$lntable['et_column'] = array ('id'         => $et . '.ln_id',
                                   'eentry'       => $et . '.ln_eentry',
                                   'tentry'       => $et . '.ln_tentry',
								   'esearch'      => $et . '.ln_esearch',
                                   'ecat'         => $et . '.ln_ecat',
                                   'ethai'        => $et . '.ln_ethai',
                                   'esyn'         => $et . '.ln_esyn',
								   'eant'         => $et . '.ln_eant',
								   'esample'      => $et . '.ln_esample',
								   'freq_use'     => $et . '.ln_freq_use',
								   'vocab_by'     => $et . '.ln_vocab_by',
								   'owner'        => $et . '.ln_owner');

// DB Mining module
// adding date: 2011.01.06
$xestat = $prefix.'_xestat';
$lntable['xestat'] = $xestat;
$lntable['xestat_column'] = array(
        'ip' => $xestat.'.ip',
        'time' => $xestat.'.time',
        'uid' => $xestat.'.uid',
        'url' => $xestat.'.url',
        'refUrl' => $xestat.'.refUrl',
        'dura' => $xestat.'.dura'
);

//bookmarks
// adding date: 2011.02.01
$bookmarks = $prefix.'_bookmarks';
$lntable['bookmarks'] = $bookmarks;
$lntable['bookmarks_column'] = array(
        'bid' => $bookmarks.'.ln_bid',
        'uid' => $bookmarks.'.ln_uid',
		'sid' => $bookmarks.'.ln_sid',
        'cid' => $bookmarks.'.ln_cid',
		'lid' => $bookmarks.'.ln_lid',
        'page' => $bookmarks.'.ln_page',
        'date' => $bookmarks.'.ln_date'
);

//file download counter
// adding date: 2012.01.16
$counters = $prefix.'_counters';
$lntable['counters'] = $counters;
$lntable['counters_column'] = array(
        'id' => $counters.'.ln_id',
        'file' => $counters.'.ln_file',
		'server_addr' => $counters.'.ln_server_addr',
        'remote_addr' => $counters.'.ln_remote_addr',
		'date' => $counters.'.ln_date'
);
?>
