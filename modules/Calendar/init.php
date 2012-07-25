<?php
/**
* init module 
*/
function Calendar_init() {
		// Set up database tables
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$calendartable = $lntable['calendar'];
		$calendarcolumn = &$lntable['calendar_column'];
		

		$sql = "DROP TABLE IF EXISTS $calendartable";
		$dbconn->Execute($sql);

		$sql ="CREATE TABLE $calendartable (
					  $calendarcolumn[calid] int(10) unsigned NOT NULL auto_increment,
					  $calendarcolumn[type] varchar(20) default '0',
					  $calendarcolumn[title] varchar(50) default NULL,
					  $calendarcolumn[uid] varchar(20) default NULL,
					  $calendarcolumn[note] text,
					  $calendarcolumn[date] date default NULL,
					  $calendarcolumn[timestart] time default NULL,
					  $calendarcolumn[timeend] time default NULL,
					  $calendarcolumn[timetype] char(1) NOT NULL default '0',
			  PRIMARY KEY  (ln_calid)
			) TYPE=MyISAM";


		$dbconn->Execute($sql);

		if ($dbconn->ErrorNo() != 0) {
			echo $sql;
			return false;
		}

		return true;
}

function Calendar_upgrade($oldversion)
{
    return true;
}

function Calendar_delete()
{
	list($dbconn) = lnDBGetConn();
	$lntable = lnDBGetTables();

	$calendartable = $lntable['calendar'];
	$calendarcolumn = &$lntable['calendar_column'];
	

	$sql = "DROP TABLE IF EXISTS $calendartable";
	$dbconn->Execute($sql);

	return true;
}
?>
