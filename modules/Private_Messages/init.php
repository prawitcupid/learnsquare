<?php
/**
* init module
*/
function Private_Messages_init() {
		// Set up database tables
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$privmsgstable = $lntable['privmsgs'];
		$privmsgscolumn = &$lntable['privmsgs_column'];

		$sql = "DROP TABLE IF EXISTS $privmsgstable";
		$dbconn->Execute($sql);

		$sql ="CREATE TABLE $privmsgstable (
						$privmsgscolumn[id] int(10) unsigned NOT NULL ,
						$privmsgscolumn[type] tinyint(4) NOT NULL default '0',

						$privmsgscolumn[priority] tinyint(1) unsigned NOT NULL default '0',
						$privmsgscolumn[subject] varchar(255) NOT NULL default '0',
						$privmsgscolumn[message] text ,
						$privmsgscolumn[from_uid] int(11) unsigned NOT NULL default '0',

						$privmsgscolumn[to_uid] int(10) unsigned NOT NULL default '0',

						$privmsgscolumn[date] int(11) unsigned NOT NULL default '0',

						$privmsgscolumn[ip] varchar(15) NOT NULL default '',
						$privmsgscolumn[enable] tinyint(1) NOT NULL default '1'
						) TYPE=MyISAM";

		$dbconn->Execute($sql);

		// Set up database tables
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();
		$module_varstable = $lntable['module_vars'];
		$module_varscolumn = &$lntable['module_vars_column'];

		$sql = "SELECT MAX(id) FROM $module_varstable";		
		$result = $dbconn->Execute($sql);
		list($id) = $result->fields;

		$value = serialize("5");
		$sql ="INSERT INTO $module_varstable VALUES ('$id','inboxsize','$value')";
		$dbconn->Execute($sql);

		return true;
}

function Private_Messages_delete() {
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$module_varstable = $lntable['module_vars'];
		$module_varscolumn = &$lntable['module_vars_column'];

		$sql ="DELETE FROM $module_varstable WHERE  $module_varscolumn[name]='inboxsize";

		$dbconn->Execute($sql);

		if ($dbconn->ErrorNo() != 0) {
			echo $sql;
			return false;
		}

		return true;
}

?>
