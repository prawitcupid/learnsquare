<?php
/**
* init module 
*/
function Forums_init() {
		// Set up database tables
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$forumstable = $lntable['forums'];
		$forumscolumn = &$lntable['forums_column'];

		$sql = "DROP TABLE IF EXISTS $forumstable";
		$dbconn->Execute($sql);

		$sql ="CREATE TABLE $forumstable (
		$forumscolumn[fid] int(10) unsigned NOT NULL auto_increment,
		$forumscolumn[sid] int(10) unsigned default NULL,
		$forumscolumn[tid] int(10) unsigned default NULL,
		$forumscolumn[tix] int(10) unsigned default NULL,
		$forumscolumn[uid] int(11) default NULL,
		$forumscolumn[subject] text,
		$forumscolumn[post_text] text,
		$forumscolumn[icon] varchar(50) default NULL,
		$forumscolumn[ip] varchar(15) default NULL,
		$forumscolumn[post_time] varchar(14) default NULL,
		$forumscolumn[options] char(1) default '1',
		PRIMARY KEY  (ln_fid)
		) TYPE=MyISAM";

		$dbconn->Execute($sql);

		if ($dbconn->ErrorNo() != 0) {
			echo $sql;
			return false;
		}

		// Settings
		$module_varstable = $lntable['module_vars'];
		$module_varscolumn = &$lntable['module_vars_column'];

		$sql = "SELECT MAX(id) FROM $module_varstable";		
		$result = $dbconn->Execute($sql);
		list($id) = $result->fields;

		$value = serialize("1");
		$sql ="INSERT INTO $module_varstable VALUES ('$id','showsmiley','$value')";
		$dbconn->Execute($sql);
		$sql ="INSERT INTO $module_varstable VALUES ('$id','uploadpic','$value')";
		$dbconn->Execute($sql);

		return true;

}

function Forums_delete() {
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$module_varstable = $lntable['module_vars'];
		$module_varscolumn = &$lntable['module_vars_column'];

		$sql ="DELETE FROM $module_varstable WHERE  $module_varscolumn[name]='showsmiley'";
		$dbconn->Execute($sql);
		$sql ="DELETE FROM $module_varstable WHERE  $module_varscolumn[name]='uploadpic'";
		$dbconn->Execute($sql);


		return true;
}
?>
