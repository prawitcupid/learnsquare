<?php
// Generated: $d$ by $id$
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2001 by the Post-Nuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Everyone
// Purpose of file: Translation files
// Translation team: Read credits in /docs/CREDITS.txt
// ----------------------------------------------------------------------
define('_ADMIN_EMAIL','Admin Email');
define('_ADMIN_LOGIN','Admin Login');
define('_ADMIN_NAME','Admin Name');
define('_ADMIN_PASS','Admin Password');
define('_ADMIN_REPEATPASS','Admin Password (verify)');
define('_ADMIN_URL','Admin URL');
define('_BTN_CONTINUE','Continue');
define('_BTN_FINISH','Finish');
define('_BTN_NEXT','Next');
define('_BTN_RECHECK','Re-check');
define('_BTN_SET_LANGUAGE','Set Language');
define('_BTN_SET_LOGIN','Set Login');
define('_BTN_START','Start');
define('_BTN_SUBMIT','Submit');
define('_CHANGE_INFO_1','Please correct your database information.');
define('_CHMOD_CHECK_1','CHMOD Check');
define('_CHMOD_CHECK_2','We will first check to see that your file permissions are correct in order for the script to write to the file. If your settings are not correct, this script will not be able to encrypt your data in your config file. Encrypting the SQL data is added security, and is set by this script. You will also not be able to update your preferences from your admin once your site is up and running.');
define('_CHMOD_CHECK_3','File permissions for config.php are 666 -- correct, this script can write to the file');
define('_CHMOD_CHECK_4','Please change permissions on config.php to 666 so this script can write and encrypt the DB data (HINT: use "chmod")');
define('_CHMOD_CHECK_5','File permissions for config-old.php are 666 -- correct, this script can write to the file');
define('_CHMOD_CHECK_6','Please change permissions on config-old.php to 666 so this script can write and encrypt the DB data (HINT: use "chmod")');
define('_CHM_CHECK_1','Please enter your DB info. If you do not have root access to your DB (virtual hosting, etc), you will need to make your database before you proceed. A good rule of thumb, if you cannot create databases through phpMyAdmin because of virtual hosting, or security on mySQL, then this script will not be able to create the db for you. This script will still be able to fill the database, and will still need to be run.<br><br>If you do not know the values for the database host, username or password, leave them as their current defaults. If that does not work then please contact your ISP who should be able to provide the information for you.');
define('_CONTINUE_1','Setting Your DB Preferences');
define('_CONTINUE_2','You can now set up your administrative account. If you pass on this set up, your login for the administrative account will be Admin / Password (case sensitive).  It is advisable to set it up now, and not wait until later.');
define('_DBHOST','Database Host');
define('_DBINFO','Database Information');
define('_DBNAME','Database Name');
define('_DBPASS','Database Password');
define('_DBPREFIX','Table Prefix (for Table Sharing)');
define('_DBTYPE','Database Type');
define('_DBTABLETYPE', 'Database Table Type');
define('_DBUNAME','Database Username');
define('_DEFAULT_1','This script will install the PostNuke database and help you set up the variables that you need to start. You will be taken through a variety of pages. Each page sets a different portion of the script. We estimate that this entire process will take about ten minutes. At any time that you get stuck, please visit our support forums for help.');
define('_DEFAULT_2','Our License');
define('_DEFAULT_3','Please read through the GNU General Public License. PostNuke is developed as free software, but there are certain requirements for distributing and editing.');
define('_DONE','Done.');
define('_FINISH_1','The Credits');
define('_FINISH_2','These are the scripts and people that make PostNuke go. Take some time and let these people know how much you appreciate their work. If you would like to be listed here, contact us about being a part of the developement team. We are always looking for some help.');
define('_FINISH_3','You are now done with the PostNuke installation. If you run into any problems, let us know.  Make sure that you delete this script. You will not need it again.');
define('_FINISH_4','Go to your PostNuke site');
define('_FOOTER_1','Thank you for trying PostNuke and welcome to our community.');
define('_FORUM_INFO_1','Your forum tables are untouched.<br><br>FYI, those tables are:');
define('_FORUM_INFO_2','So, you can delete those tables if you don\'t want to use forums.<br> phpBB should be available as a module from http://mods.postnuke.com');
define('_INPUT_DATA_1','Uploaded Data');
define('_INSTALLATION','PostNuke Installation');
define('_INTRANETINFO','You must set the "intranet" option if your site does not use a fully-qualified host name for access.  Examples of fully qualified hostnames are www.postnuke.com and foo.bar.com.  Examples of hostnames that are not fully qualified are foo.com, localhost, and mysite.org  If you do not set this paramter properly you might not be able to log in to your site. If once the install has completed you find you can not login then please rerun this install and enable the "Intranet" option.<br>This option also helps resolve a session problem found with some levels of PHP.');
define('_ISINTRANET','Site is for intranet or other local (non-internet) use');
define('_MAKE_DB_1','Unable to make database');
define('_MAKE_DB_2','has been created.');
define('_MAKE_DB_3','No database made.');
define('_MODIFY_FILE_1','Error: unable to open for read:');
define('_MODIFY_FILE_2','Error: unable to open for write:');
define('_MODIFY_FILE_3','0 lines changed, did nothing');
define('_MYPHPNUKE_1','Upgrading from MyPHPNuke 1.8.7?');
define('_MYPHPNUKE_2','Just press the <b>MyPHPNuke 1.8.7</b> button');
define('_MYPHPNUKE_3','Upgrading from MyPHPNuke 1.8.8b2?');
define('_MYPHPNUKE_4','Just press the <b>MyPHPNuke 1.8.8</b> button');
define('_NEWINSTALL','New Install');
define('_NEW_INSTALL_1','You have choosen to do a new install. Below is the information that you have entered.');
define('_NEW_INSTALL_2','There are two steps to getting a working PostNuke database. First an empty database is created, then it is populated.<br><br>If you have root access, check the <b>create the database</b> box and this script will create the empty database for you. Otherwise, just click on start.<br>If you do not have root access you need to create the empty database manually first.<br>Either way this script will then create the tables and populate your database for you.');
define('_NEW_INSTALL_3','Create the Database');
define('_NOTMADE','Unable to make ');
define('_NOTSELECT','Unable to select database.');
define('_NOTUPDATED','Unable to update ');
define('_PHPNUKE_1','Upgrading from PHP-Nuke 4.4?');
define('_PHPNUKE_10','Just press the <b>PHP-Nuke 5.3.1</b> button');
define('_PHPNUKE_11','Upgrading from PHP-Nuke 5.4?');
define('_PHPNUKE_12','Just press the <b>PHP-Nuke 5.4</b> button');
define('_PHPNUKE_2','Please read the following note, and press the <b>PHP-Nuke 4.4</b> button when ready.<br><br> This script will leave intact your forums DB but this version will not manage the data.<i> There is an upgrade script for this forum data that is being tested. It is currently held in the pn-modules CVS</i><br><br> We do not have PHPBB included into the release, but the upgrade script is the same. It will not destroy any of your data.');
define('_PHPNUKE_3','Upgrading from PHP-Nuke 5?');
define('_PHPNUKE_4','Just press the <b>PHP-Nuke 5</b> button');
define('_PHPNUKE_5','Upgrading from PHP-Nuke 5.2?');
define('_PHPNUKE_6','Just press the <b>PHP-Nuke 5.2</b> button');
define('_PHPNUKE_7','Upgrading from PHP-Nuke 5.3?');
define('_PHPNUKE_8','Just press the <b>PHP-Nuke 5.3</b> button');
define('_PHPNUKE_9','Upgrading from PHP-Nuke 5.3.1?');
define('_PHP_CHECK_1','Your PHP version is ');
define('_PHP_CHECK_2','You need to upgrade PHP to at least 4.0.1 - <a href=\'http://www.php.net\'>http://www.php.net</a>');
define('_PHP_CHECK_3','Not Good! magic_quotes_gpc is Off.<br>This can often be fixed using a .htaccess file with the following line:<br>php_flag magic_quotes_gpc On');
define('_PHP_CHECK_4','Not Good! magic_quotes_runtime is On.<br>This can often be fixed using a .htaccess file with the following line:<br>php_flag magic_quotes_runtime Off');
define('_PN6_1','Admin: You Will Need To Re-Save Your Website Settings In The Admin Page ASAP!');
define('_PN6_2','(We Are Sorry For This Inconvience)');
define('_PN6_3','ERROR: File not found: ');
define('_PN6_4','Done converting old-style button blocks.');
define('_POSTNUKE_1','Upgrading from PostNuke .5x?');
define('_POSTNUKE_10','Just press the <b>PostNuke .64</b> button');
define('_POSTNUKE_11','Upgrading from PostNuke .7?');
define('_POSTNUKE_12','Just press the <b>PostNuke 7</b> button');
define('_POSTNUKE_13','Upgrading from PostNuke .71?');
define('_POSTNUKE_14','Just press the <b>PostNuke 71</b> button');
define('_POSTNUKE_15','To confirm your system language?');
define('_POSTNUKE_16','Just press the <b>Validate</b> button');
define('_POSTNUKE_17','Validate your table structure?');
define('_POSTNUKE_18','Just press the <b>Validate</b> button');

# added for 0.7.2.2 Neo
define('_POSTNUKE_19','Upgrading from PostNuke .72?');
define('_POSTNUKE_20','Just press the <b>PostNuke 72</b> button');

define('_POSTNUKE_2','Just press the <b>PostNuke .5</b> button');
define('_POSTNUKE_3','Upgrading from PostNuke .6 / .61?');
define('_POSTNUKE_4','Just press the <b>PostNuke .6</b> button');
define('_POSTNUKE_5','Upgrading from PostNuke .62?');
define('_POSTNUKE_6','Just press the <b>PostNuke .62</b> button');
define('_POSTNUKE_7','Upgrading from PostNuke .63?');
define('_POSTNUKE_8','Just press the <b>PostNuke .63</b> button<br>');
define('_POSTNUKE_9','Upgrading from PostNuke .64?');
define('_PWBADMATCH','The passwords supplied do not match.  Please go back and re-type the passwords to ensure they are the same.');
define('_QUOTESCHECK_1','NS-Quotes Check');
define('_QUOTESCHECK_2','The Former NS-Quotes module has been deprecated in favor of the new Quotes module.<br> Please remove the modules/NS-Quotes directory.');
define('_SELECT_LANGUAGE_1','Select your language.');
define('_SELECT_LANGUAGE_2','Language: ');
define('_SHOW_ERROR_INFO_1','Write error</b> unable to update your \'config.php\' file<br/> You will have to modify this file yourself using a text editor.<br/> Here are the changes required:');
define('_SKIPPED','Skipped.');
define('_SUBMIT_1','Please, look over the information and make sure that it is correct.');
define('_SUBMIT_2','You have entered the following information:');
define('_SUBMIT_3','Select <b>New Install</b> or <b>Upgrade</b> to continue.');
define('_SUCCESS_1','Finished');
define('_SUCCESS_2','Your upgrade to the latest version of PostNuke is finished.<br> Remember to change your config.php settings before using for the first time.');
define('_UPDATED',' updated.');
define('_UPDATING','Updating table: ');
define('_UPGRADETAKESALONGTIME','Carrying out a PostNuke upgrade can take a long time, maybe a matter of minutes.  When selecting an upgrade option please select the option only once, and wait for the next screen to appear.  Clicking on upgrade options multiple times can cause the upgrade process to fail');
define('_UPGRADE_1','Upgrades');
define('_UPGRADE_2','Here is where you can select which CMS your are upgrading from.<br><br><center> Select <b>PHP-Nuke</b> to upgrade an existing PHP-Nuke install.<br> Select <b>PostNuke</b> to upgrade an existing PostNuke install.<br> Select <b>MyPHPNuke</b> to upgrade an exisitng MyPHPNuke install.');
//do_check_phpini
define('_MADE'.'made');
define('_PHPINI_CHECK_0','Check file properties php.ini');
define('_PHPINI_CHECK_00','Checking php.ini on system for configuration if success then continue to next step');
define('_PHPINI_CHECK_1','Post Max Size >= 16M');
define('_PHPINI_CHECK_2','Post Max Size Not pass it\'s < 16M (config file php.ini keyword is post_max_size)');
define('_PHPINI_CHECK_3','File Uploads On');
define('_PHPINI_CHECK_4','File Uploads Not On (config file php.ini keyword is file_uploads)');
define('_PHPINI_CHECK_5','Upload Max File Size >= 10M');
define('_PHPINI_CHECK_6','Upload Max File Size < 10M (config file php.ini keyword is upload_max_filesize)');
define('_PHPINI_CHECK_7','Max Execution Time >= 90s');
define('_PHPINI_CHECK_8','Max Execution Time < 90s (config file php.ini keyword is max_execution_time)');
define('_PHPINI_CHECK_9','extension php zip');
define('_PHPINI_CHECK_10','Not extension php zip (For Windows config file php.ini add extension=php_zip.dll)');
define('_PHPINI_CHECK_11','extension php gd');
define('_PHPINI_CHECK_12','Not extension php gd (For Windows config file php.ini add extension=php_gd2.dll, for Linux you can apt-get install php5-gd)');
?>
