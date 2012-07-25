<?php
/**
 * This function is called internally 
 */
function Note_lntables()
{
	global $config;
    
	$prefix = $config['prefix'];

	// Initialise table array
    $lntable = array();
	
	$note = $prefix . '_note';
	$lntable['note'] = $note;
	
		$sql ="CREATE TABLE $notetable (
					  $notecolumn[folder_id] int(10) unsigned NOT NULL auto_increment,
					  $notecolumn[uid] int(10) unsigned NOT NULL,
					  $notecolumn[subject] varchar(255) default NULL,
					  $notecolumn[type] char(1) default NULL,
					  $notecolumn[note] text,
					  $notecolumn[notetime] timestamp(14) NOT NULL,
					  $notecolumn[parent] int(10) unsigned default NULL,
					  PRIMARY KEY  ($notecolumn[ln_folder_id])
					) TYPE=MyISAM";

	$lntable['note_column'] = array ('folder_id'         => $note . '.ln_folder_id',
                                   'uid'        => $note . '.ln_uid',
                                   'subject'       => $note . '.ln_subject',
                                   'type'         => $note . '.ln_type',
                                   'note'         => $note . '.ln_note',
                                   'notetime'    => $note . '.ln_notetime',
                                   'parent'      => $note . '.ln_parent');

	// Return the table information
    return $lntable;
}

?>