<?php
/**
* init module 
*/
function Note_init() {
		// Set up database tables
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$notetable = $lntable['note'];
		$notecolumn = &$lntable['note_column'];
		

		$sql = "DROP TABLE IF EXISTS $notetable";
		$dbconn->Execute($sql);

		$sql ="CREATE TABLE $notetable (
					  $notecolumn[folder_id] int(10) unsigned NOT NULL auto_increment,
					  $notecolumn[uid] int(10) unsigned NOT NULL,
					  $notecolumn[subject] varchar(255) default NULL,
					  $notecolumn[type] char(1) default NULL,
					  $notecolumn[note] text,
					  $notecolumn[notetime] varchar(14)  NOT NULL,
					  $notecolumn[parent] int(10) unsigned default NULL,
					  PRIMARY KEY  (ln_folder_id)
					) TYPE=MyISAM";

		$dbconn->Execute($sql);

		if ($dbconn->ErrorNo() != 0) {
			echo $sql;
			return false;
		}

		return true;
}


function Note_upgrade($oldversion)
{
    return true;
}

function Note_delete()
{
		list($dbconn) = lnDBGetConn();
		$lntable = lnDBGetTables();

		$notetable = $lntable['note'];
		$notecolumn = &$lntable['note_column'];
		

		$sql = "DROP TABLE IF EXISTS $notetable";
		$dbconn->Execute($sql);
    
		return true;
}

?>
